import localforage from 'localforage';

// Configure localforage
localforage.config({
    name: 'AquaTrack',
    storeName: 'offline_reports',
    description: 'Storage for offline reports'
});

class OfflineService {
    constructor() {
        this.isOnline = navigator.onLine;
        this.setupEventListeners();
    }

    setupEventListeners() {
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.triggerSync();
        });

        window.addEventListener('offline', () => {
            this.isOnline = false;
        });
    }

    // Get current location with graceful fallback
    async getCurrentLocation() {
        return new Promise((resolve) => {
            if (!navigator.geolocation) {
                resolve({ latitude: null, longitude: null });
                return;
            }

            const options = {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000
            };

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    resolve({
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy
                    });
                },
                (error) => {
                    console.warn('GPS failed, submitting without location:', error);
                    resolve({ latitude: null, longitude: null });
                },
                options
            );
        });
    }

    // Convert file to base64 for offline storage
    async fileToBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => resolve(reader.result);
            reader.onerror = error => reject(error);
        });
    }

    // Save report offline
    async saveOfflineReport(reportData) {
        try {
            const timestamp = new Date().toISOString();
            const report = {
                ...reportData,
                id: `offline_${timestamp}_${Math.random().toString(36).substr(2, 9)}`,
                status: 'offline',
                created_at: timestamp,
                synced: false
            };

            // Convert media files to base64 for offline storage
            if (reportData.photos && reportData.photos.length > 0) {
                report.photos = await Promise.all(
                    reportData.photos.map(async (file) => ({
                        name: file.name,
                        type: file.type,
                        size: file.size,
                        data: await this.fileToBase64(file),
                        isBase64: true,
                        original_name: file.name
                    }))
                );
            }

            await localforage.setItem(report.id, report);
            console.log('Report saved offline:', report.id);
            return report;
        } catch (error) {
            console.error('Error saving offline report:', error);
            throw error;
        }
    }

    // Get all offline reports
    async getOfflineReports() {
        try {
            const reports = [];
            await localforage.iterate((value) => {
                if (value.id && value.id.startsWith('offline_')) {
                    reports.push(value);
                }
            });
            return reports.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        } catch (error) {
            console.error('Error getting offline reports:', error);
            return [];
        }
    }

    // Remove report after successful sync
    async removeOfflineReport(reportId) {
        try {
            await localforage.removeItem(reportId);
            console.log('Offline report removed:', reportId);
        } catch (error) {
            console.error('Error removing offline report:', error);
        }
    }

    // Get offline reports count
    async getOfflineReportsCount() {
        const reports = await this.getOfflineReports();
        return reports.length;
    }

    // Trigger sync when online
    triggerSync() {
        window.dispatchEvent(new CustomEvent('triggerSync'));
    }

    // Check online status
    getOnlineStatus() {
        return this.isOnline;
    }

    // Clear all offline reports (for testing/debugging)
    async clearAllOfflineReports() {
        try {
            const reports = await this.getOfflineReports();
            for (const report of reports) {
                await this.removeOfflineReport(report.id);
            }
            console.log('All offline reports cleared');
        } catch (error) {
            console.error('Error clearing offline reports:', error);
        }
    }
}

export default new OfflineService();
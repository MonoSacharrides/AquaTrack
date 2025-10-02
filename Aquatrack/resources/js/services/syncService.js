import offlineService from './offlineService.js';
import axios from 'axios';

class SyncService {
    constructor() {
        this.isSyncing = false;
        this.setupSyncListener();
    }

    setupSyncListener() {
        window.addEventListener('online', () => {
            this.syncOfflineReports();
        });

        // Also listen for manual sync trigger
        window.addEventListener('triggerSync', () => {
            this.syncOfflineReports();
        });
    }

    async syncOfflineReports() {
        if (this.isSyncing || !offlineService.getOnlineStatus()) {
            console.log('Sync skipped - already syncing or offline');
            return;
        }

        this.isSyncing = true;
        const offlineReports = await offlineService.getOfflineReports();
        const unsyncedReports = offlineReports.filter(report => !report.synced);

        if (unsyncedReports.length === 0) {
            console.log('No offline reports to sync');
            this.isSyncing = false;
            return;
        }

        console.log(`Starting sync of ${unsyncedReports.length} offline reports`);

        // Start sync event
        window.dispatchEvent(new CustomEvent('syncStart', {
            detail: {
                total: unsyncedReports.length,
                timestamp: new Date().toISOString()
            }
        }));

        const trackingCodes = [];
        let syncedCount = 0;
        let failedCount = 0;

        for (const report of unsyncedReports) {
            try {
                // Update progress
                window.dispatchEvent(new CustomEvent('syncProgress', {
                    detail: {
                        synced: syncedCount,
                        total: unsyncedReports.length,
                        currentReport: {
                            water_issue_type: report.water_issue_type,
                            barangay: report.barangay,
                            purok: report.purok
                        }
                    }
                }));

                const result = await this.syncSingleReport(report);
                
                if (result.success) {
                    // Remove from offline storage
                    await offlineService.removeOfflineReport(report.id);
                    syncedCount++;
                    
                    // Add to tracking codes
                    trackingCodes.push({
                        trackingCode: result.trackingCode,
                        timestamp: new Date().toISOString(),
                        reportId: result.reportId,
                        isMerged: result.isMerged
                    });

                    console.log(`Successfully synced report: ${result.trackingCode}`);
                }
            } catch (error) {
                console.error(`Failed to sync report ${report.id}:`, error);
                failedCount++;
                // Continue with other reports even if one fails
            }
        }

        // Sync complete event
        window.dispatchEvent(new CustomEvent('syncComplete', {
            detail: {
                total: unsyncedReports.length,
                synced: syncedCount,
                failed: failedCount,
                trackingCodes: trackingCodes,
                timestamp: new Date().toISOString()
            }
        }));

        console.log(`Sync completed: ${syncedCount} successful, ${failedCount} failed`);
        this.isSyncing = false;
    }

    async syncSingleReport(offlineReport) {
        const formData = new FormData();
        
        // Add basic report data
        formData.append('municipality', offlineReport.municipality);
        formData.append('province', offlineReport.province);
        formData.append('zone', offlineReport.zone);
        formData.append('barangay', offlineReport.barangay);
        formData.append('purok', offlineReport.purok);
        formData.append('description', offlineReport.description);
        formData.append('water_issue_type', offlineReport.water_issue_type);
        formData.append('reporter_name', offlineReport.reporter_name);
        formData.append('reporter_phone', offlineReport.reporter_phone);
        formData.append('latitude', offlineReport.latitude);
        formData.append('longitude', offlineReport.longitude);
        formData.append('offline_id', offlineReport.id);

        if (offlineReport.custom_water_issue) {
            formData.append('custom_water_issue', offlineReport.custom_water_issue);
        }

        // Handle media files - convert base64 back to Blob for upload
        if (offlineReport.photos && offlineReport.photos.length > 0) {
            for (const mediaItem of offlineReport.photos) {
                if (mediaItem.isBase64) {
                    try {
                        // Convert base64 to Blob
                        const response = await fetch(mediaItem.data);
                        const blob = await response.blob();
                        formData.append('photos[]', blob, mediaItem.original_name);
                    } catch (error) {
                        console.error('Failed to convert base64 to blob:', error);
                    }
                }
            }
        }

        const response = await axios.post('/reports', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            timeout: 30000 // 30 second timeout
        });

        return {
            success: true,
            trackingCode: response.data.trackingCode,
            reportId: response.data.reportId,
            isMerged: response.data.isMerged,
            offlineSync: response.data.offlineSync
        };
    }

    // Manual sync trigger
    async triggerManualSync() {
        if (this.isSyncing) {
            console.log('Sync already in progress');
            return;
        }
        await this.syncOfflineReports();
    }

    // Check if currently syncing
    getIsSyncing() {
        return this.isSyncing;
    }
}

export default new SyncService();
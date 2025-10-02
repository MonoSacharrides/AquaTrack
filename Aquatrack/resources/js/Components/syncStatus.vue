<template>
    <div v-if="isSyncing" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4 shadow-2xl">
            <!-- Loading Animation -->
            <div class="flex flex-col items-center justify-center space-y-6">
                <!-- Animated Sync Icon -->
                <div class="relative">
                    <div class="w-20 h-20 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <v-icon name="hi-refresh" class="w-8 h-8 text-blue-600 animate-pulse" />
                    </div>
                </div>
                
                <!-- Sync Text -->
                <div class="text-center">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Syncing Your Reports</h3>
                    <p class="text-gray-600 mb-4">
                        We're uploading your offline reports to the server. Please don't close this window.
                    </p>
                    
                    <!-- Progress Info -->
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Progress:</span>
                            <span>{{ syncedCount }} / {{ totalCount }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div 
                                class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                :style="{ width: progressPercentage + '%' }"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Current Report Info -->
                <div v-if="currentReport" class="bg-blue-50 border border-blue-200 rounded-lg p-4 w-full">
                    <div class="flex items-center space-x-3">
                        <v-icon name="hi-document-text" class="w-5 h-5 text-blue-600 flex-shrink-0" />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-blue-900 truncate">
                                {{ currentReport.water_issue_type }}
                            </p>
                            <p class="text-xs text-blue-700 truncate">
                                {{ currentReport.barangay }}, {{ currentReport.purok }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4 shadow-2xl">
            <div class="flex flex-col items-center justify-center space-y-6">
                <!-- Success Icon -->
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                    <v-icon name="hi-check" class="w-8 h-8 text-green-600" />
                </div>
                
                <!-- Success Text -->
                <div class="text-center">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Sync Complete!</h3>
                    <p class="text-gray-600 mb-4">
                        All your offline reports have been successfully synced to the server.
                    </p>
                </div>

                <!-- Tracking Codes -->
                <div v-if="syncedTrackingCodes.length > 0" class="w-full">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Your Tracking Codes:</h4>
                    <div class="space-y-2 max-h-40 overflow-y-auto">
                        <div 
                            v-for="(tracking, index) in syncedTrackingCodes" 
                            :key="index"
                            class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-center justify-between"
                        >
                            <div>
                                <p class="text-sm font-medium text-green-900">{{ tracking.trackingCode }}</p>
                                <p class="text-xs text-green-700">{{ formatDate(tracking.timestamp) }}</p>
                            </div>
                            <button 
                                @click="showQRCode(tracking)"
                                class="text-green-600 hover:text-green-800 transition-colors"
                            >
                                <v-icon name="hi-qrcode" class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3 w-full">
                    <button 
                        @click="closeSuccessModal"
                        class="flex-1 bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors"
                    >
                        Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Modal -->
    <div v-if="showQRModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 max-w-sm w-full mx-4 shadow-2xl">
            <div class="flex flex-col items-center justify-center space-y-6">
                <!-- QR Code Placeholder -->
                <div class="bg-gray-100 rounded-lg p-6 flex items-center justify-center">
                    <div class="text-center">
                        <div class="w-48 h-48 bg-white border-2 border-gray-300 rounded flex items-center justify-center mb-4">
                            <div class="text-center">
                                <v-icon name="hi-qrcode" class="w-16 h-16 text-gray-400 mb-2" />
                                <p class="text-xs text-gray-500">QR Code for</p>
                                <p class="text-sm font-medium text-gray-700">{{ selectedTrackingCode }}</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">Scan this code to track your report</p>
                    </div>
                </div>
                
                <!-- Tracking Info -->
                <div class="text-center">
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Tracking Code</h4>
                    <p class="text-2xl font-mono font-bold text-blue-600 bg-blue-50 px-4 py-2 rounded-lg">
                        {{ selectedTrackingCode }}
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        Use this code to track your report status
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3 w-full">
                    <button 
                        @click="closeQRModal"
                        class="flex-1 bg-gray-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-gray-700 transition-colors"
                    >
                        Close
                    </button>
                    <button 
                        @click="copyTrackingCode"
                        class="flex-1 bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors"
                    >
                        Copy Code
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import Swal from 'sweetalert2';

const isSyncing = ref(false);
const showSuccessModal = ref(false);
const showQRModal = ref(false);
const syncedCount = ref(0);
const totalCount = ref(0);
const currentReport = ref(null);
const selectedTrackingCode = ref('');
const syncedTrackingCodes = ref([]);

const progressPercentage = computed(() => {
    if (totalCount.value === 0) return 0;
    return Math.round((syncedCount.value / totalCount.value) * 100);
});

const formatDate = (timestamp) => {
    return new Date(timestamp).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const showQRCode = (tracking) => {
    selectedTrackingCode.value = tracking.trackingCode;
    showQRModal.value = true;
};

const closeQRModal = () => {
    showQRModal.value = false;
    selectedTrackingCode.value = '';
};

const closeSuccessModal = () => {
    showSuccessModal.value = false;
    syncedTrackingCodes.value = [];
};

const copyTrackingCode = async () => {
    try {
        await navigator.clipboard.writeText(selectedTrackingCode.value);
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Tracking code copied to clipboard',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    } catch (err) {
        console.error('Failed to copy:', err);
    }
};

// Listen for sync events
const handleSyncStart = (event) => {
    isSyncing.value = true;
    totalCount.value = event.detail.total;
    syncedCount.value = 0;
    syncedTrackingCodes.value = [];
};

const handleSyncProgress = (event) => {
    syncedCount.value = event.detail.synced;
    currentReport.value = event.detail.currentReport;
};

const handleSyncComplete = (event) => {
    isSyncing.value = false;
    syncedTrackingCodes.value = event.detail.trackingCodes;
    showSuccessModal.value = true;
    
    // Show success notification
    Swal.fire({
        icon: 'success',
        title: 'Sync Complete!',
        text: `${event.detail.total} reports synced successfully`,
        timer: 3000,
        showConfirmButton: false,
        position: 'top-end'
    });
};

onMounted(() => {
    window.addEventListener('syncStart', handleSyncStart);
    window.addEventListener('syncProgress', handleSyncProgress);
    window.addEventListener('syncComplete', handleSyncComplete);
});

onUnmounted(() => {
    window.removeEventListener('syncStart', handleSyncStart);
    window.removeEventListener('syncProgress', handleSyncProgress);
    window.removeEventListener('syncComplete', handleSyncComplete);
});
</script>
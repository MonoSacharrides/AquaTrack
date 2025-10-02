<template>
    <transition name="modal">
        <div v-if="isOpen" class="fixed inset-0 z-[200] overflow-y-auto" aria-labelledby="notification-modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                    @click="close"></div>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                                        Report Notifications
                                    </h3>
                                    <button @click="close" class="text-gray-400 hover:text-gray-500">
                                        <v-icon name="bi-x-lg" class="h-6 w-6" />
                                    </button>
                                </div>

                                <div class="mt-4 max-h-96 overflow-y-auto">
                                    <div v-if="reportsLoading" class="flex justify-center py-8">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                                    </div>
                                    <div v-else-if="reports.length === 0" class="text-center py-8 text-gray-500">
                                        No report updates available
                                    </div>
                                    <div v-else v-for="report in reports" :key="report.id"
                                        class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 mb-2">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 pt-0.5">
                                                <v-icon name="bi-flag-fill" :class="statusIconClass(report.status)" />
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        Report #{{ report.tracking_code }}
                                                    </p>
                                                    <span :class="statusBadgeClass(report.status)"
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                                        {{ formatStatus(report.status) }}
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    Location: {{ report.barangay }}, {{ report.municipality }}
                                                </p>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    Updated: {{ formatDate(report.updated_at) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            @click="close">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    isOpen: Boolean,
    initialReports: {
        type: Array,
        default: () => []
    }
});



const emit = defineEmits(['close']);

const reports = ref(props.initialReports);
const reportsLoading = ref(false);

const statusBadgeClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        in_progress: 'bg-blue-100 text-blue-800',
        resolved: 'bg-green-100 text-green-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const statusIconClass = (status) => {
    const classes = {
        pending: 'h-5 w-5 text-yellow-500',
        in_progress: 'h-5 w-5 text-blue-500',
        resolved: 'h-5 w-5 text-green-500'
    };
    return classes[status] || 'h-5 w-5 text-gray-500';
};

const formatStatus = (status) => {
    return status.split('_').map(word =>
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ');
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const close = () => {
    emit('close');
};

const fetchReports = async () => {
    reportsLoading.value = true;
    try {
        const response = await router.get('/api/reports/notifications', {}, {
            preserveState: true,
            only: ['reports']
        });
        reports.value = response.props.reports || [];
    } catch (error) {
        console.error('Error fetching reports:', error);
    } finally {
        reportsLoading.value = false;
    }
};

onMounted(() => {
    if (props.isOpen) {
        fetchReports();
    }
});
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
</style>

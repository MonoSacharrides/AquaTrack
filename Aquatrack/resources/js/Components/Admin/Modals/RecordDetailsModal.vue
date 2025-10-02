<script setup>
import { ref, computed, onMounted } from "vue";

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    record: {
        type: Object,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close"]);

const statusClass = computed(() => {
    if (!props.record || !props.record.status) return "";
    switch (props.record.status.toLowerCase()) {
        case "paid":
            return "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200";
        case "pending":
            return "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200";
        case "overdue":
            return "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200";
        default:
            return "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200";
    }
});

const statusLabel = computed(() => {
    if (!props.record || !props.record.status) return "";
    return (
        props.record.status.charAt(0).toUpperCase() +
        props.record.status.slice(1)
    );
});

// Format date for display
const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

// Computed property for surcharge
const surcharge = computed(() => {
    if (!props.record || !props.record.surcharge || props.record.status === "Paid") {
        return null;
    }
    return props.record.surcharge;
});
</script>

<template>
    <!-- Single transition wrapper for both overlay and panel -->
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-[1000] overflow-hidden">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/50 transition-opacity duration-300" @click="emit('close')"></div>

            <!-- Sliding panel container -->
            <div class="fixed inset-y-0 right-0 w-full max-w-2xl flex">
                <!-- Panel with transform class for animation -->
                <div class="relative w-full h-full transform transition-transform duration-300 ease-in-out">
                    <div class="h-full flex flex-col bg-white dark:bg-gray-800 shadow-xl">
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between px-6 py-5 bg-gradient-to-r from-[#062F64] to-[#1E4272]">
                            <div class="flex items-center space-x-3">
                                <v-icon name="bi-clipboard-data" class="text-amber-300" scale="1.5" />
                                <span class="text-white font-semibold text-xl">Concessioner's Record Details</span>
                            </div>
                            <button @click="emit('close')"
                                class="text-white hover:text-gray-200 transition-colors duration-200 p-2 rounded-full hover:bg-white/10 flex items-center justify-center"
                                aria-label="Close modal">
                                <v-icon name="bi-x-lg" class="h-5 w-5" />
                            </button>
                        </div>

                        <!-- Loading state -->
                        <div v-if="loading" class="flex-1 flex items-center justify-center">
                            <div class="text-center">
                                <div
                                    class="w-10 h-10 border-3 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4">
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">
                                    Loading record details...
                                </p>
                            </div>
                        </div>

                        <!-- Content -->
                        <div v-else-if="record" class="flex-1 overflow-y-auto p-6">
                            <div class="space-y-6">
                                <!-- Customer Information -->
                                <div
                                    class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-5 rounded-xl shadow-sm">
                                    <h3
                                        class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center pb-2 border-b border-gray-100 dark:border-gray-600">
                                        <v-icon name="bi-person" class="mr-3 text-blue-600" />
                                        Customer Information
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="flex items-start p-3 rounded-lg bg-gray-50 dark:bg-gray-600/30">
                                            <v-icon name="bi-person-badge" class="mr-3 mt-0.5 text-blue-500" />
                                            <div>
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                    Customer Name
                                                </p>
                                                <p class="font-semibold text-gray-900 dark:text-white">
                                                    {{ record.user.name }}
                                                    {{ record.user.lastname }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start p-3 rounded-lg bg-gray-50 dark:bg-gray-600/30">
                                            <v-icon name="bi-hash" class="mr-3 mt-0.5 text-blue-500" />
                                            <div>
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                    Account Number
                                                </p>
                                                <p class="font-semibold text-gray-900 dark:text-white">
                                                    {{
                                                        record.user
                                                            .account_number
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start p-3 rounded-lg bg-gray-50 dark:bg-gray-600/30">
                                            <v-icon name="bi-upc-scan" class="mr-3 mt-0.5 text-blue-500" />
                                            <div>
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                    Serial Number
                                                </p>
                                                <p class="font-semibold text-gray-900 dark:text-white">
                                                    {{
                                                        record.user
                                                            .serial_number
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start p-3 rounded-lg bg-gray-50 dark:bg-gray-600/30">
                                            <v-icon name="bi-geo-alt" class="mr-3 mt-0.5 text-blue-500" />
                                            <div>
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                    Address
                                                </p>
                                                <p class="font-semibold text-gray-900 dark:text-white">
                                                    {{ record.user.province }},
                                                    {{
                                                        record.user
                                                            .municipality
                                                    }},
                                                    {{ record.user.barangay }},
                                                    {{ record.user.zone }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start p-3 rounded-lg bg-gray-50 dark:bg-gray-600/30">
                                            <v-icon name="bi-telephone" class="mr-3 mt-0.5 text-blue-500" />
                                            <div>
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                    Contact Number
                                                </p>
                                                <p class="font-semibold text-gray-900 dark:text-white">
                                                    {{
                                                        record.user.phone ||
                                                        "N/A"
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start p-3 rounded-lg bg-gray-50 dark:bg-gray-600/30">
                                            <v-icon name="bi-envelope" class="mr-3 mt-0.5 text-blue-500" />
                                            <div>
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                    Email
                                                </p>
                                                <p class="font-semibold text-gray-900 dark:text-white">
                                                    {{
                                                        record.user.email ||
                                                        "N/A"
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reading Information -->
                                <div
                                    class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-5 rounded-xl shadow-sm">
                                    <h3
                                        class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center pb-2 border-b border-gray-100 dark:border-gray-600">
                                        <v-icon name="bi-speedometer2" class="mr-3 text-blue-600" />
                                        Reading Information
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="flex items-start p-3 rounded-lg bg-gray-50 dark:bg-gray-600/30">
                                            <v-icon name="bi-calendar-check" class="mr-3 mt-0.5 text-blue-500" />
                                            <div>
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                    Reading Date
                                                </p>
                                                <p class="font-semibold text-gray-900 dark:text-white">
                                                    {{
                                                        formatDate(
                                                            record.reading_date
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start p-3 rounded-lg bg-gray-50 dark:bg-gray-600/30">
                                            <v-icon name="bi-calendar-x" class="mr-3 mt-0.5 text-blue-500" />
                                            <div>
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                    Due Date
                                                </p>
                                                <p class="font-semibold text-gray-900 dark:text-white" :class="{
                                                    'text-red-600 dark:text-red-400':
                                                        record.status ===
                                                        'Overdue' &&
                                                        surcharge,
                                                }">
                                                    {{ formatDate(record.due_date) }}
                                                    <span v-if="record.status === 'Overdue' && surcharge"
                                                        class="text-xs ml-2 px-2 py-1 bg-red-100 text-red-800 rounded-full">Overdue</span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start p-3 rounded-lg bg-gray-50 dark:bg-gray-600/30">
                                            <v-icon name="bi-water" class="mr-3 mt-0.5 text-blue-500" />
                                            <div>
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                    Current Reading (m³)
                                                </p>
                                                <p class="font-semibold text-gray-900 dark:text-white">
                                                    {{ record.reading }} m³
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start p-3 rounded-lg bg-gray-50 dark:bg-gray-600/30">
                                            <v-icon name="bi-arrow-left-right" class="mr-3 mt-0.5 text-blue-500" />
                                            <div>
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                    Consumption (m³)
                                                </p>
                                                <p class="font-semibold text-gray-900 dark:text-white">
                                                    {{ record.consumption }} m³
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start p-3 rounded-lg bg-gray-50 dark:bg-gray-600/30">
                                            <v-icon name="bi-cash-coin" class="mr-3 mt-0.5 text-blue-500" />
                                            <div>
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                    Amount
                                                </p>
                                                <p class="font-semibold text-gray-900 dark:text-white text-lg">
                                                    ₱{{ record.amount }}
                                                    <span v-if="surcharge && record.status !== 'Paid'"
                                                        class="text-sm text-red-600 dark:text-red-400">
                                                        (+₱{{ surcharge }} surcharge)
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start p-3 rounded-lg bg-gray-50 dark:bg-gray-600/30">
                                            <v-icon name="bi-check-circle" class="mr-3 mt-0.5 text-blue-500" />
                                            <div>
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                    Status
                                                </p>
                                                <span class="px-3 py-1.5 text-sm font-semibold rounded-full"
                                                    :class="statusClass">
                                                    {{ statusLabel }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="border-t border-gray-200 dark:border-gray-600 px-6 py-4 bg-gray-50 dark:bg-gray-700">
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Record ID: {{ record.id }}
                                </div>
                                <button @click="emit('close')" type="button"
                                    class="inline-flex justify-center items-center rounded-lg border border-gray-300 dark:border-gray-500 shadow-sm px-4 py-2.5 bg-white dark:bg-gray-600 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    <v-icon name="bi-x-lg" class="mr-2" />
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
/* Custom scrollbar for the content */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #e2e8f0 #f8fafc;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: #94a3b8;
}

/* Dark mode scrollbar */
.dark .overflow-y-auto {
    scrollbar-color: #4b5563 #1f2937;
}

.dark .overflow-y-auto::-webkit-scrollbar-track {
    background: #1f2937;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #4b5563;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: #6b7280;
}

/* Modal transition styles */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .transform,
.modal-leave-active .transform {
    transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.5, 1);
}

.modal-enter-from .transform {
    transform: translateX(100%);
}

.modal-leave-to .transform {
    transform: translateX(100%);
}

/* Animation for info cards */
.bg-gray-50 {
    transition: all 0.2s ease;
}

.bg-gray-50:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.dark .bg-gray-50:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}
</style>
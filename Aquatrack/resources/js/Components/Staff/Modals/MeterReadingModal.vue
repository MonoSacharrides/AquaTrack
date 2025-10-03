<template>
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-[1000] overflow-hidden">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/50 transition-opacity duration-300" @click="closeModal"></div>

            <!-- Sliding panel container -->
            <div class="fixed inset-y-0 right-0 w-full max-w-2xl flex">
                <!-- Panel with transform class for animation -->
                <div class="relative w-full h-full transform transition-transform duration-300 ease-in-out">
                    <div class="h-full flex flex-col bg-white dark:bg-gray-800 shadow-xl">
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between px-6 py-5 bg-gradient-to-r from-[#062F64] to-[#1E4272]">
                            <div class="flex items-center space-x-2">
                                <v-icon name="bi-speedometer2" class="text-amber-300" scale="1.5" />
                                <span class="text-white font-[200] text-xl">Meter Reading Card</span>
                            </div>
                            <button @click="closeModal"
                                class="text-white hover:text-gray-200 transition-colors duration-200 p-1 rounded-full hover:bg-white/10">
                                <v-icon name="bi-x-lg" class="h-6 w-6" />
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 overflow-y-auto p-6">
                            <!-- Customer Summary Card -->
                            <div
                                class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-5 rounded-lg mb-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                            <v-icon name="bi-person-circle"
                                                class="text-blue-600 dark:text-blue-400 text-xl" />
                                        </div>
                                        <div>
                                            <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ user.name }}
                                                {{ user.lastname }}</h2>
                                            <p class="text-gray-600 dark:text-gray-300 mt-1">{{ user.address }}</p>
                                            <div class="flex gap-4 mt-2">
                                                <span
                                                    class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                                                    <v-icon name="bi-credit-card" class="text-blue-500" />
                                                    {{ user.account_number }}
                                                </span>
                                                <span
                                                    class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                                                    <v-icon name="bi-telephone" class="text-blue-500" />
                                                    {{ user.phone }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">
                                            <v-icon name="bi-check-circle" class="mr-1" /> Active
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Installed: {{
                                            formatDate(user.date_installed) || 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Meter Details Grid -->
                            <div class="grid grid-cols-2 gap-3 mb-6">
                                <div
                                    class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-100 dark:border-blue-700">
                                    <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400 mb-1">
                                        <v-icon name="bi-tag" class="text-blue-500" />
                                        <span class="text-xs font-medium">Brand</span>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-800 dark:text-white">{{ user.brand ||
                                        'Not specified' }}</div>
                                </div>

                                <div
                                    class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-100 dark:border-blue-700">
                                    <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400 mb-1">
                                        <v-icon name="bi-upc-scan" class="text-blue-500" />
                                        <span class="text-xs font-medium">Serial No.</span>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-800 dark:text-white">{{
                                        user.serial_number || 'N/A' }}</div>
                                </div>

                                <div
                                    class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-100 dark:border-blue-700">
                                    <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400 mb-1">
                                        <v-icon name="bi-rulers" class="text-blue-500" />
                                        <span class="text-xs font-medium">Size</span>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-800 dark:text-white">{{ user.size ||
                                        'N/A' }} mm</div>
                                </div>

                                <div
                                    class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-100 dark:border-blue-700">
                                    <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400 mb-1">
                                        <v-icon name="bi-calendar-check" class="text-blue-500" />
                                        <span class="text-xs font-medium">Last Reading</span>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-800 dark:text-white">{{ lastReadingDate
                                        || 'No records' }}</div>
                                </div>
                            </div>

                            <!-- Meter Reading Form -->
                            <div
                                class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-5 mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                                    <v-icon name="bi-speedometer2" class="mr-2" />
                                    New Meter Reading
                                </h3>

                                <!-- Year Transition Warning -->
                                <div v-if="showYearTransitionWarning"
                                    class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-600 mb-4 flex items-start gap-3">
                                    <div class="p-2 bg-yellow-100 dark:bg-yellow-800 rounded-lg mt-0.5">
                                        <v-icon name="bi-exclamation-triangle"
                                            class="text-yellow-600 dark:text-yellow-400" />
                                    </div>
                                    <div>
                                        <div class="font-medium text-yellow-800 dark:text-yellow-300">Year Transition
                                            Detected</div>
                                        <p class="text-sm text-yellow-700 dark:text-yellow-400 mt-1">
                                            You're entering a reading for January after December. Please ensure this is
                                            correct
                                            as it represents a new billing year.
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Previous Reading (m³)
                                        </label>
                                        <div class="relative">
                                            <input v-model="newReading.previous_reading" type="number" step="0.01"
                                                :class="[
                                                    'w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 pr-10',
                                                    hasPreviousReadings ? 'bg-gray-50 dark:bg-gray-600' : 'bg-white dark:bg-gray-700'
                                                ]" :disabled="hasPreviousReadings || isSubmitting"
                                                @input="calculateConsumptionAndAmount"
                                                placeholder="Enter previous reading">
                                            <span
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">m³</span>
                                        </div>
                                        <p v-if="!hasPreviousReadings"
                                            class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Enter the initial meter reading
                                        </p>
                                        <p v-else class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Last recorded reading (automatically populated)
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Current Reading (m³)
                                        </label>
                                        <div class="relative">
                                            <input v-model="newReading.reading" type="number" step="0.01"
                                                class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 pr-10"
                                                :class="{ 'border-red-300 dark:border-red-500': readingError }"
                                                :disabled="isSubmitting" @input="calculateConsumptionAndAmount"
                                                placeholder="Enter current reading">
                                            <span
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">m³</span>
                                        </div>
                                        <p v-if="readingError" class="text-xs text-red-500 dark:text-red-400 mt-1">{{
                                            readingError }}</p>
                                        <p v-else class="text-xs text-gray-500 dark:text-gray-400 mt-1">Must be higher
                                            than previous reading</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Consumption (m³)
                                        </label>
                                        <div class="relative">
                                            <input v-model="newReading.consumption" type="number" step="0.01"
                                                class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 pr-10"
                                                disabled>
                                            <span
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">m³</span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Calculated
                                            automatically</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Amount (₱)
                                        </label>
                                        <div class="relative">
                                            <input v-model="newReading.amount" type="number" step="0.01"
                                                class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 pr-10"
                                                disabled>
                                            <span
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">₱</span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Based on consumption
                                            rate</p>
                                    </div>
                                </div>

                                <!-- Summary Card -->
                                <div v-if="newReading.consumption > 0"
                                    class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-700">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-sm text-blue-700 dark:text-blue-300 font-medium">Billing
                                                Summary</div>
                                            <div class="text-xl font-bold text-blue-800 dark:text-blue-200 mt-1">₱{{
                                                newReading.amount.toFixed(2) }}</div>
                                            <div class="text-sm text-blue-600 dark:text-blue-400 mt-1">{{
                                                newReading.consumption }} m³ consumption</div>
                                        </div>
                                        <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg">
                                            <v-icon name="bi-receipt"
                                                class="text-blue-600 dark:text-blue-300 text-xl" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Previous Readings Section -->
                            <div
                                class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-5">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Reading History</h3>

                                    <!-- Year Filter -->
                                    <div class="flex items-center gap-2">
                                        <v-icon name="bi-filter" class="text-gray-500 dark:text-gray-400" />
                                        <select v-model="selectedYear"
                                            class="p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                            <option value="">All Years</option>
                                            <option v-for="year in availableYears" :key="year" :value="year">{{ year }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div v-if="isLoadingPreviousReadings" class="text-center py-8">
                                    <div class="inline-flex items-center text-blue-600 dark:text-blue-400">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span>Loading reading history...</span>
                                    </div>
                                </div>

                                <div v-else>
                                    <div v-if="filteredPreviousReadings.length === 0"
                                        class="text-center py-8 text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-600 rounded-lg">
                                        <v-icon name="bi-clock-history" class="text-2xl mb-2 opacity-50" />
                                        <p>No previous readings found</p>
                                        <p class="text-sm mt-1">Start by submitting a new reading above</p>
                                    </div>

                                    <div v-else class="space-y-3 max-h-[300px] overflow-y-auto">
                                        <div v-for="(reading, index) in filteredPreviousReadings" :key="reading.id"
                                            class="bg-gray-50 dark:bg-gray-600 p-3 rounded-lg border border-gray-200 dark:border-gray-500 hover:border-blue-200 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors group relative">
                                            <!-- Edit Button -->
                                            <button @click="openEditModal(reading)"
                                                class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:border-blue-300 dark:hover:border-blue-500"
                                                title="Edit Reading">
                                                <v-icon name="bi-pencil"
                                                    class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 h-3.5 w-3.5" />
                                            </button>

                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="p-2 bg-white dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                                        <v-icon name="bi-droplet" class="text-blue-500" />
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-gray-800 dark:text-white">{{
                                                            reading.billing_month }} {{ reading.year }}</div>
                                                        <div class="text-sm text-gray-600 dark:text-gray-300">{{
                                                            formatDate(reading.reading_date) }}</div>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="font-semibold text-blue-600 dark:text-blue-400">{{
                                                        reading.reading }} m³</div>
                                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{
                                                        reading.consumption }} m³ consumed</div>
                                                </div>
                                            </div>
                                            <div
                                                class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200 dark:border-gray-500">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm text-gray-600 dark:text-gray-400">Amount
                                                        billed</span>
                                                    <span :class="[
                                                        'px-2 py-1 rounded-full text-xs font-medium',
                                                        reading.status === 'Paid' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' :
                                                            reading.status === 'Pending' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' :
                                                                'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200'
                                                    ]">
                                                        {{ reading.status }}
                                                    </span>
                                                </div>
                                                <div class="font-medium text-green-600 dark:text-green-400">₱{{
                                                    reading.amount.toFixed(2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="border-t border-gray-200 dark:border-gray-600 p-6 bg-white dark:bg-gray-800">
                            <div class="flex gap-3 justify-end">
                                <button @click="closeModal" type="button"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-md transition-colors duration-200"
                                    :disabled="isSubmitting">
                                    Cancel
                                </button>
                                <button @click="submitReading" type="button"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-md transition-colors duration-200 flex items-center min-w-[120px] justify-center"
                                    :disabled="isSubmitting || !isFormValid">
                                    <span v-if="!isSubmitting" class="flex items-center">
                                        <v-icon name="bi-check-circle" class="mr-1" />
                                        Submit Reading
                                    </span>
                                    <span v-else class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Submitting...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>

    <!-- Edit Reading Modal -->
    <EditMeterRecordModal :show="showEditModal" :record="selectedReading" :user="user" @close="closeEditModal"
        @saved="handleReadingUpdated" />
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import Swal from 'sweetalert2';
import EditMeterRecordModal from './EditMeterRecordModal.vue';

const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    user: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['close', 'reading-submitted']);

const months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

const newReading = ref({
    billing_month: '',
    reading_date: '',
    reading: '',
    previous_reading: 0,
    consumption: 0,
    amount: 0
});

const previousReadings = ref([]);
const selectedYear = ref('');
const availableYears = computed(() => {
    const years = new Set(previousReadings.value.map(reading => reading.year));
    return [...years].sort((a, b) => b - a);
});

const filteredPreviousReadings = computed(() => {
    return sortedPreviousReadings.value.filter(reading =>
        !selectedYear.value || reading.year === selectedYear.value
    );
});

const sortedPreviousReadings = computed(() => {
    return [...previousReadings.value].sort((a, b) => {
        const dateA = new Date(`${a.billing_month} 1, ${a.year}`);
        const dateB = new Date(`${b.billing_month} 1, ${b.year}`);
        return dateB - dateA;
    });
});

const isLoadingPreviousReadings = ref(false);
const isSubmitting = ref(false);
const showYearTransitionWarning = ref(false);

// Edit modal state
const showEditModal = ref(false);
const selectedReading = ref(null);

// Computed property to check if previous readings exist
const hasPreviousReadings = computed(() => {
    return previousReadings.value && previousReadings.value.length > 0;
});

// Computed property for last reading date
const lastReadingDate = computed(() => {
    if (previousReadings.value.length === 0) return null;
    const latest = sortedPreviousReadings.value[0];
    return formatDate(latest.reading_date);
});

// Computed property for reading validation error
const readingError = computed(() => {
    if (!newReading.value.reading) return '';
    const current = parseFloat(newReading.value.reading);
    const previous = parseFloat(newReading.value.previous_reading);

    if (current < previous) {
        return `Current reading must be higher than previous reading (${previous} m³)`;
    }

    return '';
});

// Watch for changes in previous readings to update the previous reading value
watch(previousReadings, (newVal) => {
    if (newVal.length > 0) {
        updatePreviousReading();
    } else {
        newReading.value.previous_reading = 0;
    }
}, { deep: true });

const updatePreviousReading = () => {
    // If there are previous readings, use the latest one
    if (hasPreviousReadings.value) {
        if (!newReading.value.billing_month) {
            newReading.value.previous_reading = 0;
            return;
        }

        const selectedDate = new Date(newReading.value.reading_date);

        // Find the most recent reading before the selected date
        const readingsBeforeCurrent = previousReadings.value.filter(reading => {
            const readingDate = new Date(`${reading.billing_month} 1, ${reading.year}`);
            return readingDate < selectedDate;
        });

        // Check for year transition (December to January)
        const hasDecemberReading = previousReadings.value.some(r => r.billing_month === 'December' && parseInt(r.year) === selectedDate.getFullYear() - 1);
        showYearTransitionWarning.value = (months.indexOf(newReading.value.billing_month) === 0 && hasDecemberReading);

        if (readingsBeforeCurrent.length > 0) {
            // Get the latest reading before the selected date
            const latestReading = readingsBeforeCurrent.reduce((latest, current) => {
                const latestDate = new Date(`${latest.billing_month} 1, ${latest.year}`);
                const currentDate = new Date(`${current.billing_month} 1, ${current.year}`);
                return currentDate > latestDate ? current : latest;
            });

            newReading.value.previous_reading = latestReading.reading;
        } else {
            // If no readings before selected date, use the first reading
            const firstReading = [...previousReadings.value].sort((a, b) => {
                const dateA = new Date(`${a.billing_month} 1, ${a.year}`);
                const dateB = new Date(`${b.billing_month} 1, ${b.year}`);
                return dateA - dateB;
            })[0];

            newReading.value.previous_reading = firstReading ? firstReading.reading : 0;
        }
    } else {
        // If no previous readings, keep the user's input or default to 0
        newReading.value.previous_reading = newReading.value.previous_reading || 0;
    }

    // Recalculate consumption and amount
    calculateConsumptionAndAmount();
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    } catch (error) {
        return 'Invalid date';
    }
};

const isFormValid = computed(() => {
    const current = parseFloat(newReading.value.reading) || 0;
    const previous = parseFloat(newReading.value.previous_reading) || 0;

    return (
        newReading.value.billing_month &&
        newReading.value.reading_date &&
        newReading.value.reading !== '' &&
        current >= previous &&
        (!hasPreviousReadings.value || previous > 0) // If no previous readings, ensure previous is provided
    );
});

const closeModal = () => {
    emit('close');
};

const calculateConsumptionAndAmount = () => {
    const currentReading = parseFloat(newReading.value.reading) || 0;
    const previousReading = parseFloat(newReading.value.previous_reading) || 0;

    // Calculate consumption
    newReading.value.consumption = Math.max(0, currentReading - previousReading);

    // Calculate amount only if there's consumption
    if (newReading.value.consumption > 0) {
        const consumption = newReading.value.consumption;
        let amount = 0;

        if (consumption <= 10) {
            amount = 132.00;
        } else if (consumption <= 20) {
            amount = 132.00 + (consumption - 10) * 14;
        } else if (consumption <= 30) {
            amount = 132.00 + (10 * 14) + ((consumption - 20) * 14.85);
        } else if (consumption <= 40) {
            amount = 132.00 + (10 * 14) + (10 * 14.85) + ((consumption - 30) * 16);
        } else {
            amount = 132.00 + (10 * 14) + (10 * 14.85) + (10 * 16) + ((consumption - 40) * 17.25);
        }

        newReading.value.amount = parseFloat(amount.toFixed(2));
    } else {
        newReading.value.amount = 0;
    }
};

const fetchPreviousReadings = async () => {
    isLoadingPreviousReadings.value = true;
    try {
        const response = await axios.get(route('staff.reading.previous', { userId: props.user.id }));

        if (response.data.error) {
            if (response.status === 404) {
                Swal.fire({
                    icon: 'error',
                    title: 'Customer not found',
                    text: 'The customer record could not be located',
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error loading readings',
                    text: 'There was a problem loading the previous meter readings',
                });
            }
            previousReadings.value = [];
        } else {
            previousReadings.value = response.data || [];
        }
    } catch (error) {
        console.error('Error fetching previous readings:', error);
        Swal.fire({
            icon: 'error',
            title: 'Failed to load readings',
            text: 'Please try again later',
        });
        previousReadings.value = [];
    } finally {
        isLoadingPreviousReadings.value = false;
    }
};

const submitReading = async () => {
    if (!isFormValid.value) return;

    // Validate that current reading is not less than previous reading
    const current = parseFloat(newReading.value.reading);
    const previous = parseFloat(newReading.value.previous_reading);

    if (current < previous) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Reading',
            text: 'Current reading cannot be less than previous reading',
        });
        return;
    }

    // Additional validation for initial reading
    if (!hasPreviousReadings.value && previous <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Previous Reading',
            text: 'Previous reading must be greater than 0 for initial reading',
        });
        return;
    }

    // Additional validation for year transition
    const selectedDate = new Date(newReading.value.reading_date);
    const selectedYear = selectedDate.getFullYear();
    const currentMonthIndex = months.indexOf(newReading.value.billing_month);
    const hasDecemberReading = previousReadings.value.some(r => r.billing_month === 'December' && parseInt(r.year) === selectedYear - 1);

    if (currentMonthIndex === 0 && hasDecemberReading) {
        // This is January and there's a December reading from the previous year - confirm this is intentional
        const result = await Swal.fire({
            title: 'Year Transition Confirmation',
            html: `You're entering a reading for <strong>January ${selectedYear}</strong> after a <strong>December ${selectedYear - 1}</strong> reading.<br><br>
                   This indicates a new billing year. Is this correct?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, new year',
            cancelButtonText: 'No, cancel'
        });

        if (!result.isConfirmed) {
            return;
        }
    }

    isSubmitting.value = true;
    try {
        const result = await Swal.fire({
            title: 'Confirm Submission',
            html: `Submit meter reading for ${props.user.name} ${props.user.lastname}?<br>
                   <strong>Billing Month:</strong> ${newReading.value.billing_month}<br>
                   <strong>Reading:</strong> ${newReading.value.reading} m³<br>
                   ${newReading.value.consumption > 0 ? `<strong>Consumption:</strong> ${newReading.value.consumption} m³<br>` : ''}
                   ${newReading.value.amount > 0 ? `<strong>Amount:</strong> ₱${newReading.value.amount.toFixed(2)}` : ''}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, submit it!',
            cancelButtonText: 'Cancel'
        });

        if (result.isConfirmed) {
            const response = await axios.post(route('staff.reading.store'), {
                user_id: props.user.id,
                billing_month: newReading.value.billing_month,
                reading_date: newReading.value.reading_date,
                reading: newReading.value.reading,
                previous_reading: newReading.value.previous_reading
            });

            if (response.data.error) {
                throw new Error(response.data.error);
            }

            // Refresh previous readings to get the updated list
            await fetchPreviousReadings();

            // Reset only the current reading field, keep others
            newReading.value.reading = '';
            newReading.value.consumption = 0;
            newReading.value.amount = 0;
            showYearTransitionWarning.value = false;

            // Show success message but don't close the modal
            await Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Meter reading submitted successfully',
                timer: 2000,
                showConfirmButton: false
            });

            // Emit event to parent if needed
            emit('reading-submitted');
        }
    } catch (error) {
        console.error('Error submitting reading:', error);
        Swal.fire({
            icon: 'error',
            title: 'Submission Failed',
            text: error.response?.data?.error || error.message || 'There was an error submitting the meter reading',
        });
    } finally {
        isSubmitting.value = false;
    }
};

// Edit modal functions
const openEditModal = (reading) => {
    selectedReading.value = reading;
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    selectedReading.value = null;
};

const handleReadingUpdated = async (updatedReading) => {
    // Refresh the entire readings list to get updated data
    await fetchPreviousReadings();

    // Update the previous reading calculation
    updatePreviousReading();

    closeEditModal();
};

onMounted(() => {
    // Set default values for new reading
    const today = new Date();
    const currentMonth = months[today.getMonth()];

    // Fetch previous readings first
    fetchPreviousReadings().then(() => {
        newReading.value = {
            billing_month: currentMonth,
            reading_date: today.toISOString().split('T')[0],
            reading: '',
            previous_reading: hasPreviousReadings.value ? 0 : '',
            consumption: 0,
            amount: 0
        };

        // Update the previous reading based on fetched data
        updatePreviousReading();
    });

    // Set default year filter to current year
    selectedYear.value = today.getFullYear().toString();
});
</script>

<style scoped>
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
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #e2e8f0;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: #cbd5e1;
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
</style>
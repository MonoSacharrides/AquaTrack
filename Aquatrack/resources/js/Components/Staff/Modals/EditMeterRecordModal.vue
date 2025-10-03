<template>
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-[1200] overflow-hidden">
            <!-- Overlay with lower z-index than SweetAlert -->
            <div class="absolute inset-0 bg-black/60 transition-opacity duration-300" @click="confirmClose"></div>

            <!-- Sliding panel container -->
            <div class="fixed inset-y-0 right-0 w-full max-w-2xl flex">
                <!-- Panel with transform class for animation -->
                <div class="relative w-full h-full transform transition-transform duration-300 ease-in-out">
                    <div class="h-full flex flex-col bg-white dark:bg-gray-800 shadow-xl">
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between px-6 py-5 bg-gradient-to-r from-[#062F64] to-[#1E4272]">
                            <div class="flex items-center space-x-2">
                                <v-icon name="la-user-edit-solid" class="text-amber-300" scale="1.5" />
                                <span class="text-white font-[200] text-xl">Edit Meter Reading</span>
                            </div>
                            <button @click="confirmClose"
                                class="text-white hover:text-gray-200 transition-colors duration-200 p-1 rounded-full hover:bg-white/10">
                                <v-icon name="bi-x-lg" class="h-6 w-6" />
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 overflow-y-auto p-6">
                            <!-- Customer Info Summary -->
                            <div
                                class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-5 rounded-lg mb-6">
                                <div class="flex items-center gap-4">
                                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                        <v-icon name="bi-person-circle"
                                            class="text-blue-600 dark:text-blue-400 text-xl" />
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ user.name }} {{
                                            user.lastname }}</h2>
                                        <p class="text-gray-600 dark:text-gray-300 mt-1">{{ user.address }}</p>
                                        <div class="flex gap-4 mt-2">
                                            <span
                                                class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                                                <v-icon name="bi-credit-card" class="text-blue-500" />
                                                {{ user.account_number }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reading Information Section -->
                            <div
                                class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-6 rounded-lg mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                                    <v-icon name="bi-speedometer2" class="mr-2" />
                                    Meter Reading Details
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Billing Period
                                        </label>
                                        <div
                                            class="p-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white">
                                            {{ record.billing_month }} {{ record.year }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Reading Date
                                        </label>
                                        <div
                                            class="p-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white">
                                            {{ formatDate(record.reading_date) }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Previous Reading (m³)
                                        </label>
                                        <div
                                            class="p-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white">
                                            {{ record.previous_reading }} m³
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Current Reading (m³) <span class="text-red-500">*</span>
                                        </label>
                                        <input v-model="form.reading" type="number" step="0.01" min="0"
                                            class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                            :class="{ 'border-red-300 dark:border-red-500': form.errors.reading }"
                                            required @input="validateReading" />
                                        <p v-if="form.errors.reading" class="text-red-500 text-xs mt-1">{{
                                            form.errors.reading }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Consumption (m³)
                                        </label>
                                        <div
                                            class="p-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white">
                                            {{ calculatedConsumption }} m³
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Amount (₱) <span class="text-red-500">*</span>
                                        </label>
                                        <input v-model="form.amount" type="number" step="0.01" min="0"
                                            class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                            :class="{ 'border-red-300 dark:border-red-500': form.errors.amount }"
                                            required @input="validateAmount" />
                                        <p v-if="form.errors.amount" class="text-red-500 text-xs mt-1">{{
                                            form.errors.amount }}</p>
                                    </div>
                                </div>

                                <!-- Consumption Summary -->
                                <div v-if="calculatedConsumption > 0"
                                    class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-700">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-sm text-blue-700 dark:text-blue-300 font-medium">Updated
                                                Billing Summary</div>
                                            <div class="text-xl font-bold text-blue-800 dark:text-blue-200 mt-1">₱{{
                                                calculatedAmount.toFixed(2) }}</div>
                                            <div class="text-sm text-blue-600 dark:text-blue-400 mt-1">{{
                                                calculatedConsumption }} m³ consumption</div>
                                        </div>
                                        <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg">
                                            <v-icon name="bi-receipt"
                                                class="text-blue-600 dark:text-blue-300 text-xl" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="border-t border-gray-200 dark:border-gray-600 p-6 bg-white dark:bg-gray-800">
                            <div class="flex gap-3 justify-end">
                                <button @click="confirmClose" type="button"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-md transition-colors duration-200"
                                    :disabled="isSubmitting">
                                    Cancel
                                </button>
                                <button @click="submitForm" type="button"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-md transition-colors duration-200 flex items-center min-w-[120px] justify-center"
                                    :disabled="isSubmitting || !isFormValid">
                                    <span v-if="!isSubmitting" class="flex items-center">
                                        <v-icon name="bi-check-circle" class="mr-1" />
                                        Update Reading
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
                                        Updating...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

// Configure SweetAlert to have higher z-index
Swal.mixin({
    customClass: {
        popup: '!z-[1300]', // Higher than edit modal
        container: '!z-[1300]'
    }
});

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    record: {
        type: Object,
        default: null,
    },
    user: {
        type: Object,
        required: true,
    }
});

const emit = defineEmits(['close', 'saved']);

// Status options
const statusOptions = [
    {
        value: 'Paid',
        label: 'Paid',
        description: 'Payment has been completed',
        icon: 'bi-check-circle',
        classes: 'peer-checked:border-green-500 dark:peer-checked:border-green-400 text-green-600 dark:text-green-400',
    },
    {
        value: 'Pending',
        label: 'Pending',
        description: 'Awaiting payment',
        icon: 'bi-clock',
        classes: 'peer-checked:border-yellow-500 dark:peer-checked:border-yellow-400 text-yellow-600 dark:text-yellow-400',
    },
];

// Initialize form with all required fields
const form = useForm({
    reading: props.record?.reading || 0,
    amount: props.record?.amount || 0,
    status: props.record?.status || 'Pending',
    consumption: props.record?.consumption || 0,
});

const isSubmitting = ref(false);
const hasUnsavedChanges = ref(false);

// Update form when record changes
watch(
    () => props.record,
    (newRecord) => {
        if (newRecord) {
            form.reading = newRecord.reading;
            form.amount = newRecord.amount;
            form.status = newRecord.status;
            form.consumption = newRecord.consumption;
            hasUnsavedChanges.value = false;
        }
    },
    { immediate: true }
);

// Watch form for changes
watch(
    form,
    () => {
        hasUnsavedChanges.value = true;
    },
    { deep: true }
);

// Watch reading changes to auto-calculate amount
watch(
    () => form.reading,
    (newReading) => {
        if (newReading !== '' && !isNaN(newReading)) {
            // Auto-calculate the amount based on consumption
            form.amount = calculatedAmount.value;
        }
    }
);

// Computed properties
const calculatedConsumption = computed(() => {
    const current = parseFloat(form.reading) || 0;
    const previous = parseFloat(props.record?.previous_reading) || 0;
    return Math.max(0, current - previous);
});

const calculatedAmount = computed(() => {
    const consumption = calculatedConsumption.value;
    if (consumption <= 0) return 0;

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

    return parseFloat(amount.toFixed(2));
});

const isFormValid = computed(() => {
    const current = parseFloat(form.reading) || 0;
    const previous = parseFloat(props.record?.previous_reading) || 0;

    return (
        form.reading !== '' &&
        form.amount !== '' &&
        form.status !== '' &&
        current >= previous &&
        form.amount >= 0
    );
});

// Methods
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    } catch (error) {
        return 'Invalid date';
    }
};

const validateReading = () => {
    const current = parseFloat(form.reading) || 0;
    const previous = parseFloat(props.record?.previous_reading) || 0;

    if (current < previous) {
        form.errors.reading = `Current reading must be higher than previous reading (${previous} m³)`;
    } else if (form.reading === '') {
        form.errors.reading = 'Reading is required';
    } else {
        form.errors.reading = '';
        // Auto-update the amount when reading is valid
        form.amount = calculatedAmount.value;
    }
};

const validateAmount = () => {
    if (form.amount < 0) {
        form.errors.amount = 'Amount cannot be negative';
    } else if (form.amount === '') {
        form.errors.amount = 'Amount is required';
    } else {
        form.errors.amount = '';
    }
};

const confirmClose = () => {
    if (hasUnsavedChanges.value && !isSubmitting.value) {
        Swal.fire({
            title: 'Unsaved Changes',
            text: 'You have unsaved changes. Are you sure you want to close?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, close',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: '!z-[1300]',
                container: '!z-[1300]'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                emit('close');
            }
        });
    } else {
        emit('close');
    }
};


const submitForm = async () => {
    if (!isFormValid.value) return;

    // Validate reading again before submission
    const current = parseFloat(form.reading) || 0;
    const previous = parseFloat(props.record?.previous_reading) || 0;

    if (current < previous) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Reading',
            text: `Current reading must be higher than previous reading (${previous} m³)`,
            customClass: {
                popup: '!z-[1300]',
                container: '!z-[1300]'
            }
        });
        return;
    }

    isSubmitting.value = true;
    try {
        const result = await Swal.fire({
            title: 'Confirm Update',
            html: `Update meter reading for <strong>${props.user.name}</strong>?<br><br>
                   <div class="text-left">
                     <strong>Billing Month:</strong> ${props.record.billing_month} ${props.record.year}<br>
                     <strong>Reading:</strong> ${form.reading} m³<br>
                     <strong>Consumption:</strong> ${calculatedConsumption.value} m³<br>
                     <strong>Amount:</strong> ₱${parseFloat(form.amount).toFixed(2)}<br>
                     <strong>Status:</strong> ${form.status}<br>
                   </div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: '!z-[1300]',
                container: '!z-[1300]'
            }
        });

        if (result.isConfirmed) {
            const response = await axios.put(route('staff.reading.update', { readingId: props.record.id }), {
                reading: parseFloat(form.reading),
                amount: parseFloat(form.amount),
                status: form.status,
                consumption: calculatedConsumption.value
            });

            if (response.data.error) {
                throw new Error(response.data.error);
            }

            hasUnsavedChanges.value = false;

            await Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Meter reading updated successfully',
                timer: 2000,
                showConfirmButton: false,
                customClass: {
                    popup: '!z-[1300]',
                    container: '!z-[1300]'
                }
            });

            // Emit the updated reading data so parent can refresh
            emit('saved', response.data.reading);
        }
    } catch (error) {
        console.error('Error updating reading:', error);
        Swal.fire({
            icon: 'error',
            title: 'Update Failed',
            text: error.response?.data?.error || error.message || 'There was an error updating the meter reading',
            customClass: {
                popup: '!z-[1300]',
                container: '!z-[1300]'
            }
        });
    } finally {
        isSubmitting.value = false;
    }
};

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

/* Custom scrollbar */
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

/* Ensure SweetAlert appears on top */
:deep(.swal2-container) {
    z-index: 1300 !important;
}

:deep(.swal2-popup) {
    z-index: 1300 !important;
}
</style>
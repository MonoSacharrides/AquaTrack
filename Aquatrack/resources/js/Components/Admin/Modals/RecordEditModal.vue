<!-- RecordEditModal.vue -->
<template>
    <transition name="modal">
        <div
            v-if="show"
            class="fixed inset-0 z-[1000] overflow-hidden"
            role="dialog"
            aria-labelledby="modal-title"
            aria-modal="true"
            @keydown.esc="confirmClose"
        >
            <!-- Overlay with better contrast and click feedback -->
            <div
                class="absolute inset-0 bg-black/60 transition-opacity duration-300"
                @click="confirmClose"
                aria-label="Close modal"
            ></div>

            <!-- Sliding panel container -->
            <div class="fixed inset-y-0 right-0 w-full max-w-2xl flex">
                <!-- Panel with transform class for animation -->
                <div
                    class="relative w-full h-full transform transition-transform duration-300 ease-in-out"
                >
                    <div
                        class="h-full flex flex-col bg-white dark:bg-gray-800 shadow-xl"
                    >
                        <!-- Header with improved accessibility -->
                        <div
                            class="flex items-center justify-between px-6 py-5 bg-gradient-to-r from-[#062F64] to-[#1E4272]"
                        >
                            <div class="flex items-center space-x-2">
                                <v-icon
                                    name="la-user-edit-solid"
                                    class="text-amber-300"
                                    scale="1.5"
                                    aria-hidden="true"
                                />
                                <h2
                                    id="modal-title"
                                    class="text-white font-[200] text-xl"
                                >
                                    Edit Water Meter Record
                                </h2>
                            </div>
                            <button
                                @click="confirmClose"
                                class="text-white hover:text-gray-200 transition-colors duration-200 p-1 rounded-full hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                aria-label="Close modal"
                                title="Close"
                            >
                                <v-icon name="bi-x-lg" class="h-6 w-6" />
                            </button>
                        </div>

                        <!-- Loading state with better styling -->
                        <div
                            v-if="loading"
                            class="flex-1 flex items-center justify-center bg-gray-100 dark:bg-gray-900"
                        >
                            <div class="text-center">
                                <div
                                    class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"
                                ></div>
                                <p
                                    class="text-gray-600 dark:text-gray-300 text-sm font-medium"
                                >
                                    Loading record details...
                                </p>
                            </div>
                        </div>

                        <!-- Content -->
                        <div
                            v-else-if="record"
                            class="flex-1 overflow-y-auto p-6 sm:p-8"
                        >
                            <form
                                @submit.prevent="submitForm"
                                class="relative"
                                :class="{ 'opacity-50': form.processing }"
                            >
                                <!-- Overlay during form submission -->
                                <div
                                    v-if="form.processing"
                                    class="absolute inset-0 bg-gray-100/50 dark:bg-gray-800/50 flex items-center justify-center z-10"
                                >
                                    <div
                                        class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"
                                    ></div>
                                </div>

                                <div class="space-y-8">
                                    <!-- Reading Information Section -->
                                    <section
                                        class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-6 rounded-lg"
                                        aria-labelledby="reading-info-title"
                                    >
                                        <h3
                                            id="reading-info-title"
                                            class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center"
                                        >
                                            <v-icon
                                                name="bi-speedometer2"
                                                class="mr-2"
                                                aria-hidden="true"
                                            />
                                            Meter Reading Details
                                        </h3>
                                        <div
                                            class="grid grid-cols-1 sm:grid-cols-2 gap-4"
                                        >
                                            <div>
                                                <label
                                                    for="reading-date"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                                    >Reading Date</label
                                                >
                                                <div
                                                    id="reading-date"
                                                    class="p-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white"
                                                >
                                                    {{
                                                        formatDate(
                                                            record.reading_date
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                            <div>
                                                <label
                                                    for="due-date"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                                    >Due Date</label
                                                >
                                                <div
                                                    id="due-date"
                                                    class="p-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white"
                                                >
                                                    {{ dueDate }}
                                                </div>
                                            </div>
                                            <div>
                                                <label
                                                    for="current-reading"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                                    >Current Reading (m³)
                                                    <span class="text-red-500"
                                                        >*</span
                                                    ></label
                                                >
                                                <input
                                                    id="current-reading"
                                                    type="number"
                                                    v-model="form.reading"
                                                    class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                    step="0.01"
                                                    min="0"
                                                    required
                                                    placeholder="Enter reading in cubic meters"
                                                    @input="validateReading"
                                                />
                                                <p
                                                    v-if="form.errors.reading"
                                                    class="text-red-500 text-xs mt-1"
                                                    role="alert"
                                                >
                                                    {{ form.errors.reading }}
                                                </p>
                                            </div>
                                            <div>
                                                <label
                                                    for="consumption"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                                    >Consumption (m³)</label
                                                >
                                                <div
                                                    id="consumption"
                                                    class="p-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white"
                                                >
                                                    {{ record.consumption }} m³
                                                </div>
                                            </div>
                                            <div>
                                                <label
                                                    for="amount"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                                    >Amount (₱)
                                                    <span class="text-red-500"
                                                        >*</span
                                                    ></label
                                                >
                                                <input
                                                    id="amount"
                                                    type="number"
                                                    v-model="form.amount"
                                                    class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                    step="0.01"
                                                    min="0"
                                                    required
                                                    placeholder="Enter amount in dollars"
                                                    @input="validateAmount"
                                                />
                                                <p
                                                    v-if="form.errors.amount"
                                                    class="text-red-500 text-xs mt-1"
                                                    role="alert"
                                                >
                                                    {{ form.errors.amount }}
                                                </p>
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Status Update Section -->
                                    <section
                                        class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-6 rounded-lg"
                                        aria-labelledby="payment-status-title"
                                    >
                                        <h3
                                            id="payment-status-title"
                                            class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center"
                                        >
                                            <v-icon
                                                name="bi-check-circle"
                                                class="mr-2"
                                                aria-hidden="true"
                                            />
                                            Payment Status
                                        </h3>
                                        <div
                                            class="grid grid-cols-1 sm:grid-cols-2 gap-4"
                                            role="radiogroup"
                                            aria-labelledby="payment-status-title"
                                        >
                                            <div
                                                v-for="statusOption in statusOptions"
                                                :key="statusOption.value"
                                                class="relative"
                                            >
                                                <input
                                                    type="radio"
                                                    :id="`status-${statusOption.value}`"
                                                    :value="statusOption.value"
                                                    v-model="form.status"
                                                    class="hidden peer"
                                                    required
                                                />
                                                <label
                                                    :for="`status-${statusOption.value}`"
                                                    class="flex flex-col items-center p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer transition-all duration-200 peer-checked:border-2 peer-checked:shadow-md hover:bg-gray-100 dark:hover:bg-gray-600"
                                                    :class="
                                                        statusOption.classes
                                                    "
                                                    :title="
                                                        statusOption.description
                                                    "
                                                >
                                                    <v-icon
                                                        :name="
                                                            statusOption.icon
                                                        "
                                                        class="w-8 h-8 mb-2"
                                                        aria-hidden="true"
                                                    />
                                                    <span class="font-medium">{{
                                                        statusOption.label
                                                    }}</span>
                                                    <span
                                                        class="text-xs mt-1 text-gray-500 dark:text-gray-400"
                                                        >{{
                                                            statusOption.description
                                                        }}</span
                                                    >
                                                </label>
                                            </div>
                                        </div>
                                        <p
                                            v-if="form.errors.status"
                                            class="text-red-500 text-xs mt-2"
                                            role="alert"
                                        >
                                            {{ form.errors.status }}
                                        </p>
                                    </section>
                                </div>

                                <!-- Action Buttons -->
                                <div
                                    class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-600"
                                >
                                    <button
                                        type="button"
                                        @click="confirmClose"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-400"
                                        :disabled="form.processing"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-md transition-colors duration-200 flex items-center focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <v-icon
                                            v-if="form.processing"
                                            name="bi-arrow-clockwise"
                                            class="animate-spin mr-2"
                                            aria-hidden="true"
                                        />
                                        {{
                                            form.processing
                                                ? "Updating..."
                                                : "Update Record"
                                        }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

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

const emit = defineEmits(["close", "saved"]);

// Status options with styling (Overdue removed)
const statusOptions = [
    {
        value: "Paid",
        label: "Paid",
        description: "Payment has been completed",
        icon: "hi-check-circle",
        classes:
            "peer-checked:border-green-500 dark:peer-checked:border-green-400 text-green-600 dark:text-green-400",
    },
    {
        value: "Pending",
        label: "Pending",
        description: "Awaiting payment confirmation",
        icon: "hi-clock",
        classes:
            "peer-checked:border-yellow-500 dark:peer-checked:border-yellow-400 text-yellow-600 dark:text-yellow-400",
    },
];

// Initialize form
const form = useForm({
    reading: props.record?.reading || 0,
    amount: props.record?.amount || 0,
    status: props.record?.status || "Pending",
    notes: props.record?.notes || "",
});

// Track form changes for unsaved data warning
const hasUnsavedChanges = ref(false);

// Update form when record changes
watch(
    () => props.record,
    (newRecord) => {
        if (newRecord) {
            form.reading = newRecord.reading;
            form.amount = newRecord.amount;
            form.status = newRecord.status;
            form.notes = newRecord.notes || "";
            hasUnsavedChanges.value = false;
        }
    }
);

// Watch form for changes
watch(
    () => form,
    () => {
        hasUnsavedChanges.value = true;
    },
    { deep: true }
);

// Format date for display
const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

// Calculate due date
const dueDate = computed(() => {
    if (!props.record || !props.record.reading_date) return "N/A";
    const readingDate = new Date(props.record.reading_date);
    readingDate.setDate(readingDate.getDate() + 15);
    return formatDate(readingDate.toISOString().split("T")[0]);
});

// Real-time validation for reading
const validateReading = () => {
    if (form.reading < 0) {
        form.errors.reading = "Reading cannot be negative.";
    } else if (form.reading === "") {
        form.errors.reading = "Reading is required.";
    } else {
        form.errors.reading = "";
    }
};

// Real-time validation for amount
const validateAmount = () => {
    if (form.amount < 0) {
        form.errors.amount = "Amount cannot be negative.";
    } else if (form.amount === "") {
        form.errors.amount = "Amount is required.";
    } else {
        form.errors.amount = "";
    }
};

// Confirm close if there are unsaved changes
const confirmClose = () => {
    if (hasUnsavedChanges.value && !form.processing) {
        Swal.fire({
            title: "Unsaved Changes",
            text: "You have unsaved changes. Are you sure you want to close?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, close",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                emit("close");
            }
        });
    } else {
        emit("close");
    }
};

// Submit form with debouncing
let submitTimeout = null;
const submitForm = () => {
    if (submitTimeout) clearTimeout(submitTimeout);
    submitTimeout = setTimeout(() => {
        form.put(route("admin.records.update", props.record.id), {
            preserveScroll: true,
            onSuccess: () => {
                hasUnsavedChanges.value = false;
                emit("saved");
                Swal.fire({
                    icon: "success",
                    title: "Record Updated",
                    text: "The water meter record has been successfully updated.",
                    timer: 2000,
                    showConfirmButton: false,
                });
            },
            onError: (errors) => {
                Swal.fire({
                    icon: "error",
                    title: "Update Failed",
                    text: "Please check the form for errors and try again.",
                    timer: 3000,
                    showConfirmButton: false,
                });
            },
        });
    }, 300);
};

// Focus on first input when modal opens
onMounted(() => {
    if (props.show) {
        const firstInput = document.querySelector("#current-reading");
        if (firstInput) firstInput.focus();
    }
});
</script>

<style scoped>
/* Custom scrollbar for better aesthetics */
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

/* Responsive adjustments */
@media (max-width: 640px) {
    .max-w-2xl {
        max-width: 100%;
    }
    .p-6 {
        padding: 1rem;
    }
    .text-lg {
        font-size: 1.125rem;
    }
}
</style>

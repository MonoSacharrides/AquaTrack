<template>
    <!-- Single transition wrapper for both overlay and panel -->
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-[1000] overflow-hidden">
            <!-- Overlay -->
            <div
                class="absolute inset-0 bg-black/50 transition-opacity duration-300"
                @click="emit('close')"
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
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between px-4 py-6 bg-gradient-to-r from-[#062F64] to-[#1E4272]"
                        >
                            <div class="flex items-center space-x-2">
                                <v-icon
                                    name="bi-droplet-half"
                                    class="text-amber-300"
                                    scale="1.5"
                                />
                                <span class="text-white font-[200] text-xl"
                                    >Usage Details</span
                                >
                            </div>
                            <button
                                @click="emit('close')"
                                class="text-white hover:text-gray-200 transition-colors duration-200 p-1 rounded-full hover:bg-white/10"
                            >
                                <v-icon name="bi-x-lg" class="h-6 w-6" />
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 overflow-y-auto p-6">
                            <div v-if="usage" class="space-y-6">
                                <!-- Billing Information -->
                                <div>
                                    <h3
                                        class="text-lg font-semibold text-gray-900 dark:text-white mb-3"
                                    >
                                        Billing Information
                                    </h3>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                    >
                                        <div>
                                            <p
                                                class="text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                Billing Month
                                            </p>
                                            <p
                                                class="font-medium text-gray-900 dark:text-white"
                                            >
                                                {{ usage.month }}
                                            </p>
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                Amount Due
                                            </p>
                                            <p
                                                class="font-bold text-gray-900 dark:text-white"
                                            >
                                                ₱{{ usage.amount }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <hr
                                    class="border-gray-200 dark:border-gray-600"
                                />

                                <!-- Consumption Details -->
                                <div>
                                    <h3
                                        class="text-lg font-semibold text-gray-900 dark:text-white mb-3"
                                    >
                                        Consumption Details
                                    </h3>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                    >
                                        <div>
                                            <p
                                                class="text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                Previous Reading
                                            </p>
                                            <p
                                                class="font-bold text-gray-900 dark:text-white"
                                            >
                                                {{
                                                    previousReading || "N/A m³"
                                                }}
                                            </p>
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                Consumption Rate
                                            </p>
                                            <p
                                                class="font-medium text-gray-900 dark:text-white"
                                            >
                                                {{ consumptionRate }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <hr
                                    class="border-gray-200 dark:border-gray-600"
                                />

                                <!-- Usage Comparison -->
                                <div>
                                    <h3
                                        class="text-lg font-semibold text-gray-900 dark:text-white mb-3"
                                    >
                                        Usage Comparison
                                    </h3>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span
                                                class="text-gray-600 dark:text-gray-300"
                                                >This month</span
                                            >
                                            <span
                                                class="font-medium text-gray-900 dark:text-white"
                                                >{{ usage.usage }} m³</span
                                            >
                                        </div>
                                        <div class="flex justify-between">
                                            <span
                                                class="text-gray-600 dark:text-gray-300"
                                                >Previous month</span
                                            >
                                            <span
                                                class="font-medium text-gray-900 dark:text-white"
                                                >{{
                                                    previousMonthUsage ||
                                                    "N/A m³"
                                                }}</span
                                            >
                                        </div>
                                        <div class="flex justify-between">
                                            <span
                                                class="text-gray-600 dark:text-gray-300"
                                                >Average (6 months)</span
                                            >
                                            <span
                                                class="font-medium text-gray-900 dark:text-white"
                                                >{{
                                                    averageUsage || "N/A m³"
                                                }}</span
                                            >
                                        </div>
                                        <div class="pt-2">
                                            <p
                                                class="text-sm font-medium"
                                                :class="
                                                    changePercentage >= 0
                                                        ? 'text-red-600 dark:text-red-400'
                                                        : 'text-green-600 dark:text-green-400'
                                                "
                                            >
                                                {{
                                                    Math.abs(changePercentage)
                                                }}%
                                                {{
                                                    changePercentage >= 0
                                                        ? "increase"
                                                        : "decrease"
                                                }}
                                                from previous month
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <hr
                                    class="border-gray-200 dark:border-gray-600"
                                />

                                <!-- Water Consumption & Status -->
                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-6"
                                >
                                    <div>
                                        <h4
                                            class="text-md font-medium text-gray-900 dark:text-white mb-2"
                                        >
                                            Water Consumption
                                        </h4>
                                        <p
                                            class="text-2xl font-bold text-blue-600 dark:text-blue-400"
                                        >
                                            {{ usage.usage }} m³
                                        </p>
                                    </div>
                                    <div>
                                        <h4
                                            class="text-md font-medium text-gray-900 dark:text-white mb-2"
                                        >
                                            Status
                                        </h4>
                                        <span
                                            :class="statusClasses(usage.status)"
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                        >
                                            <v-icon
                                                :name="
                                                    usage.status === 'Paid'
                                                        ? 'bi-check-circle-fill'
                                                        : 'bi-clock-fill'
                                                "
                                                class="w-4 h-4 mr-1"
                                            />
                                            {{ usage.status }}
                                        </span>
                                    </div>
                                </div>

                                <hr
                                    class="border-gray-200 dark:border-gray-600"
                                />

                                <!-- Current Reading & Date -->
                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-6"
                                >
                                    <div>
                                        <h4
                                            class="text-md font-medium text-gray-900 dark:text-white mb-2"
                                        >
                                            Current Reading
                                        </h4>
                                        <p
                                            class="text-xl font-bold text-gray-900 dark:text-white"
                                        >
                                            {{ currentReading || "N/A m³" }}
                                        </p>
                                    </div>
                                    <div>
                                        <h4
                                            class="text-md font-medium text-gray-900 dark:text-white mb-2"
                                        >
                                            Reading Date
                                        </h4>
                                        <p
                                            class="text-md text-gray-900 dark:text-white"
                                        >
                                            {{ readingDate }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Loading state -->
                            <div
                                v-else
                                class="flex justify-center items-center h-64"
                            >
                                <div
                                    class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"
                                ></div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 bg-gray-50 dark:bg-gray-700"
                        >
                            <div class="flex justify-between">
                                <button
                                    @click="emit('close')"
                                    class="inline-flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-600 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                >
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

// In your customerusagemodal.vue component
<script setup>
import { ref, computed, onMounted, watch } from "vue";
import axios from "axios";

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    usage: {
        type: Object,
        default: null,
    },
    userId: {
        type: Number,
        default: null,
    },
});

const emit = defineEmits(["close"]);
const previousReadings = ref([]);
const loading = ref(false);

// Fetch previous readings when modal opens
watch(
    () => props.show,
    async (newVal) => {
        if (newVal && props.userId) {
            loading.value = true;
            try {
                const response = await axios.get(
                    `/staff/readings/${props.userId}/previous`
                );
                previousReadings.value = response.data;
            } catch (error) {
                console.error("Error fetching previous readings:", error);
            } finally {
                loading.value = false;
            }
        }
    }
);

const statusClasses = (status) => {
    return {
        "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300":
            status === "Pending",
        "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300":
            status === "Paid",
    };
};

// Helper methods to get data
const previousReading = computed(() => {
    if (!props.usage || !previousReadings.value.length) return "N/A m³";

    // Find the current reading index
    const currentIndex = previousReadings.value.findIndex(
        (r) =>
            r.billing_month === props.usage.month.split(" ")[0] &&
            r.year === props.usage.month.split(" ")[1]
    );

    // If current reading is found and there's a next one (previous in time)
    if (
        currentIndex !== -1 &&
        currentIndex < previousReadings.value.length - 1
    ) {
        return `${previousReadings.value[currentIndex + 1].reading} m³`;
    }

    return "N/A m³";
});

const currentReading = computed(() => {
    if (!props.usage || !previousReadings.value.length) return "N/A m³";

    const current = previousReadings.value.find(
        (r) =>
            r.billing_month === props.usage.month.split(" ")[0] &&
            r.year === props.usage.month.split(" ")[1]
    );

    return current ? `${current.reading} m³` : "N/A m³";
});

const consumptionRate = computed(() => {
    return "Standard";
});

const readingDate = computed(() => {
    if (!props.usage || !previousReadings.value.length)
        return new Date().toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric",
        });

    const current = previousReadings.value.find(
        (r) =>
            r.billing_month === props.usage.month.split(" ")[0] &&
            r.year === props.usage.month.split(" ")[1]
    );

    if (current && current.reading_date) {
        return new Date(current.reading_date).toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric",
        });
    }

    return new Date().toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
});

const previousMonthUsage = computed(() => {
    if (!props.usage || !previousReadings.value.length) return "N/A m³";

    // Get the month before the current usage
    const currentDate = new Date();
    const currentMonth = props.usage.month.split(" ")[0];
    const currentYear = props.usage.month.split(" ")[1];

    const monthNames = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
    ];

    const currentMonthIndex = monthNames.indexOf(currentMonth);
    let prevMonthIndex = currentMonthIndex - 1;
    let prevYear = currentYear;

    if (prevMonthIndex < 0) {
        prevMonthIndex = 11;
        prevYear = parseInt(currentYear) - 1;
    }

    const prevMonth = monthNames[prevMonthIndex];
    const prevReading = previousReadings.value.find(
        (r) => r.billing_month === prevMonth && r.year === prevYear.toString()
    );

    return prevReading ? `${prevReading.consumption} m³` : "N/A m³";
});

const averageUsage = computed(() => {
    if (!previousReadings.value.length) return "N/A m³";

    const lastSix = previousReadings.value.slice(0, 6);
    if (lastSix.length === 0) return "N/A m³";

    const total = lastSix.reduce(
        (sum, reading) => sum + parseFloat(reading.consumption),
        0
    );
    const average = total / lastSix.length;

    return `${average.toFixed(1)} m³`;
});

const changePercentage = computed(() => {
    if (!props.usage || !previousReadings.value.length) return 0;

    const currentUsage = parseFloat(props.usage.usage);
    const prevUsage = parseFloat(previousMonthUsage.value);

    if (isNaN(prevUsage) || prevUsage === 0) return 0;

    return ((currentUsage - prevUsage) / prevUsage) * 100;
});
</script>

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
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #e2e8f0;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: #cbd5e1;
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
</style>

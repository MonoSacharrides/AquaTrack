<template>
    <CustomerLayout>
        <div
            class="w-full bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
        >
            <!-- Header Section -->
            <div
                class="bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/30 dark:to-cyan-900/30 p-6 border-b border-gray-200 dark:border-gray-700"
            >
                <div
                    class="flex flex-col md:flex-row md:items-center justify-between gap-4"
                >
                    <div>
                        <h2
                            class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3"
                        >
                            <div
                                class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg"
                            >
                                <v-icon
                                    name="bi-graph-up"
                                    class="w-6 h-6 text-blue-600 dark:text-blue-300"
                                />
                            </div>
                            Water Usage History
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 mt-2">
                            Track your monthly water consumption and billing
                            information
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div
                            class="hidden sm:flex items-center gap-2 text-sm bg-white dark:bg-gray-700 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600"
                        >
                            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                            <span class="text-gray-700 dark:text-gray-300"
                                >Usage (m³)</span
                            >
                        </div>
                        <div
                            class="hidden sm:flex items-center gap-2 text-sm bg-white dark:bg-gray-700 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600"
                        >
                            <div class="w-3 h-3 rounded-full bg-gray-400"></div>
                            <span class="text-gray-700 dark:text-gray-300"
                                >Amount (₱)</span
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="p-6">
                <!-- Stats Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div
                        class="bg-blue-50 dark:bg-blue-900/20 p-5 rounded-xl border border-blue-100 dark:border-blue-800/30"
                    >
                        <div class="flex items-center justify-between">
                            <h3
                                class="text-sm font-medium text-blue-800 dark:text-blue-300"
                            >
                                Current Month Usage
                            </h3>
                            <v-icon
                                name="bi-droplet-half"
                                class="w-5 h-5 text-blue-500"
                            />
                        </div>
                        <p
                            class="text-2xl font-bold text-blue-900 dark:text-blue-100 mt-2"
                        >
                            {{ currentMonthUsage }} m³
                        </p>
                        <p
                            class="text-sm text-blue-700 dark:text-blue-300 mt-1"
                        >
                            {{ currentMonthChange }}
                        </p>
                    </div>

                    <div
                        class="bg-green-50 dark:bg-green-900/20 p-5 rounded-xl border border-green-100 dark:border-green-800/30"
                    >
                        <div class="flex items-center justify-between">
                            <h3
                                class="text-sm font-medium text-green-800 dark:text-green-300"
                            >
                                Average Monthly Usage
                            </h3>
                            <v-icon
                                name="bi-speedometer2"
                                class="w-5 h-5 text-green-500"
                            />
                        </div>
                        <p
                            class="text-2xl font-bold text-green-900 dark:text-green-100 mt-2"
                        >
                            {{ averageUsage }} m³
                        </p>
                        <p
                            class="text-sm text-green-700 dark:text-green-300 mt-1"
                        >
                            Last 6 months
                        </p>
                    </div>

                    <div
                        class="bg-purple-50 dark:bg-purple-900/20 p-5 rounded-xl border border-purple-100 dark:border-purple-800/30"
                    >
                        <div class="flex items-center justify-between">
                            <h3
                                class="text-sm font-medium text-purple-800 dark:text-purple-300"
                            >
                                Total Amount Due
                            </h3>
                            <v-icon
                                name="bi-currency-dollar"
                                class="w-5 h-5 text-purple-500"
                            />
                        </div>
                        <p
                            class="text-2xl font-bold text-purple-900 dark:text-purple-100 mt-2"
                        >
                            ₱{{ totalAmountDue }}
                        </p>
                        <p
                            class="text-sm text-purple-700 dark:text-purple-300 mt-1"
                        >
                            {{ pendingBills }} pending bills
                        </p>
                    </div>
                </div>

                <!-- Chart Section -->
                <div
                    class="bg-gray-50 dark:bg-gray-700/30 p-6 rounded-xl mb-8 border border-gray-200 dark:border-gray-600"
                >
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between mb-6"
                    >
                        <h3
                            class="text-lg font-semibold text-gray-800 dark:text-white"
                        >
                            Monthly Usage and Billing Trend
                        </h3>

                        <div class="flex items-center gap-2 mt-3 sm:mt-0">
                            <button
                                v-for="period in timePeriods"
                                :key="period"
                                @click="selectedPeriod = period"
                                :class="[
                                    'px-3 py-1.5 text-sm rounded-lg transition-colors',
                                    selectedPeriod === period
                                        ? 'bg-blue-100 text-blue-700 dark:bg-blue-800 dark:text-blue-200 font-medium'
                                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-600',
                                ]"
                            >
                                {{ period }}
                            </button>
                        </div>
                    </div>

                    <div class="h-72 md:h-80">
                        <canvas ref="usageChart"></canvas>
                    </div>
                </div>

                <!-- Usage Table -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                >
                    <div
                        class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between"
                    >
                        <h3
                            class="text-lg font-semibold text-gray-800 dark:text-white"
                        >
                            Billing History
                        </h3>

                        <div class="mt-3 sm:mt-0 relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                            >
                                <v-icon
                                    name="hi-search"
                                    class="h-4 w-4 text-gray-400"
                                />
                            </div>
                            <input
                                type="text"
                                v-model="searchQuery"
                                placeholder="Search months..."
                                class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            />
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                                        @click="sortTable('month')"
                                    >
                                        <div class="flex items-center">
                                            Month
                                            <v-icon
                                                :name="
                                                    sortField === 'month'
                                                        ? sortDirection ===
                                                          'asc'
                                                            ? 'bi-arrow-up'
                                                            : 'bi-arrow-down'
                                                        : 'bi-arrow-down-up'
                                                "
                                                class="w-3 h-3 ml-1"
                                            />
                                        </div>
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                                        @click="sortTable('usage')"
                                    >
                                        <div class="flex items-center">
                                            Usage (m³)
                                            <v-icon
                                                :name="
                                                    sortField === 'usage'
                                                        ? sortDirection ===
                                                          'asc'
                                                            ? 'bi-arrow-up'
                                                            : 'bi-arrow-down'
                                                        : 'bi-arrow-down-up'
                                                "
                                                class="w-3 h-3 ml-1"
                                            />
                                        </div>
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                                        @click="sortTable('amount')"
                                    >
                                        <div class="flex items-center">
                                            Amount (₱)
                                            <v-icon
                                                :name="
                                                    sortField === 'amount'
                                                        ? sortDirection ===
                                                          'asc'
                                                            ? 'bi-arrow-up'
                                                            : 'bi-arrow-down'
                                                        : 'bi-arrow-down-up'
                                                "
                                                class="w-3 h-3 ml-1"
                                            />
                                        </div>
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="(usage, index) in filteredUsageData"
                                    :key="index"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div
                                            class="text-sm font-medium text-gray-900 dark:text-white"
                                        >
                                            {{ usage.month }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div
                                            class="text-sm text-gray-900 dark:text-white"
                                        >
                                            {{ usage.usage }} m³
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div
                                            class="text-sm font-medium text-gray-900 dark:text-white"
                                        >
                                            ₱{{ usage.amount }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="statusClasses(usage.status)"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        >
                                            <v-icon
                                                :name="
                                                    usage.status === 'Paid'
                                                        ? 'bi-check-circle-fill'
                                                        : 'bi-clock-fill'
                                                "
                                                class="w-3 h-3 mr-1"
                                            />
                                            {{ usage.status }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                    >
                                        <div class="flex space-x-2">
                                            <button
                                                @click="openUsageModal(usage)"
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 p-1.5 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                                title="View Details"
                                            >
                                                <v-icon
                                                    name="bi-eye-fill"
                                                    class="w-4 h-4"
                                                />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-if="filteredUsageData.length === 0"
                        class="text-center py-12"
                    >
                        <v-icon
                            name="hi-clipboard-list"
                            class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto"
                        />
                        <p class="mt-4 text-gray-500 dark:text-gray-400">
                            No usage records found
                        </p>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="filteredUsageData.length > 0"
                        class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between"
                    >
                        <div class="text-sm text-gray-700 dark:text-gray-400">
                            Showing <span class="font-medium">1</span> to
                            <span class="font-medium">{{
                                filteredUsageData.length
                            }}</span>
                            of
                            <span class="font-medium">{{
                                filteredUsageData.length
                            }}</span>
                            results
                        </div>
                        <div class="flex space-x-2">
                            <button
                                class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled
                            >
                                Previous
                            </button>
                            <button
                                class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <CustomerUsageModal
            :show="showUsageModal"
            :usage="selectedUsage"
            :userId="selectedUserId"
            @close="showUsageModal = false"
        />
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import CustomerUsageModal from "@/Components/Customer/CustomerUsageModal.vue";
import { ref, onMounted, computed, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import Chart from "chart.js/auto";

const page = usePage();
const usageData = ref(page.props.usageData ?? []);
const chartData = ref(page.props.chartData ?? []);

// Add these to your component state
const showUsageModal = ref(false);
const selectedUsage = ref(null);
const selectedUserId = ref(null);

// Function to open the modal
const openUsageModal = (usage) => {
    selectedUsage.value = usage;
    selectedUserId.value = page.props.auth.user.id;
    showUsageModal.value = true;
};

// UI State
const searchQuery = ref("");
const sortField = ref("month");
const sortDirection = ref("desc");
const selectedPeriod = ref("6M");
const timePeriods = ["3M", "6M", "1Y", "All"];

// Refs
const usageChart = ref(null);
let chartInstance = null;

// Computed properties
const currentMonthUsage = computed(() => {
    if (usageData.value.length === 0) return "0";
    return usageData.value[0].usage;
});

const averageUsage = computed(() => {
    if (usageData.value.length === 0) return "0";
    const sum = usageData.value.reduce(
        (acc, item) => acc + parseFloat(item.usage),
        0
    );
    return (sum / usageData.value.length).toFixed(1);
});

const totalAmountDue = computed(() => {
    if (usageData.value.length === 0) return "0";
    return usageData.value
        .filter((item) => item.status === "Pending")
        .reduce((acc, item) => acc + parseFloat(item.amount), 0)
        .toFixed(2);
});

const pendingBills = computed(() => {
    return usageData.value.filter((item) => item.status === "Pending").length;
});

const currentMonthChange = computed(() => {
    if (usageData.value.length < 2) return "No previous data";

    const current = parseFloat(usageData.value[0].usage);
    const previous = parseFloat(usageData.value[1].usage);
    const change = ((current - previous) / previous) * 100;

    if (change > 0) {
        return `↑ ${Math.abs(change).toFixed(1)}% from last month`;
    } else if (change < 0) {
        return `↓ ${Math.abs(change).toFixed(1)}% from last month`;
    } else {
        return "No change from last month";
    }
});

const filteredUsageData = computed(() => {
    let data = [...usageData.value];

    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        data = data.filter((item) => item.month.toLowerCase().includes(query));
    }

    // Sort data
    data.sort((a, b) => {
        let modifier = 1;
        if (sortDirection.value === "desc") modifier = -1;

        if (sortField.value === "month") {
            // Convert months to dates for proper sorting
            const dateA = new Date(`1 ${a.month}`);
            const dateB = new Date(`1 ${b.month}`);
            return (dateA - dateB) * modifier;
        } else {
            if (a[sortField.value] < b[sortField.value]) return -1 * modifier;
            if (a[sortField.value] > b[sortField.value]) return 1 * modifier;
            return 0;
        }
    });

    return data;
});

// Methods
const statusClasses = (status) => {
    return {
        "px-2 py-1 rounded-full text-xs font-semibold": true,
        "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300":
            status === "Pending",
        "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300":
            status === "Paid",
    };
};

const sortTable = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortField.value = field;
        sortDirection.value = "desc";
    }
};

// Initialize chart
onMounted(() => {
    // Prepare labels and data for chart
    const labels = chartData.value.map((item) => item.month);
    const usageValues = chartData.value.map((item) => item.usage);
    const amountValues = chartData.value.map((item) => item.amount);

    // Usage Chart with dynamic data
    chartInstance = new Chart(usageChart.value, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Water Usage (m³)",
                    data: usageValues,
                    backgroundColor: "rgba(59, 130, 246, 0.7)",
                    borderColor: "rgb(59, 130, 246)",
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.6,
                },
                {
                    label: "Bill Amount (₱)",
                    data: amountValues,
                    backgroundColor: "rgba(156, 163, 175, 0.7)",
                    borderColor: "rgb(156, 163, 175)",
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "top",
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                    },
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.dataset.label || "";
                            const value = context.raw;
                            return `${label}: ${
                                label.includes("Usage")
                                    ? value + " m³"
                                    : "₱" + value
                            }`;
                        },
                    },
                    backgroundColor: "rgba(255, 255, 255, 0.95)",
                    titleColor: "#1f2937",
                    bodyColor: "#374151",
                    borderColor: "#e5e7eb",
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    usePointStyle: true,
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                    },
                    ticks: {
                        padding: 10,
                    },
                },
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        padding: 10,
                    },
                },
            },
        },
    });
});

// Update chart when period changes
watch(selectedPeriod, (newPeriod) => {
    // In a real app, you would fetch new data based on the selected period
    console.log("Period changed to:", newPeriod);
    // For demo purposes, we'll just update the chart title
    if (chartInstance) {
        chartInstance.options.plugins.title = {
            display: true,
            text: `Water Usage - ${newPeriod}`,
        };
        chartInstance.update();
    }
});
</script>

<style scoped>
/* Custom chart styles */
canvas {
    border-radius: 0.75rem;
}
</style>

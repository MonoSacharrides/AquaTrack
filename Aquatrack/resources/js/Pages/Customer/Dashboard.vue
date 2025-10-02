<template>
    <CustomerLayout>
        <div class="w-full min-h-screen bg-gray-50">
            <!-- Header Section with subtle gradient -->
            <div
                class="bg-gradient-to-r from-white to-blue-50 border-b border-gray-200"
            >
                <div class="px-6 py-5">
                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <h1
                                class="text-2xl font-semibold text-gray-800 flex items-center"
                            >
                                <span
                                    class="bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent"
                                >
                                    Dashboard Overview
                                </span>
                            </h1>
                            <p class="text-sm text-gray-600 mt-2">
                                Welcome back,
                                <span class="font-medium text-blue-700">{{
                                    customerName
                                }}</span
                                >. Here's your water usage summary for this
                                period.
                            </p>
                        </div>
                        <div class="mt-2 sm:mt-0">
                            <div
                                class="bg-white rounded-lg py-2 px-4 shadow-xs border border-gray-100"
                            >
                                <p class="text-xs text-gray-500 font-medium">
                                    Billing Period
                                </p>
                                <p class="font-medium text-gray-800">
                                    {{
                                        new Date().toLocaleDateString("en-US", {
                                            month: "long",
                                            year: "numeric",
                                        })
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="p-6">
                <!-- Key Metrics Grid with improved visual hierarchy -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
                    <!-- Monthly Usage Card -->
                    <div
                        class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden"
                    >
                        <div
                            class="absolute top-0 right-0 w-20 h-20 -mr-4 -mt-4 rounded-full bg-blue-100 opacity-30"
                        ></div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-2 bg-blue-100 rounded-lg shadow-xs">
                                <v-icon
                                    name="bi-water"
                                    class="text-blue-600 text-xl"
                                />
                            </div>
                            <span
                                class="text-xs font-medium px-2 py-1 bg-blue-50 text-blue-700 rounded-full"
                            >
                                m³
                            </span>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">
                            Monthly Usage
                        </h3>
                        <p class="text-2xl font-bold text-gray-800 mb-2">
                            {{ monthlyUsage }}
                        </p>
                        <div class="flex items-center text-xs mt-3">
                            <div
                                class="flex items-center"
                                :class="
                                    monthlyUsage > 15
                                        ? 'text-red-600'
                                        : 'text-green-600'
                                "
                            >
                                <v-icon
                                    :name="
                                        monthlyUsage > 15
                                            ? 'bi-arrow-up'
                                            : 'bi-arrow-down'
                                    "
                                    class="mr-1"
                                />
                                <span>
                                    {{
                                        monthlyUsage > 15 ? "Above" : "Below"
                                    }}
                                    average
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Current Bill Card -->
                    <div
                        class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden"
                    >
                        <div
                            class="absolute top-0 right-0 w-20 h-20 -mr-4 -mt-4 rounded-full bg-green-100 opacity-30"
                        ></div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-2 bg-green-100 rounded-lg shadow-xs">
                                <v-icon
                                    name="bi-cash-stack"
                                    class="text-green-600 text-xl"
                                />
                            </div>
                            <span
                                class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full font-medium"
                                >Due in 15 days</span
                            >
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">
                            Current Bill
                        </h3>
                        <p class="text-2xl font-bold text-gray-800 mb-3">
                            ₱{{ currentBill.toFixed(2) }}
                        </p>
                        <div class="w-full bg-gray-100 rounded-full h-2 mt-2">
                            <div
                                class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full transition-all duration-500"
                                :style="`width: ${Math.min(
                                    (currentBill / 1000) * 100,
                                    100
                                )}%`"
                            ></div>
                        </div>
                    </div>

                    <!-- Notifications Card -->
                    <div
                        class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden"
                    >
                        <div
                            class="absolute top-0 right-0 w-20 h-20 -mr-4 -mt-4 rounded-full bg-purple-100 opacity-30"
                        ></div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-2 bg-purple-100 rounded-lg shadow-xs">
                                <v-icon
                                    name="bi-bell-fill"
                                    class="text-purple-600 text-xl"
                                />
                            </div>
                            <span
                                v-if="announcements > 0"
                                class="h-2.5 w-2.5 bg-purple-500 rounded-full animate-pulse"
                            ></span>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">
                            Announcements
                        </h3>
                        <p class="text-2xl font-bold text-gray-800 mb-2">
                            {{ announcements }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{
                                announcements === 0
                                    ? "No new updates"
                                    : "Unread notifications"
                            }}
                        </p>
                        <button
                            v-if="announcements > 0"
                            class="mt-3 text-xs text-purple-600 hover:text-purple-700 font-medium flex items-center transition-colors"
                        >
                            View details
                            <v-icon
                                name="bi-arrow-right"
                                class="text-xs ml-1"
                            />
                        </button>
                    </div>
                </div>

                <!-- Analytics Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Consumption Chart -->
                    <div
                        class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm"
                    >
                        <div
                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-5"
                        >
                            <h2
                                class="text-lg font-semibold text-gray-800 mb-2 sm:mb-0"
                            >
                                <span
                                    class="bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent"
                                >
                                    Water Consumption
                                </span>
                            </h2>
                            <div
                                class="flex items-center text-xs text-gray-500 bg-gray-50 rounded-lg py-1.5 px-3"
                            >
                                <div class="flex items-center mr-3">
                                    <span
                                        class="h-2 w-2 bg-blue-500 rounded-full mr-2"
                                    ></span>
                                    <span>Your Usage</span>
                                </div>
                                <div class="flex items-center">
                                    <span
                                        class="h-2 w-2 bg-gray-400 rounded-full mr-2"
                                    ></span>
                                    <span>Area Average</span>
                                </div>
                            </div>
                        </div>
                        <div class="h-72 mt-2">
                            <canvas ref="waterChart"></canvas>
                        </div>
                    </div>

                    <!-- Meter Readings -->
                    <div
                        class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm"
                    >
                        <h2 class="text-lg font-semibold text-gray-800 mb-5">
                            <span
                                class="bg-gradient-to-r from-indigo-600 to-indigo-800 bg-clip-text text-transparent"
                            >
                                Meter Readings History
                            </span>
                        </h2>
                        <div class="h-72">
                            <canvas ref="yieldChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Stats Summary -->
                <div
                    class="bg-white rounded-xl border border-gray-100 p-6 mb-8 shadow-sm"
                >
                    <h2 class="text-lg font-semibold text-gray-800 mb-5">
                        <span
                            class="bg-gradient-to-r from-gray-700 to-gray-900 bg-clip-text text-transparent"
                        >
                            Usage Summary
                        </span>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div
                            class="p-5 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-100 transition-transform hover:scale-[1.02]"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-medium text-blue-700">
                                    Year Total
                                </h3>
                                <v-icon
                                    name="bi-calendar-check"
                                    class="text-blue-500"
                                />
                            </div>
                            <p class="text-xl font-bold text-blue-800">
                                {{ yearlyConsumption }} m³
                            </p>
                            <p class="text-xs text-blue-600 mt-2">
                                Total consumption this year
                            </p>
                        </div>

                        <div
                            class="p-5 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-100 transition-transform hover:scale-[1.02]"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-medium text-gray-700">
                                    Area Average
                                </h3>
                                <v-icon
                                    name="bi-people"
                                    class="text-gray-500"
                                />
                            </div>
                            <p class="text-xl font-bold text-gray-800">
                                {{ areaAverage }} m³
                            </p>
                            <p class="text-xs text-gray-600 mt-2">
                                Average in your area
                            </p>
                        </div>

                        <div
                            class="p-5 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-100 transition-transform hover:scale-[1.02]"
                            :class="{
                                'bg-gradient-to-br from-red-50 to-red-100 border-red-100':
                                    yearlyConsumption > areaAverage,
                            }"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <h3
                                    class="text-sm font-medium"
                                    :class="
                                        yearlyConsumption > areaAverage
                                            ? 'text-red-700'
                                            : 'text-green-700'
                                    "
                                >
                                    Your Status
                                </h3>
                                <v-icon
                                    :name="
                                        yearlyConsumption > areaAverage
                                            ? 'bi-arrow-up'
                                            : 'bi-arrow-down'
                                    "
                                    :class="
                                        yearlyConsumption > areaAverage
                                            ? 'text-red-500'
                                            : 'text-green-500'
                                    "
                                />
                            </div>
                            <p
                                class="text-xl font-bold"
                                :class="
                                    yearlyConsumption > areaAverage
                                        ? 'text-red-800'
                                        : 'text-green-800'
                                "
                            >
                                {{
                                    yearlyConsumption > areaAverage
                                        ? "Above"
                                        : "Below"
                                }}
                                Average
                            </p>
                            <p
                                class="text-xs mt-2"
                                :class="
                                    yearlyConsumption > areaAverage
                                        ? 'text-red-600'
                                        : 'text-green-600'
                                "
                            >
                                {{
                                    yearlyConsumption > areaAverage
                                        ? "Higher than area average"
                                        : "Lower than area average"
                                }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quick Reading Stats -->
                <div
                    class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm"
                >
                    <h2 class="text-lg font-semibold text-gray-800 mb-5">
                        <span
                            class="bg-gradient-to-r from-gray-700 to-gray-900 bg-clip-text text-transparent"
                        >
                            Reading Statistics
                        </span>
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div
                            class="text-center p-4 bg-gradient-to-b from-blue-50 to-white rounded-xl border border-blue-100 transition-transform hover:scale-[1.02]"
                        >
                            <div
                                class="inline-flex items-center justify-center w-10 h-10 bg-blue-100 rounded-lg mb-2"
                            >
                                <v-icon
                                    name="bi-arrow-up"
                                    class="text-blue-600"
                                />
                            </div>
                            <p class="text-xs text-blue-600 font-medium mb-1">
                                Highest Reading
                            </p>
                            <p class="text-lg font-bold text-blue-700">
                                {{
                                    Math.max(
                                        ...(chartData.map(
                                            (item) => item.reading
                                        ) || [0])
                                    )
                                }}
                                m³
                            </p>
                        </div>
                        <div
                            class="text-center p-4 bg-gradient-to-b from-gray-50 to-white rounded-xl border border-gray-100 transition-transform hover:scale-[1.02]"
                        >
                            <div
                                class="inline-flex items-center justify-center w-10 h-10 bg-gray-100 rounded-lg mb-2"
                            >
                                <v-icon name="bi-dash" class="text-gray-600" />
                            </div>
                            <p class="text-xs text-gray-600 font-medium mb-1">
                                Average Reading
                            </p>
                            <p class="text-lg font-bold text-gray-700">
                                {{
                                    (
                                        chartData.reduce(
                                            (a, b) => a + b.reading,
                                            0
                                        ) / (chartData.length || 1)
                                    ).toFixed(1)
                                }}
                                m³
                            </p>
                        </div>
                        <div
                            class="text-center p-4 bg-gradient-to-b from-green-50 to-white rounded-xl border border-green-100 transition-transform hover:scale-[1.02]"
                        >
                            <div
                                class="inline-flex items-center justify-center w-10 h-10 bg-green-100 rounded-lg mb-2"
                            >
                                <v-icon
                                    name="bi-arrow-down"
                                    class="text-green-600"
                                />
                            </div>
                            <p class="text-xs text-green-600 font-medium mb-1">
                                Lowest Reading
                            </p>
                            <p class="text-lg font-bold text-green-700">
                                {{
                                    Math.min(
                                        ...(chartData.map(
                                            (item) => item.reading
                                        ) || [0])
                                    )
                                }}
                                m³
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Water Saving Tips -->
                <div
                    class="mt-8 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border border-blue-100 p-5 shadow-sm"
                >
                    <h2
                        class="text-lg font-semibold text-gray-800 mb-3 flex items-center"
                    >
                        <v-icon
                            name="bi-lightbulb"
                            class="text-yellow-500 mr-2"
                        />
                        <span
                            class="bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent"
                        >
                            Water Saving Tip
                        </span>
                    </h2>
                    <p class="text-sm text-gray-700">
                        {{ waterSavingTips[currentTipIndex] }}
                    </p>
                    <button
                        @click="nextTip"
                        class="mt-3 text-xs text-blue-600 hover:text-blue-700 font-medium flex items-center"
                    >
                        Next tip
                        <v-icon name="bi-arrow-right" class="text-xs ml-1" />
                    </button>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import { ref, onMounted, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import Chart from "chart.js/auto";

const page = usePage();
const announcements = page.props.announcements ?? 0;
const monthlyUsage = page.props.monthlyUsage ?? 0;
const currentBill = page.props.currentBill ?? 0;
const yearlyConsumption = page.props.yearlyConsumption ?? 0;
const areaAverage = page.props.areaAverage ?? 0;
const chartData = page.props.chartData ?? [];
const customerName = page.props.customerName ?? "Customer";

const waterChart = ref(null);
const yieldChart = ref(null);
const currentTipIndex = ref(0);

const waterSavingTips = [
    "Fix leaky faucets promptly - a dripping faucet can waste up to 20 gallons of water a day.",
    "Take shorter showers. Reducing your shower time by just 2 minutes can save up to 150 gallons per month.",
    "Only run your washing machine and dishwasher with full loads to maximize water efficiency.",
    "Install water-efficient showerheads and faucet aerators to reduce water usage without sacrificing pressure.",
    "Water your plants early in the morning or late in the evening to reduce evaporation loss.",
];

function nextTip() {
    currentTipIndex.value =
        (currentTipIndex.value + 1) % waterSavingTips.length;
}

onMounted(() => {
    // Prepare labels and data for charts
    const labels = chartData.map((item) => item.month);
    const consumptionData = chartData.map((item) => item.consumption);
    const readingData = chartData.map((item) => item.reading);

    // Area average data
    const areaAvgData = Array(consumptionData.length).fill(areaAverage / 12);

    // Water Consumption Chart
    new Chart(waterChart.value, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Your Consumption",
                    data: consumptionData,
                    borderColor: "rgb(59, 130, 246)",
                    backgroundColor: "rgba(59, 130, 246, 0.08)",
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: "rgb(59, 130, 246)",
                    pointBorderColor: "#fff",
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                },
                {
                    label: "Area Average",
                    data: areaAvgData,
                    borderColor: "rgb(156, 163, 175)",
                    backgroundColor: "transparent",
                    borderWidth: 2,
                    tension: 0.3,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    pointHoverRadius: 0,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
            },
            scales: {
                y: {
                    beginAtZero: false,
                    grid: {
                        color: "rgba(0, 0, 0, 0.03)",
                    },
                    ticks: {
                        font: {
                            size: 11,
                        },
                        callback: function (value) {
                            return value + " m³";
                        },
                    },
                },
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        font: {
                            size: 11,
                        },
                    },
                },
            },
        },
    });

    // Meter Readings Chart
    new Chart(yieldChart.value, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Meter Readings",
                    data: readingData,
                    backgroundColor: "rgba(99, 102, 241, 0.7)",
                    borderColor: "rgb(99, 102, 241)",
                    borderWidth: 0,
                    borderRadius: 6,
                    barThickness: "flex",
                    maxBarThickness: 40,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
            },
            scales: {
                y: {
                    beginAtZero: false,
                    grid: {
                        color: "rgba(0, 0, 0, 0.03)",
                    },
                    ticks: {
                        font: {
                            size: 11,
                        },
                        callback: function (value) {
                            return value + " m³";
                        },
                    },
                },
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        font: {
                            size: 11,
                        },
                    },
                },
            },
        },
    });
});
</script>

<style scoped>
.shadow-xs {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
.shadow-sm {
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.07);
}
</style>

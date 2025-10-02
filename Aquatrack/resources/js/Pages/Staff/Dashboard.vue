<template>
    <StaffLayout>
        <div class="min-h-screen bg-gray-50/30 p-6">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            Good {{ timeOfDay }},
                            {{ dashboardData.staffName || "Staff" }}!
                        </h1>
                        <p class="text-gray-600 text-lg">
                            Here's your performance summary for today
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Today's Date</p>
                            <p class="text-lg font-semibold text-gray-800">
                                {{ currentDate }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-sm"
                        >
                            <v-icon
                                name="bi-person-circle"
                                class="text-white text-xl"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Metrics Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
                <!-- Today's Readings Card -->
                <div
                    class="group relative bg-white rounded-2xl p-6 shadow-xs border border-gray-100 hover:shadow-sm transition-all duration-300"
                >
                    <div class="relative">
                        <div class="flex items-center justify-between mb-6">
                            <div class="p-3 bg-blue-50 rounded-xl">
                                <v-icon
                                    name="bi-droplet"
                                    class="text-blue-600 text-xl"
                                />
                            </div>
                            <span
                                class="text-xs font-medium text-blue-600 bg-blue-50 px-3 py-1.5 rounded-full"
                            >
                                Today
                            </span>
                        </div>
                        <div>
                            <h3 class="text-4xl font-bold text-gray-900 mb-2">
                                {{ dashboardData.todaysReadings || 0 }}
                            </h3>
                            <p class="text-gray-600 font-medium">
                                Meter Readings
                            </p>
                            <div class="flex items-center gap-2 mt-3">
                                <div
                                    class="w-full bg-gray-100 rounded-full h-1.5"
                                >
                                    <div
                                        class="bg-blue-500 h-1.5 rounded-full transition-all duration-1000"
                                        :style="{
                                            width: `${Math.min(
                                                ((dashboardData.todaysReadings ||
                                                    0) /
                                                    50) *
                                                    100,
                                                100
                                            )}%`,
                                        }"
                                    ></div>
                                </div>
                                <span
                                    class="text-sm text-gray-500 whitespace-nowrap"
                                >
                                    {{
                                        Math.round(
                                            ((dashboardData.todaysReadings ||
                                                0) /
                                                50) *
                                                100
                                        )
                                    }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Weekly Readings Card -->
                <div
                    class="group relative bg-white rounded-2xl p-6 shadow-xs border border-gray-100 hover:shadow-sm transition-all duration-300"
                >
                    <div class="relative">
                        <div class="flex items-center justify-between mb-6">
                            <div class="p-3 bg-blue-50 rounded-xl">
                                <v-icon
                                    name="bi-calendar-week"
                                    class="text-blue-600 text-xl"
                                />
                            </div>
                            <span
                                class="text-xs font-medium text-blue-600 bg-blue-50 px-3 py-1.5 rounded-full"
                            >
                                This Week
                            </span>
                        </div>
                        <div>
                            <h3 class="text-4xl font-bold text-gray-900 mb-2">
                                {{ dashboardData.weeklyReadings || 0 }}
                            </h3>
                            <p class="text-gray-600 font-medium">
                                Total Readings
                            </p>
                            <p
                                class="text-sm text-blue-500 mt-2 flex items-center gap-1"
                            >
                                <v-icon name="bi-arrow-up" class="text-sm" />
                                Weekly progress
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Monthly Readings Card -->
                <div
                    class="group relative bg-white rounded-2xl p-6 shadow-xs border border-gray-100 hover:shadow-sm transition-all duration-300"
                >
                    <div class="relative">
                        <div class="flex items-center justify-between mb-6">
                            <div class="p-3 bg-blue-50 rounded-xl">
                                <v-icon
                                    name="bi-calendar-month"
                                    class="text-blue-600 text-xl"
                                />
                            </div>
                            <span
                                class="text-xs font-medium text-blue-600 bg-blue-50 px-3 py-1.5 rounded-full"
                            >
                                This Month
                            </span>
                        </div>
                        <div>
                            <h3 class="text-4xl font-bold text-gray-900 mb-2">
                                {{ dashboardData.monthlyReadings || 0 }}
                            </h3>
                            <p class="text-gray-600 font-medium">
                                Monthly Total
                            </p>
                            <p
                                class="text-sm text-blue-500 mt-2 flex items-center gap-1"
                            >
                                <v-icon name="bi-graph-up" class="text-sm" />
                                Current month
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Performance Card -->
                <div
                    class="group relative bg-white rounded-2xl p-6 shadow-xs border border-gray-100 hover:shadow-sm transition-all duration-300"
                >
                    <div class="relative">
                        <div class="flex items-center justify-between mb-6">
                            <div class="p-3 bg-blue-50 rounded-xl">
                                <v-icon
                                    name="bi-graph-up"
                                    class="text-blue-600 text-xl"
                                />
                            </div>
                            <span
                                class="text-xs font-medium text-blue-600 bg-blue-50 px-3 py-1.5 rounded-full"
                            >
                                Performance
                            </span>
                        </div>
                        <div>
                            <h3 class="text-4xl font-bold text-gray-900 mb-2">
                                {{
                                    Math.round(
                                        ((dashboardData.todaysReadings || 0) /
                                            50) *
                                            100
                                    )
                                }}%
                            </h3>
                            <p class="text-gray-600 font-medium">
                                Daily Target
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                50 readings goal
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Overview Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div
                    v-for="status in statusTypes"
                    :key="status.type"
                    class="bg-white rounded-2xl p-6 shadow-xs border border-gray-100 transition-all duration-300 hover:shadow-sm"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">
                                {{ status.label }}
                            </p>
                            <h3 class="text-3xl font-bold text-gray-900">
                                {{
                                    dashboardData.statusCounts?.[status.type] ||
                                    0
                                }}
                            </h3>
                        </div>
                        <div :class="status.iconBg" class="p-3 rounded-xl">
                            <v-icon
                                :name="status.icon"
                                :class="status.iconColor"
                                class="text-xl"
                            />
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div
                                class="h-1.5 rounded-full transition-all duration-1000"
                                :class="status.progressColor"
                                :style="{
                                    width: `${getStatusPercentage(
                                        status.type
                                    )}%`,
                                }"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Section -->
            <div
                class="bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden"
            >
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-50 rounded-lg">
                                <v-icon
                                    name="bi-activity"
                                    class="text-blue-600 text-lg"
                                />
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">
                                    Recent Activity
                                </h2>
                                <p class="text-sm text-gray-500">
                                    Latest meter readings and updates
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span
                                class="text-sm text-gray-500 bg-gray-100 px-3 py-1.5 rounded-full"
                            >
                                {{
                                    dashboardData.recentActivity?.length || 0
                                }}
                                activities
                            </span>
                            <button
                                class="p-2 hover:bg-gray-50 rounded-lg transition-colors duration-200"
                            >
                                <v-icon
                                    name="bi-filter"
                                    class="text-gray-400 hover:text-gray-600"
                                />
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="space-y-3">
                        <div
                            v-for="activity in dashboardData.recentActivity ||
                            []"
                            :key="activity.id"
                            class="group p-4 rounded-xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50/20 transition-all duration-200"
                        >
                            <div class="flex items-start gap-4">
                                <div
                                    :class="getActivityIconContainer(activity)"
                                    class="p-3 rounded-xl transition-colors duration-200"
                                >
                                    <v-icon
                                        :name="getActivityIcon(activity)"
                                        :class="getActivityIconColor(activity)"
                                        class="text-lg"
                                    />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="flex items-center justify-between mb-3"
                                    >
                                        <div class="flex items-center gap-3">
                                            <span
                                                class="font-semibold text-gray-900 truncate"
                                            >
                                                {{
                                                    activity.customer_name ||
                                                    "Unknown Customer"
                                                }}
                                            </span>
                                            <div
                                                :class="
                                                    getStatusBadgeClass(
                                                        activity.status
                                                    )
                                                "
                                                class="text-xs px-2.5 py-1 rounded-full font-medium"
                                            >
                                                {{ activity.status }}
                                            </div>
                                        </div>
                                        <span
                                            class="text-xs text-gray-400 flex items-center gap-1 whitespace-nowrap"
                                        >
                                            <v-icon
                                                name="bi-clock"
                                                class="text-xs"
                                            />
                                            {{ activity.reading_time }}
                                        </span>
                                    </div>

                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div
                                            class="flex items-center gap-4 text-sm text-gray-600"
                                        >
                                            <span
                                                class="flex items-center gap-1"
                                            >
                                                <v-icon
                                                    name="bi-speedometer2"
                                                    class="text-gray-400"
                                                />
                                                {{
                                                    activity.consumption || 0
                                                }}
                                                m³
                                            </span>
                                            <span
                                                v-if="activity.serial_number"
                                                class="flex items-center gap-1"
                                            >
                                                <v-icon
                                                    name="bi-hash"
                                                    class="text-gray-400"
                                                />
                                                #{{ activity.serial_number }}
                                            </span>
                                            <span
                                                class="flex items-center gap-1"
                                            >
                                                <v-icon
                                                    name="bi-calendar"
                                                    class="text-gray-400"
                                                />
                                                {{
                                                    formatActivityDate(
                                                        activity.reading_date,
                                                        activity.reading_time
                                                    )
                                                }}
                                            </span>
                                        </div>

                                        <div
                                            v-if="activity.is_high_consumption"
                                            class="flex items-center gap-1 text-red-500 text-sm font-medium"
                                        >
                                            <v-icon
                                                name="bi-exclamation-triangle"
                                            />
                                            High Usage
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div
                            v-if="
                                !dashboardData.recentActivity ||
                                dashboardData.recentActivity.length === 0
                            "
                            class="text-center py-12"
                        >
                            <div
                                class="flex flex-col items-center justify-center text-gray-400 max-w-sm mx-auto"
                            >
                                <div class="p-4 bg-gray-50 rounded-2xl mb-4">
                                    <v-icon name="bi-inbox" class="text-3xl" />
                                </div>
                                <p
                                    class="text-lg font-semibold text-gray-500 mb-2"
                                >
                                    No recent activity
                                </p>
                                <p class="text-sm text-gray-400">
                                    Activities will appear here once you start
                                    recording meter readings
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StaffLayout>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import StaffLayout from "@/Layouts/StaffLayout.vue";

const dashboardData = ref({});

// Computed properties for dynamic content
const timeOfDay = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return "morning";
    if (hour < 18) return "afternoon";
    return "evening";
});

const currentDate = computed(() => {
    return new Date().toLocaleDateString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    });
});

const statusTypes = computed(() => [
    {
        type: "paid",
        label: "Paid",
        icon: "bi-check-circle-fill",
        iconColor: "text-green-500",
        iconBg: "bg-green-100",
        progressColor: "bg-green-500",
    },
    {
        type: "pending",
        label: "Pending",
        icon: "bi-hourglass-split",
        iconColor: "text-yellow-500",
        iconBg: "bg-yellow-100",
        progressColor: "bg-yellow-500",
    },
    {
        type: "overdue",
        label: "Overdue",
        icon: "bi-exclamation-triangle-fill",
        iconColor: "text-red-500",
        iconBg: "bg-red-100",
        progressColor: "bg-red-500",
    },
]);

// Helper functions
const getStatusPercentage = (statusType) => {
    const total = Object.values(dashboardData.value.statusCounts || {}).reduce(
        (a, b) => a + b,
        0
    );
    if (total === 0) return 0;
    return (
        ((dashboardData.value.statusCounts?.[statusType] || 0) / total) * 100
    );
};

const formatActivityDate = (dateString, timeString) => {
    if (!dateString || !timeString) return "Unknown date";

    const today = new Date().toDateString();
    const activityDate = new Date(dateString).toDateString();

    if (today === activityDate) {
        return "Today";
    }

    const yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);

    if (yesterday.toDateString() === activityDate) {
        return "Yesterday";
    }

    return new Date(dateString).toLocaleDateString();
};

const getActivityIcon = (activity) => {
    if (activity.is_high_consumption) {
        return "bi-exclamation-triangle-fill";
    }
    if (activity.status === "Paid") {
        return "bi-check-circle-fill";
    }
    if (activity.status === "Overdue") {
        return "bi-exclamation-circle-fill";
    }
    return "bi-clock-fill";
};

const getActivityIconColor = (activity) => {
    if (activity.is_high_consumption) {
        return "text-red-500";
    }
    if (activity.status === "Paid") {
        return "text-green-500";
    }
    if (activity.status === "Overdue") {
        return "text-red-500";
    }
    return "text-yellow-500";
};

const getActivityIconContainer = (activity) => {
    if (activity.is_high_consumption) {
        return "bg-red-100";
    }
    if (activity.status === "Paid") {
        return "bg-green-100";
    }
    if (activity.status === "Overdue") {
        return "bg-red-100";
    }
    return "bg-yellow-100";
};

const getStatusBadgeClass = (status) => {
    switch (status) {
        case "Paid":
            return "bg-green-100 text-green-800 border border-green-200";
        case "Pending":
            return "bg-yellow-100 text-yellow-800 border border-yellow-200";
        case "Overdue":
            return "bg-red-100 text-red-800 border border-red-200";
        default:
            return "bg-gray-100 text-gray-800 border border-gray-200";
    }
};

const fetchDashboardData = async () => {
    try {
        const response = await axios.get(route("staff.dashboard.data"));
        dashboardData.value = response.data;
    } catch (error) {
        console.error("Error fetching dashboard data:", error);
        dashboardData.value = {
            todaysReadings: 0,
            weeklyReadings: 0,
            monthlyReadings: 0,
            recentActivity: [],
            completedToday: 0,
            pendingCount: 0,
            statusCounts: {
                paid: 0,
                pending: 0,
                overdue: 0,
            },
        };
    }
};

onMounted(() => {
    fetchDashboardData();
});
</script>

<style scoped>
/* Smooth transitions for all interactive elements */
.transition-all {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Custom shadow for subtle elevation */
.shadow-xs {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
</style>

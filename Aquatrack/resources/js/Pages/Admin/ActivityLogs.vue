<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import { ref, watch, computed, onMounted, onUnmounted } from "vue";
import { debounce } from "lodash";

const page = usePage();
const activities = computed(() => page.props.activities);
const filters = computed(() => page.props.filters);
const eventTypes = computed(() => page.props.event_types);

// Filter states
const eventType = ref(filters.value.event_type || "all");
const dateFrom = ref(filters.value.date_from || "");
const dateTo = ref(filters.value.date_to || "");
const searchTerm = ref(filters.value.search || "");
const showFilterDropdown = ref(false);

// Activity display helpers
const activityConfig = {
    created: {
        icon: "bi-plus-circle",
        bg: "bg-blue-100 dark:bg-blue-900/30",
        text: "text-blue-800 dark:text-blue-200",
        title: "New record created",
    },
    updated: {
        icon: "bi-pencil-square",
        bg: "bg-green-100 dark:bg-green-900/30",
        text: "text-green-800 dark:text-green-200",
        title: "Record updated",
    },
    deleted: {
        icon: "bi-trash",
        bg: "bg-red-100 dark:bg-red-900/30",
        text: "text-red-800 dark:text-red-200",
        title: "Record deleted",
    },
    logged_in: {
        icon: "bi-box-arrow-in-right",
        bg: "bg-purple-100 dark:bg-purple-900/30",
        text: "text-purple-800 dark:text-purple-200",
        title: "User login",
    },
    logged_out: {
        icon: "bi-box-arrow-left",
        bg: "bg-gray-100 dark:bg-gray-900/30",
        text: "text-gray-800 dark:text-gray-200",
        title: "User logout",
    },
    verification_success: {
        icon: "bi-shield-check",
        bg: "bg-teal-100 dark:bg-teal-900/30",
        text: "text-teal-800 dark:text-teal-200",
        title: "Verification passed",
    },
    verification_failed: {
        icon: "bi-shield-exclamation",
        bg: "bg-amber-100 dark:bg-amber-900/30",
        text: "text-amber-800 dark:text-amber-200",
        title: "Verification failed",
    },
    default: {
        icon: "bi-stars",
        bg: "bg-indigo-100 dark:bg-indigo-900/30",
        text: "text-indigo-800 dark:text-indigo-200",
        title: "System activity",
    },
    report_created: {
        icon: "bi-flag",
        bg: "bg-amber-100 dark:bg-amber-900/30",
        text: "text-amber-800 dark:text-amber-200",
        title: "New Report Submitted",
    },
    report_status_changed: {
        icon: "bi-arrow-repeat",
        bg: "bg-blue-100 dark:bg-blue-900/30",
        text: "text-blue-800 dark:text-blue-200",
        title: "Report Status Updated",
    },
};

const getActivityConfig = (activity) => {
    if (activity.subject_type === "App\\Models\\Report") {
        return activity.event === "report_created"
            ? activityConfig.report_created
            : activityConfig.report_status_changed;
    }
    return activityConfig[activity.event] || activityConfig.default;
};

const getActivityIcon = (activity) => {
    return getActivityConfig(activity).icon;
};

const getActivityBgColor = (activity) => {
    return getActivityConfig(activity).bg;
};

const getActivityTextColor = (activity) => {
    return getActivityConfig(activity).text;
};

const getActivityTitle = (activity) => {
    return getActivityConfig(activity).title;
};

const formatTimeAgo = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    const intervals = {
        year: 31536000,
        month: 2592000,
        week: 604800,
        day: 86400,
        hour: 3600,
        minute: 60,
    };

    if (seconds < 60) return "Just now";

    for (const [unit, secondsInUnit] of Object.entries(intervals)) {
        const interval = Math.floor(seconds / secondsInUnit);
        if (interval >= 1) {
            return `${interval} ${unit}${interval === 1 ? "" : "s"} ago`;
        }
    }

    return date.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: date.getFullYear() !== now.getFullYear() ? "numeric" : undefined,
    });
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Filter functions
const applyFilters = debounce(() => {
    router.get(
        route("admin.activity-logs"),
        {
            event_type: eventType.value,
            date_from: dateFrom.value,
            date_to: dateTo.value,
            search: searchTerm.value,
        },
        {
            preserveState: true,
            replace: true,
        }
    );
}, 300);

const clearFilters = () => {
    eventType.value = "all";
    dateFrom.value = "";
    dateTo.value = "";
    searchTerm.value = "";
    showFilterDropdown.value = false;
    applyFilters();
};

// Watch for filter changes
watch([eventType, dateFrom, dateTo, searchTerm], () => {
    applyFilters();
});

const exportLogs = () => {
    router.visit(route("admin.activity-logs.export"), {
        data: {
            event_type: eventType.value,
            date_from: dateFrom.value,
            date_to: dateTo.value,
            search: searchTerm.value,
        },
        method: "get",
    });
};

// Handle click outside to close filter dropdown
const handleClickOutside = (event) => {
    if (!event.target.closest(".relative")) {
        showFilterDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});

// Computed properties for statistics
const totalActivities = computed(() => activities.value?.total || 0);
const createdEventsCount = computed(
    () =>
        activities.value?.data?.filter((a) => a.event === "created").length || 0
);
const updatedEventsCount = computed(
    () =>
        activities.value?.data?.filter((a) => a.event === "updated").length || 0
);
const userActivitiesCount = computed(
    () =>
        activities.value?.data?.filter((a) => a.event.includes("logged"))
            .length || 0
);

const createdPercentage = computed(() => {
    if (!totalActivities.value || totalActivities.value === 0) return 0;
    return Math.round((createdEventsCount.value / totalActivities.value) * 100);
});

const updatedPercentage = computed(() => {
    if (!totalActivities.value || totalActivities.value === 0) return 0;
    return Math.round((updatedEventsCount.value / totalActivities.value) * 100);
});

const userPercentage = computed(() => {
    if (!totalActivities.value || totalActivities.value === 0) return 0;
    return Math.round(
        (userActivitiesCount.value / totalActivities.value) * 100
    );
});
</script>

<template>
    <AdminLayout>
        <div class="mx-auto w-full">
            <div class="lg:items-center lg:flex mb-4 hidden">
                <v-icon
                    name="bi-activity"
                    class="mr-2 text-green-500"
                    scale="1.5"
                />
                <h1 class="text-2xl">Activity Logs</h1>
            </div>

            <!-- Stats Cards Grid -->
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6"
            >
                <!-- Total Activities Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400"
                        >
                            <v-icon name="bi-activity" class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                Total Activities
                            </p>
                            <h3
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ totalActivities }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div
                            class="flex items-center text-sm text-blue-600 dark:text-blue-400"
                        >
                            <v-icon name="bi-list-ul" class="w-4 h-4 mr-1" />
                            <span>All system activities</span>
                        </div>
                    </div>
                </div>

                <!-- Created Events Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400"
                        >
                            <v-icon name="bi-plus-circle" class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                Created Events
                            </p>
                            <h3
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ createdEventsCount }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div
                            class="flex items-center text-sm text-green-600 dark:text-green-400"
                        >
                            <v-icon
                                name="bi-file-earmark-plus"
                                class="w-4 h-4 mr-1"
                            />
                            <span>{{ createdPercentage }}% of total</span>
                        </div>
                    </div>
                </div>

                <!-- Updated Events Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400"
                        >
                            <v-icon name="bi-pencil-square" class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                Updated Events
                            </p>
                            <h3
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ updatedEventsCount }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div
                            class="flex items-center text-sm text-yellow-600 dark:text-yellow-400"
                        >
                            <v-icon
                                name="bi-arrow-repeat"
                                class="w-4 h-4 mr-1"
                            />
                            <span>{{ updatedPercentage }}% of total</span>
                        </div>
                    </div>
                </div>

                <!-- User Activities Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400"
                        >
                            <v-icon name="bi-people" class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                User Activities
                            </p>
                            <h3
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ userActivitiesCount }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div
                            class="flex items-center text-sm text-purple-600 dark:text-purple-400"
                        >
                            <v-icon
                                name="bi-person-check"
                                class="w-4 h-4 mr-1"
                            />
                            <span>{{ userPercentage }}% of total</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Header Section with Filters -->
            <div
                class="relative bg-white shadow-md dark:bg-gray-800 sm:rounded-lg mb-6"
            >
                <div
                    class="flex flex-col items-center justify-between p-4 space-y-3 md:flex-row md:space-y-0 md:space-x-4"
                >
                    <div class="w-full md:w-1/2">
                        <form class="flex items-center">
                            <label for="simple-search" class="sr-only"
                                >Search</label
                            >
                            <div class="relative w-full">
                                <div
                                    class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                >
                                    <v-icon
                                        name="hi-solid-search"
                                        class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                    />
                                </div>
                                <input
                                    v-model="searchTerm"
                                    type="text"
                                    id="simple-search"
                                    class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Search activities, users..."
                                    @keyup.enter="applyFilters"
                                />
                            </div>
                        </form>
                    </div>
                    <div
                        class="flex flex-col items-stretch justify-end flex-shrink-0 w-full space-y-2 md:w-auto md:flex-row md:space-y-0 md:items-center md:space-x-3"
                    >
                        <div
                            class="flex items-center w-full space-x-3 md:w-auto"
                        >
                            <div class="relative">
                                <button
                                    @click="
                                        showFilterDropdown = !showFilterDropdown
                                    "
                                    class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg md:w-auto focus:outline-none hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                    type="button"
                                >
                                    <v-icon
                                        name="hi-solid-filter"
                                        class="w-4 h-4 mr-2 text-gray-400"
                                    />
                                    Filter
                                    <v-icon
                                        name="hi-chevron-down"
                                        class="-mr-1 ml-1.5 w-5 h-5"
                                    />
                                </button>

                                <!-- Custom Filter Dropdown -->
                                <div
                                    v-if="showFilterDropdown"
                                    class="absolute z-10 top-full right-0 mt-1 w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700"
                                >
                                    <h6
                                        class="mb-3 text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        Event Type
                                    </h6>
                                    <ul class="space-y-2 text-sm">
                                        <li class="flex items-center">
                                            <input
                                                id="event-all"
                                                type="radio"
                                                name="event"
                                                value="all"
                                                :checked="eventType === 'all'"
                                                @change="eventType = 'all'"
                                                class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                            />
                                            <label
                                                for="event-all"
                                                class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                                >All Events</label
                                            >
                                        </li>
                                        <li
                                            v-for="(label, value) in eventTypes"
                                            :key="value"
                                            class="flex items-center"
                                            v-if="value !== 'all'"
                                        >
                                            <input
                                                :id="`event-${value}`"
                                                type="radio"
                                                name="event"
                                                :value="value"
                                                :checked="eventType === value"
                                                @change="eventType = value"
                                                class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                            />
                                            <label
                                                :for="`event-${value}`"
                                                class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                                >{{ label }}</label
                                            >
                                        </li>
                                    </ul>
                                    <h6
                                        class="mb-3 mt-4 text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        Date Range
                                    </h6>
                                    <div class="space-y-2">
                                        <input
                                            v-model="dateFrom"
                                            type="date"
                                            class="w-full p-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                            placeholder="From date"
                                        />
                                        <input
                                            v-model="dateTo"
                                            type="date"
                                            class="w-full p-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                            placeholder="To date"
                                        />
                                    </div>
                                </div>
                            </div>
                            <button
                                @click="clearFilters"
                                class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg md:w-auto focus:outline-none hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                            >
                                <v-icon
                                    name="hi-refresh"
                                    class="w-4 h-4 mr-1"
                                />
                                Reset Filters
                            </button>
                            <!-- <button
                                @click="exportLogs"
                                class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg md:w-auto hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900"
                            >
                                <v-icon
                                    name="hi-download"
                                    class="w-4 h-4 mr-1"
                                />
                                Export
                            </button> -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity List (Grid Layout) -->
            <div class="grid grid-cols-1 gap-4 mb-6">
                <div
                    v-for="activity in activities.data"
                    :key="activity.id"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-md transition-shadow"
                >
                    <div class="flex items-start gap-4">
                        <div
                            :class="`p-3 rounded-full ${getActivityBgColor(
                                activity
                            )}`"
                        >
                            <v-icon
                                :name="getActivityIcon(activity)"
                                :class="`w-5 h-5 ${getActivityTextColor(
                                    activity
                                )}`"
                            />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <h3
                                    class="text-lg font-semibold text-gray-800 dark:text-white"
                                >
                                    {{ getActivityTitle(activity) }}
                                </h3>
                                <span
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ formatTimeAgo(activity.created_at) }}
                                </span>
                            </div>
                            <p
                                class="text-sm text-gray-600 dark:text-gray-300 mb-2"
                            >
                                {{ activity.description }}
                                <span
                                    v-if="activity.properties?.tracking_code"
                                    class="font-medium text-blue-600 dark:text-blue-400 ml-1"
                                >
                                    ({{ activity.properties.tracking_code }})
                                </span>
                            </p>
                            <div class="flex items-center justify-between">
                                <div
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    <span v-if="activity.causer_name">
                                        By {{ activity.causer_name }}
                                    </span>
                                    <span v-else>By System</span>
                                    <span
                                        v-if="activity.properties?.old_status"
                                        class="ml-2"
                                    >
                                        • Status:
                                        {{ activity.properties.old_status }} →
                                        {{ activity.properties.new_status }}
                                    </span>
                                </div>
                                <span
                                    class="text-xs text-gray-400 dark:text-gray-500"
                                >
                                    {{ formatDate(activity.created_at) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-if="activities.data.length === 0"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center"
                >
                    <div
                        class="flex flex-col items-center justify-center space-y-3"
                    >
                        <v-icon
                            name="bi-activity"
                            class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-2"
                        />
                        <span
                            class="font-semibold text-gray-400 dark:text-gray-500"
                            >No activities found</span
                        >
                        <span class="text-sm text-gray-300 dark:text-gray-600"
                            >Try adjusting your filters or search
                            keywords.</span
                        >
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div
                v-if="activities.data.length > 0"
                class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex items-center justify-between"
            >
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Showing {{ activities.from }} to {{ activities.to }} of
                    {{ activities.total }} activities
                </p>
                <div class="flex items-center gap-2">
                    <Link
                        v-if="activities.prev_page_url"
                        :href="activities.prev_page_url"
                        class="px-3 py-1 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                    >
                        Previous
                    </Link>
                    <Link
                        v-if="activities.next_page_url"
                        :href="activities.next_page_url"
                        class="px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
                    >
                        Next
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}

/* Disabled state for buttons */
button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>

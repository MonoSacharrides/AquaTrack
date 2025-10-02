<!-- resources/js/Pages/Admin/Notifications.vue -->
<template>
    <AdminLayout>
        <div class="mx-auto w-full">
            <div class="lg:items-center lg:flex mb-4 hidden">
                <v-icon
                    name="bi-bell"
                    class="mr-2 text-green-500"
                    scale="1.5"
                />
                <h1 class="text-2xl">Notifications Management</h1>
            </div>

            <!-- Notification Stats Cards Grid -->
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6"
            >
                <!-- Total Notifications Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400"
                        >
                            <v-icon name="bi-bell-fill" class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                Total Notifications
                            </p>
                            <h3
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ notifications.length || 0 }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div
                            class="flex items-center text-sm text-blue-600 dark:text-blue-400"
                        >
                            <v-icon name="bi-list-ul" class="w-4 h-4 mr-1" />
                            <span>All system notifications</span>
                        </div>
                    </div>
                </div>

                <!-- Unread Notifications Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400"
                        >
                            <v-icon name="bi-envelope-open" class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                Unread Notifications
                            </p>
                            <h3
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ unreadCount }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div
                            class="flex items-center text-sm text-green-600 dark:text-green-400"
                        >
                            <v-icon name="bi-arrow-up" class="w-4 h-4 mr-1" />
                            <span>{{ unreadPercentage }}% of total</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Notifications Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400"
                        >
                            <v-icon name="bi-clock-fill" class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                Recent (7 days)
                            </p>
                            <h3
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ recentNotificationsCount }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div
                            class="flex items-center text-sm text-purple-600 dark:text-purple-400"
                        >
                            <v-icon name="bi-calendar" class="w-4 h-4 mr-1" />
                            <span>Last 7 days</span>
                        </div>
                    </div>
                </div>

                <!-- Read Notifications Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400"
                        >
                            <v-icon
                                name="bi-check-circle-fill"
                                class="w-6 h-6"
                            />
                        </div>
                        <div class="ml-4">
                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                Read Notifications
                            </p>
                            <h3
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ readCount }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div
                            class="flex items-center text-sm text-orange-600 dark:text-orange-400"
                        >
                            <v-icon name="bi-eye" class="w-4 h-4 mr-1" />
                            <span>Already viewed</span>
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
                                    v-model="filters.search"
                                    type="text"
                                    id="simple-search"
                                    class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Search type, message..."
                                    @keyup.enter="applyFilters"
                                />
                            </div>
                        </form>
                    </div>
                    <div
                        class="flex flex-col items-stretch justify-end flex-shrink-0 w-full space-y-2 md:w-auto md:flex-row md:space-y-0 md:items-center md:space-x-3"
                    >
                        <button
                            @click="clearFilters"
                            class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg md:w-auto focus:outline-none hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                        >
                            <v-icon name="hi-refresh" class="w-4 h-4 mr-1" />
                            Reset Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Notification List (Grid Layout) -->
            <div class="grid grid-cols-1 gap-4 mb-6">
                <div
                    v-for="notification in filteredNotifications"
                    :key="notification.id"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-md transition-shadow"
                    :class="
                        notification.unread ? 'border-l-4 border-blue-500' : ''
                    "
                >
                    <div class="flex items-start gap-4">
                        <div
                            class="p-3 rounded-full bg-gray-100 dark:bg-gray-700"
                        >
                            <v-icon
                                name="bi-bell-fill"
                                class="w-5 h-5 text-gray-600 dark:text-gray-300"
                            />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <h3
                                    class="text-lg font-semibold text-gray-800 dark:text-white capitalize"
                                >
                                    {{ notification.type.replace("_", " ") }}
                                </h3>
                                <span
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{
                                        getRelativeTime(
                                            getDateField(notification)
                                        )
                                    }}
                                </span>
                            </div>
                            <p
                                class="text-sm text-gray-600 dark:text-gray-300 mb-2 line-clamp-2"
                            >
                                {{ notification.message }}
                            </p>
                            <div class="flex items-center justify-between">
                                <div
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    <span>System Notification</span>
                                </div>
                                <span
                                    class="text-xs text-gray-400 dark:text-gray-500"
                                >
                                    {{ formatDate(getDateField(notification)) }}
                                </span>
                            </div>
                            <div v-if="notification.unread" class="mt-2">
                                <button
                                    @click="markAsRead(notification.id)"
                                    class="text-blue-600 hover:text-blue-800 transition-colors text-sm"
                                    title="Mark as Read"
                                >
                                    <v-icon name="bi-check" class="w-5 h-5" />
                                    Mark as Read
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-if="filteredNotifications.length === 0"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center"
                >
                    <div
                        class="flex flex-col items-center justify-center space-y-3"
                    >
                        <v-icon
                            name="bi-bell"
                            class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-2"
                        />
                        <span
                            class="font-semibold text-gray-400 dark:text-gray-500"
                            >No notifications found</span
                        >
                        <span class="text-sm text-gray-300 dark:text-gray-600"
                            >Try adjusting your search keywords.</span
                        >
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ref, computed, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { pickBy, debounce } from "lodash";
import { usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const page = usePage();

const props = defineProps({
    notifications: Array,
    errors: Object,
    filters: Object,
});

const filters = ref({
    search: props.filters?.search || "",
    sort: props.filters?.sort || "type",
    order: props.filters?.order || "desc",
});

const isResetting = ref(false);

// Computed properties
const filteredNotifications = computed(() => {
    let filtered = [...props.notifications];

    // Apply search filter
    if (filters.value.search) {
        const searchTerm = filters.value.search.toLowerCase();
        filtered = filtered.filter(
            (notification) =>
                notification.type.toLowerCase().includes(searchTerm) ||
                notification.message.toLowerCase().includes(searchTerm)
        );
    }

    // Apply sorting
    filtered.sort((a, b) => {
        let aValue = a[filters.value.sort];
        let bValue = b[filters.value.sort];

        if (typeof aValue === "string") {
            aValue = aValue.toLowerCase();
            bValue = bValue.toLowerCase();
        }

        if (filters.value.order === "asc") {
            return aValue > bValue ? 1 : -1;
        } else {
            return aValue < bValue ? 1 : -1;
        }
    });

    return filtered;
});

const unreadCount = computed(
    () => filteredNotifications.value.filter((n) => n.unread).length
);
const readCount = computed(
    () => filteredNotifications.value.length - unreadCount.value
);
const recentNotificationsCount = computed(() => {
    const oneWeekAgo = new Date();
    oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
    return filteredNotifications.value.filter((notification) => {
        const notificationDate = new Date(getDateField(notification));
        return notificationDate >= oneWeekAgo;
    }).length;
});
const unreadPercentage = computed(() => {
    if (!filteredNotifications.value.length) return 0;
    return Math.round(
        (unreadCount.value / filteredNotifications.value.length) * 100
    );
});

// Debounced fetch with request tracking
const debouncedFetchNotifications = debounce((filters, requestId) => {
    router.get("/admin/notifications", pickBy(filters), {
        preserveState: true,
        replace: true,
        onSuccess: () => {
            if (requestId === latestRequestId) {
                // Handle success if needed
            }
        },
        onError: (errors) => {
            if (requestId === latestRequestId) {
                console.error("Fetch error:", errors);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to fetch notifications. Please try again.",
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                });
            }
        },
    });
}, 300);

let latestRequestId = 0;

watch(
    () => page.props.flash,
    (newFlash) => {
        if (newFlash.success) {
            Swal.fire({
                icon: "success",
                title: "Success",
                text: newFlash.message,
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        }
    },
    { immediate: true }
);

// Watch filter changes
watch(
    () => ({
        search: filters.value.search,
        sort: filters.value.sort,
        order: filters.value.order,
    }),
    (newFilters) => {
        latestRequestId++;
        debouncedFetchNotifications(newFilters, latestRequestId);
    },
    { deep: true }
);

// Helper functions
const statusClasses = (unread) => ({
    "px-2 py-1 rounded-full text-xs font-medium": true,
    "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200": unread,
    "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300": !unread,
});

const getDateField = (notification) => {
    if (notification.type === "overdue_warning") return notification.due_date;
    if (notification.type === "announcement") return notification.created_at;
    return notification.updated_at || notification.created_at;
};

const getRelativeTime = (dateString) => {
    if (!dateString) return "";
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return "a few seconds ago";
    if (diffInSeconds < 3600)
        return `${Math.floor(diffInSeconds / 60)} minutes ago`;
    if (diffInSeconds < 86400)
        return `${Math.floor(diffInSeconds / 3600)} hours ago`;
    if (diffInSeconds < 604800)
        return `${Math.floor(diffInSeconds / 86400)} days ago`;

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

const sortBy = (column) => {
    if (filters.value.sort === column) {
        filters.value.order = filters.value.order === "asc" ? "desc" : "asc";
    } else {
        filters.value.sort = column;
        filters.value.order = "asc";
    }
};

const applyFilters = () => {
    latestRequestId++;
    debouncedFetchNotifications(filters.value, latestRequestId);
};

const clearFilters = () => {
    isResetting.value = true;
    setTimeout(() => {
        filters.value = {
            search: "",
            sort: "type",
            order: "desc",
        };
        isResetting.value = false;
    }, 1500);
};

const markAsRead = async (notificationId) => {
    await router.post(
        route("notifications.mark-read"),
        { notification_id: notificationId },
        {
            preserveScroll: true,
        }
    );
};
</script>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}

.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

.animate-spin {
    animation: spin 1.5s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>

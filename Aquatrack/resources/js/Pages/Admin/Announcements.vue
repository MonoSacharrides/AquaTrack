<template>
    <AdminLayout>
        <div class="mx-auto w-full">
        <div class="lg:items-center lg:flex mb-4 hidden">
            <v-icon
                name="io-megaphone-sharp"
                class="mr-2 text-yellow-500"
                scale="1.5"
            />
            <h1 class="text-2xl">Announcements Management</h1>
        </div>

            <!-- Announcement Stats Cards Grid -->
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6"
            >
                <!-- Total Announcements Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400"
                        >
                            <v-icon name="hi-speakerphone" class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                Total Announcements
                            </p>
                            <h3
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ announcements.length || 0 }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div
                            class="flex items-center text-sm text-blue-600 dark:text-blue-400"
                        >
                            <v-icon
                                name="hi-document-text"
                                class="w-4 h-4 mr-1"
                            />
                            <span>All system announcements</span>
                        </div>
                    </div>
                </div>

                <!-- Active Announcements Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400"
                        >
                            <v-icon name="hi-check-circle" class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                Active Announcements
                            </p>
                            <h3
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ activeAnnouncementsCount }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div
                            class="flex items-center text-sm text-green-600 dark:text-green-400"
                        >
                            <v-icon
                                name="hi-arrow-sm-up"
                                class="w-4 h-4 mr-1"
                            />
                            <span>{{ activePercentage }}% of total</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Announcements Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400"
                        >
                            <v-icon name="hi-clock" class="w-6 h-6" />
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
                                {{ recentAnnouncementsCount }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div
                            class="flex items-center text-sm text-purple-600 dark:text-purple-400"
                        >
                            <v-icon name="hi-calendar" class="w-4 h-4 mr-1" />
                            <span>Last 7 days</span>
                        </div>
                    </div>
                </div>

                <!-- Inactive Announcements Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400"
                        >
                            <v-icon name="hi-x-circle" class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                Inactive Announcements
                            </p>
                            <h3
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ inactiveAnnouncementsCount }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div
                            class="flex items-center text-sm text-orange-600 dark:text-orange-400"
                        >
                            <v-icon
                                name="hi-lock-closed"
                                class="w-4 h-4 mr-1"
                            />
                            <span>Not currently visible</span>
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
                                    placeholder="Search title, content..."
                                    @keyup.enter="fetchAnnouncements"
                                />
                            </div>
                        </form>
                    </div>
                    <div
                        class="flex flex-col items-stretch justify-end flex-shrink-0 w-full space-y-2 md:w-auto md:flex-row md:space-y-0 md:items-center md:space-x-3"
                    >
                        <button
                            @click="openCreateModal"
                            type="button"
                            class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                        >
                            <v-icon
                                name="hi-solid-plus"
                                class="h-3.5 w-3.5 mr-2"
                            />
                            Create Announcement
                        </button>
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
                                        Status
                                    </h6>
                                    <ul class="space-y-2 text-sm">
                                        <li class="flex items-center">
                                            <input
                                                id="status-all"
                                                type="radio"
                                                name="status"
                                                value=""
                                                :checked="!filters.status"
                                                @change="updateStatusFilter('')"
                                                class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                            />
                                            <label
                                                for="status-all"
                                                class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                                >All Status</label
                                            >
                                        </li>
                                        <li class="flex items-center">
                                            <input
                                                id="status-active"
                                                type="radio"
                                                name="status"
                                                value="active"
                                                :checked="
                                                    filters.status === 'active'
                                                "
                                                @change="
                                                    updateStatusFilter('active')
                                                "
                                                class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-green-600 focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                            />
                                            <label
                                                for="status-active"
                                                class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                                >Active</label
                                            >
                                        </li>
                                        <li class="flex items-center">
                                            <input
                                                id="status-inactive"
                                                type="radio"
                                                name="status"
                                                value="inactive"
                                                :checked="
                                                    filters.status ===
                                                    'inactive'
                                                "
                                                @change="
                                                    updateStatusFilter(
                                                        'inactive'
                                                    )
                                                "
                                                class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-red-600 focus:ring-red-500 dark:focus:ring-red-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                            />
                                            <label
                                                for="status-inactive"
                                                class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                                >Inactive</label
                                            >
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <button
                                @click="resetFilters"
                                class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg md:w-auto focus:outline-none hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                            >
                                <v-icon
                                    name="hi-refresh"
                                    class="w-4 h-4 mr-1"
                                    :class="{ 'animate-spin': isResetting }"
                                />
                                Reset Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="relative overflow-x-auto mb-4 shadow-md sm:rounded-lg">
                <table
                    class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                >
                    <thead
                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                    >
                        <tr>
                            <th scope="col" class="px-6 py-3">ID</th>
                            <th
                                scope="col"
                                class="px-6 py-3 cursor-pointer"
                                @click="sortBy('title')"
                            >
                                <div class="flex items-center">
                                    Title
                                    <v-icon
                                        v-if="filters.sort === 'title'"
                                        :name="
                                            filters.order === 'asc'
                                                ? 'hi-sort-ascending'
                                                : 'hi-sort-descending'
                                        "
                                        class="w-4 h-4 ml-1"
                                    />
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Content Preview
                            </th>
                            <th scope="col" class="px-6 py-3">Start Date</th>
                            <th scope="col" class="px-6 py-3">End Date</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="announcement in filteredAnnouncements"
                            :key="announcement.id"
                            class="border-b dark:border-gray-700 border-gray-200"
                            :class="
                                announcement.status === 'active'
                                    ? 'bg-white dark:bg-gray-900'
                                    : 'bg-gray-50 dark:bg-gray-800 opacity-70'
                            "
                        >
                            <th
                                scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                            >
                                {{ announcement.id }}
                            </th>
                            <td
                                class="px-6 py-4 font-medium text-gray-900 dark:text-white"
                            >
                                {{ announcement.title }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="truncate max-w-xs">{{
                                    truncateContent(announcement.content, 50)
                                }}</span>
                            </td>
                            <td class="px-6 py-4">
                                {{
                                    formatDate(announcement.start_date) || "N/A"
                                }}
                            </td>
                            <td class="px-6 py-4">
                                {{ formatDate(announcement.end_date) || "N/A" }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    :class="statusClasses(announcement.status)"
                                    class="inline-flex items-center text-xs px-2 py-1 rounded-full"
                                >
                                    <v-icon
                                        :name="
                                            announcement.status === 'active'
                                                ? 'hi-check-circle'
                                                : 'hi-x-circle'
                                        "
                                        class="w-3 h-3 mr-1"
                                    />
                                    {{ announcement.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-3">
                                    <button
                                        @click="editAnnouncement(announcement)"
                                        class="text-blue-600 hover:text-blue-800 transition-colors"
                                        title="Edit"
                                    >
                                        <v-icon
                                            name="hi-pencil"
                                            class="w-5 h-5"
                                        />
                                    </button>
                                    <!-- <button
                                        @click="
                                            toggleAnnouncementStatus(
                                                announcement
                                            )
                                        "
                                        :class="
                                            announcement.status === 'active'
                                                ? 'text-orange-500 hover:text-orange-700'
                                                : 'text-green-500 hover:text-green-700'
                                        "
                                        class="transition-colors"
                                        :title="
                                            announcement.status === 'active'
                                                ? 'Deactivate'
                                                : 'Activate'
                                        "
                                    >
                                        <v-icon
                                            :name="
                                                announcement.status === 'active'
                                                    ? 'hi-lock-closed'
                                                    : 'hi-lock-open'
                                            "
                                            class="w-5 h-5"
                                        />
                                    </button> -->
                                    <button
                                        @click="
                                            deleteAnnouncement(announcement.id)
                                        "
                                        class="text-red-600 hover:text-red-800 transition-colors"
                                        title="Delete"
                                    >
                                        <v-icon
                                            name="hi-trash"
                                            class="w-5 h-5"
                                        />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredAnnouncements.length === 0">
                            <td
                                colspan="7"
                                class="px-6 py-8 text-center text-sm text-gray-500"
                            >
                                <div
                                    class="flex flex-col items-center justify-center space-y-2"
                                >
                                    <v-icon
                                        name="hi-speakerphone"
                                        class="w-10 h-10 text-gray-300 mb-2"
                                    />
                                    <span class="font-semibold text-gray-400"
                                        >No announcements found</span
                                    >
                                    <span class="text-xs text-gray-300"
                                        >Try adjusting your filters or search
                                        keywords.</span
                                    >
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Announcement Modal Component -->
            <AnnouncementModal
                :show="showModal"
                :editing="editing"
                :form="form"
                :errors="errors"
                @close="closeModal"
                @submit="editing ? updateAnnouncement() : createAnnouncement()"
            />
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AnnouncementModal from "@/Components/Modals/AnnouncementModal.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import { pickBy, debounce } from "lodash";
import Swal from "sweetalert2";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    announcements: Array,
    errors: Object,
    filters: Object,
});

const filters = ref({
    search: props.filters?.search || "",
    status: props.filters?.status || "",
    sort: props.filters?.sort || "id",
    order: props.filters?.order || "desc",
});

const showModal = ref(false);
const editing = ref(false);
const showFilterDropdown = ref(false);
const currentId = ref(null);
const isResetting = ref(false);

const form = useForm({
    title: "",
    content: "",
    status: "active",
    start_date: null,
    end_date: null,
});

// Computed property for filtered announcements
const filteredAnnouncements = computed(() => {
    let filtered = [...props.announcements];

    // Apply search filter
    if (filters.value.search) {
        const searchTerm = filters.value.search.toLowerCase();
        filtered = filtered.filter(
            (announcement) =>
                announcement.title.toLowerCase().includes(searchTerm) ||
                announcement.content.toLowerCase().includes(searchTerm)
        );
    }

    // Apply status filter
    if (filters.value.status) {
        filtered = filtered.filter(
            (announcement) =>
                announcement.status.toLowerCase() === filters.value.status
        );
    }

    // Apply sorting
    filtered.sort((a, b) => {
        let aValue = a[filters.value.sort];
        let bValue = b[filters.value.sort];

        if (
            filters.value.sort === "start_date" ||
            filters.value.sort === "end_date"
        ) {
            aValue = new Date(aValue || 0);
            bValue = new Date(bValue || 0);
        }

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

// Computed properties for statistics
const activeAnnouncementsCount = computed(() => {
    return filteredAnnouncements.value.filter(
        (announcement) => announcement.status.toLowerCase() === "active"
    ).length;
});

const inactiveAnnouncementsCount = computed(() => {
    return filteredAnnouncements.value.filter(
        (announcement) => announcement.status.toLowerCase() === "inactive"
    ).length;
});

const recentAnnouncementsCount = computed(() => {
    const oneWeekAgo = new Date();
    oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
    return filteredAnnouncements.value.filter((announcement) => {
        const announcementDate = new Date(
            announcement.created_at || announcement.date
        );
        return announcementDate >= oneWeekAgo;
    }).length;
});

const activePercentage = computed(() => {
    if (!filteredAnnouncements.value.length) return 0;
    return Math.round(
        (activeAnnouncementsCount.value / filteredAnnouncements.value.length) *
            100
    );
});

// Debounced fetch with request tracking
const debouncedFetchAnnouncements = debounce((filters, requestId) => {
    router.get("/admin/announcements", pickBy(filters), {
        preserveState: true,
        replace: true,
        onSuccess: () => {
            if (requestId === latestRequestId) {
            }
        },
        onError: (errors) => {
            if (requestId === latestRequestId) {
                console.error("Fetch error:", errors);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to fetch announcements. Please try again.",
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                });
            }
        },
    });
}, 300);

// Store the latest request ID to prevent race conditions
let latestRequestId = 0;

// Watch filter changes
watch(
    () => ({
        search: filters.value.search,
        status: filters.value.status,
        sort: filters.value.sort,
        order: filters.value.order,
    }),
    (newFilters) => {
        latestRequestId++;
        debouncedFetchAnnouncements(newFilters, latestRequestId);
    },
    { deep: true }
);

// Helper functions
const statusClasses = (status) => {
    const lowerStatus = status.toLowerCase();
    return {
        "px-2 py-1 rounded-full text-xs font-medium": true,
        "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200":
            lowerStatus === "active",
        "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200":
            lowerStatus === "inactive",
    };
};

const formatDate = (dateString) => {
    if (!dateString) return "";
    const options = { year: "numeric", month: "short", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const truncateContent = (content, length) => {
    if (!content) return "";
    return content.length > length
        ? content.substring(0, length) + "..."
        : content;
};

const sortBy = (column) => {
    if (filters.value.sort === column) {
        filters.value.order = filters.value.order === "asc" ? "desc" : "asc";
    } else {
        filters.value.sort = column;
        filters.value.order = "asc";
    }
};

const resetFilters = () => {
    isResetting.value = true;
    setTimeout(() => {
        filters.value = {
            search: "",
            status: "",
            sort: "id",
            order: "desc",
        };
        showFilterDropdown.value = false;
        isResetting.value = false;
    }, 1500);
};

const updateStatusFilter = (status) => {
    filters.value.status = filters.value.status === status ? "" : status;
    latestRequestId++;
    debouncedFetchAnnouncements(filters.value, latestRequestId);
};

const fetchAnnouncements = () => {
    latestRequestId++;
    debouncedFetchAnnouncements(filters.value, latestRequestId);
};

// Modal control functions
const openCreateModal = () => {
    editing.value = false;
    form.reset();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editing.value = false;
    form.reset();
    currentId.value = null;
};

// CRUD operations
const editAnnouncement = (announcement) => {
    editing.value = true;
    currentId.value = announcement.id;
    form.title = announcement.title;
    form.content = announcement.content;
    form.status = announcement.status;
    form.start_date = announcement.start_date;
    form.end_date = announcement.end_date;
    showModal.value = true;
};

const toggleAnnouncementStatus = (announcement) => {
    const newStatus = announcement.status === "active" ? "inactive" : "active";

    Swal.fire({
        title: `${
            newStatus === "active" ? "Activate" : "Deactivate"
        } Announcement`,
        text: `Are you sure you want to ${
            newStatus === "active" ? "activate" : "deactivate"
        } "${announcement.title}"?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: `Yes, ${
            newStatus === "active" ? "activate" : "deactivate"
        }`,
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            form.put(
                route("announcements.update", announcement.id),
                {
                    status: newStatus,
                },
                {
                    preserveState: true,
                    onSuccess: () => {
                        Swal.fire({
                            icon: newStatus === "active" ? "success" : "info",
                            title:
                                newStatus === "active"
                                    ? "Announcement Activated"
                                    : "Announcement Deactivated",
                            text: `Announcement "${
                                announcement.title
                            }" has been ${
                                newStatus === "active"
                                    ? "activated"
                                    : "deactivated"
                            }.`,
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 2000,
                        });
                        fetchAnnouncements();
                    },
                    onError: (errors) => {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: `Failed to update status. ${
                                errors.message ||
                                "Please check the console for details."
                            }`,
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 2000,
                        });
                        console.error("Update error:", errors);
                    },
                }
            );
        }
    });
};

const createAnnouncement = () => {
    form.post(route("announcements.store"), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
            Swal.fire({
                position: "top-end",
                icon: "success",
                title: "Announcement Created!",
                text: "Your announcement has been successfully created.",
                showConfirmButton: false,
                timer: 2000,
                toast: true,
            });
            fetchAnnouncements();
        },
        onError: () => {
            Swal.fire({
                position: "top-end",
                icon: "error",
                title: "Creation Failed",
                text: "There was an error creating the announcement.",
                showConfirmButton: false,
                timer: 2000,
                toast: true,
            });
        },
    });
};

const updateAnnouncement = () => {
    form.put(route("announcements.update", currentId.value), {
        onSuccess: () => {
            showModal.value = false;
            editing.value = false;
            form.reset();
            currentId.value = null;
            Swal.fire({
                position: "top-end",
                icon: "success",
                title: "Update Successful!",
                text: "The announcement has been updated.",
                showConfirmButton: false,
                timer: 2000,
                toast: true,
            });
            fetchAnnouncements();
        },
        onError: () => {
            Swal.fire({
                position: "top-end",
                icon: "error",
                title: "Update Failed",
                text: "There was an error updating the announcement.",
                showConfirmButton: false,
                timer: 2000,
                toast: true,
            });
        },
    });
};

const deleteAnnouncement = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "This announcement will be permanently deleted!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route("announcements.destroy", id), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The announcement has been deleted.",
                        "success"
                    );
                    fetchAnnouncements();
                },
                onError: () => {
                    Swal.fire("Error!", "Something went wrong.", "error");
                },
            });
        }
    });
};
</script>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}

tr {
    transition: opacity 0.2s ease;
}

.line-clamp-1 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
}

.animate-spin {
    animation: spin 1.5s linear;
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

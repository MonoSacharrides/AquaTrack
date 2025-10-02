<template>
    <AdminLayout>
        <div class="mx-auto w-full">
            <div class="lg:items-center lg:flex mb-4 hidden">
                <v-icon
                    name="fa-users"
                    class="mr-2 text-purple-500"
                    scale="1.5"
                />
                <h1 class="text-2xl">Users Management</h1>
            </div>

            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6"
            >
                <!-- Total Users Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400"
                        >
                            <v-icon name="hi-users" class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                Total Users
                            </p>
                            <h3
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ users.total || 0 }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div
                            class="flex items-center text-sm text-green-600 dark:text-green-400"
                        >
                            <v-icon
                                name="hi-trending-up"
                                class="w-4 h-4 mr-1"
                            />
                            <span>All registered users</span>
                        </div>
                    </div>
                </div>

                <!-- Active Users Card -->
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
                                Active Users
                            </p>
                            <h3
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ activeUsersCount }}
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

                <!-- Admin Users Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400"
                        >
                            <v-icon name="hi-shield-check" class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                Admin Users
                            </p>
                            <h3
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ adminUsersCount }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div
                            class="flex items-center text-sm text-gray-600 dark:text-gray-400"
                        >
                            <v-icon
                                name="hi-lock-closed"
                                class="w-4 h-4 mr-1"
                            />
                            <span>System administrators</span>
                        </div>
                    </div>
                </div>

                <!-- Staff Users Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400"
                        >
                            <v-icon name="hi-user-circle" class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                Staff Members
                            </p>
                            <h3
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ staffUsersCount }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div
                            class="flex items-center text-sm text-gray-600 dark:text-gray-400"
                        >
                            <v-icon name="hi-briefcase" class="w-4 h-4 mr-1" />
                            <span>Support team members</span>
                        </div>
                    </div>
                </div>
            </div>

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
                                    placeholder="Name, email, phone..."
                                    @keyup.enter="fetchUsers"
                                />
                            </div>
                        </form>
                    </div>
                    <div
                        class="flex flex-col items-stretch justify-end flex-shrink-0 w-full space-y-2 md:w-auto md:flex-row md:space-y-0 md:items-center md:space-x-3"
                    >
                        <button
                            @click="showCreateModal = true"
                            type="button"
                            class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                        >
                            <v-icon
                                name="hi-solid-plus"
                                class="h-3.5 w-3.5 mr-2"
                            />
                            Register New User
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
                                        Role
                                    </h6>
                                    <ul class="space-y-2 text-sm">
                                        <li class="flex items-center">
                                            <input
                                                id="role-all"
                                                type="radio"
                                                name="role"
                                                value=""
                                                :checked="!filters.role"
                                                @change="updateRoleFilter('')"
                                                class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                            />
                                            <label
                                                for="role-all"
                                                class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                                >All Roles</label
                                            >
                                        </li>
                                        <li class="flex items-center">
                                            <input
                                                id="role-customer"
                                                type="radio"
                                                name="role"
                                                value="customer"
                                                :checked="
                                                    filters.role === 'customer'
                                                "
                                                @change="
                                                    updateRoleFilter('customer')
                                                "
                                                class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                            />
                                            <label
                                                for="role-customer"
                                                class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                                >Customer</label
                                            >
                                        </li>
                                        <li class="flex items-center">
                                            <input
                                                id="role-staff"
                                                type="radio"
                                                name="role"
                                                value="staff"
                                                :checked="
                                                    filters.role === 'staff'
                                                "
                                                @change="
                                                    updateRoleFilter('staff')
                                                "
                                                class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                            />
                                            <label
                                                for="role-staff"
                                                class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                                >Staff</label
                                            >
                                        </li>
                                        <li class="flex items-center">
                                            <input
                                                id="role-admin"
                                                type="radio"
                                                name="role"
                                                value="admin"
                                                :checked="
                                                    filters.role === 'admin'
                                                "
                                                @change="
                                                    updateRoleFilter('admin')
                                                "
                                                class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                            />
                                            <label
                                                for="role-admin"
                                                class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                                >Admin</label
                                            >
                                        </li>
                                    </ul>
                                    <h6
                                        class="mb-3 mt-4 text-sm font-medium text-gray-900 dark:text-white"
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
                                                :checked="!filters.enabled"
                                                @change="
                                                    updateEnabledFilter('')
                                                "
                                                class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-green-600 focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
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
                                                value="1"
                                                :checked="
                                                    filters.enabled === '1'
                                                "
                                                @change="
                                                    updateEnabledFilter('1')
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
                                                value="0"
                                                :checked="
                                                    filters.enabled === '0'
                                                "
                                                @change="
                                                    updateEnabledFilter('0')
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
                            <th scope="col" class="px-6 py-3">Avatar</th>
                            <th
                                scope="col"
                                class="px-6 py-3 cursor-pointer"
                                @click="sortBy('name')"
                            >
                                <div class="flex items-center">
                                    Name
                                    <v-icon
                                        v-if="filters.sort === 'name'"
                                        :name="
                                            filters.order === 'asc'
                                                ? 'hi-sort-ascending'
                                                : 'hi-sort-descending'
                                        "
                                        class="w-4 h-4 ml-1"
                                    />
                                </div>
                            </th>
                            <!-- <th scope="col" class="px-6 py-3">Email</th> -->
                            <th scope="col" class="px-6 py-3">Serial No.</th>
                            <th scope="col" class="px-6 py-3">Role</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="user in users.data"
                            :key="user.id"
                            class="border-b dark:border-gray-700 border-gray-200"
                            :class="
                                user.enabled
                                    ? 'bg-white dark:bg-gray-900'
                                    : 'bg-gray-50 dark:bg-gray-800 opacity-70'
                            "
                        >
                            <th
                                scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                            >
                                {{ user.id }}
                            </th>
                            <td class="px-6 py-4">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img
                                        v-if="user.avatar_url"
                                        :src="user.avatar_url"
                                        :alt="user.name"
                                        class="h-10 w-10 rounded-full object-cover"
                                    />
                                    <div
                                        v-else
                                        class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center"
                                    >
                                        <span
                                            class="text-gray-500 text-sm font-semibold"
                                            >{{ userInitials(user.name) }}</span
                                        >
                                    </div>
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 font-medium text-gray-900 dark:text-white"
                            >
                                {{ user.name }}
                            </td>
                            <!-- <td class="px-6 py-4">{{ user.email || "N/A" }}</td> -->
                            <td class="px-6 py-4">
                                {{ user.serial_number || "N/A" }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    :class="roleClasses(user.role)"
                                    class="inline-flex items-center"
                                >
                                    <v-icon
                                        :name="roleIcon(user.role)"
                                        class="w-3 h-3 mr-1"
                                    />
                                    {{ capitalizeRole(user.role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    :class="statusClasses(user.enabled)"
                                    class="inline-flex items-center text-xs px-2 py-1 rounded-full"
                                >
                                    <v-icon
                                        :name="
                                            user.enabled
                                                ? 'hi-check-circle'
                                                : 'hi-x-circle'
                                        "
                                        class="w-3 h-3 mr-1"
                                    />
                                    {{ user.enabled ? "Active" : "Inactive" }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-3">
                                    <button
                                        @click="viewUser(user)"
                                        class="text-blue-600 hover:text-blue-800 transition-colors"
                                        title="View"
                                    >
                                        <v-icon name="hi-eye" class="w-5 h-5" />
                                    </button>

                                    <button
                                        @click="editUser(user)"
                                        class="text-yellow-500 hover:text-yellow-700 transition-colors"
                                        title="Edit"
                                    >
                                        <v-icon
                                            name="hi-pencil"
                                            class="w-5 h-5"
                                        />
                                    </button>
                                    <button
                                        @click="toggleUserStatus(user)"
                                        :class="
                                            user.enabled
                                                ? 'text-orange-500 hover:text-orange-700'
                                                : 'text-green-500 hover:text-green-700'
                                        "
                                        class="transition-colors"
                                        :title="
                                            user.enabled
                                                ? 'Deactivate'
                                                : 'Activate'
                                        "
                                    >
                                        <v-icon
                                            :name="
                                                user.enabled
                                                    ? 'hi-lock-closed'
                                                    : 'hi-lock-open'
                                            "
                                            class="w-5 h-5"
                                        />
                                    </button>
                                    <button
                                        @click="confirmDelete(user)"
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
                        <tr v-if="users.data.length === 0">
                            <td
                                colspan="8"
                                class="px-6 py-8 text-center text-sm text-gray-500"
                            >
                                <div
                                    class="flex flex-col items-center justify-center space-y-2"
                                >
                                    <v-icon
                                        name="hi-user-group"
                                        class="w-10 h-10 text-gray-300 mb-2"
                                    />
                                    <span class="font-semibold text-gray-400"
                                        >No users found</span
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

            <Pagination
                :items="users"
                item-name="users"
                previous-text="Previous"
                next-text="Next"
            />

            <CreateUserModal
                :show="showCreateModal"
                :zones="zones"
                @close="showCreateModal = false"
                @submit="submitCreateUser"
            />

            <UserDetailsModal
                :show="showUserModal"
                :user="selectedUser"
                @close="showUserModal = false"
            />

            <EditUserDetailsModal
                :show="showEditModal"
                :user="selectedUser"
                :zones="zones"
                @close="showEditModal = false"
                @saved="handleUserUpdated"
            />
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, watch, onMounted, onUnmounted } from "vue";
import { router } from "@inertiajs/vue3";
import { pickBy, debounce } from "lodash";
import Swal from "sweetalert2";
import CreateUserModal from "@/Components/Admin/Modals/CreateUserModal.vue";
import UserDetailsModal from "@/Components/Modals/UserDetailsModal.vue";
import EditUserDetailsModal from "@/Components/Modals/EditUserDetailsModal.vue";

const props = defineProps({
    users: Object,
    filters: Object,
    zones: Object,
});

const filters = ref({
    search: props.filters.search || "",
    role: props.filters.role || "",
    sort: props.filters.sort || "id",
    order: props.filters.order || "desc",
    per_page: props.filters.per_page || 10,
    enabled: props.filters.enabled !== undefined ? props.filters.enabled : "",
    action: props.filters.action || "",
});

const selectedUsers = ref([]);
const showCreateModal = ref(false);
const showFilterDropdown = ref(false);
const isResetting = ref(false);

// Add these refs for the modal
const showUserModal = ref(false);
const showEditModal = ref(false);
const selectedUser = ref(null);

// Define zones as a fallback in case props.zones is not provided
const zones = ref(
    props.zones || {
        "Zone 1": ["Poblacion Sur"],
        "Zone 2": ["Poblacion Centro"],
        "Zone 3": ["Poblacion Centro"],
        "Zone 4": ["Poblacion Norte"],
        "Zone 5": ["Candajec", "Buangan"],
        "Zone 6": ["Bonbon"],
        "Zone 7": ["Bonbon"],
        "Zone 8": ["Nahawan"],
        "Zone 9": ["Caboy", "Villaflor", "Cantuyoc"],
        "Zone 10": ["Bacani", "Mataub", "Comaang", "Tangaran"],
        "Zone 11": ["Cantuyoc", "Nahawan"],
        "Zone 12": ["Lajog", "Buacao"],
    }
);

// Compute user statistics
import { computed } from "vue";

const activeUsersCount = computed(() => {
    return props.users.data.filter((user) => user.enabled).length;
});

const adminUsersCount = computed(() => {
    return props.users.data.filter((user) => user.role === "admin").length;
});

const staffUsersCount = computed(() => {
    return props.users.data.filter((user) => user.role === "staff").length;
});

const activePercentage = computed(() => {
    if (!props.users.total || props.users.total === 0) return 0;
    return Math.round((activeUsersCount.value / props.users.total) * 100);
});

// Handle click outside to close dropdown
const handleClickOutside = (event) => {
    const filterButton = document.querySelector(
        '[aria-label="Filter dropdown button"]'
    );
    const dropdown = document.querySelector('[aria-label="Filter dropdown"]');

    if (
        filterButton &&
        !filterButton.contains(event.target) &&
        dropdown &&
        !dropdown.contains(event.target)
    ) {
        showFilterDropdown.value = false;
    }
};

// Add event listener for click outside
onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});

// Debounced fetch with request tracking
const debouncedFetchUsers = debounce((filters, requestId) => {
    router.get("/admin/users", pickBy(filters), {
        preserveState: true,
        replace: true,
        onSuccess: () => {
            // Only update state if this is the latest request
            if (requestId === latestRequestId) {
                selectedUsers.value = [];
            }
        },
        onError: (errors) => {
            // Only update state if this is the latest request
            if (requestId === latestRequestId) {
                console.error("Fetch error:", errors);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to fetch users. Please try again.",
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

// Watch only relevant filter properties
watch(
    () => ({
        search: filters.value.search,
        role: filters.value.role,
        sort: filters.value.sort,
        order: filters.value.order,
        per_page: filters.value.per_page,
        enabled: filters.value.enabled,
    }),
    (newFilters) => {
        latestRequestId++; // Increment request ID for each new fetch
        debouncedFetchUsers(newFilters, latestRequestId);
    },
    { deep: true }
);

// Watch selectedUsers to reset action filter
watch(selectedUsers, (value) => {
    if (value.length === 0 && filters.value.action) {
        filters.value.action = "";
        latestRequestId++;
        debouncedFetchUsers(filters.value, latestRequestId);
    }
});

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
            role: "",
            sort: "id",
            order: "desc",
            per_page: 10,
            enabled: "",
            action: "",
        };
        selectedUsers.value = [];
        showFilterDropdown.value = false;
        isResetting.value = false;
    }, 1500);
};

const fetchUsers = () => {
    latestRequestId++;
    debouncedFetchUsers(filters.value, latestRequestId);
};

// Update filter checkboxes to be mutually exclusive
const updateRoleFilter = (role) => {
    filters.value.role = filters.value.role === role ? "" : role;
    latestRequestId++;
    debouncedFetchUsers(filters.value, latestRequestId);
};

const updateEnabledFilter = (enabled) => {
    filters.value.enabled = filters.value.enabled === enabled ? "" : enabled;
    latestRequestId++;
    debouncedFetchUsers(filters.value, latestRequestId);
};

const userInitials = (name) => {
    if (!name) return "?";
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .substring(0, 2);
};

const toggleUserStatus = (user) => {
    const newStatus = !user.enabled;

    Swal.fire({
        title: `${newStatus ? "Activate" : "Deactivate"} User`,
        text: `Are you sure you want to ${
            newStatus ? "activate" : "deactivate"
        } ${user.name}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: `Yes, ${newStatus ? "activate" : "deactivate"}`,
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(
                "/admin/users/toggle-status",
                {
                    user_ids: [user.id],
                    enabled: newStatus,
                },
                {
                    preserveState: true,
                    onSuccess: () => {
                        Swal.fire({
                            icon: newStatus ? "success" : "info",
                            title: newStatus
                                ? "User Activated"
                                : "User Deactivated",
                            text: `User ${user.name} has been ${
                                newStatus ? "activated" : "deactivated"
                            }.`,
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 2000,
                        });
                        fetchUsers();
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

const editUser = (user) => {
    selectedUser.value = user;
    showEditModal.value = true;
};

const viewUser = (user) => {
    selectedUser.value = user;
    showUserModal.value = true;
};

// Add function to handle successful user update
const handleUserUpdated = () => {
    // Refresh the users list
    fetchUsers();

    // Show success message
    Swal.fire({
        icon: "success",
        title: "User Updated",
        text: "User information has been updated successfully.",
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
    });
};

const confirmDelete = (user) => {
    Swal.fire({
        title: "Are you sure?",
        text: `You are about to delete ${user.name}. This action cannot be undone.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/admin/users/${user.id}`, {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The user has been deleted.",
                        "success"
                    );
                    fetchUsers();
                },
                onError: () => {
                    Swal.fire("Error!", "Something went wrong.", "error");
                },
            });
        }
    });
};

const submitCreateUser = (userData) => {
    router.post(route("admin.users.store"), userData, {
        preserveScroll: true,
        onSuccess: (response) => {
            showCreateModal.value = false;
            const generatedPassword = response.props.flash?.generated_password;

            if (generatedPassword) {
                Swal.fire({
                    icon: "success",
                    title: "User created successfully!",
                    html: `
                        <div class="text-left flex items-center flex-col justify-center">
                            <p class="mb-2 font-medium">Generated Password:</p>
                            <input
                                id="swal-password"
                                class="swal2-input text-center font-mono text-lg"
                                value="${generatedPassword}"
                                readonly
                                style="width: 70%; padding: 0.5rem;"
                            />
                            <p class="mt-2 text-sm text-gray-500">Please provide this password to the user</p>
                        </div>
                    `,
                    showConfirmButton: true,
                    confirmButtonText: "Copy Password",
                    showCancelButton: true,
                    cancelButtonText: "Close",
                    focusConfirm: false,
                    customClass: {
                        popup: "!text-left",
                        confirmButton: "!bg-blue-600 !hover:bg-blue-700",
                    },
                    didOpen: () => {
                        const confirmBtn = Swal.getConfirmButton();
                        if (confirmBtn) {
                            confirmBtn.addEventListener("click", () => {
                                navigator.clipboard.writeText(
                                    generatedPassword
                                );
                                Swal.fire({
                                    icon: "success",
                                    title: "Copied!",
                                    text: "Password copied to clipboard",
                                    toast: true,
                                    position: "top-end",
                                    showConfirmButton: false,
                                    timer: 2000,
                                });
                            });
                        }
                    },
                });
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "User created",
                    text: "User was created successfully but password could not be displayed",
                    toast: true,
                    position: "top-end",
                    timer: 3000,
                });
            }
            fetchUsers();
        },
        /**
         * Handle create user request error
         * @param {Object} errors - Validation errors
         */
        onError: (errors) => {
            let errorMessage =
                "Failed to create user. Please check the form and try again.";

            if (errors.email) {
                errorMessage = errors.email;
            } else if (errors.name) {
                errorMessage = errors.name;
            } else if (errors.lastname) {
                errorMessage = errors.lastname;
            }

            Swal.fire({
                icon: "error",
                title: "Creation Failed",
                text: errorMessage,
                confirmButtonText: "OK",
                customClass: {
                    confirmButton: "!bg-red-600 !hover:bg-red-700",
                },
            });
        },
    });
};

const roleClasses = (role) => {
    return {
        "px-2 py-1 rounded-full text-xs font-semibold inline-flex items-center": true,
        "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200":
            role === "admin",
        "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200":
            role === "customer",
        "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200":
            role === "staff",
    };
};

const statusClasses = (enabled) => {
    return {
        "px-2 py-1 rounded-full text-xs font-medium": true,
        "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200":
            enabled,
        "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200": !enabled,
    };
};

const roleIcon = (role) => {
    if (role === "admin") return "hi-shield-check";
    if (role === "staff") return "hi-user-circle";
    if (role === "customer") return "hi-users";
    return "hi-user";
};

const capitalizeRole = (role) => {
    if (!role) return "";
    return role.charAt(0).toUpperCase() + role.slice(1);
};
</script>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}

tr {
    transition: opacity 0.2s ease;
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

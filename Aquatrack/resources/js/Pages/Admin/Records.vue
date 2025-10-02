<template>
    <AdminLayout>
        <div class="mx-auto w-full">
            <div class="lg:items-center lg:flex mb-4 hidden">
                <v-icon name="md-peoplealt-outlined" class="mr-2 text-blue-500" scale="1.5" />
                <h1 class="text-2xl">Customer Records Management</h1>
            </div>

            <!-- Records Stats Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Records Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                            <v-icon name="bi-clipboard-data" class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                Total Records
                            </p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ records.total || 0 }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm text-blue-600 dark:text-blue-400">
                            <v-icon name="bi-list-ul" class="w-4 h-4 mr-1" />
                            <span>All customer records</span>
                        </div>
                    </div>
                </div>

                <!-- Paid Records Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                            <v-icon name="bi-check-circle" class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                Paid Records
                            </p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ paidRecordsCount }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm text-green-600 dark:text-green-400">
                            <v-icon name="bi-cash" class="w-4 h-4 mr-1" />
                            <span>{{ paidPercentage }}% of total</span>
                        </div>
                    </div>
                </div>

                <!-- Pending Records Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div
                            class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                            <v-icon name="bi-clock" class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                Pending Records
                            </p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ pendingRecordsCount }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm text-yellow-600 dark:text-yellow-400">
                            <v-icon name="bi-hourglass-split" class="w-4 h-4 mr-1" />
                            <span>{{ pendingPercentage }}% of total</span>
                        </div>
                    </div>
                </div>

                <!-- Overdue Records Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                            <v-icon name="bi-exclamation-octagon" class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                Overdue Records
                            </p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ overdueRecordsCount }}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm text-red-600 dark:text-red-400">
                            <v-icon name="bi-alarm" class="w-4 h-4 mr-1" />
                            <span>{{ overduePercentage }}% of total</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Header Section with Filters -->
            <div class="relative bg-white shadow-md dark:bg-gray-800 sm:rounded-lg mb-6">
                <div
                    class="flex flex-col items-center justify-between p-4 space-y-3 md:flex-row md:space-y-0 md:space-x-4">
                    <div class="w-full md:w-1/2">
                        <form class="flex items-center">
                            <label for="simple-search" class="sr-only">Search</label>
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <v-icon name="hi-solid-search" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                                </div>
                                <input v-model="filters.search" type="text" id="simple-search"
                                    class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Search customers, account numbers..." @keyup.enter="getRecords" />
                            </div>
                        </form>
                    </div>
                    <div
                        class="flex flex-col items-stretch justify-end flex-shrink-0 w-full space-y-2 md:w-auto md:flex-row md:space-y-0 md:items-center md:space-x-3">
                        <div class="flex items-center w-full space-x-3 md:w-auto">
                            <div class="relative">
                                <button @click="
                                    showFilterDropdown = !showFilterDropdown
                                    "
                                    class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg md:w-auto focus:outline-none hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                    type="button" :disabled="loading" aria-label="Status filter dropdown button">
                                    <v-icon name="hi-solid-filter" class="w-4 h-4 mr-2 text-gray-400" />
                                    Status
                                    <v-icon name="hi-chevron-down" class="-mr-1 ml-1.5 w-5 h-5" />
                                </button>

                                <!-- Status Filter Dropdown -->
                                <div v-if="showFilterDropdown"
                                    class="absolute z-10 top-full right-0 mt-1 w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700"
                                    aria-label="Status filter dropdown">
                                    <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">
                                        Status
                                    </h6>
                                    <ul class="space-y-2 text-sm">
                                        <li class="flex items-center">
                                            <input id="status-all" type="radio" name="status" value=""
                                                :checked="!filters.status" @change="updateStatusFilter('')"
                                                class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                                            <label for="status-all"
                                                class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">All
                                                Status</label>
                                        </li>
                                        <li class="flex items-center">
                                            <input id="status-paid" type="radio" name="status" value="Paid" :checked="filters.status === 'Paid'
                                                " @change="
                                                    updateStatusFilter('Paid')
                                                    "
                                                class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-green-600 focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                                            <label for="status-paid"
                                                class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Paid</label>
                                        </li>
                                        <li class="flex items-center">
                                            <input id="status-pending" type="radio" name="status" value="Pending"
                                                :checked="filters.status === 'Pending'
                                                    " @change="
                                                    updateStatusFilter(
                                                        'Pending'
                                                    )
                                                    "
                                                class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-yellow-600 focus:ring-yellow-500 dark:focus:ring-yellow-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                                            <label for="status-pending"
                                                class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Pending</label>
                                        </li>
                                        <li class="flex items-center">
                                            <input id="status-overdue" type="radio" name="status" value="Overdue"
                                                :checked="filters.status === 'Overdue'
                                                    " @change="
                                                    updateStatusFilter(
                                                        'Overdue'
                                                    )
                                                    "
                                                class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-red-600 focus:ring-red-500 dark:focus:ring-red-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                                            <label for="status-overdue"
                                                class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Overdue</label>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Month and Year Filter -->
                            <div class="relative">
                                <button @click="
                                    showDateFilterDropdown =
                                    !showDateFilterDropdown
                                    "
                                    class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg md:w-auto focus:outline-none hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                    type="button" :disabled="loading" aria-label="Date filter dropdown button">
                                    <v-icon name="hi-solid-calendar" class="w-4 h-4 mr-2 text-gray-400" />
                                    Date
                                    <v-icon name="hi-chevron-down" class="-mr-1 ml-1.5 w-5 h-5" />
                                </button>

                                <!-- Date Filter Dropdown -->
                                <div v-if="showDateFilterDropdown"
                                    class="absolute z-10 top-full right-0 mt-1 w-64 p-3 bg-white rounded-lg shadow dark:bg-gray-700"
                                    aria-label="Date filter dropdown">
                                    <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">
                                        Filter by Month and Year
                                    </h6>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label for="filter-month"
                                                class="text-sm font-medium text-gray-900 dark:text-gray-100">Month</label>
                                            <select id="filter-month" v-model="filters.month"
                                                class="mt-1 w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                <option value="">
                                                    All Months
                                                </option>
                                                <option v-for="month in months" :key="month.value" :value="month.value">
                                                    {{ month.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="filter-year"
                                                class="text-sm font-medium text-gray-900 dark:text-gray-100">Year</label>
                                            <select id="filter-year" v-model="filters.year"
                                                class="mt-1 w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                <option value="">
                                                    All Years
                                                </option>
                                                <option v-for="year in years" :key="year" :value="year">
                                                    {{ year }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button @click="resetFilters"
                                class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg md:w-auto focus:outline-none hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                :disabled="loading">
                                <v-icon name="hi-refresh" class="w-4 h-4 mr-1"
                                    :class="{ 'animate-spin': isResetting }" />
                                Reset Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading indicator -->
            <div v-if="loading"
                class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg flex items-center">
                <div class="w-4 h-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin mr-3"></div>
                <span class="text-blue-700 dark:text-blue-300 text-sm">Loading records...</span>
            </div>

            <!-- Table Section -->
            <div class="relative overflow-x-auto mb-4 shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                    :class="{ 'opacity-70': loading }">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Account No.</th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('name')">
                                <div class="flex items-center">
                                    Customer
                                    <SortIndicator :field="'name'" :current-field="sortField"
                                        :direction="sortDirection" />
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">Serial No.</th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('reading')">
                                <div class="flex items-center">
                                    Last Reading
                                    <SortIndicator :field="'reading'" :current-field="sortField"
                                        :direction="sortDirection" />
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('consumption')">
                                <div class="flex items-center">
                                    Consumption
                                    <SortIndicator :field="'consumption'" :current-field="sortField"
                                        :direction="sortDirection" />
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('amount')">
                                <div class="flex items-center">
                                    Amount
                                    <SortIndicator :field="'amount'" :current-field="sortField"
                                        :direction="sortDirection" />
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('due_date')">
                                <div class="flex items-center">
                                    Due Date
                                    <SortIndicator :field="'due_date'" :current-field="sortField"
                                        :direction="sortDirection" />
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('status')">
                                <div class="flex items-center">
                                    Status
                                    <SortIndicator :field="'status'" :current-field="sortField"
                                        :direction="sortDirection" />
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="record in records.data" :key="record.id"
                            class="border-b dark:border-gray-700 border-gray-200"
                            :class="getStatusRowClass(record.status)">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ record.user.account_number }}
                            </th>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ record.user.name }}
                                {{ record.user.lastname }}
                            </td>
                            <td class="px-6 py-4">
                                {{ record.user.serial_number }}
                            </td>
                            <td class="px-6 py-4">{{ record.reading }} m³</td>
                            <td class="px-6 py-4">
                                {{ record.consumption }} m³
                            </td>
                            <td class="px-6 py-4">
                                ₱{{ record.amount }}
                                <span v-if="record.surcharge && record.status !== 'Paid'"
                                    class="text-xs text-red-600 dark:text-red-400">
                                    (+₱{{ record.surcharge }} surcharge)
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                {{ formatDate(record.due_date) }}
                            </td>
                            <td class="px-6 py-4">
                                <span :class="statusClasses(record.status)"
                                    class="inline-flex items-center text-xs px-2 py-1 rounded-full">
                                    <v-icon :name="getStatusIcon(record.status)" class="w-3 h-3 mr-1" />
                                    {{ record.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-3">
                                    <button @click="showRecordDetails(record)"
                                        class="text-blue-600 hover:text-blue-800 transition-colors" title="View"
                                        :disabled="loading">
                                        <v-icon name="hi-eye" class="w-5 h-5" />
                                    </button>
                                    <button @click="showEditModal(record)"
                                        class="text-yellow-500 hover:text-yellow-700 transition-colors" title="Edit"
                                        :disabled="loading">
                                        <v-icon name="hi-pencil" class="w-5 h-5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="records.data.length === 0 && !loading">
                            <td colspan="9" class="px-6 py-8 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <v-icon name="bi-clipboard-data" class="w-10 h-10 text-gray-300 mb-2" />
                                    <span class="font-semibold text-gray-400">No records found</span>
                                    <span class="text-xs text-gray-300">Try adjusting your filters or search
                                        keywords.</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="loading && records.data.length > 0">
                            <td colspan="9" class="px-6 py-4 text-center">
                                <div class="flex justify-center items-center py-2">
                                    <div
                                        class="w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin mr-2">
                                    </div>
                                    <span class="text-sm text-gray-500">Updating records...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <RecordDetailsModal :show="showRecordModal" :record="selectedRecord" :loading="loadingRecord"
                @close="closeRecordModal" />

            <RecordEditModal :show="showEditRecordModal" :record="selectedRecord" :loading="loadingRecord"
                @close="closeEditModal" @saved="handleRecordUpdated" />

            <Pagination v-if="records.data && records.data.length > 0" :items="records" item-name="records"
                class="mt-6" />
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import SortIndicator from "@/Components/SortIndicator.vue";
import { router } from "@inertiajs/vue3";
import { ref, watch, onMounted, onUnmounted, computed } from "vue";
import Swal from "sweetalert2";
import RecordDetailsModal from "@/Components/Admin/Modals/RecordDetailsModal.vue";
import RecordEditModal from "@/Components/Admin/Modals/RecordEditModal.vue";

// Props
const props = defineProps({
    records: Object,
    filters: Object,
    sortField: String,
    sortDirection: String,
});

// Reactive data
const loading = ref(false);
const showFilterDropdown = ref(false);
const showDateFilterDropdown = ref(false);
const isResetting = ref(false);
const showRecordModal = ref(false);
const selectedRecord = ref(null);
const loadingRecord = ref(false);
const showEditRecordModal = ref(false);

// Local filters with month and year
const filters = ref({
    search: props.filters.search || "",
    status: props.filters.status || "",
    month: props.filters.month || "",
    year: props.filters.year || "",
    perPage: props.filters.perPage || 10,
});

// Month options
const months = [
    { name: "January", value: "01" },
    { name: "February", value: "02" },
    { name: "March", value: "03" },
    { name: "April", value: "04" },
    { name: "May", value: "05" },
    { name: "June", value: "06" },
    { name: "July", value: "07" },
    { name: "August", value: "08" },
    { name: "September", value: "09" },
    { name: "October", value: "10" },
    { name: "November", value: "11" },
    { name: "December", value: "12" },
];

// Year options (current year and past 5 years)
const currentYear = new Date().getFullYear();
const years = Array.from({ length: 6 }, (_, i) => currentYear - i);

// Function to show edit modal
const showEditModal = (record) => {
    selectedRecord.value = record;
    showEditRecordModal.value = true;
};

// Function to close edit modal
const closeEditModal = () => {
    showEditRecordModal.value = false;
    selectedRecord.value = null;
};

// Handle record update
const handleRecordUpdated = () => {
    getRecords();
    closeEditModal();
};

// Local sorting
const sortField = ref(props.sortField || "id");
const sortDirection = ref(props.sortDirection || "desc");

// Computed properties for statistics
const paidRecordsCount = computed(() => {
    return props.records.data.filter((record) => record.status === "Paid")
        .length;
});

const pendingRecordsCount = computed(() => {
    return props.records.data.filter((record) => record.status === "Pending")
        .length;
});

const overdueRecordsCount = computed(() => {
    return props.records.data.filter((record) => record.status === "Overdue")
        .length;
});

const paidPercentage = computed(() => {
    if (!props.records.total || props.records.total === 0) return 0;
    return Math.round((paidRecordsCount.value / props.records.total) * 100);
});

const pendingPercentage = computed(() => {
    if (!props.records.total || props.records.total === 0) return 0;
    return Math.round((pendingRecordsCount.value / props.records.total) * 100);
});

const overduePercentage = computed(() => {
    if (!props.records.total || props.records.total === 0) return 0;
    return Math.round((overdueRecordsCount.value / props.records.total) * 100);
});

// Function to show record details
const showRecordDetails = async (record) => {
    selectedRecord.value = record;
    loadingRecord.value = true;
    showRecordModal.value = true;

    try {
        const response = await fetch(route("admin.records.details", record.id));
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            selectedRecord.value = await response.json();
        } else {
            throw new Error("Response is not JSON");
        }
    } catch (error) {
        console.error("Error fetching record details:", error);
        selectedRecord.value = record;
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Could not load detailed record information. Showing basic details instead.",
            timer: 3000,
            showConfirmButton: false,
        });
    } finally {
        loadingRecord.value = false;
    }
};

// Function to close the modal
const closeRecordModal = () => {
    showRecordModal.value = false;
    selectedRecord.value = null;
};

// Handle click outside to close dropdowns
const handleClickOutside = (event) => {
    const statusFilterButton = document.querySelector(
        '[aria-label="Status filter dropdown button"]'
    );
    const statusDropdown = document.querySelector(
        '[aria-label="Status filter dropdown"]'
    );
    const dateFilterButton = document.querySelector(
        '[aria-label="Date filter dropdown button"]'
    );
    const dateDropdown = document.querySelector(
        '[aria-label="Date filter dropdown"]'
    );

    if (
        statusFilterButton &&
        !statusFilterButton.contains(event.target) &&
        statusDropdown &&
        !statusDropdown.contains(event.target)
    ) {
        showFilterDropdown.value = false;
    }

    if (
        dateFilterButton &&
        !dateFilterButton.contains(event.target) &&
        dateDropdown &&
        !dateDropdown.contains(event.target)
    ) {
        showDateFilterDropdown.value = false;
    }
};

// Add event listener for click outside
onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});

// Watch for filter changes
watch(
    () => filters.value,
    (newFilters) => {
        getRecords();
    },
    { deep: true }
);

// Format date for display
const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const options = { year: "numeric", month: "short", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

// Status badge classes
const statusClasses = (status) => {
    return {
        "px-2 py-1 rounded-full text-xs font-medium": true,
        "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200":
            status === "Paid",
        "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200":
            status === "Pending",
        "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200":
            status === "Overdue",
    };
};

// Get status icon
const getStatusIcon = (status) => {
    switch (status) {
        case "Paid":
            return "hi-check-circle";
        case "Pending":
            return "hi-clock";
        case "Overdue":
            return "hi-exclamation-circle";
        default:
            return "hi-question-mark-circle";
    }
};

// Get row background class based on status
const getStatusRowClass = (status) => {
    return status === "Paid"
        ? "bg-green-50 dark:bg-gray-900"
        : status === "Pending"
            ? "bg-yellow-50 dark:bg-yellow-900/20"
            : "bg-red-50 dark:bg-red-900/20";
};

// Sort functionality
const sort = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortField.value = field;
        sortDirection.value = "asc";
    }
    getRecords();
};

// Update status filter
const updateStatusFilter = (status) => {
    filters.value.status = filters.value.status === status ? "" : status;
    showFilterDropdown.value = false;
};

// Reset filters with spinning animation
const resetFilters = () => {
    isResetting.value = true;
    setTimeout(() => {
        filters.value = {
            search: "",
            status: "",
            month: "",
            year: "",
            perPage: 10,
        };
        sortField.value = "id";
        sortDirection.value = "desc";
        showFilterDropdown.value = false;
        showDateFilterDropdown.value = false;
        isResetting.value = false;
        getRecords();
    }, 1500);
};

// Get records with current filters and sorting
const getRecords = () => {
    loading.value = true;
    router.get(
        route("admin.records.index"),
        {
            search: filters.value.search,
            status: filters.value.status,
            month: filters.value.month,
            year: filters.value.year,
            perPage: filters.value.perPage,
            sort: sortField.value,
            direction: sortDirection.value,
        },
        {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            },
        }
    );
};

// Update status functionality
const updateStatus = (record) => {
    Swal.fire({
        title: "Update Status",
        input: "select",
        inputOptions: {
            Paid: "Paid",
            Pending: "Pending",
            Overdue: "Overdue",
        },
        inputValue: record.status || "Pending",
        showCancelButton: true,
        confirmButtonText: "Update",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        showLoaderOnConfirm: true,
        preConfirm: (status) => {
            return router.put(
                route("admin.records.update", record.id),
                { status },
                {
                    preserveScroll: true,
                    onError: (errors) => {
                        Swal.showValidationMessage(
                            errors.message ||
                            "Failed to update status. Please try again."
                        );
                    },
                }
            );
        },
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire("Updated!", "The status has been updated.", "success");
            getRecords();
        }
    });
};

// Delete record functionality with SweetAlert
const confirmDelete = (record) => {
    Swal.fire({
        title: "Are you sure?",
        text: `You are about to delete the record for ${record.user.name} ${record.user.lastname}. This action cannot be undone.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
        reverseButtons: true,
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return router.delete(route("admin.records.destroy", record.id), {
                preserveScroll: true,
                onError: (errors) => {
                    Swal.showValidationMessage(
                        errors.message ||
                        "Failed to delete record. Please try again."
                    );
                },
            });
        },
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire(
                "Deleted!",
                "The record has been deleted successfully.",
                "success"
            );
            getRecords();
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

/* Disabled state for buttons */
button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.animate-spin {
    animation: spin 1s linear infinite;
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
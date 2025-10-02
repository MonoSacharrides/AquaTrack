<template>
    <CustomerLayout>
        <div
            class="w-full bg-white shadow-xl rounded-xl overflow-hidden border border-gray-100"
        >
            <!-- Header Section - Redesigned to match announcement style -->
           <div class=" px-6 py-5">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-black">
                            My Water Reports
                        </h1>
                        <p class="text-black-400 mt-1">
                            Track and manage your water quality reports
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats Overview - Redesigned to match announcement style -->
            <div
                class="grid grid-cols-1 md:grid-cols-4 gap-4 p-6 border-b border-gray-200"
            >
                <div
                    class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm"
                >
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-xl mr-4">
                            <v-icon
                                name="bi-file-earmark-text"
                                class="text-blue-600 w-6 h-6"
                            />
                        </div>

                        <div class="ml-4">
                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                Total Reports
                            </p>
                            <h3
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ reports.total || 0 }}
                            </h3>
                        </div>

                        <!-- <div>
                            <p class="text-sm font-medium text-gray-600">Total Reports</p>
                            <p class="text-2xl font-bold text-gray-900">{{ reports.total || 0 }}</p>
                        </div> -->
                    </div>
                </div>
                <div
                    class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm"
                >
                    <div class="flex items-center">
                        <div class="p-3 bg-amber-100 rounded-xl mr-4">
                            <v-icon
                                name="bi-clock-history"
                                class="text-amber-600 w-6 h-6"
                            />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">
                                Pending
                            </p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{
                                    reports.data.filter(
                                        (r) => r.status === "pending"
                                    ).length
                                }}
                            </p>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm"
                >
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-xl mr-4">
                            <v-icon
                                name="bi-arrow-repeat"
                                class="text-purple-600 w-6 h-6"
                            />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">
                                In Progress
                            </p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{
                                    reports.data.filter(
                                        (r) => r.status === "in_progress"
                                    ).length
                                }}
                            </p>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm"
                >
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-xl mr-4">
                            <v-icon
                                name="bi-check-circle"
                                class="text-green-600 w-6 h-6"
                            />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">
                                Resolved
                            </p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{
                                    reports.data.filter(
                                        (r) => r.status === "resolved"
                                    ).length
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Section -->
            <div class="p-6">
                <!-- Filters and Search - Redesigned to match announcement style -->
                <div
                    class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 p-4 bg-gray-50 rounded-xl"
                >
                    <div class="flex-1">
                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                            >
                                <v-icon
                                    name="bi-search"
                                    class="text-gray-400 w-5 h-5"
                                />
                            </div>
                            <input
                                type="text"
                                placeholder="Search reports..."
                                class="pl-10 pr-4 py-2.5 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                    </div>
                    <button
                        @click="showAddModal = true"
                        class="inline-flex items-center justify-center px-5 py-3 bg-blue-600 text-white border border-transparent rounded-xl font-semibold text-sm uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-700 transition-all duration-200 shadow-lg hover:shadow-xl"
                    >
                        <v-icon name="bi-plus-lg" class="mr-2" />
                        Submit New Report
                    </button>
                    <div class="flex items-center gap-3">
                        <select
                            class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option>All Status</option>
                            <option>Pending</option>
                            <option>In Progress</option>
                            <option>Resolved</option>
                        </select>
                        <button
                            class="p-2.5 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors"
                        >
                            <v-icon
                                name="bi-filter"
                                class="text-gray-600 w-5 h-5"
                            />
                        </button>
                    </div>
                </div>

                <!-- Reports Table - Redesigned to match announcement style -->
                <div
                    class="bg-white rounded-xl border border-gray-200 overflow-hidden"
                >
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                    >
                                        ID
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                    >
                                        Tracking Code
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                    >
                                        Location
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                    >
                                        Description
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                    >
                                        Date
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr
                                    v-for="report in reports.data"
                                    :key="report.id"
                                    class="hover:bg-gray-50 transition-colors"
                                >
                                    <td
                                        class="px-6 py-5 whitespace-nowrap text-sm font-semibold text-gray-900"
                                    >
                                        {{ report.id }}
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                        >
                                            {{ report.tracking_code }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-5 whitespace-nowrap text-sm text-gray-700"
                                    >
                                        <div class="flex items-center">
                                            <v-icon
                                                name="bi-geo-alt"
                                                class="text-gray-400 mr-2 w-4 h-4"
                                            />
                                            {{ report.barangay }},
                                            {{ report.municipality }}
                                        </div>
                                    </td>
                                    <td
                                        class="px-6 py-5 text-sm text-gray-700 max-w-xs"
                                    >
                                        <div class="line-clamp-2">
                                            {{ report.description }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <span
                                            :class="
                                                statusClasses(report.status)
                                            "
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                        >
                                            <span
                                                class="w-2 h-2 rounded-full mr-2"
                                                :class="{
                                                    'bg-gray-500':
                                                        report.status ===
                                                        'pending',
                                                    'bg-blue-500':
                                                        report.status ===
                                                        'in_progress',
                                                    'bg-green-500':
                                                        report.status ===
                                                        'resolved',
                                                }"
                                            ></span>
                                            {{ formatStatus(report.status) }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-5 whitespace-nowrap text-sm text-gray-600"
                                    >
                                        {{ formatDate(report.created_at) }}
                                    </td>
                                    <td
                                        class="px-6 py-5 whitespace-nowrap text-sm font-medium"
                                    >
                                        <button
                                            class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                            title="View Details"
                                            @click="openReportDetails(report)"
                                        >
                                            <v-icon
                                                name="bi-eye-fill"
                                                class="w-5 h-5"
                                            />
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="reports.data.length === 0">
                                    <td
                                        colspan="7"
                                        class="px-6 py-12 text-center"
                                    >
                                        <div
                                            class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6"
                                        >
                                            <v-icon
                                                name="bi-file-earmark-excel"
                                                class="text-gray-400 w-10 h-10"
                                            />
                                        </div>
                                        <h3
                                            class="text-lg font-semibold text-gray-900 mb-2"
                                        >
                                            No reports found
                                        </h3>
                                        <p
                                            class="text-gray-500 mb-6 max-w-md mx-auto"
                                        >
                                            You haven't submitted any water
                                            quality reports yet. Click "Submit
                                            New Report" to get started.
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="reports.meta && reports.meta.last_page > 1"
                        class="px-6 py-4 flex items-center justify-between border-t border-gray-200 bg-gray-50"
                    >
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link
                                v-if="reports.links.prev"
                                :href="reports.links.prev"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Previous
                            </Link>
                            <Link
                                v-if="reports.links.next"
                                :href="reports.links.next"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Next
                            </Link>
                        </div>
                        <div
                            class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
                        >
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing
                                    <span class="font-medium">{{
                                        reports.meta.from
                                    }}</span>
                                    to
                                    <span class="font-medium">{{
                                        reports.meta.to
                                    }}</span>
                                    of
                                    <span class="font-medium">{{
                                        reports.meta.total
                                    }}</span>
                                    results
                                </p>
                            </div>
                            <div>
                                <nav
                                    class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                    aria-label="Pagination"
                                >
                                    <Link
                                        v-for="(link, index) in reports.meta
                                            .links"
                                        :key="index"
                                        :href="link.url"
                                        :disabled="!link.url"
                                        :class="[
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                            link.active
                                                ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                            index === 0 ? 'rounded-l-md' : '',
                                            index ===
                                            reports.meta.links.length - 1
                                                ? 'rounded-r-md'
                                                : '',
                                        ]"
                                        v-html="link.label"
                                    />
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Report Modal -->
        <CustomerReportModal
            :isOpen="showAddModal"
            :zones="zones"
            @update:isOpen="showAddModal = $event"
            @update:successData="handleReportAdded"
            @update:showSuccessModal="showSuccessModal = $event"
        />

        <!-- Report Details Modal -->
        <ReportDetailsModal
            :show="showDetailsModal"
            :report="selectedReport"
            @close="showDetailsModal = false"
        />
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import ReportDetailsModal from "@/Components/Modals/ReportDetailsModal.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted } from "vue";
import Swal from "sweetalert2";
import { useToast } from "vue-toastification";
import CustomerReportModal from "@/Components/Customer/CustomerReportModal.vue";

const toast = useToast();
const page = usePage();

const props = defineProps({
    reports: {
        type: Object,
        required: true,
        default: () => ({
            data: [],
            meta: {},
            links: {},
        }),
    },
    zones: {
        type: Object,
        required: true,
        default: () => ({}),
    },
    swal: Object,
});

// Watch for SweetAlert notifications
// In the <script setup> section, update the watch and onMounted:
watch(
    () => page.props.swal,
    (newSwal) => {
        if (newSwal) {
            Swal.fire({
                toast: true, // Make it a toast for consistency
                position: "top-end",
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                icon: newSwal.icon,
                title: newSwal.title,
                text: newSwal.text,
                ...(newSwal.trackingCode && {
                    footer: `Tracking Code: ${newSwal.trackingCode}`,
                }),
                didOpen: (toast) => {
                    toast.addEventListener("mouseenter", Swal.stopTimer);
                    toast.addEventListener("mouseleave", Swal.resumeTimer);
                },
            });
        }
    },
    { immediate: true }
);

onMounted(() => {
    if (page.props.swal) {
        Swal.fire({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            icon: page.props.swal.icon,
            title: page.props.swal.title,
            text: page.props.swal.text,
            ...(page.props.swal.trackingCode && {
                footer: `Tracking Code: ${page.props.swal.trackingCode}`,
            }),
            didOpen: (toast) => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
            },
        });
    }
});

const showAddModal = ref(false);
const showDetailsModal = ref(false);
const showSuccessModal = ref(false);
const selectedReport = ref(null);

const openReportDetails = (report) => {
    selectedReport.value = report;
    showDetailsModal.value = true;
};

const handleReportAdded = (successData) => {
    showAddModal.value = false;
    // Optionally, you can use successData (e.g., trackingCode, dateSubmitted) for further handling
};

const statusClasses = (status) => {
    return {
        "inline-flex items-center px-2 py-1 rounded-full text-xs font-medium": true,
        "bg-blue-100 text-blue-800": status === "in_progress",
        "bg-green-100 text-green-800": status === "resolved",
        "bg-gray-100 text-gray-800": status === "pending",
    };
};

const formatStatus = (status) => {
    const statusMap = {
        pending: "Pending",
        in_progress: "In Progress",
        resolved: "Resolved",
    };
    return statusMap[status] || status;
};

const formatDate = (dateString) => {
    const options = { year: "numeric", month: "short", day: "numeric" };
    return new Date(dateString).toLocaleDateString("en-US", options);
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

table {
    width: 100%;
    table-layout: auto;
}

.w-full {
    width: 100%;
}

.p-6 {
    padding: 1.5rem;
}

/* Improved responsive behavior */
@media (max-width: 767px) {
    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
    }

    /* Hide table on mobile and show cards instead */
    table {
        display: none;
    }

    .mobile-cards {
        display: block;
    }
}

/* Mobile cards view */
.mobile-cards {
    display: none;
}

@media (max-width: 767px) {
    .mobile-cards {
        display: block;
    }

    .mobile-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
}

/* Custom animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hover\:shadow-md {
    transition: box-shadow 0.2s ease;
}

.hover\:shadow-md:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}
</style>

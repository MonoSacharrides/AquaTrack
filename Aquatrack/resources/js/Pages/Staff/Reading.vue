<template>
    <StaffLayout>
        <div class="max-w-4xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-6">
                <div class="flex items-center justify-center gap-3 mb-3">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <v-icon name="bi-droplet" class="text-blue-600 text-xl" />
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">Meter Readings</h1>
                </div>
                <p class="text-gray-600">
                    Search for customers to record their water meter readings
                </p>
            </div>

            <!-- Search Section -->
            <div class="bg-blue-50 rounded-xl p-5 mb-6 border border-blue-100">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <v-icon name="bi-search-heart" class="text-blue-600" />
                    Find Customer
                </h2>

                <div class="flex flex-col sm:flex-row gap-3 items-stretch">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <v-icon name="bi-search" class="text-gray-400" />
                        </div>
                        <input
                            v-model="searchQuery"
                            type="text"
                            class="w-full pl-10 pr-8 py-2.5 border border-gray-300 rounded-lg text-base bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            placeholder="Search by name, account, or serial number..."
                            @input="debouncedSearch"
                        />
                        <button
                            v-if="searchQuery"
                            @click="clearSearch"
                            type="button"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                        >
                            <v-icon name="bi-x-lg" class="text-sm" />
                        </button>
                    </div>
                    <button
                        @click="searchUsers"
                        type="button"
                        class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 shadow-sm min-w-[100px]"
                        :disabled="isSearching"
                        :class="{ 'opacity-50 cursor-not-allowed': isSearching }"
                    >
                        <v-icon
                            v-if="!isSearching"
                            name="bi-search"
                            class="text-sm"
                        />
                        <v-icon
                            v-else
                            name="bi-arrow-repeat"
                            class="text-sm animate-spin"
                        />
                        <span class="text-sm font-medium">
                            {{ isSearching ? "Searching..." : "Search" }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isSearching" class="text-center py-8">
                <div class="flex flex-col items-center justify-center">
                    <div class="p-3 bg-blue-100 rounded-full mb-3">
                        <v-icon
                            name="bi-arrow-repeat"
                            class="text-blue-600 animate-spin"
                        />
                    </div>
                    <p class="text-blue-600 font-medium">Searching customers...</p>
                </div>
            </div>

            <!-- Search Results -->
            <div v-if="searchPerformed && !isSearching" class="space-y-4">
                <div v-if="searchResults.length > 0">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
                        <div class="p-3 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                <v-icon name="bi-people-fill" class="text-green-500 text-sm" />
                                Found {{ searchResults.length }} customer{{ searchResults.length !== 1 ? "s" : "" }}
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            <div
                                v-for="user in searchResults"
                                :key="user.id"
                                @click="openReadingForm(user)"
                                class="p-4 cursor-pointer transition-colors hover:bg-blue-50 group"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start gap-3 flex-1 min-w-0">
                                        <div class="flex-shrink-0">
                                            <img
                                                v-if="user.avatar_url"
                                                :src="user.avatar_url"
                                                :alt="user.name"
                                                class="w-10 h-10 rounded-lg object-cover border border-gray-200"
                                            />
                                            <v-icon
                                                v-else
                                                name="bi-person-circle"
                                                class="text-blue-600 text-2xl"
                                            />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h4 class="font-medium text-gray-800 truncate group-hover:text-blue-700">
                                                {{ user.name }} {{ user.lastname }}
                                            </h4>
                                            <div class="mt-1.5 space-y-1 text-xs text-gray-600">
                                                <div class="flex items-center gap-1.5">
                                                    <v-icon name="bi-tag" class="text-gray-400 text-xs" />
                                                    <span class="truncate">Acc: {{ user.account_number }}</span>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <v-icon name="bi-geo-alt" class="text-gray-400 text-xs" />
                                                    <span class="truncate">{{ user.address }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 ml-2 text-blue-600">
                                        <span class="text-xs font-medium hidden sm:inline">Record</span>
                                        <v-icon name="bi-chevron-right" class="text-xs group-hover:translate-x-0.5 transition-transform" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Results Message -->
                <div
                    v-else
                    class="text-center py-8 bg-gray-50 rounded-xl border border-gray-200"
                >
                    <div class="flex flex-col items-center justify-center">
                        <div class="p-3 bg-gray-100 rounded-full mb-3">
                            <v-icon name="bi-search" class="text-gray-400" />
                        </div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-1">
                            No customers found
                        </h3>
                        <p class="text-xs text-gray-500">
                            Try a different search term
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions/Instructions -->
            <div v-if="!searchPerformed && !isSearching" class="bg-gray-50 rounded-xl p-5 border border-gray-200 mt-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <v-icon name="bi-info-circle" class="text-blue-500" />
                    Quick Tips
                </h3>
                <ul class="text-xs text-gray-600 space-y-1.5">
                    <li class="flex items-start gap-2">
                        <v-icon name="bi-dash" class="text-gray-400 mt-0.5 flex-shrink-0" />
                        <span>Search by customer name, account number, or meter serial number</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <v-icon name="bi-dash" class="text-gray-400 mt-0.5 flex-shrink-0" />
                        <span>Click on a customer to record their meter reading</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <v-icon name="bi-dash" class="text-gray-400 mt-0.5 flex-shrink-0" />
                        <span>Recent readings will be shown in the customer profile</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Meter Reading Modal -->
        <MeterReadingModal
            v-if="showReadingForm"
            :show="showReadingForm"
            :user="selectedUser"
            @close="closeReadingForm"
            @reading-submitted="handleReadingSubmitted"
        />
    </StaffLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { debounce } from "lodash";
import StaffLayout from "@/Layouts/StaffLayout.vue";
import MeterReadingModal from "@/Components/Staff/Modals/MeterReadingModal.vue";

// Search functionality
const searchQuery = ref("");
const searchResults = ref([]);
const isSearching = ref(false);
const searchPerformed = ref(false);

// Meter reading form
const showReadingForm = ref(false);
const selectedUser = ref({});

const searchUsers = async () => {
    if (!searchQuery.value.trim()) {
        searchResults.value = [];
        searchPerformed.value = false;
        return;
    }

    isSearching.value = true;
    searchPerformed.value = true;

    try {
        const response = await axios.get(route("staff.reading.search"), {
            params: { query: searchQuery.value },
        });

        if (response.data && Array.isArray(response.data)) {
            searchResults.value = response.data;
        } else {
            searchResults.value = [];
        }
    } catch (error) {
        console.error("Error searching users:", error);
        searchResults.value = [];
    } finally {
        isSearching.value = false;
    }
};

const debouncedSearch = debounce(searchUsers, 300);

const clearSearch = () => {
    searchQuery.value = "";
    searchResults.value = [];
    searchPerformed.value = false;
};

const openReadingForm = (user) => {
    selectedUser.value = {
        id: user.id,
        name: user.name,
        lastname: user.lastname,
        account_number: user.account_number,
        address: user.address,
        phone: user.phone,
        date_installed: user.date_installed || null,
        brand: user.brand || null,
        serial_number: user.serial_number || null,
        size: user.size || null,
    };
    showReadingForm.value = true;
};

const closeReadingForm = () => {
    showReadingForm.value = false;
    selectedUser.value = {};
};

const handleReadingSubmitted = () => {
    searchUsers();
};

onMounted(() => {
    return () => {
        debouncedSearch.cancel();
    };
});
</script>

<style scoped>
/* Custom styles for better appearance */
.shadow-xs {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

/* Ensure proper text truncation */
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Smooth transitions */
.transition-all {
    transition: all 0.2s ease;
}

/* Hover effects for interactive elements */
.hover\:bg-blue-50:hover {
    background-color: #eff6ff;
}

.group:hover .group-hover\:text-blue-700 {
    color: #1d4ed8;
}

.group:hover .group-hover\:translate-x-0\.5 {
transform: translateX(2px);
}


</style>

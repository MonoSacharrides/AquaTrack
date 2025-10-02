<template>
    <CustomerLayout>
        <div class="w-full bg-white rounded-lg shadow-sm">
            <!-- Header Section -->
            <div class="border-b border-gray-100 p-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-50 rounded-lg">
                        <v-icon name="bi-megaphone-fill" scale="1.5" class="text-blue-600" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Announcements</h1>
                        <p class="text-gray-500 text-sm">Stay updated with the latest news and updates</p>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="p-6">
                <!-- Filter/Search Bar (optional for future use) -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Latest Announcements</h2>
                    <div class="text-sm text-gray-500">{{ announcements.length }} items</div>
                </div>

                <!-- Announcements List -->
                <div class="space-y-4">
                    <!-- Announcement Item -->
                    <div v-for="announcement in announcements" :key="announcement.id"
                         class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow duration-200">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-lg font-semibold text-gray-800">{{ announcement.title }}</h3>
                            <span :class="{
                                'bg-green-100 text-green-800': announcement.status === 'Active',
                                'bg-red-100 text-red-800': announcement.status === 'Inactive',
                                'bg-gray-100 text-gray-800': announcement.status === 'Scheduled'
                            }" class="px-3 py-1 rounded-full text-xs font-medium">
                                {{ announcement.status }}
                            </span>
                        </div>
                        <p class="text-gray-700 mb-4 leading-relaxed">{{ announcement.content }}</p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <div class="flex items-center gap-1">
                                <v-icon name="bi-calendar-event" class="text-gray-400" />
                                <span>From: {{ formatDate(announcement.start_date) }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <v-icon name="bi-calendar-check" class="text-gray-400" />
                                <span>To: {{ formatDate(announcement.end_date) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="announcements.length === 0" class="text-center py-12">
                        <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <v-icon name="bi-megaphone" scale="2.5" class="text-gray-400" />
                        </div>
                        <h3 class="text-lg font-medium text-gray-600 mb-2">No announcements yet</h3>
                        <p class="text-gray-500 max-w-md mx-auto">Check back later for updates and important information.</p>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    announcements: {
        type: Array,
        required: true,
        default: () => []
    }
});

// Function to format dates (you might want to use a library like date-fns)
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';

    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<style scoped>
/* Smooth transitions for hover effects */
.transition-shadow {
    transition: box-shadow 0.2s ease-in-out;
}
</style>

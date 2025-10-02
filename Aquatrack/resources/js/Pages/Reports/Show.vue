<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Header/Navigation.vue';

defineProps({
    report: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <Navbar>

        <Head :title="`Report #${report.id}`" />


        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Report Details Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Location Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Location Information</h3>
                                <div class="space-y-2">
                                    <div class="flex">
                                        <span class="text-gray-600 w-32">Municipality:</span>
                                        <span>{{ report.municipality }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="text-gray-600 w-32">Province:</span>
                                        <span>{{ report.province }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="text-gray-600 w-32">Barangay:</span>
                                        <span>{{ report.barangay }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="text-gray-600 w-32">Purok/Street:</span>
                                        <span>{{ report.purok }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Report Metadata -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Report Information</h3>
                                <div class="space-y-2">
                                    <div class="flex">
                                        <span class="text-gray-600 w-32">Report ID:</span>
                                        <span>#{{ report.id }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="text-gray-600 w-32">Submitted:</span>
                                        <span>{{ new Date(report.created_at).toLocaleString() }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="text-gray-600 w-32">Status:</span>
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Verified
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                            <p class="text-gray-700 whitespace-pre-line">{{ report.description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Photos Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Photos</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <div v-for="(photo, index) in report.photos" :key="index" class="relative group">
                                <img :src="'/storage/' + photo.path" :alt="`Report photo ${index + 1}`"
                                    class="w-full h-64 object-cover rounded-lg border border-gray-200">
                                <div
                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <a :href="'/storage/' + photo.path" target="_blank"
                                        class="text-white bg-blue-600 bg-opacity-80 p-2 rounded-full hover:bg-opacity-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end space-x-3">
                    <Link :href="route('reports.index')"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Back to List
                    </Link>
                    <!-- <Link :href="route('reports.edit', report.id)"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Edit Report
                    </Link> -->
                </div>
            </div>
        </div>
    </Navbar>

</template>

<!-- resources/js/Pages/Reports/Success.vue -->
<script setup>
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import ReportSuccessModal from '@/Components/Modals/ReportSuccessModal.vue';

const props = defineProps({
    trackingCode: String,
    dateSubmitted: String
});

const showModal = ref(true);

const closeModal = () => {
    showModal.value = false;
    window.location.href = '/';
};
</script>

<template>

    <Head title="Report Submitted" />

    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-cover bg-center bg-no-repeat"
        style="background-image: url('/images/AquatrackIMG.jpg')">
        <!-- Dark overlay for better text readability -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>

        <!-- Main content -->
        <div class="relative z-10 w-full max-w-4xl px-4">
            <ReportSuccessModal :show="showModal" :tracking-code="trackingCode" :date-submitted="dateSubmitted"
                @close="closeModal" />
        </div>
    </div>
</template>

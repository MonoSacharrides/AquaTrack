<template>
    <div class="relative w-full min-h-screen overflow-hidden">
        <Navigation />

        <div
            class="fixed inset-0 bg-cover bg-center bg-no-repeat"
            style="background-image: url('/images/AquatrackIMG.jpg')"
        ></div>
        <div
            class="fixed inset-0 bg-gradient-to-br from-[#062F64]/80 to-[#1E4272]/80"
        >
            <div
                class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-gray-900 to-transparent"
            ></div>
        </div>

        <!-- Floating water drop decoration -->
        <div
        class="fixed top-1/4 left-1/4 w-64 h-64 rounded-full bg-blue-500 opacity-10 blur-3xl"
        ></div>
        <div
            class="fixed bottom-1/3 right-1/4 w-96 h-96 rounded-full bg-teal-500 opacity-10 blur-3xl"
        ></div>

        <!-- Main content -->
        <div
            class="relative z-10 flex items-center justify-center min-h-screen w-full"
        >
            <div
                class="flex items-center justify-center flex-col p-4 md:p-8 w-full max-w-3xl"
            >
                <div class="flex items-center justify-center flex-col mb-6">
                    <v-icon
                        name="bi-exclamation-triangle-fill"
                        class="mb-3 text-[#F87171]"
                        scale="3.5"
                    />
                    <h1 class="text-2xl md:text-3xl text-white font-bold">
                        Report Water Issue
                    </h1>
                    <p class="text-gray-300 mt-1 text-center text-sm">
                        Report water emergencies, leaks, or other issues in your
                        area
                    </p>
                </div>

                <ReportForm
                    :zones="zones"
                    :showSuccessModal="showSuccessModal"
                    :successData="successData"
                    :showTrackModal="showTrackModal"
                    :trackingCodeToShow="trackingCodeToShow"
                    @update:showSuccessModal="showSuccessModal = $event"
                    @update:successData="successData = $event"
                    @update:showTrackModal="showTrackModal = $event"
                    @track-report="handleTrackReport"
                />

                <p class="mt-3 text-xs text-gray-300">
                    For immediate emergencies, please call 911 or your local
                    emergency services
                </p>
            </div>
        </div>

        <!-- Teleported Modals -->
        <teleport to="body">
            <GlobalReportSuccessModal
                v-if="showSuccessModal"
                :show="showSuccessModal"
                :trackingCode="successData.trackingCode"
                :dateSubmitted="successData.dateSubmitted"
                @close="showSuccessModal = false"
                @track-report="handleTrackReport"
            />
            <TrackReportModal
                v-if="showTrackModal"
                :show="showTrackModal"
                :initialTrackingCode="trackingCodeToShow"
                @close="showTrackModal = false"
            />
        </teleport>
    </div>
</template>

<script setup>
import { ref } from "vue";
import ReportForm from "@/Components/Reports/ReportForm.vue";
import Navigation from "@/Components/Header/Navigation.vue";
import GlobalReportSuccessModal from "@/Components/Modals/GlobalReportSuccessModal.vue";
import TrackReportModal from "@/Components/Modals/TrackReportModal.vue";

// Success modal state
const showSuccessModal = ref(false);
const showTrackModal = ref(false);
const trackingCodeToShow = ref("");
const successData = ref({
    trackingCode: "",
    dateSubmitted: "",
});

const handleTrackReport = (trackingCode) => {
    showSuccessModal.value = false;
    trackingCodeToShow.value = trackingCode;
    showTrackModal.value = true;
};

// Zones data - restructured to easily find zone by barangay
const zones = {
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
};

// Create a flat list of all barangays
const allBarangays = ref([]);
for (const zone in zones) {
    zones[zone].forEach((barangay) => {
        allBarangays.value.push(barangay);
    });
}
// Remove duplicates and sort alphabetically
allBarangays.value = [...new Set(allBarangays.value)].sort();
</script>

<style scoped>
/* Optional: Add modal-specific styles if needed */
:deep(.modal) {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1000;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
</style>

<script setup>
import { Link } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted, watch } from "vue";
import ReportForm from "@/Components/Reports/ReportForm.vue";
import GlobalReportSuccessModal from "@/Components/Modals/GlobalReportSuccessModal.vue";
import TrackReportModal from "@/Components/Modals/TrackReportModal.vue";
import Navigation from "./Header/Navigation.vue";
import gsap from "gsap";
import { defineProps } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    canLogin: {
        type: Boolean,
        default: true,
    },
    canRegister: {
        type: Boolean,
        default: true,
    },
    trackingCode: {
        type: [String, null],
        default: null,
    },
    dateSubmitted: {
        type: [String, null],
        default: null,
    },
    dashboardStats: {
        type: Object,
        default: () => ({
            active_users: 0,
            resolution_percentage: 0
        })
    }
});

// Use reactive stats that sync with props
const stats = ref({
    active_users: props.dashboardStats?.active_users || 0,
    resolution_percentage: props.dashboardStats?.resolution_percentage || 0
});

let refreshInterval

const showSuccessModal = ref(false);
const showTrackModal = ref(false);
const showReportModal = ref(false);
const trackingInfo = ref(null);

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

// Watch for props changes and update stats
watch(() => props.dashboardStats, (newStats) => {
    if (newStats) {
        stats.value.active_users = newStats.active_users || 0;
        stats.value.resolution_percentage = newStats.resolution_percentage || 0;
    }
}, { immediate: true, deep: true });

// Function to refresh stats
const refreshStats = () => {
    router.reload({
        only: ['dashboardStats'],
        preserveScroll: true,
        preserveState: true
    });
};

onMounted(() => {
    console.log("Received props on mount:", {
        trackingCode: props.trackingCode,
        dateSubmitted: props.dateSubmitted,
        dashboardStats: props.dashboardStats
    });

    // Initialize stats from props
    if (props.dashboardStats) {
        stats.value = {
            active_users: props.dashboardStats.active_users || 0,
            resolution_percentage: props.dashboardStats.resolution_percentage || 0
        };
    }

    if (props.trackingCode && props.dateSubmitted) {
        trackingInfo.value = {
            code: props.trackingCode,
            date: props.dateSubmitted,
        };
        showSuccessModal.value = true;
    } else {
        console.log("No trackingCode or dateSubmitted, modal will not show.");
    }
    console.log("Modal state:", {
        showSuccessModal: showSuccessModal.value,
        trackingInfo: trackingInfo.value,
    });

    // Start refreshing stats every 30 seconds
    refreshInterval = setInterval(() => {
        refreshStats();
    }, 30000);

    const tl = gsap.timeline();

    tl.fromTo(
        ".hero-badge",
        { scale: 0, opacity: 0 },
        { scale: 1, opacity: 1, duration: 0.5, ease: "back.out(1.7)" }
    );
    tl.fromTo(
        ".hero-content",
        { y: 30, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.8, ease: "power2.out" },
        "-=0.2"
    );
    tl.fromTo(
        "h1 .word",
        { y: 50, opacity: 0 },
        {
            y: 0,
            opacity: 1,
            duration: 0.8,
            stagger: 0.1,
            ease: "back.out(1.7)",
        },
        "-=0.4"
    );
    tl.fromTo(
        ".subheadline",
        { y: 20, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.6, ease: "power2.out" },
        "-=0.4"
    );
    tl.fromTo(
        ".feature-card",
        { y: 30, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.5, stagger: 0.1, ease: "power2.out" },
        "-=0.3"
    );
    tl.fromTo(
        ".cta-buttons",
        { y: 20, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.6, ease: "power2.out" },
        "-=0.3"
    );
    tl.fromTo(
        ".hero-image-container",
        { scale: 0.8, opacity: 0 },
        { scale: 1, opacity: 1, duration: 1, ease: "elastic.out(1, 0.5)" },
        "-=0.8"
    );
});

onUnmounted(() => {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});

const handleTrackReport = () => {
    const trackingCode = trackingInfo.value?.code || props.trackingCode || "";
    if (trackingCode) {
        trackingInfo.value = { code: trackingCode, date: props.dateSubmitted };
    }
    showSuccessModal.value = false;
    showTrackModal.value = true;
};

const openReportModal = () => {
    showReportModal.value = true;
    document.body.style.overflow = "hidden";
};

const closeReportModal = () => {
    showReportModal.value = false;
    document.body.style.overflow = "auto";
};

const handleReportSuccess = (data) => {
    console.log("Handling report success:", data);
    showReportModal.value = false;
    document.body.style.overflow = "auto";
    trackingInfo.value = {
        code: data.trackingCode,
        date: data.dateSubmitted,
    };
    showSuccessModal.value = true;
    console.log("Modal state after handleReportSuccess:", {
        showSuccessModal: showSuccessModal.value,
        trackingInfo: trackingInfo.value,
    });

    // Refresh stats after a report is submitted
    setTimeout(() => {
        refreshStats();
    }, 2000);
};
</script>

<template>
    <main id="home"
        class="relative w-full min-h-screen overflow-hidden bg-gradient-to-br from-blue-50 via-white to-cyan-50">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-72 h-72 bg-blue-400/10 rounded-full blur-3xl animate-blob"></div>
            <div
                class="absolute top-40 right-20 w-96 h-96 bg-cyan-400/10 rounded-full blur-3xl animate-blob animation-delay-2000">
            </div>
            <div
                class="absolute -bottom-20 left-1/3 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl animate-blob animation-delay-4000">
            </div>
        </div>

        <Navigation />

        <div
            class="relative px-4 sm:px-8 lg:px-[120px] flex flex-col lg:flex-row gap-12 lg:gap-20 items-center justify-center min-h-[85vh] py-16 lg:py-0">
            <!-- Left Content -->
            <div
                class="hero-content flex flex-col max-w-[700px] order-2 lg:order-1 text-center lg:text-left lg:w-3/5 z-10">
                <!-- Badge -->
                <div
                    class="hero-badge mb-6 inline-flex items-center gap-2 bg-gradient-to-r from-blue-600/10 to-cyan-600/10 backdrop-blur-sm border border-blue-200/50 rounded-full px-4 py-2 w-fit mx-auto lg:mx-0">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-500 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                    </span>
                    <span class="text-sm font-medium text-blue-700">Clarin, Bohol Water District</span>
                </div>

                <!-- Main Heading with Individual Word Animation -->
                <h1 class="text-6xl sm:text-6xl lg:text-7xl mb-6 leading-tight font-bold">
                    <span class="block mb-2">
                        <span
                            class="word inline-block text-transparent bg-clip-text bg-gradient-to-r from-[#062F64] via-[#1E4272] to-[#0D3468]">
                            Clarin
                        </span>
                        <span
                            class="word inline-block text-transparent bg-clip-text bg-gradient-to-r from-[#1E4272] to-[#0D3468] ml-3">
                            Water
                        </span>
                    </span>
                    <span class="block">
                        <span
                            class="word inline-block text-transparent leading-snug bg-clip-text bg-gradient-to-r from-[#090979] via-[#00A8E8] to-[#00D4FF] break-words">
                            Management
                        </span>
                    </span>
                </h1>

                <!-- Subheadline -->
                <p
                    class="subheadline text-gray-600 max-w-[600px] mb-8 text-lg lg:text-xl leading-relaxed mx-auto lg:mx-0">
                    Your complete water service solution. Report issues, track consumption, manage bills, and access
                    detailed analytics built for Clarin's community.
                </p>

                <!-- CTA Buttons -->
                <div class="cta-buttons flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <button @click="openReportModal"
                        class="group relative inline-flex items-center justify-center gap-3 bg-gradient-to-r from-[#062F64] to-[#0D3468] text-white px-8 py-4 rounded-xl font-semibold shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105 overflow-hidden">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-[#0D3468] to-[#062F64] opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                        <v-icon name="bi-droplet" scale="1.2" class="relative z-10" />
                        <span class="relative z-10">Report Issue Now</span>
                    </button>
                </div>

                <!-- Trust Indicators -->
                <div
                    class="mt-10 flex items-center gap-6 text-sm text-gray-600 justify-center lg:justify-start flex-wrap">
                    <div class="flex items-center gap-2">
                        <v-icon name="hi-check-circle" class="text-green-500" scale="1.2" />
                        <span>Secure & Reliable</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <v-icon name="hi-shield-check" class="text-blue-500" scale="1.2" />
                        <span>Privacy Protected</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <v-icon name="hi-clock" class="text-purple-500" scale="1.2" />
                        <span>Quick Response</span>
                    </div>
                </div>
            </div>

            <!-- Right Image -->
            <div
                class="hero-image-container w-full lg:w-2/5 flex items-center justify-center order-1 lg:order-2 relative z-10">
                <div class="relative group">
                    <!-- Decorative Elements -->
                    <div
                        class="absolute -inset-4 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-3xl blur-2xl opacity-20 group-hover:opacity-30 transition-opacity duration-500">
                    </div>
                    <div
                        class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-2xl opacity-50 blur-xl">
                    </div>

                    <!-- Main Image Container -->
                    <div
                        class="relative bg-white rounded-2xl shadow-2xl overflow-hidden transform group-hover:scale-105 transition-transform duration-500">
                        <!-- Image Badge -->
                        <div
                            class="absolute top-4 right-4 z-10 bg-white/90 backdrop-blur-sm px-4 py-2 rounded-full shadow-lg border border-gray-200">
                            <!-- <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-sm font-semibold text-gray-700">Active</span>
                            </div> -->
                        </div>

                        <div class="p-6">
                            <img class="rounded-xl w-full h-auto object-cover shadow-inner"
                                src="/images/AquatrackIMG.jpg" alt="Water District Office" />
                        </div>
                    </div>

                    <!-- Floating Stats Cards - NOW DYNAMIC -->
                    <div
                        class="absolute -bottom-6 -left-6 bg-white rounded-xl shadow-xl p-4 border border-gray-100 animate-float">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center">
                                <v-icon name="hi-users" class="text-white" scale="1.5" />
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800">{{ stats.active_users.toLocaleString() }}+
                                </p>
                                <p class="text-xs text-gray-600">Active Users</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="absolute -top-6 -right-6 bg-white rounded-xl shadow-xl p-4 border border-gray-100 animate-float animation-delay-2000">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                                <v-icon name="hi-check-circle" class="text-white" scale="1.5" />
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800">{{ stats.resolution_percentage }}%</p>
                                <p class="text-xs text-gray-600">Resolved Issues</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Track Report Floating Button - Enhanced -->
        <div class="fixed bottom-8 right-8 z-20 group">
            <button @click="handleTrackReport"
                class="relative flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-[#062F64] to-[#0D3468] text-white shadow-2xl hover:shadow-blue-500/50 transition-all duration-300 hover:scale-110 hover:rotate-12"
                title="Track Report">
                <v-icon name="io-search" scale="1.5" class="animate-pulse" />
                <div
                    class="absolute -right-1 -top-1 w-5 h-5 bg-gradient-to-br from-red-500 to-pink-500 rounded-full border-2 border-white animate-bounce">
                </div>

                <div
                    class="absolute inset-0 rounded-full bg-blue-400 opacity-0 group-hover:opacity-20 blur-xl transition-opacity duration-300">
                </div>

                <div
                    class="absolute right-full mr-4 top-1/2 -translate-y-1/2 whitespace-nowrap px-4 py-2 rounded-xl text-sm bg-gray-900/95 text-white opacity-0 group-hover:opacity-100 transition-all duration-300 backdrop-blur-sm border border-white/10 shadow-xl">
                    Track Your Report
                    <div
                        class="absolute left-full top-1/2 -translate-y-1/2 border-4 border-transparent border-l-gray-900/95">
                    </div>
                </div>
            </button>
        </div>

        <!-- Clean Report Modal -->
        <div v-if="showReportModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-all duration-300"
                @click="closeReportModal"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    class="relative bg-white rounded-2xl shadow-2xl w-full max-w-[1000px] max-h-[90vh] overflow-hidden transform transition-all duration-300 scale-100">
                    <div class="bg-gradient-to-r from-[#062F64] via-[#1E4272] to-[#0D3468] px-6 py-5 sticky top-0 z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                                    <v-icon name="bi-droplet" scale="1.3" />
                                    Report Water Issue
                                </h2>
                                <p class="text-blue-100 text-sm mt-1">
                                    Report emergencies, leaks, or service issues in your area
                                </p>
                            </div>
                            <button @click="closeReportModal"
                                class="p-2 hover:bg-white/20 rounded-full transition-all duration-200 group">
                                <v-icon name="hi-x"
                                    class="h-6 w-6 text-white group-hover:rotate-90 transition-transform duration-300" />
                            </button>
                        </div>
                    </div>

                    <div class="overflow-y-auto max-h-[calc(90vh-100px)]">
                        <ReportForm :zones="zones" @submitted="handleReportSuccess" @cancel="closeReportModal" />
                    </div>
                </div>
            </div>
        </div>

        <GlobalReportSuccessModal v-if="showSuccessModal && trackingInfo" :show="showSuccessModal"
            :tracking-code="trackingInfo.code" :date-submitted="trackingInfo.date" @close="showSuccessModal = false"
            @track-report="handleTrackReport" />

        <TrackReportModal :show="showTrackModal" :initial-tracking-code="trackingInfo?.code || ''"
            @close="showTrackModal = false" />
    </main>
</template>

<style scoped>
@keyframes blob {

    0%,
    100% {
        transform: translate(0, 0) scale(1);
    }

    25% {
        transform: translate(20px, -50px) scale(1.1);
    }

    50% {
        transform: translate(-20px, 20px) scale(0.9);
    }

    75% {
        transform: translate(50px, 50px) scale(1.05);
    }
}

@keyframes float {

    0%,
    100% {
        transform: translateY(0px);
    }

    50% {
        transform: translateY(-20px);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

/* Hide scrollbar for modal */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Responsive Styles */
@media (max-width: 1024px) {
    .hero-content {
        text-align: center;
    }

    h1 {
        font-size: 3.5rem !important;
    }
}

@media (max-width: 640px) {
    h1 {
        font-size: 2.5rem !important;
        line-height: 1.2;
    }

    .subheadline {
        font-size: 1rem !important;
    }

    .hero-badge {
        font-size: 0.75rem;
    }

    .feature-card {
        font-size: 0.75rem;
        padding: 0.5rem 0.75rem;
    }

    .cta-buttons button {
        padding: 0.875rem 1.5rem;
        font-size: 0.875rem;
    }

    /* Hide floating stat cards on mobile */
    .animate-float {
        display: none;
    }
}

@media (max-width: 480px) {
    h1 {
        font-size: 2rem !important;
    }
}

html {
    scroll-behavior: smooth;
}
</style>
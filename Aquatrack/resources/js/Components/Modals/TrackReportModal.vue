//TrackReportModal.vue
<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, watch, nextTick, onMounted, onUnmounted } from "vue";
import axios from "axios";
import QRCode from "qrcode";
import { OhVueIcon, addIcons } from "oh-vue-icons";
import { computed } from "vue";
import html2canvas from "html2canvas";
import jsQR from "jsqr";
import {
    FaSearch,
    IoClose,
    HiStatusOnline,
    BiCalendarDate,
    MdDescriptionOutlined,
    FaMapMarkerAlt,
    FaQrcode,
    BiDownload,
    BiImages,
    BiExclamationTriangle,
    BiPlayCircleFill,
    BiZoomIn,
    FaCamera,
    BiArrowLeft,
    BiWater,
    FaWater,
    BiDroplet,
} from "oh-vue-icons/icons";

addIcons(
    FaSearch,
    IoClose,
    HiStatusOnline,
    BiCalendarDate,
    MdDescriptionOutlined,
    FaMapMarkerAlt,
    FaQrcode,
    BiDownload,
    BiImages,
    BiExclamationTriangle,
    BiPlayCircleFill,
    BiZoomIn,
    FaCamera,
    BiArrowLeft,
    BiWater,
    FaWater,
    BiDroplet
);

const props = defineProps({
    show: { type: Boolean, default: false },
    initialTrackingCode: { type: String, default: "" },
    isDeleted: { type: Boolean, default: false },
    deletionReason: { type: String, default: null },
});

const emit = defineEmits(["close"]);
const reportDetails = ref(null);
const isLoading = ref(false);
const errorMessage = ref(null);
const qrCodeCanvas = ref(null);
const showLoadingDelay = ref(false);
const modalVisible = ref(false);
const modalRef = ref(null);

// QR Scanner state
const showQrScanner = ref(false);
const videoRef = ref(null);
const canvasRef = ref(null);
const stream = ref(null);
const scanning = ref(false);
const scannerError = ref(null);

// Media modal state
const mediaModal = ref({ show: false, src: "", type: "image" });

const form = useForm({ tracking_code: props.initialTrackingCode });

onMounted(() => {
    console.log(
        "Modal mounted, initialTrackingCode:",
        props.initialTrackingCode
    );
    if (props.show) {
        modalVisible.value = true;
        if (props.initialTrackingCode) {
            form.tracking_code = props.initialTrackingCode;
            trackReport();
        }
    }
    if (props.isDeleted && props.deletionReason) {
        errorMessage.value = props.deletionReason;
        reportDetails.value = null;
    }
});

onUnmounted(() => stopCamera());

watch(
    () => props.show,
    (newVal) => {
        console.log("Show prop changed to:", newVal);
        modalVisible.value = newVal;
        if (newVal && props.initialTrackingCode) {
            form.tracking_code = props.initialTrackingCode;
            trackReport();
        } else if (!newVal) resetModal();
    }
);

watch(
    () => props.isDeleted,
    (newVal) => {
        if (newVal && props.deletionReason) {
            errorMessage.value = props.deletionReason;
            reportDetails.value = null;
        }
    }
);

const trackReport = async () => {
    if (!form.tracking_code || !form.tracking_code.trim()) {
        errorMessage.value = "Please enter a valid tracking code.";
        console.log("Invalid tracking code:", form.tracking_code);
        return;
    }

    isLoading.value = true;
    showLoadingDelay.value = true;
    errorMessage.value = null;
    reportDetails.value = null;
    console.log("Tracking report with code:", form.tracking_code);

    try {
        const response = await axios.get(route("reports.find"), {
            params: { tracking_code: form.tracking_code },
        });
        console.log("API Response:", response.data);

        if (response.data.success === false) {
            if (response.data.deleted) {
                errorMessage.value =
                    response.data.reason || "This report has been deleted.";
            } else {
                errorMessage.value =
                    response.data.message || "Report not found.";
            }
        } else if (response.data.success === true && response.data.data) {
            reportDetails.value = response.data.data;
            if (response.data.data.additional_tracking_codes) {
                reportDetails.value.allTrackingCodes = [
                    reportDetails.value.tracking_code,
                    ...JSON.parse(response.data.data.additional_tracking_codes),
                ];
            } else {
                reportDetails.value.allTrackingCodes = [
                    reportDetails.value.tracking_code,
                ];
            }
            console.log("Report details loaded:", reportDetails.value);
        } else {
            errorMessage.value =
                "Unexpected response structure. Response: " +
                JSON.stringify(response.data);
        }
    } catch (error) {
        console.error("Track report error:", error);
        if (error.response) {
            console.log(
                "Response status:",
                error.response.status,
                "Data:",
                error.response.data
            );
            if (error.response.status === 404) {
                errorMessage.value =
                    "Report not found with this tracking code.";
            } else if (error.response.status === 410) {
                errorMessage.value =
                    error.response.data.reason ||
                    "This report has been deleted.";
            } else {
                errorMessage.value = `Search failed. Status: ${
                    error.response.status
                }, Message: ${error.response.data.message || "Unknown error"}`;
            }
        } else {
            errorMessage.value = "Network error. Please check your connection.";
            console.log("Network error details:", error.message);
        }
    } finally {
        isLoading.value = false;
        showLoadingDelay.value = false;
    }
};

// QR Scanner functions
const startQrScanner = async () => {
    try {
        scannerError.value = null;
        showQrScanner.value = true;

        stream.value = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: "environment",
            },
        });

        await nextTick();

        if (videoRef.value) {
            videoRef.value.srcObject = stream.value;
            videoRef.value.play();
            scanning.value = true;
            scanQrCode();
        }
    } catch (error) {
        console.error("Camera access error:", error);
        scannerError.value = "Camera access denied. Please check permissions.";
        showQrScanner.value = false;
    }
};

const stopCamera = () => {
    if (stream.value) {
        stream.value.getTracks().forEach((track) => track.stop());
        stream.value = null;
    }
    scanning.value = false;
};

const closeQrScanner = () => {
    stopCamera();
    showQrScanner.value = false;
    scannerError.value = null;
};

const scanQrCode = () => {
    if (!scanning.value || !videoRef.value || !canvasRef.value) return;

    const video = videoRef.value;
    const canvas = canvasRef.value;
    const context = canvas.getContext("2d");

    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = context.getImageData(
            0,
            0,
            canvas.width,
            canvas.height
        );
        const code = jsQR(imageData.data, imageData.width, imageData.height);

        if (code) {
            form.tracking_code = code.data;
            closeQrScanner();
            nextTick(() => {
                trackReport();
            });
            return;
        }
    }

    if (scanning.value) {
        requestAnimationFrame(scanQrCode);
    }
};

const generateQRCode = () => {
    if (!reportDetails.value?.tracking_code || !qrCodeCanvas.value) return;

    QRCode.toCanvas(
        qrCodeCanvas.value,
        reportDetails.value.tracking_code,
        {
            width: 140,
            margin: 1,
            color: {
                dark: "#000000", // Changed to black
                light: "#ffffff",
            },
        },
        (error) => {
            if (error) console.error("QR Code generation error:", error);
        }
    );
};

const resetModal = () => {
    form.reset();
    reportDetails.value = null;
    errorMessage.value = null;
    mediaModal.value.show = false;
    mediaModal.value.src = "";
    mediaModal.value.type = "image";
};

const resetForm = () => {
    form.reset();
    reportDetails.value = null;
    errorMessage.value = null;
    mediaModal.value.show = false;
    mediaModal.value.src = "";
    mediaModal.value.type = "image";
};

watch(
    reportDetails,
    () => {
        if (reportDetails.value) {
            nextTick(() => {
                generateQRCode();
            });
        }
    },
    { deep: true }
);

const closeModal = () => {
    closeQrScanner();
    resetModal();
    modalVisible.value = false;
    setTimeout(() => {
        emit("close");
    }, 200);
};

const openMediaModal = (src, type) => {
    mediaModal.value = {
        show: true,
        src,
        type,
    };
};

const closeMediaModal = () => {
    mediaModal.value.show = false;
    document.querySelectorAll("video").forEach((video) => {
        video.pause();
    });
};

const getVideoMimeType = (src) => {
    const extension = src.split(".").pop().toLowerCase();
    switch (extension) {
        case "mp4":
            return "video/mp4";
        case "webm":
            return "video/webm";
        case "ogg":
            return "video/ogg";
        default:
            return "video/mp4";
    }
};

const downloadReportAsImage = async () => {
    try {
        if (!modalRef.value || !reportDetails.value) return;

        isLoading.value = true;

        const downloadBtn = modalRef.value.querySelector(".download-btn");
        const resetBtn = modalRef.value.querySelector(".reset-btn");
        const trackBtn = modalRef.value.querySelector(".track-btn");

        if (downloadBtn) downloadBtn.style.visibility = "hidden";
        if (resetBtn) resetBtn.style.visibility = "hidden";
        if (trackBtn) trackBtn.style.visibility = "hidden";

        const canvas = await html2canvas(modalRef.value, {
            scale: 2,
            logging: false,
            useCORS: true,
            scrollY: -window.scrollY,
            windowWidth: modalRef.value.scrollWidth,
            windowHeight: modalRef.value.scrollHeight,
        });

        const link = document.createElement("a");
        link.download = `water-report-${reportDetails.value.tracking_code}.png`;
        link.href = canvas.toDataURL("image/png");
        link.click();

        if (downloadBtn) downloadBtn.style.visibility = "visible";
        if (resetBtn) resetBtn.style.visibility = "visible";
        if (trackBtn) trackBtn.style.visibility = "visible";
    } catch (error) {
        console.error("Error generating report image:", error);
    } finally {
        isLoading.value = false;
    }
};

const formattedStatus = computed(() => {
    if (!reportDetails.value?.status) return "";
    switch (reportDetails.value.status) {
        case "in_progress":
            return "In Progress";
        case "pending":
            return "Pending";
        case "resolved":
            return "Resolved";
        default:
            return (
                reportDetails.value.status.charAt(0).toUpperCase() +
                reportDetails.value.status.slice(1).toLowerCase()
            );
    }
});

const formattedPriority = computed(() => {
    if (!reportDetails.value?.priority) return "Not Specified";
    return (
        reportDetails.value.priority.charAt(0).toUpperCase() +
        reportDetails.value.priority.slice(1)
    );
});

const isVideoFile = (media) => {
    return (
        media.type === "video" ||
        (media.mime_type && media.mime_type.includes("video"))
    );
};
</script>

<template>
    <Transition name="modal-backdrop">
        <div
            v-if="modalVisible"
            class="fixed inset-0 z-[500] flex items-center justify-center p-3"
        >
            <!-- Softer background -->
            <div
                class="fixed inset-0 bg-gray-800/80 transition-all duration-300"
                @click="closeModal"
            ></div>

            <Transition name="modal-content">
                <div
                    v-if="modalVisible"
                    ref="modalRef"
                    class="relative w-full max-w-4xl max-h-[95vh] bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 border border-gray-200"
                    @click.stop
                >
                    <!-- Header with softer gradient -->
                    <div
                        class="bg-gradient-to-r from-blue-950 to-blue-900 px-6 py-4 relative overflow-hidden"
                    >
                        <div class="flex items-center justify-between relative z-10">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="p-2 bg-white/20 rounded-xl border border-white/30 backdrop-blur-sm"
                                >
                                    <v-icon
                                        name="bi-droplet"
                                        class="text-white"
                                        scale="1.1"
                                    />
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-white">
                                        Water Report Tracker
                                    </h3>
                                    <p class="text-blue-100 text-sm">
                                        Track your water issue reports
                                    </p>
                                </div>
                            </div>
                            <button
                                @click="closeModal"
                                class="p-2 hover:bg-white/20 rounded-xl text-white/80 hover:text-white transition-all backdrop-blur-sm"
                            >
                                <v-icon name="io-close" scale="1.2" />
                            </button>
                        </div>
                    </div>

                    <div class="max-h-[calc(95vh-140px)] overflow-y-auto">
                        <div class="p-6 space-y-6">
                            <!-- Search Section -->
                            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <v-icon
                                        name="fa-search"
                                        class="text-blue-500 mr-2"
                                        scale="0.9"
                                    />
                                    Search Report
                                </h4>
                                <form
                                    @submit.prevent="trackReport"
                                    class="space-y-4"
                                >
                                    <div class="space-y-2">
                                        <label
                                            class="block text-sm font-semibold text-gray-700"
                                        >
                                            Tracking Code
                                        </label>
                                        <div class="relative">
                                            <input
                                                v-model="form.tracking_code"
                                                type="text"
                                                required
                                                class="w-full h-12 pl-12 pr-14 text-base bg-gray-50 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:bg-white transition-all placeholder:text-gray-400 font-mono focus:ring-2 focus:ring-blue-500/20"
                                                placeholder="Enter tracking code"
                                            />
                                            <div
                                                class="absolute left-3 top-1/2 -translate-y-1/2 p-1.5 bg-blue-500 rounded-lg"
                                            >
                                                <v-icon
                                                    name="fa-qrcode"
                                                    scale="0.9"
                                                    class="text-white"
                                                />
                                            </div>
                                            <button
                                                type="button"
                                                @click="startQrScanner"
                                                class="absolute right-2 top-1/2 -translate-y-1/2 p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all hover:scale-105 shadow-sm"
                                                title="Scan QR Code"
                                            >
                                                <v-icon
                                                    name="fa-camera"
                                                    scale="0.85"
                                                />
                                            </button>
                                        </div>
                                        <Transition name="error-slide">
                                            <div
                                                v-if="
                                                    errorMessage || props.isDeleted
                                                "
                                                class="flex items-center space-x-2 p-3 bg-red-50 border border-red-200 rounded-lg"
                                            >
                                                <v-icon
                                                    name="io-close"
                                                    class="text-red-500"
                                                    scale="0.8"
                                                />
                                                <span
                                                    class="text-red-700 text-sm font-medium"
                                                >
                                                    {{
                                                        errorMessage ||
                                                        props.deletionReason
                                                    }}
                                                </span>
                                            </div>
                                        </Transition>
                                    </div>
                                    <button
                                        type="submit"
                                        :disabled="
                                            isLoading || !form.tracking_code.trim()
                                        "
                                        class="w-full py-3 bg-gradient-to-r bg-blue-900 hover:from-blue-950 hover:to-blue-900 disabled:from-gray-400 disabled:to-gray-500 text-white font-semibold rounded-xl transition-all hover:shadow-md disabled:shadow-none disabled:cursor-not-allowed flex items-center justify-center space-x-2 shadow-sm"
                                    >
                                        <v-icon name="fa-search" scale="0.8" />
                                        <span class="text-sm">{{
                                            isLoading ? "Searching..." : "Track Report"
                                        }}</span>
                                    </button>
                                </form>
                            </div>

                            <Transition name="loading-bounce">
                                <div
                                    v-if="isLoading || showLoadingDelay"
                                    class="flex flex-col items-center justify-center py-12 space-y-4"
                                >
                                    <div class="relative">
                                        <div
                                            class="w-16 h-16 border-4 border-gray-200 rounded-full"
                                        ></div>
                                        <div
                                            class="absolute inset-0 w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
                                        ></div>
                                    </div>
                                    <div class="text-center">
                                        <h4
                                            class="text-base font-semibold text-gray-700"
                                        >
                                            Searching...
                                        </h4>
                                        <p class="text-sm text-gray-500">
                                            Please wait while we find your report
                                        </p>
                                    </div>
                                </div>
                            </Transition>

                            <Transition name="content-slide">
                                <div
                                    v-if="reportDetails && !props.isDeleted"
                                    class="space-y-6"
                                >
                                    <!-- Success Banner -->
                                    <div
                                        class="bg-gradient-to-r from-green-50 to-blue-50 rounded-xl p-4 border border-green-200 shadow-sm"
                                    >
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <div
                                                class="flex items-center space-x-3"
                                            >
                                                <div
                                                    class="p-2 bg-green-100 rounded-lg"
                                                >
                                                    <v-icon
                                                        name="hi-status-online"
                                                        class="text-green-600"
                                                        scale="1.1"
                                                    />
                                                </div>
                                                <div>
                                                    <h4
                                                        class="text-lg font-bold text-gray-800"
                                                    >
                                                        Report Found
                                                    </h4>
                                                    <p
                                                        class="text-sm text-gray-600"
                                                    >
                                                        Tracking Code:
                                                        <span
                                                            class="font-mono font-semibold text-blue-600"
                                                        >
                                                            {{
                                                                reportDetails.tracking_code
                                                            }}
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                            <span
                                                class="px-3 py-1 rounded-lg text-sm font-semibold shadow-sm"
                                                :class="{
                                                    'bg-green-100 text-green-700 border border-green-200':
                                                        reportDetails.status ===
                                                        'resolved',
                                                    'bg-yellow-100 text-yellow-700 border border-yellow-200':
                                                        reportDetails.status ===
                                                        'pending',
                                                    'bg-blue-100 text-blue-700 border border-blue-200':
                                                        reportDetails.status ===
                                                        'in_progress',
                                                }"
                                            >
                                                {{ formattedStatus }}
                                            </span>
                                        </div>
                                    </div>

                                    <div
                                        class="grid grid-cols-1 lg:grid-cols-4 gap-6"
                                    >
                                        <div class="lg:col-span-3 space-y-4">
                                            <!-- Reporter & Priority Cards -->
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                            >
                                                <div
                                                    class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm"
                                                >
                                                    <div
                                                        class="flex items-center space-x-2 mb-2"
                                                    >
                                                        <v-icon
                                                            name="bi-droplet"
                                                            class="text-blue-500"
                                                            scale="0.8"
                                                        />
                                                        <span
                                                            class="text-xs font-semibold text-gray-500 uppercase"
                                                        >
                                                            Reporter
                                                        </span>
                                                    </div>
                                                    <p
                                                        class="font-semibold text-gray-800"
                                                    >
                                                        {{
                                                            reportDetails.reporter_name
                                                        }}
                                                    </p>
                                                </div>
                                                <div
                                                    class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm"
                                                >
                                                    <div
                                                        class="flex items-center space-x-2 mb-2"
                                                    >
                                                        <v-icon
                                                            name="bi-exclamation-triangle"
                                                            class="text-amber-500"
                                                            scale="0.8"
                                                        />
                                                        <span
                                                            class="text-xs font-semibold text-gray-500 uppercase"
                                                        >
                                                            Priority
                                                        </span>
                                                    </div>
                                                    <span
                                                        class="inline-flex px-2 py-1 rounded-lg text-sm font-semibold shadow-sm"
                                                        :class="{
                                                            'bg-red-100 text-red-700 border border-red-200':
                                                                reportDetails.priority ===
                                                                'high',
                                                            'bg-amber-100 text-amber-700 border border-amber-200':
                                                                reportDetails.priority ===
                                                                'medium',
                                                            'bg-green-100 text-green-700 border border-green-200':
                                                                reportDetails.priority ===
                                                                'low',
                                                        }"
                                                    >
                                                        {{ formattedPriority }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Description Card -->
                                            <div
                                                class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm"
                                            >
                                                <div
                                                    class="flex items-center space-x-2 mb-3"
                                                >
                                                    <v-icon
                                                        name="md-description-outlined"
                                                        class="text-blue-500"
                                                        scale="0.8"
                                                    />
                                                    <span
                                                        class="text-xs font-semibold text-gray-500 uppercase"
                                                    >
                                                        Issue Description
                                                    </span>
                                                </div>
                                                <p
                                                    class="text-gray-700 text-sm leading-relaxed whitespace-pre-line"
                                                >
                                                    {{
                                                        reportDetails.description
                                                    }}
                                                </p>
                                            </div>

                                            <!-- Location Card -->
                                            <div
                                                class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm"
                                            >
                                                <div
                                                    class="flex items-center space-x-2 mb-3"
                                                >
                                                    <v-icon
                                                        name="fa-map-marker-alt"
                                                        class="text-blue-500"
                                                        scale="0.8"
                                                    />
                                                    <span
                                                        class="text-xs font-semibold text-gray-500 uppercase"
                                                    >
                                                        Location Details
                                                    </span>
                                                </div>
                                                <p
                                                    class="text-gray-700 text-sm"
                                                >
                                                    {{ reportDetails.purok }},
                                                    {{
                                                        reportDetails.barangay
                                                    }},
                                                    {{
                                                        reportDetails.municipality
                                                    }},
                                                    {{
                                                        reportDetails.province
                                                    }},
                                                    {{ reportDetails.zone }}
                                                </p>
                                            </div>

                                            <!-- Media Card -->
                                            <div
                                                v-if="
                                                    reportDetails.photos &&
                                                    reportDetails.photos.length
                                                "
                                                class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm"
                                            >
                                                <div
                                                    class="flex items-center space-x-2 mb-3"
                                                >
                                                    <v-icon
                                                        name="bi-images"
                                                        class="text-blue-500"
                                                        scale="0.8"
                                                    />
                                                    <span
                                                        class="text-xs font-semibold text-gray-500 uppercase"
                                                    >
                                                        Media Evidence
                                                    </span>
                                                </div>
                                                <div
                                                    class="grid grid-cols-3 sm:grid-cols-4 gap-3"
                                                >
                                                    <div
                                                        v-for="(
                                                            media, index
                                                        ) in reportDetails.photos"
                                                        :key="index"
                                                        class="relative group aspect-square rounded-lg overflow-hidden border-2 border-gray-200 hover:border-blue-300 transition-all hover:shadow-md"
                                                    >
                                                        <template
                                                            v-if="
                                                                isVideoFile(
                                                                    media
                                                                )
                                                            "
                                                        >
                                                            <div
                                                                class="w-full h-full bg-gray-600 flex items-center justify-center"
                                                            >
                                                                <video
                                                                    class="absolute inset-0 w-full h-full object-cover"
                                                                >
                                                                    <source
                                                                        :src="
                                                                            '/storage/' +
                                                                            media.path
                                                                        "
                                                                        :type="
                                                                            media.mime_type
                                                                        "
                                                                    />
                                                                </video>
                                                                <div
                                                                    class="absolute inset-0 bg-black/40 flex items-center justify-center"
                                                                >
                                                                    <v-icon
                                                                        name="bi-play-circle-fill"
                                                                        class="text-white text-xl"
                                                                    />
                                                                </div>
                                                            </div>
                                                            <button
                                                                @click="
                                                                    openMediaModal(
                                                                        '/storage/' +
                                                                            media.path,
                                                                        'video'
                                                                    )
                                                                "
                                                                class="absolute inset-0"
                                                            ></button>
                                                        </template>
                                                        <template v-else>
                                                            <img
                                                                :src="
                                                                    '/storage/' +
                                                                    media.path
                                                                "
                                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                                                            />
                                                            <div
                                                                class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all flex items-center justify-center"
                                                            >
                                                                <div
                                                                    class="opacity-0 group-hover:opacity-100 transition-opacity"
                                                                >
                                                                    <v-icon
                                                                        name="bi-zoom-in"
                                                                        class="text-white bg-black/50 p-1 rounded-full"
                                                                    />
                                                                </div>
                                                            </div>
                                                            <a
                                                                :href="
                                                                    '/storage/' +
                                                                    media.path
                                                                "
                                                                target="_blank"
                                                                class="absolute inset-0"
                                                            ></a>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- QR Code & Actions Sidebar -->
                                        <div class="space-y-4">
                                            <div
                                                class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-4 text-white shadow-md"
                                            >
                                                <div
                                                    class="flex items-center space-x-2 mb-3"
                                                >
                                                    <v-icon
                                                        name="fa-qrcode"
                                                        class="text-gray-200"
                                                        scale="0.9"
                                                    />
                                                    <span
                                                        class="text-sm font-semibold"
                                                    >
                                                        Quick Access QR
                                                    </span>
                                                </div>
                                                <div
                                                    class="bg-white p-3 rounded-lg mb-3 shadow-sm"
                                                >
                                                    <canvas
                                                        ref="qrCodeCanvas"
                                                        class="w-full max-w-[140px] mx-auto"
                                                    ></canvas>
                                                </div>
                                                <p
                                                    class="text-xs text-gray-300 text-center"
                                                >
                                                    Scan to view this report
                                                </p>
                                            </div>
                                            <button
                                                @click="downloadReportAsImage"
                                                class="download-btn w-full py-2.5 bg-blue-950 text-white font-semibold rounded-xl transition-all hover:shadow-md hover:scale-[1.02] flex items-center justify-center space-x-2 shadow-sm"
                                            >
                                                <v-icon
                                                    name="bi-download"
                                                    scale="0.9"
                                                />
                                                <span class="text-sm"
                                                    >Download Report</span
                                                >
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div
                        class="border-t border-gray-200 px-6 py-3 bg-gray-50/80 backdrop-blur-sm"
                    >
                        <div class="flex justify-between items-center">
                            <button
                                v-if="reportDetails || props.isDeleted"
                                @click="resetForm"
                                class="reset-btn flex items-center space-x-2 px-4 py-2 border-2 border-gray-300 hover:border-blue-300 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-all shadow-sm"
                            >
                                <v-icon name="bi-arrow-left" scale="0.8" />
                                <span class="text-sm">Search Again</span>
                            </button>
                            <button
                                v-else
                                @click="closeModal"
                                class="flex items-center space-x-2 px-4 py-2 border-2 border-gray-300 hover:border-blue-300 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-all shadow-sm"
                            >
                                <v-icon name="io-close" scale="0.8" />
                                <span class="text-sm">Cancel</span>
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>

    <!-- QR Scanner Modal -->
    <Transition name="modal-backdrop">
        <div
            v-if="showQrScanner"
            class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-gray-800/80 backdrop-blur-sm"
        >
            <div
                class="relative w-full max-w-sm bg-white rounded-2xl overflow-hidden shadow-2xl border border-gray-200"
            >
                <div
                    class="bg-gradient-to-r from-blue-950 to-blue-900 px-4 py-3"
                >
                    <div class="flex items-center justify-between">
                        <h3
                            class="text-base font-semibold text-white flex items-center space-x-2"
                        >
                            <v-icon
                                name="fa-camera"
                                class="text-white"
                                scale="0.9"
                            />
                            <span>Scan QR Code</span>
                        </h3>
                        <button
                            @click="closeQrScanner"
                            class="text-white/80 hover:text-white p-1 rounded-lg transition-all"
                        >
                            <v-icon name="io-close" scale="1.1" />
                        </button>
                    </div>
                </div>

                <div class="p-4">
                    <div
                        v-if="scannerError"
                        class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg"
                    >
                        <p
                            class="text-red-600 text-sm flex items-center space-x-2"
                        >
                            <v-icon
                                name="bi-exclamation-triangle"
                                class="text-red-500"
                                scale="0.8"
                            />
                            <span>{{ scannerError }}</span>
                        </p>
                    </div>

                    <div
                        class="relative bg-gray-900 rounded-xl overflow-hidden aspect-square border-2 border-gray-300"
                    >
                        <video
                            ref="videoRef"
                            autoplay
                            playsinline
                            muted
                            class="w-full h-full object-cover"
                        ></video>

                        <div
                            class="absolute inset-0 flex items-center justify-center"
                        >
                            <div
                                class="w-40 h-40 border-2 border-white/30 rounded-xl relative"
                            >
                                <div
                                    class="absolute -top-1 -left-1 w-6 h-6 border-l-3 border-t-3 border-blue-400 rounded-tl-xl"
                                ></div>
                                <div
                                    class="absolute -top-1 -right-1 w-6 h-6 border-r-3 border-t-3 border-blue-400 rounded-tr-xl"
                                ></div>
                                <div
                                    class="absolute -bottom-1 -left-1 w-6 h-6 border-l-3 border-b-3 border-blue-400 rounded-bl-xl"
                                ></div>
                                <div
                                    class="absolute -bottom-1 -right-1 w-6 h-6 border-r-3 border-b-3 border-blue-400 rounded-br-xl"
                                ></div>
                                <div
                                    class="absolute inset-x-0 top-0 h-px bg-blue-400 animate-scan-line"
                                ></div>
                            </div>
                        </div>

                        <canvas ref="canvasRef" class="hidden"></canvas>
                    </div>

                    <p class="mt-3 text-xs text-gray-600 text-center">
                        Position QR code within the frame to scan
                    </p>
                </div>
            </div>
        </div>
    </Transition>

    <!-- Media Modal -->
    <Transition name="modal-backdrop">
        <div
            v-if="mediaModal.show"
            class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-gray-800/90 backdrop-blur-sm"
        >
            <div class="relative w-full max-w-4xl">
                <button
                    @click="closeMediaModal"
                    class="absolute -top-10 right-0 p-2 text-white/80 hover:text-white bg-white/10 hover:bg-white/20 rounded-xl transition-all backdrop-blur-sm"
                >
                    <v-icon name="io-close" scale="1.2" />
                </button>

                <div
                    class="bg-white rounded-xl overflow-hidden shadow-2xl border border-gray-200"
                >
                    <div
                        v-if="mediaModal.type === 'video'"
                        class="aspect-video w-full"
                    >
                        <video
                            controls
                            autoplay
                            class="w-full h-full rounded-xl"
                        >
                            <source
                                :src="mediaModal.src"
                                :type="getVideoMimeType(mediaModal.src)"
                            />
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    <img
                        v-else
                        :src="mediaModal.src"
                        class="max-h-[80vh] w-full object-contain rounded-xl"
                    />
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.modal-backdrop-enter-active,
.modal-backdrop-leave-active {
    transition: all 0.25s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.modal-backdrop-enter-from,
.modal-backdrop-leave-to {
    opacity: 0;
}

.modal-content-enter-active,
.modal-content-leave-active {
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal-content-enter-from,
.modal-content-leave-to {
    opacity: 0;
    transform: translateY(-15px) scale(0.96);
}

.content-slide-enter-active,
.content-slide-leave-active {
    transition: all 0.35s ease-out;
}

.content-slide-enter-from,
.content-slide-leave-to {
    opacity: 0;
    transform: translateY(8px);
}

.loading-bounce-enter-active,
.loading-bounce-leave-active {
    transition: all 0.25s ease;
}

.loading-bounce-enter-from,
.loading-bounce-leave-to {
    opacity: 0;
    transform: scale(0.94);
}

.error-slide-enter-active,
.error-slide-leave-active {
    transition: all 0.25s ease;
}

.error-slide-enter-from,
.error-slide-leave-to {
    opacity: 0;
    transform: translateX(-8px);
}

@keyframes scan-line {
    0% {
        top: 0;
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
    100% {
        top: 100%;
        opacity: 0;
    }
}

.animate-scan-line {
    animation: scan-line 2s linear infinite;
}

.border-l-3 {
    border-left-width: 3px;
}

.border-t-3 {
    border-top-width: 3px;
}

.border-r-3 {
    border-right-width: 3px;
}

.border-b-3 {
    border-bottom-width: 3px;
}

.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}

.group:hover .group-hover\:opacity-100 {
    opacity: 1;
}

.group:hover .group-hover\:opacity-0 {
    opacity: 0;
}
</style>

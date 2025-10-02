<script setup>
import { ref, onMounted, watch, computed, nextTick } from "vue";
import QRCode from "qrcode";

const props = defineProps({
    show: Boolean,
    trackingCode: String,
    dateSubmitted: String,
});

const emit = defineEmits(["close", "track-report"]);
const qrCodeCanvas = ref(null);
const qrError = ref(null);

// Format date properly
const formattedDate = computed(() => {
    if (!props.dateSubmitted) return "";
    const date = new Date(props.dateSubmitted);
    return date.toLocaleString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
});

const downloadQRCode = () => {
    if (!qrCodeCanvas.value) return;

    // Create a new canvas that includes the QR code, tracking code, and date
    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");

    // Set canvas dimensions (QR code + text area)
    canvas.width = 250;
    canvas.height = 320;

    // Fill background
    ctx.fillStyle = "#ffffff";
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // Draw QR code (centered)
    const qrSize = 180;
    const qrX = (canvas.width - qrSize) / 2;
    ctx.drawImage(qrCodeCanvas.value, qrX, 20, qrSize, qrSize);

    // Draw title
    ctx.fillStyle = "#1E3A8A";
    ctx.font = "bold 16px Arial";
    ctx.textAlign = "center";
    ctx.fillText("AquaTrack Water Report", canvas.width / 2, 220);

    // Draw tracking code
    ctx.fillStyle = "#000000";
    ctx.font = "bold 14px Arial";
    ctx.fillText("Tracking Code:", canvas.width / 2, 245);
    ctx.font = "bold 16px Arial";
    ctx.fillText(props.trackingCode, canvas.width / 2, 265);

    // Draw date
    ctx.font = "12px Arial";
    ctx.fillText("Date Submitted:", canvas.width / 2, 285);
    ctx.font = "12px Arial";
    ctx.fillText(formattedDate.value, canvas.width / 2, 300);

    // Draw border
    ctx.strokeStyle = "#E5E7EB";
    ctx.lineWidth = 1;
    ctx.strokeRect(5, 5, canvas.width - 10, canvas.height - 10);

    // Create download link
    const link = document.createElement("a");
    link.download = `aquatrack-report-${props.trackingCode}.png`;
    link.href = canvas.toDataURL("image/png");
    link.click();

    // Close modal after download
    emit("close");
};

const generateQRCode = async () => {
    qrError.value = null;
    try {
        if (!props.trackingCode || !qrCodeCanvas.value) {
            throw new Error("Missing tracking code or canvas element");
        }

        await QRCode.toCanvas(qrCodeCanvas.value, props.trackingCode, {
            width: 180,
            margin: 1,
            color: {
                dark: "#1E3A8A",
                light: "#ffffff",
            },
        });
    } catch (error) {
        console.error("QR Code generation error:", error);
        qrError.value = error.message;
    }
};

// Watch for changes and generate QR code
watch(
    () => props.show,
    async (show) => {
        if (show && props.trackingCode) {
            await nextTick(); // Wait for DOM update
            generateQRCode();
        }
    }
);

watch(
    () => props.trackingCode,
    async (code) => {
        if (props.show && code) {
            await nextTick(); // Wait for DOM update
            generateQRCode();
        }
    }
);

// Initialize when component mounts
onMounted(() => {
    if (props.show && props.trackingCode) {
        nextTick().then(generateQRCode);
    }
});
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <div
            class="flex items-center justify-center min-h-screen p-4 text-center"
        >
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div
                    class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm"
                ></div>
            </div>

            <!-- Modal container -->
            <div
                class="inline-block w-full max-w-xl  bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all relative"
            >
                <!-- Modal content -->
                <div class="bg-white px-6 py-6">
                    <div class="text-center">
                        <!-- Success icon -->
                        <div
                            class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-emerald-900 mb-2"
                        >
                            <svg
                                class="h-6 w-6 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                        </div>

                        <!-- Heading -->
                        <h3 class="text-xl font-bold text-gray-900 mb-1">
                            Report Submitted!
                        </h3>
                        <p class="text-sm text-gray-500 mb-4">
                            Thank you for your contribution
                        </p>

                        <!-- Tracking info -->
                        <div class="bg-blue-50 rounded-lg p-3 mb-4 text-left">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p
                                        class="text-xs font-medium text-blue-800 uppercase tracking-wider"
                                    >
                                        Tracking Code
                                    </p>
                                    <p
                                        class="text-base font-semibold text-blue-900"
                                    >
                                        {{ trackingCode }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-xs font-medium text-blue-800 uppercase tracking-wider"
                                    >
                                        Date
                                    </p>
                                    <p
                                        class="text-base font-semibold text-blue-900"
                                    >
                                        {{ formattedDate }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- QR Code -->
                        <div class="mb-4 flex flex-col items-center">
                            <p class="text-xs text-gray-500 mb-2">
                                Scan to track your report
                            </p>
                            <div
                                class="p-2 bg-white rounded border border-gray-200"
                            >
                                <canvas
                                    ref="qrCodeCanvas"
                                    width="180"
                                    height="180"
                                    class="w-full h-full"
                                ></canvas>
                                <div
                                    v-if="qrError"
                                    class="text-red-500 text-xs mt-2"
                                >
                                    QR Code Error: {{ qrError }}
                                </div>
                            </div>
                            <p class="text-xs text-gray-600 mt-2">
                                {{ trackingCode }}
                            </p>
                        </div>

                        <!-- Important notice -->
                        <div
                            class="bg-amber-50 border-l-4 border-amber-400 p-3 rounded-r mb-5 text-left"
                        >
                            <div class="flex items-start">
                                <svg
                                    class="h-4 w-4 mt-0.5 text-amber-500 flex-shrink-0"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <div class="ml-2">
                                    <p
                                        class="text-xs font-medium text-amber-800"
                                    >
                                        Important
                                    </p>
                                    <p class="text-xs text-amber-700">
                                        Please be reminded that this QR Code is
                                        very important, once you lose it, you
                                        will not be able to continue. Save this
                                        QR code and
                                        <button
                                            @click="
                                                $emit(
                                                    'track-report',
                                                    trackingCode
                                                )
                                            "
                                            class="text-blue-600 hover:underline focus:outline-none"
                                        >
                                            click here
                                        </button>
                                        to track your report.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2">
                            <button
                                @click="downloadQRCode"
                                type="button"
                                class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                <svg
                                    class="-ml-1 mr-2 h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                    />
                                </svg>
                                Download QR
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

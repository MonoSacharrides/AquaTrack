<template>
    <div class="w-full p-6 bg-white">
        <!-- Form Errors Indicator -->
        <div
            v-if="hasErrors"
            class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm"
        >
            <div class="flex items-center mb-2">
                <v-icon name="hi-exclamation-triangle" class="w-5 h-5 mr-2" />
                <span class="font-medium"
                    >Please fix the following errors:</span
                >
            </div>
            <ul class="list-disc list-inside mt-2 space-y-1">
                <li v-for="(error, key) in form.errors" :key="key">
                    {{ error }}
                </li>
            </ul>
        </div>

        <form @submit.prevent="submitReport" class="space-y-6">
            <!-- Location Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Municipality <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.municipality"
                        readonly
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-gray-700 cursor-not-allowed focus:outline-none text-sm font-medium"
                    />
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Province
                    </label>
                    <input
                        type="text"
                        v-model="form.province"
                        readonly
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-gray-700 cursor-not-allowed focus:outline-none text-sm font-medium"
                    />
                </div>
            </div>

            <!-- Reporter Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.reporter_name"
                        required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm bg-white"
                        placeholder="Enter your full name"
                    />
                    <p
                        v-if="form.errors.reporter_name"
                        class="text-xs text-red-500 mt-1"
                    >
                        {{ form.errors.reporter_name }}
                    </p>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Phone Number
                    </label>
                    <input
                        type="tel"
                        v-model="form.reporter_phone"
                        @input="restrictPhoneInput"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm bg-white"
                        pattern="[0-9]{1,11}"
                        maxlength="11"
                        placeholder="Your contact number"
                    />
                    <p
                        v-if="form.errors.reporter_phone"
                        class="text-xs text-red-500 mt-1"
                    >
                        {{ form.errors.reporter_phone }}
                    </p>
                </div>
            </div>

            <!-- Area Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Barangay <span class="text-red-500">*</span>
                    </label>
                    <select
                        v-model="form.barangay"
                        required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm bg-white"
                    >
                        <option value="" disabled selected>
                            Select Barangay
                        </option>
                        <option
                            v-for="barangay in allBarangays"
                            :key="barangay"
                            :value="barangay"
                        >
                            {{ barangay }}
                        </option>
                    </select>
                    <p
                        v-if="form.errors.barangay"
                        class="text-xs text-red-500 mt-1"
                    >
                        {{ form.errors.barangay }}
                    </p>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Zone <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.zone"
                        readonly
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-gray-700 cursor-not-allowed focus:outline-none text-sm font-medium"
                        placeholder="Zone will be auto-filled"
                    />
                    <p
                        v-if="form.errors.zone"
                        class="text-xs text-red-500 mt-1"
                    >
                        {{ form.errors.zone }}
                    </p>
                </div>
            </div>

            <!-- Purok -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                    Purok/Street <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    v-model="form.purok"
                    required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm bg-white"
                    placeholder="Enter purok number or street name"
                />
                <p v-if="form.errors.purok" class="text-xs text-red-500 mt-1">
                    {{ form.errors.purok }}
                </p>
            </div>

            <!-- Water Issue Type -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                    Water Issue Type <span class="text-red-500">*</span>
                </label>
                <select
                    v-model="form.water_issue_type"
                    @change="selectWaterIssue"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm bg-white"
                    required
                >
                    <option disabled value="">Select water issue type</option>
                    <option
                        v-for="issue in waterIssueTypes"
                        :key="issue"
                        :value="issue"
                    >
                        {{ issue }}
                    </option>
                    <option value="others">Others (please specify)</option>
                </select>

                <div v-if="form.water_issue_type === 'others'" class="mt-3">
                    <input
                        type="text"
                        v-model="form.custom_water_issue"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm bg-white"
                        placeholder="Please specify the water issue"
                        maxlength="100"
                        required
                    />
                </div>

                <p
                    v-if="form.errors.water_issue_type"
                    class="text-xs text-red-500 mt-1"
                >
                    {{ form.errors.water_issue_type }}
                </p>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea
                    v-model="form.description"
                    rows="4"
                    required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm bg-white resize-none"
                    placeholder="Describe the water quality issue in detail..."
                ></textarea>
                <p
                    v-if="form.errors.description"
                    class="text-xs text-red-500 mt-1"
                >
                    {{ form.errors.description }}
                </p>
            </div>

            <!-- Enhanced Camera Section -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-semibold text-gray-700">
                        Capture Evidence
                        <span class="text-red-500">*</span>
                    </label>
                    <div
                        class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full"
                    >
                        {{ form.photos.length }}/{{ MAX_TOTAL }} files
                    </div>
                </div>

                <!-- Camera Status Banner -->
                <div
                    v-if="cameraError"
                    class="p-4 bg-red-50 border border-red-200 rounded-xl"
                >
                    <div class="flex items-center text-red-700 text-sm">
                        <v-icon
                            name="bi-exclamation-triangle-fill"
                            class="w-5 h-5 mr-2"
                        />
                        {{ cameraError }}
                    </div>
                    <button
                        type="button"
                        @click="retryCamera"
                        class="mt-2 text-sm text-blue-600 hover:text-blue-800 underline"
                    >
                        Try Again
                    </button>
                </div>

                <!-- Camera Interface -->
                <div
                    class="bg-gray-50 rounded-xl overflow-hidden border border-gray-200"
                >
                    <!-- Camera Not Active State -->
                    <div v-if="!isCameraActive" class="p-8 text-center">
                        <div class="mb-6">
                            <div
                                class="w-20 h-20 mx-auto bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mb-4 shadow-lg"
                            >
                                <v-icon
                                    name="hi-camera"
                                    class="w-10 h-10 text-white"
                                />
                            </div>
                            <h3
                                class="text-gray-900 text-xl font-semibold mb-3"
                            >
                                Camera Required
                            </h3>
                            <p
                                class="text-gray-600 text-sm mb-6 max-w-md mx-auto"
                            >
                                Take photos and videos to document the water
                                quality issue. Visual evidence helps us resolve
                                issues faster.
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="initializeCamera"
                            :disabled="isCameraLoading"
                            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 disabled:from-blue-300 disabled:to-blue-400 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl"
                        >
                            <v-icon
                                v-if="isCameraLoading"
                                name="bi-arrow-repeat"
                                class="animate-spin -ml-1 mr-3 h-5 w-5"
                            />
                            <v-icon
                                v-else
                                name="hi-camera"
                                class="w-5 h-5 mr-3"
                            />
                            {{
                                isCameraLoading
                                    ? "Starting Camera..."
                                    : "Open Camera"
                            }}
                        </button>

                        <div class="mt-6 text-xs text-gray-400 space-y-1">
                            <p>Your browser will ask for camera permission</p>
                            <p>
                                Make sure to allow access to capture photos and
                                videos
                            </p>
                        </div>
                    </div>

                    <!-- Active Camera View -->
                    <div v-else class="relative">
                        <!-- Video Element Container -->
                        <div
                            class="relative bg-black rounded-lg overflow-hidden"
                            style="aspect-ratio: 4/3"
                        >
                            <video
                                ref="videoElement"
                                class="w-full h-full object-cover"
                                autoplay
                                playsinline
                                muted
                            ></video>

                            <!-- Camera Loading Overlay -->
                            <div
                                v-if="!isCameraReady"
                                class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center"
                            >
                                <div class="text-center text-white">
                                    <div
                                        class="inline-block animate-spin w-8 h-8 border-2 border-white border-t-transparent rounded-full mb-3"
                                    ></div>
                                    <p class="text-sm">{{ cameraStatus }}</p>
                                </div>
                            </div>

                            <!-- Camera Ready Indicator -->
                            <div
                                v-if="isCameraReady"
                                class="absolute top-4 left-4"
                            >
                                <div
                                    class="flex items-center bg-green-500 text-white px-3 py-1.5 rounded-full text-xs font-medium shadow-lg"
                                >
                                    <div
                                        class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"
                                    ></div>
                                    LIVE
                                </div>
                            </div>

                            <!-- Recording Indicator -->
                            <div
                                v-if="isRecording"
                                class="absolute top-4 left-1/2 transform -translate-x-1/2"
                            >
                                <div
                                    class="flex items-center bg-red-500 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg"
                                >
                                    <div
                                        class="w-3 h-3 bg-white rounded-full mr-2 animate-pulse"
                                    ></div>
                                    REC {{ formatTime(recordingTime) }}
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Camera Controls -->
                        <div class="bg-white p-6 border-t border-gray-200">
                            <div class="flex items-center justify-center gap-4">
                                <!-- Switch Camera Button -->
                                <button
                                    type="button"
                                    @click="switchCamera"
                                    v-if="
                                        hasMultipleCameras &&
                                        isCameraReady &&
                                        !isRecording
                                    "
                                    class="p-3 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200 text-gray-700 disabled:opacity-50"
                                    :disabled="
                                        !isCameraReady || isSwitchingCamera
                                    "
                                >
                                    <v-icon
                                        v-if="isSwitchingCamera"
                                        name="eo-loading"
                                        class="animate-spin w-6 h-6"
                                    />
                                    <v-icon
                                        v-else
                                        name="bi-arrow-repeat"
                                        class="w-6 h-6"
                                    />
                                </button>

                                <!-- Photo Capture Button -->
                                <button
                                    type="button"
                                    @click="capturePhoto"
                                    :disabled="
                                        !isCameraReady ||
                                        form.photos.filter((file) =>
                                            file.type.startsWith('image')
                                        ).length >= MAX_PHOTOS ||
                                        isCapturing ||
                                        isRecording
                                    "
                                    class="p-4 bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-full transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg"
                                >
                                    <v-icon
                                        name="hi-camera"
                                        class="w-6 h-6 text-white"
                                    />
                                </button>

                                <!-- Video Recording Button -->
                                <button
                                    type="button"
                                    @click="
                                        isRecording
                                            ? stopVideoRecording()
                                            : startVideoRecording()
                                    "
                                    :disabled="
                                        !isCameraReady ||
                                        (form.photos.filter((file) =>
                                            file.type.startsWith('video')
                                        ).length >= MAX_VIDEOS &&
                                            !isRecording)
                                    "
                                    class="p-4 rounded-full transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg"
                                    :class="
                                        isRecording
                                            ? 'bg-gradient-to-br from-red-500 to-red-600 hover:from-red-600 hover:to-red-700'
                                            : 'bg-gradient-to-br from-red-500 to-red-600 hover:from-red-600 hover:to-red-700'
                                    "
                                >
                                    <div
                                        v-if="isRecording"
                                        class="w-6 h-6 bg-white rounded-sm"
                                    ></div>
                                    <v-icon
                                        v-else
                                        name="hi-video-camera"
                                        class="w-6 h-6 text-white"
                                    />
                                </button>

                                <!-- Close Camera Button -->
                                <button
                                    type="button"
                                    @click="stopCamera"
                                    :disabled="isRecording"
                                    class="p-3 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200 text-gray-700 disabled:opacity-50"
                                >
                                    <v-icon name="hi-solid-x" class="w-6 h-6" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Media Gallery -->
                <div v-if="form.photo_previews.length > 0" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-gray-700">
                            Captured Media ({{ form.photo_previews.length }})
                        </h4>
                        <button
                            type="button"
                            @click="clearAllMedia"
                            class="text-sm text-red-600 hover:text-red-800 font-medium"
                        >
                            Clear All
                        </button>
                    </div>

                    <div
                        class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3"
                    >
                        <div
                            v-for="(preview, index) in form.photo_previews"
                            :key="'media-' + index"
                            class="relative group aspect-square"
                        >
                            <!-- Media display logic remains the same -->
                            <div
                                v-if="
                                    form.photos[index].type.startsWith('image')
                                "
                            >
                                <img
                                    :src="preview"
                                    class="w-full h-full object-cover rounded-lg border-2 border-gray-200 group-hover:border-blue-400 transition-all duration-200"
                                    :alt="'Photo ' + (index + 1)"
                                />
                                <div
                                    class="absolute top-2 left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full flex items-center"
                                >
                                    <v-icon
                                        name="hi-photograph"
                                        class="w-3 h-3 mr-1"
                                    />
                                    {{
                                        form.photos
                                            .filter((file) =>
                                                file.type.startsWith("image")
                                            )
                                            .indexOf(form.photos[index]) + 1
                                    }}
                                </div>
                            </div>
                            <!-- Video display logic remains the same -->
                            <div v-else>
                                <video
                                    :src="preview"
                                    class="w-full h-full object-cover rounded-lg border-2 border-gray-200 group-hover:border-green-400 transition-all duration-200"
                                    muted
                                    preload="metadata"
                                ></video>
                                <div
                                    class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full flex items-center"
                                >
                                    <v-icon
                                        name="hi-video-camera"
                                        class="w-3 h-3 mr-1"
                                    />
                                    {{
                                        form.photos
                                            .filter((file) =>
                                                file.type.startsWith("video")
                                            )
                                            .indexOf(form.photos[index]) + 1
                                    }}
                                </div>
                            </div>
                            <button
                                @click="removeMedia(index)"
                                type="button"
                                class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-all duration-200"
                            >
                                <v-icon name="hi-trash" class="w-3 h-3" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Media requirement message -->
                <div
                    v-if="form.photos.length === 0"
                    class="p-4 bg-blue-50 border border-blue-200 rounded-xl"
                >
                    <div class="flex items-center text-blue-700 text-sm">
                        <v-icon
                            name="hi-information-circle"
                            class="w-5 h-5 mr-2"
                        />
                        At least one photo or video is required to submit your
                        report
                    </div>
                </div>

                <p v-if="form.errors.photos" class="text-xs text-red-500 mt-2">
                    {{ form.errors.photos }}
                </p>
            </div>

            <!-- Submit Button -->
            <div class="pt-6 border-t border-gray-200">
                <button
                    type="submit"
                    :disabled="
                        form.processing ||
                        isSubmitting ||
                        isRecording ||
                        !isFormValid
                    "
                    class="w-full py-4 px-6 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed"
                >
                    <span
                        v-if="isSubmitting"
                        class="flex items-center justify-center"
                    >
                        <v-icon
                            name="bi-arrow-repeat"
                            class="animate-spin -ml-1 mr-3 h-5 w-5"
                        />
                        Submitting Report...
                    </span>
                    <span
                        v-else-if="isRecording"
                        class="flex items-center justify-center text-yellow-200"
                    >
                        <div
                            class="w-2 h-2 bg-yellow-300 rounded-full mr-3 animate-pulse"
                        ></div>
                        Stop recording to submit
                    </span>
                    <span v-else class="flex items-center justify-center">
                        <v-icon name="hi-paper-airplane" class="w-5 h-5 mr-2" />
                        Submit Report
                    </span>
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from "vue";
import axios from "axios";
import Swal from "sweetalert2";

const props = defineProps({
    zones: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits([
    "update:showSuccessModal",
    "update:successData",
    "update:showTrackModal",
    "track-report",
    "submitted",
]);

const isSubmitting = ref(false);
const locationStatus = ref("idle");
const isCameraActive = ref(false);
const isCameraReady = ref(false);
const isCameraLoading = ref(false);
const isSwitchingCamera = ref(false);
const isCapturing = ref(false);
const isRecording = ref(false);
const recordingTime = ref(0);
const availableCameras = ref([]);
const currentCameraIndex = ref(0);
const videoElement = ref(null);
const cameraError = ref("");
const cameraStatus = ref("Initializing...");
const loadingProgress = ref(0);

let stream = null;
let progressInterval = null;
let mediaRecorder = null;
let recordedChunks = [];
let recordingInterval = null;
let timeUpdateInterval = null;

const hasMultipleCameras = computed(() => availableCameras.value.length > 1);
const currentCameraName = computed(() => {
    if (availableCameras.value.length === 0) return "";
    const camera = availableCameras.value[currentCameraIndex.value];
    return camera?.label || `Camera ${currentCameraIndex.value + 1}`;
});

const form = useForm({
    municipality: "Clarin",
    province: "Bohol",
    zone: "",
    barangay: "",
    purok: "",
    description: "",
    photos: [],
    photo_previews: [],
    videos: [],
    video_previews: [],
    reporter_name: "",
    reporter_phone: "",
    latitude: null,
    longitude: null,
    water_issue_type: "",
    custom_water_issue: "",
});

const MAX_PHOTOS = 3;
const MAX_VIDEOS = 2;
const MAX_TOTAL = MAX_PHOTOS + MAX_VIDEOS;
const MAX_PHOTO_SIZE = 5 * 1024 * 1024;
const MAX_VIDEO_SIZE = 25 * 1024 * 1024;
const MAX_VIDEO_DURATION = 30;

const currentDate = ref("");
const currentTime = ref("");
const currentLocation = ref("Clarin, Bohol");

const updateTimeDisplay = () => {
    const now = new Date();
    currentDate.value = now.toLocaleDateString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    });
    currentTime.value = now.toLocaleTimeString("en-US", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: true,
    });
};

const waterIssueTypes = ref([
    "Burst pipe",
    "Rusty water",
    "Low water pressure",
    "No water supply",
    "Clogged pipes",
    "Smelly water",
    "Cloudy or dirty water",
    "Hot water issues",
    "Running toilet",
]);

const selectWaterIssue = (event) => {
    form.water_issue_type = event.target.value || "";
    if (form.water_issue_type !== "others") {
        form.custom_water_issue = "";
    }
    console.log("Water issue selected:", form.water_issue_type);
};

const allBarangays = computed(() => {
    return Object.values(props.zones).flat();
});

const barangayToZone = computed(() => {
    const mapping = {};
    Object.entries(props.zones).forEach(([zone, barangays]) => {
        barangays.forEach((barangay) => {
            mapping[barangay] = zone;
        });
    });
    return mapping;
});

const hasErrors = computed(() => {
    return Object.keys(form.errors).length > 0;
});

const isFormValid = computed(() => {
    return (
        form.reporter_name &&
        form.barangay &&
        form.purok &&
        form.description &&
        form.water_issue_type &&
        (form.water_issue_type !== "others" || form.custom_water_issue) &&
        form.photos.length > 0
    );
});

const getNextCameraName = () => {
    if (availableCameras.value.length <= 1) return "";
    const nextIndex =
        (currentCameraIndex.value + 1) % availableCameras.value.length;
    const camera = availableCameras.value[nextIndex];
    return camera?.label || `Camera ${nextIndex + 1}`;
};

const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, "0")}`;
};

const getVideoDuration = (videoFile) => {
    return (
        videoFile.duration || Math.floor((videoFile.size / (1024 * 1024)) * 3)
    );
};

const startLoadingProgress = () => {
    loadingProgress.value = 0;
    progressInterval = setInterval(() => {
        if (loadingProgress.value < 90) {
            loadingProgress.value += Math.random() * 15;
        }
    }, 200);
};

const stopLoadingProgress = () => {
    if (progressInterval) {
        clearInterval(progressInterval);
        progressInterval = null;
    }
    loadingProgress.value = 100;
    setTimeout(() => {
        loadingProgress.value = 0;
    }, 500);
};

const getCameras = async () => {
    try {
        cameraStatus.value = "Detecting cameras...";
        const devices = await navigator.mediaDevices.enumerateDevices();
        availableCameras.value = devices.filter(
            (device) => device.kind === "videoinput"
        );

        if (
            availableCameras.value.length > 0 &&
            !availableCameras.value[0].label
        ) {
            try {
                const tempStream = await navigator.mediaDevices.getUserMedia({
                    video: true,
                });
                tempStream.getTracks().forEach((track) => track.stop());
                const devicesWithLabels =
                    await navigator.mediaDevices.enumerateDevices();
                availableCameras.value = devicesWithLabels.filter(
                    (device) => device.kind === "videoinput"
                );
            } catch (e) {
                console.warn("Could not get camera labels:", e);
            }
        }

        console.log("Available cameras:", availableCameras.value);
        return availableCameras.value;
    } catch (error) {
        console.error("Error getting cameras:", error);
        throw new Error("Could not detect camera devices");
    }
};

const initializeCamera = async (retryCount = 0, maxRetries = 3) => {
    if (retryCount >= maxRetries) {
        cameraError.value =
            "Failed to access camera after multiple attempts. Please close other applications using the camera or restart your browser.";
        return;
    }

    cameraError.value = "";
    isCameraLoading.value = true;
    startLoadingProgress();

    try {
        cameraStatus.value = "Requesting camera permission...";

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error("Camera not supported by this browser");
        }

        await getCameras();

        if (availableCameras.value.length === 0) {
            throw new Error("No camera devices found");
        }

        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
            stream = null;
            await nextTick();
        }

        await startCameraStream();

        updateTimeDisplay();
        timeUpdateInterval = setInterval(updateTimeDisplay, 1000);
    } catch (error) {
        console.error("Camera initialization failed:", error);
        handleCameraError(error, retryCount, maxRetries);
    } finally {
        isCameraLoading.value = false;
        stopLoadingProgress();
    }
};

const startCameraStream = async () => {
    try {
        cameraStatus.value = "Starting camera...";

        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
            stream = null;
        }

        isCameraActive.value = true;
        await nextTick();

        if (!videoElement.value) {
            throw new Error("Video element not available");
        }

        const constraints = {
            video: {
                width: { ideal: 1280, max: 1920 },
                height: { ideal: 720, max: 1080 },
                frameRate: { ideal: 30, max: 60 },
            },
            audio: true,
        };

        if (availableCameras.value.length > 0) {
            if (currentCameraIndex.value < availableCameras.value.length) {
                constraints.video.deviceId = {
                    exact: availableCameras.value[currentCameraIndex.value]
                        .deviceId,
                };
            }
        }

        console.log("Starting camera with constraints:", constraints);
        cameraStatus.value = "Connecting to camera...";

        stream = await navigator.mediaDevices.getUserMedia(constraints);

        cameraStatus.value = "Loading video stream...";
        videoElement.value.srcObject = stream;

        await new Promise((resolve, reject) => {
            const timeout = setTimeout(
                () => reject(new Error("Video load timeout")),
                10000
            );
            const onLoadedMetadata = () => {
                clearTimeout(timeout);
                videoElement.value.removeEventListener(
                    "loadedmetadata",
                    onLoadedMetadata
                );
                videoElement.value.removeEventListener("error", onError);
                resolve();
            };
            const onError = (e) => {
                clearTimeout(timeout);
                videoElement.value.removeEventListener(
                    "loadedmetadata",
                    onLoadedMetadata
                );
                videoElement.value.removeEventListener("error", onError);
                reject(new Error("Video failed to load"));
            };
            videoElement.value.addEventListener(
                "loadedmetadata",
                onLoadedMetadata
            );
            videoElement.value.addEventListener("error", onError);
            const playPromise = videoElement.value.play();
            if (playPromise) playPromise.catch(console.warn);
        });

        isCameraReady.value = true;
        cameraStatus.value = "Camera ready";
        console.log("Camera started successfully");
    } catch (error) {
        console.error("Camera stream error:", error);
        throw error;
    }
};

const handleCameraError = (error, retryCount, maxRetries) => {
    isCameraActive.value = false;
    isCameraReady.value = false;

    if (stream) {
        stream.getTracks().forEach((track) => track.stop());
        stream = null;
    }

    let errorMessage = "Camera initialization failed";

    if (
        error.name === "NotAllowedError" ||
        error.message.includes("permission")
    ) {
        errorMessage =
            "Camera and microphone access denied. Please allow permissions and try again.";
    } else if (
        error.name === "NotFoundError" ||
        error.message.includes("No camera")
    ) {
        errorMessage = "No camera found on your device.";
    } else if (error.name === "NotReadableError") {
        errorMessage =
            "Camera is busy or being used by another application. Please close other apps or tabs using the camera and try again.";
        if (retryCount < maxRetries - 1) {
            setTimeout(
                () => initializeCamera(retryCount + 1, maxRetries),
                2000
            );
            return;
        }
    } else if (error.name === "OverconstrainedError") {
        errorMessage = "Camera settings not supported by your device.";
    } else if (error.message.includes("not supported")) {
        errorMessage = "Camera not supported by this browser.";
    }

    cameraError.value = errorMessage;

    if (error.name === "NotReadableError" && retryCount >= maxRetries - 1) {
        Swal.fire({
            icon: "error",
            title: "Camera Busy",
            text: `${errorMessage} If the issue persists, reset camera permissions in your browser settings (e.g., chrome://settings/content/camera) and refresh the page.`,
            confirmButtonColor: "#3085d6",
        });
    }
};

const retryCamera = () => {
    cameraError.value = "";
    initializeCamera();
};

const stopCamera = () => {
    if (isRecording.value) {
        stopVideoRecording();
    }

    if (timeUpdateInterval) {
        clearInterval(timeUpdateInterval);
        timeUpdateInterval = null;
    }

    if (stream) {
        stream.getTracks().forEach((track) => track.stop());
        if (videoElement.value) {
            videoElement.value.srcObject = null;
        }
        stream = null;
        console.log("Camera stopped and stream released");
    }

    isCameraActive.value = false;
    isCameraReady.value = false;
    isCameraLoading.value = false;
    cameraError.value = "";
    stopLoadingProgress();
};

const switchCamera = async () => {
    if (
        availableCameras.value.length <= 1 ||
        !isCameraReady.value ||
        isRecording.value
    )
        return;

    try {
        isSwitchingCamera.value = true;
        isCameraReady.value = false;
        cameraStatus.value = "Switching camera...";

        currentCameraIndex.value =
            (currentCameraIndex.value + 1) % availableCameras.value.length;
        console.log("Switching to camera:", currentCameraName.value);

        await startCameraStream();
    } catch (error) {
        console.error("Camera switch failed:", error);
        handleCameraError(error, 0, 1);
    } finally {
        isSwitchingCamera.value = false;
    }
};

const startVideoRecording = async () => {
    if (
        !isCameraReady.value ||
        form.photos.filter((file) => file.type.startsWith("video")).length >=
            MAX_VIDEOS ||
        !stream
    ) {
        return;
    }

    try {
        recordedChunks = [];
        recordingTime.value = 0;

        const options = {
            mimeType: "video/webm;codecs=vp9,opus",
        };

        if (!MediaRecorder.isTypeSupported(options.mimeType)) {
            options.mimeType = "video/webm;codecs=vp8,opus";
            if (!MediaRecorder.isTypeSupported(options.mimeType)) {
                options.mimeType = "video/webm";
                if (!MediaRecorder.isTypeSupported(options.mimeType)) {
                    options.mimeType = "";
                }
            }
        }

        mediaRecorder = new MediaRecorder(stream, options);

        mediaRecorder.ondataavailable = (event) => {
            if (event.data.size > 0) {
                recordedChunks.push(event.data);
            }
        };

        mediaRecorder.onstop = async () => {
            const blob = new Blob(recordedChunks, { type: "video/webm" });

            if (blob.size > MAX_VIDEO_SIZE) {
                Swal.fire({
                    icon: "error",
                    title: "Video Too Large",
                    text: `Video size exceeds ${
                        MAX_VIDEO_SIZE / 1024 / 1024
                    }MB limit.`,
                    timer: 3000,
                });
                return;
            }

            const filename = `water-report-video-${Date.now()}.webm`;
            const file = new File([blob], filename, {
                type: "video/webm",
                lastModified: Date.now(),
            });

            file.duration = recordingTime.value;

            form.photos.push(file);
            form.photo_previews.push(URL.createObjectURL(blob));

            console.log(
                `Video ${
                    form.photos.filter((file) => file.type.startsWith("video"))
                        .length
                } recorded successfully`
            );

            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
            });

            Toast.fire({
                icon: "success",
                title: `Video ${
                    form.photos.filter((file) => file.type.startsWith("video"))
                        .length
                } recorded!`,
            });
        };

        mediaRecorder.start();
        isRecording.value = true;

        recordingInterval = setInterval(() => {
            recordingTime.value++;

            if (recordingTime.value >= MAX_VIDEO_DURATION) {
                stopVideoRecording();
            }
        }, 1000);

        console.log("Video recording started");
    } catch (error) {
        console.error("Failed to start recording:", error);
        Swal.fire({
            icon: "error",
            title: "Recording Failed",
            text: "Failed to start video recording. Please try again.",
            timer: 3000,
        });
    }
};

const stopVideoRecording = () => {
    if (!isRecording.value || !mediaRecorder) return;

    try {
        mediaRecorder.stop();
        isRecording.value = false;

        if (recordingInterval) {
            clearInterval(recordingInterval);
            recordingInterval = null;
        }

        console.log("Video recording stopped");
    } catch (error) {
        console.error("Failed to stop recording:", error);
    }
};

const capturePhoto = async () => {
    if (
        !isCameraReady.value ||
        form.photos.filter((file) => file.type.startsWith("image")).length >=
            MAX_PHOTOS ||
        isCapturing.value ||
        isRecording.value
    ) {
        return;
    }

    try {
        isCapturing.value = true;

        const video = videoElement.value;
        if (!video || !video.videoWidth || !video.videoHeight) {
            throw new Error("Video not ready for capture");
        }

        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const now = new Date();
        const timestamp = now.toLocaleString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: true,
        });

        const timestampPadding = 10;
        const fontSize = Math.max(16, Math.floor(canvas.width / 60));
        ctx.font = `bold ${fontSize}px Arial`;

        const textMetrics = ctx.measureText(timestamp);
        const textWidth = textMetrics.width;
        const textHeight = fontSize;

        const bgX = timestampPadding;
        const bgY = canvas.height - timestampPadding - textHeight - 10;
        const bgWidth = textWidth + 20;
        const bgHeight = textHeight + 20;

        ctx.fillStyle = "rgba(0, 0, 0, 0.8)";
        ctx.fillRect(bgX, bgY, bgWidth, bgHeight);

        ctx.strokeStyle = "white";
        ctx.lineWidth = 2;
        ctx.strokeRect(bgX, bgY, bgWidth, bgHeight);

        const textX = bgX + 10;
        const textY = bgY + textHeight + 5;

        ctx.lineWidth = 6;
        ctx.strokeStyle = "black";
        ctx.strokeText(timestamp, textX, textY);

        ctx.lineWidth = 4;
        ctx.strokeStyle = "white";
        ctx.strokeText(timestamp, textX, textY);

        ctx.lineWidth = 2;
        ctx.strokeStyle = "black";
        ctx.strokeText(timestamp, textX, textY);

        ctx.fillStyle = "white";
        ctx.fillText(timestamp, textX, textY);

        const locationText = currentLocation.value;
        ctx.font = `${Math.floor(fontSize * 0.8)}px Arial`;
        const locationY = textY + fontSize + 5;

        const locTextMetrics = ctx.measureText(locationText);
        const locBgWidth = locTextMetrics.width + 16;
        const locBgHeight = Math.floor(fontSize * 0.8) + 16;

        ctx.fillStyle = "rgba(0, 100, 200, 0.8)";
        ctx.fillRect(
            textX,
            locationY - Math.floor(fontSize * 0.8) - 8,
            locBgWidth,
            locBgHeight
        );

        ctx.strokeStyle = "white";
        ctx.lineWidth = 1;
        ctx.strokeRect(
            textX,
            locationY - Math.floor(fontSize * 0.8) - 8,
            locBgWidth,
            locBgHeight
        );

        ctx.fillStyle = "white";
        ctx.fillText(locationText, textX + 8, locationY);

        const blob = await new Promise((resolve) => {
            canvas.toBlob(resolve, "image/jpeg", 0.95);
        });

        if (!blob) {
            throw new Error("Failed to create image blob");
        }

        if (blob.size > MAX_PHOTO_SIZE) {
            throw new Error("Photo size too large");
        }

        const filename = `water-report-${Date.now()}.jpg`;
        const file = new File([blob], filename, {
            type: "image/jpeg",
            lastModified: Date.now(),
        });

        form.photos.push(file);
        form.photo_previews.push(URL.createObjectURL(blob));

        console.log(
            `Photo ${
                form.photos.filter((file) => file.type.startsWith("image"))
                    .length
            } captured successfully`
        );

        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });

        Toast.fire({
            icon: "success",
            title: `Photo ${
                form.photos.filter((file) => file.type.startsWith("image"))
                    .length
            } captured!`,
        });
    } catch (error) {
        console.error("Photo capture failed:", error);

        Swal.fire({
            icon: "error",
            title: "Capture Failed",
            text: error.message || "Failed to capture photo. Please try again.",
            timer: 3000,
        });
    } finally {
        isCapturing.value = false;
    }
};

const removeMedia = (index) => {
    if (index >= 0 && index < form.photo_previews.length) {
        URL.revokeObjectURL(form.photo_previews[index]);
        form.photos.splice(index, 1);
        form.photo_previews.splice(index, 1);
        console.log(`Media ${index + 1} removed`);
    }
};

const clearAllMedia = () => {
    const totalMedia = form.photos.length;
    if (totalMedia === 0) return;

    Swal.fire({
        title: "Clear All Media?",
        text: `This will remove all ${form.photos.length} media files.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc2626",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Clear All",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            form.photo_previews.forEach((url) => URL.revokeObjectURL(url));

            form.photos = [];
            form.photo_previews = [];
            form.videos = [];
            form.video_previews = [];

            Swal.fire({
                icon: "success",
                title: "Media Cleared",
                toast: true,
                position: "top-end",
                timer: 2000,
                showConfirmButton: false,
            });
        }
    });
};

const restrictPhoneInput = (event) => {
    let value = event.target.value.replace(/[^0-9]/g, "");
    if (value.length > 11) {
        value = value.slice(0, 11);
    }
    form.reporter_phone = value;
};

const submitReport = async () => {
    if (!form.water_issue_type) {
        Swal.fire({
            icon: "error",
            title: "Water Issue Required",
            text: "Please select a water issue type.",
            confirmButtonColor: "#3085d6",
        });
        return;
    }
    if (form.water_issue_type === "others" && !form.custom_water_issue) {
        Swal.fire({
            icon: "error",
            title: "Custom Issue Required",
            text: "Please specify the water issue when selecting 'Others'.",
            confirmButtonColor: "#3085d6",
        });
        return;
    }
    if (form.photos.length === 0) {
        Swal.fire({
            icon: "error",
            title: "Media Required",
            text: "Please capture at least one photo or video for your report.",
            confirmButtonColor: "#3085d6",
        });
        return;
    }

    isSubmitting.value = true;

    console.log("Form data before submission:", form.data());

    const formData = new FormData();
    Object.keys(form.data()).forEach((key) => {
        if (key !== "photos" && key !== "photo_previews") {
            formData.append(key, form[key]);
        }
    });

    form.photos.forEach((file, index) => {
        formData.append(`photos[${index}]`, file);
    });

    try {
        const response = await axios.post(route("reports.store"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        isSubmitting.value = false;
        console.log("Report submitted successfully", response.data);

        emit("submitted", {
            trackingCode: response.data.trackingCode,
            dateSubmitted: response.data.dateSubmitted,
        });

        Swal.fire({
            position: "top-end",
            title: "Report Submitted Successfully!",
            text: `Tracking Code: ${response.data.trackingCode}`,
            icon: "success",
            toast: true,
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
        });

        form.reset();
        form.photos = [];
        form.photo_previews = [];
    } catch (error) {
        isSubmitting.value = false;
        console.log("Submission errors:", error.response?.data);

        if (error.response?.data?.errors) {
            form.errors = error.response.data.errors;
            window.scrollTo({ top: 0, behavior: "smooth" });
        }

        Swal.fire({
            icon: "error",
            title: "Submission Failed",
            text: "Please check the form for errors.",
            confirmButtonColor: "#3085d6",
        });
    }
};

const getLocation = () => {
    if (!navigator.geolocation) {
        locationStatus.value = "error";
        Swal.fire({
            icon: "error",
            title: "Geolocation Not Supported",
            text: "Your browser does not support location services.",
            confirmButtonColor: "#3085d6",
        });
        return;
    }

    locationStatus.value = "loading";

    navigator.geolocation.getCurrentPosition(
        (position) => {
            form.latitude = position.coords.latitude;
            form.longitude = position.coords.longitude;
            locationStatus.value = "success";
            console.log("Location acquired:", {
                latitude: form.latitude,
                longitude: form.longitude,
            });
        },
        (error) => {
            locationStatus.value = "error";
            console.warn("Location error:", error);
            Swal.fire({
                icon: "error",
                title: "Location Access Denied",
                text: "Please enable GPS/location services for your browser.",
                confirmButtonColor: "#3085d6",
            });
        },
        {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 300000,
        }
    );
};

watch(
    () => form.barangay,
    (newBarangay) => {
        form.zone = barangayToZone.value[newBarangay] || "";
        console.log("Barangay changed, zone set to:", form.zone);
    }
);

onMounted(() => {
    getLocation();
    console.log("Enhanced report form component mounted with video support");
});

onUnmounted(() => {
    stopCamera();

    form.photo_previews.forEach((url) => URL.revokeObjectURL(url));
    form.photos = [];
    form.photo_previews = [];
    form.videos = [];
    form.video_previews = [];

    if (progressInterval) clearInterval(progressInterval);
    if (recordingInterval) clearInterval(recordingInterval);
    if (timeUpdateInterval) clearInterval(timeUpdateInterval);

    console.log("Component unmounted, resources cleaned up");
});
</script>

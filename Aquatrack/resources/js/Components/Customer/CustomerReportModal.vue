<template>
    <!-- Single transition wrapper for both overlay and panel -->
    <transition name="modal">
        <div v-if="isOpen" class="fixed inset-0 z-[1000] overflow-hidden">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-xs transition-opacity duration-300"
                @click.self="closeModal"></div>

            <!-- Sliding panel container -->
            <div class="fixed inset-y-0 right-0 w-full max-w-2xl flex">
                <!-- Panel with transform class for animation -->
                <div class="relative w-full h-full transform transition-transform duration-300 ease-in-out">
                    <div class="h-full flex flex-col bg-white shadow-2xl rounded-l-2xl overflow-hidden">
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between px-6 py-5 bg-gradient-to-r from-[#0D4C9D] to-[#1E5CB2] shadow-md">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-white/10 rounded-full">
                                    <v-icon name="oi-report" class="text-amber-300" scale="1.5" />
                                </div>
                                <div>
                                    <span class="text-white font-semibold text-xl tracking-wide">Water Quality Issue
                                        Report</span>
                                    <p class="text-blue-100 text-xs mt-1">
                                        Official Report Submission for Clarin, Bohol
                                    </p>
                                </div>
                            </div>
                            <button @click="closeModal"
                                class="text-white hover:text-gray-200 transition-all duration-200 p-2 rounded-full hover:bg-white/10">
                                <v-icon name="bi-x-lg" class="h-6 w-6" />
                            </button>
                        </div>

                        <!-- Progress Steps -->
                        <div class="bg-blue-50 px-6 py-4 border-b border-blue-100">
                            <div class="flex items-center justify-between">
                                <div v-for="(step, index) in progressSteps" :key="index" class="flex items-center">
                                    <div class="flex flex-col items-center"
                                        :class="{ 'text-blue-700': step.active, 'text-blue-400': !step.active }">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium mb-1"
                                            :class="step.active ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-500'">
                                            {{ index + 1 }}
                                        </div>
                                        <span class="text-xs font-medium">{{ step.name }}</span>
                                    </div>
                                    <div v-if="index < progressSteps.length - 1" class="h-px w-8 bg-blue-200 mx-2">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 overflow-y-auto p-6">
                            <!-- Form Errors Indicator -->
                            <div v-if="hasErrors"
                                class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm shadow-sm">
                                <div class="flex items-start">
                                    <v-icon name="bi-exclamation-triangle" class="mr-2 mt-0.5 flex-shrink-0" />
                                    <div>
                                        <p class="font-medium">
                                            Please correct the following issues before submitting:
                                        </p>
                                        <ul class="list-disc list-inside mt-1 pl-1">
                                            <li v-for="(
error, key
                                                ) in form.errors" :key="key">
                                                {{ error }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <form @submit.prevent="submitReport" class="space-y-6">
                                <!-- Location Information Section -->
                                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                            <v-icon name="bi-geo-alt" class="text-blue-600 mr-2" />
                                            Location Information
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Provide details about where the issue is located
                                        </p>
                                    </div>
                                    <div class="p-5 space-y-4">
                                        <!-- Municipality and Province -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="municipality"
                                                    class="block text-sm font-medium text-gray-700 mb-1">
                                                    Municipality
                                                </label>
                                                <input type="text" id="municipality" v-model="form.municipality"
                                                    readonly
                                                    class="w-full p-3 rounded-lg text-gray-900 bg-gray-50 border border-gray-300 text-sm" />
                                            </div>
                                            <div>
                                                <label for="province"
                                                    class="block text-sm font-medium text-gray-700 mb-1">
                                                    Province
                                                </label>
                                                <input type="text" id="province" v-model="form.province" readonly
                                                    class="w-full p-3 rounded-lg text-gray-900 bg-gray-50 border border-gray-300 text-sm" />
                                            </div>
                                        </div>

                                        <!-- Barangay and Zone -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="barangay"
                                                    class="block text-sm font-medium text-gray-700 mb-1">
                                                    Barangay <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                    <select id="barangay" v-model="form.barangay" required
                                                        class="w-full p-3 pr-10 rounded-lg text-gray-900 bg-white border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm appearance-none transition-all">
                                                        <option value="" disabled selected>
                                                            Select Barangay
                                                        </option>
                                                        <option v-for="barangay in allBarangays" :key="barangay"
                                                            :value="barangay">
                                                            {{ barangay }}
                                                        </option>
                                                    </select>
                                                    <div
                                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                                        <v-icon name="bi-chevron-down" />
                                                    </div>
                                                </div>
                                                <p v-if="form.errors.barangay"
                                                    class="mt-1 text-xs text-red-600 flex items-center">
                                                    <v-icon name="bi-exclamation-circle" class="mr-1" />
                                                    {{ form.errors.barangay }}
                                                </p>
                                            </div>
                                            <div>
                                                <label for="zone" class="block text-sm font-medium text-gray-700 mb-1">
                                                    Zone
                                                </label>
                                                <input type="text" id="zone" v-model="form.zone" readonly
                                                    class="w-full p-3 rounded-lg text-gray-900 bg-gray-50 border border-gray-300 text-sm"
                                                    placeholder="Zone will be auto-filled" />
                                                <p v-if="form.errors.zone"
                                                    class="mt-1 text-xs text-red-600 flex items-center">
                                                    <v-icon name="bi-exclamation-circle" class="mr-1" />
                                                    {{ form.errors.zone }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Purok -->
                                        <div>
                                            <label for="purok" class="block text-sm font-medium text-gray-700 mb-1">
                                                Purok/Street <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="purok" v-model="form.purok" required
                                                class="w-full p-3 rounded-lg text-gray-900 bg-white border border-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                                                placeholder="Enter purok number or street name" />
                                            <p v-if="form.errors.purok"
                                                class="mt-1 text-xs text-red-600 flex items-center">
                                                <v-icon name="bi-exclamation-circle" class="mr-1" />
                                                {{ form.errors.purok }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reporter Information Section -->
                                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                            <v-icon name="bi-person" class="text-blue-600 mr-2" />
                                            Reporter Information
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Your contact details for follow-up if needed
                                        </p>
                                    </div>
                                    <div class="p-5 space-y-4">
                                        <div>
                                            <label for="reporter_name"
                                                class="block text-sm font-medium text-gray-700 mb-1">
                                                Full Name
                                            </label>
                                            <input type="text" id="reporter_name" v-model="form.reporter_name" readonly
                                                class="w-full p-3 rounded-lg text-gray-900 bg-gray-50 border border-gray-300 text-sm" />
                                        </div>
                                        <div>
                                            <label for="reporter_phone"
                                                class="block text-sm font-medium text-gray-700 mb-1">
                                                Phone Number <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <div
                                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 text-sm">+63</span>
                                                </div>
                                                <input type="tel" id="reporter_phone" v-model="form.reporter_phone"
                                                    @input="restrictPhoneInput"
                                                    class="w-full p-3 pl-12 rounded-lg text-gray-900 bg-white border border-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                                                    pattern="[0-9]{1,11}" maxlength="11" placeholder="912 345 6789"
                                                    required />
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500">
                                                Enter your 10-digit mobile number without the leading 0
                                            </p>
                                            <p v-if="form.errors.reporter_phone"
                                                class="mt-1 text-xs text-red-600 flex items-center">
                                                <v-icon name="bi-exclamation-circle" class="mr-1" />
                                                {{ form.errors.reporter_phone }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Issue Details Section -->
                                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                            <v-icon name="bi-droplet" class="text-blue-600 mr-2" />
                                            Issue Details
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Describe the water quality issue you're reporting
                                        </p>
                                    </div>
                                    <div class="p-5 space-y-4">
                                        <!-- Water Issue Type -->
                                        <div>
                                            <label for="water_issue_type"
                                                class="block text-sm font-medium text-gray-700 mb-1">
                                                Issue Type <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <select id="water_issue_type" v-model="form.water_issue_type" required
                                                    class="w-full p-3 pr-10 rounded-lg text-gray-900 bg-white border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm appearance-none transition-all">
                                                    <option value="" disabled selected>
                                                        Select Issue Type
                                                    </option>
                                                    <option v-for="issue in waterIssueTypes" :key="issue"
                                                        :value="issue">
                                                        {{ issue }}
                                                    </option>
                                                    <option value="others">
                                                        Others
                                                    </option>
                                                </select>
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                                    <v-icon name="bi-chevron-down" />
                                                </div>
                                            </div>
                                            <p v-if="form.errors.water_issue_type"
                                                class="mt-1 text-xs text-red-600 flex items-center">
                                                <v-icon name="bi-exclamation-circle" class="mr-1" />
                                                {{
                                                    form.errors.water_issue_type
                                                }}
                                            </p>
                                        </div>

                                        <!-- Custom Issue Description -->
                                        <div v-if="form.water_issue_type === 'others'">
                                            <label for="custom_water_issue"
                                                class="block text-sm font-medium text-gray-700 mb-1">
                                                Custom Issue Description <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="custom_water_issue" v-model="form.custom_water_issue"
                                                class="w-full p-3 rounded-lg text-gray-900 bg-white border border-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                                                placeholder="Describe the custom water issue" maxlength="100"
                                                required />
                                            <p v-if="form.errors.custom_water_issue"
                                                class="mt-1 text-xs text-red-600 flex items-center">
                                                <v-icon name="bi-exclamation-circle" class="mr-1" />
                                                {{
                                                    form.errors.custom_water_issue
                                                }}
                                            </p>
                                        </div>

                                        <!-- Description -->
                                        <div>
                                            <label for="description"
                                                class="block text-sm font-medium text-gray-700 mb-1">
                                                Detailed Description <span class="text-red-500">*</span>
                                            </label>
                                            <div class="bg-white p-4 rounded-lg border border-gray-300 shadow-inner">
                                                <textarea id="description" v-model="form.description" rows="4" required
                                                    class="w-full p-3 rounded-lg text-gray-900 bg-white border-none placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 text-sm resize-none"
                                                    placeholder="Please provide a detailed description of the water quality issue, including when it started, how it's affecting you, and any other relevant details..."
                                                    maxlength="500"></textarea>
                                                <div
                                                    class="flex justify-between items-center mt-2 text-xs text-gray-500">
                                                    <div v-if="form.description" class="flex items-center">
                                                        <v-icon name="bi-check-circle" class="text-green-500 mr-1" />
                                                        <span>{{
                                                            form.description.length
                                                        }}/500 characters</span>
                                                    </div>
                                                    <div v-else>
                                                        <span>Please provide a detailed description</span>
                                                    </div>
                                                    <button type="button" @click="form.description = ''"
                                                        v-if="form.description"
                                                        class="text-red-500 hover:text-red-700 text-xs">
                                                        Clear
                                                    </button>
                                                </div>
                                                <p v-if="form.errors.description"
                                                    class="mt-1 text-xs text-red-600 flex items-center">
                                                    <v-icon name="bi-exclamation-circle" class="mr-1" />
                                                    {{ form.errors.description }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Evidence Section -->
                                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                            <v-icon name="bi-camera" class="text-blue-600 mr-2" />
                                            Evidence
                                            <span class="text-xs font-normal text-gray-500 ml-2">
                                                ({{ form.photos.length }}/{{
                                                    MAX_TOTAL
                                                }})
                                            </span>
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Visual documentation helps us address the issue more effectively
                                        </p>
                                    </div>
                                    <div class="p-5 space-y-4">
                                        <!-- Media Counters -->
                                        <div class="flex items-center justify-between text-sm">
                                            <span
                                                class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full flex items-center">
                                                <v-icon name="bi-image" class="mr-1" />
                                                Photos:
                                                {{
                                                    form.photos.filter((file) =>
                                                        file.type.startsWith(
                                                            "image"
                                                        )
                                                    ).length
                                                }}/{{ MAX_PHOTOS }}
                                            </span>
                                            <span
                                                class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full flex items-center">
                                                <v-icon name="bi-camera-video" class="mr-1" />
                                                Videos:
                                                {{
                                                    form.photos.filter((file) =>
                                                        file.type.startsWith(
                                                            "video"
                                                        )
                                                    ).length
                                                }}/{{ MAX_VIDEOS }}
                                            </span>
                                        </div>

                                        <!-- Camera Status Banner -->
                                        <div v-if="cameraError" class="p-3 bg-red-50 border border-red-200 rounded-lg">
                                            <div class="flex items-center text-red-600 text-sm">
                                                <v-icon name="bi-exclamation-triangle-fill" class="w-4 h-4 mr-2" />
                                                {{ cameraError }}
                                            </div>
                                            <button type="button" @click="retryCamera"
                                                class="mt-2 text-xs text-blue-600 hover:text-blue-700 underline flex items-center">
                                                <v-icon name="bi-arrow-repeat" class="mr-1" />
                                                Try Again
                                            </button>
                                        </div>

                                        <!-- Camera Interface -->
                                        <div class="bg-gray-800 rounded-xl overflow-hidden shadow-lg">
                                            <!-- Camera Not Active State -->
                                            <div v-if="!isCameraActive" class="p-6 text-center">
                                                <div class="mb-5">
                                                    <div
                                                        class="w-16 h-16 mx-auto bg-blue-600 rounded-full flex items-center justify-center mb-4 shadow-md">
                                                        <v-icon name="hi-camera" class="w-8 h-8 text-white" />
                                                    </div>
                                                    <p class="text-gray-300 text-sm">
                                                        Capture photos or videos to document the issue
                                                    </p>
                                                </div>
                                                <button type="button" @click="initializeCamera"
                                                    :disabled="isCameraLoading"
                                                    class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium shadow-md transition-all">
                                                    <v-icon v-if="isCameraLoading" name="bi-arrow-repeat"
                                                        class="animate-spin mr-2 h-4 w-4" />
                                                    <v-icon v-else name="hi-camera" class="mr-2 h-4 w-4" />
                                                    {{
                                                        isCameraLoading
                                                            ? "Starting Camera..."
                                                            : "Open Camera"
                                                    }}
                                                </button>
                                            </div>

                                            <!-- Active Camera View -->
                                            <div v-else class="relative">
                                                <div class="bg-black" style="aspect-ratio: 4/3">
                                                    <video ref="videoElement" class="w-full h-full object-cover"
                                                        autoplay playsinline muted></video>
                                                    <div v-if="!isCameraReady"
                                                        class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center">
                                                        <div class="text-center text-white">
                                                            <div
                                                                class="inline-block animate-spin w-8 h-8 border-2 border-white border-t-transparent rounded-full mb-3">
                                                            </div>
                                                            <p class="text-sm">
                                                                {{ cameraStatus }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div v-if="isCameraReady"
                                                        class="absolute top-3 left-3 bg-green-500 text-white px-2 py-1 rounded-full text-xs shadow-md">
                                                        LIVE
                                                    </div>
                                                    <div v-if="isRecording"
                                                        class="absolute top-3 right-3 bg-red-500 text-white px-2 py-1 rounded-full text-xs shadow-md flex items-center">
                                                        <div class="w-2 h-2 bg-white rounded-full mr-1 animate-pulse">
                                                        </div>
                                                        REC
                                                        {{
                                                            formatTime(
                                                                recordingTime
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                                <div class="bg-gray-900 p-4 flex justify-center gap-4">
                                                    <button type="button" @click="capturePhoto" :disabled="!isCameraReady ||
                                                        form.photos.filter(
                                                            (file) =>
                                                                file.type.startsWith(
                                                                    'image'
                                                                )
                                                        ).length >=
                                                        MAX_PHOTOS ||
                                                        isCapturing ||
                                                        isRecording
                                                        "
                                                        class="p-3 bg-blue-500 hover:bg-blue-600 rounded-full text-white shadow-md transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                                        <v-icon name="hi-camera" class="w-6 h-6" />
                                                    </button>
                                                    <button type="button" @click="
                                                        isRecording
                                                            ? stopVideoRecording()
                                                            : startVideoRecording()
                                                        " :disabled="!isCameraReady ||
                                                            (form.photos.filter(
                                                                (file) =>
                                                                    file.type.startsWith(
                                                                        'video'
                                                                    )
                                                            ).length >=
                                                                MAX_VIDEOS &&
                                                                !isRecording)
                                                            "
                                                        class="p-3 rounded-full text-white shadow-md transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                                        :class="isRecording
                                                                ? 'bg-red-500 hover:bg-red-600'
                                                                : 'bg-red-500 hover:bg-red-600'
                                                            ">
                                                        <div v-if="isRecording" class="w-5 h-5 bg-white rounded-sm">
                                                        </div>
                                                        <v-icon v-else name="hi-video-camera" class="w-6 h-6" />
                                                    </button>
                                                    <button type="button" @click="stopCamera" :disabled="isRecording"
                                                        class="p-3 bg-red-600 hover:bg-red-700 rounded-full text-white shadow-md transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                                        <v-icon name="hi-solid-x" class="w-6 h-6" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Media Gallery -->
                                        <div v-if="form.photo_previews.length > 0" class="mt-5">
                                            <div class="flex items-center justify-between mb-3">
                                                <h4 class="text-sm font-semibold text-gray-900">
                                                    Captured Media ({{
                                                        form.photo_previews.length
                                                    }})
                                                </h4>
                                                <button type="button" @click="clearAllMedia"
                                                    class="text-xs text-red-600 hover:text-red-700 flex items-center">
                                                    <v-icon name="hi-trash" class="mr-1" />
                                                    Clear All
                                                </button>
                                            </div>
                                            <div class="grid grid-cols-3 gap-3">
                                                <div v-for="(
preview, index
                                                    ) in form.photo_previews" :key="'media-' + index"
                                                    class="relative group aspect-square">
                                                    <div class="absolute -top-2 -right-2 z-10">
                                                        <span v-if="
                                                            form.photos[
                                                                index
                                                            ].type.startsWith(
                                                                'image'
                                                            )
                                                        "
                                                            class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                                                            Photo
                                                        </span>
                                                        <span v-else
                                                            class="bg-purple-500 text-white text-xs px-2 py-1 rounded-full">
                                                            Video
                                                        </span>
                                                    </div>
                                                    <img v-if="
                                                        form.photos[
                                                            index
                                                        ].type.startsWith(
                                                            'image'
                                                        )
                                                    " :src="preview"
                                                        class="w-full h-full object-cover rounded-lg border border-gray-200 shadow-sm" />
                                                    <video v-else :src="preview"
                                                        class="w-full h-full object-cover rounded-lg border border-gray-200 shadow-sm"
                                                        muted preload="metadata"></video>
                                                    <button @click="removeMedia(index)"
                                                        class="absolute top-1 right-1 bg-red-500 p-1 rounded-full text-white opacity-0 group-hover:opacity-100 transition-opacity shadow-md">
                                                        <v-icon name="hi-trash" class="w-3 h-3" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <p v-if="form.errors.photos"
                                            class="mt-2 text-xs text-red-600 flex items-center">
                                            <v-icon name="bi-exclamation-circle" class="mr-1" />
                                            {{ form.errors.photos }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="border-t border-gray-200 px-4 py-5 bg-gray-50 rounded-b-xl">
                                    <!-- <div class="mb-4 text-center">
                                        <p class="text-sm text-gray-600">
                                            <span class="text-red-500">*</span> indicates required fields
                                        </p>
                                    </div> -->
                                    <button type="submit" :disabled="form.processing ||
                                        isSubmitting ||
                                        isRecording
                                        "
                                        class="w-full inline-flex justify-center items-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed">
                                        <v-icon v-if="isSubmitting" name="bi-arrow-repeat"
                                            class="animate-spin mr-2 h-5 w-5" />
                                        <v-icon v-else-if="isRecording" name="bi-record-circle" class="mr-2 h-5 w-5" />
                                        <v-icon v-else name="bi-send" class="mr-2 h-5 w-5" />
                                        <span v-if="isSubmitting">Submitting Report...</span>
                                        <span v-else-if="isRecording">Stop recording to submit</span>
                                        <span v-else>Submit Report</span>
                                    </button>
                                    <p class="text-xs text-center text-gray-500 mt-3">
                                        By submitting this report, you acknowledge that the information provided will be
                                        reviewed by the appropriate authorities and may be used to address water quality
                                        issues in your area.
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from "vue";
import Swal from "sweetalert2";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    zones: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits([
    "update:isOpen",
    "update:successData",
    "update:showSuccessModal",
]);

// Get authenticated user
const { props: pageProps } = usePage();
const user = pageProps.auth?.user;

// Water issue types matching ReportForm.vue and backend
const waterIssueTypes = [
    "Burst pipe",
    "Rusty water",
    "Low water pressure",
    "No water supply",
    "Clogged pipes",
    "Smelly water",
    "Cloudy or dirty water",
    "Hot water issues",
    "Running toilet",
];

// Progress steps
const progressSteps = computed(() => [
    { name: "Location", active: !!form.barangay },
    { name: "Contact", active: !!form.reporter_name },
    { name: "Details", active: !!form.description || !!form.water_issue_type },
    { name: "Evidence", active: form.photos.length > 0 },
]);

// Form setup with authenticated user's name and fields from ReportForm.vue
const form = useForm({
    municipality: "Clarin",
    province: "Bohol",
    zone: "",
    barangay: "",
    purok: "",
    water_issue_type: "",
    custom_water_issue: "",
    description: "",
    photos: [],
    photo_previews: [],
    reporter_name: user?.name || "Authenticated User",
    reporter_phone: "",
    latitude: null,
    longitude: null,
});

const isSubmitting = ref(false);
const locationStatus = ref("idle");
const isCameraActive = ref(false);
const isCameraReady = ref(false);
const isCameraLoading = ref(false);
const isCapturing = ref(false);
const isRecording = ref(false);
const recordingTime = ref(0);
const cameraError = ref("");
const cameraStatus = ref("Initializing...");
const videoElement = ref(null);

let stream = null;
let mediaRecorder = null;
let recordedChunks = [];

const MAX_PHOTOS = 3;
const MAX_VIDEOS = 2;
const MAX_TOTAL = MAX_PHOTOS + MAX_VIDEOS;
const MAX_PHOTO_SIZE = 5 * 1024 * 1024; // 5MB
const MAX_VIDEO_SIZE = 25 * 1024 * 1024; // 25MB
const MAX_VIDEO_DURATION = 30; // 30 seconds

const allBarangays = computed(() => Object.values(props.zones).flat());
const barangayToZone = computed(() => {
    const mapping = {};
    Object.entries(props.zones).forEach(([zone, barangays]) => {
        barangays.forEach((barangay) => {
            mapping[barangay] = zone;
        });
    });
    return mapping;
});
const hasErrors = computed(() => Object.keys(form.errors).length > 0);

const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, "0")}`;
};

const initializeCamera = async () => {
    cameraError.value = "";
    isCameraLoading.value = true;
    try {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error("Camera not supported by this browser");
        }
        isCameraActive.value = true;
        await nextTick();
        const constraints = { video: true, audio: true };
        stream = await navigator.mediaDevices.getUserMedia(constraints);
        videoElement.value.srcObject = stream;
        isCameraReady.value = true;
    } catch (error) {
        cameraError.value = error.message || "Failed to access camera";
    } finally {
        isCameraLoading.value = false;
    }
};

const retryCamera = () => {
    cameraError.value = "";
    initializeCamera();
};

const stopCamera = () => {
    if (isRecording.value) stopVideoRecording();
    if (stream) {
        stream.getTracks().forEach((track) => track.stop());
        stream = null;
    }
    if (videoElement.value) {
        videoElement.value.srcObject = null;
    }
    isCameraActive.value = false;
    isCameraReady.value = false;
};

const capturePhoto = async () => {
    if (
        !isCameraReady.value ||
        form.photos.filter((file) => file.type.startsWith("image")).length >=
        MAX_PHOTOS ||
        isCapturing.value ||
        isRecording.value
    )
        return;

    isCapturing.value = true;
    try {
        const canvas = document.createElement("canvas");
        canvas.width = videoElement.value.videoWidth;
        canvas.height = videoElement.value.videoHeight;
        const ctx = canvas.getContext("2d");

        ctx.drawImage(videoElement.value, 0, 0);

        const timestamp = new Date().toLocaleString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: true,
        });
        ctx.font = "16px Arial";
        ctx.fillStyle = "white";
        ctx.textAlign = "left";
        ctx.textBaseline = "bottom";
        ctx.fillText(timestamp, 10, canvas.height - 10);

        ctx.fillStyle = "rgba(0, 0, 0, 0.5)";
        ctx.fillRect(
            0,
            canvas.height - 30,
            ctx.measureText(timestamp).width + 20,
            20
        );

        ctx.fillStyle = "white";
        ctx.fillText(timestamp, 10, canvas.height - 10);

        const blob = await new Promise((resolve) =>
            canvas.toBlob(resolve, "image/jpeg", 0.95)
        );
        if (blob.size > MAX_PHOTO_SIZE) throw new Error("Photo size too large");
        const file = new File([blob], `water-report-${Date.now()}.jpg`, {
            type: "image/jpeg",
        });
        form.photos.push(file);
        form.photo_previews.push(URL.createObjectURL(blob));
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: `Photo ${form.photos.filter((file) => file.type.startsWith("image"))
                    .length
                } captured!`,
            timer: 2000,
        });
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Capture Failed",
            text: error.message,
            timer: 3000,
        });
    } finally {
        isCapturing.value = false;
    }
};

const startVideoRecording = async () => {
    if (
        !isCameraReady.value ||
        form.photos.filter((file) => file.type.startsWith("video")).length >=
        MAX_VIDEOS ||
        !stream
    )
        return;

    recordedChunks = [];
    recordingTime.value = 0;
    const options = { mimeType: "video/webm" };
    mediaRecorder = new MediaRecorder(stream, options);

    let recordingInterval = null;

    mediaRecorder.ondataavailable = (event) => {
        if (event.data.size > 0) recordedChunks.push(event.data);
    };

    mediaRecorder.onstop = () => {
        if (recordingInterval) {
            clearInterval(recordingInterval);
        }

        const blob = new Blob(recordedChunks, { type: "video/webm" });
        if (blob.size > MAX_VIDEO_SIZE) {
            Swal.fire({
                icon: "error",
                title: "Video Too Large",
                text: "Video exceeds 25MB limit",
                timer: 3000,
            });
            return;
        }
        const file = new File([blob], `water-report-video-${Date.now()}.webm`, {
            type: "video/webm",
        });
        form.photos.push(file);
        form.photo_previews.push(URL.createObjectURL(blob));
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: `Video ${form.photos.filter((file) => file.type.startsWith("video"))
                    .length
                } recorded!`,
            timer: 2000,
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
};

const stopVideoRecording = () => {
    if (!isRecording.value || !mediaRecorder) return;
    mediaRecorder.stop();
    isRecording.value = false;
};

const removeMedia = (index) => {
    URL.revokeObjectURL(form.photo_previews[index]);
    form.photos.splice(index, 1);
    form.photo_previews.splice(index, 1);
};

const clearAllMedia = () => {
    Swal.fire({
        title: "Clear All Media?",
        text: `This will remove all ${form.photos.length} media files.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc2626",
        cancelButtonText: "Cancel",
        confirmButtonText: "Clear All",
    }).then((result) => {
        if (result.isConfirmed) {
            form.photo_previews.forEach((url) => URL.revokeObjectURL(url));
            form.photos = [];
            form.photo_previews = [];
            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "success",
                title: "Media Cleared",
                timer: 2000,
            });
        }
    });
};

const restrictPhoneInput = (event) => {
    let value = event.target.value.replace(/[^0-9]/g, "");
    if (value.length > 11) value = value.slice(0, 11);
    form.reporter_phone = value;
};

const submitReport = () => {
    if (form.photos.length === 0) {
        Swal.fire({
            icon: "error",
            title: "Media Required",
            text: "At least one photo or video is required.",
            timer: 3000,
        });
        return;
    }
    if (locationStatus.value !== "success") {
        Swal.fire({
            icon: "error",
            title: "Location Required",
            text: "Please enable GPS/location services.",
            timer: 3000,
        });
        getLocation();
        return;
    }
    if (isRecording.value) {
        Swal.fire({
            icon: "warning",
            title: "Recording in Progress",
            text: "Please stop recording.",
            timer: 3000,
        });
        return;
    }
    if (!form.water_issue_type) {
        Swal.fire({
            icon: "error",
            title: "Issue Type Required",
            text: "Please select a water issue type.",
            timer: 3000,
        });
        return;
    }
    if (form.water_issue_type === "others" && !form.custom_water_issue.trim()) {
        Swal.fire({
            icon: "error",
            title: "Custom Issue Required",
            text: "Please provide a description for the custom water issue.",
            timer: 3000,
        });
        return;
    }

    isSubmitting.value = true;

    console.log("Form data before submission:", form.data());

    form.post(route("reports.store"), {
        preserveScroll: true,
        onSuccess: (response) => {
            isSubmitting.value = false;
            const trackingCode =
                response.props.trackingCode ||
                response.props.swal?.trackingCode;
            Swal.fire({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                icon: "success",
                title: "Report Submitted Successfully!",
                text: `Your tracking code is ${trackingCode}. Save it for reference.`,
                didOpen: (toast) => {
                    toast.addEventListener("mouseenter", Swal.stopTimer);
                    toast.addEventListener("mouseleave", Swal.resumeTimer);
                },
            });
            emit("update:successData", {
                trackingCode: trackingCode,
                dateSubmitted: response.props.dateSubmitted,
            });
            emit("update:showSuccessModal", true);
            closeModal();
        },
        onError: (errors) => {
            isSubmitting.value = false;
            console.error("Form submission errors:", errors);
            Swal.fire({
                icon: "error",
                title: "Submission Failed",
                text:
                    errors.message ||
                    "Please correct the errors and try again.",
                timer: 3000,
            });
        },
    });
};

const getLocation = () => {
    if (!navigator.geolocation) {
        locationStatus.value = "error";
        Swal.fire({
            icon: "error",
            title: "Geolocation Not Supported",
            text: "Your browser does not support location services.",
            timer: 3000,
        });
        return;
    }
    locationStatus.value = "loading";
    navigator.geolocation.getCurrentPosition(
        (position) => {
            form.latitude = position.coords.latitude;
            form.longitude = position.coords.longitude;
            locationStatus.value = "success";
        },
        () => {
            locationStatus.value = "error";
            Swal.fire({
                icon: "error",
                title: "Location Access Denied",
                text: "Please enable GPS/location services.",
                timer: 3000,
            });
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 300000 }
    );
};

const closeModal = () => {
    stopCamera();
    form.reset();
    form.clearErrors();
    emit("update:isOpen", false);
};

watch(
    () => form.barangay,
    (newBarangay) => {
        form.zone = barangayToZone.value[newBarangay] || "";
    }
);

watch(
    () => form.water_issue_type,
    (newIssueType) => {
        if (newIssueType !== "others") {
            form.custom_water_issue = "";
        }
    }
);

onMounted(() => {
    getLocation();
    form.reporter_name = user?.name || "Authenticated User";
});

onUnmounted(() => {
    stopCamera();
    form.photo_previews.forEach((url) => URL.revokeObjectURL(url));
});
</script>

<style scoped>
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #e2e8f0 #f8fafc;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: #94a3b8;
}

.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .transform,
.modal-leave-active .transform {
    transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.5, 1);
}

.modal-enter-from .transform {
    transform: translateX(100%);
}

.modal-leave-to .transform {
    transform: translateX(100%);
}

@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.7;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
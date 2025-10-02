<script setup>
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import Swal from 'sweetalert2';
import { ref } from 'vue';
import { computed, watch } from 'vue';
import ReportSuccessModal from '@/Components/Modals/ReportSuccessModal.vue';

const emit = defineEmits(['close', 'submitted']);

const showSuccessModal = ref(false);
const trackingInfo = ref(null);

const handleClose = () => {
    emit('close');
};

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

defineProps({
    show: Boolean,
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
    reporter_name: "",
    reporter_phone: "",
    priority: "medium",
});

const filteredBarangays = computed(() => {
    return form.zone ? zones[form.zone] : [];
});

watch(() => form.zone, (newZone) => {
    form.barangay = "";
});

// const barangays = [
//     "Bacani", "Bogtongbod", "Bonbon", "Bontud", "Buacao",
//     "Buangan", "Cabog", "Caboy", "Caluwasan",
//     "Candajec", "Cantoyoc", "Comaang", "Danahao", "Katipunan",
//     "Lajog", "Mataub", "Nahawan",
//     "Poblacion Centro", "Poblacion Norte", "Poblacion Sur", "Tangaran",
//     "Tontunan", "Tubod", "Villaflor"
// ];

const MAX_FILES = 5;
const MAX_FILE_SIZE = 10 * 1024 * 1024;

const handleFileUpload = (event) => {
    const files = event.target.files;

    if (form.photos.length + files.length > MAX_FILES) {
        Swal.fire({
            icon: 'error',
            title: 'Too Many Files',
            html: `You can upload a maximum of ${MAX_FILES} files.<br>You currently have ${form.photos.length} files selected.`,
            confirmButtonColor: '#3085d6',
        });
        event.target.value = '';
        return;
    }

    const validFiles = [];
    const invalidFiles = [];
    const invalidTypeFiles = [];

    Array.from(files).forEach(file => {
        if (!file.type.match('image.*')) {
            invalidTypeFiles.push(file.name);
        } else if (file.size > MAX_FILE_SIZE) {
            invalidFiles.push(file.name);
        } else {
            validFiles.push(file);
        }
    });

    if (invalidTypeFiles.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid File Type',
            html: `The following files are not images:<br><strong>${invalidTypeFiles.join('<br>')}</strong><br><br>Please select only image files.`,
            confirmButtonColor: '#3085d6',
        });
    }

    if (invalidFiles.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'File Size Exceeded',
            html: `The following files exceed 10MB limit:<br><strong>${invalidFiles.join('<br>')}</strong><br><br>Please select smaller files.`,
            confirmButtonColor: '#3085d6',
        });
    }

    if (validFiles.length > 0) {
        form.photos = [...form.photos, ...validFiles];
        validFiles.forEach(file => {
            form.photo_previews.push(URL.createObjectURL(file));
        });
    }

    event.target.value = '';
};

const removePhoto = (index) => {
    URL.revokeObjectURL(form.photo_previews[index]);
    form.photos.splice(index, 1);
    form.photo_previews.splice(index, 1);
};

const submitReport = () => {
    if (form.photos.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Photos Required',
            text: 'Please upload at least one photo for your report.',
            confirmButtonColor: '#3085d6',
        });
        return;
    }

    form.post(route('reports.store'), {
        preserveScroll: true,
        onSuccess: (response) => {
            emit('submitted'); // Emit submitted event
            emit('close');

            form.reset();
            form.photo_previews.forEach(url => URL.revokeObjectURL(url));
            form.photo_previews = [];

            // Store tracking info and show success modal
            trackingInfo.value = {
                code: response.props.trackingCode,
                date: response.props.dateSubmitted
            };
            showSuccessModal.value = true;
        },
        onError: (errors) => {
            Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                text: 'There was an error submitting your report. Please try again.',
                confirmButtonColor: '#3085d6',
            });
        }
    });
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')">
        <template #title>
            <div class="flex items-center space-x-2">
                <v-icon name="bi-file-earmark-text" class="text-white" />
                <span class="text-white font-medium">Add Water Quality Report</span>
            </div>
        </template>

        <form @submit.prevent="submitReport" class="space-y-4">
            <!-- Location Section -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Municipality -->
                <div>
                    <label class="text-sm font-medium text-gray-700 flex items-center">
                        <v-icon name="bi-building" class="mr-1 text-blue-500" /> Municipality
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <v-icon name="bi-geo-alt" class="text-gray-400" />
                        </div>
                        <input type="text" value="Clarin" readonly
                            class="block w-full pl-10 rounded-md border-gray-300 bg-gray-100 sm:text-sm p-2 border">
                    </div>
                </div>

                <!-- Province -->
                <div>
                    <label class="text-sm font-medium text-gray-700 flex items-center">
                        <v-icon name="bi-map" class="mr-1 text-blue-500" /> Province
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <v-icon name="bi-geo" class="text-gray-400" />
                        </div>
                        <input type="text" value="Bohol" readonly
                            class="block w-full pl-10 rounded-md border-gray-300 bg-gray-100 sm:text-sm p-2 border">
                    </div>
                </div>

                <!-- Reporter Name -->
                <div>
                    <label for="reporter_name" class="text-sm font-medium text-gray-700 flex items-center">
                        <v-icon name="bi-person-badge" class="mr-1 text-blue-500" />
                        Reporter's Full Name <span class="text-red-500 ml-1">*</span>
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <v-icon name="bi-person" class="text-gray-400" />
                        </div>
                        <input type="text" id="reporter_name" v-model="form.reporter_name" required
                            class="block w-full pl-10 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border"
                            placeholder="Enter your full name">
                    </div>
                    <p v-if="form.errors.reporter_name" class="mt-1 text-sm text-red-600">{{ form.errors.reporter_name
                    }}</p>
                </div>

                <!-- Reporter Phone -->
                <div>
                    <label for="reporter_phone" class="text-sm font-medium text-gray-700 flex items-center">
                        <v-icon name="bi-telephone" class="mr-1 text-blue-500" />
                        Phone Number/Contact Number
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <v-icon name="bi-phone" class="text-gray-400" />
                        </div>
                        <input type="tel" id="reporter_phone" v-model="form.reporter_phone"
                            class="block w-full pl-10 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border"
                            placeholder="Enter phone number (Optional)">
                    </div>
                    <p v-if="form.errors.reporter_phone" class="mt-1 text-sm text-red-600">{{ form.errors.reporter_phone
                    }}</p>
                </div>

                <div class="sm:col-span-2">
                    <label for="zone" class="text-sm font-medium text-gray-700 flex items-center">
                        <v-icon name="bi-signpost-split" class="mr-1 text-blue-500" /> Zone
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <v-icon name="bi-grid" class="text-gray-400" />
                        </div>
                        <select id="zone" v-model="form.zone" required
                            class="block w-full pl-10 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                            <option value="" disabled selected>Select Zone</option>
                            <option v-for="(zone, index) in Object.keys(zones)" :key="index" :value="zone">
                                {{ zone }}
                            </option>
                        </select>
                    </div>
                    <p v-if="form.errors.zone" class="mt-1 text-sm text-red-600">{{ form.errors.zone }}</p>
                </div>

                <!-- Barangay Dropdown -->
                <div class="sm:col-span-2">
                    <label for="barangay" class="text-sm font-medium text-gray-700 flex items-center">
                        <v-icon name="bi-signpost" class="mr-1 text-blue-500" /> Barangay
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <v-icon name="bi-info-circle" class="text-gray-400" />
                        </div>
                        <select id="barangay" v-model="form.barangay" required :disabled="!form.zone"
                            class="block w-full pl-10 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                            <option value="" disabled selected>Select Barangay</option>
                            <option v-for="barangay in filteredBarangays" :key="barangay" :value="barangay">
                                {{ barangay }}
                            </option>
                        </select>
                    </div>
                    <p v-if="form.errors.barangay" class="mt-1 text-sm text-red-600">{{ form.errors.barangay }}</p>
                </div>

                <!-- Purok Input -->
                <div class="sm:col-span-2">
                    <label for="purok" class="text-sm font-medium text-gray-700 flex items-center">
                        <v-icon name="bi-tag" class="mr-1 text-blue-500" /> Purok/Street
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <v-icon name="bi-house" class="text-gray-400" />
                        </div>
                        <input type="text" id="purok" v-model="form.purok" required
                            class="block w-full pl-10 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border"
                            placeholder="Enter purok number or street name">
                    </div>
                    <p v-if="form.errors.purok" class="mt-1 text-sm text-red-600">{{ form.errors.purok }}</p>
                </div>
            </div>

            <div class="sm:col-span-2">
                <label class="text-sm font-medium text-gray-700 flex items-center">
                    <v-icon name="bi-exclamation-triangle" class="mr-1 text-blue-500" /> Priority
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <v-icon name="bi-flag" class="text-gray-400" />
                    </div>
                    <select v-model="form.priority" required
                        class="block w-full pl-10 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <p v-if="form.errors.priority" class="mt-1 text-sm text-red-600">{{ form.errors.priority }}</p>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="text-sm font-medium text-gray-700 flex items-center">
                    <v-icon name="bi-card-text" class="mr-1 text-blue-500" /> Description
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 pt-3 pointer-events-none">
                        <v-icon name="bi-file-earmark-text" class="text-gray-400" />
                    </div>
                    <textarea id="description" v-model="form.description" rows="3" required
                        class="block w-full pl-10 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border"
                        placeholder="Describe the water quality issue"></textarea>
                </div>
                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
            </div>

            <!-- Photo Upload -->
            <div>
                <label class="text-sm font-medium text-gray-700 flex items-center">
                    <v-icon name="bi-images" class="mr-1 text-blue-500" /> Upload Photos
                </label>
                <div class="mt-1">
                    <label for="file-upload" class="cursor-pointer">
                        <div
                            class="flex justify-center rounded-md border-2 border-dashed border-gray-300 px-6 pt-5 pb-6 hover:border-blue-400 hover:bg-blue-50 transition-colors">
                            <div class="space-y-1 text-center">
                                <v-icon name="bi-images" class="mx-auto h-12 w-12 text-gray-400" />
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <span class="relative font-medium text-blue-600 hover:text-blue-500">
                                        Click to upload files
                                    </span>
                                    <input id="file-upload" name="photos" type="file" class="sr-only" multiple
                                        accept="image/*" @change="handleFileUpload">
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB each (max {{ MAX_FILES }}
                                    files)</p>
                                <p v-if="form.photos.length > 0" class="text-xs text-blue-500">
                                    {{ form.photos.length }} of {{ MAX_FILES }} files selected
                                </p>
                            </div>
                        </div>
                    </label>
                </div>
                <p v-if="form.errors.photos" class="mt-1 text-sm text-red-600">{{ form.errors.photos }}</p>

                <!-- Photo Previews -->
                <div v-if="form.photo_previews.length > 0" class="mt-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div v-for="(preview, index) in form.photo_previews" :key="index" class="relative group">
                            <img :src="preview" class="h-24 w-full object-cover rounded border border-gray-200" />
                            <div
                                class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200">
                                <button @click="removePhoto(index)" type="button"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                                    <v-icon name="bi-x-lg" scale="0.8" />
                                </button>
                                <div
                                    class="absolute bottom-1 left-1 bg-black bg-opacity-50 text-white text-xs px-1 rounded">
                                    {{ (form.photos[index].size / 1024 / 1024).toFixed(2) }} MB
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" @click="$emit('close')"
                    class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Cancel
                </button>
                <button type="submit" :disabled="form.processing"
                    class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-75 disabled:cursor-not-allowed transition-colors">
                    <span v-if="form.processing" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Processing...
                    </span>
                    <span v-else class="flex items-center">
                        <v-icon name="bi-check-circle" class="mr-2" />
                        Submit Report
                    </span>
                </button>
            </div>
        </form>
    </Modal>

    <!-- Success Modal -->
    <ReportSuccessModal v-if="trackingInfo" :show="showSuccessModal" :tracking-code="trackingInfo.code"
        :date-submitted="trackingInfo.date" @close="showSuccessModal = false" />
</template>

<template>
    <Modal :show="show" @close="closeModal">
        <!-- Modal Header -->
        <template #header>
            <div class="bg-blue-600 px-6 py-4 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <v-icon :name="isEditing ? 'ri-edit-box-fill' : 'bi-megaphone-fill'" class="w-6 h-6 text-white" />
                        <h3 class="text-xl font-semibold text-white">
                            {{ isEditing ? 'Edit Announcement' : 'Create New Announcement' }}
                        </h3>
                    </div>
                    <button @click="closeModal" class="text-blue-100 hover:text-white">
                        <v-icon name="bi-x-lg" class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </template>

        <!-- Modal Body -->
        <div class="px-6 py-4 space-y-5">
            <!-- Title Field -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-2">
                    <v-icon name="bi-card-heading" class="w-4 h-4 text-gray-500" />
                    Title <span class="text-red-500">*</span>
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <v-icon name="bi-textarea-t" class="h-4 w-4 text-gray-400" />
                    </div>
                    <input type="text" id="title" v-model="form.title"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Enter announcement title">
                </div>
                <p v-if="errors.title" class="mt-2 text-sm text-red-600 flex items-start gap-2">
                    <v-icon name="bi-exclamation-circle-fill" class="w-4 h-4 mt-0.5 flex-shrink-0" />
                    {{ errors.title }}
                </p>
            </div>

            <!-- Content Field -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-2">
                    <v-icon name="bi-card-text" class="w-4 h-4 text-gray-500" />
                    Content <span class="text-red-500">*</span>
                </label>
                <div class="mt-1">
                    <textarea id="content" v-model="form.content" rows="4"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Write announcement content here..."></textarea>
                </div>
                <p v-if="errors.content" class="mt-2 text-sm text-red-600 flex items-start gap-2">
                    <v-icon name="bi-exclamation-circle-fill" class="w-4 h-4 mt-0.5 flex-shrink-0" />
                    {{ errors.content }}
                </p>
            </div>

            <!-- Date Range Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-2">
                        <v-icon name="bi-calendar-date" class="w-4 h-4 text-gray-500" />
                        Start Date
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <v-icon name="bi-calendar3" class="h-4 w-4 text-gray-400" />
                        </div>
                        <input type="date" id="start_date" v-model="form.start_date"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-2">
                        <v-icon name="bi-calendar-date" class="w-4 h-4 text-gray-500" />
                        End Date
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <v-icon name="bi-calendar3" class="h-4 w-4 text-gray-400" />
                        </div>
                        <input type="date" id="end_date" v-model="form.end_date" :min="form.start_date"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <!-- Status Field -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-2">
                    <v-icon name="bi-circle-fill" class="w-4 h-4 text-gray-500" />
                    Status <span class="text-red-500">*</span>
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <v-icon :name="form.status === 'active' ? 'bi-toggle-on' : 'bi-toggle-off'"
                            class="h-5 w-5 transition-colors duration-200"
                            :class="form.status === 'active' ? 'text-green-500' : 'text-gray-400'" />
                    </div>
                    <select id="status" v-model="form.status"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <template #footer>
            <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end gap-3">
                <button type="button" @click="submitForm"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <v-icon :name="isEditing ? 'ri-save-3-fill' : 'bi-save-fill'" class="-ml-1 mr-2 h-4 w-4" />
                    {{ isEditing ? 'Update' : 'Create' }}
                </button>
                <button type="button" @click="closeModal"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <v-icon name="bi-x-lg" class="-ml-1 mr-2 h-4 w-4" />
                    Cancel
                </button>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue';
import { computed } from 'vue';

const props = defineProps({
    show: Boolean,
    editing: {
        type: Boolean,
        required: true,
        default: false
    },
    form: Object,
    errors: Object
});

const emit = defineEmits(['close', 'submit']);

const isEditing = computed(() => props.editing);

const closeModal = () => {
    emit('close');
};

const submitForm = () => {
    emit('submit');
};
</script>

<style scoped>
/* Custom date input styling */
input[type="date"]::-webkit-calendar-picker-indicator {
    opacity: 0;
    position: absolute;
    width: 100%;
    height: 100%;
    left: 0;
    cursor: pointer;
}

/* Smooth transitions for interactive elements */
button,
input,
select,
textarea {
    transition: all 0.2s ease;
}

/* Focus states */
input:focus,
textarea:focus,
select:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

/* Status toggle icon animation */
.bi-toggle-on,
.bi-toggle-off {
    transition: color 0.3s ease, transform 0.2s ease;
}
</style>

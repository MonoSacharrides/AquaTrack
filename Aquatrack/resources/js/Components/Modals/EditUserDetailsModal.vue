<template>
    <!-- Single transition wrapper for both overlay and panel -->
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-[1000] overflow-hidden">
            <!-- Overlay -->
            <div
                class="absolute inset-0 bg-black/50 transition-opacity duration-300"
                @click="emit('close')"
            ></div>

            <!-- Sliding panel container -->
            <div class="fixed inset-y-0 right-0 w-full max-w-2xl flex">
                <!-- Panel with transform class for animation -->
                <div
                    class="relative w-full h-full transform transition-transform duration-300 ease-in-out"
                >
                    <div class="h-full flex flex-col bg-white shadow-xl">
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between px-4 py-6 bg-gradient-to-r from-[#062F64] to-[#1E4272]"
                        >
                            <div class="flex items-center space-x-2">
                                <v-icon
                                    name="bi-pencil-square"
                                    class="text-amber-300"
                                    scale="1.5"
                                />
                                <span class="text-white font-[200] text-xl"
                                    >Edit User</span
                                >
                            </div>
                            <button
                                @click="emit('close')"
                                class="text-white hover:text-gray-200 transition-colors duration-200 p-1 rounded-full hover:bg-white/10"
                            >
                                <v-icon name="bi-x-lg" class="h-6 w-6" />
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 overflow-y-auto p-4">
                            <div v-if="user" class="space-y-4">
                                <!-- Loading State -->
                                <div
                                    v-if="editLoading"
                                    class="flex items-center justify-center py-8"
                                >
                                    <v-icon
                                        name="bi-arrow-repeat"
                                        class="animate-spin text-blue-500 mr-2"
                                    />
                                    <span>Saving changes...</span>
                                </div>

                                <!-- Error Message -->
                                <div
                                    v-if="editErrorMessage"
                                    class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md"
                                >
                                    {{ editErrorMessage }}
                                </div>

                                <!-- Profile Information -->
                                <div
                                    class="bg-gray-50 border border-gray-200 p-4 rounded-lg shadow-sm"
                                >
                                    <h3
                                        class="text-md font-semibold text-gray-900 mb-3 flex items-center"
                                    >
                                        <v-icon
                                            name="bi-person-badge"
                                            class="mr-2 text-blue-600"
                                        />
                                        Profile Information
                                    </h3>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                    >
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                >First Name</label
                                            >
                                            <input
                                                v-model="editFormData.name"
                                                type="text"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                :class="{
                                                    'border-red-500':
                                                        editErrors.name,
                                                }"
                                            />
                                            <p
                                                v-if="editErrors.name"
                                                class="text-red-500 text-xs mt-1"
                                            >
                                                {{ editErrors.name }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                >Last Name</label
                                            >
                                            <input
                                                v-model="editFormData.lastname"
                                                type="text"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                :class="{
                                                    'border-red-500':
                                                        editErrors.lastname,
                                                }"
                                            />
                                            <p
                                                v-if="editErrors.lastname"
                                                class="text-red-500 text-xs mt-1"
                                            >
                                                {{ editErrors.lastname }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                >Email</label
                                            >
                                            <input
                                                v-model="editFormData.email"
                                                type="email"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                :class="{
                                                    'border-red-500':
                                                        editErrors.email,
                                                }"
                                            />
                                            <p
                                                v-if="editErrors.email"
                                                class="text-red-500 text-xs mt-1"
                                            >
                                                {{ editErrors.email }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                >Phone</label
                                            >
                                            <input
                                                v-model="editFormData.phone"
                                                type="tel"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                :class="{
                                                    'border-red-500':
                                                        editErrors.phone,
                                                }"
                                                placeholder="09XXXXXXXXX or +63XXXXXXXXXX"
                                            />
                                            <p
                                                v-if="editErrors.phone"
                                                class="text-red-500 text-xs mt-1"
                                            >
                                                {{ editErrors.phone }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Account Information -->
                                <div
                                    class="bg-gray-50 border border-gray-200 p-4 rounded-lg shadow-sm"
                                >
                                    <h3
                                        class="text-md font-semibold text-gray-900 mb-3 flex items-center"
                                    >
                                        <v-icon
                                            name="bi-info-circle"
                                            class="mr-2 text-blue-600"
                                        />
                                        Account Information
                                    </h3>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                    >
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                >Account Number</label
                                            >
                                            <input
                                                v-model="
                                                    editFormData.account_number
                                                "
                                                type="text"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-100"
                                                placeholder="XXX-XX-XXX"
                                                readonly
                                            />

                                            <p
                                                class="text-xs text-gray-500 mt-1"
                                            >
                                                Account number cannot be changed
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                >Password Reset</label
                                            >
                                            <div
                                                class="flex items-center space-x-2"
                                            >
                                                <button
                                                    @click="resetPassword"
                                                    :disabled="resetLoading"
                                                    class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 disabled:opacity-50 transition-colors duration-200"
                                                >
                                                    <v-icon
                                                        v-if="resetLoading"
                                                        name="bi-arrow-repeat"
                                                        class="animate-spin mr-1"
                                                    />
                                                    {{
                                                        resetLoading
                                                            ? "Resetting..."
                                                            : "Reset Password"
                                                    }}
                                                </button>
                                            </div>
                                            <p
                                                class="text-xs text-gray-500 mt-1"
                                            >
                                                Reset user's password to default
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Water Meter Information -->
                                <div
                                    class="bg-gray-50 border border-gray-200 p-4 rounded-lg shadow-sm"
                                >
                                    <h3
                                        class="text-md font-semibold text-gray-900 mb-3 flex items-center"
                                    >
                                        <v-icon
                                            name="bi-droplet"
                                            class="mr-2 text-blue-600"
                                        />
                                        Water Meter Information
                                    </h3>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                    >
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                >Serial Number</label
                                            >
                                            <div class="relative">
                                                <input
                                                    v-model="
                                                        editFormData.serial_number
                                                    "
                                                    type="text"
                                                    maxlength="9"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 pr-12"
                                                    :class="{
                                                        'border-red-500':
                                                            editErrors.serial_number,
                                                    }"
                                                    @input="restrictToNumbers"
                                                />
                                                <span
                                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-xs"
                                                    :class="
                                                        editFormData
                                                            .serial_number
                                                            .length === 9
                                                            ? 'text-green-500'
                                                            : 'text-gray-500'
                                                    "
                                                >
                                                    {{
                                                        editFormData
                                                            .serial_number
                                                            .length
                                                    }}/9
                                                </span>
                                            </div>
                                            <p
                                                v-if="editErrors.serial_number"
                                                class="text-red-500 text-xs mt-1"
                                            >
                                                {{ editErrors.serial_number }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                >Meter Size</label
                                            >
                                            <input
                                                v-model="editFormData.size"
                                                type="text"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                :class="{
                                                    'border-red-500':
                                                        editErrors.size,
                                                }"
                                            />
                                            <p
                                                v-if="editErrors.size"
                                                class="text-red-500 text-xs mt-1"
                                            >
                                                {{ editErrors.size }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                >Brand</label
                                            >
                                            <input
                                                v-model="editFormData.brand"
                                                type="text"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                :class="{
                                                    'border-red-500':
                                                        editErrors.brand,
                                                }"
                                            />
                                            <p
                                                v-if="editErrors.brand"
                                                class="text-red-500 text-xs mt-1"
                                            >
                                                {{ editErrors.brand }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                >Date Installed</label
                                            >
                                            <input
                                                v-model="
                                                    editFormData.date_installed
                                                "
                                                type="date"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                :class="{
                                                    'border-red-500':
                                                        editErrors.date_installed,
                                                }"
                                            />
                                            <p
                                                v-if="editErrors.date_installed"
                                                class="text-red-500 text-xs mt-1"
                                            >
                                                {{ editErrors.date_installed }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Location Information -->
                                <div
                                    class="bg-gray-50 border border-gray-200 p-4 rounded-lg shadow-sm"
                                >
                                    <h3
                                        class="text-md font-semibold text-gray-900 mb-3 flex items-center"
                                    >
                                        <v-icon
                                            name="bi-geo-alt"
                                            class="mr-2 text-blue-600"
                                        />
                                        Location Information
                                    </h3>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                    >
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                >Zone</label
                                            >
                                            <select
                                                v-model="editFormData.zone"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                :class="{
                                                    'border-red-500':
                                                        editErrors.zone,
                                                }"
                                            >
                                                <option
                                                    v-for="(
                                                        zone, zoneName
                                                    ) in zones"
                                                    :key="zoneName"
                                                    :value="zoneName"
                                                >
                                                    {{ zoneName }}
                                                </option>
                                            </select>
                                            <p
                                                v-if="editErrors.zone"
                                                class="text-red-500 text-xs mt-1"
                                            >
                                                {{ editErrors.zone }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                >Barangay</label
                                            >
                                            <select
                                                v-model="editFormData.barangay"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                :class="{
                                                    'border-red-500':
                                                        editErrors.barangay,
                                                }"
                                            >
                                                <option
                                                    v-for="barangay in getBarangays(
                                                        editFormData.zone
                                                    )"
                                                    :key="barangay"
                                                    :value="barangay"
                                                >
                                                    {{ barangay }}
                                                </option>
                                            </select>
                                            <p
                                                v-if="editErrors.barangay"
                                                class="text-red-500 text-xs mt-1"
                                            >
                                                {{ editErrors.barangay }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="border-t border-gray-200 px-4 py-3 bg-gray-50 flex justify-end space-x-3"
                        >
                            <button
                                @click="emit('close')"
                                type="button"
                                class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                            >
                                Cancel
                            </button>
                            <button
                                @click="submitForm"
                                :disabled="editLoading"
                                type="button"
                                class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition-colors duration-200"
                            >
                                <v-icon
                                    v-if="editLoading"
                                    name="bi-arrow-repeat"
                                    class="animate-spin mr-2"
                                />
                                {{ editLoading ? "Saving..." : "Save Changes" }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, reactive, watch } from "vue";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    user: {
        type: Object,
        default: null,
    },
    zones: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["close", "saved"]);

const editLoading = ref(false);
const resetLoading = ref(false);
const editErrorMessage = ref("");
const editErrors = reactive({});

const editFormData = reactive({
    name: "",
    lastname: "",
    email: "",
    phone: "",
    account_number: "",
    serial_number: "",
    size: "",
    brand: "",
    date_installed: "",
    zone: "",
    barangay: "",
});

// Watch for user changes and populate form
watch(
    () => props.user,
    (newUser) => {
        if (newUser) {
            // Map all user data to form data
            Object.assign(editFormData, {
                name: newUser.name || "",
                lastname: newUser.lastname || "",
                email: newUser.email || "",
                phone: newUser.phone || "",
                account_number: newUser.account_number || "",
                serial_number: newUser.serial_number || "",
                size: newUser.size || "",
                brand: newUser.brand || "",
                date_installed: newUser.date_installed
                    ? formatDateForInput(newUser.date_installed)
                    : "",
                zone: newUser.zone || "",
                barangay: newUser.barangay || "",
            });
        }
    },
    { immediate: true }
);

// Helper function to format date for input
const formatDateForInput = (dateString) => {
    const date = new Date(dateString);
    return date.toISOString().split("T")[0];
};

// Restrict serial number input to numbers only
const restrictToNumbers = (event) => {
    editFormData.serial_number = editFormData.serial_number.replace(
        /[^0-9]/g,
        ""
    );
};

const getBarangays = (zone) => {
    return props.zones[zone] || [];
};

const resetPassword = async () => {
    if (!props.user?.id) return;

    const { value: newPassword } = await Swal.fire({
        title: "Reset Password",
        input: "text",
        inputLabel: "Enter new password",
        inputPlaceholder: "Enter the new password",
        showCancelButton: true,
        confirmButtonText: "Reset Password",
        cancelButtonText: "Cancel",
        inputValidator: (value) => {
            if (!value) {
                return "You need to enter a password!";
            }
            if (value.length < 6) {
                return "Password must be at least 6 characters!";
            }
        },
    });

    if (newPassword) {
        resetLoading.value = true;

        try {
            await router.post(
                `/admin/users/${props.user.id}/reset-password`,
                {
                    password: newPassword,
                },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire({
                            icon: "success",
                            title: "Password Reset",
                            text: "Password has been reset successfully.",
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 3000,
                        });
                        resetLoading.value = false;
                    },
                    onError: (err) => {
                        Swal.fire({
                            icon: "error",
                            title: "Reset Failed",
                            text:
                                err.message ||
                                "Failed to reset password. Please try again.",
                        });
                        resetLoading.value = false;
                    },
                }
            );
        } catch (error) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "An unexpected error occurred.",
            });
            resetLoading.value = false;
        }
    }
};

const submitForm = async () => {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to save these changes?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, save it!",
        cancelButtonText: "Cancel",
    }).then(async (result) => {
        if (result.isConfirmed) {
            editLoading.value = true;
            editErrorMessage.value = "";
            Object.keys(editErrors).forEach((key) => delete editErrors[key]);

            try {
                await router.put(
                    `/admin/users/${props.user.id}`,
                    editFormData,
                    {
                        preserveScroll: true,
                        onSuccess: () => {
                            Swal.fire({
                                icon: "success",
                                title: "Saved!",
                                text: "User information has been updated successfully.",
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                                timer: 3000,
                            });

                            emit("saved");
                            emit("close");
                            editLoading.value = false;
                        },
                        onError: (err) => {
                            console.log("Update errors:", err);
                            if (err.errors) {
                                Object.keys(err.errors).forEach((key) => {
                                    editErrors[key] = err.errors[key];
                                });
                            }
                            editErrorMessage.value =
                                err.message ||
                                "Failed to update user. Please check the form.";
                            editLoading.value = false;
                        },
                    }
                );
            } catch (error) {
                editErrorMessage.value = "An unexpected error occurred.";
                editLoading.value = false;
            }
        }
    });
};
</script>

<style scoped>
/* Custom scrollbar for the content */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #e2e8f0 #f8fafc;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f8fafc;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #e2e8f0;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: #cbd5e1;
}

/* Modal transition styles */
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
</style>

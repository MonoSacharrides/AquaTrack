<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import StaffLayout from "@/Layouts/StaffLayout.vue";
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import { Head, useForm, usePage, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import Swal from "sweetalert2";

const { props } = usePage();
const user = computed(() => props.auth.user);

// Determine user type
const userType = computed(() => {
    if (user.value.roles?.includes("admin")) return "admin";
    if (user.value.roles?.includes("staff")) return "staff";
    return user.value.type || "customer";
});

// Dashboard route computed property - FIXED
const getDashboardRoute = computed(() => {
    switch (userType.value) {
        case "admin":
            return "admin.dashboard";
        case "staff":
            return "staff.dashboard";
        case "customer":
            return "customer.dashboard"; // Fixed this line
        default:
            return "dashboard";
    }
});

// Profile Information Form
const profileForm = useForm({
    name: user.value.name,
    email: user.value.email,
    avatar: null,
});

const avatarPreview = ref(user.value.avatar_url || "");
const avatarInput = ref(null);

const handleAvatarChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    avatarPreview.value = URL.createObjectURL(file);
    profileForm.avatar = file;
};

const updateProfile = () => {
    profileForm.post("/profile", {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            Swal.fire({
                position: "top-end",
                icon: "success",
                title: "Profile updated successfully!",
                showConfirmButton: false,
                timer: 1500,
                toast: true,
                timerProgressBar: true,
            }).then(() => {
                window.location.reload();
            });
        },
    });
};

// Password Form
const passwordForm = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const updatePassword = () => {
    passwordForm.put("/password", {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                position: "top-end",
                icon: "success",
                title: "Password updated successfully!",
                showConfirmButton: false,
                timer: 2000,
                toast: true,
                timerProgressBar: true,
                background: "#f8f9fa",
                width: "400px",
            });
            passwordForm.reset();
        },
        onError: () => {
            Swal.fire({
                position: "top-end",
                icon: "error",
                title: "Error updating password",
                showConfirmButton: false,
                timer: 2000,
                toast: true,
                background: "#f8f9fa",
                width: "400px",
            });
        },
    });
};

// Dynamic layout selection
const layout = computed(() => {
    return {
        admin: AdminLayout,
        staff: StaffLayout,
        customer: CustomerLayout,
    }[userType.value];
});
</script>

<template>
    <component :is="layout">
        <Head title="Profile Settings" />

        <div class="px-4 sm:px-0">
            <div class="flex flex-col space-y-6">
                <!-- Profile Information -->
                <div
                    class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100"
                >
                    <div class="p-6">
                        <div
                            class="flex items-center justify-between space-x-3 mb-4"
                        >
                            <h3 class="text-xl font-semibold text-gray-800">
                                Profile Information
                            </h3>

                            <div class="mb-6">
                                <button
                                    @click="
                                        $inertia.visit(route(getDashboardRoute))
                                    "
                                    class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg"
                                >
                                    <v-icon name="bi-arrow-left" class="mr-2" />
                                    Back to Dashboard
                                </button>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-600 mb-6">
                            Update your account's profile information and email
                            address.
                        </p>

                        <!-- Profile Form -->
                        <form @submit.prevent="updateProfile">
                            <!-- Avatar Upload -->
                            <div class="flex items-center space-x-6 mb-6">
                                <div class="shrink-0">
                                    <img
                                        v-if="avatarPreview"
                                        class="h-20 w-20 rounded-full object-cover"
                                        :src="avatarPreview"
                                        alt="Current avatar"
                                    />
                                    <div
                                        v-else
                                        class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center"
                                    >
                                        <v-icon
                                            name="bi-person-fill"
                                            class="text-gray-400 text-3xl"
                                        />
                                    </div>
                                </div>
                                <label class="block">
                                    <span class="sr-only"
                                        >Choose profile photo</span
                                    >
                                    <input
                                        @change="handleAvatarChange"
                                        ref="avatarInput"
                                        type="file"
                                        accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                    />
                                </label>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <div
                                        class="flex items-center space-x-2 mb-2"
                                    >
                                        <v-icon
                                            name="bi-person-fill"
                                            class="text-gray-500"
                                        />
                                        <label
                                            for="name"
                                            class="block text-sm font-medium text-gray-700"
                                            >Name</label
                                        >
                                    </div>
                                    <input
                                        v-model="profileForm.name"
                                        id="name"
                                        type="text"
                                        autocomplete="name"
                                        required
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                                    />
                                    <p
                                        v-if="profileForm.errors.name"
                                        class="mt-2 text-sm text-red-600"
                                    >
                                        {{ profileForm.errors.name }}
                                    </p>
                                </div>

                                <div>
                                    <div
                                        class="flex items-center space-x-2 mb-2"
                                    >
                                        <v-icon
                                            name="bi-envelope-fill"
                                            class="text-gray-500"
                                        />
                                        <label
                                            for="email"
                                            class="block text-sm font-medium text-gray-700"
                                            >Email</label
                                        >
                                    </div>
                                    <input
                                        v-model="profileForm.email"
                                        id="email"
                                        type="email"
                                        autocomplete="email"
                                        placeholder="Optional"
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                                    />
                                    <p
                                        v-if="profileForm.errors.email"
                                        class="mt-2 text-sm text-red-600"
                                    >
                                        {{ profileForm.errors.email }}
                                    </p>
                                </div>

                                <div class="pt-4">
                                    <button
                                        type="submit"
                                        :disabled="profileForm.processing"
                                        class="flex items-center justify-center w-full px-6 py-3 text-base font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition duration-150"
                                    >
                                        <v-icon
                                            name="bi-check-circle-fill"
                                            class="mr-2"
                                        />
                                        Save Profile
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Update Password -->
                <div
                    class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100"
                >
                    <div class="p-6">
                        <div class="flex items-center space-x-3 mb-4">
                            <v-icon
                                name="bi-lock-fill"
                                class="text-green-500 text-xl"
                            />
                            <h3 class="text-xl font-semibold text-gray-800">
                                Update Password
                            </h3>
                        </div>
                        <p class="mt-1 text-sm text-gray-600 mb-6">
                            Ensure your account is using a long, random password
                            to stay secure.
                        </p>

                        <!-- Password Form -->
                        <form @submit.prevent="updatePassword">
                            <div class="space-y-6">
                                <div>
                                    <div
                                        class="flex items-center space-x-2 mb-2"
                                    >
                                        <v-icon
                                            name="bi-key-fill"
                                            class="text-gray-500"
                                        />
                                        <label
                                            for="current_password"
                                            class="block text-sm font-medium text-gray-700"
                                            >Current Password</label
                                        >
                                    </div>
                                    <input
                                        v-model="passwordForm.current_password"
                                        id="current_password"
                                        type="password"
                                        autocomplete="current-password"
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-150"
                                    />
                                    <p
                                        v-if="
                                            passwordForm.errors.current_password
                                        "
                                        class="mt-2 text-sm text-red-600"
                                    >
                                        {{
                                            passwordForm.errors.current_password
                                        }}
                                    </p>
                                </div>

                                <div>
                                    <div
                                        class="flex items-center space-x-2 mb-2"
                                    >
                                        <v-icon
                                            name="bi-shield-lock-fill"
                                            class="text-gray-500"
                                        />
                                        <label
                                            for="password"
                                            class="block text-sm font-medium text-gray-700"
                                            >New Password</label
                                        >
                                    </div>
                                    <input
                                        v-model="passwordForm.password"
                                        id="password"
                                        type="password"
                                        autocomplete="new-password"
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-150"
                                    />
                                    <p
                                        v-if="passwordForm.errors.password"
                                        class="mt-2 text-sm text-red-600"
                                    >
                                        {{ passwordForm.errors.password }}
                                    </p>
                                </div>

                                <div>
                                    <div
                                        class="flex items-center space-x-2 mb-2"
                                    >
                                        <v-icon
                                            name="bi-shield-fill-check"
                                            class="text-gray-500"
                                        />
                                        <label
                                            for="password_confirmation"
                                            class="block text-sm font-medium text-gray-700"
                                            >Confirm Password</label
                                        >
                                    </div>
                                    <input
                                        v-model="
                                            passwordForm.password_confirmation
                                        "
                                        id="password_confirmation"
                                        type="password"
                                        autocomplete="new-password"
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-150"
                                    />
                                </div>

                                <div class="pt-4">
                                    <button
                                        type="submit"
                                        :disabled="passwordForm.processing"
                                        class="flex items-center justify-center w-full px-6 py-3 text-base font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 transition duration-150"
                                    >
                                        <v-icon
                                            name="bi-arrow-repeat"
                                            class="mr-2"
                                        />
                                        Update Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </component>
</template>
<style scoped>
/* Smooth transitions for form elements */
input {
    transition: all 0.2s ease;
}

/* Hover effects for buttons */
button:not(:disabled):hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Focus styles for accessibility */
input:focus,
button:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}

/* Avatar specific styles */
.avatar-upload {
    transition: all 0.3s ease;
}

.avatar-upload:hover {
    transform: scale(1.05);
}

.avatar-preview {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.avatar-preview:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* File input styling */
.file\:bg-blue-50 {
    background-color: #eff6ff;
}

.file\:text-blue-700 {
    color: #1d4ed8;
}

.hover\:file\:bg-blue-100:hover {
    background-color: #dbeafe;
}
</style>

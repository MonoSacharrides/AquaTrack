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
                                    name="bi-person"
                                    class="text-amber-300"
                                    scale="1.5"
                                />
                                <span class="text-white font-[200] text-xl"
                                    >User Details</span
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
                            <div v-if="user" class="space-y-4 text-sm">
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
                                    <div class="flex items-center mb-4">
                                        <div class="flex-shrink-0 h-16 w-16">
                                            <img
                                                v-if="user.avatar_url"
                                                :src="user.avatar_url"
                                                :alt="user.name"
                                                class="h-16 w-16 rounded-full object-cover border-2 border-white shadow"
                                            />
                                            <div
                                                v-else
                                                class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center border-2 border-white shadow"
                                            >
                                                <span
                                                    class="text-blue-600 text-xl font-semibold"
                                                    >{{
                                                        userInitials(user.name)
                                                    }}</span
                                                >

                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <h2
                                                class="text-lg font-bold text-gray-900"
                                            >
                                                {{ user.name }} {{ user.lastname }}
                                            </h2>
                                            <p class="text-sm text-gray-500">
                                                {{ user.email }}
                                            </p>
                                        </div>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-3"
                                    >
                                        <div class="flex items-start">
                                            <v-icon
                                                name="bi-person"
                                                class="mr-2 mt-0.5 text-blue-500"
                                            />
                                            <div>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Full Name
                                                </p>
                                                <p class="font-medium">
                                                    {{ user.name }}
                                                    {{ user.lastname || "" }}
                                                </p>
                                            </div>
                                        </div>
                                        <!-- <div class="flex items-start">
                                            <v-icon
                                                name="bi-envelope"
                                                class="mr-2 mt-0.5 text-blue-500"
                                            />
                                            <div>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Email Address
                                                </p>
                                                <p class="font-medium">
                                                    {{ user.email }}
                                                </p>
                                            </div>
                                        </div> -->
                                        <div class="flex items-start">
                                            <v-icon
                                                name="bi-telephone"
                                                class="mr-2 mt-0.5 text-blue-500"
                                            />
                                            <div>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Phone Number
                                                </p>
                                                <p class="font-medium">
                                                    {{ user.phone || "N/A" }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <v-icon
                                                name="bi-tag"
                                                class="mr-2 mt-0.5 text-blue-500"
                                            />
                                            <div>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Account Number
                                                </p>
                                                <p class="font-medium">
                                                    {{
                                                        user.account_number ||
                                                        "N/A"
                                                    }}
                                                </p>
                                            </div>
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
                                        class="grid grid-cols-1 md:grid-cols-2 gap-3"
                                    >
                                        <div class="flex items-start">
                                            <v-icon
                                                name="bi-person-check"
                                                class="mr-2 mt-0.5 text-blue-500"
                                            />
                                            <div>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    User ID
                                                </p>
                                                <p class="font-medium">
                                                    {{ user.id }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <v-icon
                                                name="bi-shield-check"
                                                class="mr-2 mt-0.5 text-blue-500"
                                            />
                                            <div>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Role
                                                </p>
                                                <span
                                                    :class="
                                                        roleClasses(user.role)
                                                    "
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold"
                                                >
                                                    <v-icon
                                                        :name="
                                                            roleIcon(user.role)
                                                        "
                                                        class="w-3 h-3 mr-1"
                                                    />
                                                    {{
                                                        capitalizeRole(
                                                            user.role
                                                        )
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <v-icon
                                                name="bi-check-circle"
                                                class="mr-2 mt-0.5 text-blue-500"
                                            />
                                            <div>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Status
                                                </p>
                                                <span
                                                    :class="
                                                        statusClasses(
                                                            user.enabled
                                                        )
                                                    "
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold"
                                                >
                                                    <v-icon
                                                        :name="
                                                            user.enabled
                                                                ? 'bi-check-circle'
                                                                : 'bi-x-circle'
                                                        "
                                                        class="w-3 h-3 mr-1"
                                                    />
                                                    {{
                                                        user.enabled
                                                            ? "Active"
                                                            : "Inactive"
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                        <!-- <div class="flex items-start">
                                            <v-icon
                                                name="bi-calendar"
                                                class="mr-2 mt-0.5 text-blue-500"
                                            />
                                            <div>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Member Since
                                                </p>
                                                <p class="font-medium">
                                                    {{ formatDate(user.created_at) }}
                                                </p>
                                            </div>
                                        </div> -->
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
                                        class="grid grid-cols-1 md:grid-cols-2 gap-3"
                                    >
                                        <div class="flex items-start">
                                            <v-icon
                                                name="bi-upc-scan"
                                                class="mr-2 mt-0.5 text-blue-500"
                                            />
                                            <div>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Serial Number
                                                </p>
                                                <p class="font-medium">
                                                    {{
                                                        user.serial_number ||
                                                        "N/A"
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <v-icon
                                                name="bi-rulers"
                                                class="mr-2 mt-0.5 text-blue-500"
                                            />
                                            <div>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Meter Size
                                                </p>
                                                <p class="font-medium">
                                                    {{ user.size || "N/A" }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <v-icon
                                                name="bi-building"
                                                class="mr-2 mt-0.5 text-blue-500"
                                            />
                                            <div>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Brand
                                                </p>
                                                <p class="font-medium">
                                                    {{ user.brand || "N/A" }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <v-icon
                                                name="bi-calendar-check"
                                                class="mr-2 mt-0.5 text-blue-500"
                                            />
                                            <div>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Date Installed
                                                </p>
                                                <p class="font-medium">
                                                    {{
                                                        user.date_installed
                                                            ? formatDate(
                                                                  user.date_installed
                                                              )
                                                            : "N/A"
                                                    }}
                                                </p>
                                            </div>
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
                                        class="grid grid-cols-1 md:grid-cols-2 gap-3"
                                    >
                                        <div class="flex items-start">
                                            <v-icon
                                                name="bi-geo"
                                                class="mr-2 mt-0.5 text-blue-500"
                                            />
                                            <div>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Zone
                                                </p>
                                                <p class="font-medium">
                                                    {{ user.zone || "N/A" }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <v-icon
                                                name="bi-signpost"
                                                class="mr-2 mt-0.5 text-blue-500"
                                            />
                                            <div>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Barangay
                                                </p>
                                                <p class="font-medium">
                                                    {{ user.barangay || "N/A" }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="border-t border-gray-200 px-4 py-3 bg-gray-50"
                        >
                            <button
                                @click="emit('close')"
                                type="button"
                                class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                            >
                                <v-icon name="bi-x-lg" class="mr-2" />
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, computed } from "vue";

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    user: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["close"]);

const userInitials = (name) => {
    if (!name) return "?";
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .substring(0, 2);
};

const roleClasses = (role) => {
    return {
        "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200":
            role === "admin",
        "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200":
            role === "customer",
        "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200":
            role === "staff",
    };
};

const statusClasses = (enabled) => {
    return {
        "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200":
            enabled,
        "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200": !enabled,
    };
};

const roleIcon = (role) => {
    if (role === "admin") return "bi-shield-check";
    if (role === "staff") return "bi-person-circle";
    if (role === "customer") return "bi-people";
    return "bi-person";
};

const capitalizeRole = (role) => {
    if (!role) return "";
    return role.charAt(0).toUpperCase() + role.slice(1);
};

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
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

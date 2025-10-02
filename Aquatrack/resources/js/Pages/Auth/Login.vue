//Login.vue
<script setup>
import { useForm, Link, usePage } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted } from "vue";
import { OhVueIcon } from "oh-vue-icons";
import Swal from "sweetalert2";
import axios from "axios";
import gsap from "gsap";

const props = defineProps({
    selectedRole: { type: String, default: "" },
    canResetPassword: { type: Boolean, default: true },
    status: { type: String },
    auth: { type: Object, default: () => ({ user: null }) },
});

const emit = defineEmits(["close", "login-success"]);

const showPassword = ref(false);
const codeVerified = ref(false);
const verificationCode = ref("");
const verificationError = ref("");
const isVerifying = ref(false);
const isSubmitting = ref(false); // Added for login submission

const securedRoles = ["admin", "staff"];
const requiresVerification = computed(() =>
    securedRoles.includes(props.selectedRole.toLowerCase())
);

const form = useForm({
    email: "",
    account_number: "",
    password: "",
    remember: false,
    role: props.selectedRole ? props.selectedRole.toLowerCase() : "customer",
});

// --- Lockout state ---
const formDisabled = ref(false);
const lockoutSeconds = ref(0);
let lockoutTimer = null;

// Reset code verification when selected role changes
watch(
    () => props.selectedRole,
    () => {
        codeVerified.value = false;
        verificationCode.value = "";
    }
);

// Handle logout success flash message
const page = usePage();
watch(
    () => page.props.flash,
    (flash) => {
        if (flash.logout_success) {
            Swal.fire({
                position: "top-end",
                icon: "success",
                title: "Logged Out",
                text: "You have been successfully logged out.",
                showConfirmButton: false,
                timer: 2000,
                toast: true,
                background: "#f8f9fa",
                width: "400px",
                customClass: { title: "text-lg font-medium" },
            });
        }
    },
    { immediate: true }
);

// Animate form content when mounted
onMounted(() => {
    gsap.fromTo(
        ".login-form-content",
        { opacity: 0, y: 20 },
        { opacity: 1, y: 0, duration: 0.4, ease: "power2.out" }
    );
});

const updateCsrfToken = (newToken) => {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag && newToken) metaTag.setAttribute("content", newToken);
};

const getCsrfToken = () =>
    document.querySelector('meta[name="csrf-token"]')?.content || "";

const verifyCode = async () => {
    verificationError.value = "";
    isVerifying.value = true;
    const startTime = Date.now();

    try {
        // Add minimum 2-second delay for realistic feel
        await new Promise((resolve) => setTimeout(resolve, 2000));

        const response = await axios.post(
            route("verify-code"),
            {
                role: props.selectedRole.toLowerCase(),
                code: verificationCode.value,
            },
            {
                headers: {
                    "X-CSRF-TOKEN": getCsrfToken(),
                    "X-Requested-With": "XMLHttpRequest",
                },
            }
        );

        if (response.data.csrf_token) {
            updateCsrfToken(response.data.csrf_token);
        }

        if (response.data.verified) {
            codeVerified.value = true;
            Swal.fire({
                position: "top-end",
                icon: "success",
                title: "Verification Successful!",
                text: `You can now proceed to login as ${props.selectedRole}`,
                showConfirmButton: false,
                timer: 2000,
                toast: true,
                background: "#f8f9fa",
                width: "400px",
                customClass: { title: "text-lg font-medium" },
            });
        }
    } catch (error) {
        if (error.response?.status === 419) {
            updateCsrfToken(error.response.data.csrf_token);
            verificationError.value =
                "Session expired. Please refresh the page and try again.";
            Swal.fire({
                position: "top-end",
                icon: "error",
                title: "Session Expired",
                text: verificationError.value,
                showConfirmButton: true,
                confirmButtonText: "Refresh",
                toast: true,
                background: "#f8f9fa",
                width: "400px",
                customClass: { title: "text-lg font-medium" },
            }).then((result) => {
                if (result.isConfirmed) window.location.reload();
            });
        } else {
            verificationError.value =
                error.response?.data?.message ||
                error.response?.data?.errors?.code?.[0] ||
                "The verification code you entered is incorrect";
            Swal.fire({
                position: "top-end",
                icon: "error",
                title: "Verification Failed",
                text: verificationError.value,
                showConfirmButton: false,
                timer: 2000,
                toast: true,
                background: "#f8f9fa",
                width: "400px",
                customClass: { title: "text-lg font-medium" },
            });
        }
    } finally {
        const elapsed = Date.now() - startTime;
        const minimumDelay = 2000;
        if (elapsed < minimumDelay) {
            await new Promise((r) => setTimeout(r, minimumDelay - elapsed));
        }
        isVerifying.value = false;
    }
};

const formatAccountNumber = (event) => {
    let input = event.target.value.replace(/\D/g, ""); // Remove non-digits

    // Format as XXX-XX-XXX
    if (input.length > 5) {
        form.account_number =
            input.slice(0, 3) +
            "-" +
            input.slice(3, 5) +
            "-" +
            input.slice(5, 8);
    } else if (input.length > 3) {
        form.account_number = input.slice(0, 3) + "-" + input.slice(3, 5);
    } else {
        form.account_number = input;
    }
};

const submit = async () => {
    // Add 2-second delay before processing
    isSubmitting.value = true;
    await new Promise((resolve) => setTimeout(resolve, 2000));

    form.post(route("login"), {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            form.reset("password");
            isSubmitting.value = false;
        },
        onSuccess: () => {
            const userName = page.props.auth?.user?.name || "there";
            Swal.fire({
                position: "top-end",
                icon: "success",
                title: `Welcome back, ${userName}!`,
                text: `You've successfully logged in as ${
                    props.selectedRole || "customer"
                }`,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                toast: true,
                background: "#f8f9fa",
                width: "300px", // Reduced from 400px
                customClass: {
                    title: "text-sm font-medium", // Smaller font
                    content: "text-xs", // Smaller text
                    popup: "p-2", // Reduce padding
                },
            }).then(() => emit("login-success"));
        },
        onError: (errors) => {
            isSubmitting.value = false;

            if (errors.throttle) {
                lockoutSeconds.value = errors.remaining_time || 30;
                formDisabled.value = true;

                if (lockoutTimer) clearInterval(lockoutTimer);
                lockoutTimer = setInterval(() => {
                    lockoutSeconds.value--;
                    if (lockoutSeconds.value <= 0) {
                        clearInterval(lockoutTimer);
                        formDisabled.value = false;
                    }
                }, 1000);
                return;
            }

            let errorMessage =
                errors.email ||
                errors.account_number || // Added account_number error handling
                "Invalid credentials. Please try again";
            Swal.fire({
                position: "top-end",
                icon: "error",
                title: "Login Failed",
                text: errorMessage,
                showConfirmButton: false,
                timer: 3000,
                toast: true,
                width: "300px",
                customClass: {
                    title: "text-sm",
                    htmlContainer: "text-xs",
                },
            });
        },
    });
};
</script>

<template>
    <div class="flex flex-col md:flex-row h-full bg-white">
        <!-- Left Side -->
        <div
            class="hidden md:flex md:w-1/2 bg-gradient-to-br from-[#062F64] to-[#1E4272] items-center justify-center p-12"
        >
            <div class="text-center space-y-6">
                <img
                    src="/images/MainLogo.png"
                    class="w-64 h-64 mx-auto object-contain"
                    alt="Logo"
                />
                <div class="space-y-2">
                    <h1
                        class="text-transparent text-3xl font-semibold uppercase tracking-tighter bg-clip-text bg-gradient-to-r from-blue-300 to-teal-200"
                    >
                        AquaTrack
                    </h1>
                    <p class="text-blue-100 text-lg">Water Management System</p>
                </div>
            </div>
        </div>

        <!-- Right Side -->
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
            <div class="text-center mb-8 login-form-content">
                <h2 class="text-2xl text-gray-800 mb-2">
                    Login as {{ selectedRole || "Customer" }}
                </h2>
                <p class="text-gray-600">Enter your credentials to continue</p>
            </div>

            <!-- Verification -->
            <div
                v-if="requiresVerification && !codeVerified"
                class="space-y-6 login-form-content"
            >
                <div class="relative">
                    <input
                        id="verificationCode"
                        v-model="verificationCode"
                        type="password"
                        required
                        class="w-full px-4 py-3 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent peer"
                        placeholder=" "
                    />
                    <label
                        for="verificationCode"
                        class="absolute left-3 top-3 text-gray-500 transition-all duration-200 transform peer-focus:-translate-y-5 peer-focus:scale-75 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 bg-white px-1"
                    >
                        {{ selectedRole }} Verification Code
                    </label>
                </div>
                <div v-if="verificationError" class="text-red-500 text-sm mt-1">
                    {{ verificationError }}
                </div>
                <button
                    @click="verifyCode"
                    type="button"
                    :disabled="isVerifying"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-md transition-colors duration-200 flex items-center justify-center gap-2"
                    :class="{ 'opacity-75 cursor-not-allowed': isVerifying }"
                >
                    <template v-if="isVerifying">
                        <div class="spinner-small"></div>
                        Verifying...
                    </template>
                    <template v-else> Verify Code </template>
                </button>
            </div>

            <!-- Login -->
            <form
                v-if="!requiresVerification || codeVerified"
                @submit.prevent="submit"
                class="space-y-6 login-form-content"
            >
                <div class="relative">
                    <template v-if="form.role !== 'customer'">
                        <!-- Email -->
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            class="w-full px-4 py-3 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 peer"
                            placeholder=" "
                            :disabled="formDisabled"
                        />
                        <label
                            for="email"
                            class="absolute left-3 top-3 text-gray-500 transition-all duration-200 transform peer-focus:-translate-y-5 peer-focus:scale-75 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 bg-white px-1"
                        >
                            Email Address
                        </label>
                    </template>

                    <!-- Serial Number for Customers -->
                    <template v-else>
                        <input
                            id="account_number"
                            v-model="form.account_number"
                            type="text"
                            :disabled="formDisabled"
                            @input="formatAccountNumber"
                            required
                            class="w-full px-4 py-3 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 peer"
                            placeholder=" "
                            maxlength="10"
                        />
                        <label
                            for="account_number"
                            class="absolute left-3 top-3 text-gray-500 transition-all duration-200 transform peer-focus:-translate-y-5 peer-focus:scale-75 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 bg-white px-1"
                        >
                            Account Number (XXX-XX-XXX)
                        </label>
                    </template>
                </div>

                <!-- Password -->
                <div class="relative">
                    <input
                        id="password"
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        required
                        class="w-full px-4 py-3 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 peer"
                        placeholder=" "
                        :disabled="formDisabled"
                    />
                    <label
                        for="password"
                        class="absolute left-3 top-3 text-gray-500 transition-all duration-200 transform peer-focus:-translate-y-5 peer-focus:scale-75 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 bg-white px-1"
                    >
                        Password
                    </label>
                    <button
                        type="button"
                        class="absolute right-3 top-3.5"
                        @click="showPassword = !showPassword"
                    >
                        <OhVueIcon
                            :name="showPassword ? 'bi-eye-slash' : 'bi-eye'"
                            class="text-gray-500 hover:text-gray-700"
                        />
                    </button>

                    <div
                        v-if="lockoutSeconds > 0"
                        class="border-l-4 border-red-500 bg-red-50 p-4 mb-4 rounded-r-md shadow-sm mt-3"
                    >
                        <div class="flex items-start">
                            <svg
                                class="w-5 h-5 text-red-400 mt-0.5 mr-3"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                            <div>
                                <h3 class="text-red-800 font-medium text-sm">
                                    Security Lockout
                                </h3>
                                <p class="text-red-700 text-sm mt-1">
                                    Please wait
                                    <span class="font-semibold"
                                        >{{ lockoutSeconds }} seconds</span
                                    >
                                    before attempting again.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    :disabled="form.processing || formDisabled || isSubmitting"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-md transition-colors duration-200 flex items-center justify-center gap-2"
                    :class="{
                        'opacity-75 cursor-not-allowed':
                            form.processing || formDisabled || isSubmitting,
                    }"
                >
                    <template v-if="form.processing || isSubmitting">
                        <div class="spinner-small"></div>
                        Signing in...
                    </template>
                    <template v-else>
                        <OhVueIcon name="md-login-outlined" class="text-lg" />
                        Sign In
                    </template>
                </button>
            </form>

            <div
                class="mt-6 text-sm text-[#1C606A] border border-[#D7F1F5] p-4 bg-[#D7F1F5] rounded-md login-form-content"
            >
                If you do not know your account credentials, or if you have
                forgotten your password, please contact the Systems Development
                & Administration Office.
            </div>
        </div>
    </div>
</template>

<style scoped>
input:focus ~ label,
input:not(:placeholder-shown) ~ label {
    transform: translateY(-1.5rem) scale(0.75);
    color: #2563eb;
}
input::placeholder {
    color: transparent;
}

/* Spinner styles */
.spinner-small {
    width: 16px;
    height: 16px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>

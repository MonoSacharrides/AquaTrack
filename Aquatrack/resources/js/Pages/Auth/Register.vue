<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { OhVueIcon } from 'oh-vue-icons';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const toggleConfirmPasswordVisibility = () => {
    showConfirmPassword.value = !showConfirmPassword.value;
};

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Register" />

        <div
            class="max-w-4xl w-full bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col md:flex-row h-full md:h-auto">
            <!-- Left Side - Logo -->
            <div
                class="hidden md:flex md:w-1/2 flex-col items-center justify-center p-12 bg-gradient-to-r from-blue-500 via-blue-400 to-blue-300">
                <img src="/images/MainLogo.png" class="max-w-full max-h-full h-[300px] w-[300px] object-contain" alt="">

                <h1 class="text-white text-3xl font-bold mt-6">AquaTrack</h1>
                <p class="text-blue-100 mt-2 text-center text-lg">
                    Smart Water Management System
                </p>

            </div>

            <!-- Right Side - Form -->
            <div class="w-full md:w-1/2 py-10 px-6 md:px-12">
                <div class="md:hidden mb-8 text-center">
                    <ApplicationLogo :size="32" class="fill-current text-blue-600 mx-auto" />
                    <h1 class="text-3xl font-bold text-gray-800 mt-4">AquaTrack</h1>
                    <p class="text-blue-500 mt-2 text-lg">
                        Smart Water Management System
                    </p>
                </div>

                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Create an Account</h2>
                    <p class="text-gray-600">Join AquaTrack to manage your water usage</p>
                </div>

                <form @submit.prevent="submit" class="w-full">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-1" for="name">
                            Name
                        </label>
                        <input id="name" type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            v-model="form.name" required autofocus autocomplete="name" />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-1" for="email">
                            Email Address
                        </label>
                        <input id="email" type="email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            v-model="form.email" required autocomplete="username" />
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-1" for="password">
                            Password
                        </label>
                        <div class="relative">
                            <input id="password" :type="showPassword ? 'text' : 'password'"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                v-model="form.password" required autocomplete="new-password"
                                placeholder="Enter your password" />
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                @click="togglePasswordVisibility">
                                <OhVueIcon :name="showPassword ? 'bi-eye-slash' : 'bi-eye'"
                                    class="text-gray-500 hover:text-gray-700" />
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-medium mb-1" for="password_confirmation">
                            Confirm Password
                        </label>
                        <div class="relative">
                            <input id="password_confirmation" :type="showConfirmPassword ? 'text' : 'password'"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                v-model="form.password_confirmation" required autocomplete="new-password"
                                placeholder="Confirm your password" />
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                @click="toggleConfirmPasswordVisibility">
                                <OhVueIcon :name="showConfirmPassword ? 'bi-eye-slash' : 'bi-eye'"
                                    class="text-gray-500 hover:text-gray-700" />
                            </button>
                        </div>
                        <p v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-600">
                            {{ form.errors.password_confirmation }}
                        </p>
                    </div>

                    <div class="mb-6">
                        <button
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150"
                            type="submit" :disabled="form.processing">
                            Register
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="text-gray-600 text-sm">
                            Already have an account?
                            <Link :href="route('login')" class="text-blue-600 hover:text-blue-800 font-medium">
                            Sign in here
                            </Link>
                        </p>
                    </div>
                </form>

                <div class="mt-6 text-center text-gray-500 text-xs">
                    Powered by Local Water Utilities Administration (LWUA)
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

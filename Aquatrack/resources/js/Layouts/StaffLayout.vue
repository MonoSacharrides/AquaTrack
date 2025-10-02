<template>
    <div
        class="antialiased min-h-screen bg-[#FFFFFF] dark:bg-gray-900 flex flex-col"
    >
        <DashboardSidebar
            :links="staffLinks"
            :is-open="isSidebarOpen"
            :is-mobile-menu-open="isMobileMenuOpen"
            @toggle-sidebar="toggleSidebar"
            @toggle-mobile-menu="toggleMobileMenu"
        />
        <MainContentNavbar
            :is-sidebar-open="isSidebarOpen"
            :is-mobile-menu-open="isMobileMenuOpen"
            @toggle-sidebar="toggleSidebar"
            @toggle-mobile-menu="toggleMobileMenu"
            @logout="handleLogout"
        />
        <main
            :class="[
                'p-4 pt-[100px] bg-gray-100 flex-1 transition-all duration-300 ease-in-out',
                { 'md:ml-64': isSidebarOpen, 'md:ml-16': !isSidebarOpen },
            ]"
        >
            <slot />
        </main>
    </div>
</template>

<script setup>
import DashboardSidebar from "@/Components/Sidebar/DashboardSidebar.vue";
import MainContentNavbar from "@/Components/Header/MainContentNavbar.vue";
import { ref } from "vue";
import { useForm, router } from "@inertiajs/vue3";

import Swal from "sweetalert2";

const isSidebarOpen = ref(true);
const isMobileMenuOpen = ref(false);

const staffLinks = [
    { name: "Dashboard", url: "/staff/dashboard", icon: "md-dashboard" },
    { name: "Reading", url: "/staff/reading", icon: "bi-flag-fill" },
];

const form = useForm({});

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const handleLogout = () => {
    form.post(route("logout"), {
        preserveScroll: true,
        onSuccess: () => {
            router.visit(route("select-roles"));
        },
        onError: () => {
            Swal.fire({
                icon: "error",
                title: "Logout failed",
                text: "Please try again.",
                background: "#f8f9fa",
                customClass: {
                    title: "text-lg font-medium",
                    popup: "dark:bg-gray-800 dark:text-white",
                },
            });
        },
    });
};
</script>

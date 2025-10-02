<template>
    <div
        class="antialiased min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col"
    >
        <DashboardSidebar
            :links="customerLinks"
            :is-open="isSidebarOpen"
            :is-mobile-menu-open="isMobileMenuOpen"
            @toggle-sidebar="toggleSidebar"
            @toggle-mobile-menu="toggleMobileMenu"
        >
            <template #logo>
                <div class="flex items-center p-[6.6px]">
                    <img
                        src="/images/MainLogo.png"
                        class="h-[70px] w-[70px] min-w-[50px] min-h-[50px] object-contain object-center"
                        alt=""
                    />
                    <h1 class="text-white text-[22px] font-bold">AquaTrack</h1>
                </div>
                <div
                    v-if="pageProps.announcementCount > 0"
                    class="absolute flex items-center justify-center border rounded-full bg-blue-400 w-5 h-5 top-[80px] right-3"
                >
                    <span class="text-white text-xs">{{
                        pageProps.announcementCount
                    }}</span>
                </div>
            </template>
        </DashboardSidebar>
        <MainContentNavbar
            :is-sidebar-open="isSidebarOpen"
            :is-mobile-menu-open="isMobileMenuOpen"
            @toggle-sidebar="toggleSidebar"
            @toggle-mobile-menu="toggleMobileMenu"
            @logout="handleLogout"
        />
        <main
            :class="[
                'p-4 pt-[100px] flex-1 transition-all duration-300 ease-in-out',
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
import { ref, onMounted, computed } from "vue";
import { useForm, router, usePage } from "@inertiajs/vue3";

import Swal from "sweetalert2";

const { props: pageProps } = usePage();

const isSidebarOpen = ref(true);
const isMobileMenuOpen = ref(false); // Add mobile menu state

const customerLinks = [
    { name: "Dashboard", url: "/customer/dashboard", icon: "md-dashboard" },
    { name: "Usage", url: "/customer/usage", icon: "bi-graph-up" },
    { name: "Reports", url: "/customer/reports", icon: "bi-flag-fill" },
    {
        name: "Announcements",
        url: "/customer/announcements",
        icon: "bi-megaphone-fill",
    },
];

const form = useForm({});

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

// Add mobile menu toggle function
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

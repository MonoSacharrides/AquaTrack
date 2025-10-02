<script setup>
import { computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import AquatrackLogo from '../AquatrackLogo.vue';

const emit = defineEmits(['close', 'addReport']);

const props = defineProps({
    isOpen: Boolean,
    canLogin: {
        type: Boolean,
        default: true
    },
    canRegister: {
        type: Boolean,
        default: true
    },
    version: {
        type: String,
        default: '1.0'
    }
});

const page = usePage();

const isActive = (href) => {
    const current = page.url;
    if (typeof href === 'object' && href.route) {
        const routePath = new URL(router.url(href.route)).pathname;
        const currentPath = new URL(current, window.location.origin).pathname;
        if (href.route === 'reports.index') return currentPath === routePath;
        return currentPath.startsWith(routePath);
    }
    const currentPath = new URL(current, window.location.origin).pathname;
    const targetPath = new URL(href, window.location.origin).pathname;
    if (targetPath === '/') return currentPath === targetPath;
    return currentPath.startsWith(targetPath);
};

const handleClose = () => emit('close');
const handleAddReport = () => emit('addReport');
</script>

<template>
    <!-- Single transition wrapper -->
    <transition name="sidebar">
        <div v-if="isOpen" class="fixed inset-0 z-50 overflow-hidden">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/50 transition-opacity duration-300" @click="handleClose"></div>

            <!-- Sidebar -->
            <aside
                class="fixed top-0 right-0 h-full w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out">
                <div class="h-full flex flex-col">
                    <!-- Sidebar Header -->
                    <div class="flex items-center justify-between p-4 border-b">
                        <AquatrackLogo class="h-14 w-14" />
                        <button @click="handleClose" class="p-1 rounded-full hover:bg-gray-100">
                            <v-icon name="bi-x-lg" />
                        </button>
                    </div>

                    <!-- Sidebar Content -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-2">
                        <Link href="/" @click="handleClose"
                            class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors"
                            :class="isActive('/') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'">
                        <v-icon name="bi-house" scale="0.9" />
                        <span>Home</span>
                        </Link>

                        <button @click="handleAddReport"
                            class="flex items-center gap-3 w-full text-left px-3 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                            <v-icon name="bi-plus-circle" scale="0.9" />
                            <span>Add Report</span>
                        </button>

                        <Link :href="route('reports.index')" @click="handleClose"
                            class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors"
                            :class="isActive(route('reports.index')) ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'">
                        <v-icon name="bi-flag" scale="0.9" />
                        <span>Reports</span>
                        </Link>



                        <div class="border-t border-gray-200 my-2"></div>

                        <Link v-if="canLogin" :href="route('login')" @click="handleClose"
                            class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors"
                            :class="isActive(route('login')) ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'">
                        <v-icon name="bi-box-arrow-in-right" scale="0.9" />
                        <span>Log In</span>
                        </Link>
                    </div>

                    <!-- Sidebar Footer -->
                    <div class="p-4 border-t text-sm text-gray-500">
                        <p>Aquatrack v{{ version }}</p>
                    </div>
                </div>
            </aside>
        </div>
    </transition>
</template>

<style scoped>
/* Custom scrollbar */
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

/* Smooth transitions */
.sidebar-enter-active,
.sidebar-leave-active {
    transition: opacity 0.3s ease;
}

.sidebar-enter-from,
.sidebar-leave-to {
    opacity: 0;
}

.sidebar-enter-active aside,
.sidebar-leave-active aside {
    transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.5, 1);
}

.sidebar-enter-from aside {
    transform: translateX(100%);
}

.sidebar-leave-to aside {
    transform: translateX(100%);
}

/* Performance optimizations */
aside {
    will-change: transform;
    backface-visibility: hidden;
}
</style>

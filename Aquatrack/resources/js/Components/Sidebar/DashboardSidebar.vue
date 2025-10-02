```vue
<template>
    <aside
        :class="[
            'fixed top-0 left-0 z-50 h-screen bg-gradient-to-b from-[#062F64] to-indigo-900 border-r shadow-lg dark:bg-gradient-to-b dark:from-gray-800 dark:to-gray-700 dark:border-gray-700 transition-all duration-300 ease-in-out',
            isOpen ? 'w-[260px]' : 'w-[70px]',
            isMobileMenuOpen
                ? 'translate-x-0'
                : '-translate-x-full md:translate-x-0',
        ]"
        aria-label="Sidebar"
        id="drawer-navigation"
    >
        <!-- Logo Section -->
        <div class="flex items-center justify-start py-[11px] px-4 border-b border-gray-200/20">
            <div class="flex items-center">
                <img
                    src="/images/MainLogo.png"
                    :class="[
                        'transition-all duration-300',
                        isOpen
                            ? 'w-14 h-14 object-cover'
                            : 'w-[56px] h-[56px] object-contain',
                    ]"
                    alt="AquaTrack Logo"
                />
                <span
                    v-if="isOpen"
                    class="text-lg text-white ml-3 uppercase font-semibold tracking-tighter transition-opacity duration-300"
                >
                    AquaTrack
                </span>
            </div>
        </div>

        <div class="h-full overflow-hidden px-3 pb-4 bg-transparent">
            <!-- Close button for mobile -->
            <button
                v-if="isMobileMenuOpen"
                @click="$emit('toggle-mobile-menu')"
                class="absolute top-4 right-4 p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg md:hidden z-10"
            >
                <XMarkIcon class="w-6 h-6" />
            </button>

            <!-- Menu Label -->
            <div class="mb-4">
                <span
                    v-if="isOpen"
                    class="px-3 text-xs font-semibold text-gray-300 dark:text-gray-400 uppercase tracking-wider"
                >
                    MENU
                </span>
            </div>

            <nav class="space-y-2">
                <div v-for="link in links" :key="link.url">
                    <Link
                        :href="link.url"
                        :title="!isOpen ? link.name : ''"
                        class="flex items-center text-white px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 group relative"
                        :class="{
                            'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md transform scale-[1.02]':
                                isActive(link.url),
                            'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400':
                                !isActive(link.url),
                        }"
                        @click="handleLinkClick"
                    >
                        <!-- Active indicator -->
                        <div
                            v-if="isActive(link.url)"
                            class="absolute left-0 w-2 h-8 bg-white rounded-r-full opacity-80"
                        ></div>

                        <component
                            :is="getIconComponent(link.icon)"
                            class="w-5 h-5 transition-all duration-200 flex-shrink-0"
                            :class="{
                                'text-white': isActive(link.url),
                                'text-white dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400':
                                    !isActive(link.url),
                            }"
                        />

                        <span
                            class="ml-3 transition-all duration-300"
                            :class="{
                                'opacity-0 w-0 overflow-hidden': !isOpen,
                                'opacity-100 w-auto': isOpen,
                            }"
                        >
                            {{ link.name }}
                        </span>

                        <!-- Tooltip for collapsed state -->
                        <div
                            v-if="!isOpen"
                            class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-50"
                        >
                            {{ link.name }}
                        </div>
                    </Link>
                </div>
            </nav>

            <!-- General Section (if needed) -->
            <!-- <div class="mt-8">
                <span
                    v-if="isOpen"
                    class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                    GENERAL
                </span>
            </div> -->

            <!-- Collapse/Expand Button -->
            <div class="absolute bottom-4 left-0 right-0 px-3">
                <button
                    @click="$emit('toggle-sidebar')"
                    class="w-full flex items-center justify-center px-3 py-2 text-sm font-medium text-white dark:text-gray-400 hover:bg-white hover:text-blue-500 dark:hover:bg-gray-700 rounded-xl transition-all duration-200"
                >
                    <ChevronDoubleLeftIcon
                        class="w-5 h-5 transition-transform duration-300"
                        :class="{ 'rotate-180': !isOpen }"
                    />
                    <span
                        v-if="isOpen"
                        class="ml-2 transition-opacity duration-300"
                    >

                    </span>
                </button>
            </div>
        </div>
    </aside>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import {
    HomeIcon,
    ChartBarIcon,
    FlagIcon,
    SpeakerWaveIcon,
    UsersIcon,
    DocumentTextIcon,
    BellIcon,
    ClipboardDocumentListIcon,
    ChevronDoubleLeftIcon,
    XMarkIcon,
} from "@heroicons/vue/24/outline";

defineProps({
    links: {
        type: Array,
        required: true,
    },
    isOpen: {
        type: Boolean,
        default: true,
    },
    isMobileMenuOpen: {
        type: Boolean,
        default: false,
    },
});

const iconMap = {
    "md-dashboard": HomeIcon,
    "bi-graph-up": ChartBarIcon,
    "bi-flag-fill": FlagIcon,
    "bi-megaphone-fill": SpeakerWaveIcon,
    "fa-users": UsersIcon,
    "bi-file-earmark-text-fill": DocumentTextIcon,
    "hi-bell": BellIcon,
    "hi-clipboard-list": ClipboardDocumentListIcon,
};

const getIconComponent = (iconName) => {
    return iconMap[iconName] || HomeIcon;
};

const isActive = (url) => {
    return window.location.pathname === url;
};

const emit = defineEmits(["toggle-sidebar", "toggle-mobile-menu"]);

const handleLinkClick = () => {
    if (window.innerWidth < 768) {
        emit("toggle-mobile-menu");
    }
};
</script>

<style scoped>
aside {
    transition: transform 0.3s ease-in-out, width 0.3s ease-in-out;
}

/* Scrollbar styling */
aside::-webkit-scrollbar {
    width: 4px;
}

aside::-webkit-scrollbar-track {
    background: transparent;
}

aside::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.3);
    border-radius: 2px;
}

aside::-webkit-scrollbar-thumb:hover {
    background-color: rgba(156, 163, 175, 0.5);
}

.dark aside::-webkit-scrollbar-thumb {
    background-color: rgba(75, 85, 99, 0.3);
}

.dark aside::-webkit-scrollbar-thumb:hover {
    background-color: rgba(75, 85, 99, 0.5);
}

/* Improved touch targets for mobile */
@media (max-width: 767px) {
    nav > div {
        margin-bottom: 0.25rem;
    }

    nav a {
        padding: 1rem 0.75rem;
        min-height: 3rem;
    }

    .w-5.h-5 {
        width: 1.25rem;
        height: 1.25rem;
    }
}

/* Active link glow effect */
.bg-gradient-to-r.from-blue-500 {
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

/* Smooth hover animations */
nav a:not(.bg-gradient-to-r):hover {
    transform: translateX(2px);
}

/* Tooltip arrow */
nav a .absolute.left-full::before {
    content: "";
    position: absolute;
    left: -4px;
    top: 50%;
    transform: translateY(-50%);
    border: 4px solid transparent;
    border-right-color: #1f2937;
}
</style>
```

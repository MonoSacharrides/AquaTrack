<template>
    <div
        class="min-h-screen relative overflow-hidden bg-gradient-to-br from-slate-50 to-blue-50"
    >
        <Navigation />

        <!-- Floating decorations - subtle and clean -->
        <!-- <div
            class="select-roles-drop-top absolute top-1/4 left-1/4 w-64 h-64 rounded-full bg-blue-100 opacity-40 blur-3xl"
        ></div>
        <div
            class="select-roles-drop-bottom absolute bottom-1/3 right-1/4 w-96 h-96 rounded-full bg-teal-100 opacity-40 blur-3xl"
        ></div> -->

        <!-- Main content -->
        <div
            class="relative z-10 min-h-screen flex flex-col items-center justify-center px-4"
        >
            <div class="w-full min-h-[85vh] max-w-6xl text-center">
                <!-- Header -->
                <div class="mb-16 select-roles-header">
                    <h1
                        class="text-4xl md:text-6xl text-slate-800 mb-6 font-md tracking-[-0.03em] select-roles-headline"
                    >
                        Welcome to
                        <span
                            class="text-transparent bg-clip-text bg-[#0650A7] font-md"
                            >AquaTrack</span
                        >
                    </h1>
                    <p
                        class="text-xl text-slate-600 max-w-2xl mx-auto select-roles-subheadline font-light"
                    >
                        Choose how you want to access the platform
                    </p>
                </div>

                <!-- Role cards -->
                <div
                    class="grid grid-cols-1 md:grid-cols-3 gap-8 mx-auto max-w-5xl role-cards-container"
                >
                    <!-- Concessioner Card -->
                    <div
                        class="modern-card group role-card"
                        @click="showLoginModal('customer')"
                    >
                        <div class="card-glow"></div>
                        <div
                            class="modern-card-inner bg-white border border-blue-100 shadow-lg hover:shadow-xl"
                        >
                            <div
                                class="icon-container bg-gradient-to-br from-blue-500 to-blue-600"
                            >
                                <v-icon
                                    name="hi-user"
                                    class="text-white text-3xl"
                                />
                            </div>
                            <h3 class="text-2xl font-bold text-slate-800 mb-3">
                                Concessioner
                            </h3>
                            <p class="text-slate-600 mb-6 leading-relaxed">
                                Report and track water issues
                            </p>
                            <div
                                class="select-badge bg-blue-50 text-blue-600 border-blue-200"
                            >
                                <span>Select Role</span>
                                <v-icon
                                    name="hi-arrow-right"
                                    class="ml-2 transition-transform group-hover:translate-x-1"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Admin Card -->
                    <div
                        class="modern-card group role-card"
                        @click="showLoginModal('admin')"
                    >
                        <div class="card-glow"></div>
                        <div
                            class="modern-card-inner bg-white border border-purple-100 shadow-lg hover:shadow-xl"
                        >
                            <div
                                class="icon-container bg-gradient-to-br from-purple-500 to-purple-600"
                            >
                                <v-icon
                                    name="hi-cog"
                                    class="text-white text-3xl"
                                />
                            </div>
                            <h3 class="text-2xl font-bold text-slate-800 mb-3">
                                Administrator
                            </h3>
                            <p class="text-slate-600 mb-6 leading-relaxed">
                                Manage system and users
                            </p>
                            <div
                                class="select-badge bg-purple-50 text-purple-600 border-purple-200"
                            >
                                <span>Select Role</span>
                                <v-icon
                                    name="hi-arrow-right"
                                    class="ml-2 transition-transform group-hover:translate-x-1"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Staff Card -->
                    <div
                        class="modern-card group role-card"
                        @click="showLoginModal('staff')"
                    >
                        <div class="card-glow"></div>
                        <div
                            class="modern-card-inner bg-white border border-teal-100 shadow-lg hover:shadow-xl"
                        >
                            <div
                                class="icon-container bg-gradient-to-br from-teal-500 to-teal-600"
                            >
                                <v-icon
                                    name="hi-users"
                                    class="text-white text-3xl"
                                />
                            </div>
                            <h3 class="text-2xl font-bold text-slate-800 mb-3">
                                Staff
                            </h3>
                            <p class="text-slate-600 mb-6 leading-relaxed">
                                Handle meter readings
                            </p>
                            <div
                                class="select-badge bg-teal-50 text-teal-600 border-teal-200"
                            >
                                <span>Select Role</span>
                                <v-icon
                                    name="hi-arrow-right"
                                    class="ml-2 transition-transform group-hover:translate-x-1"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-500"
            @click="hideLoginModal"
        >
            <div
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-[900px] mx-4 overflow-hidden transform transition-all duration-500 ease-out modal-container"
                :class="modalClasses"
                @click.stop
            >
                <button
                    @click="hideLoginModal"
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-10 transition-colors duration-200 p-1 rounded-full hover:bg-gray-100"
                >
                    <v-icon name="hi-x" class="text-2xl" />
                </button>
                <Login
                    :selected-role="selectedRole"
                    @close="hideLoginModal"
                    @login-success="handleLoginSuccess"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from "vue";
import { Link } from "@inertiajs/vue3";
import { OhVueIcon, addIcons } from "oh-vue-icons";
import { HiUser, HiCog, HiUsers, HiArrowRight, HiX } from "oh-vue-icons/icons";
import Login from "@/Pages/Auth/Login.vue";
import Navigation from "@/Components/Header/Navigation.vue";
import gsap from "gsap";

addIcons(HiUser, HiCog, HiUsers, HiArrowRight, HiX);

const showModal = ref(false);
const selectedRole = ref("");
const modalAnimation = ref(null);

const modalClasses = computed(() => {
    return showModal.value
        ? "opacity-100 scale-100 translate-y-0"
        : "opacity-0 scale-95 translate-y-4";
});

const showLoginModal = async (role) => {
    selectedRole.value = role.charAt(0).toUpperCase() + role.slice(1);

    // Quick card animation
    const clickedCard = event?.currentTarget;
    if (clickedCard) {
        gsap.to(clickedCard, {
            scale: 0.95,
            duration: 0.15,
            ease: "power2.out",
        });
    }

    showModal.value = true;
    await nextTick();

    // Faster modal animation
    if (modalAnimation.value) modalAnimation.value.kill();

    modalAnimation.value = gsap.fromTo(".modal-container",
        {
            opacity: 0,
            scale: 0.9,
            y: 10,
        },
        {
            opacity: 1,
            scale: 1,
            y: 0,
            duration: 0.3,
            ease: "power2.out",
        }
    );
};

const hideLoginModal = async () => {
    if (modalAnimation.value) modalAnimation.value.kill();

    modalAnimation.value = gsap.to(".modal-container", {
        opacity: 0,
        scale: 0.95,
        y: 5,
        duration: 0.2,
        ease: "power2.in",
    });

    // Reset cards quickly
    gsap.to(".role-card", {
        scale: 1,
        duration: 0.2,
        ease: "power2.out",
    });

    setTimeout(() => {
        showModal.value = false;
    }, 200);
};

const handleLoginSuccess = () => {
    hideLoginModal();
};

// OPTIMIZED GSAP animations - Much faster and performance focused
onMounted(() => {
    // Set initial states - minimal setup
    gsap.set(".select-roles-header", { y: 20, opacity: 0 });
    gsap.set(".role-card", { y: 20, opacity: 0 });

    // Remove complex background animations that slow things down
    gsap.set([".select-roles-drop-top", ".select-roles-drop-bottom"], { opacity: 0 });

    // Faster timeline with reduced duration and fewer animations
    const tl = gsap.timeline();

    // Animate header quickly
    tl.to(".select-roles-header", {
        y: 0,
        opacity: 1,
        duration: 0.4,
        ease: "power2.out",
    });

    // Animate cards with minimal stagger
    tl.to(".role-card", {
        y: 0,
        opacity: 1,
        duration: 0.5,
        stagger: 0.1, // Reduced stagger
        ease: "power2.out",
    }, "-=0.2"); // Overlap with previous animation

    // Remove continuous background animations that cause performance issues
});
</script>

<style scoped>
.modern-card {
    perspective: 1000px;
    height: 320px;
    position: relative;
    cursor: pointer;
    transform: translateZ(0);
    backface-visibility: hidden;
}

.card-glow {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 1.5rem;
    background: transparent;
    opacity: 0;
    transition: all 0.5s ease;
    z-index: -1;
}

.modern-card:hover .card-glow {
    opacity: 0.6;
    filter: blur(15px);
}

.modern-card:nth-child(1):hover .card-glow {
    background: linear-gradient(
        45deg,
        rgba(59, 130, 246, 0.15),
        rgba(6, 182, 212, 0.15)
    );
}

.modern-card:nth-child(2):hover .card-glow {
    background: linear-gradient(
        45deg,
        rgba(168, 85, 247, 0.15),
        rgba(217, 70, 239, 0.15)
    );
}

.modern-card:nth-child(3):hover .card-glow {
    background: linear-gradient(
        45deg,
        rgba(20, 184, 166, 0.15),
        rgba(16, 185, 129, 0.15)
    );
}

.modern-card-inner {
    @apply h-full w-full rounded-2xl p-8 relative overflow-hidden transition-all duration-500;
    transform-style: preserve-3d;
    transform: translateZ(0);
    backface-visibility: hidden;
}

.modern-card-inner::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(0, 0, 0, 0.05),
        transparent
    );
}

.modern-card:hover .modern-card-inner {
    transform: translateY(-8px);
    border-color: rgba(99, 102, 241, 0.3);
}

.icon-container {
    @apply w-20 h-20 rounded-2xl flex items-center justify-center mb-6 mx-auto transition-all duration-500;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.modern-card:hover .icon-container {
    transform: scale(1.05) translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.select-badge {
    @apply inline-flex items-center px-5 py-3 rounded-full text-sm font-medium transition-all duration-500;
    backdrop-filter: blur(10px);
}

.group:hover .select-badge {
    transform: scale(1.03);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

/* Modal backdrop animation */
.modal-backdrop-enter-active,
.modal-backdrop-leave-active {
    transition: opacity 0.5s ease;
}

.modal-backdrop-enter-from,
.modal-backdrop-leave-to {
    opacity: 0;
}

/* Performance optimizations */
.role-card {
    transform: translateZ(0);
    backface-visibility: hidden;
}

.select-roles-drop-top,
.select-roles-drop-bottom {
    transform: translateZ(0);
}

/* Subtle hover effect on cards */
.modern-card::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 1.5rem;
    background: radial-gradient(
        circle at center,
        transparent 30%,
        rgba(255, 255, 255, 0.1) 70%
    );
    opacity: 0;
    transition: opacity 0.5s ease;
    pointer-events: none;
}

.modern-card:hover::after {
    opacity: 1;
}
</style>

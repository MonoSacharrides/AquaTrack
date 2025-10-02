// LandingPage.vue
<script setup>
import { Head } from "@inertiajs/vue3";
import Hero from "@/Components/Hero.vue";
import Features from "@/Components/Features.vue";
import About from "@/Components/About.vue";
import CallToAction from "@/Components/CallToAction.vue";
import Footer from "@/Components/Footer.vue";
import { onMounted, ref } from "vue";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

// Register ScrollTrigger plugin
gsap.registerPlugin(ScrollTrigger);

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
    trackingCode: {
        // Add trackingCode prop
        type: String,
        default: null,
    },
    dateSubmitted: {
        // Add dateSubmitted prop
        type: String,
        default: null,
    },
});

const animationInitialized = ref(false);

// Setup scroll animations
onMounted(() => {
    if (!animationInitialized.value) {
        setTimeout(() => {
            setupScrollAnimations();
            animationInitialized.value = true;
        }, 300);
    }
});

function setupScrollAnimations() {
    if (typeof window === "undefined") return;

    ScrollTrigger.getAll().forEach((trigger) => trigger.kill());

    const masterTL = gsap.timeline();

    const featureItems = document.querySelectorAll(".feature-item");
    if (featureItems.length > 0) {
        featureItems.forEach((item, index) => {
            const animation = gsap.fromTo(
                item,
                { y: 50, opacity: 0 },
                { y: 0, opacity: 1, duration: 0.8, delay: index * 0.15 }
            );

            ScrollTrigger.create({
                animation: animation,
                trigger: item,
                start: "top 85%",
                end: "bottom 15%",
                toggleActions: "play none none reset",
                markers: false,
            });
        });
    }

    const aboutContent = document.querySelector(".about-content");
    if (aboutContent) {
        const animation = gsap.fromTo(
            aboutContent,
            { x: -50, opacity: 0 },
            { x: 0, opacity: 1, duration: 1 }
        );

        ScrollTrigger.create({
            animation: animation,
            trigger: aboutContent,
            start: "top 80%",
            end: "bottom 20%",
            toggleActions: "play none none reset",
            markers: false,
        });
    }

    const aboutImage = document.querySelector(".about-image");
    if (aboutImage) {
        const animation = gsap.fromTo(
            aboutImage,
            { x: 50, opacity: 0 },
            { x: 0, opacity: 1, duration: 1 }
        );

        ScrollTrigger.create({
            animation: animation,
            trigger: aboutImage,
            start: "top 80%",
            end: "bottom 20%",
            toggleActions: "play none none reset",
            markers: false,
        });
    }

    const ctaSection = document.querySelector(".cta-section");
    if (ctaSection) {
        const animation = gsap.fromTo(
            ctaSection,
            { scale: 0.9, opacity: 0 },
            { scale: 1, opacity: 1, duration: 0.8 }
        );

        ScrollTrigger.create({
            animation: animation,
            trigger: ctaSection,
            start: "top 85%",
            end: "bottom 15%",
            toggleActions: "play none none reset",
            markers: false,
        });
    }

    ScrollTrigger.refresh();
}

if (typeof window !== "undefined") {
    window.addEventListener("scroll", () => {
        ScrollTrigger.refresh();
    });
}
</script>

<template>
    <Head title="Landing" />

    <div class="overflow-hidden">
        <Hero
            id="home"
            :can-login="canLogin"
            :can-register="canRegister"
            :tracking-code="trackingCode"
            :date-submitted="dateSubmitted"
        />
        <Features id="features" />
        <About id="about" />
        <CallToAction id="cta" />
        <Footer />
    </div>
</template>

// ApplicationLogo.vue
<script setup>
defineProps({
    size: {
        type: [String, Number],
        default: 16
    }
});
</script>

<template>
    <div :class="`h-${size} w-${size}`">
        <img src="/images/MainLogo.png" alt="AquaTrack Logo" class="object-contain w-full h-full">
    </div>
</template>

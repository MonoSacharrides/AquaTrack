<!-- resources/js/Layouts/ProfileLayout.vue -->
<template>
  <div class="min-h-screen bg-gray-50">
    <slot />
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'

defineProps({
  title: String
})
</script>

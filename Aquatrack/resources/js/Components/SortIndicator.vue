// Components/SortIndicator.vue
<template>
    <v-icon v-if="currentField === field" :name="direction === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down'" class="w-3 h-3 inline ml-1" />
</template>

<script setup>
defineProps({
    field: String,
    currentField: String,
    direction: String,
});
</script>

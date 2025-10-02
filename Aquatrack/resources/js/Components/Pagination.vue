<template>
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Showing <span class="font-medium">{{ items.from }}</span> to
            <span class="font-medium">{{ items.to }}</span> of
            <span class="font-medium">{{ items.total }}</span> {{ itemName }}
        </div>
        <div class="flex space-x-2">
            <button
                @click="prevPage"
                :disabled="!items.prev_page_url"
                :class="{ 'opacity-50 cursor-not-allowed': !items.prev_page_url }"
                class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50"
            >
                {{ previousText }}
            </button>
            <button
                @click="nextPage"
                :disabled="!items.next_page_url"
                :class="{ 'opacity-50 cursor-not-allowed': !items.next_page_url }"
                class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50"
            >
                {{ nextText }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({
    items: Object,
    itemName: {
        type: String,
        default: 'items'
    },
    previousText: {
        type: String,
        default: 'Previous'
    },
    nextText: {
        type: String,
        default: 'Next'
    }
});

const prevPage = () => {
    if (props.items.prev_page_url) {
        router.visit(props.items.prev_page_url, {
            preserveState: true,
        });
    }
};

const nextPage = () => {
    if (props.items.next_page_url) {
        router.visit(props.items.next_page_url, {
            preserveState: true,
        });
    }
};
</script>

<template>
    <Transition name="modal">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-800 opacity-75" @click="close"></div>
                </div>

                <!-- Modal container -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle"
                    :class="maxWidthClass"
                    :style="{ width: modalWidth, resize: isResizable ? 'both' : 'none', overflow: 'auto', minWidth: '400px', maxWidth: '95vw' }"
                    ref="modalElement">
                    <!-- Resize handle -->
                    <div v-if="isResizable" class="absolute bottom-0 right-0 w-4 h-4 cursor-se-resize"
                        @mousedown="startResize"></div>

                    <!-- Modal header -->
                    <div class="bg-blue-600 px-4 py-3 sm:px-6 flex items-center justify-between">
                        <div class="flex items-center">
                            <slot name="title"></slot>
                        </div>
                        <button @click="close" class="text-white hover:text-gray-200 focus:outline-none">
                            <v-icon name="bi-x" scale="1.5" />
                        </button>
                    </div>

                    <!-- Modal content -->
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <slot></slot>
                    </div>

                    <!-- Modal footer (optional) -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse" v-if="$slots.footer">
                        <slot name="footer"></slot>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    show: Boolean,
    maxWidth: {
        type: String,
        default: '2xl'
    },
    isResizable: {
        type: Boolean,
        default: false
    },
    initialWidth: {
        type: String,
        default: null
    }
});

const emit = defineEmits(['close']);

const modalElement = ref(null);
const modalWidth = ref(props.initialWidth || '80vw');
const isResizing = ref(false);
const startX = ref(0);
const startY = ref(0);
const startWidth = ref(0);
const startHeight = ref(0);

const maxWidthClass = computed(() => {
    return {
        'sm': 'sm:max-w-sm',
        'md': 'sm:max-w-md',
        'lg': 'sm:max-w-lg',
        'xl': 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
        '3xl': 'sm:max-w-3xl',
        '4xl': 'sm:max-w-4xl',
        '5xl': 'sm:max-w-5xl',
        '6xl': 'sm:max-w-6xl',
        '7xl': 'sm:max-w-7xl',
    }[props.maxWidth];
});

const startResize = (e) => {
    isResizing.value = true;
    startX.value = e.clientX;
    startY.value = e.clientY;
    startWidth.value = parseInt(document.defaultView.getComputedStyle(modalElement.value).width, 10);
    startHeight.value = parseInt(document.defaultView.getComputedStyle(modalElement.value).height, 10);

    e.preventDefault();
    document.addEventListener('mousemove', resize);
    document.addEventListener('mouseup', stopResize);
};

const resize = (e) => {
    if (isResizing.value) {
        const width = startWidth.value + e.clientX - startX.value;
        const height = startHeight.value + e.clientY - startY.value;

        modalWidth.value = `${Math.max(width, 400)}px`;
        modalElement.value.style.height = `${Math.max(height, 200)}px`;
    }
};

const stopResize = () => {
    isResizing.value = false;
    document.removeEventListener('mousemove', resize);
    document.removeEventListener('mouseup', stopResize);
};

const close = () => {
    emit('close');
};

onMounted(() => {
    document.addEventListener('mouseup', stopResize);
});

onUnmounted(() => {
    document.removeEventListener('mouseup', stopResize);
});
</script>

<style>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.cursor-se-resize {
    cursor: se-resize;
}
</style>

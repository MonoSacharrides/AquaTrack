<template>
    <div class="flex items-center gap-1 sm:gap-2 md:gap-3">
        <div class="flex items-center group">
            <!-- Responsive logo image -->
            <img src="/images/MainLogo.png" alt="AquaTrack Logo"
                class="h-14 w-14 sm:h-14 sm:w-14 md:h-18 md:w-18 lg:h-20 lg:w-20 object-contain rounded-full" />

            <!-- Text container with responsive breakpoints -->
            <span v-if="$slots.default"
                class="ml-1 sm:ml-2 text-sm xs:text-base sm:text-sm md:text-md lg:text-lg font-bold text-white">
                <slot></slot>
            </span>

            <!-- Fallback text with responsive breakpoints -->
            <span v-else
                class="ml-1 sm:ml-2 text-md xs:text-sm sm:text-md md:text-lg lg:text-xl font-bold uppercase tracking-[-1px] text-black">
                AquaTrack
            </span>
        </div>
    </div>
</template>

<script setup>

defineProps(
    {
        email:{
            type: String,
            default: 'info@aquatrack.com'
        },

        phone:{
            type: String,
            default: '09677989106'
        },

        text:{
            type: String,
            default: 'Smart water monitoring for a sustainable future'
        },

        allRightReserved:{
            type: String,
            default: '© 2023 AquaTrack. All rights reserved.'
        }
    }
);

</script>

<template>
    <footer class="p-6 bg-gray-900 text-white">
        <div class="container mx-auto flex flex-col md:flex-row items-center md:items-start justify-between gap-8">
            <!-- Logo and Brand -->
            <div class="flex items-center gap-3 w-full md:w-auto justify-center md:justify-start">
                <img src="/images/MainLogo.png" alt="AquaTrack Logo" class="h-16 w-16 object-contain" />
                <div>
                    <h3 class="text-lg font-semibold bg-gradient-to-r from-blue-900 to-blue-700 bg-clip-text text-transparent">AquaTrack</h3>
                    <p class="mt-1 text-gray-400 text-sm">
                        {{ text }}
                    </p>
                </div>
            </div>
            <!-- Contacts -->
            <div class="w-full md:w-auto text-center md:text-right">
                <h3 class="text-lg font-semibold mb-2">Contact</h3>
                <ul class="space-y-1 text-gray-400 text-sm">
                    <li>Email: <a href="mailto:info@aquatrack.com" class="hover:text-white">{{ email }}</a></li>
                    <li>Phone: <a href="tel:+1234567890" class="hover:text-white">{{ phone }}</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-8 border-t border-gray-800 pt-6 text-center text-gray-400 text-sm">
            {{ allRightReserved  }}
        </div>
    </footer>
</template>

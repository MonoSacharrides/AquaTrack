<template>
    <section id="features" class="py-12 sm:py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-[1500px] mx-auto">
            <div class="text-center mb-8 sm:mb-12">
                <h2
                    class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-3 sm:mb-4"
                >
                    Why Choose AquaTrack?
                </h2>
                <p
                    class="text-base sm:text-md lg:text-lg text-gray-600 max-w-3xl mx-auto"
                >
                    Everything you need to manage your water consumption
                    efficiently
                </p>
            </div>

            <div
                class="grid gap-6 sm:gap-8 md:gap-[60px] sm:grid-cols-2 lg:grid-cols-4"
            >
                <div
                    class="feature-item flex flex-col items-center rounded-xl border border-blue-300 bg-white p-4 sm:p-6 md:p-8 shadow-md transition-all hover:shadow-lg hover:-translate-y-1"
                >
                    <div
                        class="flex h-12 w-12 sm:h-14 sm:w-14 md:h-16 md:w-16 items-center justify-center rounded-full bg-blue-100 mb-3 sm:mb-4"
                    >
                        <v-icon
                            name="bi-speedometer2"
                            scale="1.25"
                            class="text-blue-600"
                        />
                    </div>
                    <h3
                        class="text-lg sm:text-xl md:text-2xl font-semibold text-blue-800 mb-2 text-center"
                    >
                        Real-time Monitoring
                    </h3>
                    <p
                        class="text-sm sm:text-base text-gray-600 mt-2 sm:mt-4 text-center"
                    >
                        Track your water usage with detailed consumption
                        analytics and trends.
                    </p>
                </div>

                <!-- Secure & Reliable -->
                <div
                    class="feature-item flex flex-col items-center rounded-xl border border-blue-300 bg-white p-4 sm:p-6 md:p-8 shadow-md transition-all hover:shadow-lg hover:-translate-y-1"
                >
                    <div
                        class="flex h-12 w-12 sm:h-14 sm:w-14 md:h-16 md:w-16 items-center justify-center rounded-full bg-blue-100 mb-3 sm:mb-4"
                    >
                        <v-icon
                            name="bi-shield-lock"
                            scale="1.25"
                            class="text-blue-600"
                        />
                    </div>
                    <h3
                        class="text-lg sm:text-xl md:text-2xl font-semibold text-blue-800 mb-2 text-center"
                    >
                        Secure & Reliable
                    </h3>
                    <p
                        class="text-sm sm:text-base text-gray-600 mt-2 sm:mt-4 text-center"
                    >
                        Your data is protected with enterprise-grade security
                        and reliable service uptime.
                    </p>
                </div>

                <!-- Easy Issue Reporting -->
                <div
                    class="feature-item flex flex-col items-center rounded-xl border border-blue-300 bg-white p-4 sm:p-6 md:p-8 shadow-md transition-all hover:shadow-lg hover:-translate-y-1"
                >
                    <div
                        class="flex h-12 w-12 sm:h-14 sm:w-14 md:h-16 md:w-16 items-center justify-center rounded-full bg-blue-100 mb-3 sm:mb-4"
                    >
                        <v-icon
                            name="bi-flag"
                            scale="1.25"
                            class="text-blue-600"
                        />
                    </div>
                    <h3
                        class="text-lg sm:text-xl md:text-2xl font-semibold text-blue-800 mb-2 text-center"
                    >
                        Easy Issue Reporting
                    </h3>
                    <p
                        class="text-sm sm:text-base text-gray-600 mt-2 sm:mt-4 text-center"
                    >
                        Report water issues quickly and track resolution
                        progress through our platform.
                    </p>
                </div>

                <!-- Smart Analytics -->
                <div
                    class="feature-item flex flex-col items-center rounded-xl border border-blue-300 bg-white p-4 sm:p-6 md:p-8 shadow-md transition-all hover:shadow-lg hover:-translate-y-1"
                >
                    <div
                        class="flex h-12 w-12 sm:h-14 sm:w-14 md:h-16 md:w-16 items-center justify-center rounded-full bg-blue-100 mb-3 sm:mb-4"
                    >
                        <v-icon
                            name="bi-graph-up"
                            scale="1.25"
                            class="text-blue-600"
                        />
                    </div>
                    <h3
                        class="text-lg sm:text-xl md:text-2xl font-semibold text-blue-800 mb-2 text-center"
                    >
                        Smart Analytics
                    </h3>
                    <p
                        class="text-sm sm:text-base text-gray-600 mt-2 sm:mt-4 text-center"
                    >
                        Get insights into your consumption patterns and receive
                        tips for conservation.
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup></script>

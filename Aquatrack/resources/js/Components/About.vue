<template>
    <section id="about" class="py-4 sm:py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-[1500px] mx-auto">
            <div
                class="flex flex-col lg:flex-row items-center gap-8 lg:gap-[80px]"
            >
                <!-- Left Column - Text Content -->
                <div class="about-content lg:w-1/2">
                    <h2
                        class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-6 sm:mb-8 text-left"
                    >
                        About <span class="text-blue-600">AquaTrack</span>
                    </h2>
                    <div class="space-y-4 sm:space-y-6">
                        <p
                            class="text-base sm:text-md lg:text-lg text-gray-600 text-left"
                        >
                            AquaTrack is a comprehensive water management
                            platform designed specifically for residential
                            customers. Our mission is to make water consumption
                            tracking simple, transparent, and efficient.
                        </p>
                        <p
                            class="text-base sm:text-md lg:text-lg text-gray-600 text-left"
                        >
                            With features like real-time usage monitoring, can
                            view monthly bill, and instant issue reporting,
                            we're revolutionizing how communities interact with
                            their water services.
                        </p>
                        <div class="space-y-3 sm:space-y-4">
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 h-5 w-5 sm:h-6 sm:w-6 rounded-full border-2 border-blue-500 flex items-center justify-center mr-3 mt-0.5"
                                >
                                    <div
                                        class="h-2 w-2 sm:h-3 sm:w-3 rounded-full bg-blue-500"
                                    ></div>
                                </div>
                                <p
                                    class="text-base sm:text-md lg:text-lg text-gray-600"
                                >
                                    Water Usage Monitoring
                                </p>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 h-5 w-5 sm:h-6 sm:w-6 rounded-full border-2 border-blue-500 flex items-center justify-center mr-3 mt-0.5"
                                >
                                    <div
                                        class="h-2 w-2 sm:h-3 sm:w-3 rounded-full bg-blue-500"
                                    ></div>
                                </div>
                                <p
                                    class="text-base sm:text-md lg:text-lg text-gray-600"
                                >
                                    Instant Issue Reporting & Tracking
                                </p>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 h-5 w-5 sm:h-6 sm:w-6 rounded-full border-2 border-blue-500 flex items-center justify-center mr-3 mt-0.5"
                                >
                                    <div
                                        class="h-2 w-2 sm:h-3 sm:w-3 rounded-full bg-blue-500"
                                    ></div>
                                </div>
                                <p
                                    class="text-base sm:text-md lg:text-lg text-gray-600"
                                >
                                    Community Announcements
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Image Placeholder -->
                <div class="about-image lg:w-[600px] hidden lg:block">
                    <div
                        class="rounded-xl h-full flex items-center justify-center"
                    >
                        <img
                            src="/images/MainLogo.png"
                            alt="AquaTrack Logo"
                            class="max-w-full max-h-full object-contain hover:scale-105 transition-transform duration-300 cursor-pointer"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

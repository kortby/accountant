<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/layouts/MainLayout.vue';
</script>

<template>
    <Head title="About Us" />
    <MainLayout>
        
        <div class="bg-white overflow-hidden">
            <div class="relative max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
                
                <div class="hidden lg:block bg-slate-50 absolute top-0 bottom-0 left-3/4 w-screen"></div>
                
                <div class="mx-auto text-base max-w-prose lg:grid lg:grid-cols-2 lg:gap-8 lg:max-w-none">
                    <div>
                        <h2 class="text-base text-orange-600 font-semibold tracking-wide uppercase">About Us</h2>
                        <h3 class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-slate-900 sm:text-4xl">Driven by Accuracy, defined by Integrity</h3>
                    </div>
                </div>
                
                <div class="mt-8 lg:grid lg:grid-cols-2 lg:gap-8">
                    <div class="relative lg:row-start-1 lg:col-start-2">
                        <svg class="hidden lg:block absolute top-0 right-0 -mt-20 -mr-20 block h-42 w-42 text-slate-100" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                            <polygon points="50,0 100,0 50,100 0,100" />
                        </svg>
                        <div class="relative text-base mx-auto max-w-prose lg:max-w-none">
                            <figure>
                                <div class="aspect-w-12 aspect-h-7 lg:aspect-none">
                                    <div class="rounded-lg shadow-lg overflow-hidden bg-slate-200 h-96 flex items-center justify-center text-slate-400">
                                        <img src="/img/photos/about.png" alt="Office / Team Image" class=" object-cover object-center" />
                                    </div>
                                </div>
                            </figure>
                        </div>
                    </div>
                    
                    <div class="mt-8 lg:mt-0">
                        <div class="text-base max-w-prose mx-auto lg:max-w-none">
                            <p class="text-lg text-slate-500">
                                TaxesAccountant was founded with a simple mission: to make tax filing stress-free for everyone. We believe that expert financial advice shouldn't be complicated or inaccessible.
                            </p>
                        </div>
                        <div class="mt-5 prose prose-indigo text-slate-500 mx-auto lg:max-w-none lg:row-start-1 lg:col-start-1">
                            <p>
                                With over 10 years of experience in the industry, our team of Enrolled Agents and CPAs has helped thousands of individuals and small businesses navigate the complex landscape of tax laws.
                            </p>
                            <ul role="list" class="mt-8 space-y-4">
                                <li class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <p class="ml-3 text-base text-slate-500">Certified Experts reviewing every return.</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <p class="ml-3 text-base text-slate-500">Year-round support, not just during tax season.</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <p class="ml-3 text-base text-slate-500">Secure, digital-first document handling.</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </MainLayout>
</template>
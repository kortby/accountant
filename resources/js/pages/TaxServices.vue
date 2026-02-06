<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/layouts/MainLayout.vue';

const services = [
    {
        title: 'Individual Tax Preparation',
        description: 'Maximize your refund with our comprehensive personal tax filing services. We handle W-2s, 1099s, investments, and more.',
        icon: 'UserIcon'
    },
    {
        title: 'Business Tax & Strategy',
        description: 'From LLCs to Corporations, we ensure your business remains compliant while optimizing your tax liability.',
        icon: 'BriefcaseIcon'
    },
    {
        title: 'IRS Audit Representation',
        description: 'Facing an audit? Our enrolled agents will represent you before the IRS and help resolve tax notices.',
        icon: 'ShieldCheckIcon'
    },
    {
        title: 'Bookkeeping & Payroll',
        description: 'Keep your finances organized year-round. We offer monthly bookkeeping and seamless payroll management.',
        icon: 'CalculatorIcon'
    },
    {
        title: 'Tax Planning',
        description: 'Don’t just file—plan. We help you make strategic financial decisions throughout the year to save money.',
        icon: 'ChartBarIcon'
    },
    {
        title: 'Non-Profit Tax Services',
        description: 'Specialized filing for 501(c)(3) organizations ensuring compliance with strict IRS regulations.',
        icon: 'HeartIcon'
    }
];
</script>

<template>
    <Head title="Our Services" />
    <MainLayout>
        
        <div class="bg-slate-900 py-16 sm:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl font-extrabold text-white sm:text-5xl sm:tracking-tight lg:text-6xl">
                    Expert Tax Services
                </h1>
                <p class="mt-4 max-w-2xl mx-auto text-xl text-slate-400">
                    Comprehensive financial solutions tailored to your unique needs.
                </p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="service in services" :key="service.title" class="bg-white overflow-hidden shadow-sm rounded-lg border border-slate-200 hover:shadow-md transition-shadow p-6">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-slate-900">{{ service.title }}</h3>
                    <p class="mt-2 text-base text-slate-500">{{ service.description }}</p>
                </div>
            </div>
        </div>

        <div class="bg-orange-50">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                    <span class="block">Ready to get started?</span>
                    <span class="block text-orange-600">Book your consultation today.</span>
                </h2>
                <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                    <div class="inline-flex rounded-md shadow">
                        <Link href="/contact" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                            Contact Us
                        </Link>
                    </div>
                </div>
            </div>
        </div>

    </MainLayout>
</template>

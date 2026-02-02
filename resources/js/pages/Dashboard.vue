<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
    CardFooter
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes'; // Assuming this exists based on your snippet
import { type BreadcrumbItem } from '@/types';

// Get auth user
const page = usePage();
const authUser = page.props.auth?.user;
const isAccountant = authUser?.role === 'accountant' || authUser?.role === 'admin';

// Breadcrumbs setup
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard', // Hardcoded as requested to avoid route()
    },
];

// Props
const props = defineProps({
    testimonial: {
        type: Object,
        default: () => ({})
    }
});

// Helper to determine if testimonials exist (handling array or object check)
const hasTestimonials = computed(() => {
    if (Array.isArray(props.testimonial)) {
        return props.testimonial.length > 0;
    }
    return !!props.testimonial;
});

import { computed } from 'vue';

// Get auth user (already declared above)
const isAdmin = authUser?.role === 'admin';
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col space-y-6 p-6 lg:p-8">
            
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6 border-l-4 border-l-amber-500">
                <h1 class="text-2xl font-semibold tracking-tight text-foreground">
                    Welcome to <span class="text-amber-600 dark:text-amber-500">Taxes</span><span class="text-muted-foreground">Accountant</span>!
                </h1>
                <p class="mt-2 text-muted-foreground leading-relaxed">
                    We're glad to have you here. Stay on top of your tax filings, track your progress, and access
                    expert support—all in one place. Let’s make tax season simple and stress-free! 🚀
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <Card class="hover:shadow-md transition-shadow duration-200 dark:border-zinc-800">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-3 text-xl text-amber-600 dark:text-amber-500">
                            <div class="p-2 bg-amber-100 dark:bg-amber-900/20 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            </div>
                            Tax Returns
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground leading-relaxed">
                            View and track the status of your current and past tax returns. Communicate with your accountant and ensure everything is ready.
                        </p>
                    </CardContent>
                    <CardFooter>
                        <Button as-child variant="ghost" class="group text-amber-700 dark:text-amber-400 hover:text-amber-800 hover:bg-amber-50 dark:hover:bg-amber-950/30 p-0 hover:px-2 transition-all">
                            <Link href="/tax-returns">
                                View your returns
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1">
                                    <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.79a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                                </svg>
                            </Link>
                        </Button>
                    </CardFooter>
                </Card>

                <Card class="hover:shadow-md transition-shadow duration-200 dark:border-zinc-800">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-3 text-xl text-amber-600 dark:text-amber-500">
                            <div class="p-2 bg-amber-100 dark:bg-amber-900/20 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </div>
                            File Tax
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground leading-relaxed">
                            Start a new tax filing. We support W-2s, 1099s, and other income sources. Our wizard makes the process simple.
                        </p>
                    </CardContent>
                    <CardFooter>
                        <Button as-child variant="ghost" class="group text-amber-700 dark:text-amber-400 hover:text-amber-800 hover:bg-amber-50 dark:hover:bg-amber-950/30 p-0 hover:px-2 transition-all">
                            <Link v-if="isAccountant" href="/file-taxes-for-client">
                                Start filing now
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1">
                                    <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.79a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                                </svg>
                            </Link>
                            <Link v-else href="/file-taxes">
                                Start filing now
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1">
                                    <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.79a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                                </svg>
                            </Link>
                        </Button>
                    </CardFooter>
                </Card>

                <Card v-if="isAdmin" class="hover:shadow-md transition-shadow duration-200 dark:border-zinc-800">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-3 text-xl text-amber-600 dark:text-amber-500">
                            <div class="p-2 bg-amber-100 dark:bg-amber-900/20 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                            </div>
                            Manage Bookings
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground leading-relaxed">
                            View and manage all client booking requests. Set availability and confirm appointments.
                        </p>
                    </CardContent>
                    <CardFooter>
                        <Button as-child variant="ghost" class="group text-amber-700 dark:text-amber-400 hover:text-amber-800 hover:bg-amber-50 dark:hover:bg-amber-950/30 p-0 hover:px-2 transition-all">
                            <Link href="/bookings">
                                Manage bookings
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1">
                                    <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.79a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                                </svg>
                            </Link>
                        </Button>
                    </CardFooter>
                </Card>

                <Card v-if="isAdmin" class="hover:shadow-md transition-shadow duration-200 dark:border-zinc-800">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-3 text-xl text-amber-600 dark:text-amber-500">
                            <div class="p-2 bg-amber-100 dark:bg-amber-900/20 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            Set Availability
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground leading-relaxed">
                            Configure your working hours and available time slots for client consultations.
                        </p>
                    </CardContent>
                    <CardFooter>
                        <Button as-child variant="ghost" class="group text-amber-700 dark:text-amber-400 hover:text-amber-800 hover:bg-amber-50 dark:hover:bg-amber-950/30 p-0 hover:px-2 transition-all">
                            <Link href="/availability">
                                Set availability
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1">
                                    <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.79a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                                </svg>
                            </Link>
                        </Button>
                    </CardFooter>
                </Card>

                <Card class="md:col-span-2 border-dashed bg-muted/30 hover:bg-muted/50 transition-colors">
                    <div class="flex flex-col md:flex-row items-center p-6 gap-6">
                        <div class="p-3 bg-amber-100 dark:bg-amber-900/20 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 text-amber-600 dark:text-amber-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                            </svg>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h3 class="text-lg font-semibold text-foreground">Give us a Review</h3>
                            <p class="text-sm text-muted-foreground mt-1">
                                Tell us about your experience with our services. Your feedback helps us improve.
                            </p>
                        </div>
                        <Button as-child class="bg-amber-600 hover:bg-amber-700 text-white dark:text-white shrink-0">
                            <Link href="/testimonials/create">
                                Write a Review
                            </Link>
                        </Button>
                    </div>
                </Card>

            </div>
        </div>
    </AppLayout>
</template>
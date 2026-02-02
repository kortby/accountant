<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    content: ''
});

const submit = () => {
    form.post(route('store-message'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

// Access flash messages from Laravel session
const page = usePage();
const flashSuccess = computed(() => page.props.jetstream?.flash?.banner || page.props.flash?.banner);
</script>

<template>
    <Head title="Contact Us" />
    <MainLayout>
        
        <div class="bg-white py-16 px-4 overflow-hidden sm:px-6 lg:px-8 lg:py-24">
            <div class="relative max-w-7xl mx-auto">
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Contact Us</h2>
                    <p class="mt-4 text-lg leading-6 text-slate-500">
                        Have questions about your taxes? Send us a message and we'll get back to you within 24 hours.
                    </p>
                </div>

                <!-- Contact Information Cards -->
                <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-3">
                    <div class="text-center">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-orange-600 text-white mx-auto">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-slate-900">Address</h3>
                        <p class="mt-2 text-base text-slate-500">
                            4446 Manhattan Beach blvd<br />
                            Lawndale, CA 90260
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-orange-600 text-white mx-auto">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-slate-900">Telephone</h3>
                        <p class="mt-2 text-base text-slate-500">
                            <a href="tel:+13108488598" class="hover:text-orange-600">+1 (310) 848-8598</a>
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-orange-600 text-white mx-auto">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-slate-900">Email</h3>
                        <p class="mt-2 text-base text-slate-500">
                            <a href="mailto:support@taxesaccountant.co" class="hover:text-orange-600">support@taxesaccountant.co</a>
                        </p>
                    </div>
                </div>

                <div v-if="flashSuccess" class="mt-6 bg-green-50 border-l-4 border-green-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ flashSuccess }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-12 max-w-xl mx-auto">
                    <form @submit.prevent="submit" class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                        <div>
                            <label for="first-name" class="block text-sm font-medium text-slate-700">First name</label>
                            <div class="mt-1">
                                <Input v-model="form.first_name" type="text" name="first-name" id="first-name" autocomplete="given-name"  />
                                <div v-if="form.errors.first_name" class="text-red-500 text-xs mt-1">{{ form.errors.first_name }}</div>
                            </div>
                        </div>
                        <div>
                            <label for="last-name" class="block text-sm font-medium text-slate-700">Last name</label>
                            <div class="mt-1">
                                <Input v-model="form.last_name" type="text" name="last-name" id="last-name" autocomplete="family-name"  />
                                <div v-if="form.errors.last_name" class="text-red-500 text-xs mt-1">{{ form.errors.last_name }}</div>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                            <div class="mt-1">
                                <Input v-model="form.email" id="email" name="email" type="email" autocomplete="email"  />
                                <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="phone" class="block text-sm font-medium text-slate-700">Phone Number</label>
                            <div class="mt-1">
                                <Input v-model="form.phone" type="text" name="phone" id="phone" autocomplete="tel"  />
                                <div v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</div>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="message" class="block text-sm font-medium text-slate-700">Message</label>
                            <div class="mt-1">
                                <Textarea v-model="form.content" id="message" rows="4" />
                                <div v-if="form.errors.content" class="text-red-500 text-xs mt-1">{{ form.errors.content }}</div>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <button type="submit" :disabled="form.processing" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50">
                                <span v-if="form.processing">Sending...</span>
                                <span v-else>Send Message</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </MainLayout>
</template>
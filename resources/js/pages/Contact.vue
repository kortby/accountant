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
            <div class="relative max-w-xl mx-auto">
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Contact Us</h2>
                    <p class="mt-4 text-lg leading-6 text-slate-500">
                        Have questions about your taxes? Send us a message and we'll get back to you within 24 hours.
                    </p>
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

                <div class="mt-12">
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
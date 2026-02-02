<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

const props = defineProps({
    services: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    author_name: '',
    author_title: '',
    author_company: '',
    author_location: '',
    content: '',
    rating: 5,
    project_type: '',
    service_id: '',
    image: null,
});

const submit = () => {
    form.post('/testimonials', {
        forceFormData: true,
        preserveScroll: true,
    });
};

const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.image = file;
    }
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Write Review', href: '/testimonials/create' },
];
</script>

<template>
    <Head title="Write a Review" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-3xl mx-auto p-6 lg:p-8">
            <Card>
                <CardHeader>
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-amber-100 dark:bg-amber-900/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-amber-600 dark:text-amber-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                            </svg>
                        </div>
                        <div>
                            <CardTitle>Write a Review</CardTitle>
                            <CardDescription class="mt-1">
                                Share your experience with our services. Your feedback helps us improve and helps others make informed decisions.
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Author Name -->
                        <div class="space-y-2">
                            <Label for="author_name">Your Name <span class="text-red-500">*</span></Label>
                            <Input
                                id="author_name"
                                v-model="form.author_name"
                                type="text"
                                placeholder="John Doe"
                                required
                            />
                            <p v-if="form.errors.author_name" class="text-sm text-red-600">
                                {{ form.errors.author_name }}
                            </p>
                        </div>

                        <!-- Author Title -->
                        <div class="space-y-2">
                            <Label for="author_title">Your Title/Position</Label>
                            <Input
                                id="author_title"
                                v-model="form.author_title"
                                type="text"
                                placeholder="e.g., Business Owner, Homeowner"
                            />
                            <p v-if="form.errors.author_title" class="text-sm text-red-600">
                                {{ form.errors.author_title }}
                            </p>
                        </div>

                        <!-- Two Column Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Author Company -->
                            <div class="space-y-2">
                                <Label for="author_company">Company Name</Label>
                                <Input
                                    id="author_company"
                                    v-model="form.author_company"
                                    type="text"
                                    placeholder="ABC Corporation"
                                />
                                <p v-if="form.errors.author_company" class="text-sm text-red-600">
                                    {{ form.errors.author_company }}
                                </p>
                            </div>

                            <!-- Author Location -->
                            <div class="space-y-2">
                                <Label for="author_location">Location</Label>
                                <Input
                                    id="author_location"
                                    v-model="form.author_location"
                                    type="text"
                                    placeholder="Los Angeles, CA"
                                />
                                <p v-if="form.errors.author_location" class="text-sm text-red-600">
                                    {{ form.errors.author_location }}
                                </p>
                            </div>
                        </div>

                        <!-- Rating -->
                        <div class="space-y-2">
                            <Label for="rating">Rating <span class="text-red-500">*</span></Label>
                            <div class="flex items-center gap-2">
                                <button
                                    v-for="star in 5"
                                    :key="star"
                                    type="button"
                                    @click="form.rating = star"
                                    class="focus:outline-none"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        :fill="star <= form.rating ? 'currentColor' : 'none'"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        :class="[
                                            'size-8 transition-colors',
                                            star <= form.rating
                                                ? 'text-amber-500'
                                                : 'text-slate-300 hover:text-amber-300'
                                        ]"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                    </svg>
                                </button>
                                <span class="ml-2 text-sm text-slate-600">{{ form.rating }} star{{ form.rating !== 1 ? 's' : '' }}</span>
                            </div>
                            <p v-if="form.errors.rating" class="text-sm text-red-600">
                                {{ form.errors.rating }}
                            </p>
                        </div>

                        <!-- Review Content -->
                        <div class="space-y-2">
                            <Label for="content">Your Review <span class="text-red-500">*</span></Label>
                            <Textarea
                                id="content"
                                v-model="form.content"
                                placeholder="Share your experience with our services..."
                                rows="6"
                                required
                            />
                            <p class="text-xs text-slate-500">
                                {{ form.content.length }} characters
                            </p>
                            <p v-if="form.errors.content" class="text-sm text-red-600">
                                {{ form.errors.content }}
                            </p>
                        </div>

                        <!-- Two Column Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Service -->
                            <div class="space-y-2">
                                <Label for="service_id">Service Used</Label>
                                <select
                                    id="service_id"
                                    v-model="form.service_id"
                                    class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm ring-offset-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option value="">Select a service</option>
                                    <option
                                        v-for="service in services"
                                        :key="service.id"
                                        :value="service.id"
                                    >
                                        {{ service.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.service_id" class="text-sm text-red-600">
                                    {{ form.errors.service_id }}
                                </p>
                            </div>

                            <!-- Project Type -->
                            <div class="space-y-2">
                                <Label for="project_type">Project Type</Label>
                                <Input
                                    id="project_type"
                                    v-model="form.project_type"
                                    type="text"
                                    placeholder="e.g., Tax Filing, Consultation"
                                />
                                <p v-if="form.errors.project_type" class="text-sm text-red-600">
                                    {{ form.errors.project_type }}
                                </p>
                            </div>
                        </div>

                        <!-- Photo Upload -->
                        <div class="space-y-2">
                            <Label for="image">Your Photo (Optional)</Label>
                            <Input
                                id="image"
                                type="file"
                                accept="image/*"
                                @change="handleImageChange"
                            />
                            <p class="text-xs text-slate-500">
                                Upload a professional photo (JPG, PNG, max 2MB)
                            </p>
                            <p v-if="form.errors.image" class="text-sm text-red-600">
                                {{ form.errors.image }}
                            </p>
                        </div>

                        <!-- Info Notice -->
                        <div class="rounded-lg border border-amber-200 bg-amber-50 dark:bg-amber-950/20 p-4">
                            <div class="flex gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-amber-600 flex-shrink-0 mt-0.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                </svg>
                                <div class="text-sm text-amber-800 dark:text-amber-200">
                                    <p class="font-medium">Review Guidelines</p>
                                    <ul class="mt-2 space-y-1 list-disc list-inside">
                                        <li>Your review will be reviewed before being published</li>
                                        <li>Be honest and constructive in your feedback</li>
                                        <li>Focus on your experience with our services</li>
                                        <li>Avoid sharing sensitive personal information</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-3 pt-4">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-amber-600 hover:bg-amber-700 text-white"
                            >
                                <span v-if="form.processing">Submitting...</span>
                                <span v-else>Submit Review</span>
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                @click="$inertia.visit('/dashboard')"
                                :disabled="form.processing"
                            >
                                Cancel
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

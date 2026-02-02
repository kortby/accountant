<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref, watch } from 'vue';
import { Input } from '@/components/ui/input';

const props = defineProps({
    posts: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    categories: {
        type: Array,
        default: () => [],
    },
    tags: {
        type: Array,
        default: () => [],
    },
});

const search = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');
const selectedTag = ref(props.filters.tag || '');

const applyFilters = () => {
    router.get('/blog', {
        search: search.value || undefined,
        category: selectedCategory.value || undefined,
        tag: selectedTag.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    search.value = '';
    selectedCategory.value = '';
    selectedTag.value = '';
    router.get('/blog', {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Debounce search
let searchTimeout = null;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
});

watch([selectedCategory, selectedTag], () => {
    applyFilters();
});

const getInitials = (name) => {
    if (!name) return 'A';
    const parts = name.split(' ').filter(Boolean);
    if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
    return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
};
</script>

<template>
    <Head title="Blog - Tax Tips & Insights" />

    <MainLayout>
        <!-- Hero Section -->
        <div class="bg-slate-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                <div class="text-center">
                    <h1 class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight">
                        Tax Tips & Insights
                    </h1>
                    <p class="mt-4 text-xl text-slate-300 max-w-3xl mx-auto">
                        Expert advice, industry updates, and practical tips to help you navigate the world of taxes with confidence.
                    </p>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class=" gap-4 flex justify-between items-center">
                    <!-- Search -->
                    <div class="md:col-span-2 pt-4">
                        <div class="flex items-center justify-center relative">

                            <div class="z-10"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="pointer-events-none col-start-1 row-start-1 ml-3 size-5 self-center text-gray-400 sm:size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg></div>
                            <input
                                class="-m-8 col-start-1 row-start-1 block w-full rounded-md bg-white py-1.5 pr-3 pl-10 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:pl-9 sm:text-sm/6"
                                v-model="search"
                                type="text"
                                placeholder="Search articles..."
                            />
                        </div>

                    </div>

                    <!-- Category Filter -->
                    <div class="flex justify-center items-center gap-3">

                        <label for="category" class="block text-sm/6 font-medium text-gray-900">Categories</label>
                        <div class="mt-2 grid grid-cols-1">
                            <select  v-model="selectedCategory" id="category" name="category" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-indigo-600 sm:text-sm/6">
                                <option selected="">All Categories</option>
                                <option
                                    v-for="category in categories"
                                    :key="category.slug"
                                    :value="category.slug"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <svg class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>

                        </div>
                    </div>
                </div>

                    
            </div>
        </div>

        <!-- Blog Posts Grid -->
        <div class="bg-slate-50 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Results Count -->
                <div class="mb-6">
                    <p class="text-sm text-slate-600">
                        Showing {{ posts.data.length }} of {{ posts.total }} articles
                    </p>
                </div>

                <!-- Posts Grid -->
                <div v-if="posts.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <Link
                        v-for="post in posts.data"
                        :key="post.id"
                        :href="`/blog/${post.slug}`"
                        class="group"
                    >
                        <article class="bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200 hover:shadow-md transition-all duration-200 h-full flex flex-col">
                            <!-- Featured Image -->
                            <div class="aspect-w-16 aspect-h-9 bg-slate-200 overflow-hidden">
                                <img
                                    v-if="post.featured_image"
                                    :src="post.featured_image"
                                    :alt="post.title"
                                    class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-200"
                                />
                                <div v-else class="w-full h-48 bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-6 flex-1 flex flex-col">
                                <!-- Category Badge -->
                                <div class="mb-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                        {{ post.category.name }}
                                    </span>
                                </div>

                                <!-- Title -->
                                <h2 class="text-xl font-bold text-slate-900 group-hover:text-orange-600 transition-colors mb-2">
                                    {{ post.title }}
                                </h2>

                                <!-- Excerpt -->
                                <p class="text-slate-600 text-sm mb-4 flex-1 line-clamp-3">
                                    {{ post.excerpt }}
                                </p>

                                <!-- Tags -->
                                <div v-if="post.tags && post.tags.length" class="flex flex-wrap gap-1 mb-4">
                                    <span
                                        v-for="tag in post.tags.slice(0, 3)"
                                        :key="tag.slug"
                                        class="text-xs text-slate-500"
                                    >
                                        #{{ tag.name }}
                                    </span>
                                </div>

                                <!-- Meta Info -->
                                <div class="flex items-center justify-between text-sm text-slate-500 pt-4 border-t border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <div class="h-8 w-8 rounded-full bg-orange-200 flex items-center justify-center text-orange-700 font-semibold text-xs">
                                            {{ getInitials(post.author.name) }}
                                        </div>
                                        <span>{{ post.author.name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span>{{ post.reading_time }}</span>
                                    </div>
                                </div>

                                <!-- Date -->
                                <div class="mt-2 text-xs text-slate-400">
                                    {{ post.published_at }}
                                </div>
                            </div>
                        </article>
                    </Link>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-slate-900">No articles found</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Try adjusting your search or filter to find what you're looking for.
                    </p>
                    <div class="mt-6">
                        <button
                            @click="clearFilters"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700"
                        >
                            Clear filters
                        </button>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="posts.data.length > 0 && (posts.prev_page_url || posts.next_page_url)" class="mt-12 flex items-center justify-between border-t border-slate-200 pt-8">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link
                            v-if="posts.prev_page_url"
                            :href="posts.prev_page_url"
                            class="relative inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50"
                        >
                            Previous
                        </Link>
                        <Link
                            v-if="posts.next_page_url"
                            :href="posts.next_page_url"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50"
                        >
                            Next
                        </Link>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-slate-700">
                                Showing
                                <span class="font-medium">{{ posts.from }}</span>
                                to
                                <span class="font-medium">{{ posts.to }}</span>
                                of
                                <span class="font-medium">{{ posts.total }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                <Link
                                    v-if="posts.prev_page_url"
                                    :href="posts.prev_page_url"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50"
                                >
                                    <span class="sr-only">Previous</span>
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </Link>
                                
                                <!-- Page Numbers -->
                                <template v-for="(link, index) in posts.links" :key="index">
                                    <Link
                                        v-if="link.url && link.label !== 'Previous' && link.label !== 'Next'"
                                        :href="link.url"
                                        :class="[
                                            link.active
                                                ? 'z-10 bg-orange-50 border-orange-500 text-orange-600'
                                                : 'bg-white border-slate-300 text-slate-500 hover:bg-slate-50',
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                                        ]"
                                    >
                                        {{ link.label }}
                                    </Link>
                                </template>

                                <Link
                                    v-if="posts.next_page_url"
                                    :href="posts.next_page_url"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50"
                                >
                                    <span class="sr-only">Next</span>
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </Link>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped>
/* Line clamp utility */
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

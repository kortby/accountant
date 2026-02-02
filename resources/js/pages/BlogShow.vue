<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
});

const formattedDate = computed(() => {
    return props.post.published_at;
});

const shareOnTwitter = () => {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(props.post.title);
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
};

const shareOnFacebook = () => {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
};

const shareOnLinkedIn = () => {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank', 'width=600,height=400');
};

const copyLink = () => {
    navigator.clipboard.writeText(window.location.href);
    alert('Link copied to clipboard!');
};
</script>

<template>
    <Head :title="post.title" />

    <MainLayout>
        <!-- Hero Section with Featured Image -->
        <div class="relative bg-slate-900">
            <div class="absolute inset-0 bg-amber-300 opacity-20"></div>
            <div class="relative h-96 w-full overflow-hidden">
                <img
                    v-if="post.featured_image"
                    :src="post.featured_image"
                    :alt="post.title"
                    class="w-full h-full object-cover opacity-30"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40"></div>
            </div>
            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
                <div class="text-center">
                    <!-- Category Badge -->
                    <Link
                        :href="`/blog/category/${post.category.slug}`"
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-500 text-white hover:bg-orange-600 transition-colors"
                    >
                        {{ post.category.name }}
                    </Link>

                    <!-- Title -->
                    <h1 class="mt-4 text-4xl sm:text-5xl font-extrabold text-white tracking-tight">
                        {{ post.title }}
                    </h1>

                    <!-- Meta Information -->
                    <div class="mt-6 flex flex-col items-center justify-center gap-3 space-x-6 text-slate-300">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-orange-200 flex items-center justify-center text-orange-700 font-bold">
                                {{ post.author.name.charAt(0) }}
                            </div>
                            <div class="ml-3 text-left">
                                <p class="text-sm font-medium text-white">{{ post.author.name }}</p>
                                <p class="text-xs text-slate-300">{{ post.author.role }}</p>
                            </div>
                        </div>
                        <div>

                        <time :datetime="formattedDate" class="text-sm">
                            {{ formattedDate }}
                        </time>
                        <span class="text-slate-400">•</span>
                        <span class="text-sm">{{ post.reading_time }}</span>
                        <span class="text-slate-400">•</span>
                        <span class="text-sm">{{ post.view_count }} views</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <article class="relative py-16 bg-white overflow-hidden">
            <div class="relative px-4 sm:px-6 lg:px-8">
                <div class="max-w-4xl mx-auto">
                    <!-- Featured Image (if not shown in hero) -->
                    <!-- <div v-if="post.featured_image" class="mb-8 rounded-lg overflow-hidden shadow-lg">
                        <img
                            :src="post.featured_image"
                            :alt="post.title"
                            class="w-full h-auto"
                        />
                    </div> -->

                    <!-- Article Content -->
                    <div
                        class="prose prose-lg prose-slate max-w-none
                               prose-headings:font-bold prose-headings:text-slate-900
                               prose-p:text-slate-600 prose-p:leading-relaxed
                               prose-a:text-orange-600 prose-a:no-underline hover:prose-a:underline
                               prose-strong:text-slate-900 prose-strong:font-semibold
                               prose-ul:list-disc prose-ol:list-decimal
                               prose-li:text-slate-600
                               prose-blockquote:border-l-4 prose-blockquote:border-orange-500 prose-blockquote:pl-4 prose-blockquote:italic
                               prose-code:text-orange-600 prose-code:bg-slate-100 prose-code:px-1 prose-code:py-0.5 prose-code:rounded
                               prose-img:rounded-lg prose-img:shadow-md"
                        v-html="post.content"
                    ></div>

                    <!-- Tags -->
                    <div v-if="post.tags && post.tags.length" class="mt-12 pt-8 border-t border-slate-200">
                        <h3 class="text-sm font-semibold text-slate-900 mb-3">Tags:</h3>
                        <div class="flex flex-wrap gap-2">
                            <Link
                                v-for="tag in post.tags"
                                :key="tag.slug"
                                :href="`/blog/tag/${tag.slug}`"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors"
                            >
                                #{{ tag.name }}
                            </Link>
                        </div>
                    </div>

                    <!-- Share Buttons -->
                    <div class="mt-8 pt-8 border-t border-slate-200">
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">Share this article:</h3>
                        <div class="flex gap-3">
                            <button
                                @click="shareOnFacebook"
                                class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors"
                                title="Share on Facebook"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </button>
                            <button
                                @click="shareOnLinkedIn"
                                class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-700 text-white hover:bg-blue-800 transition-colors"
                                title="Share on LinkedIn"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </button>
                            <button
                                @click="copyLink"
                                class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-600 text-white hover:bg-slate-700 transition-colors"
                                title="Copy link"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Author Bio -->
                    <div v-if="post.author.bio" class="mt-12 p-6 bg-slate-50 rounded-lg border border-slate-200">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">About the Author</h3>
                        <div class="flex items-start gap-4">
                            <div class="h-16 w-16 rounded-full bg-orange-200 flex items-center justify-center text-orange-700 font-bold text-xl flex-shrink-0">
                                {{ post.author.name.charAt(0) }}
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-900">{{ post.author.name }}</h4>
                                <p class="text-sm text-slate-600 mb-2">{{ post.author.role }}</p>
                                <p class="text-slate-600">{{ post.author.bio }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>

        <!-- Related Posts -->
        <section v-if="post.related_posts && post.related_posts.length" class="py-16 bg-slate-50 border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-slate-900 mb-8">Related Articles</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <Link
                        v-for="relatedPost in post.related_posts"
                        :key="relatedPost.slug"
                        :href="`/blog/${relatedPost.slug}`"
                        class="group"
                    >
                        <div class="bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
                            <div class="aspect-w-16 aspect-h-9 bg-slate-200">
                                <img
                                    v-if="relatedPost.featured_image"
                                    :src="relatedPost.featured_image"
                                    :alt="relatedPost.title"
                                    class="w-full h-48 object-cover"
                                />
                                <div v-else class="w-full h-48 bg-slate-200"></div>
                            </div>
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-slate-900 group-hover:text-orange-600 transition-colors">
                                    {{ relatedPost.title }}
                                </h3>
                                <p class="mt-2 text-sm text-slate-600 line-clamp-2">
                                    {{ relatedPost.excerpt }}
                                </p>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Back to Blog -->
        <div class="bg-white border-t border-slate-200">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <Link
                    href="/blog"
                    class="inline-flex items-center text-orange-600 hover:text-orange-700 font-medium"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to all articles
                </Link>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped>
/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

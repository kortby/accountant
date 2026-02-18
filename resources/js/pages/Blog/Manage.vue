<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { debounce } from 'lodash';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

const props = defineProps({
    posts: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');

const handleSearch = debounce((value) => {
    router.get('/blogs', {
        search: value || undefined,
        status: statusFilter.value === 'all' ? undefined : statusFilter.value,
    }, { preserveState: true, replace: true });
}, 300);

const handleStatusChange = (value) => {
    statusFilter.value = value;
    router.get('/blogs', {
        search: search.value || undefined,
        status: value === 'all' ? undefined : value,
    }, { preserveState: true, replace: true });
};

const getStatusColor = (status) => {
    switch (status) {
        case 'published':
            return 'bg-green-100 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800';
        case 'draft':
            return 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700';
        default:
            return 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700';
    }
};

const confirmDelete = (post) => {
    if (confirm(`Are you sure you want to delete "${post.title}"?`)) {
        router.delete(`/blogs/${post.id}`, { preserveScroll: true });
    }
};
</script>

<template>
    <AppLayout>
        <Head title="Manage Blog Posts" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">

                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Blog Posts</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage all blog posts, drafts and published.</p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                            <div class="relative w-full sm:w-64">
                                <Input
                                    v-model="search"
                                    @input="handleSearch($event.target.value)"
                                    placeholder="Search posts..."
                                    class="pl-10"
                                />
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                            </div>

                            <Select :model-value="statusFilter" @update:model-value="handleStatusChange">
                                <SelectTrigger class="w-full sm:w-[160px]">
                                    <SelectValue placeholder="Filter by Status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Statuses</SelectItem>
                                    <SelectItem value="draft">Draft</SelectItem>
                                    <SelectItem value="published">Published</SelectItem>
                                </SelectContent>
                            </Select>

                            <Link href="/blogs/create">
                                <Button class="w-full sm:w-auto">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    New Post
                                </Button>
                            </Link>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[360px]">Title</TableHead>
                                    <TableHead>Category</TableHead>
                                    <TableHead>Status</TableHead>
                                    <!-- <TableHead>Author</TableHead> -->
                                    <TableHead>Published</TableHead>
                                    <TableHead>Updated</TableHead>
                                    <TableHead class="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>

                            <TableBody>
                                <TableRow v-if="posts.data.length === 0">
                                    <TableCell colspan="7" class="h-24 text-center text-gray-500 dark:text-gray-400">
                                        No blog posts found matching your criteria.
                                    </TableCell>
                                </TableRow>

                                <TableRow v-for="post in posts.data" :key="post.id">
                                    <TableCell class="font-medium max-w-[360px]">
                                        <div class="truncate">{{ post.title }}</div>
                                        <div v-if="post.excerpt" class="text-xs text-gray-500 dark:text-gray-400 font-normal truncate mt-0.5">
                                            {{ post.excerpt }}
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <span v-if="post.category" class="text-sm">{{ post.category.name }}</span>
                                        <span v-else class="text-gray-400 text-sm italic">None</span>
                                    </TableCell>
                                    <TableCell>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border capitalize"
                                            :class="getStatusColor(post.status)"
                                        >
                                            {{ post.status }}
                                        </span>
                                    </TableCell>
                                    <!-- <TableCell class="text-sm">{{ post.author.name }}</TableCell> -->
                                    <TableCell class="text-sm">{{ post.published_at || '—' }}</TableCell>
                                    <TableCell class="text-sm">{{ post.updated_at }}</TableCell>
                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-3">
                                            <Link
                                                :href="'/blog/' + post.slug"
                                                class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-medium text-sm"
                                            >
                                                View
                                            </Link>
                                            <Link
                                                :href="'/blogs/' + post.id + '/edit'"
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-sm"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="confirmDelete(post)"
                                                class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 font-medium text-sm"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6" v-if="posts.links.length > 3">
                        <Pagination>
                            <PaginationContent>
                                <template v-for="(link, index) in posts.links" :key="index">
                                    <PaginationPrevious
                                        v-if="link.label.includes('Previous')"
                                        :href="link.url ?? '#'"
                                        :class="{ 'pointer-events-none opacity-50': !link.url }"
                                    />

                                    <PaginationNext
                                        v-else-if="link.label.includes('Next')"
                                        :href="link.url ?? '#'"
                                        :class="{ 'pointer-events-none opacity-50': !link.url }"
                                    />

                                    <PaginationEllipsis v-else-if="link.label === '...'" />

                                    <PaginationItem v-else>
                                        <Link
                                            :href="link.url ?? '#'"
                                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10"
                                            :class="{ 'bg-accent text-accent-foreground border border-input': link.active }"
                                        >
                                            {{ link.label }}
                                        </Link>
                                    </PaginationItem>
                                </template>
                            </PaginationContent>
                        </Pagination>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>

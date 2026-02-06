<script setup>
    import {
        ref,
        watch
    } from 'vue';
    import {
        Head,
        Link,
        router
    } from '@inertiajs/vue3';
    import AppLayout from '@/layouts/AppLayout.vue';
    import {
        debounce
    } from 'lodash';

    import {
        Button
    } from '@/components/ui/button'


    // Shadcn UI Imports
    import {
        Table,
        TableBody,
        TableCaption,
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

    import {
        Input
    } from '@/components/ui/input';
    import {
        Select,
        SelectContent,
        SelectItem,
        SelectTrigger,
        SelectValue,
    } from '@/components/ui/select';

    // Props
    const props = defineProps({
        taxReturns: Object,
        filters: Object, // Received from controller
    });

    // Get auth user from Inertia page props
    import {
        usePage
    } from '@inertiajs/vue3';
    const page = usePage();
    const authUser = page.props.auth?.user;
    const isAccountant = authUser?.role === 'accountant' || authUser?.role === 'admin';

    // --- Search & Filter State ---
    const search = ref(props.filters?.search || '');
    const statusFilter = ref(props.filters?.status || 'all');

    // --- Watchers for Search/Filter ---
    // Debounce search to avoid too many requests
    const handleSearch = debounce((value) => {
        router.get(
            '/tax-returns', {
                search: value,
                status: statusFilter.value === 'all' ? null : statusFilter.value
            }, {
                preserveState: true,
                replace: true
            }
        );
    }, 300);

    const handleStatusChange = (value) => {
        statusFilter.value = value;
        router.get(
            '/tax-returns', {
                search: search.value,
                status: value === 'all' ? null : value
            }, {
                preserveState: true,
                replace: true
            }
        );
    };

    // --- Formatting Helpers ---
    const getStatusColor = (status) => {
        switch (status) {
            case 'completed':
                return 'bg-green-100 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800';
            case 'in_progress':
                return 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800';
            case 'needs_action':
                return 'bg-red-100 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800';
            case 'draft':
                return 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700';
            default:
                return 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700';
        }
    };

    const formatStatus = (status) => {
        return status ? status.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ') :
            'Unknown';
    };

    const formatDate = (dateString) => {
        if (!dateString) return 'Not submitted';
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
        }).format(amount || 0);
    };
</script>

<template>
    <AppLayout title="Tax Returns">
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Tax Returns
                </h2>
                <div class="flex gap-2">
                    <Link href="/file-taxes"
                        class="inline-flex items-center px-4 py-2 bg-gray-900 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    File New Return
                    </Link>
                    <Link v-if="isAccountant" href="/file-taxes-for-client"
                        class="inline-flex items-center px-4 py-2 bg-orange-600 dark:bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 dark:hover:bg-orange-600 focus:bg-orange-700 dark:focus:bg-orange-600 active:bg-orange-800 dark:active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 dark:focus:ring-orange-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    File For Client
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tax Filings</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage and track all your tax return
                                submissions.</p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                            <div class="relative w-full sm:w-64">
                                <Input v-model="search" @input="handleSearch($event.target.value)"
                                    placeholder="Search by ID or Year..." class="pl-10" />
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                            </div>

                            <Select :model-value="statusFilter" @update:model-value="handleStatusChange">
                                <SelectTrigger class="w-full sm:w-[180px]">
                                    <SelectValue placeholder="Filter by Status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Statuses</SelectItem>
                                    <SelectItem value="draft">Draft</SelectItem>
                                    <SelectItem value="in_progress">In Progress</SelectItem>
                                    <SelectItem value="needs_action">Needs Action</SelectItem>
                                    <SelectItem value="completed">Completed</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="pb-4">

                            <Button>
                                <a v-if="isAccountant" href="/file-taxes-for-client">New File</a>
                                <a v-else href="/file-taxes">New File</a>
                            </Button>
                        </div>
                    </div>

                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[200px]">Tax Year / ID</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Submitted On</TableHead>
                                    <TableHead>Est. Refund / Due</TableHead>
                                    <TableHead class="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>

                            <TableBody>
                                <TableRow v-if="taxReturns.data.length === 0">
                                    <TableCell colspan="5" class="h-24 text-center text-gray-500 dark:text-gray-400">
                                        No tax returns found matching your criteria.
                                    </TableCell>
                                </TableRow>

                                <TableRow v-for="tax in taxReturns.data" :key="tax.id">
                                    <TableCell class="font-medium">
                                        <div>{{ tax . tax_year }} Return</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 font-normal">
                                            #{{ tax . id }}</div>
                                    </TableCell>
                                    <TableCell>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                            :class="getStatusColor(tax.status)">
                                            {{ formatStatus(tax . status) }}
                                        </span>
                                    </TableCell>
                                    <TableCell>{{ formatDate(tax . submitted_at || tax . created_at) }}</TableCell>
                                    <TableCell>
                                        <div v-if="tax.status === 'completed'">
                                            <span v-if="tax.refund_amount > 0"
                                                class="text-green-600 dark:text-green-400 font-medium">
                                                +{{ formatCurrency(tax . refund_amount) }}
                                            </span>
                                            <span v-else-if="tax.amount_due > 0"
                                                class="text-red-600 dark:text-red-400 font-medium">
                                                -{{ formatCurrency(tax . amount_due) }}
                                            </span>
                                            <span v-else class="text-gray-500 dark:text-gray-400">-</span>
                                        </div>
                                        <div v-else class="text-gray-400 dark:text-gray-500 italic text-xs">Processing
                                        </div>
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-3">
                                            <Link :href="'/tax-returns/' + tax.id"
                                                class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-medium text-sm">
                                            View
                                            </Link>
                                            <Link v-if="isAccountant" :href="'/tax-returns/' + tax.id + '/edit'"
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-sm">
                                            Edit
                                            </Link>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div class="mt-6" v-if="taxReturns.links.length > 3">
                        <Pagination>
                            <PaginationContent>
                                <template v-for="(link, index) in taxReturns.links" :key="index">
                                    <PaginationPrevious v-if="link.label.includes('Previous')" :href="link.url ?? '#'"
                                        :class="{ 'pointer-events-none opacity-50': !link.url }" />

                                    <PaginationNext v-else-if="link.label.includes('Next')" :href="link.url ?? '#'"
                                        :class="{ 'pointer-events-none opacity-50': !link.url }" />

                                    <PaginationEllipsis v-else-if="link.label === '...'" />

                                    <PaginationItem v-else>
                                        <Link :href="link.url ?? '#'"
                                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10"
                                            :class="{ 'bg-accent text-accent-foreground border border-input': link.active }">
                                        {{ link . label }}
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

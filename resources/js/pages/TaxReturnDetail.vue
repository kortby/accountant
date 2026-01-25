<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { ref, computed } from 'vue';

// Props
const props = defineProps({
    taxReturn: Object,
    clientProfile: Object,
});

const page = usePage();
const currentUser = page.props.auth?.user || null;
const showSSN = ref(false);
const canViewSSN = computed(() => {
    const role = currentUser?.role;
    return role === 'accountant' || role === 'admin';
});

// --- Formatting Helpers ---
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount || 0);
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const getStatusVariant = (status) => {
    switch (status) {
        case 'completed': return 'success'; // You might need custom classes for success/warning in standard Shadcn
        case 'in_progress': return 'default'; // blue-ish usually
        case 'needs_action': return 'destructive';
        case 'draft': return 'secondary';
        default: return 'outline';
    }
};

const formatStatus = (status) => {
    return status ? status.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ') : 'Unknown';
};

const getIncomeLabel = (type) => {
    const labels = {
        'w2': 'W-2 Employment',
        '1099_nec': '1099-NEC Contractor',
        '1099_int': 'Interest Income',
        '1099_div': 'Dividends',
        'business': 'Business/Sole Prop',
        'rental': 'Rental Property',
        'retirement': 'Retirement/Pension',
        'other': 'Other Income'
    };
    return labels[type] || type;
};
</script>

<template>
    <AppLayout :title="`${taxReturn.tax_year} Tax Return`">
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-3">
                        {{ taxReturn.tax_year }} Tax Return
                        <Badge :variant="getStatusVariant(taxReturn.status)" class="capitalize">
                            {{ formatStatus(taxReturn.status) }}
                        </Badge>
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        ID: #{{ taxReturn.id }} • Submitted on {{ formatDate(taxReturn.submitted_at || taxReturn.created_at) }}
                    </p>
                </div>
                
                <div class="flex gap-2">
                    <Button variant="outline" as="a" :href="route('tax-return.index')">
                        Back to List
                    </Button>
                    <Button v-if="taxReturn.status === 'completed'" class="bg-orange-600 hover:bg-orange-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Download Return
                    </Button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <Card>
                        <CardHeader class="pb-2">
                            <CardTitle class="text-sm font-medium text-gray-500">Total Income</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ formatCurrency(taxReturn.total_income) }}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader class="pb-2">
                            <CardTitle class="text-sm font-medium text-gray-500">Taxable Income</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ formatCurrency(taxReturn.taxable_income) }}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader class="pb-2">
                            <CardTitle class="text-sm font-medium text-gray-500">Total Deductions</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ formatCurrency(taxReturn.total_deductions) }}</div>
                        </CardContent>
                    </Card>
                    <Card :class="{'bg-green-50 border-green-200': taxReturn.refund_amount > 0, 'bg-red-50 border-red-200': taxReturn.amount_due > 0}">
                        <CardHeader class="pb-2">
                            <CardTitle class="text-sm font-medium" :class="taxReturn.refund_amount > 0 ? 'text-green-700' : 'text-red-700'">
                                {{ taxReturn.refund_amount > 0 ? 'Estimated Refund' : 'Amount Due' }}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold" :class="taxReturn.refund_amount > 0 ? 'text-green-700' : 'text-red-700'">
                                {{ taxReturn.refund_amount > 0 ? formatCurrency(taxReturn.refund_amount) : formatCurrency(taxReturn.amount_due) }}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <div class="space-y-6 lg:col-span-1">
                        <Card>
                            <CardHeader>
                                <CardTitle>Taxpayer Info</CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-500">Filing Status</div>
                                    <div class="capitalize">{{ clientProfile?.marital_status?.replace(/_/g, ' ') || 'N/A' }}</div>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-500">Occupation</div>
                                    <div>{{ clientProfile?.occupation || 'N/A' }}</div>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-500">Address</div>
                                    <div>{{ clientProfile?.address }}</div>
                                    <div>{{ clientProfile?.city }}, {{ clientProfile?.state }} {{ clientProfile?.zip_code }}</div>
                                </div>
                                <Separator />
                                <div>
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm font-medium text-gray-500">SSN</div>
                                        <div v-if="canViewSSN">
                                            <Button size="sm" variant="outline" @click="showSSN = !showSSN">
                                                {{ showSSN ? 'Hide' : 'Show' }} SSN
                                            </Button>
                                        </div>
                                    </div>
                                    <div class="font-mono text-sm">
                                        <span v-if="showSSN">{{ clientProfile?.social_security_number || 'N/A' }}</span>
                                        <span v-else>***-**-{{ clientProfile?.social_security_number?.slice(-4) || '****' }}</span>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle class="flex justify-between items-center">
                                    Dependents
                                    <Badge variant="secondary">{{ clientProfile?.dependents?.length || 0 }}</Badge>
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div v-if="clientProfile?.dependents && clientProfile.dependents.length > 0" class="space-y-4">
                                    <div v-for="(dep, index) in clientProfile.dependents" :key="index" class="text-sm">
                                        <div class="flex justify-between items-center ">
                                            <div>
                                                <div class="font-medium">{{ dep.first_name }} {{ dep.last_name }}</div>
                                                <div class="text-xs text-gray-500 capitalize">{{ dep.relationship }}</div>
                                                <div class="text-xs text-gray-500 capitalize">{{ dep.social_security_number }}</div>
                                            </div>
                                            <div class="text-gray-500 text-xs">
                                                {{ formatDate(dep.date_of_birth) }}
                                            </div>
                                        </div>
                                        <div class="mt-4 border-t-2 pt-4">
                                            <div class="flex items-center justify-between">
                                                <div class="text-sm font-medium text-gray-500">SSN</div>
                                                <div v-if="canViewSSN">
                                                    <Button size="sm" variant="outline" @click="showSSN = !showSSN">
                                                        {{ showSSN ? 'Hide' : 'Show' }} SSN
                                                    </Button>
                                                </div>
                                            </div>
                                            <div class="font-mono text-sm">
                                                <span v-if="showSSN">{{ clientProfile?.social_security_number || 'N/A' }}</span>
                                                <span v-else>***-**-{{ clientProfile?.social_security_number?.slice(-4) || '****' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-sm text-gray-500 italic">
                                    No dependents listed.
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <div class="space-y-6 lg:col-span-2">
                        
                        <Card>
                            <CardHeader>
                                <CardTitle>Financial Details</CardTitle>
                                <CardDescription>Reported income sources and deductions.</CardDescription>
                            </CardHeader>
                            <CardContent class="space-y-6">
                                
                                <div>
                                    <h4 class="text-sm font-semibold mb-3 text-gray-900">Income Sources</h4>
                                    <div class="border rounded-md overflow-hidden">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 bg-white">
                                                <tr v-if="taxReturn.income_sources?.length === 0">
                                                    <td colspan="2" class="px-4 py-3 text-sm text-gray-500 text-center italic">No income sources recorded yet.</td>
                                                </tr>
                                                <tr v-for="source in taxReturn.income_sources" :key="source.id">
                                                    <td class="px-4 py-2 text-sm text-gray-900">{{ getIncomeLabel(source.type) }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-900 text-right font-medium">
                                                        {{ formatCurrency(source.amount) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div v-if="taxReturn.deductions?.length > 0">
                                    <h4 class="text-sm font-semibold mb-3 text-gray-900">Deductions</h4>
                                    <div class="border rounded-md overflow-hidden">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 bg-white">
                                                <tr v-for="deduction in taxReturn.deductions" :key="deduction.id">
                                                    <td class="px-4 py-2 text-sm text-gray-900">{{ deduction.description }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-900 text-right font-medium">
                                                        -{{ formatCurrency(deduction.amount) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Uploaded Documents</CardTitle>
                                <CardDescription>Files associated with this return.</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div v-if="taxReturn.media && taxReturn.media.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div 
                                        v-for="file in taxReturn.media" 
                                        :key="file.id" 
                                        class="flex items-center p-3 border rounded-lg hover:bg-slate-50 transition-colors group"
                                    >
                                        <div class="h-10 w-10 flex-shrink-0 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        
                                        <div class="ml-3 flex-1 overflow-hidden">
                                            <p class="text-sm font-medium text-slate-900 truncate" :title="file.file_name">
                                                {{ file.file_name }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ formatSize(file.size) }} • {{ file.mime_type }}
                                            </p>
                                        </div>

                                        <a 
                                            :href="file.original_url" 
                                            target="_blank" 
                                            class="ml-2 text-slate-400 hover:text-orange-600 p-2 rounded-full hover:bg-orange-50 transition-colors"
                                            title="Download"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                                <div v-else class="text-center py-8 bg-slate-50 rounded-lg border border-dashed border-slate-200">
                                    <p class="text-sm text-slate-500">No documents found for this return.</p>
                                </div>
                            </CardContent>
                        </Card>

                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

const props = defineProps({
    taxReturn: Object,
    clientProfile: Object,
    accountants: Array,
});

const page = usePage();
const authUser = page.props.auth?.user;
const showUploadSection = ref(false);
const fileInput = ref(null);

// Initialize form with tax return data
const form = useForm({
    income_sources: props.taxReturn.income_sources.map(source => ({
        id: source.id,
        type: source.type,
        source_name: source.source_name,
        amount: source.amount || 0,
    })),
    deductions: props.taxReturn.deductions.map(deduction => ({
        id: deduction.id,
        category: deduction.category,
        amount: deduction.amount,
        description: deduction.description || '',
    })),
    taxable_income: props.taxReturn.taxable_income || 0,
    tax_liability: props.taxReturn.tax_liability || 0,
    total_credits: props.taxReturn.total_credits || 0,
    status: props.taxReturn.status,
    accountant_notes: props.taxReturn.accountant_notes || '',
    documents: [],
});

// Computed totals
const totalIncome = computed(() => {
    return form.income_sources.reduce((sum, source) => sum + parseFloat(source.amount || 0), 0);
});

const totalDeductions = computed(() => {
    return form.deductions.reduce((sum, deduction) => sum + parseFloat(deduction.amount || 0), 0);
});

const calculatedTaxableIncome = computed(() => {
    return Math.max(0, totalIncome.value - totalDeductions.value);
});

const finalAmount = computed(() => {
    return parseFloat(form.tax_liability || 0) - parseFloat(form.total_credits || 0);
});

const amountDue = computed(() => {
    return finalAmount.value > 0 ? finalAmount.value : 0;
});

const refundAmount = computed(() => {
    return finalAmount.value < 0 ? Math.abs(finalAmount.value) : 0;
});

// Methods
const addDeduction = () => {
    form.deductions.push({
        category: '',
        amount: 0,
        description: '',
    });
};

const removeDeduction = (index) => {
    form.deductions.splice(index, 1);
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount || 0);
};

const formatStatus = (status) => {
    return status ? status.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ') : 'Unknown';
};

const getIncomeTypeLabel = (type) => {
    const labels = {
        'w2': 'W-2 Employment',
        '1099_nec': '1099-NEC (Contractor)',
        '1099_int': '1099-INT (Interest)',
        '1099_div': '1099-DIV (Dividends)',
        'business': 'Business Income',
        'rental': 'Rental Income',
        'retirement': 'Retirement Income',
        'other': 'Other Income',
    };
    return labels[type] || type;
};

const handleFileSelect = (event) => {
    form.documents = Array.from(event.target.files);
};

const removeDocument = (index) => {
    const newFiles = Array.from(form.documents);
    newFiles.splice(index, 1);
    form.documents = newFiles;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const formatSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const submit = () => {
    form.patch(`/tax-returns/${props.taxReturn.id}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout title="Edit Tax Return">
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Edit Tax Return #{{ taxReturn.id }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Tax Year {{ taxReturn.tax_year }} - {{ taxReturn.user.first_name }} {{ taxReturn.user.last_name }}
                    </p>
                </div>
                <Link
                    :href="`/tax-returns/${taxReturn.id}`"
                    class="inline-flex items-center px-4 py-2 bg-gray-500 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 dark:hover:bg-gray-700 focus:bg-gray-600 dark:focus:bg-gray-700 active:bg-gray-700 dark:active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Details
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Client Information Card -->
                <Card class="border border-gray-200 dark:border-gray-700">
                    <CardHeader>
                        <CardTitle class="text-gray-900 dark:text-gray-100">Client Information</CardTitle>
                        <CardDescription class="text-gray-600 dark:text-gray-400">Basic client details for this tax return</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <Label class="text-gray-700 dark:text-gray-300">Name</Label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ taxReturn.user.first_name }} {{ taxReturn.user.last_name }}</p>
                            </div>
                            <div>
                                <Label class="text-gray-700 dark:text-gray-300">Email</Label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ taxReturn.user.email }}</p>
                            </div>
                            <div v-if="clientProfile">
                                <Label class="text-gray-700 dark:text-gray-300">SSN (Masked)</Label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ clientProfile.masked_ssn }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Income Sources Card -->
                <Card class="border border-gray-200 dark:border-gray-700">
                    <CardHeader>
                        <CardTitle class="text-gray-900 dark:text-gray-100">Income Sources</CardTitle>
                        <CardDescription class="text-gray-600 dark:text-gray-400">Enter amounts for each income source type</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-for="(source, index) in form.income_sources" :key="source.id"
                             class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <Label class="text-gray-700 dark:text-gray-300 font-semibold">{{ getIncomeTypeLabel(source.type) }}</Label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ source.source_name }}</p>
                                </div>
                                <div class="w-48">
                                    <Label for="`income-${source.id}`" class="text-gray-700 dark:text-gray-300">Amount</Label>
                                    <Input
                                        :id="`income-${source.id}`"
                                        v-model="form.income_sources[index].amount"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        placeholder="0.00"
                                        class="mt-1"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total Income</span>
                                <span class="text-lg font-bold text-green-600 dark:text-green-400">{{ formatCurrency(totalIncome) }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Deductions Card -->
                <Card class="border border-gray-200 dark:border-gray-700">
                    <CardHeader>
                        <div class="flex justify-between items-start">
                            <div>
                                <CardTitle class="text-gray-900 dark:text-gray-100">Deductions</CardTitle>
                                <CardDescription class="text-gray-600 dark:text-gray-400">Add or edit tax deductions</CardDescription>
                            </div>
                            <Button @click="addDeduction" type="button" size="sm" variant="outline" class="border-gray-300 dark:border-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Deduction
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-if="form.deductions.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            No deductions added yet. Click "Add Deduction" to create one.
                        </div>

                        <div v-for="(deduction, index) in form.deductions" :key="index"
                             class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="space-y-3">
                                <div class="flex justify-between items-start gap-4">
                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <Label :for="`deduction-category-${index}`" class="text-gray-700 dark:text-gray-300">Category</Label>
                                            <Input
                                                :id="`deduction-category-${index}`"
                                                v-model="form.deductions[index].category"
                                                placeholder="e.g., Mortgage Interest, Charitable"
                                                class="mt-1"
                                            />
                                        </div>
                                        <div>
                                            <Label :for="`deduction-amount-${index}`" class="text-gray-700 dark:text-gray-300">Amount</Label>
                                            <Input
                                                :id="`deduction-amount-${index}`"
                                                v-model="form.deductions[index].amount"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                placeholder="0.00"
                                                class="mt-1"
                                            />
                                        </div>
                                    </div>
                                    <Button
                                        @click="removeDeduction(index)"
                                        type="button"
                                        size="sm"
                                        variant="destructive"
                                        class="mt-6"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </Button>
                                </div>
                                <div>
                                    <Label :for="`deduction-description-${index}`" class="text-gray-700 dark:text-gray-300">Description (Optional)</Label>
                                    <Textarea
                                        :id="`deduction-description-${index}`"
                                        v-model="form.deductions[index].description"
                                        placeholder="Add notes about this deduction..."
                                        class="mt-1"
                                        rows="2"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total Deductions</span>
                                <span class="text-lg font-bold text-red-600 dark:text-red-400">{{ formatCurrency(totalDeductions) }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Tax Calculations Card -->
                <Card class="border border-gray-200 dark:border-gray-700">
                    <CardHeader>
                        <CardTitle class="text-gray-900 dark:text-gray-100">Tax Calculations</CardTitle>
                        <CardDescription class="text-gray-600 dark:text-gray-400">Calculate final tax liability and credits</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <Label for="taxable_income" class="text-gray-700 dark:text-gray-300">Taxable Income</Label>
                                <Input
                                    id="taxable_income"
                                    v-model="form.taxable_income"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    :placeholder="formatCurrency(calculatedTaxableIncome)"
                                    class="mt-1"
                                />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Auto-calculated: {{ formatCurrency(calculatedTaxableIncome) }}</p>
                            </div>
                            <div>
                                <Label for="tax_liability" class="text-gray-700 dark:text-gray-300">Tax Liability</Label>
                                <Input
                                    id="tax_liability"
                                    v-model="form.tax_liability"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00"
                                    class="mt-1"
                                />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total tax calculated based on taxable income</p>
                            </div>
                            <div>
                                <Label for="total_credits" class="text-gray-700 dark:text-gray-300">Total Tax Credits</Label>
                                <Input
                                    id="total_credits"
                                    v-model="form.total_credits"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00"
                                    class="mt-1"
                                />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Credits reduce tax liability dollar-for-dollar</p>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700 space-y-3">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-700 dark:text-gray-300">Total Income</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(totalIncome) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-700 dark:text-gray-300">Total Deductions</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">-{{ formatCurrency(totalDeductions) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-700 dark:text-gray-300">Taxable Income</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(form.taxable_income || calculatedTaxableIncome) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-700 dark:text-gray-300">Tax Liability</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(form.tax_liability) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-700 dark:text-gray-300">Tax Credits</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">-{{ formatCurrency(form.total_credits) }}</span>
                            </div>
                            <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                                <div v-if="amountDue > 0" class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Amount Due</span>
                                    <span class="text-lg font-bold text-red-600 dark:text-red-400">{{ formatCurrency(amountDue) }}</span>
                                </div>
                                <div v-else-if="refundAmount > 0" class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Refund Amount</span>
                                    <span class="text-lg font-bold text-green-600 dark:text-green-400">{{ formatCurrency(refundAmount) }}</span>
                                </div>
                                <div v-else class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Final Amount</span>
                                    <span class="text-lg font-bold text-gray-600 dark:text-gray-400">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Status & Notes Card -->
                <Card class="border border-gray-200 dark:border-gray-700">
                    <CardHeader>
                        <CardTitle class="text-gray-900 dark:text-gray-100">Status & Notes</CardTitle>
                        <CardDescription class="text-gray-600 dark:text-gray-400">Update return status and add accountant notes</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label for="status" class="text-gray-700 dark:text-gray-300">Status</Label>
                                <Select v-model="form.status" class="mt-1">
                                    <SelectTrigger>
                                        <SelectValue :placeholder="formatStatus(form.status)" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="draft">Draft</SelectItem>
                                        <SelectItem value="submitted">Submitted</SelectItem>
                                        <SelectItem value="assigned">Assigned</SelectItem>
                                        <SelectItem value="under_review">Under Review</SelectItem>
                                        <SelectItem value="needs_action">Needs Action</SelectItem>
                                        <SelectItem value="completed">Completed</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <Label class="text-gray-700 dark:text-gray-300">Current Status</Label>
                                <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">{{ formatStatus(taxReturn.status) }}</p>
                            </div>
                        </div>
                        <div>
                            <Label for="accountant_notes" class="text-gray-700 dark:text-gray-300">Accountant Notes</Label>
                            <Textarea
                                id="accountant_notes"
                                v-model="form.accountant_notes"
                                placeholder="Add internal notes about this return..."
                                class="mt-1"
                                rows="4"
                            />
                        </div>
                    </CardContent>
                </Card>

                <!-- Upload Additional Documents Card -->
                <Card class="border-gray-200 dark:border-gray-700">
                    <CardHeader>
                        <div class="flex justify-between items-start">
                            <div>
                                <CardTitle class="text-gray-900 dark:text-gray-100">Upload Additional Documents</CardTitle>
                                <CardDescription class="text-gray-600 dark:text-gray-400">Add more tax documents to this return</CardDescription>
                            </div>
                            <Button
                                @click="showUploadSection = !showUploadSection"
                                size="sm"
                                type="button"
                                variant="outline"
                                class="border-orange-500 text-orange-600 hover:bg-orange-50 dark:border-orange-400 dark:text-orange-400 dark:hover:bg-orange-900/30"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                {{ showUploadSection ? 'Hide' : 'Show' }} Upload
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent v-if="showUploadSection" class="space-y-4">
                        <div>
                            <Label for="document-upload" class="text-gray-700 dark:text-gray-300">Select Documents</Label>
                            <Input
                                id="document-upload"
                                ref="fileInput"
                                type="file"
                                multiple
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                @change="handleFileSelect"
                                class="mt-2"
                            />
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                                Accepted formats: PDF, JPG, PNG, DOC, DOCX (max 10MB each)
                            </p>
                        </div>

                        <div v-if="form.documents.length > 0" class="space-y-2">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Selected files:</p>
                            <div class="space-y-2">
                                <div
                                    v-for="(file, index) in form.documents"
                                    :key="index"
                                    class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700"
                                >
                                    <div class="flex items-center space-x-3">
                                        <div class="h-8 w-8 flex-shrink-0 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ file.name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatSize(file.size) }}</p>
                                        </div>
                                    </div>
                                    <Button
                                        @click="removeDocument(index)"
                                        type="button"
                                        size="sm"
                                        variant="ghost"
                                        class="text-red-600 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-900/30"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <!-- Existing Documents -->
                        <div v-if="taxReturn.media && taxReturn.media.length > 0" class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Existing documents:</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div
                                    v-for="file in taxReturn.media"
                                    :key="file.id"
                                    class="flex items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700"
                                >
                                    <div class="h-8 w-8 flex-shrink-0 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1 overflow-hidden">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ file.file_name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ file.mime_type }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-4">
                    <Link
                        :href="`/tax-returns/${taxReturn.id}`"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 dark:hover:bg-gray-700 focus:bg-gray-600 dark:focus:bg-gray-700 active:bg-gray-700 dark:active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                        Cancel
                    </Link>
                    <Button
                        @click="submit"
                        :disabled="form.processing"
                        class="bg-orange-600 hover:bg-orange-700 dark:bg-orange-500 dark:hover:bg-orange-600"
                    >
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Save Changes
                    </Button>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

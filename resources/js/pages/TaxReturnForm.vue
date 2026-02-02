<script setup>
import { ref } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

// --- State ---
const step = ref(1);
const totalSteps = 4;
const dragging = ref(false);
const submissionSuccess = ref(false);

const incomeTypes = [
    { id: 'w2', label: 'W-2 (Employment)' },
    { id: '1099_nec', label: '1099-NEC (Contractor)' },
    { id: '1099_int', label: '1099-INT (Interest)' },
    { id: '1099_div', label: '1099-DIV (Dividends)' },
    { id: 'business', label: 'Business Income' },
    { id: 'rental', label: 'Rental Properties' },
    { id: 'retirement', label: 'Retirement / Pension' },
    { id: 'other', label: 'Other Income' },
];

const props = defineProps({
    clients: {
        type: Array,
        default: () => [],
    },
    isAccountantFiling: {
        type: Boolean,
        default: false,
    },
});

const form = useForm({
    user_id: '',
    tax_year: new Date().getFullYear() - 1,
    // Step 1: Personal
    ssn: '',
    date_of_birth: '',
    occupation: '',
    marital_status: 'single',
    address: '',
    city: '',
    state: '',
    zip_code: '',
    // Step 2: Dependents
    dependents: [],
    // Step 3: Income
    income_types: [],
    // Step 4: Documents
    documents: [],
});

// --- Dependents Logic ---
const addDependent = () => {
    form.dependents.push({
        first_name: '',
        middle_name: '',
        last_name: '',
        social_security_number: '',
        relationship: '',
        date_of_birth: '',
    });
};

const removeDependent = (index) => {
    form.dependents.splice(index, 1);
};

// --- File Upload Logic ---
const handleFileDrop = (e) => {
    dragging.value = false;
    const files = Array.from(e.dataTransfer.files);
    addFiles(files);
};

const handleFileSelect = (e) => {
    const files = Array.from(e.target.files);
    addFiles(files);
};

const addFiles = (files) => {
    form.documents = [...form.documents, ...files];
};

const removeFile = (index) => {
    form.documents.splice(index, 1);
};

const formatSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// --- Smart Error Navigation Logic ---
const fieldsByStep = {
    1: ['ssn', 'date_of_birth', 'occupation', 'marital_status', 'address', 'city', 'state', 'zip_code'],
    2: ['dependents'],
    3: ['income_types'],
    4: ['documents'],
};

const findStepForError = (errorKey) => {
    for (const [stepNum, fields] of Object.entries(fieldsByStep)) {
        // Match exact field or nested array (e.g. 'dependents.0.first_name')
        if (fields.some(field => errorKey === field || errorKey.startsWith(field + '.'))) {
            return parseInt(stepNum);
        }
    }
    return 1; // Default fallback
};

// --- Submit & Navigation ---
const nextStep = () => {
    if (step.value < totalSteps) step.value++;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const prevStep = () => {
    if (step.value > 1) step.value--;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const submit = () => {
    form.post('store-tax', {
        forceFormData: true,
        onError: (errors) => {
            const errorKeys = Object.keys(errors);
            if (errorKeys.length > 0) {
                // Find earliest step with an error
                const targetStep = Math.min(...errorKeys.map(key => findStepForError(key)));
                step.value = targetStep;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
        onSuccess: () => {
            submissionSuccess.value = true;
            form.reset();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
};
</script>

<template>
    <Head title="File Tax Return" />
    <MainLayout>
        <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">

                <div v-if="submissionSuccess" class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center animate-fade-in">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                        <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-900 mb-4">Filing Successful!</h2>
                    <p class="text-lg text-slate-600 mb-8 max-w-lg mx-auto">
                        We have received your tax information and documents. An accountant has been notified and will begin reviewing your case shortly.
                    </p>
                    <div class="flex justify-center gap-4">
                        <Link :href="route('dashboard')" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 shadow-sm transition-colors">
                            Go to Dashboard
                        </Link>
                    </div>
                </div>

                <div v-else>
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-slate-700">Step {{ step }} of {{ totalSteps }}</span>
                            <span class="text-sm font-medium text-orange-600">{{ Math.round((step / totalSteps) * 100) }}% Completed</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2.5">
                            <div class="bg-orange-600 h-2.5 rounded-full transition-all duration-500 ease-out" :style="{ width: (step / totalSteps) * 100 + '%' }"></div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <form @submit.prevent="submit">

                            <div v-show="step === 1" class="p-8 space-y-6 animate-fade-in">
                                <div class="border-b border-slate-100 pb-4 mb-4">
                                    <h2 class="text-2xl font-bold text-slate-900">
                                        {{ isAccountantFiling ? 'Client Selection & Personal Details' : 'Personal Details' }}
                                    </h2>
                                    <p class="text-slate-500">
                                        {{ isAccountantFiling ? 'Select the client and enter their tax information.' : "Let's get your file started with some basics." }}
                                    </p>
                                </div>

                                <!-- Client Selector (Accountant Only) -->
                                <div v-if="isAccountantFiling" class="space-y-2 bg-orange-50 p-4 rounded-lg border border-orange-200">
                                    <label class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-orange-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                        Select Client *
                                    </label>
                                    <select
                                        v-model="form.user_id"
                                        required
                                        class="w-full rounded-md border border-slate-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 focus:outline-none"
                                    >
                                        <option value="">-- Choose a client --</option>
                                        <option v-for="client in clients" :key="client.id" :value="client.id">
                                            {{ client.name }}
                                        </option>
                                    </select>
                                    <span v-if="form.errors.user_id" class="text-xs text-red-600 font-medium">{{ form.errors.user_id }}</span>
                                    <p class="text-xs text-slate-600 mt-1">
                                        <strong>Note:</strong> You are filing on behalf of this client. All information will be saved to their profile.
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium text-slate-700">Tax Year</label>
                                        <input v-model="form.tax_year" type="number" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm bg-slate-100 text-slate-500 cursor-not-allowed" readonly />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium text-slate-700">SSN</label>
                                        <input v-model="form.ssn" type="text" placeholder="XXX-XX-XXXX" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none" />

                                        <span v-if="form.errors.ssn" class="text-xs text-red-500">{{ form.errors.ssn }}</span>
                                        <span v-if="!form.errors.ssn" class="text-xs text-gray-500">eg: XXX-XX-XXXX</span>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium text-slate-700">Date of Birth</label>
                                        <input v-model="form.date_of_birth" type="date" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none" />
                                        <span v-if="form.errors.date_of_birth" class="text-xs text-red-500">{{ form.errors.date_of_birth }}</span>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium text-slate-700">Occupation</label>
                                        <input v-model="form.occupation" type="text" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none" />
                                    </div>
                                    <div class="space-y-2 md:col-span-2">
                                        <label class="text-sm font-medium text-slate-700">Marital Status</label>
                                        <select v-model="form.marital_status" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none">
                                            <option value="single">Single</option>
                                            <option value="married_filing_jointly">Married Filing Jointly</option>
                                            <option value="married_filing_separately">Married Filing Separately</option>
                                            <option value="head_of_household">Head of Household</option>
                                        </select>
                                    </div>
                                    <div class="space-y-2 md:col-span-2">
                                        <label class="text-sm font-medium text-slate-700">Address</label>
                                        <input v-model="form.address" type="text" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none" />
                                        <span v-if="form.errors.address" class="text-xs text-red-500">{{ form.errors.address }}</span>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium text-slate-700">City</label>
                                        <input v-model="form.city" type="text" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none"/>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="text-sm font-medium text-slate-700">State</label>
                                            <input v-model="form.state" type="text" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none" maxlength="2" />
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-sm font-medium text-slate-700">Zip Code</label>
                                            <input v-model="form.zip_code" type="number" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-show="step === 2" class="p-8 space-y-6 animate-fade-in">
                                <div class="flex justify-between items-center border-b border-slate-100 pb-4 mb-4">
                                    <div>
                                        <h2 class="text-2xl font-bold text-slate-900">Dependents</h2>
                                        <p class="text-slate-500">Add children or qualifying relatives.</p>
                                    </div>
                                    <button type="button" @click="addDependent" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 shadow-sm focus:outline-none">
                                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        Add Dependent
                                    </button>
                                </div>

                                <div v-if="form.dependents.length === 0" class="text-center py-12 bg-slate-50 rounded-lg border border-dashed border-slate-300">
                                    <p class="text-slate-500">No dependents added yet.</p>
                                    <button type="button" @click="addDependent" class="text-orange-600 font-medium hover:underline mt-2">Add your first dependent</button>
                                </div>

                                <div class="space-y-6">
                                    <div v-for="(dependent, index) in form.dependents" :key="index" class="bg-slate-50 p-6 rounded-lg border border-slate-200 relative group transition-all hover:shadow-md">
                                        <div class="absolute top-4 right-4">
                                            <button type="button" @click="removeDependent(index)" class="text-slate-400 hover:text-red-500 transition-colors">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                        <h3 class="text-sm font-semibold text-slate-900 mb-4 uppercase tracking-wider">Dependent #{{ index + 1 }}</h3>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div class="space-y-1">
                                                <label class="text-xs font-medium text-slate-600">First Name</label>
                                                <input v-model="dependent.first_name" type="text" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none" />
                                                <span v-if="form.errors[`dependents.${index}.first_name`]" class="text-xs text-red-500">Required</span>
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-medium text-slate-600">Middle Name</label>
                                                <input v-model="dependent.middle_name" type="text" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none" />
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-medium text-slate-600">Last Name</label>
                                                <input v-model="dependent.last_name" type="text" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none" />
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-medium text-slate-600">SSN</label>
                                                <input v-model="dependent.social_security_number" placeholder="XXX-XX-XXXX" type="text" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none" />
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-medium text-slate-600">Date of Birth</label>
                                                <input v-model="dependent.date_of_birth" type="date" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none" />
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-medium text-slate-600">Relationship</label>
                                                <select v-model="dependent.relationship" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none">
                                                    <option value="" disabled>Select...</option>
                                                    <option value="son">Son</option>
                                                    <option value="daughter">Daughter</option>
                                                    <option value="spouse">Spouse</option>
                                                    <option value="parent">Parent</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-show="step === 3" class="p-8 space-y-6 animate-fade-in">
                                <div class="border-b border-slate-100 pb-4 mb-4">
                                    <h2 class="text-2xl font-bold text-slate-900">Income Sources</h2>
                                    <p class="text-slate-500">Check all that apply to you for this year.</p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div v-for="type in incomeTypes" :key="type.id"
                                        class="relative flex items-start p-4 border rounded-lg hover:bg-slate-50 transition-colors cursor-pointer"
                                        :class="form.income_types.includes(type.id) ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500' : 'border-slate-200'">
                                        <div class="flex h-5 items-center">
                                            <input :id="type.id" :value="type.id" v-model="form.income_types" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label :for="type.id" class="font-medium text-slate-900 cursor-pointer">{{ type.label }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="form.errors.income_types" class="text-red-500 text-sm mt-2">{{ form.errors.income_types }}</div>
                            </div>

                            <div v-show="step === 4" class="p-8 space-y-6 animate-fade-in">
                                <div class="border-b border-slate-100 pb-4 mb-4">
                                    <h2 class="text-2xl font-bold text-slate-900">Upload Documents</h2>
                                    <p class="text-slate-500">Please upload pictures or PDFs of your tax forms (W-2s, 1099s, etc).</p>
                                </div>

                                <div
                                    @dragover.prevent="dragging = true"
                                    @dragleave.prevent="dragging = false"
                                    @drop.prevent="handleFileDrop"
                                    class="mt-2 flex justify-center rounded-lg border-2 border-dashed px-6 py-10 transition-colors duration-200"
                                    :class="dragging ? 'border-orange-500 bg-orange-50' : 'border-slate-300 hover:bg-slate-50'"
                                >
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-slate-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                                        </svg>
                                        <div class="mt-4 flex text-sm leading-6 text-slate-600 justify-center">
                                            <label for="file-upload" class="relative cursor-pointer rounded-md bg-white font-semibold text-orange-600 focus-within:outline-none hover:text-orange-500">
                                                <span>Upload a file</span>
                                                <input id="file-upload" name="file-upload" type="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="sr-only" multiple @change="handleFileSelect">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs leading-5 text-slate-500">PDF, JPG, PNG, DOC, DOCX up to 10MB</p>
                                    </div>
                                </div>

                                <ul v-if="form.documents.length > 0" class="mt-4 divide-y divide-slate-100 rounded-md border border-slate-200">
                                    <li v-for="(file, index) in form.documents" :key="index" class="flex items-center justify-between py-3 pl-3 pr-4 text-sm">
                                        <div class="flex items-center">
                                            <span class="truncate font-medium text-slate-600">{{ file.name }}</span>
                                            <span class="ml-2 text-slate-400 text-xs">{{ formatSize(file.size) }}</span>
                                        </div>
                                        <button @click.prevent="removeFile(index)" class="text-red-600 hover:text-red-500 font-medium">Remove</button>
                                    </li>
                                </ul>
                                <div v-if="form.errors.documents" class="text-red-500 text-sm mt-2">{{ form.errors.documents }}</div>
                            </div>

                            <div class="bg-slate-50 px-8 py-4 flex justify-between items-center border-t border-slate-200">
                                <button
                                    v-if="step > 1"
                                    type="button"
                                    @click="prevStep"
                                    class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none"
                                >
                                    Previous
                                </button>
                                <div v-else></div>

                                <button
                                    v-if="step < totalSteps"
                                    type="button"
                                    @click="nextStep"
                                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-800 focus:outline-none"
                                >
                                    Next Step
                                </button>

                                <button
                                    v-if="step === totalSteps"
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-orange-600 px-8 py-2 text-sm font-medium text-white shadow-sm hover:bg-orange-700 focus:outline-none disabled:opacity-50"
                                >
                                    <span v-if="form.processing">Processing...</span>
                                    <span v-else>Submit Return</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.4s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

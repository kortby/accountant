<script setup>
import { ref, onMounted, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import axios from 'axios';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { AlertCircleIcon, CheckCircle2Icon } from 'lucide-vue-next';
import { ref as vueRef } from 'vue';

// --- State Management ---
const loading = ref(true);
const availableSlots = ref([]); // Raw slots from backend
const selectedDate = ref(null);
const submissionSuccess = ref(false);
const showAlert = ref(false);
const alertVariant = ref('success'); // 'success' | 'destructive'
const alertMessage = ref('');
const submitting = ref(false);

// --- Form Setup ---
const form = useForm({
    service_type: '',
    location_type: 'online', // Default
    booking_date: '',
    start_time: '',
    end_time: '',
    availability_id: null,
    client_name: '',
    client_email: '',
});

// --- Constants ---
const services = [
    'Individual Tax Preparation',
    'Business Tax Filing',
    'IRS Audit Consultation',
    'Bookkeeping Review',
    'General Inquiry'
];

// --- API Logic ---
onMounted(async () => {
    try {
        // Request the API route (api.php routes are prefixed with /api)
        const response = await axios.get('/api/public/available-slots'); 
        availableSlots.value = response.data.slots;
    } catch (error) {
        console.error("Failed to fetch slots", error);
    } finally {
        loading.value = false;
    }
});

// --- Computed Properties ---

// 1. Extract unique dates from the available slots
const uniqueDates = computed(() => {
    const dates = [...new Set(availableSlots.value.map(slot => slot.booking_date))];
    return dates.sort(); // Ensure chronological order
});

// 2. Filter slots based on selected date
const slotsForSelectedDate = computed(() => {
    if (!selectedDate.value) return [];
    return availableSlots.value.filter(slot => slot.booking_date === selectedDate.value);
});

// --- Event Handlers ---

const selectDate = (date) => {
    selectedDate.value = date;
    // Reset time if date changes
    form.start_time = ''; 
    form.end_time = '';
    form.booking_date = date;
};

const selectTime = (slot) => {
    form.start_time = slot.start_time;
    form.end_time = slot.end_time;
    form.availability_id = slot.availability_id;
};

const submitBooking = async () => {
    submitting.value = true;
    showAlert.value = false;
    try {
        const payload = {
            service_type: form.service_type,
            location_type: form.location_type,
            booking_date: form.booking_date,
            start_time: form.start_time,
            end_time: form.end_time,
            availability_id: form.availability_id,
            client_name: form.client_name,
            client_email: form.client_email,
        };

        const res = await axios.post('/api/bookings', payload);

        alertVariant.value = 'success';
        alertMessage.value = res.data?.message || 'Booking requested successfully.';
        showAlert.value = true;
        submissionSuccess.value = true;
        form.reset();
    } catch (error) {
        // Map validation/errors to form.errors if available
        if (error.response) {
            const status = error.response.status;
            if (status === 422 && error.response.data.errors) {
                form.errors = error.response.data.errors;
                alertVariant.value = 'destructive';
                alertMessage.value = 'Please fix the highlighted errors.';
            } else {
                alertVariant.value = 'destructive';
                alertMessage.value = error.response.data?.message || 'An error occurred while booking.';
            }
        } else {
            alertVariant.value = 'destructive';
            alertMessage.value = 'Network error. Please try again.';
        }
        showAlert.value = true;
    } finally {
        submitting.value = false;
    }
};

// --- Helpers ---
const formatDate = (dateString) => {
    const options = { weekday: 'short', month: 'short', day: 'numeric' };
    return new Date(dateString + 'T00:00:00').toLocaleDateString('en-US', options);
};

const formatTime = (timeString) => {
    const [hours, minutes] = timeString.split(':');
    const date = new Date();
    date.setHours(hours, minutes);
    return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
};
</script>

<template>
    <Head title="Book Appointment" />
    <MainLayout>
        
        <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto">
                
                <div v-if="submissionSuccess" class="bg-white rounded-lg shadow-lg p-8 text-center border-t-4 border-green-500 animate-fade-in">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                        <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Booking Requested!</h2>
                    <p class="text-slate-600 mb-6">
                        We have received your appointment request. You will receive a confirmation email shortly once our accountant reviews the details.
                    </p>
                    <button @click="submissionSuccess = false" class="text-orange-600 hover:text-orange-700 font-medium">
                        Book another appointment
                    </button>
                </div>

                <div v-else class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-slate-900 px-6 py-6 border-b border-slate-800">
                        <h1 class="text-2xl font-bold text-white">Schedule a Consultation</h1>
                        <p class="text-slate-400 text-sm mt-1">Select a time that works for you.</p>
                    </div>

                    <form @submit.prevent="submitBooking" class="p-6 sm:p-8 space-y-8">
                        
                        <section>
                            <h3 class="text-lg font-medium text-slate-900 mb-4 flex items-center gap-2">
                                <span class="bg-orange-100 text-orange-600 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">1</span>
                                Service Details
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Service Type</label>
                                    <select v-model="form.service_type" class="w-full px-3 rounded-md border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2.5">
                                        <option value="" disabled>Select a service...</option>
                                        <option v-for="service in services" :key="service" :value="service">{{ service }}</option>
                                    </select>
                                    <p v-if="form.errors.service_type" class="text-red-500 text-xs mt-1">{{ form.errors.service_type }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Location Preference</label>
                                    <div class="flex gap-4">
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" v-model="form.location_type" value="online" class="sr-only peer">
                                            <div class="border rounded-md py-2.5 px-4 text-center text-sm font-medium peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700 hover:bg-slate-50 transition-all">
                                                Online (Zoom)
                                            </div>
                                        </label>
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" v-model="form.location_type" value="in-person" class="sr-only peer">
                                            <div class="border rounded-md py-2.5 px-4 text-center text-sm font-medium peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700 hover:bg-slate-50 transition-all">
                                                In Person
                                            </div>
                                        </label>
                                    </div>
                                    <p v-if="form.errors.location_type" class="text-red-500 text-xs mt-1">{{ form.errors.location_type }}</p>
                                </div>
                            </div>
                        </section>

                        <hr class="border-slate-100">

                        <section>
                            <h3 class="text-lg font-medium text-slate-900 mb-4 flex items-center gap-2">
                                <span class="bg-orange-100 text-orange-600 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">2</span>
                                Date & Time
                            </h3>
                            
                            <div v-if="loading" class="text-center py-8 text-slate-500">
                                <svg class="animate-spin h-8 w-8 text-orange-500 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Loading availability...
                            </div>

                            <div v-else-if="uniqueDates.length === 0" class="text-center py-8 bg-slate-50 rounded-lg">
                                <p class="text-slate-500">No availability found for the next 30 days. Please contact us directly.</p>
                            </div>

                            <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Available Dates</label>
                                    <div class="max-h-60 overflow-y-auto space-y-2 pr-2 custom-scrollbar">
                                        <button 
                                            v-for="date in uniqueDates" 
                                            :key="date"
                                            type="button"
                                            @click="selectDate(date)"
                                            :class="{'bg-orange-600 text-white border-orange-600': selectedDate === date, 'bg-white text-slate-700 border-slate-200 hover:border-orange-300': selectedDate !== date}"
                                            class="w-full text-left px-4 py-2 rounded-md border text-sm font-medium transition-colors"
                                        >
                                            {{ formatDate(date) }}
                                        </button>
                                    </div>
                                    <p v-if="form.errors.booking_date" class="text-red-500 text-xs mt-1">{{ form.errors.booking_date }}</p>
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">
                                        {{ selectedDate ? `Time slots for ${formatDate(selectedDate)}` : 'Select a date to view times' }}
                                    </label>
                                    
                                    <div v-if="!selectedDate" class="bg-slate-50 border border-dashed border-slate-300 rounded-lg h-32 flex items-center justify-center text-slate-400 text-sm">
                                        Select a date from the left
                                    </div>
                                    
                                    <div v-else class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                        <button 
                                            v-for="(slot, index) in slotsForSelectedDate" 
                                            :key="index"
                                            type="button"
                                            @click="selectTime(slot)"
                                            :class="{'bg-slate-800 text-white ring-2 ring-orange-500 ring-offset-2': form.start_time === slot.start_time, 'bg-white text-slate-700 border border-slate-200 hover:border-orange-400': form.start_time !== slot.start_time}"
                                            class="py-2 px-3 rounded-md text-sm font-medium transition-all shadow-sm"
                                        >
                                            {{ formatTime(slot.start_time) }}
                                        </button>
                                    </div>
                                    <p v-if="form.errors.start_time" class="text-red-500 text-xs mt-1">Please select a time slot.</p>
                                </div>
                            </div>
                        </section>

                        <hr class="border-slate-100">

                        <section>
                            <h3 class="text-lg font-medium text-slate-900 mb-4 flex items-center gap-2">
                                <span class="bg-orange-100 text-orange-600 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">3</span>
                                Your Details
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Full Name</label>
                                    <input v-model="form.client_name" type="text" class="w-full px-3 rounded-md border-slate-500 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2.5">
                                    <p v-if="form.errors.client_name" class="text-red-500 text-xs mt-1">{{ form.errors.client_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                                    <input v-model="form.client_email" type="email" class="w-full px-3 rounded-md border-slate-500 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2.5">
                                    <p v-if="form.errors.client_email" class="text-red-500 text-xs mt-1">{{ form.errors.client_email }}</p>
                                </div>
                            </div>
                        </section>

                        <div class="pt-4">
                            <button 
                                type="submit" 
                                :disabled="form.processing || !form.start_time"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <span v-if="form.processing">Processing...</span>
                                <span v-else>Confirm Booking</span>
                            </button>
                            <p class="text-center text-xs text-slate-400 mt-4">
                                No payment required now. Confirmation will be sent to your email.
                            </p>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped>
/* Optional: Custom scrollbar for the date list */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9; 
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1; 
    border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8; 
}
</style>
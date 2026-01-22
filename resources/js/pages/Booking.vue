<template>
    <AppWebsiteLayout>
  <div class="w-full">
    <!-- 
      1. HERO / TITLE SECTION: Full Width Black Banner
    -->
    <section class="bg-black text-white py-20 md:py-32">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-5xl sm:text-6xl font-bold uppercase tracking-widest mb-4">
          Book a Session
        </h1>
        <p class="text-xl max-w-4xl text-gray-300">
          Request a time for your personalized coaching or etiquette session. All requests are pending final confirmation.
        </p>
      </div>
    </section>

    <!-- 
      2. BOOKING FORM SECTION: White Background
    -->
    <section class="bg-white text-black py-20 md:py-32">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold mb-12 text-center uppercase tracking-widest">
          Your Booking Request (Step {{ currentStep }} of 3)
        </h2>

        <!-- Progress Indicator -->
        <div class="flex justify-between items-center mb-10">
            <div 
                v-for="step in 3"
                :key="step"
                :class="{'bg-black text-white': currentStep >= step, 'bg-gray-200 text-gray-500': currentStep < step}"
                class="w-1/3 text-center py-2 text-sm font-semibold uppercase transition-colors duration-300"
            >
                Step {{ step }}
            </div>
        </div>

        <!-- Status Message Box -->
        <div 
          v-if="statusMessage"
          :class="{'bg-black text-white border-2 border-green-500': statusType === 'success', 'bg-red-100 text-red-800 border-2 border-red-500': statusType === 'error'}"
          class="p-4 mb-8 rounded-lg font-semibold transition-all duration-300"
        >
          {{ statusMessage }}
        </div>
        
        <form @submit.prevent="submitBooking" class="space-y-6">
          
          <!-- STEP 1: SERVICE & LOCATION -->
          <div v-if="currentStep === 1" class="space-y-6">
            <h3 class="text-2xl font-bold mb-4 border-b pb-2">1. Select Service & Location</h3>

            <div>
              <label for="service_type" class="block text-sm font-semibold uppercase mb-2">Service Type</label>
              <select 
                id="service_type" 
                v-model="form.service_type" 
                required 
                class="w-full px-4 py-3 border border-black rounded-none focus:outline-none focus:border-gray-700 bg-white"
              >
                <option value="" disabled>Select a Service</option>
                <option v-for="service in services" :key="service" :value="service">{{ service }}</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold uppercase mb-2">Location Preference</label>
              <div class="flex space-x-4">
                <label v-for="location in locationTypes" :key="location" class="flex items-center space-x-2">
                  <input type="radio" :value="location" v-model="form.location_type" required class="form-radio text-black">
                  <span class="capitalize">{{ location }}</span>
                </label>
              </div>
            </div>
            
            <div class="flex justify-end pt-4">
                <Button type="button" @click="nextStep(1)" :disabled="!isStep1Valid" class="px-8 py-3 border-2 border-black font-semibold uppercase bg-black text-white hover:bg-white hover:text-black">
                    Next: Pick Time &rarr;
                </Button>
            </div>
          </div>
          
          <!-- STEP 2: AVAILABLE DATE & TIME SELECTION (New Logic) -->
          <div v-if="currentStep === 2" class="space-y-6">
            <h3 class="text-2xl font-bold mb-4 border-b pb-2">2. Select Available Date & Time (2-Hour Slot)</h3>
            
            <!-- Availability Display -->
            <div v-if="isSlotsLoading" class="p-8 text-center text-gray-500">
                <Loader2Icon class="w-6 h-6 animate-spin mx-auto mb-2" /> Loading available slots...
            </div>

            <div v-else-if="availableSlots.length > 0" class="space-y-6">
                <!-- Date Filter -->
                <div>
                    <label for="date_filter" class="block text-sm font-semibold uppercase mb-2">Filter by Date</label>
                    <input 
                        type="date" 
                        id="date_filter" 
                        v-model="selectedDateFilter" 
                        :min="today"
                        class="w-full px-4 py-3 border border-black rounded-none focus:outline-none focus:border-gray-700 bg-white"
                    >
                </div>

                <!-- Time Slot Selection Grid -->
                <div v-if="filteredSlots.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <button
                        v-for="(slot, index) in filteredSlots"
                        :key="index"
                        type="button"
                        @click="selectTimeSlot(slot)"
                        :class="{ 
                            'bg-black text-white border-black': isSlotSelected(slot), 
                            'border-black text-black hover:bg-gray-100': !isSlotSelected(slot) 
                        }"
                        class="border-2 rounded-lg p-3 text-center transition-all duration-200"
                    >
                        <span class="font-semibold block">{{ formatSlotDate(slot.booking_date) }}</span>
                        <span class="text-sm">{{ slot.start_time }} - {{ slot.end_time }}</span>
                    </button>
                </div>

                <div v-else class="p-8 text-center border border-gray-300 text-gray-700">
                    No slots available for the selected date. Please choose a different day.
                </div>
                
                <p v-if="errors.time_slot" class="text-red-500 text-xs mt-1">{{ errors.time_slot }}</p>

            </div>

            <div v-else class="p-8 text-center border-4 border-black bg-red-50 text-red-800 font-semibold">
                We currently have no available slots for booking. Please contact us directly.
            </div>

            <div class="flex justify-between pt-4">
                <Button type="button" variant="outline" @click="currentStep = 1" class="border-black text-black hover:bg-gray-100">
                    &larr; Back
                </Button>
                <Button type="button" @click="nextStep(2)" :disabled="!isStep2Valid" class="px-8 py-3 border-2 border-black font-semibold uppercase bg-black text-white hover:bg-white hover:text-black">
                    Next: Contact Details &rarr;
                </Button>
            </div>
          </div>

          <!-- STEP 3: CONTACT DETAILS & SUBMIT -->
          <div v-if="currentStep === 3" class="space-y-6">
            <h3 class="text-2xl font-bold mb-4 border-b pb-2">3. Your Contact Details</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="client_name" class="block text-sm font-semibold uppercase mb-2">Your Name</label>
                <input 
                  type="text" 
                  id="client_name" 
                  v-model="form.client_name" 
                  required 
                  :class="{'border-red-500': errors.client_name}"
                  class="w-full px-4 py-3 border border-black rounded-none focus:outline-none focus:border-gray-700 bg-white"
                >
                <p v-if="errors.client_name" class="text-red-500 text-xs mt-1">{{ errors.client_name }}</p>
              </div>
              <div>
                <label for="client_email" class="block text-sm font-semibold uppercase mb-2">Your Email</label>
                <input 
                  type="email" 
                  id="client_email" 
                  v-model="form.client_email" 
                  required 
                  :class="{'border-red-500': errors.client_email}"
                  class="w-full px-4 py-3 border border-black rounded-none focus:outline-none focus:border-gray-700 bg-white"
                >
                <p v-if="errors.client_email" class="text-red-500 text-xs mt-1">{{ errors.client_email }}</p>
              </div>
            </div>
            
            <div class="flex justify-between pt-4">
                <Button type="button" variant="outline" @click="currentStep = 2" :disabled="loading" class="border-black text-black hover:bg-gray-100">
                    &larr; Back
                </Button>
                <button 
                  type="submit" 
                  :disabled="loading"
                  class="text-xl px-10 py-4 border-2 border-black font-semibold uppercase bg-black text-white transition-all duration-300 tracking-wider disabled:opacity-50 disabled:cursor-not-allowed hover:bg-white hover:text-black"
                >
                  <span v-if="loading">Requesting Booking...</span>
                  <span v-else>Confirm & Submit Request</span>
                </button>
            </div>
          </div>
          
        </form>
      </div>
    </section>
  </div>
    </AppWebsiteLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3'; 
import axios from 'axios'; 
import { Button } from '@/components/ui/button';
import { Loader2Icon } from 'lucide-vue-next'; // Icon for loading state
import AppWebsiteLayout from '@/layouts/WebsiteLayout.vue';

// --- Types ---

interface TimeSlot {
    booking_date: string;
    start_time: string;
    end_time: string;
    // This is the ID of the recurring slot it was derived from (for potential backend reference)
    availability_id: number | null; 
}

interface BookingForm extends TimeSlot {
  client_name: string;
  client_email: string;
  service_type: string;
  location_type: 'online' | 'in-person' | '';
}

interface FormErrors {
    client_name?: string;
    client_email?: string;
    time_slot?: string;
    [key: string]: string | undefined;
}

// --- Constants ---
const services = ['Executive Coaching', 'Formal Dining Etiquette', 'Professional Presence Training'];
const locationTypes = ['online', 'in-person'];
const today = new Date().toISOString().split('T')[0];

// --- State ---
const currentStep = ref(1);

const initialForm: BookingForm = {
  client_name: '',
  client_email: '',
  booking_date: '',
  start_time: '',
  end_time: '',
  service_type: services[0],
  location_type: 'online',
  availability_id: null,
};

const form = ref<BookingForm>({ ...initialForm });
const loading = ref(false);
const statusMessage = ref<string | null>(null);
const statusType = ref<'success' | 'error' | null>(null);
const errors = ref<FormErrors>({});

// Availability States
const availableSlots = ref<TimeSlot[]>([]);
const isSlotsLoading = ref(false);
const selectedDateFilter = ref(today); // Defaults to today, will be updated by fetch

// --- Computed & Utility ---

// Filters the availableSlots based on the user's date selection
const filteredSlots = computed(() => {
    // Only show slots matching the selected date in the filter input
    return availableSlots.value.filter(slot => slot.booking_date === selectedDateFilter.value);
});

// Checks if the slot displayed in the grid is the one selected in the form
const isSlotSelected = (slot: TimeSlot) => {
    return form.value.booking_date === slot.booking_date && form.value.start_time === slot.start_time;
};

const formatSlotDate = (dateString: string) => {
    const date = new Date(dateString);
    // e.g., "Mon, Nov 21"
    return date.toLocaleDateString('en-US', { weekday: 'short', day: 'numeric', month: 'short' });
}

// --- Step Validation ---

const isStep1Valid = computed(() => {
    return !!form.value.service_type && !!form.value.location_type;
});

const isStep2Valid = computed(() => {
    errors.value.time_slot = undefined;
    
    // Check if a time slot has been selected (i.e., the form fields are populated)
    if (!form.value.booking_date || !form.value.start_time || !form.value.end_time) {
        errors.value.time_slot = 'Please select an available date and time slot.';
        return false;
    }
    
    return true;
});

const nextStep = (step: number) => {
    if (step === 1 && isStep1Valid.value) {
        currentStep.value = 2;
    } else if (step === 2 && isStep2Valid.value) {
        currentStep.value = 3;
    }
};

// --- Slot Selection ---

const selectTimeSlot = (slot: TimeSlot) => {
    form.value.booking_date = slot.booking_date;
    form.value.start_time = slot.start_time;
    form.value.end_time = slot.end_time;
    form.value.availability_id = slot.availability_id;
};

// --- Data Fetching ---

const fetchAvailableSlots = async () => {
    isSlotsLoading.value = true;
    
    try {
        // Use the defined public API endpoint for available slots
        const response = await axios.get('/api/public/available-slots'); 
        
        // The API should return the structure { slots: [...] }
        availableSlots.value = response.data.slots;
        
        // Set the default filter date to the first available date
        if (availableSlots.value.length > 0) {
            selectedDateFilter.value = availableSlots.value[0].booking_date;
        } else {
            selectedDateFilter.value = today;
        }
        
    } catch (e) {
        console.error('Failed to fetch available slots:', e);
        // Display generic error if fetch fails
        statusMessage.value = 'Failed to load schedule. Please try refreshing.';
        statusType.value = 'error';
    } finally {
        isSlotsLoading.value = false;
    }
};


// --- Form Submission ---

const resetState = (success: boolean = false) => {
    loading.value = false;
    errors.value = {};
    if (success) {
        // Reset form data and step
        form.value = { ...initialForm }; 
        currentStep.value = 1; 
        
        // Refresh slots after successful booking to remove the taken slot
        fetchAvailableSlots(); 
    }
    // Clear status message after a delay
    setTimeout(() => {
        statusMessage.value = null;
        statusType.value = null;
    }, 8000); 
}

const submitBooking = async () => {
    // Basic validation checks before network request
    if (!isStep1Valid.value) { currentStep.value = 1; return; }
    if (!isStep2Valid.value) { currentStep.value = 2; return; }
    if (!form.value.client_name || !form.value.client_email) { currentStep.value = 3; return; }
    
    loading.value = true;
    statusMessage.value = null;
    errors.value = {}; 

    try {
        // Post the booking request to the public endpoint
        const response = await axios.post('/api/bookings', form.value);
        
        statusMessage.value = response.data.message || 'Your booking request was submitted successfully! Check your email for confirmation.';
        statusType.value = 'success';
        
        resetState(true);

    } catch (error: any) {
        statusType.value = 'error';
        
        if (error.response && error.response.status === 409) {
            // Conflict (Time Slot Taken) - Critical error
            statusMessage.value = error.response.data.message;
            currentStep.value = 2; // Return to time selection step
        }
        else if (error.response && error.response.status === 422) {
            // Laravel Validation Error (General)
            const serverErrors = error.response.data.errors;
            if (serverErrors.client_name) errors.value.client_name = serverErrors.client_name[0];
            if (serverErrors.client_email) errors.value.client_email = serverErrors.client_email[0];
            statusMessage.value = 'Please correct the contact details below.';
            currentStep.value = 3; 
        }
        else {
            statusMessage.value = 'An unexpected error occurred. Please try again later.';
        }
        
        resetState(false);
    }
};

onMounted(() => {
    fetchAvailableSlots();
});
</script>

<style scoped>
/* Scoped styles */
.font-bold {
  font-weight: 700;
}
/* Style the radio button input to be visible against white */
.form-radio {
    color: black; 
    accent-color: black;
}
</style>
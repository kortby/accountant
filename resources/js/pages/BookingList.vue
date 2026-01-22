<template>
    <AppLayout>
  <div class="p-4 md:p-8 space-y-8 min-h-screen">
    
    <!-- Header -->
    <div class="max-w-7xl mx-auto space-y-1 pb-4 border-b">
      <h1 class="text-3xl font-bold tracking-tight">
        Booking Requests
      </h1>
      <p class="text-muted-foreground">
        Review and manage client requests for coaching and etiquette sessions.
      </p>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Booking List (2/3 width on large screens) -->
      <Card class="lg:col-span-2">
        <CardHeader class="flex flex-row items-center justify-between pb-3">
          <CardTitle class="text-xl">
            Booking Inbox ({{ pendingCount }} Pending)
          </CardTitle>
          <Button @click="fetchBookings" variant="ghost" size="icon" :disabled="isLoading">
            <RefreshCwIcon class="w-4 h-4" :class="{'animate-spin': isLoading}" />
          </Button>
        </CardHeader>
        <CardContent class="p-0">
          <div v-if="isLoading" class="p-8 text-center text-muted-foreground">
            <p>Loading bookings...</p>
          </div>
          <div v-else-if="bookings.length === 0" class="p-8 text-center text-muted-foreground">
            <p>No new booking requests at this time.</p>
          </div>
          
          <div v-else class="divide-y">
            <Item
              v-for="booking in bookings"
              :key="booking.id"
              :variant="getVariantByStatus(booking.status)"
              class="cursor-pointer hover:bg-accent/50 p-4 transition-colors"
              @click="viewBooking(booking)"
            >
              <ItemContent>
                <ItemTitle class="flex items-center space-x-2">
                  <StatusIndicator :status="booking.status" />
                  <span :class="{'font-semibold': booking.status === 'pending'}">
                    {{ booking.service_type }}
                  </span>
                </ItemTitle>
                <ItemDescription class="flex justify-between items-center text-sm pt-1">
                  <span class="truncate pr-4 text-xs">Client: {{ booking.client_name }} ({{ booking.client_email }})</span>
                  <span class="text-xs text-right font-bold">
                    {{ formatDate(booking.booking_date) }} at {{ booking.start_time }}
                  </span>
                </ItemDescription>
              </ItemContent>
              <ItemActions>
                 <Badge :variant="getBadgeVariant(booking.location_type)">
                    {{ booking.location_type }}
                 </Badge>
              </ItemActions>
            </Item>
          </div>
        </CardContent>
      </Card>

      <!-- Detail View Placeholder / Summary (1/3 width) -->
      <Card class="hidden lg:block lg:col-span-1 p-6">
        <CardTitle class="text-xl mb-4 border-b pb-2">Selected Booking</CardTitle>
        <div v-if="selectedBooking">
          <p class="font-bold mb-1">{{ selectedBooking.service_type }}</p>
          <p class="text-sm text-muted-foreground mb-4">
             {{ formatDate(selectedBooking.booking_date) }} at {{ selectedBooking.start_time }} ({{ selectedBooking.location_type }})
          </p>
          <Separator class="my-4" />
          <p class="text-sm">Client: <span class="font-semibold">{{ selectedBooking.client_name }}</span></p>
          <p class="text-sm text-muted-foreground mb-4">{{ selectedBooking.client_email }}</p>
          <StatusIndicator :status="selectedBooking.status" class="mt-4" text-size="text-sm"/>

          <!-- Quick Action Buttons -->
          <div class="mt-4 space-y-2">
            <Button 
                v-if="selectedBooking.status === 'pending'"
                @click="updateStatus(selectedBooking, 'confirmed')" 
                :disabled="isUpdatingStatus"
                class="w-full"
            >
                <CheckIcon class="w-4 h-4 mr-2" /> Confirm Booking
            </Button>
             <Button 
                v-if="selectedBooking.status !== 'canceled'"
                @click="updateStatus(selectedBooking, 'canceled')" 
                :disabled="isUpdatingStatus"
                variant="destructive-outline"
                class="w-full"
            >
                 <XIcon class="w-4 h-4 mr-2" /> Cancel Booking
            </Button>
          </div>
        </div>
        <div v-else class="text-center text-muted-foreground py-12">
          Select a request to view details and update status.
        </div>
      </Card>
    </div>

    <!-- Components for Status Management -->

    <!-- Status Indicator Component -->
    <component :is="StatusIndicatorComponent" />

    <!-- Mobile/Sheet Detail View -->
    <Sheet :open="showDetailSheet" @update:open="showDetailSheet = $event">
      <SheetContent side="right" class="w-[400px] sm:w-[540px] p-6 space-y-4">
        <SheetHeader>
          <SheetTitle class="text-2xl font-bold uppercase">
            {{ selectedBooking?.service_type || 'Booking Detail' }}
          </SheetTitle>
          <SheetDescription class="text-sm text-muted-foreground">
            {{ formatDate(selectedBooking?.booking_date || '') }} at {{ selectedBooking?.start_time }}
          </SheetDescription>
        </SheetHeader>

        <div v-if="selectedBooking" class="space-y-4 pt-4">
            <p class="text-sm">Client: <span class="font-semibold">{{ selectedBooking.client_name }}</span></p>
            <p class="text-sm text-muted-foreground">{{ selectedBooking.client_email }}</p>
            <p class="text-sm">Location: <span class="font-semibold capitalize">{{ selectedBooking.location_type }}</span></p>
            <StatusIndicator :status="selectedBooking.status" class="mt-2" text-size="text-base"/>

            <Separator />
            
            <h4 class="font-semibold text-lg">Actions:</h4>
            
            <Button 
                @click="updateStatus(selectedBooking, 'confirmed')" 
                :disabled="isUpdatingStatus || selectedBooking.status === 'confirmed'"
                class="w-full"
            >
                <Loader2Icon v-if="isUpdatingStatus" class="w-4 h-4 mr-2 animate-spin" />
                <CheckIcon v-else class="w-4 h-4 mr-2" />
                Mark as Confirmed
            </Button>
            
            <Button 
                @click="updateStatus(selectedBooking, 'canceled')" 
                :disabled="isUpdatingStatus || selectedBooking.status === 'canceled'"
                variant="destructive-outline"
                class="w-full"
            >
                <Loader2Icon v-if="isUpdatingStatus && statusToChange === 'canceled'" class="w-4 h-4 mr-2 animate-spin" />
                <XIcon v-else class="w-4 h-4 mr-2" />
                Mark as Canceled
            </Button>
        </div>
      </SheetContent>
    </Sheet>
    
    <!-- Status Alert -->
    <div class="fixed bottom-4 right-4 z-50">
      <Alert v-if="statusMessage" :variant="statusType === 'success' ? 'default' : 'destructive'" class="max-w-xs shadow-lg">
        <component :is="statusType === 'success' ? CheckCircle2Icon : AlertCircleIcon" class="w-4 h-4" />
        <AlertTitle class="uppercase font-semibold">
          {{ statusType === 'success' ? 'Success' : 'Error' }}
        </AlertTitle>
        <AlertDescription>
          {{ statusMessage }}
        </AlertDescription>
      </Alert>
    </div>
  </div>
  </AppLayout>

</template>

<script setup lang="ts">
import { ref, onMounted, defineComponent } from 'vue';
import axios from 'axios';
import {
  RefreshCwIcon, Loader2Icon, AlertCircleIcon, CheckCircle2Icon, ClockIcon, CheckIcon, XIcon
} from 'lucide-vue-next';

import AppLayout from '@/layouts/AppLayout.vue';

// Shadcn Components
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
  Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle
} from '@/components/ui/sheet';
import { Separator } from '@/components/ui/separator';
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert';
import {
  Item, ItemActions, ItemContent, ItemDescription, ItemTitle,
} from '@/components/ui/item';

// --- Types ---
type BookingStatus = 'pending' | 'confirmed' | 'canceled';
type LocationType = 'online' | 'in-person';

interface Booking {
  id: number;
  client_name: string;
  client_email: string;
  booking_date: string;
  start_time: string;
  end_time: string;
  service_type: string;
  location_type: LocationType;
  status: BookingStatus;
  created_at: string;
}

// --- Status Indicator Component ---
// Define a tiny component to handle status visualization cleanly
const StatusIndicatorComponent = defineComponent({
  props: {
    status: {
      type: String as () => BookingStatus,
      required: true,
    },
    textSize: {
        type: String,
        default: 'text-sm'
    }
  },
  setup(props) {
    const statusMap = {
      pending: { icon: ClockIcon, text: 'Pending Review', color: 'text-amber-600', bg: 'bg-amber-100', border: 'border-amber-300' },
      confirmed: { icon: CheckIcon, text: 'Confirmed', color: 'text-green-600', bg: 'bg-green-100', border: 'border-green-300' },
      canceled: { icon: XIcon, text: 'Canceled', color: 'text-red-600', bg: 'bg-red-100', border: 'border-red-300' },
    };
    const current = statusMap[props.status];
    return { current };
  },
  template: `
    <div :class="[current.bg, current.color, current.border, textSize]" class="flex items-center space-x-2 px-2 py-1 rounded-full font-medium border">
      <component :is="current.icon" class="w-4 h-4" />
      <span class="capitalize">{{ current.text }}</span>
    </div>
  `,
});

// --- State ---
const bookings = ref<Booking[]>([]);
const pendingCount = ref(0);
const isLoading = ref(false);
const isUpdatingStatus = ref(false);
const statusToChange = ref<BookingStatus | null>(null);

const selectedBooking = ref<Booking | null>(null);
const showDetailSheet = ref(false);

const statusMessage = ref<string | null>(null);
const statusType = ref<'success' | 'error' | null>(null);

// --- Utility Methods ---

const formatDate = (dateString: string): string => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString([], { month: 'short', day: 'numeric', year: 'numeric' });
};

const showAlert = (message: string, type: 'success' | 'error') => {
  statusMessage.value = message;
  statusType.value = type;
  setTimeout(() => {
    statusMessage.value = null;
    statusType.value = null;
  }, 4000);
};

const getVariantByStatus = (status: BookingStatus) => {
    switch (status) {
        case 'pending':
            return 'default'; // Highlight pending items
        case 'confirmed':
            return 'outline';
        case 'canceled':
            return 'muted';
        default:
            return 'default';
    }
};

const getBadgeVariant = (location: LocationType) => {
    return location === 'online' ? 'secondary' : 'default';
};

// --- CRUD & Actions ---

const fetchBookings = async () => {
  isLoading.value = true;
  try {
    // Assuming GET /api/bookings returns a list of bookings
    const response = await axios.get('/api/bookings'); 
    bookings.value = response.data.bookings.sort((a: Booking, b: Booking) => {
        // Sort: Pending first, then by date
        if (a.status === 'pending' && b.status !== 'pending') return -1;
        if (a.status !== 'pending' && b.status === 'pending') return 1;
        return new Date(a.booking_date).getTime() - new Date(b.booking_date).getTime();
    });
    pendingCount.value = bookings.value.filter(b => b.status === 'pending').length;
  } catch (e: any) {
    showAlert('Failed to load bookings. Please verify authentication.', 'error');
    console.error('Fetch Bookings Error:', e);
  } finally {
    isLoading.value = false;
  }
};

const viewBooking = (booking: Booking) => {
  selectedBooking.value = booking;
  showDetailSheet.value = true;
};

const updateStatus = async (booking: Booking, newStatus: BookingStatus) => {
  isUpdatingStatus.value = true;
  statusToChange.value = newStatus;

  try {
    // Assuming PUT /api/bookings/{booking}/status
    await axios.put(`/api/bookings/${booking.id}/status`, { status: newStatus });

    // Update local state immediately
    booking.status = newStatus;
    
    // Recalculate pending count
    pendingCount.value = bookings.value.filter(b => b.status === 'pending').length;
    
    // Re-sort the list (pending messages float to top)
    bookings.value.sort((a, b) => (a.status === 'pending' ? -1 : b.status === 'pending' ? 1 : 0));
    
    showAlert(`Booking status updated to ${newStatus}. Notification sent to client.`, 'success');

  } catch (e) {
    showAlert(`Failed to update status to ${newStatus}.`, 'error');
  } finally {
    isUpdatingStatus.value = false;
    statusToChange.value = null;
  }
};


// --- Lifecycle ---
onMounted(() => {
  fetchBookings();
});
</script>

<style scoped>
/* No custom styling */
/* Custom destructive-outline variant for buttons if Shadcn doesn't provide it */

</style>
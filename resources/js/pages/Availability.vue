<template>
    <AppLayout>
        <div class="p-4 md:p-8 space-y-8 min-h-screen">

            <!-- 1. PAGE HEADER (Minimalist Shadcn structure) -->
            <div class="max-w-4xl mx-auto space-y-6 pb-4 border-b">
                <h1 class="text-3xl font-bold tracking-tight">
                    Availability Manager
                </h1>
                <p class="text-muted-foreground">
                    Define your recurring weekly time slots for clients to book sessions.
                </p>
            </div>

            <!-- Global Status Alert -->
            <div class="max-w-4xl mx-auto">
                <Alert v-if="statusMessage" :variant="statusType === 'success' ? 'default' : 'destructive'">
                    <component :is="statusType === 'success' ? CheckCircle2Icon : AlertCircleIcon" class="w-4 h-4" />
                    <AlertTitle class="uppercase font-semibold">
                        {{ statusType === 'success' ? 'Success' : 'Error' }}
                    </AlertTitle>
                    <AlertDescription>
                        {{ statusMessage }}
                    </AlertDescription>
                </Alert>
            </div>

            <div class="max-w-4xl mx-auto space-y-6">

                <!-- Action Header -->
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">
                        Current Schedule ({{ availability.length }} slots)
                    </h2>
                    <!-- Shadcn Button to trigger the Add Dialog -->
                    <Button @click="showAddDialog = true" :disabled="isLoading">
                        <PlusIcon class="w-4 h-4 mr-2" /> Add New Slot
                    </Button>
                </div>
                
                <!-- Loading State -->
                <div v-if="isLoading" class="p-12 text-center text-muted-foreground">
                    <PopcornIcon class="w-6 h-6 animate-spin mx-auto mb-3" />
                    <p>Loading schedule...</p>
                </div>

                <!-- List of Available Slots -->
                <div v-else-if="availability.length > 0" class="flex flex-col gap-3">
                    <Item v-for="(slot) in availability" :key="slot.id" variant="outline" class="p-4">
                        <ItemContent>
                            <ItemTitle class="font-bold uppercase">
                                {{ slot.days.join(', ') }}
                            </ItemTitle>
                            <ItemDescription>
                                {{ slot.start_time }} - {{ slot.end_time }}
                            </ItemDescription>
                        </ItemContent>
                        <ItemActions>
                            <!-- Delete Button -->
                            <Button variant="ghost" size="icon" @click="confirmRemoveSlot(slot)"
                                aria-label="Remove Slot" class="text-red-500 hover:text-red-700">
                                <Trash2Icon class="w-4 h-4" />
                            </Button>
                        </ItemActions>
                    </Item>
                </div>

                <!-- Empty State -->
                <Card v-else class="p-12 text-center">
                    <CardTitle class="mb-2">No Availability Defined</CardTitle>
                    <CardDescription>Click "Add New Slot" to begin defining your recurring weekly schedule.
                    </CardDescription>
                </Card>
            </div>

            <!-- 3. ADD AVAILABILITY DIALOG (Modal) -->
            <Dialog :open="showAddDialog" @update:open="showAddDialog = $event">
                <DialogContent class="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle class="text-2xl font-bold uppercase">Define New Availability</DialogTitle>
                        <DialogDescription>
                            Select the recurring day(s) and time range for client bookings.
                        </DialogDescription>
                    </DialogHeader>

                    <form @submit.prevent="addSlot" class="space-y-4 pt-4">

                        <!-- Day Selection (Multi-Select) -->
                        <div>
                            <Label for="days" class="font-semibold block mb-2">Day(s) of the Week</Label>
                            <div class="flex flex-wrap gap-2">
                                <Button v-for="day in weekDays" :key="day" type="button"
                                    @click="toggleDay(day)"
                                    :variant="newSlot.days.includes(day) ? 'default' : 'outline'">
                                    {{ day }}
                                </Button>
                            </div>
                            <p v-if="formErrors.days" class="text-destructive text-sm mt-1">{{ formErrors.days }}</p>
                        </div>

                        <!-- Time Selection -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <Label for="startTime" class="font-semibold block mb-2">Start Time</Label>
                                <Input type="time" id="startTime" v-model="newSlot.start_time" required />
                            </div>
                            <div>
                                <Label for="endTime" class="font-semibold block mb-2">End Time</Label>
                                <Input type="time" id="endTime" v-model="newSlot.end_time" required />
                            </div>
                        </div>
                        <p v-if="formErrors.time" class="text-destructive text-sm mt-1">{{ formErrors.time }}</p>

                        <DialogFooter class="pt-6 flex justify-end space-x-2">
                            <Button type="button" variant="outline" @click="showAddDialog = false" :disabled="isSaving">Cancel</Button>
                            <Button type="submit" :disabled="isSaving">
                                {{ isSaving ? 'Saving...' : 'Confirm Slot' }}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <!--  4. DELETE CONFIRMATION ALERT DIALOG -->

            <Dialog :open="showDeleteAlert" @update:open="showDeleteAlert = $event">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Are you absolutely sure?</DialogTitle>
                        <DialogDescription>

                            This action will permanently remove the selected recurring availability slot from your
                            schedule. Clients will no longer be able to book this time.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter class="flex justify-end space-x-2">
                        <DialogClose as-child>
                            <Button type="button" variant="outline" @click="showDeleteAlert = false">Cancel</Button>
                        </DialogClose>

                        <Button type="button" class="bg-destructive hover:bg-destructive/90"
                            @click="executeRemoveSlot">
                            Yes, Remove Slot
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

        </div>
    </AppLayout>
</template>

<script setup lang="ts">
    import {
        ref,
        reactive,
        onMounted
    } from 'vue';
    import axios from 'axios'; 
    import {
        Link
    } from '@inertiajs/vue3';
    // Added Lucide icons for visual clarity
    import {
        Trash2Icon,
        PlusIcon,
        AlertCircleIcon,
        CheckCircle2Icon,
        PopcornIcon
    } from 'lucide-vue-next';
    // Using only Shadcn components
    import {
        Card,
        CardContent,
        CardDescription,
        CardHeader,
        CardTitle
    } from '@/components/ui/card';
    import {
        Button
    } from '@/components/ui/button';
    import {
        Label
    } from '@/components/ui/label';
    import {
        Input
    } from '@/components/ui/input'; // Using standard Shadcn Input
    
    import {
        Alert,
        AlertDescription,
        AlertTitle,
    } from '@/components/ui/alert'

    import {
        Dialog,
        DialogClose,
        DialogContent,
        DialogDescription,
        DialogFooter,
        DialogHeader,
        DialogTitle,
        DialogTrigger,
    } from '@/components/ui/dialog'

    // Using the recommended Item components for the list
    import {
        Item,
        ItemActions,
        ItemContent,
        ItemDescription,
        ItemTitle,
    } from '@/components/ui/item'
    import AppLayout from '@/layouts/AppLayout.vue';


    // --- Types ---
     interface AvailabilitySlot {
        // Updated interface to match backend model fields
        id?: number; 
        days: string[];
        start_time: string;
        end_time: string;
    }

    interface Errors {
        days: string | null;
        time: string | null;
    }

    // --- State ---

    const showAddDialog = ref(false);
    const showDeleteAlert = ref(false);

    const slotToDelete = ref<AvailabilitySlot | null>(null);
    const isSaving = ref(false);
    const isLoading = ref(false);

    // Global status alerts
    const statusMessage = ref<string | null>(null);
    const statusType = ref<'success' | 'error' | null>(null);

    const weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    const defaultNewSlot: AvailabilitySlot = {
        days: [],
        start_time: '09:00',
        end_time: '17:00',
    };

    const newSlot = reactive < AvailabilitySlot > ({
        ...defaultNewSlot
    });

    const formErrors = reactive < Errors > ({
        days: null,
        time: null,
    });

    const availability = ref < AvailabilitySlot[] > ([]);

    // --- Utility Methods ---

    const resetAlert = (delay = 5000) => {
        setTimeout(() => {
            statusMessage.value = null;
            statusType.value = null;
        }, delay);
    };
    
    // Function to handle API errors and show alert
    const handleApiError = (e: any, defaultMsg: string) => {
        console.error('API Error:', e);
        statusType.value = 'error';
        if (e.response && e.response.data && e.response.data.message) {
            statusMessage.value = e.response.data.message;
        } else {
            statusMessage.value = defaultMsg;
        }
        resetAlert();
    };


    // --- CRUD Methods ---

    // 1. FETCH
    const fetchAvailability = async () => {
        isLoading.value = true;
        try {
            // Using the index endpoint defined in AvailabilityController
            const response = await axios.get('/api/availability'); 
            availability.value = response.data.availability;
            
        } catch (e: any) {
            handleApiError(e, 'Failed to load schedule. Please check your network connection.');
        } finally {
            isLoading.value = false;
        }
    };

    // 2. CREATE (Add Slot)
    const addSlot = async () => {
        if (!validateForm()) {
            return;
        }

        isSaving.value = true;
        statusMessage.value = null;

        try {
            // Using the store endpoint defined in AvailabilityController
            const response = await axios.post('/api/availability', newSlot);

            statusType.value = 'success';
            statusMessage.value = response.data.message;
            
            // Add the new slot returned by the API to the local list
            if (response.data.slot) {
                 availability.value.push(response.data.slot);
            }

            // Reset dialog state
            Object.assign(newSlot, defaultNewSlot);
            showAddDialog.value = false;
            resetAlert();

        } catch (e: any) {
            // Handle validation or save errors
            if (e.response && e.response.status === 422) {
                // Manually handle server-side validation errors
                const errors = e.response.data.errors;
                if (errors.days) formErrors.days = errors.days[0];
                if (errors.start_time || errors.end_time) formErrors.time = 'Time must be a valid format and end time must be after start time.';
            }
            handleApiError(e, 'Failed to save new availability slot.');

        } finally {
            isSaving.value = false;
        }
    };

    // 3. DELETE (Remove Slot)
    const confirmRemoveSlot = (slot: AvailabilitySlot) => {
        slotToDelete.value = slot;
        showDeleteAlert.value = true;
    };

    const executeRemoveSlot = async () => {
        if (!slotToDelete.value || !slotToDelete.value.id) {
            showDeleteAlert.value = false;
            return;
        }
        
        showDeleteAlert.value = false;
        isSaving.value = true;
        statusMessage.value = null;

        try {
            const id = slotToDelete.value.id;
            // Using the destroy endpoint defined in AvailabilityController
            await axios.delete(`/api/availability/${id}`);

            statusType.value = 'success';
            statusMessage.value = 'Availability slot removed successfully.';
            
            // Remove the slot from the local reactive list
            availability.value = availability.value.filter(slot => slot.id !== id);

            resetAlert();

        } catch (e: any) {
            handleApiError(e, 'Failed to delete the availability slot.');
        } finally {
            isSaving.value = false;
            slotToDelete.value = null;
        }
    };

    // --- Local Form Logic ---

    const toggleDay = (day: string) => {
        const index = newSlot.days.indexOf(day);
        if (index > -1) {
            newSlot.days.splice(index, 1); // Remove day
        } else {
            newSlot.days.push(day); // Add day
        }
    };

    const validateForm = (): boolean => {
        formErrors.days = null;
        formErrors.time = null;

        if (newSlot.days.length === 0) {
            formErrors.days = 'Please select at least one day.';
        }

        // Simple time validation: End time must be after start time
        if (newSlot.start_time && newSlot.end_time && newSlot.start_time >= newSlot.end_time) {
            formErrors.time = 'End time must be after start time.';
        }

        return !formErrors.days && !formErrors.time;
    };

    // --- Lifecycle ---
    onMounted(() => {
        fetchAvailability();
    });
</script>

<style scoped>
    /* No custom styling or background changes, relying purely on Shadcn defaults */
</style>

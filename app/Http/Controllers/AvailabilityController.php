<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AvailabilityController extends Controller
{
    /**
     * Generates and returns a list of concrete, bookable slots for the next 30 days.
     * This is a PUBLIC endpoint used by the client booking form.
     * * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableSlots()
    {
        // For simplicity, we assume one primary coach (ID 1) or fetch the coach ID dynamically.
        // For this logic, we will assume we are checking the schedule for COACH ID 1.
        $coachId = 1; 
        
        $recurringSlots = Availability::where('user_id', $coachId)
                                      ->orderBy('start_time')
                                      ->get();

        if ($recurringSlots->isEmpty()) {
            return response()->json(['slots' => []]);
        }

        $availableSlots = [];
        $today = Carbon::today();
        $endDate = $today->copy()->addDays(30);
        
        // 1. Fetch existing bookings that conflict with the date range
        $bookedSlots = Booking::where('coach_id', $coachId)
                                ->where('booking_date', '>=', $today)
                                ->where('booking_date', '<=', $endDate)
                                ->where('status', '!=', 'canceled') // Only check confirmed/pending slots
                                ->get();

        // 2. Iterate through each day in the next 30 days
        for ($date = $today->copy(); $date->lessThanOrEqualTo($endDate); $date->addDay()) {
            
            // Get the name of the current day (e.g., 'Monday', 'Tuesday')
            $dayOfWeek = $date->format('l');

            // 3. Check which recurring slots are set for this specific day
            foreach ($recurringSlots as $recurringSlot) {
                
                // The 'days' column is cast to an array in the Availability Model
                if (in_array($dayOfWeek, $recurringSlot->days)) {
                    
                    // Convert start/end times to Carbon objects for easy manipulation
                    $startTime = Carbon::parse($recurringSlot->start_time);
                    $endTime = Carbon::parse($recurringSlot->end_time);
                    
                    // Define the fixed duration (e.g., 2 hours, as requested)
                    $slotDurationMinutes = 120; // 2 hours
                    
                    $currentSlotStart = $startTime->copy();

                    // 4. Break the recurring block into concrete 2-hour sessions
                    while ($currentSlotStart->addMinutes($slotDurationMinutes)->lessThanOrEqualTo($endTime)) {
                        
                        $slotEnd = $currentSlotStart->copy();
                        $slotStart = $currentSlotStart->copy()->subMinutes($slotDurationMinutes);
                        
                        // Check if the slot is in the past (only for today's date)
                        if ($date->isSameDay(Carbon::today()) && $slotEnd->lessThanOrEqualTo(Carbon::now())) {
                             // Skip slots that have already passed today
                             continue;
                        }

                        // 5. Check for conflicts with existing Bookings
                        $isBooked = $bookedSlots->contains(function ($bookedSlot) use ($date, $slotStart, $slotEnd) {
                            $bookedDate = Carbon::parse($bookedSlot->booking_date);
                            $bookedStart = Carbon::parse($bookedSlot->start_time);
                            $bookedEnd = Carbon::parse($bookedSlot->end_time);
                            
                            // Check if the date matches AND the times overlap
                            return $bookedDate->isSameDay($date) && 
                                (
                                    // Slot starts during a booking (or at the exact start)
                                    ($slotStart->greaterThanOrEqualTo($bookedStart) && $slotStart->lessThan($bookedEnd)) || 
                                    // Slot ends during a booking (or at the exact end)
                                    ($slotEnd->greaterThan($bookedStart) && $slotEnd->lessThanOrEqualTo($bookedEnd)) ||
                                    // Booking entirely contains the slot
                                    ($slotStart->lessThanOrEqualTo($bookedStart) && $slotEnd->greaterThanOrEqualTo($bookedEnd))
                                );
                        });

                        // 6. If no conflict, add the bookable slot
                        if (!$isBooked) {
                            $availableSlots[] = [
                                'booking_date' => $date->format('Y-m-d'),
                                'start_time' => $slotStart->format('H:i'),
                                'end_time' => $slotEnd->format('H:i'),
                                'availability_id' => $recurringSlot->id,
                            ];
                        }
                    }
                }
            }
        }

        // 7. Return the final list to the Vue frontend
        return response()->json(['slots' => $availableSlots]);
    }

    /**
     * Display a listing of the user's availability slots.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // 1. Get availability slots for the currently authenticated user
        $slots = Availability::where('user_id', Auth::id())
                              ->orderBy('start_time')
                              ->get();

        // 2. Return the list of slots
        return response()->json([
            'availability' => $slots,
        ]);
    }

    /**
     * Store a newly created availability slot.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['string'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Create the slot
        $slot = Availability::create([
            'user_id' => Auth::id(), // Assign to the current user
            'days' => $request->input('days'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
        ]);

        // 3. Return the newly created slot
        return response()->json([
            'message' => 'Availability slot created successfully.',
            'slot' => $slot
        ], 201);
    }

    /**
     * Remove the specified availability slot.
     *
     * @param  \App\Models\Availability  $availability
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Availability $availability)
    {
        // 1. Authorization: Ensure the user owns the slot before deleting
        if ($availability->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized action.'
            ], 403);
        }

        // 2. Delete
        $availability->delete();

        return response()->json([
            'message' => 'Availability slot removed successfully.'
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmation;
use App\Mail\BookingNotification;
use App\Models\Booking;
use App\Models\Message;
use Carbon\Carbon;
use PDO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
// You would also import Mail facades for confirmation emails

class BookingController extends Controller
{
    public function indexMetrics()
    {
        // --- 1. Monthly Activity Trend (Bookings vs. Inquiries) ---
        
        $months = [];
        $startDate = Carbon::now()->subMonths(5)->startOfMonth();
        
        // Prepare month names for the last 6 months
        for ($i = 0; $i < 6; $i++) {
            $months[] = $startDate->clone()->addMonths($i)->format('M');
        }

        // Fetch Bookings and Inquiries count by month using a DB-driver-aware expression
        $driver = DB::getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($driver === 'sqlite') {
            $monthExpr = "CAST(strftime('%m', created_at) AS INTEGER)"; // 01..12 -> int
        } elseif ($driver === 'pgsql') {
            $monthExpr = "EXTRACT(MONTH FROM created_at)"; // returns numeric
        } else {
            // MySQL / MariaDB
            $monthExpr = "MONTH(created_at)";
        }

        $bookingsByMonth = Booking::select(DB::raw('count(*) as count'), DB::raw("{$monthExpr} as month"))
                                  ->where('created_at', '>=', $startDate)
                                  ->groupBy('month')
                                  ->pluck('count', 'month');

        // $inquiriesByMonth = Message::select(DB::raw('count(*) as count'), DB::raw("{$monthExpr} as month"))
        //                            ->where('created_at', '>=', $startDate)
        //                            ->groupBy('month')
        //                            ->pluck('count', 'month');
        
        $monthlyActivity = [];
        
        // Combine data into the format required by the chart
        foreach ($months as $index => $monthName) {
            $monthNumber = $startDate->clone()->addMonths($index)->month;
            $monthlyActivity[] = [
                // numeric month for chart x-axis logic and a separate human label
                'month' => $monthNumber,
                'monthLabel' => $monthName,
                'Bookings' => $bookingsByMonth->get($monthNumber, 0),
                // 'Inquiries' => $inquiriesByMonth->get($monthNumber, 0),
            ];
        }


        // --- 2. Service Demand Breakdown (Volume by service_type) ---
        $serviceDemand = Booking::select('service_type', DB::raw('count(*) as Volume'))
                                ->groupBy('service_type')
                                ->orderBy('Volume', 'desc')
                                ->get()
                                ->map(fn($item) => ['service' => $item->service_type, 'Volume' => $item->Volume])
                                ->toArray();
        
        return response()->json([
            'monthlyActivity' => $monthlyActivity,
            'serviceDemand' => $serviceDemand,
        ]);
    }

    /**
     * Handles the client submission of a new booking request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // 1. Validation: Ensures all necessary fields are present and valid.
        $validator = Validator::make($request->all(), [
            'client_name' => ['required', 'string', 'max:255'],
            'client_email' => ['required', 'email', 'max:255'],
            'booking_date' => ['required', 'date_format:Y-m-d'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'service_type' => ['required', 'string', 'max:100'],
            'location_type' => ['required', 'in:online,in-person'],
            'availability_id' => ['nullable', 'exists:availabilities,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The booking request was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Business Logic & Conflict Check (Crucial for a real booking system)
        
        // Before saving, you would typically check if the selected date/time slot 
        // is ALREADY booked by another client. This is a placeholder:
        $isConflict = Booking::where('booking_date', $request->input('booking_date'))
                             ->where('start_time', $request->input('start_time'))
                             ->where('end_time', $request->input('end_time'))
                             ->where('status', '!=', 'canceled')
                             ->exists();

        if ($isConflict) {
            return response()->json([
                'message' => 'The selected time slot is no longer available. Please choose another time.'
            ], 409); // 409 Conflict
        }

        // 3. Create the Booking
        $booking = Booking::create([
            // Since this is a solo business, we assume the coach ID is 1 or fetched dynamically
            'coach_id' => 1, 
            'availability_id' => $request->input('availability_id'),
            'client_name' => $request->input('client_name'),
            'client_email' => $request->input('client_email'),
            'booking_date' => $request->input('booking_date'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'service_type' => $request->input('service_type'),
            'location_type' => $request->input('location_type'),
            'status' => 'pending', // Requires coach confirmation
        ]);
        Mail::to($booking->client_email)->send(new BookingConfirmation($booking));
        Mail::to('boutout.ea@gmail.com')->send(new BookingNotification($booking));

        // 5. Return success response
        return response()->json([
            'message' => 'Your booking request has been submitted successfully and is pending confirmation.',
            'booking' => $booking
        ], 201);
    }

    /**
     * Return a list of bookings for the coach/dashboard.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // In a multi-coach system you would filter by the authenticated coach.
        $bookings = Booking::orderBy('booking_date', 'asc')->orderBy('start_time', 'asc')->get();

        return response()->json([
            'bookings' => $bookings
        ], 200);
    }

    /**
     * Show a single booking resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Booking $booking)
    {
        return response()->json([
            'booking' => $booking
        ], 200);
    }

    /**
     * Update the booking status (confirmed / canceled / pending).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,canceled']
        ]);

        $booking->status = $validated['status'];
        $booking->save();

        // Optionally dispatch notifications to client/coach here.

        return response()->json([
            'message' => 'Booking status updated.',
            'booking' => $booking
        ], 200);
    }
}

@component('mail::message')
# New Booking Request

A new **{{ $booking->service_type }}** session has been requested by **{{ $booking->client_name }}**.

## Booking Details

@component('mail::table')
| Field          | Value                                                |
|----------------|------------------------------------------------------|
| **Service**    | {{ $booking->service_type }}                         |
| **Date**       | {{ $booking->booking_date->format('l, F jS, Y') }}   |
| **Time**       | {{ $booking->start_time }} to {{ $booking->end_time }} |
| **Location**   | {{ ucfirst($booking->location_type) }}               |
| **Client Email** | {{ $booking->client_email }}                       |
| **Status**     | {{ ucfirst($booking->status) }}                      |
@endcomponent

## Action Required
Please log into the dashboard to review the request and **confirm or cancel the booking**.

@component('mail::button', ['url' => 'YOUR_DASHBOARD_BOOKING_URL'])
Review Booking in Dashboard
@endcomponent

Thank you,  
**Parker's Excellence Group System**
@endcomponent
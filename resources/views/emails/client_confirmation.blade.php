@component('mail::message')
# Request Received!

Dear {{ $booking->client_name }},

Thank you for submitting a booking request with TaxesAccountant.  
We have successfully received your request for the following session:

## Requested Session Summary

@component('mail::table')
| Field        | Value                                    |
|--------------|-------------------------------------------|
| **Service**  | {{ $booking->service_type }}              |
| **Date**     | {{ $date }}                               |
| **Time**     | {{ $booking->start_time }} – {{ $booking->end_time }} |
| **Location** | {{ ucfirst($booking->location_type) }}    |
@endcomponent

## Important: Your Booking is Pending

This is an acknowledgment of receipt only.  
Your request status is currently **{{ ucfirst($booking->status) }}**.

A team member will review the coach’s schedule and confirm the session within **24 business hours**.  
You will receive another email once the status is updated to **Confirmed** or **Canceled**.

If you have any questions in the meantime, feel free to **reply to this email**.

Best regards,  
**TaxesAccountant**
@endcomponent
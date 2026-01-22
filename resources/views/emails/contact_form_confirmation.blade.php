<x-mail::message>
# Thank You for Contacting Us

{{ $first_name ? 'Hello ' . $first_name . ',' : 'Hello,' }}

We've received your message and appreciate you taking the time to reach out to us. A member of our team will review your inquiry and get back to you as soon as possible.

<x-mail::panel>
Our typical response time is within 24-48 business hours.
</x-mail::panel>

<x-mail::button :url="route('home')">
Visit Our Website
</x-mail::button>

Thanks,<br>
The {{ config('app.name') }} Team

<x-mail::subcopy>
If you have any urgent matters, please contact us directly at +1 (310) 848-8598 
</x-mail::subcopy>
</x-mail::message>
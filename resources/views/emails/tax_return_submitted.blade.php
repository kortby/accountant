<x-mail::message>
# Tax Return Submitted Successfully

Hello {{ $userName }},

Your tax return for **{{ $taxYear }}** has been successfully submitted on {{ $submittedAt->format('F j, Y') }} at {{ $submittedAt->format('g:i A') }}.

## What's Next?

Our team will review your submission and contact you if we need any additional information or documents. You can track the status of your tax return through your dashboard.

<x-mail::button :url="config('app.url') . '/dashboard'">
View Dashboard
</x-mail::button>

If you have any questions or concerns, please don't hesitate to reach out to us.

Thank you for choosing our services!

Best regards,<br>
{{ config('app.name') }}
</x-mail::message>

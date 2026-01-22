<x-mail::message>
# New Contact Form Submission

You have received a new message from your website's contact form.

<x-mail::panel>
**Contact Details:**
- Name: {{ $first_name ? $first_name : 'Not provided' }} {{ $last_name ? $last_name : '' }}
- Email: {{ $email ? $email : 'Not provided' }}
- Phone: {{ $phone ? $phone : 'Not provided' }}
</x-mail::panel>

<x-mail::panel>
**Message:**
{{ $content ? $content : 'No message provided' }}
</x-mail::panel>

<x-mail::button :url="url('/admin')">
View All Contact Submissions
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}

<x-mail::subcopy>
This is an automated message. Please do not reply directly to this email.
</x-mail::subcopy>
</x-mail::message>

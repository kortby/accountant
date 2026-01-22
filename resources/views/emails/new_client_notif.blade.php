<x-mail::message>
# New User Registration

A new user has signed up for your platform:

<x-mail::panel>
**User Details:**
- Name: {{ $first_name }} {{ $last_name }}
- Email: {{ $email }}
- Signed up at: {{ $created_at->format('Y-m-d H:i:s') }}
</x-mail::panel>


Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
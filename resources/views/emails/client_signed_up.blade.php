<x-mail::message>
# Welcome, {{ $first_name }}!

Thank you for signing up with our company. We're excited to have you on board!

Your account has been successfully created and is now ready to use. Below are some quick links to help you get started:

<x-mail::button :url="route('dashboard')">
Go to Dashboard
</x-mail::button>

<x-mail::panel>
If you have any questions or need assistance, please don't hesitate to contact our support team.
</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}

<x-mail::subcopy>
This email was sent to you because you signed up for an account on our platform.
</x-mail::subcopy>
</x-mail::message>


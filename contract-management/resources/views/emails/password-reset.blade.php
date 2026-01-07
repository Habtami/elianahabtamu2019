<x-mail::message>
# Password Reset Request

Hello {{ $user->name }},

You are receiving this email because we received a password reset request for your EPSS Contract Management System account.

<x-mail::button :url="$url" color="primary">
Reset Password
</x-mail::button>

This password reset link will expire in {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60) }} minutes.

If you did not request a password reset, no further action is required.

<x-mail::panel>
If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:

<x-mail::text :url="$url" />
</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}

<x-mail::footer>
© {{ date('Y') }} Ethiopian Pharmaceutical Supply Service. All rights reserved.
</x-mail::footer>
</x-mail::message>

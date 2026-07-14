<x-mail::message>
@if ($isPasswordReset)
# HR Password Reset

Your HR staff password for **{{ $user->company?->name }}** has been reset by your company admin.
@else
# Welcome to Akwaaba NSS Portal

You have been added as HR staff for **{{ $user->company?->name }}**.
@endif

## Your Login Credentials

- **Email:** {{ $user->email }}
- **Temporary Password:** `{{ $temporaryPassword }}`

<x-mail::button :url="route('hr.login')">
Login to HR Portal
</x-mail::button>

You will be required to change your password on first login.

Please keep your credentials safe.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

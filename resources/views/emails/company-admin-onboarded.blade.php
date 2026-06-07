<x-mail::message>
# Company Registered on Akwaaba NSS Portal

Your company, **{{ $user->company?->name }}**, has been registered on the Akwaaba NSS Portal.

## Your Admin Login Credentials

- **Email:** {{ $user->email }}
- **Temporary Password:** `{{ $temporaryPassword }}`

<x-mail::button :url="url('/login')">
Login to Portal
</x-mail::button>

You will be required to change your password on first login.

Please keep your credentials safe.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

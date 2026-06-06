<x-mail::message>
# Welcome to Akwaaba NSS Portal

You have been successfully onboarded to **{{ $user->company?->name }}** for your National Service.

## Your Login Credentials

- **Email:** {{ $user->email }}
- **NSS Number:** {{ $user->nss_number }}
- **Temporary Password:** `{{ $temporaryPassword }}`

<x-mail::button :url="route('personnel.login')">
Login to Portal
</x-mail::button>

You will be required to change your password on first login and complete your profile information.

Please keep your credentials safe.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

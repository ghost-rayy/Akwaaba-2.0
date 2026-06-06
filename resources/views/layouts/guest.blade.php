<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }} — NSS Portal</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-stormy-900 via-stormy-800 to-stormy-900">
            <div class="w-full sm:max-w-md px-6">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/10 backdrop-blur rounded-2xl mb-4">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white">Akwaaba NSS Portal</h1>
                    <p class="text-stormy-200 text-sm mt-1">National Service Scheme — Enrollment Platform</p>
                </div>

                <div class="bg-white/95 backdrop-blur shadow-2xl rounded-2xl p-8">
                    {{ $slot }}
                </div>

                <p class="text-center text-stormy-300 text-xs mt-6">
                    &copy; {{ date('Y') }} Akwaaba NSS Portal. All rights reserved.
                </p>
            </div>
        </div>
    </body>
</html>

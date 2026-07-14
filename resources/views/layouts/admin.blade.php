<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} @hasSection('title') — @yield('title') @endif</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
@php
    $user = auth('admin')->user();

    $navGroups = [
        [
            'label' => 'Overview',
            'items' => [
                ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ],
        ],
        [
            'label' => 'Management',
            'items' => [
                ['route' => 'admin.companies', 'label' => 'Companies', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                ['route' => 'admin.personnel', 'label' => 'All Personnel', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ],
        ],
    ];
@endphp

<div
    x-data="{ sidebarOpen: false }"
    x-on:keydown.escape.window="sidebarOpen = false"
    class="relative flex min-h-screen"
>
    {{-- Mobile overlay --}}
    <div
        x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-stormy-900/40 backdrop-blur-[2px] lg:hidden"
        style="display: none;"
        aria-hidden="true"
    ></div>

    {{-- Sidebar --}}
    <aside
        :class="sidebarOpen && 'max-lg:translate-x-0'"
        class="z-50 flex w-72 shrink-0 flex-col overflow-hidden border-r border-stormy-100 bg-white transition-transform duration-300 ease-out max-lg:fixed max-lg:inset-y-0 max-lg:left-0 max-lg:-translate-x-full max-lg:shadow-xl lg:fixed lg:top-0 lg:left-0 lg:z-20 lg:h-screen lg:translate-x-0 lg:shadow-none"
        aria-label="Admin navigation"
    >
        <div class="border-b border-stormy-50 px-4 py-4">
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-3" @click="sidebarOpen = false">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-stormy-500 to-stormy-700">
                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="truncate text-base font-bold text-gray-900">{{ config('app.name') }}</p>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-stormy-400">Admin portal</p>
                </div>
            </a>
        </div>

        <nav class="flex-1 overflow-hidden px-3 py-3 flex flex-col justify-between">
            <div class="space-y-3">
            @foreach ($navGroups as $group)
                <div>
                    <p class="mb-1 px-3 text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-400">
                        {{ $group['label'] }}
                    </p>
                    <ul class="space-y-0.5">
                        @foreach ($group['items'] as $item)
                            @php $active = request()->routeIs($item['route']); @endphp
                            <li>
                                <a
                                    href="{{ route($item['route']) }}"
                                    wire:navigate
                                    @click="sidebarOpen = false"
                                    class="group relative flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-medium transition-all
                                        {{ $active
                                            ? 'bg-gradient-to-r from-stormy-600 to-stormy-700 text-white shadow-md shadow-stormy-600/20'
                                            : 'text-gray-600 hover:bg-stormy-50 hover:text-stormy-800' }}"
                                >
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                                        {{ $active ? 'bg-white/15 text-white' : 'bg-stormy-50 text-stormy-600 group-hover:bg-white group-hover:text-stormy-700' }}">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                                        </svg>
                                    </span>
                                    <span class="flex-1 truncate">{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
            </div>
        </nav>

        <div class="space-y-1.5 border-t border-stormy-50 p-3">
            <div class="flex items-center gap-2.5 rounded-xl bg-stormy-50/80 px-3 py-2">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-stormy-100 text-xs font-bold text-stormy-700">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-stormy-900">{{ $user->name }}</p>
                    <p class="truncate text-xs capitalize text-stormy-500">{{ str_replace('_', ' ', $user->role) }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-1.5">
                <a
                    href="{{ route('admin.profile') }}"
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                >
                    Profile
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center rounded-xl border border-rose-100 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100"
                    >
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex min-w-0 flex-1 flex-col lg:ml-72">
        <button
            type="button"
            @click="sidebarOpen = true"
            class="fixed bottom-5 left-5 z-30 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-stormy-700 text-white shadow-lg shadow-stormy-700/30 hover:bg-stormy-800 lg:hidden"
            aria-label="Open menu"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <main class="flex-1 py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>

@include('layouts.partials.app-chrome')
</body>
</html>

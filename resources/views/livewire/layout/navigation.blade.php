<?php

use App\Livewire\Actions\Logout;
use App\Models\Company;
use App\Support\GuardSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component
{
    public ?string $companyLogoUrl = null;

    public ?string $companyName = null;

    public function mount(): void
    {
        $this->refreshCompanyBranding();
    }

    #[On('company-logo-updated')]
    public function refreshCompanyBranding(?string $url = null): void
    {
        if ($url) {
            $this->companyLogoUrl = $url;

            return;
        }

        $user = auth()->user();

        if (! $user?->company_id) {
            $this->companyName = config('app.name');
            $this->companyLogoUrl = null;

            return;
        }

        $company = Company::query()
            ->whereKey($user->company_id)
            ->first(['name', 'logo_path']);

        $this->companyName = $company?->name ?? config('app.name');
        $this->companyLogoUrl = $company?->logo_path
            ? asset('storage/'.$company->logo_path)
            : null;
    }

    public function logout(Logout $logout): void
    {
        $user = Auth::user();
        $guard = Auth::getDefaultDriver();
        $logout($guard);

        $this->redirect(route(GuardSession::loginRouteForUser($user), absolute: false), navigate: true);
    }
}; ?>

<nav x-data="{ open: false }"
     x-on:livewire:navigated.window="$wire.refreshCompanyBranding()"
     class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route(GuardSession::dashboardRoute()) }}" wire:navigate class="flex items-center gap-3">
                    @if ($companyLogoUrl)
                        <img src="{{ $companyLogoUrl }}"
                             alt="{{ $companyName }} logo"
                             wire:key="company-logo-{{ md5($companyLogoUrl) }}"
                             class="h-9 w-9 object-contain rounded-lg border border-gray-200 bg-white p-0.5">
                    @else
                        <div class="w-9 h-9 bg-gradient-to-br from-stormy-500 to-stormy-700 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                        </div>
                    @endif
                    <span class="font-bold text-gray-900 text-lg">{{ $companyName }}</span>
                </a>
            </div>

            <div class="flex items-center gap-4">
                @php $user = auth()->user(); @endphp

                <div class="hidden sm:flex sm:items-center">
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 border border-gray-200 transition-colors">
                                <div class="w-7 h-7 bg-stormy-100 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-bold text-stormy-600">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                                <span x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name" class="max-w-[120px] truncate"></span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                <p class="text-xs text-stormy-600 font-medium capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                            </div>
                            <x-dropdown-link :href="route(GuardSession::profileRoute())" wire:navigate>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Profile
                                </div>
                            </x-dropdown-link>
                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link>
                                    <div class="flex items-center gap-2 text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Log Out
                                    </div>
                                </x-dropdown-link>
                            </button>
                        </x-slot>
                    </x-dropdown>
                </div>

                <div class="flex items-center sm:hidden">
                    <button @click="open = ! open" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                        <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-200">
        <div class="px-4 py-3 bg-gray-50">
            <p class="text-sm font-medium text-gray-900" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"></p>
            <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
        </div>
        <div class="py-2">
            <x-responsive-nav-link :href="route(GuardSession::profileRoute())" wire:navigate>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profile
                </div>
            </x-responsive-nav-link>
            <button wire:click="logout" class="w-full text-start">
                <x-responsive-nav-link>
                    <div class="flex items-center gap-2 text-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Log Out
                    </div>
                </x-responsive-nav-link>
            </button>
        </div>
    </div>
</nav>

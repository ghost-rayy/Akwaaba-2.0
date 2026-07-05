<?php

use App\Livewire\Forms\LoginForm;
use App\Support\GuardSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $user = $this->form->validateCredentials();

        if ($user->role !== 'company_admin') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'form.email' => GuardSession::wrongPortalMessage($user->role, 'company.login'),
            ]);
        }

        Auth::guard('company')->login($user, $this->form->remember);
        Session::regenerate();
        session()->forget('url.intended');

        cookie()->queue(GuardSession::portalCookie('company'));

        $this->redirect(route('company.dashboard', absolute: false));
    }
}; ?>

<div>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-gray-900">Company Sign In</h2>
        <p class="text-sm text-gray-500 mt-1">Restricted access</p>
    </div>

    <form wire:submit="login" class="space-y-5">
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <input wire:model="form.email" id="email" type="email" required autofocus autocomplete="username"
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm"
                       placeholder="you@company.com">
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-1" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <input wire:model="form.password" id="password" type="password" required autocomplete="current-password"
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm"
                       placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-1" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember" class="flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox"
                       class="rounded border-gray-300 text-stormy-600 shadow-sm focus:ring-stormy-500">
                <span class="ml-2 text-sm text-gray-600">Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm text-stormy-600 hover:text-stormy-800 font-medium" href="{{ route('password.request') }}" wire:navigate>
                    Forgot password?
                </a>
            @endif
        </div>

        <x-loading-button
            target="login"
            loading="Signing in..."
            class="w-full py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-gradient-to-r from-stormy-600 to-stormy-700 hover:from-stormy-700 hover:to-stormy-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stormy-500 transition-all">
            Sign In
        </x-loading-button>
    </form>
</div>

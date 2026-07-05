<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest:admin')->group(function () {
    Volt::route('admin/login', 'pages.auth.admin-login')
        ->name('admin.login');
});

Route::middleware('guest:company')->group(function () {
    Volt::route('company/login', 'pages.auth.login')
        ->name('company.login');

    Volt::route('hr/login', 'pages.auth.hr-login')
        ->name('hr.login');

    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->name('password.reset');
});

Route::middleware('guest:personnel')->group(function () {
    Volt::route('login', 'pages.auth.personnel-login')
        ->name('login');

    Volt::route('register', 'pages.auth.register')
        ->name('register');
});

Route::middleware('auth:admin,company,personnel')->group(function () {
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');
});

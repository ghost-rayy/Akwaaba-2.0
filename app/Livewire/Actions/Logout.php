<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke(?string $guard = null): void
    {
        $guard = $guard ?? Auth::getDefaultDriver();

        Auth::guard($guard)->logout();

        Session::invalidate();
        Session::regenerateToken();
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        \Illuminate\Support\Facades\View::composer(['layouts.app', 'layouts.company'], function ($view) {
            $view->with('pendingToasts', \App\Support\FlashToast::pending());
        });

        \Illuminate\Support\Facades\View::composer('layouts.company', function ($view) {
            $user = auth()->user();

            $view->with(
                'navCounters',
                ($user && $user->company_id)
                    ? \App\Support\CompanyNavCounters::for($user)
                    : \App\Support\CompanyNavCounters::empty()
            );
        });

        $request = request();

        config([
            'session.secure' => $request->isSecure(),
            'session.same_site' => 'lax',
        ]);

        if ($this->app->environment('local')) {
            URL::forceRootUrl(rtrim($request->root(), '/'));
        }

        $livewireTmp = storage_path('app/private/livewire-tmp');

        if (! is_dir($livewireTmp)) {
            mkdir($livewireTmp, 0755, true);
        }
    }
}

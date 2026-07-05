<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Support\GuardSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        
        $middleware->web(prepend: [
            \App\Http\Middleware\ConfigureGuardSession::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'completed.profile' => \App\Http\Middleware\EnsureProfileCompleted::class,
            'auth.any' => \App\Http\Middleware\AuthenticateAnyGuard::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin', 'admin/*')) {
                return route('admin.login');
            }

            if ($request->is('company', 'company/*')) {
                return route('company.login');
            }

            if ($request->is('personnel', 'personnel/*')) {
                return route('login');
            }

            return route('login');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();

            if ($user?->must_change_password && auth()->getDefaultDriver() === 'personnel') {
                return route(GuardSession::profileRoute());
            }

            return route(GuardSession::dashboardRoute());
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

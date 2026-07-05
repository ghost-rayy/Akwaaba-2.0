<?php

namespace App\Http\Middleware;

use App\Support\GuardSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAnyGuard
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        foreach (GuardSession::GUARDS as $guard) {
            if ($this->authenticateGuard($request, $guard)) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            abort(401);
        }

        return redirect()->guest(route('login'));
    }

    protected function authenticateGuard(Request $request, string $guard): bool
    {
        $sessionId = $request->cookies->get(GuardSession::cookieName($guard));

        if (! $sessionId) {
            return false;
        }

        $session = app('session')->driver();
        $session->setId($sessionId);
        $session->start();

        $request->setLaravelSession($session);

        config([
            'session.cookie' => GuardSession::cookieName($guard),
            'auth.defaults.guard' => $guard,
        ]);

        Auth::shouldUse($guard);

        return Auth::guard($guard)->check();
    }
}

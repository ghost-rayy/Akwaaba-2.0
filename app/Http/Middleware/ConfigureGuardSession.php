<?php

namespace App\Http\Middleware;

use App\Support\GuardSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ConfigureGuardSession
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $guard = null): Response
    {
        $guard = $guard ?? GuardSession::resolveGuard($request);

        config([
            'session.cookie' => GuardSession::cookieName($guard),
            'auth.defaults.guard' => $guard,
        ]);

        Auth::shouldUse($guard);

        $response = $next($request);

        $path = GuardSession::normalizePath($request, trim($request->path(), '/'));

        if (isset(GuardSession::LOGIN_PATHS[$path])) {
            $response->headers->setCookie(GuardSession::portalCookie(
                GuardSession::LOGIN_PATHS[$path]
            ));
        } elseif (Auth::check()) {
            $guard = Auth::getDefaultDriver();

            if (in_array($guard, GuardSession::GUARDS, true)) {
                $response->headers->setCookie(GuardSession::portalCookie($guard));
            }
        }

        return $response;
    }
}

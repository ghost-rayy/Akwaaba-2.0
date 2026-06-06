<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'nss_personnel') {
            if ($user->must_change_password || $user->form_step < 3) {
                if (!$request->routeIs('personnel.dashboard')) {
                    return redirect()->route('personnel.dashboard')
                        ->with('error', 'Please complete all registration steps to access other sections.');
                }
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuardSession
{
    public const GUARDS = ['admin', 'company', 'personnel'];

    public const PORTAL_COOKIE = 'auth_portal';

    public const LOGIN_PATHS = [
        'admin/login' => 'admin',
        'admin.login' => 'admin',
        'company/login' => 'company',
        'company.login' => 'company',
        'hr/login' => 'company',
        'hr.login' => 'company',
        'login' => 'personnel',
    ];

    public static function cookieName(string $guard): string
    {
        $slug = Str::slug((string) config('app.name', 'laravel'));

        return match ($guard) {
            'admin' => "{$slug}-admin-session",
            'company' => "{$slug}-company-session",
            'personnel' => "{$slug}-personnel-session",
            default => "{$slug}-session",
        };
    }

    public static function resolveGuard(Request $request): string
    {
        $path = self::normalizePath($request, trim($request->path(), '/'));

        if (str_starts_with($path, 'livewire') || str_starts_with($path, 'document/stream')) {
            return self::resolveGuardFromPortalCookie($request)
                ?? self::resolveGuardFromReferer($request)
                ?? self::resolveGuardFromSessionCookie($request)
                ?? 'web';
        }

        return self::guardForPath($path) ?? 'web';
    }

    public static function resolveGuardFromReferer(Request $request): ?string
    {
        $referer = $request->headers->get('referer');

        if (! $referer) {
            return null;
        }

        $path = self::normalizePath(
            $request,
            trim(parse_url($referer, PHP_URL_PATH) ?? '', '/'),
        );

        return self::guardForPath($path);
    }

    public static function normalizePath(Request $request, string $path): string
    {
        $path = trim($path, '/');

        foreach ([trim($request->getBasePath(), '/'), trim(parse_url((string) config('app.url'), PHP_URL_PATH) ?? '', '/')] as $basePath) {
            if ($basePath === '') {
                continue;
            }

            if ($path === $basePath) {
                return '';
            }

            if (str_starts_with($path, $basePath.'/')) {
                $path = substr($path, strlen($basePath) + 1);
            }
        }

        $path = trim($path, '/');

        foreach (['admin/login', 'admin.login', 'company/login', 'company.login', 'hr/login', 'hr.login', 'login', 'register', 'forgot-password', 'livewire/update', 'document/stream'] as $route) {
            if ($path === $route || str_ends_with($path, '/'.$route)) {
                return $route;
            }
        }

        foreach (['admin', 'company', 'personnel'] as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return $path;
            }

            $segment = '/'.$prefix.'/';
            $position = strrpos($path, $segment);

            if ($position !== false) {
                return substr($path, $position + 1);
            }

            if (str_ends_with($path, '/'.$prefix)) {
                return $prefix;
            }
        }

        if (str_starts_with($path, 'reset-password')) {
            return $path;
        }

        $resetPosition = strrpos($path, '/reset-password');

        if ($resetPosition !== false) {
            return substr($path, $resetPosition + 1);
        }

        return $path;
    }

    public static function guardForPath(string $path): ?string
    {
        if (isset(self::LOGIN_PATHS[$path])) {
            return self::LOGIN_PATHS[$path];
        }

        if (in_array($path, ['register'], true)) {
            return 'personnel';
        }

        if (str_starts_with($path, 'admin')) {
            return 'admin';
        }

        if (in_array($path, ['forgot-password'], true) || str_starts_with($path, 'reset-password') || str_starts_with($path, 'company')) {
            return 'company';
        }

        if (str_starts_with($path, 'personnel')) {
            return 'personnel';
        }

        return null;
    }

    public static function resolveGuardFromPortalCookie(Request $request): ?string
    {
        $portal = $request->cookie(self::PORTAL_COOKIE);

        return in_array($portal, self::GUARDS, true) ? $portal : null;
    }

    public static function resolveGuardFromSessionCookie(Request $request): ?string
    {
        $portal = self::resolveGuardFromPortalCookie($request);

        if ($portal && $request->cookies->has(self::cookieName($portal))) {
            return $portal;
        }

        foreach (self::GUARDS as $guard) {
            if ($request->cookies->has(self::cookieName($guard))) {
                return $guard;
            }
        }

        return null;
    }

    public static function portalCookie(string $guard): \Symfony\Component\HttpFoundation\Cookie
    {
        return cookie(
            self::PORTAL_COOKIE,
            $guard,
            60 * 24 * 7,
            '/',
            null,
            (bool) config('session.secure'),
            false,
            false,
            config('session.same_site', 'lax'),
        );
    }

    public static function loginPathForGuard(string $guard): ?string
    {
        return array_search($guard, self::LOGIN_PATHS, true) ?: null;
    }

    public static function profileRoute(): string
    {
        return match (auth()->getDefaultDriver()) {
            'admin' => 'admin.profile',
            'company' => 'company.profile',
            'personnel' => 'personnel.profile',
            default => 'company.profile',
        };
    }

    public static function dashboardRoute(): string
    {
        return match (auth()->getDefaultDriver()) {
            'admin' => 'admin.dashboard',
            'company' => 'company.dashboard',
            'personnel' => 'personnel.dashboard',
            default => 'company.dashboard',
        };
    }

    public static function loginRoute(): string
    {
        return match (auth()->getDefaultDriver()) {
            'admin' => 'admin.login',
            'company' => 'company.login',
            default => 'login',
        };
    }

    public static function loginRouteForRole(string $role): string
    {
        return match ($role) {
            'super_admin' => 'admin.login',
            'company_admin' => 'company.login',
            'hr_staff' => 'hr.login',
            'nss_personnel' => 'login',
            default => 'login',
        };
    }

    public static function loginRouteForUser(?\App\Models\User $user): string
    {
        if ($user) {
            return self::loginRouteForRole($user->role);
        }

        return self::loginRoute();
    }

    public static function wrongPortalMessage(string $role, string $currentLoginRoute): string
    {
        $correctRoute = self::loginRouteForRole($role);

        if ($correctRoute === $currentLoginRoute) {
            return trans('auth.failed');
        }

        $portalLabel = match ($correctRoute) {
            'admin.login' => 'administrator',
            'company.login' => 'company',
            'hr.login' => 'HR',
            default => 'personnel',
        };

        return "This account belongs on the {$portalLabel} sign-in page.";
    }
}

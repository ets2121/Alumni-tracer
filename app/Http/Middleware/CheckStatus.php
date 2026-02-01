<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // Admins are always allowed
            if ($user->role === 'admin') {
                return $next($request);
            }

            // Block rejected users entirely
            if ($user->status === 'rejected') {
                auth()->logout();
                return redirect()->route('login')->with('status', 'Your account application has been rejected. Please contact the administrator.');
            }

            // Block pending users from internal features (except dashboard/profile if needed, but per request we block data)
            // However, it's simpler to allow only active users for the entire route group
            // Block pending users from internal features
            if ($user->status !== 'active') {
                $allowedRoutes = [
                    'dashboard',
                    'alumni.profile.edit',
                    'alumni.profile.update',
                    'profile.edit',
                    'profile.update',
                    'profile.destroy',
                    'alumni.news.index', // Allow News Feed
                    'alumni.news.show',
                    'alumni.employment.store', // Allow editing profile related stuff
                    'alumni.employment.update',
                    'alumni.employment.destroy',
                    'logout'
                ];

                if (!in_array($request->route()->getName(), $allowedRoutes)) {
                    return redirect()->route('dashboard')->with('warning', 'Your account is under verification. Access to sensitive records is restricted.');
                }
            }
        }

        return $next($request);
    }
}

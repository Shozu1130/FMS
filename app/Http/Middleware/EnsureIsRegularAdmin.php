<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsRegularAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Only regular admins (not master admins) can access these routes
        if (auth()->user()->isMasterAdmin()) {
            abort(403, 'Access denied. This section is only available to department administrators.');
        }

        // Ensure user is an admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Administrator privileges required.');
        }

        return $next($request);
    }
}

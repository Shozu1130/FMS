<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // ← ADD THIS IMPORT

class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Use Auth facade instead of auth() helper for better IDE support
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login first.');
        }

        if (Auth::user()->role !== 'admin') {
            return redirect('/login')->with('error', 'Admin access required.');
        }

        return $next($request);
    }
}
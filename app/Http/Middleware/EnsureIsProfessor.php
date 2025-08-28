<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // ← ADD THIS IMPORT

class EnsureIsProfessor
{
    public function handle(Request $request, Closure $next): Response
    {
        // Use Auth facade instead of auth() helper
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login first.');
        }

        $professor = Auth::guard('faculty')->user();
        if (!$professor || $professor->role !== 'professor') {
            return redirect('/login')->with('error', 'Professor access required.');
        }

        return $next($request);
    }
}
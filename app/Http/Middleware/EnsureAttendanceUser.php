<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAttendanceUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated as faculty
        if (!Auth::guard('faculty')->check()) {
            return redirect()->route('login');
        }

        // Check if the attendance_user session flag is set
        if (!session()->has('attendance_user') || !session('attendance_user')) {
            return redirect()->route('attendance.login')
                ->with('error', 'You must log in to access attendance monitoring.');
        }

        return $next($request);
    }
}

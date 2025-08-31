<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AttendanceAuthController extends Controller
{
    /**
     * Show the attendance login form.
     */
    public function showLoginForm()
    {
        return view('attendance.login');
    }

    /**
     * Handle attendance login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $credentials = $request->only('email', 'password');

        // Attempt to authenticate using faculty guard
        if (Auth::guard('faculty')->attempt($credentials)) {
            $faculty = Auth::guard('faculty')->user();
            
            // Check if faculty is active
            if ($faculty->status !== 'active') {
                Auth::guard('faculty')->logout();
                return redirect()->back()
                    ->withInput($request->only('email'))
                    ->with('error', 'Your account is not active. Please contact the administrator.');
            }

            $request->session()->regenerate();
            
            // Mark this user as an attendance user
            $request->session()->put('attendance_user', true);

            // Log successful login
            \Log::info("Faculty {$faculty->name} logged into attendance system at " . now()->format('Y-m-d H:i:s'));

            return redirect()->route('attendance.dashboard');
        }

        // If authentication fails, return error
        return redirect()->back()
            ->withInput($request->only('email'))
            ->with('error', 'Invalid credentials. Please check your username and password.');
    }

    /**
     * Handle attendance logout.
     */
    public function logout(Request $request)
    {
        $faculty = Auth::guard('faculty')->user();
        
        if ($faculty) {
            \Log::info("Faculty {$faculty->name} logged out of attendance system at " . now()->format('Y-m-d H:i:s'));
        }

        Auth::guard('faculty')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Clear attendance user flag
        $request->session()->forget('attendance_user');

        return redirect()->route('attendance.login')
            ->with('success', 'You have been successfully logged out.');
    }

}

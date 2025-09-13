<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Faculty;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        Log::info('Login attempt', ['email' => $credentials['email']]);

        // First try to authenticate as ADMIN (User model)
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            Log::info('Admin login successful', ['user_id' => $user->id, 'role' => $user->role]);
            
            if ($user->role === 'admin' || $user->role === 'master_admin') {
                return redirect()->route('admin.dashboard');
            }
            
            Auth::logout();
            Log::info('Admin user logged out (not admin role)');
        }

        // Then try to authenticate as PROFESSOR (Faculty model)
        if (Auth::guard('faculty')->attempt($credentials)) {
            $professor = Auth::guard('faculty')->user();
            Log::info('Professor login successful', ['professor_id' => $professor->id]);
            return redirect()->route('professor.dashboard');
        }

        Log::warning('Login failed for email: ' . $credentials['email']);
        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ]); 
    }

    public function logout(Request $request)
{
    // Clear all session data first to prevent array conversion issues
    $request->session()->flush();
    
    // Then logout from both guards
    Auth::logout();
    Auth::guard('faculty')->logout();
    
    // Regenerate session token for security
    $request->session()->regenerateToken();
    
    return redirect()->route('login')->with('message', 'You have been logged out successfully.');
}
}
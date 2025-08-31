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
            
            if ($user->role === 'admin') {
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
        Auth::logout();
        Auth::guard('faculty')->logout();
        
        // Clear attendance user flag if it exists
        $request->session()->forget('attendance_user');
        
        // Clear all session flash data that might contain arrays
        $sessionKeys = array_keys($request->session()->all());
        $messageKeys = array_filter($sessionKeys, function($key) {
            return strpos($key, 'errors') !== false || 
                   strpos($key, 'error') !== false ||
                   strpos($key, 'message') !== false ||
                   strpos($key, 'warning') !== false ||
                   strpos($key, 'info') !== false ||
                   strpos($key, 'success') !== false;
        });
        
        foreach ($messageKeys as $key) {
            $request->session()->forget($key);
        }
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}
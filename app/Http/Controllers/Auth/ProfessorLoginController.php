<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfessorLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // Adjust the view as necessary
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('faculty')->attempt($credentials)) {
            return redirect()->route('professor.dashboard');
        }

        return redirect()->back()->withErrors(['email' => 'Invalid credentials.']);
    }
}

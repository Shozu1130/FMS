<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfessorProfileController extends Controller
{
    public function edit()
    {
        $professor = Auth::guard('faculty')->user();
        return view('professor.profile', compact('professor'));
    }

    public function update(Request $request)
    {
        $professor = Auth::guard('faculty')->user();

        $validated = $request->validate([
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'skills' => 'nullable|string|max:500',
            'experiences' => 'nullable|string|max:1000'
        ]);

        // Handle file upload
        if ($request->hasFile('picture')) {
            // Delete old picture if exists
            if ($professor->picture) {
                Storage::disk('public')->delete($professor->picture);
            }
            
            $path = $request->file('picture')->store('professor_pictures', 'public');
            $validated['picture'] = $path;
        }

        $professor->update($validated);

        return redirect()->route('professor.profile.edit')
                         ->with('success', 'Profile updated successfully!');
    }

    public function changePassword(Request $request)
    {
        $professor = Auth::guard('faculty')->user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Verify current password
        if (!\Illuminate\Support\Facades\Hash::check($validated['current_password'], $professor->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        // Update password
        $professor->password = \Illuminate\Support\Facades\Hash::make($validated['new_password']);
        $professor->save();

        return redirect()->route('professor.profile.edit')
                         ->with('success', 'Password changed successfully!');
    }
}

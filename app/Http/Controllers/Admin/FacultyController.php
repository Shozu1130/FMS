<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; // ← ADD THIS IMPORT
use App\Mail\ProfessorAccountCreated;
use Illuminate\Support\Facades\Mail;


class FacultyController extends Controller
{
    public function create()
    {
        return view('admin.faculty.create');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $faculty = Faculty::whereNull('deleted_at')->orderByDesc('created_at')->paginate(10);
        return view('admin.faculty.index', compact('faculty'));
    }

    /**
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
{
    // FORCE GMAIL CONFIGURATION AT THE TOP
    config([
        'mail.mailers.smtp' => [
            'transport' => 'smtp',
            'host' => 'smtp.gmail.com',
            'port' => 465,
            'encryption' => 'ssl',
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
        ]
    ]);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:faculty',
    ]);

    // Generate professor ID
    $validated['professor_id'] = Faculty::generateProfessorId();
    
    // Store password in variable BEFORE hashing
    $plainPassword = Str::random(12);
    $validated['password'] = bcrypt($plainPassword);
    
    $validated['status'] = 'active';

    // Create faculty member
    $faculty = Faculty::create($validated);

    try {
        // FIXED: Send to PROFESSOR'S email, not yours
        Mail::raw("Welcome {$faculty->name}!

Your Professor Account Details:
- Professor ID: {$faculty->professor_id}
- Email: {$faculty->email}
- Password: {$plainPassword}

Please login at: ".url('/login')."

Change your password after first login.", function($message) use ($faculty) {
            $message->to($faculty->email) // ← CHANGED TO PROFESSOR'S EMAIL
                    ->subject('Professor Account Created');
        });

        return redirect()->route('admin.faculty.index')
                         ->with('success', 'Professor account created! ID: ' . $faculty->professor_id);
                         
    } catch (\Exception $e) {
        return redirect()->route('admin.faculty.index')
                         ->with('warning', 'Professor account created but email failed: ' . $e->getMessage());
    }
}
    /**
     * Display the specified resource.
     */
    public function show(Faculty $faculty)
    {
        $professor = $faculty;
        return view('admin.faculty.show', compact('professor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faculty $faculty)
    {
        $professor = $faculty;
        return view('admin.faculty.edit', compact('professor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faculty $faculty)
    {
        $validated = $request->validate([
            'professor_id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:faculty,email,' . $faculty->id,
            'status' => 'required|in:active,inactive',
        ]);

        $faculty->update($validated);

        return redirect()->route('admin.faculty.index')
            ->with('success', 'Professor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faculty $faculty)
    {
        try {
            $faculty->delete();
            return redirect()->route('admin.faculty.index')
                ->with('success', 'Professor moved to directory successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete professor', [
                'faculty_id' => $faculty->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('admin.faculty.index')
                ->with('warning', 'Unable to delete professor. It may be referenced by other records.');
        }
    }

    /**
     * Restore a soft deleted faculty member.
     */
    public function restore($id)
    {
        try {
            $faculty = Faculty::onlyTrashed()->findOrFail($id);
            $faculty->restore();
            return redirect()->route('admin.directory.index')
                ->with('success', 'Professor restored successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to restore professor', [
                'faculty_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('admin.directory.index')
                ->with('warning', 'Unable to restore professor.');
        }
    }

    /**
     * Permanently delete a faculty member.
     */
    public function forceDelete($id)
    {
        try {
            $faculty = Faculty::onlyTrashed()->findOrFail($id);
            $faculty->forceDelete();
            return redirect()->route('admin.directory.index')
                ->with('success', 'Professor permanently deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to permanently delete professor', [
                'faculty_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('admin.directory.index')
                ->with('warning', 'Unable to permanently delete professor.');
        }
    }
}

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
        $query = Faculty::whereNull('deleted_at');
        
        // Filter by department if not master admin
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $query->where('department', auth()->user()->department);
        }
        
        $faculty = $query->orderByDesc('created_at')->paginate(10);
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

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:faculties',
        'employment_type' => 'required|in:Full-Time,Part-Time',
    ]);

    // Generate professor ID
    $validated['professor_id'] = Faculty::generateProfessorId();
    
    // Store password in variable BEFORE hashing
    $plainPassword = Str::random(12);
    $validated['password'] = bcrypt($plainPassword);
    
    $validated['status'] = 'active';
    
    // Assign department based on admin's department (or allow master admin to choose)
    if (auth()->user()->isMasterAdmin()) {
        $validated['department'] = $request->input('department', 'BSIT');
    } else {
        $validated['department'] = auth()->user()->department ?? 'BSIT';
    }

    // Create faculty member
    $faculty = Faculty::create($validated);

    try {
        // Send email using the ProfessorAccountCreated mailable
        Mail::to($faculty->email)->send(new ProfessorAccountCreated(
            $faculty->name,
            $faculty->email,
            $faculty->professor_id,
            $plainPassword
        ));

        return redirect()->route('admin.faculty.index')
                         ->with('success', 'Professor account created and email sent! ID: ' . $faculty->professor_id);
                         
    } catch (\Exception $e) {
        Log::error('Email sending failed', [
            'faculty_id' => $faculty->id,
            'email' => $faculty->email,
            'error' => $e->getMessage()
        ]);
        
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
            'email' => 'required|email|unique:faculties,email,' . $faculty->id,
            'status' => 'required|in:active,inactive',
            'employment_type' => 'required|in:Full-Time,Part-Time',
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

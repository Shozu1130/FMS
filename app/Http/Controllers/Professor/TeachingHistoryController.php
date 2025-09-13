<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\TeachingHistory;
use Illuminate\Http\Request;

class TeachingHistoryController extends Controller
{
    public function index()
    {
        $faculty = auth('faculty')->user();
        $teachingHistories = $faculty->teachingHistories()
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester')
            ->paginate(15);

        return view('professor.teaching_history.index', compact('teachingHistories'));
    }

    public function create()
    {
        return view('professor.teaching_history.create');
    }

    public function store(Request $request)
    {
        $faculty = auth('faculty')->user();

        // Debug: Check if faculty is authenticated
        if (!$faculty) {
            return redirect()->back()->withErrors(['auth' => 'Faculty authentication failed.']);
        }

        $validated = $request->validate(TeachingHistory::rules());
        $validated['professor_id'] = $faculty->id;
        $validated['is_active'] = true; // Set default value for is_active

        try {
            TeachingHistory::create($validated);
            return redirect()->route('professor.teaching_history.index')
                ->with('success', 'Teaching assignment added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['db' => 'Failed to save teaching history: ' . $e->getMessage()]);
        }
    }

    public function show(TeachingHistory $teachingHistory)
    {
        // Ensure the teaching history belongs to the authenticated professor
        if ($teachingHistory->professor_id !== auth('faculty')->id()) {
            abort(403);
        }

        $teachingHistory->load('faculty', 'evaluations');
        return view('professor.teaching_history.show', compact('teachingHistory'));
    }

    public function edit(TeachingHistory $teachingHistory)
    {
        // Ensure the teaching history belongs to the authenticated professor
        if ($teachingHistory->professor_id !== auth('faculty')->id()) {
            abort(403);
        }

        return view('professor.teaching_history.edit', compact('teachingHistory'));
    }

    public function update(Request $request, TeachingHistory $teachingHistory)
    {
        // Ensure the teaching history belongs to the authenticated professor
        if ($teachingHistory->professor_id !== auth('faculty')->id()) {
            abort(403);
        }

        $validated = $request->validate(TeachingHistory::rules($teachingHistory->id));

        $teachingHistory->update($validated);

        return redirect()->route('professor.teaching_history.index')
            ->with('success', 'Teaching assignment updated successfully.');
    }

    public function destroy(TeachingHistory $teachingHistory)
    {
        // Ensure the teaching history belongs to the authenticated professor
        if ($teachingHistory->professor_id !== auth('faculty')->id()) {
            abort(403);
        }

        $teachingHistory->delete();

        return redirect()->route('professor.teaching_history.index')
            ->with('success', 'Teaching assignment deleted successfully.');
    }
}

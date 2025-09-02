<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeachingHistory;
use App\Models\Faculty;
use Illuminate\Http\Request;

class TeachingHistoryController extends Controller
{
    public function index()
    {
        $teachingHistories = TeachingHistory::with('faculty')
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester')
            ->orderBy('faculty_id')
            ->paginate(20);

        return view('admin.teaching_history.index', compact('teachingHistories'));
    }

    public function create()
    {
        $faculties = Faculty::where('status', 'active')->orderBy('name')->get();
        return view('admin.teaching_history.create', compact('faculties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(TeachingHistory::rules());
        
        TeachingHistory::create($validated);

        return redirect()->route('admin.teaching_history.index')
            ->with('success', 'Teaching assignment created successfully.');
    }

    public function show(TeachingHistory $teachingHistory)
    {
        $teachingHistory->load('faculty', 'evaluations');
        return view('admin.teaching_history.show', compact('teachingHistory'));
    }

    public function edit(TeachingHistory $teachingHistory)
    {
        $faculties = Faculty::where('status', 'active')->orderBy('name')->get();
        return view('admin.teaching_history.edit', compact('teachingHistory', 'faculties'));
    }

    public function update(Request $request, TeachingHistory $teachingHistory)
    {
        $validated = $request->validate(TeachingHistory::rules($teachingHistory->id));
        
        $teachingHistory->update($validated);

        return redirect()->route('admin.teaching_history.index')
            ->with('success', 'Teaching assignment updated successfully.');
    }

    public function destroy(TeachingHistory $teachingHistory)
    {
        $teachingHistory->delete();

        return redirect()->route('admin.teaching_history.index')
            ->with('success', 'Teaching assignment deleted successfully.');
    }

    public function facultyTeachingHistory(Faculty $faculty)
    {
        $teachingHistories = $faculty->teachingHistories()
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester')
            ->paginate(15);

        return view('admin.teaching_history.faculty', compact('faculty', 'teachingHistories'));
    }

    public function currentSemester()
    {
        $currentYear = date('Y');
        $currentSemester = TeachingHistory::getCurrentSemesterStatic();

        $teachingHistories = TeachingHistory::with('faculty')
            ->where('academic_year', $currentYear)
            ->where('semester', $currentSemester)
            ->where('is_active', true)
            ->orderBy('faculty_id')
            ->paginate(20);

        return view('admin.teaching_history.current', compact('teachingHistories', 'currentYear', 'currentSemester'));
    }
}

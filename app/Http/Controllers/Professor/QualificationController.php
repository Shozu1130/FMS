<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\ProfessorQualification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class QualificationController extends Controller
{
    /**
     * Store a newly created qualification.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required', Rule::in(array_keys(ProfessorQualification::TYPES))],
            'title' => 'required|string|max:255',
            'institution_company' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'level' => ['nullable', Rule::in(array_keys(ProfessorQualification::LEVELS))],
            'is_current' => 'boolean'
        ]);

        $data = $request->all();
        $data['professor_id'] = Auth::id();
        
        // If is_current is true, set end_date to null
        if ($request->boolean('is_current')) {
            $data['end_date'] = null;
        }

        $qualification = ProfessorQualification::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Qualification added successfully',
            'qualification' => $qualification
        ]);
    }

    /**
     * Display the specified qualification.
     */
    public function show(ProfessorQualification $qualification)
    {
        // Ensure the qualification belongs to the authenticated professor
        if ($qualification->professor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'qualification' => $qualification
        ]);
    }

    /**
     * Update the specified qualification.
     */
    public function update(Request $request, ProfessorQualification $qualification)
    {
        // Ensure the qualification belongs to the authenticated professor
        if ($qualification->professor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'type' => ['required', Rule::in(array_keys(ProfessorQualification::TYPES))],
            'title' => 'required|string|max:255',
            'institution_company' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'level' => ['nullable', Rule::in(array_keys(ProfessorQualification::LEVELS))],
            'is_current' => 'boolean'
        ]);

        $data = $request->all();
        
        // If is_current is true, set end_date to null
        if ($request->boolean('is_current')) {
            $data['end_date'] = null;
        }

        $qualification->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Qualification updated successfully',
            'qualification' => $qualification->fresh()
        ]);
    }

    /**
     * Remove the specified qualification.
     */
    public function destroy(ProfessorQualification $qualification)
    {
        // Ensure the qualification belongs to the authenticated professor
        if ($qualification->professor_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $qualification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Qualification deleted successfully'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $professor = Auth::guard('faculty')->user();
        $requests = LeaveRequest::where('faculty_id', $professor->id)
            ->orderByDesc('created_at')
            ->get();
        return view('professor.leave.index', compact('requests'));
    }

    public function create()
    {
        $types = LeaveRequest::types();
        return view('professor.leave.create', compact('types'));
    }

    public function store(Request $request)
    {
        $professor = Auth::guard('faculty')->user();
        $validated = $request->validate([
            'type' => 'required|string',
            'reason' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $validated['faculty_id'] = $professor->id;
        $validated['status'] = LeaveRequest::STATUS_PENDING;
        LeaveRequest::create($validated);

        return redirect()->route('professor.leave.index')
            ->with('success', 'Leave request submitted.');
    }
}




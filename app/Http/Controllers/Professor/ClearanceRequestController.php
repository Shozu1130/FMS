<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\ClearanceRequest;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClearanceRequestController extends Controller
{
    /**
     * Display a listing of the professor's clearance requests.
     */
    public function index()
    {
        $faculty = Auth::guard('faculty')->user();
        
        if (!$faculty) {
            return redirect()->route('professor.dashboard')->with('error', 'Faculty profile not found.');
        }

        $requests = ClearanceRequest::where('faculty_id', $faculty->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('professor.clearance_requests.index', compact('requests', 'faculty'));
    }

    /**
     * Show the form for creating a new clearance request.
     */
    public function create()
    {
        $faculty = Auth::guard('faculty')->user();
        
        if (!$faculty) {
            return redirect()->route('professor.dashboard')->with('error', 'Faculty profile not found.');
        }

        $clearanceTypes = ClearanceRequest::getClearanceTypes();

        return view('professor.clearance_requests.create', compact('clearanceTypes', 'faculty'));
    }

    /**
     * Store a newly created clearance request in storage.
     */
    public function store(Request $request)
    {
        $faculty = Auth::guard('faculty')->user();
        
        if (!$faculty) {
            return redirect()->route('professor.dashboard')->with('error', 'Faculty profile not found.');
        }

        $validated = $request->validate([
            'clearance_type' => 'required|string|in:' . implode(',', array_keys(ClearanceRequest::getClearanceTypes())),
            'reason' => 'required|string|max:1000',
        ]);

        // Check if there's already a pending request for this clearance type
        $existingRequest = ClearanceRequest::where('faculty_id', $faculty->id)
            ->where('clearance_type', $validated['clearance_type'])
            ->where('status', ClearanceRequest::STATUS_PENDING)
            ->first();

        if ($existingRequest) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'You already have a pending request for this clearance type.');
        }

        ClearanceRequest::create([
            'faculty_id' => $faculty->id,
            'clearance_type' => $validated['clearance_type'],
            'reason' => $validated['reason'],
            'status' => ClearanceRequest::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        return redirect()->route('professor.clearance-requests.index')
            ->with('success', 'Clearance request submitted successfully.');
    }

    /**
     * Display the specified clearance request.
     */
    public function show(ClearanceRequest $clearanceRequest)
    {
        $faculty = Auth::guard('faculty')->user();
        
        if (!$faculty || $clearanceRequest->faculty_id !== $faculty->id) {
            abort(403, 'Unauthorized access to this clearance request.');
        }

        return view('professor.clearance_requests.show', compact('clearanceRequest', 'faculty'));
    }

    /**
     * Show the form for editing the specified clearance request.
     * Only pending requests can be edited.
     */
    public function edit(ClearanceRequest $clearanceRequest)
    {
        $faculty = Auth::guard('faculty')->user();
        
        if (!$faculty || $clearanceRequest->faculty_id !== $faculty->id) {
            abort(403, 'Unauthorized access to this clearance request.');
        }

        if (!$clearanceRequest->isPending()) {
            return redirect()->route('professor.clearance-requests.show', $clearanceRequest)
                ->with('error', 'Only pending requests can be edited.');
        }

        $clearanceTypes = ClearanceRequest::getClearanceTypes();

        return view('professor.clearance_requests.edit', compact('clearanceRequest', 'clearanceTypes', 'faculty'));
    }

    /**
     * Update the specified clearance request in storage.
     * Only pending requests can be updated.
     */
    public function update(Request $request, ClearanceRequest $clearanceRequest)
    {
        $faculty = Auth::guard('faculty')->user();
        
        if (!$faculty || $clearanceRequest->faculty_id !== $faculty->id) {
            abort(403, 'Unauthorized access to this clearance request.');
        }

        if (!$clearanceRequest->isPending()) {
            return redirect()->route('professor.clearance-requests.show', $clearanceRequest)
                ->with('error', 'Only pending requests can be edited.');
        }

        $validated = $request->validate([
            'clearance_type' => 'required|string|in:' . implode(',', array_keys(ClearanceRequest::getClearanceTypes())),
            'reason' => 'required|string|max:1000',
        ]);

        // Check if there's already a pending request for this clearance type (excluding current request)
        if ($validated['clearance_type'] !== $clearanceRequest->clearance_type) {
            $existingRequest = ClearanceRequest::where('faculty_id', $faculty->id)
                ->where('clearance_type', $validated['clearance_type'])
                ->where('status', ClearanceRequest::STATUS_PENDING)
                ->where('id', '!=', $clearanceRequest->id)
                ->first();

            if ($existingRequest) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'You already have a pending request for this clearance type.');
            }
        }

        $clearanceRequest->update($validated);

        return redirect()->route('professor.clearance-requests.show', $clearanceRequest)
            ->with('success', 'Clearance request updated successfully.');
    }

    /**
     * Remove the specified clearance request from storage.
     * Only pending requests can be deleted.
     */
    public function destroy(ClearanceRequest $clearanceRequest)
    {
        $faculty = Auth::guard('faculty')->user();
        
        if (!$faculty || $clearanceRequest->faculty_id !== $faculty->id) {
            abort(403, 'Unauthorized access to this clearance request.');
        }

        if (!$clearanceRequest->isPending()) {
            return redirect()->route('professor.clearance-requests.index')
                ->with('error', 'Only pending requests can be deleted.');
        }

        $clearanceRequest->delete();

        return redirect()->route('professor.clearance-requests.index')
            ->with('success', 'Clearance request deleted successfully.');
    }

    /**
     * Get clearance request statistics for the professor.
     */
    public function stats()
    {
        $faculty = Auth::guard('faculty')->user();
        
        if (!$faculty) {
            return response()->json(['error' => 'Faculty profile not found.'], 404);
        }

        $stats = [
            'total' => ClearanceRequest::where('faculty_id', $faculty->id)->count(),
            'pending' => ClearanceRequest::where('faculty_id', $faculty->id)->pending()->count(),
            'approved' => ClearanceRequest::where('faculty_id', $faculty->id)->approved()->count(),
            'rejected' => ClearanceRequest::where('faculty_id', $faculty->id)->rejected()->count(),
        ];

        return response()->json($stats);
    }
}

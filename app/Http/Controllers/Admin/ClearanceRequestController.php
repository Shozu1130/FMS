<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClearanceRequest;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClearanceRequestController extends Controller
{
    /**
     * Display a listing of all clearance requests.
     */
    public function index(Request $request)
    {
        $query = ClearanceRequest::with(['faculty']);

        // Filter by department if not master admin
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $query->whereHas('faculty', function($q) {
                $q->where('department', auth()->user()->department);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by clearance type
        if ($request->filled('clearance_type')) {
            $query->where('clearance_type', $request->clearance_type);
        }

        // Filter by faculty
        if ($request->filled('professor_id')) {
            $query->where('professor_id', $request->professor_id);
        }

        // Search by faculty name or reason
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('faculty', function($facultyQuery) use ($search) {
                    $facultyQuery->where('name', 'like', "%{$search}%");
                })->orWhere('reason', 'like', "%{$search}%");
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        $clearanceTypes = ClearanceRequest::getClearanceTypes();
        
        // Filter faculties by department
        $facultiesQuery = Faculty::query();
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultiesQuery->where('department', auth()->user()->department);
        }
        $faculties = $facultiesQuery->get();

        return view('admin.clearance_requests.index', compact('requests', 'clearanceTypes', 'faculties'));
    }

    /**
     * Display the specified clearance request.
     */
    public function show(ClearanceRequest $clearanceRequest)
    {
        $clearanceRequest->load(['faculty', 'processedBy']);
        
        return view('admin.clearance_requests.show', compact('clearanceRequest'));
    }

    /**
     * Approve a clearance request.
     */
    public function approve(Request $request, ClearanceRequest $clearanceRequest)
    {
        if (!$clearanceRequest->isPending()) {
            return redirect()->back()->with('error', 'Only pending requests can be approved.');
        }

        $validated = $request->validate([
            'admin_remarks' => 'nullable|string|max:500',
        ]);

        $clearanceRequest->update([
            'status' => ClearanceRequest::STATUS_APPROVED,
            'admin_remarks' => $validated['admin_remarks'] ?? null,
            'processed_at' => now(),
            'processed_by' => Auth::id(),
        ]);

        return redirect()->route('admin.clearance-requests.show', $clearanceRequest)
            ->with('success', 'Clearance request approved successfully.');
    }

    /**
     * Reject a clearance request.
     */
    public function reject(Request $request, ClearanceRequest $clearanceRequest)
    {
        if (!$clearanceRequest->isPending()) {
            return redirect()->back()->with('error', 'Only pending requests can be rejected.');
        }

        $validated = $request->validate([
            'admin_remarks' => 'required|string|max:500',
        ]);

        $clearanceRequest->update([
            'status' => ClearanceRequest::STATUS_REJECTED,
            'admin_remarks' => $validated['admin_remarks'],
            'processed_at' => now(),
            'processed_by' => Auth::id(),
        ]);

        return redirect()->route('admin.clearance-requests.show', $clearanceRequest)
            ->with('success', 'Clearance request rejected successfully.');
    }

    /**
     * Bulk approve multiple clearance requests.
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:clearance_requests,id',
            'admin_remarks' => 'nullable|string|max:500',
        ]);

        $updated = ClearanceRequest::whereIn('id', $validated['request_ids'])
            ->where('status', ClearanceRequest::STATUS_PENDING)
            ->update([
                'status' => ClearanceRequest::STATUS_APPROVED,
                'admin_remarks' => $validated['admin_remarks'] ?? null,
                'processed_at' => now(),
                'processed_by' => Auth::id(),
            ]);

        return redirect()->back()->with('success', "Successfully approved {$updated} clearance requests.");
    }

    /**
     * Bulk reject multiple clearance requests.
     */
    public function bulkReject(Request $request)
    {
        $validated = $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:clearance_requests,id',
            'admin_remarks' => 'required|string|max:500',
        ]);

        $updated = ClearanceRequest::whereIn('id', $validated['request_ids'])
            ->where('status', ClearanceRequest::STATUS_PENDING)
            ->update([
                'status' => ClearanceRequest::STATUS_REJECTED,
                'admin_remarks' => $validated['admin_remarks'],
                'processed_at' => now(),
                'processed_by' => Auth::id(),
            ]);

        return redirect()->back()->with('success', "Successfully rejected {$updated} clearance requests.");
    }

    /**
     * Get clearance request statistics.
     */
    public function stats()
    {
        $stats = [
            'total' => ClearanceRequest::count(),
            'pending' => ClearanceRequest::pending()->count(),
            'approved' => ClearanceRequest::approved()->count(),
            'rejected' => ClearanceRequest::rejected()->count(),
            'by_type' => [],
            'recent_requests' => ClearanceRequest::with(['faculty'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];

        // Get statistics by clearance type
        foreach (ClearanceRequest::getClearanceTypes() as $key => $name) {
            $stats['by_type'][$key] = [
                'name' => $name,
                'total' => ClearanceRequest::where('clearance_type', $key)->count(),
                'pending' => ClearanceRequest::where('clearance_type', $key)->pending()->count(),
                'approved' => ClearanceRequest::where('clearance_type', $key)->approved()->count(),
                'rejected' => ClearanceRequest::where('clearance_type', $key)->rejected()->count(),
            ];
        }

        return response()->json($stats);
    }

    /**
     * Export clearance requests to CSV.
     */
    public function export(Request $request)
    {
        $query = ClearanceRequest::with(['faculty', 'processedBy']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('clearance_type')) {
            $query->where('clearance_type', $request->clearance_type);
        }

        if ($request->filled('professor_id')) {
            $query->where('professor_id', $request->professor_id);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        $filename = 'clearance_requests_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($requests) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'Faculty Name',
                'Employee ID',
                'Clearance Type',
                'Reason',
                'Status',
                'Requested At',
                'Processed At',
                'Processed By',
                'Admin Remarks'
            ]);

            // CSV data
            foreach ($requests as $request) {
                fputcsv($file, [
                    $request->id,
                    $request->faculty->name ?? 'N/A',
                    $request->faculty->employee_id ?? 'N/A',
                    $request->clearance_type_name,
                    $request->reason,
                    ucfirst($request->status),
                    $request->requested_at->format('Y-m-d H:i:s'),
                    $request->processed_at ? $request->processed_at->format('Y-m-d H:i:s') : 'N/A',
                    $request->processedBy->name ?? 'N/A',
                    $request->admin_remarks ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Dashboard view with statistics and recent requests.
     */
    public function dashboard()
    {
        $stats = [
            'total' => ClearanceRequest::count(),
            'pending' => ClearanceRequest::pending()->count(),
            'approved' => ClearanceRequest::approved()->count(),
            'rejected' => ClearanceRequest::rejected()->count(),
        ];

        $recentRequests = ClearanceRequest::with(['faculty'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $pendingRequests = ClearanceRequest::with(['faculty'])
            ->pending()
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        return view('admin.clearance_requests.dashboard', compact('stats', 'recentRequests', 'pendingRequests'));
    }
}

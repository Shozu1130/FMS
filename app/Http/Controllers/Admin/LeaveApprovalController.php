<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveApprovalController extends Controller
{
    public function index()
    {
        $query = LeaveRequest::with('faculty');
        
        // Filter by department if not master admin
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $query->whereHas('faculty', function($q) {
                $q->where('department', auth()->user()->department);
            });
        }
        
        $requests = $query->orderByDesc('created_at')->get();
        return view('admin.leave.index', compact('requests'));
    }

    public function update(Request $request, LeaveRequest $leave)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);
        $leave->update(['status' => $validated['status']]);

        return redirect()->route('admin.leave.index')->with('success', 'Leave request updated.');
    }
}




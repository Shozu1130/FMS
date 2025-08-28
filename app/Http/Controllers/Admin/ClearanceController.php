<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clearance;
use App\Models\Faculty;
use Illuminate\Http\Request;

class ClearanceController extends Controller
{
    public function index()
    {
        $clearances = Clearance::with('faculty')
            ->orderBy('is_cleared')
            ->orderBy('expiration_date', 'desc')
            ->orderBy('faculty_id')
            ->paginate(20);

        return view('admin.clearance.index', compact('clearances'));
    }

    public function create()
    {
        $faculties = Faculty::where('status', 'active')->orderBy('name')->get();
        $clearanceTypes = Clearance::getClearanceTypes();
        
        return view('admin.clearance.create', compact('faculties', 'clearanceTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(Clearance::rules());

        Clearance::create($validated);

        return redirect()->route('admin.clearance.index')
            ->with('success', 'Clearance record created successfully.');
    }

    public function show(Clearance $clearance)
    {
        $clearance->load('faculty');
        return view('admin.clearance.show', compact('clearance'));
    }

    public function edit(Clearance $clearance)
    {
        $faculties = Faculty::where('status', 'active')->orderBy('name')->get();
        $clearanceTypes = Clearance::getClearanceTypes();
        
        return view('admin.clearance.edit', compact('clearance', 'faculties', 'clearanceTypes'));
    }

    public function update(Request $request, Clearance $clearance)
    {
        $validated = $request->validate(Clearance::rules($clearance->id));

        $clearance->update($validated);

        return redirect()->route('admin.clearance.index')
            ->with('success', 'Clearance record updated successfully.');
    }

    public function destroy(Clearance $clearance)
    {
        $clearance->delete();

        return redirect()->route('admin.clearance.index')
            ->with('success', 'Clearance record deleted successfully.');
    }

    public function facultyClearances(Faculty $faculty)
    {
        $clearances = $faculty->clearances()
            ->orderBy('is_cleared')
            ->orderBy('expiration_date', 'desc')
            ->paginate(15);

        return view('admin.clearance.faculty', compact('faculty', 'clearances'));
    }

    public function pending()
    {
        $clearances = Clearance::with('faculty')
            ->pending()
            ->orderBy('faculty_id')
            ->paginate(20);

        return view('admin.clearance.pending', compact('clearances'));
    }

    public function expired()
    {
        $clearances = Clearance::with('faculty')
            ->expired()
            ->orderBy('expiration_date')
            ->paginate(20);

        return view('admin.clearance.expired', compact('clearances'));
    }

    public function markCleared(Clearance $clearance)
    {
        $clearance->update(['is_cleared' => true]);

        return redirect()->back()
            ->with('success', 'Clearance marked as cleared successfully.');
    }

    public function markPending(Clearance $clearance)
    {
        $clearance->update(['is_cleared' => false]);

        return redirect()->back()
            ->with('success', 'Clearance marked as pending successfully.');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'clearances' => 'required|array',
            'clearances.*' => 'exists:clearances,id',
            'action' => 'required|in:clear,pending'
        ]);

        $action = $request->action;
        $clearances = Clearance::whereIn('id', $request->clearances)->get();

        foreach ($clearances as $clearance) {
            $clearance->update([
                'is_cleared' => $action === 'clear'
            ]);
        }

        return redirect()->back()
            ->with('success', count($clearances) . ' clearance records updated successfully.');
    }
}

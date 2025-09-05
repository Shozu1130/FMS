<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyDirectoryController extends Controller
{
    public function index()
    {
        // Retrieve only soft deleted faculty members for the directory
        $query = Faculty::onlyTrashed();
        
        // Filter by department if not master admin
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $query->where('department', auth()->user()->department);
        }
        
        $deletedFaculty = $query->orderByDesc('deleted_at')->get();
        return view('admin.directory.index', compact('deletedFaculty'));
    }
}

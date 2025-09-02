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
        $deletedFaculty = Faculty::onlyTrashed()->orderByDesc('deleted_at')->get();
        return view('admin.directory.index', compact('deletedFaculty'));
    }
}

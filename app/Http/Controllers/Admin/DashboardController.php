<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty; // Make sure this import exists

class DashboardController extends Controller
{
    public function index()
    {
        $facultyCount = Faculty::count(); // Now uses correct table
        return view('admin.dashboard', compact('facultyCount'));
    }
}
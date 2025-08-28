<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FacultyDirectoryController extends Controller
{
    public function index()
    {
        // Logic to retrieve and display the faculty directory
        return view('admin.directory.index'); // Adjust the view path as necessary
    }
}

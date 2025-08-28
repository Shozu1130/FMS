<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $professor = Auth::guard('faculty')->user();
        
        $currentSalaryGrade = $professor->getCurrentSalaryGrade();
        
        return view('professor.dashboard', compact('professor', 'currentSalaryGrade'));
    }
}

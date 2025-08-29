<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProfessorProfileController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\TeachingHistoryController;
use App\Http\Controllers\Admin\ClearanceController;
use App\Http\Controllers\Admin\EvaluationController;

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Admin Routes
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('faculty', FacultyController::class)->names('admin.faculty');
    
    // Leave approvals
    Route::get('leave', [\App\Http\Controllers\Admin\LeaveApprovalController::class, 'index'])->name('admin.leave.index');
    Route::put('leave/{leave}', [\App\Http\Controllers\Admin\LeaveApprovalController::class, 'update'])->name('admin.leave.update');
    
    // Salary Grades
    Route::resource('salary-grades', \App\Http\Controllers\Admin\SalaryGradeController::class)->names('admin.salary_grades');
    
    // Teaching History
    Route::resource('teaching-history', TeachingHistoryController::class)->names('admin.teaching_history');
    
    // Clearance System
    Route::resource('clearance', ClearanceController::class)->names('admin.clearance');
    
    // Evaluation System
    Route::resource('evaluation', EvaluationController::class)->names('admin.evaluation');
    
    // Faculty Directory
    Route::get('directory', [\App\Http\Controllers\Admin\FacultyDirectoryController::class, 'index'])->name('admin.directory.index');
    Route::patch('faculty/{faculty}/restore', [FacultyController::class, 'restore'])->name('admin.faculty.restore');
    Route::delete('faculty/{faculty}/force-delete', [FacultyController::class, 'forceDelete'])->name('admin.faculty.force-delete');
 });

// Professor Routes (faculty guard)
Route::prefix('professor')->middleware(['auth:faculty'])->group(function () {
    Route::get('salary-grades', [\App\Http\Controllers\Professor\SalaryGradeController::class, 'index'])->name('professor.salary_grades.index');
    Route::get('dashboard', [\App\Http\Controllers\Professor\DashboardController::class, 'index'])->name('professor.dashboard');
    Route::get('profile', [ProfessorProfileController::class, 'edit'])->name('professor.profile.edit');
    Route::put('profile', [ProfessorProfileController::class, 'update'])->name('professor.profile.update');
    
    // Leave requests
    Route::get('leave', [\App\Http\Controllers\Professor\LeaveRequestController::class, 'index'])->name('professor.leave.index');
    Route::get('leave/create', [\App\Http\Controllers\Professor\LeaveRequestController::class, 'create'])->name('professor.leave.create');
    Route::post('leave', [\App\Http\Controllers\Professor\LeaveRequestController::class, 'store'])->name('professor.leave.store');
});

// Debug routes (keep only essential ones)
Route::get('/test-email-simple', function () {
    config([
        'mail.mailers.smtp' => [
            'transport' => 'smtp',
            'host' => 'smtp.gmail.com',
            'port' => 465,
            'encryption' => 'ssl',
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
        ]
    ]);

    try {
        \Illuminate\Support\Facades\Mail::raw('Simple test email without database', function($message) {
            $message->to('your-email@gmail.com')
                    ->subject('Test - No DB Involved');
        });
        return 'Email sent successfully without database!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/force-logout', function () {
    Auth::logout();
    session()->flush();
    return redirect('/login');
});

// Smart default redirect based on who's logged in
Route::get('/', function () {
    if (Auth::guard('faculty')->check()) {
        return redirect()->route('professor.dashboard');
    }
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

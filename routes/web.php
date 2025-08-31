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
use App\Http\Controllers\AttendanceAuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Attendance Monitoring Routes
Route::get('attendance/login', [AttendanceAuthController::class, 'showLoginForm'])->name('attendance.login');
Route::post('attendance/login', [AttendanceAuthController::class, 'login']);
Route::post('attendance/logout', [AttendanceAuthController::class, 'logout'])->name('attendance.logout');

// Admin Routes
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('faculty', FacultyController::class)->names('admin.faculty');
    
    // Leave approvals
    Route::get('leave', [\App\Http\Controllers\Admin\LeaveApprovalController::class, 'index'])->name('admin.leave.index');
    Route::put('leave/{leave}', [\App\Http\Controllers\Admin\LeaveApprovalController::class, 'update'])->name('admin.leave.update');
    
    // Salary Grades
    Route::resource('salary-grades', \App\Http\Controllers\Admin\SalaryGradeController::class)->names('admin.salary_grades');
    
    // Attendance Management
    Route::resource('attendance', AdminAttendanceController::class)->names('admin.attendance');
    Route::get('attendance/faculty-summary', [AdminAttendanceController::class, 'facultySummary'])->name('admin.attendance.faculty_summary');
    Route::get('attendance/export', [AdminAttendanceController::class, 'export'])->name('admin.attendance.export');
    
    // Teaching History
    Route::resource('teaching-history', TeachingHistoryController::class)->names('admin.teaching_history');
    
    // Clearance System
    Route::resource('clearance', ClearanceController::class)->names('admin.clearance');
    
    // Evaluation System (specific routes first to avoid resource catching them)
    Route::get('evaluation/faculty-summary', [EvaluationController::class, 'facultyRatingSummary'])->name('admin.evaluation.faculty_summary');
    Route::get('evaluation/faculty/{faculty}/create', [EvaluationController::class, 'createForFaculty'])->name('admin.evaluation.create_for_faculty');
    Route::post('evaluation/faculty/{faculty}', [EvaluationController::class, 'storeForFaculty'])->name('admin.evaluation.store_for_faculty');
    Route::post('evaluation/store-from-modal', [EvaluationController::class, 'storeFromModal'])->name('admin.evaluation.store');
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
    Route::post('profile/change-password', [ProfessorProfileController::class, 'changePassword'])->name('professor.profile.change-password');
    
    // Leave requests
    Route::get('leave', [\App\Http\Controllers\Professor\LeaveRequestController::class, 'index'])->name('professor.leave.index');
    Route::get('leave/create', [\App\Http\Controllers\Professor\LeaveRequestController::class, 'create'])->name('professor.leave.create');
    Route::post('leave', [\App\Http\Controllers\Professor\LeaveRequestController::class, 'store'])->name('professor.leave.store');
    
    // Evaluation results
    Route::get('evaluation', [\App\Http\Controllers\Professor\EvaluationController::class, 'index'])->name('professor.evaluation.index');
    
    // Attendance history
    Route::get('attendance', [\App\Http\Controllers\Professor\DashboardController::class, 'attendanceHistory'])->name('professor.attendance.history');
});

use App\Http\Middleware\EnsureAttendanceUser;

// Attendance Monitoring Routes (requires faculty authentication and attendance login)
Route::prefix('attendance')->middleware(['auth:faculty', 'attendance_user'])->group(function () {
    Route::get('dashboard', [AttendanceController::class, 'dashboard'])->name('attendance.dashboard');
    Route::post('time-in', [AttendanceController::class, 'timeIn'])->name('attendance.time-in');
    Route::post('time-out', [AttendanceController::class, 'timeOut'])->name('attendance.time-out');
    Route::get('{id}/details', [AttendanceController::class, 'showDetails'])->name('attendance.details');
    Route::get('monthly-stats', [AttendanceController::class, 'getMonthlyStats'])->name('attendance.monthly_stats');
    
    // Route to switch to regular professor portal
    Route::get('switch-to-professor', function() {
        session()->forget('attendance_user');
        return redirect()->route('professor.dashboard');
    })->name('attendance.switch_to_professor');
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
        // Check if this is an attendance user (they came from attendance login)
        if (session()->has('attendance_user')) {
            return redirect()->route('attendance.dashboard');
        }
        // Regular faculty user goes to professor dashboard
        return redirect()->route('professor.dashboard');
    }
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

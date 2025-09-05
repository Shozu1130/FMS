<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProfessorProfileController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\TeachingHistoryController;
use App\Http\Controllers\Admin\EvaluationController;
use App\Http\Controllers\AttendanceAuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Middleware\EnsureAttendanceUser;

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Attendance Monitoring Routes
Route::get('attendance/login', [AttendanceAuthController::class, 'showLoginForm'])->name('attendance.login');
Route::post('attendance/login', [AttendanceAuthController::class, 'login']);
Route::post('attendance/logout', [AttendanceAuthController::class, 'logout'])->name('attendance.logout');

// Master Admin Routes
Route::prefix('master-admin')->middleware(['auth', 'master_admin'])->group(function () {
    Route::resource('admin-management', \App\Http\Controllers\MasterAdmin\AdminManagementController::class)->names('master_admin.admin_management');
});

// Admin Routes
Route::prefix('admin')->middleware('auth')->group(function () {
    // Dashboard accessible to all admins (both master and regular)
    Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Routes restricted to regular admins only (not master admins)
    Route::middleware('regular_admin')->group(function () {
        // Faculty Management
        Route::resource('faculty', \App\Http\Controllers\Admin\FacultyController::class)->names('admin.faculty');
        Route::patch('faculty/{faculty}/restore', [FacultyController::class, 'restore'])->name('admin.faculty.restore');
        Route::delete('faculty/{faculty}/force-delete', [FacultyController::class, 'forceDelete'])->name('admin.faculty.force-delete');
        
        // Faculty Directory
        Route::get('directory', [\App\Http\Controllers\Admin\FacultyDirectoryController::class, 'index'])->name('admin.directory.index');
        
        // Leave approvals
        Route::get('leave', [\App\Http\Controllers\Admin\LeaveApprovalController::class, 'index'])->name('admin.leave.index');
        Route::put('leave/{leave}', [\App\Http\Controllers\Admin\LeaveApprovalController::class, 'update'])->name('admin.leave.update');
        
        // Salary Grades & Pay
        Route::resource('salary-grades', \App\Http\Controllers\Admin\SalaryGradeController::class)->names('admin.salary-grades');
        Route::get('salary-grades/{salary_grade}/faculty', [\App\Http\Controllers\Admin\SalaryGradeController::class, 'faculty'])->name('admin.salary-grades.faculty');
        Route::get('salary-grades-assign', [\App\Http\Controllers\Admin\SalaryGradeController::class, 'assign'])->name('admin.salary-grades.assign');
        Route::post('salary-grades-assign', [\App\Http\Controllers\Admin\SalaryGradeController::class, 'assignStore'])->name('admin.salary-grades.assign.store');
        Route::delete('salary-grades-assign/{assignment}', [\App\Http\Controllers\Admin\SalaryGradeController::class, 'assignRemove'])->name('admin.salary-grades.assign.remove');
        
        // Payslips
        Route::get('payslips', [\App\Http\Controllers\Admin\PayslipController::class, 'index'])->name('admin.payslips.index');
        Route::get('payslips/calculations', [\App\Http\Controllers\Admin\PayslipController::class, 'calculations'])->name('admin.payslips.calculations');
        Route::post('payslips/generate-all', [\App\Http\Controllers\Admin\PayslipController::class, 'generateAll'])->name('admin.payslips.generate-all');
        Route::post('payslips/generate-single', [\App\Http\Controllers\Admin\PayslipController::class, 'generateSingle'])->name('admin.payslips.generate-single');
        Route::get('payslips/{payslip}', [\App\Http\Controllers\Admin\PayslipController::class, 'show'])->name('admin.payslips.show');
        Route::post('payslips/{payslip}/finalize', [\App\Http\Controllers\Admin\PayslipController::class, 'finalize'])->name('admin.payslips.finalize');
        Route::post('payslips/{payslip}/mark-paid', [\App\Http\Controllers\Admin\PayslipController::class, 'markPaid'])->name('admin.payslips.mark-paid');
        Route::post('payslips/bulk-finalize', [\App\Http\Controllers\Admin\PayslipController::class, 'bulkFinalize'])->name('admin.payslips.bulk-finalize');
        
        // Attendance Management
        Route::resource('attendance', \App\Http\Controllers\Admin\AttendanceController::class)->names('admin.attendance');
        Route::get('attendance/faculty-summary', [\App\Http\Controllers\Admin\AttendanceController::class, 'facultySummary'])->name('admin.attendance.faculty_summary');
        Route::get('attendance/export', [\App\Http\Controllers\Admin\AttendanceController::class, 'export'])->name('admin.attendance.export');
        
        // Teaching History
        Route::resource('teaching-history', \App\Http\Controllers\Admin\TeachingHistoryController::class)->names('admin.teaching_history');
        
        // Clearance Requests Management
        Route::get('clearance-requests/dashboard', [\App\Http\Controllers\Admin\ClearanceRequestController::class, 'dashboard'])->name('admin.clearance-requests.dashboard');
        Route::get('clearance-requests/export', [\App\Http\Controllers\Admin\ClearanceRequestController::class, 'export'])->name('admin.clearance-requests.export');
        Route::post('clearance-requests/bulk-approve', [\App\Http\Controllers\Admin\ClearanceRequestController::class, 'bulkApprove'])->name('admin.clearance-requests.bulk-approve');
        Route::post('clearance-requests/bulk-reject', [\App\Http\Controllers\Admin\ClearanceRequestController::class, 'bulkReject'])->name('admin.clearance-requests.bulk-reject');
        Route::post('clearance-requests/{clearanceRequest}/approve', [\App\Http\Controllers\Admin\ClearanceRequestController::class, 'approve'])->name('admin.clearance-requests.approve');
        Route::post('clearance-requests/{clearanceRequest}/reject', [\App\Http\Controllers\Admin\ClearanceRequestController::class, 'reject'])->name('admin.clearance-requests.reject');
        Route::resource('clearance-requests', \App\Http\Controllers\Admin\ClearanceRequestController::class)->names('admin.clearance-requests');
        
        // Subject Load Tracker
        Route::get('subject-loads/dashboard', [\App\Http\Controllers\Admin\SubjectLoadTrackerController::class, 'dashboard'])->name('admin.subject-loads.dashboard');
        Route::get('subject-loads/report', [\App\Http\Controllers\Admin\SubjectLoadTrackerController::class, 'report'])->name('admin.subject-loads.report');
        Route::get('subject-loads/export', [\App\Http\Controllers\Admin\SubjectLoadTrackerController::class, 'export'])->name('admin.subject-loads.export');
        Route::post('subject-loads/check-conflicts', [\App\Http\Controllers\Admin\SubjectLoadTrackerController::class, 'checkConflicts'])->name('admin.subject-loads.check-conflicts');
        Route::post('subject-loads/faculty-load', [\App\Http\Controllers\Admin\SubjectLoadTrackerController::class, 'getFacultyLoad'])->name('admin.subject-loads.faculty-load');
        Route::post('subject-loads/bulk-status', [\App\Http\Controllers\Admin\SubjectLoadTrackerController::class, 'bulkUpdateStatus'])->name('admin.subject-loads.bulk-status');
        Route::resource('subject-loads', \App\Http\Controllers\Admin\SubjectLoadTrackerController::class)->names('admin.subject-loads');
        
        // Schedule Assignment
        Route::get('schedule-assignment/dashboard', [\App\Http\Controllers\Admin\ScheduleAssignmentController::class, 'dashboard'])->name('admin.schedule-assignment.dashboard');
        Route::get('schedule-assignment/calendar', [\App\Http\Controllers\Admin\ScheduleAssignmentController::class, 'calendar'])->name('admin.schedule-assignment.calendar');
        Route::get('schedule-assignment/reports', [\App\Http\Controllers\Admin\ScheduleAssignmentController::class, 'reports'])->name('admin.schedule-assignment.reports');
        Route::get('schedule-assignment/export', [\App\Http\Controllers\Admin\ScheduleAssignmentController::class, 'export'])->name('admin.schedule-assignment.export');
        Route::get('schedule-assignment/faculty-load-summary', [\App\Http\Controllers\Admin\ScheduleAssignmentController::class, 'getFacultyLoadSummary'])->name('admin.schedule-assignment.faculty-load-summary');
        Route::get('schedule-assignment/check-conflict', [\App\Http\Controllers\Admin\ScheduleAssignmentController::class, 'checkConflict'])->name('admin.schedule-assignment.check-conflict');
        Route::get('schedule-assignment/check-duplicate', [\App\Http\Controllers\Admin\ScheduleAssignmentController::class, 'checkDuplicate'])->name('admin.schedule-assignment.check-duplicate');
        Route::post('schedule-assignment/bulk-update-status', [\App\Http\Controllers\Admin\ScheduleAssignmentController::class, 'bulkUpdateStatus'])->name('admin.schedule-assignment.bulk-update-status');
        Route::delete('schedule-assignment/bulk-delete', [\App\Http\Controllers\Admin\ScheduleAssignmentController::class, 'bulkDelete'])->name('admin.schedule-assignment.bulk-delete');
        Route::resource('schedule-assignment', \App\Http\Controllers\Admin\ScheduleAssignmentController::class)->names('admin.schedule-assignment');
        
        // Schedule Search
        Route::get('schedule-search', [\App\Http\Controllers\Admin\ScheduleSearchController::class, 'index'])->name('admin.schedule-search.index');
        Route::get('schedule-search/export', [\App\Http\Controllers\Admin\ScheduleSearchController::class, 'export'])->name('admin.schedule-search.export');

        // Evaluation System
        Route::get('evaluation/faculty-summary', [EvaluationController::class, 'facultyRatingSummary'])->name('admin.evaluation.faculty_summary');
        Route::get('evaluation/faculty/{faculty}/create', [EvaluationController::class, 'createForFaculty'])->name('admin.evaluation.create_for_faculty');
        Route::post('evaluation/faculty/{faculty}', [EvaluationController::class, 'storeForFaculty'])->name('admin.evaluation.store_for_faculty');
        Route::post('evaluation/store-from-modal', [EvaluationController::class, 'storeFromModal'])->name('admin.evaluation.store_from_modal');
        Route::resource('evaluation', EvaluationController::class)->names('admin.evaluation');
    });
});

// Professor Routes (faculty guard)
Route::prefix('professor')->middleware(['auth:faculty'])->group(function () {
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

    // Teaching History
    Route::resource('teaching-history', \App\Http\Controllers\Professor\TeachingHistoryController::class)->names('professor.teaching_history');

    // Attendance history
    Route::get('attendance', [\App\Http\Controllers\Professor\DashboardController::class, 'attendanceHistory'])->name('professor.attendance.history');

    // Pay Records
    Route::get('pay', [\App\Http\Controllers\Professor\PayslipController::class, 'index'])->name('professor.pay.index');
    Route::get('pay/{payslip}', [\App\Http\Controllers\Professor\PayslipController::class, 'show'])->name('professor.pay.show');
    Route::get('pay/{payslip}/download-pdf', [\App\Http\Controllers\Professor\PayslipController::class, 'downloadPdf'])->name('professor.pay.download-pdf');
    
    // Clearance Requests
    Route::resource('clearance-requests', \App\Http\Controllers\Professor\ClearanceRequestController::class)->names('professor.clearance-requests');
    
    // Subject Loads (read-only for professors)
    Route::get('subject-loads', [\App\Http\Controllers\Professor\SubjectLoadController::class, 'index'])->name('professor.subject-loads.index');
    Route::get('subject-loads/schedule', [\App\Http\Controllers\Professor\SubjectLoadController::class, 'schedule'])->name('professor.subject-loads.schedule');
    Route::get('subject-loads/{subjectLoad}', [\App\Http\Controllers\Professor\SubjectLoadController::class, 'show'])->name('professor.subject-loads.show');
});

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

// Test route for payslip PDF generation
Route::get('/test-payslip', function () {
    try {
        $faculty = \App\Models\Faculty::first();
        if (!$faculty) {
            return 'No faculty found in database';
        }

        $currentSalaryGrade = $faculty->getCurrentSalaryGrade();
        if (!$currentSalaryGrade) {
            return 'No current salary grade found for faculty';
        }

        // Generate payslip data
        $attendanceSummary = $currentSalaryGrade->getCurrentMonthAttendanceSummary($faculty->id);
        $salaryCalculation = $currentSalaryGrade->getCurrentMonthAdjustedSalary($faculty->id);

        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('professor.salary_grades.payslip', [
            'professor' => $faculty,
            'salaryGrade' => $currentSalaryGrade,
            'attendanceSummary' => $attendanceSummary,
            'salaryCalculation' => $salaryCalculation,
        ]);

        return $pdf->download('test-payslip-' . now()->format('Y-m-d-H-i-s') . '.pdf');
    } catch (\Exception $e) {
        return 'Error generating PDF: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
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

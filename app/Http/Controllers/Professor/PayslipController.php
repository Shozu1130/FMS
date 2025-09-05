<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Payslip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PayslipController extends Controller
{
    /**
     * Display professor's pay records.
     */
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $faculty = Auth::user();
        
        // Auto-generate current month payslip if it doesn't exist
        $currentMonthPayslip = $faculty->payslips()
            ->currentMonth()
            ->first();
            
        if (!$currentMonthPayslip) {
            try {
                $currentMonthPayslip = Payslip::generateForFaculty($faculty->id, now()->year, now()->month);
            } catch (\Exception $e) {
                // Log error but continue showing the page
                \Log::error('Auto-generation of payslip failed: ' . $e->getMessage());
            }
        }
        
        $payslips = $faculty->payslips()
            ->where('year', $year)
            ->orderBy('month', 'desc')
            ->paginate(12);

        return view('professor.pay.index', compact('payslips', 'year', 'currentMonthPayslip'));
    }

    /**
     * Show specific payslip details.
     */
    public function show($id)
    {
        $payslip = Auth::user()->payslips()->findOrFail($id);
        $faculty = Auth::user();
        $salaryGrade = $faculty->getCurrentSalaryGrade();
        
        // Get attendance details for the month
        $attendances = $faculty->attendances()
            ->whereYear('date', $payslip->year)
            ->whereMonth('date', $payslip->month)
            ->orderBy('date')
            ->get();

        return view('professor.pay.show', compact('payslip', 'faculty', 'salaryGrade', 'attendances'));
    }

    /**
     * Download pay record as PDF.
     */
    public function downloadPdf($id)
    {
        $payslip = Auth::user()->payslips()->findOrFail($id);
        $faculty = Auth::user();
        $salaryGrade = $faculty->getCurrentSalaryGrade();
        
        // Get attendance details for the month
        $attendances = $faculty->attendances()
            ->whereYear('date', $payslip->year)
            ->whereMonth('date', $payslip->month)
            ->orderBy('date')
            ->get();

        $pdf = Pdf::loadView('professor.pay.pdf', compact('payslip', 'faculty', 'salaryGrade', 'attendances'));
        
        $filename = "pay_record_{$faculty->professor_id}_{$payslip->year}_{$payslip->month}.pdf";
        
        return $pdf->download($filename);
    }
}

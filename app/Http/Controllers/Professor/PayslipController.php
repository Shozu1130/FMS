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
     * Display professor's payslips.
     */
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        
        $payslips = Auth::user()->payslips()
            ->where('year', $year)
            ->orderBy('month', 'desc')
            ->paginate(12);

        $currentMonthPayslip = Auth::user()->payslips()
            ->currentMonth()
            ->first();

        return view('professor.payslips.index', compact('payslips', 'year', 'currentMonthPayslip'));
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

        return view('professor.payslips.show', compact('payslip', 'faculty', 'salaryGrade', 'attendances'));
    }

    /**
     * Download payslip as PDF.
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

        $pdf = Pdf::loadView('professor.payslips.pdf', compact('payslip', 'faculty', 'salaryGrade', 'attendances'));
        
        $filename = "payslip_{$faculty->professor_id}_{$payslip->year}_{$payslip->month}.pdf";
        
        return $pdf->download($filename);
    }

    /**
     * Generate current month payslip if not exists.
     */
    public function generateCurrent()
    {
        try {
            $faculty = Auth::user();
            $payslip = Payslip::generateForFaculty($faculty->id, now()->year, now()->month);
            
            return redirect()->route('professor.payslips.show', $payslip->id)
                ->with('success', 'Current month payslip generated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating payslip: ' . $e->getMessage());
        }
    }
}

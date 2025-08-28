<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Faculty;
use App\Models\TeachingHistory;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index()
    {
        $evaluations = Evaluation::with(['faculty', 'teachingHistory'])
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester')
            ->orderBy('faculty_id')
            ->paginate(20);

        return view('admin.evaluation.index', compact('evaluations'));
    }

    public function create()
    {
        $faculties = Faculty::where('status', 'active')->orderBy('name')->get();
        $teachingHistories = TeachingHistory::active()->with('faculty')->get();
        $evaluationPeriods = Evaluation::getEvaluationPeriods();
        
        return view('admin.evaluation.create', compact('faculties', 'teachingHistories', 'evaluationPeriods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(Evaluation::rules());

        $evaluation = Evaluation::create($validated);

        return redirect()->route('admin.evaluation.index')
            ->with('success', 'Evaluation created successfully.');
    }

    public function show(Evaluation $evaluation)
    {
        $evaluation->load(['faculty', 'teachingHistory']);
        return view('admin.evaluation.show', compact('evaluation'));
    }

    public function edit(Evaluation $evaluation)
    {
        $faculties = Faculty::where('status', 'active')->orderBy('name')->get();
        $teachingHistories = TeachingHistory::active()->with('faculty')->get();
        $evaluationPeriods = Evaluation::getEvaluationPeriods();
        
        return view('admin.evaluation.edit', compact('evaluation', 'faculties', 'teachingHistories', 'evaluationPeriods'));
    }

    public function update(Request $request, Evaluation $evaluation)
    {
        $validated = $request->validate(Evaluation::rules($evaluation->id));

        $evaluation->update($validated);

        return redirect()->route('admin.evaluation.index')
            ->with('success', 'Evaluation updated successfully.');
    }

    public function destroy(Evaluation $evaluation)
    {
        $evaluation->delete();

        return redirect()->route('admin.evaluation.index')
            ->with('success', 'Evaluation deleted successfully.');
    }

    public function facultyEvaluations(Faculty $faculty)
    {
        $evaluations = $faculty->evaluations()
            ->with('teachingHistory')
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester')
            ->paginate(15);

        return view('admin.evaluation.faculty', compact('faculty', 'evaluations'));
    }

    public function publish(Evaluation $evaluation)
    {
        $evaluation->update(['is_published' => true]);

        return redirect()->back()
            ->with('success', 'Evaluation published successfully.');
    }

    public function unpublish(Evaluation $evaluation)
    {
        $evaluation->update(['is_published' => false]);

        return redirect()->back()
            ->with('success', 'Evaluation unpublished successfully.');
    }

    public function published()
    {
        $evaluations = Evaluation::with(['faculty', 'teachingHistory'])
            ->published()
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester')
            ->paginate(20);

        return view('admin.evaluation.published', compact('evaluations'));
    }

    public function byPeriod(Request $request)
    {
        $academicYear = $request->get('academic_year', date('Y'));
        $semester = $request->get('semester', '1st Semester');
        $period = $request->get('evaluation_period');

        $evaluations = Evaluation::with(['faculty', 'teachingHistory'])
            ->byPeriod($academicYear, $semester, $period)
            ->orderBy('faculty_id')
            ->paginate(20);

        $years = range(date('Y') - 5, date('Y') + 1);
        $semesters = ['1st Semester', '2nd Semester', 'Summer'];
        $evaluationPeriods = Evaluation::getEvaluationPeriods();

        return view('admin.evaluation.by_period', compact(
            'evaluations', 'years', 'semesters', 'evaluationPeriods',
            'academicYear', 'semester', 'period'
        ));
    }

    public function ratingsReport()
    {
        $evaluations = Evaluation::published()
            ->with('faculty')
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester')
            ->orderBy('overall_rating', 'desc')
            ->get();

        $ratingStats = [
            'outstanding' => $evaluations->where('overall_rating', '>=', 4.5)->count(),
            'very_satisfactory' => $evaluations->whereBetween('overall_rating', [4.0, 4.49])->count(),
            'satisfactory' => $evaluations->whereBetween('overall_rating', [3.5, 3.99])->count(),
            'fair' => $evaluations->whereBetween('overall_rating', [3.0, 3.49])->count(),
            'needs_improvement' => $evaluations->where('overall_rating', '<', 3.0)->count(),
        ];

        return view('admin.evaluation.ratings_report', compact('evaluations', 'ratingStats'));
    }

    public function facultyRatingSummary()
    {
        $faculties = Faculty::with(['evaluations' => function($query) {
            $query->published()->orderBy('academic_year', 'desc');
        }])->where('status', 'active')->get();

        $faculties = $faculties->map(function($faculty) {
            $faculty->average_rating = $faculty->getOverallRatingAverage();
            $faculty->recent_evaluations = $faculty->recentEvaluations();
            return $faculty;
        })->sortByDesc('average_rating');

        return view('admin.evaluation.faculty_summary', compact('faculties'));
    }
}

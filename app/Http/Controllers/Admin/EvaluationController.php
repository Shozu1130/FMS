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
        $query = Evaluation::with(['faculty', 'teachingHistory']);
        
        // Filter by department if not master admin
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $query->whereHas('faculty', function($q) {
                $q->where('department', auth()->user()->department);
            });
        }
        
        $evaluations = $query->orderBy('academic_year', 'desc')
            ->orderBy('semester')
            ->orderBy('professor_id')
            ->paginate(20);

        // Filter faculties by department
        $facultiesQuery = Faculty::where('status', 'active')->orderBy('name');
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultiesQuery->where('department', auth()->user()->department);
        }
        $faculties = $facultiesQuery->get();

        return view('admin.evaluation.index', compact('evaluations', 'faculties'));
    }

    public function create()
    {
        // Filter faculties by department
        $facultiesQuery = Faculty::where('status', 'active')->orderBy('name');
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultiesQuery->where('department', auth()->user()->department);
        }
        $faculties = $facultiesQuery->get();
        
        // Filter teaching histories by department
        $teachingHistoriesQuery = TeachingHistory::active()->with('faculty');
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $teachingHistoriesQuery->whereHas('faculty', function($q) {
                $q->where('department', auth()->user()->department);
            });
        }
        $teachingHistories = $teachingHistoriesQuery->get();
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
        // Filter faculties by department
        $facultiesQuery = Faculty::where('status', 'active')->orderBy('name');
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultiesQuery->where('department', auth()->user()->department);
        }
        $faculties = $facultiesQuery->get();
        
        // Filter teaching histories by department
        $teachingHistoriesQuery = TeachingHistory::active()->with('faculty');
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $teachingHistoriesQuery->whereHas('faculty', function($q) {
                $q->where('department', auth()->user()->department);
            });
        }
        $teachingHistories = $teachingHistoriesQuery->get();
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
        $evaluationsQuery = Evaluation::with(['faculty', 'teachingHistory'])
            ->published()
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester');
            
        // Filter by department
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $evaluationsQuery->whereHas('faculty', function($q) {
                $q->where('department', auth()->user()->department);
            });
        }
        
        $evaluations = $evaluationsQuery->paginate(20);

        return view('admin.evaluation.published', compact('evaluations'));
    }

    public function byPeriod(Request $request)
    {
        $academicYear = $request->get('academic_year', date('Y'));
        $semester = $request->get('semester', '1st Semester');
        $period = $request->get('evaluation_period');

        $evaluationsQuery = Evaluation::with(['faculty', 'teachingHistory'])
            ->byPeriod($academicYear, $semester, $period)
            ->orderBy('professor_id');
            
        // Filter by department
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $evaluationsQuery->whereHas('faculty', function($q) {
                $q->where('department', auth()->user()->department);
            });
        }
        
        $evaluations = $evaluationsQuery->paginate(20);

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
        $evaluationsQuery = Evaluation::published()
            ->with('faculty')
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester')
            ->orderBy('overall_rating', 'desc');
            
        // Filter by department
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $evaluationsQuery->whereHas('faculty', function($q) {
                $q->where('department', auth()->user()->department);
            });
        }
        
        $evaluations = $evaluationsQuery->get();

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
        $facultiesQuery = Faculty::with(['evaluations' => function($query) {
            $query->published()->orderBy('academic_year', 'desc');
        }])->where('status', 'active');
        
        // Filter by department
        if (!auth()->user()->isMasterAdmin() && auth()->user()->department) {
            $facultiesQuery->where('department', auth()->user()->department);
        }
        
        $faculties = $facultiesQuery->get();

        $faculties = $faculties->map(function($faculty) {
            $faculty->average_rating = $faculty->getOverallRatingAverage();
            $faculty->recent_evaluations = $faculty->recentEvaluations();
            return $faculty;
        })->sortByDesc('average_rating');

        return view('admin.evaluation.faculty_summary', compact('faculties'));
    }

    /**
     * Show detailed evaluation form for a specific faculty
     */
    public function createForFaculty(Faculty $faculty)
    {
        $academicYears = [];
        for ($y = (int)date('Y'); $y >= 2020; $y--) {
            $academicYears[] = sprintf('%d-%d', $y, $y + 1);
        }

        $semesters = ['1st Semester', '2nd Semester', 'Summer'];

        return view('admin.evaluation.create_for_faculty', [
            'faculty' => $faculty,
            'academicYears' => $academicYears,
            'semesters' => $semesters,
        ]);
    }

    /**
     * Store detailed evaluation for a faculty
     */
    public function storeForFaculty(Request $request, Faculty $faculty)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string',
            'semester' => 'required|in:1st Semester,2nd Semester,Summer',
            'evaluation_period' => 'required|string',
            'group_A' => 'required|array',
            'group_B' => 'required|array',
            'group_C' => 'required|array',
            'group_D' => 'required|array',
            'group_E' => 'required|array',
            'group_F' => 'required|array',
            'group_G' => 'required|array',
            'group_H' => 'required|array',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
        ]);

        $avg = function(array $vals): float {
            $filtered = array_map('floatval', $vals);
            return count($filtered) ? array_sum($filtered) / count($filtered) : 0.0;
        };

        $subjectMatter = $avg($validated['group_A']);
        $motivation = $avg($validated['group_B']);
        $classroom = $avg($validated['group_C']);
        $punctuality = $avg($validated['group_D']);
        $communication = $avg($validated['group_E']);
        $personality = $avg($validated['group_F']);
        $humanRelations = $avg($validated['group_G']);
        $fairness = $avg($validated['group_H']);

        $teachingEffectiveness = $avg([$motivation, $personality]);
        $studentEngagement = $avg([$punctuality, $humanRelations]);
        $classroomComposite = $avg([$classroom, $fairness]);

        $overall = $avg([
            $teachingEffectiveness,
            $subjectMatter,
            $classroomComposite,
            $communication,
            $studentEngagement,
        ]);

        Evaluation::create([
            'professor_id' => $faculty->id,
            'evaluation_period' => $validated['evaluation_period'],
            'academic_year' => $validated['academic_year'],
            'semester' => $validated['semester'],
            'teaching_effectiveness' => $teachingEffectiveness,
            'subject_matter_knowledge' => $subjectMatter,
            'classroom_management' => $classroomComposite,
            'communication_skills' => $communication,
            'student_engagement' => $studentEngagement,
            'strengths' => $validated['strengths'] ?? null,
            'areas_for_improvement' => $validated['areas_for_improvement'] ?? null,
            'overall_rating' => $overall,
            'is_published' => false,
        ]);

        return redirect()->route('admin.evaluation.faculty_summary')
            ->with('success', 'Evaluation saved for ' . $faculty->name);
    }

    public function storeFromModal(Request $request)
    {
        $validated = $request->validate([
            'professor_id' => 'required|exists:faculties,id',
            'academic_year' => 'required|string',
            'evaluation_period' => 'required|string',
            'teaching_effectiveness' => 'required|numeric|min:1|max:5',
            'research_scholarship' => 'required|numeric|min:1|max:5',
            'service_engagement' => 'required|numeric|min:1|max:5',
            'professional_development' => 'required|numeric|min:1|max:5',
            'student_advising' => 'required|numeric|min:1|max:5',
            'feedback' => 'nullable|string',
            'overall_rating' => 'required|numeric|min:1|max:5'
        ]);

        // Calculate overall rating if not provided
        if (!isset($validated['overall_rating'])) {
            $ratings = [
                $validated['teaching_effectiveness'],
                $validated['research_scholarship'],
                $validated['service_engagement'],
                $validated['professional_development'],
                $validated['student_advising']
            ];
            $validated['overall_rating'] = array_sum($ratings) / count($ratings);
        }

        $evaluation = Evaluation::create($validated);

        return redirect()->route('admin.evaluation.faculty_summary')
            ->with('success', 'Evaluation submitted successfully for ' . $evaluation->faculty->name);
    }
}

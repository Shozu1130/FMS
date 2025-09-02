<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    public function index()
    {
        $professor = Auth::guard('faculty')->user();
        
        $evaluations = Evaluation::where('faculty_id', $professor->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $averageRating = $evaluations->avg('overall_rating') ?? 0;
        $latestEvaluation = $evaluations->first();
        
        // Determine rating category
        $ratingCategory = $this->getRatingCategory($averageRating);
        $ratingCategoryColor = $this->getRatingCategoryColor($averageRating);

        return view('professor.evaluation.index', compact(
            'evaluations',
            'averageRating',
            'latestEvaluation',
            'ratingCategory',
            'ratingCategoryColor'
        ));
    }

    private function getRatingCategory($rating)
    {
        if ($rating >= 4.5) return 'Outstanding';
        if ($rating >= 4.0) return 'Very Satisfactory';
        if ($rating >= 3.5) return 'Satisfactory';
        if ($rating >= 3.0) return 'Fair';
        return 'Needs Improvement';
    }

    private function getRatingCategoryColor($rating)
    {
        if ($rating >= 4.5) return 'success';
        if ($rating >= 4.0) return 'info';
        if ($rating >= 3.5) return 'warning';
        if ($rating >= 3.0) return 'secondary';
        return 'danger';
    }
}

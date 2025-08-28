<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'faculty_id',
        'teaching_history_id',
        'evaluation_period',
        'academic_year',
        'semester',
        'teaching_effectiveness',
        'subject_matter_knowledge',
        'classroom_management',
        'communication_skills',
        'student_engagement',
        'overall_rating',
        'strengths',
        'areas_for_improvement',
        'recommendations',
        'is_published',
    ];

    protected $casts = [
        'academic_year' => 'integer',
        'teaching_effectiveness' => 'decimal:2',
        'subject_matter_knowledge' => 'decimal:2',
        'classroom_management' => 'decimal:2',
        'communication_skills' => 'decimal:2',
        'student_engagement' => 'decimal:2',
        'overall_rating' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    protected $appends = [
        'rating_category',
        'formatted_overall_rating',
        'evaluation_period_full',
    ];

    /**
     * Get the faculty that owns the evaluation.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the teaching history that owns the evaluation.
     */
    public function teachingHistory(): BelongsTo
    {
        return $this->belongsTo(TeachingHistory::class);
    }

    /**
     * Scope a query to only include published evaluations.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope a query to only include evaluations for a specific period.
     */
    public function scopeByPeriod($query, $academicYear, $semester, $evaluationPeriod = null)
    {
        $query = $query->where('academic_year', $academicYear)
                      ->where('semester', $semester);

        if ($evaluationPeriod) {
            $query->where('evaluation_period', $evaluationPeriod);
        }

        return $query;
    }

    /**
     * Scope a query to only include evaluations with rating above threshold.
     */
    public function scopeAboveRating($query, $threshold = 3.0)
    {
        return $query->where('overall_rating', '>=', $threshold);
    }

    /**
     * Scope a query to only include evaluations with rating below threshold.
     */
    public function scopeBelowRating($query, $threshold = 3.0)
    {
        return $query->where('overall_rating', '<', $threshold);
    }

    /**
     * Get the rating category.
     */
    public function getRatingCategoryAttribute()
    {
        if ($this->overall_rating >= 4.5) {
            return 'Outstanding';
        } elseif ($this->overall_rating >= 4.0) {
            return 'Very Satisfactory';
        } elseif ($this->overall_rating >= 3.5) {
            return 'Satisfactory';
        } elseif ($this->overall_rating >= 3.0) {
            return 'Fair';
        } else {
            return 'Needs Improvement';
        }
    }

    /**
     * Get formatted overall rating.
     */
    public function getFormattedOverallRatingAttribute()
    {
        return number_format($this->overall_rating, 2);
    }

    /**
     * Get full evaluation period description.
     */
    public function getEvaluationPeriodFullAttribute()
    {
        $periods = [
            'midterm' => 'Midterm Evaluation',
            'final' => 'Final Evaluation',
            'annual' => 'Annual Evaluation',
        ];

        return $periods[$this->evaluation_period] ?? ucfirst($this->evaluation_period);
    }

    /**
     * Calculate overall rating from component scores.
     */
    public function calculateOverallRating()
    {
        $components = [
            $this->teaching_effectiveness,
            $this->subject_matter_knowledge,
            $this->classroom_management,
            $this->communication_skills,
            $this->student_engagement,
        ];

        $validComponents = array_filter($components, function($score) {
            return $score > 0;
        });

        if (count($validComponents) > 0) {
            $this->overall_rating = array_sum($validComponents) / count($validComponents);
        }

        return $this;
    }

    /**
     * Get evaluation periods.
     */
    public static function getEvaluationPeriods()
    {
        return [
            'midterm' => 'Midterm Evaluation',
            'final' => 'Final Evaluation',
            'annual' => 'Annual Evaluation',
        ];
    }

    /**
     * Get rating scale description.
     */
    public static function getRatingScale()
    {
        return [
            '5.00' => 'Outstanding',
            '4.00-4.99' => 'Very Satisfactory',
            '3.00-3.99' => 'Satisfactory',
            '2.00-2.99' => 'Fair',
            '1.00-1.99' => 'Needs Improvement',
        ];
    }

    /**
     * Validation rules for creating/updating evaluations.
     */
    public static function rules($id = null)
    {
        return [
            'faculty_id' => 'required|exists:faculty,id',
            'teaching_history_id' => 'nullable|exists:teaching_histories,id',
            'evaluation_period' => 'required|in:midterm,final,annual',
            'academic_year' => 'required|integer|min:2000|max:2100',
            'semester' => 'required|in:1st Semester,2nd Semester,Summer',
            'teaching_effectiveness' => 'required|numeric|min:1|max:5',
            'subject_matter_knowledge' => 'required|numeric|min:1|max:5',
            'classroom_management' => 'required|numeric|min:1|max:5',
            'communication_skills' => 'required|numeric|min:1|max:5',
            'student_engagement' => 'required|numeric|min:1|max:5',
            'strengths' => 'nullable|string|max:1000',
            'areas_for_improvement' => 'nullable|string|max:1000',
            'recommendations' => 'nullable|string|max:1000',
            'is_published' => 'nullable|boolean',
        ];
    }

    /**
     * Automatically calculate overall rating before saving.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($evaluation) {
            $evaluation->calculateOverallRating();
        });
    }
}

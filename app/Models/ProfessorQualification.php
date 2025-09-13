<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessorQualification extends Model
{
    use HasFactory;

    protected $fillable = [
        'professor_id',
        'type',
        'title',
        'institution_company',
        'description',
        'start_date',
        'end_date',
        'is_current',
        'location',
        'level'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean'
    ];

    public const TYPES = [
        'education' => 'Education',
        'experience' => 'Work Experience',
        'skill' => 'Skill',
        'certification' => 'Certification',
        'award' => 'Award'
    ];

    public const LEVELS = [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        'expert' => 'Expert'
    ];

    /**
     * Get the professor that owns the qualification.
     */
    public function professor(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'professor_id');
    }

    /**
     * Get the formatted type name.
     */
    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Get the formatted level name.
     */
    public function getLevelNameAttribute(): ?string
    {
        return $this->level ? (self::LEVELS[$this->level] ?? $this->level) : null;
    }

    /**
     * Get the duration of the qualification.
     */
    public function getDurationAttribute(): string
    {
        if (!$this->start_date) {
            return 'N/A';
        }

        $start = $this->start_date->format('M Y');
        
        if ($this->is_current) {
            return $start . ' - Present';
        }
        
        if ($this->end_date) {
            return $start . ' - ' . $this->end_date->format('M Y');
        }
        
        return $start;
    }

    /**
     * Scope to filter by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get current qualifications.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }
}

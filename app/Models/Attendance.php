<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'faculty_id',
        'date',
        'time_in',
        'time_out',
        'time_in_photo',
        'time_out_photo',
        'time_in_location',
        'time_out_location',
        'total_hours',
        'status', // present, absent, late, etc.
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'total_hours' => 'decimal:2',
    ];

    protected $appends = [
        'formatted_time_in',
        'formatted_time_out',
        'formatted_total_hours',
        'status_badge',
        'is_late',
        'is_early_departure'
    ];

    /**
     * Get the faculty member that owns the attendance record.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get formatted time in.
     */
    public function getFormattedTimeInAttribute()
    {
        return $this->time_in ? $this->time_in->format('h:i A') : 'Not logged in';
    }

    /**
     * Get formatted time out.
     */
    public function getFormattedTimeOutAttribute()
    {
        return $this->time_out ? $this->time_out->format('h:i A') : 'Not logged out';
    }

    /**
     * Get formatted total hours.
     */
    public function getFormattedTotalHoursAttribute()
    {
        return $this->total_hours ? number_format($this->total_hours, 2) . ' hrs' : '0.00 hrs';
    }

    /**
     * Get status badge with color.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'present' => '<span class="badge bg-success">Present</span>',
            'absent' => '<span class="badge bg-danger">Absent</span>',
            'late' => '<span class="badge bg-warning">Late</span>',
            'early_departure' => '<span class="badge bg-info">Early Departure</span>',
            'half_day' => '<span class="badge bg-secondary">Half Day</span>'
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Check if faculty is late (after 8:00 AM).
     */
    public function getIsLateAttribute()
    {
        if (!$this->time_in) return false;

        $expectedTime = \Carbon\Carbon::parse($this->date)->setTime(8, 0, 0);
        return $this->time_in->gt($expectedTime);
    }

    /**
     * Check if faculty left early (before 5:00 PM).
     */
    public function getIsEarlyDepartureAttribute()
    {
        if (!$this->time_out) return false;

        $expectedTime = \Carbon\Carbon::parse($this->date)->setTime(17, 0, 0);
        return $this->time_out->lt($expectedTime);
    }

    /**
     * Calculate total hours worked.
     */
    public function calculateTotalHours()
    {
        if ($this->time_in && $this->time_out) {
            $this->total_hours = $this->time_in->diffInMinutes($this->time_out) / 60;
            $this->save();
        }
        return $this->total_hours;
    }

    /**
     * Scope to get attendance for a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope to get attendance for a specific faculty.
     */
    public function scopeByFaculty($query, $facultyId)
    {
        return $query->where('faculty_id', $facultyId);
    }

    /**
     * Scope to get attendance for current month.
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereYear('date', now()->year)
                     ->whereMonth('date', now()->month);
    }
}

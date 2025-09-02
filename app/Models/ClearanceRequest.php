<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClearanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'faculty_id',
        'clearance_type',
        'reason',
        'status',
        'admin_remarks',
        'requested_at',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the faculty that owns the clearance request.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the admin who processed the request.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include rejected requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope a query by clearance type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('clearance_type', $type);
    }

    /**
     * Get available clearance types for requests.
     */
    public static function getClearanceTypes()
    {
        return [
            'faculty_clearance_form' => 'Faculty Clearance Form',
            'grade_submission_confirmation' => 'Grade Submission Confirmation Clearance',
            'library_clearance_slip' => 'Library Clearance Slip',
            'property_acknowledgment_receipt' => 'Property Acknowledgment Receipt',
            'certification_no_pending_obligation' => 'Certification of No Pending Obligation',
            'final_teaching_report' => 'Final Teaching Report',
            'leave_clearance' => 'Leave Clearance',
            'faculty_evaluation_summary' => 'Faculty Evaluation Summary',
        ];
    }

    /**
     * Get status badge class for display.
     */
    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return 'badge-warning';
            case self::STATUS_APPROVED:
                return 'badge-success';
            case self::STATUS_REJECTED:
                return 'badge-danger';
            default:
                return 'badge-secondary';
        }
    }

    /**
     * Get formatted clearance type name.
     */
    public function getClearanceTypeNameAttribute()
    {
        $types = self::getClearanceTypes();
        return $types[$this->clearance_type] ?? $this->clearance_type;
    }

    /**
     * Check if request is pending.
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if request is approved.
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if request is rejected.
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Validation rules for creating/updating clearance requests.
     */
    public static function rules($id = null)
    {
        return [
            'faculty_id' => 'required|exists:faculty,id',
            'clearance_type' => 'required|string|in:' . implode(',', array_keys(self::getClearanceTypes())),
            'reason' => 'required|string|max:1000',
            'status' => 'nullable|string|in:' . self::STATUS_PENDING . ',' . self::STATUS_APPROVED . ',' . self::STATUS_REJECTED,
            'admin_remarks' => 'nullable|string|max:500',
        ];
    }

    /**
     * Boot method to set default values.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            if (!$request->status) {
                $request->status = self::STATUS_PENDING;
            }
            if (!$request->requested_at) {
                $request->requested_at = now();
            }
        });
    }
}

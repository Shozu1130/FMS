<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Clearance extends Model
{
    use HasFactory;

    protected $fillable = [
        'professor_id',
        'clearance_type',
        'issued_date',
        'expiration_date',
        'is_cleared',
        'remarks',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'expiration_date' => 'date',
        'is_cleared' => 'boolean',
    ];

    protected $appends = [
        'status',
        'days_until_expiration',
        'is_expired',
        'is_valid',
    ];

    /**
     * Get the faculty that owns the clearance.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Scope a query to only include cleared clearances.
     */
    public function scopeCleared($query)
    {
        return $query->where('is_cleared', true);
    }

    /**
     * Scope a query to only include pending clearances.
     */
    public function scopePending($query)
    {
        return $query->where('is_cleared', false);
    }

    /**
     * Scope a query to only include valid (not expired) clearances.
     */
    public function scopeValid($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expiration_date')
              ->orWhere('expiration_date', '>', now());
        });
    }

    /**
     * Scope a query to only include expired clearances.
     */
    public function scopeExpired($query)
    {
        return $query->where('expiration_date', '<=', now());
    }

    /**
     * Scope a query by clearance type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('clearance_type', $type);
    }

    /**
     * Get the clearance status.
     */
    public function getStatusAttribute()
    {
        if (!$this->is_cleared) {
            return 'Pending';
        }

        if ($this->is_expired) {
            return 'Expired';
        }

        return 'Valid';
    }

    /**
     * Check if clearance is expired.
     */
    public function getIsExpiredAttribute()
    {
        if (!$this->expiration_date) {
            return false;
        }

        return $this->expiration_date->isPast();
    }

    /**
     * Check if clearance is valid (cleared and not expired).
     */
    public function getIsValidAttribute()
    {
        return $this->is_cleared && !$this->is_expired;
    }

    /**
     * Get days until expiration.
     */
    public function getDaysUntilExpirationAttribute()
    {
        if (!$this->expiration_date) {
            return null;
        }

        return now()->diffInDays($this->expiration_date, false);
    }

    /**
     * Get clearance types.
     */
    public static function getClearanceTypes()
    {
        return [
            'Academic Clearance',
            'Library Clearance',
            'Accounting Clearance',
            'Registrar Clearance',
            'Property Clearance',
            'Research Clearance',
            'Extension Clearance',
            'ICT Clearance',
            'Security Clearance',
            'Medical Clearance',
        ];
    }

    /**
     * Validation rules for creating/updating clearances.
     */
    public static function rules($id = null)
    {
        return [
            'professor_id' => 'required|exists:faculties,id',
            'clearance_type' => 'required|string|max:100',
            'issued_date' => 'required|date',
            'expiration_date' => 'nullable|date|after_or_equal:issued_date',
            'is_cleared' => 'nullable|boolean',
            'remarks' => 'nullable|string|max:500',
        ];
    }

    /**
     * Automatically set expiration date if not provided for certain types.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($clearance) {
            if (!$clearance->expiration_date && in_array($clearance->clearance_type, [
                'Academic Clearance', 
                'Library Clearance',
                'Accounting Clearance'
            ])) {
                $clearance->expiration_date = Carbon::now()->addYear();
            }
        });
    }
}

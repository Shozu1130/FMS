<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'faculty_id',
        'type',
        'reason',
        'start_date',
        'end_date',
        'status',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public static function types(): array
    {
        return [
            'sick' => 'Sick Leave – illness',
            'vacation' => 'Vacation Leave – personal time',
            'maternity' => 'Maternity',
            'emergency' => 'Emergency Leave – personal emergency',
            'bereavement' => 'Bereavement Leave – family death',
            'study' => 'Study Leave – masteral/professional degree',
        ];
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }
}




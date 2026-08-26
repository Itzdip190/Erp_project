<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class StudentGatePass extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'student_gate_passes';

    protected $fillable = [
        'school_id',
        'student_id',
        'academic_session_id',
        'gate_pass_number',
        'pass_date',
        'template',
        'reason',
        'guardian_type',
        'guardian_name',
        'guardian_relation',
        'guardian_phone',
        'expected_return_time',
        'actual_return_time',
        'status',
        'remarks',
        'approved_by',
        'issued_by',
        'school_name',
        'school_logo',
        'school_address',
        'school_city',
        'school_state',
        'school_pincode',
        'school_phone',
        'school_email',
        'student_snapshot',
        'meta_data',
    ];

    protected $casts = [
        'pass_date'            => 'datetime',
        'actual_return_time'   => 'datetime',
        'student_snapshot'     => 'array',
        'meta_data'            => 'array',
    ];

    /**
     * Relationship to Student.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship to School.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relationship to AcademicSession.
     */
    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Relationship to User (Issued by).
     */
    public function issuedByUser()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Generate next atomic sequential gate pass number for school.
     * Example: 2026/GP/1 or GP/2026/000001
     */
    public static function generateNextNumber(int $schoolId, string $format = 'yearly_seq'): string
    {
        $year = date('Y');
        
        // Find latest pass for this school and current year
        $countThisYear = static::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereYear('pass_date', $year)
            ->count();

        $nextSeq = $countThisYear + 1;

        if ($format === 'padded') {
            return sprintf('GP/%s/%06d', $year, $nextSeq);
        }

        // Default format matching Image 2 & 5: 2026/GP/1
        return sprintf('%s/GP/%d', $year, $nextSeq);
    }

    /**
     * Status color helper.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft'     => '#6b7280',
            'generated' => '#3b82f6',
            'issued'    => '#10b981',
            'returned'  => '#059669',
            'cancelled' => '#ef4444',
            default     => '#3b82f6',
        };
    }

    /**
     * HTML Badge for status.
     */
    public function getStatusBadgeAttribute(): string
    {
        $labels = [
            'draft'     => 'Draft',
            'generated' => 'Generated',
            'issued'    => 'Issued / Out',
            'returned'  => 'Returned',
            'cancelled' => 'Cancelled',
        ];

        $colors = [
            'draft'     => ['bg' => 'rgba(107, 114, 128, 0.12)', 'text' => '#6b7280', 'border' => 'rgba(107, 114, 128, 0.25)'],
            'generated' => ['bg' => 'rgba(59, 130, 246, 0.12)', 'text' => '#2563eb', 'border' => 'rgba(59, 130, 246, 0.25)'],
            'issued'    => ['bg' => 'rgba(16, 185, 129, 0.15)', 'text' => '#059669', 'border' => 'rgba(16, 185, 129, 0.3)'],
            'returned'  => ['bg' => 'rgba(14, 165, 233, 0.15)', 'text' => '#0284c7', 'border' => 'rgba(14, 165, 233, 0.3)'],
            'cancelled' => ['bg' => 'rgba(239, 68, 68, 0.12)', 'text' => '#dc2626', 'border' => 'rgba(239, 68, 68, 0.25)'],
        ];

        $status = $this->status ?? 'issued';
        $style = $colors[$status] ?? $colors['issued'];
        $label = $labels[$status] ?? ucfirst($status);

        return sprintf(
            '<span class="badge" style="background-color: %s; color: %s; border: 1px solid %s; padding: 4px 10px; border-radius: 20px; font-weight: 700; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa fa-circle" style="font-size: 7px;"></i> %s</span>',
            $style['bg'],
            $style['text'],
            $style['border'],
            $label
        );
    }
}

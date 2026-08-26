<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class StaffGatePass extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'staff_gate_passes';

    protected $fillable = [
        'school_id',
        'staff_id',
        'academic_session_id',
        'gate_pass_number',
        'pass_date',
        'template',
        'reason',
        'expected_return_time',
        'actual_return_time',
        'status',
        'remarks',
        'cancellation_reason',
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
        'staff_snapshot',
        'meta_data',
    ];

    protected $casts = [
        'pass_date' => 'datetime',
        'actual_return_time' => 'datetime',
        'staff_snapshot' => 'array',
        'meta_data' => 'array',
    ];

    protected $appends = [
        'status_badge',
    ];

    /**
     * Relationship with School.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relationship with Staff.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    /**
     * Relationship with Academic Session.
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Relationship with Issuer User.
     */
    public function issuedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Atomically generate next sequential Staff Gate Pass number for a school.
     * Format: YYYY/SGP/{seq} (e.g. 2026/SGP/1)
     */
    public static function generateNextNumber(int $schoolId): string
    {
        $year = date('Y');
        $prefix = "{$year}/SGP/";

        return DB::transaction(function () use ($schoolId, $prefix, $year) {
            $latestPass = self::where('school_id', $schoolId)
                ->where('gate_pass_number', 'LIKE', "{$prefix}%")
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            $nextSeq = 1;
            if ($latestPass && preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $latestPass->gate_pass_number, $matches)) {
                $nextSeq = (int)$matches[1] + 1;
            }

            return "{$prefix}{$nextSeq}";
        });
    }

    /**
     * HTML Badge for Status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'issued' => '<span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; padding:4px 8px; border-radius:6px; font-weight:600; font-size:11.5px;"><i class="fa fa-walking"></i> Issued / Out</span>',
            'returned' => '<span class="badge" style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; padding:4px 8px; border-radius:6px; font-weight:600; font-size:11.5px;"><i class="fa fa-check-circle"></i> Returned</span>',
            'cancelled' => '<span class="badge" style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca; padding:4px 8px; border-radius:6px; font-weight:600; font-size:11.5px;"><i class="fa fa-ban"></i> Cancelled</span>',
            default => '<span class="badge" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; padding:4px 8px; border-radius:6px; font-weight:600; font-size:11.5px;">' . ucfirst($this->status) . '</span>',
        };
    }
}

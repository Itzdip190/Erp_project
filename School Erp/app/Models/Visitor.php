<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Visitor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'visitors';

    protected $fillable = [
        'school_id',
        'academic_session_id',
        'registered_by',
        'pass_number',
        'visitor_type',
        'full_name',
        'gender',
        'dob',
        'mobile_number',
        'alternate_mobile',
        'email',
        'street_address',
        'state',
        'city',
        'pincode',
        'whom_to_meet_type',
        'host_name',
        'security_gate',
        'entourage_count',
        'visit_purpose',
        'detailed_purpose_remarks',
        'id_proof_type',
        'id_proof_number',
        'vehicle_number',
        'photo_path',
        'security_notes',
        'status',
        'is_self_registered',
        'rejection_reason',
        'approved_at',
        'approved_by',
        'check_in_at',
        'check_out_at',
        'meta_data',
    ];

    protected $casts = [
        'dob' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_self_registered' => 'boolean',
        'meta_data' => 'array',
        'entourage_count' => 'integer',
    ];

    /**
     * Scope for pending visitor self-registration requests.
     */
    public function scopePendingRequests($query, ?int $schoolId = null)
    {
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }
        return $query->where('status', 'pending');
    }

    /**
     * Get user who approved this request.
     */
    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the school that owns the visitor record.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the academic session associated.
     */
    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the user who registered this visitor.
     */
    public function registeredByUser()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    /**
     * Get formatted photo URL or SVG fallback.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo_path) {
            if (str_starts_with($this->photo_path, 'data:') || str_starts_with($this->photo_path, 'http')) {
                return $this->photo_path;
            }
            if (Storage::disk('public')->exists($this->photo_path)) {
                return Storage::disk('public')->url($this->photo_path);
            }
            return asset('storage/' . ltrim($this->photo_path, '/'));
        }
        return null;
    }

    /**
     * Generate unique sequential pass number matching PASS-{CODE}-{0001} format.
     */
    public static function generatePassNumber(int $schoolId): string
    {
        $school = School::find($schoolId);
        $rawCode = $school?->code ?: 'EDUZEN';
        $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $rawCode));
        if (empty($code)) {
            $code = 'EDUZEN';
        }
        $prefix = "PASS-{$code}";

        $lastVisitor = self::where('school_id', $schoolId)
            ->where(function($q) use ($prefix) {
                $q->where('pass_number', 'LIKE', "{$prefix}-%")
                  ->orWhere('pass_number', 'LIKE', "PASS-%")
                  ->orWhere('pass_number', 'LIKE', "VIS-%");
            })
            ->orderBy('id', 'desc')
            ->first();

        $nextSeq = 1;
        if ($lastVisitor && preg_match('/-(\d+)$/', $lastVisitor->pass_number, $matches)) {
            $nextSeq = ((int) $matches[1]) + 1;
        }

        return sprintf('%s-%04d', $prefix, $nextSeq);
    }
}

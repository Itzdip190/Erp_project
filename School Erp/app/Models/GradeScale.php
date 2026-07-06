<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeScale extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'academic_session_id',
        'name',
        'scale_basis',
        'type',
        'applicable_classes',
        'ranges',
    ];

    protected $casts = [
        'applicable_classes' => 'array',
        'ranges' => 'array',
    ];

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public static function getGradeScaleType($subjectType)
    {
        $type = strtolower($subjectType);
        if ($type === 'non scholastic' || $type === 'non_scholastic') {
            return 'non_scholastic';
        }
        if ($type === 'custom' || $type === 'custom_subject' || $type === 'custom subject') {
            return 'custom_subject';
        }
        return 'scholastic';
    }

    public static function getGradeForPercentage($schoolId, $classId, $pct, $type = 'scholastic', $scaleBasis = 'subject')
    {
        $scale = self::where('school_id', $schoolId)
            ->where('scale_basis', $scaleBasis)
            ->where('type', $type)
            ->get()
            ->first(function($item) use ($classId) {
                return is_array($item->applicable_classes) && in_array((string)$classId, array_map('strval', $item->applicable_classes));
            });

        if (!$scale) {
            $scale = self::where('school_id', $schoolId)
                ->where('scale_basis', $scaleBasis)
                ->where('type', 'scholastic')
                ->get()
                ->first(function($item) use ($classId) {
                    return is_array($item->applicable_classes) && in_array((string)$classId, array_map('strval', $item->applicable_classes));
                });
        }

        if (!$scale || empty($scale->ranges)) {
            // Fallback to default grading system
            if ($pct >= 90) return 'A+';
            if ($pct >= 80) return 'A';
            if ($pct >= 70) return 'B';
            if ($pct >= 60) return 'C';
            if ($pct >= 50) return 'D';
            return 'F';
        }

        $matchedGrade = null;
        foreach ($scale->ranges as $range) {
            $from = (float)($range['from'] ?? 0);
            $to = (float)($range['to'] ?? 100);

            if ($from === 0.0 && $pct >= 0.0 && $pct <= $to) {
                $matchedGrade = $range['grade_value'] ?? 'F';
            } elseif ($pct > $from && $pct <= $to) {
                $matchedGrade = $range['grade_value'] ?? 'F';
            }
        }

        return $matchedGrade ?? 'F';
    }
}

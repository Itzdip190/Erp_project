<?php

namespace App\Services;

use App\Models\School;
use App\Models\Student;
use App\Models\StudentSession;
use Illuminate\Support\Facades\DB;

class StudentNumberService
{
    public function generateAdmissionNumber(int $schoolId): string
    {
        $school = School::findOrFail($schoolId);
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $school->name), 0, 3));

        return DB::transaction(function () use ($schoolId, $prefix) {
            $currentYear = (int) date('Y');
            
            $last = Student::where('school_id', $schoolId)
                ->where('admission_year', $currentYear)
                ->lockForUpdate() // exclusive lock to prevent concurrent duplicates
                ->max('admission_sequence') ?? 0;
                
            $next = $last + 1;
            
            // Format example: YIS/2026/00042
            return sprintf('%s/%d/%05d', $prefix, $currentYear, $next);
        });
    }

    public function generateRollNumber(int $sectionId, int $sessionId): int
    {
        $max = StudentSession::where('section_id', $sectionId)
            ->where('academic_session_id', $sessionId)
            ->max('roll_number');
            
        return $max ? ((int) $max + 1) : 1;
    }
}

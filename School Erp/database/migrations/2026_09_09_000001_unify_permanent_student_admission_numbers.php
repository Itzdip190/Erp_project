<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');

        $schools = DB::table('schools')->pluck('id')->toArray();

        // Helper to safely repoint records from duplicate student to primary student without duplicate key collisions
        $repointTable = function (string $table, string $foreignKey, int $fromId, int $toId, array $uniqueCols = []) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, $foreignKey)) {
                return;
            }

            $records = DB::table($table)->where($foreignKey, $fromId)->get();
            foreach ($records as $row) {
                $hasConflict = false;
                if (!empty($uniqueCols)) {
                    $conflictQuery = DB::table($table)->where($foreignKey, $toId);
                    $allColsPresent = true;
                    foreach ($uniqueCols as $uCol) {
                        if (property_exists($row, $uCol)) {
                            $conflictQuery->where($uCol, $row->$uCol);
                        } else {
                            $allColsPresent = false;
                        }
                    }
                    if ($allColsPresent && $conflictQuery->exists()) {
                        $hasConflict = true;
                    }
                }

                if ($hasConflict) {
                    DB::table($table)->where('id', $row->id)->delete();
                } else {
                    try {
                        DB::table($table)->where('id', $row->id)->update([$foreignKey => $toId]);
                    } catch (\Throwable $e) {
                        // If any unique constraint or constraint violation occurs, delete the duplicate row
                        DB::table($table)->where('id', $row->id)->delete();
                    }
                }
            }
        };

        foreach ($schools as $schoolId) {
            // Get all academic sessions ordered chronologically
            $sessions = DB::table('academic_sessions')
                ->where('school_id', $schoolId)
                ->orderBy('start_date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $sessionOrderMap = [];
            $orderIndex = 0;
            foreach ($sessions as $s) {
                $sessionOrderMap[$s->id] = $orderIndex++;
            }

            // Fetch all students for this school
            $students = DB::table('students')
                ->where('school_id', $schoolId)
                ->get();

            if ($students->isEmpty()) {
                continue;
            }

            // Group students by reliable identity: (Full Name + Father/Guardian Name + DOB)
            $groupedByIdentity = [];

            foreach ($students as $st) {
                $rawFullName = trim(($st->first_name ?? '') . ' ' . ($st->last_name ?? ''));
                $cleanFullName = strtolower(preg_replace('/[^a-z0-9]/', '', $rawFullName));

                $rawFather = trim((string)($st->father_name ?? ($st->guardian_name ?? '')));
                $cleanFather = strtolower(preg_replace('/[^a-z0-9]/', '', $rawFather));

                $dob = !empty($st->date_of_birth) ? date('Y-m-d', strtotime($st->date_of_birth)) : 'NODOB';

                if (empty($cleanFullName) || $dob === 'NODOB') {
                    continue;
                }

                $identityKey = $cleanFullName . '|' . $cleanFather . '|' . $dob;
                $groupedByIdentity[$identityKey][] = $st;
            }

            foreach ($groupedByIdentity as $identityKey => $stGroup) {
                if (count($stGroup) <= 1) {
                    continue;
                }

                // Sort the group to find the Primary / Master student record
                // Preference order:
                // 1. Earliest academic session (e.g. 2024-2025)
                // 2. Earliest admission_date
                // 3. Lowest ID
                usort($stGroup, function ($a, $b) use ($sessionOrderMap) {
                    $orderA = $sessionOrderMap[$a->academic_session_id] ?? 999;
                    $orderB = $sessionOrderMap[$b->academic_session_id] ?? 999;
                    if ($orderA !== $orderB) {
                        return $orderA <=> $orderB;
                    }

                    $dateA = $a->admission_date ? strtotime($a->admission_date) : 0;
                    $dateB = $b->admission_date ? strtotime($b->admission_date) : 0;
                    if ($dateA !== $dateB && $dateA > 0 && $dateB > 0) {
                        return $dateA <=> $dateB;
                    }

                    return $a->id <=> $b->id;
                });

                $primaryStudent = $stGroup[0];
                $secondaryStudents = array_slice($stGroup, 1);

                // Find the latest session and class/section pointers among all records in the group
                $latestSessionStudent = $primaryStudent;
                $highestSessionRank = $sessionOrderMap[$primaryStudent->academic_session_id] ?? -1;

                foreach ($secondaryStudents as $secSt) {
                    $secRank = $sessionOrderMap[$secSt->academic_session_id] ?? -1;
                    if ($secRank >= $highestSessionRank) {
                        $highestSessionRank = $secRank;
                        $latestSessionStudent = $secSt;
                    }
                }

                DB::transaction(function () use (
                    $primaryStudent,
                    $secondaryStudents,
                    $latestSessionStudent,
                    $repointTable,
                    $schoolId
                ) {
                    foreach ($secondaryStudents as $secSt) {
                        // 1. Student Sessions
                        if (Schema::hasTable('student_sessions')) {
                            $secSessions = DB::table('student_sessions')
                                ->where('school_id', $schoolId)
                                ->where('student_id', $secSt->id)
                                ->get();

                            foreach ($secSessions as $ss) {
                                $existingPrimarySession = DB::table('student_sessions')
                                    ->where('school_id', $schoolId)
                                    ->where('student_id', $primaryStudent->id)
                                    ->where('academic_session_id', $ss->academic_session_id)
                                    ->first();

                                if ($existingPrimarySession) {
                                    DB::table('student_sessions')
                                        ->where('id', $existingPrimarySession->id)
                                        ->update([
                                            'class_id'     => $ss->class_id ?: $existingPrimarySession->class_id,
                                            'section_id'   => $ss->section_id ?: $existingPrimarySession->section_id,
                                            'roll_number'  => $ss->roll_number ?: $existingPrimarySession->roll_number,
                                            'is_promoted'  => $ss->is_promoted ? 1 : $existingPrimarySession->is_promoted,
                                            'session_data' => $ss->session_data ?: $existingPrimarySession->session_data,
                                        ]);
                                    DB::table('student_sessions')->where('id', $ss->id)->delete();
                                } else {
                                    DB::table('student_sessions')
                                        ->where('id', $ss->id)
                                        ->update(['student_id' => $primaryStudent->id]);
                                }
                            }

                            if ($secSt->academic_session_id) {
                                $hasSession = DB::table('student_sessions')
                                    ->where('school_id', $schoolId)
                                    ->where('student_id', $primaryStudent->id)
                                    ->where('academic_session_id', $secSt->academic_session_id)
                                    ->exists();

                                if (!$hasSession) {
                                    DB::table('student_sessions')->insert([
                                        'school_id'           => $schoolId,
                                        'student_id'          => $primaryStudent->id,
                                        'class_id'            => $secSt->class_id,
                                        'section_id'          => $secSt->section_id,
                                        'academic_session_id' => $secSt->academic_session_id,
                                        'roll_number'         => $secSt->roll_number,
                                        'is_promoted'         => 0,
                                        'created_at'          => now(),
                                        'updated_at'          => now(),
                                    ]);
                                }
                            }
                        }

                        // 2. Student Attendances (unique: school_id, student_id, date)
                        $repointTable('student_attendances', 'student_id', $secSt->id, $primaryStudent->id, ['school_id', 'date']);

                        // 3. Bus Attendances (unique: school_id, student_id, date)
                        $repointTable('bus_attendances', 'student_id', $secSt->id, $primaryStudent->id, ['school_id', 'date']);

                        // 4. Optional Subjects (unique: student_id, subject_id, academic_session_id)
                        $repointTable('student_optional_subjects', 'student_id', $secSt->id, $primaryStudent->id, ['subject_id', 'academic_session_id']);

                        // 5. Student Marks (unique: school_id, student_id, subject_id, exam_name)
                        $repointTable('student_marks', 'student_id', $secSt->id, $primaryStudent->id, ['school_id', 'subject_id']);

                        // 6. Report Card History Students
                        $repointTable('report_card_history_students', 'student_id', $secSt->id, $primaryStudent->id, ['report_card_history_id']);

                        // 7. Fees, Invoices, Receipts, and other child tables
                        $repointTable('student_fees', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('fee_receipts', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('fee_invoices', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('fee_refunds', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('fee_fines', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('late_fine_audit_logs', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('installment_edit_histories', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('student_documents', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('student_certificates', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('student_cards', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('student_gate_passes', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('student_leave_applications', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('teacher_assignment_submissions', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('report_card_histories', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('daily_task_evaluations', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('daily_task_reviews', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('library_transactions', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('inventory_sales', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('student_deletion_requests', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('payment_links', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('pending_cheques', 'student_id', $secSt->id, $primaryStudent->id);
                        $repointTable('optional_fee_mappings', 'student_id', $secSt->id, $primaryStudent->id);

                        // Clean up duplicate student user account if created separately
                        if ($secSt->user_id && $secSt->user_id !== $primaryStudent->user_id) {
                            if (!$primaryStudent->user_id) {
                                DB::table('students')
                                    ->where('id', $primaryStudent->id)
                                    ->update(['user_id' => $secSt->user_id]);
                            } else {
                                DB::table('users')->where('id', $secSt->user_id)->delete();
                            }
                        }

                        // Permanently remove the duplicate student record
                        DB::table('students')->where('id', $secSt->id)->delete();
                    }

                    // 8. Ensure Primary student's current pointers reflect their latest academic enrollment
                    $updateFields = [
                        'academic_session_id' => $latestSessionStudent->academic_session_id ?: $primaryStudent->academic_session_id,
                        'class_id'            => $latestSessionStudent->class_id ?: $primaryStudent->class_id,
                        'section_id'          => $latestSessionStudent->section_id ?: $primaryStudent->section_id,
                        'roll_number'         => $latestSessionStudent->roll_number ?: $primaryStudent->roll_number,
                    ];

                    if ($primaryStudent->academic_session_id && Schema::hasTable('student_sessions')) {
                        $hasOrigSession = DB::table('student_sessions')
                            ->where('school_id', $schoolId)
                            ->where('student_id', $primaryStudent->id)
                            ->where('academic_session_id', $primaryStudent->academic_session_id)
                            ->exists();

                        if (!$hasOrigSession) {
                            DB::table('student_sessions')->insert([
                                'school_id'           => $schoolId,
                                'student_id'          => $primaryStudent->id,
                                'class_id'            => $primaryStudent->class_id,
                                'section_id'          => $primaryStudent->section_id,
                                'academic_session_id' => $primaryStudent->academic_session_id,
                                'roll_number'         => $primaryStudent->roll_number,
                                'is_promoted'         => 0,
                                'created_at'          => now(),
                                'updated_at'          => now(),
                            ]);
                        }
                    }

                    DB::table('students')
                        ->where('id', $primaryStudent->id)
                        ->update($updateFields);
                });
            }

            Cache::forget('students_list_version_' . $schoolId);
            Cache::put('students_list_version_' . $schoolId, time(), 86400);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-reversible data unification
    }
};

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\SchoolClass;
use App\Models\Section;

class ConsolidateDuplicateClasses extends Command
{
    protected $signature = 'erp:consolidate-duplicate-classes';
    protected $description = 'Consolidate duplicate class masters safely by migrating all related student references.';

    public function info($string, $verbosity = null)
    {
        if ($this->output) {
            parent::info($string, $verbosity);
        } else {
            \Illuminate\Support\Facades\Log::info($string);
        }
    }

    public function error($string, $verbosity = null)
    {
        if ($this->output) {
            parent::error($string, $verbosity);
        } else {
            \Illuminate\Support\Facades\Log::error($string);
        }
    }

    public function handle()
    {
        $this->info("Starting duplicate classes consolidation...");

        $normalizeClassName = function($str) {
            $str = trim(strtolower((string)$str));
            $str = preg_replace('/^class\s*/i', '', $str);
            $str = preg_replace('/\s*class$/i', '', $str);
            $str = trim($str);
            return preg_replace('/[^a-z0-9]/', '', $str);
        };

        DB::transaction(function () use ($normalizeClassName) {
            // Group all classes by school_id
            $classesBySchool = SchoolClass::all()->groupBy('school_id');

            foreach ($classesBySchool as $schoolId => $classes) {
                // Group classes by canonical normalized name
                $grouped = $classes->groupBy(function ($class) use ($normalizeClassName) {
                    return $normalizeClassName($class->name);
                });

                foreach ($grouped as $canonicalName => $dupClasses) {
                    if ($dupClasses->count() <= 1) {
                        continue;
                    }

                    // Choose preferred class name according to ERP naming conventions
                    // e.g. "Class 2" preferred over "2"
                    $preferredClass = $dupClasses->first(function ($c) {
                        return preg_match('/^Class\s+\d+$/i', trim($c->name));
                    });

                    if (!$preferredClass) {
                        // fallback to the oldest created
                        $preferredClass = $dupClasses->sortBy('id')->first();
                    }

                    $this->info("School {$schoolId}: Merging duplicates for group '{$canonicalName}' into preferred Class: ID {$preferredClass->id} ('{$preferredClass->name}')");

                    $duplicateClasses = $dupClasses->reject(function ($c) use ($preferredClass) {
                        return $c->id === $preferredClass->id;
                    });

                    foreach ($duplicateClasses as $dupClass) {
                        $this->info("  Merging duplicate Class: ID {$dupClass->id} ('{$dupClass->name}')");

                        // 1. Process sections of duplicate class
                        $dupSections = Section::where('class_id', $dupClass->id)->get();

                        foreach ($dupSections as $dupSection) {
                            $normSecName = preg_replace('/[^a-z0-9]/', '', strtolower(trim($dupSection->name)));

                            // Check if preferred class already has a section with the same name
                            $targetSection = Section::where('class_id', $preferredClass->id)
                                ->get()
                                ->first(function ($sec) use ($normSecName) {
                                    return preg_replace('/[^a-z0-9]/', '', strtolower(trim($sec->name))) === $normSecName;
                                });

                            if ($targetSection) {
                                $this->info("    Merging Section ID {$dupSection->id} ('{$dupSection->name}') to Target Section ID {$targetSection->id}");
                                // Update all tables referencing this section to the target section
                                $this->updateSectionReferences($dupSection->id, $targetSection->id, $dupClass->id, $preferredClass->id);
                                // Delete the duplicate section
                                $dupSection->delete();
                            } else {
                                $this->info("    Moving Section ID {$dupSection->id} ('{$dupSection->name}') to Preferred Class");
                                // Just move section to preferred class (this updates class_id in sections table)
                                $dupSection->update(['class_id' => $preferredClass->id]);
                                // Also update any direct class references for rows belonging to this section
                                $this->updateDirectClassReferencesForSection($dupSection->id, $preferredClass->id);
                            }
                        }

                        // 2. Update remaining direct class references (e.g. subjects or any rows referencing the class id directly)
                        $this->updateDirectClassReferences($dupClass->id, $preferredClass->id);

                        // 3. Delete duplicate class
                        $dupClass->delete();
                    }
                }
            }
        });

        $this->info("Consolidation completed successfully!");
        return 0;
    }

    private function updateSectionReferences($oldSectionId, $newSectionId, $oldClassId, $newClassId)
    {
        // Tables referencing both class_id and section_id:
        // Update both section_id and class_id to target class/section
        $tablesWithBoth = [
            'students',
            'student_sessions',
            'student_attendances',
            'exams',
            'offline_tests',
            'class_wise_fees',
            'study_materials',
            'teacher_assignments',
            'timetables',
            'class_timetable_cells',
            'class_subject_teacher',
            'timetable_group_class_section',
        ];

        foreach ($tablesWithBoth as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            // If table has unique constraint, handle it
            if ($table === 'student_attendances') {
                DB::table($table)
                    ->where('class_id', $oldClassId)
                    ->where('section_id', $oldSectionId)
                    ->update([
                        'class_id' => $newClassId,
                        'section_id' => $newSectionId
                    ]);
            } elseif ($table === 'class_timetable_cells') {
                $records = DB::table($table)->where('class_id', $oldClassId)->where('section_id', $oldSectionId)->get();
                foreach ($records as $r) {
                    $exists = DB::table($table)
                        ->where('class_id', $newClassId)
                        ->where('section_id', $newSectionId)
                        ->where('timetable_group_period_id', $r->timetable_group_period_id)
                        ->where('day_of_week', $r->day_of_week)
                        ->exists();
                    if ($exists) {
                        DB::table($table)->where('id', $r->id)->delete();
                    } else {
                        DB::table($table)->where('id', $r->id)->update([
                            'class_id' => $newClassId,
                            'section_id' => $newSectionId
                        ]);
                    }
                }
            } elseif ($table === 'class_subject_teacher') {
                $records = DB::table($table)->where('class_id', $oldClassId)->where('section_id', $oldSectionId)->get();
                foreach ($records as $r) {
                    $exists = DB::table($table)
                        ->where('class_id', $newClassId)
                        ->where('section_id', $newSectionId)
                        ->where('subject_id', $r->subject_id)
                        ->exists();
                    if ($exists) {
                        DB::table($table)->where('id', $r->id)->delete();
                    } else {
                        DB::table($table)->where('id', $r->id)->update([
                            'class_id' => $newClassId,
                            'section_id' => $newSectionId
                        ]);
                    }
                }
            } elseif ($table === 'timetable_group_class_section') {
                $records = DB::table($table)->where('class_id', $oldClassId)->where('section_id', $oldSectionId)->get();
                foreach ($records as $r) {
                    $exists = DB::table($table)
                        ->where('timetable_group_id', $r->timetable_group_id)
                        ->where('class_id', $newClassId)
                        ->where('section_id', $newSectionId)
                        ->exists();
                    if ($exists) {
                        DB::table($table)->where('id', $r->id)->delete();
                    } else {
                        DB::table($table)->where('id', $r->id)->update([
                            'class_id' => $newClassId,
                            'section_id' => $newSectionId
                        ]);
                    }
                }
            } else {
                DB::table($table)
                    ->where('class_id', $oldClassId)
                    ->where('section_id', $oldSectionId)
                    ->update([
                        'class_id' => $newClassId,
                        'section_id' => $newSectionId
                    ]);
            }
        }

        // Tables referencing only section_id:
        $tablesWithOnlySection = [
            'section_subject_staff',
        ];

        foreach ($tablesWithOnlySection as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            if ($table === 'section_subject_staff') {
                $records = DB::table($table)->where('section_id', $oldSectionId)->get();
                foreach ($records as $r) {
                    $exists = DB::table($table)
                        ->where('section_id', $newSectionId)
                        ->where('subject_id', $r->subject_id)
                        ->where('academic_session_id', $r->academic_session_id)
                        ->exists();
                    if ($exists) {
                        DB::table($table)->where('id', $r->id)->delete();
                    } else {
                        DB::table($table)->where('id', $r->id)->update(['section_id' => $newSectionId]);
                    }
                }
            } else {
                DB::table($table)->where('section_id', $oldSectionId)->update(['section_id' => $newSectionId]);
            }
        }
    }

    private function updateDirectClassReferencesForSection($sectionId, $newClassId)
    {
        $tablesWithBoth = [
            'students',
            'student_sessions',
            'student_attendances',
            'exams',
            'offline_tests',
            'class_wise_fees',
            'study_materials',
            'teacher_assignments',
            'timetables',
            'class_timetable_cells',
            'class_subject_teacher',
            'timetable_group_class_section',
        ];

        foreach ($tablesWithBoth as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            DB::table($table)
                ->where('section_id', $sectionId)
                ->update(['class_id' => $newClassId]);
        }
    }

    private function updateDirectClassReferences($oldClassId, $newClassId)
    {
        $tablesWithOnlyClass = [
            'subjects',
        ];

        foreach ($tablesWithOnlyClass as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            DB::table($table)
                ->where('class_id', $oldClassId)
                ->update(['class_id' => $newClassId]);
        }
    }
}

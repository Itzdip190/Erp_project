<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\SectionSubjectStaff;
use App\Models\Staff;
use App\Models\AcademicSession;
use Illuminate\Http\Request;

class ClassAssignmentController extends Controller
{
    public function classOverview(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Fetch academic sessions
        $academicSessions = AcademicSession::where('school_id', $schoolId)->get();
        $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
            ?? AcademicSession::where('school_id', $schoolId)->first();

        $sessionId = $request->get('academic_session_id', $currentSession ? $currentSession->id : null);
        $selectedSession = $sessionId ? AcademicSession::find($sessionId) : null;

        // Start of academic year date to classify new vs old admissions
        $startOfYear = $selectedSession ? $selectedSession->start_date->toDateString() : now()->startOfYear()->toDateString();
        $today = now()->toDateString();

        // Dropdown options
        $classList = SchoolClass::where('school_id', $schoolId)->get();
        
        $classFilterId = $request->get('class_id');
        $sectionFilterId = $request->get('section_id');

        $sectionList = collect();
        if ($classFilterId) {
            $sectionList = Section::where('class_id', $classFilterId)->get();
        }

        // Teachers for the inline double click editor
        $teachers = Staff::where('school_id', $schoolId)->where('is_active', true)->get();

        // Get view mode & include_deactivated
        $viewMode = $request->get('view_mode', 'section');
        $includeDeactivated = filter_var($request->get('include_deactivated', false), FILTER_VALIDATE_BOOLEAN);

        // Fetch all issued transfer certificates
        $tcStudentIds = \App\Models\StudentCertificate::whereHas('template', function($q) {
            $q->where('type', 'transfer');
        })->pluck('student_id')->toArray();

        // Fetch irregular student IDs (attendance rate < 75%)
        $irregularStudentIds = [];
        $studentAttendanceStats = \App\Models\StudentAttendance::where('school_id', $schoolId)
            ->select('student_id', \DB::raw('count(*) as total'), \DB::raw("sum(case when status in ('present', 'late') then 1 else 0 end) as present"))
            ->groupBy('student_id')
            ->get();
        foreach ($studentAttendanceStats as $stat) {
            $rate = $stat->total > 0 ? ($stat->present / $stat->total) * 100 : 100;
            if ($rate < 75) {
                $irregularStudentIds[] = $stat->student_id;
            }
        }

        // Fetch sections or classes based on view mode
        $reportData = [];

        if ($viewMode === 'section') {
            $sectionsQuery = Section::with(['schoolClass', 'classTeacher'])
                ->where('school_id', $schoolId);

            if ($classFilterId) {
                $sectionsQuery->where('class_id', $classFilterId);
            }
            if ($sectionFilterId) {
                $sectionsQuery->where('id', $sectionFilterId);
            }

            $sections = $sectionsQuery->get();

            foreach ($sections as $section) {
                // Students query for this section
                $studentsQuery = \App\Models\Student::where('section_id', $section->id);
                $allStudents = $studentsQuery->get();
                $deletedStudents = \App\Models\Student::onlyTrashed()->where('section_id', $section->id)->get();

                // Compute counts
                $activeStudents = $allStudents->where('is_active', true);
                $deactivatedStudents = $allStudents->where('is_active', false);

                // Calculate base for Old/New admissions depending on includeDeactivated toggle
                $targetStudents = $includeDeactivated ? $allStudents : $activeStudents;

                $newAdmissions = $targetStudents->filter(function($s) use ($startOfYear) {
                    $d = $s->admission_date;
                    if ($d instanceof \Carbon\Carbon) {
                        return $d->toDateString() >= $startOfYear;
                    }
                    return $d >= $startOfYear;
                });
                
                $oldAdmissions = $targetStudents->filter(function($s) use ($startOfYear) {
                    $d = $s->admission_date;
                    if ($d instanceof \Carbon\Carbon) {
                        return $d->toDateString() < $startOfYear;
                    }
                    return $d < $startOfYear;
                });

                $todayAdmissions = $targetStudents->filter(function($s) use ($today) {
                    $d = $s->admission_date;
                    if ($d instanceof \Carbon\Carbon) {
                        return $d->toDateString() === $today;
                    }
                    return $d === $today;
                });

                $promoted = $oldAdmissions->count();
                $repeated = 0;

                // TC Students count
                $tcStudents = $allStudents->filter(function($s) use ($tcStudentIds) {
                    return in_array($s->id, $tcStudentIds);
                });
                $oldStudentTC = $tcStudents->filter(function($s) use ($startOfYear) {
                    $d = $s->admission_date;
                    if ($d instanceof \Carbon\Carbon) {
                        return $d->toDateString() < $startOfYear;
                    }
                    return $d < $startOfYear;
                })->count();
                $newStudentTC = $tcStudents->filter(function($s) use ($startOfYear) {
                    $d = $s->admission_date;
                    if ($d instanceof \Carbon\Carbon) {
                        return $d->toDateString() >= $startOfYear;
                    }
                    return $d >= $startOfYear;
                })->count();

                // Irregular Students
                $irregular = $activeStudents->filter(function($s) use ($irregularStudentIds) {
                    return in_array($s->id, $irregularStudentIds);
                })->count();

                // Deleted Students
                $oldStudentDeleted = $deletedStudents->filter(function($s) use ($startOfYear) {
                    $d = $s->admission_date;
                    if ($d instanceof \Carbon\Carbon) {
                        return $d->toDateString() < $startOfYear;
                    }
                    return $d < $startOfYear;
                })->count();
                $newStudentDeleted = $deletedStudents->filter(function($s) use ($startOfYear) {
                    $d = $s->admission_date;
                    if ($d instanceof \Carbon\Carbon) {
                        return $d->toDateString() >= $startOfYear;
                    }
                    return $d >= $startOfYear;
                })->count();

                $reportData[] = [
                    'label' => ($section->schoolClass ? $section->schoolClass->name : 'N/A') . ' - ' . $section->name,
                    'class_name' => $section->schoolClass ? $section->schoolClass->name : 'N/A',
                    'section_name' => $section->name,
                    'section_id' => $section->id,
                    'class_teacher' => $section->classTeacher ? $section->classTeacher->full_name : 'Not Assigned',
                    'class_teacher_id' => $section->class_teacher_id,
                    'total_subjects' => $section->schoolClass ? $section->schoolClass->subjects->count() : 0,
                    'timetable_created' => \App\Models\Timetable::where('section_id', $section->id)->exists() ? 'Created' : 'Not Created',
                    'promoted' => $promoted,
                    'repeated' => $repeated,
                    'new_admissions' => $newAdmissions->count(),
                    'today_admissions' => $todayAdmissions->count(),
                    'old_student_tc' => $oldStudentTC,
                    'new_student_tc' => $newStudentTC,
                    'irregular' => $irregular,
                    'deactivated' => $deactivatedStudents->count(),
                    'total_students' => $allStudents->count(),
                    'old_deleted' => $oldStudentDeleted,
                    'new_deleted' => $newStudentDeleted,
                    'active_students' => $activeStudents->count(),
                ];
            }
        } else {
            // Class View
            $classesQuery = SchoolClass::with(['subjects'])
                ->where('school_id', $schoolId);

            if ($classFilterId) {
                $classesQuery->where('id', $classFilterId);
            }

            $classes = $classesQuery->get();

            foreach ($classes as $class) {
                // Students query for this class
                $studentsQuery = \App\Models\Student::where('class_id', $class->id);
                $allStudents = $studentsQuery->get();
                $deletedStudents = \App\Models\Student::onlyTrashed()->where('class_id', $class->id)->get();

                // Compute counts
                $activeStudents = $allStudents->where('is_active', true);
                $deactivatedStudents = $allStudents->where('is_active', false);

                // Calculate base for Old/New admissions depending on includeDeactivated toggle
                $targetStudents = $includeDeactivated ? $allStudents : $activeStudents;

                $newAdmissions = $targetStudents->filter(function($s) use ($startOfYear) {
                    $d = $s->admission_date;
                    if ($d instanceof \Carbon\Carbon) {
                        return $d->toDateString() >= $startOfYear;
                    }
                    return $d >= $startOfYear;
                });
                
                $oldAdmissions = $targetStudents->filter(function($s) use ($startOfYear) {
                    $d = $s->admission_date;
                    if ($d instanceof \Carbon\Carbon) {
                        return $d->toDateString() < $startOfYear;
                    }
                    return $d < $startOfYear;
                });

                $todayAdmissions = $targetStudents->filter(function($s) use ($today) {
                    $d = $s->admission_date;
                    if ($d instanceof \Carbon\Carbon) {
                        return $d->toDateString() === $today;
                    }
                    return $d === $today;
                });

                $promoted = $oldAdmissions->count();
                $repeated = 0;

                // TC Students count
                $tcStudents = $allStudents->filter(function($s) use ($tcStudentIds) {
                    return in_array($s->id, $tcStudentIds);
                });
                $oldStudentTC = $tcStudents->filter(function($s) use ($startOfYear) {
                    $d = $s->admission_date;
                    if ($d instanceof \Carbon\Carbon) {
                        return $d->toDateString() < $startOfYear;
                    }
                    return $d < $startOfYear;
                })->count();
                $newStudentTC = $tcStudents->filter(function($s) use ($startOfYear) {
                    $d = $s->admission_date;
                    if ($d instanceof \Carbon\Carbon) {
                        return $d->toDateString() >= $startOfYear;
                    }
                    return $d >= $startOfYear;
                })->count();

                // Irregular Students
                $irregular = $activeStudents->filter(function($s) use ($irregularStudentIds) {
                    return in_array($s->id, $irregularStudentIds);
                })->count();

                // Deleted Students
                $oldStudentDeleted = $deletedStudents->filter(function($s) use ($startOfYear) {
                    $d = $s->admission_date;
                    if ($d instanceof \Carbon\Carbon) {
                        return $d->toDateString() < $startOfYear;
                    }
                    return $d < $startOfYear;
                })->count();
                $newStudentDeleted = $deletedStudents->filter(function($s) use ($startOfYear) {
                    $d = $s->admission_date;
                    if ($d instanceof \Carbon\Carbon) {
                        return $d->toDateString() >= $startOfYear;
                    }
                    return $d >= $startOfYear;
                })->count();

                $reportData[] = [
                    'label' => $class->name,
                    'class_name' => $class->name,
                    'section_name' => 'All Sections',
                    'section_id' => null,
                    'class_teacher' => 'N/A (Grouped)',
                    'class_teacher_id' => null,
                    'total_subjects' => $class->subjects->count(),
                    'timetable_created' => \App\Models\Timetable::where('class_id', $class->id)->exists() ? 'Created' : 'Not Created',
                    'promoted' => $promoted,
                    'repeated' => $repeated,
                    'new_admissions' => $newAdmissions->count(),
                    'today_admissions' => $todayAdmissions->count(),
                    'old_student_tc' => $oldStudentTC,
                    'new_student_tc' => $newStudentTC,
                    'irregular' => $irregular,
                    'deactivated' => $deactivatedStudents->count(),
                    'total_students' => $allStudents->count(),
                    'old_deleted' => $oldStudentDeleted,
                    'new_deleted' => $newStudentDeleted,
                    'active_students' => $activeStudents->count(),
                ];
            }
        }

        // Totals for footer
        $totals = [
            'promoted' => 0,
            'repeated' => 0,
            'new_admissions' => 0,
            'today_admissions' => 0,
            'old_student_tc' => 0,
            'new_student_tc' => 0,
            'irregular' => 0,
            'deactivated' => 0,
            'total_students' => 0,
            'old_deleted' => 0,
            'new_deleted' => 0,
            'active_students' => 0,
        ];

        foreach ($reportData as $row) {
            foreach ($totals as $key => $val) {
                $totals[$key] += $row[$key];
            }
        }

        return view('school.assignments.overview', compact(
            'academicSessions', 'sessionId', 'selectedSession',
            'classList', 'classFilterId', 'sectionList', 'sectionFilterId',
            'teachers', 'viewMode', 'includeDeactivated', 'reportData', 'totals'
        ));
    }

    public function classesForm(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $classes = SchoolClass::where('school_id', $schoolId)
            ->with(['sections' => function($q) {
                $q->orderBy('sort_order')->orderBy('id');
            }, 'sections.classTeacher'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
            
        $totalClasses = $classes->count();
        $totalSections = $classes->sum(function($c) {
            return $c->sections->count();
        });

        $teachers = Staff::where('school_id', $schoolId)->where('is_active', true)->get();
        return view('school.assignments.classes', compact('classes', 'teachers', 'totalClasses', 'totalSections'));
    }

    public function storeClass(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'local_name' => 'nullable|string|max:255',
            'class_code' => 'nullable|string|max:255',
            'sections' => 'required|array|min:1',
            'sections.*.name' => 'required|string|max:255',
            'sections.*.local_name' => 'nullable|string|max:255',
        ]);

        $schoolId = auth()->user()->school_id;

        // Auto-calculate numeric name
        $numericName = filter_var($request->name, FILTER_SANITIZE_NUMBER_INT);
        if (empty($numericName) && $numericName !== '0') {
            $maxNumeric = SchoolClass::where('school_id', $schoolId)->max('numeric_name');
            $numericName = ($maxNumeric !== null) ? $maxNumeric + 1 : 1;
        } else {
            $numericName = intval($numericName);
        }

        \DB::transaction(function() use ($request, $schoolId, $numericName) {
            $class = SchoolClass::create([
                'school_id' => $schoolId,
                'name' => $request->name,
                'local_name' => $request->local_name,
                'class_code' => $request->class_code,
                'numeric_name' => $numericName,
            ]);

            // Save sections
            foreach ($request->sections as $index => $secData) {
                Section::create([
                    'school_id' => $schoolId,
                    'class_id' => $class->id,
                    'name' => $secData['name'],
                    'local_name' => $secData['local_name'] ?? null,
                    'sort_order' => $index,
                ]);
            }

            // Create an audit activity log entry
            \App\Models\ImplementationTracker\ImplActivityLog::create([
                'school_id' => $schoolId,
                'tab_name' => 'Classes & Sections',
                'row_reference' => $class->name,
                'field_changed' => 'Creation',
                'old_value' => null,
                'new_value' => 'Class and sections created',
                'changed_by' => auth()->user()->name,
                'changed_at' => now(),
            ]);
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Class created successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Class created successfully.');
    }

    public function updateClass(Request $request, SchoolClass $class)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'local_name' => 'nullable|string|max:255',
            'class_code' => 'nullable|string|max:255',
            'sections' => 'required|array|min:1',
            'sections.*.id' => 'nullable|exists:sections,id',
            'sections.*.name' => 'required|string|max:255',
            'sections.*.local_name' => 'nullable|string|max:255',
        ]);

        $schoolId = auth()->user()->school_id;

        \DB::transaction(function() use ($request, $class, $schoolId) {
            $oldValues = "Name: {$class->name}, Local Name: {$class->local_name}, Code: {$class->class_code}";

            $class->update([
                'name' => $request->name,
                'local_name' => $request->local_name,
                'class_code' => $request->class_code,
            ]);

            $newValues = "Name: {$class->name}, Local Name: {$class->local_name}, Code: {$class->class_code}";

            // Sync sections
            $submittedIds = [];
            foreach ($request->sections as $index => $secData) {
                if (!empty($secData['id'])) {
                    $section = Section::where('class_id', $class->id)->find($secData['id']);
                    if ($section) {
                        $section->update([
                            'name' => $secData['name'],
                            'local_name' => $secData['local_name'] ?? null,
                            'sort_order' => $index,
                        ]);
                        $submittedIds[] = $section->id;
                    }
                } else {
                    $newSec = Section::create([
                        'school_id' => $schoolId,
                        'class_id' => $class->id,
                        'name' => $secData['name'],
                        'local_name' => $secData['local_name'] ?? null,
                        'sort_order' => $index,
                    ]);
                    $submittedIds[] = $newSec->id;
                }
            }

            // Remove sections that were removed from the UI
            Section::where('class_id', $class->id)
                ->whereNotIn('id', $submittedIds)
                ->delete();

            \App\Models\ImplementationTracker\ImplActivityLog::create([
                'school_id' => $schoolId,
                'tab_name' => 'Classes & Sections',
                'row_reference' => $class->name,
                'field_changed' => 'Update',
                'old_value' => $oldValues,
                'new_value' => $newValues,
                'changed_by' => auth()->user()->name,
                'changed_at' => now(),
            ]);
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Class updated successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Class updated successfully.');
    }

    public function reorderClasses(Request $request)
    {
        $request->validate([
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'required|exists:school_classes,id',
        ]);

        $schoolId = auth()->user()->school_id;

        \DB::transaction(function() use ($request, $schoolId) {
            foreach ($request->ordered_ids as $index => $id) {
                SchoolClass::where('school_id', $schoolId)
                    ->where('id', $id)
                    ->update(['sort_order' => $index]);
            }

            \App\Models\ImplementationTracker\ImplActivityLog::create([
                'school_id' => $schoolId,
                'tab_name' => 'Classes & Sections',
                'row_reference' => 'All Classes',
                'field_changed' => 'Order',
                'old_value' => null,
                'new_value' => 'Classes order updated',
                'changed_by' => auth()->user()->name,
                'changed_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Classes reordered successfully.'
        ]);
    }

    public function classLogs(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $logs = \App\Models\ImplementationTracker\ImplActivityLog::where('school_id', $schoolId)
            ->where('tab_name', 'Classes & Sections')
            ->orderBy('id', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'success' => true,
            'logs' => $logs->map(function($l) {
                return [
                    'id' => $l->id,
                    'row_reference' => $l->row_reference,
                    'field_changed' => $l->field_changed,
                    'old_value' => $l->old_value,
                    'new_value' => $l->new_value,
                    'changed_by' => $l->changed_by,
                    'changed_at' => $l->changed_at ? $l->changed_at->format('Y-m-d H:i:s') : 'N/A',
                ];
            })
        ]);
    }

    public function destroyClass(SchoolClass $class)
    {
        // Deleting class will also delete sections/subjects through database cascade or manually
        $class->sections()->delete();
        $class->subjects()->delete();
        $class->delete();

        return redirect()->back()->with('success', 'Class and its sections/subjects deleted successfully.');
    }

    public function storeSection(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:school_classes,id',
            'class_teacher_id' => 'nullable|exists:staff,id',
        ]);

        Section::create([
            'name' => $request->name,
            'class_id' => $request->class_id,
            'class_teacher_id' => $request->class_teacher_id,
        ]);

        return redirect()->back()->with('success', 'Section created successfully.');
    }

    public function destroySection(Section $section)
    {
        $section->delete();
        return redirect()->back()->with('success', 'Section deleted successfully.');
    }

    public function updateClassTeacher(Request $request, Section $section)
    {
        $request->validate([
            'class_teacher_id' => 'nullable|exists:staff,id',
        ]);

        $schoolId = auth()->user()->school_id;
        
        $oldTeacher = $section->classTeacher ? $section->classTeacher->full_name : 'None';

        $section->update([
            'class_teacher_id' => $request->class_teacher_id,
        ]);
        
        $section->refresh();
        $newTeacher = $section->classTeacher ? $section->classTeacher->full_name : 'None';

        // Audit log
        \App\Models\ImplementationTracker\ImplActivityLog::create([
            'school_id' => $schoolId,
            'tab_name' => 'Classes & Sections',
            'row_reference' => ($section->schoolClass ? $section->schoolClass->name : 'N/A') . ' - ' . $section->name,
            'field_changed' => 'Class Teacher Update',
            'old_value' => "Teacher: {$oldTeacher}",
            'new_value' => "Teacher: {$newTeacher}",
            'changed_by' => auth()->user()->name,
            'changed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Class teacher updated successfully.'
        ]);
    }


    public function subjectsForm(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $classes = SchoolClass::where('school_id', $schoolId)->orderBy('sort_order')->orderBy('id')->get();
        
        $classId = $request->get('class_id', $classes->first()?->id);
        $selectedClass = $classId ? SchoolClass::find($classId) : null;

        $typeFilter = $request->get('subject_type');
        $searchQuery = $request->get('search_query');

        $subjectsQuery = Subject::where('school_id', $schoolId);
        if ($classId) {
            $subjectsQuery->where('class_id', $classId);
        }
        if ($typeFilter) {
            $subjectsQuery->where('type', $typeFilter);
        }
        if ($searchQuery) {
            $subjectsQuery->where(function($q) use ($searchQuery) {
                $q->where('name', 'like', "%{$searchQuery}%")
                  ->orWhere('code', 'like', "%{$searchQuery}%");
            });
        }
        $subjects = $subjectsQuery->orderBy('sort_order')->orderBy('id')->get();

        // Calculate Stats
        $allClassSubjects = $classId ? Subject::where('school_id', $schoolId)->where('class_id', $classId)->get() : collect();
        
        $totalSubjectsCount = $allClassSubjects->count();
        $totalMandatory = $allClassSubjects->where('is_mandatory', true)->count();
        $totalElective = $allClassSubjects->where('is_mandatory', false)->count();

        $scholasticSubjects = $allClassSubjects->filter(fn($s) => strtolower($s->type) === 'scholastic');
        $scholasticCount = $scholasticSubjects->count();
        $scholasticMandatory = $scholasticSubjects->where('is_mandatory', true)->count();
        $scholasticElective = $scholasticSubjects->where('is_mandatory', false)->count();

        $customSubjects = $allClassSubjects->filter(fn($s) => strtolower($s->type) !== 'scholastic');
        $customCount = $customSubjects->count();
        $customMandatory = $customSubjects->where('is_mandatory', true)->count();
        $customElective = $customSubjects->where('is_mandatory', false)->count();

        // Collect all distinct subject types for the dropdown filter
        $subjectTypes = Subject::where('school_id', $schoolId)->distinct()->pluck('type')->toArray();
        if (!in_array('Scholastic', $subjectTypes)) $subjectTypes[] = 'Scholastic';
        if (!in_array('Non Scholastic', $subjectTypes)) $subjectTypes[] = 'Non Scholastic';
        if (!in_array('custom subject', $subjectTypes)) $subjectTypes[] = 'custom subject';

        return view('school.assignments.subjects', compact(
            'classes', 'subjects', 'selectedClass', 'classId', 'typeFilter', 'searchQuery',
            'totalSubjectsCount', 'totalMandatory', 'totalElective',
            'scholasticCount', 'scholasticMandatory', 'scholasticElective',
            'customCount', 'customMandatory', 'customElective', 'subjectTypes'
        ));
    }

    public function storeSubject(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'local_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_mandatory' => 'required|boolean',
            'type' => 'required|string|max:100',
            'class_ids' => 'required|array',
            'class_ids.*' => 'exists:school_classes,id',
        ]);

        $schoolId = auth()->user()->school_id;

        \DB::transaction(function() use ($request, $schoolId) {
            foreach ($request->class_ids as $cId) {
                // Determine max sort order for this class
                $maxSort = Subject::where('school_id', $schoolId)->where('class_id', $cId)->max('sort_order') ?? 0;

                Subject::create([
                    'school_id' => $schoolId,
                    'class_id' => $cId,
                    'name' => $request->name,
                    'code' => $request->code ?? '',
                    'local_name' => $request->local_name,
                    'description' => $request->description,
                    'is_mandatory' => $request->is_mandatory,
                    'type' => $request->type,
                    'max_marks' => 100,
                    'pass_marks' => 33,
                    'sort_order' => $maxSort + 1,
                ]);
            }

            // Create an audit activity log entry
            \App\Models\ImplementationTracker\ImplActivityLog::create([
                'school_id' => $schoolId,
                'tab_name' => 'Subjects',
                'row_reference' => $request->name,
                'field_changed' => 'Creation',
                'old_value' => null,
                'new_value' => "Subject created for classes: " . implode(', ', $request->class_ids),
                'changed_by' => auth()->user()->name,
                'changed_at' => now(),
            ]);
        });

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Subject(s) created successfully.']);
        }

        return redirect()->back()->with('success', 'Subject created successfully.');
    }

    public function updateSubject(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'local_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_mandatory' => 'required|boolean',
            'type' => 'required|string|max:100',
        ]);

        $schoolId = auth()->user()->school_id;

        $oldValues = "Name: {$subject->name}, Code: {$subject->code}, Type: {$subject->type}, Mandatory: " . ($subject->is_mandatory ? 'Yes' : 'No');

        $subject->update([
            'name' => $request->name,
            'code' => $request->code ?? '',
            'local_name' => $request->local_name,
            'description' => $request->description,
            'is_mandatory' => $request->is_mandatory,
            'type' => $request->type,
        ]);

        $newValues = "Name: {$subject->name}, Code: {$subject->code}, Type: {$subject->type}, Mandatory: " . ($subject->is_mandatory ? 'Yes' : 'No');

        // Log audit trail
        \App\Models\ImplementationTracker\ImplActivityLog::create([
            'school_id' => $schoolId,
            'tab_name' => 'Subjects',
            'row_reference' => $subject->name,
            'field_changed' => 'Update',
            'old_value' => $oldValues,
            'new_value' => $newValues,
            'changed_by' => auth()->user()->name,
            'changed_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Subject updated successfully.']);
        }

        return redirect()->back()->with('success', 'Subject updated successfully.');
    }

    public function destroySubject(Subject $subject)
    {
        $subject->delete();
        return redirect()->back()->with('success', 'Subject deleted successfully.');
    }

    public function reorderSubjects(Request $request)
    {
        $request->validate([
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'required|exists:subjects,id',
        ]);

        $schoolId = auth()->user()->school_id;

        \DB::transaction(function() use ($request, $schoolId) {
            foreach ($request->ordered_ids as $index => $id) {
                Subject::where('school_id', $schoolId)
                    ->where('id', $id)
                    ->update(['sort_order' => $index]);
            }

            \App\Models\ImplementationTracker\ImplActivityLog::create([
                'school_id' => $schoolId,
                'tab_name' => 'Subjects',
                'row_reference' => 'All Subjects',
                'field_changed' => 'Order',
                'old_value' => null,
                'new_value' => 'Subjects order updated via drag-and-drop',
                'changed_by' => auth()->user()->name,
                'changed_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Subjects reordered successfully.'
        ]);
    }

    public function subjectLogs(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $logs = \App\Models\ImplementationTracker\ImplActivityLog::where('school_id', $schoolId)
            ->where('tab_name', 'Subjects')
            ->orderBy('id', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'success' => true,
            'logs' => $logs->map(function($l) {
                return [
                    'id' => $l->id,
                    'row_reference' => $l->row_reference,
                    'field_changed' => $l->field_changed,
                    'old_value' => $l->old_value,
                    'new_value' => $l->new_value,
                    'changed_by' => $l->changed_by,
                    'changed_at' => $l->changed_at ? $l->changed_at->format('Y-m-d H:i:s') : 'N/A',
                ];
            })
        ]);
    }

    public function teachersForm(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Fetch academic sessions
        $academicSessions = AcademicSession::where('school_id', $schoolId)->get();
        $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
            ?? AcademicSession::where('school_id', $schoolId)->first();
        
        $sessionId = $request->get('academic_session_id', $currentSession ? $currentSession->id : null);
        $selectedSession = $sessionId ? AcademicSession::find($sessionId) : null;

        $classes = SchoolClass::where('school_id', $schoolId)->orderBy('sort_order')->orderBy('id')->get();
        $classId = $request->get('class_id', $classes->first()?->id);
        $selectedClass = $classId ? SchoolClass::find($classId) : null;

        $sections = $classId ? Section::where('class_id', $classId)->orderBy('sort_order')->orderBy('id')->get() : collect();
        $sectionId = $request->get('section_id', $sections->first()?->id);
        $selectedSection = $sectionId ? Section::find($sectionId) : null;

        // Teachers (Staff)
        $teachers = Staff::where('school_id', $schoolId)->where('is_active', true)->get();

        return view('school.assignments.teachers', compact(
            'academicSessions', 'sessionId', 'selectedSession',
            'classes', 'classId', 'selectedClass',
            'sections', 'sectionId', 'selectedSection',
            'teachers'
        ));
    }

    public function loadTeacherGrid(Request $request)
    {
        $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $schoolId = auth()->user()->school_id;
        $sessionId = $request->academic_session_id;
        $classId = $request->class_id;
        $sectionId = $request->section_id;

        $section = Section::with(['classTeacher', 'assistantClassTeacher'])->find($sectionId);

        // Fetch all subjects for this class
        $subjects = Subject::where('school_id', $schoolId)->where('class_id', $classId)->orderBy('sort_order')->orderBy('id')->get();

        // Fetch current assignments for this section, session
        $assignments = SectionSubjectStaff::where('school_id', $schoolId)
            ->where('section_id', $sectionId)
            ->where('academic_session_id', $sessionId)
            ->with(['staff', 'substituteStaff'])
            ->get();

        // Build grid data
        $gridData = [];
        $assignedCount = 0;
        $unassignedCount = 0;

        foreach ($subjects as $index => $subject) {
            // Find all primary teacher assignments for this subject
            $subAssignments = $assignments->where('subject_id', $subject->id);
            
            $assignedTeachers = [];
            foreach ($subAssignments as $assign) {
                $assignedTeachers[] = [
                    'assignment_id' => $assign->id,
                    'staff_id' => $assign->staff_id,
                    'staff_name' => $assign->staff ? $assign->staff->full_name : 'Unknown',
                    'substitute_staff_id' => $assign->substitute_staff_id,
                    'substitute_name' => $assign->substituteStaff ? $assign->substituteStaff->full_name : 'No Teacher Selected',
                ];
            }

            if (count($assignedTeachers) > 0) {
                $assignedCount++;
            } else {
                $unassignedCount++;
            }

            $gridData[] = [
                'index' => $index + 1,
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'subject_code' => $subject->code,
                'assignments' => $assignedTeachers,
            ];
        }

        return response()->json([
            'success' => true,
            'class_teacher_id' => $section->class_teacher_id,
            'assistant_class_teacher_id' => $section->assistant_class_teacher_id,
            'subjects_count' => $subjects->count(),
            'assigned_count' => $assignedCount,
            'unassigned_count' => $unassignedCount,
            'grid' => $gridData,
        ]);
    }

    public function saveTeacherGrid(Request $request)
    {
        $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'required|exists:sections,id',
            'class_teacher_id' => 'nullable|exists:staff,id',
            'assistant_class_teacher_id' => 'nullable|exists:staff,id',
            'assignments' => 'nullable|array',
            'assignments.*.subject_id' => 'required|exists:subjects,id',
            'assignments.*.staff_id' => 'required|exists:staff,id',
            'assignments.*.substitute_staff_id' => 'nullable|exists:staff,id',
        ]);

        $schoolId = auth()->user()->school_id;
        $sessionId = $request->academic_session_id;
        $sectionId = $request->section_id;

        \DB::transaction(function() use ($request, $schoolId, $sessionId, $sectionId) {
            // Update Class Teacher & Assistant Class Teacher
            $section = Section::find($sectionId);
            $section->update([
                'class_teacher_id' => $request->class_teacher_id,
                'assistant_class_teacher_id' => $request->assistant_class_teacher_id,
            ]);

            // Track existing keys in the request to delete others
            $submittedKeys = [];

            if ($request->assignments) {
                foreach ($request->assignments as $assignData) {
                    $subId = $assignData['subject_id'];
                    $staffId = $assignData['staff_id'];
                    $substituteStaffId = $assignData['substitute_staff_id'] ?? null;

                    // Save or update mapping
                    $mapping = SectionSubjectStaff::updateOrCreate([
                        'school_id' => $schoolId,
                        'section_id' => $sectionId,
                        'subject_id' => $subId,
                        'staff_id' => $staffId,
                        'academic_session_id' => $sessionId,
                    ], [
                        'substitute_staff_id' => $substituteStaffId,
                    ]);

                    $submittedKeys[] = $mapping->id;
                }
            }

            // Remove any mappings for this section and session that are NOT in the submitted list
            SectionSubjectStaff::where('school_id', $schoolId)
                ->where('section_id', $sectionId)
                ->where('academic_session_id', $sessionId)
                ->whereNotIn('id', $submittedKeys)
                ->delete();

            // Log activity audit trail
            \App\Models\ImplementationTracker\ImplActivityLog::create([
                'school_id' => $schoolId,
                'tab_name' => 'Teacher Assignment',
                'row_reference' => $section->schoolClass->name . ' - ' . $section->name,
                'field_changed' => 'Update Grid',
                'old_value' => null,
                'new_value' => 'Timetable teacher assignments and class leaders updated',
                'changed_by' => auth()->user()->name,
                'changed_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Teacher assignments saved successfully.'
        ]);
    }

    public function teacherLogs(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $logs = \App\Models\ImplementationTracker\ImplActivityLog::where('school_id', $schoolId)
            ->where('tab_name', 'Teacher Assignment')
            ->orderBy('id', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'success' => true,
            'logs' => $logs->map(function($l) {
                return [
                    'id' => $l->id,
                    'row_reference' => $l->row_reference,
                    'field_changed' => $l->field_changed,
                    'old_value' => $l->old_value,
                    'new_value' => $l->new_value,
                    'changed_by' => $l->changed_by,
                    'changed_at' => $l->changed_at ? $l->changed_at->format('Y-m-d H:i:s') : 'N/A',
                ];
            })
        ]);
    }

    public function exportTeacherMappingTemplate(Request $request)
    {
        $request->validate([
            'section_ids' => 'required|string',
        ]);

        $schoolId = auth()->user()->school_id;
        $sectionIds = explode(',', $request->section_ids);

        $sections = Section::where('school_id', $schoolId)
            ->whereIn('id', $sectionIds)
            ->with(['schoolClass', 'schoolClass.subjects'])
            ->get();

        $filename = "teacher_assignment_template_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($sections) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Class Name', 
                'Section Name', 
                'Subject Name', 
                'Subject Code', 
                'Primary Teacher Employee ID', 
                'Substitute Teacher Employee ID'
            ]);

            foreach ($sections as $section) {
                $subjects = $section->schoolClass ? $section->schoolClass->subjects : collect();
                foreach ($subjects as $subject) {
                    fputcsv($file, [
                        $section->schoolClass->name,
                        $section->name,
                        $subject->name,
                        $subject->code ?? '',
                        '', 
                        ''  
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importTeacherMapping(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $schoolId = auth()->user()->school_id;
        
        $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
            ?? AcademicSession::where('school_id', $schoolId)->first();

        if (!$currentSession) {
            return response()->json(['success' => false, 'message' => 'No active academic session found.'], 422);
        }

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        $rows = [];
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            $header = fgetcsv($handle, 1000, ",");
            $header = array_map(function($h) {
                return trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h));
            }, $header);

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($header) === count($data)) {
                    $rows[] = array_combine($header, $data);
                }
            }
            fclose($handle);
        }

        $importedCount = 0;
        $errors = [];

        \DB::transaction(function() use ($rows, $schoolId, $currentSession, &$importedCount, &$errors) {
            foreach ($rows as $index => $row) {
                $className = trim($row['Class Name'] ?? '');
                $sectionName = trim($row['Section Name'] ?? '');
                $subjectName = trim($row['Subject Name'] ?? '');
                $primaryEmpId = trim($row['Primary Teacher Employee ID'] ?? '');
                $substituteEmpId = trim($row['Substitute Teacher Employee ID'] ?? '');

                if (empty($className) || empty($sectionName) || empty($subjectName)) {
                    continue;
                }

                $class = SchoolClass::where('school_id', $schoolId)->where('name', $className)->first();
                if (!$class) {
                    $errors[] = "Row " . ($index + 2) . ": Class '{$className}' not found.";
                    continue;
                }

                $section = Section::where('school_id', $schoolId)->where('class_id', $class->id)->where('name', $sectionName)->first();
                if (!$section) {
                    $errors[] = "Row " . ($index + 2) . ": Section '{$sectionName}' not found in Class '{$className}'.";
                    continue;
                }

                $subject = Subject::where('school_id', $schoolId)->where('class_id', $class->id)->where('name', $subjectName)->first();
                if (!$subject) {
                    $errors[] = "Row " . ($index + 2) . ": Subject '{$subjectName}' not found in Class '{$className}'.";
                    continue;
                }

                $primaryStaffIds = [];
                if (!empty($primaryEmpId)) {
                    $empIds = array_map('trim', explode(',', $primaryEmpId));
                    foreach ($empIds as $id) {
                        $staff = Staff::where('school_id', $schoolId)->where('employee_id', $id)->first();
                        if ($staff) {
                            $primaryStaffIds[] = $staff->id;
                        } else {
                            $errors[] = "Row " . ($index + 2) . ": Primary Teacher Employee ID '{$id}' not found.";
                        }
                    }
                }

                $substituteStaffId = null;
                if (!empty($substituteEmpId)) {
                    $staff = Staff::where('school_id', $schoolId)->where('employee_id', $substituteEmpId)->first();
                    if ($staff) {
                        $substituteStaffId = $staff->id;
                    } else {
                        $errors[] = "Row " . ($index + 2) . ": Substitute Teacher Employee ID '{$substituteEmpId}' not found.";
                    }
                }

                foreach ($primaryStaffIds as $pStaffId) {
                    SectionSubjectStaff::updateOrCreate([
                        'school_id' => $schoolId,
                        'section_id' => $section->id,
                        'subject_id' => $subject->id,
                        'staff_id' => $pStaffId,
                        'academic_session_id' => $currentSession->id,
                    ], [
                        'substitute_staff_id' => $substituteStaffId,
                    ]);
                    $importedCount++;
                }
            }

            if ($importedCount > 0) {
                \App\Models\ImplementationTracker\ImplActivityLog::create([
                    'school_id' => $schoolId,
                    'tab_name' => 'Teacher Assignment',
                    'row_reference' => 'Bulk Import',
                    'field_changed' => 'Import',
                    'old_value' => null,
                    'new_value' => "Imported {$importedCount} teacher assignments via CSV template upload",
                    'changed_by' => auth()->user()->name,
                    'changed_at' => now(),
                ]);
            }
        });

        if (count($errors) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk upload completed with errors.',
                'errors' => $errors,
                'imported_count' => $importedCount
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully imported {$importedCount} teacher assignments."
        ]);
    }
}

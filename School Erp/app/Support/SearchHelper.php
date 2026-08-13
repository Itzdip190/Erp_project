<?php

namespace App\Support;

use App\Models\Student;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SearchHelper
{
    /**
     * Normalize search input: trim whitespace and collapse internal extra spaces.
     */
    public static function normalizeQuery(?string $search): string
    {
        if ($search === null) {
            return '';
        }
        return trim(preg_replace('/\s+/', ' ', $search));
    }

    /**
     * Get SQL concat expression for first_name and last_name based on DB driver.
     */
    public static function getConcatNameExpr(string $tablePrefix = ''): string
    {
        $prefix = $tablePrefix ? rtrim($tablePrefix, '.') . '.' : '';
        $firstName = "COALESCE({$prefix}first_name, '')";
        $lastName = "COALESCE({$prefix}last_name, '')";

        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite' || $driver === 'pgsql') {
            return "LOWER(TRIM({$firstName} || ' ' || {$lastName}))";
        }
        return "LOWER(TRIM(CONCAT({$firstName}, ' ', {$lastName})))";
    }

    /**
     * Apply standardized student search on an Eloquent builder.
     */
    public static function applyStudentSearch(Builder $builder, ?string $search, string $tablePrefix = ''): Builder
    {
        $normalized = self::normalizeQuery($search);
        if ($normalized === '') {
            return $builder;
        }

        $terms = explode(' ', $normalized);
        $lowerSearch = strtolower($normalized);
        $prefix = $tablePrefix ? rtrim($tablePrefix, '.') . '.' : '';

        return $builder->where(function ($q) use ($normalized, $terms, $lowerSearch, $prefix) {
            // 1. Full concatenated name match (e.g. "Gaurav Yadav")
            $concatExpr = self::getConcatNameExpr($prefix);
            $q->whereRaw("{$concatExpr} LIKE ?", ['%' . $lowerSearch . '%']);

            // 2. Multi-word AND matching across student fields
            if (count($terms) > 1) {
                $q->orWhere(function ($multiQ) use ($terms, $prefix) {
                    foreach ($terms as $term) {
                        $lowerTerm = '%' . strtolower($term) . '%';
                        $multiQ->where(function ($termQ) use ($lowerTerm, $prefix) {
                            $termQ->whereRaw("LOWER({$prefix}first_name) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}last_name) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}admission_number) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}roll_number) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}phone) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}email) LIKE ?", [$lowerTerm]);
                        });
                    }
                });
            } else {
                // 3. Single term column matching for student fields
                $lowerTerm = '%' . strtolower($terms[0]) . '%';
                $q->orWhereRaw("LOWER({$prefix}first_name) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}last_name) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}admission_number) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}roll_number) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}phone) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}email) LIKE ?", [$lowerTerm]);
            }
        });
    }

    /**
     * Apply standardized staff search on an Eloquent builder.
     */
    public static function applyStaffSearch(Builder $builder, ?string $search, string $tablePrefix = ''): Builder
    {
        $normalized = self::normalizeQuery($search);
        if ($normalized === '') {
            return $builder;
        }

        $terms = explode(' ', $normalized);
        $lowerSearch = strtolower($normalized);
        $prefix = $tablePrefix ? rtrim($tablePrefix, '.') . '.' : '';

        return $builder->where(function ($q) use ($normalized, $terms, $lowerSearch, $prefix) {
            // 1. Full concatenated name match
            $concatExpr = self::getConcatNameExpr($prefix);
            $q->whereRaw("{$concatExpr} LIKE ?", ['%' . $lowerSearch . '%']);

            // 2. Multi-word AND matching
            if (count($terms) > 1) {
                $q->orWhere(function ($multiQ) use ($terms, $prefix) {
                    foreach ($terms as $term) {
                        $lowerTerm = '%' . strtolower($term) . '%';
                        $multiQ->where(function ($termQ) use ($lowerTerm, $prefix) {
                            $termQ->whereRaw("LOWER({$prefix}first_name) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}last_name) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}employee_id) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}email) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}phone) LIKE ?", [$lowerTerm]);
                        });
                    }
                });
            } else {
                // 3. Single term matching
                $lowerTerm = '%' . strtolower($terms[0]) . '%';
                $q->orWhereRaw("LOWER({$prefix}first_name) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}last_name) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}employee_id) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}email) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}phone) LIKE ?", [$lowerTerm]);
            }
        });
    }

    /**
     * Ensure all active students and staff in the tenant school have provisioned/linked User accounts.
     */
    public static function syncSchoolUserAccounts(int $schoolId): void
    {
        // 0a. Repair any parent users that were incorrectly mutated to 'student' role by previous sync bug
        $corruptedParentUsers = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
            ->where('school_id', $schoolId)
            ->where(function ($uq) {
                $uq->where('email', 'LIKE', '%@parent.%')
                   ->orWhereExists(function ($sq) {
                       $sq->select(DB::raw(1))
                          ->from('students')
                          ->whereColumn('students.school_id', 'users.school_id')
                          ->where(function ($matchQ) {
                              $matchQ->whereRaw("LOWER(users.email) = LOWER(students.father_email)")
                                     ->orWhereRaw("LOWER(users.email) = LOWER(students.mother_email)")
                                     ->orWhereRaw("LOWER(users.email) = LOWER(students.guardian_email)")
                                     ->orWhereRaw("users.phone = students.father_phone")
                                     ->orWhereRaw("users.phone = students.mother_phone")
                                     ->orWhereRaw("users.phone = students.guardian_phone");
                          });
                   });
            })
            ->get();

        foreach ($corruptedParentUsers as $parentUser) {
            $isStudentUser = Student::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
                ->where('school_id', $schoolId)
                ->where('user_id', $parentUser->id)
                ->where(function ($sq) use ($parentUser) {
                    $sq->whereRaw("LOWER(email) = LOWER(?)", [$parentUser->email])
                       ->where('email', 'NOT LIKE', '%@parent.%');
                })
                ->exists();

            if (!$isStudentUser) {
                if ($parentUser->role !== 'parent') {
                    $parentUser->update(['role' => 'parent']);
                }
                if ($parentUser->hasRole('student')) {
                    $parentUser->removeRole('student');
                }
                if (!$parentUser->hasRole('parent')) {
                    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
                    $parentUser->assignRole('parent');
                }
            }
        }

        // 0b. Clean up any mislinked students where user_id points to a parent user account
        $mislinkedStudents = Student::where('school_id', $schoolId)
            ->whereNotNull('user_id')
            ->whereHas('user', function ($uq) {
                $uq->where('role', 'parent')
                   ->orWhereHas('roles', function ($rq) {
                       $rq->where('name', 'parent');
                   });
            })
            ->get();

        foreach ($mislinkedStudents as $mislinked) {
            $mislinked->update(['user_id' => null]);
        }

        // 1. Sync unlinked students
        $unlinkedStudents = Student::where('school_id', $schoolId)
            ->whereNull('user_id')
            ->where('is_active', true)
            ->get();

        foreach ($unlinkedStudents as $student) {
            $studentSchoolId = $student->school_id;
            $schoolCode = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $student->school?->code ?? ('sch' . $studentSchoolId)));

            $targetEmail = !empty($student->email) ? trim($student->email) : null;
            if ($targetEmail) {
                $parentEmails = array_filter([$student->guardian_email, $student->father_email, $student->mother_email]);
                if (in_array(strtolower($targetEmail), array_map('strtolower', $parentEmails)) || str_contains(strtolower($targetEmail), '@parent.')) {
                    $targetEmail = null;
                }
            }

            if (!$targetEmail) {
                $cleanFirstName   = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $student->first_name));
                $cleanLastName    = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $student->last_name ?? ''));
                $cleanAdmissionId = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $student->admission_number));
                $targetEmail     = $cleanFirstName . '.' . $cleanLastName . '.' . $cleanAdmissionId . '@student.' . $schoolCode . '.com';
            }

            $user = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
                ->where('school_id', $studentSchoolId)
                ->where('email', $targetEmail)
                ->where(function ($rq) {
                    $rq->where('role', 'student')
                       ->orWhereHas('roles', function ($spatieQ) {
                           $spatieQ->where('name', 'student');
                       });
                })
                ->first();

            if (!$user) {
                $existingParentUser = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
                    ->where('school_id', $studentSchoolId)
                    ->where('email', $targetEmail)
                    ->where(function ($prq) {
                        $prq->where('role', 'parent')
                            ->orWhereHas('roles', function ($spatieQ) {
                                $spatieQ->where('name', 'parent');
                            });
                    })
                    ->exists();

                if ($existingParentUser) {
                    $cleanFirstName   = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $student->first_name));
                    $cleanLastName    = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $student->last_name ?? ''));
                    $cleanAdmissionId = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $student->admission_number));
                    $targetEmail     = $cleanFirstName . '.' . $cleanLastName . '.' . $cleanAdmissionId . '@student.' . $schoolCode . '.com';
                }

                try {
                    $user = User::create([
                        'school_id' => $studentSchoolId,
                        'name'      => trim($student->first_name . ' ' . ($student->last_name ?? '')),
                        'email'     => $targetEmail,
                        'phone'     => $student->phone ?: null,
                        'password'  => \Illuminate\Support\Facades\Hash::make('Student@2026!'),
                        'role'      => 'student',
                        'is_active' => true,
                    ]);
                    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
                    $user->assignRole('student');
                } catch (\Exception $e) {
                    $user = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
                        ->where('school_id', $studentSchoolId)
                        ->where('email', $targetEmail)
                        ->where(function ($rq) {
                            $rq->where('role', 'student')
                               ->orWhereHas('roles', function ($spatieQ) {
                                   $spatieQ->where('name', 'student');
                               });
                        })
                        ->first();
                }
            }

            if ($user && ($user->role === 'student' || $user->hasRole('student'))) {
                $student->update(['user_id' => $user->id]);
            }
        }

        // 2. Sync unlinked staff
        $unlinkedStaff = Staff::where('school_id', $schoolId)
            ->whereNull('user_id')
            ->where('is_active', true)
            ->get();

        foreach ($unlinkedStaff as $staff) {
            $staffSchoolId = $staff->school_id;
            $schoolCode = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $staff->school?->code ?? ('sch' . $staffSchoolId)));

            $targetEmail = !empty($staff->email) ? trim($staff->email) : null;
            if (!$targetEmail) {
                $cleanFirstName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $staff->first_name));
                $cleanLastName  = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $staff->last_name ?? ''));
                $cleanEmpId     = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $staff->employee_id));
                $targetEmail   = $cleanFirstName . '.' . $cleanLastName . '.' . $cleanEmpId . '@staff.' . $schoolCode . '.com';
            }

            $user = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
                ->where('school_id', $staffSchoolId)
                ->where('email', $targetEmail)
                ->first();

            if (!$user) {
                try {
                    $user = User::create([
                        'school_id' => $staffSchoolId,
                        'name'      => trim($staff->first_name . ' ' . ($staff->last_name ?? '')),
                        'email'     => $targetEmail,
                        'phone'     => $staff->phone ?? null,
                        'password'  => \Illuminate\Support\Facades\Hash::make('Staff@2026!'),
                        'role'      => 'teacher',
                        'is_active' => true,
                    ]);
                    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
                    $user->assignRole('teacher');
                } catch (\Exception $e) {
                    $user = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
                        ->where('school_id', $staffSchoolId)
                        ->where('email', $targetEmail)
                        ->first();
                }
            }

            if ($user) {
                $staff->update(['user_id' => $user->id]);
            }
        }
    }

    /**
     * Apply standardized parent search on a Student query builder.
     */
    public static function applyParentSearch(Builder $builder, ?string $search, string $tablePrefix = ''): Builder
    {
        $normalized = self::normalizeQuery($search);
        if ($normalized === '') {
            return $builder;
        }

        $terms = explode(' ', $normalized);
        $lowerSearch = strtolower($normalized);
        $prefix = $tablePrefix ? rtrim($tablePrefix, '.') . '.' : '';

        return $builder->where(function ($q) use ($normalized, $terms, $lowerSearch, $prefix) {
            $concatExpr = self::getConcatNameExpr($prefix);
            $q->whereRaw("{$concatExpr} LIKE ?", ['%' . $lowerSearch . '%']);

            if (count($terms) > 1) {
                $q->orWhere(function ($multiQ) use ($terms, $prefix) {
                    foreach ($terms as $term) {
                        $lowerTerm = '%' . strtolower($term) . '%';
                        $multiQ->where(function ($termQ) use ($lowerTerm, $prefix) {
                            $termQ->whereRaw("LOWER({$prefix}guardian_name) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}father_name) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}mother_name) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}guardian_phone) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}father_phone) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}mother_phone) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}guardian_email) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}father_email) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}first_name) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}last_name) LIKE ?", [$lowerTerm])
                                  ->orWhereRaw("LOWER({$prefix}admission_number) LIKE ?", [$lowerTerm]);
                        });
                    }
                });
            } else {
                $lowerTerm = '%' . strtolower($terms[0]) . '%';
                $q->orWhereRaw("LOWER({$prefix}guardian_name) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}father_name) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}mother_name) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}guardian_phone) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}father_phone) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}mother_phone) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}guardian_email) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}father_email) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}first_name) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}last_name) LIKE ?", [$lowerTerm])
                  ->orWhereRaw("LOWER({$prefix}admission_number) LIKE ?", [$lowerTerm]);
            }
        });
    }

    /**
     * Apply standardized user search on an Eloquent builder.
     */
    public static function applyUserSearch(Builder $builder, ?string $search): Builder
    {
        $normalized = self::normalizeQuery($search);
        if ($normalized === '') {
            return $builder;
        }

        $terms = explode(' ', $normalized);
        $lowerSearch = strtolower($normalized);

        return $builder->where(function ($q) use ($normalized, $terms, $lowerSearch) {
            // 1. Direct User table field matches (name, email, phone)
            $q->whereRaw("LOWER(name) LIKE ?", ['%' . $lowerSearch . '%'])
              ->orWhereRaw("LOWER(email) LIKE ?", ['%' . $lowerSearch . '%'])
              ->orWhereRaw("LOWER(phone) LIKE ?", ['%' . $lowerSearch . '%']);

            // 2. Student User matches: match student model attributes (first_name, last_name, admission_number, roll_number, email, phone)
            // for Student accounts (role is 'student' or has Spatie role 'student' or has student model link)
            $q->orWhere(function ($studentQ) use ($lowerSearch, $terms) {
                $studentQ->where(function ($roleQ) {
                    $roleQ->where('role', 'student')
                          ->orWhereHas('roles', function ($rq) {
                              $rq->where('name', 'student');
                          })
                          ->orWhereHas('student');
                })->whereHas('student', function ($sq) use ($lowerSearch, $terms) {
                    $concatExpr = self::getConcatNameExpr('');
                    $sq->where(function ($subSq) use ($concatExpr, $lowerSearch, $terms) {
                        $subSq->whereRaw("{$concatExpr} LIKE ?", ['%' . $lowerSearch . '%'])
                              ->orWhereRaw("LOWER(admission_number) LIKE ?", ['%' . $lowerSearch . '%'])
                              ->orWhereRaw("LOWER(roll_number) LIKE ?", ['%' . $lowerSearch . '%'])
                              ->orWhereRaw("LOWER(phone) LIKE ?", ['%' . $lowerSearch . '%'])
                              ->orWhereRaw("LOWER(email) LIKE ?", ['%' . $lowerSearch . '%']);

                        if (count($terms) > 1) {
                            $subSq->orWhere(function ($multiQ) use ($terms) {
                                foreach ($terms as $term) {
                                    $lowerTerm = '%' . strtolower($term) . '%';
                                    $multiQ->where(function ($termQ) use ($lowerTerm) {
                                        $termQ->whereRaw("LOWER(first_name) LIKE ?", [$lowerTerm])
                                              ->orWhereRaw("LOWER(last_name) LIKE ?", [$lowerTerm])
                                              ->orWhereRaw("LOWER(admission_number) LIKE ?", [$lowerTerm])
                                              ->orWhereRaw("LOWER(roll_number) LIKE ?", [$lowerTerm])
                                              ->orWhereRaw("LOWER(phone) LIKE ?", [$lowerTerm])
                                              ->orWhereRaw("LOWER(email) LIKE ?", [$lowerTerm]);
                                    });
                                }
                            });
                        }
                    });
                });
            });

            // 3. Parent User matches: match parent fields on linked student records
            // for Parent accounts (role is 'parent' or has Spatie role 'parent')
            $q->orWhere(function ($parentQ) use ($lowerSearch, $terms) {
                $parentQ->where(function ($roleQ) {
                    $roleQ->where('role', 'parent')
                          ->orWhereHas('roles', function ($rq) {
                              $rq->where('name', 'parent');
                          });
                })->where(function ($pMatchQ) use ($lowerSearch, $terms) {
                    $pMatchQ->whereHas('students', function ($sq) use ($lowerSearch, $terms) {
                        $sq->where(function ($subSq) use ($lowerSearch, $terms) {
                            $subSq->whereRaw("LOWER(father_name) LIKE ?", ['%' . $lowerSearch . '%'])
                                  ->orWhereRaw("LOWER(guardian_name) LIKE ?", ['%' . $lowerSearch . '%'])
                                  ->orWhereRaw("LOWER(mother_name) LIKE ?", ['%' . $lowerSearch . '%'])
                                  ->orWhereRaw("LOWER(father_phone) LIKE ?", ['%' . $lowerSearch . '%'])
                                  ->orWhereRaw("LOWER(guardian_phone) LIKE ?", ['%' . $lowerSearch . '%'])
                                  ->orWhereRaw("LOWER(father_email) LIKE ?", ['%' . $lowerSearch . '%'])
                                  ->orWhereRaw("LOWER(guardian_email) LIKE ?", ['%' . $lowerSearch . '%'])
                                  ->orWhereRaw("LOWER(admission_number) LIKE ?", ['%' . $lowerSearch . '%']);

                            if (count($terms) > 1) {
                                $subSq->orWhere(function ($multiQ) use ($terms) {
                                    foreach ($terms as $term) {
                                        $lowerTerm = '%' . strtolower($term) . '%';
                                        $multiQ->where(function ($termQ) use ($lowerTerm) {
                                            $termQ->whereRaw("LOWER(father_name) LIKE ?", [$lowerTerm])
                                                  ->orWhereRaw("LOWER(guardian_name) LIKE ?", [$lowerTerm])
                                                  ->orWhereRaw("LOWER(mother_name) LIKE ?", [$lowerTerm])
                                                  ->orWhereRaw("LOWER(father_phone) LIKE ?", [$lowerTerm])
                                                  ->orWhereRaw("LOWER(guardian_phone) LIKE ?", [$lowerTerm])
                                                  ->orWhereRaw("LOWER(father_email) LIKE ?", [$lowerTerm])
                                                  ->orWhereRaw("LOWER(guardian_email) LIKE ?", [$lowerTerm])
                                                  ->orWhereRaw("LOWER(admission_number) LIKE ?", [$lowerTerm]);
                                        });
                                    }
                                });
                            }
                        });
                    })
                    ->orWhereExists(function ($subQ) use ($lowerSearch, $terms) {
                        $subQ->select(DB::raw(1))
                             ->from('students')
                             ->whereColumn('students.school_id', 'users.school_id')
                             ->where(function ($sq) {
                                 $sq->whereRaw("LOWER(users.email) = LOWER(students.guardian_email)")
                                    ->orWhereRaw("LOWER(users.email) = LOWER(students.father_email)")
                                    ->orWhereRaw("LOWER(users.email) = LOWER(students.mother_email)")
                                    ->orWhereRaw("users.phone = students.guardian_phone")
                                    ->orWhereRaw("users.phone = students.father_phone");
                             })
                             ->where(function ($subSq) use ($lowerSearch, $terms) {
                                 $subSq->whereRaw("LOWER(father_name) LIKE ?", ['%' . $lowerSearch . '%'])
                                       ->orWhereRaw("LOWER(guardian_name) LIKE ?", ['%' . $lowerSearch . '%'])
                                       ->orWhereRaw("LOWER(mother_name) LIKE ?", ['%' . $lowerSearch . '%'])
                                       ->orWhereRaw("LOWER(father_phone) LIKE ?", ['%' . $lowerSearch . '%'])
                                       ->orWhereRaw("LOWER(guardian_phone) LIKE ?", ['%' . $lowerSearch . '%'])
                                       ->orWhereRaw("LOWER(father_email) LIKE ?", ['%' . $lowerSearch . '%'])
                                       ->orWhereRaw("LOWER(guardian_email) LIKE ?", ['%' . $lowerSearch . '%'])
                                       ->orWhereRaw("LOWER(admission_number) LIKE ?", ['%' . $lowerSearch . '%']);

                                 if (count($terms) > 1) {
                                     $subSq->orWhere(function ($multiQ) use ($terms) {
                                         foreach ($terms as $term) {
                                             $lowerTerm = '%' . strtolower($term) . '%';
                                             $multiQ->where(function ($termQ) use ($lowerTerm) {
                                                 $termQ->whereRaw("LOWER(father_name) LIKE ?", [$lowerTerm])
                                                       ->orWhereRaw("LOWER(guardian_name) LIKE ?", [$lowerTerm])
                                                       ->orWhereRaw("LOWER(mother_name) LIKE ?", [$lowerTerm])
                                                       ->orWhereRaw("LOWER(father_phone) LIKE ?", [$lowerTerm])
                                                       ->orWhereRaw("LOWER(guardian_phone) LIKE ?", [$lowerTerm])
                                                       ->orWhereRaw("LOWER(father_email) LIKE ?", [$lowerTerm])
                                                       ->orWhereRaw("LOWER(guardian_email) LIKE ?", [$lowerTerm])
                                                       ->orWhereRaw("LOWER(admission_number) LIKE ?", [$lowerTerm]);
                                             });
                                         }
                                     });
                                 }
                             });
                    });
                });
            });

            // 4. Staff / Teacher User matches:
            $q->orWhere(function ($staffQ) use ($lowerSearch, $terms) {
                $staffQ->whereHas('staff', function ($sq) use ($lowerSearch, $terms) {
                    $concatExpr = self::getConcatNameExpr('');
                    $sq->where(function ($subSq) use ($concatExpr, $lowerSearch, $terms) {
                        $subSq->whereRaw("{$concatExpr} LIKE ?", ['%' . $lowerSearch . '%'])
                              ->orWhereRaw("LOWER(employee_id) LIKE ?", ['%' . $lowerSearch . '%'])
                              ->orWhereRaw("LOWER(email) LIKE ?", ['%' . $lowerSearch . '%'])
                              ->orWhereRaw("LOWER(phone) LIKE ?", ['%' . $lowerSearch . '%']);

                        if (count($terms) > 1) {
                            $subSq->orWhere(function ($multiQ) use ($terms) {
                                foreach ($terms as $term) {
                                    $lowerTerm = '%' . strtolower($term) . '%';
                                    $multiQ->where(function ($termQ) use ($lowerTerm) {
                                        $termQ->whereRaw("LOWER(first_name) LIKE ?", [$lowerTerm])
                                              ->orWhereRaw("LOWER(last_name) LIKE ?", [$lowerTerm])
                                              ->orWhereRaw("LOWER(employee_id) LIKE ?", [$lowerTerm])
                                              ->orWhereRaw("LOWER(email) LIKE ?", [$lowerTerm])
                                              ->orWhereRaw("LOWER(phone) LIKE ?", [$lowerTerm]);
                                    });
                                }
                            });
                        }
                    });
                });
            });
        });
    }


    /**
     * Get a typo-tolerant suggestion string when exact search returns 0 results.
     */
    public static function getStudentSuggestion(int $schoolId, ?string $search, ?int $classId = null, ?int $sectionId = null): ?string
    {
        $normalized = self::normalizeQuery($search);
        if (strlen($normalized) < 3) {
            return null;
        }

        $query = Student::where('school_id', $schoolId);
        if ($classId) {
            $query->where('class_id', $classId);
        }
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        // Fetch candidate full names
        $candidates = $query->select(['first_name', 'last_name'])
            ->get()
            ->map(function ($s) {
                return trim("{$s->first_name} {$s->last_name}");
            })
            ->filter()
            ->unique();

        $targetLower = strtolower($normalized);
        $bestMatch = null;
        $bestScore = 999;

        foreach ($candidates as $name) {
            $nameLower = strtolower($name);
            if ($nameLower === $targetLower) {
                return null; // Exact match exists, no suggestion needed
            }

            // Calculate Levenshtein distance
            $dist = levenshtein($targetLower, $nameLower);
            if ($dist >= 0 && $dist <= 3 && $dist < $bestScore) {
                $bestScore = $dist;
                $bestMatch = $name;
            } else {
                // Check similarity percentage
                similar_text($targetLower, $nameLower, $percent);
                if ($percent >= 70 && (100 - $percent) < $bestScore) {
                    $bestScore = 100 - $percent;
                    $bestMatch = $name;
                }
            }
        }

        return $bestMatch;
    }

    /**
     * Get typo suggestion for staff.
     */
    public static function getStaffSuggestion(int $schoolId, ?string $search): ?string
    {
        $normalized = self::normalizeQuery($search);
        if (strlen($normalized) < 3) {
            return null;
        }

        $candidates = Staff::where('school_id', $schoolId)
            ->select(['first_name', 'last_name'])
            ->get()
            ->map(function ($s) {
                return trim("{$s->first_name} {$s->last_name}");
            })
            ->filter()
            ->unique();

        $targetLower = strtolower($normalized);
        $bestMatch = null;
        $bestScore = 999;

        foreach ($candidates as $name) {
            $nameLower = strtolower($name);
            if ($nameLower === $targetLower) {
                return null;
            }

            $dist = levenshtein($targetLower, $nameLower);
            if ($dist >= 0 && $dist <= 3 && $dist < $bestScore) {
                $bestScore = $dist;
                $bestMatch = $name;
            } else {
                similar_text($targetLower, $nameLower, $percent);
                if ($percent >= 70 && (100 - $percent) < $bestScore) {
                    $bestScore = 100 - $percent;
                    $bestMatch = $name;
                }
            }
        }

        return $bestMatch;
    }
}

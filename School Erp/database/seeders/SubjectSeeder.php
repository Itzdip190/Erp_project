<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Database\Seeder;

/**
 * SubjectSeeder
 *
 * Seeds a comprehensive, class-appropriate subject list for every class
 * (Nursery → Class 12) in the school. Safe to re-run – uses firstOrCreate.
 *
 * Run with:
 *   php artisan db:seed --class=SubjectSeeder
 */
class SubjectSeeder extends Seeder
{
    // ─────────────────────────────────────────────────────────────────────────
    // Subject definitions per class group
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Subjects for early-childhood classes: Nursery, LKG, UKG
     */
    private array $earlyChildhood = [
        ['name' => 'English',         'code_suffix' => 'ENG',  'type' => 'theory',    'max' => 50,  'pass' => 17],
        ['name' => 'Hindi',           'code_suffix' => 'HIN',  'type' => 'theory',    'max' => 50,  'pass' => 17],
        ['name' => 'Mathematics',     'code_suffix' => 'MATH', 'type' => 'theory',    'max' => 50,  'pass' => 17],
        ['name' => 'EVS',             'code_suffix' => 'EVS',  'type' => 'theory',    'max' => 50,  'pass' => 17],
        ['name' => 'Drawing & Craft', 'code_suffix' => 'ART',  'type' => 'practical', 'max' => 50,  'pass' => 17],
        ['name' => 'GK & Value Edu',  'code_suffix' => 'GK',   'type' => 'theory',    'max' => 50,  'pass' => 17],
    ];

    /**
     * Subjects for primary classes: Class 1–5
     */
    private array $primary = [
        ['name' => 'English',            'code_suffix' => 'ENG',  'type' => 'theory',    'max' => 100, 'pass' => 33],
        ['name' => 'Hindi',              'code_suffix' => 'HIN',  'type' => 'theory',    'max' => 100, 'pass' => 33],
        ['name' => 'Mathematics',        'code_suffix' => 'MATH', 'type' => 'theory',    'max' => 100, 'pass' => 33],
        ['name' => 'Environmental Studies (EVS)', 'code_suffix' => 'EVS', 'type' => 'theory', 'max' => 100, 'pass' => 33],
        ['name' => 'General Knowledge',  'code_suffix' => 'GK',   'type' => 'theory',    'max' => 100, 'pass' => 33],
        ['name' => 'Computer',           'code_suffix' => 'COMP', 'type' => 'practical', 'max' => 100, 'pass' => 33],
        ['name' => 'Drawing',            'code_suffix' => 'DRAW', 'type' => 'practical', 'max' => 50,  'pass' => 17],
        ['name' => 'Moral Science',      'code_suffix' => 'MS',   'type' => 'theory',    'max' => 50,  'pass' => 17],
    ];

    /**
     * Subjects for middle-school classes: Class 6–8
     */
    private array $middle = [
        ['name' => 'English',         'code_suffix' => 'ENG',  'type' => 'theory',    'max' => 100, 'pass' => 33],
        ['name' => 'Hindi',           'code_suffix' => 'HIN',  'type' => 'theory',    'max' => 100, 'pass' => 33],
        ['name' => 'Mathematics',     'code_suffix' => 'MATH', 'type' => 'theory',    'max' => 100, 'pass' => 33],
        ['name' => 'Science',         'code_suffix' => 'SCI',  'type' => 'both',      'max' => 100, 'pass' => 33],
        ['name' => 'Social Science',  'code_suffix' => 'SST',  'type' => 'theory',    'max' => 100, 'pass' => 33],
        ['name' => 'Computer Science','code_suffix' => 'CS',   'type' => 'practical', 'max' => 100, 'pass' => 33],
        ['name' => 'Sanskrit',        'code_suffix' => 'SAN',  'type' => 'theory',    'max' => 100, 'pass' => 33],
        ['name' => 'General Knowledge','code_suffix' => 'GK',  'type' => 'theory',    'max' => 50,  'pass' => 17],
    ];

    /**
     * Subjects for secondary classes: Class 9–10
     */
    private array $secondary = [
        ['name' => 'English',         'code_suffix' => 'ENG',  'type' => 'theory',    'max' => 100, 'pass' => 33],
        ['name' => 'Hindi',           'code_suffix' => 'HIN',  'type' => 'theory',    'max' => 100, 'pass' => 33],
        ['name' => 'Mathematics',     'code_suffix' => 'MATH', 'type' => 'theory',    'max' => 100, 'pass' => 33],
        ['name' => 'Science',         'code_suffix' => 'SCI',  'type' => 'both',      'max' => 100, 'pass' => 33],
        ['name' => 'Social Science',  'code_suffix' => 'SST',  'type' => 'theory',    'max' => 100, 'pass' => 33],
        ['name' => 'Computer Science','code_suffix' => 'CS',   'type' => 'practical', 'max' => 100, 'pass' => 33],
        ['name' => 'Sanskrit',        'code_suffix' => 'SAN',  'type' => 'theory',    'max' => 100, 'pass' => 33],
        ['name' => 'Physical Education', 'code_suffix' => 'PE', 'type' => 'practical','max' => 100, 'pass' => 33],
    ];

    /**
     * Subjects for senior secondary Science stream: Class 11–12
     */
    private array $seniorScience = [
        ['name' => 'English Core',    'code_suffix' => 'ENG',  'type' => 'theory',    'max' => 100, 'pass' => 33],
        ['name' => 'Physics',         'code_suffix' => 'PHY',  'type' => 'both',      'max' => 100, 'pass' => 33],
        ['name' => 'Chemistry',       'code_suffix' => 'CHEM', 'type' => 'both',      'max' => 100, 'pass' => 33],
        ['name' => 'Mathematics',     'code_suffix' => 'MATH', 'type' => 'theory',    'max' => 100, 'pass' => 33],
        ['name' => 'Biology',         'code_suffix' => 'BIO',  'type' => 'both',      'max' => 100, 'pass' => 33],
        ['name' => 'Computer Science','code_suffix' => 'CS',   'type' => 'both',      'max' => 100, 'pass' => 33],
        ['name' => 'Physical Education', 'code_suffix' => 'PE','type' => 'practical', 'max' => 100, 'pass' => 33],
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Map class name  →  subject group & numeric prefix for code generation
    // ─────────────────────────────────────────────────────────────────────────
    private array $classMap = [
        'Nursery'  => ['group' => 'earlyChildhood', 'prefix' => 'NUR'],
        'LKG'      => ['group' => 'earlyChildhood', 'prefix' => 'LKG'],
        'UKG'      => ['group' => 'earlyChildhood', 'prefix' => 'UKG'],
        'Class 1'  => ['group' => 'primary',        'prefix' => 'C1'],
        'Class 2'  => ['group' => 'primary',        'prefix' => 'C2'],
        'Class 3'  => ['group' => 'primary',        'prefix' => 'C3'],
        'Class 4'  => ['group' => 'primary',        'prefix' => 'C4'],
        'Class 5'  => ['group' => 'primary',        'prefix' => 'C5'],
        'Class 6'  => ['group' => 'middle',         'prefix' => 'C6'],
        'Class 7'  => ['group' => 'middle',         'prefix' => 'C7'],
        'Class 8'  => ['group' => 'middle',         'prefix' => 'C8'],
        'Class 9'  => ['group' => 'secondary',      'prefix' => 'C9'],
        'Class 10' => ['group' => 'secondary',      'prefix' => 'C10'],
        'Class 11' => ['group' => 'seniorScience',  'prefix' => 'C11'],
        'Class 12' => ['group' => 'seniorScience',  'prefix' => 'C12'],
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // run()
    // ─────────────────────────────────────────────────────────────────────────
    public function run(): void
    {
        $school = School::where('code', 'YIS2024')->firstOrFail();

        $totalCreated = 0;
        $totalSkipped = 0;

        foreach ($this->classMap as $className => $config) {
            $class = SchoolClass::where('school_id', $school->id)
                ->where('name', $className)
                ->first();

            if (! $class) {
                $this->command->warn("  ⚠  Class '{$className}' not found – skipping.");
                continue;
            }

            // Resolve subject list
            $subjectList = $this->{$config['group']};
            $prefix      = $config['prefix'];
            $sortOrder   = 1;

            foreach ($subjectList as $subjectData) {
                $code = $prefix . '-' . $subjectData['code_suffix'];

                [$subject, $created] = [
                    Subject::firstOrCreate(
                        [
                            'school_id' => $school->id,
                            'class_id'  => $class->id,
                            'code'      => $code,
                        ],
                        [
                            'name'       => $subjectData['name'],
                            'type'       => $subjectData['type'],
                            'max_marks'  => $subjectData['max'],
                            'pass_marks' => $subjectData['pass'],
                            'sort_order' => $sortOrder,
                        ]
                    ),
                    ! Subject::where('school_id', $school->id)
                               ->where('class_id', $class->id)
                               ->where('code', $code)
                               ->exists(),
                ];

                if ($created) {
                    $totalCreated++;
                } else {
                    $totalSkipped++;
                }

                $sortOrder++;
            }

            $this->command->info(
                "  ✓  {$className} — seeded " . count($subjectList) . " subjects."
            );
        }

        $this->command->newLine();
        $this->command->info("Subject seeding complete.");
        $this->command->info("  Created : {$totalCreated}");
        $this->command->info("  Skipped : {$totalSkipped} (already existed)");
    }
}

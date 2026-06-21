<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            SchoolSeeder::class,
            PlanSeeder::class,
            SubscriptionSeeder::class,
            SuperAdminSeeder::class,
            SchoolAdminSeeder::class,
            DepartmentSeeder::class,
            DesignationSeeder::class,
            TeacherSeeder::class,
            AcademicSessionSeeder::class,
            ClassSectionSeeder::class,
            SubjectSeeder::class,
            ParentStudentSeeder::class,
            TestStudentSeeder::class,
            ImplDataImplementationSeeder::class,
            ImplTemplateImplementationSeeder::class,
            ImplIntegrationsSeeder::class,
            ImplTrainingSeeder::class,
        ]);
    }
}


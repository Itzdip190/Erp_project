# 🛡️ ERP SYSTEM COMPREHENSIVE CODE AUDIT & STABILITY LOG
**Generated On:** 2026-09-11  
**Project:** Enterprise Multi-Tenant School ERP  
**Scope:** Core Architecture, Multi-Tenancy Scoping, Database Schemas, Payroll & Salary Calculations, Fee Management, Routing, View Rendering, Push Notifications.

---

## 📋 Executive Summary
A comprehensive, multi-angle code inspection of the School ERP system was conducted to identify all failure points, runtime exceptions, database integrity errors, and calculation bugs causing data corruption or UI crashes.

This document serves as the **Master Defect & Remediation Log**. It details:
1. **Root Cause Analysis (RCA)** for each issue.
2. **Impact on Data & User Experience** (why wrong data appears or code breaks).
3. **Exact File Locations & Line Numbers**.
4. **Concrete Remediation Code** to make the ERP 100% stable.

---

## 🚨 Summary Table of Identified Defects

| ID | Module / Area | Severity | Defect Description | Data / System Impact |
|:---|:---|:---:|:---|:---|
| **BUG-01** | Multi-Tenancy Scoping | **CRITICAL** | Orphaned Models missing `BelongsToSchool` trait | Cross-school data leakage; wrong data displays across tenants |
| **BUG-02** | Query Engine / Global Scope | **CRITICAL** | Ambiguous `id` & `sort_order` in `SchoolClass` booted scope | SQLSTATE[23000] 1052 / 1054 crash on table joins |
| **BUG-03** | Database Migration / Schema | **CRITICAL** | `numeric_name` non-nullable without default on `school_classes` | SQLSTATE[HY000] 1364 crash when creating non-numeric classes |
| **BUG-04** | Push Notifications / FCM | **CRITICAL** | Unique constraint collision in `FcmDeviceToken` registration | SQLSTATE[23000] 1062 duplicate key crash on token refresh |
| **BUG-05** | Payroll & Compensation | **HIGH** | Allowance & Deduction blackout via structure relation mismatch | Staff allowances & deductions drop to 0.00; Net pay calculation wrong |
| **BUG-06** | Payroll & Attendance Deductions | **HIGH** | Hardcoded 30-day divisor for daily wage calculations | Distorted per-day salary deductions in 28, 29, and 31-day months |
| **BUG-07** | Fee Management / Receipts | **HIGH** | Cheque clearance random receipt numbers & race conditions | Receipt sequence corrupted; SQLSTATE[23000] duplicate receipt crashes |
| **BUG-08** | Routing Architecture | **HIGH** | Duplicate route group overwriting `parent.*` routes with `student` | Parent portal URLs resolve to `/student/*`, breaking navigation |
| **BUG-09** | Certificate & Card Views | **MEDIUM** | Unchecked `$school` properties (`udise_data`, `name`, `logo`) | Fatal PHP TypeError / Attempt to read property on null |
| **BUG-10** | Plan Licensing & Features | **MEDIUM** | Plan feature synchronization disconnected from `FeatureVisibilityHelper` | School subscription feature toggles fail to apply across portals |
| **BUG-11** | Leave Management | **MEDIUM** | Dynamic accessor `staff_type` queried as database column | Query crashes or returns empty sets on staff type filters |

---

## 🔍 Detailed Forensic Audit & Remediation Plan

---

### BUG-01: Multi-Tenancy Scoping Leakage (Missing `BelongsToSchool`)
- **Severity:** `CRITICAL`
- **Affected Files:**
  - `app/Models/LeaveType.php`
  - `app/Models/StaffLeaveApplication.php`
  - `app/Models/StaffLeaveBalance.php`
  - `app/Models/ReportCardHistory.php`
  - `app/Models/ReportCardHistoryStudent.php`
  - `app/Models/TeacherNotification.php`
  - `app/Models/Visitor.php`
- **Root Cause Analysis:**
  These models define `school_id` in their database tables and `$fillable` arrays, but **do not use the `App\Models\Traits\BelongsToSchool` trait**. In Laravel, whenever an Eloquent query is executed without explicitly chaining `->where('school_id', $schoolId)`, records from **all tenant schools** in the database are returned.
- **Why Wrong Data Appears:**
  - Leave balances and applications of staff from School B show up in School A's dashboard.
  - Report card histories and exam student rosters get intermingled across schools.
  - Visitors registered in one school are visible in other schools.
- **Remediation:**
  Add `use App\Models\Traits\BelongsToSchool;` and include `use BelongsToSchool;` inside each of the 7 models.
  ```php
  // Example for app/Models/LeaveType.php
  use App\Models\Traits\BelongsToSchool;
  
  class LeaveType extends Model
  {
      use HasFactory, BelongsToSchool;
      ...
  ```

---

### BUG-02: Ambiguous `id` & `sort_order` Query Exceptions in `SchoolClass`
- **Severity:** `CRITICAL`
- **Affected File:** `app/Models/SchoolClass.php` (Lines 26–31)
- **Current Code:**
  ```php
  protected static function booted()
  {
      static::addGlobalScope('order', function ($builder) {
          $builder->orderBy('sort_order')->orderBy('id');
      });
  }
  ```
- **Root Cause Analysis:**
  The global scope attaches `ORDER BY sort_order, id` to **every** query involving `SchoolClass`. When `SchoolClass` is joined with another table that also has an `id` column (e.g. `students`, `sections`, `timetable_group_class_section`, `class_wise_fees`), MySQL fails with:
  `SQLSTATE[23000]: 1052 Column 'id' in order clause is ambiguous` or `1054 Unknown column 'sort_order' in order clause`.
- **Why Code Breaks:**
  Any report, timetable lookup, or student list performing joins on `school_classes` crashes with an HTTP 500 error.
- **Remediation:**
  Qualify the column names with the model's table name:
  ```php
  protected static function booted()
  {
      static::addGlobalScope('order', function ($builder) {
          $table = $builder->getModel()->getTable();
          $builder->orderBy("{$table}.sort_order")->orderBy("{$table}.id");
      });
  }
  ```

---

### BUG-03: Non-Nullable `numeric_name` on `school_classes`
- **Severity:** `CRITICAL`
- **Affected File:** `database/migrations/2026_06_11_100005_create_school_classes_table.php` (Line 15)
- **Current Code:**
  ```php
  Schema::create('school_classes', function (Blueprint $table) {
      $table->id();
      $table->foreignId('school_id')->constrained()->cascadeOnDelete();
      $table->string('name');
      $table->integer('numeric_name'); // Non-nullable, no default
      $table->timestamps();
  });
  ```
- **Root Cause Analysis:**
  `numeric_name` was defined as non-nullable without a default value. When schools create pre-primary or non-standard classes (e.g. "Playgroup", "Nursery", "LKG", "UKG", "Activity Class") where no numeric value is provided, MySQL throws:
  `SQLSTATE[HY000]: 1364 Field 'numeric_name' doesn't have a default value`.
- **Remediation:**
  Create a migration modifying `numeric_name` to be nullable:
  ```php
  Schema::table('school_classes', function (Blueprint $table) {
      $table->integer('numeric_name')->nullable()->default(0)->change();
  });
  ```

---

### BUG-04: FCM Device Registration Unique Constraint Collision
- **Severity:** `CRITICAL`
- **Affected File:** `app/Http/Controllers/NotificationStreamController.php` (Lines 381–394)
- **Database Schema:** `fcm_device_tokens` table has unique constraint: `UNIQUE(['user_id', 'device_name'])`.
- **Current Code:**
  ```php
  \App\Models\FcmDeviceToken::updateOrCreate(
      [
          'school_id' => $schoolId,
          'user_id'   => $user?->id,
          'token'     => $token,
      ],
      [
          'device_name' => $deviceName,
          'platform'    => $platform,
          'updated_at'  => now(),
      ]
  );
  ```
- **Root Cause Analysis:**
  `updateOrCreate` uses `['school_id', 'user_id', 'token']` as the lookup criteria. When a user's FCM token refreshes on the client (browser or mobile app), the new token does not match any existing record, so Eloquent attempts an `INSERT` with `user_id` and `device_name` (e.g. "Web Browser"). Since a row for that `user_id` and `device_name` already exists, MySQL throws:
  `SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1-Web Browser' for key 'fcm_user_device_unique'`.
- **Why Code Breaks:**
  Every time a user logs in or their token auto-refreshes, the background push registration fails with a 500 error and fills `laravel.log` with hundreds of megabytes of stack traces.
- **Remediation:**
  Change the lookup criteria in `updateOrCreate` to match the table's unique key (`user_id` and `device_name`):
  ```php
  \App\Models\FcmDeviceToken::updateOrCreate(
      [
          'user_id'     => $user->id,
          'device_name' => $deviceName,
      ],
      [
          'school_id'   => $schoolId,
          'token'       => $token,
          'platform'    => $platform,
          'updated_at'  => now(),
      ]
  );
  ```

---

### BUG-05: Payroll Salary & Allowance Blackout
- **Severity:** `HIGH`
- **Affected File:** `app/Http/Controllers/School/PayrollController.php` (Lines 1241–1256)
- **Current Code:**
  ```php
  $struct = $staff->salaryStructure; // hasOne(StaffSalaryStructure::class)

  $basicSalary = $struct ? (float)$struct->basic_salary : (float)($staff->basic_salary ?: 0);
  if ($basicSalary <= 0) {
      continue;
  }

  $hra = $struct ? (float)($struct->hra ?: 0) : 0;
  $da = $struct ? (float)($struct->da ?: 0) : 0;
  $ta = $struct ? (float)($struct->ta ?: 0) : 0;
  $allowance = $struct ? (float)($struct->allowance ?: 0) : 0;
  $pf = $struct ? (float)($struct->pf ?: 0) : 0;
  $esi = $struct ? (float)($struct->esi ?: 0) : 0;
  $tds = $struct ? (float)($struct->tds ?: 0) : 0;
  $profTax = $struct ? (float)($struct->prof_tax ?: 0) : 0;
  ```
- **Root Cause Analysis:**
  The system has two ways a salary structure is linked to staff:
  1. `StaffSalaryStructure` (custom one-to-one record per staff).
  2. `assignedSalaryStructure` (foreign key `staff.salary_structure_id` pointing to master template `SalaryStructure`).
  `PayrollController::processGenerateSalary` only checks `$staff->salaryStructure`. If a staff member is assigned to a master salary structure via `staff.salary_structure_id`, `$staff->salaryStructure` is `null`.
- **Why Wrong Data Appears:**
  - HRA, DA, TA, Special Allowance all become `0.00`.
  - PF, ESI, TDS, Professional Tax all become `0.00`.
  - The staff member receives only basic salary with zero allowances and zero deductions, generating completely incorrect payroll slips and inaccurate school expense records.
- **Remediation:**
  Fallback to `$staff->assignedSalaryStructure`:
  ```php
  $struct = $staff->salaryStructure ?: $staff->assignedSalaryStructure;
  ```

---

### BUG-06: Fixed 30-Day Divisor for Daily Wage & Leave Deductions
- **Severity:** `HIGH`
- **Affected File:** `app/Http/Controllers/School/PayrollController.php` (Line 1267)
- **Current Code:**
  ```php
  // Fixed 30 Days Cycle Base Calculation: Daily Salary = Basic Salary / 30
  $dailySalary = round($basicSalary / 30, 4);
  $attendanceDeduction = round($extraLeaveDays * $dailySalary * $multiplier, 2);
  ```
- **Root Cause Analysis:**
  Using a constant divisor of 30 ignores the actual number of days in the payroll month:
  - In February (28 or 29 days), staff are under-deducted per day of unpaid absence.
  - In 31-day months (January, March, May, July, August, October, December), staff are over-deducted per day.
- **Remediation:**
  Use `$totalDaysInMonth`:
  ```php
  $daysInMonth = (int)($attDetails['days_in_month'] ?? Carbon::createFromFormat('Y-m', $payrollMonth)->daysInMonth);
  $dailySalary = round($basicSalary / max(1, $daysInMonth), 4);
  $attendanceDeduction = round($extraLeaveDays * $dailySalary * $multiplier, 2);
  ```

---

### BUG-07: Receipt Number Random Collisions & Concurrency Race Condition
- **Severity:** `HIGH`
- **Affected File:** `app/Http/Controllers/School/FeeManagementController.php` (Lines 6591 & 7056–7085)
- **Current Code:**
  ```php
  // Line 6591:
  $receiptNum = $cheque->receipt_number ?: ('REC-' . rand(100000, 999999));
  
  // Line 7062:
  $latestReceipt = \App\Models\FeeReceipt::withoutGlobalScope('active')
      ->where('school_id', $schoolId)
      ->where('receipt_number', 'like', $prefix . '-%')
      ->orderBy('id', 'desc')
      ->first();
  ```
- **Root Cause Analysis:**
  1. When a cheque is cleared without an existing receipt number, line 6591 assigns a pseudo-random number like `REC-789213`.
  2. Sequential receipt generator `generateNextReceiptNumber` parses the last receipt's numeric suffix (`789213`) and sets `$nextNum = 789214`. All subsequent normal receipts jump to `789214` instead of sequential `000045`.
  3. Concurrent receipt generations have no database lock. If two cashiers collect fees at the same time, both receive the exact same `$nextNum`. The second insertion crashes with:
     `SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'REC-00045' for key 'fee_receipts_receipt_number_unique'`.
- **Remediation:**
  1. Replace `rand(100000, 999999)` with `self::generateNextReceiptNumber($schoolId, $prefix)`.
  2. Implement retry logic and sequential locking or a dedicated receipt sequence table with atomic increments.

---

### BUG-08: Parent vs Student Route Group Overwrite
- **Severity:** `HIGH`
- **Affected File:** `bootstrap/app.php` (Lines 22–29)
- **Current Code:**
  ```php
  Route::middleware(['web', 'auth', 'role:parent|student', 'active_student', 'check.mobile_maintenance'])
      ->prefix('parent')
      ->group(base_path('routes/parent.php'));

  Route::middleware(['web', 'auth', 'role:parent|student', 'active_student', 'check.mobile_maintenance'])
      ->prefix('student')
      ->group(base_path('routes/parent.php'));
  ```
- **Root Cause Analysis:**
  `routes/parent.php` declares named routes such as `->name('parent.dashboard')`, `->name('parent.documents.download')`, etc.
  Because the file is included a second time under `prefix('student')`, Laravel registers the exact same route names twice. The second registration **overwrites** the first.
- **Why Code Breaks:**
  Calling `route('parent.dashboard')` anywhere in Blade or Controllers produces `/student/dashboard` instead of `/parent/dashboard`. When a Parent accesses the portal, links misroute them to student URLs.
- **Remediation:**
  Separate student routes into `routes/student.php` with distinct route names (`student.*`), or remove the duplicate group and let `routes/student.php` handle the student portal.

---

### BUG-09: Unchecked `$school` Properties in Certificate Views
- **Severity:** `MEDIUM`
- **Affected Files:**
  - `resources/views/school/certificates/report.blade.php` (Lines 7–13)
  - `resources/views/school/certificates/manage.blade.php` (Line 12)
  - `resources/views/school/certificates/template_edit.blade.php` (Line 12)
  - `resources/views/school/certificates/edit_issued.blade.php` (Line 12)
- **Current Code:**
  ```php
  $school = app()->bound('currentSchool') ? app('currentSchool') : auth()->user()->school;
  $logoUrl = ($school->logo && Storage::disk('public')->exists($school->logo)) ? Storage::disk('public')->url($school->logo) : '';
  $schoolName = $school->name;
  $directorName = $school->director_name ?? 'Principal';
  $udise = is_array($school->udise_data) ? $school->udise_data : json_decode($school->udise_data ?? '[]', true);
  ```
- **Root Cause Analysis:**
  If a user logs in as Superadmin (who has no direct `school` relation) or if an unlinked tenant session accesses certificate routes, `$school` evaluates to `null`. Direct property access (`$school->logo`, `$school->name`, `$school->udise_data`) causes a fatal PHP error:
  `Attempt to read property "name" on null` / `Attempt to read property "udise_data" on null`.
- **Remediation:**
  Use null-safe operators and sensible fallbacks:
  ```php
  $school = app()->bound('currentSchool') ? app('currentSchool') : auth()->user()?->school;
  $logoUrl = ($school?->logo && Storage::disk('public')->exists($school->logo)) ? Storage::disk('public')->url($school->logo) : '';
  $schoolName = $school?->name ?? 'School';
  $directorName = $school?->director_name ?? 'Principal';
  $udise = is_array($school?->udise_data) ? $school->udise_data : json_decode($school?->udise_data ?? '[]', true);
  ```

---

### BUG-10: Plan Feature Synchronization Disconnected from Feature Visibility
- **Severity:** `MEDIUM`
- **Affected Files:**
  - `app/Models/Plan.php` (`syncToSchool` method)
  - `app/Helpers/FeatureVisibilityHelper.php`
- **Root Cause Analysis:**
  When a Superadmin assigns or updates a subscription plan for a school, `Plan::syncToSchool` updates `$school->disabled_features` and `$school->disabled_modules`.
  However, `FeatureVisibilityHelper::isVisible` queries `SettingService` for master keys (`feat_{key}_master`), ignoring `$school->disabled_features`.
- **Why Wrong Data / Behavior Occurs:**
  Disabling a feature in a school's subscription plan does not hide the feature from navigation bars or protect routes, allowing schools on basic tiers to access premium features.
- **Remediation:**
  In `FeatureVisibilityHelper::isVisible`, check `$school->disabled_features` as a primary boundary before checking global settings.

---

### BUG-11: `staff_type` Query Mismatch on `Staff` Model
- **Severity:** `MEDIUM`
- **Affected Files:**
  - `app/Models/Staff.php`
  - `app/Http/Controllers/School/LeaveManagementController.php` (Line 344)
  - `app/Http/Controllers/School/Attendance/StaffAttendanceController.php`
- **Root Cause Analysis:**
  `Staff` does not have a physical `staff_type` column in the database; it is computed via an accessor `getStaffTypeAttribute()`.
  Where code calls `Staff::where('staff_type', ...)` directly against the MySQL database, MySQL errors out with `1054 Unknown column 'staff_type' in 'where clause'`.
- **Remediation:**
  Either add a persisted `staff_type` column to the `staff` table populated on save, or ensure all queries filter using relation scopes:
  ```php
  // Query by role / designation rather than non-existent staff_type column
  $query->where(function($q) use ($staffType) {
      if ($staffType === 'Teaching') {
          $q->whereHas('designation', fn($d) => $d->where('name', 'like', '%teacher%'))
            ->orWhereHas('user', fn($u) => $u->where('role', 'teacher'));
      }
      ...
  });
  ```

---

## 🛠️ Step-by-Step Stabilization Execution Roadmap

To achieve 100% stability across the entire ERP, implement the fixes in the following order:

1. **Step 1: Multi-Tenancy Scoping (BUG-01)**
   - Add `use BelongsToSchool;` to `LeaveType`, `StaffLeaveApplication`, `StaffLeaveBalance`, `ReportCardHistory`, `ReportCardHistoryStudent`, `TeacherNotification`, `Visitor`.
2. **Step 2: Fix Global Scope Column Ambiguity (BUG-02)**
   - Update `SchoolClass::booted()` to prefix `sort_order` and `id` with table name.
3. **Step 3: Fix Push Notification Unique Collisions (BUG-04)**
   - Update `NotificationStreamController::registerDevice` to search by `['user_id' => $user->id, 'device_name' => $deviceName]`.
4. **Step 4: Fix Payroll Calculations (BUG-05 & BUG-06)**
   - Update `PayrollController::processGenerateSalary` to inspect `$staff->salaryStructure ?: $staff->assignedSalaryStructure`.
   - Update daily wage calculation to divide by actual month days (`Carbon::createFromFormat('Y-m', $payrollMonth)->daysInMonth`).
5. **Step 5: Fix View Null Pointers (BUG-09)**
   - Add null-safe operators in certificate blade views (`$school?->udise_data`).
6. **Step 6: Fix Route Conflict (BUG-08)**
   - Separate student routes from parent routes to prevent route name collisions.
7. **Step 7: Database Migration for Non-Nullable Columns (BUG-03)**
   - Add migration to make `numeric_name` nullable on `school_classes`.

CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE INDEX "cache_expiration_index" on "cache"("expiration");
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE INDEX "cache_locks_expiration_index" on "cache_locks"("expiration");
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "permissions"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "guard_name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "permissions_name_guard_name_unique" on "permissions"(
  "name",
  "guard_name"
);
CREATE TABLE IF NOT EXISTS "roles"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "guard_name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "roles_name_guard_name_unique" on "roles"(
  "name",
  "guard_name"
);
CREATE TABLE IF NOT EXISTS "model_has_permissions"(
  "permission_id" integer not null,
  "model_type" varchar not null,
  "model_id" integer not null,
  foreign key("permission_id") references "permissions"("id") on delete cascade,
  primary key("permission_id", "model_id", "model_type")
);
CREATE INDEX "model_has_permissions_model_id_model_type_index" on "model_has_permissions"(
  "model_id",
  "model_type"
);
CREATE TABLE IF NOT EXISTS "model_has_roles"(
  "role_id" integer not null,
  "model_type" varchar not null,
  "model_id" integer not null,
  foreign key("role_id") references "roles"("id") on delete cascade,
  primary key("role_id", "model_id", "model_type")
);
CREATE INDEX "model_has_roles_model_id_model_type_index" on "model_has_roles"(
  "model_id",
  "model_type"
);
CREATE TABLE IF NOT EXISTS "role_has_permissions"(
  "permission_id" integer not null,
  "role_id" integer not null,
  foreign key("permission_id") references "permissions"("id") on delete cascade,
  foreign key("role_id") references "roles"("id") on delete cascade,
  primary key("permission_id", "role_id")
);
CREATE TABLE IF NOT EXISTS "schools"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "code" varchar not null,
  "custom_domain" varchar,
  "logo" varchar,
  "address" text,
  "phone" varchar,
  "dashboard_theme" varchar not null default 'blue',
  "status" varchar not null default 'trial',
  "sms_config" text,
  "late_grace_minutes" integer not null default '15',
  "staff_punch_in_start" time not null default '08:00:00',
  "staff_punch_in_end" time not null default '18:00:00',
  "created_at" datetime,
  "updated_at" datetime,
  "udise_data" text,
  "email" varchar
);
CREATE UNIQUE INDEX "schools_code_unique" on "schools"("code");
CREATE UNIQUE INDEX "schools_custom_domain_unique" on "schools"(
  "custom_domain"
);
CREATE TABLE IF NOT EXISTS "plans"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "price" numeric not null,
  "duration_days" integer not null default '30',
  "features" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "subscriptions"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "plan_id" integer not null,
  "subscription_ends_at" datetime,
  "status" varchar not null default 'active',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "subscription_orders"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "plan_id" integer not null,
  "amount" numeric not null,
  "gateway" varchar not null,
  "status" varchar not null default 'completed',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar,
  "mobile" varchar,
  "admission_id" varchar,
  "email_verified_at" datetime,
  "password" varchar not null,
  "role" varchar,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer,
  "is_active" tinyint(1) not null default '1',
  "phone" varchar,
  "photo" varchar,
  "last_login_at" datetime,
  foreign key("school_id") references "schools"("id") on delete set null
);
CREATE UNIQUE INDEX "users_admission_id_unique" on "users"("admission_id");
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE UNIQUE INDEX "users_mobile_unique" on "users"("mobile");
CREATE TABLE IF NOT EXISTS "personal_access_tokens"(
  "id" integer primary key autoincrement not null,
  "tokenable_type" varchar not null,
  "tokenable_id" integer not null,
  "name" text not null,
  "token" varchar not null,
  "abilities" text,
  "last_used_at" datetime,
  "expires_at" datetime,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "personal_access_tokens_tokenable_type_tokenable_id_index" on "personal_access_tokens"(
  "tokenable_type",
  "tokenable_id"
);
CREATE UNIQUE INDEX "personal_access_tokens_token_unique" on "personal_access_tokens"(
  "token"
);
CREATE INDEX "personal_access_tokens_expires_at_index" on "personal_access_tokens"(
  "expires_at"
);
CREATE TABLE IF NOT EXISTS "login_logs"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "email_attempted" varchar not null,
  "ip_address" varchar not null,
  "user_agent" text not null,
  "status" varchar check("status" in('success', 'failed')) not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "departments"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "name" varchar not null,
  "description" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "designations"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "name" varchar not null,
  "description" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "staff"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "user_id" integer,
  "employee_id" varchar not null,
  "first_name" varchar not null,
  "last_name" varchar not null,
  "email" varchar,
  "phone" varchar,
  "date_of_birth" date,
  "gender" varchar check("gender" in('male', 'female', 'other')),
  "blood_group" varchar,
  "address" text,
  "city" varchar,
  "state" varchar,
  "pincode" varchar,
  "department_id" integer not null,
  "designation_id" integer not null,
  "employment_type" varchar check("employment_type" in('permanent', 'contract', 'part_time')) not null default 'permanent',
  "qualification" varchar,
  "experience_years" integer not null default '0',
  "photo" varchar,
  "joining_date" date not null,
  "basic_salary" numeric not null default '0',
  "bank_account_number" varchar,
  "bank_name" varchar,
  "ifsc_code" varchar,
  "pan_number" varchar,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete set null,
  foreign key("department_id") references "departments"("id") on delete cascade,
  foreign key("designation_id") references "designations"("id") on delete cascade
);
CREATE UNIQUE INDEX "staff_school_employee_unique" on "staff"(
  "school_id",
  "employee_id"
);
CREATE TABLE IF NOT EXISTS "school_classes"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "name" varchar not null,
  "numeric_name" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sections"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "class_id" integer not null,
  "name" varchar not null,
  "class_teacher_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("class_id") references "school_classes"("id") on delete cascade,
  foreign key("class_teacher_id") references "staff"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "student_categories"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "name" varchar not null,
  "description" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "student_houses"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "name" varchar not null,
  "color_code" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "academic_sessions"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "name" varchar not null,
  "start_date" date not null,
  "end_date" date not null,
  "is_current" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "subjects"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "class_id" integer not null,
  "name" varchar not null,
  "code" varchar not null,
  "type" varchar check("type" in('theory', 'practical', 'both')) not null,
  "max_marks" integer not null default '100',
  "pass_marks" integer not null default '33',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("class_id") references "school_classes"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "section_subject_staff"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "section_id" integer not null,
  "subject_id" integer not null,
  "staff_id" integer not null,
  "academic_session_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("section_id") references "sections"("id") on delete cascade,
  foreign key("subject_id") references "subjects"("id") on delete cascade,
  foreign key("staff_id") references "staff"("id") on delete cascade,
  foreign key("academic_session_id") references "academic_sessions"("id") on delete cascade
);
CREATE UNIQUE INDEX "sss_section_subject_session_unique" on "section_subject_staff"(
  "section_id",
  "subject_id",
  "academic_session_id"
);
CREATE TABLE IF NOT EXISTS "staff_attendances"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "staff_id" integer not null,
  "date" date not null,
  "status" varchar check("status" in('present', 'absent', 'late', 'half_day', 'holiday', 'leave')) not null,
  "clock_in_at" time,
  "clock_out_at" time,
  "attendance_type" varchar check("attendance_type" in('manual', 'biometric', 'gps')) not null,
  "latitude" numeric,
  "longitude" numeric,
  "marked_by" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("staff_id") references "staff"("id") on delete cascade,
  foreign key("marked_by") references "users"("id") on delete set null
);
CREATE UNIQUE INDEX "staffatt_school_staff_date_unique" on "staff_attendances"(
  "school_id",
  "staff_id",
  "date"
);
CREATE TABLE IF NOT EXISTS "fcm_device_tokens"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "school_id" integer not null,
  "token" text not null,
  "device_name" varchar not null,
  "platform" varchar check("platform" in('android', 'ios')) not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "fcm_user_device_unique" on "fcm_device_tokens"(
  "user_id",
  "device_name"
);
CREATE TABLE IF NOT EXISTS "otp_logins"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "user_id" integer not null,
  "phone" varchar not null,
  "otp" varchar not null,
  "expires_at" datetime not null,
  "used_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "otp_logins_school_id_phone_otp_index" on "otp_logins"(
  "school_id",
  "phone",
  "otp"
);
CREATE TABLE IF NOT EXISTS "face_vectors"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "user_id" integer not null,
  "encoding" text not null,
  "photo_path" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "face_vectors_school_id_index" on "face_vectors"("school_id");
CREATE TABLE IF NOT EXISTS "import_logs"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "job_id" varchar,
  "file_path" varchar not null,
  "total_rows" integer not null default '0',
  "success_rows" integer not null default '0',
  "failed_rows" integer not null default '0',
  "errors" text,
  "status" varchar check("status" in('pending', 'processing', 'completed', 'failed')) not null default 'pending',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "students"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "user_id" integer,
  "admission_number" varchar not null,
  "admission_sequence" integer not null default '0',
  "admission_year" integer,
  "roll_number" varchar,
  "first_name" varchar not null,
  "last_name" varchar not null,
  "date_of_birth" date not null,
  "gender" varchar check("gender" in('male', 'female', 'other')) not null,
  "blood_group" varchar,
  "religion" varchar,
  "caste" varchar,
  "category_id" integer,
  "house_id" integer,
  "photo" varchar,
  "guardian_name" varchar not null,
  "guardian_phone" varchar not null,
  "guardian_email" varchar,
  "guardian_relationship" varchar check("guardian_relationship" in('father', 'mother', 'guardian')) not null,
  "address" text not null,
  "city" varchar not null,
  "state" varchar not null,
  "pincode" varchar not null,
  "section_id" integer not null,
  "class_id" integer not null,
  "academic_session_id" integer not null,
  "admission_date" date not null,
  "is_active" tinyint(1) not null default '1',
  "opening_due_balance" numeric not null default '0',
  "custom_fields" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete set null,
  foreign key("category_id") references "student_categories"("id") on delete set null,
  foreign key("house_id") references "student_houses"("id") on delete set null,
  foreign key("section_id") references "sections"("id") on delete cascade,
  foreign key("class_id") references "school_classes"("id") on delete cascade,
  foreign key("academic_session_id") references "academic_sessions"("id") on delete cascade
);
CREATE UNIQUE INDEX "students_school_admission_unique" on "students"(
  "school_id",
  "admission_number"
);
CREATE INDEX "students_school_guardian_email_index" on "students"(
  "school_id",
  "guardian_email"
);
CREATE UNIQUE INDEX "students_school_sequence_year_unique" on "students"(
  "school_id",
  "admission_sequence",
  "admission_year"
);
CREATE TABLE IF NOT EXISTS "student_sessions"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "student_id" integer not null,
  "class_id" integer not null,
  "section_id" integer not null,
  "academic_session_id" integer not null,
  "roll_number" varchar,
  "is_promoted" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("student_id") references "students"("id") on delete cascade,
  foreign key("class_id") references "school_classes"("id") on delete cascade,
  foreign key("section_id") references "sections"("id") on delete cascade,
  foreign key("academic_session_id") references "academic_sessions"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "student_attendances"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "student_id" integer not null,
  "section_id" integer not null,
  "class_id" integer not null,
  "academic_session_id" integer not null,
  "date" date not null,
  "status" varchar check("status" in('present', 'absent', 'late', 'half_day', 'holiday', 'leave')) not null,
  "marked_by" integer not null,
  "remark" varchar,
  "attendance_type" varchar check("attendance_type" in('manual', 'biometric', 'qr', 'face')) not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("student_id") references "students"("id") on delete cascade,
  foreign key("section_id") references "sections"("id") on delete cascade,
  foreign key("class_id") references "school_classes"("id") on delete cascade,
  foreign key("academic_session_id") references "academic_sessions"("id") on delete cascade,
  foreign key("marked_by") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "sa_school_student_date_unique" on "student_attendances"(
  "school_id",
  "student_id",
  "date"
);
CREATE INDEX "sa_school_section_date_index" on "student_attendances"(
  "school_id",
  "section_id",
  "date"
);
CREATE INDEX "sa_school_student_date_index" on "student_attendances"(
  "school_id",
  "student_id",
  "date"
);
CREATE TABLE IF NOT EXISTS "student_documents"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "student_id" integer not null,
  "document_type" varchar not null,
  "file_path" varchar not null,
  "original_name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("student_id") references students("id") on delete cascade on update no action,
  foreign key("school_id") references schools("id") on delete cascade on update no action
);
CREATE TABLE IF NOT EXISTS "timetables"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "class_id" integer not null,
  "section_id" integer not null,
  "day_of_week" varchar not null,
  "start_time" varchar not null,
  "end_time" varchar not null,
  "subject_id" integer not null,
  "staff_id" integer not null,
  "room_number" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("class_id") references "school_classes"("id") on delete cascade,
  foreign key("section_id") references "sections"("id") on delete cascade,
  foreign key("subject_id") references "subjects"("id") on delete cascade,
  foreign key("staff_id") references "staff"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "timetable_substitutions"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "date" date not null,
  "timetable_id" integer not null,
  "original_staff_id" integer not null,
  "substitute_staff_id" integer not null,
  "status" varchar not null default 'active',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("timetable_id") references "timetables"("id") on delete cascade,
  foreign key("original_staff_id") references "staff"("id") on delete cascade,
  foreign key("substitute_staff_id") references "staff"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "fee_categories"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "name" varchar not null,
  "description" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "fee_structures"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "class_id" integer not null,
  "fee_category_id" integer not null,
  "amount" numeric not null,
  "schedule_type" varchar not null default 'monthly',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("class_id") references "school_classes"("id") on delete cascade,
  foreign key("fee_category_id") references "fee_categories"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "student_fees"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "student_id" integer not null,
  "fee_category_id" integer not null,
  "amount" numeric not null,
  "due_date" date not null,
  "paid_amount" numeric not null default '0',
  "status" varchar not null default 'pending',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("student_id") references "students"("id") on delete cascade,
  foreign key("fee_category_id") references "fee_categories"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "fee_receipts"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "student_id" integer not null,
  "receipt_number" varchar not null,
  "amount_paid" numeric not null,
  "payment_mode" varchar not null default 'cash',
  "transaction_id" varchar,
  "payment_date" date not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("student_id") references "students"("id") on delete cascade
);
CREATE UNIQUE INDEX "fee_receipts_receipt_number_unique" on "fee_receipts"(
  "receipt_number"
);
CREATE TABLE IF NOT EXISTS "pending_cheques"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "student_id" integer not null,
  "bank_name" varchar not null,
  "cheque_number" varchar not null,
  "amount" numeric not null,
  "cheque_date" date not null,
  "status" varchar not null default 'pending',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("student_id") references "students"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "payment_links"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "student_id" integer not null,
  "amount" numeric not null,
  "purpose" varchar not null,
  "link_url" varchar not null,
  "status" varchar not null default 'active',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("student_id") references "students"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "fee_refunds"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "student_id" integer not null,
  "amount" numeric not null,
  "refund_date" date not null,
  "reason" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("student_id") references "students"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "optional_fee_mappings"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "student_id" integer not null,
  "fee_category_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("student_id") references "students"("id") on delete cascade,
  foreign key("fee_category_id") references "fee_categories"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "card_templates"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "name" varchar not null,
  "type" varchar not null,
  "background_color" varchar not null default '#1a1f3c',
  "text_color" varchar not null default '#ffffff',
  "layout_style" varchar not null default 'classic',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "student_cards"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "student_id" integer not null,
  "card_template_id" integer not null,
  "card_number" varchar not null,
  "expiry_date" date not null,
  "status" varchar not null default 'active',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("student_id") references "students"("id") on delete cascade,
  foreign key("card_template_id") references "card_templates"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "digital_diaries"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "class_id" integer not null,
  "section_id" integer not null,
  "staff_id" integer not null,
  "title" varchar not null,
  "content" text not null,
  "diary_date" date not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("class_id") references "school_classes"("id") on delete cascade,
  foreign key("section_id") references "sections"("id") on delete cascade,
  foreign key("staff_id") references "staff"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "events"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "title" varchar not null,
  "description" text,
  "start_date" date not null,
  "end_date" date not null,
  "is_holiday" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "certificate_templates"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "name" varchar not null,
  "type" varchar not null,
  "title_text" varchar not null,
  "body_text" text not null,
  "background_image" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "student_certificates"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "student_id" integer not null,
  "certificate_template_id" integer not null,
  "certificate_number" varchar not null,
  "issue_date" date not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("student_id") references "students"("id") on delete cascade,
  foreign key("certificate_template_id") references "certificate_templates"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "leave_applications"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "user_id" integer not null,
  "applicant_type" varchar not null,
  "leave_type" varchar not null,
  "start_date" date not null,
  "end_date" date not null,
  "reason" text not null,
  "status" varchar not null default 'pending',
  "approved_by" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "notices"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "title" varchar not null,
  "content" text not null,
  "target_audience" varchar not null default 'all',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "surveys"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "question" varchar not null,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "survey_options"(
  "id" integer primary key autoincrement not null,
  "survey_id" integer not null,
  "option_text" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("survey_id") references "surveys"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "survey_responses"(
  "id" integer primary key autoincrement not null,
  "survey_id" integer not null,
  "survey_option_id" integer not null,
  "user_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("survey_id") references "surveys"("id") on delete cascade,
  foreign key("survey_option_id") references "survey_options"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "student_marks"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "student_id" integer not null,
  "subject_id" integer not null,
  "exam_name" varchar not null,
  "marks_obtained" numeric not null,
  "max_marks" numeric not null,
  "grade" varchar,
  "remarks" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("student_id") references "students"("id") on delete cascade,
  foreign key("subject_id") references "subjects"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "chat_messages"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "sender_id" integer not null,
  "receiver_id" integer not null,
  "message" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("sender_id") references "users"("id") on delete cascade,
  foreign key("receiver_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "enquiry_leads"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "student_name" varchar not null,
  "parent_name" varchar not null,
  "phone" varchar not null,
  "email" varchar,
  "class_interested" varchar,
  "status" varchar not null default 'new',
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "impl_data_implementation"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "module_name" varchar not null,
  "attachments" text,
  "uploaded_by" varchar,
  "data_received_date" datetime,
  "data_implemented_on" datetime,
  "tat" varchar,
  "owner_school_side" varchar,
  "confirmation_school_side" varchar,
  "status" varchar not null default 'Pending',
  "comment" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "impl_template_implementation"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "template_name" varchar not null,
  "important_dates" date,
  "template_received_attachment" text,
  "uploaded_by_1" varchar,
  "template_received_on" datetime,
  "implemented_template_attachment" text,
  "uploaded_by_2" varchar,
  "template_implemented_on" datetime,
  "owner_school_side" varchar,
  "confirmation_school_side" varchar,
  "status" varchar not null default 'Pending',
  "comment" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "impl_integrations"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "integration_name" varchar not null,
  "company" varchar,
  "serial_number" varchar,
  "vendor_contact_details" varchar,
  "api_received_on" datetime,
  "implemented_on" datetime,
  "tat" varchar,
  "owner_school_side" varchar,
  "confirmation_school_side" varchar,
  "status" varchar not null default 'Pending',
  "comment" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "impl_training"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "module_name" varchar not null,
  "training_done_on" datetime,
  "training_given_to" text,
  "minutes_of_meeting" text,
  "attachments" text,
  "uploaded_by" varchar,
  "owner_school_side" varchar,
  "confirmation_school_side" varchar,
  "status" varchar not null default 'Pending',
  "comment" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "impl_activity_logs"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "tab_name" varchar not null,
  "row_reference" varchar not null,
  "field_changed" varchar not null,
  "old_value" text,
  "new_value" text,
  "changed_by" varchar not null,
  "changed_at" datetime not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "module_permissions"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "module_key" varchar not null,
  "feature_key" varchar not null,
  "view_access" tinyint(1) not null default '0',
  "edit_access" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade
);
CREATE UNIQUE INDEX "module_permissions_school_id_module_key_feature_key_unique" on "module_permissions"(
  "school_id",
  "module_key",
  "feature_key"
);
CREATE INDEX "module_permissions_school_id_module_key_index" on "module_permissions"(
  "school_id",
  "module_key"
);
CREATE TABLE IF NOT EXISTS "staff_module_access"(
  "id" integer primary key autoincrement not null,
  "school_id" integer not null,
  "user_id" integer not null,
  "module_key" varchar not null,
  "feature_key" varchar not null,
  "view_access" tinyint(1) not null default '0',
  "edit_access" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "schools"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "staff_module_access_school_id_user_id_module_key_feature_key_unique" on "staff_module_access"(
  "school_id",
  "user_id",
  "module_key",
  "feature_key"
);
CREATE INDEX "staff_module_access_school_id_module_key_feature_key_index" on "staff_module_access"(
  "school_id",
  "module_key",
  "feature_key"
);

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2026_06_05_033249_create_permission_tables',1);
INSERT INTO migrations VALUES(5,'2026_06_05_033400_create_schools_table',1);
INSERT INTO migrations VALUES(6,'2026_06_05_033401_create_plans_table',1);
INSERT INTO migrations VALUES(7,'2026_06_05_033402_create_subscriptions_table',1);
INSERT INTO migrations VALUES(8,'2026_06_05_033403_create_subscription_orders_table',1);
INSERT INTO migrations VALUES(9,'2026_06_11_000001_add_fields_to_users_table',1);
INSERT INTO migrations VALUES(10,'2026_06_11_062359_create_personal_access_tokens_table',1);
INSERT INTO migrations VALUES(11,'2026_06_11_100001_create_login_logs_table',1);
INSERT INTO migrations VALUES(12,'2026_06_11_100002_create_departments_table',1);
INSERT INTO migrations VALUES(13,'2026_06_11_100003_create_designations_table',1);
INSERT INTO migrations VALUES(14,'2026_06_11_100004_create_staff_table',1);
INSERT INTO migrations VALUES(15,'2026_06_11_100005_create_school_classes_table',1);
INSERT INTO migrations VALUES(16,'2026_06_11_100006_create_sections_table',1);
INSERT INTO migrations VALUES(17,'2026_06_11_100007_create_student_categories_table',1);
INSERT INTO migrations VALUES(18,'2026_06_11_100008_create_student_houses_table',1);
INSERT INTO migrations VALUES(19,'2026_06_11_100009_create_academic_sessions_table',1);
INSERT INTO migrations VALUES(20,'2026_06_11_100010_create_subjects_table',1);
INSERT INTO migrations VALUES(21,'2026_06_11_100011_create_section_subject_staff_table',1);
INSERT INTO migrations VALUES(22,'2026_06_11_100015_create_staff_attendances_table',1);
INSERT INTO migrations VALUES(23,'2026_06_11_100016_create_fcm_device_tokens_table',1);
INSERT INTO migrations VALUES(24,'2026_06_11_100017_create_otp_logins_table',1);
INSERT INTO migrations VALUES(25,'2026_06_11_100018_create_face_vectors_table',1);
INSERT INTO migrations VALUES(26,'2026_06_11_100019_create_import_logs_table',1);
INSERT INTO migrations VALUES(27,'2026_06_11_100020_create_students_table',1);
INSERT INTO migrations VALUES(28,'2026_06_11_100021_create_student_sessions_table',1);
INSERT INTO migrations VALUES(29,'2026_06_11_100022_create_student_documents_table',1);
INSERT INTO migrations VALUES(30,'2026_06_11_100023_create_student_attendances_table',1);
INSERT INTO migrations VALUES(31,'2026_06_15_000000_change_student_documents_type_column',1);
INSERT INTO migrations VALUES(32,'2026_06_15_000001_add_udise_data_to_schools_table',1);
INSERT INTO migrations VALUES(33,'2026_06_18_034144_create_timetables_table',2);
INSERT INTO migrations VALUES(34,'2026_06_18_034145_create_timetable_substitutions_table',2);
INSERT INTO migrations VALUES(35,'2026_06_18_044230_create_fee_management_tables',3);
INSERT INTO migrations VALUES(36,'2026_06_18_052800_create_more_management_tables',4);
INSERT INTO migrations VALUES(37,'2026_06_19_000000_create_new_erp_modules_tables',5);
INSERT INTO migrations VALUES(38,'2026_06_19_000001_add_nursery_to_class12',6);
INSERT INTO migrations VALUES(39,'2026_06_21_000001_create_impl_data_implementation_table',7);
INSERT INTO migrations VALUES(40,'2026_06_21_000002_create_impl_template_implementation_table',7);
INSERT INTO migrations VALUES(41,'2026_06_21_000003_create_impl_integrations_table',7);
INSERT INTO migrations VALUES(42,'2026_06_21_000004_create_impl_training_table',7);
INSERT INTO migrations VALUES(43,'2026_06_21_000005_create_impl_activity_logs_table',7);
INSERT INTO migrations VALUES(44,'2026_06_21_100001_create_module_permissions_table',8);

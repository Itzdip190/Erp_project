<?php

use App\Http\Controllers\School\Student\StudentController;
use App\Http\Controllers\School\Student\StudentIdCardController;
use App\Http\Controllers\School\Student\AdmitCardController;
use App\Http\Controllers\School\Student\CertificateController;
use App\Http\Controllers\School\Attendance\StudentAttendanceController;
use App\Http\Controllers\School\Attendance\StaffAttendanceController;
use App\Http\Controllers\School\SchoolDashboardController;
use App\Http\Controllers\School\SettingsController;
use App\Http\Controllers\School\ClassAssignmentController;
use App\Http\Controllers\School\TimetableController;
use App\Http\Controllers\School\StudentManagementController;
use App\Http\Controllers\School\DownloadStatisticsController;
use App\Http\Controllers\School\FeeManagementController;
use App\Http\Controllers\School\CardManagementController;
use App\Http\Controllers\School\DigitalDiaryController;
use App\Http\Controllers\School\EventHolidayController;
use App\Http\Controllers\School\CertificateManagementController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/dashboard', [SchoolDashboardController::class, 'index'])->name('school.dashboard');
Route::get('/dashboard/chart/fee',    [SchoolDashboardController::class, 'feeChartData'])->name('school.dashboard.chart.fee');
Route::get('/dashboard/chart/attend', [SchoolDashboardController::class, 'attendanceChartData'])->name('school.dashboard.chart.attend');
Route::get('/dashboard/snapshot',     [SchoolDashboardController::class, 'snapshot'])->name('school.dashboard.snapshot');
Route::post('/chatbot/send',          [SchoolDashboardController::class, 'chatbotStub'])->name('school.chatbot.send');

// Students Module
Route::middleware(['check.module:students'])->group(function () {
    Route::get('/students/import-template', [StudentController::class, 'downloadTemplate'])->name('school.students.import-template');
    Route::post('/students/import', [StudentController::class, 'bulkImport'])->name('school.students.import');
    Route::get('/students/export', [StudentController::class, 'export'])->name('school.students.export');
    Route::get('/students/promote', [StudentController::class, 'promoteForm'])->name('school.students.promote-form');
    Route::post('/students/promote', [StudentController::class, 'promote'])->name('school.students.promote');

    Route::resource('students', StudentController::class)->names([
        'index' => 'school.students.index',
        'create' => 'school.students.create',
        'store' => 'school.students.store',
        'show' => 'school.students.show',
        'edit' => 'school.students.edit',
        'update' => 'school.students.update',
        'destroy' => 'school.students.destroy',
    ]);

    Route::get('/students/{student}/id-card', [StudentIdCardController::class, 'generate'])->name('school.students.id-card');
    Route::get('/students/{student}/admit-card', [AdmitCardController::class, 'generate'])->name('school.students.admit-card');
    Route::get('/students/{student}/certificate/{type}', [CertificateController::class, 'generate'])->name('school.students.certificate');
    Route::post('/students/{student}/issue-document', [StudentController::class, 'issueDocument'])->name('school.students.issue-document');
    Route::post('/students/bulk-issue-document', [StudentController::class, 'bulkIssueDocuments'])->name('school.students.bulk-issue-document');
});

// Attendance Module
Route::middleware(['check.module:attendance'])->group(function () {
    // Student Attendance
    Route::get('/attendance/students', [StudentAttendanceController::class, 'index'])->name('school.attendance.students.index');
    Route::post('/attendance/students/load', [StudentAttendanceController::class, 'loadSection'])->name('school.attendance.students.load');
    Route::post('/attendance/students', [StudentAttendanceController::class, 'store'])->name('school.attendance.students.store');
    Route::get('/attendance/students/report', [StudentAttendanceController::class, 'report'])->name('school.attendance.students.report');
    Route::get('/attendance/students/daily', [StudentAttendanceController::class, 'dailyReport'])->name('school.attendance.students.daily');
    Route::get('/attendance/students/stats', [StudentAttendanceController::class, 'stats'])->name('school.attendance.students.stats');

    // Staff Attendance
    Route::get('/attendance/staff', [StaffAttendanceController::class, 'index'])->name('school.attendance.staff.index');
    Route::post('/attendance/staff', [StaffAttendanceController::class, 'store'])->name('school.attendance.staff.store');
    Route::get('/attendance/staff/report', [StaffAttendanceController::class, 'report'])->name('school.attendance.staff.report');
});

// Settings & Profile
Route::get('/settings', [SettingsController::class, 'index'])->name('school.settings.index');
Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('school.settings.profile');
Route::put('/settings/password', [SettingsController::class, 'changePassword'])->name('school.settings.password');

// Overview Features
Route::get('/dashboard/mis-report', [SchoolDashboardController::class, 'misReport'])->name('school.dashboard.mis-report');

// Settings & Institute Info
Route::get('/settings/institute-info', [SettingsController::class, 'instituteInfo'])->name('school.settings.institute-info');
Route::put('/settings/institute-info', [SettingsController::class, 'updateInstituteInfo'])->name('school.settings.institute-info.update');
Route::get('/settings/implementation', [SettingsController::class, 'implementationProcess'])->name('school.settings.implementation');
Route::get('/settings/udise', [SettingsController::class, 'udise'])->name('school.settings.udise');
Route::put('/settings/udise', [SettingsController::class, 'updateUdise'])->name('school.settings.udise.update');
Route::get('/settings/reset-password', [SettingsController::class, 'resetPasswordPage'])->name('school.settings.reset-password');
Route::post('/settings/reset-password', [SettingsController::class, 'resetPassword'])->name('school.settings.reset-password.post');

// Role Management Features
Route::get('/role-management/roles', [\App\Http\Controllers\School\RoleController::class, 'index'])->name('school.roles.index');
Route::get('/role-management/staff-access', [\App\Http\Controllers\School\RoleController::class, 'staffAccess'])->name('school.roles.staff-access');
Route::put('/role-management/staff-access/{user}', [\App\Http\Controllers\School\RoleController::class, 'updateStaffAccess'])->name('school.roles.staff-access.update');

// Staff Management CRUD & Additional Features
Route::get('/staff/import', [\App\Http\Controllers\School\StaffController::class, 'importForm'])->name('school.staff.import');
Route::post('/staff/import', [\App\Http\Controllers\School\StaffController::class, 'bulkImport'])->name('school.staff.import.post');
Route::get('/staff/bulk-photo', [\App\Http\Controllers\School\StaffController::class, 'bulkPhotoForm'])->name('school.staff.bulk-photo');
Route::post('/staff/bulk-photo', [\App\Http\Controllers\School\StaffController::class, 'bulkPhotoUpload'])->name('school.staff.bulk-photo.post');
Route::get('/staff/bulk-attendance', [\App\Http\Controllers\School\StaffController::class, 'bulkAttendance'])->name('school.staff.bulk-attendance');
Route::post('/staff/bulk-attendance', [\App\Http\Controllers\School\StaffController::class, 'saveBulkAttendance'])->name('school.staff.bulk-attendance.post');

Route::resource('staff', \App\Http\Controllers\School\StaffController::class)->names([
    'index' => 'school.staff.index',
    'create' => 'school.staff.create',
    'store' => 'school.staff.store',
    'show' => 'school.staff.show',
    'edit' => 'school.staff.edit',
    'update' => 'school.staff.update',
    'destroy' => 'school.staff.destroy',
]);

// Class, Subject & Teacher Assignment Module Routes
Route::get('/assignments/class-overview', [ClassAssignmentController::class, 'classOverview'])->name('school.assignments.class-overview');
Route::get('/assignments/classes', [ClassAssignmentController::class, 'classesForm'])->name('school.assignments.classes');
Route::post('/assignments/classes', [ClassAssignmentController::class, 'storeClass'])->name('school.assignments.classes.store');
Route::post('/assignments/sections', [ClassAssignmentController::class, 'storeSection'])->name('school.assignments.sections.store');
Route::delete('/assignments/classes/{class}', [ClassAssignmentController::class, 'destroyClass'])->name('school.assignments.classes.destroy');
Route::delete('/assignments/sections/{section}', [ClassAssignmentController::class, 'destroySection'])->name('school.assignments.sections.destroy');

Route::get('/assignments/subjects', [ClassAssignmentController::class, 'subjectsForm'])->name('school.assignments.subjects');
Route::post('/assignments/subjects', [ClassAssignmentController::class, 'storeSubject'])->name('school.assignments.subjects.store');
Route::delete('/assignments/subjects/{subject}', [ClassAssignmentController::class, 'destroySubject'])->name('school.assignments.subjects.destroy');

Route::get('/assignments/teachers', [ClassAssignmentController::class, 'teachersForm'])->name('school.assignments.teachers');
Route::post('/assignments/teachers', [ClassAssignmentController::class, 'storeAssignment'])->name('school.assignments.teachers.store');
Route::post('/assignments/sections/{section}/class-teacher', [ClassAssignmentController::class, 'updateClassTeacher'])->name('school.assignments.class-teacher.update');
Route::delete('/assignments/teachers/{assignment}', [ClassAssignmentController::class, 'destroyAssignment'])->name('school.assignments.teachers.destroy');

// Timetable Administration Routes
Route::get('/timetable/class', [TimetableController::class, 'classTimetable'])->name('school.timetable.class');
Route::post('/timetable/class', [TimetableController::class, 'storeClassTimetable'])->name('school.timetable.class.store');
Route::delete('/timetable/class/{timetable}', [TimetableController::class, 'destroyClassTimetable'])->name('school.timetable.class.destroy');
Route::get('/timetable/group', [TimetableController::class, 'groupTimetable'])->name('school.timetable.group');
Route::get('/timetable/teacher', [TimetableController::class, 'teacherTimetable'])->name('school.timetable.teacher');
Route::get('/timetable/substitution', [TimetableController::class, 'teacherSubstitution'])->name('school.timetable.substitution');
Route::post('/timetable/substitution', [TimetableController::class, 'storeSubstitution'])->name('school.timetable.substitution.store');
Route::delete('/timetable/substitution/{substitution}', [TimetableController::class, 'destroySubstitution'])->name('school.timetable.substitution.destroy');
Route::get('/timetable/workload', [TimetableController::class, 'teacherWorkload'])->name('school.timetable.workload');

// Student Management Extension Routes
Route::get('/student-mgmt/import', [StudentManagementController::class, 'bulkImport'])->name('school.student-mgmt.import');
Route::get('/student-mgmt/bulk-photo', [StudentManagementController::class, 'bulkPhoto'])->name('school.student-mgmt.bulk-photo');
Route::get('/student-mgmt/optional-subject', [StudentManagementController::class, 'optionalSubject'])->name('school.student-mgmt.optional-subject');
Route::post('/student-mgmt/optional-subject', [StudentManagementController::class, 'saveOptionalSubject']);
Route::get('/student-mgmt/admission-report', [StudentManagementController::class, 'admissionReport'])->name('school.student-mgmt.admission-report');
Route::get('/student-mgmt/siblings', [StudentManagementController::class, 'siblings'])->name('school.student-mgmt.siblings');
Route::get('/student-mgmt/bulk-attendance', [StudentManagementController::class, 'bulkAttendance'])->name('school.student-mgmt.bulk-attendance');
Route::post('/student-mgmt/bulk-attendance', [\App\Http\Controllers\School\Attendance\StudentAttendanceController::class, 'store']);
Route::get('/student-mgmt/report', [StudentManagementController::class, 'studentReport'])->name('school.student-mgmt.report');
Route::get('/student-mgmt/app-settings', [StudentManagementController::class, 'appSettings'])->name('school.student-mgmt.app-settings');
Route::post('/student-mgmt/app-settings', [StudentManagementController::class, 'saveAppSettings']);
Route::get('/student-mgmt/bulk-admission-number', [StudentManagementController::class, 'bulkAdmissionNumber'])->name('school.student-mgmt.bulk-admission-number');
Route::post('/student-mgmt/bulk-admission-number', [StudentManagementController::class, 'saveBulkAdmissionNumber']);
Route::get('/student-mgmt/attendance-report', [StudentManagementController::class, 'attendanceReport'])->name('school.student-mgmt.attendance-report');
Route::get('/student-mgmt/discipline', [StudentManagementController::class, 'discipline'])->name('school.student-mgmt.discipline');
Route::post('/student-mgmt/discipline', [StudentManagementController::class, 'saveDiscipline']);
Route::get('/student-mgmt/bulk-operation', [StudentManagementController::class, 'bulkOperation'])->name('school.student-mgmt.bulk-operation');
Route::post('/student-mgmt/bulk-operation', [StudentManagementController::class, 'saveBulkOperation']);
Route::get('/student-mgmt/ptm', [StudentManagementController::class, 'ptm'])->name('school.student-mgmt.ptm');
Route::post('/student-mgmt/ptm', [StudentManagementController::class, 'savePtm']);
Route::get('/student-mgmt/cca', [StudentManagementController::class, 'cca'])->name('school.student-mgmt.cca');
Route::post('/student-mgmt/cca', [StudentManagementController::class, 'saveCca']);

// Download Statistics Routes
Route::get('/downloads/student-status', [DownloadStatisticsController::class, 'studentDownloadStatus'])->name('school.downloads.student-status');
Route::get('/downloads/staff-status', [DownloadStatisticsController::class, 'staffDownloadStatus'])->name('school.downloads.staff-status');
Route::get('/downloads/parent-status', [DownloadStatisticsController::class, 'parentDownloadStatus'])->name('school.downloads.parent-status');
Route::get('/downloads/student-activity', [DownloadStatisticsController::class, 'studentActivity'])->name('school.downloads.student-activity');
Route::get('/downloads/staff-activity', [DownloadStatisticsController::class, 'staffActivity'])->name('school.downloads.staff-activity');
Route::get('/downloads/parent-activity', [DownloadStatisticsController::class, 'parentActivity'])->name('school.downloads.parent-activity');

// Legacy compatibility
Route::get('/downloads/status', function() { return redirect()->route('school.downloads.student-status'); })->name('school.downloads.status');
Route::get('/downloads/activity', function() { return redirect()->route('school.downloads.student-activity'); })->name('school.downloads.activity');


// Fee Management Routes
Route::get('/fees/configuration', [FeeManagementController::class, 'feeConfiguration'])->name('school.fees.configuration');
Route::post('/fees/configuration', [FeeManagementController::class, 'feeConfiguration']);
Route::get('/fees/basics', [FeeManagementController::class, 'feeBasics'])->name('school.fees.basics');
Route::get('/fees/class-wise', [FeeManagementController::class, 'classWiseFee'])->name('school.fees.class-wise');
Route::post('/fees/class-wise', [FeeManagementController::class, 'classWiseFee']);
Route::get('/fees/student-wise', [FeeManagementController::class, 'studentWiseFee'])->name('school.fees.student-wise');
Route::post('/fees/student-wise', [FeeManagementController::class, 'studentWiseFee']);
Route::get('/fees/optional-mapping', [FeeManagementController::class, 'optionalFeeMapping'])->name('school.fees.optional-mapping');
Route::post('/fees/optional-mapping', [FeeManagementController::class, 'optionalFeeMapping']);
Route::get('/fees/payment-links', [FeeManagementController::class, 'paymentLinks'])->name('school.fees.payment-links');
Route::post('/fees/payment-links', [FeeManagementController::class, 'paymentLinks']);
Route::get('/fees/collection-followup', [FeeManagementController::class, 'collectionFollowup'])->name('school.fees.collection-followup');
Route::post('/fees/collection-followup', [FeeManagementController::class, 'collectionFollowup']);
Route::get('/fees/schedule-mapper', [FeeManagementController::class, 'scheduleMapper'])->name('school.fees.schedule-mapper');
Route::post('/fees/schedule-mapper', [FeeManagementController::class, 'scheduleMapper']);
Route::get('/fees/refund', [FeeManagementController::class, 'refundFee'])->name('school.fees.refund');
Route::post('/fees/refund', [FeeManagementController::class, 'refundFee']);
Route::get('/fees/receipts', [FeeManagementController::class, 'feeReceipts'])->name('school.fees.receipts');
Route::get('/fees/pending-cheques', [FeeManagementController::class, 'pendingCheques'])->name('school.fees.pending-cheques');
Route::post('/fees/pending-cheques', [FeeManagementController::class, 'pendingCheques']);
Route::get('/fees/reports', [FeeManagementController::class, 'feeReports'])->name('school.fees.reports');
Route::get('/fees/invoice', [FeeManagementController::class, 'feeInvoice'])->name('school.fees.invoice');
Route::get('/fees/invoice1', [FeeManagementController::class, 'feeInvoice1'])->name('school.fees.invoice1');
Route::get('/fees/bulk-upload', [FeeManagementController::class, 'feeBulkUpload'])->name('school.fees.bulk-upload');
Route::post('/fees/bulk-upload', [FeeManagementController::class, 'feeBulkUpload']);
Route::get('/fees/statement-of-account', [FeeManagementController::class, 'statementOfAccount'])->name('school.fees.statement-of-account');
Route::get('/fees/xero-integration', [FeeManagementController::class, 'xeroIntegration'])->name('school.fees.xero-integration');
Route::post('/fees/xero-integration', [FeeManagementController::class, 'xeroIntegration']);

// I Card / Bus Pass / Admit Card Routes
Route::get('/cards/template-creator', [CardManagementController::class, 'templateCreator'])->name('school.cards.template-creator');
Route::post('/cards/template-creator', [CardManagementController::class, 'templateCreator']);
Route::get('/cards/generate-card', [CardManagementController::class, 'generateCard'])->name('school.cards.generate-card');
Route::post('/cards/generate-card', [CardManagementController::class, 'generateCard']);

// Digital Diary Routes
Route::get('/diary/create', [DigitalDiaryController::class, 'createDiary'])->name('school.diary.create');
Route::post('/diary/create', [DigitalDiaryController::class, 'createDiary']);
Route::get('/diary/report', [DigitalDiaryController::class, 'diaryReport'])->name('school.diary.report');

// Event & Holiday Management Routes
Route::get('/events', [EventHolidayController::class, 'eventManagement'])->name('school.events.index');
Route::post('/events', [EventHolidayController::class, 'eventManagement']);

// Certificate Management Routes
Route::get('/certificates/template-creator', [CertificateManagementController::class, 'templateCreator'])->name('school.certificates.template-creator');
Route::post('/certificates/template-creator', [CertificateManagementController::class, 'templateCreator']);
Route::get('/certificates/manage', [CertificateManagementController::class, 'manageCertificates'])->name('school.certificates.manage');
Route::post('/certificates/manage', [CertificateManagementController::class, 'manageCertificates']);
Route::get('/certificates/class-wise', [CertificateManagementController::class, 'classWiseStudentCertificate'])->name('school.certificates.class-wise');
Route::get('/certificates/report', [CertificateManagementController::class, 'certificatesReport'])->name('school.certificates.report');

// Leave Management
Route::match(['get', 'post'], '/leave/basics', [App\Http\Controllers\School\LeaveManagementController::class, 'basics'])->name('school.leave.basics');
Route::match(['get', 'post'], '/leave/staff', [App\Http\Controllers\School\LeaveManagementController::class, 'staff'])->name('school.leave.staff');
Route::match(['get', 'post'], '/leave/student', [App\Http\Controllers\School\LeaveManagementController::class, 'student'])->name('school.leave.student');

// Communication
Route::match(['get', 'post'], '/communication/settings', [App\Http\Controllers\School\CommunicationController::class, 'settings'])->name('school.communication.settings');
Route::match(['get', 'post'], '/communication/notice', [App\Http\Controllers\School\CommunicationController::class, 'notice'])->name('school.communication.notice');
Route::match(['get', 'post'], '/communication/survey', [App\Http\Controllers\School\CommunicationController::class, 'survey'])->name('school.communication.survey');
Route::match(['get', 'post'], '/communication/sms', [App\Http\Controllers\School\CommunicationController::class, 'sms'])->name('school.communication.sms');
Route::match(['get', 'post'], '/communication/sms-template', [App\Http\Controllers\School\CommunicationController::class, 'smsTemplate'])->name('school.communication.sms-template');
Route::match(['get', 'post'], '/communication/whatsapp', [App\Http\Controllers\School\CommunicationController::class, 'whatsapp'])->name('school.communication.whatsapp');
Route::match(['get', 'post'], '/communication/email', [App\Http\Controllers\School\CommunicationController::class, 'email'])->name('school.communication.email');
Route::match(['get', 'post'], '/communication/chat', [App\Http\Controllers\School\CommunicationController::class, 'chat'])->name('school.communication.chat');

// Examination
Route::match(['get', 'post'], '/examination/grade-scale', [App\Http\Controllers\School\ExaminationController::class, 'gradeScale'])->name('school.examination.grade-scale');
Route::match(['get', 'post'], '/examination/marks-entry', [App\Http\Controllers\School\ExaminationController::class, 'marksEntry'])->name('school.examination.marks-entry');
Route::match(['get', 'post'], '/examination/offline-tests', [App\Http\Controllers\School\ExaminationController::class, 'offlineTests'])->name('school.examination.offline-tests');
Route::match(['get', 'post'], '/examination/lms-tests', [App\Http\Controllers\School\ExaminationController::class, 'lmsTests'])->name('school.examination.lms-tests');
Route::match(['get', 'post'], '/examination/report-card-template', [App\Http\Controllers\School\ExaminationController::class, 'reportCardTemplate'])->name('school.examination.report-card-template');
Route::match(['get', 'post'], '/examination/report-card', [App\Http\Controllers\School\ExaminationController::class, 'reportCard'])->name('school.examination.report-card');
Route::match(['get', 'post'], '/examination/report-card-v2', [App\Http\Controllers\School\ExaminationController::class, 'reportCardV2'])->name('school.examination.report-card-v2');
Route::match(['get', 'post'], '/examination/marksheets-report', [App\Http\Controllers\School\ExaminationController::class, 'marksheetsReport'])->name('school.examination.marksheets-report');
Route::match(['get', 'post'], '/examination/reports', [App\Http\Controllers\School\ExaminationController::class, 'reports'])->name('school.examination.reports');

// Admissions
Route::match(['get', 'post'], '/admissions/process', [App\Http\Controllers\School\AdmissionsController::class, 'process'])->name('school.admissions.process');
Route::match(['get', 'post'], '/admissions/settings', [App\Http\Controllers\School\AdmissionsController::class, 'settings'])->name('school.admissions.settings');
Route::match(['get', 'post'], '/admissions/enquiry-leads', [App\Http\Controllers\School\AdmissionsController::class, 'enquiryLeads'])->name('school.admissions.enquiry-leads');
Route::match(['get', 'post'], '/admissions/application-payment', [App\Http\Controllers\School\AdmissionsController::class, 'applicationPayment'])->name('school.admissions.application-payment');
Route::match(['get', 'post'], '/admissions/pending-documents', [App\Http\Controllers\School\AdmissionsController::class, 'pendingDocuments'])->name('school.admissions.pending-documents');
Route::match(['get', 'post'], '/admissions/interaction-evaluation', [App\Http\Controllers\School\AdmissionsController::class, 'interactionEvaluation'])->name('school.admissions.interaction-evaluation');
Route::match(['get', 'post'], '/admissions/admission', [App\Http\Controllers\School\AdmissionsController::class, 'admission'])->name('school.admissions.admission');
Route::match(['get', 'post'], '/admissions/new-admission-report', [App\Http\Controllers\School\AdmissionsController::class, 'newAdmissionReport'])->name('school.admissions.new-admission-report');
Route::match(['get', 'post'], '/admissions/daily-planner', [App\Http\Controllers\School\AdmissionsController::class, 'dailyPlanner'])->name('school.admissions.daily-planner');
Route::match(['get', 'post'], '/admissions/dashboard', [App\Http\Controllers\School\AdmissionsController::class, 'dashboard'])->name('school.admissions.dashboard');





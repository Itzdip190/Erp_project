@extends('superadmin.layouts.master')

@section('styles')
<style>
    /* Premium Panel Layout */
    .notif-card {
        border-radius: 20px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01) !important;
        background-color: #ffffff;
        transition: transform 0.2s, box-shadow 0.2s;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .notif-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.03) !important;
        border-color: #cbd5e1 !important;
    }
    .notif-header {
        background-color: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .notif-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }
    .notif-body {
        padding: 24px;
    }
    .tag-badge {
        font-size: 11px;
        font-family: monospace;
        background-color: rgba(59, 130, 246, 0.08);
        color: #2563eb;
        padding: 4px 8px;
        border-radius: 6px;
        display: inline-block;
        margin-right: 6px;
        margin-bottom: 6px;
        font-weight: 700;
        cursor: pointer;
        border: 1px dashed rgba(59, 130, 246, 0.2);
    }
    .tag-badge:hover {
        background-color: rgba(59, 130, 246, 0.15);
    }

    body.dark-mode .notif-card {
        background-color: #111827;
        border-color: #1e293b !important;
    }
    body.dark-mode .notif-card:hover {
        border-color: #374151 !important;
    }
    body.dark-mode .notif-header {
        background-color: #0f172a;
        border-color: #1e293b;
    }
    body.dark-mode .notif-title {
        color: #f8fafc;
    }
    body.dark-mode .tag-badge {
        background-color: rgba(96, 165, 250, 0.1);
        color: #60a5fa;
        border-color: rgba(96, 165, 250, 0.2);
    }
    body.dark-mode .form-control {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .form-control:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    }
    body.dark-mode .form-label, body.dark-mode label {
        color: #cbd5e1 !important;
    }
    body.dark-mode .text-dark {
        color: #f8fafc !important;
    }
    body.dark-mode .text-muted {
        color: #94a3b8 !important;
    }
    body.dark-mode .custom-checkbox .custom-control-label::before {
        background-color: #1f2937;
        border-color: #374151;
    }
    body.dark-mode .custom-checkbox .custom-control-input:checked ~ .custom-control-label::before {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 font-weight-bold text-dark m-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">Notification Types</h1>
        <p class="text-muted m-0" style="font-size: 0.85rem;">Customize template subjects, bodies, and target delivery channels for automated system alerts.</p>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; font-size: 13.5px; background-color: #ecfdf5; color: #065f46;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('superadmin.notification-types.update') }}" method="POST">
        @csrf

        <!-- 1. STUDENT ABSENT NOTIFICATION -->
        <div class="card notif-card">
            <div class="notif-header">
                <h5 class="notif-title"><i class="fas fa-user-clock mr-2 text-warning"></i> Student Absent Alert</h5>
                <span class="badge badge-pill badge-warning px-3 py-1 font-weight-bold" style="font-size: 11px;">Attendance Event</span>
            </div>
            <div class="notif-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13px;">Subject Template</label>
                            <input type="text" name="attendance[title]" value="{{ $settings['attendance']['title'] }}" class="form-control mb-3" style="border-radius: 10px; height: 42px; font-weight: 700;">
                            <input type="text" name="attendance[subject]" value="{{ $settings['attendance']['subject'] }}" class="form-control" style="border-radius: 10px; height: 42px;">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13px;">Body Template</label>
                            <textarea name="attendance[body]" class="form-control" rows="4" style="border-radius: 10px;">{{ $settings['attendance']['body'] }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="form-label font-weight-bold d-block mb-2" style="font-size: 13px;">Delivery Channels</label>
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input" id="channel_att_email" name="attendance[channels][]" value="email" {{ in_array('email', $settings['attendance']['channels']) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="channel_att_email" style="cursor: pointer;">Email Alert</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="channel_att_sms" name="attendance[channels][]" value="sms" {{ in_array('sms', $settings['attendance']['channels']) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="channel_att_sms" style="cursor: pointer;">SMS Text Message</label>
                            </div>
                        </div>

                        <div>
                            <label class="form-label font-weight-bold d-block mb-2" style="font-size: 13px;">Available Dynamic Tags</label>
                            <div class="tag-badge" onclick="insertTag(this, 'attendance')">{student_name}</div>
                            <div class="tag-badge" onclick="insertTag(this, 'attendance')">{date}</div>
                            <small class="text-muted d-block mt-2" style="font-size: 11px; line-height: 1.3;">Click tags to append them inside subject or body field templates.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. FEE INVOICE REMINDER -->
        <div class="card notif-card">
            <div class="notif-header">
                <h5 class="notif-title"><i class="fas fa-file-invoice-dollar mr-2 text-danger"></i> Fee Payment Reminder</h5>
                <span class="badge badge-pill badge-danger px-3 py-1 font-weight-bold" style="font-size: 11px;">Finance Event</span>
            </div>
            <div class="notif-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13px;">Subject Template</label>
                            <input type="text" name="fee_reminder[title]" value="{{ $settings['fee_reminder']['title'] }}" class="form-control mb-3" style="border-radius: 10px; height: 42px; font-weight: 700;">
                            <input type="text" name="fee_reminder[subject]" value="{{ $settings['fee_reminder']['subject'] }}" class="form-control" style="border-radius: 10px; height: 42px;">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13px;">Body Template</label>
                            <textarea name="fee_reminder[body]" class="form-control" rows="4" style="border-radius: 10px;">{{ $settings['fee_reminder']['body'] }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="form-label font-weight-bold d-block mb-2" style="font-size: 13px;">Delivery Channels</label>
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input" id="channel_fee_email" name="fee_reminder[channels][]" value="email" {{ in_array('email', $settings['fee_reminder']['channels']) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="channel_fee_email" style="cursor: pointer;">Email Alert</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="channel_fee_sms" name="fee_reminder[channels][]" value="sms" {{ in_array('sms', $settings['fee_reminder']['channels']) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="channel_fee_sms" style="cursor: pointer;">SMS Text Message</label>
                            </div>
                        </div>

                        <div>
                            <label class="form-label font-weight-bold d-block mb-2" style="font-size: 13px;">Available Dynamic Tags</label>
                            <div class="tag-badge" onclick="insertTag(this, 'fee_reminder')">{student_name}</div>
                            <div class="tag-badge" onclick="insertTag(this, 'fee_reminder')">{due_amount}</div>
                            <div class="tag-badge" onclick="insertTag(this, 'fee_reminder')">{due_date}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. EXAM RESULTS ANNOUNCEMENT -->
        <div class="card notif-card">
            <div class="notif-header">
                <h5 class="notif-title"><i class="fas fa-graduation-cap mr-2 text-info"></i> Exam Results Published</h5>
                <span class="badge badge-pill badge-info px-3 py-1 font-weight-bold" style="font-size: 11px;">Academics Event</span>
            </div>
            <div class="notif-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13px;">Subject Template</label>
                            <input type="text" name="exam_publish[title]" value="{{ $settings['exam_publish']['title'] }}" class="form-control mb-3" style="border-radius: 10px; height: 42px; font-weight: 700;">
                            <input type="text" name="exam_publish[subject]" value="{{ $settings['exam_publish']['subject'] }}" class="form-control" style="border-radius: 10px; height: 42px;">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13px;">Body Template</label>
                            <textarea name="exam_publish[body]" class="form-control" rows="4" style="border-radius: 10px;">{{ $settings['exam_publish']['body'] }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="form-label font-weight-bold d-block mb-2" style="font-size: 13px;">Delivery Channels</label>
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input" id="channel_exam_email" name="exam_publish[channels][]" value="email" {{ in_array('email', $settings['exam_publish']['channels']) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="channel_exam_email" style="cursor: pointer;">Email Alert</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="channel_exam_sms" name="exam_publish[channels][]" value="sms" {{ in_array('sms', $settings['exam_publish']['channels']) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="channel_exam_sms" style="cursor: pointer;">SMS Text Message</label>
                            </div>
                        </div>

                        <div>
                            <label class="form-label font-weight-bold d-block mb-2" style="font-size: 13px;">Available Dynamic Tags</label>
                            <div class="tag-badge" onclick="insertTag(this, 'exam_publish')">{student_name}</div>
                            <div class="tag-badge" onclick="insertTag(this, 'exam_publish')">{exam_name}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Global Save Button -->
        <div class="text-right mb-5">
            <button type="submit" class="btn btn-primary px-5" style="border-radius: 12px; font-weight: 700; height: 44px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);">
                <i class="fas fa-save mr-2"></i> Update Templates
            </button>
        </div>

    </form>
</div>
@endsection

@section('scripts')
<script>
    // Appends tag string into the corresponding textarea body
    function insertTag(badgeElement, sectionName) {
        const text = $(badgeElement).text();
        const textarea = $(`textarea[name="${sectionName}[body]"]`);
        const currentVal = textarea.val();
        textarea.val(currentVal + " " + text);
        textarea.focus();
    }
</script>
@endsection

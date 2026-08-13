@extends('layouts.app')

@section('title', 'Create New Password')
@section('page-title', 'Create New Password')

@section('content')
<style>
.change-pass-container {
    max-width: 580px;
    margin: 40px auto;
}
.change-pass-card {
    background: var(--card, #ffffff);
    border: 1px solid var(--border, #e5e7eb);
    border-radius: 16px;
    box-shadow: var(--shadow-lg, 0 10px 25px -5px rgba(0, 0, 0, 0.1));
    overflow: hidden;
}
.change-pass-header {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: #ffffff;
    padding: 28px 32px;
    position: relative;
}
.change-pass-header h2 {
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}
.change-pass-header p {
    font-size: 13.5px;
    color: #94a3b8;
    margin: 0;
    line-height: 1.5;
}
.change-pass-body {
    padding: 32px;
}
.alert-notice {
    background: rgba(245, 158, 11, 0.1);
    border-left: 4px solid var(--gold, #f59e0b);
    padding: 14px 18px;
    border-radius: 8px;
    margin-bottom: 24px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    color: var(--t1, #1f2937);
    font-size: 13.5px;
}
.alert-notice i {
    color: var(--gold, #f59e0b);
    font-size: 18px;
    margin-top: 2px;
}
.input-wrap {
    position: relative;
}
.input-wrap input {
    padding-right: 42px !important;
}
.password-toggle-btn {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--t3, #9ca3af);
    cursor: pointer;
    font-size: 15px;
    padding: 4px;
}
.password-toggle-btn:hover {
    color: var(--t1, #111827);
}
.rules-card {
    background: rgba(15, 23, 42, 0.03);
    border: 1px solid var(--border, #e5e7eb);
    border-radius: 10px;
    padding: 16px 20px;
    margin: 20px 0 28px 0;
}
body.dark-mode .rules-card {
    background: rgba(255, 255, 255, 0.04);
}
.rules-card-title {
    font-size: 12.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--t2, #6b7280);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.rule-item {
    font-size: 13px;
    color: var(--t2, #6b7280);
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
    transition: color 0.2s ease;
}
.rule-item:last-child {
    margin-bottom: 0;
}
.rule-item.valid {
    color: #10b981;
}
.rule-item.invalid {
    color: var(--t2, #6b7280);
}
.rule-icon {
    font-size: 12px;
    width: 16px;
    text-align: center;
}
</style>

<div class="change-pass-container">
    <div class="change-pass-card">
        <div class="change-pass-header">
            <h2><i class="fa-solid fa-user-shield" style="color:var(--gold,#f59e0b);"></i> Create New Password</h2>
            <p>Your password was reset to a temporary password. Please set a new password to secure your account.</p>
        </div>

        <div class="change-pass-body">
            @if(session('warning'))
                <div class="alert-notice">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div>{{ session('warning') }}</div>
                </div>
            @else
                <div class="alert-notice">
                    <i class="fa-solid fa-lock"></i>
                    <div><strong>Security Requirement:</strong> You must create a new password before accessing the system dashboard.</div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" style="background:#fef2f2; border-left:4px solid #ef4444; color:#991b1b; padding:14px 18px; border-radius:8px; margin-bottom:24px; font-size:13.5px;">
                    <ul style="margin:0; padding-left:18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.change.update') }}" id="changePasswordForm">
                @csrf

                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label" style="font-weight:600; font-size:13.5px; margin-bottom:8px; display:block;">
                        Current Temporary Password <span style="color:var(--red,#ef4444);">*</span>
                    </label>
                    <div class="input-wrap">
                        <input type="password" name="current_password" id="current_password" class="form-control" required placeholder="Enter temporary password (e.g. Welcome@123)">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('current_password', this)">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label" style="font-weight:600; font-size:13.5px; margin-bottom:8px; display:block;">
                        New Password <span style="color:var(--red,#ef4444);">*</span>
                    </label>
                    <div class="input-wrap">
                        <input type="password" name="password" id="new_password" class="form-control" required placeholder="Enter new strong password" oninput="validatePasswordStrength()">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('new_password', this)">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label" style="font-weight:600; font-size:13.5px; margin-bottom:8px; display:block;">
                        Confirm New Password <span style="color:var(--red,#ef4444);">*</span>
                    </label>
                    <div class="input-wrap">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Re-enter new password" oninput="validatePasswordStrength()">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password_confirmation', this)">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="rules-card">
                    <div class="rules-card-title">
                        <i class="fa-solid fa-shield-halved"></i> Password Security Requirements
                    </div>
                    <div class="rule-item" id="rule-length">
                        <i class="fa-solid fa-circle-notch rule-icon"></i> Minimum 8 characters
                    </div>
                    <div class="rule-item" id="rule-uppercase">
                        <i class="fa-solid fa-circle-notch rule-icon"></i> At least one uppercase letter (A-Z)
                    </div>
                    <div class="rule-item" id="rule-lowercase">
                        <i class="fa-solid fa-circle-notch rule-icon"></i> At least one lowercase letter (a-z)
                    </div>
                    <div class="rule-item" id="rule-number">
                        <i class="fa-solid fa-circle-notch rule-icon"></i> At least one number (0-9)
                    </div>
                    <div class="rule-item" id="rule-special">
                        <i class="fa-solid fa-circle-notch rule-icon"></i> At least one special character (e.g. @$!%*#?&)
                    </div>
                    <div class="rule-item" id="rule-match">
                        <i class="fa-solid fa-circle-notch rule-icon"></i> Passwords must match
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; padding:12px; font-weight:600; font-size:15px; border-radius:8px; display:flex; justify-content:center; align-items:center; gap:8px;">
                    <i class="fa-solid fa-key"></i> Update Password & Proceed
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function validatePasswordStrength() {
    const pwd = document.getElementById('new_password').value;
    const confirmPwd = document.getElementById('password_confirmation').value;

    updateRuleState('rule-length', pwd.length >= 8);
    updateRuleState('rule-uppercase', /[A-Z]/.test(pwd));
    updateRuleState('rule-lowercase', /[a-z]/.test(pwd));
    updateRuleState('rule-number', /[0-9]/.test(pwd));
    updateRuleState('rule-special', /[@$!%*#?&~^\(\)_\+=\-\[\]\{\};:'",<>\./\?\\|]/.test(pwd));
    updateRuleState('rule-match', pwd !== '' && pwd === confirmPwd);
}

function updateRuleState(ruleId, isValid) {
    const el = document.getElementById(ruleId);
    const icon = el.querySelector('i');
    if (isValid) {
        el.classList.add('valid');
        el.classList.remove('invalid');
        icon.className = 'fa-solid fa-circle-check rule-icon';
    } else {
        el.classList.remove('valid');
        el.classList.add('invalid');
        icon.className = 'fa-solid fa-circle-notch rule-icon';
    }
}
</script>
@endsection

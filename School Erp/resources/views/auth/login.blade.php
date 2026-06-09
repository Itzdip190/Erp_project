@extends('layouts.auth')

@section('styles')
<style>
    /* Reset and Core Layout */
    body {
        background-color: #ffffff;
        color: #1e293b;
        min-height: 100vh;
        display: block !important;
        font-family: 'Lato', sans-serif !important;
    }

    .login-container {
        display: flex;
        min-height: 100vh;
        width: 100vw;
        overflow-x: hidden;
    }

    /* Left Side: Branding & Features (Royal Blue Theme) */
    .login-left-side {
        width: 50%;
        background: linear-gradient(135deg, #0947ca 0%, #031a61 100%);
        color: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 3.5rem;
        position: relative;
        overflow: hidden;
    }

    /* Floating dots accent styling */
    .login-left-side::before {
        content: "";
        position: absolute;
        width: 160px;
        height: 160px;
        background-image: radial-gradient(rgba(255, 255, 255, 0.15) 1.5px, transparent 1.5px);
        background-size: 16px 16px;
        top: 40px;
        right: 40px;
        opacity: 0.6;
        pointer-events: none;
    }

    .left-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 2;
    }

    .left-brand-logo {
        width: 44px;
        height: 44px;
        background-color: #ffffff;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0947ca;
        font-size: 1.4rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .left-brand-text {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }

    .left-brand-name {
        font-size: 1.45rem;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.5px;
    }

    .left-brand-sub {
        font-size: 0.72rem;
        color: rgba(255, 255, 255, 0.7);
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .left-hero-section {
        margin-top: 1.5rem;
        z-index: 2;
    }

    .left-hero-title {
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1.2;
        color: #ffffff;
        letter-spacing: -0.5px;
    }

    .left-hero-sub {
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.8);
        margin-top: 0.8rem;
        max-width: 460px;
        line-height: 1.5;
    }

    /* Core Outline Feature Cards */
    .left-features-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
        margin-top: 1.75rem;
        z-index: 2;
    }

    .left-feature-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 8px;
        text-decoration: none !important;
    }

    .left-feature-icon-circle {
        width: 52px;
        height: 52px;
        border: 1.5px solid rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        background-color: rgba(255, 255, 255, 0.02);
    }

    .left-feature-card:hover .left-feature-icon-circle {
        background-color: #ffffff;
        color: #0947ca;
        border-color: #ffffff;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(255, 255, 255, 0.15);
    }

    .left-feature-text {
        font-size: 0.72rem;
        color: rgba(255, 255, 255, 0.85);
        font-weight: 600;
        line-height: 1.2;
    }

    /* Center Illustration */
    .left-illustration-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 1.5rem 0;
        z-index: 2;
    }

    .left-illustration-img {
        max-width: 85%;
        max-height: 270px;
        object-fit: contain;
        filter: drop-shadow(0 15px 30px rgba(0, 0, 0, 0.25));
    }

    /* Bottom stats bar */
    .left-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.12);
        padding-top: 1.5rem;
        z-index: 2;
    }

    .left-stat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 2px;
    }

    .left-stat-icon-wrapper {
        color: #22d3ee;
        font-size: 1.25rem;
        margin-bottom: 2px;
    }

    .left-stat-num {
        font-size: 1.2rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.1;
    }

    .left-stat-label {
        font-size: 0.65rem;
        color: rgba(255, 255, 255, 0.6);
        font-weight: 600;
    }

    .left-countries-pill {
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 30px;
        padding: 6px 16px;
        font-size: 0.72rem;
        color: rgba(255, 255, 255, 0.8);
        text-align: center;
        margin-top: 1.25rem;
        font-weight: 600;
        background-color: rgba(255, 255, 255, 0.04);
        align-self: center;
        z-index: 2;
    }

    /* Right Side: Login Form (White Theme) */
    .login-right-side {
        width: 50%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 3rem 2rem;
        background-color: #ffffff;
        overflow-y: auto;
    }

    .right-brand-badge {
        display: none; /* Only show on small screens when left side is hidden */
        align-items: center;
        gap: 10px;
        margin-bottom: 2rem;
    }

    .right-brand-logo {
        width: 44px;
        height: 44px;
        background-color: #0947ca;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 1.4rem;
        box-shadow: 0 4px 10px rgba(9, 71, 202, 0.15);
    }

    .right-brand-text {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
        text-align: left;
    }

    .right-brand-name {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.5px;
    }

    .right-brand-sub {
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 600;
    }

    /* Form Container */
    .right-form-container {
        width: 100%;
        max-width: 380px;
    }

    .right-welcome-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0f172a;
        text-align: center;
        margin-bottom: 0.25rem;
        letter-spacing: -0.5px;
    }

    .right-welcome-sub {
        font-size: 0.85rem;
        color: #64748b;
        text-align: center;
        margin-bottom: 1.75rem;
        font-weight: 500;
    }

    /* Tab Selector Pill */
    .tab-selector-pill {
        display: flex;
        background-color: #f1f5f9;
        border-radius: 30px;
        padding: 4px;
        margin-bottom: 1.75rem;
        border: 1px solid #e2e8f0;
    }

    .tab-btn {
        flex: 1;
        border: none;
        background: transparent;
        padding: 10px 16px;
        font-size: 0.85rem;
        font-weight: 700;
        border-radius: 26px;
        color: #64748b;
        transition: all 0.25s ease;
        cursor: pointer;
        text-align: center;
    }

    .tab-btn.active {
        background-color: #0947ca;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(9, 71, 202, 0.2);
    }

    /* Input Field Styling */
    .form-group-custom {
        margin-bottom: 1.25rem;
    }

    .form-label-custom {
        font-size: 0.82rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.45rem;
        display: block;
    }

    .input-wrapper {
        display: flex;
        align-items: center;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 0 1rem;
        transition: all 0.2s ease;
        background-color: #ffffff;
    }

    .input-wrapper:focus-within {
        border-color: #0947ca;
        box-shadow: 0 0 0 3.5px rgba(9, 71, 202, 0.1);
    }

    .input-wrapper input {
        border: none;
        background: transparent;
        width: 100%;
        padding: 11px 0;
        outline: none;
        color: #0f172a;
        font-size: 0.92rem;
        font-weight: 500;
    }

    .input-wrapper input::placeholder {
        color: #94a3b8;
    }

    .input-wrapper i {
        color: #94a3b8;
        font-size: 1rem;
    }

    .btn-eye-toggle {
        border: none;
        background: transparent;
        color: #94a3b8;
        outline: none;
        cursor: pointer;
        padding: 0;
        transition: color 0.2s;
    }

    .btn-eye-toggle:hover {
        color: #0947ca;
    }

    /* Remember and Forgot password link */
    .forgot-link {
        font-size: 0.82rem;
        color: #0947ca;
        text-decoration: none;
        font-weight: 700;
        transition: color 0.2s;
    }

    .forgot-link:hover {
        color: #06318c;
        text-decoration: underline;
    }

    .checkbox-custom input {
        width: 16px;
        height: 16px;
        border-radius: 4px;
        border: 1.5px solid #cbd5e1;
        cursor: pointer;
    }

    .checkbox-custom input:checked {
        background-color: #0947ca;
        border-color: #0947ca;
    }

    .checkbox-custom label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        padding-left: 4px;
    }

    /* Submit Action Button */
    .btn-login-submit {
        width: 100%;
        border: none;
        background: #0947ca;
        color: #ffffff;
        padding: 13px;
        border-radius: 12px;
        font-size: 0.92rem;
        font-weight: 700;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(9, 71, 202, 0.2);
        margin-top: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-login-submit:hover:not(:disabled) {
        background-color: #06318c;
        box-shadow: 0 6px 16px rgba(9, 71, 202, 0.3);
    }

    .btn-login-submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .btn-spinner {
        display: none;
    }

    /* OR Separator */
    .or-separator {
        display: flex;
        align-items: center;
        width: 100%;
        margin: 1.5rem 0;
        color: #94a3b8;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        gap: 10px;
    }

    .or-line {
        flex: 1;
        height: 1px;
        background-color: #e2e8f0;
    }

    /* App Downloads section */
    .app-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        width: 100%;
    }

    .app-text {
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 700;
    }

    .qr-code-img {
        width: 86px;
        height: 86px;
        margin: 4px 0;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 4px;
        background-color: #ffffff;
    }

    .app-badges {
        display: flex;
        gap: 10px;
        justify-content: center;
        align-items: center;
        margin-top: 4px;
    }

    .app-badge-link img {
        height: 35px;
        border-radius: 6px;
        transition: transform 0.2s;
    }

    .app-badge-link:hover img {
        transform: translateY(-1.5px);
    }

    .right-footer {
        margin-top: 2.25rem;
        font-size: 0.72rem;
        color: #94a3b8;
        font-weight: 600;
        text-align: center;
    }

    /* Responsive Queries */
    @media (max-width: 991px) {
        .login-left-side {
            display: none;
        }
        .login-right-side {
            width: 100%;
            padding: 3rem 1.5rem;
        }
        .right-brand-badge {
            display: flex;
        }
    }
</style>
@endsection

@section('content')
<div class="login-container">
    
    <!-- Left Side: Features & Showcase (Hidden on Mobile) -->
    <div class="login-left-side">
        <!-- Logo Branding Header -->
        <div class="left-brand">
            <div class="left-brand-logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="left-brand-text">
                <span class="left-brand-name">SchoolCloud ERP</span>
                <span class="left-brand-sub">Smart School & College ERP</span>
            </div>
        </div>

        <!-- Heading Text -->
        <div class="left-hero-section">
            <h1 class="left-hero-title">Empowering Education.<br><span class="accent-teal">Enriching Future.</span></h1>
            <p class="left-hero-sub">A complete digital solution to manage your school or college efficiently.</p>
        </div>

        <!-- Horizontal Outline Feature Cards -->
        <div class="left-features-grid">
            <div class="left-feature-card">
                <div class="left-feature-icon-circle">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <span class="left-feature-text">Academics</span>
            </div>
            <div class="left-feature-card">
                <div class="left-feature-icon-circle">
                    <i class="fas fa-user-friends"></i>
                </div>
                <span class="left-feature-text">Student<br>Management</span>
            </div>
            <div class="left-feature-card">
                <div class="left-feature-icon-circle">
                    <i class="fas fa-wallet"></i>
                </div>
                <span class="left-feature-text">Fee<br>Management</span>
            </div>
            <div class="left-feature-card">
                <div class="left-feature-icon-circle">
                    <i class="fas fa-chart-line"></i>
                </div>
                <span class="left-feature-text">Reports &<br>Analytics</span>
            </div>
        </div>

        <!-- Illustration -->
        <div class="left-illustration-container">
            <img src="{{ asset('images/login_illustration.png') }}" class="left-illustration-img" alt="ERP Login Illustration">
        </div>

        <!-- Bottom Stats Row -->
        <div class="left-stats-grid">
            <div class="left-stat-item">
                <div class="left-stat-icon-wrapper"><i class="fas fa-school"></i></div>
                <span class="left-stat-num">900+</span>
                <span class="left-stat-label">Schools Trusted</span>
            </div>
            <div class="left-stat-item">
                <div class="left-stat-icon-wrapper"><i class="fas fa-globe"></i></div>
                <span class="left-stat-num">7</span>
                <span class="left-stat-label">Countries Active</span>
            </div>
            <div class="left-stat-item">
                <div class="left-stat-icon-wrapper"><i class="fas fa-shield-alt"></i></div>
                <span class="left-stat-num">100%</span>
                <span class="left-stat-label">Secure & Reliable</span>
            </div>
            <div class="left-stat-item">
                <div class="left-stat-icon-wrapper"><i class="fas fa-chart-line"></i></div>
                <span class="left-stat-num">27%</span>
                <span class="left-stat-label">Revenue Growth</span>
            </div>
        </div>

        <!-- Countries Pill Footer -->
        <div class="left-countries-pill">
            Singapore &nbsp;|&nbsp; Malaysia &nbsp;|&nbsp; India &nbsp;|&nbsp; Kenya &nbsp;|&nbsp; Nepal &nbsp;|&nbsp; Vietnam &nbsp;|&nbsp; Bahrain
        </div>
    </div>

    <!-- Right Side: Clean Login Form -->
    <div class="login-right-side">
        <!-- Logo Branding Header (Mobile Only) -->
        <div class="right-brand-badge">
            <div class="right-brand-logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="right-brand-text">
                <span class="right-brand-name">SchoolCloud ERP</span>
                <span class="right-brand-sub">Smart School & College ERP</span>
            </div>
        </div>

        <div class="right-form-container">
            <!-- Header Text -->
            <h2 class="right-welcome-title">Welcome Back!</h2>
            <p class="right-welcome-sub">Login to access your school/college dashboard</p>

            <!-- Inline Validation Error Alert -->
            @error('username')
                <div class="alert alert-danger border-0 d-flex align-items-center gap-2 mb-3" style="border-radius: 10px; font-size: 0.85rem; background-color: #fef2f2; color: #991b1b;">
                    <i class="fas fa-circle-exclamation"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror

            <!-- Login Form Toggles (Pill Selectors) -->
            <div class="tab-selector-pill">
                <button type="button" class="tab-btn active" id="tabEmailMobile">Email / Mobile</button>
                <button type="button" class="tab-btn" id="tabAdmissionId">Admission ID</button>
            </div>

            <!-- Credentials Post form -->
            <form action="{{ route('login') }}" method="POST" id="mainLoginForm">
                @csrf
                <!-- Hidden selector state -->
                <input type="hidden" name="login_type" id="loginTypeInput" value="{{ old('login_type', 'email_mobile') }}">

                <!-- Username/Email/ID field -->
                <div class="form-group-custom">
                    <label class="form-label-custom" id="usernameLabel" for="username">Email / Mobile <span class="text-danger">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" name="username" id="username" value="{{ old('username') }}" placeholder="Enter email or mobile number" required>
                        <i class="fas fa-user-circle ms-2" id="usernameIcon"></i>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="form-group-custom">
                    <label class="form-label-custom" for="password">Password <span class="text-danger">*</span></label>
                    <div class="input-wrapper">
                        <input type="password" name="password" id="password" placeholder="Enter your password" required autocomplete="current-password">
                        <button type="button" class="btn-eye-toggle ms-2" id="btnTogglePassword">
                            <i class="far fa-eye" id="passwordEyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check checkbox-custom m-0">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>

                <!-- Action Submit Button -->
                <button type="submit" class="btn-login-submit" id="btnLoginSubmit">
                    <span id="btnText">Login</span>
                    <span class="spinner-border spinner-border-sm btn-spinner" id="btnSpinner" role="status" aria-hidden="true"></span>
                </button>
            </form>

            <!-- OR separator -->
            <div class="or-separator">
                <div class="or-line"></div>
                <span>or</span>
                <div class="or-line"></div>
            </div>

            <!-- Downloads Section -->
            <div class="app-section">
                <span class="app-text">Download our mobile app</span>
                <img src="{{ asset('images/qr_code.png') }}" class="qr-code-img" alt="App Download QR Code">
                <div class="app-badges">
                    <a href="#" class="app-badge-link">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play Store Badge">
                    </a>
                    <a href="#" class="app-badge-link">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg" alt="Apple App Store Badge">
                    </a>
                </div>
            </div>

            <!-- Footer Section -->
            <div class="right-footer">
                &copy; {{ date('Y') }} SchoolCloud ERP. All rights reserved.
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Tab toggling variables
    const tabEmailMobile = document.getElementById('tabEmailMobile');
    const tabAdmissionId = document.getElementById('tabAdmissionId');
    const loginTypeInput = document.getElementById('loginTypeInput');
    const usernameLabel = document.getElementById('usernameLabel');
    const usernameInput = document.getElementById('username');
    const usernameIcon = document.getElementById('usernameIcon');

    // Email/Mobile Tab Event
    tabEmailMobile.addEventListener('click', function() {
        tabAdmissionId.classList.remove('active');
        tabEmailMobile.classList.add('active');
        loginTypeInput.value = 'email_mobile';
        usernameLabel.innerHTML = 'Email / Mobile <span class="text-danger">*</span>';
        usernameInput.placeholder = 'Enter email or mobile number';
        usernameIcon.className = 'fas fa-user-circle ms-2';
    });

    // Admission ID Tab Event
    tabAdmissionId.addEventListener('click', function() {
        tabEmailMobile.classList.remove('active');
        tabAdmissionId.classList.add('active');
        loginTypeInput.value = 'admission_id';
        usernameLabel.innerHTML = 'Admission ID <span class="text-danger">*</span>';
        usernameInput.placeholder = 'Enter admission ID';
        usernameIcon.className = 'fas fa-id-card ms-2';
    });

    // Restore old tab selection if validation failed
    @if(old('login_type') === 'admission_id')
        tabAdmissionId.click();
    @endif

    // Password visibility toggle
    const passwordInput = document.getElementById('password');
    const btnTogglePassword = document.getElementById('btnTogglePassword');
    const passwordEyeIcon = document.getElementById('passwordEyeIcon');

    btnTogglePassword.addEventListener('click', function() {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        
        if (isPassword) {
            passwordEyeIcon.className = 'far fa-eye-slash';
        } else {
            passwordEyeIcon.className = 'far fa-eye';
        }
    });

    // Form submit loading spinner
    const mainForm = document.getElementById('mainLoginForm');
    const btnLoginSubmit = document.getElementById('btnLoginSubmit');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    mainForm.addEventListener('submit', function() {
        btnLoginSubmit.disabled = true;
        btnText.textContent = 'Logging in...';
        btnSpinner.style.display = 'inline-block';
    });
</script>
@endsection

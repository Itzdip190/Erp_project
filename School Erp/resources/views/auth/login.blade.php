<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EduCore — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        :root {
            --primary-blue: #0252D9;
            --hover-blue: #0143B5;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --bg-light: #f9fafb;
            --error-red: #ef4444;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }
        .container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ─── LEFT PANEL ────────────────────────────────────────── */
        .left-panel {
            flex: 1.15;
            background: linear-gradient(135deg, #0252D9 0%, #00287A 100%);
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            color: #ffffff;
        }
        
        .left-panel::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -20%;
            width: 700px;
            height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 60%);
            pointer-events: none;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -10%;
            left: -10%;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.04) 0%, transparent 65%);
            pointer-events: none;
        }

        .left-content-wrap {
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: space-between;
            position: relative;
            z-index: 10;
        }

        /* Branding Logo Left */
        .left-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .left-logo-icon {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 18px;
        }
        .left-logo-text strong {
            display: block;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.1;
        }
        .left-logo-text span {
            font-size: 10px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Hero Text Left */
        .left-hero {
            margin-top: 24px;
        }
        .left-hero h1 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 34px;
            font-weight: 800;
            line-height: 1.25;
            color: #ffffff;
            letter-spacing: -0.5px;
        }
        .left-hero h1 span {
            color: #38bdf8; /* cyan */
        }
        .left-hero p {
            font-size: 13.5px;
            color: rgba(255, 255, 255, 0.75);
            margin-top: 10px;
            max-width: 480px;
            line-height: 1.5;
        }

        /* Features row left */
        .left-features {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-top: 24px;
        }
        .feat-item {
            text-align: center;
        }
        .feat-icon {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            background-color: rgba(255, 255, 255, 0.08);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            color: #ffffff;
            margin-bottom: 6px;
        }
        .feat-item span {
            display: block;
            font-size: 10px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.3;
        }

        /* Central Illustration */
        .ill-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px 0;
        }
        .ill-img {
            max-width: 90%;
            max-height: 320px;
            object-fit: contain;
        }

        /* Stats Row */
        .left-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            padding-top: 18px;
        }
        .l-stat-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .l-stat-icon {
            font-size: 15px;
            color: #38bdf8;
            margin-top: 2px;
        }
        .l-stat-info strong {
            display: block;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 16px;
            font-weight: 800;
            color: #ffffff;
        }
        .l-stat-info span {
            display: block;
            font-size: 9px;
            color: rgba(255, 255, 255, 0.65);
            line-height: 1.3;
            margin-top: 1px;
        }

        /* Countries Pill */
        .countries-pill-wrap {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .countries-pill {
            background-color: #ffffff;
            border-radius: 30px;
            padding: 8px 20px;
            display: inline-flex;
            gap: 8px;
            align-items: center;
            font-size: 11px;
            font-weight: 700;
            color: var(--primary-blue);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .pill-divider {
            color: #e5e7eb;
            font-weight: 400;
        }

        @media (max-width: 1024px) {
            .left-panel {
                display: none;
            }
        }

        /* ─── RIGHT PANEL ────────────────────────────────────────── */
        .right-panel {
            flex: 0.85;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 40px;
            background-color: #ffffff;
            overflow-y: auto;
        }
        .right-content {
            width: 100%;
            max-width: 400px;
            margin: auto;
        }

        /* Logo Area */
        .logo-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 24px;
        }
        .logo-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background-color: var(--primary-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 20px;
        }
        .logo-text strong {
            display: block;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: var(--primary-blue);
            line-height: 1.1;
        }
        .logo-text span {
            font-size: 10.5px;
            font-weight: 500;
            color: var(--text-muted);
        }

        /* Welcome text */
        .welcome-hdr {
            text-align: center;
            margin-bottom: 24px;
        }
        .welcome-hdr h2 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
        }
        .welcome-hdr p {
            font-size: 13px;
            color: var(--text-muted);
        }

        /* Tabs Switcher */
        .tab-switcher {
            display: flex;
            background-color: #f3f4f6;
            border-radius: 30px;
            padding: 4px;
            margin-bottom: 24px;
        }
        .tab-btn {
            flex: 1;
            border: none;
            background: none;
            padding: 8px 12px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-muted);
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }
        .tab-btn.active {
            background-color: var(--primary-blue);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(2, 82, 217, 0.25);
        }

        /* Alerts */
        .alert {
            background-color: #fee2e2;
            border: 1px solid #fca5a5;
            color: #b91c1c;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 18px;
        }

        /* Forms */
        .form-group {
            margin-bottom: 16px;
        }
        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 6px;
        }
        .form-label span {
            color: var(--error-red);
        }
        .input-relative {
            position: relative;
        }
        .form-control {
            width: 100%;
            height: 44px;
            padding: 0 40px 0 14px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 13.5px;
            color: var(--text-dark);
            outline: none;
            background-color: #ffffff;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: var(--primary-blue);
        }
        .input-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 15px;
        }
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 15px;
            cursor: pointer;
            user-select: none;
        }

        /* Checkbox & Forgot link */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 12.5px;
        }
        .remember-me {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            color: var(--text-muted);
        }
        .remember-me input {
            cursor: pointer;
        }
        .forgot-link {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
        }
        .forgot-link:hover {
            text-decoration: underline;
        }

        /* Button */
        .btn-submit {
            width: 100%;
            height: 44px;
            background-color: var(--primary-blue);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit:hover {
            background-color: var(--hover-blue);
        }
        .btn-submit:active {
            transform: scale(0.99);
        }
        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            display: none;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .submitting .spinner {
            display: inline-block;
        }
        .submitting span {
            display: none;
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
            color: var(--text-muted);
            font-size: 11px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--border-color);
        }
        .divider:not(:empty)::before {
            margin-right: .5em;
        }
        .divider:not(:empty)::after {
            margin-left: .5em;
        }

        /* App Section */
        .app-sec {
            text-align: center;
        }
        .app-sec p {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .qr-wrap {
            margin-bottom: 14px;
        }
        .qr-code {
            width: 90px;
            height: 90px;
            object-fit: contain;
            border: 1px solid var(--border-color);
            padding: 4px;
            border-radius: 6px;
        }
        .badges-wrap {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .app-badge {
            height: 32px;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .app-badge:hover {
            opacity: 0.85;
        }

        /* Footer */
        .footer {
            font-size: 11px;
            color: var(--text-muted);
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
<div class="container">
    
    <!-- LEFT PANEL: Dynamic HTML + Illustration overlay -->
    <div class="left-panel">
        <div class="left-content-wrap">
            <!-- Top Branding Logo -->
            <div class="left-logo">
                <div class="left-logo-icon"><i class="fas fa-graduation-cap"></i></div>
                <div class="left-logo-text">
                    <strong>EduCore</strong>
                    <span>Smart School & College ERP</span>
                </div>
            </div>

            <!-- Hero Headlines -->
            <div class="left-hero">
                <h1>Empowering Education.<br><span>Enriching Future.</span></h1>
                <p>A complete digital solution to manage your school or college efficiently.</p>
            </div>

            <!-- 4 Features Icons Row -->
            <div class="left-features">
                <div class="feat-item">
                    <div class="feat-icon"><i class="fas fa-graduation-cap"></i></div>
                    <span>Academics</span>
                </div>
                <div class="feat-item">
                    <div class="feat-icon"><i class="fas fa-users"></i></div>
                    <span>Student<br>Management</span>
                </div>
                <div class="feat-item">
                    <div class="feat-icon"><i class="fas fa-indian-rupee-sign"></i></div>
                    <span>Fee<br>Management</span>
                </div>
                <div class="feat-item">
                    <div class="feat-icon"><i class="fas fa-chart-line"></i></div>
                    <span>Reports &<br>Analytics</span>
                </div>
            </div>

            <!-- Central Illustration -->
            <div class="ill-wrap">
                <img src="/images/login_illustration.png" alt="Illustration" class="ill-img">
            </div>

            <!-- Bottom Stats Strip -->
            <div class="left-stats">
                <div class="l-stat-item">
                    <div class="l-stat-icon"><i class="fas fa-school"></i></div>
                    <div class="l-stat-info">
                        <strong>900+</strong>
                        <span>Schools/Colleges<br>Trusted</span>
                    </div>
                </div>
                <div class="l-stat-item">
                    <div class="l-stat-icon"><i class="fas fa-globe"></i></div>
                    <div class="l-stat-info">
                        <strong>7</strong>
                        <span>Countries<br>Worldwide</span>
                    </div>
                </div>
                <div class="l-stat-item">
                    <div class="l-stat-icon"><i class="fas fa-shield-halved"></i></div>
                    <div class="l-stat-info">
                        <strong>100%</strong>
                        <span>Secure &<br>Reliable</span>
                    </div>
                </div>
                <div class="l-stat-item">
                    <div class="l-stat-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="l-stat-info">
                        <strong>27%</strong>
                        <span>Average Increase<br>in Revenue</span>
                    </div>
                </div>
            </div>

            <!-- Countries Pill -->
            <div class="countries-pill-wrap">
                <div class="countries-pill">
                    <span>Singapore</span>
                    <span class="pill-divider">|</span>
                    <span>Malaysia</span>
                    <span class="pill-divider">|</span>
                    <span>India</span>
                    <span class="pill-divider">|</span>
                    <span>Kenya</span>
                    <span class="pill-divider">|</span>
                    <span>Nepal</span>
                    <span class="pill-divider">|</span>
                    <span>Vietnam</span>
                    <span class="pill-divider">|</span>
                    <span>Bahrain</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- RIGHT PANEL: Clean White Authentication Form -->
    <div class="right-panel">
        <div class="right-content">
            
            <!-- Logo Header -->
            <div class="logo-wrap">
                <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
                <div class="logo-text">
                    <strong>EduCore</strong>
                    <span>Smart School & College ERP</span>
                </div>
            </div>

            <div class="welcome-hdr">
                <h2>Welcome Back!</h2>
                <p>Login to access your school/college dashboard</p>
            </div>

            <!-- Tab Switcher -->
            <div class="tab-switcher">
                <button type="button" class="tab-btn active" id="tabEmail">Email / Mobile</button>
                <button type="button" class="tab-btn" id="tabAdmission">Admission ID</button>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="alert">
                    @foreach ($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('login') }}" method="POST" id="loginForm">
                @csrf

                <!-- Username/Email/Mobile input -->
                <div class="form-group">
                    <label class="form-label" id="inputLabel" for="email">Email / Mobile <span>*</span></label>
                    <div class="input-relative">
                        <input type="text" name="email" id="email" class="form-control" placeholder="Enter email or mobile number" value="{{ old('email') }}" required autofocus autocomplete="email">
                        <i class="fas fa-user input-icon" id="userIcon"></i>
                    </div>
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <label class="form-label" for="password">Password <span>*</span></label>
                    <div class="input-relative">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required autocomplete="current-password">
                        <i class="fas fa-eye password-toggle" id="passwordToggle"></i>
                    </div>
                </div>

                <!-- Options -->
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        Remember me
                    </label>
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-submit" id="btnSubmit">
                    <span>LOGIN</span>
                    <div class="spinner" id="submitSpinner"></div>
                </button>
            </form>

            <div class="divider">or</div>

            <!-- Download Section -->
            <div class="app-sec">
                <p>Download our mobile app</p>
                <div class="qr-wrap">
                    <img src="/images/qr_code.png" alt="QR Code" class="qr-code">
                </div>
                <div class="badges-wrap">
                    <!-- Google Play Badge (SVG) -->
                    <svg class="app-badge" viewBox="0 0 135 40" onclick="window.open('#')" xmlns="http://www.w3.org/2000/svg">
                        <rect width="135" height="40" rx="6" fill="#000000"/>
                        <path d="M20.5 11l-3 3 3 3 3-3z" fill="#00e676"/>
                        <path d="M14 11v8c0 .6.4 1 1 1h.2l4.8-5-6-4z" fill="#ffeb3b"/>
                        <path d="M20 15l-3-3h-3.2l6.2 6z" fill="#f44336"/>
                        <path d="M14 11l6.2 6.2 3.2-3.2-9.4-3z" fill="#2196f3"/>
                        <text x="38" y="18" fill="#ffffff" font-family="'Inter', sans-serif" font-size="7" font-weight="600" letter-spacing="0.3">GET IT ON</text>
                        <text x="38" y="30" fill="#ffffff" font-family="'Inter', sans-serif" font-size="11.5" font-weight="700">Google Play</text>
                    </svg>
                    <!-- App Store Badge (SVG) -->
                    <svg class="app-badge" viewBox="0 0 135 40" onclick="window.open('#')" xmlns="http://www.w3.org/2000/svg">
                        <rect width="135" height="40" rx="6" fill="#000000"/>
                        <path d="M20.4 20.3c.1 2.2 1.9 2.9 2 3-.1.1-1.6 5.3-5.1 5.3-1.6 0-3.1-1.1-4.8-1.1-1.7 0-3.4 1.1-4.9 1.1C4.8 28.6 2 22 2 16.8c0-5.3 3.4-8.2 6.8-8.2 1.8 0 3.3 1.1 4.5 1.1 1.1 0 3-.1 5.1 1.1 1.7.7 3.3 2.5 3.3 4.8-1.9.8-3.2 2.7-3.2 4.7zM16 4.5c.9-1.1 1.5-2.7 1.4-4.5-1.5.1-3.3 1-4.4 2.3-1 1.1-1.8 2.8-1.6 4.5 1.7.1 3.4-.8 4.6-2.3z" fill="#ffffff"/>
                        <text x="38" y="17" fill="#ffffff" font-family="'Inter', sans-serif" font-size="6.5" font-weight="600" letter-spacing="0.2">Download on the</text>
                        <text x="38" y="30" fill="#ffffff" font-family="'Inter', sans-serif" font-size="12" font-weight="700">App Store</text>
                    </svg>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                © 2026 EduCore. All rights reserved.
            </div>
            
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabEmail = document.getElementById('tabEmail');
        const tabAdmission = document.getElementById('tabAdmission');
        const inputLabel = document.getElementById('inputLabel');
        const emailInput = document.getElementById('email');
        const userIcon = document.getElementById('userIcon');

        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('passwordToggle');
        const loginForm = document.getElementById('loginForm');
        const btnSubmit = document.getElementById('btnSubmit');

        // Tab selection logic
        tabEmail.addEventListener('click', function() {
            tabEmail.classList.add('active');
            tabAdmission.classList.remove('active');
            inputLabel.innerHTML = 'Email / Mobile <span>*</span>';
            emailInput.placeholder = 'Enter email or mobile number';
            emailInput.focus();
            userIcon.className = 'fas fa-user input-icon';
        });

        tabAdmission.addEventListener('click', function() {
            tabAdmission.classList.add('active');
            tabEmail.classList.remove('active');
            inputLabel.innerHTML = 'Admission ID <span>*</span>';
            emailInput.placeholder = 'Enter admission ID';
            emailInput.focus();
            userIcon.className = 'fas fa-id-card input-icon';
        });

        // Toggle password visibility
        passwordToggle.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.className = 'fas fa-eye-slash password-toggle';
            } else {
                passwordInput.type = 'password';
                passwordToggle.className = 'fas fa-eye password-toggle';
            }
        });

        // Add submitting state on form submit
        loginForm.addEventListener('submit', function() {
            btnSubmit.classList.add('submitting');
            btnSubmit.disabled = true;
        });
    });
</script>
</body>
</html>

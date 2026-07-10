<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SchoolCloud ERP — School Registration Request</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary-blue: #0252D9;
            --hover-blue:   #0143B5;
            --text-dark:    #1f2937;
            --text-muted:   #6b7280;
            --border-color: #e5e7eb;
            --bg-light:     #f9fafb;
            --error-red:    #ef4444;
            --card-shadow:  0 10px 40px rgba(0,0,0,0.04), 0 2px 10px rgba(0,0,0,0.01);
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fafbfe;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .signup-container {
            width: 100%;
            max-width: 600px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(229,231,235,0.8);
            overflow: hidden;
        }

        /* ── Header ── */
        .signup-header {
            background: linear-gradient(135deg, #0252D9 0%, #00287A 100%);
            padding: 36px 40px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }
        .signup-header::after {
            content: '';
            position: absolute;
            top: -50%; right: -10%;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 60%);
        }
        .header-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .logo-icon {
            width: 36px; height: 36px; border-radius: 8px;
            background-color: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 16px;
        }
        .logo-text { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 18px; letter-spacing: 0.5px; }
        .signup-header h1 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 24px; font-weight: 700; margin-bottom: 6px; }
        .signup-header p  { font-size: 14px; color: rgba(255,255,255,0.8); }

        /* ── Body ── */
        .signup-body { padding: 40px; }

        /* ── Info banner ── */
        .info-banner {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 28px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 13.5px;
            color: #1d4ed8;
            line-height: 1.5;
        }
        .info-banner i { margin-top: 2px; flex-shrink: 0; }

        /* ── Alerts ── */
        .alert-error {
            background-color: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c;
            padding: 16px 20px; border-radius: 12px; font-size: 13px; margin-bottom: 24px;
        }
        .alert-error ul { margin-left: 20px; margin-top: 5px; }

        /* ── Form ── */
        .section-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 15px; font-weight: 700; color: #0c1024;
            margin-bottom: 20px; padding-bottom: 8px;
            border-bottom: 2px solid #f1f5f9;
            display: flex; align-items: center; gap: 8px;
        }
        .section-title i { color: var(--primary-blue); }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px; }
        .form-label span { color: var(--error-red); }

        .form-control {
            width: 100%; height: 46px;
            padding: 0 14px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 14px; color: var(--text-dark);
            outline: none; background-color: #ffffff;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }
        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(2,82,217,0.08);
        }
        select.form-control { cursor: pointer; }

        /* ── Footer ── */
        .submit-section {
            margin-top: 30px;
            display: flex; align-items: center; justify-content: space-between; gap: 20px;
            padding-top: 20px; border-top: 1px solid #f1f5f9;
        }
        @media(max-width: 576px) { .submit-section { flex-direction: column; align-items: stretch; } }

        .btn-submit {
            height: 48px;
            background-color: var(--primary-blue);
            color: #ffffff; border: none; border-radius: 10px;
            font-size: 14px; font-weight: 700; padding: 0 32px;
            cursor: pointer; transition: all 0.2s;
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 4px 12px rgba(2,82,217,0.15);
        }
        .btn-submit:hover { background-color: var(--hover-blue); transform: translateY(-1px); box-shadow: 0 6px 15px rgba(2,82,217,0.25); }
        .login-back-link { font-size: 14px; color: var(--text-muted); text-decoration: none; font-weight: 600; transition: color 0.2s; }
        .login-back-link:hover { color: var(--primary-blue); }

        @media(max-width: 576px) { .signup-body { padding: 25px; } .signup-header { padding: 25px; } }

        /* ── Success screen ── */
        .success-box { text-align: center; padding: 50px 40px; }
        .success-icon {
            width: 80px; height: 80px;
            background: #ecfdf5; color: #10b981;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 36px; margin: 0 auto 24px;
            box-shadow: 0 8px 24px rgba(16,185,129,0.12);
        }
        .success-box h2 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 24px; color: #1e1b4b; margin-bottom: 12px; }
        .success-box p  { font-size: 15px; color: var(--text-muted); line-height: 1.6; margin-bottom: 30px; max-width: 440px; margin-left: auto; margin-right: auto; }
        .btn-success-back {
            display: inline-flex; align-items: center; gap: 8px;
            background-color: var(--primary-blue); color: #ffffff;
            text-decoration: none; padding: 12px 24px;
            border-radius: 10px; font-weight: 600; font-size: 14px; transition: all 0.2s;
        }
        .btn-success-back:hover { background-color: var(--hover-blue); transform: translateY(-1px); }
    </style>
</head>
<body>

    <div class="signup-container">

        @if(!session('success'))
            <div class="signup-header">
                <div class="header-logo">
                    <div class="logo-icon"><i class="fas fa-cloud"></i></div>
                    <span class="logo-text">SchoolCloud ERP</span>
                </div>
                <h1>School Registration Request</h1>
                <p>Fill in the basic details below to request an account. Our team will contact you shortly.</p>
            </div>

            <div class="signup-body">

                @if ($errors->any())
                    <div class="alert-error">
                        <strong>Please resolve the following errors:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="info-banner">
                    <i class="fas fa-info-circle"></i>
                    <span>
                        This form submits a <strong>registration request</strong> only. Once received, our agent will reach out to you to discuss your requirements and complete the onboarding process.
                    </span>
                </div>

                <form action="{{ route('school.signup.submit') }}" method="POST" id="signupForm">
                    @csrf

                    <div class="section-title">
                        <i class="fas fa-school"></i> School Information
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="name">School Name <span>*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="{{ old('name') }}"
                               placeholder="e.g. Greenwood International School" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="director_name">Principal / Director Full Name <span>*</span></label>
                        <input type="text" class="form-control" id="director_name" name="director_name"
                               value="{{ old('director_name') }}"
                               placeholder="e.g. Dr. John Doe" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="school_type">School Board / Type <span>*</span></label>
                        <select class="form-control" id="school_type" name="school_type" required>
                            <option value="">Select Board...</option>
                            <option value="CBSE"         {{ old('school_type') == 'CBSE'         ? 'selected' : '' }}>CBSE</option>
                            <option value="CBSE PATTERN" {{ old('school_type') == 'CBSE PATTERN' ? 'selected' : '' }}>CBSE PATTERN</option>
                            <option value="ICSE"         {{ old('school_type') == 'ICSE'         ? 'selected' : '' }}>ICSE</option>
                            <option value="STATE BOARD"  {{ old('school_type') == 'STATE BOARD'  ? 'selected' : '' }}>STATE BOARD</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">School Email Address <span>*</span></label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="e.g. contact@greenwood.com" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                               value="{{ old('phone') }}"
                               placeholder="e.g. +91 98765 43210">
                    </div>

                    <div class="submit-section">
                        <a href="{{ route('login') }}" class="login-back-link">
                            <i class="fas fa-arrow-left"></i> Back to Sign In
                        </a>
                        <button type="submit" class="btn-submit" id="btnSubmit">
                            <i class="fas fa-paper-plane"></i> Submit Request
                        </button>
                    </div>

                </form>
            </div>
        @else
            {{-- Success Screen --}}
            <div class="success-box">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h2>Request Submitted!</h2>
                <p>{{ session('success') }}</p>
                <p style="font-size:13px; color:#9ca3af; margin-top:-16px; margin-bottom:28px;">
                    A confirmation email has been sent to your school email address.
                </p>
                <a href="{{ route('login') }}" class="btn-success-back">
                    <i class="fas fa-home"></i> Return to Login Screen
                </a>
            </div>
        @endif

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const signupForm = document.getElementById('signupForm');
            const btnSubmit  = document.getElementById('btnSubmit');

            if (signupForm) {
                signupForm.addEventListener('submit', function() {
                    if (btnSubmit) {
                        btnSubmit.disabled = true;
                        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                    }
                });
            }
        });
    </script>
</body>
</html>

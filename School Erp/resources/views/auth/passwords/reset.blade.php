<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EduCore — Reset Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }
        :root {
            --primary-blue: #0252D9;
            --hover-blue: #0143B5;
            --cyan-accent: #00d2ff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --bg-light: #f8fafc;
            --error-red: #ef4444;
            --success-green: #10b981;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .reset-card {
            background: #ffffff;
            width: 100%;
            max-width: 480px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(2, 82, 217, 0.08), 0 4px 12px rgba(0,0,0,0.04);
            padding: 40px 36px;
            border: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
        }
        .reset-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #0252D9, #00d2ff);
        }
        .brand-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .brand-logo {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #0252D9 0%, #002ca6 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px auto;
            box-shadow: 0 8px 16px rgba(2, 82, 217, 0.25);
            color: #ffffff;
            font-size: 24px;
        }
        .brand-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 24px;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.5px;
        }
        .brand-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 4px;
        }
        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            font-size: 13.5px;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .alert-success {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }
        .req-star {
            color: var(--error-red);
        }
        .input-relative {
            position: relative;
        }
        .form-control {
            width: 100%;
            height: 48px;
            padding: 0 44px 0 42px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            background-color: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            transition: all 0.2s ease;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(2, 82, 217, 0.12);
        }
        .input-prefix-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
        }
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s;
        }
        .password-toggle:hover {
            color: var(--primary-blue);
        }
        .btn-submit {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #0252D9 0%, #0143B5 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 8px 18px rgba(2, 82, 217, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 28px;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #0143B5 0%, #003396 100%);
            box-shadow: 0 10px 22px rgba(2, 82, 217, 0.35);
            transform: translateY(-1px);
        }
        .btn-submit:active {
            transform: translateY(0);
        }
        .back-to-login {
            text-align: center;
            margin-top: 24px;
        }
        .back-to-login a {
            color: var(--primary-blue);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }
        .back-to-login a:hover {
            color: var(--hover-blue);
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="reset-card">
    <div class="brand-header">
        <div class="brand-logo">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <h1 class="brand-title">Reset Your Password</h1>
        <p class="brand-subtitle">Enter your registered email and your new password below</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-1"></i> {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST" id="resetForm">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label class="form-label" for="email">Email Address <span class="req-star">*</span></label>
            <div class="input-relative">
                <input type="text" name="email" id="email" class="form-control" placeholder="Enter your email or phone" value="{{ old('email', $email) }}" required autofocus>
                <i class="fas fa-envelope input-prefix-icon"></i>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">New Password <span class="req-star">*</span></label>
            <div class="input-relative">
                <input type="password" name="password" id="password" class="form-control" placeholder="Minimum 8 characters" required minlength="8">
                <i class="fas fa-lock input-prefix-icon"></i>
                <i class="fas fa-eye password-toggle" id="togglePassword"></i>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirm New Password <span class="req-star">*</span></label>
            <div class="input-relative">
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Re-enter new password" required minlength="8">
                <i class="fas fa-lock input-prefix-icon"></i>
                <i class="fas fa-eye password-toggle" id="toggleConfirmPassword"></i>
            </div>
        </div>

        <button type="submit" class="btn-submit" id="btnReset">
            <span>RESET PASSWORD</span>
        </button>
    </form>

    <div class="back-to-login">
        <a href="{{ route('login') }}">
            <i class="fas fa-arrow-left"></i> Back to Sign In
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('password_confirmation');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            togglePassword.className = type === 'password' ? 'fas fa-eye password-toggle' : 'fas fa-eye-slash password-toggle';
        });

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordInput.type === 'password' ? 'text' : 'password';
            confirmPasswordInput.type = type;
            toggleConfirmPassword.className = type === 'password' ? 'fas fa-eye password-toggle' : 'fas fa-eye-slash password-toggle';
        });
    });
</script>
</body>
</html>

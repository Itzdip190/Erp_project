@extends('layouts.auth')

@section('content')
<div class="auth-container">
    
    <!-- Left Panel: Branding & Impact Showcase -->
    <div class="left-panel">
        <div class="branding">
            <h1 class="branding-title">
                <span class="brand-logo-icon"></span>
                SchoolCloud <span>ERP</span>
            </h1>
        </div>

        <div class="hero-text">
            <h2 class="hero-heading">Empowering education, shaping futures.</h2>
            <p class="hero-desc">The next generation multi-tenant SaaS ERP providing high-performance, real-time metrics for students, staff, and parents.</p>
        </div>

        <div class="stats-row">
            <div class="stat-item">
                <span class="stat-num">900+</span>
                <span class="stat-label">Schools Active</span>
            </div>
            <div class="stat-item">
                <span class="stat-num">100%</span>
                <span class="stat-label">Cloud Isolated</span>
            </div>
            <div class="stat-item">
                <span class="stat-num">99.9%</span>
                <span class="stat-label">Uptime SLA</span>
            </div>
        </div>
    </div>

    <!-- Right Panel: Premium Glassmorphic Login Form -->
    <div class="right-panel">
        <div class="glass-card">
            <div class="card-header">
                <h2 class="card-title">Sign In</h2>
                <p class="card-subtitle">Enter your credentials to access the ERP panel</p>
            </div>

            <!-- Slide-down validation alerts -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div style="margin-bottom: 2px;">• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" id="loginForm">
                @csrf

                <!-- Email Input -->
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="name@school.com" value="{{ old('email') }}" required autofocus autocomplete="email">
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                    <span class="password-toggle" id="passwordToggle">Show</span>
                </div>

                <!-- Form Options -->
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        Remember me
                    </label>
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-primary" id="btnSubmit">
                    <span>Sign In to Cloud</span>
                    <div class="spinner" id="submitSpinner"></div>
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Interactive JS behavior for password toggle and loader state -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('passwordToggle');
        const loginForm = document.getElementById('loginForm');
        const btnSubmit = document.getElementById('btnSubmit');

        // Toggle password visibility
        passwordToggle.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.textContent = 'Hide';
            } else {
                passwordInput.type = 'password';
                passwordToggle.textContent = 'Show';
            }
        });

        // Add submitting state on form submit
        loginForm.addEventListener('submit', function() {
            btnSubmit.classList.add('submitting');
            btnSubmit.disabled = true;
        });
    });
</script>
@endsection

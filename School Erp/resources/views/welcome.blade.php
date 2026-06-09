<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchoolCloud ERP - Smart School & College Management System</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Lato', sans-serif;
            background-color: #fafbfd;
            color: #1e293b;
        }

        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: 'Noto Sans', sans-serif;
            font-weight: 700;
        }

        /* Navbar Styling */
        .navbar-custom {
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 1rem 2rem;
        }

        .navbar-brand-custom {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.3rem;
            font-weight: 800;
            color: #0947ca;
            text-decoration: none;
        }

        .navbar-brand-icon {
            width: 38px;
            height: 38px;
            background-color: #0947ca;
            color: #ffffff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .nav-link-custom {
            font-weight: 600;
            color: #475569;
            transition: color 0.2s;
        }

        .nav-link-custom:hover {
            color: #0947ca;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #0947ca 0%, #031a61 100%);
            color: #ffffff;
            padding: 7rem 0 6rem 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::after {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            top: -50px;
            right: -50px;
        }

        .hero-title {
            font-size: 3.2rem;
            line-height: 1.2;
            letter-spacing: -1px;
            margin-bottom: 1.5rem;
        }

        .hero-sub {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 2.25rem;
            line-height: 1.6;
        }

        /* Features Section */
        .features-section {
            padding: 5rem 0;
        }

        .section-title {
            font-size: 2.25rem;
            text-align: center;
            margin-bottom: 0.5rem;
            color: #0f172a;
        }

        .section-subtitle {
            text-align: center;
            color: #64748b;
            margin-bottom: 3.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .feature-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 2.25rem;
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(9, 71, 202, 0.08);
            border-color: #cbd5e1;
        }

        .feature-icon-wrapper {
            width: 56px;
            height: 56px;
            background-color: rgba(9, 71, 202, 0.08);
            color: #0947ca;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .feature-title {
            font-size: 1.25rem;
            color: #0f172a;
            margin-bottom: 0.75rem;
        }

        .feature-desc {
            font-size: 0.92rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 0;
        }

        /* Pricing Section */
        .pricing-section {
            background-color: #f8fafc;
            padding: 5rem 0;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }

        .pricing-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 2.5rem 2rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            height: 100%;
        }

        .pricing-card.popular {
            border-color: #0947ca;
            box-shadow: 0 10px 30px rgba(9, 71, 202, 0.08);
        }

        .popular-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #0947ca;
            color: #ffffff;
            padding: 4px 14px;
            font-size: 0.72rem;
            font-weight: 700;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .plan-name {
            font-size: 1.15rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }

        .plan-price {
            font-size: 2.5rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .plan-duration {
            font-size: 0.85rem;
            color: #94a3b8;
            margin-bottom: 2rem;
        }

        .plan-features-list {
            list-style: none;
            padding: 0;
            margin: 0 0 2.25rem 0;
            text-align: left;
        }

        .plan-features-list li {
            font-size: 0.92rem;
            color: #475569;
            margin-bottom: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .plan-features-list li i {
            color: #10b981;
        }

        /* Call To Action */
        .cta-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            padding: 5rem 0;
            text-align: center;
        }

        .cta-title {
            font-size: 2.25rem;
            margin-bottom: 1rem;
        }

        .cta-desc {
            color: #94a3b8;
            max-width: 550px;
            margin: 0 auto 2rem auto;
        }

        /* Footer */
        .footer {
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 3rem 0;
            font-size: 0.88rem;
            color: #64748b;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            text-decoration: none;
            margin-bottom: 1rem;
        }

        .footer-logo-icon {
            width: 32px;
            height: 32px;
            background-color: #0947ca;
            color: #ffffff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
        }

        .footer-links-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links-list li {
            margin-bottom: 0.5rem;
        }

        .footer-links-list a {
            color: #64748b;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links-list a:hover {
            color: #0947ca;
        }
    </style>
</head>
<body>

    <!-- Header Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand-custom" href="/">
                <div class="navbar-brand-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <span>SchoolCloud ERP</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom active" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#pricing">Pricing Plans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#support">Support</a>
                    </li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-primary px-4 fw-bold" style="border-radius: 20px; border-color: #cbd5e1; color: #475569;">Sign In</a>
                    <a href="#pricing" class="btn btn-primary px-4 fw-bold" style="background-color: #0947ca; border-color: #0947ca; border-radius: 20px; box-shadow: 0 4px 10px rgba(9, 71, 202, 0.15);">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-title">Empowering Education. Enriching Future.</h1>
                    <p class="hero-sub">SchoolCloud ERP is a premium, secure multi-tenant management portal built to streamline administration, fee collection, staff allocation, and student performance tracking for modern schools and colleges.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('login') }}" class="btn btn-light btn-lg px-4 py-3 fw-bold text-primary" style="border-radius: 12px; font-size: 1rem; color: #0947ca !important; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                            <i class="fas fa-sign-in-alt me-2"></i>Login to Portal
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg px-4 py-3 fw-bold" style="border-radius: 12px; font-size: 1rem;">
                            Explore Features
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0 text-center">
                    <img src="{{ asset('images/login_illustration.png') }}" alt="SchoolCloud Dashboard Mockup" class="img-fluid" style="max-height: 400px; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));">
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <h2 class="section-title">All-in-One ERP Solution</h2>
            <p class="section-subtitle">Unlock full administrative control and digital agility with modules tailor-made for institutional success.</p>
            
            <div class="row g-4">
                <!-- Card 1 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h4 class="feature-title">Academics</h4>
                        <p class="feature-desc">Manage curriculums, lesson schedules, exams, gradebooks, and reports seamlessly across classes.</p>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <h4 class="feature-title">Student Mgmt</h4>
                        <p class="feature-desc">Track admissions, attendance records, profiles, medical reports, and dynamic student badges.</p>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h4 class="feature-title">Fee Collection</h4>
                        <p class="feature-desc">Automate invoicing, track payment gateways, generate receipts, and manage outstanding dues.</p>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <h4 class="feature-title">Live Reports</h4>
                        <p class="feature-desc">Access financial charts, plan distributions, and registration trends directly on your dashboard.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing-section" id="pricing">
        <div class="container">
            <h2 class="section-title">Transparent Pricing Plans</h2>
            <p class="section-subtitle">Choose the plan that matches your institutional requirements. Scale up at any time.</p>
            
            <div class="row g-4 justify-content-center">
                <!-- Plan 1 -->
                <div class="col-md-6 col-lg-4">
                    <div class="pricing-card">
                        <div class="plan-name">Basic Plan</div>
                        <div class="plan-price">₹ 5,000</div>
                        <div class="plan-duration">for 30 Days</div>
                        <ul class="plan-features-list">
                            <li><i class="fas fa-check-circle"></i> Up to 100 Students</li>
                            <li><i class="fas fa-check-circle"></i> Standard Academics Module</li>
                            <li><i class="fas fa-check-circle"></i> Single-tenant Support</li>
                            <li><i class="fas fa-check-circle"></i> Daily Email Backups</li>
                        </ul>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 py-2.5 fw-bold" style="border-radius: 10px;">Select Plan</a>
                    </div>
                </div>
                <!-- Plan 2 -->
                <div class="col-md-6 col-lg-4">
                    <div class="pricing-card popular">
                        <div class="popular-badge">Most Popular</div>
                        <div class="plan-name">Standard Plan</div>
                        <div class="plan-price">₹ 15,000</div>
                        <div class="plan-duration">for 90 Days</div>
                        <ul class="plan-features-list">
                            <li><i class="fas fa-check-circle"></i> Up to 500 Students</li>
                            <li><i class="fas fa-check-circle"></i> Advanced Academics & Attendance</li>
                            <li><i class="fas fa-check-circle"></i> Integrated Fee Management</li>
                            <li><i class="fas fa-check-circle"></i> 24/7 Standard Support</li>
                        </ul>
                        <a href="{{ route('login') }}" class="btn btn-primary w-100 py-2.5 fw-bold" style="background-color: #0947ca; border-color: #0947ca; border-radius: 10px;">Select Plan</a>
                    </div>
                </div>
                <!-- Plan 3 -->
                <div class="col-md-6 col-lg-4">
                    <div class="pricing-card">
                        <div class="plan-name">Premium Plan</div>
                        <div class="plan-price">₹ 50,000</div>
                        <div class="plan-duration">for 365 Days</div>
                        <ul class="plan-features-list">
                            <li><i class="fas fa-check-circle"></i> Unlimited Students</li>
                            <li><i class="fas fa-check-circle"></i> Full ERP Modules Access</li>
                            <li><i class="fas fa-check-circle"></i> Live Financial Analytics & Charting</li>
                            <li><i class="fas fa-check-circle"></i> Dedicated Priority Support</li>
                        </ul>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 py-2.5 fw-bold" style="border-radius: 10px;">Select Plan</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section" id="support">
        <div class="container">
            <h2 class="cta-title">Streamline Your Campus Today</h2>
            <p class="cta-desc">Join more than 900+ trusted schools and colleges globally that rely on SchoolCloud ERP for their everyday educational administration.</p>
            <a href="{{ route('login') }}" class="btn btn-light btn-lg px-5 py-3 fw-bold text-primary" style="border-radius: 12px; font-size: 1rem; color: #0947ca !important;">
                Request Demo Account
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <a class="footer-logo" href="/">
                        <div class="footer-logo-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <span>SchoolCloud ERP</span>
                    </a>
                    <p class="mt-2 text-muted">A premium cloud ERP management portal providing institutional agility, automated finance records, and optimized parent-student workflows.</p>
                </div>
                <div class="col-6 col-lg-2 offset-lg-2">
                    <h5 class="text-dark">Company</h5>
                    <ul class="footer-links-list">
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Partners</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h5 class="text-dark">Solutions</h5>
                    <ul class="footer-links-list">
                        <li><a href="#">For Schools</a></li>
                        <li><a href="#">For Colleges</a></li>
                        <li><a href="#">For Academies</a></li>
                        <li><a href="#">Pricing</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h5 class="text-dark">Resources</h5>
                    <ul class="footer-links-list">
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Integrations</a></li>
                        <li><a href="#">System Status</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-top mt-4 pt-4 text-center">
                <p class="mb-0">&copy; {{ date('Y') }} SchoolCloud ERP. All rights reserved. Built with security and compliance.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Educorerp - Next-Gen Smart School & College Management System')</title>
    <meta name="description" content="@yield('meta_description', 'Cloud-based, AI-powered School & College ERP software designed to automate admissions, fee management, student LMS, staff apps, and institution operations.')">

    <!-- Performance Preconnect & Preload Hints -->
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Noto+Sans:ital,wght@0,400;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Landing CSS -->
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v=1.0.2">
    @yield('extra_css')
</head>
<body>

    <!-- 1. Top Header Bar (Image 1 Style) -->
    <div class="top-header-bar">
        <div class="container d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <span><i class="fas fa-headset me-1 text-primary"></i> For queries, contact:</span>
                <a href="mailto:info@educorerp.com"><i class="fas fa-envelope me-1"></i> info@educorerp.com</a>
            </div>
            <div class="d-none d-sm-flex align-items-center gap-3">
                <a href="tel:+919451805575"><i class="fas fa-phone-alt me-1"></i> +91 94518 05575</a>
                <span>|</span>
                <a href="{{ route('login') }}" class="fw-bold text-white"><i class="fas fa-sign-in-alt me-1"></i> ERP Portal Login</a>
            </div>
        </div>
    </div>

    <!-- 2. Main Navigation Bar (Image 1 Style) -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand-custom" href="{{ url('/') }}">
                <div class="navbar-brand-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <span>Educorerp</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
                    <li class="nav-item">
                        <a class="nav-link-custom {{ request()->is('/') || request()->is('welcome') ? 'active' : '' }}" href="{{ url('/') }}">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom {{ request()->is('software*') ? 'active' : '' }}" href="{{ route('landing.software') }}">
                            Education ERP Software
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom {{ request()->is('features*') ? 'active' : '' }}" href="{{ route('landing.features') }}">
                            Features
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom {{ request()->is('resources*') ? 'active' : '' }}" href="{{ route('landing.resources') }}">
                            Resources
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom {{ request()->is('about*') ? 'active' : '' }}" href="{{ route('landing.about') }}">
                            About Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom {{ request()->is('contact*') ? 'active' : '' }}" href="{{ route('landing.contact') }}">
                            Contact Us
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('landing.book-demo') }}" class="btn-demo-nav">
                        Get A Demo <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Injection -->
    <main>
        @yield('content')
    </main>

    <!-- 3. Interactive Module Detail Modal ("Learn More" Popup) -->
    <div class="modal fade" id="moduleDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom d-flex align-items-center justify-content-between">
                    <h5 class="modal-title font-heading fw-bold text-white d-flex align-items-center gap-2" id="modalModuleTitle">
                        Module Features
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <p class="text-secondary leading-relaxed mb-4" id="modalModuleDesc">
                        Detailed information about this ERP module.
                    </p>
                    
                    <div class="bg-light p-4 rounded-4 mb-4 border">
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-star text-warning me-2"></i> Key Capabilities Included:</h6>
                        <ul class="list-unstyled mb-0" id="modalModuleFeatures">
                            <!-- Injected by JavaScript -->
                        </ul>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center pt-2 border-top">
                        <div class="text-muted small">
                            <i class="fas fa-users text-primary me-1"></i> Target Users: <span id="modalModuleRole" class="fw-bold text-dark">Management & Staff</span>
                        </div>
                        <a href="{{ route('landing.book-demo') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                            Request Demo For This Module <i class="fas fa-chevron-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Video Walkthrough Modal -->
    <div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-dark border-0 rounded-4 overflow-hidden">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=0" title="ERP Walkthrough Video" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Main Footer -->
    <footer class="footer-wrapper">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <a class="navbar-brand-custom text-white mb-3 d-inline-flex" href="{{ url('/') }}">
                        <div class="navbar-brand-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <span class="text-white">Educorerp</span>
                    </a>
                    <p class="text-slate-400 mt-2 mb-4 leading-relaxed" style="color: #94a3b8;">
                        Educorerp provides a complete suite of solutions for admissions, billing, student apps, library management, HR, LMS, and transport—all in one powerful platform.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <h5 class="footer-title">Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li><a href="{{ route('landing.software') }}">Education ERP</a></li>
                        <li><a href="{{ route('landing.features') }}">All Features</a></li>
                        <li><a href="{{ route('landing.resources') }}">Resources</a></li>
                        <li><a href="{{ route('landing.about') }}">About Us</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-3">
                    <h5 class="footer-title">ERP Solutions</h5>
                    <ul class="footer-links">
                        <li><a href="{{ route('landing.software') }}">School Management</a></li>
                        <li><a href="{{ route('landing.software') }}">College & University ERP</a></li>
                        <li><a href="{{ route('landing.features') }}">Student & Parent Mobile App</a></li>
                        <li><a href="{{ route('landing.features') }}">Teacher & Faculty App</a></li>
                        <li><a href="{{ route('landing.features') }}">Basic AI Support & LMS</a></li>
                    </ul>
                </div>

                <div class="col-lg-3">
                    <h5 class="footer-title">Get In Touch</h5>
                    <p class="text-slate-400 mb-2" style="color: #94a3b8;"><i class="fas fa-envelope text-primary me-2"></i> info@educorerp.com</p>
                    <p class="text-slate-400 mb-2" style="color: #94a3b8;"><i class="fas fa-phone-alt text-primary me-2"></i> +91 94518 05575</p>
                    <p class="text-slate-400 mb-3" style="color: #94a3b8;"><i class="fas fa-map-marker-alt text-primary me-2"></i> Sector 88A Gurgaon, Haryana</p>
                    <a href="{{ route('landing.book-demo') }}" class="btn btn-sm btn-primary fw-bold px-3 py-2 rounded-3">
                        <i class="fas fa-calendar-check me-1"></i> Schedule Live Demo
                    </a>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                    <div>
                        &copy; {{ date('Y') }} Educorerp Software. All rights reserved.
                    </div>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-slate-400 text-decoration-none">Privacy Policy</a>
                        <span>•</span>
                        <a href="#" class="text-slate-400 text-decoration-none">Terms of Service</a>
                        <span>•</span>
                        <a href="#" class="text-slate-400 text-decoration-none">Security Overview</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Landing JS -->
    <script src="{{ asset('js/landing.js') }}?v=1.0.2"></script>
    @yield('extra_js')
</body>
</html>

@extends('landing.layout')

@section('title', 'Education ERP Software Solutions - Educorerp')
@section('meta_description', 'Comprehensive Cloud & AI Education ERP software solutions tailored for K-12 Schools, Colleges, Higher Education, and Coaching Institutes.')

@section('content')
    <!-- Page Header -->
    <div class="page-header-wrapper">
        <div class="container">
            <h1 class="page-header-title">Education ERP Software Solutions</h1>
            <p class="page-header-lead">
                An all-in-one unified cloud platform built for modern educational institutions, automating everything from student admissions to alumni networks.
            </p>
        </div>
    </div>

    <!-- Software Solutions Grid -->
    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="section-header">
                <h2 class="section-title">Tailored for Every Institution Type</h2>
                <div class="section-title-line mx-auto"></div>
                <p class="section-subtitle mt-3">Whether you run a single K-12 school or a multi-campus university network, Educorerp scales seamlessly.</p>
            </div>

            <div class="row g-4">
                <!-- School ERP -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card text-start">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-school"></i>
                        </div>
                        <h4 class="feature-card-title text-start">K-12 School ERP</h4>
                        <p class="feature-card-desc text-start">Automate attendance, timetable, homework, parent notifications, CBCE grading, and fee collections for primary & secondary schools.</p>
                        <a href="{{ route('landing.book-demo') }}" class="btn btn-outline-primary rounded-pill btn-sm fw-bold">Explore Solution <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>

                <!-- College & University -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card text-start">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-university"></i>
                        </div>
                        <h4 class="feature-card-title text-start">Colleges & Higher Ed</h4>
                        <p class="feature-card-desc text-start">Credit system management, semester examinations, placement drives, hostel allocations, research logs, and department budgeting.</p>
                        <a href="{{ route('landing.book-demo') }}" class="btn btn-outline-primary rounded-pill btn-sm fw-bold">Explore Solution <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>

                <!-- Coaching & Training Institutes -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card text-start">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h4 class="feature-card-title text-start">Coaching Institutes</h4>
                        <p class="feature-card-desc text-start">Batch management, mock test engines, performance analysis, enquiry CRM, installment fee tracking, and online study material.</p>
                        <a href="{{ route('landing.book-demo') }}" class="btn btn-outline-primary rounded-pill btn-sm fw-bold">Explore Solution <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>

                <!-- Multi-Branch Group -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card text-start">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-sitemap"></i>
                        </div>
                        <h4 class="feature-card-title text-start">Group of Institutions</h4>
                        <p class="feature-card-desc text-start">Multi-tenant centralization, consolidated financial reporting, cross-branch staff movement, and unified group analytics.</p>
                        <a href="{{ route('landing.book-demo') }}" class="btn btn-outline-primary rounded-pill btn-sm fw-bold">Explore Solution <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Pillars Section -->
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill mb-3">Enterprise Cloud ERP</span>
                    <h2 class="font-heading fw-bold text-dark display-6 mb-4">Why Educational Leaders Trust Educorerp</h2>
                    
                    <div class="d-flex gap-3 mb-4">
                        <div class="feature-icon-wrapper flex-shrink-0" style="width: 50px; height: 50px; font-size: 1.3rem;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark">Bank-Grade Data Security</h5>
                            <p class="text-secondary small mb-0">Role-based access control, SSL encryption, daily automated backups, and complete audit logs.</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <div class="feature-icon-wrapper flex-shrink-0" style="width: 50px; height: 50px; font-size: 1.3rem;">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark">Dedicated Native Mobile Apps</h5>
                            <p class="text-secondary small mb-0">Native Android & iOS mobile applications for Teachers, Students, Parents, and Drivers.</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <div class="feature-icon-wrapper flex-shrink-0" style="width: 50px; height: 50px; font-size: 1.3rem;">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark">AI-Powered Insights</h5>
                            <p class="text-secondary small mb-0">Smart analytics for fee default predictions, student attendance trends, and automated report generation.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 text-center">
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80" alt="ERP Software Dashboard" class="img-fluid rounded-4 shadow-lg border">
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom CTA Banner -->
    <div class="container">
        <div class="transform-cta-banner text-center">
            <h2 class="transform-cta-title text-white">Experience Next-Gen Education Management</h2>
            <p class="transform-cta-desc">
                Book a personalized live walkthrough with our education technology experts today.
            </p>
            <a href="{{ route('landing.book-demo') }}" class="btn btn-light btn-lg rounded-pill fw-bold text-primary px-5 shadow">
                Book Your Free Demo Now <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
@endsection

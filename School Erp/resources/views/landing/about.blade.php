@extends('landing.layout')

@section('title', 'About Us - Educorerp Expertise & Vision')
@section('meta_description', 'With over 20 years of industry expertise, Educorerp delivers efficient, user-friendly, and innovative education software that transforms school management.')

@section('content')
    <!-- Page Header -->
    <div class="page-header-wrapper">
        <div class="container">
            <h1 class="page-header-title">About Educorerp</h1>
            <p class="page-header-lead">
                Transforming the way educational institutions operate through innovative, user-friendly, and AI-powered cloud technology.
            </p>
        </div>
    </div>

    <!-- About Content Section -->
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill mb-3">Over 20 Years of Expertise</span>
                    <h2 class="font-heading fw-bold text-dark display-6 mb-4">Empowering Schools & Colleges Nationwide</h2>
                    <p class="text-secondary leading-relaxed mb-4">
                        With over 20 years of industry expertise, we deliver efficient, user-friendly, and innovative education software that streamlines operations and transforms the way your institution runs.
                    </p>
                    <p class="text-secondary leading-relaxed mb-4">
                        From student admissions, daily attendance, online fee collections, and exams to basic AI support and live transport GPS tracking, our platform combines every administrative and academic workflow under a single, unified interface.
                    </p>

                    <div class="row g-3 pt-2">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <h3 class="fw-bold text-primary mb-1">200+</h3>
                                <div class="small text-muted fw-bold">Institutions Empowered</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <h3 class="fw-bold text-primary mb-1">50,000+</h3>
                                <div class="small text-muted fw-bold">Active Students & Parents</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <h3 class="fw-bold text-primary mb-1">99.9%</h3>
                                <div class="small text-muted fw-bold">System Uptime SLA</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <h3 class="fw-bold text-primary mb-1">24/7</h3>
                                <div class="small text-muted fw-bold">Dedicated Helpdesk Support</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=800&q=80" alt="Education Technology Leadership" class="img-fluid rounded-4 shadow-lg">
                        <div class="bg-primary text-white p-4 rounded-4 position-absolute bottom-0 start-0 translate-middle-y ms-n4 shadow d-none d-md-block" style="max-width: 260px;">
                            <i class="fas fa-quote-left fs-3 opacity-50 mb-2"></i>
                            <div class="small fw-bold">Building next-gen digital infrastructure for tomorrow's learning leaders.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Core Values Section -->
    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="section-header">
                <h2 class="section-title">Our Mission & Values</h2>
                <div class="section-title-line mx-auto"></div>
                <p class="section-subtitle mt-3">The foundational principles driving our products and partner relationships.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 rounded-4 p-4 shadow-sm h-100 text-center">
                        <div class="feature-icon-wrapper mx-auto mb-3">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Simplicity First</h4>
                        <p class="text-secondary small mb-0">We craft clean, intuitive interfaces that teachers, accountants, and parents can start using immediately without extensive technical training.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 rounded-4 p-4 shadow-sm h-100 text-center">
                        <div class="feature-icon-wrapper mx-auto mb-3">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Continuous Innovation</h4>
                        <p class="text-secondary small mb-0">Integrating AI doubt solvers, real-time GPS tracking, and instant WhatsApp alerts to keep your institution ahead of the curve.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 rounded-4 p-4 shadow-sm h-100 text-center">
                        <div class="feature-icon-wrapper mx-auto mb-3">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Unwavering Support</h4>
                        <p class="text-secondary small mb-0">Every client receives dedicated account managers, comprehensive data migration support, and 24/7 technical helpdesk assistance.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom CTA Banner -->
    <div class="container">
        <div class="transform-cta-banner text-center">
            <h2 class="transform-cta-title text-white">Join 200+ Modern Institutions</h2>
            <p class="transform-cta-desc">
                Partner with Educorerp and transform your campus with smart automation today.
            </p>
            <a href="{{ route('landing.book-demo') }}" class="btn btn-light btn-lg rounded-pill fw-bold text-primary px-5 shadow">
                Schedule a Demo <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
@endsection

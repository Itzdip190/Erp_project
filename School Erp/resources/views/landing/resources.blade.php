@extends('landing.layout')

@section('title', 'Resources, Guides & Walkthroughs - Educorerp')
@section('meta_description', 'Access Educorerp user documentation, video walkthroughs, implementation guides, case studies, and frequently asked questions.')

@section('content')
    <!-- Page Header -->
    <div class="page-header-wrapper">
        <div class="container">
            <h1 class="page-header-title">Educorerp Resource Center</h1>
            <p class="page-header-lead">
                Everything you need to successfully launch, train staff, and get the most out of your school ERP platform.
            </p>
        </div>
    </div>

    <!-- Resources Section -->
    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="row g-4">
                <!-- Video Walkthroughs -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm h-100 p-4">
                        <div class="feature-icon-wrapper mb-3">
                            <i class="fas fa-video"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Video Walkthroughs</h4>
                        <p class="text-secondary small mb-4">Watch step-by-step video tutorials covering admission setup, fee structure creation, and report card generation.</p>
                        <button class="btn btn-outline-primary rounded-pill btn-sm mt-auto fw-bold" data-bs-toggle="modal" data-bs-target="#videoModal">
                            <i class="fas fa-play me-1"></i> Watch Main Walkthrough
                        </button>
                    </div>
                </div>

                <!-- Documentation & Manuals -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm h-100 p-4">
                        <div class="feature-icon-wrapper mb-3">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">User Guides & Manuals</h4>
                        <p class="text-secondary small mb-4">Comprehensive PDF user manuals for Principal, Accountant, Class Teacher, Administrator, and Parents.</p>
                        <a href="{{ route('landing.book-demo') }}" class="btn btn-outline-primary rounded-pill btn-sm mt-auto fw-bold">
                            <i class="fas fa-download me-1"></i> Request Knowledge Base
                        </a>
                    </div>
                </div>

                <!-- Case Studies -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm h-100 p-4">
                        <div class="feature-icon-wrapper mb-3">
                            <i class="fas fa-award"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Success Stories</h4>
                        <p class="text-secondary small mb-4">Discover how 200+ schools and colleges saved 15+ hours weekly per teacher using smart automation.</p>
                        <a href="{{ route('landing.book-demo') }}" class="btn btn-outline-primary rounded-pill btn-sm mt-auto fw-bold">
                            Read Case Studies <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="section-header">
                <h2 class="section-title">Frequently Asked Questions</h2>
                <div class="section-title-line mx-auto"></div>
                <p class="section-subtitle mt-3">Answers to common questions about cloud setup, data migration, and security.</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="accordion accordion-flush rounded-4 overflow-hidden border" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How long does it take to deploy Educorerp for our institution?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary">
                                    Educorerp is 100% cloud-based. Most schools get fully set up with student data migration, class creation, and staff logins in less than 48 hours. Our onboarding team handles all data importing.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Can we customize report cards, fee receipts, and certificates?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary">
                                    Yes! All templates (Report Cards, Transfer Certificates, ID Cards, Fee Receipts, Salary Slips) are completely customizable with your institution's logo, header background, and specific grading logic.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Is there a dedicated mobile app for parents and teachers?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary">
                                    Yes, we provide native Android & iOS mobile applications tailored for Teachers, Parents, Students, and School Management with instant push notifications.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    How secure is our school data on the ERP platform?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary">
                                    We enforce SSL 256-bit encryption, strict role-based access permissions, daily automated off-site backups, and strict privacy standards ensuring your data is never shared.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom CTA Banner -->
    <div class="container">
        <div class="transform-cta-banner text-center">
            <h2 class="transform-cta-title text-white">Have More Questions?</h2>
            <p class="transform-cta-desc">
                Our education software specialists are ready to answer any questions and walk you through a live demonstration.
            </p>
            <a href="{{ route('landing.book-demo') }}" class="btn btn-light btn-lg rounded-pill fw-bold text-primary px-5 shadow">
                Talk To Our Team <i class="fas fa-phone-alt ms-2"></i>
            </a>
        </div>
    </div>
@endsection

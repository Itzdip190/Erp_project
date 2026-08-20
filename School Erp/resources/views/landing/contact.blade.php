@extends('landing.layout')

@section('title', 'Contact Us - Educorerp Support & Inquiries')
@section('meta_description', 'Get in touch with Educorerp for product demos, technical support, sales queries, or partnership opportunities.')

@section('content')
    <!-- Page Header -->
    <div class="page-header-wrapper">
        <div class="container">
            <h1 class="page-header-title">Contact Us</h1>
            <p class="page-header-lead">
                Have questions about our ERP platform or need technical assistance? Our expert team is here to help 24/7.
            </p>
        </div>
    </div>

    <!-- Contact Info & Form Section -->
    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="row g-5">
                <!-- Left: Contact Details -->
                <div class="col-lg-5">
                    <div class="card border-0 rounded-4 shadow-sm p-4 h-100">
                        <h3 class="fw-bold text-dark mb-4">Direct Contact Channels</h3>
                        
                        <div class="d-flex gap-3 mb-4">
                            <div class="feature-icon-wrapper flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.2rem;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Official Email</h6>
                                <a href="mailto:info@educorerp.com" class="text-primary text-decoration-none fw-bold">info@educorerp.com</a>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mb-4">
                            <div class="feature-icon-wrapper flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.2rem;">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Helpline Phone</h6>
                                <a href="tel:+919451805575" class="text-dark text-decoration-none fw-bold">+91 94518 05575</a>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mb-4">
                            <div class="feature-icon-wrapper flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.2rem;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Headquarters Location</h6>
                                <p class="text-secondary small mb-0">Sector 88A Gurgaon, Haryana</p>
                            </div>
                        </div>

                        <div class="p-3 bg-primary-subtle rounded-3 text-primary mt-auto">
                            <i class="fas fa-clock me-2"></i> <strong>Support Hours:</strong> Monday – Saturday, 9:00 AM – 7:00 PM IST. Emergency SLA 24/7.
                        </div>
                    </div>
                </div>

                <!-- Right: Quick Contact Form -->
                <div class="col-lg-7">
                    <div class="demo-form-card">
                        <h3 class="demo-form-title text-start mb-2">Send Us a Direct Message</h3>
                        <p class="text-muted small mb-4">Fill out the form below and an ERP specialist will respond within 2 business hours.</p>
                        
                        <form id="contactPageForm" action="{{ route('landing.book-demo.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="role" value="Contact Page Inquiry">
                            <input type="hidden" name="city" value="General">
                            <input type="hidden" name="state" value="General">
                            <input type="hidden" name="country" value="India">

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Your Name *</label>
                                    <input type="text" name="full_name" class="form-control form-control-custom" placeholder="Enter full name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Email Address *</label>
                                    <input type="email" name="email" class="form-control form-control-custom" placeholder="Enter email address" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Mobile Number *</label>
                                    <input type="tel" name="phone" class="form-control form-control-custom" placeholder="Enter mobile number" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary small">Institution Name</label>
                                    <input type="text" name="institute_name" class="form-control form-control-custom" placeholder="Enter school/college name">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-secondary small">Message / Inquiry *</label>
                                <textarea name="message" rows="4" class="form-control form-control-custom" placeholder="Write your requirements or question..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-submit-demo">
                                Send Message <i class="fas fa-paper-plane ms-1"></i>
                            </button>

                            <div id="contactAlertBox" class="mt-3" style="display:none;"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

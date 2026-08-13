@extends('landing.layout')

@section('title', 'Book a Free Live Demo — EducoreERP Walkthrough')
@section('meta_description', 'Schedule a personalized 1-on-1 live walkthrough of EducoreERP with our product specialist. See how we automate admissions, fee management, student apps, and staff operations.')

@section('extra_css')
<style>
    /* Modern SaaS Book a Demo Page Styling */
    .demo-hero-wrapper {
        background: linear-gradient(135deg, #0947ca 0%, #031a61 100%);
        color: #ffffff;
        padding: 60px 0 90px;
        position: relative;
        overflow: hidden;
    }
    .demo-hero-wrapper::after {
        content: '';
        position: absolute;
        bottom: -50px;
        left: 0;
        right: 0;
        height: 100px;
        background: #f8fafc;
        transform: skewY(-2deg);
        z-index: 1;
    }
    .demo-hero-content {
        position: relative;
        z-index: 2;
    }
    .demo-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #ffffff;
        padding: 6px 18px;
        border-radius: 50px;
        font-size: 0.88rem;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .demo-hero-title {
        font-size: 2.8rem;
        font-weight: 800;
        line-height: 1.25;
        letter-spacing: -0.5px;
        margin-bottom: 16px;
    }
    .demo-hero-subtitle {
        font-size: 1.15rem;
        color: rgba(255, 255, 255, 0.88);
        max-width: 680px;
        margin: 0 auto 30px;
        line-height: 1.6;
    }
    .trust-pills {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
    }
    .trust-pill-item {
        background: rgba(255, 255, 255, 0.1);
        padding: 8px 18px;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.95);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Main Container Section */
    .demo-main-section {
        position: relative;
        z-index: 3;
        margin-top: -50px;
        padding-bottom: 80px;
        background: #f8fafc;
    }

    /* Sidebar Contact & Info Cards */
    .contact-info-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .contact-info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08);
    }
    .contact-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }
    .contact-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #eff6ff;
        color: #0947ca;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    .contact-card-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .phone-list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 0.92rem;
        border-bottom: 1px dashed #f1f5f9;
    }
    .phone-list-item:last-child {
        border-bottom: none;
    }
    .phone-label {
        font-weight: 600;
        color: #475569;
    }
    .phone-number {
        font-weight: 700;
        color: #0947ca;
        text-decoration: none;
    }
    .phone-number:hover {
        text-decoration: underline;
    }

    /* 4-Step Interactive Booking Engine Card */
    .booking-engine-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(9, 71, 202, 0.08);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .booking-header-bar {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 20px 28px;
    }
    .step-progress-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
    }
    .step-progress-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        z-index: 2;
        cursor: pointer;
    }
    .step-badge {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #64748b;
        font-weight: 700;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border: 2px solid #e2e8f0;
    }
    .step-progress-item.active .step-badge {
        background: #0947ca;
        color: #ffffff;
        border-color: #0947ca;
        box-shadow: 0 0 0 4px rgba(9, 71, 202, 0.15);
    }
    .step-progress-item.completed .step-badge {
        background: #10b981;
        color: #ffffff;
        border-color: #10b981;
    }
    .step-text {
        font-size: 0.78rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .step-progress-item.active .step-text {
        color: #0947ca;
    }
    .step-line-bar {
        position: absolute;
        top: 17px;
        left: 15%;
        right: 15%;
        height: 3px;
        background: #e2e8f0;
        z-index: 1;
    }
    .step-line-fill {
        height: 100%;
        background: #0947ca;
        width: 0%;
        transition: width 0.4s ease;
    }

    .booking-body-padding {
        padding: 32px 36px;
    }

    /* Calendar Grid Styling (Step 1) */
    .calendar-container {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        background: #ffffff;
    }
    .calendar-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .calendar-month-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
    }
    .btn-cal-nav {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .btn-cal-nav:hover {
        background: #f1f5f9;
        color: #0947ca;
        border-color: #0947ca;
    }
    .calendar-days-header {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
        font-weight: 700;
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 12px;
    }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
    }
    .calendar-day-btn {
        aspect-ratio: 1;
        border: 1px solid #f1f5f9;
        background: #ffffff;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        color: #1e293b;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .calendar-day-btn:hover:not(.disabled) {
        background: #eff6ff;
        color: #0947ca;
        border-color: #93c5fd;
    }
    .calendar-day-btn.selected {
        background: #0947ca !important;
        color: #ffffff !important;
        border-color: #0947ca !important;
        box-shadow: 0 4px 12px rgba(9, 71, 202, 0.3);
    }
    .calendar-day-btn.disabled {
        color: #cbd5e1;
        background: #f8fafc;
        cursor: not-allowed;
        border-color: transparent;
    }
    .calendar-day-btn.today {
        border-color: #0947ca;
    }

    /* Time Slots Styling (Step 2) */
    .time-slot-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 12px;
        max-height: 380px;
        overflow-y: auto;
        padding-right: 5px;
    }
    .time-slot-btn {
        border: 1.5px solid #0947ca;
        background: #ffffff;
        color: #0947ca;
        border-radius: 10px;
        padding: 12px 14px;
        font-weight: 700;
        font-size: 0.92rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .time-slot-btn:hover {
        background: #0947ca;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(9, 71, 202, 0.2);
    }
    .time-slot-btn.selected {
        background: #0947ca;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(9, 71, 202, 0.35);
    }

    /* Details Form Inputs (Step 3) */
    .form-control-saas {
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }
    .form-control-saas:focus {
        border-color: #0947ca;
        box-shadow: 0 0 0 4px rgba(9, 71, 202, 0.12);
    }

    /* Summary Bar for Steps 2 & 3 */
    .summary-info-bar {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 14px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .summary-info-text {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e3a8a;
    }

    /* Success Card (Step 4) */
    .success-check-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #d1fae5;
        color: #10b981;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 20px;
    }

    @media (max-width: 768px) {
        .demo-hero-title { font-size: 2rem; }
        .booking-body-padding { padding: 20px 16px; }
        .time-slot-grid { grid-template-columns: repeat(2, 1fr); }
        .step-text { display: none; }
    }
</style>
@endsection

@section('content')
    <!-- 1. SaaS Hero Section -->
    <section class="demo-hero-wrapper text-center">
        <div class="container demo-hero-content">
            <div class="demo-hero-badge">
                <i class="fas fa-calendar-check text-warning"></i> Instant 1-on-1 Product Walkthrough
            </div>
            <h1 class="demo-hero-title">Experience EducoreERP in Action</h1>
            <p class="demo-hero-subtitle">
                Reserve your personalized live demo with our Senior Product Specialist. Learn how to digitize admissions, fees, exams, student apps, and staff operations.
            </p>
            <div class="trust-pills">
                <span class="trust-pill-item"><i class="fas fa-school text-warning"></i> 500+ Institutions</span>
                <span class="trust-pill-item"><i class="fas fa-clock text-warning"></i> 15-Minute Session</span>
                <span class="trust-pill-item"><i class="fas fa-shield-alt text-warning"></i> 100% Free & No Obligation</span>
                <span class="trust-pill-item"><i class="fas fa-star text-warning"></i> Rated 4.9/5</span>
            </div>
        </div>
    </section>

    <!-- 2. Main Booking Engine Section -->
    <section class="demo-main-section">
        <div class="container">
            <div class="row g-4 justify-content-center">
                
                <!-- Left Sidebar: Support Info & Branch Office -->
                <div class="col-lg-4 col-md-5">
                    
                    <!-- Contact Info Card -->
                    <div class="contact-info-card">
                        <div class="contact-card-header">
                            <div class="contact-card-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <h3 class="contact-card-title">Contact Info</h3>
                        </div>
                        <div class="phone-list-item">
                            <span class="phone-label">Phone:</span>
                            <a href="tel:+919451805575" class="phone-number">+91-9451805575</a>
                        </div>
                        <div class="phone-list-item">
                            <span class="phone-label">Email:</span>
                            <a href="mailto:vedantpublicschool@gmail.com" class="phone-number" style="word-break: break-all; font-size: 0.85rem;">vedantpublicschool@gmail.com</a>
                        </div>
                    </div>

                    <!-- Branch Office Card -->
                    <div class="contact-info-card">
                        <div class="contact-card-header">
                            <div class="contact-card-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <h3 class="contact-card-title">Branch-Office</h3>
                        </div>
                        <p class="text-secondary small mb-0 lh-base">
                            <strong>Vedant Public School</strong><br>
                            Sector 88A Gurgaon, Haryana
                        </p>
                    </div>

                    <!-- Guarantee Badge Card -->
                    <div class="contact-info-card" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="contact-card-icon" style="background: #fef3c7; color: #d97706;">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div>
                                <h4 class="h6 font-heading mb-1 text-dark fw-bold" style="color: #0f172a !important;">Privacy Guaranteed</h4>
                                <p class="small mb-0" style="color: #334155 !important; font-weight: 500;">Your institution data is 100% confidential and secured with enterprise SSL encryption.</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column: 4-Step Booking Engine Card -->
                <div class="col-lg-8 col-md-7">
                    <div class="booking-engine-card">
                        
                        <!-- Step Progress Header -->
                        <div class="booking-header-bar">
                            <div class="step-progress-wrapper">
                                <div class="step-line-bar">
                                    <div class="step-line-fill" id="stepProgressFill"></div>
                                </div>
                                <div class="step-progress-item active" id="progressStep1">
                                    <div class="step-badge">1</div>
                                    <span class="step-text">Date</span>
                                </div>
                                <div class="step-progress-item" id="progressStep2">
                                    <div class="step-badge">2</div>
                                    <span class="step-text">Time</span>
                                </div>
                                <div class="step-progress-item" id="progressStep3">
                                    <div class="step-badge">3</div>
                                    <span class="step-text">Details</span>
                                </div>
                                <div class="step-progress-item" id="progressStep4">
                                    <div class="step-badge">4</div>
                                    <span class="step-text">Done</span>
                                </div>
                            </div>
                        </div>

                        <!-- Step Content Body -->
                        <div class="booking-body-padding">
                            <form id="demoBookingForm" action="{{ route('landing.book-demo.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="booking_date" id="selectedBookingDate" value="">
                                <input type="hidden" name="booking_time" id="selectedBookingTime" value="">
                                <input type="hidden" name="timezone" id="selectedTimezoneVal" value="India Standard Time">
                                <input type="hidden" name="source" id="bookingSource" value="Website">

                                <!-- STEP 1: DATE SELECTION -->
                                <div id="step1Container">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
                                        <div>
                                            <h2 class="h5 font-heading text-dark mb-1">15 Minute Meeting</h2>
                                            <p class="text-muted small mb-0"><i class="fas fa-clock text-primary me-1"></i> 15 min &nbsp;|&nbsp; <i class="fas fa-phone-alt text-primary me-1"></i> Phone / Video call</p>
                                        </div>
                                        <div>
                                            <label class="form-label text-muted small fw-bold mb-1">Time Zone:</label>
                                            <select id="timezoneSelect" class="form-select form-select-sm border-secondary-subtle font-medium">
                                                <option value="India Standard Time" selected>India Standard Time (IST)</option>
                                                <option value="Coordinated Universal Time">Coordinated Universal Time (UTC)</option>
                                                <option value="Eastern Standard Time">Eastern Standard Time (EST)</option>
                                                <option value="Pacific Standard Time">Pacific Standard Time (PST)</option>
                                                <option value="Gulf Standard Time">Gulf Standard Time (GST)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="calendar-container mt-4">
                                        <div class="calendar-nav">
                                            <button type="button" class="btn-cal-nav" id="btnPrevMonth"><i class="fas fa-chevron-left"></i></button>
                                            <div class="calendar-month-title" id="calendarMonthYearTitle">August 2026</div>
                                            <button type="button" class="btn-cal-nav" id="btnNextMonth"><i class="fas fa-chevron-right"></i></button>
                                        </div>
                                        <div class="calendar-days-header">
                                            <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
                                        </div>
                                        <div class="calendar-grid" id="calendarDaysGrid">
                                            <!-- Dynamically populated days -->
                                        </div>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <button type="button" id="btnGoToStep2" class="btn btn-primary btn-lg px-5 py-3 rounded-pill fw-bold" disabled>
                                            Select Time Slot <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- STEP 2: TIME SELECTION -->
                                <div id="step2Container" style="display:none;">
                                    <div class="summary-info-bar">
                                        <div class="summary-info-text" id="step2DateSummary">
                                            <i class="fas fa-calendar-alt text-primary me-2"></i> Wednesday, August 5, 2026
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnBackToStep1">
                                            <i class="fas fa-edit me-1"></i> Change Date
                                        </button>
                                    </div>

                                    <div class="mb-3">
                                        <h3 class="h5 font-heading text-dark mb-1">Select a Time Slot</h3>
                                        <p class="text-muted small">Duration: 15 minutes session</p>
                                    </div>

                                    <div class="time-slot-grid my-4" id="timeSlotGrid">
                                        <!-- Time slots populated via JS -->
                                    </div>

                                    <div class="mt-4 d-flex justify-content-between align-items-center">
                                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" id="btnBackToStep1Sec">
                                            <i class="fas fa-arrow-left me-2"></i> Back
                                        </button>
                                        <button type="button" id="btnGoToStep3" class="btn btn-primary btn-lg px-5 py-3 rounded-pill fw-bold" disabled>
                                            Enter Details <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- STEP 3: DETAILS ENTRY -->
                                <div id="step3Container" style="display:none;">
                                    <div class="summary-info-bar">
                                        <div class="summary-info-text" id="step3Summary">
                                            <i class="fas fa-clock text-primary me-2"></i> 10:15am - 10:30am, Wednesday, Aug 5, 2026
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnBackToStep2">
                                            <i class="fas fa-edit me-1"></i> Change Time
                                        </button>
                                    </div>

                                    <h3 class="h5 font-heading text-dark mb-3">Enter Your Contact Details</h3>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" name="full_name" class="form-control form-control-saas" placeholder="e.g. Dr. Rajesh Sharma" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small">Official Email Address <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control form-control-saas" placeholder="e.g. principal@school.edu.in" required>
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small">Phone Number <span class="text-danger">*</span></label>
                                            <input type="tel" name="phone" class="form-control form-control-saas" placeholder="e.g. +91 9876543210" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small">Institution / Company Name <span class="text-danger">*</span></label>
                                            <input type="text" name="institute_name" class="form-control form-control-saas" placeholder="e.g. St. Xavier Public School" required>
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small">Designation / Role <span class="text-danger">*</span></label>
                                            <select name="role" class="form-select form-control-saas" required>
                                                <option value="" selected disabled>Select your role</option>
                                                <option value="School Owner / Trustee">School Owner / Trustee</option>
                                                <option value="Principal / Director">Principal / Director</option>
                                                <option value="Administrator / Vice Principal">Administrator / Vice Principal</option>
                                                <option value="Accountant / Finance Manager">Accountant / Finance Manager</option>
                                                <option value="IT Head / Computer Teacher">IT Head / Computer Teacher</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small">Total Student Count</label>
                                            <select name="student_count" class="form-select form-control-saas">
                                                <option value="Under 200">Under 200 Students</option>
                                                <option value="200 - 500" selected>200 - 500 Students</option>
                                                <option value="500 - 1500">500 - 1500 Students</option>
                                                <option value="1500+">1500+ Students</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold text-dark small">City <span class="text-danger">*</span></label>
                                            <input type="text" name="city" class="form-control form-control-saas" placeholder="City" value="Jaipur" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold text-dark small">State <span class="text-danger">*</span></label>
                                            <input type="text" name="state" class="form-control form-control-saas" placeholder="State" value="Rajasthan" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold text-dark small">Country <span class="text-danger">*</span></label>
                                            <input type="text" name="country" class="form-control form-control-saas" value="India" required>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-dark small">Please share anything that will help prepare for our meeting <span class="text-danger">*</span></label>
                                        <textarea name="message" rows="3" class="form-control form-control-saas" placeholder="e.g. Interested in Fee collection & Parent mobile app modules..." required></textarea>
                                    </div>

                                    <div id="demoAlertBox" class="mb-3" style="display:none;"></div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" id="btnBackToStep2Sec">
                                            <i class="fas fa-arrow-left me-2"></i> Back
                                        </button>
                                        <button type="submit" id="btnSubmitBooking" class="btn btn-success btn-lg px-5 py-3 rounded-pill fw-bold">
                                            Schedule Event <i class="fas fa-check-circle ms-2"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- STEP 4: SUCCESS CONFIRMATION -->
                                <div id="step4Container" style="display:none;" class="text-center py-4">
                                    <div class="success-check-icon shadow-sm">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <h2 class="h3 font-heading text-dark mb-2">You are Scheduled!</h2>
                                    <p class="text-muted mb-4">A calendar invitation and confirmation details have been emailed to your address.</p>
                                    
                                    <div class="card border-0 bg-light rounded-4 p-4 text-start max-width-500 mx-auto mb-4 shadow-sm">
                                        <div class="border-bottom pb-3 mb-3">
                                            <div class="text-uppercase text-muted small fw-bold">Meeting Summary</div>
                                            <div class="h5 font-heading text-primary mb-0 mt-1">15 Minute Live Walkthrough</div>
                                        </div>
                                        <div class="mb-2">
                                            <strong class="text-dark">Date & Time:</strong>
                                            <div id="summarySuccessDateTime" class="text-secondary"></div>
                                        </div>
                                        <div class="mb-2">
                                            <strong class="text-dark">Time Zone:</strong>
                                            <div id="summarySuccessTZ" class="text-secondary"></div>
                                        </div>
                                        <div class="mb-2">
                                            <strong class="text-dark">Client:</strong>
                                            <div id="summarySuccessClient" class="text-secondary"></div>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-primary rounded-pill px-5 py-2.5 font-heading" onclick="window.location.reload();">
                                        <i class="fas fa-sync-alt me-2"></i> Book Another Demo
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

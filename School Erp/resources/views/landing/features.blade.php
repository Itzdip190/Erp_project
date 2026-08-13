@extends('landing.layout')

@section('title', 'All Features & ERP Modules - Educorerp')
@section('meta_description', 'Explore all 20+ modules of Educorerp including Admission CRM, SIS, Fee Management, Faculty App, Basic AI Support, Transport GPS, and Exam Management.')

@section('content')
    <!-- Page Header -->
    <div class="page-header-wrapper">
        <div class="container">
            <h1 class="page-header-title">Educorerp Complete Feature Matrix</h1>
            <p class="page-header-lead">
                Discover the comprehensive suite of tools engineered to transform school administration, teacher efficiency, and student learning outcomes.
            </p>
        </div>
    </div>

    <!-- Features Section (Image 4 & 5 Style with Hover Buttons and Modal Trigger) -->
    <section class="features-section-wrapper">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Key Modules & Capabilities</h2>
                <div class="section-title-line mx-auto"></div>
                <p class="section-subtitle mt-3">Hover over any feature card below and click <strong>Learn More</strong> to inspect detailed sub-features and capabilities.</p>
            </div>

            <div class="row g-4">
                <!-- 1. Advanced Business Management App -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h3 class="feature-card-title">Advanced Business Management App</h3>
                        <p class="feature-card-desc">
                            Gain real-time visibility into fee collection, admissions, staff attendance, and payroll. Educorerp’s tools enable smooth day-to-day operations and data-driven decision-making.
                        </p>
                        <button class="btn-learn-more-card" data-module="business_mgmt">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- 2. Student Learning App -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h3 class="feature-card-title">Student Learning App</h3>
                        <p class="feature-card-desc">
                            A powerful all-in-one platform for students featuring a digital diary, performance tracking, basic AI support, LMS courses, circular alerts, live bus tracking, and digital student ID.
                        </p>
                        <button class="btn-learn-more-card" data-module="student_app">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- 3. Faculty App -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h3 class="feature-card-title">Faculty App</h3>
                        <p class="feature-card-desc">
                            Empower your teaching staff with a smart app that simplifies daily tasks. Mark attendance, upload test scores, access circulars, manage classes, track leave, and rate student performance.
                        </p>
                        <button class="btn-learn-more-card" data-module="faculty_app">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- 4. Admission & Enquiry CRM -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-funnel-dollar"></i>
                        </div>
                        <h3 class="feature-card-title">Admission and Enquiry CRM</h3>
                        <p class="feature-card-desc">
                            Streamline student admissions with our intelligent CRM. Manage walk-in and online enquiries, assign counselors automatically, schedule follow-ups, and track leads.
                        </p>
                        <button class="btn-learn-more-card" data-module="admission_crm">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- 5. Enquiry Management App -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3 class="feature-card-title">Enquiry Management App</h3>
                        <p class="feature-card-desc">
                            Capture, manage, and convert enquiries on the go. Use our mobile app to handle walk-in and online leads, make direct calls, and automate follow-ups.
                        </p>
                        <button class="btn-learn-more-card" data-module="enquiry_app">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- 6. Basic AI Support -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h3 class="feature-card-title">Basic AI Support</h3>
                        <p class="feature-card-desc">
                            Get essential AI assistance across the platform to help answer routine queries, guide staff and students, and streamline basic campus activities.
                        </p>
                        <button class="btn-learn-more-card" data-module="ai_learning">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- 7. Online Learning LMS -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <h3 class="feature-card-title">Online Learning – LMS</h3>
                        <p class="feature-card-desc">
                            Deliver interactive and flexible learning experiences. Create courses, edit content, track student progress, and engage learners with quizzes and assignments.
                        </p>
                        <button class="btn-learn-more-card" data-module="lms">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- 8. Skill & Progress Tracking -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="feature-card-title">Skill & Progress Tracking</h3>
                        <p class="feature-card-desc">
                            Support student growth with goal setting, personalized learning plans, teacher feedback, and skill development tools. Track progress and encourage continuous improvement.
                        </p>
                        <button class="btn-learn-more-card" data-module="skill_tracking">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- 9. Placement & Alumni -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3 class="feature-card-title">Placement & Alumni Network</h3>
                        <p class="feature-card-desc">
                            Manage placements and alumni networks effortlessly. Organize recruitment drives, track placement records, share job opportunities, and empower students.
                        </p>
                        <button class="btn-learn-more-card" data-module="placement_alumni">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- 10. Fee Collection & Accounting -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <h3 class="feature-card-title">Fee Collection & Accounting</h3>
                        <p class="feature-card-desc">
                            Configure complex fee categories, structure term fees, automate SMS due alerts, process online payments, late fee fines, and print tax receipts.
                        </p>
                        <button class="btn-learn-more-card" data-module="fee_management">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- 11. Transport & Bus Pass -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-bus-alt"></i>
                        </div>
                        <h3 class="feature-card-title">Transport & GPS Bus Tracking</h3>
                        <p class="feature-card-desc">
                            Vehicle fleet management, driver logging, route creation, pickup point mapping, digital bus pass printing, and live GPS bus tracking for parents.
                        </p>
                        <button class="btn-learn-more-card" data-module="transport_mgmt">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- 12. Exams & Marksheets -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <h3 class="feature-card-title">Exams, Report Cards & TC</h3>
                        <p class="feature-card-desc">
                            Exam timetable scheduling, marks entry portal, automated CBSE/ICSE board grading, instant PDF report card generation, and Transfer Certificates.
                        </p>
                        <button class="btn-learn-more-card" data-module="exam_certificate">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Bottom CTA Banner -->
    <div class="container">
        <div class="transform-cta-banner text-center">
            <h2 class="transform-cta-title text-white">Need a Customized Module Configuration?</h2>
            <p class="transform-cta-desc">
                Our modular architecture lets you choose only the modules your institution needs today, with zero setup friction.
            </p>
            <a href="{{ route('landing.book-demo') }}" class="btn btn-light btn-lg rounded-pill fw-bold text-primary px-5 shadow">
                Schedule a Demo & Custom Quote <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
@endsection

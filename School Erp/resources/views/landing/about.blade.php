@extends('landing.layout')

@section('title', 'About Us - Over 20 Years of Expertise | Educorerp')
@section('meta_description', 'Educorerp is a venture of Global Tech Solutions backed by over 20 years of expertise, empowering 200+ schools and colleges nationwide with unified management software.')

@section('extra_css')
<style>
    /* About Page Custom Aesthetics */
    .about-hero-badge {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff;
        font-weight: 700;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35);
    }

    .about-lead-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        box-shadow: 0 20px 45px rgba(9, 71, 202, 0.07);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .about-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 1.5rem;
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
    }

    .about-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(9, 71, 202, 0.12);
        border-color: #93c5fd;
    }

    .about-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #0947ca 0%, #3b82f6 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .about-stat-card:hover::before {
        opacity: 1;
    }

    .stat-number {
        font-family: 'Noto Sans', sans-serif;
        font-size: 2.25rem;
        font-weight: 800;
        background: linear-gradient(135deg, #031a61 0%, #0947ca 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        line-height: 1.2;
    }

    .stat-label {
        font-size: 0.88rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Story Section */
    .story-card-wrapper {
        background: linear-gradient(135deg, #f8fafc 0%, #edf4fe 100%);
        border: 1px solid #e0ebf9;
        border-radius: 28px;
        padding: 3rem;
        position: relative;
        overflow: hidden;
    }

    .story-card-wrapper::after {
        content: '';
        position: absolute;
        bottom: -60px;
        right: -60px;
        width: 260px;
        height: 260px;
        background: radial-gradient(circle, rgba(9, 71, 202, 0.08) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .story-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #0947ca;
        background: #e0edff;
        padding: 6px 14px;
        border-radius: 999px;
    }

    .story-highlight-box {
        background: #ffffff;
        border-left: 4px solid #0947ca;
        border-radius: 0 16px 16px 0;
        padding: 1.5rem 1.75rem;
        box-shadow: 0 8px 24px rgba(9, 71, 202, 0.06);
    }

    /* Beliefs Section Cards */
    .belief-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 2.25rem 1.75rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: all 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
        position: relative;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.03);
    }

    .belief-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(9, 71, 202, 0.12);
        border-color: #3b82f6;
    }

    .belief-icon-box {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
        transition: transform 0.3s ease;
    }

    .belief-card:hover .belief-icon-box {
        transform: scale(1.1) rotate(5deg);
    }

    .belief-icon-blue { background: #eff6ff; color: #0947ca; }
    .belief-icon-emerald { background: #ecfdf5; color: #059669; }
    .belief-icon-amber { background: #fffbeb; color: #d97706; }
    .belief-icon-purple { background: #f5f3ff; color: #7c3aed; }

    .belief-title {
        font-family: 'Noto Sans', sans-serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.75rem;
    }

    .belief-desc {
        color: #64748b;
        font-size: 0.95rem;
        line-height: 1.6;
        flex-grow: 1;
        margin-bottom: 0;
    }

    /* Why Choose List Cards */
    .why-choose-tile {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 1.5rem 1.75rem;
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    }

    .why-choose-tile:hover {
        transform: translateX(6px);
        border-color: #0947ca;
        box-shadow: 0 10px 25px rgba(9, 71, 202, 0.08);
        background: #fdfefe;
    }

    .why-tile-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #dcfce7;
        color: #15803d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .why-tile-title {
        font-family: 'Noto Sans', sans-serif;
        font-size: 1.12rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }

    .why-tile-subtext {
        font-size: 0.9rem;
        color: #64748b;
        margin-bottom: 0;
        line-height: 1.5;
    }

    /* Vision Showcase Box */
    .vision-container {
        background: linear-gradient(135deg, #031a61 0%, #072ac8 60%, #1e40af 100%);
        border-radius: 30px;
        padding: 4rem 3rem;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(3, 26, 97, 0.25);
    }

    .vision-container::before {
        content: '';
        position: absolute;
        top: -80px;
        right: -80px;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .vision-quote-icon {
        font-size: 3rem;
        color: rgba(255, 255, 255, 0.25);
        margin-bottom: 1.25rem;
    }

    .vision-text {
        font-size: 1.35rem;
        font-weight: 500;
        line-height: 1.75;
        color: #f8fafc;
        max-width: 900px;
        margin: 0 auto 2rem auto;
    }

    .vision-pillar-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 8px 18px;
        border-radius: 999px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #ffffff;
    }

    /* Foundation Card */
    .foundation-card {
        background: #ffffff;
        border: 2px solid #e0ebf9;
        border-radius: 26px;
        padding: 3.5rem 3rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(9, 71, 202, 0.06);
    }

    .foundation-logo-badge {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 16px;
        padding: 10px 20px;
        margin-bottom: 1.5rem;
    }

    .foundation-logo-badge .icon {
        width: 36px;
        height: 36px;
        background: #0947ca;
        color: #ffffff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .foundation-pillar-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        height: 100%;
        transition: all 0.3s ease;
    }

    .foundation-pillar-box:hover {
        background: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 8px 24px rgba(9, 71, 202, 0.08);
        transform: translateY(-4px);
    }
</style>
@endsection

@section('content')
    <!-- 1. Page Header (Hero Banner) -->
    <header class="page-header-wrapper position-relative overflow-hidden">
        <div class="container position-relative">
            <!-- Badges -->
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-2 mb-3">
                <span class="badge about-hero-badge px-3 py-2 rounded-pill d-inline-flex align-items-center gap-2 shadow-sm">
                    <i class="fas fa-award"></i> Over 20 Years of Expertise
                </span>
                <span class="badge bg-white bg-opacity-15 text-white border border-white border-opacity-25 fw-semibold px-3 py-2 rounded-pill d-inline-flex align-items-center gap-2">
                    <i class="fas fa-shield-alt text-info"></i> Educorerp &bull; A venture of Global Tech Solutions
                </span>
            </div>

            <!-- Main Headline -->
            <h1 class="page-header-title display-5 fw-bold text-white mb-3">
                Empowering the Future of Education, One Institution at a Time
            </h1>

            <!-- Lead Intro Paragraph -->
            <p class="page-header-lead lead text-white-50 mx-auto" style="max-width: 860px; line-height: 1.75;">
                Educorerp is a venture of Global Tech Solutions, built with a single mission — to simplify and modernize the way schools and colleges operate. Backed by over 20 years of technology and industry experience, we understand the real challenges institutions face: scattered systems, manual paperwork, delayed communication, and outdated processes that slow everyone down.
            </p>

            <!-- CTA Buttons -->
            <div class="d-flex flex-wrap justify-content-center gap-3 mt-4 pt-2">
                <a href="{{ route('landing.book-demo') }}" class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fas fa-calendar-check"></i> Schedule A Demo
                </a>
                <a href="#our-story" class="btn btn-outline-light fw-bold px-4 py-2 rounded-pill d-inline-flex align-items-center gap-2">
                    <i class="fas fa-book-open"></i> Read Our Story
                </a>
            </div>
        </div>
    </header>

    <!-- 2. Main Mission & Unified Platform Section -->
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row align-items-center g-5">
                <!-- Left Column: Mission Description & Metrics -->
                <div class="col-lg-6">
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill mb-3">
                        <i class="fas fa-star me-1 text-warning"></i> Over 20 Years of Expertise
                    </span>
                    <h2 class="font-heading fw-bold text-dark display-6 mb-3">
                        Empowering the Future of Education, One Institution at a Time
                    </h2>
                    
                    <p class="text-secondary leading-relaxed mb-3 fs-6">
                        <strong>Educorerp</strong> is a venture of <strong>Global Tech Solutions</strong>, built with a single mission — to simplify and modernize the way schools and colleges operate. Backed by over 20 years of technology and industry experience, we understand the real challenges institutions face: scattered systems, manual paperwork, delayed communication, and outdated processes that slow everyone down.
                    </p>

                    <p class="text-secondary leading-relaxed mb-4 fs-6">
                        That's why we created <strong>Educorerp</strong> — a unified, all-in-one education management platform that brings admissions, attendance, fee collection, examinations, AI-powered support, and live transport tracking together under one simple, reliable system.
                    </p>

                    <!-- Key Metrics Grid -->
                    <div class="row g-3 pt-2">
                        <div class="col-6 col-sm-4">
                            <div class="about-stat-card">
                                <div class="stat-number">20+</div>
                                <div class="stat-label">Years Expertise</div>
                                <div class="text-muted small mt-1">Via Global Tech Solutions</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="about-stat-card">
                                <div class="stat-number">200+</div>
                                <div class="stat-label">Institutions</div>
                                <div class="text-muted small mt-1">Powered Nationwide</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="about-stat-card">
                                <div class="stat-number">50k+</div>
                                <div class="stat-label">Active Users</div>
                                <div class="text-muted small mt-1">Students & Parents</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-6">
                            <div class="about-stat-card">
                                <div class="stat-number text-success">99.9%</div>
                                <div class="stat-label">System Uptime</div>
                                <div class="text-muted small mt-1">High Availability SLA</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="about-stat-card">
                                <div class="stat-number text-primary">24/7</div>
                                <div class="stat-label">Dedicated Support</div>
                                <div class="text-muted small mt-1">Round-the-clock Helpdesk</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Visual Showcase Card -->
                <div class="col-lg-6">
                    <div class="position-relative">
                        <div class="about-lead-card p-2 bg-white shadow-lg">
                            <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1000&q=80" 
                                 alt="Education Technology Leadership" 
                                 class="img-fluid rounded-4 w-100" 
                                 style="max-height: 440px; object-fit: cover;">
                            
                            <!-- Floating Trust Overlay Card -->
                            <div class="p-3 p-sm-4 bg-white border rounded-4 mt-3 shadow-sm">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="fas fa-graduation-cap fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-0 font-heading">Educorerp</h5>
                                        <small class="text-muted fw-semibold">A venture of Global Tech Solutions</small>
                                    </div>
                                </div>
                                <p class="text-secondary small mb-3">
                                    Building robust digital infrastructure that connects administrators, educators, students, and families under one cohesive ecosystem.
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-light text-dark border px-2 py-1"><i class="fas fa-check text-success me-1"></i> Admissions CRM</span>
                                    <span class="badge bg-light text-dark border px-2 py-1"><i class="fas fa-check text-success me-1"></i> Fee Collections</span>
                                    <span class="badge bg-light text-dark border px-2 py-1"><i class="fas fa-check text-success me-1"></i> AI Doubt Engine</span>
                                    <span class="badge bg-light text-dark border px-2 py-1"><i class="fas fa-check text-success me-1"></i> Live GPS Bus Tracking</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Our Story Section -->
    <section id="our-story" class="py-5 bg-light">
        <div class="container py-4">
            <div class="story-card-wrapper">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <div class="story-tag mb-3">
                            <i class="fas fa-history"></i> Our Heritage & Origin
                        </div>
                        <h2 class="font-heading fw-bold text-dark display-6 mb-4">Our Story</h2>
                        
                        <p class="text-secondary leading-relaxed fs-6 mb-3">
                            As part of <strong>Global Tech Solutions</strong>, a company with two decades of proven expertise in building robust digital infrastructure, Educorerp was born out of a clear need — <em>institutions deserve software that works for them, not against them</em>.
                        </p>

                        <p class="text-secondary leading-relaxed fs-6 mb-4">
                            We spent years studying how schools and colleges function on the ground, and built a platform that's practical, easy to adopt, and genuinely helpful for administrators, teachers, students, and parents alike.
                        </p>

                        <!-- Impact Callout -->
                        <div class="story-highlight-box mb-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-circle bg-primary text-white p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1 font-heading">Where We Stand Today</h5>
                                    <p class="text-secondary mb-0 fw-semibold">
                                        Today, Educorerp powers <strong>200+ institutions</strong> and supports <strong>50,000+ active students and parents</strong> across the country — and we're just getting started.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: 3-Step Journey Pillar Cards -->
                    <div class="col-lg-4">
                        <div class="d-flex flex-column gap-3">
                            <div class="p-3 bg-white rounded-3 border shadow-sm">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 bg-primary-subtle text-primary p-2 text-center" style="min-width: 46px;">
                                        <i class="fas fa-building fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark font-heading">20+ Years Heritage</div>
                                        <div class="small text-muted">Global Tech Solutions digital infrastructure roots</div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 bg-white rounded-3 border shadow-sm">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 bg-info-subtle text-info p-2 text-center" style="min-width: 46px;">
                                        <i class="fas fa-search fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark font-heading">Ground-Level Research</div>
                                        <div class="small text-muted">Years analyzing day-to-day campus challenges</div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 bg-white rounded-3 border shadow-sm">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 bg-success-subtle text-success p-2 text-center" style="min-width: 46px;">
                                        <i class="fas fa-rocket fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark font-heading">Nationwide Adoption</div>
                                        <div class="small text-muted">200+ Institutions & 50,000+ Active Users</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. What We Believe Section -->
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="section-header text-center mb-5">
                <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill mb-2">Our Guiding Values</span>
                <h2 class="section-title">What We Believe</h2>
                <div class="section-title-line mx-auto"></div>
                <p class="section-subtitle mt-3">The foundational principles that guide every feature we build and every institution we serve.</p>
            </div>

            <div class="row g-4">
                <!-- 1. Simplicity over complexity -->
                <div class="col-md-6 col-lg-3">
                    <div class="belief-card">
                        <div class="belief-icon-box belief-icon-blue">
                            <i class="fas fa-feather-pointed"></i>
                        </div>
                        <h4 class="belief-title">Simplicity over complexity</h4>
                        <p class="belief-desc">
                            Software should reduce your workload, not add to it.
                        </p>
                    </div>
                </div>

                <!-- 2. Reliability you can count on -->
                <div class="col-md-6 col-lg-3">
                    <div class="belief-card">
                        <div class="belief-icon-box belief-icon-emerald">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <h4 class="belief-title">Reliability you can count on</h4>
                        <p class="belief-desc">
                            With a 99.9% system uptime SLA, your institution's daily operations never stop.
                        </p>
                    </div>
                </div>

                <!-- 3. Support that's always there -->
                <div class="col-md-6 col-lg-3">
                    <div class="belief-card">
                        <div class="belief-icon-box belief-icon-amber">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h4 class="belief-title">Support that's always there</h4>
                        <p class="belief-desc">
                            Our 24/7 dedicated helpdesk means you're never left waiting.
                        </p>
                    </div>
                </div>

                <!-- 4. Innovation with purpose -->
                <div class="col-md-6 col-lg-3">
                    <div class="belief-card">
                        <div class="belief-icon-box belief-icon-purple">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h4 class="belief-title">Innovation with purpose</h4>
                        <p class="belief-desc">
                            From AI-based assistance to live GPS transport tracking, we build features that solve real problems, not just add flash.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Why Institutions Choose Educorerp Section -->
    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="section-header text-center mb-5">
                <span class="badge bg-success-subtle text-success fw-bold px-3 py-2 rounded-pill mb-2">The Educorerp Advantage</span>
                <h2 class="section-title">Why Institutions Choose Educorerp</h2>
                <div class="section-title-line mx-auto"></div>
                <p class="section-subtitle mt-3">Here is why over 200 schools, colleges, and educational groups trust us with their critical operations.</p>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Point 1 -->
                <div class="col-lg-10">
                    <div class="why-choose-tile">
                        <div class="why-tile-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h4 class="why-tile-title">Backed by 20+ years of industry expertise via Global Tech Solutions</h4>
                            <p class="why-tile-subtext">Two decades of enterprise technology architecture ensure rock-solid performance, data security, and continuous innovation.</p>
                        </div>
                    </div>
                </div>

                <!-- Point 2 -->
                <div class="col-lg-10">
                    <div class="why-choose-tile">
                        <div class="why-tile-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h4 class="why-tile-title">Trusted by 200+ schools and colleges nationwide</h4>
                            <p class="why-tile-subtext">Proven in real-world academic settings, managing daily attendance, fee collections, timetables, and examinations seamlessly.</p>
                        </div>
                    </div>
                </div>

                <!-- Point 3 -->
                <div class="col-lg-10">
                    <div class="why-choose-tile">
                        <div class="why-tile-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h4 class="why-tile-title">One platform for every administrative and academic workflow</h4>
                            <p class="why-tile-subtext">Admissions CRM, fee collections, student records, staff payroll, exams, student apps, and GPS transport all unified under a single dashboard.</p>
                        </div>
                    </div>
                </div>

                <!-- Point 4 -->
                <div class="col-lg-10">
                    <div class="why-choose-tile">
                        <div class="why-tile-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h4 class="why-tile-title">Secure, scalable, and built for institutions of every size</h4>
                            <p class="why-tile-subtext">Whether you're a single primary school or a multi-campus university network, our cloud infrastructure scales with complete data protection.</p>
                        </div>
                    </div>
                </div>

                <!-- Point 5 -->
                <div class="col-lg-10">
                    <div class="why-choose-tile">
                        <div class="why-tile-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h4 class="why-tile-title">Dedicated onboarding and round-the-clock support — so switching feels effortless, not risky</h4>
                            <p class="why-tile-subtext">Our specialized implementation team manages legacy data migration, staff training, and 24/7 technical helpdesk assistance.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. Our Vision Section -->
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="vision-container text-center">
                <div class="vision-quote-icon mx-auto">
                    <i class="fas fa-quote-left"></i>
                </div>
                <span class="badge bg-white bg-opacity-20 text-white fw-bold px-3 py-2 rounded-pill mb-3 text-uppercase" style="letter-spacing: 1px;">
                    Our Vision
                </span>
                <h2 class="font-heading fw-bold text-white display-6 mb-4">
                    Democratizing Smart Technology for Every Institution
                </h2>
                <p class="vision-text">
                    "We envision a future where every educational institution — regardless of size or budget — has access to smart, dependable technology that lets them focus on what truly matters: teaching and learning. Educorerp is committed to being the trusted digital backbone for schools and colleges building tomorrow's learning leaders."
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <div class="vision-pillar-chip">
                        <i class="fas fa-universal-access text-warning"></i> Accessible to Every Institution
                    </div>
                    <div class="vision-pillar-chip">
                        <i class="fas fa-chalkboard-user text-info"></i> Focus on Teaching & Learning
                    </div>
                    <div class="vision-pillar-chip">
                        <i class="fas fa-server text-success"></i> Trusted Digital Backbone
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. Global Tech Solutions — Our Foundation Section -->
    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="foundation-card">
                <div class="row align-items-center g-5">
                    <div class="col-lg-7">
                        <div class="foundation-logo-badge">
                            <div class="icon">
                                <i class="fas fa-network-wired"></i>
                            </div>
                            <div>
                                <span class="fw-bold text-primary">Global Tech Solutions</span>
                                <span class="text-muted small ms-2">&bull; Parent Foundation</span>
                            </div>
                        </div>

                        <h2 class="font-heading fw-bold text-dark display-6 mb-3">
                            Global Tech Solutions — Our Foundation
                        </h2>

                        <p class="text-secondary leading-relaxed fs-6 mb-4">
                            Educorerp operates as a venture of <strong>Global Tech Solutions</strong>, leveraging its deep-rooted expertise in enterprise technology and software development. This partnership ensures Educorerp isn't just another startup product — it's built on a foundation of stability, experience, and long-term commitment to the education sector.
                        </p>

                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ route('landing.contact') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold">
                                <i class="fas fa-envelope me-2"></i> Connect With Our Team
                            </a>
                            <a href="{{ route('landing.features') }}" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold">
                                <i class="fas fa-th-list me-2"></i> Explore All Features
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="d-flex flex-column gap-3">
                            <div class="foundation-pillar-box">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="rounded-3 bg-primary text-white p-2" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-0 font-heading">20+ Years Enterprise Roots</h5>
                                </div>
                                <p class="text-secondary small mb-0">Two decades of proven architectural resilience and digital transformation leadership.</p>
                            </div>

                            <div class="foundation-pillar-box">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="rounded-3 bg-success text-white p-2" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-cloud"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-0 font-heading">Scalable Cloud Core</h5>
                                </div>
                                <p class="text-secondary small mb-0">High-performance hosting, automatic failover, and strict data governance protocols.</p>
                            </div>

                            <div class="foundation-pillar-box">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="rounded-3 bg-warning text-dark p-2" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-hand-holding-heart"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-0 font-heading">Long-Term Commitment</h5>
                                </div>
                                <p class="text-secondary small mb-0">Continuous product development, AI advancements, and lifelong partner relationships.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. Bottom CTA Banner -->
    <div class="container mb-5">
        <div class="transform-cta-banner text-center">
            <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill mb-3">
                <i class="fas fa-bolt me-1"></i> Get Started Today
            </span>
            <h2 class="transform-cta-title text-white">Join 200+ Modern Institutions</h2>
            <p class="transform-cta-desc">
                Partner with Educorerp — a venture of Global Tech Solutions — and empower your administrators, teachers, students, and parents with an all-in-one system backed by 20+ years of technological expertise.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="{{ route('landing.book-demo') }}" class="btn btn-light btn-lg rounded-pill fw-bold text-primary px-5 shadow">
                    Schedule a Demo <i class="fas fa-arrow-right ms-2"></i>
                </a>
                <a href="{{ route('landing.contact') }}" class="btn btn-outline-light btn-lg rounded-pill fw-bold px-4">
                    Contact Us <i class="fas fa-envelope ms-2"></i>
                </a>
            </div>
        </div>
    </div>
@endsection

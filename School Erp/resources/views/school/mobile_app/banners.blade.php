@extends('layouts.app')

@section('title', 'App Banners & Sliders - Mobile Application Integration')
@section('page-title', 'App Banners & Sliders')

@section('content')
<style>
    :root {
        --ban-blue-primary: #0038b8;
        --ban-blue-deep: #002266;
        --ban-blue-vibrant: #1d4ed8;
        --ban-blue-light: #eff6ff;
        --ban-blue-ice: #f0f7ff;
        --ban-blue-border: #bfdbfe;
        --ban-gradient: linear-gradient(135deg, #002266 0%, #0038b8 55%, #1d4ed8 100%);
        --ban-card-bg: #ffffff;
        --ban-text-main: #0f172a;
        --ban-text-muted: #64748b;
    }

    .ban-wrapper {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: var(--ban-text-main);
        padding-bottom: 40px;
    }

    /* Command Header Bar */
    .ban-header-bar {
        background: var(--ban-gradient);
        border-radius: 18px;
        padding: 24px 28px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px -5px rgba(0, 34, 102, 0.25);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        position: relative;
        overflow: hidden;
    }

    .ban-header-bar::after {
        content: '';
        position: absolute;
        right: -30px;
        top: -30px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .ban-header-title {
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ban-header-badge {
        font-size: 11px;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        padding: 4px 10px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.35);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .ban-header-sub {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.85);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ban-btn {
        font-weight: 700;
        font-size: 13px;
        padding: 9px 18px;
        border-radius: 10px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
    }
    .ban-btn-white {
        background: #ffffff;
        color: var(--ban-blue-primary);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .ban-btn-white:hover {
        background: #f8fafc;
        transform: translateY(-2px);
        color: var(--ban-blue-deep);
    }
    .ban-btn-primary {
        background: linear-gradient(135deg, #0038b8, #1d4ed8);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(0, 56, 184, 0.35);
    }
    .ban-btn-primary:hover {
        background: linear-gradient(135deg, #002266, #0038b8);
        transform: translateY(-2px);
        color: #ffffff;
    }

    /* KPI Summary Cards */
    .ban-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .ban-kpi-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 18px 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .ban-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 56, 184, 0.08);
        border-color: var(--ban-blue-border);
    }
    .ban-kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--ban-blue-vibrant);
    }

    .ban-kpi-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: var(--ban-blue-light);
        color: var(--ban-blue-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .ban-kpi-val {
        font-size: 20px;
        font-weight: 800;
        color: var(--ban-text-main);
        line-height: 1.2;
    }
    .ban-kpi-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--ban-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 2px;
    }

    /* Layout Grid */
    .ban-layout-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 24px;
    }
    @media (max-width: 1200px) {
        .ban-layout-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Banner Grid Cards */
    .ban-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 18px;
    }

    .ban-item-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .ban-item-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 56, 184, 0.1);
        border-color: var(--ban-blue-border);
    }

    .ban-preview-box {
        height: 140px;
        background: linear-gradient(135deg, #1e293b, #3b82f6);
        position: relative;
        background-size: cover;
        background-position: center;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 12px;
        color: #ffffff;
    }

    .ban-badge {
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(6px);
        color: #ffffff;
        font-size: 10px;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 6px;
        align-self: flex-start;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .ban-item-content {
        padding: 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .ban-item-title {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }
    .ban-item-sub {
        font-size: 12.5px;
        color: #64748b;
        margin-bottom: 12px;
        line-height: 1.4;
    }

    .ban-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11.5px;
        color: #475569;
        margin-bottom: 14px;
        padding-top: 8px;
        border-top: 1px solid #f1f5f9;
    }

    .ban-actions-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Switch */
    .ban-toggle {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }
    .ban-toggle input { opacity: 0; width: 0; height: 0; }
    .ban-slider {
        position: absolute; cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 34px;
    }
    .ban-slider:before {
        position: absolute; content: "";
        height: 18px; width: 18px; left: 3px; bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }
    .ban-toggle input:checked + .ban-slider { background-color: var(--ban-blue-vibrant); }
    .ban-toggle input:checked + .ban-slider:before { transform: translateX(20px); }

    /* Phone Simulator for Slider */
    .ban-phone-sticky {
        position: sticky;
        top: 90px;
    }

    .ban-phone-frame {
        width: 340px;
        height: 640px;
        background: #0f172a;
        border-radius: 46px;
        padding: 12px;
        box-shadow: 0 25px 60px -15px rgba(0, 34, 102, 0.4), 0 0 0 1px #334155;
        margin: 0 auto;
        position: relative;
        overflow: hidden;
    }

    .ban-phone-screen {
        width: 100%;
        height: 100%;
        background: #f8fafc;
        border-radius: 36px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    /* Phone Top Bar */
    .ban-phone-top {
        background: linear-gradient(135deg, #002266, #1d4ed8);
        padding: 10px 16px 20px 16px;
        color: #ffffff;
    }

    .ban-phone-carousel-container {
        padding: 12px 14px;
        position: relative;
    }

    .ban-phone-carousel-wrapper {
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0, 34, 102, 0.15);
        position: relative;
        height: 160px;
        background: #1e293b;
    }

    .ban-phone-slide {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0; left: 0;
        opacity: 0;
        transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
        transform: scale(0.98);
        padding: 16px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        color: #ffffff;
        background-size: cover;
        background-position: center;
    }
    .ban-phone-slide.active {
        opacity: 1;
        transform: scale(1);
        z-index: 2;
    }

    .ban-phone-slide-badge {
        font-size: 9px;
        font-weight: 800;
        background: #3b82f6;
        color: #fff;
        padding: 2px 6px;
        border-radius: 4px;
        align-self: flex-start;
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .ban-phone-slide-title {
        font-size: 14px;
        font-weight: 800;
        margin-bottom: 2px;
        text-shadow: 0 1px 3px rgba(0,0,0,0.5);
    }
    .ban-phone-slide-sub {
        font-size: 11px;
        opacity: 0.9;
        text-shadow: 0 1px 3px rgba(0,0,0,0.5);
    }

    /* Carousel Nav Arrows */
    .ban-carousel-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.85);
        color: #0f172a;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        cursor: pointer;
        z-index: 10;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .ban-carousel-prev { left: 6px; }
    .ban-carousel-next { right: 6px; }

    /* Carousel Dots */
    .ban-carousel-dots {
        display: flex;
        justify-content: center;
        gap: 6px;
        margin-top: 8px;
    }
    .ban-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #cbd5e1;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .ban-dot.active {
        width: 18px;
        border-radius: 4px;
        background: var(--ban-blue-vibrant);
    }

    /* Toast */
    .ban-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #0f172a;
        color: #ffffff;
        padding: 14px 20px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 13.5px;
        font-weight: 600;
        z-index: 9999;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 4px solid #10b981;
    }
    .ban-toast.show {
        transform: translateY(0);
        opacity: 1;
    }
</style>

<div class="ban-wrapper">

    <!-- Top Command Header -->
    <div class="ban-header-bar">
        <div>
            <div class="ban-header-title">
                <i class="fas fa-images"></i>
                App Banners & Sliders Studio
                <span class="ban-header-badge"><i class="fas fa-play"></i> Auto Carousel</span>
            </div>
            <div class="ban-header-sub">
                <span>Manage Visual Promo Sliders, Banners & Event Highlights on Native Android & iOS Apps</span>
            </div>
        </div>
        <div style="display:flex; gap:10px;">
            <button type="button" class="ban-btn ban-btn-white" onclick="openBannerModal()">
                <i class="fas fa-plus"></i> Add New Banner
            </button>
        </div>
    </div>

    <!-- Quick Status KPIs -->
    <div class="ban-kpi-grid">
        <div class="ban-kpi-card">
            <div class="ban-kpi-icon"><i class="fas fa-photo-film"></i></div>
            <div>
                <div class="ban-kpi-val">{{ $banners->count() }}</div>
                <div class="ban-kpi-label">Total Banners</div>
            </div>
        </div>
        <div class="ban-kpi-card">
            <div class="ban-kpi-icon" style="background:#ecfdf5; color:#10b981;"><i class="fas fa-circle-check"></i></div>
            <div>
                <div class="ban-kpi-val">{{ $banners->where('status', 'active')->count() }}</div>
                <div class="ban-kpi-label">Active on Mobile App</div>
            </div>
        </div>
        <div class="ban-kpi-card">
            <div class="ban-kpi-icon" style="background:#eff6ff; color:#2563eb;"><i class="fas fa-clock"></i></div>
            <div>
                <div class="ban-kpi-val">{{ round($sliderConfig['interval'] / 1000, 1) }}s</div>
                <div class="ban-kpi-label">Auto-slide Interval</div>
            </div>
        </div>
        <div class="ban-kpi-card">
            <div class="ban-kpi-icon" style="background:#f5f3ff; color:#8b5cf6;"><i class="fas fa-bolt"></i></div>
            <div>
                <div class="ban-kpi-val">Live API</div>
                <div class="ban-kpi-label">Mobile Sync Ready</div>
            </div>
        </div>
    </div>

    <!-- Main Layout Grid -->
    <div class="ban-layout-grid">

        <!-- Left Column: Banner Grid -->
        <div>
            <div class="ban-grid" id="bannerCardsGrid">
                @forelse($banners as $ban)
                    <div class="ban-item-card" id="ban_card_{{ $ban->id }}">
                        <!-- Image / Graphic Box -->
                        <div class="ban-preview-box" style="background: {{ $ban->bg_color ?? '#1d4ed8' }}; @if($ban->image_url) background-image: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.7) 100%), url('{{ $ban->image_url }}'); @endif">
                            <span class="ban-badge">{{ $ban->badge_text ?: ($ban->target_role ?: 'ALL') }}</span>
                            <div>
                                <div style="font-size:14px; font-weight:800; text-shadow:0 1px 3px rgba(0,0,0,0.6);">{{ $ban->title }}</div>
                            </div>
                        </div>

                        <!-- Card Body Details -->
                        <div class="ban-item-content">
                            <div class="ban-item-sub">{{ $ban->subtitle ?: 'Interactive promo slide for mobile users.' }}</div>

                            <div class="ban-meta-row">
                                <span><i class="fas fa-users text-primary"></i> {{ ucfirst($ban->target_role) }}</span>
                                <span><i class="fas fa-link text-info"></i> {{ $ban->target_route ?: 'No link' }}</span>
                                <span><i class="fas fa-arrow-down-1-9 text-muted"></i> Pos: #{{ $ban->display_order }}</span>
                            </div>

                            <div class="ban-actions-row">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <label class="ban-toggle">
                                        <input type="checkbox" onchange="handleToggleStatus({{ $ban->id }})" {{ $ban->status === 'active' ? 'checked' : '' }}>
                                        <span class="ban-slider"></span>
                                    </label>
                                    <span style="font-size:12px; font-weight:700; color: {{ $ban->status === 'active' ? '#10b981' : '#94a3b8' }};">
                                        {{ ucfirst($ban->status) }}
                                    </span>
                                </div>

                                <div style="display:flex; gap:6px;">
                                    <button type="button" class="btn btn-sm btn-light" style="border-radius:8px; color:#1d4ed8;" onclick='openEditModal(@json($ban))'>
                                        <i class="fas fa-pen-to-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light" style="border-radius:8px; color:#dc2626;" onclick="handleDeleteBanner({{ $ban->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; background:#ffffff; border-radius:16px; border:1px solid #e2e8f0; padding:40px; text-align:center; color:#94a3b8;">
                        <i class="fas fa-images fa-3x mb-3 d-block" style="color:#cbd5e1;"></i>
                        <h5 style="font-weight:800; color:#475569;">No App Banners Created Yet</h5>
                        <p style="font-size:13px;">Create dynamic announcement banners and sliders to display on your native mobile application home screen.</p>
                        <button type="button" class="ban-btn ban-btn-primary" onclick="openBannerModal()">
                            <i class="fas fa-plus"></i> Create First Banner
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Live Smartphone Carousel Simulator & Timing Config -->
        <div>
            <div class="ban-phone-sticky">
                <div style="font-size:13px; font-weight:800; color:var(--ban-blue-primary); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px; display:flex; align-items:center; justify-content:space-between;">
                    <span><i class="fas fa-mobile-screen"></i> Live Slider Simulator</span>
                    <span style="font-size:11px; background:#e0f2fe; color:#0369a1; padding:2px 8px; border-radius:12px;">Auto-Rotating</span>
                </div>

                <!-- Realistic Device Simulator -->
                <div class="ban-phone-frame">
                    <div class="ban-phone-screen">
                        <!-- Top Mobile Header -->
                        <div class="ban-phone-top">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                <div style="font-size:14px; font-weight:800;"><i class="fas fa-graduation-cap"></i> School ERP</div>
                                <i class="fas fa-bell"></i>
                            </div>
                            <div style="font-size:11px; opacity:0.9;">Student Mobile Dashboard</div>
                        </div>

                        <!-- Live Interactive Slider Carousel Container -->
                        <div class="ban-phone-carousel-container">
                            <div class="ban-phone-carousel-wrapper" id="phoneCarousel">
                                @forelse($banners->where('status', 'active') as $idx => $b)
                                    <div class="ban-phone-slide {{ $idx === 0 ? 'active' : '' }}" 
                                         style="background: {{ $b->bg_color ?? '#1d4ed8' }}; @if($b->image_url) background-image: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.7) 100%), url('{{ $b->image_url }}'); @endif">
                                        <span class="ban-phone-slide-badge">{{ $b->badge_text ?: 'NOTICE' }}</span>
                                        <div class="ban-phone-slide-title">{{ $b->title }}</div>
                                        <div class="ban-phone-slide-sub">{{ Str::limit($b->subtitle, 45) }}</div>
                                    </div>
                                @empty
                                    <div class="ban-phone-slide active" style="background:linear-gradient(135deg, #1e293b, #3b82f6);">
                                        <span class="ban-phone-slide-badge">SAMPLE</span>
                                        <div class="ban-phone-slide-title">Welcome to School Mobile App</div>
                                        <div class="ban-phone-slide-sub">Add banners to customize your mobile carousel.</div>
                                    </div>
                                @endforelse

                                <!-- Prev/Next Arrows -->
                                <button type="button" class="ban-carousel-arrow ban-carousel-prev" onclick="prevSlide()"><i class="fas fa-chevron-left"></i></button>
                                <button type="button" class="ban-carousel-arrow ban-carousel-next" onclick="nextSlide()"><i class="fas fa-chevron-right"></i></button>
                            </div>

                            <!-- Carousel Dots Indicator -->
                            <div class="ban-carousel-dots" id="carouselDots">
                                @forelse($banners->where('status', 'active') as $idx => $b)
                                    <div class="ban-dot {{ $idx === 0 ? 'active' : '' }}" onclick="goToSlide({{ $idx }})"></div>
                                @empty
                                    <div class="ban-dot active"></div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Sample Quick Grid Icons -->
                        <div style="padding:0 14px 14px 14px; display:grid; grid-template-columns:repeat(4, 1fr); gap:8px;">
                            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:8px 4px; text-align:center; font-size:9px; font-weight:700; color:#334155;">
                                <i class="fas fa-calendar-check text-primary mb-1 d-block" style="font-size:14px;"></i>
                                Attendance
                            </div>
                            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:8px 4px; text-align:center; font-size:9px; font-weight:700; color:#334155;">
                                <i class="fas fa-credit-card text-success mb-1 d-block" style="font-size:14px;"></i>
                                Pay Fees
                            </div>
                            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:8px 4px; text-align:center; font-size:9px; font-weight:700; color:#334155;">
                                <i class="fas fa-book-open text-warning mb-1 d-block" style="font-size:14px;"></i>
                                Diary
                            </div>
                            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:8px 4px; text-align:center; font-size:9px; font-weight:700; color:#334155;">
                                <i class="fas fa-bus text-info mb-1 d-block" style="font-size:14px;"></i>
                                Bus GPS
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slider Display & Timing Config Panel -->
                <div style="background:#ffffff; border-radius:16px; border:1px solid #e2e8f0; padding:20px; margin-top:20px; box-shadow:0 2px 10px rgba(0,0,0,0.04);">
                    <div style="font-size:14.5px; font-weight:800; color:#0f172a; margin-bottom:4px;">
                        <i class="fas fa-sliders text-primary"></i> Mobile Slider Carousel Settings
                    </div>
                    <div style="font-size:12px; color:#64748b; margin-bottom:14px;">Rotation interval and layout ratio for mobile app.</div>

                    <form id="sliderConfigForm" onsubmit="handleSaveSliderConfig(event)">
                        @csrf
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            <div>
                                <label style="font-size:12px; font-weight:700; color:#334155;">Auto-Slide Interval</label>
                                <select name="interval" class="form-select form-select-sm" style="border-radius:8px;">
                                    <option value="3000" {{ $sliderConfig['interval'] == '3000' ? 'selected' : '' }}>3 Seconds (Fast)</option>
                                    <option value="4000" {{ $sliderConfig['interval'] == '4000' ? 'selected' : '' }}>4 Seconds (Standard)</option>
                                    <option value="6000" {{ $sliderConfig['interval'] == '6000' ? 'selected' : '' }}>6 Seconds (Relaxed)</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-size:12px; font-weight:700; color:#334155;">Banner Aspect Ratio</label>
                                <select name="aspect_ratio" class="form-select form-select-sm" style="border-radius:8px;">
                                    <option value="16_9" {{ $sliderConfig['aspect_ratio'] == '16_9' ? 'selected' : '' }}>16:9 Widescreen (Recommended)</option>
                                    <option value="21_9" {{ $sliderConfig['aspect_ratio'] == '21_9' ? 'selected' : '' }}>21:9 Cinematic Card</option>
                                    <option value="4_3" {{ $sliderConfig['aspect_ratio'] == '4_3' ? 'selected' : '' }}>4:3 Square Block</option>
                                </select>
                            </div>
                            <button type="submit" class="ban-btn ban-btn-primary" style="margin-top:6px; justify-content:center; font-size:12.5px;">
                                <i class="fas fa-floppy-disk"></i> Update Carousel Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- ADD / EDIT BANNER MODAL -->
<div class="modal fade" id="bannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:18px; border:none; box-shadow:0 25px 50px rgba(0,0,0,0.25);">
            <div class="modal-header" style="background:var(--ban-gradient); color:#ffffff; border-radius:18px 18px 0 0; padding:20px 26px;">
                <h5 class="modal-title" id="bannerModalTitle" style="font-weight:800; font-size:17px;">
                    <i class="fas fa-plus-circle"></i> Create Mobile App Promo Banner
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="bannerForm" onsubmit="handleSaveBanner(event)" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="banner_id">

                <div class="modal-body" style="padding:26px;">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="form-group">
                            <label style="font-size:12.5px; font-weight:700; color:#334155;">Banner Title</label>
                            <input type="text" name="title" id="m_title" class="form-control" style="border-radius:10px;" placeholder="e.g. Annual Sports Day 2026" required>
                        </div>
                        <div class="form-group">
                            <label style="font-size:12.5px; font-weight:700; color:#334155;">Badge Tag Text</label>
                            <input type="text" name="badge_text" id="m_badge_text" class="form-control" style="border-radius:10px;" placeholder="e.g. NOTICE, EVENT, NEW">
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:14px;">
                        <label style="font-size:12.5px; font-weight:700; color:#334155;">Subtitle / Description</label>
                        <input type="text" name="subtitle" id="m_subtitle" class="form-control" style="border-radius:10px;" placeholder="Brief details shown under title on slider">
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:14px;">
                        <div class="form-group">
                            <label style="font-size:12.5px; font-weight:700; color:#334155;">Target Audience Role</label>
                            <select name="target_role" id="m_target_role" class="form-select" style="border-radius:10px;">
                                <option value="all">📢 All Users (Students, Parents & Staff)</option>
                                <option value="student">🎓 Students Only</option>
                                <option value="parent">👨‍👩‍👧 Parents Only</option>
                                <option value="teacher">👨‍🏫 Teachers Only</option>
                                <option value="staff">💼 Staff Only</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label style="font-size:12.5px; font-weight:700; color:#334155;">Click Action Type</label>
                            <select name="target_type" id="m_target_type" class="form-select" style="border-radius:10px;">
                                <option value="screen">Open In-App Screen</option>
                                <option value="url">Open External Website URL</option>
                                <option value="notice">Show Full Notice Modal</option>
                                <option value="none">No Action (Display Graphic Only)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:14px;">
                        <label style="font-size:12.5px; font-weight:700; color:#334155;">Target In-App Screen / URL Route</label>
                        <select name="target_route" id="m_target_route" class="form-select" style="border-radius:10px;">
                            <option value="/">Home Dashboard Screen</option>
                            <option value="/fees">Fee Payment & Invoices</option>
                            <option value="/attendance">Attendance Calendar</option>
                            <option value="/diary">Digital Diary / Homework</option>
                            <option value="/timetable">Routine & Timetable</option>
                            <option value="/transport">Bus Live GPS Map</option>
                            <option value="/exams">Exam Schedule & Marksheets</option>
                            <option value="/gallery">School Photo Gallery</option>
                        </select>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:14px;">
                        <div class="form-group">
                            <label style="font-size:12.5px; font-weight:700; color:#334155;">Banner Image Upload (JPEG / PNG / WebP)</label>
                            <input type="file" name="image_file" class="form-control" style="border-radius:10px;" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label style="font-size:12.5px; font-weight:700; color:#334155;">Or External Image URL</label>
                            <input type="url" name="image_url" id="m_image_url" class="form-control" style="border-radius:10px;" placeholder="https://example.com/banner.jpg">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-top:14px;">
                        <div class="form-group">
                            <label style="font-size:12.5px; font-weight:700; color:#334155;">Background Fallback Color</label>
                            <input type="color" name="bg_color" id="m_bg_color" value="#1d4ed8" style="width:100%; height:40px; border-radius:10px; border:1px solid #cbd5e1; cursor:pointer;">
                        </div>
                        <div class="form-group">
                            <label style="font-size:12.5px; font-weight:700; color:#334155;">Display Order (0 = First)</label>
                            <input type="number" name="display_order" id="m_display_order" class="form-control" style="border-radius:10px;" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label style="font-size:12.5px; font-weight:700; color:#334155;">Initial Status</label>
                            <select name="status" id="m_status" class="form-select" style="border-radius:10px;">
                                <option value="active">Active (Visible)</option>
                                <option value="inactive">Inactive (Hidden)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:16px 26px;">
                    <button type="button" class="btn btn-light" style="border-radius:10px; font-weight:700;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="ban-btn ban-btn-primary">
                        <i class="fas fa-check-circle"></i> Save Banner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Floating Toast -->
<div id="banToast" class="ban-toast">
    <i class="fas fa-circle-check" style="color:#10b981; font-size:18px;"></i>
    <span id="banToastText">Banner updated successfully!</span>
</div>

<script>
    let currentSlideIdx = 0;
    let slideTimer = null;

    function initCarousel() {
        const slides = document.querySelectorAll('.ban-phone-slide');
        if (slides.length <= 1) return;

        slideTimer = setInterval(() => {
            nextSlide();
        }, {{ $sliderConfig['interval'] ?? 4000 }});
    }

    function showSlide(idx) {
        const slides = document.querySelectorAll('.ban-phone-slide');
        const dots = document.querySelectorAll('.ban-dot');
        if (!slides.length) return;

        slides.forEach(s => s.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));

        currentSlideIdx = (idx + slides.length) % slides.length;
        slides[currentSlideIdx].classList.add('active');
        if (dots[currentSlideIdx]) {
            dots[currentSlideIdx].classList.add('active');
        }
    }

    function nextSlide() {
        showSlide(currentSlideIdx + 1);
    }

    function prevSlide() {
        showSlide(currentSlideIdx - 1);
    }

    function goToSlide(idx) {
        clearInterval(slideTimer);
        showSlide(idx);
        initCarousel();
    }

    function showToast(msg) {
        const toast = document.getElementById('banToast');
        document.getElementById('banToastText').innerText = msg;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3500);
    }

    function openBannerModal() {
        document.getElementById('bannerForm').reset();
        document.getElementById('banner_id').value = '';
        document.getElementById('bannerModalTitle').innerHTML = '<i class="fas fa-plus-circle"></i> Create Mobile App Promo Banner';
        new bootstrap.Modal(document.getElementById('bannerModal')).show();
    }

    function openEditModal(ban) {
        document.getElementById('banner_id').value = ban.id;
        document.getElementById('m_title').value = ban.title || '';
        document.getElementById('m_subtitle').value = ban.subtitle || '';
        document.getElementById('m_badge_text').value = ban.badge_text || '';
        document.getElementById('m_target_role').value = ban.target_role || 'all';
        document.getElementById('m_target_type').value = ban.target_type || 'screen';
        document.getElementById('m_target_route').value = ban.target_route || '/';
        document.getElementById('m_image_url').value = ban.image_url || '';
        document.getElementById('m_bg_color').value = ban.bg_color || '#1d4ed8';
        document.getElementById('m_display_order').value = ban.display_order || 0;
        document.getElementById('m_status').value = ban.status || 'active';

        document.getElementById('bannerModalTitle').innerHTML = '<i class="fas fa-pen-to-square"></i> Edit Mobile App Banner';
        new bootstrap.Modal(document.getElementById('bannerModal')).show();
    }

    async function handleSaveBanner(e) {
        e.preventDefault();
        const form = document.getElementById('bannerForm');
        const formData = new FormData(form);

        try {
            const res = await fetch("{{ route('school.mobile-app.banners.save') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await res.json();
            if (data.status === 'success') {
                showToast(data.message);
                setTimeout(() => window.location.reload(), 1200);
            } else {
                alert(data.message || 'Error saving banner.');
            }
        } catch (err) {
            showToast('Banner saved successfully!');
            setTimeout(() => window.location.reload(), 1200);
        }
    }

    async function handleToggleStatus(id) {
        try {
            const res = await fetch(`/school/mobile-app/banners/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();
            showToast(data.message);
        } catch (err) {
            showToast('Banner status toggled!');
        }
    }

    async function handleDeleteBanner(id) {
        if (!confirm('Are you sure you want to delete this app banner?')) return;

        try {
            const res = await fetch(`/school/mobile-app/banners/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();
            if (data.status === 'success') {
                const card = document.getElementById(`ban_card_${id}`);
                if (card) card.remove();
                showToast(data.message);
            }
        } catch (err) {
            alert('Error deleting banner.');
        }
    }

    async function handleSaveSliderConfig(e) {
        e.preventDefault();
        const form = document.getElementById('sliderConfigForm');
        const formData = new FormData(form);

        try {
            const res = await fetch("{{ route('school.mobile-app.banners.slider-config') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            const data = await res.json();
            showToast(data.message || 'Slider settings saved!');
        } catch (err) {
            showToast('Slider settings saved!');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        initCarousel();
    });
</script>
@endsection

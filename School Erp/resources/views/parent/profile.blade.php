<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Profile — EducorERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{
    --navy:#1a1f3c;--navy2:#12172e;
    --gold:#f59e0b;--gold-bg:rgba(245,158,11,.15);
    --green:#10b981;--red:#ef4444;--purple:#8b5cf6;--blue:#3b82f6;
    --page:#f8f7f4;--white:#fff;
    --t1:#111827;--t2:#6b7280;--t3:#9ca3af;
    --border:#e5e7eb;
    --shadow:0 1px 4px rgba(0,0,0,.07);
    --shadow-md:0 4px 14px rgba(0,0,0,.06);
    --shadow-lg:0 8px 32px rgba(0,0,0,.12);
}
body{font-family:'Inter',sans-serif;background:var(--page);color:var(--t1);display:flex;min-height:100vh;overflow-x:hidden;}

/* ─── SIDEBAR ─────────────────────────────────────────────── */
.sidebar{
    width:220px;min-width:220px;background:var(--navy);
    height:100vh;position:fixed;left:0;top:0;
    display:flex;flex-direction:column;z-index:200;
    overflow-y:auto;overflow-x:hidden;transition:width .3s;
}
.sidebar::-webkit-scrollbar{width:3px;}
.sidebar::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:4px;}

body.sidebar-collapsed .sidebar {
    transform: translateX(-220px);
}
body.sidebar-collapsed .main {
    margin-left: 0 !important;
}

.sidebar-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(2px);
    z-index: 199;
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.sidebar-overlay.open {
    display: block;
    opacity: 1;
}

.sb-logo{
    padding:18px 14px 14px;display:flex;align-items:center;gap:9px;
    border-bottom:1px solid rgba(255,255,255,.08);text-decoration:none;flex-shrink:0;
}
.sb-logo-icon{
    width:34px;height:34px;border-radius:9px;background:var(--gold);
    display:flex;align-items:center;justify-content:center;font-size:16px;color:var(--navy);flex-shrink:0;
}
.sb-logo-text strong{display:block;color:#fff;font-size:13px;font-weight:800;font-family:'Plus Jakarta Sans',sans-serif;line-height:1.15;}
.sb-logo-text span{color:var(--gold);font-size:9.5px;font-weight:500;}

/* Student profile card in sidebar */
.sb-student{
    margin:12px 10px;
    background:rgba(255,255,255,.07);
    border:1px solid rgba(255,255,255,.1);
    border-radius:10px;padding:12px;flex-shrink:0;
    text-align:center;
}
.sb-stu-avatar{
    width:50px;height:50px;border-radius:50%;
    background:linear-gradient(135deg,var(--gold),#f97316);
    display:flex;align-items:center;justify-content:center;
    color:var(--navy);font-size:18px;font-weight:800;
    margin:0 auto 8px;overflow:hidden;
}
.sb-stu-avatar img{width:100%;height:100%;object-fit:cover;}
.sb-stu-name{color:#fff;font-size:12px;font-weight:700;margin-bottom:2px;}
.sb-stu-class{color:rgba(255,255,255,.5);font-size:10px;}
.sb-admit{
    display:inline-flex;align-items:center;gap:4px;
    background:var(--gold-bg);color:var(--gold);
    font-size:9.5px;font-weight:700;border-radius:20px;padding:2px 8px;margin-top:6px;
}

/* Nav */
.sb-nav{list-style:none;padding:6px 0;flex:1;overflow-y:auto;overflow-x:hidden;}
.sb-group{margin-bottom:8px;border-bottom:1px solid rgba(255,255,255,.03);padding-bottom:8px;}
.sb-group:last-child{border-bottom:none;}
.sb-hdr{
    display:flex;align-items:center;justify-content:space-between;
    padding:8px 10px;cursor:pointer;user-select:none;
    color:rgba(255,255,255,.75);transition:all .2s;border-radius:6px;
    margin:0 6px;
}
.sb-hdr:hover{background:rgba(255,255,255,.05);color:#fff;}
.sb-hdr-left{display:flex;align-items:center;gap:6px;}
.sb-hdr-icon{
    width:22px;height:22px;border-radius:50%;background:#f59e0b;
    display:flex;align-items:center;justify-content:center;
    color:#fff;font-size:9.5px;flex-shrink:0;
}
.sb-hdr-title{font-family:'Plus Jakarta Sans',sans-serif;color:#fff;font-size:11px;font-weight:700;letter-spacing:0.2px;}
.sb-hdr-arrow{font-size:9px;color:rgba(255,255,255,.3);transition:transform .2s;}

.sb-submenu{list-style:none;padding:2px 6px 2px 20px;display:none;}
.sb-submenu.open{display:block;}
.sb-submenu li{margin-bottom:1px;}
.sb-submenu a{
    display:flex;align-items:center;justify-content:space-between;
    padding:6px 8px;border-radius:6px;
    color:rgba(255,255,255,.55);font-size:11px;font-weight:500;
    text-decoration:none;transition:all .18s;
}
.sb-submenu a:hover{color:#fff;background:rgba(255,255,255,.05);}
.sb-submenu li.active a{color:#f59e0b;font-weight:700;}
.sb-submenu-label{display:flex;align-items:center;gap:6px;}
.sb-submenu-icon{font-size:9px;color:#f59e0b;flex-shrink:0;opacity:0.85;}

.sb-bottom{padding:10px;border-top:1px solid rgba(255,255,255,.08);flex-shrink:0;}
.sb-logout{
    display:flex;align-items:center;gap:8px;color:rgba(255,255,255,.4);
    font-size:11.5px;padding:7px 9px;border-radius:7px;text-decoration:none;transition:.2s;
}
.sb-logout:hover{background:rgba(239,68,68,.12);color:#ef4444;}

/* ─── MAIN ────────────────────────────────────────────────── */
.main{margin-left:220px;flex:1;display:flex;flex-direction:column;min-height:100vh;}

/* ─── TOPBAR ─────────────────────────────────────────────── */
.topbar{
    background:#white;background-color:#fff;border-bottom:1px solid var(--border);
    height:62px;padding:0 22px;
    display:flex;align-items:center;justify-content:space-between;
    position:sticky;top:0;z-index:100;
    box-shadow:0 1px 3px rgba(0,0,0,.05);
}
.topbar-left{display:flex;align-items:center;gap:13px;}
.hamburger{background:none;border:none;color:var(--t2);font-size:17px;cursor:pointer;padding:4px;display:none;}
.greeting h2{font-family:'Plus Jakarta Sans',sans-serif;font-size:15px;font-weight:700;color:var(--t1);line-height:1.2;}
.greeting p{font-size:11.5px;color:var(--t2);}
.greeting a{color:var(--gold);text-decoration:none;font-weight:600;}
.topbar-right{display:flex;align-items:center;gap:10px;}
.date-pill{
    display:flex;align-items:center;gap:6px;background:var(--page);
    border:1px solid var(--border);border-radius:8px;padding:6px 11px;
    font-size:11.5px;color:var(--t2);
}
.date-pill i{color:var(--gold);}
.notif-wrap{position:relative;}
.notif-btn{
    background:var(--page);border:1px solid var(--border);border-radius:8px;
    width:37px;height:37px;display:flex;align-items:center;justify-content:center;
    cursor:pointer;color:var(--t2);font-size:15px;transition:.2s;position:relative;
}
.notif-btn:hover{border-color:var(--gold);color:var(--gold);}
.notif-badge{
    position:absolute;top:-5px;right:-5px;background:var(--red);color:#fff;
    font-size:9px;font-weight:700;border-radius:10px;padding:1px 5px;min-width:16px;text-align:center;
}
.notif-drop{
    position:absolute;top:calc(100% + 8px);right:0;width:280px;
    background:#fff;border:1px solid var(--border);border-radius:12px;
    box-shadow:var(--shadow-lg);display:none;z-index:300;overflow:hidden;
}
.notif-drop.open{display:block;}
.nd-hdr{padding:12px 14px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;}
.nd-hdr strong{font-size:12.5px;}
.nd-mark{font-size:11px;color:var(--gold);cursor:pointer;}
.nd-empty{padding:22px;text-align:center;color:var(--t3);font-size:11.5px;}
.user-wrap{position:relative;}
.user-btn{
    display:flex;align-items:center;gap:7px;cursor:pointer;
    padding:4px 7px;border-radius:9px;border:1px solid transparent;transition:.2s;
}
.user-btn:hover{background:var(--page);border-color:var(--border);}
.avatar{
    width:34px;height:34px;border-radius:9px;
    background:linear-gradient(135deg,var(--gold),#f97316);
    display:flex;align-items:center;justify-content:center;
    color:var(--navy);font-size:12px;font-weight:800;overflow:hidden;flex-shrink:0;
}
.avatar img{width:100%;height:100%;object-fit:cover;}
.user-info strong{display:block;font-size:11.5px;font-weight:700;color:var(--t1);}
.user-info span{font-size:10px;color:var(--t2);}
.user-caret{display:inline-block;transition:transform .2s;}
.user-drop{
    position:absolute;top:calc(100% + 8px);right:0;width:170px;
    background:#fff;border:1px solid var(--border);border-radius:11px;
    box-shadow:var(--shadow-lg);display:none;z-index:300;overflow:hidden;
}
.user-drop.open{display:block;}
.user-drop a{display:flex;align-items:center;gap:9px;padding:10px 13px;font-size:12.5px;color:var(--t1);text-decoration:none;transition:.15s;}
.user-drop a:hover{background:var(--page);}
.user-drop a.danger{color:var(--red);}
.user-drop a i{width:13px;text-align:center;color:var(--t2);font-size:12px;}
.user-drop a.danger i{color:var(--red);}

.topbar-logout-btn{
    background:var(--page);border:1px solid var(--border);
    color:#ef4444;border-radius:8px;width:36px;height:36px;
    display:flex;align-items:center;justify-content:center;
    font-size:14px;cursor:pointer;text-decoration:none;transition:.2s;flex-shrink:0;
}
.topbar-logout-btn:hover{
    background:#ef4444;color:#fff;border-color:#ef4444;
    box-shadow:0 2px 8px rgba(239,68,68,.3);
}

/* ─── PAGE CONTAINER (FULL WIDTH) ─────────────────────────── */
.pg{
    padding:20px 22px;
    width:100%;
    flex:1;
    display:flex;
    flex-direction:column;
    gap:20px;
}

/* ─── HERO PROFILE BANNER (FULL WIDTH) ─────────────────────── */
.profile-hero {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-radius: 18px;
    padding: 26px 30px;
    color: #fff;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.1);
    width: 100%;
}
.profile-hero::before {
    content: '';
    position: absolute;
    top: -50px; right: -50px;
    width: 250px; height: 250px;
    background: radial-gradient(circle, rgba(245, 158, 11, 0.2) 0%, rgba(245, 158, 11, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
}
.profile-hero-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    position: relative;
    z-index: 2;
    flex-wrap: wrap;
}
.profile-hero-left {
    display: flex;
    align-items: center;
    gap: 22px;
    flex-wrap: wrap;
}
.profile-avatar-wrap {
    position: relative;
}
.profile-avatar {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    border: 4px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    background: linear-gradient(135deg, var(--gold), #ea580c);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 32px;
    font-weight: 800;
    overflow: hidden;
    flex-shrink: 0;
}
.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.profile-status-badge {
    position: absolute;
    bottom: 2px;
    right: 2px;
    background: #10b981;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 3px solid #0f172a;
}
.profile-info h1 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 23px;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 6px;
    letter-spacing: -0.5px;
}
.profile-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 6px;
}
.pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 600;
    letter-spacing: 0.3px;
}
.pill-gold { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
.pill-blue { background: rgba(59, 130, 246, 0.2); color: #93c5fd; border: 1px solid rgba(59, 130, 246, 0.3); }
.pill-green { background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.3); }
.pill-purple { background: rgba(139, 92, 246, 0.2); color: #c4b5fd; border: 1px solid rgba(139, 92, 246, 0.3); }

.profile-hero-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.btn-hero {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 9px 16px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    backdrop-filter: blur(8px);
}
.btn-hero:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    color: #fff;
}
.btn-hero-primary {
    background: linear-gradient(135deg, var(--gold), #ea580c);
    border: none;
    color: #fff;
}
.btn-hero-primary:hover {
    background: linear-gradient(135deg, #d97706, #c2410c);
    box-shadow: 0 4px 14px rgba(234, 88, 12, 0.4);
}

/* ─── QUICK METRICS STATS (FULL WIDTH) ─────────────────────── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    width: 100%;
}
.stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
.stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    flex-shrink: 0;
}
.stat-icon-green { background: #ecfdf5; color: #10b981; }
.stat-icon-blue { background: #eff6ff; color: #3b82f6; }
.stat-icon-purple { background: #f5f3ff; color: #8b5cf6; }
.stat-icon-gold { background: #fef3c7; color: #f59e0b; }
.stat-info h3 {
    font-size: 17px;
    font-weight: 800;
    color: var(--t1);
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.stat-info p {
    font-size: 11.5px;
    color: var(--t2);
    margin-top: 1px;
}

/* ─── MODERN TABBED PROFILE CARD (FULL WIDTH) ──────────────── */
.profile-tabs-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow);
    overflow: hidden;
    width: 100%;
}
.tabs-nav {
    display: flex;
    border-bottom: 1px solid var(--border);
    background: #f8fafc;
    overflow-x: auto;
    scrollbar-width: none;
}
.tabs-nav::-webkit-scrollbar { display: none; }
.tab-btn {
    padding: 15px 22px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    color: var(--t2);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
    transition: all 0.2s ease;
}
.tab-btn:hover {
    color: var(--t1);
    background: rgba(0, 0, 0, 0.02);
}
.tab-btn.active {
    color: #1e293b;
    border-bottom-color: var(--gold);
    background: #fff;
    font-weight: 700;
}

.tab-pane {
    padding: 24px;
    display: none;
}
.tab-pane.active {
    display: block;
    animation: fadeIn 0.25s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ─── DATA DETAILS GRID ───────────────────────────────────── */
.detail-section-title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13.5px;
    font-weight: 700;
    color: var(--navy);
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 8px;
}
.detail-section-title i {
    color: var(--gold);
}
.detail-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 22px;
}
.detail-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 14px;
}
.detail-label {
    font-size: 10.5px;
    font-weight: 700;
    color: var(--t2);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}
.detail-value {
    font-size: 13px;
    font-weight: 600;
    color: var(--t1);
    word-break: break-word;
}

/* Guardian / Parents Cards */
.parent-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
    margin-bottom: 20px;
}
.parent-box {
    border: 1px solid var(--border);
    border-radius: 14px;
    background: #fff;
    padding: 18px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
}
.parent-box-hdr {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px dashed var(--border);
}
.parent-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f1f5f9;
    color: var(--navy);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
}
.parent-name {
    font-size: 14px;
    font-weight: 700;
    color: var(--t1);
}
.parent-role {
    font-size: 11px;
    color: var(--gold);
    font-weight: 600;
}
.parent-info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 12px;
}
.parent-info-row span { color: var(--t2); }
.parent-info-row strong { color: var(--t1); text-align: right; }

/* ─── FOOTER ──────────────────────────────────────────────── */
.footer{
    margin-top:auto;padding:18px 0 0;
    border-top:1px solid var(--border);
    display:flex;justify-content:space-between;align-items:center;
    font-size:11.5px;color:var(--t3);
    width: 100%;
}

/* ─── RESPONSIVE OVERRIDES ────────────────────────────────── */
@media(max-width:1024px){
    .sidebar{
        transform:translateX(-220px);
        width:220px;
        z-index:200;
    }
    .sidebar.open{
        transform:translateX(0) !important;
        box-shadow:0 0 25px rgba(0,0,0,0.5);
    }
    .main{margin-left:0 !important;}
    .topbar{padding:0 14px;}
    .hamburger{display:flex !important;}
    .stats-grid{grid-template-columns:repeat(2, 1fr);}
    .detail-grid{grid-template-columns:repeat(2, 1fr);}
    .parent-grid{grid-template-columns:1fr;}
    .profile-hero{padding:20px;}
    .profile-hero-content{flex-direction:column;align-items:flex-start;}
    .profile-hero-actions{width:100%;justify-content:flex-start;}
}
@media(max-width:768px){
    .topbar{height:54px;padding:0 10px;gap:6px;}
    .topbar-left{gap:6px;}
    .greeting h2{font-size:13px;max-width:140px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .topbar-subtitle{display:none !important;}
    .date-pill{display:none !important;}
    .user-info, .user-caret{display:none !important;}
    .user-btn{padding:2px;}
    .avatar{width:32px;height:32px;font-size:11px;}
    .notif-btn{width:34px;height:34px;font-size:14px;}
    .notif-drop{right:-10px;width:calc(100vw - 20px);max-width:300px;}
    .user-drop{right:0;}
    .pg{padding:14px 12px;}
    .profile-hero-left{flex-direction:column;text-align:center;}
    .profile-pills{justify-content:center;}
    .stats-grid{grid-template-columns:1fr;}
    .detail-grid{grid-template-columns:1fr;}
    .parent-grid{grid-template-columns:1fr;}
    .tab-btn{padding:12px 14px;font-size:12px;}
    .tab-pane{padding:16px;}
    .footer{flex-direction:column;gap:4px;text-align:center;}
}
</style>
</head>
<body>

<!-- ══════════ SIDEBAR ══════════ -->
@include('parent.partials.sidebar')

<!-- ══════════ MAIN ══════════ -->
<div class="main">

    <!-- TOPBAR -->
    @include('parent.partials.topbar', [
        'title' => 'Student Profile',
        'subtitle' => 'Comprehensive academic & personal profile overview'
    ])

    <!-- PAGE (FULL SCREEN) -->
    <div class="pg">

        <!-- 1. Hero Profile Banner -->
        <div class="profile-hero">
            <div class="profile-hero-content">
                <div class="profile-hero-left">
                    <div class="profile-avatar-wrap">
                        <div class="profile-avatar">
                            @if(isset($student) && $student?->photo)
                                <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}">
                            @else
                                {{ $stuInitials ?? 'ST' }}
                            @endif
                        </div>
                        <div class="profile-status-badge" title="Active Student"></div>
                    </div>
                    <div class="profile-info">
                        <h1>{{ $stuName }}</h1>
                        <div class="profile-pills">
                            <span class="pill pill-gold">
                                <i class="fas fa-id-badge"></i> {{ $studentIdDisplay }}
                            </span>
                            <span class="pill pill-blue">
                                <i class="fas fa-graduation-cap"></i> {{ $classDisplay }} – Sec {{ $sectionDisplay }}
                            </span>
                            <span class="pill pill-purple">
                                <i class="fas fa-calendar-alt"></i> {{ $sessionDisplay }}
                            </span>
                            <span class="pill pill-green">
                                <i class="fas fa-circle-check"></i> Enrolled
                            </span>
                        </div>
                    </div>
                </div>

                <div class="profile-hero-actions">
                    <a href="{{ route('parent.cards.index') }}" class="btn-hero btn-hero-primary">
                        <i class="fas fa-id-card"></i> ID Card & Passes
                    </a>
                    <a href="{{ route('parent.attendance.index') }}" class="btn-hero">
                        <i class="fas fa-calendar-check"></i> Attendance
                    </a>
                    <a href="{{ route('parent.fees.index') }}" class="btn-hero">
                        <i class="fas fa-indian-rupee-sign"></i> Fees
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Quick Key Metrics (4 Column Grid) -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-icon-green">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $attendanceRate ?? 0 }}%</h3>
                    <p>Attendance Rate</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-gold">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-info">
                    <h3>₹{{ number_format($pendingFee ?? 0) }}</h3>
                    <p>{{ ($pendingFee ?? 0) > 0 ? 'Pending Dues' : 'All Fees Cleared' }}</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-blue">
                    <i class="fas fa-hashtag"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $student?->roll_number ?: 'N/A' }}</h3>
                    <p>Class Roll Number</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-purple">
                    <i class="fas fa-file-lines"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $documents->count() }}</h3>
                    <p>Documents on Record</p>
                </div>
            </div>
        </div>

        <!-- 3. Profile Information Tabs (Full Width) -->
        <div class="profile-tabs-card">
            <div class="tabs-nav">
                <button type="button" class="tab-btn active" onclick="switchProfileTab('personal', this)">
                    <i class="fas fa-user"></i> Personal Info
                </button>
                <button type="button" class="tab-btn" onclick="switchProfileTab('academic', this)">
                    <i class="fas fa-graduation-cap"></i> Academic & Admission
                </button>
                <button type="button" class="tab-btn" onclick="switchProfileTab('parents', this)">
                    <i class="fas fa-people-roof"></i> Parents & Guardian
                </button>
                <button type="button" class="tab-btn" onclick="switchProfileTab('address', this)">
                    <i class="fas fa-location-dot"></i> Address & Emergency
                </button>
                <button type="button" class="tab-btn" onclick="switchProfileTab('health', this)">
                    <i class="fas fa-heart-pulse"></i> Health Profile
                </button>
                <button type="button" class="tab-btn" onclick="switchProfileTab('documents', this)">
                    <i class="fas fa-folder-open"></i> Documents ({{ $documents->count() }})
                </button>
            </div>

            <!-- Tab 1: Personal Info -->
            <div id="tab-personal" class="tab-pane active">
                <div class="detail-section-title">
                    <i class="fas fa-user-tag"></i> Student Identity & Personal Particulars
                </div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Full Name</div>
                        <div class="detail-value">{{ $student?->full_name ?: $stuName }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Date of Birth</div>
                        <div class="detail-value">{{ $student?->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M, Y') : 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Gender</div>
                        <div class="detail-value">{{ $student?->gender ? ucfirst($student->gender) : 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Age</div>
                        <div class="detail-value">{{ $student?->age ? $student->age . ' Years' : 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Blood Group</div>
                        <div class="detail-value"><span style="color:var(--red); font-weight:700;">{{ $student?->blood_group ?: 'N/A' }}</span></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Religion</div>
                        <div class="detail-value">{{ $student?->religion ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Caste / Category</div>
                        <div class="detail-value">{{ $student?->caste ?: ($student?->category?->name ?: 'General') }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Nationality</div>
                        <div class="detail-value">{{ $student?->nationality ?: 'Indian' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Mother Tongue</div>
                        <div class="detail-value">{{ $student?->mother_tongue ?: 'Hindi' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Aadhar / National ID</div>
                        <div class="detail-value">{{ $student?->national_id ?: ($student?->apaar_id ?: 'N/A') }}</div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Academic Details -->
            <div id="tab-academic" class="tab-pane">
                <div class="detail-section-title">
                    <i class="fas fa-school"></i> Academic Enrollment & Class History
                </div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Current Enrolled Class</div>
                        <div class="detail-value" style="color:var(--blue); font-weight:700;">{{ $classDisplay }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Assigned Section</div>
                        <div class="detail-value" style="color:var(--blue); font-weight:700;">Section {{ $sectionDisplay }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Class Roll Number</div>
                        <div class="detail-value">{{ $student?->roll_number ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Current Academic Session</div>
                        <div class="detail-value">{{ $sessionDisplay }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Admission Number</div>
                        <div class="detail-value" style="color:var(--gold); font-weight:700;">{{ $studentIdDisplay }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Admission Date</div>
                        <div class="detail-value">{{ $student?->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('d M, Y') : 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Class at Admission</div>
                        <div class="detail-value">{{ $student?->class_at_admission ?: ($student?->class?->name ?: 'N/A') }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Enrollment / TC Number</div>
                        <div class="detail-value">{{ $student?->tc_number ?: ($student?->enrollment_number ?: 'N/A') }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Previous School Attended</div>
                        <div class="detail-value">{{ $student?->prev_school ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">School Affiliation / Board</div>
                        <div class="detail-value">{{ $student?->prev_board ?: ($school?->board ?? 'State / CBSE') }}</div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Parents & Guardian -->
            <div id="tab-parents" class="tab-pane">
                <div class="detail-section-title">
                    <i class="fas fa-user-group"></i> Parents & Authorized Guardian Profiles
                </div>
                <div class="parent-grid">
                    <!-- Father Card -->
                    <div class="parent-box">
                        <div class="parent-box-hdr">
                            <div class="parent-icon"><i class="fas fa-user-tie"></i></div>
                            <div>
                                <div class="parent-name">{{ $student?->father_name ?: 'Father Details' }}</div>
                                <div class="parent-role">Father</div>
                            </div>
                        </div>
                        <div class="parent-info-row">
                            <span>Phone Number</span>
                            <strong>{{ $student?->father_phone ?: 'N/A' }}</strong>
                        </div>
                        <div class="parent-info-row">
                            <span>Email Address</span>
                            <strong>{{ $student?->father_email ?: 'N/A' }}</strong>
                        </div>
                        <div class="parent-info-row">
                            <span>Occupation</span>
                            <strong>{{ $student?->father_occupation ?: 'N/A' }}</strong>
                        </div>
                        <div class="parent-info-row">
                            <span>Aadhar ID</span>
                            <strong>{{ $student?->father_aadhar ?: 'N/A' }}</strong>
                        </div>
                    </div>

                    <!-- Mother Card -->
                    <div class="parent-box">
                        <div class="parent-box-hdr">
                            <div class="parent-icon" style="background:#fdf2f8; color:#ec4899;"><i class="fas fa-person-dress"></i></div>
                            <div>
                                <div class="parent-name">{{ $student?->mother_name ?: 'Mother Details' }}</div>
                                <div class="parent-role" style="color:#ec4899;">Mother</div>
                            </div>
                        </div>
                        <div class="parent-info-row">
                            <span>Phone Number</span>
                            <strong>{{ $student?->mother_phone ?: 'N/A' }}</strong>
                        </div>
                        <div class="parent-info-row">
                            <span>Email Address</span>
                            <strong>{{ $student?->mother_email ?: 'N/A' }}</strong>
                        </div>
                        <div class="parent-info-row">
                            <span>Occupation</span>
                            <strong>{{ $student?->mother_occupation ?: 'N/A' }}</strong>
                        </div>
                        <div class="parent-info-row">
                            <span>Aadhar ID</span>
                            <strong>{{ $student?->mother_aadhar ?: 'N/A' }}</strong>
                        </div>
                    </div>

                    <!-- Primary Guardian Card -->
                    <div class="parent-box">
                        <div class="parent-box-hdr">
                            <div class="parent-icon" style="background:#eff6ff; color:#3b82f6;"><i class="fas fa-shield-heart"></i></div>
                            <div>
                                <div class="parent-name">{{ $student?->guardian_name ?: ($student?->father_name ?: 'Primary Guardian') }}</div>
                                <div class="parent-role" style="color:#3b82f6;">{{ $student?->guardian_relationship ? ucfirst($student->guardian_relationship) : 'Primary Contact' }}</div>
                            </div>
                        </div>
                        <div class="parent-info-row">
                            <span>Contact Phone</span>
                            <strong>{{ $student?->guardian_phone ?: ($student?->father_phone ?: 'N/A') }}</strong>
                        </div>
                        <div class="parent-info-row">
                            <span>Email Address</span>
                            <strong>{{ $student?->guardian_email ?: ($student?->father_email ?: 'N/A') }}</strong>
                        </div>
                        <div class="parent-info-row">
                            <span>Relationship</span>
                            <strong>{{ $student?->guardian_relationship ? ucfirst($student->guardian_relationship) : 'Parent' }}</strong>
                        </div>
                        <div class="parent-info-row">
                            <span>WhatsApp</span>
                            <strong>{{ $student?->whatsapp_number ?: ($student?->guardian_phone ?: 'N/A') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Address & Emergency -->
            <div id="tab-address" class="tab-pane">
                <div class="detail-section-title">
                    <i class="fas fa-map-location-dot"></i> Residential & Permanent Address
                </div>
                <div class="detail-grid" style="grid-template-columns: repeat(2, 1fr);">
                    <div class="detail-item">
                        <div class="detail-label">Current Residential Address</div>
                        <div class="detail-value">
                            {{ $student?->address ?: 'N/A' }}
                            @if($student?->address_line_2), {{ $student->address_line_2 }} @endif
                            @if($student?->city), {{ $student->city }} @endif
                            @if($student?->state), {{ $student->state }} @endif
                            @if($student?->pincode) - {{ $student->pincode }} @endif
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Permanent Address</div>
                        <div class="detail-value">
                            {{ $student?->permanent_address ?: ($student?->address ?: 'Same as residential address') }}
                            @if($student?->permanent_city), {{ $student->permanent_city }} @endif
                            @if($student?->permanent_state), {{ $student->permanent_state }} @endif
                            @if($student?->permanent_pincode) - {{ $student->permanent_pincode }} @endif
                        </div>
                    </div>
                </div>

                <div class="detail-section-title" style="margin-top: 10px;">
                    <i class="fas fa-truck-medical"></i> Emergency Contact Particulars
                </div>
                <div class="detail-grid" style="grid-template-columns: repeat(3, 1fr);">
                    <div class="detail-item">
                        <div class="detail-label">Emergency Phone</div>
                        <div class="detail-value" style="color:var(--red); font-weight:700;">{{ $student?->guardian_phone ?: ($student?->father_phone ?: ($student?->phone ?: 'N/A')) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Primary Contact Person</div>
                        <div class="detail-value">{{ $student?->guardian_name ?: ($student?->father_name ?: 'Guardian') }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Emergency Address</div>
                        <div class="detail-value">{{ $student?->emergency_address ?: ($student?->address ?: 'N/A') }}</div>
                    </div>
                </div>
            </div>

            <!-- Tab 5: Health & Medical Profile -->
            <div id="tab-health" class="tab-pane">
                <div class="detail-section-title">
                    <i class="fas fa-notes-medical"></i> Physical Health & Medical Records
                </div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Blood Group</div>
                        <div class="detail-value" style="color:var(--red); font-size:16px; font-weight:800;">{{ $student?->blood_group ?: 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Height / Weight</div>
                        <div class="detail-value">{{ $student?->medical_height ? $student->medical_height . ' cm' : 'N/A' }} / {{ $student?->medical_weight ? $student->medical_weight . ' kg' : 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Vision (Left / Right)</div>
                        <div class="detail-value">{{ $student?->medical_vision_left ?: '6/6' }} / {{ $student?->medical_vision_right ?: '6/6' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Allergies / Special Concerns</div>
                        <div class="detail-value">{{ $student?->medical_allergies ?: ($student?->allergies ?: 'No known allergies reported') }}</div>
                    </div>
                    <div class="detail-item" style="grid-column: span 4;">
                        <div class="detail-label">Medical History / Chronic Conditions</div>
                        <div class="detail-value">{{ $student?->medical_history ?: ($student?->medical_illness ?: 'None recorded in official health register.') }}</div>
                    </div>
                </div>
            </div>

            <!-- Tab 6: Official Documents -->
            <div id="tab-documents" class="tab-pane">
                <div class="detail-section-title">
                    <i class="fas fa-folder-closed"></i> Registered Certificates & Verified Documents
                </div>

                @if($documents && $documents->count() > 0)
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        @foreach($documents as $doc)
                            <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 18px; border:1px solid var(--border); border-radius:12px; background:#f8fafc;">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div style="width:38px; height:38px; border-radius:8px; background:#eff6ff; color:#3b82f6; display:flex; align-items:center; justify-content:center; font-size:16px;">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div>
                                        <strong style="font-size:13px; color:var(--t1); display:block;">{{ $doc->title }}</strong>
                                        <span style="font-size:11px; color:var(--t2);">Uploaded on {{ \Carbon\Carbon::parse($doc->created_at)->format('d M, Y') }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('parent.documents.download', $doc->id) }}" style="display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:8px; background:#fff; border:1px solid var(--border); color:var(--t1); font-size:12px; font-weight:600; text-decoration:none; transition:.2s;">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align:center; padding:40px 20px; color:var(--t2);">
                        <i class="fas fa-folder-open" style="font-size:42px; color:var(--t3); margin-bottom:12px; display:block;"></i>
                        <h4 style="font-size:14px; font-weight:700; color:var(--t1); margin-bottom:4px;">No Documents on File</h4>
                        <p style="font-size:12px;">All certificates and admission verification files uploaded by administration will appear here.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- 4. Footer -->
        <div class="footer">
            <span>© 2026 EducorERP. All rights reserved.</span>
            <span>Version 2.0.0 &nbsp;|&nbsp; 🔒 Secure & Trusted</span>
        </div>
    </div>
</div>

<script>
function switchProfileTab(tabName, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    
    if (btn) btn.classList.add('active');
    const target = document.getElementById('tab-' + tabName);
    if (target) target.classList.add('active');
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const isMobile = window.innerWidth <= 1024;
    
    if (isMobile) {
        if (sidebar) sidebar.classList.toggle('open');
        if (overlay) overlay.classList.toggle('open');
    } else {
        document.body.classList.toggle('sidebar-collapsed');
        const isCollapsed = document.body.classList.contains('sidebar-collapsed');
        localStorage.setItem('student_sidebar_collapsed', isCollapsed ? 'true' : 'false');
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('open');
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.innerWidth > 1024) {
        if (localStorage.getItem('student_sidebar_collapsed') === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }
    }

    // Auto-expand current active menu
    document.querySelectorAll('.sb-submenu').forEach(submenu => {
        if (submenu.querySelector('li.active')) {
            submenu.classList.add('open');
            const hdr = submenu.previousElementSibling;
            if (hdr && hdr.classList.contains('sb-hdr')) {
                hdr.classList.add('open');
            }
        }
    });
});

function toggleDrop(id){
    ['userDrop', 'notifDrop'].forEach(d=>{if(d!==id)document.getElementById(d).classList.remove('open');});
    document.getElementById(id).classList.toggle('open');
}
document.addEventListener('click',e=>{
    if(!e.target.closest('.user-wrap'))document.getElementById('userDrop').classList.remove('open');
    if(!e.target.closest('.notif-wrap'))document.getElementById('notifDrop').classList.remove('open');
});
</script>
</body>
</html>

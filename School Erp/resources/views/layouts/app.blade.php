@php
    $navNotifications = collect();
    if (auth()->check()) {
        $schoolId = auth()->user()->school_id ?: (request()->route('school')?->id ?: (request()->attributes->get('school')?->id ?: (\Illuminate\Support\Facades\App::resolved('currentSchool') ? app('currentSchool')?->id : null)));
        if ($schoolId) {
            $centralNotifs = \App\Services\NotificationService::getNotifications(auth()->user(), 30, true)->map(function ($n) {
                $user = auth()->user();
                $role = $user->hasRole('school_admin') ? 'school_admin' : ($user->hasRole('teacher') ? 'teacher' : ($user->hasRole('student') ? 'student' : ($user->hasRole('parent') ? 'parent' : null)));
                $actionUrl = $n->action_url;
                if (empty($actionUrl) || $actionUrl === '#') {
                    $actionUrl = \App\Services\NotificationService::getDefaultActionUrl($n->module, $role);
                }
                return (object) [
                    'id' => $n->id,
                    'title' => $n->title,
                    'content' => $n->message,
                    'time' => $n->created_at ? $n->created_at->diffForHumans() : 'Just now',
                    'icon' => $n->icon ?: \App\Services\NotificationService::getDefaultIcon($n->module),
                    'color' => $n->color ?: \App\Services\NotificationService::getDefaultColor($n->module),
                    'type' => ucfirst($n->module),
                    'created_at' => $n->created_at,
                    'is_today' => true,
                    'action_url' => $actionUrl ?? '#'
                ];
            });

            // Query pending fee structure deletions
            $pendingDeletions = \App\Models\PendingDeletion::where('school_id', $schoolId)
                ->get()
                ->map(function ($pd) {
                    return (object) [
                        'id' => $pd->id,
                        'title' => 'Delete Request: ' . $pd->item_name,
                        'content' => 'Requested by: ' . $pd->requested_by . ' (' . ucfirst($pd->type) . ')',
                        'time' => $pd->created_at->diffForHumans(),
                        'icon' => 'fa-trash-can',
                        'color' => '#ef4444',
                        'type' => 'Delete Request',
                        'created_at' => $pd->created_at,
                        'is_today' => true,
                        'action_url' => '#' // Handled via inline modal action
                    ];
                });

            $navNotifications = $pendingDeletions->concat($centralNotifs);
        }
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EducorERP') — EducorERP</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{
    --navy:#1a1f3c;--navy2:#12172e;--navy3:rgba(255,255,255,.06);
    --gold:#f59e0b;--gold-bg:rgba(245,158,11,.15);
    --green:#10b981;--red:#ef4444;--purple:#8b5cf6;--blue:#3b82f6;
    --page:#f8f7f4;--white:#fff;
    --t1:#111827;--t2:#6b7280;--t3:#9ca3af;
    --border:#e5e7eb;--card:#fff;
    --shadow:0 1px 4px rgba(0,0,0,.07);
    --shadow-lg:0 8px 32px rgba(0,0,0,.12);
}
@media print {
    #robot-assistant,
    .robot-body,
    #custom-lang-switcher,
    .lang-switch-wrap,
    .lang-switch-container,
    .skiptranslate,
    iframe.goog-te-banner-frame,
    .goog-te-banner-frame,
    #goog-gt-tt,
    .goog-tooltip,
    .goog-te-balloon-frame,
    #robot-chat-panel {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
    }
}
body{font-family:'Plus Jakarta Sans',sans-serif !important;background:var(--page);color:var(--t1);display:flex;min-height:100vh;overflow-x:hidden;}
.w-5 { width: 1.25rem !important; }
.h-5 { height: 1.25rem !important; }

/* Sidebar variables */
:root {
    --sidebar-bg: #ffffff;
    --sidebar-bg-rgb: 255, 255, 255;
    --sidebar-stitch: rgba(18, 23, 46, 0.2); /* Dark color stitch same like dark mode in light mode */
    --sidebar-text: #374151;
}
body.dark-mode {
    --sidebar-bg: #0b0f1a;
    --sidebar-bg-rgb: 11, 15, 26;
    --sidebar-stitch: rgba(255, 255, 255, 0.2);
    --sidebar-text: rgba(255, 255, 255, 0.8);
}

/* ─── SIDEBAR ─────────────────────────────────────────────── */
.sidebar{
    width:240px;min-width:240px;
    background-color: var(--sidebar-bg) !important;
    background-image: linear-gradient(rgba(var(--sidebar-bg-rgb), 0.88), rgba(var(--sidebar-bg-rgb), 0.88)), url('/images/leather_texture.png') !important;
    background-repeat: repeat !important;
    background-size: 180px 180px !important;
    border-right:1px solid rgba(255,255,255,0.06) !important;
    height:100vh;height:100dvh;position:fixed;left:0;top:0;
    display:flex;flex-direction:column;z-index:1001;
    overflow:hidden;
    box-shadow: inset -5px 0 15px rgba(0,0,0,0.2) !important;
    transition:transform .3s ease, width .3s ease;
}
/* Sidebar closed state for desktop */
body.sidebar-closed .sidebar {
    transform: translateX(-240px) !important;
}
body.sidebar-closed .main {
    margin-left: 0 !important;
}

.sidebar::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    right: 8px;
    width: 0;
    border-right: 1.5px dashed var(--sidebar-stitch) !important;
    pointer-events: none;
    z-index: 10;
}
.sidebar::-webkit-scrollbar{width:3px;}
.sidebar::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:4px;}

/* Logo */
.sb-logo{
    padding:20px 16px 16px;
    display:flex;align-items:center;gap:10px;
    border-bottom:1px solid rgba(255,255,255,0.08) !important;
    text-decoration:none;flex-shrink:0;position:relative;
}
.sb-close-btn{
    display:none;background:rgba(0,0,0,0.06);border:none;color:var(--sidebar-text) !important;
    width:30px;height:30px;border-radius:8px;align-items:center;justify-content:center;
    margin-left:auto;cursor:pointer;font-size:14px;transition:all .2s;
}
body.dark-mode .sb-close-btn {
    background:rgba(255,255,255,0.08);
}
.sb-close-btn:hover{background:rgba(0,0,0,0.12);}
body.dark-mode .sb-close-btn:hover{background:rgba(255,255,255,0.15);}
.sb-logo-icon{
    width:38px;height:38px;border-radius:12px;
    background:linear-gradient(135deg,#3b82f6,#2563eb) !important;
    display:flex;align-items:center;justify-content:center;
    font-size:18px;color:#fff;flex-shrink:0;
    box-shadow:0 4px 12px rgba(37,99,235,.25);
}
.sb-logo-text strong{
    display:block;color:var(--sidebar-text) !important;font-size:15px;font-weight:800;
    font-family:'Plus Jakarta Sans',sans-serif;line-height:1.15;
}
.sb-logo-text span{color:var(--sidebar-stitch) !important;font-size:11.5px;font-weight:600;}

/* Unified School Brand Header (Clickable box) */
.sb-school-header {
    position: relative;
    padding: 24px 16px 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    border-bottom: 1px solid var(--sidebar-stitch) !important;
    text-decoration: none;
    flex-shrink: 0;
    transition: background 0.2s ease;
    background: rgba(18, 23, 46, 0.04) !important; /* Slightly dark background to differentiate */
}
body.dark-mode .sb-school-header {
    background: rgba(0, 0, 0, 0.25) !important;
}
.sb-school-header:hover {
    background: rgba(18, 23, 46, 0.07) !important;
}
body.dark-mode .sb-school-header:hover {
    background: rgba(0, 0, 0, 0.35) !important;
}

/* Sidebar Module Search styling */
.sb-search-box i {
    color: #64748b !important;
}
body.dark-mode .sb-search-box i {
    color: rgba(255, 255, 255, 0.5) !important;
}
#sbModuleSearch {
    background: #ffffff !important;
    color: #1e293b !important;
}
#sbModuleSearch::placeholder {
    color: #94a3b8 !important;
    opacity: 1 !important;
}
#sbModuleSearch:focus {
    border-color: #8b5cf6 !important;
    background: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15) !important;
}
body.dark-mode #sbModuleSearch {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #ffffff !important;
}
body.dark-mode #sbModuleSearch::placeholder {
    color: rgba(255, 255, 255, 0.5) !important;
}
body.dark-mode #sbModuleSearch:focus {
    background: rgba(255, 255, 255, 0.12) !important;
    border-color: #a78bfa !important;
    box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.2) !important;
}
.sb-school-header-top {
    display: flex;
    align-items: center;
    gap: 12px;
}
.sb-school-logo {
    width: 56px; /* Prominent and bigger logo */
    height: 56px;
    border-radius: 12px;
    background: rgba(18, 23, 46, 0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
    border: 1.5px solid var(--sidebar-stitch);
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    transition: transform 0.2s ease;
}
body.dark-mode .sb-school-logo {
    background: rgba(255, 255, 255, 0.05);
}
.sb-school-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.sb-school-logo i {
    font-size: 24px;
    color: var(--sidebar-text);
}
.sb-school-name-wrapper {
    min-width: 0;
    flex: 1;
}
.sb-school-name {
    color: var(--sidebar-text) !important;
    font-size: 15px;
    font-weight: 750;
    line-height: 1.3;
    word-break: break-word;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
@media (min-width: 769px) {
    .sb-school-sub,
    .sb-logo-text span,
    .sb-notif-btn {
        display: none !important;
    }
}
.sb-school-meta-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    margin-top: 2px;
}
.sb-school-session {
    display: flex;
    align-items: center;
    gap: 4px;
    color: var(--sidebar-text) !important;
    opacity: 0.85;
    font-size: 11.5px;
    font-weight: 600;
}
.sb-school-session i {
    font-size: 10px;
    opacity: 0.8;
}
.sb-school-header .sb-plan-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: rgba(139, 92, 246, 0.12) !important;
    color: #8b5cf6 !important;
    font-size: 10.5px;
    font-weight: 700;
    border-radius: 20px;
    padding: 3px 10px;
    border: 1px solid rgba(139, 92, 246, 0.2);
}
.sb-school-header .sb-plan-badge i {
    font-size: 8px;
    color: #8b5cf6 !important;
}
body.dark-mode .sb-school-header .sb-plan-badge {
    background: rgba(167, 139, 250, 0.18) !important;
    color: #a78bfa !important;
    border-color: rgba(167, 139, 250, 0.25);
}
body.dark-mode .sb-school-header .sb-plan-badge i {
    color: #a78bfa !important;
}
.sb-school-header .sb-close-btn {
    position: relative;
    top: auto;
    right: auto;
    margin: 0;
}
.avatar img{
    width:100%;height:100%;object-fit:cover;border-radius:9px;
}

/* Nav */
.sb-nav{list-style:none;padding:8px 10px 24px;flex:1;min-height:0;overflow-y:auto;overflow-x:hidden;}
.sb-group{margin-bottom:4px;border-bottom:none !important;padding-bottom:0;}
.sb-hdr{
    display:flex;align-items:center;justify-content:space-between;
    padding:10px 12px;cursor:pointer;user-select:none;
    color:var(--sidebar-text) !important;transition:all .2s;border-radius:12px !important;
    margin:2px 0;
    position:relative;
    -webkit-tap-highlight-color: transparent;
}
.sb-hdr:hover{background:rgba(0,0,0,0.08) !important;color:var(--sidebar-text) !important;}
body.dark-mode .sb-hdr:hover{background:rgba(255,255,255,0.08) !important;}
.sb-hdr-left{display:flex;align-items:center;gap:8px;}
.sb-hdr-icon{
    width:30px;height:30px;border-radius:50%;background:rgba(0,0,0,0.1) !important;
    display:flex;align-items:center;justify-content:center;
    color:var(--sidebar-text) !important;font-size:12px;flex-shrink:0;
}
body.dark-mode .sb-hdr-icon{background:rgba(255,255,255,0.1) !important;color:#fff !important;}
.sb-hdr-title{font-family:'Plus Jakarta Sans',sans-serif;color:inherit;font-size:13.5px;font-weight:700;letter-spacing:0.1px;}
.sb-hdr-arrow{font-size:10px;color:var(--sidebar-stitch) !important;transition:transform .2s;}
.sb-hdr.open .sb-hdr-arrow{transform:rotate(180deg);}

/* Active Group / Link highlighting matching Image 2 */
.sb-group.active-group .sb-hdr,
.sb-hdr.active-link {
    background: linear-gradient(135deg, #7c3aed, #6366f1) !important;
    color:#ffffff !important;
    box-shadow:0 4px 14px rgba(124,58,237,.35) !important;
}
body.dark-mode .sb-group.active-group .sb-hdr,
body.dark-mode .sb-hdr.active-link {
    background:linear-gradient(135deg,#6366f1,#818cf8) !important;
    color:#ffffff !important;
    box-shadow:0 4px 14px rgba(99,102,241,.35) !important;
}
.sb-group.active-group .sb-hdr .sb-hdr-title,
.sb-hdr.active-link .sb-hdr-title {
    color:#ffffff !important;
}
.sb-group.active-group .sb-hdr .sb-hdr-icon,
.sb-hdr.active-link .sb-hdr-icon {
    background:rgba(255,255,255,.25) !important;
    color:#ffffff !important;
}

/* Hover/Active Item Left Stitch Effect */
.sb-hdr:hover::before, .sb-submenu a:hover::before, .sb-tooltip-popup a:hover::before {
    content: '';
    position: absolute;
    left: 5px;
    top: 6px;
    bottom: 6px;
    width: 0;
    border-left: 1.5px dashed rgba(80, 30, 20, 0.7) !important;
    pointer-events: none;
}
body.dark-mode .sb-hdr:hover::before,
body.dark-mode .sb-submenu a:hover::before,
body.dark-mode .sb-tooltip-popup a:hover::before {
    border-left-color: rgba(255, 255, 255, 0.6) !important;
}
.sb-group.active-group .sb-hdr::before,
.sb-hdr.active-link::before {
    content: '';
    position: absolute;
    left: 5px;
    top: 6px;
    bottom: 6px;
    width: 0;
    border-left: 1.5px dashed #ff9800 !important;
    pointer-events: none;
}

.sb-submenu {
    list-style: none;
    padding: 0 6px 0 20px;
    max-height: 0;
    opacity: 0;
    overflow: hidden;
    transform: translateY(-4px);
    transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease, transform 0.25s ease, padding 0.3s ease;
    pointer-events: none;
}
@media(min-width: 769px) {
    .sb-group:hover .sb-submenu,
    .sb-submenu.open {
        max-height: 600px;
        opacity: 1;
        transform: translateY(0);
        padding: 4px 6px 6px 20px;
        pointer-events: auto;
    }
    .sb-group:hover .sb-hdr-arrow,
    .sb-hdr.open .sb-hdr-arrow {
        transform: rotate(180deg);
        color: #ff9800 !important;
    }
}
.sb-submenu li{margin-bottom:1px;}
.sb-submenu a{
    display:flex;align-items:center;justify-content:space-between;
    padding:6px 8px;border-radius:6px;
    color:var(--sidebar-text) !important;font-size:13px;font-weight:500;
    text-decoration:none;transition:all .18s;
    position:relative;
    -webkit-tap-highlight-color: transparent;
}
.sb-submenu a:hover{color:var(--sidebar-text) !important;background:rgba(0,0,0,0.07) !important;}
body.dark-mode .sb-submenu a:hover{color:#ffffff !important;background:rgba(255,255,255,0.06) !important;}
.sb-submenu li.active a{color:#ff9800 !important;background:rgba(255, 152, 0, 0.08) !important;border-left:none !important;font-weight:700;}
.sb-submenu li.active a::before {
    content: '';
    position: absolute;
    left: 4px;
    top: 4px;
    bottom: 4px;
    width: 0;
    border-left: 1.5px dashed #ff9800 !important;
    pointer-events: none;
}
.sb-submenu-label{display:flex;align-items:center;gap:6px;}
.sb-submenu-icon{font-size:10px;color:rgba(255,255,255,0.5);flex-shrink:0;opacity:0.85;}

/* Sidebar bottom */
.sb-bottom{padding:12px;border-top:1px solid var(--sidebar-stitch) !important;flex-shrink:0;background:transparent !important;}
.sb-logout{
    display:flex;align-items:center;gap:8px;
    color:var(--sidebar-text) !important;font-size:13.5px;font-weight:600;
    padding:8px 12px;border-radius:10px;
    text-decoration:none;transition:.2s;
}
.sb-logout:hover{background:rgba(239,68,68,0.12) !important;color:#ef4444 !important;}

/* ─── LIGHT MODE SIDEBAR OVERRIDES ──────────────────────── */
body:not(.dark-mode) .sidebar {
    background-color: #FDFDFD !important;
    background-image: linear-gradient(rgba(253, 253, 253, 0.90), rgba(253, 253, 253, 0.90)), url('/images/leather_texture.png') !important;
    background-repeat: repeat !important;
    background-size: 180px 180px !important;
    box-shadow: inset -5px 0 15px rgba(0,0,0,0.015), 0 4px 20px rgba(0, 0, 0, 0.05) !important;
    border-right: 1px solid #eef2f6 !important;
}
body:not(.dark-mode) .sidebar::after {
    display: none !important;
}
body:not(.dark-mode) .sb-logo {
    border-bottom: 1px solid #f1f5f9 !important;
}
body:not(.dark-mode) .sb-logo-text strong {
    color: #1e1b4b !important;
}
body:not(.dark-mode) .sb-logo-text span {
    color: #64748b !important;
}
body:not(.dark-mode) .sb-logo-icon {
    background: linear-gradient(135deg, #7c3aed, #6366f1) !important;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2) !important;
}
body:not(.dark-mode) .sb-school {
    background: #ffffff !important;
    border: 1px solid #eef2f6 !important;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.05) !important;
}
body:not(.dark-mode) .sb-school-icon {
    background: #f3f4f6 !important;
    color: #374151 !important;
}
body:not(.dark-mode) .sb-school-name {
    color: #1e1b4b !important;
}
body:not(.dark-mode) .sb-school-session {
    color: #64748b !important;
}
body:not(.dark-mode) .sb-plan-badge {
    background: #f5f3ff !important;
    color: #7c3aed !important;
}
body:not(.dark-mode) .sb-hdr-title {
    color: #374151 !important;
    font-weight: 600;
}
body:not(.dark-mode) .sb-hdr-icon {
    background: #EEEBFC !important;
    color: #7c3aed !important;
    border-radius: 50%;
}
body:not(.dark-mode) .sb-hdr:hover {
    background: #EEEBFC !important;
    color: #7c3aed !important;
}
body:not(.dark-mode) .sb-hdr:hover .sb-hdr-title {
    color: #7c3aed !important;
}
body:not(.dark-mode) .sb-hdr:hover .sb-hdr-icon {
    background: #ffffff !important;
    color: #7c3aed !important;
}
body:not(.dark-mode) .sb-hdr::before,
body:not(.dark-mode) .sb-hdr:hover::before,
body:not(.dark-mode) .sb-group.active-group .sb-hdr::before,
body:not(.dark-mode) .sb-hdr.active-link::before {
    display: none !important;
}
body:not(.dark-mode) .sb-bottom {
    border-top: 1px solid #f1f5f9 !important;
}
body:not(.dark-mode) .sb-logout {
    color: #4b5563 !important;
}
body:not(.dark-mode) .sb-logout:hover {
    background: rgba(239, 68, 68, 0.08) !important;
    color: #ef4444 !important;
}

/* ─── LIGHT MODE HOVER MENU OVERRIDES ────────────────────── */
body:not(.dark-mode) .sb-tooltip-popup {
    background-color: #FDFDFD !important;
    background-image: linear-gradient(rgba(253, 253, 253, 0.90), rgba(253, 253, 253, 0.90)), url('/images/leather_texture.png') !important;
    background-repeat: repeat !important;
    background-size: 180px 180px !important;
    border: 1px solid #eef2f6 !important;
    box-shadow: 0 12px 36px rgba(99, 102, 241, 0.1) !important;
}
body:not(.dark-mode) .sb-tooltip-popup::before {
    border-color: transparent #FDFDFD transparent transparent !important;
}
body:not(.dark-mode) .sb-tooltip-popup::after {
    display: none !important;
}
body:not(.dark-mode) .sb-tooltip-title {
    color: #7c3aed !important;
    border-bottom: 1px solid #f1f5f9 !important;
}
body:not(.dark-mode) .sb-tooltip-popup a {
    color: #4b5563 !important;
    background: transparent !important;
}
body:not(.dark-mode) .sb-tooltip-popup a:hover {
    background: #EEEBFC !important;
    color: #7c3aed !important;
}
body:not(.dark-mode) .sb-tooltip-popup a.active-link {
    color: #7c3aed !important;
    background: #EEEBFC !important;
    font-weight: 700 !important;
}
body:not(.dark-mode) .sb-tooltip-popup a::before,
body:not(.dark-mode) .sb-tooltip-popup a.active-link::before {
    display: none !important;
}
body:not(.dark-mode) .sb-tooltip-popup a .sb-submenu-icon {
    color: #7c3aed !important;
}

/* ─── HIDE SIDEBAR SCROLLBAR COMPLETELY ──────────────────── */
.sidebar, #appSidebar {
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
}
.sidebar::-webkit-scrollbar, #appSidebar::-webkit-scrollbar {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
}

/* ─── SHOW SIDEBAR SCROLLBAR FOR NAV ──────────────────── */
.sb-nav {
    scrollbar-width: thin !important;
    scrollbar-color: rgba(18, 23, 46, 0.25) transparent !important;
}
body.dark-mode .sb-nav {
    scrollbar-color: rgba(255, 255, 255, 0.2) transparent !important;
}
.sb-nav::-webkit-scrollbar {
    width: 5px !important;
    height: 0 !important;
    display: block !important;
}
.sb-nav::-webkit-scrollbar-track {
    background: transparent !important;
}
.sb-nav::-webkit-scrollbar-thumb {
    background: rgba(18, 23, 46, 0.25) !important;
    border-radius: 4px !important;
}
body.dark-mode .sb-nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2) !important;
}

/* ─── HIDE INLINE SUBMENU & ARROW (HOVER FLYOUT ONLY) ────── */
.sb-submenu { display: none !important; }
.sb-hdr-arrow { display: none !important; }

/* ─── FLOATING FLYOUT HOVER TOOLTIP ───────────────────────── */
.sb-group { position: relative; }
.sb-tooltip-popup {
    display: none;
    position: fixed;
    background-color: var(--sidebar-bg) !important;
    background-image: linear-gradient(rgba(var(--sidebar-bg-rgb), 0.88), rgba(var(--sidebar-bg-rgb), 0.88)), url('/images/leather_texture.png') !important;
    background-repeat: repeat !important;
    background-size: 180px 180px !important;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.45);
    min-width: 220px;
    max-width: 290px;
    max-height: calc(100vh - 20px);
    overflow-y: auto;
    scrollbar-width: thin;
    z-index: 99999;
    padding: 8px;
    pointer-events: auto;
    backdrop-filter: blur(8px);
}
.sb-tooltip-popup::before {
    content: '';
    position: absolute;
    left: -6px; top: var(--arrow-top, 16px);
    border-width: 6px 6px 6px 0;
    border-style: solid;
    border-color: transparent var(--sidebar-bg) transparent transparent;
    transition: top 0.1s ease;
}
.sb-tooltip-popup::after {
    content: '';
    position: absolute;
    top: 4px;
    bottom: 4px;
    left: 4px;
    width: 0;
    border-left: 1px dashed rgba(255, 255, 255, 0.15) !important;
    pointer-events: none;
    z-index: 10;
}
.sb-tooltip-title {
    padding: 6px 10px 8px;
    font-size: 11px; font-weight: 800;
    color: rgba(255, 255, 255, 0.5);
    text-transform: uppercase; letter-spacing: 0.6px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    margin-bottom: 6px;
}
.sb-tooltip-popup a {
    display: flex; align-items: center; justify-content: space-between; gap: 8px;
    padding: 9px 12px;
    border-radius: 8px;
    color: rgba(255, 255, 255, 0.88);
    font-size: 13px; font-weight: 600;
    text-decoration: none; transition: all 0.18s ease;
    margin-bottom: 4px;
    background: rgba(255, 255, 255, 0.04);
    position: relative;
    z-index: 2;
    cursor: pointer !important;
}
.sb-tooltip-popup a:last-child { margin-bottom: 0; }
.sb-tooltip-popup a:hover {
    background: rgba(255, 255, 255, 0.16);
    color: #fff;
    transform: translateX(3px);
}
.sb-tooltip-popup a.active-link {
    color: #ff9800 !important;
    background: rgba(255, 152, 0, 0.14) !important;
    font-weight: 700 !important;
}
.sb-tooltip-popup a.active-link::before {
    content: '';
    position: absolute;
    left: 4px;
    top: 4px;
    bottom: 4px;
    width: 0;
    border-left: 1.5px dashed #ff9800 !important;
    pointer-events: none;
}
.sb-pop-badge {
    background: #10b981;
    color: #fff;
    font-size: 9px; font-weight: 800;
    padding: 2px 6px; border-radius: 4px;
    margin-left: auto; margin-right: 6px;
    letter-spacing: 0.3px;
}
.sb-pop-icon {
    font-size: 11px; color: #ff9800; opacity: 0.9;
}

/* ─── MAIN ─────────────────────────────────────────────────── */
.main{
    margin-left:240px;
    flex:1;
    display:flex;
    flex-direction:column;
    min-height:100vh;
    min-width:0;
    transition: margin-left .3s ease;
}

/* ─── TOPBAR ───────────────────────────────────────────────── */
.topbar{
    background:#fff;border-bottom:1px solid var(--border);
    height:62px;padding:0 22px;
    display:flex;align-items:center;justify-content:space-between;
    position:sticky;top:0;z-index:100;
    box-shadow:0 1px 3px rgba(0,0,0,.05);gap:12px;
}
.topbar-left{display:flex;align-items:center;gap:13px;min-width:0;flex:1;}
.hamburger{background:none;border:none;color:var(--t2);font-size:18px;cursor:pointer;padding:6px;display:flex;border-radius:6px;}
.hamburger:hover{background:var(--page);}
.page-heading{font-family:'Plus Jakarta Sans',sans-serif;font-size:15px;font-weight:700;color:var(--t1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;min-width:0;}
.topbar-right{display:flex;align-items:center;gap:10px;flex-shrink:0;}
.topbar-school-name{font-size:12px;color:var(--t2);font-weight:600;display:flex;align-items:center;gap:4px;white-space:nowrap;max-width:180px;overflow:hidden;text-overflow:ellipsis;}

.notif-wrap{position:relative;}
.notif-btn{
    background:var(--page);border:1px solid var(--border);
    border-radius:8px;width:37px;height:37px;
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;color:var(--t2);font-size:15px;transition:.2s;position:relative;
}
.notif-btn:hover{border-color:var(--gold);color:var(--gold);}

.logout-btn {
    color: #ef4444 !important;
    border-color: rgba(239, 68, 68, 0.2) !important;
    background: rgba(239, 68, 68, 0.05) !important;
}
.logout-btn:hover {
    border-color: #ef4444 !important;
    color: #fff !important;
    background: #ef4444 !important;
}
body.dark-mode .logout-btn {
    background: rgba(239, 68, 68, 0.1) !important;
    border-color: rgba(239, 68, 68, 0.3) !important;
}
body.dark-mode .logout-btn:hover {
    border-color: #f87171 !important;
    color: #fff !important;
    background: #ef4444 !important;
}

.sb-profile-btn {
    background: transparent;
    border: 1px solid var(--sidebar-stitch);
    border-radius: 10px;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--sidebar-text);
    transition: all .2s;
}
.sb-profile-btn:hover {
    background: rgba(255,255,255,0.08) !important;
    border-color: rgba(255,255,255,0.15);
}
body:not(.dark-mode) .sb-profile-btn:hover {
    background: rgba(0,0,0,0.05) !important;
    border-color: rgba(0,0,0,0.1);
}
.sb-drawer-btn {
    background: transparent;
    border: 1px solid var(--sidebar-stitch);
    border-radius: 8px;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--sidebar-text);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.sb-drawer-btn:hover {
    background: rgba(255,255,255,0.08) !important;
    border-color: rgba(255,255,255,0.15);
    transform: scale(1.08) rotate(90deg);
    color: var(--gold);
}
body:not(.dark-mode) .sb-drawer-btn {
    border-color: rgba(0,0,0,0.08);
    color: #4b5563;
}
body:not(.dark-mode) .sb-drawer-btn:hover {
    background: rgba(0,0,0,0.04) !important;
    border-color: rgba(0,0,0,0.12);
    color: #7c3aed;
}

.sb-profile-menu {
    display: none;
    position: absolute;
    bottom: calc(100% + 8px);
    left: 50%;
    transform: translateX(-50%);
    width: 195px;
    background: var(--page);
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: var(--shadow-lg);
    z-index: 1000;
    padding: 6px 0;
    overflow: hidden;
}
.sb-profile-menu.open {
    display: block;
}
.sb-menu-parent-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    font-size: 13px;
    color: var(--t1);
    font-weight: 600;
    cursor: pointer;
    user-select: none;
    transition: background 0.15s ease;
}
.sb-menu-parent-left {
    display: flex;
    align-items: center;
    gap: 10px;
}
.sb-menu-parent-left i {
    font-size: 13px;
    width: 16px;
    text-align: center;
}
.sb-menu-parent-item:hover,
.sb-menu-item-has-submenu.open .sb-menu-parent-item {
    background: var(--border);
}
.sb-submenu-arrow {
    font-size: 11px;
    color: var(--t2);
    transition: transform 0.2s ease, color 0.2s ease;
}
.sb-menu-item-has-submenu:hover .sb-submenu-arrow,
.sb-menu-item-has-submenu.open .sb-submenu-arrow {
    transform: rotate(90deg);
    color: var(--purple);
}
.sb-profile-submenu {
    display: none;
    flex-direction: column;
    background: rgba(0, 0, 0, 0.03);
    border-left: 2px solid var(--purple);
    margin: 2px 8px 6px 14px;
    border-radius: 0 6px 6px 0;
    padding: 3px 0;
}
.sb-menu-item-has-submenu:hover .sb-profile-submenu,
.sb-menu-item-has-submenu.open .sb-profile-submenu {
    display: flex;
}
.sb-submenu-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    font-size: 12.5px;
    color: var(--t1);
    text-decoration: none;
    font-weight: 500;
    transition: background 0.15s ease, color 0.15s ease;
    border-radius: 4px;
}
.sb-submenu-link i {
    font-size: 12px;
    width: 16px;
    text-align: center;
}
.sb-submenu-link:hover {
    background: rgba(124, 58, 237, 0.08);
    color: var(--purple);
}
.sb-submenu-link.active {
    background: rgba(124, 58, 237, 0.12);
    color: var(--purple);
    font-weight: 700;
}
.sb-logout-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    font-size: 13px;
    color: #ef4444 !important;
    text-decoration: none;
    font-weight: 600;
    transition: background 0.15s ease;
}
.sb-logout-link i {
    font-size: 13px;
    width: 16px;
    text-align: center;
}
.sb-logout-link:hover {
    background: rgba(239, 68, 68, 0.08);
}
body.dark-mode .sb-profile-menu {
    background: #1f2937;
    border-color: #374151;
}
body.dark-mode .sb-profile-submenu {
    background: rgba(255, 255, 255, 0.05);
}
body.dark-mode .sb-menu-parent-item {
    color: #f8fafc;
}
body.dark-mode .sb-menu-parent-item:hover,
body.dark-mode .sb-menu-item-has-submenu.open .sb-menu-parent-item {
    background: #374151;
}
body.dark-mode .sb-submenu-link {
    color: #e2e8f0;
}
body.dark-mode .sb-submenu-link:hover {
    background: rgba(167, 139, 250, 0.15);
    color: #c084fc;
}
body.dark-mode .sb-submenu-link.active {
    background: rgba(167, 139, 250, 0.22);
    color: #c084fc;
}

/* ─── PAGE CONTENT ─────────────────────────────────────────── */
.pg{padding:22px 24px 60px 24px;flex:1;min-width:0;}

/* ─── PAGE HEADER BAR ──────────────────────────────────────── */
.page-hdr{
    display:flex;align-items:center;justify-content:space-between;
    margin-bottom:20px;
}
.page-hdr-left h1{
    font-family:'Plus Jakarta Sans',sans-serif;
    font-size:19px;font-weight:800;color:var(--t1);
}
.page-hdr-left p{font-size:12px;color:var(--t2);margin-top:2px;}
.page-hdr-right{display:flex;gap:9px;align-items:center;}

/* ─── CARD ─────────────────────────────────────────────────── */
.card{
    background:var(--white);border:1px solid var(--border);
    border-radius:13px;box-shadow:var(--shadow);overflow:hidden;
    margin-bottom:18px;
}
.card-hdr{
    padding:16px 20px;
    display:flex;align-items:center;justify-content:space-between;
    border-bottom:1px solid var(--border);
}
.card-hdr h3{font-size:14px;font-weight:700;color:var(--t1);}
.card-body{padding:20px;}

/* ─── BUTTONS ──────────────────────────────────────────────── */
.btn{
    display:inline-flex;align-items:center;gap:6px;
    padding:8px 16px;border-radius:8px;font-size:12.5px;font-weight:600;
    cursor:pointer;text-decoration:none;border:none;transition:.2s;
}
.btn-primary{background:var(--navy);color:#fff;}
.btn-primary:hover{background:var(--navy2);color:#fff;}
.btn-gold{background:var(--gold);color:var(--navy);}
.btn-gold:hover{background:#d97706;color:var(--navy);}
.btn-success{background:var(--green);color:#fff;}
.btn-success:hover{background:#059669;color:#fff;}
.btn-danger{background:var(--red);color:#fff;}
.btn-danger:hover{background:#dc2626;color:#fff;}
.btn-outline{background:transparent;color:var(--t1);border:1px solid var(--border);}
.btn-outline:hover{background:var(--page);border-color:var(--t3);}
.btn-accent{background:var(--blue);color:#fff;}
.btn-accent:hover{background:#2563eb;color:#fff;}

/* ─── FORM ELEMENTS ────────────────────────────────────────── */
.form-group{margin-bottom:16px;}
.form-label{display:block;font-size:12px;font-weight:600;color:var(--t2);margin-bottom:5px;text-transform:uppercase;letter-spacing:.4px;}
.form-control{
    width:100%;background:#fff;border:1px solid var(--border);
    border-radius:8px;padding:9px 13px;font-size:13px;color:var(--t1);
    outline:none;transition:.2s;font-family:'Inter',sans-serif;
}
.form-control:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(245,158,11,.1);}
.form-control option{background:#fff;color:var(--t1);}

/* ─── TABLE ────────────────────────────────────────────────── */
.table-wrap{overflow-x:auto;}
table.tbl{width:100%;border-collapse:collapse;}
table.tbl th{
    padding:11px 14px;text-align:left;
    font-size:11px;font-weight:700;color:var(--t2);
    text-transform:uppercase;letter-spacing:.5px;
    border-bottom:2px solid var(--border);background:var(--page);
}
table.tbl td{
    padding:11px 14px;font-size:13px;color:var(--t1);
    border-bottom:1px solid var(--border);vertical-align:middle;
}
table.tbl tr:hover td{background:rgba(245,158,11,.03);}
table.tbl tr:last-child td{border-bottom:none;}

/* ─── BADGES ───────────────────────────────────────────────── */
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;}
.badge-success{background:rgba(16,185,129,.12);color:var(--green);}
.badge-danger{background:rgba(239,68,68,.12);color:var(--red);}
.badge-warning{background:rgba(245,158,11,.12);color:var(--gold);}
.badge-blue{background:rgba(59,130,246,.12);color:var(--blue);}
.badge-purple{background:rgba(139,92,246,.12);color:var(--purple);}

/* ─── ALERTS ───────────────────────────────────────────────── */
.alert{padding:12px 16px;border-radius:9px;font-size:13px;margin-bottom:16px;display:flex;align-items:flex-start;gap:9px;}
.alert-success{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.2);color:#065f46;}
.alert-danger{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#991b1b;}
.alert-warning{background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);color:#92400e;}
.alert i{margin-top:1px;flex-shrink:0;}

/* ─── GRID UTILS ───────────────────────────────────────────── */
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}
.grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;}
@media(max-width:900px){.grid-3,.grid-4{grid-template-columns:1fr 1fr;}.grid-2{grid-template-columns:1fr;}}
@media(max-width:600px){.grid-2,.grid-3,.grid-4{grid-template-columns:1fr;}}

/* ─── TOAST ────────────────────────────────────────────────── */
#appToast{
    position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);
    background:var(--navy);color:#fff;font-size:12.5px;font-weight:600;
    padding:11px 22px;border-radius:10px;box-shadow:0 8px 28px rgba(0,0,0,.25);
    z-index:9999;opacity:0;transition:all .3s ease;pointer-events:none;
    border-left:3px solid var(--gold);white-space:nowrap;
}
#appToast.show{opacity:1;transform:translateX(-50%) translateY(0);}

/* ─── RESPONSIVE & MOBILE NAVIGATION DRAWER ─────────────────── */
.sidebar-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    width: 100vw; height: 100vh; height: 100dvh;
    background: rgba(15, 23, 42, 0.62);
    -webkit-backdrop-filter: blur(6px);
    backdrop-filter: blur(6px);
    z-index: 1004;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity 0.32s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.32s ease;
    cursor: pointer;
}
.sidebar-overlay.active {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

@media(min-width:769px) and (max-width:1024px){
    .sidebar{width:54px;}
    .sb-logo-text,.sb-school,.sb-hdr-title,.sb-hdr-arrow,.sb-submenu,.sb-bottom{display:none!important;}
    .sb-hdr{justify-content:center;padding:10px 0;margin:0;cursor:pointer;}
    .sb-hdr-icon{width:22px;height:22px;font-size:9.5px;}
    .main{margin-left:54px !important;}
    .hamburger{display:flex;}
}

.sb-mobile-user-footer {
    display: none !important;
}
.mobile-welcome-card {
    display: none !important;
}

@media(max-width:768px){
    /* Full-screen Mobile Application Grid Drawer */
    .sidebar {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        max-width: 100vw !important;
        height: 100vh !important;
        height: 100dvh !important;
        z-index: 1005 !important;
        transform: translateX(-100%) !important;
        box-shadow: none !important;
        transition: transform 0.36s cubic-bezier(0.16, 1, 0.3, 1) !important;
        display: flex !important;
        flex-direction: column !important;
        background: linear-gradient(180deg, #f0f4fd 0%, #f8fafc 100%) !important;
        will-change: transform;
    }
    body.dark-mode .sidebar {
        background: #0f172a !important;
    }
    .sidebar.open {
        transform: translateX(0) !important;
    }

    /* Mobile Flagship Header Bar (PREMIUM BLUE & WHITE GLASS THEME) */
    .sb-school-header {
        padding: 18px 18px 16px 18px !important;
        position: relative !important;
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 55%, #3b82f6 100%) !important;
        color: #ffffff !important;
        border-bottom-left-radius: 26px !important;
        border-bottom-right-radius: 26px !important;
        box-shadow: 0 12px 32px rgba(37,99,235,0.3), inset 0 1px 0 rgba(255,255,255,0.25) !important;
        flex-shrink: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 14px !important;
    }
    body.dark-mode .sb-school-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%) !important;
        box-shadow: 0 12px 32px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.1) !important;
    }

    .sb-school-header-top {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        width: 100% !important;
        min-width: 0 !important;
        gap: 10px !important;
    }
    .sb-school-logo {
        width: 44px !important;
        height: 44px !important;
        border-radius: 14px !important;
        background: rgba(255, 255, 255, 0.22) !important;
        border: 1.5px solid rgba(255, 255, 255, 0.45) !important;
        box-shadow: 0 6px 16px rgba(0,0,0,0.15), inset 0 1px 0 rgba(255,255,255,0.3) !important;
        backdrop-filter: blur(10px) !important;
        -webkit-backdrop-filter: blur(10px) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        overflow: hidden !important;
    }
    .sb-school-logo img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }
    .sb-school-logo i { color: #ffffff !important; font-size: 20px !important; }
    
    .sb-school-name-wrapper {
        flex: 1 !important;
        margin: 0 !important;
        min-width: 0 !important;
        overflow: hidden !important;
    }
    .sb-school-name {
        font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif !important;
        font-size: 15px !important;
        font-weight: 800 !important;
        line-height: 1.25 !important;
        letter-spacing: -0.3px !important;
        color: #ffffff !important;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        width: 100% !important;
    }
    .sb-school-sub,
    .sb-logo-text span {
        display: block !important;
    }

    .sb-header-actions {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        flex-shrink: 0 !important;
    }
    .sb-notif-btn {
        width: 38px !important;
        height: 38px !important;
        border-radius: 50% !important;
        background: rgba(255, 255, 255, 0.22) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.38) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        font-size: 14px !important;
        position: relative !important;
        transition: all 0.2s ease !important;
    }
    .sb-notif-btn:active {
        transform: scale(0.92) !important;
        background: rgba(255, 255, 255, 0.38) !important;
    }
    .sb-notif-badge {
        position: absolute !important;
        top: -3px !important;
        right: -3px !important;
        background: #ef4444 !important;
        color: #ffffff !important;
        font-size: 9px !important;
        font-weight: 800 !important;
        width: 17px !important;
        height: 17px !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border: 2px solid #2563eb !important;
        box-shadow: 0 2px 4px rgba(239,68,68,0.4) !important;
    }

    /* Close Button on Mobile Drawer */
    .sb-school-header .sb-close-btn,
    .sb-close-btn {
        display: inline-flex !important;
        width: 38px !important;
        height: 38px !important;
        border-radius: 50% !important;
        background: rgba(255, 255, 255, 0.22) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.38) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        font-size: 14px !important;
        position: relative !important;
        top: auto !important;
        right: auto !important;
        margin: 0 !important;
        transition: all 0.2s ease !important;
    }
    .sb-school-header .sb-close-btn:active,
    .sb-close-btn:active {
        transform: scale(0.92) !important;
        background: rgba(255, 255, 255, 0.38) !important;
    }

    .sb-school-meta-row {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        width: 100% !important;
        gap: 8px !important;
    }
    .sb-school-session {
        font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif !important;
        font-size: 11.5px !important;
        font-weight: 700 !important;
        background: rgba(255, 255, 255, 0.22) !important;
        color: #ffffff !important;
        padding: 5px 14px !important;
        border-radius: 20px !important;
        border: 1px solid rgba(255, 255, 255, 0.4) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25) !important;
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
    }
    .sb-school-session span,
    .sb-school-session i {
        color: #ffffff !important;
        font-size: 11px !important;
        opacity: 1 !important;
    }
    
    .sb-plan-badge {
        font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif !important;
        font-size: 11.5px !important;
        font-weight: 800 !important;
        padding: 5px 14px !important;
        border-radius: 20px !important;
        background: #ffffff !important;
        color: #1d4ed8 !important;
        border: 1px solid rgba(255, 255, 255, 0.9) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12) !important;
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        letter-spacing: 0.2px !important;
    }
    .sb-plan-badge span {
        color: #1d4ed8 !important;
        font-weight: 800 !important;
    }
    .sb-status-dot {
        width: 8px !important;
        height: 8px !important;
        border-radius: 50% !important;
        background: #10b981 !important;
        box-shadow: 0 0 8px #10b981, 0 0 2px #059669 !important;
    }

    /* Mobile Welcome Card Banner */
    .mobile-welcome-card {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        margin: 14px 14px 4px 14px !important;
        padding: 16px 18px !important;
        background: #ffffff !important;
        border-radius: 20px !important;
        border: 1px solid rgba(37,99,235,0.08) !important;
        box-shadow: 0 8px 24px rgba(37,99,235,0.05), 0 2px 6px rgba(0,0,0,0.02) !important;
        gap: 12px !important;
        flex-shrink: 0 !important;
    }
    body.dark-mode .mobile-welcome-card {
        background: #1e293b !important;
        border-color: rgba(255,255,255,0.08) !important;
        box-shadow: 0 8px 24px rgba(0,0,0,0.3) !important;
    }
    .mwc-content {
        flex: 1 !important;
        min-width: 0 !important;
    }
    .mwc-title {
        font-size: 15px !important;
        font-weight: 800 !important;
        color: #1e293b !important;
        margin: 0 !important;
        line-height: 1.3 !important;
        letter-spacing: -0.2px !important;
    }
    body.dark-mode .mwc-title { color: #f8fafc !important; }
    .mwc-subtitle {
        font-size: 11.5px !important;
        font-weight: 500 !important;
        color: #64748b !important;
        margin: 4px 0 0 0 !important;
        line-height: 1.35 !important;
    }
    body.dark-mode .mwc-subtitle { color: #94a3b8 !important; }
    .mwc-illustration {
        width: 86px !important;
        height: 64px !important;
        flex-shrink: 0 !important;
    }
    .mwc-illustration svg {
        width: 100% !important;
        height: 100% !important;
    }

    /* Hide Search Bar on Mobile Drawer */
    .sb-search-wrapper {
        display: none !important;
    }

    /* Scrollable Application Grid Nav Container (3 items per row) */
    .sb-nav {
        padding: 10px 12px calc(40px + env(safe-area-inset-bottom, 20px)) 12px !important;
        flex: 1 !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 10px 8px !important;
        align-content: start !important;
    }

    /* App Grid Card Container (.sb-group) */
    .sb-group {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: flex-start !important;
        text-align: center !important;
        margin: 0 !important;
        padding: 10px 6px 10px 6px !important;
        position: relative !important;
        background: #ffffff !important;
        border-radius: 16px !important;
        border: 1px solid rgba(0,0,0,0.04) !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03), 0 1px 2px rgba(0,0,0,0.02) !important;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease, border-color 0.2s ease !important;
        cursor: pointer !important;
    }
    body.dark-mode .sb-group {
        background: #1e293b !important;
        border-color: rgba(255,255,255,0.06) !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25) !important;
    }
    .sb-group:active {
        transform: translateY(-1px) scale(0.96) !important;
        box-shadow: 0 4px 14px rgba(37,99,235,0.12) !important;
    }

    /* App Tile Header (.sb-hdr) */
    .sb-hdr {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        padding: 0 !important;
        margin: 0 !important;
        min-height: unset !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        -webkit-tap-highlight-color: transparent !important;
        width: 100% !important;
        cursor: pointer !important;
    }
    .sb-hdr-left {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 5px !important;
        width: 100% !important;
    }

    /* App Card Squircle Icon Box (.sb-hdr-icon) - Visual Focal Point for 3D SVG Icons */
    .sb-hdr-icon {
        width: 58px !important;
        height: 58px !important;
        min-width: 58px !important;
        min-height: 58px !important;
        border-radius: 16px !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        filter: drop-shadow(0 6px 12px rgba(0,0,0,0.12)) !important;
    }
    .sb-hdr-icon i,
    .sb-hdr-icon svg,
    .sb-hdr-icon .m3d-icon {
        width: 58px !important;
        height: 58px !important;
        line-height: 58px !important;
        display: block !important;
    }
    .sb-group:hover .sb-hdr-icon,
    .sb-group:active .sb-hdr-icon {
        transform: translateY(-2px) scale(1.06) !important;
    }

    /* App Card Title Label (.sb-hdr-title) */
    .sb-hdr-title {
        font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif !important;
        font-size: 11.5px !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        text-align: center !important;
        line-height: 1.25 !important;
        margin-top: 6px !important;
        max-width: 100% !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
        letter-spacing: -0.2px !important;
    }
    body.dark-mode .sb-hdr-title {
        color: #f1f5f9 !important;
    }
    .sb-hdr-arrow { display: none !important; }

    /* Active App Card Animations & Unique Glowing Design */
    @keyframes activeGroupPulse {
        0%, 100% {
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.35), 0 0 0 2px rgba(59, 130, 246, 0.4) !important;
            transform: translateY(-2px) !important;
        }
        50% {
            box-shadow: 0 12px 30px rgba(79, 70, 229, 0.48), 0 0 0 3.5px rgba(99, 102, 241, 0.65) !important;
            transform: translateY(-4px) scale(1.02) !important;
        }
    }
    @keyframes activeIconFloat {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-2px) scale(1.08); }
    }
    @keyframes activeBadgeGlow {
        0%, 100% { opacity: 1; transform: scale(1); box-shadow: 0 0 8px #10b981; }
        50% { opacity: 0.6; transform: scale(0.85); box-shadow: 0 0 4px #10b981; }
    }

    .sb-group.active-group {
        border-color: rgba(37, 99, 235, 0.6) !important;
        background: linear-gradient(180deg, #eff6ff 0%, #ffffff 100%) !important;
        animation: activeGroupPulse 3s infinite ease-in-out !important;
    }
    body.dark-mode .sb-group.active-group {
        background: linear-gradient(180deg, #1e1b4b 0%, #0f172a 100%) !important;
        border-color: rgba(96, 165, 250, 0.6) !important;
    }
    .sb-group.active-group::after {
        content: '';
        position: absolute;
        top: 7px;
        right: 7px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        animation: activeBadgeGlow 2s infinite ease-in-out;
    }
    .sb-group.active-group .sb-hdr-icon {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 50%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 8px 22px rgba(37,99,235,0.45) !important;
    }
    .sb-group.active-group .sb-hdr-icon i,
    .sb-group.active-group .sb-hdr-icon svg {
        color: #ffffff !important;
        animation: activeIconFloat 2.5s infinite ease-in-out !important;
    }
    .sb-group.active-group .sb-hdr-title {
        color: #1d4ed8 !important;
        font-weight: 800 !important;
    }
    body.dark-mode .sb-group.active-group .sb-hdr-title {
        color: #60a5fa !important;
    }

    /* Sub-Menu Override */
    .sb-nav .sb-submenu,
    .sb-nav .sb-submenu.open,
    .sidebar .sb-submenu,
    .sidebar .sb-submenu.open,
    .sb-submenu,
    .sb-submenu.open {
        display: none !important;
        height: 0 !important;
        max-height: 0 !important;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
    }

    /* Flagship Floating Glassmorphic Bottom Navigation Bar (<768px) */
    .sb-bottom { display: none !important; }
    .sb-mobile-user-footer { display: none !important; }

    .sb-muf-item {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 3px !important;
        color: #64748b !important;
        text-decoration: none !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        background: transparent !important;
        border: none !important;
        outline: none !important;
        cursor: pointer !important;
        padding: 4px 10px !important;
        border-radius: 14px !important;
        transition: all 0.2s ease !important;
    }
    body.dark-mode .sb-muf-item { color: #94a3b8 !important; }
    .sb-muf-item.active {
        color: #2563eb !important;
    }
    body.dark-mode .sb-muf-item.active { color: #60a5fa !important; }

    .sb-muf-icon {
        font-size: 18px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 28px !important;
        height: 24px !important;
        transition: transform 0.2s ease !important;
    }
    .sb-muf-item.active .sb-muf-icon {
        transform: translateY(-2px) !important;
    }

    .sb-muf-badge {
        position: absolute !important;
        top: -3px !important;
        right: -6px !important;
        background: #ef4444 !important;
        color: #ffffff !important;
        font-size: 8.5px !important;
        font-weight: 800 !important;
        padding: 1px 4px !important;
        border-radius: 10px !important;
        border: 1.5px solid #ffffff !important;
    }

    .sb-muf-avatar-btn {
        width: 24px !important;
        height: 24px !important;
        border-radius: 50% !important;
        background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
        color: #ffffff !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .main { margin-left: 0 !important; max-width: 100vw !important; overflow-x: hidden !important; }
    .topbar { padding: 0 14px; height: 56px; max-width: 100vw !important; }
    .hamburger { display: flex !important; }
    .page-heading { font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sb-tooltip-popup { display: none !important; }

    /* Hide internal page duplicate topbar when opened in drawer view or on mobile sub-pages */
    .drawer-open .main > .topbar,
    body.in-drawer-view .main > .topbar,
    .is-drawer-page .topbar {
        display: none !important;
    }
}

/* Ensure no horizontal scroll or cropping on small phones (under 360px) */
@media (max-width: 360px) {
    .sb-nav {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 8px 4px !important;
        padding: 8px 6px 90px 6px !important;
    }
    .sb-group {
        padding: 8px 4px !important;
    }
    .sb-hdr-icon {
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        min-height: 38px !important;
        font-size: 16px !important;
    }
    .sb-hdr-icon i, .sb-hdr-icon svg {
        font-size: 16px !important;
    }
    .sb-hdr-title {
        font-size: 10px !important;
    }
    .mobile-welcome-card {
        padding: 12px 14px !important;
        margin: 10px 10px 4px 10px !important;
    }
    .mwc-title {
        font-size: 13.5px !important;
    }
    .mwc-subtitle {
        font-size: 10.5px !important;
    }
}

/* Sub-Module Mobile Popup Modal Styles */
.sb-module-popup-modal {
    position: fixed !important;
    top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important;
    width: 100vw !important; height: 100vh !important; height: 100dvh !important;
    background: rgba(15, 23, 42, 0.65) !important;
    -webkit-backdrop-filter: blur(6px) !important;
    backdrop-filter: blur(6px) !important;
    z-index: 100050 !important;
    display: flex !important;
    align-items: flex-end !important;
    justify-content: center !important;
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
    transition: opacity 0.28s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.28s ease !important;
}
.sb-module-popup-modal.active {
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
}
.sb-mpm-card {
    width: 100% !important;
    max-width: 450px !important;
    background: var(--card) !important;
    border-top-left-radius: 24px !important;
    border-top-right-radius: 24px !important;
    box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.3) !important;
    padding: 20px 18px 28px 18px !important;
    transform: translateY(100%) !important;
    transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1) !important;
    max-height: calc(100vh - 80px) !important;
    max-height: calc(100dvh - 80px) !important;
    margin-bottom: max(10px, env(safe-area-inset-bottom)) !important;
    display: flex !important;
    flex-direction: column !important;
}
.sb-module-popup-modal.active .sb-mpm-card {
    transform: translateY(0) !important;
}
.sb-mpm-header {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding-bottom: 14px !important;
    border-bottom: 1px solid var(--border) !important;
    margin-bottom: 14px !important;
}
.sb-mpm-header-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
}
.sb-mpm-icon {
    width: 42px !important;
    height: 42px !important;
    border-radius: 12px !important;
    background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
    color: #ffffff !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 18px !important;
}
.sb-mpm-title {
    font-size: 16px !important;
    font-weight: 800 !important;
    color: var(--t1) !important;
}
.sb-mpm-close {
    width: 34px !important;
    height: 34px !important;
    border-radius: 50% !important;
    background: rgba(0, 0, 0, 0.06) !important;
    border: 1px solid var(--border) !important;
    color: var(--t1) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    font-size: 14px !important;
}
.sb-mpm-close:active {
    transform: scale(0.92) !important;
}
.sb-mpm-body {
    overflow-y: auto !important;
    -webkit-overflow-scrolling: touch !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
    padding-bottom: 20px !important;
}
.sb-mpm-item {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 12px 14px !important;
    border-radius: 14px !important;
    background: rgba(37, 99, 235, 0.05) !important;
    border: 1px solid rgba(37, 99, 235, 0.12) !important;
    color: var(--t1) !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    text-decoration: none !important;
    transition: all 0.18s ease !important;
}
.sb-mpm-item:active, .sb-mpm-item.active {
    background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
    color: #ffffff !important;
    border-color: transparent !important;
    box-shadow: 0 4px 14px rgba(37,99,235,0.3) !important;
}
/* Dedicated Mobile Notifications Full Sheet Modal (<768px) */
.sb-notif-full-modal {
    position: fixed !important;
    top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important;
    width: 100vw !important; height: 100vh !important; height: 100dvh !important;
    background: rgba(15, 23, 42, 0.55) !important;
    -webkit-backdrop-filter: blur(5px) !important;
    backdrop-filter: blur(5px) !important;
    z-index: 100050 !important;
    display: flex !important;
    align-items: flex-end !important;
    justify-content: center !important;
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
    transition: opacity 0.28s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.28s ease !important;
}
.sb-notif-full-modal.active {
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
}
.sb-nfm-card {
    width: 100% !important;
    max-width: 480px !important;
    max-height: calc(100vh - 80px) !important;
    max-height: calc(100dvh - 80px) !important;
    margin-bottom: max(10px, env(safe-area-inset-bottom)) !important;
    background: #ffffff !important;
    border-top-left-radius: 24px !important;
    border-top-right-radius: 24px !important;
    box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.25) !important;
    display: flex !important;
    flex-direction: column !important;
    transform: translateY(100%) !important;
    transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1) !important;
    overflow: hidden !important;
}
.sb-notif-full-modal.active .sb-nfm-card {
    transform: translateY(0) !important;
}
body.dark-mode .sb-nfm-card {
    background: #1e293b !important;
    color: #f8fafc !important;
}
.sb-nfm-header {
    padding: 16px 18px !important;
    border-bottom: 1px solid rgba(0,0,0,0.06) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    background: #f8fafc !important;
}
body.dark-mode .sb-nfm-header {
    background: #0f172a !important;
    border-bottom-color: rgba(255,255,255,0.06) !important;
}
.sb-nfm-header-left {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
}
.sb-nfm-icon {
    width: 32px !important;
    height: 32px !important;
    border-radius: 10px !important;
    background: #dbeafe !important;
    color: #2563eb !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 14px !important;
}
.sb-nfm-title {
    font-size: 16px !important;
    font-weight: 800 !important;
    color: #0f172a !important;
}
body.dark-mode .sb-nfm-title { color: #f8fafc !important; }
.sb-nfm-badge {
    font-size: 10.5px !important;
    font-weight: 800 !important;
    background: #ef4444 !important;
    color: #ffffff !important;
    padding: 2px 8px !important;
    border-radius: 12px !important;
}
.sb-nfm-markall {
    font-size: 11.5px !important;
    font-weight: 700 !important;
    color: #2563eb !important;
    background: transparent !important;
    border: none !important;
    cursor: pointer !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    padding: 4px 8px !important;
    border-radius: 6px !important;
}
body.dark-mode .sb-nfm-markall { color: #60a5fa !important; }
.sb-nfm-markall:hover { background: rgba(37,99,235,0.08) !important; }
.sb-nfm-close {
    width: 32px !important;
    height: 32px !important;
    border-radius: 50% !important;
    border: none !important;
    background: rgba(0,0,0,0.06) !important;
    color: #64748b !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 14px !important;
    cursor: pointer !important;
}
body.dark-mode .sb-nfm-close {
    background: rgba(255,255,255,0.1) !important;
    color: #cbd5e1 !important;
}
.sb-nfm-body {
    flex: 1 !important;
    overflow-y: auto !important;
    padding: 12px 14px 28px 14px !important;
    -webkit-overflow-scrolling: touch !important;
}

@media(max-width:576px){
    .topbar-school-name .ts-text{display:none;}
    .user-info{display:none!important;}
    .topbar-right{gap:6px;}
    .pg{padding:14px 12px;}
}
.badge-sidebar-pro {
    display: inline-flex;
    align-items: center;
    background: #f59e0b;
    color: #fff;
    font-size: 8px;
    font-weight: 800;
    padding: 1px 4px;
    border-radius: 4px;
    margin-left: 4px;
}
.badge-sidebar-prox {
    display: inline-flex;
    align-items: center;
    background: #475569;
    color: #fff;
    font-size: 8px;
    font-weight: 800;
    padding: 1px 4px;
    border-radius: 4px;
    margin-left: 4px;
}
.badge-sidebar-premium {
    display: inline-flex;
    align-items: center;
    background: #10b981;
    color: #fff;
    font-size: 8px;
    font-weight: 800;
    padding: 1px 4px;
    border-radius: 4px;
    margin-left: 4px;
}
/* Laravel Tailwind Pagination Fix & Beautification */
nav[role="navigation"] {
    display: flex;
    flex-direction: column;
    gap: 12px;
    align-items: center;
    justify-content: center;
    padding: 16px 20px;
    background: var(--white);
}
/* Hide the mobile-only pagination container by element position */
nav[role="navigation"] > div:first-of-type {
    display: none !important;
}
/* Style the desktop pagination container */
nav[role="navigation"] > div:last-child {
    display: flex !important;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    width: 100%;
}
@media (min-width: 640px) {
    nav[role="navigation"] {
        flex-direction: row;
        justify-content: space-between;
    }
    nav[role="navigation"] > div:last-child {
        flex-direction: row !important;
        justify-content: space-between !important;
    }
}
/* Style the "Showing X to Y of Z results" text */
nav[role="navigation"] p {
    font-size: 13px;
    color: var(--t2);
    margin: 0;
}
/* Style the pagination buttons wrapper */
nav[role="navigation"] span.shadow-sm {
    display: inline-flex !important;
    gap: 8px !important;
    border: none !important;
    box-shadow: none !important;
    background: transparent !important;
    overflow: visible !important;
}
/* Style each pagination link/span */
nav[role="navigation"] span.shadow-sm > a,
nav[role="navigation"] span.shadow-sm > span {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 36px !important;
    height: 36px !important;
    min-width: 36px !important;
    padding: 0 !important;
    background-color: var(--white) !important;
    border: 2px solid #2563eb !important;
    border-radius: 10px !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    color: #2563eb !important;
    text-decoration: none !important;
    transition: all 0.2s ease !important;
    box-shadow: none !important;
}
/* Hover effect for active/clickable pages */
nav[role="navigation"] span.shadow-sm a:hover {
    background-color: rgba(37, 99, 235, 0.08) !important;
    border-color: #1d4ed8 !important;
    color: #1d4ed8 !important;
}
/* Style the current/active page container */
nav[role="navigation"] span.shadow-sm span[aria-current="page"] {
    background-color: #2563eb !important;
    border-color: #2563eb !important;
    color: var(--white) !important;
}
/* Reset the inner span inside active/disabled wrappers to not have borders or backgrounds */
nav[role="navigation"] span.shadow-sm span[aria-current="page"] span,
nav[role="navigation"] span.shadow-sm span[aria-disabled="true"] span {
    background: transparent !important;
    border: none !important;
    color: inherit !important;
    padding: 0 !important;
    margin: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 100% !important;
    height: 100% !important;
    box-shadow: none !important;
}
/* Disabled buttons */
nav[role="navigation"] span.shadow-sm span[aria-disabled="true"] {
    color: var(--t3) !important;
    border-color: var(--border) !important;
    background-color: #f3f4f6 !important;
    cursor: not-allowed !important;
    opacity: 0.6 !important;
}
nav[role="navigation"] svg {
    width: 16px !important;
    height: 16px !important;
    display: inline-block !important;
    vertical-align: middle !important;
}

/* Notification dropdown in admin topbar */
.notif-drop {
    position: absolute;
    right: 0;
    top: 45px;
    width: 320px;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
    display: none;
    z-index: 1000;
    overflow: hidden;
}
.notif-drop.open {
    display: block;
}
.nd-hdr {
    padding: 12px 14px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--page);
}
.nd-hdr strong {
    font-size: 12.5px;
    color: var(--t1);
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.nd-mark {
    font-size: 10.5px;
    color: var(--gold);
    font-weight: 600;
    cursor: pointer;
}
.nd-item {
    display: flex;
    align-items: start;
    gap: 10px;
    padding: 12px 14px;
    border-bottom: 1px solid var(--border);
    transition: .15s;
    text-decoration: none;
}
.nd-item:hover {
    background: var(--page);
}
.nd-item:last-child {
    border-bottom: none;
}
.nd-ico {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
    background: rgba(245, 158, 11, 0.12);
    color: var(--gold);
}
.nd-body {
    flex: 1;
    min-width: 0;
}
.nd-title {
    font-size: 11.5px;
    font-weight: 700;
    color: var(--t1);
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.nd-desc {
    font-size: 10.5px;
    color: var(--t2);
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.nd-time {
    font-size: 9.5px;
    color: var(--t3);
    margin-top: 4px;
}
.nd-empty {
    padding: 30px 14px;
    text-align: center;
    color: var(--t3);
    font-size: 11.5px;
}
.notif-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #ef4444;
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--white);
}

/* ══════════════════════════════════════════════════════════════
   COMPREHENSIVE DARK MODE THEME (NO WHITE BACKGROUNDS)
   ══════════════════════════════════════════════════════════════ */
body.dark-mode {
    --page: #0b0f19 !important;
    --white: #111827 !important;
    --card: #111827 !important;
    --t1: #f8fafc !important;
    --t2: #cbd5e1 !important;
    --t3: #94a3b8 !important;
    --border: #1e293b !important;
    background: #0b0f19 !important;
    color: #f8fafc !important;
}
body.dark-mode .main,
body.dark-mode .pg,
body.dark-mode .mis-page,
body.dark-mode .content-wrapper,
body.dark-mode .wrapper,
body.dark-mode div.pg,
body.dark-mode div.main {
    background: #0b0f19 !important;
    color: #f8fafc !important;
}

/* Override any elements with inline white background styles across all school pages */
body.dark-mode [style*="background:#fff"],
body.dark-mode [style*="background: #fff"],
body.dark-mode [style*="background:#ffffff"],
body.dark-mode [style*="background: #ffffff"],
body.dark-mode [style*="background: white"],
body.dark-mode [style*="background-color:#fff"],
body.dark-mode [style*="background-color: #fff"],
body.dark-mode [style*="background-color:#ffffff"],
body.dark-mode [style*="background-color: #ffffff"] {
    background-color: #111827 !important;
    color: #f8fafc !important;
    border-color: #1e293b !important;
}

/* Sidebar in Dark Mode */
body.dark-mode .sidebar {
    background-color: var(--sidebar-bg) !important;
    border-right: 1px solid rgba(255,255,255,0.06) !important;
}

/* Topbar in Dark Mode */
body.dark-mode .topbar {
    background: #111827 !important;
    border-bottom: 1px solid #1e293b !important;
    color: #f8fafc !important;
}
body.dark-mode .page-heading {
    color: #f8fafc !important;
}
body.dark-mode .notif-btn, 
body.dark-mode .theme-toggle-btn {
    background: #1f2937 !important;
    color: #f8fafc !important;
    border: 1px solid #374151 !important;
}
body.dark-mode .notif-btn:hover, 
body.dark-mode .theme-toggle-btn:hover {
    background: #374151 !important;
    color: #818cf8 !important;
}
body.dark-mode .topbar-school-name {
    background: #1f2937 !important;
    color: #cbd5e1 !important;
    border: 1px solid #374151 !important;
}

/* Topbar Search Styles */
.topbar-search-wrap input {
    background: var(--page) !important;
    border: 1px solid var(--border) !important;
    color: var(--t1) !important;
}
.topbar-search-wrap input:focus {
    border-color: var(--gold) !important;
}
.search-result-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    text-decoration: none;
    color: var(--t1) !important;
    border-bottom: 1px solid var(--border);
    transition: background 0.15s;
}
.search-result-item:hover {
    background: rgba(245, 158, 11, 0.08) !important;
}

body.dark-mode .topbar-search-wrap input {
    background: #1f2937 !important;
    border-color: #374151 !important;
    color: #f3f4f6 !important;
}
body.dark-mode .topbar-search-wrap input:focus {
    border-color: #818cf8 !important;
}
body.dark-mode #topbarSearchResults {
    background: #111827 !important;
    border-color: #374151 !important;
}
body.dark-mode .search-result-item {
    color: #cbd5e1 !important;
    border-bottom: 1px solid #374151;
}
body.dark-mode .search-result-item:hover {
    background: rgba(255, 255, 255, 0.05) !important;
}

@media (max-width: 768px) {
    .topbar-search-wrap {
        display: none !important;
    }
}
body.dark-mode .user-btn {
    background: #1f2937 !important;
    border: 1px solid #374151 !important;
    color: #f8fafc !important;
}
body.dark-mode .user-info strong,
body.dark-mode .user-name {
    color: #f8fafc !important;
}
body.dark-mode .user-info span,
body.dark-mode .user-role {
    color: #94a3b8 !important;
}
body.dark-mode .user-drop {
    background: #1f2937 !important;
    border: 1px solid #374151 !important;
    box-shadow: 0 10px 25px rgba(0,0,0,0.5) !important;
}
body.dark-mode .user-drop a {
    color: #cbd5e1 !important;
}
body.dark-mode .user-drop a:hover {
    background: #374151 !important;
    color: #ffffff !important;
}

/* Global Cards, Panels & Module Containers in Dark Mode */
body.dark-mode .card,
body.dark-mode .db-card,
body.dark-mode .panel,
body.dark-mode .box,
body.dark-mode .card-hdr,
body.dark-mode .card-body,
body.dark-mode .card-footer,
body.dark-mode .inst-page,
body.dark-mode .inst-details-panel,
body.dark-mode .inst-panel,
body.dark-mode .inst-action-card,
body.dark-mode .inst-modal-content,
body.dark-mode .sa-card,
body.dark-mode .rc-card,
body.dark-mode .form-card,
body.dark-mode .report-card,
body.dark-mode .profile-card,
body.dark-mode .filter-card {
    background: #111827 !important;
    border-color: #1e293b !important;
    color: #f8fafc !important;
}
body.dark-mode .card,
body.dark-mode .inst-details-panel,
body.dark-mode .inst-panel,
body.dark-mode .inst-action-card {
    box-shadow: 0 4px 20px rgba(0,0,0,0.3) !important;
}
body.dark-mode .card-hdr,
body.dark-mode .card-header-row,
body.dark-mode .card-header,
body.dark-mode .box-header,
body.dark-mode .inst-details-hdr,
body.dark-mode .inst-panel-hdr {
    border-bottom: 1px solid #1e293b !important;
}
body.dark-mode .card-hdr h3,
body.dark-mode .card-header-row h3,
body.dark-mode .sec-title,
body.dark-mode .inst-name-title,
body.dark-mode .inst-panel-title,
body.dark-mode .inst-card-title,
body.dark-mode h1, body.dark-mode h2, body.dark-mode h3, body.dark-mode h4, body.dark-mode h5, body.dark-mode h6 {
    color: #f8fafc !important;
}
body.dark-mode .inst-detail-lbl,
body.dark-mode .inst-detail-colon,
body.dark-mode .inst-asset-lbl,
body.dark-mode .inst-card-sub,
body.dark-mode .inst-form-label {
    color: #cbd5e1 !important;
}
body.dark-mode .inst-detail-val {
    color: #f8fafc !important;
}
body.dark-mode .inst-asset-box {
    background: #1f2937 !important;
    border-color: #374151 !important;
}
body.dark-mode .inst-asset-col {
    border-left-color: #1e293b !important;
}
body.dark-mode .inst-card-icon {
    background: rgba(59, 130, 246, 0.2) !important;
    color: #60a5fa !important;
}
body.dark-mode .inst-btn-social,
body.dark-mode .inst-btn-edit,
body.dark-mode .inst-card-btn {
    background: #1f2937 !important;
    color: #818cf8 !important;
    border-color: #6366f1 !important;
}
body.dark-mode .inst-btn-social:hover,
body.dark-mode .inst-btn-edit:hover,
body.dark-mode .inst-card-btn:hover {
    background: #6366f1 !important;
    color: #ffffff !important;
}

/* Top Summary Cards in Dark Mode */
body.dark-mode .sum-card.hc-blue {
    background: rgba(30, 58, 138, 0.35) !important;
    border: 1px solid rgba(59, 130, 246, 0.4) !important;
}
body.dark-mode .sum-card.ac-teal {
    background: rgba(6, 78, 59, 0.35) !important;
    border: 1px solid rgba(16, 185, 129, 0.4) !important;
}
body.dark-mode .sum-card.fe-purple {
    background: rgba(88, 28, 135, 0.35) !important;
    border: 1px solid rgba(168, 85, 247, 0.4) !important;
}
body.dark-mode .sum-card.at-lavender {
    background: rgba(120, 53, 15, 0.35) !important;
    border: 1px solid rgba(245, 158, 11, 0.4) !important;
}
body.dark-mode .sum-card .card-hdr span.title,
body.dark-mode .sum-card .card-body-content .body-row,
body.dark-mode .sum-card .card-body-content .body-row strong {
    color: #f8fafc !important;
}

/* Subpanels, Tables & Forms in Dark Mode */
body.dark-mode .attrition-box,
body.dark-mode .attendance-subpanel,
body.dark-mode .fee-management-subcard,
body.dark-mode .subcard,
body.dark-mode .table-wrap,
body.dark-mode table.tbl,
body.dark-mode .table,
body.dark-mode .inst-table,
body.dark-mode th, body.dark-mode td,
body.dark-mode .table-responsive,
body.dark-mode .modal-content,
body.dark-mode .modal-header,
body.dark-mode .modal-body,
body.dark-mode .modal-footer,
body.dark-mode .dropdown-menu,
body.dark-mode .form-control,
body.dark-mode .form-select,
body.dark-mode .inst-form-control,
body.dark-mode .input-group-text,
body.dark-mode nav[role="navigation"] {
    background: #1f2937 !important;
    border-color: #374151 !important;
    color: #f8fafc !important;
}

body.dark-mode table.tbl th,
body.dark-mode .table th,
body.dark-mode .inst-table th {
    background: #111827 !important;
    color: #cbd5e1 !important;
    border-bottom: 2px solid #374151 !important;
}
body.dark-mode table.tbl td,
body.dark-mode .table td,
body.dark-mode .inst-table td {
    border-bottom: 1px solid #374151 !important;
    color: #f8fafc !important;
}
body.dark-mode table.tbl tr:hover td,
body.dark-mode .table tr:hover td,
body.dark-mode .inst-table tr:hover td {
    background: rgba(255, 255, 255, 0.04) !important;
    color: #ffffff !important;
}
    background: rgba(255, 255, 255, 0.04) !important;
}

body.dark-mode .attrition-box-title,
body.dark-mode .attendance-subpanel-hdr,
body.dark-mode .attrition-row .row-val {
    color: #f8fafc !important;
}
body.dark-mode .attrition-row,
body.dark-mode .form-label {
    color: #cbd5e1 !important;
}
body.dark-mode .recent-updates-tabs {
    background: #1f2937 !important;
}
body.dark-mode .recent-updates-tabs button {
    color: #94a3b8 !important;
}
body.dark-mode .recent-updates-tabs button.active {
    background: #6366f1 !important;
    color: #ffffff !important;
}
body.dark-mode .admission-checkbox-lbl,
body.dark-mode .admission-bar-label {
    color: #cbd5e1 !important;
}
body.dark-mode .admission-bar-value {
    color: #f8fafc !important;
}
body.dark-mode .admission-bar-chart {
    border-bottom: 1.5px solid #374151 !important;
}
body.dark-mode select, body.dark-mode input, body.dark-mode textarea, body.dark-mode .form-control {
    background-color: #1f2937 !important;
    color: #f8fafc !important;
    border-color: #374151 !important;
}
body.dark-mode option {
    background-color: #1f2937 !important;
    color: #f8fafc !important;
}
body.dark-mode a {
    color: #818cf8;
}
body.dark-mode a:hover {
    color: #a5b4fc;
}
body.dark-mode nav[role="navigation"] span.shadow-sm > a,
body.dark-mode nav[role="navigation"] span.shadow-sm > span {
    background-color: #1f2937 !important;
    border-color: #374151 !important;
    color: #cbd5e1 !important;
}
body.dark-mode nav[role="navigation"] span.shadow-sm span[aria-current="page"] {
    background-color: #6366f1 !important;
    border-color: #6366f1 !important;
    color: #ffffff !important;
}

/* ─── ERP PORTAL FOOTER ────────────────────────────────────── */
.erp-footer {
    background: #ffffff;
    border-top: 1px solid var(--border);
    padding: 10px 24px;
    font-size: 12px;
    color: var(--t2);
    transition: left 0.3s ease, background 0.2s ease, border-top-color 0.2s ease;
    position: fixed;
    bottom: 0;
    left: 240px;
    right: 0;
    z-index: 95;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.04);
}
body.sidebar-closed .erp-footer {
    left: 0 !important;
}
.erp-footer-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.erp-footer-left {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
}
.erp-footer-right {
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}
.erp-footer-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--t2);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.15s ease;
}
.erp-footer-item:hover {
    color: #2563eb;
}
.erp-footer-item i {
    font-size: 13px;
}
.erp-footer-ultraviewer {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 10px;
    border-radius: 6px;
    background: rgba(37, 99, 235, 0.08);
    color: #2563eb !important;
    font-weight: 600;
    transition: all 0.2s ease;
}
.erp-footer-ultraviewer:hover {
    background: rgba(37, 99, 235, 0.16);
    color: #1d4ed8 !important;
    transform: translateY(-1px);
}
.erp-footer-divider {
    color: var(--border);
    font-weight: 300;
}

body.dark-mode .erp-footer {
    background: #111827 !important;
    border-top-color: #1e293b !important;
    color: #94a3b8 !important;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.25);
}
body.dark-mode .erp-footer-item {
    color: #cbd5e1 !important;
}
body.dark-mode .erp-footer-item:hover {
    color: #60a5fa !important;
}
body.dark-mode .erp-footer-ultraviewer {
    background: rgba(96, 165, 250, 0.15);
    color: #93c5fd !important;
}
body.dark-mode .erp-footer-ultraviewer:hover {
    background: rgba(96, 165, 250, 0.25);
    color: #bfdbfe !important;
}
body.dark-mode .erp-footer-divider {
    color: #374151 !important;
}

@media (max-width: 768px) {
    .erp-footer {
        left: 0 !important;
        padding: 10px 16px;
        text-align: center;
    }
    .erp-footer-content {
        flex-direction: column;
        justify-content: center;
        gap: 6px;
    }
    .erp-footer-right {
        justify-content: center;
        gap: 10px;
    }
}

@media print {
    .erp-footer {
        display: none !important;
    }
}

/* ─── BRANCH SWITCHER & BRAND HEADER (Image 2) ────────────── */
.topbar-brand-section {
    display: inline-flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    margin-left: 8px;
    padding: 2px 0;
}
.topbar-school-title {
    font-size: 13.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: var(--t1);
    line-height: 1.25;
    white-space: nowrap;
    max-width: 320px;
    overflow: hidden;
    text-overflow: ellipsis;
}
.topbar-school-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    line-height: 1.2;
    margin-top: 2px;
    flex-wrap: nowrap;
}
.topbar-account-id {
    font-weight: 800;
    color: #10b981;
    letter-spacing: 0.3px;
}
.btn-change-school {
    color: #f59e0b !important;
    font-weight: 700;
    font-size: 11px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    background: transparent;
    border: none;
    padding: 0;
    transition: all 0.15s ease;
}
.btn-change-school:hover {
    color: #fbbf24 !important;
    text-decoration: underline !important;
}
.branch-dropdown-menu {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    min-width: 320px;
    max-width: 380px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.18), 0 4px 12px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
    z-index: 9999;
    overflow: hidden;
    display: none;
    animation: branchDropFade 0.15s ease-out;
}
@keyframes branchDropFade {
    from { opacity: 0; transform: translateY(-6px); }
    to { opacity: 1; transform: translateY(0); }
}
.branch-dropdown-header {
    padding: 12px 16px 8px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    border-bottom: 1px solid #f1f5f9;
}
.branch-dropdown-list {
    max-height: 280px;
    overflow-y: auto;
    padding: 4px 0;
}
.branch-item-btn {
    width: 100%;
    border: none;
    background: transparent;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    text-align: left;
    cursor: pointer;
    transition: all 0.15s ease;
    font-family: inherit;
    color: #1e1b4b;
}
.branch-item-btn:hover {
    background: #f8fafc;
}
.branch-item-btn.active-branch {
    background: #dcfce7 !important;
    color: #15803d !important;
}
.branch-item-logo {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border: 1px solid #e2e8f0;
    flex-shrink: 0;
    overflow: hidden;
}
.branch-item-btn.active-branch .branch-item-logo {
    border-color: #86efac;
}
.branch-item-name {
    font-size: 13px;
    font-weight: 700;
    color: inherit;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.branch-item-code {
    font-size: 10.5px;
    font-weight: 600;
    color: #64748b;
    margin-top: 1px;
}
.branch-item-btn.active-branch .branch-item-code {
    color: #16a34a;
}
body.dark-mode .branch-dropdown-menu {
    background: #111827 !important;
    border-color: #374151 !important;
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.5) !important;
}
body.dark-mode .branch-dropdown-header {
    color: #94a3b8 !important;
    border-bottom-color: #1e293b !important;
}
body.dark-mode .branch-item-btn {
    color: #f1f5f9 !important;
}
body.dark-mode .branch-item-btn:hover {
    background: #1f2937 !important;
}
body.dark-mode .branch-item-btn.active-branch {
    background: rgba(22, 163, 74, 0.22) !important;
    color: #4ade80 !important;
}
body.dark-mode .branch-item-logo {
    background: #1f2937 !important;
    border-color: #374151 !important;
}

/* Hide in-page Academic Year selectors/filters across all ERP pages while preserving topbar header selector */
body .academic-year-box,
body .academic-filter-wrap .session-selector-box,
body .sw-topbar-session,
body .tt-academic-year-wrapper,
body select.select-academic-year,
body div.select-academic-year {
    display: none !important;
}
</style>
@yield('styles')
</head>
<body class="dark-mode">
<script>
    (function() {
        if (localStorage.getItem('school_erp_theme') === 'light') {
            document.body.classList.remove('dark-mode');
        } else {
            document.body.classList.add('dark-mode');
        }
        if (localStorage.getItem('school_erp_sidebar_closed') === 'true') {
            document.body.classList.add('sidebar-closed');
        }
    })();
</script>
@php
    use Carbon\Carbon;
    $authUser     = auth()->user();
    $authInitials = strtoupper(substr($authUser->name,0,1).(str_contains($authUser->name,' ') ? substr($authUser->name,strrpos($authUser->name,' ')+1,1) : ''));
    $authRole     = ucfirst(str_replace('_',' ',$authUser->roles->first()?->name ?? 'User'));
    try {
        $currentSchool  = app()->bound('currentSchool') ? app('currentSchool') : null;
        $currentSession = $currentSchool
            ? \App\Models\AcademicSession::resolveCurrentSessionForUser($authUser, $currentSchool->id)
            : null;
        $planName = $currentSchool ? ucfirst($currentSchool->status ?? 'Basic') : 'Basic';
        $layoutAcademicSessions = $currentSchool
            ? \App\Models\AcademicSession::where('school_id', $currentSchool->id)->orderByDesc('start_date')->get(['id','name','is_current'])
            : collect();
        $layoutCurrentSessionId = $currentSession ? $currentSession->id : ($layoutAcademicSessions->first()?->id ?? null);
    } catch (\Exception $e) {
        $currentSchool = null; $currentSession = null; $planName = 'Basic';
        $layoutAcademicSessions = collect();
        $layoutCurrentSessionId = null;
    }
@endphp

@php
    $canManageSession = $authUser && (
        (method_exists($authUser, 'isSchoolAdmin') && $authUser->isSchoolAdmin()) ||
        $authUser->hasRole('school_admin') || 
        $authUser->hasRole('superadmin') || 
        in_array(strtolower($authUser->role ?? ''), ['school_admin', 'superadmin', 'admin'], true)
    );
    $isSchoolDashboard = request()->routeIs('school.dashboard') || request()->is('school/dashboard');
@endphp
@include('layouts.sidebar')

<!-- ══════════ MAIN ══════════ -->
<div class="main">

    @if(session()->has('is_impersonating'))
    <div style="background: linear-gradient(135deg, #f59e0b, #ea580c); color: #fff; padding: 10px 24px; font-size: 13px; font-weight: 700; display: flex; align-items: center; justify-content: space-between; gap: 12px; z-index: 1002; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
        <div style="display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-user-secret" style="font-size: 15px;"></i>
            <span>You are currently impersonating <strong>{{ $currentSchool?->name ?? 'School' }}</strong></span>
        </div>
        <a href="{{ route('school.exit-impersonate') }}" style="background: rgba(255,255,255,0.2); color: #fff; text-decoration: none; padding: 5px 12px; border-radius: 6px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
            <i class="fas fa-right-from-bracket"></i> Exit Impersonation
        </a>
    </div>
    @endif

    <!-- TOPBAR -->
    <nav class="topbar">
        <div class="topbar-left" style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
            <button type="button" class="hamburger" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </button>

            @if($currentSchool)
                @php
                    $accessibleBranches = method_exists($currentSchool, 'getAccessibleBranches')
                        ? $currentSchool->getAccessibleBranches()
                        : collect([$currentSchool]);
                    $hasMultipleBranches = $accessibleBranches->count() > 1;
                @endphp
                <!-- School Brand Info & Branch Switcher (Image 2) -->
                <div class="topbar-brand-section">
                    <div class="topbar-school-title" title="{{ $currentSchool->name }}">
                        {{ $currentSchool->name }}
                    </div>
                    <div class="topbar-school-meta">
                        <span class="topbar-account-id">
                            ACCOUNT ID:{{ $currentSchool->code ?? 'N/A' }}
                        </span>
                        @if($hasMultipleBranches)
                        <div style="position: relative; display: inline-block;">
                            <button type="button" class="btn-change-school" id="btnChangeSchool" onclick="toggleBranchDropdown(event)" title="Change School Branch">
                                <span>Change School</span>
                                <i class="fas fa-caret-down" style="font-size: 10px;"></i>
                            </button>

                            <!-- Branch Dropdown (Image 2) -->
                            <div class="branch-dropdown-menu" id="branchDropdownMenu">
                                <div class="branch-dropdown-header">Choose a school</div>
                                <div class="branch-dropdown-list">
                                    @foreach($accessibleBranches as $branch)
                                        @php
                                            $isCurrent = ($branch->id === $currentSchool->id);
                                        @endphp
                                        <form method="POST" action="{{ route('school.switch-branch') }}" style="margin: 0;">
                                            @csrf
                                            <input type="hidden" name="school_id" value="{{ $branch->id }}">
                                            <button type="submit" class="branch-item-btn {{ $isCurrent ? 'active-branch' : '' }}">
                                                <div class="branch-item-logo">
                                                    @if($branch->logo_url)
                                                        <img src="{{ $branch->logo_url }}" alt="{{ $branch->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                    @else
                                                        <i class="fas fa-school" style="color: {{ $isCurrent ? '#16a34a' : '#6366f1' }}; font-size: 14px;"></i>
                                                    @endif
                                                </div>
                                                <div style="flex: 1; min-width: 0;">
                                                    <div class="branch-item-name">{{ $branch->name }}</div>
                                                    <div class="branch-item-code">ID: {{ $branch->code }}</div>
                                                </div>
                                                @if($isCurrent)
                                                    <i class="fas fa-check-circle" style="color: #16a34a; font-size: 14px; margin-left: auto;"></i>
                                                @endif
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="page-heading">@yield('page-title', 'Dashboard')</div>
            @endif

            @if($currentSchool)
            <div class="topbar-search-wrap" style="position: relative; margin-left: 12px; display: flex; align-items: center; gap: 8px; flex: 1; max-width: 560px;">
                <div style="position: relative; flex: 1;">
                    <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--t2); font-size: 13px; pointer-events: none;"></i>
                    <input type="text" id="topbarSearchInput" placeholder="Search by name, mobile, adm no..." style="width: 100%; height: 34px; padding: 0 10px 0 28px; border: 1px solid var(--border); border-radius: 8px; font-size: 12.5px; font-weight: 500; background: var(--page); color: var(--t1); outline: none; transition: all 0.2s;" autocomplete="off">
                    <div id="topbarSearchResults" style="display: none; position: absolute; top: 40px; left: 0; background: #fff; border: 1px solid var(--border); border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); max-height: 300px; overflow-y: auto; z-index: 1000; width: 340px;"></div>
                </div>
                @if(isset($layoutAcademicSessions) && $layoutAcademicSessions->isNotEmpty())
                    @if($canManageSession && $isSchoolDashboard)
                    <select id="topbarAcademicYear" onchange="switchAcademicYear(this.value)" style="height: 34px; padding: 0 10px; border: 1px solid var(--border); border-radius: 8px; font-size: 12px; font-weight: 700; background: var(--page); color: var(--t1); outline: none; cursor: pointer; min-width: 130px; white-space: nowrap; flex-shrink: 0; transition: all 0.2s;" title="Switch Academic Year">
                        @foreach($layoutAcademicSessions as $ses)
                            <option value="{{ $ses->id }}" {{ ($layoutCurrentSessionId == $ses->id) ? 'selected' : '' }}>
                                {{ $ses->name }}{{ $ses->is_current ? ' ★' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @elseif($canManageSession)
                    <div class="topbar-session-badge topbar-session-locked" onclick="if(typeof showToast === 'function') { showToast('Academic Year is locked. It can only be changed from Dashboard.', 'info'); }" style="height: 34px; padding: 0 10px; border: 1px solid var(--border); border-radius: 8px; font-size: 12px; font-weight: 700; background: var(--page); color: var(--t1); display: inline-flex; align-items: center; gap: 6px; min-width: 130px; white-space: nowrap; flex-shrink: 0; cursor: not-allowed; user-select: none;" title="Academic Year is locked on this page. Can only be changed from Dashboard.">
                        <span>{{ $currentSession?->name ?? ($layoutAcademicSessions->firstWhere('is_current', true)?->name ?? $layoutAcademicSessions->first()?->name ?? 'Academic Year') }}{{ ($currentSession && $currentSession->is_current) ? ' ★' : '' }}</span>
                        <i class="fas fa-lock" style="color: var(--t2); font-size: 10px; margin-left: auto;" title="Locked: Can only be changed from Dashboard"></i>
                    </div>
                    <input type="hidden" id="topbarAcademicYear" value="{{ $layoutCurrentSessionId ?? '' }}">
                    @else
                    <div class="topbar-session-badge" style="height: 34px; padding: 0 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 12px; font-weight: 700; background: var(--page); color: var(--t1); display: inline-flex; align-items: center; gap: 6px; min-width: 110px; white-space: nowrap; flex-shrink: 0;" title="Default Academic Year (Set by School Admin)">
                        <i class="fas fa-calendar-check" style="color: #10b981; font-size: 12px;"></i>
                        <span>{{ $currentSession?->name ?? ($layoutAcademicSessions->firstWhere('is_current', true)?->name ?? $layoutAcademicSessions->first()?->name ?? 'Academic Year') }}</span>
                    </div>
                    @endif
                @endif
            </div>
            @endif
        </div>
        <div class="topbar-right">
            <div class="theme-toggle-wrap" style="margin-right:4px;">
                <div class="notif-btn" id="themeToggleBtn" onclick="toggleTheme()" title="Toggle Dark/Light Mode" style="cursor:pointer;">
                    <i class="fas fa-moon" id="themeToggleIcon"></i>
                </div>
            </div>
            <div class="notif-wrap">
                <div class="notif-btn" onclick="toggleNotifDrop(event)" title="Notifications" style="cursor:pointer;">
                    <i class="fas fa-bell"></i>
                    @php
                        $appUnreadCount = $navNotifications->count();
                    @endphp
                    <span class="notif-badge" id="notifBadgeCount" style="{{ $appUnreadCount > 0 ? '' : 'display:none;' }}">{{ $appUnreadCount }}</span>
                </div>
                <div class="notif-drop" id="notifDrop">
                    <div class="nd-hdr" style="display:flex; justify-content:space-between; align-items:center;">
                        <strong>Notifications</strong>
                        <span class="nd-mark" onclick="if(typeof markAllNotifsAsRead==='function') markAllNotifsAsRead();" style="cursor:pointer; font-size:11px; color:#2563eb; font-weight:600; padding:2px 6px; border-radius:4px; transition:background 0.2s;" onmouseover="this.style.background='rgba(37,99,235,0.08)'" onmouseout="this.style.background='transparent'"><i class="fas fa-check-double" style="margin-right:3px;"></i>Mark All as Read</span>
                    </div>
                    <div id="notifListContainer" style="max-height: 250px; overflow-y: auto;">
                        @forelse($navNotifications as $notif)
                        @if($notif->type === 'Delete Request')
                        <div class="nd-item" style="cursor: default; background: #fffefe; border-left: 3px solid #ef4444;">
                            <div class="nd-ico" style="background: {{ $notif->color }}20; color: {{ $notif->color }};">
                                <i class="fas {{ $notif->icon }}"></i>
                            </div>
                            <div class="nd-body" style="width: 100%;">
                                <div class="nd-title" style="font-weight: 700;">{{ $notif->title }} <span style="font-size: 8.5px; opacity: 0.75; font-weight: 500;">(Approval Required)</span></div>
                                <div class="nd-desc" style="margin-top: 2px; color: #475569;">{{ $notif->content }}</div>
                                <div style="display: flex; gap: 6px; margin-top: 8px;">
                                    <form method="POST" action="{{ route('school.fees.basics') }}" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="action" value="approve_deletion">
                                        <input type="hidden" name="deletion_id" value="{{ $notif->id }}">
                                        <input type="hidden" name="status" value="approve">
                                        <button type="submit" style="background: #16a34a; color: #fff; border: none; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; cursor: pointer; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1">Approve (Yes)</button>
                                    </form>
                                    <form method="POST" action="{{ route('school.fees.basics') }}" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="action" value="approve_deletion">
                                        <input type="hidden" name="deletion_id" value="{{ $notif->id }}">
                                        <input type="hidden" name="status" value="reject">
                                        <button type="submit" style="background: #ef4444; color: #fff; border: none; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; cursor: pointer; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1">Reject (No)</button>
                                    </form>
                                </div>
                                <div class="nd-time" style="margin-top: 4px;">{{ $notif->time }}</div>
                            </div>
                        </div>
                        @else
                        <a href="{{ $notif->action_url && $notif->action_url !== '#' ? $notif->action_url : 'javascript:void(0);' }}" class="nd-item" onclick="if(typeof markNotificationRead==='function') markNotificationRead({{ $notif->id }});">
                            <div class="nd-ico" style="background: {{ $notif->color }}20; color: {{ $notif->color }};">
                                <i class="fas {{ $notif->icon }}"></i>
                            </div>
                            <div class="nd-body">
                                <div class="nd-title" style="font-weight: 700;">{{ $notif->title }} <span style="font-size: 8.5px; opacity: 0.75; font-weight: 500;">({{ $notif->type }})</span></div>
                                <div class="nd-desc" style="margin-top: 2px;">{{ $notif->content }}</div>
                                <div class="nd-time">{{ $notif->time }}</div>
                            </div>
                        </a>
                        @endif
                        @empty
                        <div class="nd-empty">No new notifications today</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="notif-wrap" style="margin-left: 4px;">
                <a href="{{ route('logout') }}" class="notif-btn logout-btn" title="One-Click Logout" style="text-decoration: none;">
                    <i class="fas fa-power-off"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- PAGE CONTENT -->
    <div class="pg">
        @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger"><i class="fas fa-circle-xmark"></i> {{ session('error') }}</div>
        @endif
        @yield('content')
    </div>

    <!-- ERP PORTAL FOOTER -->
    <footer class="erp-footer">
        <div class="erp-footer-content">
            <div class="erp-footer-left">
                <span>&copy; {{ date('Y') }} Educorerp. All rights reserved.</span>
            </div>
            <div class="erp-footer-right">
                <a href="mailto:info@educorerp.com" class="erp-footer-item" title="Support Email">
                    <i class="fas fa-envelope text-primary"></i>
                    <span>info@educorerp.com</span>
                </a>
                <span class="erp-footer-divider">|</span>
                <a href="tel:+919219441716" class="erp-footer-item" title="Helpline Number">
                    <i class="fas fa-phone-alt fa-flip-horizontal text-primary"></i>
                    <span>+91 92194 41716</span>
                </a>
                <span class="erp-footer-divider">|</span>
                <a href="https://www.ultraviewer.net/en/download.html" target="_blank" rel="noopener noreferrer" class="erp-footer-item erp-footer-ultraviewer" title="Download UltraViewer Remote Support">
                    <i class="fas fa-desktop text-primary"></i>
                    <span>UltraViewer</span>
                </a>
            </div>
        </div>
    </footer>
</div>

<!-- TOAST -->
<div id="appToast"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});

function showToast(msg){
    const t=document.getElementById('appToast');
    t.textContent=msg;t.classList.add('show');
    setTimeout(()=>t.classList.remove('show'),3000);
}

function toggleSidebar(e) {
    if (e && e.preventDefault) { e.preventDefault(); e.stopPropagation(); }
    const sidebar = document.getElementById('appSidebar') || document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar) return;
    
    if (window.innerWidth > 768) {
        document.body.classList.toggle('sidebar-closed');
        const isClosed = document.body.classList.contains('sidebar-closed');
        localStorage.setItem('school_erp_sidebar_closed', isClosed ? 'true' : 'false');
    } else {
        const isOpen = sidebar.classList.toggle('open');
        if (overlay) overlay.classList.toggle('active', isOpen);
        document.body.style.overflow = (isOpen && window.innerWidth <= 768) ? 'hidden' : '';
        if (isOpen) {
            const sbNav = sidebar.querySelector('.sb-nav');
            if (sbNav) sbNav.scrollTop = 0;
        }
    }
}

function closeSidebar(e) {
    if (e && e.preventDefault) { e.preventDefault(); e.stopPropagation(); }
    const sidebar = document.getElementById('appSidebar') || document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('active');
    document.body.style.overflow = '';
}

function renumberSidebarModules() {
    const navs = document.querySelectorAll('.sb-nav');
    navs.forEach(nav => {
        const groups = nav.querySelectorAll(':scope > .sb-group');
        groups.forEach(group => {
            const style = window.getComputedStyle(group);
            if (style.display !== 'none' && style.visibility !== 'hidden') {
                const titleEl = group.querySelector('.sb-hdr-title');
                if (titleEl) {
                    let cleanText = titleEl.textContent.replace(/^\d+[.)] */, '').trim();
                    titleEl.textContent = cleanText;
                }
            }
        });
    });
}

function isCompactSidebar() {
    return window.innerWidth > 768 && window.innerWidth <= 1024;
}

let activeTip = null;
let hideTimer = null;

function buildSidebarTooltips() {
    document.querySelectorAll('.sb-tooltip-popup').forEach(t => t.remove());

    document.querySelectorAll('.sb-group').forEach(group => {
        const hdr = group.querySelector('.sb-hdr');
        const submenu = group.querySelector('.sb-submenu');
        const titleEl = group.querySelector('.sb-hdr-title');
        if (!hdr) return;

        const tip = document.createElement('div');
        tip.className = 'sb-tooltip-popup';

        if (titleEl) {
            const titleDiv = document.createElement('div');
            titleDiv.className = 'sb-tooltip-title';
            titleDiv.textContent = titleEl.textContent.replace(/^\d+[.)] */, '').trim();
            tip.appendChild(titleDiv);
        }

        if (submenu) {
            submenu.querySelectorAll('li a').forEach(link => {
                const a = document.createElement('a');
                a.href = link.href;
                if (link.closest('li') && link.closest('li').classList.contains('active')) {
                    a.className = 'active-link';
                }
                const label = link.querySelector('.sb-submenu-label');
                const labelText = label ? label.textContent.trim() : link.textContent.trim();
                
                const span = document.createElement('span');
                span.textContent = labelText;
                a.appendChild(span);



                const extIcon = document.createElement('i');
                extIcon.className = 'fas fa-arrow-up-right-from-square sb-pop-icon';
                a.appendChild(extIcon);

                tip.appendChild(a);
            });
        }

        document.body.appendChild(tip);
        hdr._sbTip = tip;

        function openTip() {
            if (window.innerWidth <= 768) return;
            clearTimeout(hideTimer);
            if (activeTip && activeTip !== tip) {
                activeTip.style.display = 'none';
            }
            activeTip = tip;
            const rect = hdr.getBoundingClientRect();
            const sidebar = document.getElementById('appSidebar') || document.getElementById('sidebar');
            const sidebarWidth = sidebar ? sidebar.getBoundingClientRect().width : 230;
            
            tip.style.left = (sidebarWidth - 2) + 'px'; // Zero gap, smooth overlap
            tip.style.top = rect.top + 'px';
            tip.style.display = 'block';

            // Check viewport bottom collision and adjust positioning if hiding behind screen
            const tipRect = tip.getBoundingClientRect();
            const viewportHeight = window.innerHeight;
            let targetTop = rect.top;
            if (tipRect.bottom > viewportHeight - 10) {
                targetTop = Math.max(10, viewportHeight - tipRect.height - 10);
                tip.style.top = targetTop + 'px';
            }
            const arrowTop = Math.max(10, Math.min(tipRect.height - 20, rect.top - targetTop + 16));
            tip.style.setProperty('--arrow-top', arrowTop + 'px');
        }

        function scheduleClose() {
            clearTimeout(hideTimer);
            hideTimer = setTimeout(() => {
                if (activeTip) {
                    activeTip.style.display = 'none';
                    activeTip = null;
                }
            }, 400);
        }

        group.addEventListener('mouseenter', openTip);
        group.addEventListener('mouseleave', scheduleClose);
        tip.addEventListener('mouseenter', () => {
            clearTimeout(hideTimer);
            tip.style.display = 'block';
        });
        tip.addEventListener('mouseleave', scheduleClose);

        // Touch support for tablet compact sidebar (769–1024px)
        hdr.addEventListener('touchstart', function(e) {
            if (window.innerWidth <= 768) return; // mobile uses accordion
            e.preventDefault();
            if (tip.style.display === 'block') {
                tip.style.display = 'none';
                activeTip = null;
            } else {
                openTip();
            }
        }, { passive: false });
    });
}

function openModulePopupModal(iconHtml, titleText, itemsHtml) {
    const modal = document.getElementById('sbModulePopupModal');
    const iconEl = document.getElementById('sbMpmIcon');
    const titleEl = document.getElementById('sbMpmTitle');
    const bodyEl = document.getElementById('sbMpmBody');
    if (!modal) return;
    if (iconEl) iconEl.innerHTML = iconHtml;
    if (titleEl) titleEl.textContent = titleText;
    if (bodyEl) bodyEl.innerHTML = itemsHtml;
    modal.classList.add('active');
}

function closeModulePopupModal(e) {
    if (e && e.preventDefault) { e.preventDefault(); e.stopPropagation(); }
    const modal = document.getElementById('sbModulePopupModal');
    if (modal) modal.classList.remove('active');
}

function setupSidebarAccordion() {
    document.querySelectorAll('.sb-group').forEach(group => {
        group.addEventListener('click', function(e) {
            // Do not block clicks on actual submenu links or modal items
            if (e.target.closest('a') || e.target.closest('.sb-submenu li') || e.target.closest('#sbModulePopupModal')) {
                return;
            }
            if (window.innerWidth <= 768) {
                e.preventDefault();
                e.stopPropagation();

                const submenu = this.querySelector('.sb-submenu');
                const iconEl = this.querySelector('.sb-hdr-icon');
                const titleEl = this.querySelector('.sb-hdr-title');
                const iconHtml = iconEl ? iconEl.innerHTML : '<i class="fas fa-cubes"></i>';
                const titleText = titleEl ? titleEl.textContent.trim() : 'Module Pages';

                if (submenu && submenu.classList.contains('sb-submenu')) {
                    const links = submenu.querySelectorAll('li a');
                    if (links.length > 0) {
                        let itemsHtml = '';
                        links.forEach(link => {
                            const href = link.getAttribute('href');
                            const labelEl = link.querySelector('.sb-submenu-label');
                            const label = labelEl ? labelEl.textContent.trim() : link.textContent.trim();
                            const iconElSub = link.querySelector('.sb-submenu-icon');
                            const icon = iconElSub ? iconElSub.outerHTML : '<i class="fas fa-chevron-right" style="color:#2563eb;"></i>';
                            const isActive = link.closest('li')?.classList.contains('active') ? 'active' : '';
                            itemsHtml += `
                                <a href="${href}" class="sb-mpm-item ${isActive}" onclick="closeSidebar(); closeModulePopupModal();">
                                    <span>${label}</span>
                                    ${icon}
                                </a>
                            `;
                        });
                        openModulePopupModal(iconHtml, titleText, itemsHtml);
                        return;
                    }
                }
            }
        });
    });

    document.querySelectorAll('.sb-hdr').forEach(hdr => {
        hdr.addEventListener('click', function(e) {
            if (window.innerWidth > 768) {
                e.preventDefault();
                if (this._sbTip) {
                    const rect = this.getBoundingClientRect();
                    const sidebar = document.getElementById('appSidebar') || document.getElementById('sidebar');
                    const sidebarWidth = sidebar ? sidebar.getBoundingClientRect().width : 230;
                    this._sbTip.style.left = (sidebarWidth - 2) + 'px';
                    this._sbTip.style.top = rect.top + 'px';
                    
                    const isDisplayed = this._sbTip.style.display === 'block';
                    document.querySelectorAll('.sb-tooltip-popup').forEach(t => {
                        if (t !== this._sbTip) t.style.display = 'none';
                    });
                    
                    if (isDisplayed) {
                        this._sbTip.style.display = 'none';
                    } else {
                        this._sbTip.style.display = 'block';
                        const tipRect = this._sbTip.getBoundingClientRect();
                        const viewportHeight = window.innerHeight;
                        let targetTop = rect.top;
                        if (tipRect.bottom > viewportHeight - 10) {
                            targetTop = Math.max(10, viewportHeight - tipRect.height - 10);
                            this._sbTip.style.top = targetTop + 'px';
                        }
                        const arrowTop = Math.max(10, Math.min(tipRect.height - 20, rect.top - targetTop + 16));
                        this._sbTip.style.setProperty('--arrow-top', arrowTop + 'px');
                    }
                }
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    renumberSidebarModules();
    // Build tooltips FIRST so hdr._sbTip references are ready
    buildSidebarTooltips();
    // Then set up accordion (uses _sbTip in compact mode)
    setupSidebarAccordion();

    // Auto-expand current active menu section & highlight active group header
    document.querySelectorAll('.sb-submenu').forEach(submenu => {
        if (submenu.querySelector('li.active')) {
            submenu.classList.add('open');
            const hdr = submenu.previousElementSibling;
            if (hdr && hdr.classList.contains('sb-hdr')) {
                hdr.classList.add('open');
                hdr.classList.add('active-link');
            }
            const group = submenu.closest('.sb-group');
            if (group) group.classList.add('active-group');
        }
    });

    // Client-side path matching fallback to make sure active class is set
    const currentPath = window.location.pathname;
    document.querySelectorAll('.sb-nav a').forEach(a => {
        try {
            const linkPath = new URL(a.href).pathname;
            if (linkPath === currentPath) {
                const li = a.closest('li');
                if (li) {
                    li.classList.add('active');
                    
                    const submenu = li.closest('.sb-submenu');
                    if (submenu) {
                        submenu.classList.add('open');
                        const hdr = submenu.previousElementSibling;
                        if (hdr && hdr.classList.contains('sb-hdr')) {
                            hdr.classList.add('open');
                            hdr.classList.add('active-link');
                        }
                        const group = submenu.closest('.sb-group');
                        if (group) group.classList.add('active-group');
                    }
                }
            }
        } catch(e) {}
    });

    // Auto-scroll active group/header into view inside sidebar
    // Prioritize visible group container (.active-group) because inner submenu list items (li.active) are display:none on desktop
    const activeItem = document.querySelector('.sb-nav .active-group') || 
                       document.querySelector('.sb-nav .active-link') || 
                       document.querySelector('.sb-nav li.active');
    
    if (window.innerWidth > 768 && activeItem) {
        activeItem.scrollIntoView({ block: 'center', behavior: 'instant' });
        setTimeout(() => {
            activeItem.scrollIntoView({ block: 'center', behavior: 'smooth' });
        }, 450);
    } else {
        const sbNav = document.querySelector('.sb-nav');
        if (sbNav) sbNav.scrollTop = 0;
    }

    // Topbar Search Event Listeners
    const searchInput = $('#topbarSearchInput');
    const resultsContainer = $('#topbarSearchResults');
    let searchTimeout = null;

    if (searchInput.length) {
        searchInput.on('input', function() {
            clearTimeout(searchTimeout);
            const query = $(this).val().trim();
            
            if (query.length < 2) {
                resultsContainer.hide().empty();
                return;
            }
            
            const currentAcademicSessionId = $('#topbarAcademicYear').val() || '{{ $layoutCurrentSessionId ?? "" }}';

            searchTimeout = setTimeout(function() {
                $.get('{{ route("school.topbar-search") }}', { 
                    query: query,
                    academic_session_id: currentAcademicSessionId
                })
                    .done(function(data) {
                        resultsContainer.empty();
                        let html = '';
                        
                        if (data.students && data.students.length > 0) {
                            html += '<div style="padding: 6px 12px; font-size: 10px; font-weight: 700; color: var(--gold); text-transform: uppercase; background: var(--page); border-bottom: 1px solid var(--border);">Students</div>';
                            data.students.forEach(student => {
                                const name = (student.first_name || '') + ' ' + (student.last_name || '');
                                const adm = student.admission_number ? 'Adm: ' + student.admission_number : '';
                                const cls = student.class_name ? 'Class: ' + student.class_name : '';
                                const roll = student.roll_number ? 'Roll: ' + student.roll_number : '';
                                const phone = student.phone ? '📞 ' + student.phone : '';
                                const details = [adm, cls, roll, phone].filter(Boolean).join(' | ');
                                const photo = student.photo_url || null;
                                
                                html += `
                                    <a href="/school/students/${student.id}" class="search-result-item">
                                        <div style="width: 28px; height: 28px; border-radius: 50%; background: #e5e7eb; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            ${photo ? `<img src="${photo}" style="width: 100%; height: 100%; object-fit: cover;">` : `<i class="fas fa-user" style="color: #9ca3af; font-size: 11px;"></i>`}
                                        </div>
                                        <div style="min-width: 0;">
                                            <div style="font-size: 12.5px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${name.trim()}</div>
                                            <div style="font-size: 10.5px; color: var(--t2);">${details}</div>
                                        </div>
                                    </a>
                                `;
                            });
                        }
                        
                        if (data.staff && data.staff.length > 0) {
                            html += '<div style="padding: 6px 12px; font-size: 10px; font-weight: 700; color: var(--gold); text-transform: uppercase; background: var(--page); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">Staff</div>';
                            data.staff.forEach(member => {
                                const name = (member.first_name || '') + ' ' + (member.last_name || '');
                                const empId = member.employee_id ? 'ID: ' + member.employee_id : '';
                                const phone = member.phone ? '📞 ' + member.phone : '';
                                const details = [empId, phone].filter(Boolean).join(' | ');
                                const photo = member.photo ? member.photo_url : null;
                                
                                html += `
                                    <a href="/school/staff/${member.id}/edit" class="search-result-item">
                                        <div style="width: 28px; height: 28px; border-radius: 50%; background: #e5e7eb; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            ${photo ? `<img src="${photo}" style="width: 100%; height: 100%; object-fit: cover;">` : `<i class="fas fa-user-tie" style="color: #9ca3af; font-size: 11px;"></i>`}
                                        </div>
                                        <div style="min-width: 0;">
                                            <div style="font-size: 12.5px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${name.trim()}</div>
                                            <div style="font-size: 10.5px; color: var(--t2);">${details}</div>
                                        </div>
                                    </a>
                                `;
                            });
                        }
                        
                        if (html === '') {
                            html = '<div style="padding: 12px; font-size: 12.5px; color: var(--t2); text-align: center;">No matches found</div>';
                        }
                        
                        resultsContainer.html(html).show();
                    })
                    .fail(function() {
                        resultsContainer.hide().empty();
                    });
            }, 300);
        });

        // Navigate on Enter if result exists
        searchInput.on('keydown', function(e) {
            if (e.key === 'Enter') {
                const firstResult = resultsContainer.find('a.search-result-item').first();
                if (firstResult.length) {
                    window.location.href = firstResult.attr('href');
                }
            }
        });

        // Close results when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.topbar-search-wrap').length) {
                resultsContainer.hide();
            }
        });
    }

    // Sidebar Module Search filtering logic
    const moduleSearchInput = document.getElementById('sbModuleSearch');
    if (moduleSearchInput) {
        moduleSearchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const groups = document.querySelectorAll('.sb-nav .sb-group');
            groups.forEach(group => {
                const titleEl = group.querySelector('.sb-hdr-title');
                const titleText = titleEl ? titleEl.textContent.toLowerCase() : '';
                const submenuLabels = Array.from(group.querySelectorAll('.sb-submenu-label'))
                    .map(el => el.textContent.toLowerCase());
                
                const matchesTitle = titleText.includes(query);
                const matchesSubmenu = submenuLabels.some(lbl => lbl.includes(query));
                
                if (matchesTitle || matchesSubmenu) {
                    group.style.display = '';
                    const submenu = group.querySelector('.sb-submenu');
                    const hdr = group.querySelector('.sb-hdr');
                    if (query.length > 0 && matchesSubmenu) {
                        if (submenu) {
                            submenu.style.maxHeight = '600px';
                            submenu.style.opacity = '1';
                            submenu.style.transform = 'translateY(0)';
                            submenu.style.padding = '4px 6px 6px 20px';
                            submenu.style.pointerEvents = 'auto';
                        }
                        if (hdr) hdr.classList.add('open');
                    } else if (query.length === 0) {
                        if (submenu) {
                            if (!group.classList.contains('active-group')) {
                                submenu.style.maxHeight = '';
                                submenu.style.opacity = '';
                                submenu.style.transform = '';
                                submenu.style.padding = '';
                                submenu.style.pointerEvents = '';
                                if (hdr) hdr.classList.remove('open');
                            }
                        }
                    }
                } else {
                    group.style.display = 'none';
                }
            });
        });
    }

    // Auto-close mobile navigation drawer when tapping any menu item link
    document.querySelectorAll('.sb-nav a').forEach(a => {
        a.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                const href = this.getAttribute('href');
                if (href && !href.startsWith('javascript:')) {
                    closeSidebar();
                }
            }
        });
    });

    // Touch gesture support: Swipe left on mobile drawer to close
    let touchStartX = 0;
    let touchStartY = 0;
    document.addEventListener('touchstart', e => {
        if (window.innerWidth <= 768 && e.touches && e.touches.length > 0) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }
    }, { passive: true });

    document.addEventListener('touchend', e => {
        if (window.innerWidth <= 768 && e.changedTouches && e.changedTouches.length > 0) {
            const sidebar = document.getElementById('appSidebar') || document.getElementById('sidebar');
            if (sidebar && sidebar.classList.contains('open')) {
                const touchEndX = e.changedTouches[0].clientX;
                const touchEndY = e.changedTouches[0].clientY;
                const diffX = touchStartX - touchEndX;
                const diffY = Math.abs(touchStartY - touchEndY);
                if (diffX > 50 && diffY < 60) {
                    closeSidebar();
                }
            }
        }
    }, { passive: true });
});

window.addEventListener('resize', () => {
    document.querySelectorAll('.sb-tooltip-popup').forEach(t => { t.style.display = 'none'; });
    // Release body scroll lock if window resizes above mobile breakpoint
    if (window.innerWidth > 768) document.body.style.overflow = '';
});

document.addEventListener('click', e => {
    if (!e.target.closest('.user-wrap')) {
        document.querySelectorAll('.user-drop').forEach(d => d.classList.remove('open'));
    }
    if (!e.target.closest('.notif-wrap')) {
        const drop = document.getElementById('notifDrop');
        if (drop) drop.classList.remove('open');
    }
    // Close sidebar profile menu when clicking outside
    if (!e.target.closest('.sb-bottom')) {
        const m = document.getElementById('sbProfileMenu');
        if (m) m.classList.remove('open');
        const s = document.getElementById('sbSettingsSubmenuParent');
        if (s && !s.classList.contains('active-parent')) s.classList.remove('open');
    }
    // Close branch switcher dropdown when clicking outside
    if (!e.target.closest('.branch-dropdown-menu') && !e.target.closest('#btnChangeSchool')) {
        const bd = document.getElementById('branchDropdownMenu');
        if (bd) bd.style.display = 'none';
    }
    // Close tooltips when clicking outside sidebar
    if (!e.target.closest('.sb-group') && !e.target.closest('.sb-tooltip-popup')) {
        document.querySelectorAll('.sb-tooltip-popup').forEach(t => { t.style.display = 'none'; });
    }
});

function toggleBranchDropdown(e) {
    if (e) e.stopPropagation();
    const bd = document.getElementById('branchDropdownMenu');
    if (bd) {
        bd.style.display = (bd.style.display === 'none' || bd.style.display === '') ? 'block' : 'none';
    }
}

function updateDynamicGreeting() {
    const titleEl = document.getElementById('mwcTitle');
    const subtitleEl = document.getElementById('mwcSubtitle');
    if (!titleEl && !subtitleEl) return;

    const userName = titleEl ? (titleEl.getAttribute('data-user-name') || 'User') : 'User';
    const now = new Date();
    const hour = now.getHours();

    let greeting = '';
    let emoji = '';
    let subtitle = '';

    if (hour >= 5 && hour < 12) {
        greeting = 'Good Morning';
        emoji = '👋';
        subtitle = 'Manage your school operations efficiently today.';
    } else if (hour >= 12 && hour < 17) {
        greeting = 'Good Afternoon';
        emoji = '☀';
        subtitle = 'Hope your day is going great. Stay productive.';
    } else if (hour >= 17 && hour < 21) {
        greeting = 'Good Evening';
        emoji = '🌇';
        subtitle = 'Review today\'s activities and stay organized.';
    } else {
        greeting = 'Good Night';
        emoji = '🌙';
        subtitle = 'Everything is under control. Have a peaceful evening.';
    }

    if (titleEl) {
        titleEl.textContent = `${greeting}, ${userName} ${emoji}`;
    }
    if (subtitleEl) {
        subtitleEl.textContent = subtitle;
    }
}

function openMobileNotifModal(event) {
    if (event) {
        event.stopPropagation();
        if (event.preventDefault) event.preventDefault();
    }
    const modal = document.getElementById('sbNotifFullModal');
    if (modal) {
        modal.classList.add('active');
        if (typeof fetchLatestNotifications === 'function') {
            fetchLatestNotifications();
        }
    } else {
        toggleNotifDrop(event);
    }
}

function closeMobileNotifModal(event) {
    if (event) {
        event.stopPropagation();
        if (event.preventDefault) event.preventDefault();
    }
    const modal = document.getElementById('sbNotifFullModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

function toggleNotifDrop(event) {
    if (event) event.stopPropagation();
    const drop = document.getElementById('notifDrop');
    if (drop) drop.classList.toggle('open');
}

function toggleSbProfileMenu(event) {
    if (event) event.stopPropagation();
    const menu = document.getElementById('sbProfileMenu');
    if (menu) {
        menu.classList.toggle('open');
    }
}

function toggleSbSubmenu(event) {
    if (event) event.stopPropagation();
    const parentItem = document.getElementById('sbSettingsSubmenuParent') || (event && event.currentTarget ? event.currentTarget.closest('.sb-menu-item-has-submenu') : null);
    if (parentItem) {
        parentItem.classList.toggle('open');
    }
}

function toggleTheme() {
    const isDark = document.body.classList.toggle('dark-mode');
    localStorage.setItem('school_erp_theme', isDark ? 'dark' : 'light');
    updateThemeIcon(isDark);
}
function updateThemeIcon(isDark) {
    const icon = document.getElementById('themeToggleIcon');
    if (icon) {
        icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        icon.style.color = isDark ? '#f59e0b' : '';
    }
}
document.addEventListener('DOMContentLoaded', () => {
    if (document.body.classList.contains('dark-mode')) {
        updateThemeIcon(true);
    }

    // Dynamic Time-Based Greeting Initialization & Interval
    updateDynamicGreeting();
    setInterval(updateDynamicGreeting, 60000);

    // Ensure mobile navigation drawer menu remains closed by default on page load

});

function switchAcademicYear(sessionId) {
    @if(!$isSchoolDashboard)
        if (typeof showToast === 'function') {
            showToast('Academic year can only be changed from the Dashboard.', 'warning');
        }
        return;
    @endif
    if (!sessionId) return;
    $.post('{{ route("school.dashboard.change-session") }}', {
        academic_session_id: sessionId
    }).done(function(res) {
        if (res.success) {
            showToast(res.message || 'Academic session changed successfully!');
            setTimeout(() => {
                location.reload();
            }, 800);
        } else {
            showToast('Failed to switch academic session.');
        }
    }).fail(function() {
        showToast('Error switching academic session.');
    });
}
</script>
@include('layouts.ai_chatbot')
@include('partials.realtime_notifications')
@include('partials.android_bottom_nav')
@yield('scripts')
</body>
</html>

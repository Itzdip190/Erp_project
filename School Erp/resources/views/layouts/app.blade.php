<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SchoolCloud ERP') — SchoolCloud ERP</title>

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
body{font-family:'Inter',sans-serif;background:var(--page);color:var(--t1);display:flex;min-height:100vh;overflow-x:hidden;}

/* ─── SIDEBAR ─────────────────────────────────────────────── */
.sidebar{
    width:185px;min-width:185px;background:var(--navy);
    height:100vh;position:fixed;left:0;top:0;
    display:flex;flex-direction:column;z-index:200;
    overflow-y:auto;overflow-x:hidden;
    transition:width .3s ease;
}
.sidebar::-webkit-scrollbar{width:3px;}
.sidebar::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:4px;}

.sb-logo{
    padding:18px 14px 14px;
    display:flex;align-items:center;gap:9px;
    border-bottom:1px solid rgba(255,255,255,.08);
    text-decoration:none;flex-shrink:0;
}
.sb-logo-icon{
    width:34px;height:34px;border-radius:9px;
    background:var(--gold);
    display:flex;align-items:center;justify-content:center;
    font-size:16px;color:var(--navy);flex-shrink:0;
}
.sb-logo-text strong{display:block;color:#fff;font-size:13px;font-weight:800;font-family:'Plus Jakarta Sans',sans-serif;line-height:1.15;}
.sb-logo-text span{color:var(--gold);font-size:9.5px;font-weight:500;}

.sb-school{
    margin:12px 10px;
    background:rgba(255,255,255,.07);
    border:1px solid rgba(255,255,255,.1);
    border-radius:10px;padding:10px 11px;flex-shrink:0;
}
.sb-school-row{display:flex;align-items:center;gap:6px;margin-bottom:4px;}
.sb-school-icon{
    width:22px;height:22px;border-radius:5px;
    background:rgba(245,158,11,.2);
    display:flex;align-items:center;justify-content:center;
    color:var(--gold);font-size:10px;flex-shrink:0;
}
.sb-school-name{color:#fff;font-size:11.5px;font-weight:700;line-height:1.3;}
.sb-school-session{color:rgba(255,255,255,.45);font-size:9.5px;margin-bottom:6px;}
.sb-plan-badge{
    display:inline-flex;align-items:center;gap:4px;
    background:var(--gold-bg);color:var(--gold);
    font-size:9.5px;font-weight:700;border-radius:20px;padding:2px 8px;
}

.sb-nav{list-style:none;padding:6px 8px;flex:1;}
.sb-nav li{margin-bottom:1px;}
.sb-nav a{
    display:flex;align-items:center;gap:9px;
    padding:8px 9px;border-radius:7px;
    color:rgba(255,255,255,.55);font-size:12px;font-weight:500;
    text-decoration:none;transition:all .18s;
    border-left:2.5px solid transparent;white-space:nowrap;
}
.sb-nav a i{width:15px;text-align:center;font-size:12px;flex-shrink:0;}
.sb-nav a:hover{background:rgba(255,255,255,.08);color:#fff;}
.sb-nav li.active a{background:var(--gold-bg);color:#fff;border-left-color:var(--gold);}
.sb-nav li.active a i{color:var(--gold);}

.sb-bottom{padding:10px;border-top:1px solid rgba(255,255,255,.08);flex-shrink:0;}
.sb-help{
    background:linear-gradient(135deg,rgba(245,158,11,.18),rgba(245,158,11,.04));
    border:1px solid rgba(245,158,11,.22);
    border-radius:9px;padding:11px 11px 10px;margin-bottom:8px;
}
.sb-help p{color:rgba(255,255,255,.5);font-size:10px;margin-bottom:6px;}
.sb-help strong{display:block;color:#fff;font-size:11.5px;margin-bottom:8px;}
.btn-support{
    display:block;text-align:center;
    background:var(--gold);color:var(--navy);
    font-size:11px;font-weight:700;border-radius:6px;
    padding:7px;text-decoration:none;transition:.2s;
}
.btn-support:hover{background:#d97706;}
.sb-logout{
    display:flex;align-items:center;gap:8px;
    color:rgba(255,255,255,.4);font-size:11.5px;
    padding:7px 9px;border-radius:7px;
    text-decoration:none;transition:.2s;
}
.sb-logout:hover{background:rgba(239,68,68,.12);color:#ef4444;}

/* ─── MAIN ─────────────────────────────────────────────────── */
.main{margin-left:185px;flex:1;display:flex;flex-direction:column;min-height:100vh;}

/* ─── TOPBAR ───────────────────────────────────────────────── */
.topbar{
    background:#fff;border-bottom:1px solid var(--border);
    height:62px;padding:0 22px;
    display:flex;align-items:center;justify-content:space-between;
    position:sticky;top:0;z-index:100;
    box-shadow:0 1px 3px rgba(0,0,0,.05);
}
.topbar-left{display:flex;align-items:center;gap:13px;}
.hamburger{background:none;border:none;color:var(--t2);font-size:17px;cursor:pointer;padding:4px;display:none;}
.page-heading{font-family:'Plus Jakarta Sans',sans-serif;font-size:15px;font-weight:700;color:var(--t1);}
.topbar-right{display:flex;align-items:center;gap:10px;}

.notif-wrap{position:relative;}
.notif-btn{
    background:var(--page);border:1px solid var(--border);
    border-radius:8px;width:37px;height:37px;
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;color:var(--t2);font-size:15px;transition:.2s;position:relative;
}
.notif-btn:hover{border-color:var(--gold);color:var(--gold);}

.user-wrap{position:relative;}
.user-btn{
    display:flex;align-items:center;gap:7px;
    cursor:pointer;padding:4px 7px;border-radius:9px;
    border:1px solid transparent;transition:.2s;
}
.user-btn:hover{background:var(--page);border-color:var(--border);}
.avatar{
    width:34px;height:34px;border-radius:9px;
    background:linear-gradient(135deg,var(--navy),var(--purple));
    display:flex;align-items:center;justify-content:center;
    color:#fff;font-size:12px;font-weight:700;flex-shrink:0;
}
.user-info strong{display:block;font-size:11.5px;font-weight:700;color:var(--t1);}
.user-info span{font-size:10px;color:var(--t2);}
.user-drop{
    position:absolute;top:calc(100% + 8px);right:0;width:170px;
    background:#fff;border:1px solid var(--border);
    border-radius:11px;box-shadow:var(--shadow-lg);
    display:none;z-index:300;overflow:hidden;
}
.user-drop.open{display:block;}
.user-drop a{
    display:flex;align-items:center;gap:9px;
    padding:10px 13px;font-size:12.5px;color:var(--t1);
    text-decoration:none;transition:.15s;
}
.user-drop a:hover{background:var(--page);}
.user-drop a.danger{color:var(--red);}
.user-drop a i{width:13px;text-align:center;color:var(--t2);font-size:12px;}
.user-drop a.danger i{color:var(--red);}

/* ─── PAGE CONTENT ─────────────────────────────────────────── */
.pg{padding:22px 24px;flex:1;}

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

/* ─── RESPONSIVE ───────────────────────────────────────────── */
@media(max-width:1024px){
    .sidebar{width:54px;}
    .sb-logo-text,.sb-school,.sb-nav a span,.sb-bottom{display:none;}
    .sb-nav a{justify-content:center;padding:10px 0;}
    .sb-nav a i{width:100%;font-size:15px;}
    .main{margin-left:54px;}
    .hamburger{display:flex;}
}
@media(max-width:768px){
    .sidebar{transform:translateX(-185px);width:185px;}
    .sidebar.open{transform:translateX(0);}
    .sb-logo-text,.sb-school,.sb-nav a span,.sb-bottom{display:block!important;}
    .sb-nav a{justify-content:flex-start!important;padding:8px 9px!important;}
    .sb-nav a i{width:15px!important;font-size:12px!important;}
    .main{margin-left:0;}
}
</style>
@yield('styles')
</head>
<body>
@php
    use Carbon\Carbon;
    $authUser     = auth()->user();
    $authInitials = strtoupper(substr($authUser->name,0,1).(str_contains($authUser->name,' ') ? substr($authUser->name,strrpos($authUser->name,' ')+1,1) : ''));
    $authRole     = ucfirst(str_replace('_',' ',$authUser->roles->first()?->name ?? 'User'));
    try {
        $currentSchool  = app()->bound('currentSchool') ? app('currentSchool') : null;
        $currentSession = $currentSchool
            ? \App\Models\AcademicSession::where('school_id',$currentSchool->id)->where('is_current',true)->first()
            : null;
        $planName = $currentSchool ? ucfirst($currentSchool->status ?? 'Basic') : 'Basic';
    } catch (\Exception $e) {
        $currentSchool = null; $currentSession = null; $planName = 'Basic';
    }
@endphp

<!-- ══════════ SIDEBAR ══════════ -->
<aside class="sidebar" id="appSidebar">
    <a href="{{ route('school.dashboard') }}" class="sb-logo">
        <div class="sb-logo-icon"><i class="fas fa-shield-halved"></i></div>
        <div class="sb-logo-text">
            <strong>SchoolCloud ERP</strong>
            <span>Smart School ERP</span>
        </div>
    </a>

    @if($currentSchool)
    <div class="sb-school">
        <div class="sb-school-row">
            <div class="sb-school-icon"><i class="fas fa-school"></i></div>
            <div class="sb-school-name">{{ $currentSchool->name }}</div>
        </div>
        <div class="sb-school-session">
            <i class="fas fa-calendar-alt" style="font-size:9px;margin-right:3px;"></i>
            Session: {{ $currentSession?->name ?? '—' }}
        </div>
        <span class="sb-plan-badge">
            <i class="fas fa-star" style="font-size:8px;"></i>
            {{ $planName }}
        </span>
    </div>
    @endif

    <ul class="sb-nav">
        <li class="{{ request()->is('school/dashboard') ? 'active' : '' }}">
            <a href="{{ route('school.dashboard') }}"><i class="fas fa-th-large"></i><span>Dashboard</span></a>
        </li>
        <li class="{{ request()->is('school/students*') ? 'active' : '' }}">
            <a href="{{ route('school.students.index') }}"><i class="fas fa-user-graduate"></i><span>Students</span></a>
        </li>
        <li class="{{ request()->is('school/attendance/students*') ? 'active' : '' }}">
            <a href="{{ route('school.attendance.students.index') }}"><i class="fas fa-calendar-check"></i><span>Attendance</span></a>
        </li>
        <li><a href="javascript:void(0)" onclick="showToast('🚧 Fees module coming soon!')"><i class="fas fa-indian-rupee-sign"></i><span>Fees</span></a></li>
        <li><a href="javascript:void(0)" onclick="showToast('🚧 Academics module coming soon!')"><i class="fas fa-book"></i><span>Academics</span></a></li>
        <li><a href="javascript:void(0)" onclick="showToast('🚧 Exams module coming soon!')"><i class="fas fa-file-alt"></i><span>Exams</span></a></li>
        <li><a href="javascript:void(0)" onclick="showToast('🚧 Transport module coming soon!')"><i class="fas fa-bus"></i><span>Transport</span></a></li>
        <li><a href="javascript:void(0)" onclick="showToast('🚧 Communication module coming soon!')"><i class="fas fa-comment-dots"></i><span>Communication</span></a></li>
        <li><a href="javascript:void(0)" onclick="showToast('🚧 Library module coming soon!')"><i class="fas fa-book-open"></i><span>Library</span></a></li>
        <li class="{{ request()->is('school/attendance/students/report*') ? 'active' : '' }}">
            <a href="{{ route('school.attendance.students.report') }}"><i class="fas fa-chart-bar"></i><span>Reports</span></a>
        </li>
        <li class="{{ request()->is('school/attendance/staff*') ? 'active' : '' }}">
            <a href="{{ route('school.attendance.staff.index') }}"><i class="fas fa-users"></i><span>Staff</span></a>
        </li>
        <li><a href="javascript:void(0)" onclick="showToast('🚧 Inventory module coming soon!')"><i class="fas fa-boxes-stacked"></i><span>Inventory</span></a></li>
        <li class="{{ request()->is('school/settings*') ? 'active' : '' }}">
            <a href="{{ route('school.settings.index') }}"><i class="fas fa-gear"></i><span>Settings</span></a>
        </li>
    </ul>

    <div class="sb-bottom">
        <div class="sb-help">
            <strong>Need Help?</strong>
            <p>We're here to support you</p>
            <a href="#" class="btn-support"><i class="fas fa-headset"></i> Contact Support</a>
        </div>
        <a href="{{ route('logout') }}" class="sb-logout">
            <i class="fas fa-right-from-bracket"></i><span>Logout</span>
        </a>
    </div>
</aside>

<!-- ══════════ MAIN ══════════ -->
<div class="main">

    <!-- TOPBAR -->
    <nav class="topbar">
        <div class="topbar-left">
            <button class="hamburger" onclick="document.getElementById('appSidebar').classList.toggle('open')">
                <i class="fas fa-bars"></i>
            </button>
            <div class="page-heading">@yield('page-title', 'Dashboard')</div>
        </div>
        <div class="topbar-right">
            @if($currentSchool)
            <span style="font-size:12px;color:var(--t2);font-weight:600;">
                <i class="fas fa-school" style="color:var(--gold);margin-right:4px;"></i>
                {{ $currentSchool->name }}
            </span>
            @endif
            <div class="notif-wrap">
                <div class="notif-btn" title="Notifications">
                    <i class="fas fa-bell"></i>
                </div>
            </div>
            <div class="user-wrap">
                <div class="user-btn" onclick="this.closest('.user-wrap').querySelector('.user-drop').classList.toggle('open')">
                    <div class="avatar">{{ $authInitials }}</div>
                    <div class="user-info">
                        <strong>{{ $authUser->name }}</strong>
                        <span>{{ $authRole }}</span>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size:9px;color:var(--t2);margin-left:4px;"></i>
                </div>
                <div class="user-drop">
                    <a href="#"><i class="fas fa-user"></i> Profile</a>
                    <a href="{{ route('school.settings.index') }}"><i class="fas fa-gear"></i> Settings</a>
                    <a href="{{ route('logout') }}" class="danger"><i class="fas fa-right-from-bracket"></i> Logout</a>
                </div>
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
document.addEventListener('click',e=>{
    if(!e.target.closest('.user-wrap'))
        document.querySelectorAll('.user-drop').forEach(d=>d.classList.remove('open'));
});
</script>
@yield('scripts')
</body>
</html>

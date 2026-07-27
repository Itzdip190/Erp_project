<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Settings — {{ $school?->name ?? 'Student Portal' }}</title>
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
    --shadow-lg:0 8px 32px rgba(0,0,0,.12);
}
body{font-family:'Inter',sans-serif;background:var(--page);color:var(--t1);display:flex;min-height:100vh;overflow-x:hidden;}

/* ─── SIDEBAR ─────────────────────────────────────────────── */
.sidebar{
    width:220px;min-width:220px;background:var(--navy);
    height:100vh;position:fixed;left:0;top:0;
    display:flex;flex-direction:column;z-index:200;
    overflow-y:auto;overflow-x:hidden;
    transition:transform .3s ease, width .3s ease;
}
.sidebar::-webkit-scrollbar{width:3px;}
.sidebar::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:4px;}

/* Desktop Collapsed Sidebar State */
body.sidebar-collapsed .sidebar {
    transform: translateX(-220px);
}
body.sidebar-collapsed .main {
    margin-left: 0 !important;
}

/* Sidebar Overlay for Mobile */
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
.main{margin-left:220px;flex:1;display:flex;flex-direction:column;min-height:100vh;transition:margin-left .3s ease;}

/* ─── TOPBAR ─────────────────────────────────────────────── */
.topbar{
    background:#fff;border-bottom:1px solid var(--border);
    height:60px;padding:0 18px;
    display:flex;align-items:center;justify-content:space-between;
    position:sticky;top:0;z-index:100;
    box-shadow:0 1px 3px rgba(0,0,0,.05);gap:10px;
}
.topbar-left{display:flex;align-items:center;gap:10px;min-width:0;flex:1;}
.hamburger{background:none;border:none;color:var(--t2);font-size:17px;cursor:pointer;padding:6px;display:flex;border-radius:6px;transition:background 0.2s;}
.hamburger:hover{background:var(--page);color:var(--t1);}
.greeting{min-width:0;flex:1;}
.greeting h2{font-family:'Plus Jakarta Sans',sans-serif;font-size:14.5px;font-weight:700;color:var(--t1);line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.greeting p{font-size:11px;color:var(--t2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.topbar-right{display:flex;align-items:center;gap:8px;flex-shrink:0;}
.date-pill{
    display:flex;align-items:center;gap:6px;background:var(--page);
    border:1px solid var(--border);border-radius:8px;padding:5px 10px;
    font-size:11px;color:var(--t2);white-space:nowrap;
}
.date-pill i{color:var(--gold);}
.notif-wrap{position:relative;}
.notif-btn{
    background:var(--page);border:1px solid var(--border);border-radius:8px;
    width:36px;height:36px;display:flex;align-items:center;justify-content:center;
    cursor:pointer;color:var(--t2);font-size:14px;transition:.2s;position:relative;
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
    padding:3px 6px;border-radius:9px;border:1px solid transparent;transition:.2s;
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
    background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);
    color:#ef4444;border-radius:8px;width:36px;height:36px;
    display:flex;align-items:center;justify-content:center;
    font-size:14px;cursor:pointer;text-decoration:none;transition:.2s;flex-shrink:0;
}
.topbar-logout-btn:hover{
    background:#ef4444;color:#fff;border-color:#ef4444;
    box-shadow:0 2px 8px rgba(239,68,68,.3);
}

/* ─── PAGE CONTENT ─────────────────────────────────────────── */
.pg{padding:20px 22px;max-width:850px;}
.settings-card{
    background:#fff;border:1px solid var(--border);
    border-radius:14px;box-shadow:var(--shadow);
    padding:24px;margin-bottom:20px;
}
.settings-hdr{border-bottom:1px solid var(--border);padding-bottom:14px;margin-bottom:18px;}
.settings-hdr h2{font-size:16px;font-weight:800;color:var(--t1);display:flex;align-items:center;gap:9px;font-family:'Plus Jakarta Sans',sans-serif;}
.settings-hdr h2 i{color:var(--gold);}
.settings-hdr p{font-size:11.5px;color:var(--t2);margin-top:3px;}
.form-group{margin-bottom:16px;}
.form-group label{display:block;font-size:11.5px;font-weight:700;color:var(--t1);margin-bottom:6px;}
.form-control{
    width:100%;padding:10px 14px;border:1px solid var(--border);
    border-radius:9px;font-size:12.5px;background:var(--page);color:var(--t1);
    transition:all .18s;
}
.form-control:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(245,158,11,.15);background:#fff;}
.btn-save{
    background:var(--gold);color:var(--navy);border:none;font-weight:700;
    padding:10px 22px;border-radius:9px;cursor:pointer;font-size:12.5px;transition:.2s;
    display:inline-flex;align-items:center;gap:6px;
}
.btn-save:hover{background:#d97706;}

.footer{display:flex;align-items:center;justify-content:space-between;padding:14px 0 6px;border-top:1px solid var(--border);font-size:10.5px;color:var(--t3);}

/* Responsive */
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
    .sb-close-btn{display:flex !important;}
}
@media(max-width:768px){
    .topbar{height:54px;padding:0 10px;gap:6px;}
    .topbar-left{gap:6px;}
    .greeting h2{font-size:13px;max-width:140px;}
    .topbar-subtitle{display:none !important;}
    .date-pill{display:none !important;}
    .user-info, .user-caret{display:none !important;}
    .user-btn{padding:2px;}
    .avatar{width:32px;height:32px;font-size:11px;}
    .notif-btn{width:34px;height:34px;font-size:14px;}
    .notif-drop{right:-10px;width:calc(100vw - 20px);max-width:300px;}
    .user-drop{right:0;}
    .pg{padding:12px 10px;}
    .settings-card{padding:16px;}
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
        'title' => 'Portal Settings',
        'subtitle' => 'Manage your student portal preferences and account security'
    ])

    <!-- PAGE CONTENT -->
    <div class="pg">
        <div class="settings-card">
            <div class="settings-hdr">
                <h2><i class="fas fa-user-gear"></i> Account & Profile Information</h2>
                <p>Personal details for {{ $stuName }}</p>
            </div>
            <div class="form-group">
                <label>Student Full Name</label>
                <input type="text" class="form-control" value="{{ $stuName }}" readonly disabled>
            </div>
            <div class="form-group">
                <label>Class & Section</label>
                <input type="text" class="form-control" value="Class {{ $classDisplay }} - {{ $sectionDisplay }}" readonly disabled>
            </div>
            <div class="form-group">
                <label>School Name</label>
                <input type="text" class="form-control" value="{{ $school?->name ?? 'N/A' }}" readonly disabled>
            </div>
        </div>

        <div class="settings-card">
            <div class="settings-hdr">
                <h2><i class="fas fa-lock"></i> Security & Password</h2>
                <p>Ensure your account password is strong and updated</p>
            </div>
            <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Password update requested. Please contact your school administrator if you require a password reset.');">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" class="form-control" placeholder="••••••••">
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" class="form-control" placeholder="Enter new password">
                </div>
                <button type="submit" class="btn-save"><i class="fas fa-shield-halved"></i> Update Password</button>
            </form>
        </div>

        <div class="footer" style="margin-top: 20px;">
            <span>© 2026 {{ $school?->name ?? 'SchoolCloud ERP' }}. All rights reserved.</span>
            <span>Version 2.0.0 &nbsp;|&nbsp; 🔒 Secure & Trusted</span>
        </div>
    </div>
</div>

<script>
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

    // Accordion toggle
    document.querySelectorAll('.sb-hdr').forEach(hdr => {
        hdr.addEventListener('click', () => {
            const submenu = hdr.nextElementSibling;
            if (submenu && submenu.classList.contains('sb-submenu')) {
                hdr.classList.toggle('open');
                submenu.classList.toggle('open');
            }
        });
    });

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

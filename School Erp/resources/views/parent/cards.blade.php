<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My ID Cards & Passes — SchoolCloud ERP</title>
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
    overflow-y:auto;overflow-x:hidden;transition:width .3s;
}
.sidebar::-webkit-scrollbar{width:3px;}
.sidebar::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:4px;}
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
    background:#fff;border-bottom:1px solid var(--border);
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

/* ─── PAGE ────────────────────────────────────────────────── */
.pg{padding:20px 22px;}

/* CARD */
.card{
    background:#white;border:1px solid var(--border);
    border-radius:13px;box-shadow:var(--shadow);overflow:hidden;
    margin-bottom:18px;
}
.card-hdr{
    padding:16px 20px;
    display:flex;align-items:center;justify-content:space-between;
    border-bottom:1px solid var(--border);
}
.card-title{font-size:14px;font-weight:700;color:var(--t1);}
.card-body{padding:20px;}

.btn {
    display:inline-flex;align-items:center;gap:6px;
    padding:8px 16px;border-radius:8px;font-size:12.5px;font-weight:600;
    cursor:pointer;text-decoration:none;border:none;transition:.2s;
}
.btn-outline{background:transparent;color:var(--t1);border:1px solid var(--border);}
.btn-outline:hover{background:var(--page);border-color:var(--t3);}
.btn-gold{background:var(--gold);color:var(--navy);}
.btn-gold:hover{background:#d97706;}

/* ID CARDS CONTAINER */
.cards-layout{
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));
    gap:20px;
}

/* STUDENT CARD DESIGN */
.id-pass-card{
    border-radius:16px;
    overflow:hidden;
    box-shadow:var(--shadow-lg);
    background:#fff;
    border:1px solid var(--border);
    display:flex;
    flex-direction:column;
    position:relative;
}
.card-preview-area{
    padding:24px;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#f1f5f9;
}

/* Beautiful card visual style */
.visual-id-card{
    width:260px;
    height:380px;
    border-radius:12px;
    color:#fff;
    box-shadow:0 12px 24px rgba(0,0,0,0.15);
    display:flex;
    flex-direction:column;
    overflow:hidden;
    font-family:'Inter', sans-serif;
    position:relative;
}
.visual-id-card-hdr{
    padding:16px;
    text-align:center;
    border-bottom:1px solid rgba(255,255,255,0.1);
}
.visual-id-card-school{
    font-size:12px;
    font-weight:800;
    font-family:'Plus Jakarta Sans', sans-serif;
    letter-spacing:0.5px;
    text-transform:uppercase;
}
.visual-id-card-type{
    font-size:9px;
    font-weight:600;
    opacity:0.8;
    letter-spacing:1px;
    text-transform:uppercase;
    margin-top:2px;
}
.visual-id-card-photo-wrap{
    display:flex;
    justify-content:center;
    margin-top:16px;
}
.visual-id-card-photo{
    width:90px;
    height:100px;
    border-radius:8px;
    border:3px solid rgba(255,255,255,0.25);
    background:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    color:var(--navy);
    font-size:32px;
    font-weight:800;
    overflow:hidden;
}
.visual-id-card-photo img{
    width:100%;
    height:100%;
    object-fit:cover;
}
.visual-id-card-body{
    padding:16px;
    text-align:center;
    flex:1;
    display:flex;
    flex-direction:column;
    justify-content:center;
}
.visual-id-card-name{
    font-size:15px;
    font-weight:700;
    margin-bottom:6px;
}
.visual-id-card-detail{
    font-size:11px;
    opacity:0.9;
    margin-bottom:4px;
}
.visual-id-card-footer{
    padding:12px;
    border-top:1px dashed rgba(255,255,255,0.15);
    text-align:center;
    font-size:10px;
    background:rgba(0,0,0,0.15);
}

/* Print controls */
.card-controls{
    padding:16px;
    border-top:1px solid var(--border);
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.card-meta{
    font-size:11px;
    color:var(--t2);
}

.footer{display:flex;align-items:center;justify-content:space-between;padding:14px 0 6px;border-top:1px solid var(--border);font-size:10.5px;color:var(--t3);}

@media(max-width:1024px){
    .sidebar{width:54px;}
    .sb-logo-text,.sb-student,.sb-hdr-title,.sb-hdr-arrow,.sb-submenu,.sb-bottom{display:none!important;}
    .sb-hdr{justify-content:center;padding:10px 0;margin:0;}
    .sb-hdr-icon{width:24px;height:24px;font-size:11px;}
    .main{margin-left:54px;}
    .hamburger{display:flex;}
}
@media(max-width:768px){
    .sidebar{transform:translateX(-220px);width:220px;}
    .sidebar.open{transform:translateX(0);}
    .sb-logo-text,.sb-student,.sb-hdr-title,.sb-hdr-arrow,.sb-bottom{display:block!important;}
    .sb-submenu{display:none;}
    .sb-submenu.open{display:block!important;}
    .sb-hdr{justify-content:space-between!important;padding:8px 10px!important;margin:0 6px!important;}
    .sb-hdr-icon{width:22px!important;height:22px!important;font-size:9.5px!important;}
    .main{margin-left:0;}
}

/* Print CSS */
@media print {
    body * {
        visibility: hidden;
    }
    .print-card-target, .print-card-target * {
        visibility: visible;
    }
    .print-card-target {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background: none !important;
        box-shadow: none !important;
    }
}
</style>
</head>
<body>

<!-- ══════════ SIDEBAR ══════════ -->
@include('parent.partials.sidebar')

<!-- ══════════ MAIN ══════════ -->
<div class="main">

    <!-- TOPBAR -->
    <nav class="topbar">
        <div class="topbar-left">
            <button class="hamburger" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <i class="fas fa-bars"></i>
            </button>
            <div class="greeting">
                <h2>ID Cards & School Passes</h2>
                <p>View, display on mobile, or print official school identification cards, bus passes, and admit cards</p>
            </div>
        </div>
        <div class="topbar-right">
            <div class="date-pill">
                <i class="fas fa-calendar-days"></i>
                {{ now()->format('M j, Y') }}
            </div>
            <!-- Bell -->
            <div class="notif-wrap">
                <div class="notif-btn" onclick="toggleDrop('notifDrop')">
                    <i class="fas fa-bell"></i>
                    @if($documents->count() > 0)
                        <span class="notif-badge">{{ $documents->count() }}</span>
                    @endif
                </div>
                <div class="notif-drop" id="notifDrop">
                    <div class="nd-hdr">
                        <strong>Notifications</strong>
                        <span class="nd-mark" onclick="document.getElementById('notifDrop').classList.remove('open')">Dismiss</span>
                    </div>
                    <div style="max-height: 250px; overflow-y: auto;">
                        @forelse($documents as $doc)
                        <a href="{{ route('parent.documents.download', ['document' => $doc->id, 'action' => 'view']) }}" target="_blank" class="nd-item" style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-bottom:1px solid var(--border);text-decoration:none;color:var(--t1);">
                            <div style="width:28px;height:28px;border-radius:50%;background:var(--gold-bg);color:var(--gold);display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0;">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:11.5px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">New Document Issued</div>
                                <div style="font-size:10.5px;color:var(--t2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $doc->original_name }}</div>
                            </div>
                            <div style="font-size:9.5px;color:var(--t3);white-space:nowrap;">{{ $doc->created_at->diffForHumans() }}</div>
                        </a>
                        @empty
                        <div class="nd-empty"><i class="fas fa-bell-slash" style="font-size:22px;color:var(--border);display:block;margin-bottom:8px;"></i>No new notifications</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <!-- User -->
            <div class="user-wrap">
                <div class="user-btn" onclick="toggleDrop('userDrop')">
                    <div class="avatar">{{ $stuInitials }}</div>
                    <div class="user-info">
                        <strong>{{ explode(' ',$stuName)[0] }}</strong>
                        <span>Student</span>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size:9px;color:var(--t2);margin-left:4px;"></i>
                </div>
                <div class="user-drop" id="userDrop">
                    <a href="{{ route('logout') }}" class="danger"><i class="fas fa-right-from-bracket"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- PAGE -->
    <div class="pg">
        <div class="card">
            <div class="card-hdr">
                <span class="card-title"><i class="fas fa-id-card" style="color:var(--gold);margin-right:8px;"></i>Active Identification & Travel Passes</span>
            </div>
            <div class="card-body" style="background:#fff;">
                <div class="cards-layout">
                    @forelse($cards as $card)
                    <div class="id-pass-card" id="cardContainer-{{ $card->id }}">
                        <div class="card-preview-area print-card-target">
                            <!-- Visual Card -->
                            <div class="visual-id-card" style="background: {{ $card->template->background_color }}; color: {{ $card->template->text_color }};">
                                <div class="visual-id-card-hdr">
                                    <div class="visual-id-card-school">{{ $school->name }}</div>
                                    <div class="visual-id-card-type">{{ $card->template->name }}</div>
                                </div>
                                <div class="visual-id-card-photo-wrap">
                                    <div class="visual-id-card-photo">
                                        @if($student?->photo)
                                            <img src="{{ $student->photo_url }}" alt="">
                                        @else
                                            <i class="fas fa-user"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="visual-id-card-body">
                                    <div class="visual-id-card-name">{{ $stuName }}</div>
                                    <div class="visual-id-card-detail"><strong>Admission ID:</strong> {{ $student->admission_number }}</div>
                                    <div class="visual-id-card-detail"><strong>Grade:</strong> {{ $classDisplay }} – {{ $sectionDisplay }}</div>
                                    <div class="visual-id-card-detail"><strong>Card No:</strong> {{ $card->card_number }}</div>
                                </div>
                                <div class="visual-id-card-footer">
                                    <span>VALID UNTIL: {{ \Carbon\Carbon::parse($card->expiry_date)->format('M Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-controls">
                            <div class="card-meta">
                                <span style="font-weight: 700; color: var(--green);"><i class="fas fa-circle-check"></i> Active</span>
                                <span style="display: block; margin-top: 2px;">No: {{ $card->card_number }}</span>
                            </div>
                            <button onclick="printCard('cardContainer-{{ $card->id }}')" class="btn btn-outline" style="padding: 6px 12px; font-size: 11.5px;">
                                <i class="fas fa-print"></i> Print Card
                            </button>
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:60px 20px;color:var(--t3);grid-column: 1 / -1;">
                        <i class="fas fa-address-card" style="font-size:42px;display:block;margin-bottom:12px;color:var(--border);"></i>
                        <span style="font-size:13px;">No active ID cards, bus passes, or admit cards have been generated by the administrator yet.</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="footer">
            <span>© 2026 SchoolCloud ERP. All rights reserved.</span>
            <span>Version 2.0.0 &nbsp;|&nbsp; 🔒 Secure & Trusted</span>
        </div>
    </div>
</div>

<script>
function printCard(containerId) {
    const el = document.getElementById(containerId);
    el.classList.add('print-card-target');
    window.print();
    el.classList.remove('print-card-target');
}

document.addEventListener('DOMContentLoaded', () => {
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
    ['notifDrop','userDrop'].forEach(d=>{if(d!==id)document.getElementById(d).classList.remove('open');});
    document.getElementById(id).classList.toggle('open');
}
document.addEventListener('click',e=>{
    if(!e.target.closest('.notif-wrap'))document.getElementById('notifDrop').classList.remove('open');
    if(!e.target.closest('.user-wrap'))document.getElementById('userDrop').classList.remove('open');
});
</script>
</body>
</html>

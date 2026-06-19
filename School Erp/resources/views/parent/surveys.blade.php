<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Surveys & Polls — SchoolCloud ERP</title>
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
    background:#white;background-color:#fff;border:1px solid var(--border);
    border-radius:13px;box-shadow:var(--shadow);overflow:hidden;
    margin-bottom:18px;
}
.card-hdr{
    padding:16px 20px;
    display:flex;align-items:center;justify-content:space-between;
    border-bottom:1px solid var(--border);
}
.card-title{font-size:14px;font-weight:700;color:var(--t1);font-family:'Plus Jakarta Sans',sans-serif;}
.card-body{padding:20px;}

.btn {
    display:inline-flex;align-items:center;gap:6px;
    padding:10px 20px;border-radius:8px;font-size:13px;font-weight:600;
    cursor:pointer;text-decoration:none;border:none;transition:.2s;
}
.btn-primary{background:var(--navy);color:#fff;}
.btn-primary:hover{background:var(--navy2);}

/* SURVEY CARD */
.survey-card{
    padding:20px; border:1px solid var(--border); border-radius:12px;
    background:var(--page); margin-bottom:20px; display:flex; flex-direction:column; gap:15px;
}
.survey-question{font-size:15px; font-weight:750; color:var(--navy); font-family:'Plus Jakarta Sans',sans-serif;}
.survey-options{display:flex; flex-direction:column; gap:10px;}

/* Option layout */
.option-label{
    display:flex; align-items:center; gap:10px; padding:12px 15px;
    border:1px solid var(--border); border-radius:8px; background:#fff; cursor:pointer;
    font-size:13px; font-weight:600; color:var(--t1); transition:.18s;
}
.option-label:hover{border-color:var(--gold); background:rgba(245,158,11,0.02);}

/* Result bar */
.result-item{display:flex; flex-direction:column; gap:5px;}
.result-label{display:flex; justify-content:space-between; font-size:13px; font-weight:600; color:var(--t1);}
.progress-container{height:12px; background:var(--border); border-radius:6px; overflow:hidden;}
.progress-bar{height:100%; background:var(--gold); border-radius:6px;}

/* Alert */
.alert{
    padding:12px 16px;border-radius:8px;margin-bottom:15px;font-size:13px;
    display:flex;align-items:center;gap:10px;
}
.alert-success{background:rgba(16,185,129,0.1);color:var(--green);border:1px solid rgba(16,185,129,0.2);}
.alert-danger{background:rgba(239,68,68,0.1);color:var(--red);border:1px solid rgba(239,68,68,0.2);}

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
                <h2>Surveys & Feedback</h2>
                <p>Participate in official school opinion polls and surveys</p>
            </div>
        </div>
        <div class="topbar-right">
            <div class="date-pill">
                <i class="fas fa-calendar-days"></i>
                {{ now()->format('M j, Y') }}
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
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <div class="card-hdr">
                <span class="card-title"><i class="fas fa-square-poll-vertical" style="color:var(--gold);margin-right:8px;"></i>Active Opinion Surveys</span>
            </div>
            <div class="card-body">
                @forelse($surveys as $survey)
                <div class="survey-card">
                    <h3 class="survey-question">{{ $survey->question }}</h3>
                    
                    @if($survey->has_voted)
                    <!-- Voted: Display Results -->
                    @php
                        $totalVotes = $survey->options->sum('votes');
                    @endphp
                    <div style="display:flex; flex-direction:column; gap:15px; margin-top:10px;">
                        <span style="font-size:11px; color:var(--green); font-weight:700; display:flex; align-items:center; gap:5px;">
                            <i class="fas fa-check-circle"></i> You have voted on this survey
                        </span>
                        @foreach($survey->options as $option)
                        @php
                            $percent = $totalVotes > 0 ? round(($option->votes / $totalVotes) * 100, 1) : 0;
                        @endphp
                        <div class="result-item">
                            <div class="result-label">
                                <span>{{ $option->option_text }}</span>
                                <span>{{ $percent }}% ({{ $option->votes }} votes)</span>
                            </div>
                            <div class="progress-container">
                                <div class="progress-bar" style="width: {{ $percent }}%;"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <!-- Not voted: Display Radio Form -->
                    <form method="POST" action="{{ route('parent.surveys.vote', $survey->id) }}">
                        @csrf
                        <div class="survey-options">
                            @foreach($survey->options as $option)
                            <label class="option-label">
                                <input type="radio" name="option_id" value="{{ $option->id }}" required style="accent-color:var(--gold);">
                                {{ $option->option_text }}
                            </label>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-top:15px;">
                            <i class="fas fa-vote-yea"></i> Cast My Vote
                        </button>
                    </form>
                    @endif
                </div>
                @empty
                <div style="text-align:center; padding:60px; color:var(--t3);">
                    <i class="fas fa-poll" style="font-size:42px; display:block; margin-bottom:12px; color:var(--border);"></i>
                    No active opinion surveys are available at this time.
                </div>
                @endforelse
            </div>
        </div>

        <div class="footer">
            <span>© 2026 SchoolCloud ERP. All rights reserved.</span>
            <span>Version 2.0.0 &nbsp;|&nbsp; 🔒 Secure & Trusted</span>
        </div>
    </div>
</div>

<script>
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
    ['userDrop'].forEach(d=>{if(d!==id)document.getElementById(d).classList.remove('open');});
    document.getElementById(id).classList.toggle('open');
}
document.addEventListener('click',e=>{
    if(!e.target.closest('.user-wrap'))document.getElementById('userDrop').classList.remove('open');
});
</script>
</body>
</html>

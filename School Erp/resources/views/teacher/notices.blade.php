<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Notice Board — Teacher Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    *{margin:0;padding:0;box-sizing:border-box;}
    :root{
        --navy:#1a1f3c;--navy2:#12172e;
        --purple:#7c3aed;--purple-light:#f3e8ff;
        --blue:#2563eb;--blue-light:#eff6ff;
        --green:#10b981;--green-light:#ecfdf5;
        --orange:#f97316;--orange-light:#fff7ed;
        --red:#ef4444;--red-light:#fef2f2;
        --gold:#f59e0b;--gold-light:#fef3c7;
        --page:#f8fafc;--white:#ffffff;
        --t1:#0f172a;--t2:#475569;--t3:#94a3b8;
        --border:#e2e8f0;
        --shadow:0 1px 3px rgba(0,0,0,.05);
        --shadow-lg:0 8px 32px rgba(0,0,0,.08);
    }
    body{font-family:'Inter',sans-serif;background:var(--page);color:var(--t1);display:flex;min-height:100vh;overflow-x:hidden;}

    /* ─── SIDEBAR ─────────────────────────────────────────────── */
    .sidebar{
        width:240px;min-width:240px;background:var(--navy);
        height:100vh;position:fixed;left:0;top:0;
        display:flex;flex-direction:column;z-index:200;
        overflow-y:auto;overflow-x:hidden;
    }
    .sb-profile{
        padding:24px 18px;display:flex;align-items:center;gap:12px;
        border-bottom:1px solid rgba(255,255,255,.05);flex-shrink:0;
    }
    .sb-avatar{
        width:42px;height:42px;border-radius:12px;
        background:linear-gradient(135deg,var(--purple),#c084fc);
        display:flex;align-items:center;justify-content:center;
        color:#fff;font-weight:800;font-size:15px;flex-shrink:0;
    }
    .sb-prof-info h4{color:#fff;font-size:13px;font-weight:700;margin-bottom:2px;}
    .sb-prof-info p{color:rgba(255,255,255,.4);font-size:10px;margin-bottom:3px;}
    .sb-prof-badge{
        display:inline-block;padding:2px 8px;border-radius:4px;
        background:rgba(124,58,237,.15);color:#c084fc;font-size:9px;font-weight:700;
    }
    .sb-nav{list-style:none;padding:16px 0;flex:1;overflow-y:auto;overflow-x:hidden;}
    .sb-grp-title{padding:0 18px 6px;font-size:9.5px;color:rgba(255,255,255,.3);font-weight:700;text-transform:uppercase;letter-spacing:.8px;}
    .sb-item{
        display:flex;align-items:center;gap:12px;padding:10px 18px;
        color:rgba(255,255,255,.6);text-decoration:none;font-size:12.5px;font-weight:600;
        transition:.15s;
    }
    .sb-item:hover{background:rgba(255,255,255,.03);color:#fff;}
    .sb-item.active{background:rgba(124,58,237,.12);color:#c084fc;border-left:3px solid var(--purple);}
    .sb-item i{width:16px;text-align:center;font-size:14px;}
    .sb-sub-list{display:flex;flex-direction:column;padding:2px 0 10px;}
    .sb-sub-link{
        display:flex;align-items:center;justify-content:space-between;
        padding:8px 18px 8px 30px;color:rgba(255,255,255,.45);
        font-size:11.5px;text-decoration:none;transition:.15s;font-weight:500;
    }
    .sb-sub-link:hover{color:#fff;background:rgba(255,255,255,.02);}
    .sb-sub-link-left{display:flex;align-items:center;gap:8px;}
    .sb-sub-link i.dot{font-size:6px;opacity:.5;}
    .sb-logout{padding:14px 18px;border-top:1px solid rgba(255,255,255,.05);flex-shrink:0;}
    .btn-logout{
        display:flex;align-items:center;gap:10px;color:rgba(255,255,255,.4);
        font-size:12.5px;text-decoration:none;transition:.15s;font-weight:600;
    }
    .btn-logout:hover{color:#ef4444;}

    /* ─── MAIN ────────────────────────────────────────────────── */
    .main-wrapper{margin-left:240px;flex:1;display:flex;flex-direction:column;min-height:100vh;}

    /* ─── TOPBAR ─────────────────────────────────────────────── */
    .top-header{
        background:var(--white);border-bottom:1px solid var(--border);
        height:68px;padding:0 24px;
        display:flex;align-items:center;justify-content:space-between;
        position:sticky;top:0;z-index:100;
        box-shadow:var(--shadow);
    }
    .th-title h2{font-family:'Plus Jakarta Sans',sans-serif;font-size:16px;font-weight:800;color:var(--t1);line-height:1.2;}
    .th-title p{font-size:11.5px;color:var(--t2);}
    .th-actions{display:flex;align-items:center;gap:12px;}
    .th-user-pill{
        display:flex;align-items:center;gap:10px;padding:6px 12px;
        background:var(--page);border:1px solid var(--border);border-radius:12px;
    }
    .th-user-img{
        width:28px;height:28px;border-radius:8px;
        background:rgba(124,58,237,.1);color:var(--purple);
        display:flex;align-items:center;justify-content:center;
        font-size:11px;font-weight:800;
    }
    .th-user-info h5{font-size:11.5px;font-weight:700;color:var(--t1);}
    .th-user-info p{font-size:9.5px;color:var(--t3);}

    /* ─── PAGE ────────────────────────────────────────────────── */
    .content-area{padding:24px;}

    /* CARD */
    .card{
        background:var(--white);border:1px solid var(--border);
        border-radius:16px;box-shadow:var(--shadow);overflow:hidden;
        margin-bottom:20px;
    }
    .card-hdr{
        padding:18px 24px;
        display:flex;align-items:center;justify-content:space-between;
        border-bottom:1px solid var(--border);
    }
    .card-title{font-size:14.5px;font-weight:800;color:var(--t1);font-family:'Plus Jakarta Sans',sans-serif;}
    .card-body{padding:24px;}

    /* NOTICE BOX ELEMENT */
    .notice-box{
        padding:20px; border:1px solid var(--border); border-radius:12px;
        background:var(--page); margin-bottom:16px; display:flex; flex-direction:column; gap:10px;
        transition:transform .2s, box-shadow .2s;
    }
    .notice-box:hover{transform:translateY(-1px); box-shadow:0 4px 12px rgba(0,0,0,.03);}
    .notice-hdr{display:flex; justify-content:space-between; align-items:flex-start;}
    .notice-title{font-size:15px; font-weight:850; color:var(--navy); font-family:'Plus Jakarta Sans',sans-serif;}
    .notice-date{font-size:11px; color:var(--t3); font-weight:600;}
    .notice-content{font-size:13px; color:var(--t2); line-height:1.6; white-space:pre-line;}

    .footer{display:flex;align-items:center;justify-content:space-between;padding:16px 0 6px;border-top:1px solid var(--border);font-size:10.5px;color:var(--t3);}
</style>
</head>
<body>

    <!-- ══════════ SIDEBAR ══════════ -->
    <aside class="sidebar">
        <!-- Sidebar Profile -->
        <div class="sb-profile">
            <div class="sb-avatar">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="sb-prof-info">
                <h4>{{ $user->name }}</h4>
                <p>{{ $staff?->employee_id ?? 'EMP-TEACHER' }}</p>
                <span class="sb-prof-badge">{{ $staff?->designation?->name ?? 'Mathematics Teacher' }}</span>
            </div>
        </div>

        <!-- Sidebar Nav -->
        <div class="sb-nav">
            <div class="sb-grp-title">Core Navigation</div>
            <a href="{{ route('teacher.dashboard') }}" class="sb-item">
                <i class="fas fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>

            @if(count($accessibleModules) > 0)
                <div class="sb-grp-title" style="margin-top:12px;">Granted Modules</div>
                @foreach($accessibleModules as $mKey => $m)
                    <div class="sb-grp-title" style="color:var(--gold);text-transform:none;font-size:12px;">
                        <span>{{ $m['label'] }}</span>
                    </div>
                    <div class="sb-sub-list">
                        @foreach($m['features'] as $fKey => $fData)
                            <a href="{{ $fData['url'] }}" class="sb-sub-link" title="Open {{ $fData['label'] }}">
                                <div class="sb-sub-link-left">
                                    <i class="fas fa-circle dot"></i>
                                    <span>{{ $fData['label'] }}</span>
                                </div>
                                <i class="fas fa-chevron-right" style="font-size:9px;opacity:.5;"></i>
                            </a>
                        @endforeach
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Logout -->
        <div class="sb-logout">
            <a href="{{ route('logout') }}" class="btn-logout">
                <i class="fas fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- ─── MAIN CONTENT WRAPPER ────────────────────────────────── -->
    <div class="main-wrapper">
        <!-- Top Header -->
        <header class="top-header">
            <div class="th-title">
                <h2>Announcements & Notice Board</h2>
                <p>Read notices published by the school administration</p>
            </div>
            <div class="th-actions">
                <div class="th-user-pill">
                    <div class="th-user-img">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <div class="th-user-info">
                        <h5>{{ $user->name }}</h5>
                        <p>{{ $staff?->designation?->name ?? 'Teacher' }}</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- PAGE -->
        <main class="content-area">
            <div class="card">
                <div class="card-hdr">
                    <span class="card-title"><i class="fas fa-bullhorn" style="color:var(--purple);margin-right:8px;"></i>Important Circulars & Announcements</span>
                </div>
                <div class="card-body">
                    @forelse($notices as $notice)
                    <div class="notice-box">
                        <div class="notice-hdr">
                            <h4 class="notice-title">{{ $notice->title }}</h4>
                            <span class="notice-date"><i class="far fa-clock"></i> {{ $notice->created_at ? $notice->created_at->format('M d, Y') : '-' }}</span>
                        </div>
                        <p class="notice-content">{{ $notice->content }}</p>
                    </div>
                    @empty
                    <div style="text-align:center; padding:60px; color:var(--t3);">
                        <i class="fas fa-bullhorn" style="font-size:42px; display:block; margin-bottom:12px; color:var(--border);"></i>
                        No circulars or notice board entries have been published.
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="footer">
                <span>© 2026 SchoolCloud ERP. All rights reserved.</span>
                <span>Version 2.0.0 &nbsp;|&nbsp; 🔒 Secure & Trusted</span>
            </div>
        </main>
    </div>
</body>
</html>

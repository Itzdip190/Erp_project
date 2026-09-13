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

    /* ─── SIDEBAR & NAV COMPONENT STYLING ─────────────────────── */
    .sidebar-overlay{
        position:fixed;top:0;left:0;right:0;bottom:0;width:100vw;height:100vh;height:100dvh;
        background:rgba(15,23,42,0.65);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);
        z-index:1004;opacity:0;visibility:hidden;pointer-events:none;
        transition:opacity .32s cubic-bezier(0.16,1,0.3,1),visibility .32s ease;cursor:pointer;
    }
    .sidebar-overlay.active{opacity:1;visibility:visible;pointer-events:auto;}
    .sidebar{
        width:260px;min-width:260px;background:var(--navy);
        height:100vh;position:fixed;left:0;top:0;
        display:flex;flex-direction:column;z-index:200;
        overflow-y:auto;overflow-x:hidden;box-shadow:4px 0 20px rgba(0,0,0,0.15);
    }
    .sb-logo{
        padding:18px 16px;display:flex;align-items:center;justify-content:space-between;
        border-bottom:1px solid rgba(255,255,255,.08);text-decoration:none;color:#fff;
    }
    .sb-logo-left{display:flex;align-items:center;gap:12px;text-decoration:none;color:#fff;min-width:0;flex:1;}
    .sb-logo-icon{
        width:38px;height:38px;background:linear-gradient(135deg,var(--blue),#1d4ed8);
        border-radius:10px;display:flex;align-items:center;justify-content:center;
        font-size:18px;color:#fff;box-shadow:0 2px 8px rgba(37,99,235,.4);flex-shrink:0;overflow:hidden;
    }
    .sb-logo-text strong{display:block;font-family:'Plus Jakarta Sans',sans-serif;font-size:14.5px;font-weight:800;letter-spacing:-.3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .sb-logo-text span{font-size:11px;color:rgba(255,255,255,.5);font-weight:500;}
    .sb-close-btn{
        display:none;width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,0.1);
        color:#ffffff;border:1px solid rgba(255,255,255,0.18);align-items:center;justify-content:center;
        cursor:pointer;font-size:13px;transition:all .2s ease;flex-shrink:0;margin-left:8px;
    }
    .sb-close-btn:active{transform:scale(0.92);background:rgba(255,255,255,0.22);}

    .sb-profile{
        padding:14px;display:flex;align-items:center;gap:12px;
        border-bottom:1px solid rgba(255,255,255,.08);flex-shrink:0;
    }
    .sb-avatar{
        width:40px;height:40px;border-radius:12px;
        background:linear-gradient(135deg,var(--purple),#c084fc);
        display:flex;align-items:center;justify-content:center;
        color:#fff;font-weight:800;font-size:15px;flex-shrink:0;
    }
    .sb-prof-info h4{color:#fff;font-size:13px;font-weight:700;margin-bottom:2px;}
    .sb-prof-info p{color:rgba(255,255,255,.5);font-size:11px;margin-bottom:3px;}
    .sb-prof-badge{
        display:inline-block;padding:2px 8px;border-radius:4px;
        background:rgba(124,58,237,.15);color:#c084fc;font-size:9.5px;font-weight:700;
    }
    /* sidebar_nav Styling */
    .sb-nav{padding:8px 10px;flex:1;overflow-y:auto;overflow-x:hidden;}
    .sb-search-wrapper input{background:rgba(255,255,255,0.06) !important;color:#fff !important;border-color:rgba(255,255,255,0.12) !important;}
    .sb-search-wrapper input::placeholder{color:rgba(255,255,255,0.4) !important;}
    .sb-search-box i{color:rgba(255,255,255,0.4) !important;}
    
    .sb-group{margin-bottom:4px;border-bottom:none !important;}
    .sb-hdr{
        display:flex;align-items:center;justify-content:space-between;
        padding:9px 12px;cursor:pointer;user-select:none;
        color:rgba(255,255,255,0.82) !important;transition:all .2s;border-radius:10px;
        margin:2px 0;
    }
    .sb-hdr:hover{background:rgba(255,255,255,0.08) !important;color:#fff !important;}
    .sb-hdr-left{display:flex;align-items:center;gap:10px;}
    .sb-hdr-icon{
        width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,0.08) !important;
        display:flex;align-items:center;justify-content:center;
        color:rgba(255,255,255,0.85) !important;font-size:12px;flex-shrink:0;
    }
    .sb-hdr-icon svg, .sb-hdr-icon .m3d-icon{width:28px;height:28px;}
    .sb-hdr-title{font-family:'Plus Jakarta Sans',sans-serif;color:inherit;font-size:13px;font-weight:700;}
    .sb-hdr-arrow{font-size:10px;color:rgba(255,255,255,0.4) !important;transition:transform .2s;}
    .sb-hdr.open .sb-hdr-arrow{transform:rotate(180deg);color:var(--gold) !important;}

    .sb-submenu{list-style:none;padding:2px 0 4px 14px;}
    .sb-submenu li{margin-bottom:2px;}
    .sb-submenu a{
        display:flex;align-items:center;justify-content:space-between;
        padding:7px 10px;border-radius:8px;
        color:rgba(255,255,255,0.75) !important;font-size:12.5px;font-weight:500;
        text-decoration:none !important;transition:all .18s;
    }
    .sb-submenu a:hover{color:#fff !important;background:rgba(255,255,255,0.1) !important;}
    .sb-submenu li.active a{color:#fff !important;background:linear-gradient(90deg, #7c3aed, #6d28d9) !important;font-weight:700;box-shadow:0 2px 8px rgba(124,58,237,.3);}
    .sb-item.active{background:rgba(124,58,237,.12);color:#c084fc;border-left:3px solid var(--purple);}
    .sb-item i{width:16px;text-align:center;font-size:14px;}
    .sb-logout{padding:14px 18px;border-top:1px solid rgba(255,255,255,.05);flex-shrink:0;}
    .btn-logout{
        display:flex;align-items:center;justify-content:center;gap:10px;color:#fca5a5;
        font-size:12.5px;text-decoration:none;transition:.15s;font-weight:700;background:rgba(239,68,68,0.12);
        padding:10px;border-radius:10px;border:1px solid rgba(239,68,68,0.2);
    }
    .btn-logout:hover{background:#ef4444;color:#fff;}

    .hamburger-btn{
        display:none;align-items:center;justify-content:center;width:38px;height:38px;
        background:rgba(26,31,60,.06);border:1px solid var(--border);border-radius:10px;
        color:var(--t1);font-size:16px;cursor:pointer;margin-right:12px;transition:all 0.2s;
    }
    .hamburger-btn:hover{background:var(--purple-light);color:var(--purple);}

    /* ─── MOBILE ICON-BASED APP GRID SIDEBAR (< 991px) ───────── */
    @media (max-width: 991px) {
        .sidebar{
            position:fixed !important;top:0 !important;left:0 !important;bottom:0 !important;
            width:100vw !important;max-width:100vw !important;height:100vh !important;height:100dvh !important;
            z-index:1005 !important;transform:translateX(-100%) !important;
            display:flex !important;flex-direction:column !important;
            background:linear-gradient(180deg, #10162f 0%, #171d3d 100%) !important;
            box-shadow:none !important;transition:transform 0.36s cubic-bezier(0.16, 1, 0.3, 1) !important;
            overflow:hidden !important;
        }
        .sidebar.open{transform:translateX(0) !important;}
        
        .sb-logo{
            padding:16px 18px 14px 18px !important;
            background:linear-gradient(135deg, #1e1b4b 0%, #2e1065 50%, #4c1d95 100%) !important;
            border-bottom:1px solid rgba(255,255,255,0.12) !important;
            border-bottom-left-radius:24px !important;border-bottom-right-radius:24px !important;
            box-shadow:0 8px 24px rgba(124, 58, 237, 0.25) !important;
            flex-shrink:0 !important;
        }
        .sb-close-btn{display:flex !important;}
        .sb-logo-icon{width:42px !important;height:42px !important;border-radius:12px !important;}
        .sb-logo-text strong{font-size:15px !important;color:#fff !important;}
        .sb-logo-text span{font-size:11px !important;color:rgba(255,255,255,0.7) !important;}

        .sb-profile{
            margin:12px 14px 4px 14px !important;padding:12px 14px !important;
            background:rgba(255,255,255,0.05) !important;border:1px solid rgba(255,255,255,0.1) !important;
            border-radius:16px !important;box-shadow:0 4px 14px rgba(0,0,0,0.15) !important;
        }
        .sb-avatar{width:42px !important;height:42px !important;border-radius:12px !important;font-size:16px !important;}

        .sb-search-wrapper{display:none !important;}
        .sb-teacher-return-wrap{grid-column:1 / -1 !important;margin:0 0 8px 0 !important;}

        /* 3-Column App Grid */
        .sb-nav{
            padding:10px 12px calc(30px + env(safe-area-inset-bottom, 20px)) 12px !important;
            flex:1 !important;overflow-y:auto !important;-webkit-overflow-scrolling:touch !important;
            display:grid !important;grid-template-columns:repeat(3, 1fr) !important;
            gap:10px 8px !important;align-content:start !important;
        }
        .sb-group{
            display:flex !important;flex-direction:column !important;
            align-items:center !important;justify-content:flex-start !important;
            text-align:center !important;margin:0 !important;padding:10px 6px !important;
            position:relative !important;background:rgba(255, 255, 255, 0.05) !important;
            border-radius:16px !important;border:1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow:0 4px 12px rgba(0, 0, 0, 0.15) !important;
            transition:transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), background 0.2s ease, border-color 0.2s ease !important;
            cursor:pointer !important;-webkit-tap-highlight-color:transparent !important;
        }
        .sb-group:active{
            transform:translateY(-1px) scale(0.96) !important;
            background:rgba(255, 255, 255, 0.12) !important;
            border-color:rgba(124, 58, 237, 0.4) !important;
        }
        .sb-hdr{
            display:flex !important;flex-direction:column !important;
            align-items:center !important;justify-content:center !important;
            text-align:center !important;padding:0 !important;margin:0 !important;
            min-height:unset !important;background:transparent !important;
            border:none !important;box-shadow:none !important;width:100% !important;
            cursor:pointer !important;-webkit-tap-highlight-color:transparent !important;
        }
        .sb-hdr-left{
            display:flex !important;flex-direction:column !important;
            align-items:center !important;justify-content:center !important;
            gap:5px !important;width:100% !important;
        }
        .sb-hdr-icon{
            width:58px !important;height:58px !important;min-width:58px !important;min-height:58px !important;
            border-radius:16px !important;background:transparent !important;border:none !important;
            box-shadow:none !important;display:flex !important;align-items:center !important;
            justify-content:center !important;flex-shrink:0 !important;
            transition:transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
            filter:drop-shadow(0 6px 12px rgba(0, 0, 0, 0.25)) !important;
        }
        .sb-hdr-icon svg, .sb-hdr-icon .m3d-icon, .sb-hdr-icon i{
            width:58px !important;height:58px !important;line-height:58px !important;display:block !important;
        }
        .sb-group:hover .sb-hdr-icon, .sb-group:active .sb-hdr-icon{transform:translateY(-2px) scale(1.06) !important;}
        .sb-hdr-title{
            font-family:'Plus Jakarta Sans', 'Inter', system-ui, sans-serif !important;
            font-size:11.5px !important;font-weight:700 !important;color:#f1f5f9 !important;
            text-align:center !important;line-height:1.25 !important;margin-top:6px !important;
            max-width:100% !important;overflow:hidden !important;text-overflow:ellipsis !important;
            display:-webkit-box !important;-webkit-line-clamp:2 !important;-webkit-box-orient:vertical !important;
            letter-spacing:-0.2px !important;
        }
        .sb-hdr-arrow{display:none !important;}
        .sb-submenu{
            display:none !important;height:0 !important;max-height:0 !important;opacity:0 !important;
            visibility:hidden !important;pointer-events:none !important;margin:0 !important;padding:0 !important;
            overflow:hidden !important;
        }

        .sb-group.active-group{
            border-color:rgba(124, 58, 237, 0.8) !important;
            background:linear-gradient(180deg, rgba(124, 58, 237, 0.2) 0%, rgba(30, 27, 75, 0.6) 100%) !important;
            box-shadow:0 8px 24px rgba(124, 58, 237, 0.35) !important;
        }
        .sb-group.active-group::after{
            content:'';position:absolute;top:7px;right:7px;width:8px;height:8px;
            border-radius:50%;background:#10b981;box-shadow:0 0 8px #10b981;
        }
        .sb-group.active-group .sb-hdr-title{color:#c4b5fd !important;font-weight:800 !important;}

        .main-wrapper{margin-left:0 !important;}
        .hamburger-btn{display:inline-flex !important;}
        .top-header{padding:10px 14px;height:auto;flex-direction:row;align-items:center;justify-content:space-between;flex-wrap:nowrap;gap:8px;}
        .th-actions{width:auto;justify-content:flex-end;gap:6px;flex-shrink:0;}
        .th-title h2{font-size:14px;white-space:nowrap;max-width:160px;overflow:hidden;text-overflow:ellipsis;}
        .th-title p{display:none;}
        .th-user-info{display:none;}
        .th-user-pill{padding:2px;background:transparent;border:none;}
        .content-area{padding:12px 10px;}
    }

    /* ─── MODULE SUB-PAGES POPUP MODAL STYLES ───────────────── */
    .sb-module-popup-modal{
        position:fixed !important;top:0 !important;left:0 !important;right:0 !important;bottom:0 !important;
        width:100vw !important;height:100vh !important;height:100dvh !important;
        background:rgba(15, 23, 42, 0.72) !important;
        -webkit-backdrop-filter:blur(8px) !important;backdrop-filter:blur(8px) !important;
        z-index:100050 !important;display:flex !important;align-items:flex-end !important;
        justify-content:center !important;opacity:0 !important;visibility:hidden !important;
        pointer-events:none !important;transition:opacity 0.28s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.28s ease !important;
    }
    .sb-module-popup-modal.active{opacity:1 !important;visibility:visible !important;pointer-events:auto !important;}
    .sb-mpm-card{
        width:100% !important;max-width:460px !important;
        background:linear-gradient(180deg, #1f2244 0%, #151833 100%) !important;
        border:1px solid rgba(255, 255, 255, 0.12) !important;
        border-top-left-radius:24px !important;border-top-right-radius:24px !important;
        box-shadow:0 -10px 40px rgba(0, 0, 0, 0.5) !important;
        padding:20px 18px 28px 18px !important;transform:translateY(100%) !important;
        transition:transform 0.32s cubic-bezier(0.16, 1, 0.3, 1) !important;
        max-height:calc(100vh - 80px) !important;max-height:calc(100dvh - 80px) !important;
        margin-bottom:max(10px, env(safe-area-inset-bottom)) !important;
        display:flex !important;flex-direction:column !important;
    }
    .sb-module-popup-modal.active .sb-mpm-card{transform:translateY(0) !important;}
    .sb-mpm-header{
        display:flex !important;align-items:center !important;justify-content:space-between !important;
        padding-bottom:14px !important;border-bottom:1px solid rgba(255, 255, 255, 0.1) !important;
        margin-bottom:14px !important;
    }
    .sb-mpm-header-left{display:flex !important;align-items:center !important;gap:12px !important;}
    .sb-mpm-icon{
        width:44px !important;height:44px !important;border-radius:12px !important;
        background:linear-gradient(135deg, #7c3aed, #4f46e5) !important;color:#ffffff !important;
        display:flex !important;align-items:center !important;justify-content:center !important;
        font-size:20px !important;box-shadow:0 4px 12px rgba(124, 58, 237, 0.4) !important;
    }
    .sb-mpm-icon svg, .sb-mpm-icon .m3d-icon{width:44px !important;height:44px !important;}
    .sb-mpm-title{font-size:16px !important;font-weight:800 !important;color:#ffffff !important;font-family:'Plus Jakarta Sans', sans-serif !important;}
    .sb-mpm-close{
        width:34px !important;height:34px !important;border-radius:50% !important;
        background:rgba(255, 255, 255, 0.1) !important;border:1px solid rgba(255, 255, 255, 0.15) !important;
        color:#ffffff !important;display:flex !important;align-items:center !important;
        justify-content:center !important;cursor:pointer !important;font-size:14px !important;
        transition:all 0.2s ease !important;
    }
    .sb-mpm-close:active{transform:scale(0.92) !important;background:rgba(255, 255, 255, 0.2) !important;}
    .sb-mpm-body{
        overflow-y:auto !important;-webkit-overflow-scrolling:touch !important;
        display:flex !important;flex-direction:column !important;gap:8px !important;
        padding-bottom:16px !important;
    }
    .sb-mpm-item{
        display:flex !important;align-items:center !important;justify-content:space-between !important;
        padding:12px 16px !important;border-radius:14px !important;
        background:rgba(255, 255, 255, 0.06) !important;border:1px solid rgba(255, 255, 255, 0.08) !important;
        color:#ffffff !important;font-size:13.5px !important;font-weight:700 !important;
        text-decoration:none !important;transition:all 0.18s ease !important;
    }
    .sb-mpm-item:hover, .sb-mpm-item:active{
        background:linear-gradient(135deg, #7c3aed, #4f46e5) !important;
        color:#ffffff !important;border-color:rgba(255, 255, 255, 0.2) !important;
        transform:translateX(3px) !important;
    }
    .sb-mpm-item.active{
        background:linear-gradient(135deg, #7c3aed, #6d28d9) !important;
        color:#ffffff !important;border-color:#f59e0b !important;
    }
    .sb-mpm-item i{font-size:12px !important;color:#a78bfa !important;}
    .sb-mpm-item:hover i, .sb-mpm-item:active i{color:#ffffff !important;}

    @media (max-width: 360px) {
        .sb-nav{grid-template-columns:repeat(3, minmax(0, 1fr)) !important;gap:8px 4px !important;}
        .sb-hdr-icon{width:46px !important;height:46px !important;min-width:46px !important;min-height:46px !important;}
        .sb-hdr-icon svg, .sb-hdr-icon .m3d-icon, .sb-hdr-icon i{width:46px !important;height:46px !important;}
        .sb-hdr-title{font-size:10px !important;}
    }

    /* ─── MAIN ────────────────────────────────────────────────── */
    .main-wrapper{margin-left:260px;flex:1;display:flex;flex-direction:column;min-height:100vh;}

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

    <!-- Sidebar Overlay for mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleTeacherSidebar()"></div>

    <!-- ══════════ SIDEBAR ══════════ -->
    <aside class="sidebar" id="teacherSidebar">
        <!-- Sidebar Brand Header -->
        <div class="sb-logo">
            <a href="{{ route('teacher.dashboard') }}" class="sb-logo-left" title="Teacher Dashboard">
                <div class="sb-logo-icon">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div class="sb-logo-text">
                    <strong>Notice Board</strong>
                    <span>Teacher Portal</span>
                </div>
            </a>
            <button type="button" class="sb-close-btn" onclick="toggleTeacherSidebar(event)" aria-label="Close sidebar">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <!-- Sidebar Profile -->
        <div class="sb-profile">
            <div class="sb-avatar" style="overflow:hidden;padding:0;">
                @php $tAvatar = $teacherAvatarUrl ?? $user->photo_url ?? $staff?->photo_url ?? null; @endphp
                @if(!empty($tAvatar))
                    <img src="{{ $tAvatar }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:12px;" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                    <span style="display:none;width:100%;height:100%;align-items:center;justify-content:center;">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @else
                    <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @endif
            </div>
            <div class="sb-prof-info">
                <h4>{{ $user->name }}</h4>
                <p>{{ $staff?->employee_id ?? 'EMP-TEACHER' }}</p>
                <span class="sb-prof-badge">{{ $staff?->designation?->name ?? 'Teacher' }}</span>
            </div>
        </div>

        <!-- Single Centralized Nav Component -->
        @include('layouts.sidebar_nav')

        <!-- Logout -->
        <div class="sb-logout">
            <a href="{{ route('logout') }}" class="btn-logout">
                <i class="fas fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>

        <!-- Mobile Module Sub-Pages Popup Modal (<991px) -->
        <div id="sbModulePopupModal" class="sb-module-popup-modal" onclick="closeModulePopupModal(event)">
            <div class="sb-mpm-card" onclick="event.stopPropagation()">
                <div class="sb-mpm-header">
                    <div class="sb-mpm-header-left">
                        <div class="sb-mpm-icon" id="sbMpmIcon"><i class="fas fa-cubes"></i></div>
                        <div class="sb-mpm-title" id="sbMpmTitle">Module Pages</div>
                    </div>
                    <button type="button" class="sb-mpm-close" onclick="closeModulePopupModal(event)" aria-label="Close">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
                <div class="sb-mpm-body" id="sbMpmBody">
                    <!-- Dynamically populated sub-module pages list/grid -->
                </div>
            </div>
        </div>
    </aside>

    <!-- ─── MAIN CONTENT WRAPPER ────────────────────────────────── -->
    <div class="main-wrapper">
        <!-- Top Header -->
        <header class="top-header">
            <div style="display:flex;align-items:center;">
                <button type="button" class="hamburger-btn" onclick="toggleTeacherSidebar()" aria-label="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="th-title">
                    <h2>Announcements & Notice Board</h2>
                    <p>Read notices published by the school administration</p>
                </div>
            </div>
            <div class="th-actions">
                <!-- DIRECT LOGOUT ICON BUTTON -->
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('teacher-notices-logout-form').submit();" class="th-icon-btn th-logout-btn" title="Logout" style="width:38px;height:38px;border-radius:50%;background:rgba(239,68,68,0.1);color:#ef4444;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;">
                    <i class="fas fa-power-off"></i>
                </a>
                <form id="teacher-notices-logout-form" action="{{ route('logout.post') }}" method="POST" style="display: none;">
                    @csrf
                </form>

                <a href="{{ route('teacher.dashboard') }}" class="th-user-pill" style="text-decoration:none;">
                    <div class="th-user-img" style="overflow:hidden;">
                        @if(!empty($tAvatar))
                            <img src="{{ $tAvatar }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                            <span style="display:none;width:100%;height:100%;align-items:center;justify-content:center;">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @else
                            <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="th-user-info">
                        <h5>{{ $user->name }}</h5>
                        <p>{{ $staff?->designation?->name ?? 'Teacher' }}</p>
                    </div>
                </a>
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
                <span>© 2026 EducorERP. All rights reserved.</span>
                <span>Version 2.0.0 &nbsp;|&nbsp; 🔒 Secure & Trusted</span>
            </div>
        </main>
    </div>

    <script>
        function toggleTeacherSidebar(e) {
            if (e && e.preventDefault) { e.preventDefault(); e.stopPropagation(); }
            const sidebar = document.getElementById('teacherSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (!sidebar) return;

            const isOpen = sidebar.classList.toggle('open');
            if (overlay) overlay.classList.toggle('active', isOpen);
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

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.sb-group').forEach(group => {
                group.addEventListener('click', function(e) {
                    if (e.target.closest('a') || e.target.closest('.sb-submenu li') || e.target.closest('#sbModulePopupModal')) {
                        return;
                    }
                    if (window.innerWidth <= 991) {
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
                                    const icon = iconElSub ? iconElSub.outerHTML : '<i class="fas fa-arrow-up-right-from-square"></i>';
                                    const isActive = link.closest('li')?.classList.contains('active') ? 'active' : '';
                                    itemsHtml += `
                                        <a href="${href}" class="sb-mpm-item ${isActive}" onclick="toggleTeacherSidebar(); closeModulePopupModal();">
                                            <span>${label}</span>
                                            ${icon}
                                        </a>
                                    `;
                                });
                                openModulePopupModal(iconHtml, titleText, itemsHtml);
                                return;
                            }
                        }
                    } else {
                        const submenu = this.querySelector('.sb-submenu');
                        const hdr = this.querySelector('.sb-hdr');
                        if (submenu) {
                            const isOpen = submenu.classList.toggle('open');
                            if (hdr) hdr.classList.toggle('open', isOpen);
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>

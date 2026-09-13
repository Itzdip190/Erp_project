<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Teacher Portal — EducorERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
            --shadow:0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
            --shadow-lg:0 10px 25px -5px rgba(0,0,0,0.08), 0 8px 10px -6px rgba(0,0,0,0.01);
        }
        body{font-family:'Inter',sans-serif;background:var(--page);color:var(--t1);display:flex;min-height:100vh;overflow-x:hidden;}

        /* ─── SIDEBAR & NAV COMPONENT (sidebar_nav) STYLING ─────────────────────── */
        .sidebar-overlay{
            position:fixed;top:0;left:0;right:0;bottom:0;width:100vw;height:100vh;height:100dvh;
            background:rgba(15,23,42,0.65);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);
            z-index:1004;opacity:0;visibility:hidden;pointer-events:none;
            transition:opacity .32s cubic-bezier(0.16,1,0.3,1),visibility .32s ease;cursor:pointer;
        }
        .sidebar-overlay.active{opacity:1;visibility:visible;pointer-events:auto;}
        .sidebar{
            width:260px;min-width:260px;background:var(--navy);
            display:flex;flex-direction:column;color:#fff;position:sticky;top:0;height:100vh;overflow-y:auto;
            z-index:100;box-shadow:4px 0 20px rgba(0,0,0,0.15);transition:transform 0.3s ease;
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

        /* Profile Badge Box */
        .sb-profile{
            margin:14px;padding:12px 14px;background:rgba(255,255,255,.05);
            border:1px solid rgba(255,255,255,.1);border-radius:14px;display:flex;align-items:center;gap:12px;flex-shrink:0;
        }
        .sb-avatar{
            width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#7c3aed,#4c1d95);
            color:#fff;font-weight:800;font-size:15px;display:flex;align-items:center;justify-content:center;
            box-shadow:0 2px 8px rgba(124,58,237,.4);flex-shrink:0;
        }
        .sb-prof-info h4{font-size:13px;font-weight:700;color:#fff;line-height:1.2;margin-bottom:2px;}
        .sb-prof-info p{font-size:11px;color:rgba(255,255,255,.6);}
        .sb-prof-badge{
            display:inline-block;padding:2px 8px;background:rgba(245,158,11,.2);color:var(--gold);
            border-radius:20px;font-size:10px;font-weight:700;margin-top:3px;
        }

        /* sidebar_nav Styling (Desktop) */
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
        .sb-submenu-label{display:flex;align-items:center;gap:6px;}
        .sb-submenu-icon{font-size:10px;color:rgba(255,255,255,0.4);flex-shrink:0;}

        .sb-logout{padding:16px;border-top:1px solid rgba(255,255,255,.08);flex-shrink:0;}
        .btn-logout{
            display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:10px;
            background:rgba(239,68,68,.12);color:#fca5a5;border:1px solid rgba(239,68,68,.2);
            border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;transition:all .2s;
        }
        .btn-logout:hover{background:#ef4444;color:#fff;}

        /* Hamburger button */
        .hamburger-btn{
            display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;
            background:rgba(26,31,60,.06);border:1px solid var(--border);border-radius:10px;
            color:var(--t1);font-size:16px;cursor:pointer;margin-right:14px;transition:all 0.2s;
        }
        .hamburger-btn:hover{background:var(--purple-light);color:var(--purple);}

        /* Desktop Sidebar Close/Collapse Support */
        body.sidebar-closed .sidebar {
            display: none !important;
        }

        /* ─── MOBILE ICON-BASED APP GRID SIDEBAR (< 991px) ───────── */
        @media (max-width: 991px) {
            body.sidebar-closed .sidebar { display: flex !important; }
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

            .top-header{padding:10px 14px;height:auto;flex-direction:row;align-items:center;justify-content:space-between;flex-wrap:nowrap;gap:8px;}
            .th-actions{width:auto;justify-content:flex-end;gap:6px;flex-shrink:0;}
            .th-title h2{font-size:14px;white-space:nowrap;max-width:160px;overflow:hidden;text-overflow:ellipsis;}
            .th-title p{display:none;}
            .th-user-info{display:none;}
            .th-user-pill{padding:2px;background:transparent;border:none;}
            .th-user-img{width:30px;height:30px;font-size:12px;}
            .th-icon-btn{width:36px;height:36px;font-size:14px;}
            .th-date-btn, .th-export-btn{display:none !important;}
            .content-area{padding:12px 10px;}
            .metrics-5-grid{grid-template-columns:1fr;gap:10px;}
            .grid-3-col{grid-template-columns:1fr;gap:14px;}
            .followup-alert-box{max-width:100%;height:auto;padding:6px 10px;margin-top:2px;}
            .followup-slide{height:auto;flex-wrap:wrap;gap:4px;}
            .greeting-clock-wrap{margin-left:0;}
            .dash-card{padding:14px;}
            .qa-grid{grid-template-columns:1fr 1fr;}
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

        @media (max-width: 576px) {
            .th-title h2{max-width:130px;font-size:13px;}
            .hamburger-btn{margin-right:8px;width:34px;height:34px;font-size:14px;}
            .qa-grid{grid-template-columns:1fr;}
        }

        @media (max-width: 360px) {
            .sb-nav{grid-template-columns:repeat(3, minmax(0, 1fr)) !important;gap:8px 4px !important;}
            .sb-hdr-icon{width:46px !important;height:46px !important;min-width:46px !important;min-height:46px !important;}
            .sb-hdr-icon svg, .sb-hdr-icon .m3d-icon, .sb-hdr-icon i{width:46px !important;height:46px !important;}
            .sb-hdr-title{font-size:10px !important;}
        }

        @media (max-width: 768px) {
            .notif-dropdown-panel {
                position: fixed !important;
                top: 60px !important;
                left: 12px !important;
                right: 12px !important;
                width: auto !important;
                max-width: none !important;
                z-index: 1050 !important;
                box-shadow: 0 12px 36px rgba(0, 0, 0, 0.2) !important;
            }
        }

        /* ─── MAIN WRAPPER & HEADER ───────────────────────────────── */
        .main-wrapper{flex:1;display:flex;flex-direction:column;min-width:0;}
        
        .top-header{
            height:72px;background:#fff;border-bottom:1px solid var(--border);
            display:flex;align-items:center;justify-content:space-between;padding:0 32px;
            position:sticky;top:0;z-index:90;box-shadow:0 1px 3px rgba(0,0,0,0.02);
        }
        .th-title h2{font-family:'Plus Jakarta Sans',sans-serif;font-size:20px;font-weight:800;color:var(--t1);}
        .th-title p{font-size:13px;color:var(--t2);margin-top:2px;}
        
        .th-actions{display:flex;align-items:center;gap:14px;}
        .th-date-btn{
            background:#fff;border:1px solid var(--border);padding:8px 14px;border-radius:10px;
            font-size:13px;font-weight:600;color:var(--t2);display:flex;align-items:center;gap:8px;cursor:pointer;
        }
        .th-export-btn{
            background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;border:none;padding:9px 18px;
            border-radius:10px;font-size:13px;font-weight:700;display:flex;align-items:center;gap:8px;cursor:pointer;
            box-shadow:0 4px 12px rgba(99,102,241,0.25);transition:all .2s;
        }
        .th-export-btn:hover{opacity:.95;transform:translateY(-1px);}
        .th-icon-btn{
            width:42px;height:42px;border-radius:12px;background:#fff;border:1.5px solid var(--border);
            display:flex;align-items:center;justify-content:center;color:var(--t2);font-size:17px;
            position:relative;cursor:pointer;transition:all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            outline:none;
        }
        .th-icon-btn:hover{
            border-color:var(--purple);color:var(--purple);background:var(--purple-light);
            transform:translateY(-1px);
        }
        .th-icon-btn .badge-dot{
            position:absolute;top:8px;right:8px;width:9px;height:9px;background:var(--red);
            border-radius:50%;border:2px solid #fff;animation:pulseDot 2s infinite;
        }
        .th-icon-btn .badge-count{
            position:absolute;top:-5px;right:-5px;background:var(--red);color:#fff;
            font-size:10px;font-weight:800;padding:2px 6px;border-radius:10px;border:2px solid #fff;
            min-width:18px;text-align:center;line-height:1;box-shadow:0 2px 6px rgba(239,68,68,0.3);
        }
        @keyframes pulseDot {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* Notification Dropdown Panel */
        .notif-dropdown-panel {
            position: absolute;
            top: 54px;
            right: 0;
            width: 360px;
            max-width: 90vw;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            overflow: hidden;
            animation: notifSlide 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes notifSlide {
            from { opacity: 0; transform: translateY(-8px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .notif-header {
            padding: 14px 18px;
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .notif-header-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            font-weight: 800;
            color: var(--t1);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .notif-unread-pill {
            padding: 2px 8px;
            background: var(--purple-light);
            color: var(--purple);
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }
        .btn-mark-all-read {
            background: transparent;
            border: none;
            color: var(--purple);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: opacity 0.2s;
        }
        .btn-mark-all-read:hover {
            opacity: 0.8;
            text-decoration: underline;
        }

        .notif-body {
            max-height: 380px;
            overflow-y: auto;
        }
        .notif-item {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            cursor: pointer;
            transition: background 0.2s ease;
            position: relative;
        }
        .notif-item:last-child { border-bottom: none; }
        .notif-item:hover { background: #f8fafc; }
        .notif-item.unread {
            background: rgba(124, 58, 237, 0.03);
            border-left: 3px solid var(--purple);
        }
        .notif-item.read {
            background: #ffffff;
            border-left: 3px solid transparent;
            opacity: 0.85;
        }
        .notif-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        .notif-content { flex: 1; min-width: 0; }
        .notif-item-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--t1);
            margin-bottom: 2px;
            line-height: 1.2;
        }
        .notif-item-msg {
            font-size: 12px;
            color: var(--t2);
            line-height: 1.4;
            word-wrap: break-word;
        }
        .notif-item-time {
            font-size: 10.5px;
            color: var(--t3);
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .notif-unread-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--purple);
            flex-shrink: 0;
            margin-top: 5px;
        }
        .notif-empty-state {
            padding: 36px 20px;
            text-align: center;
            color: var(--t3);
        }
        .th-user-pill{
            display:flex;align-items:center;gap:10px;padding:4px 6px 4px 4px;background:#f8fafc;
            border:1px solid var(--border);border-radius:30px;
        }
        .th-user-img{
            width:34px;height:34px;border-radius:50%;background:#7c3aed;color:#fff;
            display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;
        }
        .th-user-info h5{font-size:13px;font-weight:700;color:var(--t1);line-height:1.1;}
        .th-user-info p{font-size:11px;color:var(--t3);}

        /* ── Direct Logout Icon & User Dropdown Styles ── */
        .th-logout-btn {
            background: rgba(239, 68, 68, 0.08) !important;
            border: 1px solid rgba(239, 68, 68, 0.2) !important;
            color: #ef4444 !important;
            transition: all 0.2s ease;
        }
        .th-logout-btn:hover {
            background: #ef4444 !important;
            color: #fff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
        .th-logout-btn i {
            color: #ef4444 !important;
        }
        .th-logout-btn:hover i {
            color: #fff !important;
        }

        .th-user-pill:hover {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
        }
        .th-user-pill.active {
            background: #eef2ff !important;
            border-color: #7c3aed !important;
        }
        .th-user-pill.active .th-user-chevron {
            transform: rotate(180deg);
            color: #7c3aed !important;
        }

        /* User Dropdown Panel */
        .teacher-user-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 280px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.05);
            z-index: 999;
            overflow: hidden;
            animation: tDdSlideDown 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes tDdSlideDown {
            from { opacity: 0; transform: translateY(-8px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .t-dd-header {
            padding: 16px;
            background: linear-gradient(135deg, #1a1f3c, #2a3158);
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .t-dd-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #7c3aed;
            color: #fff;
            font-weight: 800;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, 0.2);
            flex-shrink: 0;
        }
        .t-dd-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .t-dd-details {
            overflow: hidden;
        }
        .t-dd-details h6 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .t-dd-details span {
            display: block;
            font-size: 11.5px;
            color: rgba(255, 255, 255, 0.7);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .t-dd-role-badge {
            display: inline-flex !important;
            align-items: center;
            gap: 4px;
            margin-top: 4px;
            background: rgba(124, 58, 237, 0.3);
            color: #ddd6fe !important;
            font-size: 10.5px !important;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 600;
        }
        .t-dd-body {
            padding: 8px;
        }
        .t-dd-item {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 12px;
            border: none;
            background: transparent;
            cursor: pointer;
            text-decoration: none !important;
            color: #1e293b;
            text-align: left;
            transition: background 0.15s ease;
        }
        .t-dd-item:hover {
            background: #f8fafc;
        }
        .t-dd-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }
        .t-dd-text {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .t-dd-text strong {
            font-size: 13px;
            font-weight: 600;
        }
        .t-dd-text small {
            font-size: 11px;
            color: #64748b;
        }
        .t-dd-arrow {
            font-size: 12px;
            color: #94a3b8;
        }
        .t-dd-divider {
            height: 1px;
            background: #e2e8f0;
            margin: 6px 0;
        }

        /* Modals Styling */
        .t-modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .t-modal-dialog {
            width: 100%;
            max-width: 540px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: tModalPop 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes tModalPop {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .t-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .t-modal-title {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .t-modal-title i {
            font-size: 24px;
        }
        .t-modal-title h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
        }
        .t-modal-title p {
            margin: 2px 0 0 0;
            font-size: 12px;
            color: #64748b;
        }
        .t-modal-close {
            background: transparent;
            border: none;
            font-size: 24px;
            color: #64748b;
            cursor: pointer;
            line-height: 1;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s;
        }
        .t-modal-close:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .t-modal-tabs {
            display: flex;
            gap: 4px;
            padding: 10px 24px 0;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .t-tab-btn {
            padding: 10px 16px;
            border: none;
            background: transparent;
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .t-tab-btn:hover {
            color: #7c3aed;
        }
        .t-tab-btn.active {
            color: #7c3aed;
            border-bottom-color: #7c3aed;
        }
        .t-modal-body {
            padding: 24px;
            max-height: 75vh;
            overflow-y: auto;
        }
        .t-avatar-upload-box {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 16px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 16px;
            margin-bottom: 20px;
        }
        .t-avatar-preview {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #7c3aed;
            color: #fff;
            font-size: 32px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 3px solid #fff;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2);
            flex-shrink: 0;
        }
        .t-avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .t-avatar-upload-info h4 {
            margin: 0 0 4px;
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }
        .t-avatar-upload-info p {
            margin: 0 0 10px;
            font-size: 12px;
            color: #64748b;
        }
        .t-file-btn-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .t-file-name {
            font-size: 12px;
            color: #64748b;
        }
        .t-form-group {
            margin-bottom: 18px;
        }
        .t-form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }
        .t-input-pass-wrap {
            position: relative;
        }
        .t-input-pass-wrap input, .t-select-control {
            width: 100%;
            padding: 10px 40px 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 13.5px;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .t-select-control {
            padding-right: 14px;
        }
        .t-input-pass-wrap input:focus, .t-select-control:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
        }
        .t-toggle-pass {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
        }
        .t-toggle-pass:hover {
            color: #475569;
        }
        .t-modal-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
        }
        .t-btn {
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s;
        }
        .t-btn-secondary {
            background: #f1f5f9;
            color: #475569;
        }
        .t-btn-secondary:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
        .t-btn-purple {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.25);
        }
        .t-btn-purple:hover {
            box-shadow: 0 6px 16px rgba(124, 58, 237, 0.35);
            transform: translateY(-1px);
        }
        .t-btn-primary {
            background: #3b82f6;
            color: #fff;
        }

        /* Info Tab Grid */
        .t-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        .t-info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
        }
        .t-info-label {
            font-size: 11px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .t-info-val {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2px;
        }

        /* Settings Styles */
        .t-setting-section {
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .t-setting-section:last-child {
            border-bottom: none;
        }
        .t-setting-title {
            font-size: 13px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .t-theme-picker {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        .t-theme-opt {
            cursor: pointer;
            text-align: center;
        }
        .t-theme-opt input {
            display: none;
        }
        .t-theme-box {
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 16px;
            margin-bottom: 6px;
            border: 2px solid transparent;
            transition: all 0.15s;
        }
        .t-theme-box i {
            opacity: 0;
            transform: scale(0.6);
            transition: all 0.15s;
        }
        .t-theme-opt input:checked + .t-theme-box {
            border-color: #0f172a;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .t-theme-opt input:checked + .t-theme-box i {
            opacity: 1;
            transform: scale(1);
        }
        .t-theme-opt span {
            font-size: 11.5px;
            font-weight: 700;
            color: #475569;
        }
        .t-switch-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .t-switch-row strong {
            display: block;
            font-size: 13px;
            color: #1e293b;
        }
        .t-switch-row small {
            font-size: 11.5px;
            color: #64748b;
        }
        .t-toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
            flex-shrink: 0;
        }
        .t-toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .t-slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #cbd5e1;
            transition: 0.2s;
            border-radius: 24px;
        }
        .t-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.2s;
            border-radius: 50%;
        }
        .t-toggle-switch input:checked + .t-slider {
            background-color: #7c3aed;
        }
        .t-toggle-switch input:checked + .t-slider:before {
            transform: translateX(20px);
        }

        /* ─── CONTENT AREA ────────────────────────────────────────── */
        .content-area{padding:32px;flex:1;}

        /* Top 5 Metric Sparkline Grid */
        .metrics-5-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:18px;margin-bottom:24px;}
        @media(max-width:1400px){.metrics-5-grid{grid-template-columns:repeat(3,1fr);}}
        @media(max-width:900px){.metrics-5-grid{grid-template-columns:1fr;}}

        .spark-card{
            background:#fff;border-radius:16px;padding:20px;border:1px solid var(--border);
            box-shadow:var(--shadow);display:flex;flex-direction:column;justify-content:space-between;
            position:relative;overflow:hidden;
        }
        .sc-top{display:flex;align-items:center;gap:12px;margin-bottom:14px;}
        .sc-ico{
            width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;
        }
        .sc-label{font-size:12px;font-weight:700;color:var(--t2);}
        .sc-val{font-family:'Plus Jakarta Sans',sans-serif;font-size:26px;font-weight:800;color:var(--t1);line-height:1.1;}
        .sc-trend{display:flex;align-items:center;gap:6px;font-size:11.5px;font-weight:700;margin-top:10px;}
        .sc-sparkline{margin-top:12px;height:35px;width:100%;}

        /* Dashboard Rows Layout */
        .grid-3-col{display:grid;grid-template-columns:1fr 1fr 1fr;gap:24px;margin-bottom:24px;}
        @media(max-width:1200px){.grid-3-col{grid-template-columns:1fr;}}

        .dash-card{
            background:#fff;border-radius:20px;border:1px solid var(--border);padding:24px;
            box-shadow:var(--shadow);display:flex;flex-direction:column;justify-content:space-between;
        }
        .dc-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
        .dc-hdr h3{font-family:'Plus Jakarta Sans',sans-serif;font-size:16px;font-weight:800;color:var(--t1);}
        .dc-link{font-size:12px;font-weight:700;color:var(--purple);text-decoration:none;}
        .dc-select{padding:4px 10px;border-radius:8px;border:1px solid var(--border);font-size:12px;font-weight:600;color:var(--t2);outline:none;}

        /* Donut Chart Legend */
        .donut-legend{display:flex;flex-direction:column;gap:10px;margin-top:16px;}
        .leg-item{display:flex;align-items:center;justify-content:space-between;font-size:13px;}
        .leg-left{display:flex;align-items:center;gap:8px;font-weight:600;color:var(--t2);}
        .leg-dot{width:10px;height:10px;border-radius:50%;}
        .leg-val{font-weight:800;color:var(--t1);}

        /* Today Schedule List */
        .sched-list{display:flex;flex-direction:column;gap:14px;}
        .sched-item{display:flex;align-items:center;gap:14px;padding:12px;border-radius:12px;background:#f8fafc;border:1px solid #f1f5f9;}
        .sched-time{font-size:12px;font-weight:700;color:var(--t2);width:70px;}
        .sched-info h4{font-size:13.5px;font-weight:700;color:var(--t1);}
        .sched-info p{font-size:11.5px;color:var(--t3);}
        .sched-badge{margin-left:auto;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;}

        /* Quick Actions Grid */
        .qa-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
        .qa-btn{
            background:#f8fafc;border:1px solid var(--border);border-radius:14px;padding:16px;
            display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;
            text-decoration:none;color:var(--t1);font-size:12.5px;font-weight:700;transition:all .2s;text-align:center;
        }
        .qa-btn:hover{background:#fff;border-color:var(--purple);transform:translateY(-2px);box-shadow:var(--shadow-lg);}
        .qa-btn i{font-size:22px;}

        /* Assignments List */
        .assign-item{display:flex;align-items:center;justify-content:space-between;padding:12px;border-bottom:1px dashed var(--border);}
        .assign-item:last-child{border-bottom:none;}
        .assign-left{display:flex;align-items:center;gap:12px;}
        .assign-ico{width:36px;height:36px;border-radius:10px;background:var(--purple-light);color:var(--purple);display:flex;align-items:center;justify-content:center;font-size:16px;}
        .assign-info h4{font-size:13px;font-weight:700;color:var(--t1);}
        .assign-info p{font-size:11px;color:var(--t3);}
        .assign-sub-badge{padding:4px 10px;border-radius:20px;background:var(--green-light);color:var(--green);font-size:11px;font-weight:700;}

        /* ── GREETING ALERT BANNER STYLES ── */
        .db-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
        }
        .followup-alert-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.08), rgba(99, 102, 241, 0.05));
            border: 1.5px solid rgba(124, 58, 237, 0.25);
            border-radius: 24px;
            padding: 0 18px;
            height: 44px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 2px 12px rgba(124, 58, 237, 0.08);
            flex: 1;
            max-width: 680px;
        }
        .followup-slider {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100%;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .followup-slide {
            display: flex;
            align-items: center;
            gap: 10px;
            height: 44px;
            flex-shrink: 0;
            width: 100%;
        }
        .greeting-clock-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(124, 58, 237, 0.12);
            border-radius: 20px;
            padding: 3px 10px;
            margin-left: auto;
            flex-shrink: 0;
        }
        #greeting-clock {
            font-family: 'Courier New', monospace;
            font-size: 12.5px;
            font-weight: 800;
            color: #7c3aed;
            letter-spacing: 1px;
        }
        .greeting-clock-icon {
            color: #7c3aed;
            font-size: 11px;
            animation: clock-tick 1s steps(1) infinite;
        }
        @keyframes clock-tick {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        .typewriter-cursor::after {
            content: '|';
            color: #7c3aed;
            animation: blink-cursor 0.8s step-end infinite;
            font-weight: 400;
        }
        @keyframes blink-cursor {
            from, to { color: transparent }
            50% { color: #7c3aed; }
        }
    </style>
</head>
<body>

    <!-- Sidebar Overlay for mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleTeacherSidebar()"></div>

    <!-- ─── SIDEBAR ─────────────────────────────────────────────── -->
    <aside class="sidebar" id="teacherSidebar">
        <div class="sb-logo">
            <a href="{{ route('teacher.dashboard') }}" class="sb-logo-left" title="Teacher Dashboard">
                <div class="sb-logo-icon">
                    @if(!empty($school->logo) && Storage::disk('public')->exists($school->logo))
                        <img src="{{ Storage::disk('public')->url($school->logo) }}" alt="{{ $school->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
                    @else
                        <i class="fas fa-school"></i>
                    @endif
                </div>
                <div class="sb-logo-text">
                    <strong>{{ $school?->name ?? 'School ERP' }}</strong>
                    <span>Teacher Portal</span>
                </div>
            </a>
            <button type="button" class="sb-close-btn" onclick="toggleTeacherSidebar(event)" aria-label="Close sidebar">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <!-- Teacher Profile Badge -->
        <div class="sb-profile">
            <div class="sb-avatar" style="overflow:hidden;padding:0;">
                @if(!empty($teacherAvatarUrl))
                    <img src="{{ $teacherAvatarUrl }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:12px;" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
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
                    <h2>Good Morning, {{ $user->name }}! 🌟</h2>
                    <p>Here's your teaching overview for today.</p>
                </div>
            </div>
            <div class="th-actions">
                <button class="th-date-btn"><i class="fas fa-calendar"></i> May 1 – May 31, 2026 <i class="fas fa-chevron-down" style="font-size:10px;margin-left:4px;"></i></button>
                <button class="th-export-btn"><i class="fas fa-download"></i> Export Report</button>
                <!-- NOTIFICATION BELL CENTER -->
                <div class="th-notif-wrapper" style="position: relative;">
                    <button type="button" class="th-icon-btn" id="teacherNotifBell" onclick="toggleNotifDropdown()" title="Notifications">
                        <i class="fas fa-bell"></i>
                        @php
                            $tUnread = \App\Services\NotificationService::getUnreadCount();
                        @endphp
                        <span class="badge-dot" id="notifBadgeDot" style="{{ $tUnread > 0 ? '' : 'display: none;' }}"></span>
                        <span class="badge-count" id="notifBadgeCount" style="{{ $tUnread > 0 ? 'display: inline-flex;' : 'display: none;' }}">{{ $tUnread > 99 ? '99+' : $tUnread }}</span>
                    </button>

                    <!-- Notification Dropdown Panel -->
                    <div class="notif-dropdown-panel" id="notifDropdownPanel" style="display: none;">
                        <div class="notif-header">
                            <div class="notif-header-title">
                                <i class="fas fa-bell" style="color: var(--purple);"></i>
                                Notifications
                                <span class="notif-unread-pill" id="notifUnreadPill">0 Unread</span>
                            </div>
                            <button type="button" class="btn-mark-all-read" onclick="markAllNotifsAsRead()">
                                <i class="fas fa-check-double"></i> Mark all read
                            </button>
                        </div>
                        <div class="notif-body" id="notifListContainer">
                            <div class="notif-empty-state">
                                <i class="fas fa-spinner fa-spin" style="font-size:20px; color:var(--purple); margin-bottom:8px;"></i>
                                <div>Loading notifications...</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- DIRECT LOGOUT ICON BUTTON -->
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('teacher-header-logout-form').submit();" class="th-icon-btn th-logout-btn" title="Logout">
                    <i class="fas fa-power-off"></i>
                </a>
                <form id="teacher-header-logout-form" action="{{ route('logout.post') }}" method="POST" style="display: none;">
                    @csrf
                </form>

                <!-- TEACHER USER PILL WITH DROPDOWN -->
                <div class="th-user-pill-wrapper" style="position: relative;">
                    <div class="th-user-pill" id="teacherUserPillBtn" onclick="toggleTeacherUserDropdown(event)" style="cursor: pointer; user-select: none;">
                        <div class="th-user-img" id="headerAvatarContainer" style="overflow:hidden;">
                            @if(!empty($teacherAvatarUrl))
                                <img src="{{ $teacherAvatarUrl }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                <span style="display:none;width:100%;height:100%;align-items:center;justify-content:center;">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @else
                                <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="th-user-info">
                            <h5>{{ $user->name }}</h5>
                            <p>{{ $staff?->designation?->name ?? 'Teacher' }}</p>
                        </div>
                        <i class="fas fa-chevron-down th-user-chevron" style="font-size: 11px; color: var(--t3); margin-left: 4px; transition: transform 0.2s;"></i>
                    </div>

                    <!-- USER DROPDOWN PANEL -->
                    <div class="teacher-user-dropdown" id="teacherUserDropdownPanel" style="display: none;">
                        <div class="t-dd-header">
                            <div class="t-dd-avatar" style="overflow:hidden;">
                                @if(!empty($teacherAvatarUrl))
                                    <img src="{{ $teacherAvatarUrl }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                    <span style="display:none;width:100%;height:100%;align-items:center;justify-content:center;">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                @else
                                    <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="t-dd-details">
                                <h6>{{ $user->name }}</h6>
                                <span>{{ $user->email }}</span>
                                <span class="t-dd-role-badge"><i class="fas fa-chalkboard-teacher"></i> {{ $staff?->designation?->name ?? 'Teacher' }}</span>
                            </div>
                        </div>
                        <div class="t-dd-body">
                            <button type="button" class="t-dd-item" onclick="openTeacherProfileModal()">
                                <div class="t-dd-icon" style="background: rgba(124, 58, 237, 0.1); color: #7c3aed;">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="t-dd-text">
                                    <strong>My Profile</strong>
                                    <small>Update picture & password</small>
                                </div>
                                <i class="fas fa-angle-right t-dd-arrow"></i>
                            </button>
                            <button type="button" class="t-dd-item" onclick="openTeacherSettingsModal()">
                                <div class="t-dd-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                                    <i class="fas fa-sliders-h"></i>
                                </div>
                                <div class="t-dd-text">
                                    <strong>General Settings</strong>
                                    <small>Theme & dashboard preferences</small>
                                </div>
                                <i class="fas fa-angle-right t-dd-arrow"></i>
                            </button>
                            <div class="t-dd-divider"></div>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('teacher-header-logout-form').submit();" class="t-dd-item t-dd-logout">
                                <div class="t-dd-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                    <i class="fas fa-sign-out-alt"></i>
                                </div>
                                <div class="t-dd-text">
                                    <strong style="color: #ef4444;">Sign Out</strong>
                                    <small>Log out of your account</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="content-area">

            <!-- ══ GREETING ALERT BANNER ══ -->
            <div class="db-header-row" style="margin-bottom: 20px;">
                <div class="followup-alert-box">
                    <div class="followup-slider" id="followupSlider">
                        <!-- Slide 1: Greeting with clock -->
                        <div class="followup-slide">
                            <i id="greeting-icon" class="fas fa-sun" style="color:#7c3aed;font-size:14px;flex-shrink:0;"></i>
                            <span id="greeting-text" class="typewriter-cursor" style="font-weight:700;color:#7c3aed;font-family:'Plus Jakarta Sans',sans-serif;font-size:12.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Good day, {{ $user->name }}! 👋</span>
                            <div class="greeting-clock-wrap">
                                <i class="fas fa-clock greeting-clock-icon"></i>
                                <span id="greeting-clock" style="font-family:'Courier New',monospace;font-size:11.5px;font-weight:800;color:#7c3aed;letter-spacing:0.5px;">00:00:00 AM</span>
                            </div>
                        </div>
                        <!-- Slide for Today's Events (if any) -->
                        @if(isset($todayEvents) && $todayEvents->count() > 0)
                            @foreach($todayEvents as $evt)
                                <div class="followup-slide" style="justify-content: space-between; width: 100%;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-calendar-day" style="color: #ef4444; font-size: 14px;"></i>
                                        <span style="font-weight: 700; color: #7c3aed; font-size:12px;">Today is: {{ $evt->title }} 🎉</span>
                                    </div>
                                    <span style="background: {{ $evt->is_holiday ? 'rgba(239, 68, 68, 0.12)' : 'rgba(16, 185, 129, 0.12)' }}; color: {{ $evt->is_holiday ? '#ef4444' : '#10b981' }}; font-size: 10px; padding: 2px 8px; border-radius:12px; font-weight:700;">{{ $evt->is_holiday ? 'Holiday' : 'Event' }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div style="background:var(--green-light);border:1px solid #bbf7d0;color:#065f46;padding:14px 20px;border-radius:14px;margin-bottom:24px;font-weight:600;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif

            <!-- Top 5 Metric Sparkline Cards -->
            <div class="metrics-5-grid">
                <div class="spark-card">
                    <div class="sc-top">
                        <div class="sc-ico" style="background:var(--blue-light);color:var(--blue);"><i class="fas fa-users"></i></div>
                        <div>
                            <div class="sc-label">Total Students</div>
                            <div class="sc-val">{{ $totalStudents }}</div>
                        </div>
                    </div>
                    <div class="sc-trend" style="color:var(--blue);"><i class="fas fa-arrow-up"></i> 8.3% <span style="color:var(--t3);font-weight:500;">vs last month</span></div>
                    <div style="position:relative;height:40px;width:100%;margin-top:10px;"><canvas id="spark1"></canvas></div>
                </div>

                <div class="spark-card">
                    <div class="sc-top">
                        <div class="sc-ico" style="background:var(--purple-light);color:var(--purple);"><i class="fas fa-book-open"></i></div>
                        <div>
                            <div class="sc-label">Classes Assigned</div>
                            <div class="sc-val">{{ $classesAssignedCount }}</div>
                        </div>
                    </div>
                    <div class="sc-trend" style="color:var(--t3);"><i class="fas fa-minus"></i> No change</div>
                    <div style="position:relative;height:40px;width:100%;margin-top:10px;"><canvas id="spark2"></canvas></div>
                </div>

                <div class="spark-card">
                    <div class="sc-top">
                        <div class="sc-ico" style="background:var(--green-light);color:var(--green);"><i class="fas fa-check-circle"></i></div>
                        <div>
                            <div class="sc-label">Attendance Today</div>
                            <div class="sc-val">{{ $attendanceTodayPct }}%</div>
                        </div>
                    </div>
                    <div class="sc-trend" style="color: {{ ($totalAttCount ?? 0) > 0 ? 'var(--green)' : 'var(--t3)' }};">
                        @if(($totalAttCount ?? 0) > 0)
                            <i class="fas fa-check"></i> {{ $presentAttCount ?? 0 }}/{{ $totalAttCount }} <span style="color:var(--t3);font-weight:500;">Present today</span>
                        @else
                            <i class="fas fa-clock"></i> <span style="color:var(--t3);font-weight:500;">Not marked today</span>
                        @endif
                    </div>
                    <div style="position:relative;height:40px;width:100%;margin-top:10px;"><canvas id="spark3"></canvas></div>
                </div>

                <div class="spark-card">
                    <div class="sc-top">
                        <div class="sc-ico" style="background:var(--gold-light);color:var(--gold);"><i class="fas fa-star"></i></div>
                        <div>
                            <div class="sc-label">Average Score</div>
                            <div class="sc-val">{{ $avgScore }}%</div>
                        </div>
                    </div>
                    <div class="sc-trend" style="color:var(--green);"><i class="fas fa-chart-line"></i> <span style="color:var(--t3);font-weight:500;">Assigned classes</span></div>
                    <div style="position:relative;height:40px;width:100%;margin-top:10px;"><canvas id="spark4"></canvas></div>
                </div>

                <div class="spark-card">
                    <div class="sc-top">
                        <div class="sc-ico" style="background:var(--red-light);color:var(--red);"><i class="fas fa-clipboard-list"></i></div>
                        <div>
                            <div class="sc-label">Assignments Pending</div>
                            <div class="sc-val">{{ $pendingAssignmentsCount }}</div>
                        </div>
                    </div>
                    <div class="sc-trend" style="color:var(--t3);"><i class="fas fa-tasks"></i> <span style="color:var(--t3);font-weight:500;">Total assigned</span></div>
                    <div style="position:relative;height:40px;width:100%;margin-top:10px;"><canvas id="spark5"></canvas></div>
                </div>
            </div>

            <!-- Row 1: Attendance Overview | Class Performance | Upcoming Classes -->
            <div class="grid-3-col">
                <!-- Attendance Overview -->
                <div class="dash-card">
                    <div>
                        <div class="dc-hdr">
                            <h3>Attendance Overview</h3>
                            <select class="dc-select"><option>{{ ($monthTotalAtt ?? 0) > 0 ? 'This Month' : 'Today' }}</option></select>
                        </div>
                        @php
                            $overviewPct = ($monthTotalAtt ?? 0) > 0 ? ($monthAvgPct ?? 0) : ($attendanceTodayPct ?? 0);
                            $overviewPresent = ($monthTotalAtt ?? 0) > 0 ? ($monthPresentAtt ?? 0) : ($presentAttCount ?? 0);
                            $overviewAbsent = ($monthTotalAtt ?? 0) > 0 ? ($monthAbsentAtt ?? 0) : ($absentAttCount ?? 0);
                            $overviewLeave = ($monthTotalAtt ?? 0) > 0 ? ($monthLeaveAtt ?? 0) : ($leaveAttCount ?? 0);
                            $overviewTotal = ($monthTotalAtt ?? 0) > 0 ? ($monthTotalAtt ?? 0) : ($totalAttCount ?? 0);
                            $overviewPresentPct = $overviewTotal > 0 ? round(($overviewPresent / $overviewTotal) * 100) : 0;
                            $overviewAbsentPct = $overviewTotal > 0 ? round(($overviewAbsent / $overviewTotal) * 100) : 0;
                            $overviewLeavePct = $overviewTotal > 0 ? round(($overviewLeave / $overviewTotal) * 100) : 0;
                        @endphp
                        <div style="position:relative;height:180px;display:flex;align-items:center;justify-content:center;">
                            <canvas id="donutChart"></canvas>
                            <div style="position:absolute;text-align:center;">
                                <div style="font-family:'Plus Jakarta Sans';font-size:24px;font-weight:800;">{{ $overviewPct }}%</div>
                                <div style="font-size:10px;color:var(--t3);font-weight:700;text-transform:uppercase;">Avg Attendance</div>
                            </div>
                        </div>
                        <div class="donut-legend">
                            <div class="leg-item"><div class="leg-left"><span class="leg-dot" style="background:var(--green);"></span>Present</div><div class="leg-val">{{ $overviewPresentPct }}% ({{ $overviewPresent }})</div></div>
                            <div class="leg-item"><div class="leg-left"><span class="leg-dot" style="background:var(--red);"></span>Absent</div><div class="leg-val">{{ $overviewAbsentPct }}% ({{ $overviewAbsent }})</div></div>
                            <div class="leg-item"><div class="leg-left"><span class="leg-dot" style="background:var(--gold);"></span>Leave</div><div class="leg-val">{{ $overviewLeavePct }}% ({{ $overviewLeave }})</div></div>
                        </div>
                    </div>
                    @if($overviewTotal > 0)
                        <div style="margin-top:20px;padding:12px;background:var(--green-light);border-radius:12px;display:flex;align-items:center;justify-content:space-between;font-size:12px;font-weight:700;color:#065f46;">
                            <span>{{ $overviewPct >= 75 ? 'Great! Classes attendance is on track.' : 'Classes attendance needs attention.' }}</span>
                            <span style="background:#fff;padding:4px 8px;border-radius:20px;color:var(--green);">Target: 85%</span>
                        </div>
                    @else
                        <div style="margin-top:20px;padding:12px;background:#f8fafc;border:1px dashed var(--border);border-radius:12px;display:flex;align-items:center;justify-content:space-between;font-size:12px;font-weight:600;color:var(--t2);">
                            <span><i class="fas fa-info-circle me-1" style="color:var(--purple);"></i> No attendance recorded yet today.</span>
                            <a href="{{ Route::has('school.attendance.students.index') ? route('school.attendance.students.index') : '#' }}" style="color:var(--purple);font-weight:700;text-decoration:none;">Mark Now &rarr;</a>
                        </div>
                    @endif
                </div>

                <!-- Class Performance Bar Chart -->
                <div class="dash-card">
                    <div>
                        <div class="dc-hdr">
                            <h3>Class Performance</h3>
                            <select class="dc-select"><option>This Month</option></select>
                        </div>
                        <div style="font-size:12px;color:var(--t2);font-weight:600;margin-bottom:12px;">Average Score (%)</div>
                        <div style="height:210px;"><canvas id="barChart"></canvas></div>
                    </div>
                    <a href="#" class="btn" style="background:#f1f5f9;color:var(--purple);justify-content:center;margin-top:16px;font-weight:700;">View Detailed Report <i class="fas fa-arrow-right me-1"></i></a>
                </div>

                <!-- Upcoming Classes -->
                <div class="dash-card">
                    <div>
                        <div class="dc-hdr">
                            <h3>Upcoming Classes</h3>
                            <a href="{{ Route::has('school.timetable.teacher') ? route('school.timetable.teacher') : '#' }}" class="dc-link">View Timetable</a>
                        </div>
                        <div class="sched-list">
                            @if($todaysSchedule->count() > 0)
                                @foreach($todaysSchedule as $ts)
                                    <div class="sched-item">
                                        <div class="sched-ico" style="background:var(--blue-light);color:var(--blue);width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-calendar-alt"></i></div>
                                        <div class="sched-info">
                                            <h4>{{ $ts->schoolClass?->name }} - {{ $ts->section?->name }} &bull; {{ $ts->subject?->name }}</h4>
                                            <p>{{ $ts->period?->name ?? 'Class Period' }}</p>
                                        </div>
                                        <div class="sched-time" style="margin-left:auto;text-align:right;font-size:11px;">{{ $ts->period?->start_time ?? 'Scheduled' }}</div>
                                    </div>
                                @endforeach
                            @else
                                <div style="padding:24px;text-align:center;color:var(--t3);font-size:13px;font-weight:600;"><i class="fas fa-calendar-times me-2" style="font-size:18px;display:block;margin-bottom:8px;color:var(--t3);"></i> No classes scheduled for today.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Recent Activity | Today's Schedule | Quick Actions -->
            <div class="grid-3-col">
                <!-- Recent Activity / Announcements -->
                <div class="dash-card">
                    <div>
                        <div class="dc-hdr">
                            <h3>Class Overview</h3>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:12px;padding:10px 0;">
                            <div style="font-size:13px;color:var(--t2);line-height:1.5;">
                                Welcome to your teaching dashboard! Access your assigned classes, attendance registers, timetables, and gradebooks seamlessly.
                            </div>
                            <div style="padding:12px;background:var(--blue-light);border-radius:10px;font-size:12px;color:var(--blue);font-weight:600;">
                                <i class="fas fa-info-circle me-1"></i> Total Assigned Classes: <strong>{{ $classesAssignedCount }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Schedule Timeline -->
                <div class="dash-card">
                    <div>
                        <div class="dc-hdr">
                            <h3>Today's Schedule</h3>
                            <a href="{{ Route::has('school.timetable.class') ? route('school.timetable.class') : '#' }}" class="dc-link">Full Schedule</a>
                        </div>
                        <div class="sched-list">
                            @if($todaysSchedule->count() > 0)
                                @foreach($todaysSchedule as $ts)
                                    <div class="sched-item">
                                        <div style="width:10px;height:10px;border-radius:50%;background:var(--purple);"></div>
                                        <div class="sched-time">{{ $ts->period?->start_time ?? '08:00 AM' }}</div>
                                        <div class="sched-info">
                                            <h4>{{ $ts->subject?->name }}</h4>
                                        </div>
                                        <span class="sched-badge" style="background:var(--purple-light);color:var(--purple);">{{ $ts->schoolClass?->name }}-{{ $ts->section?->name }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div style="padding:24px;text-align:center;color:var(--t3);font-size:13px;font-weight:600;"><i class="fas fa-clock me-2" style="font-size:18px;display:block;margin-bottom:8px;color:var(--t3);"></i> Timetable not set for today.</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Grid -->
                <div class="dash-card">
                    <div>
                        <div class="dc-hdr">
                            <h3>Quick Actions</h3>
                        </div>
                        <div class="qa-grid">
                            <a href="{{ Route::has('school.attendance.students.index') ? route('school.attendance.students.index') : '#' }}" class="qa-btn">
                                <i class="fas fa-user-check" style="color:var(--blue);"></i>
                                <span>Mark Attendance</span>
                            </a>
                            <a href="{{ Route::has('school.timetable.teacher') ? route('school.timetable.teacher') : '#' }}" class="qa-btn">
                                <i class="fas fa-calendar-days" style="color:var(--purple);"></i>
                                <span>My Timetable</span>
                            </a>
                             <a href="{{ Route::has('teacher.notices.index') ? route('teacher.notices.index') : '#' }}" class="qa-btn">
                                 <i class="fas fa-bullhorn" style="color:var(--orange);"></i>
                                 <span>Announcements</span>
                             </a>
                            <a href="{{ Route::has('school.diary.create') ? route('school.diary.create') : '#' }}" class="qa-btn">
                                <i class="fas fa-book-open" style="color:var(--green);"></i>
                                <span>Digital Diary</span>
                            </a>
                            <a href="{{ route('teacher.assignments.index') }}" class="qa-btn">
                                <i class="fas fa-tasks" style="color:var(--blue);"></i>
                                <span>Class Assignments</span>
                            </a>
                            <a href="{{ route('teacher.study-materials.index') }}" class="qa-btn">
                                <i class="fas fa-folder-open" style="color:var(--red);"></i>
                                <span>Study Materials</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Chart.js Scripts -->
    <script>
        // Sparklines
        function createSparkline(id, color, data) {
            const ctx = document.getElementById(id).getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [1,2,3,4,5,6,7],
                    datasets: [{
                        data: data,
                        borderColor: color,
                        borderWidth: 2.5,
                        pointRadius: 0,
                        tension: 0.4,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {legend: {display: false}},
                    scales: {x: {display: false}, y: {display: false}}
                }
            });
        }

        createSparkline('spark1', '#2563eb', [10, 15, 12, 18, 20, 25, 28]);
        createSparkline('spark2', '#7c3aed', [5, 5, 5, 5, 5, 5, 5]);
        createSparkline('spark3', '#10b981', [80, 85, 82, 88, 90, 91, 92]);
        createSparkline('spark4', '#f59e0b', [70, 72, 71, 74, 75, 77, 78]);
        createSparkline('spark5', '#ef4444', [12, 10, 11, 9, 8, 8, 7]);

        // Donut Chart
        const dPresent = {{ $overviewPresent ?? 0 }};
        const dAbsent = {{ $overviewAbsent ?? 0 }};
        const dLeave = {{ $overviewLeave ?? 0 }};
        const dHasData = (dPresent + dAbsent + dLeave) > 0;

        new Chart(document.getElementById('donutChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: dHasData ? ['Present', 'Absent', 'Leave'] : ['No Attendance'],
                datasets: [{
                    data: dHasData ? [dPresent, dAbsent, dLeave] : [1],
                    backgroundColor: dHasData ? ['#10b981', '#ef4444', '#f59e0b'] : ['#e2e8f0'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {display: false},
                    tooltip: {enabled: dHasData}
                }
            }
        });

        // Bar Chart (Teacher's Assigned Classes)
        const perfLabels = {!! json_encode(collect($classPerformance ?? [])->pluck('class')) !!};
        const perfScores = {!! json_encode(collect($classPerformance ?? [])->pluck('score')) !!};
        const hasPerfData = perfLabels.length > 0;

        new Chart(document.getElementById('barChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: hasPerfData ? perfLabels : ['Assigned Classes'],
                datasets: [{
                    data: hasPerfData ? perfScores : [0],
                    backgroundColor: '#7c3aed',
                    borderRadius: 8,
                    barThickness: 24
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {legend: {display: false}},
                scales: {
                    y: {max: 100, grid: {borderDash: [4, 4]}},
                    x: {grid: {display: false}}
                }
            }
        });

        // ── GREETING SLIDER WITH CLOCK & TYPEWRITER ERASE (Teacher Portal) ──
        const hour = new Date().getHours();
        let greeting = 'Good night';
        let greetEmoji = '🌙';
        let greetIcon = 'fa-moon';
        
        if (hour >= 5 && hour < 12)  { greeting = 'Good morning';   greetEmoji = '☀️'; greetIcon = 'fa-sun'; }
        else if (hour >= 12 && hour < 14) { greeting = 'Good afternoon'; greetEmoji = '🌤️'; greetIcon = 'fa-cloud-sun'; }
        else if (hour >= 14 && hour < 18) { greeting = 'Good evening'; greetEmoji = '🌇'; greetIcon = 'fa-cloud-sun'; }
        else if (hour >= 18 && hour < 21) { greeting = 'Good evening'; greetEmoji = '🌆'; greetIcon = 'fa-sunset'; }

        const greetingText = document.getElementById('greeting-text');
        const greetingIcon = document.getElementById('greeting-icon');

        if (greetingText) {
            greetingText.textContent = `${greeting}, {{ $user->name }}! 👋`;
        }
        if (greetingIcon) {
            greetingIcon.className = `fas ${greetIcon}`;
        }

        // Live Clock
        const clockEl = document.getElementById('greeting-clock');
        function updateClock() {
            if (!clockEl) return;
            const now = new Date();
            const hh = String(now.getHours()).padStart(2, '0');
            const mm = String(now.getMinutes()).padStart(2, '0');
            const ss = String(now.getSeconds()).padStart(2, '0');
            const ampm = now.getHours() >= 12 ? 'PM' : 'AM';
            const hh12 = String(now.getHours() % 12 || 12).padStart(2, '0');
            clockEl.textContent = `${hh12}:${mm}:${ss} ${ampm}`;
        }
        updateClock();
        setInterval(updateClock, 1000);

        // Auto Slider with typewriter erase
        let currentSlide = 0;
        const slider = document.getElementById('followupSlider');
        const greetEl = document.getElementById('greeting-text');
        const SLIDE_HEIGHT = 44; // px

        function typewriterErase(el, fullText, onDone) {
            if (!el) { if (onDone) onDone(); return; }
            el.classList.remove('typewriter-cursor');
            let i = fullText.length;
            const eraseInterval = setInterval(() => {
                i--;
                el.textContent = fullText.substring(0, i);
                if (i <= 0) {
                    clearInterval(eraseInterval);
                    el.textContent = '';
                    if (onDone) onDone();
                }
            }, 45);
        }

        function typewriterType(el, text, onDone) {
            if (!el) { if (onDone) onDone(); return; }
            el.textContent = '';
            el.classList.add('typewriter-cursor');
            let i = 0;
            const typeInterval = setInterval(() => {
                i++;
                el.textContent = text.substring(0, i);
                if (i >= text.length) {
                    clearInterval(typeInterval);
                    if (onDone) onDone();
                }
            }, 55);
        }

        const activeGreetText = `${greeting}, {{ $user->name }}! 👋`;

        if (slider) {
            const slidesCount = slider.children.length;
            if (slidesCount > 1) {
                setInterval(() => {
                    if (currentSlide === 0) {
                        if (greetEl) {
                            typewriterErase(greetEl, greetEl.textContent, () => {
                                currentSlide = (currentSlide + 1) % slidesCount;
                                slider.style.transform = `translateY(-${currentSlide * SLIDE_HEIGHT}px)`;
                            });
                        } else {
                            currentSlide = (currentSlide + 1) % slidesCount;
                            slider.style.transform = `translateY(-${currentSlide * SLIDE_HEIGHT}px)`;
                        }
                    } else {
                        currentSlide = (currentSlide + 1) % slidesCount;
                        slider.style.transform = `translateY(-${currentSlide * SLIDE_HEIGHT}px)`;
                        if (currentSlide === 0) {
                            setTimeout(() => {
                                if (greetEl) typewriterType(greetEl, activeGreetText, null);
                            }, 700);
                        }
                    }
                }, 5000);
            }
        }
    </script>
    <!-- NOTIFICATION SYSTEM SCRIPT -->
    <script>
        function toggleNotifDropdown() {
            const panel = document.getElementById('notifDropdownPanel');
            if (!panel) return;
            const isVisible = panel.style.display === 'block';
            panel.style.display = isVisible ? 'none' : 'block';
            if (!isVisible) {
                fetchTeacherNotifications();
            }
        }

        document.addEventListener('click', function(e) {
            const wrapper = document.querySelector('.th-notif-wrapper');
            const panel = document.getElementById('notifDropdownPanel');
            if (wrapper && panel && !wrapper.contains(e.target)) {
                panel.style.display = 'none';
            }
        });

        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function fetchTeacherNotifications() {
            fetch("{{ route('teacher.notifications.index') }}", {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                const count = data.unread_count || 0;
                const badgeDot = document.getElementById('notifBadgeDot');
                const badgeCount = document.getElementById('notifBadgeCount');
                const unreadPill = document.getElementById('notifUnreadPill');
                const container = document.getElementById('notifListContainer');

                if (count > 0) {
                    if (badgeDot) badgeDot.style.display = 'block';
                    if (badgeCount) {
                        badgeCount.textContent = count > 99 ? '99+' : count;
                        badgeCount.style.display = 'inline-flex';
                    }
                    if (unreadPill) unreadPill.textContent = `${count} Unread`;
                } else {
                    if (badgeDot) badgeDot.style.display = 'none';
                    if (badgeCount) badgeCount.style.display = 'none';
                    if (unreadPill) unreadPill.textContent = `0 Unread`;
                }

                if (!container) return;

                if (!data.notifications || data.notifications.length === 0) {
                    container.innerHTML = `
                        <div class="notif-empty-state">
                            <i class="fas fa-bell-slash" style="font-size:24px; color:var(--t3); margin-bottom:8px; display:block;"></i>
                            <div style="font-weight:700; color:var(--t1); font-size:13px;">No notifications yet</div>
                            <div style="font-size:11.5px; color:var(--t3); margin-top:2px;">Leave updates and system notices will appear here.</div>
                        </div>`;
                    return;
                }

                let html = '';
                data.notifications.forEach(item => {
                    let iconClass = 'fas fa-info-circle';
                    let iconBg = 'rgba(37,99,235,0.1)';
                    let iconColor = '#2563eb';

                    if (item.type === 'leave_approved') {
                        iconClass = 'fas fa-circle-check';
                        iconBg = 'rgba(16,185,129,0.1)';
                        iconColor = '#10b981';
                    } else if (item.type === 'leave_rejected') {
                        iconClass = 'fas fa-circle-xmark';
                        iconBg = 'rgba(239,68,68,0.1)';
                        iconColor = '#ef4444';
                    } else if (item.type === 'leave_submitted') {
                        iconClass = 'fas fa-paper-plane';
                        iconBg = 'rgba(124,58,237,0.1)';
                        iconColor = '#7c3aed';
                    } else if (item.type === 'chat' || item.module === 'communication') {
                        iconClass = 'fas fa-comments';
                        iconBg = 'rgba(37,99,235,0.15)';
                        iconColor = '#2563eb';
                    } else if (item.icon) {
                        iconClass = item.icon.startsWith('fa-') ? `fas ${item.icon}` : item.icon;
                        iconColor = item.color || '#2563eb';
                        iconBg = `${iconColor}18`;
                    }

                    const unreadClass = item.is_read ? 'read' : 'unread';
                    const timeDisplay = item.time || item.time_ago || '';
                    const dateDisplay = item.date_str || item.created_at || '';
                    let timeText = '';
                    if (timeDisplay && dateDisplay && !timeDisplay.includes(dateDisplay)) {
                        timeText = `${timeDisplay} (${dateDisplay})`;
                    } else {
                        timeText = timeDisplay || dateDisplay || '';
                    }

                    const actionUrl = item.action_url ? escapeHtml(item.action_url) : '';
                    html += `
                        <div class="notif-item ${unreadClass}" onclick="handleTeacherNotifClick(${item.id}, '${actionUrl}')">
                            <div class="notif-icon" style="background: ${iconBg}; color: ${iconColor};">
                                <i class="${iconClass}"></i>
                            </div>
                            <div class="notif-content">
                                <div class="notif-item-title">${escapeHtml(item.title)}</div>
                                <div class="notif-item-msg">${escapeHtml(item.message)}</div>
                                <div class="notif-item-time"><i class="far fa-clock"></i> ${escapeHtml(timeText)}</div>
                            </div>
                            ${!item.is_read ? '<div class="notif-unread-dot"></div>' : ''}
                        </div>`;
                });
                container.innerHTML = html;
            })
            .catch(err => {
                console.error('Error fetching notifications:', err);
            });
        }

        function handleTeacherNotifClick(id, actionUrl) {
            markNotifAsRead(id);
            if (actionUrl && actionUrl !== '#' && actionUrl !== 'javascript:void(0);') {
                setTimeout(() => {
                    window.location.href = actionUrl;
                }, 150);
            }
        }

        function markNotifAsRead(id) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch(`/teacher/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                fetchTeacherNotifications();
            })
            .catch(err => console.error('Error marking notification read:', err));
        }

        function markAllNotifsAsRead() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch("{{ route('teacher.notifications.read-all') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                fetchTeacherNotifications();
            })
            .catch(err => console.error('Error marking all read:', err));
        }

        function toggleTeacherSidebar(e) {
            if (e && e.preventDefault) { e.preventDefault(); e.stopPropagation(); }
            const sidebar = document.getElementById('teacherSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (!sidebar) return;

            if (window.innerWidth > 991) {
                document.body.classList.toggle('sidebar-closed');
            } else {
                const isOpen = sidebar.classList.toggle('open');
                if (overlay) overlay.classList.toggle('active', isOpen);
            }
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

        function setupTeacherSidebarEvents() {
            document.querySelectorAll('.sb-group').forEach(group => {
                group.addEventListener('click', function(e) {
                    // Do not block clicks on actual links or inside popup modal
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
                        // Desktop accordion toggle
                        const submenu = this.querySelector('.sb-submenu');
                        const hdr = this.querySelector('.sb-hdr');
                        if (submenu) {
                            const isOpen = submenu.classList.toggle('open');
                            if (hdr) hdr.classList.toggle('open', isOpen);
                        }
                    }
                });
            });
        }

        // ── Teacher Profile & Settings Modals JS ──
        function toggleTeacherUserDropdown(event) {
            if (event) event.stopPropagation();
            const dropdown = document.getElementById('teacherUserDropdownPanel');
            const pill = document.getElementById('teacherUserPillBtn');
            const notifDropdown = document.getElementById('notifDropdownPanel');
            if (notifDropdown) notifDropdown.style.display = 'none';

            if (dropdown.style.display === 'none' || !dropdown.style.display) {
                dropdown.style.display = 'block';
                if (pill) pill.classList.add('active');
            } else {
                dropdown.style.display = 'none';
                if (pill) pill.classList.remove('active');
            }
        }

        document.addEventListener('click', function (e) {
            const dropdown = document.getElementById('teacherUserDropdownPanel');
            const pill = document.getElementById('teacherUserPillBtn');
            if (dropdown && !dropdown.contains(e.target) && pill && !pill.contains(e.target)) {
                dropdown.style.display = 'none';
                pill.classList.remove('active');
            }
        });

        function openTeacherProfileModal() {
            const dropdown = document.getElementById('teacherUserDropdownPanel');
            if (dropdown) dropdown.style.display = 'none';
            document.getElementById('teacherProfileModal').style.display = 'flex';
        }
        function closeTeacherProfileModal() {
            document.getElementById('teacherProfileModal').style.display = 'none';
        }

        function openTeacherSettingsModal() {
            const dropdown = document.getElementById('teacherUserDropdownPanel');
            if (dropdown) dropdown.style.display = 'none';
            document.getElementById('teacherSettingsModal').style.display = 'flex';
        }
        function closeTeacherSettingsModal() {
            document.getElementById('teacherSettingsModal').style.display = 'none';
        }

        function switchTeacherProfileTab(tabName) {
            const tabs = document.querySelectorAll('#teacherProfileModal .t-tab-btn');
            tabs.forEach(t => t.classList.remove('active'));
            
            document.getElementById('tpTabPicture').style.display = 'none';
            document.getElementById('tpTabPassword').style.display = 'none';
            document.getElementById('tpTabInfo').style.display = 'none';

            if (tabName === 'picture') {
                tabs[0].classList.add('active');
                document.getElementById('tpTabPicture').style.display = 'block';
            } else if (tabName === 'password') {
                tabs[1].classList.add('active');
                document.getElementById('tpTabPassword').style.display = 'block';
            } else if (tabName === 'info') {
                tabs[2].classList.add('active');
                document.getElementById('tpTabInfo').style.display = 'block';
            }
        }

        function previewTeacherAvatar(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                document.getElementById('teacherPhotoFileName').innerText = file.name;
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.getElementById('tAvatarImg');
                    const init = document.getElementById('tAvatarInitial');
                    img.src = e.target.result;
                    img.style.display = 'block';
                    if (init) init.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        }

        function togglePassVisibility(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function checkPassStrength(val) {
            const bar = document.getElementById('tPassStrengthBar');
            const fill = document.getElementById('tPassStrengthFill');
            if (!val) {
                if (bar) bar.style.display = 'none';
                return;
            }
            if (bar) bar.style.display = 'block';
            let strength = 0;
            if (val.length >= 6) strength += 33;
            if (val.length >= 8) strength += 33;
            if (/[A-Z]/.test(val) && /[0-9]/.test(val)) strength += 34;

            if (fill) {
                fill.style.width = strength + '%';
                fill.style.background = strength < 50 ? '#ef4444' : strength < 80 ? '#f59e0b' : '#10b981';
            }
        }

        function handleProfilePictureUpload(e) {
            e.preventDefault();
            const form = document.getElementById('teacherPictureForm');
            const formData = new FormData(form);
            const btn = document.getElementById('btnSavePicture');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';

            fetch('{{ route("teacher.profile.update-picture") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save"></i> Save Profile Picture';
                if (data.success) {
                    showTeacherToast(data.message, 'success');
                    if (data.photo_url) {
                        const headerAvatar = document.getElementById('headerAvatarContainer');
                        if (headerAvatar) {
                            headerAvatar.innerHTML = `<img src="${data.photo_url}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
                        }
                        const ddAvatars = document.querySelectorAll('.t-dd-avatar');
                        ddAvatars.forEach(av => {
                            av.innerHTML = `<img src="${data.photo_url}">`;
                        });
                    }
                    setTimeout(closeTeacherProfileModal, 1000);
                } else {
                    showTeacherToast(data.message || 'Failed to update picture.', 'error');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save"></i> Save Profile Picture';
                showTeacherToast('Error uploading photo.', 'error');
            });
        }

        function handlePasswordUpdate(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSavePassword');
            const newPass = document.getElementById('tNewPass').value;
            const confirmPass = document.getElementById('tConfirmPass').value;

            if (newPass !== confirmPass) {
                showTeacherToast('New password and confirmation do not match!', 'error');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

            const formData = new FormData();
            formData.append('current_password', document.getElementById('tCurrentPass').value);
            formData.append('new_password', newPass);
            formData.append('new_password_confirmation', confirmPass);

            fetch('{{ route("teacher.profile.update-password") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-shield-alt"></i> Update Password';
                if (data.success) {
                    showTeacherToast(data.message, 'success');
                    document.getElementById('teacherPasswordForm').reset();
                    setTimeout(closeTeacherProfileModal, 1000);
                } else {
                    showTeacherToast(data.message || 'Failed to update password.', 'error');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-shield-alt"></i> Update Password';
                showTeacherToast('Error updating password.', 'error');
            });
        }

        function applyLiveTheme(themeName) {
            localStorage.setItem('teacher_theme_accent', themeName);
            let primaryColor = '#7c3aed';
            if (themeName === 'ocean') primaryColor = '#2563eb';
            else if (themeName === 'emerald') primaryColor = '#059669';
            else if (themeName === 'dark') primaryColor = '#0f172a';

            document.documentElement.style.setProperty('--purple', primaryColor);
        }

        function handleSettingsSave(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSaveSettings');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            const theme = document.querySelector('input[name="theme_accent"]:checked')?.value || 'purple';
            const showGreeting = document.getElementById('settGreetingBanner').checked;
            const autoCollapse = document.getElementById('settAutoCollapse').checked;
            const soundAlerts = document.getElementById('settSoundAlerts').checked;
            const desktopNotifs = document.getElementById('settDesktopNotifs').checked;
            const autoRefresh = document.getElementById('settAutoRefresh').value;

            const settingsObj = { theme, showGreeting, autoCollapse, soundAlerts, desktopNotifs, autoRefresh };
            localStorage.setItem('teacher_dashboard_settings', JSON.stringify(settingsObj));

            const bannerRow = document.querySelector('.db-header-row');
            if (bannerRow) {
                bannerRow.style.display = showGreeting ? 'block' : 'none';
            }

            fetch('{{ route("teacher.settings.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ settings: settingsObj })
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save"></i> Save Settings';
                showTeacherToast('Dashboard settings updated!', 'success');
                setTimeout(closeTeacherSettingsModal, 800);
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save"></i> Save Settings';
                showTeacherToast('Settings saved locally.', 'success');
                setTimeout(closeTeacherSettingsModal, 800);
            });
        }

        function showTeacherToast(msg, type = 'info') {
            let toast = document.getElementById('teacherToastNotification');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'teacherToastNotification';
                toast.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:99999;padding:12px 20px;border-radius:12px;color:#fff;font-size:13.5px;font-weight:700;display:flex;align-items:center;gap:10px;box-shadow:0 10px 25px rgba(0,0,0,0.2);';
                document.body.appendChild(toast);
            }
            toast.style.background = (type === 'success') ? '#10b981' : (type === 'error') ? '#ef4444' : '#7c3aed';
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i> ${msg}`;
            toast.style.display = 'flex';

            setTimeout(() => {
                toast.style.display = 'none';
            }, 3500);
        }

        document.addEventListener('DOMContentLoaded', function() {
            setupTeacherSidebarEvents();
            fetchTeacherNotifications();
            setInterval(fetchTeacherNotifications, 15000);

            const saved = localStorage.getItem('teacher_dashboard_settings');
            if (saved) {
                try {
                    const setts = JSON.parse(saved);
                    if (setts.theme) {
                        applyLiveTheme(setts.theme);
                        const radio = document.querySelector(`input[name="theme_accent"][value="${setts.theme}"]`);
                        if (radio) radio.checked = true;
                    }
                    if (setts.showGreeting === false) {
                        const bannerRow = document.querySelector('.db-header-row');
                        if (bannerRow) bannerRow.style.display = 'none';
                        const chk = document.getElementById('settGreetingBanner');
                        if (chk) chk.checked = false;
                    }
                    if (setts.autoCollapse && window.innerWidth > 991) {
                        document.body.classList.add('sidebar-closed');
                        const chk = document.getElementById('settAutoCollapse');
                        if (chk) chk.checked = true;
                    }
                } catch(e) {}
            }
        });
    </script>

    <!-- ════════════════════════════════════════════════════════════════ -->
    <!-- TEACHER PROFILE MODAL -->
    <!-- ════════════════════════════════════════════════════════════════ -->
    <div class="t-modal-backdrop" id="teacherProfileModal" style="display: none;">
        <div class="t-modal-dialog">
            <div class="t-modal-header">
                <div class="t-modal-title">
                    <i class="fas fa-user-shield" style="color: #7c3aed;"></i>
                    <div>
                        <h5>Teacher Profile & Account</h5>
                        <p>Manage your profile picture, security password, and personal info.</p>
                    </div>
                </div>
                <button type="button" class="t-modal-close" onclick="closeTeacherProfileModal()">&times;</button>
            </div>
            
            <div class="t-modal-tabs">
                <button type="button" class="t-tab-btn active" onclick="switchTeacherProfileTab('picture')">
                    <i class="fas fa-camera"></i> Profile Picture
                </button>
                <button type="button" class="t-tab-btn" onclick="switchTeacherProfileTab('password')">
                    <i class="fas fa-key"></i> Update Password
                </button>
                <button type="button" class="t-tab-btn" onclick="switchTeacherProfileTab('info')">
                    <i class="fas fa-id-card"></i> Personal Info
                </button>
            </div>

            <div class="t-modal-body">
                <!-- TAB 1: PROFILE PICTURE -->
                <div class="t-tab-pane active" id="tpTabPicture">
                    <form id="teacherPictureForm" onsubmit="handleProfilePictureUpload(event)" enctype="multipart/form-data">
                        @csrf
                        <div class="t-avatar-upload-box">
                            <div class="t-avatar-preview" id="tAvatarPreview" style="overflow:hidden;">
                                @if(!empty($teacherAvatarUrl))
                                    <img src="{{ $teacherAvatarUrl }}" id="tAvatarImg" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" onerror="this.style.display='none'; if(document.getElementById('tAvatarInitial')) document.getElementById('tAvatarInitial').style.display='flex';">
                                    <span id="tAvatarInitial" style="display:none;">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                @else
                                    <span id="tAvatarInitial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    <img src="" id="tAvatarImg" alt="" style="display:none;width:100%;height:100%;object-fit:cover;border-radius:50%;">
                                @endif
                            </div>
                            <div class="t-avatar-upload-info">
                                <h4>{{ $user->name }}</h4>
                                <p>Upload a clean square profile image (JPG, PNG, WEBP, max 4MB).</p>
                                <div class="t-file-btn-wrap">
                                    <label for="teacherPhotoFile" class="t-btn t-btn-primary">
                                        <i class="fas fa-cloud-upload-alt"></i> Choose Image
                                    </label>
                                    <input type="file" id="teacherPhotoFile" name="photo" accept="image/*" onchange="previewTeacherAvatar(this)" style="display:none;">
                                    <span class="t-file-name" id="teacherPhotoFileName">No file chosen</span>
                                </div>
                            </div>
                        </div>

                        <div class="t-modal-footer">
                            <button type="button" class="t-btn t-btn-secondary" onclick="closeTeacherProfileModal()">Cancel</button>
                            <button type="submit" class="t-btn t-btn-purple" id="btnSavePicture">
                                <i class="fas fa-save"></i> Save Profile Picture
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB 2: UPDATE PASSWORD -->
                <div class="t-tab-pane" id="tpTabPassword" style="display: none;">
                    <form id="teacherPasswordForm" onsubmit="handlePasswordUpdate(event)">
                        @csrf
                        <div class="t-form-group">
                            <label><i class="fas fa-lock"></i> Current Password <span class="text-danger">*</span></label>
                            <div class="t-input-pass-wrap">
                                <input type="password" id="tCurrentPass" name="current_password" required placeholder="Enter current password">
                                <i class="fas fa-eye t-toggle-pass" onclick="togglePassVisibility('tCurrentPass', this)"></i>
                            </div>
                        </div>

                        <div class="t-form-group">
                            <label><i class="fas fa-key"></i> New Password <span class="text-danger">*</span></label>
                            <div class="t-input-pass-wrap">
                                <input type="password" id="tNewPass" name="new_password" required placeholder="At least 6 characters" oninput="checkPassStrength(this.value)">
                                <i class="fas fa-eye t-toggle-pass" onclick="togglePassVisibility('tNewPass', this)"></i>
                            </div>
                            <div class="t-pass-strength" id="tPassStrengthBar" style="display:none; margin-top:6px; height:4px; background:#e2e8f0; border-radius:4px; overflow:hidden;">
                                <div class="t-pass-strength-fill" id="tPassStrengthFill" style="height:100%; width:0%; transition:all 0.2s;"></div>
                            </div>
                        </div>

                        <div class="t-form-group">
                            <label><i class="fas fa-check-double"></i> Confirm New Password <span class="text-danger">*</span></label>
                            <div class="t-input-pass-wrap">
                                <input type="password" id="tConfirmPass" name="new_password_confirmation" required placeholder="Re-enter new password">
                                <i class="fas fa-eye t-toggle-pass" onclick="togglePassVisibility('tConfirmPass', this)"></i>
                            </div>
                        </div>

                        <div class="t-modal-footer">
                            <button type="button" class="t-btn t-btn-secondary" onclick="closeTeacherProfileModal()">Cancel</button>
                            <button type="submit" class="t-btn t-btn-purple" id="btnSavePassword">
                                <i class="fas fa-shield-alt"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB 3: PERSONAL INFO -->
                <div class="t-tab-pane" id="tpTabInfo" style="display: none;">
                    <div class="t-info-grid">
                        <div class="t-info-card">
                            <span class="t-info-label">Full Name</span>
                            <span class="t-info-val">{{ $user->name }}</span>
                        </div>
                        <div class="t-info-card">
                            <span class="t-info-label">Email Address</span>
                            <span class="t-info-val">{{ $user->email }}</span>
                        </div>
                        <div class="t-info-card">
                            <span class="t-info-label">Employee Code</span>
                            <span class="t-info-val">{{ $staff?->employee_id ?? 'EMP-' . str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="t-info-card">
                            <span class="t-info-label">Designation</span>
                            <span class="t-info-val">{{ $staff?->designation?->name ?? 'Teacher' }}</span>
                        </div>
                        <div class="t-info-card">
                            <span class="t-info-label">Department</span>
                            <span class="t-info-val">{{ $staff?->department?->name ?? 'Academic' }}</span>
                        </div>
                        <div class="t-info-card">
                            <span class="t-info-label">Phone Number</span>
                            <span class="t-info-val">{{ $staff?->phone ?? $user->phone ?? 'Not specified' }}</span>
                        </div>
                    </div>
                    <div class="t-modal-footer">
                        <button type="button" class="t-btn t-btn-secondary" onclick="closeTeacherProfileModal()">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════ -->
    <!-- TEACHER DASHBOARD SETTINGS MODAL -->
    <!-- ════════════════════════════════════════════════════════════════ -->
    <div class="t-modal-backdrop" id="teacherSettingsModal" style="display: none;">
        <div class="t-modal-dialog">
            <div class="t-modal-header">
                <div class="t-modal-title">
                    <i class="fas fa-sliders-h" style="color: #3b82f6;"></i>
                    <div>
                        <h5>Teacher Dashboard Settings</h5>
                        <p>Customize your workspace appearance, notifications, and preferences.</p>
                    </div>
                </div>
                <button type="button" class="t-modal-close" onclick="closeTeacherSettingsModal()">&times;</button>
            </div>
            
            <div class="t-modal-body">
                <form id="teacherSettingsForm" onsubmit="handleSettingsSave(event)">
                    @csrf
                    
                    <!-- SECTION 1: THEME & COLOR ACCENT -->
                    <div class="t-setting-section">
                        <div class="t-setting-title"><i class="fas fa-palette" style="color:#7c3aed;"></i> Theme Accent Style</div>
                        <div class="t-theme-picker">
                            <label class="t-theme-opt">
                                <input type="radio" name="theme_accent" value="purple" checked onchange="applyLiveTheme('purple')">
                                <div class="t-theme-box" style="background: linear-gradient(135deg, #7c3aed, #6d28d9);">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Royal Purple</span>
                            </label>
                            <label class="t-theme-opt">
                                <input type="radio" name="theme_accent" value="ocean" onchange="applyLiveTheme('ocean')">
                                <div class="t-theme-box" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Ocean Blue</span>
                            </label>
                            <label class="t-theme-opt">
                                <input type="radio" name="theme_accent" value="emerald" onchange="applyLiveTheme('emerald')">
                                <div class="t-theme-box" style="background: linear-gradient(135deg, #059669, #047857);">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Emerald Green</span>
                            </label>
                            <label class="t-theme-opt">
                                <input type="radio" name="theme_accent" value="dark" onchange="applyLiveTheme('dark')">
                                <div class="t-theme-box" style="background: linear-gradient(135deg, #1e293b, #0f172a);">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Dark Slate</span>
                            </label>
                        </div>
                    </div>

                    <!-- SECTION 2: DASHBOARD PREFERENCES -->
                    <div class="t-setting-section">
                        <div class="t-setting-title"><i class="fas fa-desktop" style="color:#3b82f6;"></i> Display & Layout</div>
                        
                        <div class="t-switch-row">
                            <div>
                                <strong>Greeting Alert Banner</strong>
                                <small>Show the top clock & daily event banner on dashboard load</small>
                            </div>
                            <label class="t-toggle-switch">
                                <input type="checkbox" id="settGreetingBanner" checked>
                                <span class="t-slider"></span>
                            </label>
                        </div>

                        <div class="t-switch-row">
                            <div>
                                <strong>Auto-Collapse Sidebar</strong>
                                <span style="display:block;"><small>Keep navigation sidebar collapsed by default on larger screens</small></span>
                            </div>
                            <label class="t-toggle-switch">
                                <input type="checkbox" id="settAutoCollapse">
                                <span class="t-slider"></span>
                            </label>
                        </div>
                    </div>

                    <!-- SECTION 3: NOTIFICATIONS & SOUND -->
                    <div class="t-setting-section">
                        <div class="t-setting-title"><i class="fas fa-bell" style="color:#f59e0b;"></i> Notifications & Sound</div>
                        
                        <div class="t-switch-row">
                            <div>
                                <strong>Audio Sound Alerts</strong>
                                <small>Play a chime sound when a new notice or update is received</small>
                            </div>
                            <label class="t-toggle-switch">
                                <input type="checkbox" id="settSoundAlerts" checked>
                                <span class="t-slider"></span>
                            </label>
                        </div>

                        <div class="t-switch-row">
                            <div>
                                <strong>Desktop Push Alerts</strong>
                                <small>Show floating popups for unread messages</small>
                            </div>
                            <label class="t-toggle-switch">
                                <input type="checkbox" id="settDesktopNotifs" checked>
                                <span class="t-slider"></span>
                            </label>
                        </div>
                    </div>

                    <!-- SECTION 4: AUTO REFRESH -->
                    <div class="t-setting-section">
                        <div class="t-setting-title"><i class="fas fa-sync" style="color:#10b981;"></i> Stats Auto-Refresh</div>
                        <div class="t-form-group" style="margin-bottom:0;">
                            <label>Refresh Interval for Dashboard Counters</label>
                            <select id="settAutoRefresh" class="t-select-control">
                                <option value="0">Off (Manual Refresh)</option>
                                <option value="300" selected>Every 5 Minutes</option>
                                <option value="600">Every 10 Minutes</option>
                            </select>
                        </div>
                    </div>

                    <div class="t-modal-footer">
                        <button type="button" class="t-btn t-btn-secondary" onclick="closeTeacherSettingsModal()">Cancel</button>
                        <button type="submit" class="t-btn t-btn-purple" id="btnSaveSettings">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('partials.realtime_notifications')
</body>
</html>


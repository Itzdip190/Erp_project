<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Apply Leave — Teacher Portal</title>
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
            --shadow:0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
            --shadow-lg:0 10px 25px -5px rgba(0,0,0,0.08), 0 8px 10px -6px rgba(0,0,0,0.01);
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

        /* Notification Bell Icon & Counter */
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

        /* ─── CONTENT AREA ────────────────────────────────────────── */
        .content-area{padding:32px;flex:1;}

        /* Balance cards grid */
        .balance-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .balance-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: var(--shadow);
        }
        .balance-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: var(--purple-light);
            color: var(--purple);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .balance-details h4 {
            font-size: 12px;
            color: var(--t2);
            font-weight: 600;
            margin-bottom: 2px;
        }
        .balance-details .val {
            font-size: 20px;
            font-weight: 800;
            color: var(--t1);
        }
        .balance-details .sub {
            font-size: 10.5px;
            color: var(--t3);
        }

        /* Two columns details */
        .columns-layout {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 30px;
        }
        @media(max-width:992px){.columns-layout{grid-template-columns:1fr;}}

        .card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--shadow);
        }
        .card-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 16px;
            font-weight: 800;
            color: var(--t1);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Form styling */
        .form-group {
            margin-bottom: 16px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--t1);
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            height: 40px;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13.5px;
            outline: none;
            background: #f8fafc;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            background: #fff;
            border-color: var(--purple);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }
        textarea.form-control {
            height: 100px;
            resize: none;
        }
        .btn-submit {
            background: linear-gradient(135deg, var(--purple), #6d28d9);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13.5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2);
            width: 100%;
            justify-content: center;
        }
        .btn-submit:hover {
            opacity: 0.95;
        }

        /* Table styling */
        .tbl {
            width: 100%;
            border-collapse: collapse;
        }
        .tbl th, .tbl td {
            padding: 14px;
            font-size: 13px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            vertical-align: middle;
        }
        .tbl th {
            background: #f8fafc;
            font-weight: 700;
            color: var(--t2);
        }
        .tbl tr:last-child td {
            border-bottom: none;
        }
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            display: inline-block;
        }
        .badge-pending { background: var(--gold-light); color: var(--gold); }
        .badge-approved { background: var(--green-light); color: var(--green); }
        .badge-rejected { background: var(--red-light); color: var(--red); }
        .badge-type { background: var(--blue-light); color: var(--blue); }

        .remark-box {
            font-size: 12px;
            line-height: 1.4;
            max-width: 280px;
        }
        .remark-box.rejected {
            color: var(--red);
            font-weight: 600;
        }
        .remark-box.approved {
            color: var(--green);
            font-weight: 600;
        }

        .form-row-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        /* ─── COMPREHENSIVE MOBILE RESPONSIVE STYLING ─────────────── */
        @media (max-width: 991px) {
            body.sidebar-closed .sidebar { display: flex !important; }
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                transform: translateX(-100%);
                z-index: 1000;
                transition: transform 0.3s ease;
            }
            .sidebar.open { transform: translateX(0); }
            
            .top-header {
                padding: 8px 12px !important;
                height: auto !important;
                min-height: 56px;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
                flex-wrap: nowrap !important;
                gap: 6px !important;
            }
            .th-actions {
                width: auto !important;
                justify-content: flex-end !important;
                gap: 6px !important;
                flex-shrink: 0 !important;
            }
            .th-title {
                min-width: 0;
                flex: 1;
            }
            .th-title h2 {
                font-size: 14px !important;
                white-space: nowrap !important;
                max-width: 140px !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            .th-title p { display: none !important; }
            .th-user-info { display: none !important; }
            .th-user-pill { padding: 2px !important; background: transparent !important; border: none !important; }
            .th-user-img { width: 32px !important; height: 32px !important; font-size: 12px !important; }
            .th-icon-btn { width: 36px !important; height: 36px !important; font-size: 14px !important; }
            .th-logout-btn { width: 36px !important; height: 36px !important; margin-right: 0 !important; }

            .content-area { padding: 14px 10px !important; }
            .balance-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)) !important; gap: 12px !important; margin-bottom: 20px !important; }
            .balance-card { padding: 12px 14px !important; gap: 10px !important; }
            .balance-icon { width: 38px !important; height: 38px !important; font-size: 1rem !important; }
            .balance-details .val { font-size: 17px !important; }
            .columns-layout { grid-template-columns: 1fr !important; gap: 16px !important; }
            .card { padding: 16px 14px !important; border-radius: 14px !important; }
        }

        @media (max-width: 576px) {
            .top-header { padding: 6px 8px !important; }
            .th-title h2 { max-width: 110px !important; font-size: 13px !important; }
            .hamburger-btn { margin-right: 4px !important; width: 32px !important; height: 32px !important; font-size: 13px !important; }
            .th-icon-btn { width: 32px !important; height: 32px !important; font-size: 13px !important; }
            .th-logout-btn { width: 32px !important; height: 32px !important; }
            .th-user-img { width: 30px !important; height: 30px !important; font-size: 11px !important; }
            .th-actions { gap: 4px !important; }
            .lang-switch-container { transform: scale(0.85); transform-origin: center right; margin: 0 -4px; }
            .card-title { font-size: 14px !important; margin-bottom: 14px !important; }
            .form-group { margin-bottom: 12px !important; }
            .form-label { font-size: 12px !important; }
            .form-control { height: 38px !important; font-size: 12.5px !important; padding: 6px 10px !important; }
            .tbl th, .tbl td { padding: 10px 8px !important; font-size: 11.5px !important; }
            .form-row-2col { grid-template-columns: 1fr !important; gap: 10px !important; }
            .balance-grid { grid-template-columns: 1fr 1fr !important; gap: 8px !important; }
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
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleTeacherSidebar()"></div>

    <!-- ─── SIDEBAR ─────────────────────────────────────────────── -->
    <aside class="sidebar" id="teacherSidebar">
        <!-- Logo -->
        <div class="sb-logo">
            <a href="{{ route('teacher.dashboard') }}" class="sb-logo-left" title="Teacher Dashboard">
                <div class="sb-logo-icon"><i class="fas fa-graduation-cap"></i></div>
                <div class="sb-logo-text">
                    <strong>Leave Portal</strong>
                    <span>Teacher Portal</span>
                </div>
            </a>
            <button type="button" class="sb-close-btn" onclick="toggleTeacherSidebar(event)" aria-label="Close sidebar">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <!-- Profile Badge Box -->
        <div class="sb-profile">
            <div class="sb-avatar">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="sb-prof-info">
                <h4>{{ $user->name }}</h4>
                <p>{{ $staff->employee_id ?? 'EMP-TEACHER' }}</p>
                <span class="sb-prof-badge">{{ $staff->designation?->name ?? 'Staff Member' }}</span>
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
                    <h2>Leave Application Portal</h2>
                    <p>Apply for leaves and manage your balances</p>
                </div>
            </div>
            <div class="th-actions">
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
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('teacher-leave-logout-form').submit();" class="th-icon-btn th-logout-btn" title="Logout" style="width:38px;height:38px;border-radius:50%;background:rgba(239,68,68,0.1);color:#ef4444;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;margin-right:4px;">
                    <i class="fas fa-power-off"></i>
                </a>
                <form id="teacher-leave-logout-form" action="{{ route('logout.post') }}" method="POST" style="display: none;">
                    @csrf
                </form>

                <!-- User Profile Pill -->
                <a href="{{ route('teacher.dashboard') }}" class="th-user-pill" style="text-decoration:none;">
                    <div class="th-user-img">
                        @if(!empty($user->photo))
                            <img src="{{ asset($user->photo) }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="th-user-info">
                        <h5>{{ $user->name }}</h5>
                        <p>{{ $staff->designation?->name ?? 'Staff Member' }}</p>
                    </div>
                </a>
            </div>
        </header>

        <!-- Content Area -->
        <main class="content-area">

            @if(session('success'))
                <div style="background:var(--green-light);border:1px solid #bbf7d0;color:#065f46;padding:14px 20px;border-radius:14px;margin-bottom:24px;font-weight:600;">
                    <i class="fas fa-check-circle" style="margin-right:6px;"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background:var(--red-light);border:1px solid #fca5a5;color:#991b1b;padding:14px 20px;border-radius:14px;margin-bottom:24px;font-weight:600;">
                    <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i> {{ $errors->first() }}
                </div>
            @endif

            <!-- 1. LEAVE SUMMARIES GRID -->
            <div class="balance-grid">
                @foreach($leaveSummaries as $sum)
                <div class="balance-card">
                    <div class="balance-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="balance-details">
                        <h4>{{ $sum['name'] }}</h4>
                        <div class="val">{{ $sum['remaining'] }}</div>
                        <div class="sub">Allowed: {{ $sum['allowed'] }} | Availed: {{ $sum['availed'] }}</div>
                    </div>
                </div>
                @endforeach
                @if($leaveSummaries->isEmpty())
                <div style="grid-column:1/-1;text-align:center;padding:24px;background:#fff;border:1px solid var(--border);border-radius:12px;color:var(--t3);">
                    No leave type configurations found for your profile. Please contact the administrator.
                </div>
                @endif
            </div>

            <!-- 2. TWO COLUMN DETAILS -->
            <div class="columns-layout">
                
                <!-- APPLY FORM -->
                <div class="card">
                    <h3 class="card-title"><i class="fas fa-paper-plane" style="color:var(--purple);"></i> Apply For Leave</h3>
                    <form method="POST" action="{{ route('teacher.leave.store') }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Leave Type *</label>
                            <select name="leave_type_id" class="form-control" style="height:44px;" required>
                                <option value="">Select Leave Type</option>
                                @foreach($leaveSummaries as $sum)
                                    <option value="{{ $sum['id'] }}">{{ $sum['name'] }} (Remaining: {{ $sum['remaining'] }} days)</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-row-2col">
                            <div class="form-group">
                                <label class="form-label">From Date *</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">To Date *</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Reason *</label>
                            <textarea name="reason" class="form-control" placeholder="Enter reason for leave..." required></textarea>
                        </div>
                        
                        <button type="submit" class="btn-submit"><i class="fas fa-check"></i> Submit Leave Request</button>
                    </form>
                </div>

                <!-- HISTORY -->
                <div class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
                    <div style="padding: 24px 24px 12px 24px;">
                        <h3 class="card-title" style="margin-bottom:0;"><i class="fas fa-history" style="color:var(--purple);"></i> Leave Request History</h3>
                    </div>
                    <div style="overflow-x: auto; flex: 1;">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Leave Type</th>
                                    <th>Dates</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Admin Remark / Rejection Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($applications as $app)
                                @php
                                    $start = \Carbon\Carbon::parse($app->start_date);
                                    $end = \Carbon\Carbon::parse($app->end_date);
                                    $duration = $start->diffInDays($end) + 1;
                                @endphp
                                <tr>
                                    <td><span class="badge badge-type">{{ $app->leave_type }}</span></td>
                                    <td>
                                        <div style="font-weight:600;color:var(--t1);">{{ $app->start_date ? $app->start_date->format('d/m/Y') : '' }}</div>
                                        <div style="font-size:11px;color:var(--t3);">to {{ $app->end_date ? $app->end_date->format('d/m/Y') : '' }}</div>
                                    </td>
                                    <td><strong>{{ $duration }} {{ $duration > 1 ? 'days' : 'day' }}</strong></td>
                                    <td>
                                        @if($app->status === 'pending')
                                            <span class="badge badge-pending">Pending</span>
                                        @elseif($app->status === 'approved')
                                            <span class="badge badge-approved">Approved</span>
                                        @else
                                            <span class="badge badge-rejected">Rejected</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($app->status === 'rejected')
                                            <div class="remark-box rejected">
                                                <i class="fas fa-circle-xmark" style="margin-right:4px;"></i> Rejection Reason: {{ $app->rejection_reason ?? $app->admin_remark ?? 'Not approved by admin.' }}
                                            </div>
                                        @elseif($app->status === 'approved')
                                            @if(!empty($app->admin_remark))
                                                <div class="remark-box approved">
                                                    <i class="fas fa-circle-check" style="margin-right:4px;"></i> Approval Remark: {{ $app->admin_remark }}
                                                </div>
                                            @else
                                                <span style="font-size:12px; color:var(--t3);">--</span>
                                            @endif
                                        @else
                                            <span style="font-size:12px; color:var(--t3); font-style:italic;">Awaiting approval</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" style="text-align:center; padding:40px; color:var(--t3);">
                                        <i class="fas fa-calendar-times" style="font-size:24px;margin-bottom:10px;display:block;"></i>
                                        No leave requests submitted yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </main>
    </div>

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

        // Close dropdown when clicking outside
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

                    html += `
                        <div class="notif-item ${unreadClass}" onclick="markNotifAsRead(${item.id})">
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

        window.syncTeacherLeaveUI = function() {
            fetch("{{ route('teacher.leave.history-json') }}", {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;

                // Update Leave History Table
                const tbody = document.querySelector('.table-leave-history tbody');
                if (tbody && data.applications) {
                    if (data.applications.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="5" style="text-align:center; padding:40px; color:var(--t3);">
                                    <i class="fas fa-calendar-times" style="font-size:24px;margin-bottom:10px;display:block;"></i>
                                    No leave requests submitted yet.
                                </td>
                            </tr>`;
                    } else {
                        let html = '';
                        data.applications.forEach(app => {
                            let badgeHtml = '';
                            if (app.status === 'pending') {
                                badgeHtml = '<span class="badge badge-pending">Pending</span>';
                            } else if (app.status === 'approved') {
                                badgeHtml = '<span class="badge badge-approved">Approved</span>';
                            } else {
                                badgeHtml = '<span class="badge badge-rejected">Rejected</span>';
                            }

                            let remarkHtml = '';
                            if (app.status === 'rejected') {
                                remarkHtml = `
                                    <div class="remark-box rejected">
                                        <i class="fas fa-circle-xmark" style="margin-right:4px;"></i> Rejection Reason: ${escapeHtml(app.rejection_reason || app.admin_remark || 'Not approved by admin.')}
                                    </div>`;
                            } else if (app.status === 'approved') {
                                if (app.admin_remark) {
                                    remarkHtml = `
                                        <div class="remark-box approved">
                                            <i class="fas fa-circle-check" style="margin-right:4px;"></i> Approval Remark: ${escapeHtml(app.admin_remark)}
                                        </div>`;
                                } else {
                                    remarkHtml = `<span style="font-size:12px; color:var(--t3);">--</span>`;
                                }
                            } else {
                                remarkHtml = `<span style="font-size:12px; color:var(--t3); font-style:italic;">Awaiting approval</span>`;
                            }

                            html += `
                                <tr>
                                    <td><span class="badge badge-type">${escapeHtml(app.leave_type)}</span></td>
                                    <td>
                                        <div style="font-weight:600;color:var(--t1);">${app.start_date_fmt}</div>
                                        <div style="font-size:11px;color:var(--t3);">to ${app.end_date_fmt}</div>
                                    </td>
                                    <td><strong>${app.duration} ${app.duration > 1 ? 'days' : 'day'}</strong></td>
                                    <td>${badgeHtml}</td>
                                    <td>${remarkHtml}</td>
                                </tr>`;
                        });
                        tbody.innerHTML = html;
                    }
                }

                // Update Leave Balances Cards
                if (data.balances) {
                    data.balances.forEach(b => {
                        const cardElement = Array.from(document.querySelectorAll('.stat-card')).find(c => c.textContent.includes(b.leave_type));
                        if (cardElement) {
                            const valEl = cardElement.querySelector('.stat-value');
                            if (valEl) valEl.textContent = b.remaining;
                            const subEl = cardElement.querySelector('.stat-sub');
                            if (subEl) subEl.textContent = `Allowed: ${b.allowed} | Availed: ${b.availed}`;
                        }
                    });
                }
            })
            .catch(err => console.error('Error syncing leave UI:', err));
        };

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

            fetchTeacherNotifications();
            window.syncTeacherLeaveUI();
            setInterval(fetchTeacherNotifications, 15000);
            setInterval(window.syncTeacherLeaveUI, 10000);
        });
    </script>
    @include('partials.realtime_notifications')
</body>
</html>


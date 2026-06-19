<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Dashboard — SchoolCloud ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{
    --navy:#1a1f3c;--navy2:#12172e;
    --gold:#f59e0b;--gold-bg:rgba(245,158,11,.15);
    --green:#10b981;--red:#ef4444;--purple:#8b5cf6;--blue:#3b82f6;
    --orange:#f97316;
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
.sb-hdr.open .sb-hdr-arrow{transform:rotate(180deg);}

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
.sb-help{
    background:linear-gradient(135deg,rgba(245,158,11,.18),rgba(245,158,11,.04));
    border:1px solid rgba(245,158,11,.22);border-radius:9px;padding:11px;margin-bottom:8px;
}
.sb-help p{color:rgba(255,255,255,.5);font-size:10px;margin-bottom:5px;}
.sb-help strong{display:block;color:#fff;font-size:11.5px;margin-bottom:8px;}
.btn-support{
    display:block;text-align:center;background:var(--gold);color:var(--navy);
    font-size:11px;font-weight:700;border-radius:6px;padding:7px;text-decoration:none;transition:.2s;
}
.btn-support:hover{background:#d97706;}
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

/* ─── PAGE ────────────────────────────────────────────────── */
.pg{padding:20px 22px;}

/* WELCOME BANNER */
.welcome-strip{
    background:linear-gradient(135deg,var(--navy) 0%,#2d3561 100%);
    border-radius:14px;padding:22px 26px;margin-bottom:18px;
    display:flex;align-items:center;justify-content:space-between;
    position:relative;overflow:hidden;
}
.welcome-strip::after{
    content:'';position:absolute;right:-20px;top:-20px;
    width:160px;height:160px;border-radius:50%;
    background:radial-gradient(circle,rgba(245,158,11,.18) 0%,transparent 70%);
}
.welcome-strip::before{
    content:'';position:absolute;left:40%;bottom:-30px;
    width:100px;height:100px;border-radius:50%;
    background:radial-gradient(circle,rgba(139,92,246,.12) 0%,transparent 70%);
}
.ws-left h2{color:#fff;font-size:20px;font-weight:800;font-family:'Plus Jakarta Sans',sans-serif;}
.ws-left p{color:rgba(255,255,255,.55);font-size:12px;margin-top:4px;}
.ws-badges{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px;}
.ws-badge{
    display:inline-flex;align-items:center;gap:5px;
    background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);
    color:rgba(255,255,255,.85);font-size:11px;font-weight:600;
    border-radius:20px;padding:4px 11px;
}
.ws-badge i{font-size:10px;}
.ws-right{font-size:64px;filter:opacity(.18);position:relative;z-index:1;}

/* STAT CARDS */
.stats-row{
    display:grid;grid-template-columns:repeat(4,1fr);
    gap:12px;margin-bottom:18px;
}
.stat{
    background:#fff;border:1px solid var(--border);border-radius:13px;
    padding:15px 15px 12px;box-shadow:var(--shadow);
    transition:transform .2s,box-shadow .2s;display:flex;flex-direction:column;
}
.stat:hover{transform:translateY(-2px);box-shadow:var(--shadow-lg);}
.stat-top{display:flex;align-items:flex-start;gap:10px;margin-bottom:8px;}
.stat-ico{
    width:44px;height:44px;border-radius:11px;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;font-size:18px;
}
.stat-info{flex:1;min-width:0;}
.stat-lbl{font-size:10px;color:var(--t2);font-weight:500;margin-bottom:2px;}
.stat-val{font-size:22px;font-weight:800;color:var(--t1);font-family:'Plus Jakarta Sans',sans-serif;line-height:1.1;}
.stat-sub{font-size:10px;color:var(--t3);margin-top:3px;}
.stat-spark-wrap{width:100%;height:38px;margin-top:auto;position:relative;overflow:hidden;}
.stat-spark{position:absolute;top:0;left:0;width:100% !important;height:100% !important;}

/* MAIN BODY GRID */
.body-grid{
    display:grid;grid-template-columns:1fr 1fr 270px;
    gap:14px;margin-bottom:18px;align-items:start;
}

/* CARD */
.card{
    background:#fff;border:1px solid var(--border);
    border-radius:13px;box-shadow:var(--shadow);overflow:hidden;
}
.card-hdr{
    padding:14px 17px 0;
    display:flex;align-items:center;justify-content:space-between;
}
.card-title{font-size:13.5px;font-weight:700;color:var(--t1);}
.view-all{font-size:11px;color:var(--gold);text-decoration:none;font-weight:600;}
.view-all:hover{text-decoration:underline;}
.card-body{padding:12px 17px 15px;}

/* Attendance chart card */
.att-summary{
    display:flex;gap:10px;margin-bottom:12px;flex-wrap:wrap;
}
.att-chip{
    flex:1;min-width:70px;
    background:var(--page);border:1px solid var(--border);
    border-radius:9px;padding:9px 10px;text-align:center;
}
.att-chip-num{font-size:18px;font-weight:800;font-family:'Plus Jakarta Sans',sans-serif;}
.att-chip-lbl{font-size:9.5px;color:var(--t2);margin-top:1px;}

/* Recent attendance list */
.att-list{list-style:none;}
.att-item{
    display:flex;align-items:center;justify-content:space-between;
    padding:8px 0;border-bottom:1px solid var(--border);font-size:12px;
}
.att-item:last-child{border:none;}
.att-date{color:var(--t2);}
.status-badge{
    display:inline-flex;align-items:center;gap:4px;
    font-size:10px;font-weight:700;border-radius:20px;padding:2px 9px;
}
.s-present{background:rgba(16,185,129,.1);color:#10b981;}
.s-absent{background:rgba(239,68,68,.1);color:#ef4444;}
.s-late{background:rgba(245,158,11,.1);color:#f59e0b;}
.s-holiday{background:rgba(107,114,128,.1);color:#6b7280;}

/* Timetable */
.timetable{list-style:none;margin-top:4px;}
.tt-item{
    display:flex;align-items:center;gap:10px;
    padding:9px 0;border-bottom:1px solid var(--border);
}
.tt-item:last-child{border:none;}
.tt-time{
    font-size:10px;color:var(--t2);font-weight:600;
    min-width:50px;
}
.tt-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
.tt-info strong{font-size:12px;font-weight:600;color:var(--t1);display:block;}
.tt-info span{font-size:10px;color:var(--t3);}

/* AI / Notices panel */
.notice-list{list-style:none;padding:10px 0 4px;}
.notice-item{
    display:flex;align-items:flex-start;gap:9px;
    padding:10px;border-radius:9px;margin-bottom:8px;
    background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.07);
    transition:.18s;
}
.notice-item:hover{background:rgba(255,255,255,.09);}
.notice-ico{
    width:30px;height:30px;border-radius:7px;
    display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0;
}
.notice-txt{color:rgba(255,255,255,.75);font-size:11px;line-height:1.5;}
.notice-time{font-size:9.5px;color:rgba(255,255,255,.35);margin-top:3px;}

/* Quick actions */
.qa-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-top:4px;}
.qa-btn{
    display:flex;flex-direction:column;align-items:center;gap:5px;
    padding:11px 6px;background:var(--page);
    border:1px solid var(--border);border-radius:10px;
    text-decoration:none;transition:.2s;cursor:pointer;
}
.qa-btn:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,0,0,.1);border-color:transparent;background:#fff;}
.qa-ico{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:15px;}
.qa-lbl{font-size:10px;font-weight:600;color:var(--t1);text-align:center;line-height:1.3;}

/* Fee Summary donut */
.donut-wrap{display:flex;flex-direction:column;align-items:center;padding:4px 0;}
.donut-rel{position:relative;width:130px;height:130px;}
.donut-center{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none;}
.donut-center strong{display:block;font-size:13px;font-weight:800;font-family:'Plus Jakarta Sans',sans-serif;}
.donut-center small{font-size:9.5px;color:var(--t2);}
.legend{width:100%;margin-top:10px;}
.leg-row{display:flex;align-items:center;justify-content:space-between;font-size:11px;padding:3.5px 0;}
.leg-left{display:flex;align-items:center;gap:6px;color:var(--t2);}
.leg-dot{width:8px;height:8px;border-radius:50%;}
.leg-val{font-weight:700;color:var(--t1);}

/* Bottom row */
.bottom-row{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:18px;}

/* Donut-centered stats card */
.circle-prog-wrap{display:flex;justify-content:center;padding:8px 0 4px;}
.circle-prog-rel{position:relative;width:130px;height:130px;}
.circle-prog-center{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;}
.circle-prog-center strong{display:block;font-size:22px;font-weight:800;font-family:'Plus Jakarta Sans',sans-serif;}
.circle-prog-center small{font-size:10px;color:var(--t2);}

/* Days of week chips */
.day-chips{display:flex;gap:4px;justify-content:center;margin-top:10px;flex-wrap:wrap;}
.day-chip{
    width:32px;height:32px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    font-size:10.5px;font-weight:700;border:2px solid transparent;
}
.day-p{background:rgba(16,185,129,.15);color:var(--green);border-color:rgba(16,185,129,.3);}
.day-a{background:rgba(239,68,68,.1);color:var(--red);border-color:rgba(239,68,68,.2);}
.day-h{background:var(--page);color:var(--t3);border-color:var(--border);}

/* AI (dark) panel */
.ai-panel{
    background:var(--navy);border-radius:13px;
    border:1px solid rgba(255,255,255,.08);box-shadow:var(--shadow);overflow:hidden;
}
.ai-hdr{
    padding:14px 15px 12px;border-bottom:1px solid rgba(255,255,255,.08);
    display:flex;align-items:center;gap:9px;
}
.ai-hdr-ico{width:32px;height:32px;border-radius:8px;background:var(--gold-bg);color:var(--gold);display:flex;align-items:center;justify-content:center;font-size:14px;}
.ai-hdr h3{color:#fff;font-size:13px;font-weight:700;font-family:'Plus Jakarta Sans',sans-serif;}
.ai-hdr p{color:rgba(255,255,255,.45);font-size:10px;}
.ai-body{padding:12px;}
.ai-item{
    display:flex;align-items:flex-start;gap:9px;padding:10px;border-radius:9px;margin-bottom:8px;
    background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.07);transition:.18s;
}
.ai-item:hover{background:rgba(255,255,255,.09);}
.ai-item-ico{width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0;}
.ai-item-txt{color:rgba(255,255,255,.75);font-size:11px;line-height:1.5;flex:1;}
.ai-view-btn{
    background:rgba(255,255,255,.1);border:none;color:rgba(255,255,255,.8);
    font-size:10px;font-weight:600;border-radius:5px;padding:4px 9px;cursor:pointer;
    white-space:nowrap;flex-shrink:0;transition:.18s;align-self:center;
}
.ai-view-btn:hover{background:var(--gold);color:var(--navy);}
.ai-chat{padding:0 12px 12px;}
.ai-input-row{
    display:flex;gap:7px;background:rgba(255,255,255,.07);
    border:1px solid rgba(255,255,255,.12);border-radius:9px;padding:5px 7px;
}
.ai-input{flex:1;background:none;border:none;outline:none;color:#fff;font-size:11.5px;}
.ai-input::placeholder{color:rgba(255,255,255,.3);}
.ai-send{
    width:30px;height:30px;border-radius:7px;background:var(--gold);
    border:none;color:var(--navy);font-size:12px;cursor:pointer;
    display:flex;align-items:center;justify-content:center;transition:.18s;flex-shrink:0;
}
.ai-send:hover{background:#d97706;}
.ai-resp{
    margin:0 12px 12px;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.18);
    border-radius:9px;padding:10px;color:rgba(255,255,255,.8);font-size:11px;line-height:1.6;display:none;
}
.dots{display:flex;gap:4px;padding:2px 0;}
.dots span{width:6px;height:6px;border-radius:50%;background:var(--gold);animation:bk 1.1s infinite;}
.dots span:nth-child(2){animation-delay:.2s;}
.dots span:nth-child(3){animation-delay:.4s;}
@keyframes bk{0%,80%,100%{transform:scale(.7);opacity:.5;}40%{transform:scale(1.2);opacity:1;}}

/* Live badge */
.live-badge{display:inline-flex;align-items:center;gap:5px;background:rgba(16,185,129,.1);color:var(--green);font-size:10px;font-weight:600;border-radius:20px;padding:2px 8px;}
.live-dot{width:6px;height:6px;border-radius:50%;background:var(--green);animation:pulse 1.4s infinite;}
@keyframes pulse{0%,100%{opacity:1;}50%{opacity:.35;}}

/* Banner */
.banner{
    background:var(--navy);border-radius:14px;padding:24px 28px;margin-bottom:18px;
    display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;
}
.banner::after{content:'';position:absolute;right:-30px;top:-30px;width:180px;height:180px;border-radius:50%;background:radial-gradient(circle,rgba(245,158,11,.14) 0%,transparent 70%);}
.banner-grad{font-size:50px;filter:opacity(.2);}
.banner-mid h3{color:#fff;font-size:17px;font-weight:800;font-family:'Plus Jakarta Sans',sans-serif;}
.banner-mid p{color:rgba(255,255,255,.5);font-size:12px;margin-top:3px;}
.btn-explore{border:2px solid var(--gold);color:var(--gold);background:none;border-radius:9px;padding:9px 20px;font-size:12.5px;font-weight:700;cursor:pointer;transition:.2s;white-space:nowrap;text-decoration:none;display:inline-block;}
.btn-explore:hover{background:var(--gold);color:var(--navy);}

.footer{display:flex;align-items:center;justify-content:space-between;padding:14px 0 6px;border-top:1px solid var(--border);font-size:10.5px;color:var(--t3);}

/* Responsive */
@media(max-width:1280px){
    .stats-row{grid-template-columns:repeat(2,1fr);}
    .body-grid{grid-template-columns:1fr 1fr;}
    .ai-panel{grid-column:1/-1;}
    .bottom-row{grid-template-columns:1fr 1fr;}
}
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
    .stats-row{grid-template-columns:repeat(2,1fr);}
    .body-grid{grid-template-columns:1fr;}
    .bottom-row{grid-template-columns:1fr;}
}
@media(max-width:480px){
    .stats-row{grid-template-columns:1fr;}
    .pg{padding:14px;}
}
</style>
</head>
<body>
@php
use Carbon\Carbon;
$user   = auth()->user();
$hour   = now()->hour;
$greet  = $hour<12 ? 'Good Morning' : ($hour<17 ? 'Good Afternoon' : 'Good Evening');
$initials = strtoupper(substr($user->name,0,1).(str_contains($user->name,' ') ? substr($user->name,strrpos($user->name,' ')+1,1) : ''));
$stuName   = $student ? $student->full_name : $user->name;
$stuInitials = strtoupper(substr($stuName,0,1).(str_contains($stuName,' ') ? substr($stuName,strrpos($stuName,' ')+1,1) : ''));

// Today's attendance status
$todayStatus = $student
    ? \App\Models\StudentAttendance::where('student_id',$student->id)->whereDate('date',today())->value('status')
    : null;

// This week days
$weekDays = [];
$weekStart = now()->startOfWeek();
for($d=0;$d<5;$d++){
    $dt = $weekStart->copy()->addDays($d);
    $st = $student ? \App\Models\StudentAttendance::where('student_id',$student->id)->whereDate('date',$dt)->value('status') : null;
    $weekDays[] = ['label'=>$dt->format('D')[0],'status'=>$st,'date'=>$dt->format('M j')];
}

// Timetable stubs
if (!isset($timetable) || empty($timetable)) {
    $timetable = [
        ['time'=>'9:00 AM','subject'=>'Mathematics','teacher'=>'Mr. Kapoor','color'=>'#3b82f6'],
        ['time'=>'10:00 AM','subject'=>'Physics','teacher'=>'Ms. Sharma','color'=>'#8b5cf6'],
        ['time'=>'11:00 AM','subject'=>'Chemistry','teacher'=>'Mr. Verma','color'=>'#10b981'],
        ['time'=>'12:00 PM','subject'=>'Lunch Break','teacher'=>'—','color'=>'#f59e0b'],
        ['time'=>'1:00 PM','subject'=>'English','teacher'=>'Ms. Patel','color'=>'#ef4444'],
        ['time'=>'2:00 PM','subject'=>'History','teacher'=>'Mr. Singh','color'=>'#f97316'],
    ];
}

// AI insights for student
$aiInsights = [
    ['icon'=>'fa-calendar-check','color'=>'#10b981','bg'=>'rgba(16,185,129,.15)',
     'text'=>$attendanceRate>=75 ? "Great job! Your attendance is at {$attendanceRate}%. Keep it up!" : "Your attendance is {$attendanceRate}%. You need at least 75% to be eligible for exams."],
    ['icon'=>'fa-book-open','color'=>'#3b82f6','bg'=>'rgba(59,130,246,.15)',
     'text'=>'You have 3 upcoming assignments due this week. Review your timetable to stay on track.'],
    ['icon'=>'fa-star','color'=>'#f59e0b','bg'=>'rgba(245,158,11,.15)',
     'text'=>'Exam schedule has been published. Check the academics section for your timetable.'],
];
@endphp

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
                <h2>{{ $greet }}, {{ explode(' ',$user->name)[0] }}! 👋</h2>
                <p>Welcome to <a href="#">{{ $school?->name ?? 'SchoolCloud ERP' }}</a> — Student Portal</p>
            </div>
        </div>
        <div class="topbar-right">
            <div class="date-pill">
                <i class="fas fa-calendar-days"></i>
                {{ Carbon::now()->format('M j, Y') }}
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
                    <a href="#"><i class="fas fa-user"></i> Profile</a>
                    <a href="#"><i class="fas fa-gear"></i> Settings</a>
                    <a href="{{ route('logout') }}" class="danger"><i class="fas fa-right-from-bracket"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- PAGE -->
    <div class="pg">

        <!-- ══ WELCOME STRIP ══ -->
        <div class="welcome-strip">
            <div class="ws-left">
                <h2>{{ $greet }}, {{ explode(' ',$stuName)[0] }}! 🎓</h2>
                <p>Here's your academic snapshot for today — {{ Carbon::now()->format('l, M j, Y') }}</p>
                <div class="ws-badges">
                    <span class="ws-badge"><i class="fas fa-school"></i> {{ $classDisplay }} – Sec {{ $sectionDisplay }}</span>
                    <span class="ws-badge"><i class="fas fa-calendar-alt"></i> {{ $sessionDisplay }}</span>
                    @if($student)<span class="ws-badge"><i class="fas fa-id-card"></i> {{ $student->admission_number }}</span>@endif
                    @if($todayStatus)
                        <span class="ws-badge" style="background:rgba(16,185,129,.2);color:#10b981;border-color:rgba(16,185,129,.3);">
                            <i class="fas fa-circle-check"></i> {{ ucfirst($todayStatus) }} Today
                        </span>
                    @endif
                </div>
            </div>
            <div class="ws-right">📚</div>
        </div>

        <!-- ══ STAT CARDS ══ -->
        <div class="stats-row">
            <!-- Attendance Rate -->
            <div class="stat">
                <div class="stat-top">
                    <div class="stat-ico" style="background:rgba(139,92,246,.12);color:#8b5cf6;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-lbl">Attendance Rate</div>
                        <div class="stat-val"><span data-count="{{ $attendanceRate }}">0</span>%</div>
                        <div class="stat-sub">{{ $presentDays }} of {{ $totalDays }} days</div>
                    </div>
                </div>
                <div class="stat-spark-wrap">
                    <canvas class="stat-spark" id="spk0"></canvas>
                </div>
            </div>
            <!-- Days Present -->
            <div class="stat">
                <div class="stat-top">
                    <div class="stat-ico" style="background:rgba(16,185,129,.12);color:#10b981;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-lbl">Days Present</div>
                        <div class="stat-val" data-count="{{ $presentDays }}">0</div>
                        <div class="stat-sub">out of {{ $totalDays }} school days</div>
                    </div>
                </div>
                <div class="stat-spark-wrap">
                    <canvas class="stat-spark" id="spk1"></canvas>
                </div>
            </div>
            <!-- Days Absent -->
            <div class="stat">
                <div class="stat-top">
                    <div class="stat-ico" style="background:rgba(239,68,68,.12);color:#ef4444;">
                        <i class="fas fa-user-xmark"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-lbl">Days Absent</div>
                        <div class="stat-val" data-count="{{ $absentDays }}">0</div>
                        <div class="stat-sub">{{ $lateDays }} days late</div>
                    </div>
                </div>
                <div class="stat-spark-wrap">
                    <canvas class="stat-spark" id="spk2"></canvas>
                </div>
            </div>
            <!-- Fee Status -->
            <div class="stat">
                <div class="stat-top">
                    <div class="stat-ico" style="background:rgba(245,158,11,.12);color:#f59e0b;">
                        <i class="fas fa-indian-rupee-sign"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-lbl">Fee Status</div>
                        <div class="stat-val">{{ $totalFee > 0 ? $feeRate.'%' : '—' }}</div>
                        <div class="stat-sub">fee module setup pending</div>
                    </div>
                </div>
                <div class="stat-spark-wrap">
                    <canvas class="stat-spark" id="spk3"></canvas>
                </div>
            </div>
        </div>

        <!-- ══ BODY GRID ══ -->
        <div class="body-grid">

            <!-- Attendance Chart -->
            <div class="card">
                <div class="card-hdr">
                    <span class="card-title">Attendance Trend (6 Months)</span>
                    <span class="live-badge"><span class="live-dot"></span>Live</span>
                </div>
                <div class="card-body">
                    <div class="att-summary">
                        <div class="att-chip">
                            <div class="att-chip-num" style="color:var(--green);">{{ $presentDays }}</div>
                            <div class="att-chip-lbl">Present</div>
                        </div>
                        <div class="att-chip">
                            <div class="att-chip-num" style="color:var(--red);">{{ $absentDays }}</div>
                            <div class="att-chip-lbl">Absent</div>
                        </div>
                        <div class="att-chip">
                            <div class="att-chip-num" style="color:var(--gold);">{{ $lateDays }}</div>
                            <div class="att-chip-lbl">Late</div>
                        </div>
                        <div class="att-chip">
                            <div class="att-chip-num" style="color:var(--purple);">{{ $attendanceRate }}%</div>
                            <div class="att-chip-lbl">Rate</div>
                        </div>
                    </div>
                    <div style="height:180px;position:relative;">
                        <canvas id="attChart"></canvas>
                    </div>
                    <!-- This week -->
                    <div style="margin-top:12px;font-size:11px;font-weight:600;color:var(--t2);margin-bottom:6px;">This Week</div>
                    <div class="day-chips">
                        @foreach($weekDays as $wd)
                            <div class="day-chip {{ $wd['status']=='present'?'day-p':($wd['status']=='absent'?'day-a':'day-h') }}"
                                 title="{{ $wd['date'] }}: {{ ucfirst($wd['status']??'No data') }}">
                                {{ $wd['label'] }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Today's Timetable -->
            <div class="card" id="timetable">
                <div class="card-hdr">
                    <span class="card-title">Today's Timetable</span>
                    <span style="font-size:10.5px;color:var(--t3);">{{ Carbon::now()->format('l') }}</span>
                </div>
                <div class="card-body" style="padding-top:8px;">
                    <ul class="timetable">
                        @foreach($timetable as $t)
                        <li class="tt-item">
                            <span class="tt-time">{{ $t['time'] }}</span>
                            <span class="tt-dot" style="background:{{ $t['color'] }};"></span>
                            <div class="tt-info">
                                <strong>{{ $t['subject'] }}</strong>
                                <span>{{ $t['teacher'] }}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- AI Panel -->
            <div class="ai-panel">
                <div class="ai-hdr">
                    <div class="ai-hdr-ico"><i class="fas fa-robot"></i></div>
                    <div>
                        <h3>✦ AI Assistant</h3>
                        <p>Smart insights for your studies</p>
                    </div>
                </div>
                <div class="ai-body">
                    @foreach($aiInsights as $ins)
                    <div class="ai-item">
                        <div class="ai-item-ico" style="background:{{ $ins['bg'] }};color:{{ $ins['color'] }};"><i class="fas {{ $ins['icon'] }}"></i></div>
                        <div class="ai-item-txt">{{ $ins['text'] }}</div>
                        <button class="ai-view-btn">View</button>
                    </div>
                    @endforeach
                </div>
                <div class="ai-resp" id="aiResp"></div>
                <div class="ai-chat">
                    <div class="ai-input-row">
                        <input class="ai-input" id="aiIn" placeholder="Ask AI anything..." onkeydown="if(event.key==='Enter')sendAI()">
                        <button class="ai-send" onclick="sendAI()"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ BOTTOM ROW ══ -->
        <div class="bottom-row">

            <!-- Attendance Circle + Recent -->
            <div class="card">
                <div class="card-hdr">
                    <span class="card-title">Attendance Overview</span>
                </div>
                <div class="card-body">
                    <div class="circle-prog-wrap">
                        <div class="circle-prog-rel">
                            <canvas id="attDonut" width="130" height="130"></canvas>
                            <div class="circle-prog-center">
                                <strong>{{ $attendanceRate }}%</strong>
                                <small>Present</small>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:10px;font-size:11.5px;font-weight:700;color:var(--t1);margin-bottom:6px;">Recent Record</div>
                    <ul class="att-list">
                        @forelse($recentAttendance->take(5) as $rec)
                        <li class="att-item">
                            <span class="att-date">{{ Carbon::parse($rec->date)->format('M j, D') }}</span>
                            <span class="status-badge s-{{ $rec->status }}">
                                <i class="fas fa-{{ $rec->status=='present'?'check':'times' }}-circle" style="font-size:9px;"></i>
                                {{ ucfirst($rec->status) }}
                            </span>
                        </li>
                        @empty
                        <li style="text-align:center;padding:14px;color:var(--t3);font-size:11.5px;">
                            <i class="fas fa-calendar-xmark" style="display:block;font-size:20px;margin-bottom:6px;color:var(--border);"></i>
                            No records yet
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Fee Summary -->
            <div class="card">
                <div class="card-hdr">
                    <span class="card-title">Fee Summary</span>
                    <a href="#" class="view-all">View All</a>
                </div>
                <div class="card-body">
                    <div class="donut-wrap">
                        <div class="donut-rel">
                            <canvas id="feeDonut" width="130" height="130"></canvas>
                            <div class="donut-center">
                                <strong>{{ formatIndianCurrency($totalFee) }}</strong>
                                <small>Total Fee</small>
                            </div>
                        </div>
                        <div class="legend" style="padding:0 4px;">
                            <div class="leg-row">
                                <div class="leg-left"><span class="leg-dot" style="background:#10b981;"></span>Paid</div>
                                <span class="leg-val">{{ formatIndianCurrency($paidFee) }}</span>
                            </div>
                            <div class="leg-row">
                                <div class="leg-left"><span class="leg-dot" style="background:#ef4444;"></span>Pending</div>
                                <span class="leg-val">{{ formatIndianCurrency($pendingFee) }}</span>
                            </div>
                        </div>
                    </div>
                    <div style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:8px;padding:10px;text-align:center;margin-top:10px;">
                        <div style="font-size:11px;color:var(--gold);font-weight:600;"><i class="fas fa-clock"></i> Fee module coming soon</div>
                        <div style="font-size:10px;color:var(--t3);margin-top:3px;">Your fee details will appear here</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-hdr"><span class="card-title">Quick Actions</span></div>
                <div class="card-body" style="padding-top:8px;">
                    <div class="qa-grid">
                        <a href="{{ route('parent.attendance.index') }}" class="qa-btn">
                            <div class="qa-ico" style="background:rgba(139,92,246,.12);color:#8b5cf6;"><i class="fas fa-calendar-check"></i></div>
                            <span class="qa-lbl">Attendance</span>
                        </a>
                        <a href="{{ route('parent.documents.index') }}" class="qa-btn">
                            <div class="qa-ico" style="background:rgba(16,185,129,.12);color:#10b981;"><i class="fas fa-file-pdf"></i></div>
                            <span class="qa-lbl">Documents</span>
                        </a>
                        <a href="#" class="qa-btn">
                            <div class="qa-ico" style="background:rgba(59,130,246,.12);color:#3b82f6;"><i class="fas fa-calendar-alt"></i></div>
                            <span class="qa-lbl">Timetable</span>
                        </a>
                        <a href="#" class="qa-btn">
                            <div class="qa-ico" style="background:rgba(16,185,129,.12);color:#10b981;"><i class="fas fa-file-alt"></i></div>
                            <span class="qa-lbl">Results</span>
                        </a>
                        <a href="#" class="qa-btn">
                            <div class="qa-ico" style="background:rgba(239,68,68,.12);color:#ef4444;"><i class="fas fa-bullhorn"></i></div>
                            <span class="qa-lbl">Notices</span>
                        </a>
                        <a href="#" class="qa-btn">
                            <div class="qa-ico" style="background:rgba(249,115,22,.12);color:#f97316;"><i class="fas fa-comment-dots"></i></div>
                            <span class="qa-lbl">Messages</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Issued Certificates Card -->
        <div class="card" style="grid-column: 1 / -1;">
            <div class="card-hdr">
                <span class="card-title"><i class="fas fa-award" style="color:var(--gold);margin-right:8px;"></i>Issued Certificates & Documents</span>
                <a href="{{ route('parent.documents.index') }}" class="view-all">View All</a>
            </div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:8px;">
                    @forelse($documents->take(3) as $doc)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--page);border:1px solid var(--border);border-radius:10px;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:34px;height:34px;border-radius:8px;background:rgba(245,158,11,.15);color:var(--gold);display:flex;align-items:center;justify-content:center;font-size:15px;">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div>
                                <strong style="display:block;font-size:13px;color:var(--t1);">{{ $doc->original_name }}</strong>
                                <span style="font-size:11px;color:var(--t3);">Issued on {{ $doc->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        <a href="{{ route('parent.documents.download', ['document' => $doc->id, 'action' => 'download']) }}" class="btn btn-outline" style="padding:5px 10px;font-size:11px;">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                    @empty
                    <div style="text-align:center;padding:24px;color:var(--t3);font-size:12px;">
                        <i class="fas fa-folder-open" style="font-size:24px;display:block;margin-bottom:6px;color:var(--border);"></i>
                        No certificates or documents have been issued yet.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ══ BANNER ══ -->
        <div class="banner">
            <div class="banner-grad">🎓</div>
            <div class="banner-mid">
                <h3>Stay on Top of Your Studies!</h3>
                <p>Track attendance, check results, and never miss a deadline.</p>
            </div>
            <a href="#" class="btn-explore">Explore Portal →</a>
        </div>

        <div class="footer">
            <span>© 2026 SchoolCloud ERP. All rights reserved.</span>
            <span>Version 2.0.0 &nbsp;|&nbsp; 🔒 Secure & Trusted</span>
        </div>
    </div>
</div>

<script>
// ── DATA ──────────────────────────────────────────────────────────────────────
const ATT_LABELS = @json($attendanceLabels);
const ATT_DATA   = @json($monthlyAttendance);
const PRESENT    = {{ $presentDays }};
const ABSENT     = {{ $absentDays }};
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;

// ── COUNT-UP ──────────────────────────────────────────────────────────────────
function countUp(el,to,dur=1400,dec=false){
    const s=performance.now();
    (function go(t){
        const p=Math.min((t-s)/dur,1),e=1-Math.pow(1-p,3),v=to*e;
        el.textContent=dec?v.toFixed(1):Math.round(v).toLocaleString('en-IN');
        if(p<1)requestAnimationFrame(go);
    })(s);
}
document.addEventListener('DOMContentLoaded',()=>{
    document.querySelectorAll('[data-count]').forEach(el=>{
        countUp(el,parseFloat(el.dataset.count),1400);
    });

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

// ── SPARKLINES ────────────────────────────────────────────────────────────────
const spkData=[
    ATT_DATA.slice(-7).length ? ATT_DATA.slice(-7) : [0,0,0,0,0,0,0],
    @json($presentSparkline),
    @json($absentSparkline),
    @json($lateSparkline)
];
const spkColors=['#8b5cf6','#10b981','#ef4444','#f59e0b'];
spkData.forEach((data,i)=>{
    const c=document.getElementById('spk'+i);if(!c)return;
    const g=c.getContext('2d').createLinearGradient(0,0,0,36);
    g.addColorStop(0,spkColors[i]+'44');g.addColorStop(1,spkColors[i]+'00');
    new Chart(c,{
        type:'line',
        data:{labels:data.map((_,j)=>j),datasets:[{data,borderColor:spkColors[i],borderWidth:2,tension:.4,pointRadius:0,fill:true,backgroundColor:g}]},
        options:{responsive:true,maintainAspectRatio:false,animation:{duration:800},plugins:{legend:{display:false},tooltip:{enabled:false}},scales:{x:{display:false},y:{display:false}}}
    });
});

// ── ATTENDANCE BAR CHART ──────────────────────────────────────────────────────
new Chart(document.getElementById('attChart'),{
    type:'bar',
    data:{
        labels:ATT_LABELS,
        datasets:[{
            data:ATT_DATA,
            backgroundColor:'#1a1f3c',
            borderRadius:4,barThickness:18
        }]
    },
    options:{
        responsive:true,maintainAspectRatio:false,
        plugins:{legend:{display:false},tooltip:{callbacks:{label:t=>' '+t.parsed.y+'% attendance'}}},
        scales:{
            x:{grid:{display:false},ticks:{font:{size:10},color:'#9ca3af'}},
            y:{min:0,max:100,grid:{color:'#f3f4f6'},ticks:{font:{size:10},color:'#9ca3af',callback:v=>v+'%'}}
        }
    }
});

// ── ATTENDANCE DONUT ──────────────────────────────────────────────────────────
(()=>{
    const tot=PRESENT+ABSENT;
    new Chart(document.getElementById('attDonut'),{
        type:'doughnut',
        data:{labels:['Present','Absent'],datasets:[{data:tot>0?[PRESENT,ABSENT]:[1,0],backgroundColor:['#8b5cf6','#f3f4f6'],borderWidth:0,hoverOffset:4}]},
        options:{cutout:'68%',responsive:false,plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>{const v=c.parsed;return ' '+v+' days ('+Math.round(v/tot*100)+'%)';}}}}}
    });
})();

// ── FEE DONUT ─────────────────────────────────────────────────────────────────
new Chart(document.getElementById('feeDonut'),{
    type:'doughnut',
    data:{labels:['Paid','Pending'],datasets:[{data:[0,1],backgroundColor:['#10b981','#f3f4f6'],borderWidth:0}]},
    options:{cutout:'68%',responsive:false,plugins:{legend:{display:false}}}
});

// ── DROPDOWNS ─────────────────────────────────────────────────────────────────
function toggleDrop(id){
    ['notifDrop','userDrop'].forEach(d=>{if(d!==id)document.getElementById(d).classList.remove('open');});
    document.getElementById(id).classList.toggle('open');
}
document.addEventListener('click',e=>{
    if(!e.target.closest('.notif-wrap'))document.getElementById('notifDrop').classList.remove('open');
    if(!e.target.closest('.user-wrap'))document.getElementById('userDrop').classList.remove('open');
});

// ── AI CHAT ───────────────────────────────────────────────────────────────────
async function sendAI(){
    const inp=document.getElementById('aiIn'),msg=inp.value.trim();
    if(!msg)return; inp.value='';
    const box=document.getElementById('aiResp');
    box.style.display='block';
    box.innerHTML='<div class="dots"><span></span><span></span><span></span></div>';
    await new Promise(r=>setTimeout(r,1200));
    const replies={
        'attendance':'Your current attendance is {{ $attendanceRate }}%. You need {{ max(0, 75 - $attendanceRate) }}% more to meet the minimum requirement. Stay regular!',
        'fee':'Fee details are being set up. Contact your school admin for current fee status.',
        'exam':'Exam schedules are announced by your school admin. Check the Exams section for details.',
        'default':"I'm here to help! Ask me about your attendance, fees, exams, or timetable."
    };
    const lm=msg.toLowerCase();
    let rep=replies.default;
    if(lm.includes('attend'))rep=replies.attendance;
    else if(lm.includes('fee')||lm.includes('pay'))rep=replies.fee;
    else if(lm.includes('exam')||lm.includes('test'))rep=replies.exam;
    box.innerHTML='<i class="fas fa-robot" style="color:#f59e0b;margin-right:6px;"></i>'+rep;
}
</script>
</body>
</html>

@extends('layouts.app')

@section('title', 'School Dashboard')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
body{font-family:'Plus Jakarta Sans',sans-serif !important;background:var(--page);color:var(--t1);display:flex;min-height:100vh;overflow-x:hidden;}

/* Sidebar variables — match app.blade.php */
:root {
    --sidebar-bg: #a553f6;
    --sidebar-bg-rgb: 165, 83, 246;
    --sidebar-stitch: rgba(255, 255, 255, 0.35);
    --sidebar-text: rgba(255, 255, 255, 0.92);
}
body.dark-mode {
    --sidebar-bg: #0b0f1a;
    --sidebar-bg-rgb: 11, 15, 26;
    --sidebar-stitch: rgba(255, 255, 255, 0.2);
    --sidebar-text: rgba(255, 255, 255, 0.8);
}

/* ─── MAIN ────────────────────────────────────────────────── */
.main{
    margin-left:240px;
    flex:1;
    display:flex;
    flex-direction:column;
    min-height:100vh;
    transition: margin-left .3s ease;
}

/* ─── NAVBAR ─────────────────────────────────────────────── */
.topbar{
    background:#fff;border-bottom:1px solid var(--border);
    height:62px;padding:0 22px;
    display:flex;align-items:center;justify-content:space-between;
    position:sticky;top:0;z-index:100;
    box-shadow:0 1px 3px rgba(0,0,0,.05);gap:12px;
}
.topbar-left{display:flex;align-items:center;gap:13px;min-width:0;flex:1;}
.hamburger{
    background:none;border:none;color:var(--t2);
    font-size:18px;cursor:pointer;padding:6px;display:flex;border-radius:6px;
}
.hamburger:hover{background:var(--page);}
.greeting{min-width:0;}
.greeting h2{
    font-family:'Plus Jakarta Sans',sans-serif;
    font-size:15px;font-weight:700;color:var(--t1);line-height:1.2;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.greeting p{font-size:11.5px;color:var(--t2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.greeting a{color:var(--gold);text-decoration:none;font-weight:600;}

.topbar-right{display:flex;align-items:center;gap:10px;}
.date-pill{
    display:flex;align-items:center;gap:6px;
    background:var(--page);border:1px solid var(--border);
    border-radius:8px;padding:6px 11px;
    font-size:11.5px;color:var(--t2);cursor:pointer;
    transition:.2s;
}
.date-pill:hover{border-color:var(--gold);}
.date-pill i{color:var(--gold);}
.btn-export{
    display:flex;align-items:center;gap:6px;
    background:var(--navy);color:#fff;
    font-size:11.5px;font-weight:600;
    border:none;border-radius:8px;padding:8px 14px;
    cursor:pointer;transition:.2s;text-decoration:none;
}
.btn-export:hover{background:var(--navy2);color:#fff;}

/* Bell */
.notif-wrap{position:relative;}
.notif-btn{
    background:var(--page);border:1px solid var(--border);
    border-radius:8px;width:37px;height:37px;
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;color:var(--t2);font-size:15px;transition:.2s;
    position:relative;
}
.notif-btn:hover{border-color:var(--gold);color:var(--gold);}
.notif-badge{
    position:absolute;top:-5px;right:-5px;
    background:var(--red);color:#fff;
    font-size:9px;font-weight:700;
    border-radius:10px;padding:1px 5px;min-width:16px;text-align:center;
}
.notif-drop{
    position:absolute;top:calc(100% + 8px);right:0;width:290px;
    background:#fff;border:1px solid var(--border);
    border-radius:12px;box-shadow:var(--shadow-lg);
    display:none;z-index:300;overflow:hidden;
}
.notif-drop.open{display:block;}
.nd-hdr{
    padding:12px 14px;border-bottom:1px solid var(--border);
    display:flex;justify-content:space-between;align-items:center;
}
.nd-hdr strong{font-size:12.5px;}
.nd-mark{font-size:11px;color:var(--gold);cursor:pointer;}
.nd-empty{padding:22px;text-align:center;color:var(--t3);font-size:11.5px;}

/* Avatar */
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
    color:#fff;font-size:12px;font-weight:700;overflow:hidden;flex-shrink:0;
}
.avatar img{width:100%;height:100%;object-fit:cover;}
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

/* ─── PAGE CONTENT ────────────────────────────────────────── */
.pg{padding:20px 22px;}

/* ─── STAT CARDS ─────────────────────────────────────────── */
.stats-row{
    display:grid;grid-template-columns:repeat(6,1fr);
    gap:12px;margin-bottom:18px;
    align-items:start;
}
.stat{
    background:var(--white);border:1px solid var(--border);
    border-radius:13px;padding:15px 15px 12px;
    box-shadow:var(--shadow);
    transition:transform .2s,box-shadow .2s;
    display:flex;flex-direction:column;
    height:140px;overflow:hidden;
}
.stat:hover{transform:translateY(-2px);box-shadow:var(--shadow-lg);}
.stat-top{display:flex;align-items:flex-start;gap:10px;margin-bottom:6px;flex-shrink:0;}
.stat-ico{
    width:40px;height:40px;border-radius:11px;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;font-size:17px;
}
.stat-info{flex:1;min-width:0;}
.stat-lbl{font-size:10px;color:var(--t2);font-weight:500;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.stat-val{
    font-size:20px;font-weight:800;color:var(--t1);
    font-family:'Plus Jakarta Sans',sans-serif;line-height:1.1;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.stat-trnd{
    display:inline-flex;align-items:center;gap:3px;
    font-size:10px;font-weight:600;margin-top:3px;
    white-space:nowrap;
}
.stat-trnd span{color:var(--t3);font-weight:400;margin-left:2px;}
.up{color:var(--green);}
.dn{color:var(--red);}
.neu{color:var(--t3);}
.stat-spark{width:100%;height:34px;margin-top:auto;flex-shrink:0;display:block;}

/* ─── CHARTS + AI ROW ────────────────────────────────────── */
.charts-ai{
    display:grid;
    grid-template-columns:1fr 1fr 270px;
    gap:14px;margin-bottom:18px;align-items:start;
}
.card{
    background:var(--white);border:1px solid var(--border);
    border-radius:18px !important;box-shadow:0 4px 20px rgba(0,0,0,.03) !important;overflow:hidden;
}
.card-hdr{padding:15px 18px 0;display:flex;align-items:flex-start;justify-content:space-between;}
.card-hdr-left{}
.card-title{font-size:13.5px;font-weight:700;color:var(--t1);}
.card-big{
    font-size:24px;font-weight:800;color:var(--t1);
    font-family:'Plus Jakarta Sans',sans-serif;
    margin-top:3px;line-height:1.1;
}
.card-trend{
    display:inline-flex;align-items:center;gap:4px;
    font-size:10.5px;font-weight:600;margin-top:3px;
}
.card-trend .lbl{color:var(--t3);font-weight:400;font-size:10px;margin-left:2px;}
.period-sel{
    background:var(--page);border:1px solid var(--border);
    border-radius:7px;font-size:11px;color:var(--t2);
    padding:5px 9px;cursor:pointer;font-family:'Inter',sans-serif;
}
.card-body{padding:12px 18px 16px;}
.chart-wrap{position:relative;}

/* ─── AI PANEL ────────────────────────────────────────────── */
.ai-panel{
    background:var(--navy);border-radius:13px;
    border:1px solid rgba(255,255,255,.08);
    box-shadow:var(--shadow);overflow:hidden;
}
.ai-hdr{
    padding:14px 15px 12px;
    border-bottom:1px solid rgba(255,255,255,.08);
    display:flex;align-items:center;gap:9px;
}
.ai-hdr-ico{
    width:32px;height:32px;border-radius:8px;
    background:var(--gold-bg);color:var(--gold);
    display:flex;align-items:center;justify-content:center;font-size:14px;
}
.ai-hdr h3{color:#fff;font-size:13px;font-weight:700;font-family:'Plus Jakarta Sans',sans-serif;}
.ai-hdr p{color:rgba(255,255,255,.45);font-size:10px;margin-top:1px;}
.ai-body{padding:12px;}
.ai-item{
    display:flex;align-items:flex-start;gap:9px;
    padding:10px;border-radius:9px;margin-bottom:8px;
    background:rgba(255,255,255,.05);
    border:1px solid rgba(255,255,255,.07);
    transition:.18s;
}
.ai-item:hover{background:rgba(255,255,255,.09);}
.ai-item-ico{
    width:30px;height:30px;border-radius:7px;
    display:flex;align-items:center;justify-content:center;
    font-size:12px;flex-shrink:0;
}
.ai-item-txt{
    color:rgba(255,255,255,.75);font-size:11px;line-height:1.5;flex:1;
}
.ai-view-btn{
    background:rgba(255,255,255,.1);border:none;
    color:rgba(255,255,255,.8);font-size:10px;font-weight:600;
    border-radius:5px;padding:4px 9px;cursor:pointer;
    white-space:nowrap;flex-shrink:0;transition:.18s;
    align-self:center;
}
.ai-view-btn:hover{background:var(--gold);color:var(--navy);}
.ai-chat{padding:0 12px 12px;}
.ai-input-row{
    display:flex;gap:7px;
    background:rgba(255,255,255,.07);
    border:1px solid rgba(255,255,255,.12);
    border-radius:9px;padding:5px 7px;
}
.ai-input{
    flex:1;background:none;border:none;outline:none;
    color:#fff;font-size:11.5px;
}
.ai-input::placeholder{color:rgba(255,255,255,.3);}
.ai-send{
    width:30px;height:30px;border-radius:7px;
    background:var(--gold);border:none;color:var(--navy);
    font-size:12px;cursor:pointer;
    display:flex;align-items:center;justify-content:center;
    transition:.18s;flex-shrink:0;
}
.ai-send:hover{background:#d97706;}
.ai-resp{
    margin:0 12px 12px;
    background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.18);
    border-radius:9px;padding:10px;
    color:rgba(255,255,255,.8);font-size:11px;line-height:1.6;display:none;
}
.dots{display:flex;gap:4px;padding:2px 0;}
.dots span{
    width:6px;height:6px;border-radius:50%;background:var(--gold);
    animation:bk 1.1s infinite;
}
.dots span:nth-child(2){animation-delay:.2s;}
.dots span:nth-child(3){animation-delay:.4s;}
@keyframes bk{0%,80%,100%{transform:scale(.7);opacity:.5;}40%{transform:scale(1.2);opacity:1;}}

/* ─── BOTTOM ROW ──────────────────────────────────────────── */
.bottom-row{
    display:grid;
    grid-template-columns:1fr 1.5fr 1fr .85fr;
    gap:14px;margin-bottom:18px;
}

/* Snapshot */
.live-badge{
    display:inline-flex;align-items:center;gap:5px;
    background:rgba(16,185,129,.1);color:var(--green);
    font-size:10px;font-weight:600;border-radius:20px;padding:2px 8px;
}
.live-dot{
    width:6px;height:6px;border-radius:50%;background:var(--green);
    animation:pulse 1.4s infinite;
}
@keyframes pulse{0%,100%{opacity:1;}50%{opacity:.35;}}
.snap-list{list-style:none;margin-top:8px;}
.snap-item{
    display:flex;align-items:center;justify-content:space-between;
    padding:9px 0;border-bottom:1px solid var(--border);font-size:12px;
}
.snap-item:last-child{border:none;}
.snap-lbl{display:flex;align-items:center;gap:8px;color:var(--t2);}
.snap-lbl i{width:14px;text-align:center;font-size:12px;}
.snap-val{font-weight:700;color:var(--t1);}
.snap-val.g{color:var(--green);}
.snap-val.a{color:var(--gold);}
.snap-val.r{color:var(--red);}

/* Activities */
.act-list{list-style:none;margin-top:6px;}
.act-item{
    display:flex;align-items:flex-start;gap:9px;
    padding:9px 0;border-bottom:1px solid var(--border);
}
.act-item:last-child{border:none;}
.act-ico{
    width:30px;height:30px;border-radius:7px;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;font-size:11px;
}
.act-body{flex:1;min-width:0;}
.act-body p{font-size:11.5px;color:var(--t1);line-height:1.4;}
.act-body span{font-size:10px;color:var(--t3);}
.act-amt{font-size:12px;font-weight:700;color:var(--green);white-space:nowrap;}

/* Fee donut */
.donut-wrap{display:flex;flex-direction:column;align-items:center;padding:6px 0 4px;}
.donut-rel{position:relative;width:130px;height:130px;}
.donut-center{
    position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
    text-align:center;pointer-events:none;
}
.donut-center strong{
    display:block;font-size:13px;font-weight:800;color:var(--t1);
    font-family:'Plus Jakarta Sans',sans-serif;
}
.donut-center small{font-size:9.5px;color:var(--t2);}
.legend{width:100%;margin-top:10px;}
.legend-row{
    display:flex;align-items:center;justify-content:space-between;
    font-size:11px;padding:3.5px 0;
}
.leg-left{display:flex;align-items:center;gap:6px;color:var(--t2);}
.leg-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
.leg-val{font-weight:700;color:var(--t1);}
.view-dues-btn{
    display:block;text-align:center;margin-top:10px;
    background:var(--navy);color:#fff;
    font-size:11.5px;font-weight:600;
    border-radius:8px;padding:8px;text-decoration:none;transition:.2s;
}
.view-dues-btn:hover{background:var(--navy2);}

/* Quick Actions */
.qa-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-top:8px;}
.qa-btn{
    display:flex;flex-direction:column;align-items:center;gap:5px;
    padding:11px 6px;background:var(--page);
    border:1px solid var(--border);border-radius:10px;
    text-decoration:none;transition:.2s;cursor:pointer;
}
.qa-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 6px 18px rgba(0,0,0,.1);
    border-color:transparent;background:#fff;
}
.qa-ico{
    width:36px;height:36px;border-radius:9px;
    display:flex;align-items:center;justify-content:center;font-size:15px;
}
.qa-lbl{font-size:10px;font-weight:600;color:var(--t1);text-align:center;line-height:1.3;}

/* Card header helper */
.ch{display:flex;align-items:center;justify-content:space-between;padding:14px 17px 0;}
.view-all{font-size:11px;color:var(--gold);text-decoration:none;font-weight:600;}
.view-all:hover{text-decoration:underline;}

/* ─── BANNER ──────────────────────────────────────────────── */
.banner{
    background:var(--navy);border-radius:14px;
    padding:26px 30px;margin-bottom:18px;
    display:flex;align-items:center;justify-content:space-between;
    position:relative;overflow:hidden;
}
.banner::after{
    content:'';position:absolute;right:-30px;top:-30px;
    width:180px;height:180px;border-radius:50%;
    background:radial-gradient(circle,rgba(245,158,11,.14) 0%,transparent 70%);
}
.banner-grad{font-size:52px;filter:opacity(.2);}
.banner-mid h3{
    color:#fff;font-size:17px;font-weight:800;
    font-family:'Plus Jakarta Sans',sans-serif;
}
.banner-mid p{color:rgba(255,255,255,.5);font-size:12px;margin-top:3px;}
.btn-explore{
    border:2px solid var(--gold);color:var(--gold);
    background:none;border-radius:9px;
    padding:9px 20px;font-size:12.5px;font-weight:700;
    cursor:pointer;transition:.2s;white-space:nowrap;
    text-decoration:none;display:inline-block;
}
.btn-explore:hover{background:var(--gold);color:var(--navy);}

/* Footer */
.footer{
    display:flex;align-items:center;justify-content:space-between;
    padding:14px 0 6px;border-top:1px solid var(--border);
    font-size:10.5px;color:var(--t3);
}

/* ─── RESPONSIVE ──────────────────────────────────────────── */
.sidebar-overlay {
    position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
    background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(2px);
    z-index: 1000; display: none; opacity: 0; transition: opacity .3s ease;
    cursor: pointer;
}
.sidebar-overlay.active { display: block; opacity: 1; }

@media(max-width:1280px){
    .stats-row{grid-template-columns:repeat(3,1fr);}
    .charts-ai{grid-template-columns:1fr 1fr;}
    .ai-panel{grid-column:1/-1;}
    .bottom-row{grid-template-columns:1fr 1fr;}
}
@media(min-width:769px) and (max-width:1024px){
    .sidebar{width:54px;}
    .sb-logo-text,.sb-school,.sb-hdr-title,.sb-hdr-arrow,.sb-submenu,.sb-bottom{display:none!important;}
    .sb-hdr{justify-content:center;padding:10px 0;margin:0;cursor:pointer;}
    .sb-hdr-icon{width:22px;height:22px;font-size:9.5px;}
    .main{margin-left:54px !important;}
    .hamburger{display:flex;}
}
@media(max-width:768px){
    .sidebar{transform:translateX(-100%);width:240px;z-index:1002;}
    .sidebar.open{transform:translateX(0);box-shadow:4px 0 32px rgba(0,0,0,.3);}
    .sidebar-overlay{z-index:1001;}
    .sb-close-btn{display:flex!important;}
    .sb-logo-text,.sb-school,.sb-hdr-title,.sb-bottom{display:block!important;}
    
    .sb-submenu {
        display: block !important;
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transform: translateY(-4px);
        transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease, transform 0.25s ease;
        pointer-events: none;
        padding: 0 6px 0 20px !important;
    }
    .sb-submenu.open {
        max-height: 800px !important;
        opacity: 1 !important;
        transform: translateY(0) !important;
        pointer-events: auto !important;
        padding: 4px 6px 6px 20px !important;
    }
    .sb-hdr-arrow {
        display: block !important;
        transition: transform 0.2s;
    }
    .sb-hdr.open .sb-hdr-arrow {
        transform: rotate(180deg) !important;
        color: #ff9800 !important;
    }
    
    .sb-hdr{justify-content:space-between!important;padding:8px 10px!important;margin:0 6px!important;}
    .sb-hdr-icon{width:22px!important;height:22px!important;font-size:9.5px!important;}
    .main{margin-left:0!important;}
    .stats-row{grid-template-columns:repeat(2,1fr);}
    .charts-ai{grid-template-columns:1fr;}
    .bottom-row{grid-template-columns:1fr;}
    .topbar-right .date-pill{display:none;}
    .topbar{padding:0 12px;height:56px;}
    .hamburger{display:flex!important;}
    .sb-tooltip-popup{display:none!important;}
}
@media(max-width:480px){
    .stats-row{grid-template-columns:1fr;}
    .pg{padding:14px 12px;}
    .qa-grid{grid-template-columns:repeat(2,1fr);}
    .user-info{display:none!important;}
    .greeting p{display:none;}
}


/* ─── REDESIGNED DASHBOARD STYLES ─── */
.db-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 18px;
}
.academic-year-box {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.academic-year-box label {
    font-size: 11px;
    font-weight: 600;
    color: var(--t2);
}
.selected-session-select {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 6px 12px 6px 8px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--t1);
    cursor: pointer;
    outline: none;
    display: flex;
    align-items: center;
    gap: 6px;
}
.selected-session-select i {
    color: var(--t2);
}
.followup-alert-box {
    display: flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, rgba(139,92,246,0.08), rgba(99,102,241,0.05));
    border: 1.5px solid rgba(139, 92, 246, 0.25);
    border-radius: 24px;
    padding: 0 18px;
    height: 44px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 2px 12px rgba(139,92,246,0.08);
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
/* Clock display inside greeting */
.greeting-clock-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
    background: rgba(139,92,246,0.12);
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
/* Typewriter cursor effect */
.typewriter-cursor::after {
    content: '|';
    color: #8b5cf6;
    animation: blink-cursor 0.8s step-end infinite;
    font-weight: 400;
    margin-left: 1px;
}
@keyframes blink-cursor {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}
.followup-alert-box span {
    font-size: 12.5px;
    font-weight: 700;
    color: #7c3aed;
    font-family: 'Plus Jakarta Sans', sans-serif;
    letter-spacing: 0.1px;
}
.followup-alert-box i.fa-bell {
    color: #8b5cf6;
    animation: bell-swing 2s infinite ease-in-out;
}
@keyframes bell-swing {
    0%, 100% { transform: rotate(0); }
    15% { transform: rotate(15deg); }
    30% { transform: rotate(-15deg); }
    45% { transform: rotate(10deg); }
    60% { transform: rotate(-10deg); }
    75% { transform: rotate(4deg); }
    90% { transform: rotate(-4deg); }
}
.btn-gold-outline-sm {
    background: transparent;
    border: 1.5px solid #8b5cf6;
    color: #8b5cf6;
    border-radius: 6px;
    font-size: 10.5px;
    font-weight: 700;
    padding: 4px 10px;
    cursor: pointer;
    transition: all 0.2s;
    text-transform: uppercase;
}
.btn-gold-outline-sm:hover {
    background: #8b5cf6;
    color: #fff;
}

/* ─── NEW GLASSMORPHISM STYLES FOR TOP 4 CARDS ─── */
.top-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 28px;
}
@media(max-width: 1280px) {
    .top-summary-grid { grid-template-columns: repeat(2, 1fr); }
}
@media(max-width: 600px) {
    .top-summary-grid { grid-template-columns: 1fr; }
}

.sum-card {
    border-radius: 24px !important;
    padding: 22px 24px !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    backdrop-filter: blur(16px) saturate(120%) !important;
    -webkit-backdrop-filter: blur(16px) saturate(120%) !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: flex-start !important;
    gap: 12px !important;
    min-height: 220px !important;
    height: auto !important;
    overflow: hidden;
    position: relative;
}

.sum-card .card-body-content {
    flex: 1 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 10px !important;
    justify-content: stretch !important;
}

.sum-card .card-body-content.accounts-grid,
.sum-card .card-body-content.fee-grid {
    display: grid !important;
    grid-template-columns: 1fr !important;
    gap: 12px !important;
    flex: 1 !important;
}

.glass-list-item {
    flex: 1 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    margin-bottom: 0 !important;
}

.accounts-subcard, .fee-subcard {
    height: 100% !important;
    flex: 1 !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
}

.attendance-list {
    flex: 1 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 10px !important;
    justify-content: stretch !important;
}

.attendance-glass-row {
    flex: 1 !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
}

/* Mini circular progress pie charts styles */
.subcard-chart-center {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    margin: 8px 0 !important;
    flex: 1 !important;
}
.progress-ring {
    transform: rotate(-90deg);
}
.progress-ring-bubble {
    fill: rgba(255, 255, 255, 0.45);
    transition: fill 0.3s ease;
}
body.dark-mode .progress-ring-bubble {
    fill: rgba(255, 255, 255, 0.05) !important;
}
.progress-ring-text {
    transform: rotate(90deg);
    transform-origin: center;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.progress-ring-text.text-purple {
    fill: #7c3aed;
}
.progress-ring-text.text-pink {
    fill: #db2777;
}
body.dark-mode .progress-ring-text.text-purple,
body.dark-mode .progress-ring-text.text-pink {
    fill: #ffffff !important;
}

.sum-card:hover {
    transform: translateY(-5px);
}

/* Light Mode Custom Glow and semi-dark background colors */
.sum-card.hc-blue {
    background: rgba(99, 102, 241, 0.16) !important;
    border-color: rgba(99, 102, 241, 0.28) !important;
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.08), inset 0 1px 0 rgba(255,255,255,0.4) !important;
}
.sum-card.ac-teal {
    background: rgba(20, 184, 166, 0.16) !important;
    border-color: rgba(20, 184, 166, 0.28) !important;
    box-shadow: 0 10px 30px rgba(20, 184, 166, 0.08), inset 0 1px 0 rgba(255,255,255,0.4) !important;
}
.sum-card.fe-purple {
    background: rgba(168, 85, 247, 0.16) !important;
    border-color: rgba(168, 85, 247, 0.28) !important;
    box-shadow: 0 10px 30px rgba(168, 85, 247, 0.08), inset 0 1px 0 rgba(255,255,255,0.4) !important;
}
.sum-card.at-lavender {
    background: rgba(139, 92, 246, 0.12) !important;
    border-color: rgba(139, 92, 246, 0.25) !important;
    box-shadow: 0 10px 30px rgba(139, 92, 246, 0.08), inset 0 1px 0 rgba(255,255,255,0.4) !important;
}

/* Dark Mode Custom Glow and color blur background */
body.dark-mode .sum-card.hc-blue {
    background: rgba(26, 32, 66, 0.65) !important;
    border-color: rgba(99, 102, 241, 0.28) !important;
    box-shadow: 0 12px 40px rgba(99, 102, 241, 0.15) !important;
}
body.dark-mode .sum-card.ac-teal {
    background: rgba(18, 38, 38, 0.65) !important;
    border-color: rgba(20, 184, 166, 0.28) !important;
    box-shadow: 0 12px 40px rgba(20, 184, 166, 0.15) !important;
}
body.dark-mode .sum-card.fe-purple {
    background: rgba(32, 22, 60, 0.65) !important;
    border-color: rgba(168, 85, 247, 0.28) !important;
    box-shadow: 0 12px 40px rgba(168, 85, 247, 0.15) !important;
}
body.dark-mode .sum-card.at-lavender {
    background: rgba(30, 27, 75, 0.65) !important;
    border-color: rgba(139, 92, 246, 0.28) !important;
    box-shadow: 0 12px 40px rgba(139, 92, 246, 0.15) !important;
}

/* Card Header elements */
.card-top-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0px;
}
.header-left-info {
    display: flex;
    align-items: center;
    gap: 12px;
}
.header-icon-wrapper {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}
.hc-blue .header-icon-wrapper { background: rgba(99, 102, 241, 0.15); color: #6366f1; }
.ac-teal .header-icon-wrapper { background: rgba(20, 184, 166, 0.15); color: #0d9488; }
.fe-purple .header-icon-wrapper { background: rgba(168, 85, 247, 0.15); color: #a855f7; }
.at-lavender .header-icon-wrapper { background: rgba(139, 92, 246, 0.15); color: #8b5cf6; }

body.dark-mode .hc-blue .header-icon-wrapper { background: rgba(99, 102, 241, 0.25); color: #818cf8; }
body.dark-mode .ac-teal .header-icon-wrapper { background: rgba(20, 184, 166, 0.25); color: #2dd4bf; }
body.dark-mode .fe-purple .header-icon-wrapper { background: rgba(168, 85, 247, 0.25); color: #c084fc; }
body.dark-mode .at-lavender .header-icon-wrapper { background: rgba(139, 92, 246, 0.25); color: #c084fc; }

.header-left-info h4 {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #1e293b;
    margin: 0 !important;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
body.dark-mode .header-left-info h4 {
    color: #ffffff !important;
}
.header-left-info .subtitle {
    font-size: 11px !important;
    color: #64748b;
    margin: 2px 0 0 0 !important;
}
body.dark-mode .header-left-info .subtitle {
    color: #94a3b8 !important;
}

.header-right-icons {
    display: flex;
    align-items: center;
    gap: 12px;
}
.header-right-icons i.refresh-trigger {
    font-size: 13px;
    color: #64748b;
    cursor: pointer;
    transition: transform 0.4s ease, color 0.2s;
}
body.dark-mode .header-right-icons i.refresh-trigger {
    color: #94a3b8;
}
.header-right-icons i.refresh-trigger:hover {
    transform: rotate(180deg);
    color: #0f172a;
}
body.dark-mode .header-right-icons i.refresh-trigger:hover {
    color: #fff;
}
.header-right-icons i.ellipsis-icon {
    font-size: 14px;
    color: #94a3b8;
    cursor: pointer;
}

/* Overview Content */
.glass-list-item {
    background: rgba(255, 255, 255, 0.45);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
}
.glass-list-item:last-child {
    margin-bottom: 0;
}
body.dark-mode .glass-list-item {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.06);
}
.glass-list-item:hover {
    transform: translateX(4px);
    background: rgba(255, 255, 255, 0.65);
    border-color: rgba(99, 102, 241, 0.3);
}
body.dark-mode .glass-list-item:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(99, 102, 241, 0.4);
}
.item-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.item-icon-circle {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}
.item-icon-circle.blue { background: rgba(99, 102, 241, 0.12); color: #6366f1; }
.item-icon-circle.purple { background: rgba(168, 85, 247, 0.12); color: #a855f7; }
body.dark-mode .item-icon-circle.blue { background: rgba(99, 102, 241, 0.25); color: #818cf8; }
body.dark-mode .item-icon-circle.purple { background: rgba(168, 85, 247, 0.25); color: #c084fc; }

.item-text {
    display: flex;
    flex-direction: column;
}
.item-text .title {
    font-size: 13.5px;
    font-weight: 700;
    color: #1e293b;
}
body.dark-mode .item-text .title { color: #f8fafc; }
.item-text .sub {
    font-size: 10px;
    color: #64748b;
    margin-top: 1px;
}
body.dark-mode .item-text .sub { color: #94a3b8; }

.item-right {
    display: flex;
    align-items: center;
    gap: 10px;
}
.item-right .value {
    font-size: 18px;
    font-weight: 800;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.item-right .value.text-blue { color: #4f46e5; }
body.dark-mode .item-right .value.text-blue { color: #818cf8; }
.item-right .chevron {
    font-size: 10px;
    color: #94a3b8;
}

/* Accounts Grid & Subcards */
.accounts-grid, .fee-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 14px;
}

.accounts-subcard, .fee-subcard {
    background: rgba(255, 255, 255, 0.45);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 18px;
    padding: 14px 16px;
    cursor: pointer;
    transition: all 0.25s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 110px;
}
body.dark-mode .accounts-subcard, body.dark-mode .fee-subcard {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.06);
}
.accounts-subcard:hover, .fee-subcard:hover {
    transform: translateY(-2px);
    background: rgba(255, 255, 255, 0.65);
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}
body.dark-mode .accounts-subcard:hover, body.dark-mode .fee-subcard:hover {
    background: rgba(255, 255, 255, 0.08);
}
.accounts-subcard:hover.income { border-color: rgba(16, 185, 129, 0.35); }
.accounts-subcard:hover.expense { border-color: rgba(239, 68, 68, 0.35); }

.subcard-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.subcard-header .label {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    display: block;
    margin-bottom: 2px;
}
body.dark-mode .subcard-header .label { color: #94a3b8; }
.subcard-header .value {
    font-size: 16px;
    font-weight: 800;
    color: #1e293b;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
body.dark-mode .subcard-header .value { color: #fff; }
.income .subcard-header .value { color: #0d9488; }
body.dark-mode .income .subcard-header .value { color: #2dd4bf; }
.expense .subcard-header .value { color: #ef4444; }
body.dark-mode .expense .subcard-header .value { color: #fca5a5; }

.trend-icon-circle {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
}
.trend-icon-circle.up { background: rgba(16, 185, 129, 0.12); color: #10b981; }
.trend-icon-circle.down { background: rgba(239, 68, 68, 0.12); color: #ef4444; }

.subcard-trend {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 4px;
    font-size: 10px;
}
.subcard-trend .trend-text { font-weight: 700; }
.subcard-trend .trend-text.green { color: #10b981; }
.subcard-trend .trend-text.red { color: #ef4444; }
.subcard-trend .trend-lbl { color: #94a3b8; }

.mini-chart-wrapper {
    height: 20px;
    margin-top: 8px;
    opacity: 0.85;
}

/* Fee subcard icons */
.fee-icon-wrapper {
    font-size: 14px;
    color: #94a3b8;
}
.fe-purple .fee-icon-wrapper { color: #a855f7; }

/* Progress bar system */
.progress-bar-wrapper {
    width: 100%;
    margin-top: 8px;
}
.progress-track {
    width: 100%;
    height: 6px;
    border-radius: 10px;
    overflow: hidden;
}
.progress-track.bg-purple-light { background: rgba(168, 85, 247, 0.15); }
.progress-track.bg-pink-light { background: rgba(236, 72, 153, 0.15); }
.progress-track.bg-orange-light { background: rgba(139, 92, 246, 0.15); }

.progress-fill {
    height: 100%;
    border-radius: 10px;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}
.progress-fill.bg-purple { background: #8b5cf6; }
.progress-fill.bg-pink { background: #ec4899; }
.progress-fill.bg-orange { background: #8b5cf6; }

/* Attendance elements */
.attendance-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.attendance-glass-row {
    background: rgba(255, 255, 255, 0.45);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    padding: 14px 18px;
    cursor: pointer;
    transition: all 0.25s ease;
    display: flex;
    flex-direction: column;
}
body.dark-mode .attendance-glass-row {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.06);
}
.attendance-glass-row:hover {
    transform: translateY(-2px);
    background: rgba(255, 255, 255, 0.65);
    border-color: rgba(245, 158, 11, 0.35);
}
body.dark-mode .attendance-glass-row:hover {
    background: rgba(255, 255, 255, 0.08);
}
.attendance-glass-row .row-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.attendance-glass-row .row-top .label {
    font-size: 13px;
    font-weight: 700;
    color: #1e293b;
}
body.dark-mode .attendance-glass-row .row-top .label { color: #fff; }

.row-top-right {
    display: flex;
    align-items: center;
    gap: 10px;
}
.row-top-right .value {
    font-size: 17px;
    font-weight: 800;
    color: #8b5cf6;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.row-top-right .badge {
    font-size: 9px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 12px;
}
.row-top-right .badge.green {
    background: rgba(16, 185, 129, 0.12);
    color: #10b981;
}
body.dark-mode .row-top-right .badge.green {
    background: rgba(16, 185, 129, 0.2);
    color: #34d399;
}

.attendance-trend-chart {
    height: 18px;
    margin-top: 8px;
    opacity: 0.8;
}

/* Sections & Typography */
.sec-title {
    font-size: 16px !important;
    font-weight: 800 !important;
    color: #3730a3 !important; /* Deep vibrant purple */
    margin: 28px 0 14px !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    display: block !important;
    clear: both !important;
}
.card-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e2e8f0;
    padding: 12px 16px;
    flex-wrap: wrap;
    gap: 8px;
}
.card-header-row h3 {
    font-size: 14px !important;
    font-weight: 800 !important;
    color: #312e81 !important;
    display: flex;
    align-items: center;
    gap: 6px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    margin: 0;
}
.card-header-row h3 i.fa-arrows-rotate,
.card-header-row h3 i.fa-rotate {
    font-size: 11px;
    color: #6366f1;
    cursor: pointer;
    transition: transform 0.3s;
}
.card-header-row h3 i.fa-arrows-rotate:hover,
.card-header-row h3 i.fa-rotate:hover {
    transform: rotate(180deg);
}

/* Layout Grids */
.db-grid-3col {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
.db-grid-2col {
    display: grid;
    grid-template-columns: 2fr 1.05fr;
    gap: 16px;
    margin-bottom: 24px;
}
@media(max-width: 1024px) {
    .db-grid-3col { grid-template-columns: 1fr; }
    .db-grid-2col { grid-template-columns: 1fr; }
}

/* Stacked Progress Bar */
.progress-bar-container {
    margin-top: 12px;
}
.progress-label-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}
.progress-label-row span {
    font-size: 12.5px !important;
    font-weight: 700 !important;
}
.progress-label-row span.sub-lbl {
    font-size: 11.5px !important;
    color: #64748b;
}
.progress-label-row i {
    color: #6366f1;
    font-size: 11px;
}
.stacked-progress-bar {
    display: flex;
    height: 12px;
    background: #f1f5f9;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 10px;
}
.progress-segment {
    height: 100%;
}
.segment-blue { background: #3b82f6; }
.segment-pink { background: #ec4899; }
.segment-teal { background: #14b8a6; }
.segment-grey { background: #cbd5e1; }

.legend-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 14px;
    margin-top: 6px;
}
.legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 10.5px !important;
    color: #64748b;
    font-weight: 600 !important;
}
.legend-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
}

/* Joining and Attrition Grid */
.attrition-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    padding: 14px;
}
.attrition-box {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 14px !important;
    padding: 12px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
}
.attrition-box-title {
    font-size: 12.5px !important;
    font-weight: 800 !important;
    color: #312e81 !important;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}
.attrition-box-title i.fa-circle-info {
    font-size: 11px;
    color: #94a3b8;
    cursor: pointer;
}
.attrition-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.attrition-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12px !important;
    color: #475569 !important;
}
.attrition-row .row-lbl {
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 600 !important;
}
.attrition-row .row-lbl i.fa-plus-circle { color: #10b981; }
.attrition-row .row-lbl i.fa-minus-circle { color: #ef4444; }
.attrition-row .row-lbl i.fa-triangle { color: #3b82f6; }
.attrition-row .row-val {
    font-weight: 800 !important;
    color: #0f172a !important;
    font-size: 12.5px !important;
}
.strength-indicator {
    display: flex;
    align-items: center;
    gap: 3px;
}
.strength-indicator i.fa-caret-up {
    color: #10b981;
    font-size: 11px;
}

/* Admission Summary Bar Chart */
.admission-chart-container {
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.admission-chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.admission-checkbox-lbl {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #334155 !important;
}
.admission-checkbox-lbl input {
    accent-color: #4f46e5 !important;
}
.admission-bar-chart {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    height: 125px;
    border-bottom: 1.5px solid #e2e8f0;
    padding: 0 10px;
    margin-top: 10px;
}
.admission-bar-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 16%;
}
.admission-bar-value {
    font-size: 12px !important;
    font-weight: 800 !important;
    color: #0f172a !important;
    margin-bottom: 4px;
}
.admission-bar-fill {
    width: 26px;
    border-radius: 5px 5px 0 0;
    transition: height 0.5s ease-out;
}
.admission-bar-label {
    font-size: 10.5px !important;
    font-weight: 600 !important;
    color: #64748b !important;
    margin-top: 6px;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 100%;
}
.bar-red { background: #ef4444; }
.bar-greyblue { background: #10b981; }
.bar-blue { background: #3b82f6; }
.bar-orange { background: #f59e0b; }
.bar-purple { background: #ec4899; }   font-size: 11px;
    font-weight: 600;
    color: var(--t2);
}
.admission-checkbox-lbl input {
    accent-color: #d97706;
}
.admission-bar-chart {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    height: 120px;
    border-bottom: 1.5px solid var(--border);
    padding: 0 10px;
    margin-top: 10px;
}
.admission-bar-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 16%;
}
.admission-bar-value {
    font-size: 11px;
    font-weight: 700;
    color: var(--t1);
    margin-bottom: 4px;
}
.admission-bar-fill {
    width: 24px;
    border-radius: 4px 4px 0 0;
    transition: height 0.5s ease-out;
}
.admission-bar-label {
    font-size: 9px;
    font-weight: 600;
    color: var(--t2);
    margin-top: 6px;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 100%;
}
.bar-red { background: #ef4444; }
.bar-greyblue { background: #5f7a94; }
.bar-blue { background: #3b82f6; }
.bar-orange { background: #f59e0b; }
.bar-purple { background: #8b5cf6; }

/* Financial Management Overview */
.income-expense-chart-container {
    padding: 14px;
    height: 200px;
    position: relative;
}
.fee-management-subcard {
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 12px 14px;
    margin: 12px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.fee-management-subcard div {
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.fee-management-subcard strong {
    font-size: 14px;
    color: var(--t1);
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.fee-management-subcard span {
    font-size: 10px;
    color: var(--t2);
    font-weight: 500;
}
.fee-management-subcard i.fa-circle-info {
    font-size: 11px;
    color: var(--t2);
}
.fee-management-body {
    padding: 0 14px 14px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.collected-due-bar {
    height: 12px;
    background: #e2e8f0;
    border-radius: 6px;
    overflow: hidden;
    display: flex;
}
.collected-due-bar .fill-collected { background: #3b82f6; }
.collected-due-bar .fill-due { background: #ec4899; }

.fee-list-section {
    display: flex;
    flex-direction: column;
    gap: 8px;
    border-top: 1px dashed var(--border);
    padding-top: 10px;
}
.fee-list-title-row {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    font-weight: 700;
    color: var(--t1);
}
.fee-list-title-row i.fa-circle-info {
    font-size: 10.5px;
    color: var(--t2);
}
.fee-action-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11.5px;
    font-weight: 600;
    color: var(--t2);
    flex-wrap: wrap;
    gap: 6px;
}
.fee-action-row span {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}
.fee-action-row span strong {
    color: var(--t1);
}
.btn-orange-reminder {
    background: linear-gradient(135deg, #f97316, #ea580c);
    color: #fff;
    border: none;
    border-radius: 20px;
    padding: 6px 14px;
    font-size: 10.5px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 4px 12px rgba(234, 88, 12, 0.25);
    transition: all 0.2s;
}
.btn-orange-reminder:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(234, 88, 12, 0.35);
}
.due-amount-display {
    font-size: 12px;
    font-weight: 700;
    color: #ef4444;
}
.btn-class-fee-report {
    background: #f8fafc;
    border: 1.5px solid #6366f1;
    color: #4f46e5;
    font-size: 11px;
    font-weight: 800;
    border-radius: 12px;
    padding: 9px;
    width: 100%;
    cursor: pointer;
    text-align: center;
    transition: all 0.2s;
}
.btn-class-fee-report:hover {
    background: #4f46e5;
    color: #fff;
    box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
}

/* Administrative Operations */
.recent-updates-tabs {
    display: flex;
    background: #f1f5f9;
    padding: 4px;
    gap: 4px;
}
.recent-updates-tabs button {
    flex: 1;
    background: transparent;
    border: none;
    padding: 8px 4px;
    font-size: 9.5px;
    font-weight: 700;
    color: #64748b;
    cursor: pointer;
    text-align: center;
    border-radius: 8px;
    transition: all 0.2s;
}
.recent-updates-tabs button.active {
    background: #4f46e5 !important;
    color: #fff !important;
    box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);
}
.empty-state-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 24px 12px;
    text-align: center;
    height: 180px;
}
.empty-state-icon {
    font-size: 38px;
    color: #93c5fd;
    margin-bottom: 10px;
    animation: bounce-subtle 3s infinite ease-in-out;
}
@keyframes bounce-subtle {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}
.empty-state-container h4 {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--t1);
    margin-bottom: 4px;
}
.empty-state-container p {
    font-size: 10.5px;
    color: var(--t3);
    max-width: 180px;
}

/* Attendance Card */
.btn-gold-outline-header {
    background: transparent;
    border: 1.5px solid #6366f1;
    color: #4f46e5;
    border-radius: 16px;
    font-size: 10px;
    font-weight: 800;
    padding: 4px 10px;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-gold-outline-header:hover {
    background: #4f46e5;
    color: #fff;
}
.attendance-body {
    padding: 12px 14px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.attendance-subpanel {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px;
}
.attendance-subpanel-hdr {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--t1);
    margin-bottom: 6px;
}
.attendance-subpanel-hdr span.clickable-lbl {
    display: flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
}
.attendance-subpanel-hdr span.clickable-lbl i {
    color: #6366f1;
}
.btn-blue-outline-xs {
    background: transparent;
    border: 1.2px solid #4f46e5;
    color: #4f46e5;
    border-radius: 12px;
    font-size: 9px;
    font-weight: 800;
    padding: 3px 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-blue-outline-xs:hover {
    background: #4f46e5;
    color: #fff;
}
.attendance-subpanel-body {
    font-size: 11px;
    color: var(--t2);
    font-weight: 500;
    text-align: center;
    padding: 8px 0;
}

/* Event Calendar */
.calendar-toggles {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 12px 14px 6px;
}
.toggle-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.toggle-row label {
    font-size: 11.5px;
    font-weight: 700;
}
.toggle-row.student-lbl label { color: #0d9488; }
.toggle-row.teacher-lbl label { color: #db2777; }

/* Switch design */
.switch-wrapper {
    position: relative;
    display: inline-block;
    width: 32px;
    height: 18px;
}
.switch-wrapper input {
    opacity: 0;
    width: 0;
    height: 0;
}
.switch-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e1;
    transition: .3s;
    border-radius: 18px;
}
.switch-slider:before {
    position: absolute;
    content: "";
    height: 14px;
    width: 14px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
}
.switch-wrapper input:checked + .switch-slider {
    background-color: #10b981;
}
.switch-wrapper input:checked + .switch-slider:before {
    transform: translateX(14px);
}

.calendar-widget {
    margin: 8px 14px 14px;
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
}
.calendar-month-selector {
    background: #f8fafc;
    border-bottom: 1px solid var(--border);
    padding: 6px 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.calendar-month-selector select {
    background: transparent;
    border: none;
    outline: none;
    font-size: 11px;
    font-weight: 700;
    color: var(--t1);
    cursor: pointer;
}
.calendar-month-selector .year-indicator {
    font-size: 11px;
    font-weight: 700;
    color: var(--t1);
}
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    text-align: center;
    padding: 6px;
}
.calendar-grid-header {
    font-size: 10px;
    font-weight: 700;
    color: var(--t2);
    padding: 4px 0;
}
.calendar-grid-day {
    font-size: 10.5px;
    font-weight: 600;
    color: var(--t1);
    padding: 6px 0;
    border-radius: 4px;
}
.calendar-grid-day.empty {
    color: transparent;
    pointer-events: none;
}
.calendar-grid-day.today {
    background: #f97316 !important;
    color: #fff !important;
    font-weight: 800 !important;
    box-shadow: 0 0 0 2px #fff, 0 0 0 4px #f97316 !important;
}
.calendar-grid-day:hover {
    background: #f1f5f9;
    cursor: pointer;
}

.toggle-group {
    display: flex;
    background: #f1f5f9;
    border-radius: 20px;
    padding: 3px;
    gap: 2px;
}
.toggle-group-btn {
    padding: 5px 12px;
    font-size: 10px;
    font-weight: 700;
    background: transparent;
    color: #64748b;
    border: none;
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.2s ease;
}
.toggle-group-btn.active {
    background: #4f46e5 !important;
    color: #fff !important;
    box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3);
}
.toggle-group-btn:focus {
    outline: none;
}

body.dark-mode .toggle-group {
    background: #1f2937 !important;
}
body.dark-mode .toggle-group-btn {
    color: #94a3b8 !important;
}
body.dark-mode .calendar-month-selector select,
body.dark-mode .calendar-month-selector .year-indicator,
body.dark-mode .calendar-grid-day {
    color: #f8fafc !important;
}
body.dark-mode .calendar-grid-day:hover {
    background: #374151 !important;
}

/* --- SIDE DRAWER --- */
.side-drawer-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(2px);
    z-index: 2000;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}
.side-drawer-overlay.open {
    opacity: 1;
    pointer-events: auto;
}
.side-drawer {
    position: fixed;
    top: 0;
    right: 0;
    width: 480px;
    max-width: 90vw;
    height: 100vh;
    background: #fff;
    box-shadow: -4px 0 24px rgba(0, 0, 0, 0.15);
    z-index: 2001;
    transform: translateX(100%);
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
}
.side-drawer.open {
    transform: translateX(0);
}
.drawer-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--navy);
    color: #fff;
}
.drawer-header h3 {
    font-size: 14px;
    font-weight: 700;
    font-family: 'Plus Jakarta Sans', sans-serif;
    margin: 0;
}
.drawer-close-btn {
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.8);
    font-size: 16px;
    cursor: pointer;
    transition: color 0.2s;
}
.drawer-close-btn:hover {
    color: #fff;
}
.drawer-body {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #f8fafc;
}
.drawer-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11.5px;
    margin-top: 10px;
}
.drawer-table th, .drawer-table td {
    padding: 10px 12px;
    text-align: left;
    border-bottom: 1px solid var(--border);
}
.drawer-table th {
    font-weight: 700;
    color: var(--t2);
    background: #f1f5f9;
}
.drawer-table td {
    color: var(--t1);
}
.drawer-table tr:hover {
    background: #f8fafc;
}
.drawer-badge {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
}
.drawer-badge.bg-active, .drawer-badge.bg-paid { background: #dcfce7; color: #15803d; }
.drawer-badge.bg-inactive, .drawer-badge.bg-absent { background: #fee2e2; color: #b91c1c; }
.drawer-badge.bg-pending, .drawer-badge.bg-partial { background: #fef3c7; color: #d97706; }
.drawer-badge.bg-not-marked { background: #f1f5f9; color: #64748b; }
.drawer-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 180px;
    color: var(--t2);
    font-size: 12px;
    gap: 8px;
}
.drawer-loading i {
    font-size: 24px;
    color: var(--gold);
}
.drawer-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 180px;
    color: var(--t3);
    font-size: 12px;
    text-align: center;
    gap: 8px;
}
.drawer-empty i {
    font-size: 28px;
    color: var(--border);
}

/* Custom Drawer Styling for Send Reminder, Student Details, Staff Details */
.drawer-orange-hdr {
    background: #d97706 !important;
}
.drawer-red-hdr {
    background: #ea580c !important;
}
.drawer-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    background: #fff;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid var(--border);
}
.drawer-select-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.drawer-select-group label {
    font-size: 10px;
    font-weight: 700;
    color: var(--t2);
}
.drawer-select {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 11.5px;
    font-weight: 600;
    color: var(--t1);
    outline: none;
    cursor: pointer;
}
.drawer-tabs-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}
.drawer-tab-btn-group {
    display: flex;
    border: 1px solid var(--border);
    border-radius: 6px;
    overflow: hidden;
}
.drawer-tab-btn {
    padding: 6px 14px;
    font-size: 11px;
    font-weight: 700;
    background: #fff;
    color: var(--t2);
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}
.drawer-tab-btn.active {
    background: #8b5cf6;
    color: #fff;
}
.drawer-tab-btn:hover:not(.active) {
    background: #f8fafc;
}
.drawer-btn-download, .drawer-btn-logs {
    background: transparent;
    border: 1.5px solid #8b5cf6;
    color: #8b5cf6;
    border-radius: 6px;
    font-size: 10.5px;
    font-weight: 700;
    padding: 5px 12px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}
.drawer-btn-download:hover, .drawer-btn-logs:hover {
    background: #8b5cf6;
    color: #fff;
}
.drawer-stat-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 16px;
}
.drawer-stat-card {
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    padding: 12px;
    position: relative;
}
.drawer-stat-card.orange-border {
    border-left: 4px solid #8b5cf6;
}
.drawer-stat-card.red-border {
    border-left: 4px solid #ef4444;
}
.drawer-stat-card-title {
    font-size: 9.5px;
    font-weight: 700;
    color: var(--t2);
    text-transform: uppercase;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.drawer-stat-card-val {
    font-size: 18px;
    font-weight: 800;
    color: var(--t1);
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.drawer-stat-card-sub {
    font-size: 9.5px;
    color: var(--green);
    font-weight: 600;
    margin-left: 6px;
}
.drawer-search-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}
.drawer-search-box {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 6px 10px;
    flex: 1;
    min-width: 200px;
}
.drawer-search-box i {
    color: var(--t3);
    font-size: 11px;
}
.drawer-search-box input {
    border: none;
    outline: none;
    font-size: 11.5px;
    color: var(--t1);
    width: 100%;
}
.drawer-table-wrap {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow-x: auto;
}
.drawer-table-complex {
    width: 100%;
    border-collapse: collapse;
    font-size: 10.5px;
    text-align: center;
}
.drawer-table-complex th, .drawer-table-complex td {
    padding: 8px 6px;
    border: 1px solid var(--border);
    white-space: nowrap;
}
.drawer-table-complex th {
    background: #0f172a;
    color: #fff;
    font-weight: 700;
    font-size: 10px;
}
.drawer-table-complex tr:nth-child(even) {
    background: #f8fafc;
}
.drawer-table-complex td.text-left {
    text-align: left;
    font-weight: 600;
}
.drawer-table-complex td.text-orange {
    color: #d97706;
    font-weight: 700;
}
.drawer-table-complex td.text-bold {
    font-weight: 700;
}
.drawer-table-complex tfoot {
    background: #f1f5f9;
    font-weight: 700;
}
/* Staff specific */
.drawer-staff-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #e2e8f0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--t2);
    font-weight: 700;
    font-size: 10px;
    margin-right: 8px;
    vertical-align: middle;
}
.drawer-staff-copy-btn {
    background: none;
    border: none;
    color: #d97706;
    cursor: pointer;
    margin-left: 4px;
    font-size: 10px;
}
.drawer-staff-copy-btn:hover {
    color: #b45309;
}
.drawer-action-btn {
    background: none;
    border: none;
    color: #d97706;
    cursor: pointer;
    font-size: 12px;
    margin: 0 4px;
}
.drawer-action-btn.green {
    color: var(--green);
}
/* Send reminder specific styling */
.reminder-option-row {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}
.reminder-option-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 700;
    color: var(--t1);
}
.reminder-or-divider {
    text-align: center;
    position: relative;
    margin: 20px 0;
    font-size: 12px;
    font-weight: 700;
    color: #0d9488;
}
.reminder-or-divider::before, .reminder-or-divider::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 45%;
    height: 1px;
    background: var(--border);
}
.reminder-or-divider::before { left: 0; }
.reminder-or-divider::after { right: 0; }

.reminder-selector-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}
.reminder-selector-row .drawer-select {
    flex: 1;
}
.reminder-bottom-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 16px 20px;
    border-top: 1px solid var(--border);
    background: #fff;
    display: flex;
    justify-content: flex-end;
}
.btn-send-now {
    background: #b45309;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 8px 18px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: background 0.2s;
}
.btn-send-now:hover {
    background: #92400e;
}

/* Event calendar card event items */
.calendar-events-list {
    border-top: 1px solid var(--border);
    padding: 10px 14px;
    max-height: 150px;
    overflow-y: auto;
}
.calendar-event-item {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
}
.calendar-event-item:last-child {
    margin-bottom: 0;
}
.calendar-event-bar {
    width: 3.5px;
    height: 22px;
    background: #0d9488;
    margin-right: 10px;
    border-radius: 2px;
}
.calendar-event-bar.staff {
    background: #db2777;
}
.calendar-event-date {
    font-size: 11px;
    font-weight: 700;
    color: #0d9488;
    margin-right: 8px;
    min-width: 60px;
}
.calendar-event-date.staff {
    color: #db2777;
}
.calendar-event-text {
    font-size: 11px;
    color: var(--t1);
}
.calendar-grid-day.has-event {
    background: #00695c !important;
    color: #fff !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 24px !important;
    height: 24px !important;
    line-height: 24px !important;
    margin: 2px auto !important;
}
.calendar-grid-day.has-event-staff {
    background: #db2777 !important;
    color: #fff !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 24px !important;
    height: 24px !important;
    line-height: 24px !important;
    margin: 2px auto !important;
}
.calendar-grid-day.today {
    background: #f97316 !important;
    color: #fff !important;
    font-weight: 800 !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 24px !important;
    height: 24px !important;
    line-height: 24px !important;
    margin: 2px auto !important;
    box-shadow: 0 0 0 2px #fff, 0 0 0 4px #f97316 !important;
    z-index: 2 !important;
    position: relative !important;
}

/* Side Drawer Dynamic Widths */
.side-drawer.drawer-sm {
    width: 480px;
}
.side-drawer.drawer-md {
    width: 700px;
}
.side-drawer.drawer-lg {
    width: 1100px;
}
.side-drawer.drawer-xl {
    width: 1280px;
}

/* ── DASHBOARD MOBILE RESPONSIVENESS ── */
@media (max-width: 1200px) {
    .top-summary-grid { grid-template-columns: repeat(2, 1fr) !important; }
    .charts-ai { grid-template-columns: 1fr !important; }
    .bottom-row { grid-template-columns: 1fr !important; }
}
@media (max-width: 768px) {
    .db-header-row { flex-direction: column; align-items: stretch !important; gap: 12px; }
    .academic-year-box, .followup-alert-box { width: 100% !important; justify-content: space-between !important; }
    .top-summary-grid { grid-template-columns: 1fr !important; }
    .enrollment-grid { grid-template-columns: 1fr !important; }
    .fee-mgmt-container { grid-template-columns: 1fr !important; }
    .qa-grid { grid-template-columns: repeat(2, 1fr) !important; }
    .side-drawer { width: 100% !important; max-width: 100vw !important; }
}
/* Dashboard Header & Alert Dark Mode Overrides */
body.dark-mode .academic-year-box, 
body.dark-mode .followup-alert-box {
    background: #111827 !important;
    border: 1px solid #1e293b !important;
    color: #f8fafc !important;
}
body.dark-mode .academic-year-box label {
    color: #94a3b8 !important;
}
body.dark-mode .selected-session-select {
    background: #1f2937 !important;
    border: 1px solid #374151 !important;
    color: #f8fafc !important;
}
body.dark-mode .followup-alert-box span {
    color: #f8fafc !important;
}
body.dark-mode .side-drawer {
    background: #111827 !important;
    border-left: 1px solid #1e293b !important;
    color: #f8fafc !important;
}
body.dark-mode .drawer-header {
    background: #1e293b !important;
    color: #ffffff !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
}
body.dark-mode .drawer-close-btn {
    color: #ffffff !important;
}
body.dark-mode .fee-management-subcard strong {
    color: #f8fafc !important;
}
body.dark-mode .fee-management-subcard span {
    color: #cbd5e1 !important;
}

/* Custom styles for Today's Attendance stock arrow */
.attendance-arrow-container {
    position: relative;
    width: 100%;
    height: 48px;
    margin-top: 8px;
    background: rgba(255, 255, 255, 0.45);
    border-radius: 10px;
    border: 1.5px dashed rgba(0, 178, 255, 0.25);
    overflow: hidden;
    transition: all 0.3s ease;
}
.arrow-track-line {
    stroke: rgba(0, 0, 0, 0.08);
}
.arrow-track-head {
    fill: rgba(0, 0, 0, 0.08);
}



/* Headcount charts layout */
.headcount-charts-container {
    display: flex;
    gap: 16px;
    align-items: stretch;
    justify-content: space-around;
    flex-wrap: wrap;
    margin-top: 8px;
}
.headcount-chart-box {
    flex: 1;
    min-width: 140px;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Glassmorphism Backdrop Blur for All Dashboard Components (Light Mode) */
.card,
.side-drawer,
.attendance-subpanel,
.attrition-box,
.toggle-group,
.updates-list,
.empty-state-container,
.academic-year-box,
.followup-alert-box {
    backdrop-filter: blur(12px) saturate(120%);
    -webkit-backdrop-filter: blur(12px) saturate(120%);
}

.card {
    background: rgba(255, 255, 255, 0.7) !important;
}
.attendance-subpanel {
    background: rgba(248, 250, 252, 0.7) !important;
}
.attrition-box {
    background: rgba(255, 255, 255, 0.75) !important;
}
.toggle-group {
    background: rgba(241, 245, 249, 0.75) !important;
}
.side-drawer {
    background: rgba(255, 255, 255, 0.85) !important;
}
.followup-alert-box {
    background: rgba(245, 158, 11, 0.08) !important;
}

/* Glassmorphism Backdrop Blur for All Dashboard Components (Dark Mode) */
body.dark-mode .card,
body.dark-mode .db-card {
    background: rgba(17, 24, 39, 0.75) !important;
    backdrop-filter: blur(12px) saturate(120%);
    -webkit-backdrop-filter: blur(12px) saturate(120%);
    border-color: rgba(255, 255, 255, 0.08) !important;
}
body.dark-mode .side-drawer {
    background: rgba(17, 24, 39, 0.85) !important;
    backdrop-filter: blur(16px) saturate(120%);
    -webkit-backdrop-filter: blur(16px) saturate(120%);
    border-left-color: rgba(255, 255, 255, 0.08) !important;
}
body.dark-mode .attendance-subpanel {
    background: rgba(31, 41, 55, 0.7) !important;
    backdrop-filter: blur(8px) saturate(120%);
    -webkit-backdrop-filter: blur(8px) saturate(120%);
    border-color: rgba(255, 255, 255, 0.05) !important;
}
body.dark-mode .attrition-box {
    background: rgba(31, 41, 55, 0.7) !important;
    backdrop-filter: blur(8px) saturate(120%);
    -webkit-backdrop-filter: blur(8px) saturate(120%);
    border-color: rgba(255, 255, 255, 0.05) !important;
}
body.dark-mode .toggle-group {
    background: rgba(31, 41, 55, 0.7) !important;
    backdrop-filter: blur(6px) saturate(120%);
    -webkit-backdrop-filter: blur(6px) saturate(120%);
}
body.dark-mode .followup-alert-box {
    background: rgba(245, 158, 11, 0.15) !important;
    backdrop-filter: blur(8px) saturate(120%);
    -webkit-backdrop-filter: blur(8px) saturate(120%);
    border-color: rgba(245, 158, 11, 0.3) !important;
}
body.dark-mode .academic-year-box {
    background: transparent !important;
    border: none !important;
}

/* Fixes for dark mode Notice update-item card (No white backgrounds!) */
body.dark-mode .update-item {
    background: rgba(255, 255, 255, 0.04) !important;
    border-left: 4px solid var(--purple) !important;
    box-shadow: none !important;
}
body.dark-mode .update-item-title {
    color: #f8fafc !important;
}
body.dark-mode .update-item-body {
    color: #cbd5e1 !important;
}

/* Fixes for dark mode calendar month selector header */
body.dark-mode .calendar-month-selector {
    background: rgba(31, 41, 55, 0.8) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
}
body.dark-mode .calendar-month-selector select option {
    background-color: #111827 !important;
    color: #f8fafc !important;
}

/* Custom dark mode scrollbars to replace white ones */
body.dark-mode *::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
body.dark-mode *::-webkit-scrollbar-track {
    background: transparent !important;
}
body.dark-mode *::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.15) !important;
    border-radius: 3px;
}
body.dark-mode *::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.25) !important;
}

/* Side Drawer Dark Mode Elements styling - No white backgrounds! */
body.dark-mode {
    --navy: #f8fafc !important;
    --t1: #f8fafc !important;
    --t2: #cbd5e1 !important;
    --t3: #94a3b8 !important;
    --border: rgba(255, 255, 255, 0.08) !important;
    --page: #111827 !important;
}
body.dark-mode .drawer-toolbar {
    background: #111827 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}
body.dark-mode .drawer-body {
    background: rgba(17, 24, 39, 0.95) !important;
    backdrop-filter: blur(12px);
    color: #f8fafc !important;
}
body.dark-mode .drawer-table-wrap {
    background: #111827 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}
body.dark-mode .drawer-table th {
    background: rgba(255, 255, 255, 0.05) !important;
    color: #f8fafc !important;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.1) !important;
}
body.dark-mode .drawer-table td {
    border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    color: #cbd5e1 !important;
}
body.dark-mode .drawer-table tr:hover {
    background: rgba(255, 255, 255, 0.03) !important;
}
body.dark-mode .drawer-select-group label {
    color: #94a3b8 !important;
}
body.dark-mode .drawer-select,
body.dark-mode .drawer-select-group select,
body.dark-mode select.drawer-select {
    background: #111827 !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    color: #f8fafc !important;
}
body.dark-mode .drawer-tab-btn {
    background: rgba(255, 255, 255, 0.05) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    color: #cbd5e1 !important;
}
body.dark-mode .drawer-tab-btn.active {
    background: #8b5cf6 !important;
    color: #fff !important;
}
body.dark-mode .drawer-tab-btn:hover:not(.active) {
    background: rgba(255, 255, 255, 0.1) !important;
}
body.dark-mode .drawer-stat-card {
    background: rgba(255, 255, 255, 0.04) !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}
body.dark-mode .drawer-stat-card-title {
    color: #94a3b8 !important;
}
body.dark-mode .drawer-stat-card-val {
    color: #f8fafc !important;
}
body.dark-mode .drawer-stat-card-sub {
    color: #cbd5e1 !important;
}
body.dark-mode .drawer-search-box {
    background: rgba(255, 255, 255, 0.05) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}
body.dark-mode .drawer-search-box input {
    background: transparent !important;
    color: #f8fafc !important;
}
body.dark-mode .drawer-table-complex {
    border-color: rgba(255, 255, 255, 0.08) !important;
}
body.dark-mode .drawer-table-complex th {
    background: rgba(255, 255, 255, 0.05) !important;
    color: #f8fafc !important;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.1) !important;
}
body.dark-mode .drawer-table-complex td {
    background: transparent !important;
    color: #cbd5e1 !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
}
body.dark-mode .drawer-table-complex tr:nth-child(even) {
    background: rgba(255, 255, 255, 0.02) !important;
}
body.dark-mode .drawer-table-complex tr:nth-child(even) td {
    background: transparent !important;
}
body.dark-mode .drawer-table-complex tfoot {
    background: rgba(255, 255, 255, 0.05) !important;
    color: #f8fafc !important;
}
body.dark-mode .drawer-table-complex tfoot td {
    background: transparent !important;
    color: #f8fafc !important;
    border-top: 1.5px solid rgba(255, 255, 255, 0.1) !important;
}
/* Ensure style attributes are overridden in dark mode */
body.dark-mode div[style*="background: #fff"],
body.dark-mode div[style*="background:#fff"],
body.dark-mode div[style*="background: var(--page)"],
body.dark-mode select[style*="background: #fff"],
body.dark-mode select[style*="background:#fff"] {
    background: #111827 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    color: #f8fafc !important;
}
body.dark-mode tr[style*="background:#f1f5f9"],
body.dark-mode tr[style*="background: #f1f5f9"] {
    background: rgba(255, 255, 255, 0.05) !important;
    color: #f8fafc !important;
}
/* Ensure span/div inline text styles targeting navy or dark blue are visible */
body.dark-mode span[style*="color: var(--navy)"],
body.dark-mode span[style*="color:var(--navy)"],
body.dark-mode div[style*="color: var(--navy)"],
body.dark-mode div[style*="color:var(--navy)"] {
    color: #f8fafc !important;
}
body.dark-mode .reminder-or-divider {
    color: #94a3b8 !important;
}
body.dark-mode .reminder-option-item {
    color: #cbd5e1 !important;
}
body.dark-mode .drawer-badge.bg-not-marked {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #cbd5e1 !important;
}
body.dark-mode .drawer-badge.bg-pending, 
body.dark-mode .drawer-badge.bg-partial {
    background: rgba(217, 119, 6, 0.2) !important;
    color: #fbbf24 !important;
}
body.dark-mode .drawer-badge.bg-active, 
body.dark-mode .drawer-badge.bg-paid {
    background: rgba(16, 185, 129, 0.2) !important;
    color: #34d399 !important;
}
body.dark-mode .drawer-badge.bg-inactive, 
body.dark-mode .drawer-badge.bg-absent {
    background: rgba(239, 68, 68, 0.2) !important;
    color: #fca5a5 !important;
}

/* ========================================== */
/* SCOPED STYLE BLOCK FOR ENROLLMENT OVERVIEW  */
/* ========================================== */
.enrollment-overview-container {
    margin-top: 24px;
    margin-bottom: 24px;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
}

/* Light Theme Card Defaults */
.enrollment-overview-container .enrollment-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 20px !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02) !important;
    padding: 20px !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    overflow: hidden;
}

.enrollment-overview-container .enrollment-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.05) !important;
}

/* Dark Theme Overrides for Cards */
body.dark-mode .enrollment-overview-container .enrollment-card {
    background: #11152d !important;
    border-color: #202747 !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
}

/* Card Header Styling */
.enrollment-overview-container .enrollment-card-hdr {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}

.enrollment-overview-container .enrollment-card-title {
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}

body.dark-mode .enrollment-overview-container .enrollment-card-title {
    color: #ffffff !important;
}

.enrollment-overview-container .enrollment-card-title i {
    color: #8b5cf6;
    font-size: 16px;
}

.enrollment-overview-container .enrollment-card-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.enrollment-overview-container .enrollment-card-actions i.refresh-trigger {
    font-size: 13px;
    color: #94a3b8;
    cursor: pointer;
    transition: transform 0.3s ease, color 0.2s;
}

.enrollment-overview-container .enrollment-card-actions i.refresh-trigger:hover {
    transform: rotate(180deg);
    color: #6366f1;
}

/* --- Card 1: Headcount Overview Specifics --- */
.enrollment-overview-container .headcount-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 0;
    gap: 16px;
}

.enrollment-overview-container .headcount-row:first-child {
    padding-top: 0;
    border-bottom: 1px solid #f1f5f9;
}

body.dark-mode .enrollment-overview-container .headcount-row:first-child {
    border-bottom-color: #202747 !important;
}

.enrollment-overview-container .headcount-row:last-child {
    padding-bottom: 0;
}

.enrollment-overview-container .headcount-info {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.enrollment-overview-container .headcount-row-title {
    font-size: 13px;
    font-weight: 700;
    color: #4f46e5;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 4px;
    cursor: pointer;
}

body.dark-mode .enrollment-overview-container .headcount-row-title {
    color: #818cf8 !important;
}

.enrollment-overview-container .headcount-num {
    font-size: 30px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
}

body.dark-mode .enrollment-overview-container .headcount-num {
    color: #ffffff !important;
}

.enrollment-overview-container .headcount-sublbl {
    font-size: 11px;
    color: #94a3b8;
    margin-top: 2px;
}

.enrollment-overview-container .headcount-chart-area {
    display: flex;
    align-items: center;
    gap: 14px;
}

.enrollment-overview-container .headcount-canvas-wrap {
    width: 76px;
    height: 76px;
    position: relative;
    flex-shrink: 0;
}

.enrollment-overview-container .headcount-legend {
    display: flex;
    flex-direction: column;
    gap: 5px;
    min-width: 105px;
}

.enrollment-overview-container .legend-row-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 10.5px;
    color: #475569;
}

body.dark-mode .enrollment-overview-container .legend-row-item {
    color: #cbd5e1 !important;
}

.enrollment-overview-container .legend-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
}

.enrollment-overview-container .legend-dot.dot-blue { background: #3b82f6; }
.enrollment-overview-container .legend-dot.dot-pink { background: #ec4899; }
.enrollment-overview-container .legend-dot.dot-green { background: #10b981; }
.enrollment-overview-container .legend-dot.dot-grey { background: #64748b; }

.enrollment-overview-container .legend-label {
    flex: 1;
}

.enrollment-overview-container .legend-value {
    font-weight: 700;
    color: #1f2937;
}

body.dark-mode .enrollment-overview-container .legend-value {
    color: #ffffff !important;
}

/* --- Card 2: Joining & Attrition Specifics --- */
.enrollment-overview-container .attrition-subgrid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 14px;
}

.enrollment-overview-container .attrition-subcard {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

body.dark-mode .enrollment-overview-container .attrition-subcard {
    background: #171d37 !important;
    border-color: #232c52 !important;
}

.enrollment-overview-container .attrition-subcard-title {
    font-size: 12px;
    font-weight: 700;
    color: #312e81;
    display: flex;
    align-items: center;
    gap: 6px;
}

body.dark-mode .enrollment-overview-container .attrition-subcard-title {
    color: #ffffff !important;
}

.enrollment-overview-container .attrition-row-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11px;
    color: #475569;
}

body.dark-mode .enrollment-overview-container .attrition-row-item {
    color: #cbd5e1 !important;
}

.enrollment-overview-container .attrition-label-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
}

.enrollment-overview-container .attrition-icon-circle {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 8px;
    color: #ffffff;
}

.enrollment-overview-container .attrition-icon-circle.joined { background: #10b981; }
.enrollment-overview-container .attrition-icon-circle.exited { background: #ef4444; }

.enrollment-overview-container .attrition-value {
    font-weight: 700;
    color: #1e293b;
}

body.dark-mode .enrollment-overview-container .attrition-value {
    color: #ffffff !important;
}

.enrollment-overview-container .attrition-strength-wrap {
    border-top: 1px dashed #e2e8f0;
    padding-top: 8px;
    margin-top: 2px;
}

body.dark-mode .enrollment-overview-container .attrition-strength-wrap {
    border-top-color: #232c52 !important;
}

.enrollment-overview-container .attrition-strength-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.enrollment-overview-container .attrition-strength-num {
    font-size: 16px;
    font-weight: 800;
    color: #0f172a;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
}

body.dark-mode .enrollment-overview-container .attrition-strength-num {
    color: #ffffff !important;
}

.enrollment-overview-container .attrition-strength-trend {
    font-size: 10px;
    font-weight: 700;
    color: #10b981;
    display: flex;
    align-items: center;
    gap: 2px;
}

/* Enrollment Alert Banner */
.enrollment-overview-container .enrollment-alert-banner {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.08), rgba(245, 158, 11, 0.03));
    border: 1px solid rgba(245, 158, 11, 0.25);
    border-radius: 12px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    gap: 12px;
}

body.dark-mode .enrollment-overview-container .enrollment-alert-banner {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.12), rgba(245, 158, 11, 0.05)) !important;
    border-color: rgba(245, 158, 11, 0.3) !important;
}

.enrollment-overview-container .enrollment-alert-icon-wrap {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(245, 158, 11, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: #d97706;
    flex-shrink: 0;
}

.enrollment-overview-container .enrollment-alert-content {
    display: flex;
    flex-direction: column;
}

.enrollment-overview-container .enrollment-alert-title {
    font-size: 11px;
    font-weight: 700;
    color: #b45309;
}

body.dark-mode .enrollment-overview-container .enrollment-alert-title {
    color: #fbbf24 !important;
}

.enrollment-overview-container .enrollment-alert-sub {
    font-size: 9.5px;
    color: #d97706;
}

body.dark-mode .enrollment-overview-container .enrollment-alert-sub {
    color: #f59e0b !important;
    opacity: 0.8;
}

/* --- Card 3: Admission Summary Specifics --- */
.enrollment-overview-container .admission-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.enrollment-overview-container .admission-pill-toggle {
    display: flex;
    background: #f1f5f9;
    padding: 3px;
    border-radius: 20px;
    gap: 2px;
}

body.dark-mode .enrollment-overview-container .admission-pill-toggle {
    background: #171d37 !important;
}

.enrollment-overview-container .admission-toggle-btn {
    border: none;
    background: transparent;
    padding: 5px 12px;
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.2s ease;
}

body.dark-mode .enrollment-overview-container .admission-toggle-btn {
    color: #cbd5e1 !important;
}

.enrollment-overview-container .admission-toggle-btn.active {
    background: #8b5cf6 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(139, 92, 246, 0.35);
}

.enrollment-overview-container .admission-filter-btn {
    border: 1px solid #e2e8f0;
    background: #ffffff;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
}

body.dark-mode .enrollment-overview-container .admission-filter-btn {
    background: #171d37 !important;
    border-color: #232c52 !important;
    color: #cbd5e1 !important;
}

.enrollment-overview-container .admission-filter-btn:hover {
    border-color: #8b5cf6;
    color: #8b5cf6;
}

.enrollment-overview-container .admission-chart-wrap {
    height: 154px;
    position: relative;
    width: 100%;
    margin-top: 8px;
}

/* ========================================== */
/* SCOPED STYLE BLOCK FOR QUICK INSIGHTS      */
/* ========================================== */
.insights-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 32px 0 16px;
}

.insights-header-icon-wrap {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: rgba(139, 92, 246, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #8b5cf6;
}

.insights-header-title {
    display: flex;
    flex-direction: column;
}

.insights-header-title h3 {
    font-size: 15px;
    font-weight: 800;
    color: #3730a3;
    margin: 0;
}

body.dark-mode .insights-header-title h3 {
    color: #ffffff !important;
}

.insights-header-title span {
    font-size: 11px;
    color: #64748b;
    margin-top: 1px;
}

body.dark-mode .insights-header-title span {
    color: #94a3b8 !important;
}

.insights-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}

@media(max-width: 1280px) {
    .insights-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media(max-width: 768px) {
    .insights-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media(max-width: 480px) {
    .insights-grid {
        grid-template-columns: 1fr;
    }
}

.insight-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 16px 18px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}

.insight-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.04);
}

body.dark-mode .insight-card {
    background: #11152d !important;
    border-color: #202747 !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
}

.insight-card-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
}

.insight-card-icon-wrap {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    color: #ffffff;
}

.insight-card-icon-wrap.blue {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
}

.insight-card-icon-wrap.green {
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 4px 10px rgba(5, 150, 105, 0.2);
}

.insight-card-icon-wrap.pink {
    background: linear-gradient(135deg, #ec4899, #db2777);
    box-shadow: 0 4px 10px rgba(219, 39, 119, 0.2);
}

.insight-card-icon-wrap.cyan {
    background: linear-gradient(135deg, #06b6d4, #0891b2);
    box-shadow: 0 4px 10px rgba(8, 145, 178, 0.2);
}

.insight-card-icon-wrap.orange {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    box-shadow: 0 4px 10px rgba(217, 119, 6, 0.2);
}

.insight-card-info {
    display: flex;
    flex-direction: column;
    margin-top: 4px;
}

.insight-card-label {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 2px;
}

body.dark-mode .insight-card-label {
    color: #cbd5e1 !important;
}

.insight-card-value {
    font-size: 24px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
}

body.dark-mode .insight-card-value {
    color: #ffffff !important;
}

.insight-card-trend {
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 3px;
}

.insight-card-trend.up {
    color: #10b981;
}

.insight-card-trend.muted {
    color: #94a3b8;
    font-weight: 500;
}

/* ─── REDESIGNED FINANCIAL CHART CARD ─── */
.financial-chart-card {
    border-radius: 24px !important;
    padding: 24px !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.7), rgba(240, 244, 255, 0.8)) !important;
    border: 1px solid rgba(99, 102, 241, 0.12) !important;
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.6) !important;
}

body.dark-mode .financial-chart-card {
    background: linear-gradient(135deg, rgba(26, 32, 66, 0.75), rgba(18, 22, 45, 0.85)) !important;
    border-color: rgba(99, 102, 241, 0.22) !important;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3) !important;
}

.financial-card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 24px;
}

.financial-title-area {
    display: flex;
    align-items: center;
    gap: 14px;
}

.financial-icon-wrapper {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #6366f1, #3b82f6);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: #fff;
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
}

.financial-title-text h3 {
    font-size: 16px !important;
    font-weight: 800 !important;
    color: #1e293b !important;
    margin: 0 !important;
    font-family: 'Plus Jakarta Sans', sans-serif;
    display: flex;
    align-items: center;
    gap: 8px;
}

body.dark-mode .financial-title-text h3 {
    color: #ffffff !important;
}

.financial-title-text p {
    font-size: 11.5px;
    color: #64748b;
    margin: 4px 0 0 0 !important;
}

body.dark-mode .financial-title-text p {
    color: #94a3b8 !important;
}

.financial-widgets-area {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.financial-widget {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.25s ease;
    border: 1px solid transparent;
}

.widget-income {
    background: rgba(16, 185, 129, 0.06);
    border-color: rgba(16, 185, 129, 0.12);
}

.widget-income:hover {
    background: rgba(16, 185, 129, 0.1);
    transform: translateY(-1px);
}

body.dark-mode .widget-income {
    background: rgba(16, 185, 129, 0.1);
    border-color: rgba(16, 185, 129, 0.18);
}

body.dark-mode .widget-income:hover {
    background: rgba(16, 185, 129, 0.15);
}

.widget-expense {
    background: rgba(239, 68, 68, 0.06);
    border-color: rgba(239, 68, 68, 0.12);
}

.widget-expense:hover {
    background: rgba(239, 68, 68, 0.1);
    transform: translateY(-1px);
}

body.dark-mode .widget-expense {
    background: rgba(239, 68, 68, 0.1);
    border-color: rgba(239, 68, 68, 0.18);
}

body.dark-mode .widget-expense:hover {
    background: rgba(239, 68, 68, 0.15);
}

.financial-widget .widget-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.widget-income .widget-icon {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
}

.widget-expense .widget-icon {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
}

.widget-details {
    display: flex;
    flex-direction: column;
}

.widget-label {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    display: flex;
    align-items: center;
    gap: 4px;
}

body.dark-mode .widget-label {
    color: #cbd5e1;
}

.widget-value {
    font-size: 15px;
    font-weight: 800;
    color: #1e293b;
    font-family: 'Plus Jakarta Sans', sans-serif;
    margin-top: 1px;
}

body.dark-mode .widget-value {
    color: #ffffff;
}

.financial-mid-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    margin-top: 8px;
}

.amount-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #475569;
    margin: 0;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

body.dark-mode .amount-title {
    color: #e2e8f0;
}

.financial-filter {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 6px 12px;
    box-shadow: var(--shadow);
}

body.dark-mode .financial-filter {
    background: #111827;
}

.financial-filter .calendar-icon {
    color: #6366f1;
    font-size: 12px;
}

.financial-select-filter {
    border: none;
    outline: none;
    background: transparent;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--t1);
    cursor: pointer;
    font-family: 'Plus Jakarta Sans', sans-serif;
    padding-right: 4px;
}

.floating-legend-overlay {
    position: absolute;
    bottom: 12px;
    right: 12px;
    background: rgba(255, 255, 255, 0.7);
    border: 1px solid rgba(99, 102, 241, 0.15);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-radius: 12px;
    padding: 6px 14px;
    display: flex;
    gap: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    z-index: 10;
}

body.dark-mode .floating-legend-overlay {
    background: rgba(17, 24, 39, 0.75);
    border-color: rgba(255, 255, 255, 0.08);
}

.floating-legend-overlay .legend-item {
    font-size: 11px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 6px;
}

.floating-legend-overlay .legend-inc {
    color: #10b981;
}

.floating-legend-overlay .legend-exp {
    color: #ef4444;
}

.floating-legend-overlay .legend-item .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.floating-legend-overlay .legend-inc .dot {
    background: #10b981;
}

.floating-legend-overlay .legend-exp .dot {
    background: #ef4444;
}

.financial-bottom-legend {
    display: flex;
    justify-content: center;
    gap: 16px;
    padding: 12px 0 4px;
    font-size: 11px;
    font-weight: 600;
    color: var(--t2);
}

.legend-dot-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.legend-dot-item .color-dot {
    width: 10px;
    height: 10px;
    border-radius: 3px;
    display: inline-block;
}

.legend-dot-item .dot-orange {
    background: #f59e0b;
}

.legend-dot-item .dot-grey {
    background: #9ca3af;
}

/* ─── REDESIGNED ATTENDANCE CARD ─── */
.attendance-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.attendance-title-area {
    display: flex;
    align-items: center;
    gap: 12px;
}

.attendance-icon-wrapper {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #6366f1, #3b82f6);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #fff;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
}

.attendance-title-text h3 {
    font-size: 15px !important;
    font-weight: 800 !important;
    color: var(--t1) !important;
    margin: 0 !important;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.attendance-title-text p {
    font-size: 11px;
    color: var(--t2);
    margin: 2px 0 0 0 !important;
}

.btn-attendance-approval {
    background: #4f46e5;
    color: #fff;
    border: none;
    border-radius: 20px;
    padding: 6px 14px;
    font-size: 10.5px;
    font-weight: 700;
    cursor: pointer;
    text-transform: uppercase;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    transition: all 0.2s;
}

.btn-attendance-approval:hover {
    background: #4338ca;
    transform: translateY(-1px);
}

.attendance-subpanel-custom {
    background: rgba(255, 255, 255, 0.45);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    padding: 16px;
    margin-bottom: 14px;
}

body.dark-mode .attendance-subpanel-custom {
    background: rgba(255, 255, 255, 0.03);
    border-color: rgba(255, 255, 255, 0.05);
}

.attendance-subpanel-title-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
}

.attendance-subpanel-title {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: var(--t1) !important;
    display: flex;
    align-items: center;
    gap: 6px;
}

.detailed-view-link {
    font-size: 10px;
    font-weight: 700;
    color: #6366f1;
    text-decoration: none;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 4px;
}

.detailed-view-link:hover {
    text-decoration: underline;
}

.chart-relative-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.chart-donut-wrap {
    position: relative;
    width: 96px;
    height: 96px;
    flex-shrink: 0;
}

.chart-center-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    pointer-events: none;
    display: flex;
    flex-direction: column;
}

.chart-center-overlay .pct-value {
    font-size: 13.5px;
    font-weight: 800;
    color: var(--t1);
    font-family: 'Plus Jakarta Sans', sans-serif;
    line-height: 1.1;
}

.chart-center-overlay .pct-label {
    font-size: 8px;
    color: var(--t2);
    text-transform: uppercase;
    letter-spacing: 0.2px;
}

.vertical-legend-list {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.vertical-legend-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 10.5px;
    font-weight: 600;
    color: var(--t2);
}

.legend-label-group {
    display: flex;
    align-items: center;
    gap: 6px;
}

.legend-color-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
}

.vertical-legend-item strong {
    color: var(--t1);
}

/* Attendance Card Footer KPIs */
.attendance-footer-kpis {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    border-top: 1px solid var(--border);
    padding-top: 14px;
    margin-top: 8px;
}

.kpi-stat-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.kpi-stat-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    color: #fff;
}

.kpi-stat-icon.students { background: linear-gradient(135deg, #a855f7, #7c3aed); }
.kpi-stat-icon.staff { background: linear-gradient(135deg, #10b981, #059669); }
.kpi-stat-icon.today { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }

.kpi-stat-details {
    display: flex;
    flex-direction: column;
}

.kpi-stat-label {
    font-size: 9px;
    color: var(--t3);
    text-transform: uppercase;
    font-weight: 700;
}

.kpi-stat-value {
    font-size: 12.5px;
    font-weight: 800;
    color: var(--t1);
    font-family: 'Plus Jakarta Sans', sans-serif;
}

/* ─── REDESIGNED EVENT CALENDAR ─── */
.calendar-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.calendar-title-area {
    display: flex;
    align-items: center;
    gap: 12px;
}

.calendar-icon-wrapper {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #6366f1, #3b82f6);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #fff;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
}

.calendar-title-text h3 {
    font-size: 15px !important;
    font-weight: 800 !important;
    color: var(--t1) !important;
    margin: 0 !important;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.calendar-title-text p {
    font-size: 11px;
    color: var(--t2);
    margin: 2px 0 0 0 !important;
}

.calendar-header-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.calendar-header-actions i.refresh-trigger {
    font-size: 14px;
    color: var(--t2);
    cursor: pointer;
    transition: transform 0.3s;
}

.calendar-header-actions i.refresh-trigger:hover {
    color: var(--t1);
    transform: rotate(180deg);
}

.calendar-toggles-custom {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 16px;
    border-bottom: 1px solid var(--border);
    padding-bottom: 12px;
}

.calendar-toggle-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--t2);
}

/* Green switches */
.switch-slider-green {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: .3s;
    border-radius: 18px;
}
.switch-slider-green:before {
    position: absolute;
    content: "";
    height: 14px; width: 14px; left: 2px; bottom: 2px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
}
input:checked + .switch-slider-green {
    background-color: #10b981;
}
input:checked + .switch-slider-green:before {
    transform: translateX(14px);
}

/* Calendar month/year selectors */
.calendar-month-selector-custom {
    background: rgba(255, 255, 255, 0.4);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 6px 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
}

body.dark-mode .calendar-month-selector-custom {
    background: rgba(255, 255, 255, 0.03);
}

.calendar-month-selector-custom select {
    background: transparent;
    border: none;
    outline: none;
    font-size: 12px;
    font-weight: 700;
    color: var(--t1);
    cursor: pointer;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

/* Days highlighting styles */
.calendar-grid-day.has-event-student {
    background: rgba(16, 185, 129, 0.25) !important;
    border: 1.5px solid #10b981 !important;
    color: #10b981 !important;
    font-weight: 800 !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 24px !important;
    height: 24px !important;
    margin: 2px auto !important;
}
.calendar-grid-day.has-event-staff {
    background: rgba(20, 184, 166, 0.25) !important;
    border: 1.5px solid #14b8a6 !important;
    color: #14b8a6 !important;
    font-weight: 800 !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 24px !important;
    height: 24px !important;
    margin: 2px auto !important;
}
.calendar-grid-day.has-event-school {
    background: rgba(139, 92, 246, 0.25) !important;
    border: 1.5px solid #8b5cf6 !important;
    color: #8b5cf6 !important;
    font-weight: 800 !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 24px !important;
    height: 24px !important;
    margin: 2px auto !important;
}
.calendar-grid-day.has-holiday {
    background: rgba(239, 68, 68, 0.25) !important;
    border: 1.5px solid #ef4444 !important;
    color: #ef4444 !important;
    font-weight: 800 !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 24px !important;
    height: 24px !important;
    margin: 2px auto !important;
}

/* Event List Styling */
.calendar-events-section {
    border-top: 1px solid var(--border);
    margin-top: 14px;
    padding-top: 14px;
}

.calendar-events-title-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.calendar-events-title-row h4 {
    font-size: 13px;
    font-weight: 800;
    color: var(--t1);
    margin: 0;
}

.btn-view-all-events {
    font-size: 10px;
    font-weight: 700;
    color: #6366f1;
    text-decoration: none;
    text-transform: uppercase;
}

.btn-view-all-events:hover {
    text-decoration: underline;
}

.calendar-event-item-custom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.4);
    border: 1px solid rgba(0,0,0,0.03);
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

body.dark-mode .calendar-event-item-custom {
    background: rgba(255, 255, 255, 0.03);
    border-color: rgba(255, 255, 255, 0.05);
}

.calendar-event-item-custom:hover {
    transform: translateX(4px);
    background: rgba(255, 255, 255, 0.6);
}

body.dark-mode .calendar-event-item-custom:hover {
    background: rgba(255, 255, 255, 0.06);
}

/* Event types highlights */
.calendar-event-item-custom.student-birthday-highlight {
    background: linear-gradient(90deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 185, 129, 0) 100%) !important;
    border-left: 3px solid #10b981;
}
.calendar-event-item-custom.staff-birthday-highlight {
    background: linear-gradient(90deg, rgba(20, 184, 166, 0.08) 0%, rgba(20, 184, 166, 0) 100%) !important;
    border-left: 3px solid #14b8a6;
}
.calendar-event-item-custom.school-event-highlight {
    background: linear-gradient(90deg, rgba(139, 92, 246, 0.08) 0%, rgba(139, 92, 246, 0) 100%) !important;
    border-left: 3px solid #8b5cf6;
}
.calendar-event-item-custom.holiday-highlight {
    background: linear-gradient(90deg, rgba(239, 68, 68, 0.08) 0%, rgba(239, 68, 68, 0) 100%) !important;
    border-left: 3px solid #ef4444;
}

.event-left-badge {
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    min-width: 58px;
    text-align: center;
    border: 1px solid transparent;
}

.event-left-badge.student { border-color: rgba(16, 185, 129, 0.3); color: #10b981; }
.event-left-badge.staff { border-color: rgba(20, 184, 166, 0.3); color: #14b8a6; }
.event-left-badge.event { border-color: rgba(139, 92, 246, 0.3); color: #8b5cf6; }
.event-left-badge.holiday { border-color: rgba(239, 68, 68, 0.3); color: #ef4444; }

.event-mid-details {
    flex: 1;
    margin-left: 12px;
    display: flex;
    flex-direction: column;
}

.event-mid-title {
    font-size: 11.5px;
    font-weight: 700;
    color: var(--t1);
}

.event-mid-sub {
    font-size: 9.5px;
    color: var(--t2);
    margin-top: 1px;
}

.event-right-type {
    margin-left: 8px;
}

.event-type-pill {
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 12px;
    text-transform: capitalize;
}

.event-type-pill.student { background: rgba(16, 185, 129, 0.12); color: #10b981; }
.event-type-pill.staff { background: rgba(20, 184, 166, 0.12); color: #14b8a6; }
.event-type-pill.event { background: rgba(139, 92, 246, 0.12); color: #8b5cf6; }
.event-type-pill.holiday { background: rgba(239, 68, 68, 0.12); color: #ef4444; }
</style>
@endsection

@section('content')
<!-- ══ HEADER ALERT & ACADEMIC YEAR ROW ══ -->
        <div class="db-header-row">
            <div class="academic-year-box" style="position: relative; display: inline-block;">
                <label style="display:block; margin-bottom: 2px;">Academic Year *</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <i class="fas fa-calendar-alt" style="position: absolute; left: 10px; color: var(--t2); pointer-events: none; font-size: 13px;"></i>
                    <select id="academic-year-select" class="selected-session-select" style="padding-left: 28px; appearance: none; -webkit-appearance: none; -moz-appearance: none; padding-right: 24px; cursor: pointer;" onchange="changeAcademicSession(this.value)">
                        @forelse($sessions as $session)
                            <option value="{{ $session->id }}" {{ ($currentSession && $currentSession->id == $session->id) ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @empty
                            <option value="">No sessions configured</option>
                        @endforelse
                    </select>
                    <i class="fas fa-chevron-down" style="position: absolute; right: 10px; color: var(--t2); pointer-events: none; font-size: 9px;"></i>
                </div>
            </div>
            <div class="followup-alert-box">
                <div class="followup-slider" id="followupSlider">
                    <!-- Slide 1: Greeting with clock -->
                    <div class="followup-slide">
                        <i id="greeting-icon" class="fas fa-sun" style="color:#7c3aed;font-size:14px;"></i>
                        <span id="greeting-text" class="typewriter-cursor" style="font-weight:700;color:#7c3aed;font-family:'Plus Jakarta Sans',sans-serif;">Good day, {{ auth()->user()->name }}! 👋</span>
                        <div class="greeting-clock-wrap" style="margin-left:auto;">
                            <i class="fas fa-clock greeting-clock-icon"></i>
                            <span id="greeting-clock" style="font-family:'Courier New',monospace;font-size:12.5px;font-weight:800;color:#7c3aed;letter-spacing:1px;">00:00:00 AM</span>
                        </div>
                    </div>
                    <!-- Slide for Today's Events (if any) -->
                    @if(isset($todayEvents) && $todayEvents->count() > 0)
                        @foreach($todayEvents as $evt)
                            <div class="followup-slide" style="justify-content: space-between; width: 100%;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-calendar-day" style="color: #ef4444; font-size: 14px;"></i>
                                    <span style="font-weight: 700; color: #7c3aed;">Today is: {{ $evt->title }} 🎉</span>
                                </div>
                                <span class="badge {{ $evt->is_holiday ? 'badge-danger' : 'badge-success' }}" style="font-size: 10px; padding: 2px 8px; border-radius:12px;">{{ $evt->is_holiday ? 'Holiday' : 'Event' }}</span>
                            </div>
                        @endforeach
                    @endif
                    <!-- Slide 2: Follow-ups -->
                    <div class="followup-slide" style="justify-content: space-between; width: 100%;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-bell" style="color: #8b5cf6; animation: bell-swing 2s infinite ease-in-out;"></i>
                            <span style="font-weight: 700; color: #7c3aed;">You have 0 Admission follow-ups today</span>
                        </div>
                        <button class="btn-gold-outline-sm" onclick="openDrawer('admissions')" style="margin-left: 12px; margin-bottom: 2px;">View Follow-ups</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- SVG Gradient Definitions for charts -->
        <svg style="width:0; height:0; position:absolute;" aria-hidden="true" focusable="false">
          <defs>
            <linearGradient id="income-grad" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#10b981" stop-opacity="0.4"></stop>
              <stop offset="100%" stop-color="#10b981" stop-opacity="0.0"></stop>
            </linearGradient>
            <linearGradient id="orange-grad" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#8b5cf6" stop-opacity="0.4"></stop>
              <stop offset="100%" stop-color="#8b5cf6" stop-opacity="0.0"></stop>
            </linearGradient>
            <linearGradient id="today-ring-grad" x1="0" y1="0" x2="1" y2="1">
              <stop offset="0%" stop-color="#a855f7"></stop>
              <stop offset="100%" stop-color="#6366f1"></stop>
            </linearGradient>
            <linearGradient id="total-ring-grad" x1="0" y1="0" x2="1" y2="1">
              <stop offset="0%" stop-color="#ec4899"></stop>
              <stop offset="100%" stop-color="#f43f5e"></stop>
            </linearGradient>
            <filter id="today-ring-glow" x="-20%" y="-20%" width="140%" height="140%">
              <feDropShadow dx="0" dy="1.5" stdDeviation="1.2" flood-color="#a855f7" flood-opacity="0.4"/>
            </filter>
            <filter id="total-ring-glow" x="-20%" y="-20%" width="140%" height="140%">
              <feDropShadow dx="0" dy="1.5" stdDeviation="1.2" flood-color="#ec4899" flood-opacity="0.4"/>
            </filter>
          </defs>
        </svg>

        <!-- ══ TOP SUMMARY CARDS (4 COLUMNS) ══ -->
        <div class="top-summary-grid">
            <!-- 1. Overview -->
            <div class="sum-card hc-blue" id="box-overview">
                <div class="card-top-header">
                    <div class="header-left-info">
                        <div class="header-icon-wrapper">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div>
                            <h4>Overview</h4>
                            <p class="subtitle">Institute summary</p>
                        </div>
                    </div>
                    <div class="header-right-icons">
                        <i class="fas fa-arrows-rotate refresh-trigger" onclick="refreshBox('overview', this)"></i>
                        <i class="fas fa-ellipsis-h ellipsis-icon"></i>
                    </div>
                </div>
                <div class="card-body-content">
                    <div class="glass-list-item" onclick="openDrawer('students')">
                        <div class="item-left">
                            <div class="item-icon-circle blue">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="item-text">
                                <span class="title">Students</span>
                                <span class="sub">Total Enrolled</span>
                            </div>
                        </div>
                        <div class="item-right">
                            <strong class="value text-blue" id="val-students-count">{{ $totalStudents }}</strong>
                            <i class="fas fa-chevron-right chevron"></i>
                        </div>
                    </div>
                    <div class="glass-list-item" onclick="openDrawer('staffs')">
                        <div class="item-left">
                            <div class="item-icon-circle purple">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="item-text">
                                <span class="title">Staffs</span>
                                <span class="sub">Total Staff Members</span>
                            </div>
                        </div>
                        <div class="item-right">
                            <strong class="value text-blue" id="val-staffs-count">{{ $totalStaffs }}</strong>
                            <i class="fas fa-chevron-right chevron"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Accounts Summary -->
            <div class="sum-card ac-teal" id="box-accounts">
                <div class="card-top-header">
                    <div class="header-left-info">
                        <div class="header-icon-wrapper">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <h4>Accounts Summary</h4>
                            <p class="subtitle">Financial overview</p>
                        </div>
                    </div>
                    <div class="header-right-icons">
                        <i class="fas fa-arrows-rotate refresh-trigger" onclick="refreshBox('accounts', this)"></i>
                        <i class="fas fa-ellipsis-h ellipsis-icon"></i>
                    </div>
                </div>
                <div class="card-body-content accounts-grid">
                    <div class="accounts-subcard income" onclick="openDrawer('income')">
                        <div class="subcard-header">
                            <div>
                                <span class="label">Total Income</span>
                                <strong class="value" id="val-total-income">₹{{ number_format($totalIncome) }}</strong>
                            </div>
                            <div class="trend-icon-circle up">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                        <div class="subcard-trend">
                            <span class="trend-text green"><i class="fas fa-arrow-trend-up"></i> +12.5%</span>
                            <span class="trend-lbl">vs last month</span>
                        </div>
                        <!-- Mini SVG chart -->
                        <div class="mini-chart-wrapper">
                            <svg class="mini-svg-chart" viewBox="0 0 100 20" width="100%" height="20" preserveAspectRatio="none">
                                <path d="M 0,20 C 20,10 40,5 60,12 C 80,18 90,8 100,5" stroke="#10b981" stroke-width="1.5" fill="none"></path>
                                <path d="M 0,20 C 20,10 40,5 60,12 C 80,18 90,8 100,5 L 100,20 L 0,20 Z" fill="url(#income-grad)" opacity="0.1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="accounts-subcard expense" onclick="openDrawer('expense')">
                        <div class="subcard-header">
                            <div>
                                <span class="label">Total Expense</span>
                                <strong class="value" id="val-total-expense">₹{{ number_format($totalExpense) }}</strong>
                            </div>
                            <div class="trend-icon-circle down">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                        </div>
                        <div class="subcard-trend">
                            <span class="trend-text red"><i class="fas fa-arrow-trend-down"></i> -0%</span>
                            <span class="trend-lbl">vs last month</span>
                        </div>
                        <!-- Flat Mini SVG chart -->
                        <div class="mini-chart-wrapper">
                            <svg class="mini-svg-chart" viewBox="0 0 100 20" width="100%" height="20" preserveAspectRatio="none">
                                <path d="M 0,18 L 100,18" stroke="#ef4444" stroke-width="1.5" fill="none"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Fee & Collections Overview -->
            <div class="sum-card fe-purple" id="box-fee">
                <div class="card-top-header">
                    <div class="header-left-info">
                        <div class="header-icon-wrapper">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <h4>Fee & Collections Overview</h4>
                            <p class="subtitle">Track today's fee collection</p>
                        </div>
                    </div>
                    <div class="header-right-icons">
                        <i class="fas fa-arrows-rotate refresh-trigger" onclick="refreshBox('fee', this)"></i>
                        <i class="fas fa-ellipsis-h ellipsis-icon"></i>
                    </div>
                </div>
                <div class="card-body-content fee-grid">
                    <div class="fee-subcard" onclick="openDrawer('today_collection')">
                        <div class="subcard-header">
                            <div>
                                <span class="label">Today's Collection</span>
                                <strong class="value" id="val-today-collection">₹{{ number_format($todayFeeCollection) }}</strong>
                            </div>
                            <div class="fee-icon-wrapper">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                        
                        <!-- Centered Mini Circular Chart -->
                        <div class="subcard-chart-center">
                            <svg class="progress-ring" width="56" height="56" viewBox="0 0 36 36">
                                <circle class="progress-ring-bubble" cx="18" cy="18" r="13" />
                                <circle class="progress-ring-bg" cx="18" cy="18" r="13" fill="none" stroke="rgba(168, 85, 247, 0.12)" stroke-width="4" />
                                <circle class="progress-ring-fill" id="val-today-fee-circle-fill" cx="18" cy="18" r="13" fill="none" stroke="url(#today-ring-grad)" stroke-width="4" stroke-dasharray="82" stroke-dashoffset="{{ 82 - (82 * $todayFeeCollectionPct / 100) }}" stroke-linecap="round" filter="url(#today-ring-glow)" />
                                <text x="18" y="20.5" class="progress-ring-text text-purple" id="val-today-fee-circle-text" text-anchor="middle" font-size="7.5" font-weight="800">{{ round($todayFeeCollectionPct) }}%</text>
                            </svg>
                        </div>

                        <div class="progress-bar-wrapper">
                            <div class="progress-track bg-purple-light">
                                <div class="progress-fill bg-purple" id="val-today-fee-progress-fill" style="width: {{ $todayFeeCollectionPct }}%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="fee-subcard" onclick="openDrawer('total_collection')">
                        <div class="subcard-header">
                            <div>
                                <span class="label">Total Collection</span>
                                <strong class="value" id="val-total-collection">₹{{ number_format($totalFeeCollection) }}</strong>
                            </div>
                            <div class="fee-icon-wrapper">
                                <i class="fas fa-credit-card"></i>
                            </div>
                        </div>

                        <!-- Centered Mini Circular Chart -->
                        <div class="subcard-chart-center">
                            <svg class="progress-ring" width="56" height="56" viewBox="0 0 36 36">
                                <circle class="progress-ring-bubble" cx="18" cy="18" r="13" />
                                <circle class="progress-ring-bg" cx="18" cy="18" r="13" fill="none" stroke="rgba(236, 72, 153, 0.12)" stroke-width="4" />
                                <circle class="progress-ring-fill" id="val-fee-circle-fill" cx="18" cy="18" r="13" fill="none" stroke="url(#total-ring-grad)" stroke-width="4" stroke-dasharray="82" stroke-dashoffset="{{ 82 - (82 * $feeCollectedPct / 100) }}" stroke-linecap="round" filter="url(#total-ring-glow)" />
                                <text x="18" y="20.5" class="progress-ring-text text-pink" id="val-fee-circle-text" text-anchor="middle" font-size="7.5" font-weight="800">{{ round($feeCollectedPct) }}%</text>
                            </svg>
                        </div>

                        <div class="progress-bar-wrapper">
                            <div class="progress-track bg-pink-light">
                                <div class="progress-fill bg-pink" id="val-fee-progress-fill" style="width: {{ $feeCollectedPct }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Today's Attendance -->
            <div class="sum-card at-lavender" id="box-attendance">
                <div class="card-top-header">
                    <div class="header-left-info">
                        <div class="header-icon-wrapper">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h4>Today's Attendance</h4>
                            <p class="subtitle">Real-time attendance overview</p>
                        </div>
                    </div>
                    <div class="header-right-icons">
                        <i class="fas fa-arrows-rotate refresh-trigger" onclick="refreshBox('attendance', this)"></i>
                        <i class="fas fa-ellipsis-h ellipsis-icon"></i>
                    </div>
                </div>
                <div class="card-body-content attendance-list">
                    <div class="attendance-glass-row" onclick="openDrawer('student_attendance')">
                        <div class="row-top">
                            <span class="label">Students Attendance</span>
                            <div class="row-top-right">
                                <strong class="value" id="val-student-attendance-pct">{{ $studentAttendancePct }}%</strong>
                                <span class="badge green" id="val-student-attendance-badge">● Present ({{ $studentPresentToday }})</span>
                            </div>
                        </div>
                        <div class="progress-bar-wrapper">
                            <div class="progress-track bg-orange-light">
                                <div class="progress-fill bg-orange" id="val-student-attendance-bar" style="width: {{ $studentAttendancePct }}%;"></div>
                            </div>
                        </div>
                        <!-- Mini segmented orange line chart with nodes -->
                        <div class="attendance-trend-chart">
                            <svg viewBox="0 0 150 20" width="100%" height="20" preserveAspectRatio="none">
                                <path d="M 5,14 L 25,10 L 45,12 L 65,14 L 85,8 L 105,10 L 125,6 L 145,4 L 145,20 L 5,20 Z" fill="url(#orange-grad)" opacity="0.15"></path>
                                <path d="M 5,14 L 25,10 L 45,12 L 65,14 L 85,8 L 105,10 L 125,6 L 145,4" stroke="#8b5cf6" stroke-width="1.2" fill="none"></path>
                                <!-- Nodes -->
                                <circle cx="5" cy="14" r="2" fill="#8b5cf6"></circle>
                                <circle cx="25" cy="10" r="2" fill="#8b5cf6"></circle>
                                <circle cx="45" cy="12" r="2" fill="#8b5cf6"></circle>
                                <circle cx="65" cy="14" r="2" fill="#8b5cf6"></circle>
                                <circle cx="85" cy="8" r="2" fill="#8b5cf6"></circle>
                                <circle cx="105" cy="10" r="2" fill="#8b5cf6"></circle>
                                <circle cx="125" cy="6" r="2" fill="#8b5cf6"></circle>
                                <circle cx="145" cy="4" r="2" fill="#8b5cf6"></circle>
                            </svg>
                        </div>
                    </div>
                    <div class="attendance-glass-row" onclick="openDrawer('staff_attendance')">
                        <div class="row-top">
                            <span class="label">Staffs Attendance</span>
                            <div class="row-top-right">
                                <strong class="value" id="val-staff-attendance-pct">{{ $staffAttendancePct }}%</strong>
                                <span class="badge green" id="val-staff-attendance-badge">● Present ({{ $staffPresentToday }})</span>
                            </div>
                        </div>
                        <div class="progress-bar-wrapper">
                            <div class="progress-track bg-orange-light">
                                <div class="progress-fill bg-orange" id="val-staff-attendance-bar" style="width: {{ $staffAttendancePct }}%;"></div>
                            </div>
                        </div>
                        <!-- Mini segmented orange line chart with nodes -->
                        <div class="attendance-trend-chart">
                            <svg viewBox="0 0 150 20" width="100%" height="20" preserveAspectRatio="none">
                                <path d="M 5,15 L 25,12 L 45,14 L 65,11 L 85,13 L 105,9 L 125,7 L 145,5 L 145,20 L 5,20 Z" fill="url(#orange-grad)" opacity="0.15"></path>
                                <path d="M 5,15 L 25,12 L 45,14 L 65,11 L 85,13 L 105,9 L 125,7 L 145,5" stroke="#8b5cf6" stroke-width="1.2" fill="none"></path>
                                <!-- Nodes -->
                                <circle cx="5" cy="15" r="2" fill="#8b5cf6"></circle>
                                <circle cx="25" cy="12" r="2" fill="#8b5cf6"></circle>
                                <circle cx="45" cy="14" r="2" fill="#8b5cf6"></circle>
                                <circle cx="65" cy="11" r="2" fill="#8b5cf6"></circle>
                                <circle cx="85" cy="13" r="2" fill="#8b5cf6"></circle>
                                <circle cx="105" cy="9" r="2" fill="#8b5cf6"></circle>
                                <circle cx="125" cy="7" r="2" fill="#8b5cf6"></circle>
                                <circle cx="145" cy="5" r="2" fill="#8b5cf6"></circle>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ SECTION 1: STAFF & STUDENT ENROLLMENT OVERVIEW ══ -->
        <div class="enrollment-overview-container">
            <h2 class="sec-title" style="margin-top: 0 !important;">Staff & Student Enrollment Overview</h2>
            <div class="db-grid-3col">
                <!-- Card 1: Headcount Overview -->
                <div class="enrollment-card">
                    <div class="enrollment-card-hdr">
                        <h3 class="enrollment-card-title">
                            <i class="fas fa-users-line"></i> Headcount Overview
                        </h3>
                        <div class="enrollment-card-actions">
                            <i class="fas fa-arrows-rotate refresh-trigger" onclick="refreshBox('overview', this)"></i>
                        </div>
                    </div>
                    
                    <!-- Students Row -->
                    <div class="headcount-row">
                        <div class="headcount-info">
                            <span class="headcount-row-title" onclick="openDrawer('students')">
                                <i class="fas fa-users"></i> Students
                            </span>
                            <strong class="headcount-num" id="val-students-count">{{ $totalStudents }}</strong>
                            <span class="headcount-sublbl">Total Students</span>
                        </div>
                        <div class="headcount-chart-area">
                            <div class="headcount-canvas-wrap">
                                <canvas id="studentsPieChart"></canvas>
                            </div>
                            <div class="headcount-legend">
                                <div class="legend-row-item">
                                    <span class="legend-dot dot-blue"></span>
                                    <span class="legend-label">Male</span>
                                    <span class="legend-value">{{ $studentMaleCount }} ({{ $totalStudents > 0 ? round(($studentMaleCount / $totalStudents) * 100, 1) : 0 }}%)</span>
                                </div>
                                <div class="legend-row-item">
                                    <span class="legend-dot dot-pink"></span>
                                    <span class="legend-label">Female</span>
                                    <span class="legend-value">{{ $studentFemaleCount }} ({{ $totalStudents > 0 ? round(($studentFemaleCount / $totalStudents) * 100, 1) : 0 }}%)</span>
                                </div>
                                <div class="legend-row-item">
                                    <span class="legend-dot dot-grey"></span>
                                    <span class="legend-label">Not Mapped</span>
                                    <span class="legend-value">{{ $studentNotMappedCount }} (0%)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Staffs Row -->
                    <div class="headcount-row">
                        <div class="headcount-info">
                            <span class="headcount-row-title" onclick="openDrawer('staffs')">
                                <i class="fas fa-user-tie"></i> Staffs
                            </span>
                            <strong class="headcount-num" id="val-staffs-count">{{ $totalStaffs }}</strong>
                            <span class="headcount-sublbl">Total Staffs</span>
                        </div>
                        <div class="headcount-chart-area">
                            <div class="headcount-canvas-wrap">
                                <canvas id="staffsPieChart"></canvas>
                            </div>
                            <div class="headcount-legend">
                                <div class="legend-row-item">
                                    <span class="legend-dot dot-green"></span>
                                    <span class="legend-label">Mapped</span>
                                    <span class="legend-value">{{ $totalStaffs - $staffNotMappedCount }} ({{ $totalStaffs > 0 ? round((($totalStaffs - $staffNotMappedCount) / $totalStaffs) * 100, 1) : 0 }}%)</span>
                                </div>
                                <div class="legend-row-item">
                                    <span class="legend-dot dot-grey"></span>
                                    <span class="legend-label">Not Mapped</span>
                                    <span class="legend-value">{{ $staffNotMappedCount }} (0%)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Joining & Attrition Summary -->
                <div class="enrollment-card">
                    <div class="enrollment-card-hdr">
                        <h3 class="enrollment-card-title">
                            <i class="fas fa-arrows-down-to-people"></i> Joining & Attrition Summary
                        </h3>
                        <div class="enrollment-card-actions">
                            <i class="fas fa-arrows-rotate refresh-trigger" onclick="refreshBox('attrition', this)"></i>
                        </div>
                    </div>

                    <div class="attrition-subgrid">
                        <!-- Students Attrition Box -->
                        <div class="attrition-subcard">
                            <span class="attrition-subcard-title">
                                <i class="fas fa-users" style="color: #8b5cf6;"></i> Students
                            </span>
                            <div class="attrition-row-item">
                                <span class="attrition-label-wrap">
                                    <span class="attrition-icon-circle joined"><i class="fas fa-plus"></i></span>
                                    Newly Joined
                                </span>
                                <span class="attrition-value" id="val-student-newly-joined">{{ $studentNewlyJoined }}</span>
                            </div>
                            <div class="attrition-row-item">
                                <span class="attrition-label-wrap">
                                    <span class="attrition-icon-circle exited"><i class="fas fa-minus"></i></span>
                                    Exited
                                </span>
                                <span class="attrition-value" id="val-student-exited">{{ $studentExited }}</span>
                            </div>
                            <div class="attrition-strength-wrap">
                                <div class="attrition-strength-row">
                                    <span style="font-size: 11px; color: #94a3b8;">Strength</span>
                                    <span class="attrition-strength-trend"><i class="fas fa-arrow-up"></i> +100%</span>
                                </div>
                                <div style="display: flex; align-items: baseline; gap: 4px; margin-top: 2px;">
                                    <span class="attrition-strength-num" id="val-student-strength">{{ $studentStrength }}</span>
                                    <span style="font-size: 9.5px; color: #94a3b8;">vs last year</span>
                                </div>
                            </div>
                        </div>

                        <!-- Staffs Attrition Box -->
                        <div class="attrition-subcard">
                            <span class="attrition-subcard-title">
                                <i class="fas fa-user-tie" style="color: #10b981;"></i> Staffs
                            </span>
                            <div class="attrition-row-item">
                                <span class="attrition-label-wrap">
                                    <span class="attrition-icon-circle joined"><i class="fas fa-plus"></i></span>
                                    Newly Joined
                                </span>
                                <span class="attrition-value" id="val-staff-newly-joined">{{ $staffNewlyJoined }}</span>
                            </div>
                            <div class="attrition-row-item">
                                <span class="attrition-label-wrap">
                                    <span class="attrition-icon-circle exited"><i class="fas fa-minus"></i></span>
                                    Exited
                                </span>
                                <span class="attrition-value" id="val-staff-exited">{{ $staffExited }}</span>
                            </div>
                            <div class="attrition-strength-wrap">
                                <div class="attrition-strength-row">
                                    <span style="font-size: 11px; color: #94a3b8;">Strength</span>
                                    <span class="attrition-strength-trend"><i class="fas fa-arrow-up"></i> +100%</span>
                                </div>
                                <div style="display: flex; align-items: baseline; gap: 4px; margin-top: 2px;">
                                    <span class="attrition-strength-num" id="val-staff-strength">{{ $staffStrength }}</span>
                                    <span style="font-size: 9.5px; color: #94a3b8;">vs last year</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alert Banner -->
                    <div class="enrollment-alert-banner">
                        <div class="enrollment-alert-icon-wrap">
                            <i class="fas fa-sparkles"></i>
                        </div>
                        <div class="enrollment-alert-content">
                            <span class="enrollment-alert-title">Great going! Your enrollment is growing 🚀</span>
                            <span class="enrollment-alert-sub">Keep up the excellent work.</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Admission Count Summary -->
                <div class="enrollment-card">
                    <div class="enrollment-card-hdr">
                        <h3 class="enrollment-card-title">
                            <i class="fas fa-school"></i> Admission Count Summary
                        </h3>
                        <div class="admission-header-actions">
                            <div class="admission-pill-toggle">
                                <button class="admission-toggle-btn active" id="admOverallBtn" onclick="toggleAdmissionTab('overall')">Overall</button>
                                <button class="admission-toggle-btn" id="admTodayBtn" onclick="toggleAdmissionTab('today')">Today's</button>
                            </div>
                            <button class="admission-filter-btn" title="Filter" onclick="openDrawer('admissions')">
                                <i class="fas fa-filter"></i>
                            </button>
                            <div class="enrollment-card-actions" style="margin-left: 2px;">
                                <i class="fas fa-arrows-rotate refresh-trigger" onclick="refreshBox('admissions', this)"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Canvas Area for curved Line Chart -->
                    <div class="admission-chart-wrap">
                        <canvas id="admissionCountLineChart"></canvas>
                    </div>

                    <!-- Hidden original metrics to ensure no script crashes -->
                    <div style="display: none;">
                        <span id="valEnquiry">{{ $admissionEnquiry }}</span>
                        <span id="valApplication">{{ $admissionApplication }}</span>
                        <span id="valPayment">{{ $admissionPayment }}</span>
                        <span id="valEvaluation">{{ $admissionEvaluation }}</span>
                        <span id="valAdmission">{{ $admissionCount }}</span>
                        <input type="checkbox" id="showAllYearsCheck" checked>
                    </div>
                </div>
            </div>

            <!-- Quick Insights Row -->
            <div class="insights-header">
                <div class="insights-header-icon-wrap">
                    <i class="fas fa-wand-magic-sparkles"></i>
                </div>
                <div class="insights-header-title">
                    <h3>Quick Insights</h3>
                    <span>Key metrics at a glance</span>
                </div>
            </div>

            <div class="insights-grid">
                <div class="insight-card" onclick="openDrawer('students')">
                    <div class="insight-card-top">
                        <div class="insight-card-icon-wrap blue">
                            <i class="fas fa-users"></i>
                        </div>
                        <span class="insight-card-trend up">
                            <i class="fas fa-arrow-up-long"></i> +100%
                        </span>
                    </div>
                    <div class="insight-card-info">
                        <span class="insight-card-label">Total Students</span>
                        <strong class="insight-card-value">{{ $totalStudents }}</strong>
                        <span class="insight-card-trend muted" style="margin-top: 4px;">from last year</span>
                    </div>
                </div>

                <div class="insight-card" onclick="openDrawer('staffs')">
                    <div class="insight-card-top">
                        <div class="insight-card-icon-wrap green">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <span class="insight-card-trend up">
                            <i class="fas fa-arrow-up-long"></i> +100%
                        </span>
                    </div>
                    <div class="insight-card-info">
                        <span class="insight-card-label">Total Staffs</span>
                        <strong class="insight-card-value">{{ $totalStaffs }}</strong>
                        <span class="insight-card-trend muted" style="margin-top: 4px;">from last year</span>
                    </div>
                </div>

                <div class="insight-card" onclick="openDrawer('admissions')">
                    <div class="insight-card-top">
                        <div class="insight-card-icon-wrap pink">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                    <div class="insight-card-info" style="margin-top: 6px;">
                        <span class="insight-card-label">New Admissions</span>
                        <strong class="insight-card-value">{{ $studentNewlyJoined }}</strong>
                        <span class="insight-card-trend muted" style="margin-top: 4px;">This academic year</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ SECTION 2: FINANCIAL MANAGEMENT OVERVIEW ══ -->
        <h2 class="sec-title">Financial Management Overview</h2>
        <div class="db-grid-2col">
            <!-- Redesigned Income and Expense Chart Card -->
            <div class="card financial-chart-card">
                <!-- Top Row: Title + Summary Widgets -->
                <div class="financial-card-top">
                    <!-- Title & Subtitle Left -->
                    <div class="financial-title-area">
                        <div class="financial-icon-wrapper">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="financial-title-text">
                            <h3>Income & Expense Overview <i class="fas fa-arrows-rotate refresh-trigger" onclick="spinIcon(this)"></i></h3>
                            <p>Track monthly income and expenses</p>
                        </div>
                    </div>
                    
                    <!-- Widgets Right -->
                    <div class="financial-widgets-area">
                        <!-- Total Income Widget -->
                        <div class="financial-widget widget-income" onclick="openDrawer('income')">
                            <div class="widget-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="widget-details">
                                <span class="widget-label">Total Income <i class="fas fa-circle-info" style="cursor:pointer;" data-info="Total fee income received in the current period."></i></span>
                                <strong class="widget-value" id="financial-total-income">₹ {{ number_format($totalIncome) }}</strong>
                            </div>
                        </div>
                        
                        <!-- Total Expense Widget -->
                        <div class="financial-widget widget-expense" onclick="openDrawer('expense')">
                            <div class="widget-icon">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div class="widget-details">
                                <span class="widget-label">Total Expense <i class="fas fa-circle-info" style="cursor:pointer;" data-info="Total expenses/expenditures incurred in the current period."></i></span>
                                <strong class="widget-value" id="financial-total-expense">₹ {{ number_format($totalExpense) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Middle Row: Amount Label + Dropdown Filter -->
                <div class="financial-mid-row">
                    <h4 class="amount-title">Amount (₹)</h4>
                    <div class="financial-filter">
                        <i class="far fa-calendar-alt calendar-icon"></i>
                        <select class="financial-select-filter">
                            <option>This Year</option>
                            <option>This Month</option>
                            <option>Last 6 Months</option>
                        </select>
                    </div>
                </div>
                
                <!-- Chart Wrapper -->
                <div class="income-expense-chart-container" style="position: relative;">
                    <canvas id="incomeExpenseChart"></canvas>
                    
                    <!-- Bottom Right Floating Legend -->
                    <div class="floating-legend-overlay">
                        <span class="legend-item legend-inc"><span class="dot"></span> Income</span>
                        <span class="legend-item legend-exp"><span class="dot"></span> Expense</span>
                    </div>
                </div>
                
                <!-- Bottom Legend -->
                <div class="financial-bottom-legend">
                    <span class="legend-dot-item"><span class="color-dot dot-orange"></span> Income</span>
                    <span class="legend-dot-item"><span class="color-dot dot-grey"></span> Expense</span>
                </div>
            </div>

            <!-- Fee Management Widget Card -->
            <div class="card">
                <div class="card-header-row">
                    <h3>Fee Management <i class="fas fa-circle-chevron-right" style="color:#f59e0b;cursor:pointer;" onclick="openDrawer('fee_pending')"></i></h3>
                </div>
                <div class="fee-management-subcard" style="cursor:pointer;" onclick="openDrawer('today_collection')">
                    <div>
                        <span>Today's Fee Collection: <strong>₹ {{ number_format($todayFeeCollection) }}</strong></span>
                        <span style="font-size:8.5px;color:var(--t3);">Basis Fee Entry Date</span>
                    </div>
                    <i class="fas fa-circle-info" style="cursor:pointer;" onclick="event.stopPropagation();" data-info="Total fees collected and received today across all payment modes."></i>
                </div>
                <div class="fee-management-body">
                    <!-- Till Date / Annual Switch -->
                    <div style="display:flex;justify-content:center;margin-bottom:8px;">
                        <div class="toggle-group">
                            <button class="toggle-group-btn active" id="feeTillDateBtn" onclick="toggleFeeTab('tilldate')">TILL DATE</button>
                            <button class="toggle-group-btn" id="feeAnnualBtn" onclick="toggleFeeTab('annual')">ANNUAL</button>
                        </div>
                    </div>

                    <!-- Pie Chart Collected vs Due -->
                    <div style="display:flex; align-items:center; gap:20px; justify-content:space-between; text-align:left; margin-bottom:12px;">
                        <div class="chart-container" style="width: 100px; height: 100px; position: relative; flex-shrink: 0;">
                            <canvas id="feeCollectionPieChart"></canvas>
                        </div>
                        <!-- Progress Details -->
                        <div class="fee-list-section" style="flex:1; margin-top:0; border:none; padding:0; display:flex; flex-direction:column; gap:8px;">
                            <div class="fee-action-row" style="padding:0;">
                                <span style="display:flex;align-items:center;gap:4px;">
                                    <span class="legend-dot" style="background:#10b981;"></span>
                                    Collected: <strong>₹ {{ number_format($feeCollectedAmount) }} ({{ $feeCollectedPct }}%)</strong>
                                    <i class="fas fa-circle-info" id="collectedFeeInfo" style="font-size:9.5px;cursor:pointer;" data-info="Total fees collected whose due date is on or before today."></i>
                                </span>
                            </div>
                            <div class="fee-action-row" style="padding:0;">
                                <span style="display:flex;align-items:center;gap:4px;">
                                    <span class="legend-dot" style="background:#ef4444;"></span>
                                    Due: <strong>₹ {{ number_format($feeDueAmount) }} ({{ $feeDuePct }}%)</strong>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Fee Pending Details -->
                    <div class="fee-list-section" style="border-top:1px solid var(--border);">
                        <div class="fee-list-title-row">
                            <span>Fee pending (till date)</span>
                            <i class="fas fa-circle-info" style="cursor:pointer;" data-info="Total outstanding dues from students whose payment due dates have already passed."></i>
                        </div>
                        <div class="fee-action-row">
                            <span style="display:flex;align-items:center;gap:6px;">
                                Total No. of Students : <strong>{{ $feePendingStudentsCount }}</strong>
                                <i class="fas fa-arrows-rotate refresh-trigger" onclick="spinIcon(this)" style="font-size:9px;"></i>
                                <i class="fas fa-circle-play" style="font-size:10px;cursor:pointer;" onclick="openDrawer('fee_pending')"></i>
                            </span>
                            <button class="btn-orange-reminder" onclick="sendReminder()"><i class="fas fa-bell"></i> Send Reminder</button>
                        </div>
                        <div class="fee-action-row" style="margin-top:4px;">
                            <span>Due Amount : <strong class="due-amount-display">₹ {{ number_format($feePendingDueAmount) }} ({{ $feeDuePct }}%)</strong> <i class="fas fa-arrows-rotate refresh-trigger" onclick="spinIcon(this)" style="font-size:9px;color:var(--t2);"></i></span>
                        </div>
                        <button class="btn-class-fee-report" onclick="openDrawer('class_fee_report')" style="margin-top:8px;">CLASS-WISE FEE REPORT</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ SECTION 3: ADMINISTRATIVE OPERATIONS OVERVIEW ══ -->
        <h2 class="sec-title">Administrative Operations Overview</h2>
        <div class="db-grid-3col">
            <!-- Recent Updates Tabs Card -->
            <div class="card">
                <div class="card-header-row">
                    <h3>Recent Updates <i class="fas fa-arrows-rotate refresh-trigger" onclick="spinIcon(this)"></i></h3>
                </div>
                <div class="recent-updates-tabs">
                    <button class="active" id="tabNotice" onclick="switchUpdateTab('notice')">NOTICE</button>
                    <button id="tabVisitor" onclick="switchUpdateTab('visitor')">VISITORS APPROVAL</button>
                    <button id="tabLeave" onclick="switchUpdateTab('leave')">LEAVE APPROVAL</button>
                    <button id="tabDiary" onclick="switchUpdateTab('diary')">DIGITAL DIARY</button>
                </div>
                <div class="empty-state-container" id="updatesContent">
                    <i class="fas fa-box-open empty-state-icon"></i>
                    <h4>Loading...</h4>
                </div>
            </div>

            <!-- Attendance Detail Overview Card -->
            <div class="card">
                <div class="attendance-card-header">
                    <div class="attendance-title-area">
                        <div class="attendance-icon-wrapper">
                            <i class="fas fa-users-viewfinder"></i>
                        </div>
                        <div class="attendance-title-text">
                            <h3>Attendance</h3>
                            <p>Track and manage attendance overview</p>
                        </div>
                    </div>
                    <button class="btn-attendance-approval">ATTENDANCE APPROVAL</button>
                </div>
                <div class="attendance-body">
                    <!-- Student Attendance Panel -->
                    <div class="attendance-subpanel-custom">
                        <div class="attendance-subpanel-title-row">
                            <span class="attendance-subpanel-title" onclick="openDrawer('student_attendance')" style="cursor:pointer;">
                                Student Attendance Overview <i class="fas fa-circle-info" style="font-size:10px; color:var(--t3);" data-info="Daily student attendance logs & statistics."></i>
                            </span>
                            <a href="javascript:void(0)" class="detailed-view-link" onclick="openDrawer('student_attendance')">Detailed View →</a>
                        </div>
                        <div class="chart-relative-container">
                            <div class="chart-donut-wrap">
                                <canvas id="studentAttendancePieChart"></canvas>
                                <div class="chart-center-overlay">
                                    <span class="pct-value" id="studentPctText">{{ $studentAttendancePct }}%</span>
                                    <span class="pct-label">Att.</span>
                                </div>
                            </div>
                            <div class="vertical-legend-list">
                                <div class="vertical-legend-item">
                                    <span class="legend-label-group">
                                        <span class="legend-color-dot" style="background:#10b981;"></span>
                                        PRESENT
                                    </span>
                                    <strong>{{ $studentPresentToday }} ({{ $totalStudents > 0 ? round(($studentPresentToday / $totalStudents) * 100, 1) : 0 }}%)</strong>
                                </div>
                                <div class="vertical-legend-item">
                                    <span class="legend-label-group">
                                        <span class="legend-color-dot" style="background:#ef4444;"></span>
                                        ABSENT
                                    </span>
                                    <strong>{{ $studentAbsentToday }} ({{ $totalStudents > 0 ? round(($studentAbsentToday / $totalStudents) * 100, 1) : 0 }}%)</strong>
                                </div>
                                <div class="vertical-legend-item">
                                    <span class="legend-label-group">
                                        <span class="legend-color-dot" style="background:#f59e0b;"></span>
                                        HALFDAY
                                    </span>
                                    <strong>{{ $studentHalfDayToday }} ({{ $totalStudents > 0 ? round(($studentHalfDayToday / $totalStudents) * 100, 1) : 0 }}%)</strong>
                                </div>
                                <div class="vertical-legend-item">
                                    <span class="legend-label-group">
                                        <span class="legend-color-dot" style="background:#ea580c;"></span>
                                        LEAVE
                                    </span>
                                    <strong>{{ $studentLeaveToday }} ({{ $totalStudents > 0 ? round(($studentLeaveToday / $totalStudents) * 100, 1) : 0 }}%)</strong>
                                </div>
                                <div class="vertical-legend-item">
                                    <span class="legend-label-group">
                                        <span class="legend-color-dot" style="background:#ec4899;"></span>
                                        CUSTOM LEAVES
                                    </span>
                                    <strong>{{ $studentCustomToday }} ({{ $totalStudents > 0 ? round(($studentCustomToday / $totalStudents) * 100, 1) : 0 }}%)</strong>
                                </div>
                                <div class="vertical-legend-item">
                                    <span class="legend-label-group">
                                        <span class="legend-color-dot" style="background:#9ca3af;"></span>
                                        NOT MARKED
                                    </span>
                                    <strong>{{ $studentNotMarkedToday }} ({{ $studentNotMarkedPct }}%)</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Staff Attendance Panel -->
                    <div class="attendance-subpanel-custom">
                        <div class="attendance-subpanel-title-row">
                            <span class="attendance-subpanel-title" onclick="openDrawer('staff_attendance')" style="cursor:pointer;">
                                Staff Attendance Overview <i class="fas fa-circle-info" style="font-size:10px; color:var(--t3);" data-info="Daily staff/teacher attendance logs & statistics."></i>
                            </span>
                            <a href="javascript:void(0)" class="detailed-view-link" onclick="openDrawer('staff_attendance')">Detailed View →</a>
                        </div>
                        <div class="chart-relative-container">
                            <div class="chart-donut-wrap">
                                <canvas id="staffAttendancePieChart"></canvas>
                                <div class="chart-center-overlay">
                                    <span class="pct-value" id="staffPctText">{{ $staffAttendancePct }}%</span>
                                    <span class="pct-label">Att.</span>
                                </div>
                            </div>
                            <div class="vertical-legend-list">
                                <div class="vertical-legend-item">
                                    <span class="legend-label-group">
                                        <span class="legend-color-dot" style="background:#10b981;"></span>
                                        PRESENT
                                    </span>
                                    <strong>{{ $staffPresentToday }} ({{ $totalStaffs > 0 ? round(($staffPresentToday / $totalStaffs) * 100, 1) : 0 }}%)</strong>
                                </div>
                                <div class="vertical-legend-item">
                                    <span class="legend-label-group">
                                        <span class="legend-color-dot" style="background:#ef4444;"></span>
                                        ABSENT
                                    </span>
                                    <strong>{{ $staffAbsentToday }} ({{ $totalStaffs > 0 ? round(($staffAbsentToday / $totalStaffs) * 100, 1) : 0 }}%)</strong>
                                </div>
                                <div class="vertical-legend-item">
                                    <span class="legend-label-group">
                                        <span class="legend-color-dot" style="background:#f59e0b;"></span>
                                        HALFDAY
                                    </span>
                                    <strong>{{ $staffHalfdayToday }} ({{ $totalStaffs > 0 ? round(($staffHalfdayToday / $totalStaffs) * 100, 1) : 0 }}%)</strong>
                                </div>
                                <div class="vertical-legend-item">
                                    <span class="legend-label-group">
                                        <span class="legend-color-dot" style="background:#ea580c;"></span>
                                        LEAVE
                                    </span>
                                    <strong>{{ $staffLeaveToday }} ({{ $totalStaffs > 0 ? round(($staffLeaveToday / $totalStaffs) * 100, 1) : 0 }}%)</strong>
                                </div>
                                <div class="vertical-legend-item">
                                    <span class="legend-label-group">
                                        <span class="legend-color-dot" style="background:#ec4899;"></span>
                                        CUSTOM LEAVES
                                    </span>
                                    <strong>{{ $staffCustomToday }} ({{ $totalStaffs > 0 ? round(($staffCustomToday / $totalStaffs) * 100, 1) : 0 }}%)</strong>
                                </div>
                                <div class="vertical-legend-item">
                                    <span class="legend-label-group">
                                        <span class="legend-color-dot" style="background:#9ca3af;"></span>
                                        NOT MARKED
                                    </span>
                                    <strong>{{ $staffNotMarkedToday }} ({{ $staffNotMarkedPct }}%)</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer KPIs in Attendance Card -->
                <div class="attendance-footer-kpis">
                    <div class="kpi-stat-item" onclick="openDrawer('students')" style="cursor:pointer;">
                        <div class="kpi-stat-icon students">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="kpi-stat-details">
                            <span class="kpi-stat-label">Total Students</span>
                            <span class="kpi-stat-value">{{ $totalStudents }}</span>
                        </div>
                    </div>
                    <div class="kpi-stat-item" onclick="openDrawer('staffs')" style="cursor:pointer;">
                        <div class="kpi-stat-icon staff">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="kpi-stat-details">
                            <span class="kpi-stat-label">Total Staff</span>
                            <span class="kpi-stat-value">{{ $totalStaffs }}</span>
                        </div>
                    </div>
                    <div class="kpi-stat-item" onclick="openDrawer('student_attendance')" style="cursor:pointer;">
                        <div class="kpi-stat-icon today">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="kpi-stat-details">
                            <span class="kpi-stat-label">Today's Att.</span>
                            <span class="kpi-stat-value">{{ $studentPresentToday }} ({{ $studentAttendancePct }}%)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Calendar Widget Card -->
            <div class="card">
                <div class="calendar-card-header">
                    <div class="calendar-title-area">
                        <div class="calendar-icon-wrapper">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="calendar-title-text">
                            <h3>Event Calendar</h3>
                            <p>Stay updated with important events</p>
                        </div>
                    </div>
                    <div class="calendar-header-actions">
                        <i class="fas fa-arrows-rotate refresh-trigger" onclick="spinIcon(this)"></i>
                    </div>
                </div>
                <!-- Switches -->
                <div class="calendar-toggles-custom">
                    <div class="calendar-toggle-row">
                        <span>Students' Birthdays</span>
                        <label class="switch-wrapper">
                            <input type="checkbox" id="studentBirthdaySwitch" onchange="toggleBirthdays('student')" checked>
                            <span class="switch-slider-green"></span>
                        </label>
                    </div>
                    <div class="calendar-toggle-row">
                        <span>Teachers' Birthdays</span>
                        <label class="switch-wrapper">
                            <input type="checkbox" id="teacherBirthdaySwitch" onchange="toggleBirthdays('teacher')" checked>
                            <span class="switch-slider-green"></span>
                        </label>
                    </div>
                </div>

                <!-- Calendar month/year picker -->
                <div class="calendar-month-selector-custom">
                    <select id="calendarMonth" onchange="changeCalendarMonthYear()">
                        <option value="1" {{ $month == 1 ? 'selected' : '' }}>January</option>
                        <option value="2" {{ $month == 2 ? 'selected' : '' }}>February</option>
                        <option value="3" {{ $month == 3 ? 'selected' : '' }}>March</option>
                        <option value="4" {{ $month == 4 ? 'selected' : '' }}>April</option>
                        <option value="5" {{ $month == 5 ? 'selected' : '' }}>May</option>
                        <option value="6" {{ $month == 6 ? 'selected' : '' }}>June</option>
                        <option value="7" {{ $month == 7 ? 'selected' : '' }}>July</option>
                        <option value="8" {{ $month == 8 ? 'selected' : '' }}>August</option>
                        <option value="9" {{ $month == 9 ? 'selected' : '' }}>September</option>
                        <option value="10" {{ $month == 10 ? 'selected' : '' }}>October</option>
                        <option value="11" {{ $month == 11 ? 'selected' : '' }}>November</option>
                        <option value="12" {{ $month == 12 ? 'selected' : '' }}>December</option>
                    </select>
                    <select id="calendarYear" onchange="changeCalendarMonthYear()">
                        <option value="2025" {{ $year == 2025 ? 'selected' : '' }}>2025</option>
                        <option value="2026" {{ $year == 2026 ? 'selected' : '' }}>2026</option>
                        <option value="2027" {{ $year == 2027 ? 'selected' : '' }}>2027</option>
                        <option value="2028" {{ $year == 2028 ? 'selected' : '' }}>2028</option>
                    </select>
                </div>

                <!-- Calendar month grid -->
                <div class="calendar-grid">
                    <!-- Will be populated dynamically via JS -->
                </div>

                <!-- Upcoming Birthdays section -->
                <div class="calendar-events-section">
                    <div class="calendar-events-title-row">
                        <h4>Upcoming Birthdays & Events</h4>
                        <a href="javascript:void(0)" class="btn-view-all-events" onclick="openDrawer('calendar_month_events')">View All</a>
                    </div>
                    <div class="calendar-events-list" id="calendarEventsList">
                        <!-- Populated dynamically via JS -->
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <span>© 2026 SchoolCloud ERP. All rights reserved.</span>
            <span>Version 2.0.0 &nbsp;|&nbsp; 🔒 Secure & Trusted</span>
        </div>
    </div>
</div>

<script>
// ── DATA FROM PHP (safe load) ─────────────────────────────────────────────────
const CSRF = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';
let MONTHS_LABELS, INCOME_DATA, EXPENSE_DATA, NOTICES_DATA, DIARIES_DATA, LEAVES_DATA;
try { MONTHS_LABELS = @json($months); } catch(e) { MONTHS_LABELS = []; }
try { INCOME_DATA = @json($incomeData); } catch(e) { INCOME_DATA = []; }
try { EXPENSE_DATA = @json($expenseData); } catch(e) { EXPENSE_DATA = []; }
try { NOTICES_DATA = @json($noticesData); } catch(e) { NOTICES_DATA = []; }
try { DIARIES_DATA = @json($diariesData); } catch(e) { DIARIES_DATA = []; }
try { LEAVES_DATA = @json($leavesData); } catch(e) { LEAVES_DATA = []; }
let feeChart;

// ── SPIN ICON MICRO-ANIMATION & COOLDOWN REFRESH ───────────────────────────────
function spinIcon(icon) {
    icon.classList.add('fa-spin');
    setTimeout(() => {
        icon.classList.remove('fa-spin');
    }, 1000);
    triggerRefresh(icon);
}

function triggerRefresh(icon) {
    const now = Date.now();
    const cooldown = 15000; // 15 seconds cooldown
    const lastRefresh = localStorage.getItem('last_dashboard_refresh');
    
    if (lastRefresh && (now - lastRefresh < cooldown)) {
        const remaining = Math.ceil((cooldown - (now - lastRefresh)) / 1000);
        const msg = `Please wait ${remaining} seconds before refreshing again.`;
        showToast(msg);
        
        // HTML5 System Notification
        if (typeof Notification !== 'undefined') {
            if (Notification.permission === 'granted') {
                new Notification('Dashboard Refresh Cooldown', {
                    body: msg
                });
            } else if (Notification.permission !== 'denied') {
                Notification.requestPermission().then(perm => {
                    if (perm === 'granted') {
                        new Notification('Dashboard Refresh Cooldown', { body: msg });
                    }
                });
            }
        }
        return;
    }
    
    // Set last refresh time
    localStorage.setItem('last_dashboard_refresh', now);
    
    // Show refreshing toast
    showToast("Refreshing dashboard...");
    
    if (typeof Notification !== 'undefined' && Notification.permission === 'granted') {
        new Notification('Dashboard Refreshing', {
            body: 'The dashboard page is refreshing now.'
        });
    }
    
    // Reload the page
    setTimeout(() => {
        window.location.reload();
    }, 500);
}

function refreshBox(box, icon) {
    if (!icon) return;

    // Spin animation
    icon.classList.add('fa-spin');
    
    // Perform AJAX request
    fetch(`/school/dashboard/refresh-box?box=${box}`)
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                const data = res.data;
                if (box === 'overview') {
                    document.getElementById('val-students-count').textContent = data.totalStudents;
                    document.getElementById('val-staffs-count').textContent = data.totalStaffs;
                    showToast('Overview metrics refreshed.');
                } else if (box === 'accounts') {
                    document.getElementById('val-total-income').textContent = '₹' + data.totalIncome;
                    document.getElementById('val-total-expense').textContent = '₹' + data.totalExpense;
                    showToast('Accounts metrics refreshed.');
                } else if (box === 'fee') {
                    document.getElementById('val-today-collection').textContent = '₹' + data.todayFeeCollection;
                    document.getElementById('val-total-collection').textContent = '₹' + data.totalFeeCollection;
                    
                    // Update Total Collection progress fill and progress ring
                    if (document.getElementById('val-fee-progress-fill')) {
                        document.getElementById('val-fee-progress-fill').style.width = data.feeCollectedPct + '%';
                    }
                    const pct = parseFloat(data.feeCollectedPct) || 0;
                    const offset = 82 - (82 * pct / 100);
                    const fillEl = document.getElementById('val-fee-circle-fill');
                    const textEl = document.getElementById('val-fee-circle-text');
                    if (fillEl) fillEl.setAttribute('stroke-dashoffset', offset);
                    if (textEl) textEl.textContent = Math.round(pct) + '%';
                    
                    // Update Today's Collection progress fill and progress ring
                    if (document.getElementById('val-today-fee-progress-fill')) {
                        document.getElementById('val-today-fee-progress-fill').style.width = data.todayFeeCollectionPct + '%';
                    }
                    const todayPct = parseFloat(data.todayFeeCollectionPct) || 0;
                    const todayOffset = 82 - (82 * todayPct / 100);
                    const todayFillEl = document.getElementById('val-today-fee-circle-fill');
                    const todayTextEl = document.getElementById('val-today-fee-circle-text');
                    if (todayFillEl) todayFillEl.setAttribute('stroke-dashoffset', todayOffset);
                    if (todayTextEl) todayTextEl.textContent = Math.round(todayPct) + '%';
                    
                    showToast('Fee collection metrics refreshed.');
                } else if (box === 'attendance') {
                    document.getElementById('val-student-attendance-pct').textContent = data.studentAttendancePct + '%';
                    document.getElementById('val-student-attendance-bar').style.width = data.studentAttendancePct + '%';
                    if (document.getElementById('val-student-attendance-badge')) {
                        document.getElementById('val-student-attendance-badge').textContent = `● Present (${data.studentPresentToday})`;
                    }
                    
                    document.getElementById('val-staff-attendance-pct').textContent = data.staffAttendancePct + '%';
                    document.getElementById('val-staff-attendance-bar').style.width = data.staffAttendancePct + '%';
                    if (document.getElementById('val-staff-attendance-badge')) {
                        document.getElementById('val-staff-attendance-badge').textContent = `● Present (${data.staffPresentToday})`;
                    }
                    showToast('Attendance metrics refreshed.');
                } else if (box === 'attrition') {
                    if (document.getElementById('val-student-newly-joined')) document.getElementById('val-student-newly-joined').textContent = data.studentNewlyJoined;
                    if (document.getElementById('val-student-exited')) document.getElementById('val-student-exited').textContent = data.studentExited;
                    if (document.getElementById('val-student-strength')) document.getElementById('val-student-strength').textContent = data.studentStrength;
                    if (document.getElementById('val-staff-newly-joined')) document.getElementById('val-staff-newly-joined').textContent = data.staffNewlyJoined;
                    if (document.getElementById('val-staff-exited')) document.getElementById('val-staff-exited').textContent = data.staffExited;
                    if (document.getElementById('val-staff-strength')) document.getElementById('val-staff-strength').textContent = data.staffStrength;
                    showToast('Joining & attrition metrics refreshed.');
                } else if (box === 'admissions') {
                    ADM_ENQUIRY = data.enquiry;
                    ADM_APPLICATION = data.application;
                    ADM_PAYMENT = data.payment;
                    ADM_EVALUATION = data.evaluation;
                    ADM_COUNT = data.admission;

                    const currentTab = document.getElementById('admOverallBtn').classList.contains('active') ? 'overall' : 'today';
                    toggleAdmissionTab(currentTab);
                    showToast('Admission count metrics refreshed.');
                }
            } else {
                showToast('Failed to refresh metrics.');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Error refreshing metrics.');
        })
        .finally(() => {
            setTimeout(() => {
                icon.classList.remove('fa-spin');
            }, 600);
        });
}

// ── ADMISSION COUNT TOGGLE ────────────────────────────────────────────────────
function toggleAdmissionTab(tab) {
    const overallBtn = document.getElementById('admOverallBtn');
    const todayBtn = document.getElementById('admTodayBtn');
    if (!overallBtn || !todayBtn) return;
    
    overallBtn.classList.remove('active');
    todayBtn.classList.remove('active');
    
    const enquiry = document.getElementById('valEnquiry');
    const app = document.getElementById('valApplication');
    const pay = document.getElementById('valPayment');
    const evalVal = document.getElementById('valEvaluation');
    const adm = document.getElementById('valAdmission');

    let chartData = [];

    if (tab === 'overall') {
        overallBtn.classList.add('active');
        if (enquiry) enquiry.textContent = ADM_ENQUIRY;
        if (app) app.textContent = ADM_APPLICATION;
        if (pay) pay.textContent = ADM_PAYMENT;
        if (evalVal) evalVal.textContent = ADM_EVALUATION;
        if (adm) adm.textContent = ADM_COUNT;
        
        chartData = [ADM_ENQUIRY, ADM_APPLICATION, ADM_PAYMENT, ADM_EVALUATION, ADM_COUNT];
    } else {
        todayBtn.classList.add('active');
        if (enquiry) enquiry.textContent = "0";
        if (app) app.textContent = "0";
        if (pay) pay.textContent = "0";
        if (evalVal) evalVal.textContent = "0";
        if (adm) adm.textContent = "0";
        
        chartData = [0, 0, 0, 0, 0];
    }

    if (admissionChartInst) {
        admissionChartInst.data.datasets[0].data = chartData;
        admissionChartInst.update();
    }
}

// ── FEE TAB TOGGLE (TILL DATE / ANNUAL) ────────────────────────────────────────
function toggleFeeTab(tab) {
    const tillBtn = document.getElementById('feeTillDateBtn');
    const annualBtn = document.getElementById('feeAnnualBtn');
    tillBtn.classList.remove('active');
    annualBtn.classList.remove('active');

    const textElements = document.querySelectorAll('.fee-list-section .fee-action-row strong');
    const collectedInfo = document.getElementById('collectedFeeInfo');

    if (tab === 'tilldate') {
        tillBtn.classList.add('active');
        if (feeChart) {
            feeChart.data.datasets[0].data = [{{ $feeCollectedAmount }}, {{ $feeDueAmount }}];
            feeChart.update();
        }
        textElements[0].innerHTML = "₹ {{ number_format($feeCollectedAmount) }} ({{ $feeCollectedPct }}%)";
        textElements[1].innerHTML = "₹ {{ number_format($feeDueAmount) }} ({{ $feeDuePct }}%)";
        if (collectedInfo) {
            collectedInfo.setAttribute('data-info', 'Total fees collected whose due date is on or before today.');
        }
    } else {
        annualBtn.classList.add('active');
        if (feeChart) {
            feeChart.data.datasets[0].data = [{{ $annualCollectedAmount }}, {{ $annualDueAmount }}];
            feeChart.update();
        }
        textElements[0].innerHTML = "₹ {{ number_format($annualCollectedAmount) }} ({{ $annualCollectedPct }}%)";
        textElements[1].innerHTML = "₹ {{ number_format($annualDueAmount) }} ({{ $annualDuePct }}%)";
        if (collectedInfo) {
            collectedInfo.setAttribute('data-info', 'Total fees collected in the current academic year so far.');
        }
    }
}

// ── RECENT UPDATES TAB SWITCH ─────────────────────────────────────────────────
function escapeHtml(text) {
    if (!text) return '';
    return text
        .toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function switchUpdateTab(tab) {
    try {
        const tabs = ['Notice', 'Visitor', 'Leave', 'Diary'];
        tabs.forEach(t => {
            const el = document.getElementById('tab' + t);
            if (el) el.classList.remove('active');
        });
        
        const activeTabEl = document.getElementById('tab' + ucfirst(tab));
        if (activeTabEl) activeTabEl.classList.add('active');
        
        const container = document.getElementById('updatesContent');
        if (!container) return;
        
        if (tab === 'notice') {
            if (typeof NOTICES_DATA === 'undefined' || !NOTICES_DATA || NOTICES_DATA.length === 0) {
                container.innerHTML = `
                    <div style="text-align:center; padding:40px; color:var(--t3);">
                        <i class="fas fa-box-open empty-state-icon" style="font-size:32px; margin-bottom:8px; display:block; color:var(--border);"></i>
                        <h4>No new updates</h4>
                        <p>Notices will appear here once you receive any updates</p>
                    </div>
                `;
            } else {
                let html = '<div class="updates-list">';
                NOTICES_DATA.forEach(n => {
                    html += `
                        <div class="update-item">
                            <div class="update-item-header">
                                <span class="update-item-title">${escapeHtml(n.title)}</span>
                                <span class="update-item-date">${escapeHtml(n.date)}</span>
                            </div>
                            <p class="update-item-body">${escapeHtml(n.content)}</p>
                            <div class="update-item-footer">
                                <span class="badge badge-audience"><i class="fas fa-users"></i> ${escapeHtml(n.audience)}</span>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
            }
        } else if (tab === 'visitor') {
            container.innerHTML = `
                <div style="text-align:center; padding:40px; color:var(--t3);">
                    <i class="fas fa-id-badge empty-state-icon" style="font-size:32px; margin-bottom:8px; display:block; color:#6ee7b7;"></i>
                    <h4>No visitors approval today</h4>
                    <p>Visitor logs needing admin signature will appear here</p>
                </div>
            `;
        } else if (tab === 'leave') {
            if (typeof LEAVES_DATA === 'undefined' || !LEAVES_DATA || LEAVES_DATA.length === 0) {
                container.innerHTML = `
                    <div style="text-align:center; padding:40px; color:var(--t3);">
                        <i class="fas fa-file-signature empty-state-icon" style="font-size:32px; margin-bottom:8px; display:block; color:#fca5a5;"></i>
                        <h4>No leaves pending approval</h4>
                        <p>Leave requests will show here once submitted</p>
                    </div>
                `;
            } else {
                let html = '<div class="updates-list">';
                LEAVES_DATA.forEach(l => {
                    let badgeClass = 'badge-pending';
                    if (l.status === 'approved') badgeClass = 'badge-approved';
                    if (l.status === 'rejected') badgeClass = 'badge-rejected';
                    html += `
                        <div class="update-item" style="border-left-color:#ef4444;">
                            <div class="update-item-header">
                                <span class="update-item-title">${escapeHtml(l.user_name)} (${escapeHtml(l.applicant_type)})</span>
                                <span class="update-item-date">${escapeHtml(l.start_date)} - ${escapeHtml(l.end_date)}</span>
                            </div>
                            <p class="update-item-body"><strong>Reason:</strong> ${escapeHtml(l.reason)}</p>
                            <div class="update-item-footer">
                                <span class="badge badge-type"><i class="fas fa-calendar-day"></i> ${escapeHtml(l.leave_type)}</span>
                                <span class="badge ${badgeClass}">${escapeHtml(l.status.toUpperCase())}</span>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
            }
        } else if (tab === 'diary') {
            if (typeof DIARIES_DATA === 'undefined' || !DIARIES_DATA || DIARIES_DATA.length === 0) {
                container.innerHTML = `
                    <div style="text-align:center; padding:40px; color:var(--t3);">
                        <i class="fas fa-book-open empty-state-icon" style="font-size:32px; margin-bottom:8px; display:block; color:#c084fc;"></i>
                        <h4>No digital diary entries</h4>
                        <p>Today's homework & class logs are up to date</p>
                    </div>
                `;
            } else {
                let html = '<div class="updates-list">';
                DIARIES_DATA.forEach(d => {
                    html += `
                        <div class="update-item" style="border-left-color:#8b5cf6;">
                            <div class="update-item-header">
                                <span class="update-item-title">${escapeHtml(d.title)}</span>
                                <span class="update-item-date">${escapeHtml(d.date)}</span>
                            </div>
                            <p class="update-item-body">${escapeHtml(d.content)}</p>
                            <div class="update-item-footer">
                                <span class="badge badge-teacher"><i class="fas fa-user-tie"></i> ${escapeHtml(d.staff_name)}</span>
                                <span class="badge badge-class"><i class="fas fa-graduation-cap"></i> Class ${escapeHtml(d.class_section)}</span>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
            }
        }
    } catch (e) {
        console.error("Error switching updates tab:", e);
    }
}

function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// ── CALENDAR DATE SELECTION ───────────────────────────────────────────────────
function selectDate(day) {
    const days = document.querySelectorAll('.calendar-grid-day');
    days.forEach(d => d.classList.remove('today'));
    
    // Find the cell matching this day and make it selected
    days.forEach(d => {
        if (d.textContent === day.toString() && !d.classList.contains('empty')) {
            d.classList.add('today');
        }
    });

    const month = document.getElementById('calendarMonth').value;
    const year = document.getElementById('calendarYear').value;
    const dateStr = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

    // Open calendar events details drawer
    openDrawer('calendar_events', dateStr);
}

function toggleBirthdays(type) {
    const isChecked = document.getElementById(type + 'BirthdaySwitch').checked;
    showToast(`Birthday filter for ${type}s is now ${isChecked ? 'Enabled' : 'Disabled'}`);
    renderCalendarGridAndList();
}

// ── COMING SOON TOAST ─────────────────────────────────────────────────────────
function showToast(msg) {
    const t = document.getElementById('toastMsg');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}

// ── CALENDAR DATA LOADING AND RENDERING ───────────────────────────────────────
let calendarEventsData = [];

function renderCalendarGrid() {
    const monthSelect = document.getElementById('calendarMonth');
    const yearSelect = document.getElementById('calendarYear');
    if (!monthSelect || !yearSelect) return;
    
    const month = parseInt(monthSelect.value); // 1-12
    const year = parseInt(yearSelect.value);
    
    const firstDate = new Date(year, month - 1, 1);
    const startOffset = (firstDate.getDay() + 6) % 7; // Mo = 0, Tu = 1, ..., Su = 6
    
    const daysInMonth = new Date(year, month, 0).getDate();
    
    const gridContainer = document.querySelector('.calendar-grid');
    if (!gridContainer) return;
    
    const headers = `
        <div class="calendar-grid-header">Mo</div>
        <div class="calendar-grid-header">Tu</div>
        <div class="calendar-grid-header">We</div>
        <div class="calendar-grid-header">Th</div>
        <div class="calendar-grid-header">Fr</div>
        <div class="calendar-grid-header">Sa</div>
        <div class="calendar-grid-header">Su</div>
    `;
    
    let daysHtml = headers;
    
    for (let i = 0; i < startOffset; i++) {
        daysHtml += '<div class="calendar-grid-day empty"></div>';
    }
    
    const todayObj = new Date();
    const isCurrentMonthYear = (todayObj.getMonth() + 1 === month && todayObj.getFullYear() === year);
    
    for (let i = 1; i <= daysInMonth; i++) {
        const isToday = (isCurrentMonthYear && todayObj.getDate() === i);
        const todayClass = isToday ? 'today' : '';
        daysHtml += `<div class="calendar-grid-day ${todayClass}" onclick="selectDate(${i})">${i}</div>`;
    }
    
    const totalCells = startOffset + daysInMonth;
    const remaining = (7 - (totalCells % 7)) % 7;
    for (let i = 0; i < remaining; i++) {
        daysHtml += '<div class="calendar-grid-day empty"></div>';
    }
    
    gridContainer.innerHTML = daysHtml;
}

function changeCalendarMonthYear() {
    renderCalendarGrid();
    loadCalendarMonthEvents();
}

function loadCalendarMonthEvents() {
    const monthSelect = document.getElementById('calendarMonth');
    const yearSelect = document.getElementById('calendarYear');
    if (!monthSelect || !yearSelect) return;
    
    const month = monthSelect.value;
    const year = yearSelect.value;
    
    fetch(`/school/dashboard/details?type=calendar_month_events&month=${month}&year=${year}`)
        .then(res => res.json())
        .then(res => {
            calendarEventsData = res.events || [];
            renderCalendarGridAndList();
        })
        .catch(err => {
            console.error('Error loading calendar events:', err);
        });
}

function renderCalendarGridAndList() {
    const studentSwitch = document.getElementById('studentBirthdaySwitch').checked;
    const teacherSwitch = document.getElementById('teacherBirthdaySwitch').checked;

    // Filter events based on active switches
    const activeEvents = calendarEventsData.filter(evt => {
        if (evt.type === 'student' && !studentSwitch) return false;
        if (evt.type === 'staff' && !teacherSwitch) return false;
        return true;
    });

    // 1. Highlight days in the grid
    const dayCells = document.querySelectorAll('.calendar-grid-day:not(.empty)');
    
    dayCells.forEach(cell => {
        cell.classList.remove('has-event', 'has-event-staff', 'has-event-student', 'has-event-school', 'has-holiday');
    });

    activeEvents.forEach(evt => {
        dayCells.forEach(cell => {
            if (cell.textContent.trim() === evt.day.toString()) {
                if (evt.type === 'staff') {
                    cell.classList.add('has-event-staff');
                } else if (evt.type === 'student') {
                    cell.classList.add('has-event-student');
                } else if (evt.type === 'event') {
                    cell.classList.add('has-event-school');
                } else if (evt.type === 'holiday') {
                    cell.classList.add('has-holiday');
                }
            }
        });
    });

    // 2. Render event listing at the bottom of the card
    const listContainer = document.getElementById('calendarEventsList');
    if (!listContainer) return;

    if (activeEvents.length === 0) {
        listContainer.innerHTML = `<div style="text-align:center;padding:18px;color:var(--t3);font-size:11px;">No active birthdays or events scheduled</div>`;
        return;
    }

    // Sort chronologically by day
    activeEvents.sort((a, b) => a.day - b.day);

    const monthSelect = document.getElementById('calendarMonth');
    const monthShort = monthSelect ? monthSelect.options[monthSelect.selectedIndex].text.substring(0,3) : 'June';

    let html = '';
    activeEvents.forEach(evt => {
        let badgeText = evt.type;
        let highlightClass = '';
        let subText = '';
        
        let displayName = evt.name;
        if (displayName.endsWith("'s Birthday")) {
            displayName = displayName.replace("'s Birthday", "");
        }

        if (evt.type === 'student') {
            badgeText = 'Student';
            highlightClass = 'student-birthday-highlight';
            subText = evt.details || 'Birthday';
        } else if (evt.type === 'staff') {
            badgeText = 'Teacher';
            highlightClass = 'staff-birthday-highlight';
            subText = evt.details || 'Teacher\'s Birthday';
        } else if (evt.type === 'event') {
            badgeText = 'Event';
            highlightClass = 'school-event-highlight';
            subText = evt.details || 'School Event';
        } else if (evt.type === 'holiday') {
            badgeText = 'Holiday';
            highlightClass = 'holiday-highlight';
            subText = evt.details || 'School Holiday';
        }

        html += `
            <div class="calendar-event-item-custom ${highlightClass}" onclick="selectDate(${evt.day})">
                <div class="event-left-badge ${evt.type}">${evt.day} ${monthShort}</div>
                <div class="event-mid-details">
                    <span class="event-mid-title">${displayName.toUpperCase()}</span>
                    <span class="event-mid-sub">${subText}</span>
                </div>
                <div class="event-right-type">
                    <span class="event-type-pill ${evt.type}">${badgeText}</span>
                </div>
            </div>
        `;
    });

    listContainer.innerHTML = html;
}

function dayWithSuffix(day) {
    if (day > 3 && day < 21) return day + 'th';
    switch (day % 10) {
        case 1:  return day + "st";
        case 2:  return day + "nd";
        case 3:  return day + "rd";
        default: return day + "th";
    }
}

// ── SIDE DRAWER CONTROLLER ──────────────────────────────────────────────────
function openDrawer(type, extraVal = '') {
    const drawer = document.getElementById('sideDrawer');
    const overlay = document.getElementById('drawerOverlay');
    const body = document.getElementById('drawerBody');
    const title = document.getElementById('drawerTitle');

    // Remove any previous sizing classes
    drawer.classList.remove('drawer-sm', 'drawer-md', 'drawer-lg', 'drawer-xl');

    // Apply proper size class based on details type
    if (type === 'students') {
        drawer.classList.add('drawer-xl');
    } else if (type === 'staffs' || type === 'income') {
        drawer.classList.add('drawer-lg');
    } else if (type === 'send_reminder' || type === 'class_fee_report' || type === 'student_attendance' || type === 'staff_attendance' || type === 'calendar_month_events') {
        drawer.classList.add('drawer-md');
    } else {
        drawer.classList.add('drawer-sm');
    }

    drawer.classList.add('open');
    overlay.classList.add('open');

    // Show spinner
    body.innerHTML = `
        <div class="drawer-loading">
            <i class="fas fa-spinner fa-spin"></i>
            <span>Fetching details...</span>
        </div>
    `;

    // calendar_month_events needs month/year params and special rendering
    if (type === 'calendar_month_events') {
        const monthEl = document.getElementById('calendarMonth');
        const yearEl = document.getElementById('calendarYear');
        const month = monthEl ? monthEl.value : new Date().getMonth() + 1;
        const year = yearEl ? yearEl.value : new Date().getFullYear();
        const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        title.textContent = `${monthNames[parseInt(month) - 1]} ${year} — All Events & Birthdays`;
        fetch(`/school/dashboard/details?type=calendar_month_events&month=${month}&year=${year}`)
            .then(r => r.json())
            .then(res => {
                const events = res.events || [];
                if (events.length === 0) {
                    body.innerHTML = `<div class="drawer-empty"><i class="fas fa-calendar-xmark"></i><span>No events or birthdays this month.</span></div>`;
                    return;
                }
                // Group events by day
                const byDay = {};
                events.forEach(ev => {
                    const d = ev.day;
                    if (!byDay[d]) byDay[d] = [];
                    byDay[d].push(ev);
                });
                let html = '<div style="padding: 0;">';
                Object.keys(byDay).sort((a,b) => a - b).forEach(day => {
                    html += `<div style="padding: 10px 16px; border-bottom: 1px solid var(--border);">`;
                    html += `<div style="font-size:11px;font-weight:800;color:var(--gold);margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px;">Day ${day}</div>`;
                    byDay[day].forEach(ev => {
                        const isHoliday = ev.type === 'holiday';
                        const isBirthday = ev.type === 'student' || ev.type === 'staff';
                        const color = isHoliday ? '#ef4444' : isBirthday ? '#8b5cf6' : '#10b981';
                        const icon = isHoliday ? 'fa-calendar-xmark' : isBirthday ? 'fa-birthday-cake' : 'fa-calendar-check';
                        html += `<div style="display:flex;align-items:center;gap:10px;padding:6px 0;">`;
                        html += `<div style="width:28px;height:28px;border-radius:50%;background:${color}20;color:${color};display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0;"><i class="fas ${icon}"></i></div>`;
                        html += `<div style="flex:1;"><div style="font-size:12px;font-weight:700;color:var(--t1);">${ev.name}</div><div style="font-size:10.5px;color:var(--t3);">${ev.type === 'student' ? 'Student Birthday' : ev.type === 'staff' ? 'Staff Birthday' : ev.type === 'holiday' ? 'Holiday' : 'Event'} • ${ev.details || ''}</div></div>`;
                        html += `</div>`;
                    });
                    html += `</div>`;
                });
                html += '</div>';
                body.innerHTML = html;
            })
            .catch(err => {
                console.error(err);
                body.innerHTML = `<div class="drawer-empty"><i class="fas fa-triangle-exclamation" style="color:var(--red);"></i><span>Failed to load events.</span></div>`;
            });
        return;
    }

    let url = `/school/dashboard/details?type=${type}`;
    if (type === 'calendar_events' && extraVal) {
        url += `&date=${extraVal}`;
    }

    fetch(url)
        .then(response => response.json())
        .then(res => {
            title.textContent = res.title || 'Details Listing';
            renderDrawerContent(res.type, res.data);
        })
        .catch(err => {
            console.error(err);
            body.innerHTML = `
                <div class="drawer-empty">
                    <i class="fas fa-triangle-exclamation" style="color:var(--red);"></i>
                    <span>Failed to load details. Please try again.</span>
                </div>
            `;
        });
}

function closeDrawer() {
    document.getElementById('sideDrawer').classList.remove('open');
    document.getElementById('drawerOverlay').classList.remove('open');
}

function toggleSendAllSelector(cb) {
    const isAll = cb.checked;
    const classEl = document.getElementById('reminderClass');
    const sectionEl = document.getElementById('reminderSection');
    if (classEl) classEl.disabled = isAll;
    if (sectionEl) sectionEl.disabled = isAll;
    // Visual cue
    const row = document.querySelector('.reminder-selector-row');
    if (row) row.style.opacity = isAll ? '0.45' : '1';
}

function addReminderClass() {
    const classEl = document.getElementById('reminderClass');
    const sectionEl = document.getElementById('reminderSection');
    if (!classEl || !sectionEl) return;
    const cls = classEl.options[classEl.selectedIndex]?.text || '';
    const sec = sectionEl.options[sectionEl.selectedIndex]?.text || '';
    if (!classEl.value) { showToast('Please select a class first.'); return; }
    showToast(`Class ${cls}${sec && sec !== 'Select Section' ? ' - ' + sec : ''} added to reminder list.`);
}

function triggerSendReminder() {
    const btn = document.querySelector('.btn-send-now');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

    const sendAll = document.getElementById('sendAllReminder')?.checked ?? true;
    const classId = document.getElementById('reminderClass')?.value || '';
    const sectionId = document.getElementById('reminderSection')?.value || '';
    const prevYear = document.getElementById('sendPrevReminder')?.checked ?? false;

    fetch('/school/dashboard/send-reminder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF
        },
        body: JSON.stringify({ send_all: sendAll, class_id: classId, section_id: sectionId, prev_year: prevYear })
    })
    .then(response => response.json())
    .then(res => {
        if (res.success) {
            showToast(res.message || 'Reminders sent successfully!');
            closeDrawer();
        } else {
            showToast(res.message || 'Reminders sent.');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Reminder notifications sent!');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

function filterReminderStudents(query) {
    const rows = document.querySelectorAll('#reminderStudentList .reminder-student-row');
    const q = query.toLowerCase().trim();
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = (!q || text.includes(q)) ? '' : 'none';
    });
    updateReminderCount();
}

function toggleSelectAllReminder(cb) {
    const rows = document.querySelectorAll('#reminderStudentList .reminder-student-row');
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const chk = row.querySelector('.reminder-chk');
            if (chk) chk.checked = cb.checked;
        }
    });
    updateReminderCount();
}

function updateReminderCount() {
    const checked = document.querySelectorAll('.reminder-chk:checked').length;
    const counter = document.getElementById('reminderSelectedCount');
    if (counter) counter.textContent = checked + ' selected';
}

// ── CUSTOM DETAILED RENDERERS FOR STUDENT, STAFF AND FEE REPORT ─────────────
let studentRowsData = [];
let currentStudentView = 'section';

function renderStudentsDrawer(data) {
    studentRowsData = data.rows || [];

    // Extract unique classes
    const uniqueClasses = [...new Set(studentRowsData.map(r => {
        const parts = r.class_section.split(' ');
        return parts.slice(0, -1).join(' ') || r.class_section;
    }))];

    let classOptions = '<option value="">Select Class</option>';
    uniqueClasses.forEach(cls => {
        classOptions += `<option value="${cls}">${cls}</option>`;
    });

    let sectionOptions = '<option value="">Select Section</option>';
    const uniqueSections = ['A', 'B'];
    uniqueSections.forEach(sec => {
        sectionOptions += `<option value="${sec}">${sec}</option>`;
    });

    const body = document.getElementById('drawerBody');
    body.innerHTML = `
        <div class="drawer-toolbar">
            <div class="drawer-select-group">
                <label>Academic Year *</label>
                <select class="drawer-select">
                    <option>Apr 2025 - Mar 2026</option>
                </select>
            </div>
            <div class="drawer-select-group">
                <label>Select Class *</label>
                <select class="drawer-select" id="studentFilterClass">
                    ${classOptions}
                </select>
            </div>
            <div class="drawer-select-group">
                <label>Select Section *</label>
                <select class="drawer-select" id="studentFilterSection">
                    ${sectionOptions}
                </select>
            </div>
            <div style="flex: 1; min-width: 140px; margin-top: 12px;">
                <label class="admission-checkbox-lbl" style="font-size:9.5px; font-weight:700; cursor:pointer;">
                    <input type="checkbox" id="studentFilterDeactivated" checked> Include deactivated students in old/new admissions
                </label>
            </div>
        </div>

        <div class="drawer-tabs-row">
            <div class="drawer-tab-btn-group">
                <button class="drawer-tab-btn active" id="studentTabSection" onclick="switchStudentView('section')">SECTION VIEW</button>
                <button class="drawer-tab-btn" id="studentTabClass" onclick="switchStudentView('class')">CLASS VIEW</button>
            </div>
            <button class="drawer-btn-download" onclick="showToast('Exporting students list...')"><i class="fas fa-download"></i> DOWNLOAD</button>
        </div>

        <div class="drawer-table-wrap">
            <table class="drawer-table-complex">
                <thead>
                    <tr>
                        <th rowspan="2" id="studentColHdr">Class & Section</th>
                        <th colspan="2">Old Admissions</th>
                        <th rowspan="2">New Admissions</th>
                        <th rowspan="2">Today's Admissions <i class="fas fa-circle-info" style="font-size:8px;cursor:pointer;" data-info="Students admitted on the selected/current date."></i></th>
                        <th colspan="2">TC Students <i class="fas fa-circle-info" style="font-size:8px;cursor:pointer;" data-info="Students issued Transfer Certificates (Old vs New)."></i></th>
                        <th rowspan="2">Irregular Students <i class="fas fa-circle-info" style="font-size:8px;cursor:pointer;" data-info="Students flagged for highly irregular attendance."></i></th>
                        <th rowspan="2">Deactivated Students</th>
                        <th rowspan="2">Total Students</th>
                        <th colspan="2">Deleted Students <i class="fas fa-circle-info" style="font-size:8px;cursor:pointer;" data-info="Students deleted from the active registry."></i></th>
                        <th rowspan="2">Active Students</th>
                    </tr>
                    <tr>
                        <th>Promoted</th>
                        <th>Repeated</th>
                        <th>Old Student TC</th>
                        <th>New Student TC</th>
                        <th>Old Student deleted</th>
                        <th>New Student deleted</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody">
                </tbody>
                <tfoot id="studentTableFoot">
                </tfoot>
            </table>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;font-size:11px;color:var(--t2);">
            <span id="studentPaginationText">1-8 of 8</span>
            <div style="display:flex;gap:8px;">
                <button class="drawer-staff-copy-btn" style="border:1px solid var(--border);padding:2px 6px;border-radius:4px;"><i class="fas fa-chevron-left"></i></button>
                <button class="drawer-staff-copy-btn" style="border:1px solid var(--border);padding:2px 6px;border-radius:4px;"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    `;

    document.getElementById('studentFilterClass').addEventListener('change', filterStudentTable);
    document.getElementById('studentFilterSection').addEventListener('change', filterStudentTable);
    document.getElementById('studentFilterDeactivated').addEventListener('change', filterStudentTable);

    filterStudentTable();
}

function switchStudentView(view) {
    currentStudentView = view;
    document.getElementById('studentTabSection').classList.toggle('active', view === 'section');
    document.getElementById('studentTabClass').classList.toggle('active', view === 'class');
    document.getElementById('studentColHdr').textContent = view === 'section' ? 'Class & Section' : 'Class';
    filterStudentTable();
}

function filterStudentTable() {
    const selClass = document.getElementById('studentFilterClass').value;
    const selSec = document.getElementById('studentFilterSection').value;
    const inclDeact = document.getElementById('studentFilterDeactivated').checked;

    let filtered = studentRowsData;

    if (selClass) {
        filtered = filtered.filter(r => {
            const parts = r.class_section.split(' ');
            const cls = parts.slice(0, -1).join(' ') || r.class_section;
            return cls === selClass;
        });
    }

    if (selSec) {
        filtered = filtered.filter(r => {
            const parts = r.class_section.split(' ');
            const sec = parts[parts.length - 1] || '';
            return sec === selSec;
        });
    }

    let finalRows = [];
    if (currentStudentView === 'class') {
        const groups = {};
        filtered.forEach(r => {
            const parts = r.class_section.split(' ');
            const cls = parts.slice(0, -1).join(' ') || r.class_section;
            if (!groups[cls]) {
                groups[cls] = {
                    class_section: cls, promoted: 0, repeated: 0, new: 0, today: 0,
                    old_tc: 0, new_tc: 0, irregular: 0, deactivated: 0, total: 0,
                    old_deleted: 0, new_deleted: 0, active: 0
                };
            }
            groups[cls].promoted += r.promoted;
            groups[cls].repeated += r.repeated;
            groups[cls].new += r.new;
            groups[cls].today += r.today;
            groups[cls].old_tc += r.old_tc;
            groups[cls].new_tc += r.new_tc;
            groups[cls].irregular += r.irregular;
            groups[cls].deactivated += r.deactivated;
            groups[cls].total += r.total;
            groups[cls].old_deleted += r.old_deleted;
            groups[cls].new_deleted += r.new_deleted;
            groups[cls].active += r.active;
        });
        finalRows = Object.values(groups);
    } else {
        finalRows = filtered;
    }

    if (!inclDeact) {
        finalRows = finalRows.map(r => {
            const copy = { ...r };
            copy.total = copy.total - copy.deactivated;
            copy.deactivated = 0;
            return copy;
        });
    }

    let rowsHtml = '';
    const totals = {
        promoted: 0, repeated: 0, new: 0, today: 0,
        old_tc: 0, new_tc: 0, irregular: 0, deactivated: 0,
        total: 0, old_deleted: 0, new_deleted: 0, active: 0
    };

    finalRows.forEach(r => {
        rowsHtml += `
            <tr>
                <td class="text-left">${r.class_section}</td>
                <td class="text-bold">${r.promoted}</td>
                <td>${r.repeated}</td>
                <td class="text-bold">${r.new}</td>
                <td>${r.today}</td>
                <td>${r.old_tc}</td>
                <td>${r.new_tc}</td>
                <td>${r.irregular}</td>
                <td class="text-orange">${r.deactivated}</td>
                <td class="text-bold">${r.total}</td>
                <td>${r.old_deleted}</td>
                <td>${r.new_deleted}</td>
                <td class="text-bold" style="color:var(--green);">${r.active}</td>
            </tr>
        `;
        Object.keys(totals).forEach(k => {
            totals[k] += r[k];
        });
    });

    document.getElementById('studentTableBody').innerHTML = rowsHtml || '<tr><td colspan="13" style="text-align:center;padding:20px;color:var(--t3);">No classes found</td></tr>';

    document.getElementById('studentTableFoot').innerHTML = `
        <tr>
            <td>Total</td>
            <td>${totals.promoted}</td>
            <td>${totals.repeated}</td>
            <td>${totals.new}</td>
            <td>${totals.today}</td>
            <td>${totals.old_tc}</td>
            <td>${totals.new_tc}</td>
            <td>${totals.irregular}</td>
            <td>${totals.deactivated}</td>
            <td>${totals.total}</td>
            <td>${totals.old_deleted}</td>
            <td>${totals.new_deleted}</td>
            <td>${totals.active}</td>
        </tr>
    `;

    document.getElementById('studentPaginationText').textContent = `1-${finalRows.length} of ${finalRows.length}`;
}

let staffRowsData = [];
let staffFilterActive = true;
let currentStaffTypeTab = 'Teaching';

function renderStaffsDrawer(data) {
    staffRowsData = data.rows || [];

    const stats = data.stats;
    const body = document.getElementById('drawerBody');

    body.innerHTML = `
        <div class="drawer-tabs-row" style="margin-bottom:12px;">
            <div class="drawer-tab-btn-group">
                <button class="drawer-tab-btn active" id="staffFilterActiveBtn" onclick="switchStaffActiveStatus(true)">ACTIVE STAFF</button>
                <button class="drawer-tab-btn" id="staffFilterDeactivatedBtn" onclick="switchStaffActiveStatus(false)">DEACTIVATED STAFF</button>
            </div>
            <div class="drawer-select-group">
                <select class="drawer-select" id="staffFilterDept" onchange="filterStaffTable()">
                    <option value="">Filter by department</option>
                    <option value="Teaching">Teaching</option>
                    <option value="Admin Staff">Admin Staff</option>
                    <option value="Support">Support</option>
                </select>
            </div>
        </div>

        <div class="drawer-stat-cards">
            <div class="drawer-stat-card orange-border">
                <div class="drawer-stat-card-title"><i class="fas fa-user-plus" style="color:#8b5cf6;"></i> Newly Added Staff</div>
                <div class="drawer-stat-card-val">
                    ${stats.newly_added}
                    <span class="drawer-stat-card-sub">+${stats.newly_added_academic_year} in this academic year</span>
                </div>
            </div>
            <div class="drawer-stat-card orange-border">
                <div class="drawer-stat-card-title"><i class="fas fa-users" style="color:#8b5cf6;"></i> Old Staff</div>
                <div class="drawer-stat-card-val">${stats.old_staff}</div>
            </div>
            <div class="drawer-stat-card red-border">
                <div class="drawer-stat-card-title"><i class="fas fa-user-slash" style="color:#ef4444;"></i> Deactivated Staff</div>
                <div class="drawer-stat-card-val" style="color:#ef4444;">${stats.deactivated}</div>
            </div>
        </div>

        <div class="drawer-search-row">
            <div class="drawer-tab-btn-group" id="staffTypeTabs">
                <button class="drawer-tab-btn active" onclick="switchStaffTypeTab('Teaching')">TEACHING (${staffRowsData.filter(r=>r.employment_type==='Teaching').length})</button>
                <button class="drawer-tab-btn" onclick="switchStaffTypeTab('Non-Teaching')">NON-TEACHING (${staffRowsData.filter(r=>r.employment_type==='Non-Teaching').length})</button>
                <button class="drawer-tab-btn" onclick="switchStaffTypeTab('Driver/Supporting Staff')">DRIVER/SUPPORTING STAFF (${staffRowsData.filter(r=>r.employment_type==='Driver/Supporting Staff' || r.employment_type==='Driver').length})</button>
                <button class="drawer-tab-btn" onclick="switchStaffTypeTab('Others')">OTHERS (${staffRowsData.filter(r=>r.employment_type==='Others'|| r.employment_type==='Other').length})</button>
                <button class="drawer-tab-btn" onclick="switchStaffTypeTab('Admin')">ADMIN (${staffRowsData.filter(r=>r.employment_type==='Admin').length})</button>
            </div>
            <button class="drawer-btn-logs" onclick="showToast('Showing system logs...')">SHOW LOGS</button>
        </div>

        <div class="drawer-search-row">
            <div class="drawer-search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="staffSearchInput" onkeyup="filterStaffTable()" placeholder="Search by staff name, employee ID, mobile, email">
            </div>
        </div>

        <div class="drawer-table-wrap">
            <table class="drawer-table-complex" style="text-align: left;">
                <thead>
                    <tr>
                        <th>Staff ID <i class="fas fa-sort" style="font-size:8px;"></i></th>
                        <th>Name <i class="fas fa-sort" style="font-size:8px;"></i></th>
                        <th>Designation</th>
                        <th>Highest Qualification</th>
                        <th>Department</th>
                        <th>Mobile</th>
                        <th>E-mail</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="staffTableBody">
                </tbody>
            </table>
        </div>
    `;

    filterStaffTable();
}

function switchStaffActiveStatus(isActive) {
    staffFilterActive = isActive;
    document.getElementById('staffFilterActiveBtn').classList.toggle('active', isActive);
    document.getElementById('staffFilterDeactivatedBtn').classList.toggle('active', !isActive);
    filterStaffTable();
}

function switchStaffTypeTab(type) {
    currentStaffTypeTab = type;
    const buttons = document.querySelectorAll('#staffTypeTabs .drawer-tab-btn');
    const tabNames = ['Teaching', 'Non-Teaching', 'Driver/Supporting Staff', 'Others', 'Admin'];
    buttons.forEach((btn, idx) => {
        btn.classList.toggle('active', tabNames[idx] === type);
    });
    filterStaffTable();
}

function filterStaffTable() {
    const dept = document.getElementById('staffFilterDept').value;
    const searchQuery = document.getElementById('staffSearchInput').value.toLowerCase();

    let filtered = staffRowsData.filter(r => r.is_active === staffFilterActive);

    filtered = filtered.filter(r => {
        if (currentStaffTypeTab === 'Teaching') {
            return r.employment_type === 'Teaching';
        } else if (currentStaffTypeTab === 'Non-Teaching') {
            return r.employment_type === 'Non-Teaching';
        } else if (currentStaffTypeTab === 'Driver/Supporting Staff') {
            return r.employment_type === 'Driver/Supporting Staff' || r.employment_type === 'Driver';
        } else if (currentStaffTypeTab === 'Others') {
            return r.employment_type === 'Others' || r.employment_type === 'Other';
        } else if (currentStaffTypeTab === 'Admin') {
            return r.employment_type === 'Admin';
        }
        return true;
    });

    if (dept) {
        filtered = filtered.filter(r => r.department === dept);
    }

    if (searchQuery) {
        filtered = filtered.filter(r => 
            r.name.toLowerCase().includes(searchQuery) ||
            r.staff_id.toLowerCase().includes(searchQuery) ||
            r.phone.toLowerCase().includes(searchQuery) ||
            r.email.toLowerCase().includes(searchQuery)
        );
    }

    let rowsHtml = '';
    if (filtered.length === 0) {
        rowsHtml = `<tr><td colspan="8" style="text-align:center;color:var(--t3);padding:20px;">No staff records match the filters.</td></tr>`;
    } else {
        filtered.forEach(r => {
            rowsHtml += `
                <tr>
                    <td>${r.staff_id}</td>
                    <td class="text-left">
                        <span class="drawer-staff-avatar"><i class="fas fa-user"></i></span>
                        <strong>${r.name}</strong>
                    </td>
                    <td>${r.designation}</td>
                    <td>${r.highest_qualification}</td>
                    <td>${r.department}</td>
                    <td>
                        ${r.phone}
                        <button class="drawer-staff-copy-btn" onclick="navigator.clipboard.writeText('${r.phone}'); showToast('Mobile copied!')" title="Copy"><i class="far fa-copy"></i></button>
                    </td>
                    <td>
                        ${r.email}
                        <button class="drawer-staff-copy-btn" onclick="navigator.clipboard.writeText('${r.email}'); showToast('Email copied!')" title="Copy"><i class="far fa-copy"></i></button>
                    </td>
                    <td>
                        <button class="drawer-action-btn green" onclick="window.location.href='/school/staff/${r.id}'" title="View details"><i class="far fa-eye"></i></button>
                        <button class="drawer-action-btn" onclick="window.location.href='/school/staff/${r.id}/edit'" title="Edit staff"><i class="far fa-edit"></i></button>
                    </td>
                </tr>
            `;
        });
    }

    document.getElementById('staffTableBody').innerHTML = rowsHtml;
}

function renderClassFeeReportDrawer(data) {
    let rowsHtml = '';
    let totals = {
        total_fee: 0,
        paid: 0,
        due: 0
    };
    
    const reportData = data || [
        { class_name: 'Class NUR', total_fee: 60000, paid: 50000, due: 10000 },
        { class_name: 'Class LKG', total_fee: 80000, paid: 70000, due: 10000 },
        { class_name: 'Class UKG', total_fee: 50000, paid: 40000, due: 10000 },
        { class_name: 'Class 1', total_fee: 120000, paid: 90000, due: 30000 },
        { class_name: 'Class 2', total_fee: 100000, paid: 80000, due: 20000 },
        { class_name: 'Class 3', total_fee: 90000, paid: 70000, due: 20000 }
    ];

    reportData.forEach(r => {
        const pctPaid = r.total_fee > 0 ? ((r.paid / r.total_fee) * 100).toFixed(1) : '0.0';
        rowsHtml += `
            <tr>
                <td class="text-left" style="font-weight:600;">${r.class_name}</td>
                <td style="font-weight:700;">₹ ${r.total_fee.toLocaleString()}</td>
                <td style="color:var(--green);font-weight:700;">₹ ${r.paid.toLocaleString()} (${pctPaid}%)</td>
                <td style="color:var(--red);font-weight:700;">₹ ${r.due.toLocaleString()}</td>
            </tr>
        `;
        totals.total_fee += r.total_fee;
        totals.paid += r.paid;
        totals.due += r.due;
    });

    const totPctPaid = totals.total_fee > 0 ? ((totals.paid / totals.total_fee) * 100).toFixed(1) : '0.0';

    const body = document.getElementById('drawerBody');
    body.innerHTML = `
        <div class="drawer-tabs-row" style="margin-bottom:12px;">
            <h4 style="font-size:12px;font-weight:700;color:var(--t2);">Class-Wise Fee Collection Breakdown</h4>
            <button class="drawer-btn-download" onclick="downloadClassFeeReport()"><i class="fas fa-download"></i> DOWNLOAD</button>
        </div>
        <div style="overflow:hidden;position:relative;">
            <div id="classFeeSlider" style="display:flex;flex-direction:column;transition:transform 0.4s ease;">
                <table class="drawer-table-complex" style="text-align:left;width:100%;">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Total Fee Amount</th>
                            <th>Collected Amount</th>
                            <th>Due Amount</th>
                        </tr>
                    </thead>
                    <tbody id="classFeeTableBody">
                        ${rowsHtml}
                    </tbody>
                    <tfoot>
                        <tr style="background:#f1f5f9;font-weight:700;">
                            <td>Total</td>
                            <td>₹ ${totals.total_fee.toLocaleString()}</td>
                            <td style="color:var(--green);">₹ ${totals.paid.toLocaleString()} (${totPctPaid}%)</td>
                            <td style="color:var(--red);">₹ ${totals.due.toLocaleString()}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    `;

    // Store report data globally for download
    window._classFeeReportData = reportData;
    window._classFeeReportTotals = totals;
}

function downloadClassFeeReport() {
    const data = window._classFeeReportData || [];
    const totals = window._classFeeReportTotals || {};
    let csv = 'Class,Total Fee Amount,Collected Amount,Due Amount\n';
    data.forEach(r => {
        const pct = r.total_fee > 0 ? ((r.paid/r.total_fee)*100).toFixed(1) : '0.0';
        csv += `"${r.class_name}","Rs ${r.total_fee.toLocaleString()}","Rs ${r.paid.toLocaleString()} (${pct}%)","Rs ${r.due.toLocaleString()}"\n`;
    });
    const totPct = totals.total_fee > 0 ? ((totals.paid/totals.total_fee)*100).toFixed(1) : '0.0';
    csv += `"Total","Rs ${(totals.total_fee||0).toLocaleString()}","Rs ${(totals.paid||0).toLocaleString()} (${totPct}%)","Rs ${(totals.due||0).toLocaleString()}"`;
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'class_wise_fee_report.csv';
    a.click();
    URL.revokeObjectURL(url);
    showToast('Class-wise fee report downloaded!');
}

let incomeRowsData = [];
function renderTotalIncomeDrawer(data) {
    incomeRowsData = data || [];
    
    // Calculate cash and bank deposits dynamically from payment modes
    let totalCash = 0;
    let totalBank = 0;
    incomeRowsData.forEach(item => {
        let amt = parseFloat(item.amount.replace(/[^0-9.]/g, ''));
        if (!isNaN(amt)) {
            if (item.payment_mode && item.payment_mode.toLowerCase().includes('cash')) {
                totalCash += amt;
            } else {
                totalBank += amt;
            }
        }
    });

    let totalIncome = totalCash + totalBank;
    const totalIncomeStr = '₹ ' + totalIncome.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const totalCashStr = '₹ ' + totalCash.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const totalBankStr = '₹ ' + totalBank.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const body = document.getElementById('drawerBody');
    body.innerHTML = `
        <div class="drawer-toolbar" style="margin-bottom: 20px; display: flex; gap: 15px; font-family: 'Plus Jakarta Sans', sans-serif;">
            <div class="drawer-select-group" style="margin: 0; min-width: 180px;">
                <label style="font-size: 11px; font-weight: 700; color: var(--t2); margin-bottom: 4px; display: block;">Academic Year *</label>
                <div style="position: relative;">
                    <i class="far fa-calendar-alt" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--t2); font-size: 14px;"></i>
                    <select class="drawer-select" style="width: 100%; padding-left: 30px; font-weight: 600; height: 34px; border: 1px solid var(--border); border-radius: 4px; background: #fff; font-family: inherit;">
                        <option>Apr 2025 - Mar 2026</option>
                    </select>
                </div>
            </div>
        </div>

        <div style="background: var(--page); border: 1px solid var(--border); border-radius: 8px; padding: 20px; margin-bottom: 20px; font-family: 'Plus Jakarta Sans', sans-serif;">
            <div style="font-size: 16px; font-weight: 700; color: var(--navy); margin-bottom: 15px;">
                Total Profit/Loss: <span contenteditable="true" id="editableProfitLoss" style="color: var(--navy); font-weight: 800; border-bottom: 1px dashed var(--gold); outline: none; padding: 0 4px;" onblur="updateCustomProfitLoss(this)" onkeydown="checkAmountEnter(event, this)">${totalIncomeStr}</span>
            </div>
            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                <!-- Cash in hand -->
                <div style="display: flex; flex-direction: column;">
                    <div style="background: #004d40; color: #fff; padding: 10px 16px; border-radius: 4px; font-weight: 700; font-size: 13px; display: flex; align-items: center; gap: 4px;">
                        <span>Total Cash in Hand:</span>
                        <span contenteditable="true" id="editableCash" style="border-bottom: 1px dashed #a7f3d0; outline: none; padding: 0 4px;" onblur="updateCustomTotals()" onkeydown="checkAmountEnter(event, this)">${totalCashStr}</span>
                    </div>
                    <a href="javascript:void(0)" style="color: #8b5cf6; text-decoration: none; font-size: 11px; font-weight: 700; margin-top: 6px; display: inline-flex; align-items: center; gap: 4px;" onclick="showToast('Sending cash in hand to bank...')">
                        SEND TO BANK <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                    </a>
                </div>
                
                <div style="font-size: 20px; font-weight: 700; color: var(--t2); padding-bottom: 18px;">+</div>
                
                <!-- Bank deposit -->
                <div style="display: flex; flex-direction: column;">
                    <div style="background: #004d40; color: #fff; padding: 10px 16px; border-radius: 4px; font-weight: 700; font-size: 13px; margin-bottom: 18px; display: flex; align-items: center; gap: 4px;">
                        <span>Total Bank Deposit:</span>
                        <span contenteditable="true" id="editableBank" style="border-bottom: 1px dashed #a7f3d0; outline: none; padding: 0 4px;" onblur="updateCustomTotals()" onkeydown="checkAmountEnter(event, this)">${totalBankStr}</span>
                    </div>
                </div>
                
                <div style="font-size: 20px; font-weight: 700; color: var(--t2); padding-bottom: 18px;">=</div>
                
                <!-- Total Income -->
                <div style="display: flex; flex-direction: column;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 18px;">
                        <div style="background: #004d40; color: #fff; padding: 10px 16px; border-radius: 4px; font-weight: 700; font-size: 13px; display: flex; align-items: center; gap: 4px;">
                            <span>Total Income:</span>
                            <span id="summaryTotalIncome" style="font-weight: 800;">${totalIncomeStr}</span>
                        </div>
                        <i class="far fa-eye" style="cursor: pointer; color: #8b5cf6; font-size: 16px; margin-left: 4px;" title="View details" onclick="showToast('Viewing income details...')"></i>
                        <i class="fas fa-download" style="cursor: pointer; color: #8b5cf6; font-size: 16px; margin-left: 4px;" title="Download invoice" onclick="showToast('Downloading statement...')"></i>
                    </div>
                </div>
                
                <!-- Show Institute Fee Toggle -->
                <div style="margin-left: auto; display: flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 600; color: var(--t2); margin-bottom: 18px;">
                    <span>SHOW INSTITUTE FEE</span>
                    <label class="switch-wrapper" style="margin: 0; transform: scale(0.9);">
                        <input type="checkbox" id="showInstituteFeeToggle" checked onchange="filterIncomeTable()">
                        <span class="switch-slider"></span>
                    </label>
                    <i class="fas fa-circle-info" style="font-size: 12px; cursor: pointer; color: #000;" data-info="Toggle to filter between institute and general fees."></i>
                </div>
            </div>
        </div>

        <div style="background: #4db6ac; color: #fff; font-size: 12px; font-weight: 700; padding: 6px 16px; border-radius: 20px; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 20px; font-family: 'Plus Jakarta Sans', sans-serif;" id="incomeModuleBadge">
            <span style="width: 7px; height: 7px; border-radius: 50%; background: #fff; display: inline-block;"></span>
            <span id="incomeModuleBadgeText">Total Income Added in Income Module: ${totalIncomeStr}</span>
        </div>

        <!-- Action / Search Toolbar -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; font-family: 'Plus Jakarta Sans', sans-serif;">
            <div style="display: flex; gap: 12px; align-items: center;">
                <button class="drawer-btn-download" style="border: 1px solid #8b5cf6; color: #8b5cf6; background: #fff; padding: 8px 16px; font-size: 11.5px; font-weight: 700; border-radius: 4px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer;" onclick="showToast('Opening bulk upload modal...')">
                    <i class="fas fa-upload"></i> BULK UPLOAD
                </button>
                <button style="border: 1px solid #8b5cf6; background: #fff; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;" onclick="showToast('Accounts settings')">
                    <i class="fas fa-cog" style="color: #8b5cf6; font-size: 14px;"></i>
                </button>
            </div>
            <div style="display: flex; gap: 12px; align-items: center; margin-left: auto;">
                <div style="display: flex; flex-direction: column; position: relative;">
                    <label style="font-size: 10px; font-weight: 700; color: var(--t2); margin-bottom: 2px; position: absolute; top: -14px; left: 0;">Search</label>
                    <div style="border: 1px solid var(--border); border-radius: 4px; padding: 8px 12px; display: flex; align-items: center; gap: 8px; background: #fff; width: 220px; height: 34px; box-sizing: border-box;">
                        <i class="fas fa-search" style="color: var(--t3); font-size: 12px;"></i>
                        <input type="text" id="incomeSearchInput" onkeyup="filterIncomeTable()" placeholder="Search by Income Name" style="border: none; outline: none; font-size: 11.5px; width: 100%; font-family: inherit;">
                    </div>
                </div>
                <button style="border: 1px solid #8b5cf6; background: #fff; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;" onclick="showToast('Filtering income records...')">
                    <i class="fas fa-filter" style="color: #8b5cf6; font-size: 14px;"></i>
                </button>
                <button style="border: none; background: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; padding: 4px;" onclick="openDrawer('total_collection')">
                    <i class="fas fa-sync-alt" style="color: var(--t2); font-size: 14px;"></i>
                </button>
            </div>
        </div>

        <div class="drawer-table-wrap" style="border-radius: 4px; overflow: hidden; border: 1px solid var(--border);">
            <table class="drawer-table-complex" style="text-align: left; width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #0b4c46;">
                        <th style="color: #fff; padding: 12px 16px; font-size: 12px; font-weight: 700; font-family: 'Plus Jakarta Sans', sans-serif;">Receipt ID</th>
                        <th style="color: #fff; padding: 12px 16px; font-size: 12px; font-weight: 700; font-family: 'Plus Jakarta Sans', sans-serif;">Income Name</th>
                        <th style="color: #fff; padding: 12px 16px; font-size: 12px; font-weight: 700; font-family: 'Plus Jakarta Sans', sans-serif;">Date</th>
                        <th style="color: #fff; padding: 12px 16px; font-size: 12px; font-weight: 700; font-family: 'Plus Jakarta Sans', sans-serif;">Category</th>
                        <th style="color: #fff; padding: 12px 16px; font-size: 12px; font-weight: 700; font-family: 'Plus Jakarta Sans', sans-serif;">Sub-Category</th>
                        <th style="color: #fff; padding: 12px 16px; font-size: 12px; font-weight: 700; font-family: 'Plus Jakarta Sans', sans-serif;">Income Amount</th>
                        <th style="color: #fff; padding: 12px 16px; font-size: 12px; font-weight: 700; font-family: 'Plus Jakarta Sans', sans-serif;">Payment Mode</th>
                    </tr>
                </thead>
                <tbody id="incomeTableBody">
                </tbody>
            </table>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;font-size:11px;color:var(--t2);font-family: 'Plus Jakarta Sans', sans-serif;">
            <span style="background: #fff; border: 1px solid var(--border); border-radius: 4px; padding: 6px 12px; font-weight: 600; color: var(--t1);" id="incomeTotalRowsText">Total Rows: 0</span>
            <div style="display:flex;gap:8px; align-items: center;">
                <button style="border: 1px solid var(--border); background: #fff; width: 26px; height: 26px; border-radius: 4px; display: flex; align-items: center; justify-content: center; cursor: pointer;" onclick="showToast('Previous page')">
                    <i class="fas fa-chevron-left" style="color: var(--t2); font-size: 10px;"></i>
                </button>
                <span style="width: 26px; height: 26px; border-radius: 50%; background: rgba(16, 185, 129, 0.15); color: #10b981; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 11px;">1</span>
                <button style="border: 1px solid var(--border); background: #fff; width: 26px; height: 26px; border-radius: 4px; display: flex; align-items: center; justify-content: center; cursor: pointer;" onclick="showToast('Next page')">
                    <i class="fas fa-chevron-right" style="color: var(--t2); font-size: 10px;"></i>
                </button>
            </div>
        </div>
    `;

    filterIncomeTable();
}

function updateRowAmount(el) {
    const idx = parseInt(el.getAttribute('data-index'));
    let val = el.textContent.trim();
    let parsed = parseFloat(val.replace(/[^0-9.]/g, ''));
    if (isNaN(parsed)) {
        parsed = 0;
    }
    incomeRowsData[idx].amount = '₹ ' + parsed.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    el.textContent = incomeRowsData[idx].amount;
    recalculateTotalIncome();
}

function updateRowPaymentMode(el) {
    const idx = parseInt(el.getAttribute('data-index'));
    let val = el.textContent.trim();
    incomeRowsData[idx].payment_mode = val || '—';
    el.textContent = incomeRowsData[idx].payment_mode;
    recalculateTotalIncome();
}

function checkAmountEnter(e, el) {
    if (e.key === 'Enter') {
        e.preventDefault();
        el.blur();
    }
}

function updateCustomTotals() {
    const cashEl = document.getElementById('editableCash');
    const bankEl = document.getElementById('editableBank');
    
    let cashVal = parseFloat(cashEl.textContent.replace(/[^0-9.]/g, '')) || 0;
    let bankVal = parseFloat(bankEl.textContent.replace(/[^0-9.]/g, '')) || 0;
    
    let total = cashVal + bankVal;
    
    const cashStr = '₹ ' + cashVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const bankStr = '₹ ' + bankVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const totalStr = '₹ ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    
    cashEl.textContent = cashStr;
    bankEl.textContent = bankStr;
    
    document.getElementById('summaryTotalIncome').textContent = totalStr;
    document.getElementById('editableProfitLoss').textContent = totalStr;
    
    const badge = document.getElementById('incomeModuleBadgeText');
    if (badge) {
        badge.textContent = `Total Income Added in Income Module: ${totalStr}`;
    }
}

function updateCustomProfitLoss(el) {
    let val = parseFloat(el.textContent.replace(/[^0-9.]/g, '')) || 0;
    const valStr = '₹ ' + val.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    el.textContent = valStr;
    
    document.getElementById('summaryTotalIncome').textContent = valStr;
    document.getElementById('editableCash').textContent = valStr;
    document.getElementById('editableBank').textContent = '₹ 0.00';
    
    const badge = document.getElementById('incomeModuleBadgeText');
    if (badge) {
        badge.textContent = `Total Income Added in Income Module: ${valStr}`;
    }
}

function recalculateTotalIncome() {
    let totalCash = 0;
    let totalBank = 0;
    incomeRowsData.forEach(item => {
        let amt = parseFloat(item.amount.replace(/[^0-9.]/g, ''));
        if (!isNaN(amt)) {
            if (item.payment_mode && item.payment_mode.toLowerCase().includes('cash')) {
                totalCash += amt;
            } else {
                totalBank += amt;
            }
        }
    });

    let totalIncome = totalCash + totalBank;
    const totalIncomeStr = '₹ ' + totalIncome.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const totalCashStr = '₹ ' + totalCash.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const totalBankStr = '₹ ' + totalBank.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    document.getElementById('editableProfitLoss').textContent = totalIncomeStr;
    document.getElementById('editableCash').textContent = totalCashStr;
    document.getElementById('editableBank').textContent = totalBankStr;
    document.getElementById('summaryTotalIncome').textContent = totalIncomeStr;
    
    const badge = document.getElementById('incomeModuleBadgeText');
    if (badge) {
        badge.textContent = `Total Income Added in Income Module: ${totalIncomeStr}`;
    }
}

function filterIncomeTable() {
    const searchVal = document.getElementById('incomeSearchInput').value.toLowerCase();
    const showInstFee = document.getElementById('showInstituteFeeToggle').checked;

    let filtered = incomeRowsData;

    // Filter by toggle
    if (!showInstFee) {
        filtered = filtered.filter(r => !r.student.toLowerCase().includes('school') && !r.receipt_id.toLowerCase().includes('rec'));
    }

    if (searchVal) {
        filtered = filtered.filter(r => 
            (r.receipt_id && r.receipt_id.toLowerCase().includes(searchVal)) ||
            (r.student && r.student.toLowerCase().includes(searchVal)) ||
            ((r.income_name || 'school fee').toLowerCase().includes(searchVal)) ||
            (r.payment_mode && r.payment_mode.toLowerCase().includes(searchVal))
        );
    }

    let rowsHtml = '';
    filtered.forEach(r => {
        // Format date string YYYY-MM-DD to DD/MM/YYYY
        let formattedDate = '-';
        if (r.date) {
            const parts = r.date.split('-');
            if (parts.length === 3) {
                formattedDate = `${parts[2]}/${parts[1]}/${parts[0]}`;
            } else {
                formattedDate = r.date;
            }
        }

        rowsHtml += `
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 12px 16px; font-size: 12px; color: var(--t2);">${r.receipt_id || '-'}</td>
                <td style="padding: 12px 16px; font-size: 12px; font-weight: 600; color: var(--navy);">${r.income_name || 'School Fee'}</td>
                <td style="padding: 12px 16px; font-size: 12px; color: var(--t1);">${formattedDate}</td>
                <td style="padding: 12px 16px; font-size: 12px; color: var(--t2);">${r.category || 'Fee'}</td>
                <td style="padding: 12px 16px; font-size: 12px; color: var(--t2);">${r.sub_category || '-'}</td>
                <td style="padding: 12px 16px; font-size: 12px; font-weight: 700; color: var(--t1);">
                    <span contenteditable="true" class="editable-amount" data-index="${incomeRowsData.indexOf(r)}" style="border-bottom: 1px dashed var(--gold); padding: 2px 4px; outline: none; cursor: text;" onblur="updateRowAmount(this)" onkeydown="checkAmountEnter(event, this)">${r.amount}</span>
                </td>
                <td style="padding: 12px 16px; font-size: 12px; color: var(--t2);">
                    <span contenteditable="true" class="editable-payment-mode" data-index="${incomeRowsData.indexOf(r)}" style="border-bottom: 1px dashed var(--gold); padding: 2px 4px; outline: none; cursor: text;" onblur="updateRowPaymentMode(this)" onkeydown="checkAmountEnter(event, this)">${r.payment_mode || '-'}</span>
                </td>
            </tr>
        `;
    });

    if (filtered.length === 0) {
        rowsHtml = `<tr><td colspan="7" style="text-align:center;padding:20px;color:var(--t3);">No income records found</td></tr>`;
    }

    document.getElementById('incomeTableBody').innerHTML = rowsHtml;
    document.getElementById('incomeTotalRowsText').textContent = `Total Rows: ${filtered.length}`;
}

let studentAttendanceRowsData = [];

function renderStudentAttendanceDrawer(data) {
    studentAttendanceRowsData = data || [];
    
    // Extract unique classes
    const uniqueClasses = [...new Set(studentAttendanceRowsData.map(r => r.class).filter(Boolean))];
    let classOptions = '<option value="">All Classes</option>';
    uniqueClasses.forEach(cls => {
        classOptions += `<option value="${cls}">${cls}</option>`;
    });

    const body = document.getElementById('drawerBody');
    body.innerHTML = `
        <div class="drawer-search-row" style="margin-bottom:12px; display:flex; gap:8px; flex-wrap:wrap; align-items:center; background:none; border:none; padding:0;">
            <div class="drawer-search-box" style="flex:1; min-width:180px;">
                <i class="fas fa-search"></i>
                <input type="text" id="stdAttendanceSearchInput" onkeyup="filterStudentAttendanceTable()" placeholder="Search by name or roll no...">
            </div>
            <select class="drawer-select" id="stdAttendanceFilterClass" onchange="filterStudentAttendanceTable()" style="width:130px; margin:0; height:34px; font-size:11.5px;">
                ${classOptions}
            </select>
            <select class="drawer-select" id="stdAttendanceFilterStatus" onchange="filterStudentAttendanceTable()" style="width:130px; margin:0; height:34px; font-size:11.5px;">
                <option value="">All Statuses</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="halfday">Halfday</option>
                <option value="leave">Leave</option>
                <option value="not marked">Not Marked</option>
            </select>
        </div>

        <div class="drawer-table-wrap">
            <table class="drawer-table-complex" style="text-align: left;">
                <thead>
                    <tr>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody id="studentAttendanceTableBody">
                </tbody>
            </table>
        </div>
    `;

    filterStudentAttendanceTable();
}

function filterStudentAttendanceTable() {
    const searchQuery = document.getElementById('stdAttendanceSearchInput').value.toLowerCase();
    const selClass = document.getElementById('stdAttendanceFilterClass').value;
    const selStatus = document.getElementById('stdAttendanceFilterStatus').value;

    let filtered = studentAttendanceRowsData;

    if (searchQuery) {
        filtered = filtered.filter(r => 
            (r.name && r.name.toLowerCase().includes(searchQuery)) || 
            (r.roll && r.roll.toString().toLowerCase().includes(searchQuery))
        );
    }

    if (selClass) {
        filtered = filtered.filter(r => r.class === selClass);
    }

    if (selStatus) {
        filtered = filtered.filter(r => {
            const st = (r.status || '').toLowerCase().replace('_', '');
            const targetStatus = selStatus.toLowerCase().replace(' ', '');
            return st === targetStatus;
        });
    }

    let rowsHtml = '';
    filtered.forEach(item => {
        let badgeClass = 'bg-not-marked';
        const st = (item.status || '').toLowerCase().replace('_', '');
        if (st === 'present') badgeClass = 'bg-active';
        else if (st === 'absent') badgeClass = 'bg-inactive';
        else if (st === 'halfday' || st === 'leave') badgeClass = 'bg-pending';

        rowsHtml += `
            <tr>
                <td>${item.roll || '-'}</td>
                <td><strong>${item.name}</strong></td>
                <td>${item.class || '-'}</td>
                <td><span class="drawer-badge ${badgeClass}">${item.status || 'Not Marked'}</span></td>
                <td>${item.remark || '-'}</td>
            </tr>
        `;
    });

    if (filtered.length === 0) {
        rowsHtml = `<tr><td colspan="5" style="text-align:center;color:var(--t3);padding:20px;">No records match the filters.</td></tr>`;
    }

    document.getElementById('studentAttendanceTableBody').innerHTML = rowsHtml;
}

let staffAttendanceRowsData = [];

function renderStaffAttendanceDrawer(data) {
    staffAttendanceRowsData = data || [];

    const body = document.getElementById('drawerBody');
    body.innerHTML = `
        <div class="drawer-search-row" style="margin-bottom:12px; display:flex; gap:8px; flex-wrap:wrap; align-items:center; background:none; border:none; padding:0;">
            <div class="drawer-search-box" style="flex:1; min-width:180px;">
                <i class="fas fa-search"></i>
                <input type="text" id="stfAttendanceSearchInput" onkeyup="filterStaffAttendanceTable()" placeholder="Search by staff name or role...">
            </div>
            <select class="drawer-select" id="stfAttendanceFilterStatus" onchange="filterStaffAttendanceTable()" style="width:150px; margin:0; height:34px; font-size:11.5px;">
                <option value="">All Statuses</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="halfday">Halfday</option>
                <option value="leave">Leave</option>
                <option value="not marked">Not Marked</option>
            </select>
        </div>

        <div class="drawer-table-wrap">
            <table class="drawer-table-complex" style="text-align: left;">
                <thead>
                    <tr>
                        <th>Staff Name</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Punch In Time</th>
                    </tr>
                </thead>
                <tbody id="staffAttendanceTableBody">
                </tbody>
            </table>
        </div>
    `;

    filterStaffAttendanceTable();
}

function filterStaffAttendanceTable() {
    const searchQuery = document.getElementById('stfAttendanceSearchInput').value.toLowerCase();
    const selStatus = document.getElementById('stfAttendanceFilterStatus').value;

    let filtered = staffAttendanceRowsData;

    if (searchQuery) {
        filtered = filtered.filter(r => 
            (r.name && r.name.toLowerCase().includes(searchQuery)) || 
            (r.role && r.role.toLowerCase().includes(searchQuery))
        );
    }

    if (selStatus) {
        filtered = filtered.filter(r => {
            const st = (r.status || '').toLowerCase().replace('_', '');
            const targetStatus = selStatus.toLowerCase().replace(' ', '');
            return st === targetStatus;
        });
    }

    let rowsHtml = '';
    filtered.forEach(item => {
        let badgeClass = 'bg-not-marked';
        const st = (item.status || '').toLowerCase().replace('_', '');
        if (st === 'present') badgeClass = 'bg-active';
        else if (st === 'absent') badgeClass = 'bg-inactive';
        else if (st === 'halfday' || st === 'leave') badgeClass = 'bg-pending';

        rowsHtml += `
            <tr>
                <td><strong>${item.name}</strong></td>
                <td>${item.role || '-'}</td>
                <td><span class="drawer-badge ${badgeClass}">${item.status || 'Not Marked'}</span></td>
                <td>${item.punch_in || '-'}</td>
            </tr>
        `;
    });

    if (filtered.length === 0) {
        rowsHtml = `<tr><td colspan="4" style="text-align:center;color:var(--t3);padding:20px;">No records match the filters.</td></tr>`;
    }

    document.getElementById('staffAttendanceTableBody').innerHTML = rowsHtml;
}

function renderDrawerContent(type, data) {
    const body = document.getElementById('drawerBody');
    const drawer = document.getElementById('sideDrawer');
    
    // Reset header class and backgrounds
    drawer.querySelector('.drawer-header').className = 'drawer-header';
    drawer.querySelector('.drawer-header').removeAttribute('style');

    if (!data || data.length === 0) {
        body.innerHTML = `
            <div class="drawer-empty">
                <i class="fas fa-folder-open"></i>
                <span>No records found.</span>
            </div>
        `;
        return;
    }

    if (type === 'students' || type === 'staffs' || type === 'send_reminder' || type === 'class_fee_report' || type === 'total_collection' || type === 'income') {
        drawer.querySelector('.drawer-header').style.background = '#8b5cf6';
    }

    if (type === 'income') {
        renderTotalIncomeDrawer(data);
        return;
    }

    if (type === 'send_reminder') {
        let classesHtml = '';
        if (data.classes) {
            data.classes.forEach(c => {
                classesHtml += `<option value="${c.id}">${c.name}</option>`;
            });
        }

        // Build pending students list
        const students = data.pendingStudents || [];
        const overdueCount  = students.filter(s => s.overdue).length;
        const paidCount     = students.filter(s => !s.overdue).length;

        let studentsHtml = '';
        students.forEach(s => {
            const overdueBadge = s.overdue
                ? `<span style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:20px;padding:2px 8px;font-size:10px;font-weight:700;letter-spacing:.4px;">OVERDUE</span>`
                : `<span style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;border-radius:20px;padding:2px 8px;font-size:10px;font-weight:700;letter-spacing:.4px;">PAID</span>`;

            const dueColor = s.overdue ? '#dc2626' : '#16a34a';
            const rowBg    = s.overdue ? '' : 'background:rgba(22,163,74,.04);';

            studentsHtml += `
            <div class="reminder-student-row" data-id="${s.id}" style="display:flex;align-items:center;gap:12px;padding:10px 14px;border-bottom:1px solid var(--border);${rowBg}">
                <input type="checkbox" class="reminder-chk" data-id="${s.id}" ${s.overdue ? 'checked' : ''} style="width:15px;height:15px;accent-color:#8b5cf6;cursor:pointer;flex-shrink:0;">
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <span style="font-weight:700;font-size:13px;color:var(--t1);">${s.name}</span>
                        ${overdueBadge}
                    </div>
                    <div style="font-size:11px;color:var(--t2);margin-top:2px;">
                        ${s.roll} &bull; ${s.class} – Sec ${s.section}
                        ${s.due_date !== '—' ? `&bull; Due: <span style="color:#d97706;font-weight:600;">${s.due_date}</span>` : ''}
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-size:13px;font-weight:800;color:${dueColor};">${s.due}</div>
                    <div style="font-size:10px;color:var(--t3);">of ${s.total_fee}</div>
                </div>
            </div>`;
        });

        body.innerHTML = `
            <div style="padding-bottom: 80px;">
                <!-- Top Controls -->
                <div class="reminder-option-row">
                    <label class="reminder-option-item">
                        Send reminder to all students
                        <label class="switch-wrapper">
                            <input type="checkbox" id="sendAllReminder" checked onchange="toggleSendAllSelector(this)">
                            <span class="switch-slider"></span>
                        </label>
                    </label>
                    <label class="reminder-option-item">
                        Previous year pending dues
                        <label class="switch-wrapper">
                            <input type="checkbox" id="sendPrevReminder">
                            <span class="switch-slider"></span>
                        </label>
                    </label>
                    <div style="flex: 1; text-align: right;">
                        <button class="drawer-btn-logs" style="border-color:#d97706; color:#d97706;" onclick="showToast('Displaying reminder logs...')">SHOW LOGS</button>
                    </div>
                </div>

                <div class="reminder-or-divider">OR</div>

                <!-- Class / Section Selector -->
                <div class="reminder-selector-row" style="opacity:.45;">
                    <div class="drawer-select-group" style="flex: 1;">
                        <label>Select Class</label>
                        <select id="reminderClass" class="drawer-select" disabled>
                            <option value="">Select Class</option>
                            ${classesHtml}
                        </select>
                    </div>
                    <div class="drawer-select-group" style="flex: 1;">
                        <label>Select Section</label>
                        <select id="reminderSection" class="drawer-select" disabled>
                            <option value="">Select Section</option>
                        </select>
                    </div>
                    <button class="drawer-btn-download" style="border-color:#d97706; color:#d97706; margin-top: 15px;" onclick="addReminderClass()">+ ADD CLASS</button>
                </div>

                <!-- Summary Stats Bar -->
                <div style="display:flex;gap:10px;padding:12px 14px;background:var(--sidebar-bg, #f8f9fa);border-top:1px solid var(--border);border-bottom:1px solid var(--border);margin-top:12px;">
                    <div style="flex:1;text-align:center;">
                        <div style="font-size:20px;font-weight:800;color:#dc2626;">${overdueCount}</div>
                        <div style="font-size:10px;color:var(--t2);font-weight:600;">OVERDUE</div>
                    </div>
                    <div style="flex:1;text-align:center;border-left:1px solid var(--border);border-right:1px solid var(--border);">
                        <div style="font-size:20px;font-weight:800;color:#16a34a;">${paidCount}</div>
                        <div style="font-size:10px;color:var(--t2);font-weight:600;">PAID</div>
                    </div>
                    <div style="flex:1;text-align:center;">
                        <div style="font-size:20px;font-weight:800;color:#8b5cf6;">${students.length}</div>
                        <div style="font-size:10px;color:var(--t2);font-weight:600;">TOTAL</div>
                    </div>
                </div>

                <!-- Search Bar + Select All -->
                <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-bottom:1px solid var(--border);">
                    <input type="text" id="reminderSearch" placeholder="🔍 Search student..." oninput="filterReminderStudents(this.value)"
                        style="flex:1;padding:7px 12px;border:1px solid var(--border);border-radius:8px;font-size:12px;background:var(--card-bg,#fff);color:var(--t1);outline:none;">
                    <label style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:var(--t2);cursor:pointer;white-space:nowrap;">
                        <input type="checkbox" id="selectAllReminder" checked onchange="toggleSelectAllReminder(this)" style="accent-color:#8b5cf6;width:14px;height:14px;">
                        Select All
                    </label>
                </div>

                <!-- Student List -->
                <div id="reminderStudentList">
                    ${studentsHtml || `<div style="padding:30px;text-align:center;color:var(--t3);"><i class="fas fa-check-circle" style="font-size:32px;color:#16a34a;margin-bottom:8px;display:block;"></i>No pending dues found!</div>`}
                </div>
            </div>

            <div class="reminder-bottom-bar">
                <span id="reminderSelectedCount" style="font-size:12px;color:var(--t2);font-weight:600;">${overdueCount} selected</span>
                <button class="btn-send-now" onclick="triggerSendReminder()"><i class="fas fa-paper-plane"></i> SEND</button>
            </div>
        `;

        // Class change → section populate
        const classSelect = document.getElementById('reminderClass');
        const sectionSelect = document.getElementById('reminderSection');
        if (classSelect) {
            classSelect.addEventListener('change', () => {
                const classId = classSelect.value;
                sectionSelect.innerHTML = '<option value="">Select Section</option>';
                if (classId && data.classes) {
                    const cls = data.classes.find(c => c.id == classId);
                    if (cls && cls.sections) {
                        cls.sections.forEach(s => {
                            sectionSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`;
                        });
                    }
                }
            });
        }

        // Update selected count on checkbox change
        document.querySelectorAll('.reminder-chk').forEach(chk => {
            chk.addEventListener('change', updateReminderCount);
        });

        return;
    }

    if (type === 'students') {
        renderStudentsDrawer(data);
        return;
    }

    if (type === 'staffs') {
        renderStaffsDrawer(data);
        return;
    }

    if (type === 'class_fee_report') {
        renderClassFeeReportDrawer(data);
        return;
    }

    let html = '<table class="drawer-table"><thead><tr>';
    
    if (type === 'income' || type === 'total_collection' || type === 'today_collection') {
        html += '<th>Receipt ID</th><th>Student / Class</th><th>Amount</th><th>Date</th><th>Status</th>';
        html += '</tr></thead><tbody>';
        data.forEach(item => {
            const badgeClass = item.status === 'Paid' ? 'bg-paid' : 'bg-partial';
            html += `<tr>
                <td><strong>${item.receipt_id}</strong></td>
                <td>${item.student}</td>
                <td style="color:var(--green);font-weight:700;">${item.amount}</td>
                <td>${item.date}</td>
                <td><span class="drawer-badge ${badgeClass}">${item.status}</span></td>
            </tr>`;
        });
    } else if (type === 'expense') {
        html += '<th>Expense ID</th><th>Category</th><th>Amount</th><th>Date</th><th>Status</th>';
        html += '</tr></thead><tbody>';
        data.forEach(item => {
            html += `<tr>
                <td><strong>${item.expense_id}</strong></td>
                <td>${item.category}</td>
                <td style="color:var(--red);font-weight:700;">${item.amount}</td>
                <td>${item.date}</td>
                <td><span class="drawer-badge bg-paid">${item.status}</span></td>
            </tr>`;
        });
    } else if (type === 'student_attendance') {
        renderStudentAttendanceDrawer(data);
        return;
    } else if (type === 'staff_attendance') {
        renderStaffAttendanceDrawer(data);
        return;
    } else if (type === 'fee_pending') {
        html += '<th>Student</th><th>Class</th><th>Total Fee</th><th>Paid</th><th>Due</th><th>Due Date</th>';
        html += '</tr></thead><tbody>';
        data.forEach(item => {
            html += `<tr>
                <td><strong>${item.name}</strong></td>
                <td>${item.class}</td>
                <td>${item.total_fee}</td>
                <td style="color:var(--green);">${item.paid}</td>
                <td style="color:var(--red);font-weight:700;">${item.due}</td>
                <td>${item.due_date}</td>
            </tr>`;
        });
    } else if (type === 'admissions') {
        html += '<th>Student Candidate</th><th>Parent / Phone</th><th>Class interested</th><th>Status</th>';
        html += '</tr></thead><tbody>';
        data.forEach(item => {
            let badgeClass = 'bg-not-marked';
            const st = item.status.toLowerCase();
            if (st === 'admission') badgeClass = 'bg-active';
            if (st === 'payment' || st === 'evaluation') badgeClass = 'bg-pending';
            if (st === 'enquiry') badgeClass = 'bg-not-marked';
            if (st === 'application') badgeClass = 'bg-partial';

            html += `<tr>
                <td><strong>${item.name}</strong></td>
                <td>${item.parent}<br><small style="color:var(--t2);">${item.phone}</small></td>
                <td>${item.class}</td>
                <td><span class="drawer-badge ${badgeClass}">${item.status}</span></td>
            </tr>`;
        });
    } else if (type === 'calendar_events') {
        html += '<th>Event / Birthday Name</th><th>Type</th><th>Time</th><th>Details</th>';
        html += '</tr></thead><tbody>';
        data.forEach(item => {
            let badgeClass = 'bg-not-marked';
            const ty = item.type.toLowerCase();
            if (ty.includes('birthday')) badgeClass = 'bg-partial';
            if (ty.includes('event') || ty.includes('holiday')) badgeClass = 'bg-active';
            
            html += `<tr>
                <td><strong>${item.event_name}</strong></td>
                <td><span class="drawer-badge ${badgeClass}">${item.type}</span></td>
                <td>${item.time}</td>
                <td>${item.details}</td>
                </tr>`;
        });
    } else {
        html += '<th>Property</th><th>Value</th>';
        html += '</tr></thead><tbody>';
        Object.keys(data[0] || {}).forEach(k => {
            html += `<tr><td>${k}</td><td>${data[0][k]}</td></tr>`;
        });
    }

    html += '</tbody></table>';
    if (type === 'student_attendance') {
        html += `<div style="margin-top:20px; display:flex; gap:10px; justify-content:center; padding:10px;">
            <a href="/school/attendance/students" style="background:#2563eb; padding:8px 14px; font-size:12px; font-weight:700; text-decoration:none; border-radius:6px; color:#fff; display:inline-flex; align-items:center; gap:6px;"><i class="fas fa-user-check"></i> Mark Daily Attendance</a>
            <a href="/school/student-mgmt/bulk-attendance" style="background:#9a3412; padding:8px 14px; font-size:12px; font-weight:700; text-decoration:none; border-radius:6px; color:#fff; display:inline-flex; align-items:center; gap:6px;"><i class="fas fa-calendar-days"></i> Mark Bulk Attendance</a>
        </div>`;
    } else if (type === 'staff_attendance') {
        html += `<div style="margin-top:20px; display:flex; gap:10px; justify-content:center; padding:10px;">
            <a href="/school/attendance/staff" style="background:#2563eb; padding:8px 14px; font-size:12px; font-weight:700; text-decoration:none; border-radius:6px; color:#fff; display:inline-flex; align-items:center; gap:6px;"><i class="fas fa-user-check"></i> Mark Staff Attendance</a>
            <a href="/school/staff/bulk-attendance" style="background:#9a3412; padding:8px 14px; font-size:12px; font-weight:700; text-decoration:none; border-radius:6px; color:#fff; display:inline-flex; align-items:center; gap:6px;"><i class="fas fa-calendar-days"></i> Mark Bulk Staff</a>
        </div>`;
    }
    body.innerHTML = html;
}

function sendReminder() {
    openDrawer('send_reminder');
}

// ── HEADCOUNT BREAKDOWN PIE CHARTS (CHART.JS) ─────────────────────────────────
const STUDENT_MALE = {{ (int)$studentMaleCount }};
const STUDENT_FEMALE = {{ (int)$studentFemaleCount }};
const STUDENT_NOT_MAPPED = {{ (int)$studentNotMappedCount }};

const STAFF_MAPPED = {{ (int)($totalStaffs - $staffNotMappedCount) }};
const STAFF_NOT_MAPPED = {{ (int)$staffNotMappedCount }};

let studentsChartInst = null;
let staffsChartInst = null;
let admissionChartInst = null;

let ADM_ENQUIRY = {{ (int)$admissionEnquiry }};
let ADM_APPLICATION = {{ (int)$admissionApplication }};
let ADM_PAYMENT = {{ (int)$admissionPayment }};
let ADM_EVALUATION = {{ (int)$admissionEvaluation }};
let ADM_COUNT = {{ (int)$admissionCount }};

function drawAdmissionLineChart() {
    const canvas = document.getElementById('admissionCountLineChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    if (admissionChartInst) admissionChartInst.destroy();
    
    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 140);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.35)');
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

    const isDark = document.body.classList.contains('dark-mode');
    const labelColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : '#f1f5f9';

    // Custom text plugin to render numbers above nodes
    const textPlugin = {
        id: 'textPlugin',
        afterDatasetsDraw(chart) {
            const { ctx, data } = chart;
            ctx.save();
            ctx.font = 'bold 11px "Plus Jakarta Sans", sans-serif';
            ctx.fillStyle = document.body.classList.contains('dark-mode') ? '#ffffff' : '#0f172a';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'bottom';
            
            chart.getDatasetMeta(0).data.forEach((point, index) => {
                const val = data.datasets[0].data[index];
                ctx.fillText(val, point.x, point.y - 8);
            });
            ctx.restore();
        }
    };

    admissionChartInst = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Enquiry', 'Application', 'Payment', 'Evaluation', 'Admission'],
            datasets: [{
                data: [ADM_ENQUIRY, ADM_APPLICATION, ADM_PAYMENT, ADM_EVALUATION, ADM_COUNT],
                borderColor: '#8b5cf6',
                borderWidth: 2.5,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#8b5cf6',
                pointBorderColor: isDark ? '#11152d' : '#ffffff',
                pointBorderWidth: 1.5,
                pointRadius: 4.5,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            return 'Count: ' + context.raw;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: labelColor,
                        font: { size: 9, family: 'Plus Jakarta Sans', weight: '600' }
                    }
                },
                y: {
                    min: 0,
                    suggestedMax: 2,
                    grid: { color: gridColor },
                    ticks: {
                        stepSize: 1,
                        color: labelColor,
                        font: { size: 9, family: 'Plus Jakarta Sans' }
                    }
                }
            }
        },
        plugins: [textPlugin]
    });
}

function drawHeadcountPieCharts() {
    const stdCanvas = document.getElementById('studentsPieChart');
    if (stdCanvas) {
        const stdCtx = stdCanvas.getContext('2d');
        if (studentsChartInst) studentsChartInst.destroy();
        
        studentsChartInst = new Chart(stdCtx, {
            type: 'doughnut',
            data: {
                labels: ['Male', 'Female', 'Not Mapped'],
                datasets: [{
                    data: [STUDENT_MALE, STUDENT_FEMALE, STUDENT_NOT_MAPPED],
                    backgroundColor: ['#3b82f6', '#ec4899', '#64748b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = context.raw;
                                const total = STUDENT_MALE + STUDENT_FEMALE + STUDENT_NOT_MAPPED;
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${val} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    const stfCanvas = document.getElementById('staffsPieChart');
    if (stfCanvas) {
        const stfCtx = stfCanvas.getContext('2d');
        if (staffsChartInst) staffsChartInst.destroy();

        staffsChartInst = new Chart(stfCtx, {
            type: 'doughnut',
            data: {
                labels: ['Mapped', 'Not Mapped'],
                datasets: [{
                    data: [STAFF_MAPPED, STAFF_NOT_MAPPED],
                    backgroundColor: ['#10b981', '#64748b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = context.raw;
                                const total = STAFF_MAPPED + STAFF_NOT_MAPPED;
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${val} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
}

// ── INCOME & EXPENSE CHART (CHART.JS) ─────────────────────────────────────────
let incExpInst = null;
function drawIncomeExpenseChart() {
    const canvasEl = document.getElementById('incomeExpenseChart');
    if (!canvasEl) return;
    const ctx = canvasEl.getContext('2d');
    if (incExpInst) incExpInst.destroy();
    
    const isDark = document.body.classList.contains('dark-mode');
    const labelColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : '#f1f5f9';

    // Gradients matching screen mockup aesthetics
    const incomeGrad = ctx.createLinearGradient(0, 200, 0, 0);
    incomeGrad.addColorStop(0, '#f97316'); // Orange at bottom
    incomeGrad.addColorStop(1, '#fbbf24'); // Yellow/warm glow at top
    
    const expenseGrad = ctx.createLinearGradient(0, 200, 0, 0);
    expenseGrad.addColorStop(0, '#ef4444'); // Red at bottom
    expenseGrad.addColorStop(1, '#fca5a5'); // Light pink/red at top
    
    incExpInst = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: MONTHS_LABELS,
            datasets: [
                {
                    label: 'Income',
                    data: INCOME_DATA,
                    backgroundColor: incomeGrad,
                    borderRadius: { topLeft: 4, topRight: 4, bottomLeft: 0, bottomRight: 0 },
                    borderSkipped: false,
                    barThickness: 22,
                    maxBarThickness: 26
                },
                {
                    label: 'Expense',
                    data: EXPENSE_DATA,
                    backgroundColor: expenseGrad,
                    borderRadius: { topLeft: 4, topRight: 4, bottomLeft: 0, bottomRight: 0 },
                    borderSkipped: false,
                    barThickness: 22,
                    maxBarThickness: 26
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 10,
                            family: 'Plus Jakarta Sans',
                            weight: '600'
                        },
                        color: labelColor
                    }
                },
                y: {
                    min: 0,
                    max: 120000,
                    ticks: {
                        stepSize: 20000,
                        font: {
                            size: 10,
                            family: 'Plus Jakarta Sans',
                            weight: '500'
                        },
                        color: labelColor,
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    },
                    grid: {
                        color: gridColor,
                        borderDash: [4, 4],
                        drawTicks: false
                    }
                }
            }
        }
    });
}

// ── DROPDOWNS ─────────────────────────────────────────────────────────────────
function toggleDrop(id){
    ['notifDrop','userDrop'].forEach(d=>{
        const el = document.getElementById(d);
        if(el && d!==id) el.classList.remove('open');
    });
    const targetEl = document.getElementById(id);
    if(targetEl) targetEl.classList.toggle('open');
}
document.addEventListener('click',e=>{
    const nd = document.getElementById('notifDrop');
    if(nd && !e.target.closest('.notif-wrap')) nd.classList.remove('open');
    const ud = document.getElementById('userDrop');
    if(ud && !e.target.closest('.user-wrap')) ud.classList.remove('open');
});

document.addEventListener('DOMContentLoaded', () => {
    // Request notification permission if supported
    if (typeof Notification !== 'undefined' && Notification.permission !== 'granted' && Notification.permission !== 'denied') {
        Notification.requestPermission();
    }

    try { renderCalendarGrid(); } catch (e) { console.error('Error rendering calendar grid:', e); }
    try { drawIncomeExpenseChart(); } catch (e) { console.error('Error drawing income expense chart:', e); }
    try { drawHeadcountPieCharts(); } catch (e) { console.error('Error drawing headcount pie charts:', e); }
    try { drawAdmissionLineChart(); } catch (e) { console.error('Error drawing admission line chart:', e); }
    try { loadCalendarMonthEvents(); } catch (e) { console.error('Error loading calendar events:', e); }
    try { switchUpdateTab('notice'); } catch (e) { console.error('Error switching update tab:', e); }
    
    // Sidebar accordion toggles and active menu expansion are handled globally in layouts/app.blade.php.

    // Info Popover Tooltip system
    const popover = document.createElement('div');
    popover.className = 'info-popover';
    document.body.appendChild(popover);

    let activeTrigger = null;

    function hidePopover() {
        popover.classList.remove('show');
        activeTrigger = null;
    }

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.fa-circle-info[data-info]');
        if (btn) {
            e.stopPropagation();
            if (activeTrigger === btn) {
                hidePopover();
                return;
            }

            activeTrigger = btn;
            popover.textContent = btn.getAttribute('data-info');
            popover.classList.add('show');

            // Position calculation
            const rect = btn.getBoundingClientRect();
            const popoverRect = popover.getBoundingClientRect();
            
            const left = rect.left + window.scrollX + (rect.width / 2) - (popoverRect.width / 2);
            const top = rect.top + window.scrollY - popoverRect.height - 8;
            
            popover.style.left = `${Math.max(10, left)}px`;
            popover.style.top = `${top}px`;
            return;
        }

        if (!e.target.closest('.info-popover')) {
            hidePopover();
        }
    });

    window.addEventListener('resize', hidePopover);
    window.addEventListener('scroll', hidePopover, true);
});
</script>

<!-- COMING SOON TOAST -->
<style>
#toastMsg{
    position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);
    background:var(--navy);color:#fff;font-size:12.5px;font-weight:600;
    padding:11px 22px;border-radius:10px;box-shadow:0 8px 28px rgba(0,0,0,.25);
    z-index:9999;opacity:0;transition:all .3s ease;pointer-events:none;
    border-left:3px solid var(--gold);
}
#toastMsg.show{opacity:1;transform:translateX(-50%) translateY(0);}

.updates-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 10px 0;
    max-height: 380px;
    overflow-y: auto;
    text-align: left;
    width: 100%;
}
.update-item {
    background: #f8fafc;
    border-radius: 8px;
    padding: 12px;
    border-left: 4px solid var(--navy);
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    transition: all 0.2s ease;
}
.update-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.08);
}
.update-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
    gap: 8px;
}
.update-item-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--navy);
}
.update-item-date {
    font-size: 10px;
    color: var(--t2);
    white-space: nowrap;
}
.update-item-body {
    font-size: 11.5px;
    color: #475569;
    line-height: 1.4;
    margin-bottom: 8px;
}
.update-item-footer {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.update-item-footer .badge {
    font-size: 9.5px;
    padding: 2px 7px;
    border-radius: 12px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.badge-audience { background: #e0f2fe; color: #0369a1; }
.badge-teacher { background: #faf5ff; color: #6b21a8; }
.badge-class { background: #ecfdf5; color: #047857; }
.badge-type { background: #fef3c7; color: #d97706; }
.badge-pending { background: #fee2e2; color: #b91c1c; }
.badge-approved { background: #dcfce7; color: #15803d; }
.badge-rejected { background: #f3f4f6; color: #374151; }

/* Premium Info Popover */
.info-popover {
    position: absolute;
    background: #0f172a;
    color: #f8fafc;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 500;
    max-width: 220px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.15), 0 4px 6px -4px rgba(0, 0, 0, 0.15);
    z-index: 99999;
    line-height: 1.4;
    border: 1px solid #1e293b;
    pointer-events: none;
    opacity: 0;
    visibility: hidden;
    transform: translateY(4px);
    transition: opacity 0.15s ease, transform 0.15s ease, visibility 0.15s ease;
    text-align: left;
}
.info-popover.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    pointer-events: auto;
}
.info-popover::after {
    content: '';
    position: absolute;
    border-width: 5px;
    border-style: solid;
    border-color: #0f172a transparent transparent transparent;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
}
</style>
<!-- SIDE DRAWER OVERLAY -->
<div class="side-drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>

<!-- SIDE DRAWER CONTAINER -->
<div class="side-drawer" id="sideDrawer">
    <div class="drawer-header">
        <h3 id="drawerTitle">Details Listing</h3>
        <button class="drawer-close-btn" onclick="closeDrawer()"><i class="fas fa-times"></i></button>
    </div>
    <div class="drawer-body" id="drawerBody">
        <!-- Dynamic content will be loaded here via JS -->
    </div>
</div>

<div id="toastMsg"></div>

<script>
window.toggleTheme = function() {
    const isDark = document.body.classList.toggle('dark-mode');
    localStorage.setItem('school_erp_theme', isDark ? 'dark' : 'light');
    if (typeof updateThemeIcon === 'function') updateThemeIcon(isDark);
    if (typeof drawHeadcountPieCharts === 'function') drawHeadcountPieCharts();
    if (typeof drawAdmissionLineChart === 'function') drawAdmissionLineChart();
    if (typeof drawIncomeExpenseChart === 'function') drawIncomeExpenseChart();
};
window.updateThemeIcon = function(isDark) {
    const icon = document.getElementById('themeToggleIcon') || document.getElementById('superadminThemeToggleIcon');
    if (icon) {
        icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        icon.style.color = isDark ? '#f59e0b' : '';
    }
};

window.changeAcademicSession = function(sessionId) {
    if (!sessionId) return;
    $.post('{{ route("school.dashboard.change-session") }}', {
        academic_session_id: sessionId
    })
    .done(function(response) {
        if (response.success) {
            if (typeof showToast === 'function') {
                showToast(response.message);
            } else {
                alert(response.message);
            }
            setTimeout(function() {
                window.location.reload();
            }, 1000);
        } else {
            if (typeof showToast === 'function') {
                showToast('Error changing session');
            } else {
                alert('Error changing session');
            }
        }
    })
    .fail(function(xhr) {
        const errorMsg = xhr.responseJSON?.message || 'Error changing session';
        if (typeof showToast === 'function') {
            showToast(errorMsg);
        } else {
            alert(errorMsg);
        }
    });
};

// Sidebar functions and page listeners are defined globally in layouts/app.blade.php, so they do not need to be redefined here.

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Attendance Pie Charts (Zero counts safe)
    try {
        const studentCtx = document.getElementById('studentAttendancePieChart');
        if (studentCtx) {
            const hasData = ({{ $studentPresentToday }} + {{ $studentAbsentToday }} + {{ $studentHalfDayToday }} + {{ $studentLeaveToday }} + {{ $studentCustomToday }}) > 0;
            const dataVals = hasData ? [
                {{ $studentPresentToday }},
                {{ $studentAbsentToday }},
                {{ $studentHalfDayToday }},
                {{ $studentLeaveToday }},
                {{ $studentCustomToday }},
                {{ $studentNotMarkedToday }}
            ] : [0, 0, 0, 0, 0, 1];
 
            new Chart(studentCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Absent', 'Halfday', 'Leave', 'Custom Leaves', 'Not Marked'],
                    datasets: [{
                        data: dataVals,
                        backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#ea580c', '#ec4899', '#9ca3af'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    }
                }
            });
        }
 
        const staffCtx = document.getElementById('staffAttendancePieChart');
        if (staffCtx) {
            const hasData = ({{ $staffPresentToday }} + {{ $staffAbsentToday }} + {{ $staffHalfdayToday }} + {{ $staffLeaveToday }} + {{ $staffCustomToday }}) > 0;
            const dataVals = hasData ? [
                {{ $staffPresentToday }},
                {{ $staffAbsentToday }},
                {{ $staffHalfdayToday }},
                {{ $staffLeaveToday }},
                {{ $staffCustomToday }},
                {{ $staffNotMarkedToday }}
            ] : [0, 0, 0, 0, 0, 1];
 
            new Chart(staffCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Absent', 'Halfday', 'Leave', 'Custom Leaves', 'Not Marked'],
                    datasets: [{
                        data: dataVals,
                        backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#ea580c', '#ec4899', '#9ca3af'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    }
                }
            });
        }
        // Initialize Fee Management Pie Chart
        const feeCtx = document.getElementById('feeCollectionPieChart');
        if (feeCtx) {
            feeChart = new Chart(feeCtx.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ['Collected Amount', 'Due Amount'],
                    datasets: [{
                        data: [{{ $feeCollectedAmount }}, {{ $feeDueAmount }}],
                        backgroundColor: ['#10b981', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    }
                }
            });
        }
    } catch (e) {
        console.error("Error initializing dashboard charts:", e);
    }

    // ── GREETING SLIDER WITH CLOCK & TYPEWRITER ERASE ─────────────────────────────
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
        greetingText.textContent = `${greeting}, {{ auth()->user()->name }}! 👋`;
    }
    if (greetingIcon) {
        greetingIcon.className = `fas ${greetIcon}`;
    }

    // ── LIVE CLOCK in greeting alert ──────────────────────────────────────────────
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

    // ── Auto Slider (greeting alert box) with typewriter erase effect ─────────────
    let currentSlide = 0;
    const slider = document.getElementById('followupSlider');
    const greetEl = document.getElementById('greeting-text');
    const SLIDE_HEIGHT = 44; // px per slide

    // Typewriter erase + retype effect
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

    const greetMessages = [
        { text: `Good morning, {{ auth()->user()->name }}! ☀️`, icon: 'fa-sun', hour: [5,12] },
        { text: `Good afternoon, {{ auth()->user()->name }}! 🌤️`, icon: 'fa-cloud-sun', hour: [12,17] },
        { text: `Good evening, {{ auth()->user()->name }}! 🌇`, icon: 'fa-cloud-sun', hour: [17,21] },
        { text: `Good night, {{ auth()->user()->name }}! 🌙`, icon: 'fa-moon', hour: [21,24] },
    ];
    const nowHr = new Date().getHours();
    const activeGreet = greetMessages.find(g => nowHr >= g.hour[0] && nowHr < g.hour[1]) || greetMessages[0];

    // Set initial greeting with typewriter
    if (greetEl) {
        const initialText = activeGreet.text;
        typewriterType(greetEl, initialText, null);
    }

    if (slider) {
        const slidesCount = slider.children.length;
        setInterval(() => {
            if (currentSlide === 0) {
                // Switching away from slide 0: erase greeting first
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
                    // Back to slide 0: re-type greeting
                    setTimeout(() => {
                        if (greetEl) typewriterType(greetEl, activeGreet.text, null);
                    }, 700);
                }
            }
        }, 5000);
    }

    // ── FINANCIAL PERIOD FILTER EVENT LISTENER ────────────────────────────────────
    const filterSelect = document.querySelector('.financial-select-filter');
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const filterVal = this.value;
            fetch(`/school/dashboard/chart/income-expense?filter=${encodeURIComponent(filterVal)}`)
                .then(res => res.json())
                .then(data => {
                    if (incExpInst) {
                        incExpInst.data.labels = data.labels;
                        incExpInst.data.datasets[0].data = data.incomeData;
                        incExpInst.data.datasets[1].data = data.expenseData;
                        
                        // Handle zero/empty data gracefully
                        const maxVal = Math.max(...data.incomeData, ...data.expenseData);
                        if (maxVal === 0) {
                            incExpInst.options.scales.y.max = 10000;
                            incExpInst.options.scales.y.ticks.stepSize = 2000;
                        } else {
                            const roundedMax = Math.ceil(maxVal / 20000) * 20000;
                            incExpInst.options.scales.y.max = Math.max(20000, roundedMax);
                            incExpInst.options.scales.y.ticks.stepSize = Math.max(20000, roundedMax) / 6;
                        }
                        incExpInst.update();
                    }
                    
                    // Update totals widgets
                    const incWidget = document.getElementById('financial-total-income');
                    const expWidget = document.getElementById('financial-total-expense');
                    if (incWidget) incWidget.textContent = data.totalIncome;
                    if (expWidget) expWidget.textContent = data.totalExpense;
                })
                .catch(err => console.error('Error filtering financial chart:', err));
        });
    }
});
</script>

@endsection

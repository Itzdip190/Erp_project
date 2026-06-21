<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>School Dashboard — SchoolCloud ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

/* Logo */
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
.sb-logo-text strong{
    display:block;color:#fff;font-size:13px;font-weight:800;
    font-family:'Plus Jakarta Sans',sans-serif;line-height:1.15;
}
.sb-logo-text span{color:var(--gold);font-size:9.5px;font-weight:500;}

/* School card */
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

/* Sidebar bottom */
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

/* ─── MAIN ────────────────────────────────────────────────── */
.main{margin-left:185px;flex:1;display:flex;flex-direction:column;min-height:100vh;}

/* ─── NAVBAR ─────────────────────────────────────────────── */
.topbar{
    background:#fff;border-bottom:1px solid var(--border);
    height:62px;padding:0 22px;
    display:flex;align-items:center;justify-content:space-between;
    position:sticky;top:0;z-index:100;
    box-shadow:0 1px 3px rgba(0,0,0,.05);
}
.topbar-left{display:flex;align-items:center;gap:13px;}
.hamburger{
    background:none;border:none;color:var(--t2);
    font-size:17px;cursor:pointer;padding:4px;display:none;
}
.greeting h2{
    font-family:'Plus Jakarta Sans',sans-serif;
    font-size:15px;font-weight:700;color:var(--t1);line-height:1.2;
}
.greeting p{font-size:11.5px;color:var(--t2);}
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
    border-radius:13px;box-shadow:var(--shadow);overflow:hidden;
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
@media(max-width:1280px){
    .stats-row{grid-template-columns:repeat(3,1fr);}
    .charts-ai{grid-template-columns:1fr 1fr;}
    .ai-panel{grid-column:1/-1;}
    .bottom-row{grid-template-columns:1fr 1fr;}
}
@media(max-width:1024px){
    .sidebar{width:54px;}
    .sb-logo-text,.sb-school,.sb-hdr-title,.sb-hdr-arrow,.sb-submenu,.sb-bottom{display:none!important;}
    .sb-hdr{justify-content:center;padding:10px 0;margin:0;}
    .sb-hdr-icon{width:22px;height:22px;font-size:9.5px;}
    .main{margin-left:54px;}
    .hamburger{display:flex;}
}
@media(max-width:768px){
    .sidebar{transform:translateX(-185px);width:185px;}
    .sidebar.open{transform:translateX(0);}
    .sb-logo-text,.sb-school,.sb-hdr-title,.sb-hdr-arrow,.sb-bottom{display:block!important;}
    .sb-submenu{display:none;}
    .sb-submenu.open{display:block!important;}
    .sb-hdr{justify-content:space-between!important;padding:8px 10px!important;margin:0 6px!important;}
    .sb-hdr-icon{width:22px!important;height:22px!important;font-size:9.5px!important;}
    .main{margin-left:0;}
    .stats-row{grid-template-columns:repeat(2,1fr);}
    .charts-ai{grid-template-columns:1fr;}
    .bottom-row{grid-template-columns:1fr;}
    .topbar-right .date-pill{display:none;}
}
}
@media(max-width:480px){
    .stats-row{grid-template-columns:1fr;}
    .pg{padding:14px;}
    .qa-grid{grid-template-columns:repeat(3,1fr);}
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
    background: rgba(245,158,11,0.06);
    border: 1px solid rgba(245,158,11,0.18);
    border-radius: 20px;
    padding: 6px 14px;
}
.followup-alert-box span {
    font-size: 11.5px;
    font-weight: 600;
    color: #b45309;
}
.followup-alert-box i.fa-bell {
    color: #f59e0b;
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
    border: 1.5px solid #d97706;
    color: #d97706;
    border-radius: 6px;
    font-size: 10.5px;
    font-weight: 700;
    padding: 4px 10px;
    cursor: pointer;
    transition: all 0.2s;
    text-transform: uppercase;
}
.btn-gold-outline-sm:hover {
    background: #d97706;
    color: #fff;
}

/* Summary Grid & Cards */
.top-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
@media(max-width: 1024px) {
    .top-summary-grid { grid-template-columns: repeat(2, 1fr); }
}
@media(max-width: 600px) {
    .top-summary-grid { grid-template-columns: 1fr; }
}
.sum-card {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow);
    display: flex;
    height: 90px;
    color: #fff;
    border: 1px solid var(--border);
}
.sum-card .card-left-part {
    width: 32%;
    padding: 12px 8px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    border-right: 1px solid rgba(255,255,255,0.08);
}
.sum-card .card-left-part h4 {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-align: center;
}
.sum-card .card-left-part i.fa-arrows-rotate,
.sum-card .card-left-part i.fa-rotate {
    font-size: 11px;
    opacity: 0.6;
    cursor: pointer;
    transition: transform 0.3s;
}
.sum-card .card-left-part i.fa-arrows-rotate:hover,
.sum-card .card-left-part i.fa-rotate:hover {
    transform: rotate(180deg);
    opacity: 1;
}
.sum-card .card-right-part {
    width: 68%;
    padding: 10px 14px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 6px;
}
.sum-card .right-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    font-weight: 500;
    color: rgba(255,255,255,0.85);
}
.sum-card .right-row strong {
    font-size: 13.5px;
    font-weight: 800;
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.sum-card .right-row .row-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}
.sum-card .right-row i {
    cursor: pointer;
    font-size: 10.5px;
    opacity: 0.7;
    transition: opacity 0.2s;
}
.sum-card .right-row i:hover {
    opacity: 1;
}

/* Card Themes */
.sum-card.hc-blue {
    background: #1e70cd;
}
.sum-card.hc-blue .card-left-part {
    background: #1862b5;
}
.sum-card.hc-blue .card-right-part {
    background: #1e70cd;
}
.sum-card.ac-teal {
    background: #00695c;
}
.sum-card.ac-teal .card-left-part {
    background: #005b4f;
}
.sum-card.ac-teal .card-right-part {
    background: #00695c;
}
.sum-card.fe-purple {
    background: #5e35b1;
}
.sum-card.fe-purple .card-left-part {
    background: #512da8;
}
.sum-card.fe-purple .card-right-part {
    background: #5e35b1;
}

/* Lavender Attendance Theme */
.sum-card.at-lavender {
    background: #eae6f3;
    color: var(--t1);
    border: 1.5px solid #dcd8eb;
}
.sum-card.at-lavender .card-left-part {
    background: #e2def0;
    border-right: 1px solid #dcd8eb;
    color: var(--t1);
}
.sum-card.at-lavender .card-left-part h4 {
    color: var(--t1);
}
.sum-card.at-lavender .card-left-part i {
    color: var(--t1);
}
.sum-card.at-lavender .card-right-part {
    background: #eae6f3;
    color: var(--t1);
}
.sum-card.at-lavender .right-row {
    color: var(--t2);
}
.sum-card.at-lavender .right-row strong {
    color: var(--t1);
}
.sum-card.at-lavender .right-row i {
    color: var(--t2);
}
.sum-card.at-lavender .progress-track {
    flex: 1;
    height: 5px;
    background: #e0dbe9;
    border-radius: 3px;
    overflow: hidden;
    margin: 0 8px;
}
.sum-card.at-lavender .progress-fill {
    height: 100%;
    background: #b71c1c;
    border-radius: 3px;
}

/* Sections & Typography */
.sec-title {
    font-size: 14.5px;
    font-weight: 700;
    color: var(--t1);
    margin: 22px 0 12px;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.card-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--border);
    padding: 10px 14px;
}
.card-header-row h3 {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--t1);
    display: flex;
    align-items: center;
    gap: 6px;
}
.card-header-row h3 i.fa-arrows-rotate,
.card-header-row h3 i.fa-rotate {
    font-size: 11px;
    color: #f59e0b;
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
    gap: 14px;
    margin-bottom: 20px;
}
.db-grid-2col {
    display: grid;
    grid-template-columns: 2fr 1.05fr;
    gap: 14px;
    margin-bottom: 20px;
}
@media(max-width: 1024px) {
    .db-grid-3col { grid-template-columns: 1fr; }
    .db-grid-2col { grid-template-columns: 1fr; }
}

/* Stacked Progress Bar */
.progress-bar-container {
    margin-top: 10px;
}
.progress-label-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
}
.progress-label-row span {
    font-size: 11.5px;
    font-weight: 600;
    color: var(--t1);
}
.progress-label-row span.sub-lbl {
    font-size: 11px;
    color: var(--t2);
}
.progress-label-row i {
    color: #f59e0b;
    font-size: 10.5px;
}
.stacked-progress-bar {
    display: flex;
    height: 10px;
    background: #e5e7eb;
    border-radius: 5px;
    overflow: hidden;
    margin-bottom: 8px;
}
.progress-segment {
    height: 100%;
}
.segment-blue { background: #3b82f6; }
.segment-pink { background: #ec4899; }
.segment-teal { background: #14b8a6; }
.segment-grey { background: #9ca3af; }

.legend-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 12px;
    margin-top: 4px;
}
.legend-item {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 9.5px;
    color: var(--t2);
    font-weight: 500;
}
.legend-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

/* Joining and Attrition Grid */
.attrition-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    padding: 12px 14px;
}
.attrition-box {
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 10px;
    text-align: center;
}
.attrition-box-title {
    font-size: 11.5px;
    font-weight: 700;
    color: var(--t1);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.attrition-box-title i.fa-circle-info {
    font-size: 10.5px;
    color: var(--t2);
    cursor: pointer;
}
.attrition-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.attrition-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    color: var(--t2);
}
.attrition-row .row-lbl {
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
}
.attrition-row .row-lbl i.fa-plus-circle { color: #10b981; }
.attrition-row .row-lbl i.fa-minus-circle { color: #ef4444; }
.attrition-row .row-lbl i.fa-triangle { color: #3b82f6; }
.attrition-row .row-val {
    font-weight: 700;
    color: var(--t1);
}
.strength-indicator {
    display: flex;
    align-items: center;
    gap: 3px;
}
.strength-indicator i.fa-caret-up {
    color: #10b981;
    font-size: 10px;
}

/* Admission Summary Bar Chart */
.admission-chart-container {
    padding: 14px;
    display: flex;
    flex-direction: column;
    gap: 12px;
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
    font-size: 11px;
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
    background: #f1f5f9;
    border-radius: 8px;
    padding: 10px 12px;
    margin: 10px 14px;
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
}
.fee-action-row span strong {
    color: var(--t1);
}
.btn-orange-reminder {
    background: #ea580c;
    color: #fff;
    border: none;
    border-radius: 18px;
    padding: 6px 12px;
    font-size: 10.5px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: background 0.2s;
}
.btn-orange-reminder:hover {
    background: #c2410c;
}
.due-amount-display {
    font-size: 12px;
    font-weight: 700;
    color: #ef4444;
}
.btn-class-fee-report {
    background: transparent;
    border: 1.5px solid #d97706;
    color: #d97706;
    font-size: 11px;
    font-weight: 700;
    border-radius: 6px;
    padding: 8px;
    width: 100%;
    cursor: pointer;
    text-align: center;
    transition: all 0.2s;
}
.btn-class-fee-report:hover {
    background: #d97706;
    color: #fff;
}

/* Administrative Operations */
.recent-updates-tabs {
    display: flex;
    border-bottom: 1.5px solid var(--border);
}
.recent-updates-tabs button {
    flex: 1;
    background: transparent;
    border: none;
    padding: 8px 4px;
    font-size: 9px;
    font-weight: 700;
    color: var(--t2);
    cursor: pointer;
    text-align: center;
    border-bottom: 2px solid transparent;
}
.recent-updates-tabs button.active {
    background: #b45309;
    color: #fff;
    border-bottom: 2px solid #b45309;
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
    border: 1.5px solid #d97706;
    color: #d97706;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 700;
    padding: 4px 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-gold-outline-header:hover {
    background: #d97706;
    color: #fff;
}
.attendance-body {
    padding: 10px 14px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.attendance-subpanel {
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 10px;
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
    color: #f59e0b;
}
.btn-blue-outline-xs {
    background: transparent;
    border: 1.2px solid #2563eb;
    color: #2563eb;
    border-radius: 4px;
    font-size: 9px;
    font-weight: 700;
    padding: 2px 6px;
    cursor: pointer;
}
.btn-blue-outline-xs:hover {
    background: #2563eb;
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
    background: #eae6f3;
    color: #5e35b1;
    font-weight: 700;
}
.calendar-grid-day:hover {
    background: #f1f5f9;
    cursor: pointer;
}

.toggle-group {
    display: flex;
    border: 1px solid var(--border);
    border-radius: 6px;
    overflow: hidden;
}
.toggle-group-btn {
    padding: 4px 8px;
    font-size: 9.5px;
    font-weight: 700;
    background: #fff;
    color: var(--t2);
    border: none;
    cursor: pointer;
}
.toggle-group-btn.active {
    background: #b45309;
    color: #fff;
}
.toggle-group-btn:focus {
    outline: none;
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
    z-index: 1000;
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
    z-index: 1001;
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
    background: #d97706;
    color: #fff;
}
.drawer-tab-btn:hover:not(.active) {
    background: #f8fafc;
}
.drawer-btn-download, .drawer-btn-logs {
    background: transparent;
    border: 1.5px solid #d97706;
    color: #d97706;
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
    background: #d97706;
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
    border-left: 4px solid #d97706;
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
    background: #eae6f3 !important;
    color: #5e35b1 !important;
    font-weight: 700 !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 24px !important;
    height: 24px !important;
    line-height: 24px !important;
    margin: 2px auto !important;
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
</style>
</head>
<body>
@php
use Carbon\Carbon;
$user = auth()->user();
$initials = strtoupper(substr($user->name,0,1).(str_contains($user->name,' ') ? substr($user->name,strrpos($user->name,' ')+1,1) : ''));
$role = ucfirst(str_replace('_',' ',$user->roles->first()?->name ?? 'Admin'));
$hour = now()->hour;
$greet = $hour<12?'Good Morning':($hour<17?'Good Afternoon':'Good Evening');
$monthLabel = Carbon::create($year,$month)->format('F Y');
$monthShort = Carbon::create($year,$month)->format('M');
@endphp

<!-- ══════════ SIDEBAR ══════════ -->
<aside class="sidebar" id="sidebar">
    <a href="{{ route('school.dashboard') }}" class="sb-logo">
        <div class="sb-logo-icon"><i class="fas fa-shield-halved"></i></div>
        <div class="sb-logo-text">
            <strong>SchoolCloud ERP</strong>
            <span>Smart School ERP</span>
        </div>
    </a>

    <div class="sb-school">
        <div class="sb-school-row">
            <div class="sb-school-icon"><i class="fas fa-school"></i></div>
            <div class="sb-school-name">{{ $school->name }}</div>
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

    <div class="sb-nav">
        <!-- 1. Overview -->
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-house"></i></div>
                    <span class="sb-hdr-title">1. Overview</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                <li class="{{ request()->is('school/dashboard/mis-report') ? 'active' : '' }}">
                    <a href="{{ route('school.dashboard.mis-report') }}">
                        <span class="sb-submenu-label">Daily MIS Report</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/dashboard') ? 'active' : '' }}">
                    <a href="{{ route('school.dashboard') }}">
                        <span class="sb-submenu-label">Admin Dashboard</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 2. Institute Info -->
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-building"></i></div>
                    <span class="sb-hdr-title">2. Institute Info</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                <li class="{{ request()->is('school/settings/institute-info') ? 'active' : '' }}">
                    <a href="{{ route('school.settings.institute-info') }}">
                        <span class="sb-submenu-label">Basic Institute Info</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/settings/implementation') ? 'active' : '' }}">
                    <a href="{{ route('school.settings.implementation') }}">
                        <span class="sb-submenu-label">Implementation Process</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/settings/udise') ? 'active' : '' }}">
                    <a href="{{ route('school.settings.udise') }}">
                        <span class="sb-submenu-label">UDISE</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 3. Admin Role Management -->
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-users"></i></div>
                    <span class="sb-hdr-title">3. Admin Role Management</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                <li class="{{ request()->is('school/role-management/roles') ? 'active' : '' }}">
                    <a href="{{ route('school.roles.index') }}">
                        <span class="sb-submenu-label">Role Category</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/role-management/staff-access') ? 'active' : '' }}">
                    <a href="{{ route('school.roles.staff-access') }}">
                        <span class="sb-submenu-label">Staff Access Control</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 4. Password Management -->
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-lock"></i></div>
                    <span class="sb-hdr-title">4. Password Management</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                <li class="{{ request()->is('school/settings/reset-password') ? 'active' : '' }}">
                    <a href="{{ route('school.settings.reset-password') }}">
                        <span class="sb-submenu-label">Reset Password</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 6. Staff Management -->
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-user-cog"></i></div>
                    <span class="sb-hdr-title">6. Staff Management</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                <li class="{{ request()->is('school/staff') && !request()->is('school/staff/create') && !request()->is('school/staff/import') && !request()->is('school/staff/bulk-photo') && !request()->is('school/staff/bulk-attendance') ? 'active' : '' }}">
                    <a href="{{ route('school.staff.index') }}">
                        <span class="sb-submenu-label">Staff Directory</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/staff/create') ? 'active' : '' }}">
                    <a href="{{ route('school.staff.create') }}">
                        <span class="sb-submenu-label">Add Staff</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/staff/import') ? 'active' : '' }}">
                    <a href="{{ route('school.staff.import') }}">
                        <span class="sb-submenu-label">Bulk Staff Import</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/staff/bulk-photo') ? 'active' : '' }}">
                    <a href="{{ route('school.staff.bulk-photo') }}">
                        <span class="sb-submenu-label">Bulk Photo Upload</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/attendance/staff') ? 'active' : '' }}">
                    <a href="{{ route('school.attendance.staff.index') }}">
                        <span class="sb-submenu-label">Staff Attendance</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/staff/bulk-attendance') ? 'active' : '' }}">
                    <a href="{{ route('school.staff.bulk-attendance') }}">
                        <span class="sb-submenu-label">Staff Mark Bulk Attendance</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/attendance/students/report') ? 'active' : '' }}">
                    <a href="{{ route('school.attendance.students.report') }}">
                        <span class="sb-submenu-label">Student Attendance Marking Report</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 7. Class, Subject & Teacher Assignment -->
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-book"></i></div>
                    <span class="sb-hdr-title">7. Class, Subject & Teacher Assignment</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                <li class="{{ request()->is('school/assignments/class-overview') ? 'active' : '' }}">
                    <a href="{{ route('school.assignments.class-overview') }}">
                        <span class="sb-submenu-label">Class Overview</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/assignments/classes') ? 'active' : '' }}">
                    <a href="{{ route('school.assignments.classes') }}">
                        <span class="sb-submenu-label">Add/modify class</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/assignments/subjects') ? 'active' : '' }}">
                    <a href="{{ route('school.assignments.subjects') }}">
                        <span class="sb-submenu-label">Add/modify subjects</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/assignments/teachers') ? 'active' : '' }}">
                    <a href="{{ route('school.assignments.teachers') }}">
                        <span class="sb-submenu-label">Assign teachers</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 8. Time Table -->
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-calendar-days"></i></div>
                    <span class="sb-hdr-title">8. Time Table</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                <li class="{{ request()->is('school/timetable/class*') ? 'active' : '' }}">
                    <a href="{{ route('school.timetable.class') }}">
                        <span class="sb-submenu-label">Class Timetable</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/timetable/group*') ? 'active' : '' }}">
                    <a href="{{ route('school.timetable.group') }}">
                        <span class="sb-submenu-label">Group Timetable</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/timetable/teacher*') ? 'active' : '' }}">
                    <a href="{{ route('school.timetable.teacher') }}">
                        <span class="sb-submenu-label">Teacher Timetable</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/timetable/substitution*') ? 'active' : '' }}">
                    <a href="{{ route('school.timetable.substitution') }}">
                        <span class="sb-submenu-label">Teacher Substitution</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/timetable/workload*') ? 'active' : '' }}">
                    <a href="{{ route('school.timetable.workload') }}">
                        <span class="sb-submenu-label">Teacher Workload</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 9. Student Management -->
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-graduation-cap"></i></div>
                    <span class="sb-hdr-title">9. Student Management</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                <li class="{{ request()->is('school/students/create') ? 'active' : '' }}">
                    <a href="{{ route('school.students.create') }}">
                        <span class="sb-submenu-label">Add Student</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/student-mgmt/import*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.import') }}">
                        <span class="sb-submenu-label">Bulk Student Import</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/student-mgmt/bulk-photo*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.bulk-photo') }}">
                        <span class="sb-submenu-label">Bulk Photo/Document Upload</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/student-mgmt/optional-subject*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.optional-subject') }}">
                        <span class="sb-submenu-label">Student Optional Subject Allocation</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/students') && !request()->is('school/students/create') ? 'active' : '' }}">
                    <a href="{{ route('school.students.index') }}">
                        <span class="sb-submenu-label">Student Directory</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/student-mgmt/admission-report*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.admission-report') }}">
                        <span class="sb-submenu-label">New Admission Report</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/student-mgmt/siblings*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.siblings') }}">
                        <span class="sb-submenu-label">Siblings List</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/attendance/students') && !request()->is('school/attendance/students/report') && !request()->is('school/attendance/students/daily') && !request()->is('school/attendance/students/stats') ? 'active' : '' }}">
                    <a href="{{ route('school.attendance.students.index') }}">
                        <span class="sb-submenu-label">Student Attendance</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/student-mgmt/bulk-attendance*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.bulk-attendance') }}">
                        <span class="sb-submenu-label">Student Mark Bulk Attendance</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/student-mgmt/report*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.report') }}">
                        <span class="sb-submenu-label">Student Report</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/student-mgmt/app-settings*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.app-settings') }}">
                        <span class="sb-submenu-label">Student Info. Update Settings on App</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/student-mgmt/bulk-admission-number*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.bulk-admission-number') }}">
                        <span class="sb-submenu-label">Bulk Admission Number Change</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/student-mgmt/attendance-report*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.attendance-report') }}">
                        <span class="sb-submenu-label">Attendance Report</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/student-mgmt/discipline*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.discipline') }}">
                        <span class="sb-submenu-label">Discipline Management</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/student-mgmt/bulk-operation*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.bulk-operation') }}">
                        <span class="sb-submenu-label">Bulk Student Operation</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/student-mgmt/ptm*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.ptm') }}">
                        <span class="sb-submenu-label">PTM Attendance</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/student-mgmt/cca*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.cca') }}">
                        <span class="sb-submenu-label">CCA Module</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 10. Download Statistics -->
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-chart-pie"></i></div>
                    <span class="sb-hdr-title">10. Download Statistics</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                <li class="{{ request()->is('school/downloads/student-status*') ? 'active' : '' }}">
                    <a href="{{ route('school.downloads.student-status') }}">
                        <span class="sb-submenu-label">Student Download Status</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/downloads/staff-status*') ? 'active' : '' }}">
                    <a href="{{ route('school.downloads.staff-status') }}">
                        <span class="sb-submenu-label">Staff Download Status</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/downloads/parent-status*') ? 'active' : '' }}">
                    <a href="{{ route('school.downloads.parent-status') }}">
                        <span class="sb-submenu-label">Parent Download Status</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/downloads/student-activity*') ? 'active' : '' }}">
                    <a href="{{ route('school.downloads.student-activity') }}">
                        <span class="sb-submenu-label">Student Activity</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/downloads/staff-activity*') ? 'active' : '' }}">
                    <a href="{{ route('school.downloads.staff-activity') }}">
                        <span class="sb-submenu-label">Staff Activity</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/downloads/parent-activity*') ? 'active' : '' }}">
                    <a href="{{ route('school.downloads.parent-activity') }}">
                        <span class="sb-submenu-label">Parent Activity</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 11. Fee Management -->
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-indian-rupee-sign"></i></div>
                    <span class="sb-hdr-title">11. Fee Management</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                <li class="{{ request()->is('school/fees/configuration*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.configuration') }}">
                        <span class="sb-submenu-label">Fee Configuration</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/fees/basics*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.basics') }}">
                        <span class="sb-submenu-label">Fee Basics</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/fees/class-wise*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.class-wise') }}">
                        <span class="sb-submenu-label">Class-wise Fee</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/fees/student-wise*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.student-wise') }}">
                        <span class="sb-submenu-label">Student-wise Fee</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/fees/optional-mapping*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.optional-mapping') }}">
                        <span class="sb-submenu-label">Optional Fee Mapping</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/fees/payment-links*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.payment-links') }}">
                        <span class="sb-submenu-label">Payment Links</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/fees/collection-followup*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.collection-followup') }}">
                        <span class="sb-submenu-label">Collection Follow-Up</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/fees/schedule-mapper*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.schedule-mapper') }}">
                        <span class="sb-submenu-label">Student Class & Fee Schedule Mapper</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/fees/refund*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.refund') }}">
                        <span class="sb-submenu-label">Refund Fee</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/fees/receipts*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.receipts') }}">
                        <span class="sb-submenu-label">Fee Receipts</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/fees/pending-cheques*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.pending-cheques') }}">
                        <span class="sb-submenu-label">Pending Cheques</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/fees/reports*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.reports') }}">
                        <span class="sb-submenu-label">Fee Reports</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/fees/invoice') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.invoice') }}">
                        <span class="sb-submenu-label">Fee Invoice</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/fees/invoice1') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.invoice1') }}">
                        <span class="sb-submenu-label">Fee Invoice 1</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/fees/bulk-upload*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.bulk-upload') }}">
                        <span class="sb-submenu-label">Fee Bulk Upload</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/fees/statement-of-account*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.statement-of-account') }}">
                        <span class="sb-submenu-label">Statement of Account</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/fees/xero-integration*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.xero-integration') }}">
                        <span class="sb-submenu-label">Xero Integration</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 13. I Card/ Bus Pass/ Admit Card -->
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-address-card"></i></div>
                    <span class="sb-hdr-title">13. I Card/ Bus Pass/ Admit Card</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                <li class="{{ request()->is('school/cards/template-creator*') ? 'active' : '' }}">
                    <a href="{{ route('school.cards.template-creator') }}">
                        <span class="sb-submenu-label">Template Creator</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/cards/generate-card*') ? 'active' : '' }}">
                    <a href="{{ route('school.cards.generate-card') }}">
                        <span class="sb-submenu-label">Generate Card</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 14. Digital Diary -->
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-book-open"></i></div>
                    <span class="sb-hdr-title">14. Digital Diary</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                <li class="{{ request()->is('school/diary/create*') ? 'active' : '' }}">
                    <a href="{{ route('school.diary.create') }}">
                        <span class="sb-submenu-label">Create Diary</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/diary/report*') ? 'active' : '' }}">
                    <a href="{{ route('school.diary.report') }}">
                        <span class="sb-submenu-label">Daily Diary Report</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 15. Event & Holiday Management -->
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-calendar-check"></i></div>
                    <span class="sb-hdr-title">15. Event & Holiday Management</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                <li class="{{ request()->is('school/events*') ? 'active' : '' }}">
                    <a href="{{ route('school.events.index') }}">
                        <span class="sb-submenu-label">Event & Holiday Management</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 16. Certificate Management -->
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-certificate"></i></div>
                    <span class="sb-hdr-title">16. Certificate Management</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                <li class="{{ request()->is('school/certificates/template-creator*') ? 'active' : '' }}">
                    <a href="{{ route('school.certificates.template-creator') }}">
                        <span class="sb-submenu-label">Certificate Template Creator</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/certificates/manage*') ? 'active' : '' }}">
                    <a href="{{ route('school.certificates.manage') }}">
                        <span class="sb-submenu-label">Manage Certificates</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/certificates/class-wise*') ? 'active' : '' }}">
                    <a href="{{ route('school.certificates.class-wise') }}">
                        <span class="sb-submenu-label">Class-wise Student Certificate</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/certificates/report*') ? 'active' : '' }}">
                    <a href="{{ route('school.certificates.report') }}">
                        <span class="sb-submenu-label">Certificates Report</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

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
            <button class="hamburger" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <i class="fas fa-bars"></i>
            </button>
            <div class="greeting">
                <h2>{{ $greet }}, {{ explode(' ',$user->name)[0] }}! 👋</h2>
                <p>Here's what's happening in
                    <a href="#">{{ $school->name }}</a> today.
                </p>
            </div>
        </div>
        <div class="topbar-right">
            <div class="date-pill">
                <i class="fas fa-calendar-days"></i>
                {{ Carbon::now()->startOfMonth()->format('M j') }} – {{ Carbon::now()->endOfMonth()->format('M j, Y') }}
                <i class="fas fa-chevron-down" style="font-size:9px;"></i>
            </div>
            <a href="#" class="btn-export"><i class="fas fa-download"></i> Export Report</a>
            <!-- Bell -->
            <div class="notif-wrap">
                <div class="notif-btn" onclick="toggleDrop('notifDrop')">
                    <i class="fas fa-bell"></i>
                    @if($notificationCount>0)<span class="notif-badge">{{ $notificationCount }}</span>@endif
                </div>
                <div class="notif-drop" id="notifDrop">
                    <div class="nd-hdr">
                        <strong>Notifications</strong>
                        <span class="nd-mark" onclick="document.getElementById('notifDrop').classList.remove('open')">Mark all read</span>
                    </div>
                    <div class="nd-empty"><i class="fas fa-bell-slash" style="font-size:22px;color:var(--border);display:block;margin-bottom:8px;"></i>No new notifications</div>
                </div>
            </div>
            <!-- User -->
            <div class="user-wrap">
                <div class="user-btn" onclick="toggleDrop('userDrop')">
                    <div class="avatar">
                        @if($user->photo)<img src="{{ $user->photo }}" alt="">@else{{ $initials }}@endif
                    </div>
                    <div class="user-info">
                        <strong>{{ $user->name }}</strong>
                        <span>{{ $role }}</span>
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

        <!-- ══ HEADER ALERT & ACADEMIC YEAR ROW ══ -->
        <div class="db-header-row">
            <div class="academic-year-box">
                <label>Academic Year *</label>
                <div class="selected-session-select">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Apr 2025 - Mar 2026</span>
                    <i class="fas fa-chevron-down" style="margin-left:6px;font-size:9px;"></i>
                </div>
            </div>
            <div class="followup-alert-box">
                <i class="fas fa-bell"></i>
                <span>You have 0 Admission follow-ups today</span>
                <button class="btn-gold-outline-sm">View Follow-ups</button>
            </div>
        </div>

        <!-- ══ TOP SUMMARY CARDS (4 COLUMNS) ══ -->
        <div class="top-summary-grid">
            <!-- 1. Headcount -->
            <div class="sum-card hc-blue">
                <div class="card-left-part">
                    <h4>Headcount</h4>
                    <i class="fas fa-arrows-rotate refresh-trigger" onclick="spinIcon(this)"></i>
                </div>
                <div class="card-right-part">
                    <div class="right-row">
                        <span>Students</span>
                        <div class="row-actions">
                            <i class="fas fa-circle-info" onclick="openDrawer('students')"></i>
                            <i class="fas fa-circle-play" onclick="openDrawer('students')"></i>
                            <strong>{{ $totalStudents }}</strong>
                        </div>
                    </div>
                    <div class="right-row">
                        <span>Staffs</span>
                        <div class="row-actions">
                            <i class="fas fa-circle-play" onclick="openDrawer('staffs')"></i>
                            <strong>{{ $totalStaffs }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Accounts -->
            <div class="sum-card ac-teal">
                <div class="card-left-part">
                    <h4>Accounts</h4>
                    <i class="fas fa-arrows-rotate refresh-trigger" onclick="spinIcon(this)"></i>
                </div>
                <div class="card-right-part">
                    <div class="right-row">
                        <span>Total Income</span>
                        <div class="row-actions">
                            <i class="fas fa-circle-play" onclick="openDrawer('income')"></i>
                            <strong>₹ {{ number_format($totalIncome) }}</strong>
                        </div>
                    </div>
                    <div class="right-row">
                        <span>Total Expense</span>
                        <div class="row-actions">
                            <i class="fas fa-circle-play" onclick="openDrawer('expense')"></i>
                            <strong>₹ {{ number_format($totalExpense) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Fee -->
            <div class="sum-card fe-purple">
                <div class="card-left-part">
                    <h4>Fee</h4>
                    <i class="fas fa-arrows-rotate refresh-trigger" onclick="spinIcon(this)"></i>
                </div>
                <div class="card-right-part">
                    <div class="right-row">
                        <span>Today's Collection</span>
                        <div class="row-actions">
                            <i class="fas fa-circle-info" onclick="openDrawer('today_collection')"></i>
                            <i class="fas fa-circle-play" onclick="openDrawer('today_collection')"></i>
                            <strong>₹ {{ number_format($todayFeeCollection) }}</strong>
                        </div>
                    </div>
                    <div class="right-row">
                        <span>Total Collection</span>
                        <div class="row-actions">
                            <i class="fas fa-circle-info" onclick="openDrawer('total_collection')"></i>
                            <i class="fas fa-circle-play" onclick="openDrawer('total_collection')"></i>
                            <strong>₹ {{ number_format($totalFeeCollection) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Today's Attendance -->
            <div class="sum-card at-lavender">
                <div class="card-left-part" style="width:36%;">
                    <h4 style="font-size:10px;line-height:1.2;">Today's Attendance</h4>
                    <i class="fas fa-rotate refresh-trigger" onclick="spinIcon(this)" style="margin-top:4px;"></i>
                </div>
                <div class="card-right-part" style="width:64%;padding:8px 10px;">
                    <div class="right-row" style="margin-bottom:2px;">
                        <span>Students</span>
                        <i class="fas fa-circle-play" style="font-size:10px;cursor:pointer;" onclick="openDrawer('student_attendance')"></i>
                        <div class="progress-track">
                            <div class="progress-fill" style="width: {{ $studentAttendancePct }}%;"></div>
                        </div>
                        <strong>{{ $studentAttendancePct }}%</strong>
                    </div>
                    <div class="right-row">
                        <span>Staffs</span>
                        <i class="fas fa-circle-play" style="font-size:10px;cursor:pointer;" onclick="openDrawer('staff_attendance')"></i>
                        <div class="progress-track">
                            <div class="progress-fill" style="width: {{ $staffAttendancePct }}%;"></div>
                        </div>
                        <strong>{{ $staffAttendancePct }}% <span style="font-size:9px;color:var(--t2);margin-left:2px;">{{ $totalStaffs }}</span></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ SECTION 1: STAFF & STUDENT ENROLLMENT OVERVIEW ══ -->
        <h2 class="sec-title">Staff & Student Enrollment Overview</h2>
        <div class="db-grid-3col">
            <!-- Headcount Breakdown Card -->
            <div class="card">
                <div class="card-header-row">
                    <h3>Headcount <i class="fas fa-arrows-rotate refresh-trigger" onclick="spinIcon(this)"></i></h3>
                </div>
                <div style="padding:14px;">
                    <!-- Students Breakdown -->
                    <div class="progress-bar-container">
                        <div class="progress-label-row">
                            <span style="color:#d97706;"><i class="fas fa-users" style="margin-right:4px;"></i> Students ({{ $totalStudents }})</span>
                            <i class="fas fa-circle-chevron-right" style="cursor:pointer;" onclick="openDrawer('students')"></i>
                        </div>
                        <div class="stacked-progress-bar">
                            <div class="progress-segment segment-blue" style="width: {{ $studentMalePct }}%;" title="Male: {{ $studentMalePct }}%"></div>
                            <div class="progress-segment segment-pink" style="width: {{ $studentFemalePct }}%;" title="Female: {{ $studentFemalePct }}%"></div>
                            <div class="progress-segment segment-grey" style="width: {{ $studentNotMappedPct }}%;" title="Not Mapped: {{ $studentNotMappedPct }}%"></div>
                        </div>
                        <div class="legend-container">
                            <div class="legend-item"><span class="legend-dot segment-blue"></span>Male- {{ $studentMaleCount }} ({{ $studentMalePct }}%)</div>
                            <div class="legend-item"><span class="legend-dot segment-pink"></span>Female- {{ $studentFemaleCount }} ({{ $studentFemalePct }}%)</div>
                            <div class="legend-item"><span class="legend-dot segment-grey"></span>Not Mapped- {{ $studentNotMappedCount }} ({{ $studentNotMappedPct }}%)</div>
                        </div>
                    </div>

                    <!-- Staffs Breakdown -->
                    <div class="progress-bar-container" style="margin-top:20px;">
                        <div class="progress-label-row">
                            <span style="color:#d97706;"><i class="fas fa-user-tie" style="margin-right:6px;"></i> Staffs ({{ $totalStaffs }})</span>
                            <i class="fas fa-circle-chevron-right" style="cursor:pointer;" onclick="openDrawer('staffs')"></i>
                        </div>
                        <div class="stacked-progress-bar">
                            <div class="progress-segment segment-grey" style="width: {{ $staffNotMappedPct }}%;" title="Not Mapped: {{ $staffNotMappedPct }}%"></div>
                        </div>
                        <div class="legend-container">
                            <div class="legend-item"><span class="legend-dot segment-grey"></span>Not Mapped- {{ $staffNotMappedCount }} ({{ $staffNotMappedPct }}%)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Joining and Attrition Card -->
            <div class="card">
                <div class="card-header-row">
                    <h3>Joining And Attrition <i class="fas fa-arrows-rotate refresh-trigger" onclick="spinIcon(this)"></i></h3>
                </div>
                <div class="attrition-grid">
                    <!-- Students Box -->
                    <div class="attrition-box">
                        <div class="attrition-box-title">
                            <i class="fas fa-users" style="color:#f59e0b;"></i>
                            <span>Students</span>
                            <i class="fas fa-circle-info" onclick="openDrawer('students')"></i>
                        </div>
                        <div class="attrition-list">
                            <div class="attrition-row">
                                <span class="row-lbl"><i class="fas fa-plus-circle"></i> Newly Joined</span>
                                <span class="row-val">: {{ $studentNewlyJoined }}</span>
                            </div>
                            <div class="attrition-row">
                                <span class="row-lbl"><i class="fas fa-minus-circle"></i> Exited</span>
                                <span class="row-val">: {{ $studentExited }}</span>
                            </div>
                            <div class="attrition-row">
                                <span class="row-lbl"><i class="fas fa-caret-up" style="color:#3b82f6;font-size:12px;"></i> Strength</span>
                                <span class="row-val strength-indicator">
                                    : {{ $studentStrength }} <i class="fas fa-caret-up"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Staffs Box -->
                    <div class="attrition-box">
                        <div class="attrition-box-title">
                            <i class="fas fa-user-tie" style="color:#f59e0b;"></i>
                            <span>Staffs</span>
                            <i class="fas fa-circle-info" onclick="openDrawer('staffs')"></i>
                        </div>
                        <div class="attrition-list">
                            <div class="attrition-row">
                                <span class="row-lbl"><i class="fas fa-plus-circle"></i> Newly Joined</span>
                                <span class="row-val">: {{ $staffNewlyJoined }}</span>
                            </div>
                            <div class="attrition-row">
                                <span class="row-lbl"><i class="fas fa-minus-circle"></i> Exited</span>
                                <span class="row-val">: {{ $staffExited }}</span>
                            </div>
                            <div class="attrition-row">
                                <span class="row-lbl"><i class="fas fa-caret-up" style="color:#3b82f6;font-size:12px;"></i> Strength</span>
                                <span class="row-val strength-indicator">
                                    : {{ $staffStrength }} <i class="fas fa-caret-up"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admission Count Summary Card -->
            <div class="card">
                <div class="card-header-row">
                    <h3 style="flex:1;">
                        Admission Count Summary
                        <i class="fas fa-circle-play" style="margin-left:4px;color:#f59e0b;cursor:pointer;" onclick="openDrawer('admissions')"></i>
                        <i class="fas fa-arrows-rotate refresh-trigger" onclick="spinIcon(this)" style="margin-left:4px;"></i>
                    </h3>
                    <div class="toggle-group">
                        <button class="toggle-group-btn active" id="admOverallBtn" onclick="toggleAdmissionTab('overall')">OVERALL</button>
                        <button class="toggle-group-btn" id="admTodayBtn" onclick="toggleAdmissionTab('today')">TODAY'S</button>
                    </div>
                </div>
                <div class="admission-chart-container">
                    <div class="admission-chart-header">
                        <label class="admission-checkbox-lbl">
                            <input type="checkbox" id="showAllYearsCheck" checked> Show All Academic Years Data
                        </label>
                    </div>
                    <!-- Custom Bar Chart UI -->
                    <div class="admission-bar-chart">
                        <div class="admission-bar-wrapper">
                            <span class="admission-bar-value" id="valEnquiry">{{ $admissionEnquiry }}</span>
                            <div class="admission-bar-fill bar-red" style="height: {{ min($admissionEnquiry * 5, 80) }}px;"></div>
                            <span class="admission-bar-label">Enquiry</span>
                        </div>
                        <div class="admission-bar-wrapper">
                            <span class="admission-bar-value" id="valApplication">{{ $admissionApplication }}</span>
                            <div class="admission-bar-fill bar-greyblue" style="height: {{ min($admissionApplication * 5, 80) }}px;"></div>
                            <span class="admission-bar-label">Application</span>
                        </div>
                        <div class="admission-bar-wrapper">
                            <span class="admission-bar-value" id="valPayment">{{ $admissionPayment }}</span>
                            <div class="admission-bar-fill bar-blue" style="height: {{ min($admissionPayment * 5, 80) }}px;"></div>
                            <span class="admission-bar-label" title="Payment Collected">Payment C...</span>
                        </div>
                        <div class="admission-bar-wrapper">
                            <span class="admission-bar-value" id="valEvaluation">{{ $admissionEvaluation }}</span>
                            <div class="admission-bar-fill bar-orange" style="height: {{ min($admissionEvaluation * 5, 80) }}px;"></div>
                            <span class="admission-bar-label">Evaluation</span>
                        </div>
                        <div class="admission-bar-wrapper">
                            <span class="admission-bar-value" id="valAdmission">{{ $admissionCount }}</span>
                            <div class="admission-bar-fill bar-purple" style="height: {{ min($admissionCount * 5, 80) }}px;"></div>
                            <span class="admission-bar-label">Admission</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ SECTION 2: FINANCIAL MANAGEMENT OVERVIEW ══ -->
        <h2 class="sec-title">Financial Management Overview</h2>
        <div class="db-grid-2col">
            <!-- Income and Expense Chart Card -->
            <div class="card">
                <div class="card-header-row" style="border-bottom:none;">
                    <h3>Income And Expense <i class="fas fa-arrows-rotate refresh-trigger" onclick="spinIcon(this)"></i></h3>
                    <div style="font-size:11.5px;font-weight:600;color:var(--t1);display:flex;gap:12px;">
                        <span>Total Income: <strong>₹ {{ number_format($totalIncome) }}</strong> <i class="fas fa-circle-info" style="color:var(--t2);"></i></span>
                        <span>Total Expense: <strong>₹ {{ number_format($totalExpense) }}</strong> <i class="fas fa-circle-info" style="color:var(--t2);"></i></span>
                    </div>
                </div>
                <div class="income-expense-chart-container">
                    <canvas id="incomeExpenseChart"></canvas>
                </div>
                <div style="display:flex;justify-content:center;gap:16px;padding:8px 0 14px;font-size:11px;font-weight:600;color:var(--t2);">
                    <span style="display:flex;align-items:center;gap:6px;"><span style="width:10px;height:10px;background:#f59e0b;border-radius:2px;display:inline-block;"></span> Income</span>
                    <span style="display:flex;align-items:center;gap:6px;"><span style="width:10px;height:10px;background:#9ca3af;border-radius:2px;display:inline-block;"></span> Expense</span>
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
                    <i class="fas fa-circle-info"></i>
                </div>
                <div class="fee-management-body">
                    <!-- Till Date / Annual Switch -->
                    <div style="display:flex;justify-content:center;margin-bottom:2px;">
                        <div class="toggle-group">
                            <button class="toggle-group-btn active" id="feeTillDateBtn" onclick="toggleFeeTab('tilldate')">TILL DATE</button>
                            <button class="toggle-group-btn" id="feeAnnualBtn" onclick="toggleFeeTab('annual')">ANNUAL</button>
                        </div>
                    </div>

                    <!-- Progress Bar Collected vs Due -->
                    <div class="collected-due-bar">
                        <div class="fill-collected" style="width: {{ $feeCollectedPct }}%;"></div>
                        <div class="fill-due" style="width: {{ $feeDuePct }}%;"></div>
                    </div>

                    <!-- Progress Details -->
                    <div class="fee-list-section">
                        <div class="fee-action-row">
                            <span style="display:flex;align-items:center;gap:4px;">
                                <span class="legend-dot" style="background:#3b82f6;"></span>
                                Collected Amount - <strong>₹ {{ number_format($feeCollectedAmount) }} ({{ $feeCollectedPct }}%)</strong>
                                <i class="fas fa-circle-info" style="font-size:9.5px;"></i>
                                <i class="fas fa-arrows-rotate refresh-trigger" onclick="spinIcon(this)" style="font-size:9px;"></i>
                            </span>
                        </div>
                        <div class="fee-action-row">
                            <span style="display:flex;align-items:center;gap:4px;">
                                <span class="legend-dot" style="background:#ec4899;"></span>
                                Due Amount - <strong>₹ {{ number_format($feeDueAmount) }} ({{ $feeDuePct }}%)</strong>
                                <i class="fas fa-arrows-rotate refresh-trigger" onclick="spinIcon(this)" style="font-size:9px;"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Fee Pending Details -->
                    <div class="fee-list-section" style="border-top:1px solid var(--border);">
                        <div class="fee-list-title-row">
                            <span>Fee pending (till date)</span>
                            <i class="fas fa-circle-info"></i>
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
                    <h4>No new updates</h4>
                    <p>Notices will appear here once you receive any updates</p>
                </div>
            </div>

            <!-- Attendance Detail Overview Card -->
            <div class="card">
                <div class="card-header-row">
                    <h3>Attendance <i class="fas fa-arrows-rotate refresh-trigger" onclick="spinIcon(this)"></i></h3>
                    <button class="btn-gold-outline-header">ATTENDANCE APPROVAL</button>
                </div>
                <div class="attendance-body">
                    <!-- Student Attendance Panel -->
                    <div class="attendance-subpanel">
                        <div class="attendance-subpanel-hdr">
                            <span class="clickable-lbl" onclick="openDrawer('student_attendance')">Student Attendance Overview <i class="fas fa-circle-chevron-right"></i></span>
                            <button class="btn-blue-outline-xs" onclick="openDrawer('student_attendance')">DETAILED VIEW</button>
                        </div>
                        <div class="attendance-subpanel-body">
                            No student attendance to show
                        </div>
                    </div>

                    <!-- Staff Attendance Panel -->
                    <div class="attendance-subpanel">
                        <div class="attendance-subpanel-hdr">
                            <span class="clickable-lbl" onclick="openDrawer('staff_attendance')">Staff Attendance Overview <i class="fas fa-circle-chevron-right"></i></span>
                        </div>
                        <!-- Empty grey progress bar matching screenshot -->
                        <div style="height:10px;background:#e2e8f0;border-radius:5px;width:100%;margin-bottom:8px;"></div>
                        <div class="legend-container" style="gap:6px 10px;">
                            <div class="legend-item"><span class="legend-dot" style="background:#10b981;"></span>PRESENT: {{ $staffPresentToday }}</div>
                            <div class="legend-item"><span class="legend-dot" style="background:#ef4444;"></span>ABSENT: {{ $staffAbsentToday }}</div>
                            <div class="legend-item"><span class="legend-dot" style="background:#f59e0b;"></span>HALFDAY: {{ $staffHalfdayToday }}</div>
                            <div class="legend-item"><span class="legend-dot" style="background:#ea580c;"></span>LEAVE: {{ $staffLeaveToday }}</div>
                            <div class="legend-item"><span class="legend-dot" style="background:#ec4899;"></span>CUSTOM LEAVES: {{ $staffCustomToday }}</div>
                            <div class="legend-item"><span class="legend-dot" style="background:#9ca3af;"></span>NOT MARKED: {{ $staffNotMarkedToday }} ({{ $staffNotMarkedPct }}%)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Calendar Widget Card -->
            <div class="card">
                <div class="card-header-row">
                    <h3>Event Calender <i class="fas fa-arrows-rotate refresh-trigger" onclick="spinIcon(this)"></i></h3>
                </div>
                <!-- Switches -->
                <div class="calendar-toggles">
                    <div class="toggle-row student-lbl">
                        <label>Students' Birthdays</label>
                        <label class="switch-wrapper">
                            <input type="checkbox" id="studentBirthdaySwitch" onchange="toggleBirthdays('student')" checked>
                            <span class="switch-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-row teacher-lbl">
                        <label>Teachers' Birthdays</label>
                        <label class="switch-wrapper">
                            <input type="checkbox" id="teacherBirthdaySwitch" onchange="toggleBirthdays('teacher')">
                            <span class="switch-slider"></span>
                        </label>
                    </div>
                </div>

                <!-- Calendar Widget showing June 2026 -->
                <div class="calendar-widget">
                    <div class="calendar-month-selector">
                        <select id="calendarMonth">
                            <option value="5" selected>June</option>
                        </select>
                        <span class="year-indicator">2026 <i class="fas fa-caret-down" style="font-size:9px;"></i></span>
                    </div>
                    <div class="calendar-grid">
                        <div class="calendar-grid-header">Mo</div>
                        <div class="calendar-grid-header">Tu</div>
                        <div class="calendar-grid-header">We</div>
                        <div class="calendar-grid-header">Th</div>
                        <div class="calendar-grid-header">Fr</div>
                        <div class="calendar-grid-header">Sa</div>
                        <div class="calendar-grid-header">Su</div>

                        <!-- Grid days starting Monday for June 2026 (June 1st is Monday) -->
                        @for($i = 1; $i <= 30; $i++)
                            <div class="calendar-grid-day {{ $i === 21 ? 'today' : '' }}" onclick="selectDate({{ $i }})">{{ $i }}</div>
                        @endfor
                        <!-- Empty cells to pad grid to multiple of 7 -->
                        <div class="calendar-grid-day empty"></div>
                        <div class="calendar-grid-day empty"></div>
                        <div class="calendar-grid-day empty"></div>
                        <div class="calendar-grid-day empty"></div>
                        <div class="calendar-grid-day empty"></div>
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
// ── DATA FROM PHP ─────────────────────────────────────────────────────────────
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const MONTHS_LABELS = @json($months);
const INCOME_DATA = @json($incomeData);
const EXPENSE_DATA = @json($expenseData);

// ── SPIN ICON MICRO-ANIMATION ─────────────────────────────────────────────────
function spinIcon(icon) {
    icon.classList.add('fa-spin');
    setTimeout(() => {
        icon.classList.remove('fa-spin');
    }, 1000);
}

// ── ADMISSION COUNT TOGGLE ────────────────────────────────────────────────────
function toggleAdmissionTab(tab) {
    const overallBtn = document.getElementById('admOverallBtn');
    const todayBtn = document.getElementById('admTodayBtn');
    
    overallBtn.classList.remove('active');
    todayBtn.classList.remove('active');
    
    const enquiry = document.getElementById('valEnquiry');
    const app = document.getElementById('valApplication');
    const pay = document.getElementById('valPayment');
    const evalVal = document.getElementById('valEvaluation');
    const adm = document.getElementById('valAdmission');

    const bars = document.querySelectorAll('.admission-bar-fill');

    if (tab === 'overall') {
        overallBtn.classList.add('active');
        enquiry.textContent = "{{ $admissionEnquiry }}";
        app.textContent = "{{ $admissionApplication }}";
        pay.textContent = "{{ $admissionPayment }}";
        evalVal.textContent = "{{ $admissionEvaluation }}";
        adm.textContent = "{{ $admissionCount }}";

        bars[0].style.height = `${Math.min({{ $admissionEnquiry }} * 5, 80)}px`;
        bars[1].style.height = `${Math.min({{ $admissionApplication }} * 5, 80)}px`;
        bars[2].style.height = `${Math.min({{ $admissionPayment }} * 5, 80)}px`;
        bars[3].style.height = `${Math.min({{ $admissionEvaluation }} * 5, 80)}px`;
        bars[4].style.height = `${Math.min({{ $admissionCount }} * 5, 80)}px`;
    } else {
        todayBtn.classList.add('active');
        enquiry.textContent = "0";
        app.textContent = "0";
        pay.textContent = "0";
        evalVal.textContent = "0";
        adm.textContent = "0";

        bars.forEach(bar => bar.style.height = "0px");
    }
}

// ── FEE TAB TOGGLE (TILL DATE / ANNUAL) ────────────────────────────────────────
function toggleFeeTab(tab) {
    const tillBtn = document.getElementById('feeTillDateBtn');
    const annualBtn = document.getElementById('feeAnnualBtn');
    tillBtn.classList.remove('active');
    annualBtn.classList.remove('active');

    const collectedFill = document.querySelector('.collected-due-bar .fill-collected');
    const dueFill = document.querySelector('.collected-due-bar .fill-due');

    const textElements = document.querySelectorAll('.fee-list-section .fee-action-row strong');

    if (tab === 'tilldate') {
        tillBtn.classList.add('active');
        collectedFill.style.width = "{{ $feeCollectedPct }}%";
        dueFill.style.width = "{{ $feeDuePct }}%";
        
        textElements[0].innerHTML = "₹ {{ number_format($feeCollectedAmount) }} ({{ $feeCollectedPct }}%)";
        textElements[1].innerHTML = "₹ {{ number_format($feeDueAmount) }} ({{ $feeDuePct }}%)";
    } else {
        annualBtn.classList.add('active');
        collectedFill.style.width = "{{ $annualCollectedPct }}%";
        dueFill.style.width = "{{ $annualDuePct }}%";
        
        textElements[0].innerHTML = "₹ {{ number_format($annualCollectedAmount) }} ({{ $annualCollectedPct }}%)";
        textElements[1].innerHTML = "₹ {{ number_format($annualDueAmount) }} ({{ $annualDuePct }}%)";
    }
}

// ── RECENT UPDATES TAB SWITCH ─────────────────────────────────────────────────
function switchUpdateTab(tab) {
    const tabs = ['Notice', 'Visitor', 'Leave', 'Diary'];
    tabs.forEach(t => document.getElementById('tab' + t).classList.remove('active'));
    
    document.getElementById('tab' + ucfirst(tab)).classList.add('active');
    
    const container = document.getElementById('updatesContent');
    
    if (tab === 'notice') {
        container.innerHTML = `
            <i class="fas fa-box-open empty-state-icon"></i>
            <h4>No new updates</h4>
            <p>Notices will appear here once you receive any updates</p>
        `;
    } else if (tab === 'visitor') {
        container.innerHTML = `
            <i class="fas fa-id-badge empty-state-icon" style="color: #6ee7b7;"></i>
            <h4>No visitors approval today</h4>
            <p>Visitor logs needing admin signature will appear here</p>
        `;
    } else if (tab === 'leave') {
        container.innerHTML = `
            <i class="fas fa-file-signature empty-state-icon" style="color: #fca5a5;"></i>
            <h4>No leaves pending approval</h4>
            <p>Leave requests from staff & students will show here</p>
        `;
    } else if (tab === 'diary') {
        container.innerHTML = `
            <i class="fas fa-book-open empty-state-icon" style="color: #c084fc;"></i>
            <h4>No digital diary entries</h4>
            <p>Today's homework & class logs are up to date</p>
        `;
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

    // Open calendar events details drawer
    openDrawer('calendar_events', '2026-06-' + String(day).padStart(2, '0'));
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

// ── SIDE DRAWER CONTROLLER ──────────────────────────────────────────────────
// ── CALENDAR DATA LOADING AND RENDERING ───────────────────────────────────────
let calendarEventsData = [];

function loadCalendarMonthEvents() {
    // Default to June 2026 as per dashboard layout
    const month = 6;
    const year = 2026;
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
        cell.classList.remove('has-event', 'has-event-staff');
    });

    activeEvents.forEach(evt => {
        dayCells.forEach(cell => {
            if (cell.textContent.trim() === evt.day.toString()) {
                if (evt.type === 'staff') {
                    cell.classList.add('has-event-staff');
                } else if (evt.type === 'student') {
                    cell.classList.add('has-event');
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

    let html = '';
    activeEvents.forEach(evt => {
        const dayWithSuffixVal = dayWithSuffix(evt.day);
        const barClass = evt.type === 'staff' ? 'staff' : '';
        const dateClass = evt.type === 'staff' ? 'staff' : '';
        
        let displayName = evt.name;
        if (displayName.endsWith("'s Birthday")) {
            displayName = displayName.replace("'s Birthday", "'s");
        }

        let subText = "Birthday (1)";
        if (evt.type === 'event') {
            subText = evt.details || "School Event";
        } else if (evt.type === 'staff') {
            subText = evt.details || "Teacher's Birthday (1)";
        }

        html += `
            <div class="calendar-event-item" onclick="selectDate(${evt.day})" style="cursor:pointer; display:flex; align-items:center; margin-bottom:8px;">
                <div class="calendar-event-bar ${barClass}"></div>
                <div class="calendar-event-date ${dateClass}">${dayWithSuffixVal} June</div>
                <div class="calendar-event-text">
                    <div style="color:#00695c; font-weight:600; font-size:11px;">${displayName.toUpperCase()}</div>
                    <div style="font-size:9.5px; color:#0d9488; margin-top:1px;">${subText}</div>
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
    } else if (type === 'staffs') {
        drawer.classList.add('drawer-lg');
    } else if (type === 'send_reminder' || type === 'class_fee_report') {
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
    document.getElementById('reminderClass').disabled = isAll;
    document.getElementById('reminderSection').disabled = isAll;
}

function triggerSendReminder() {
    const btn = document.querySelector('.btn-send-now');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

    fetch('/school/dashboard/send-reminder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF
        }
    })
    .then(response => response.json())
    .then(res => {
        if (res.success) {
            showToast(res.message);
            closeDrawer();
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Failed to send reminder notifications.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
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
                        <th rowspan="2">Today's Admissions <i class="fas fa-circle-info" style="font-size:8px;"></i></th>
                        <th colspan="2">TC Students <i class="fas fa-circle-info" style="font-size:8px;"></i></th>
                        <th rowspan="2">Irregular Students <i class="fas fa-circle-info" style="font-size:8px;"></i></th>
                        <th rowspan="2">Deactivated Students</th>
                        <th rowspan="2">Total Students</th>
                        <th colspan="2">Deleted Students <i class="fas fa-circle-info" style="font-size:8px;"></i></th>
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
                <div class="drawer-stat-card-title"><i class="fas fa-user-plus" style="color:#d97706;"></i> Newly Added Staff</div>
                <div class="drawer-stat-card-val">
                    ${stats.newly_added}
                    <span class="drawer-stat-card-sub">+${stats.newly_added_academic_year} in this academic year</span>
                </div>
            </div>
            <div class="drawer-stat-card orange-border">
                <div class="drawer-stat-card-title"><i class="fas fa-users" style="color:#d97706;"></i> Old Staff</div>
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
                        <button class="drawer-action-btn green" onclick="showToast('Viewing staff details...')" title="View details"><i class="far fa-eye"></i></button>
                        <button class="drawer-action-btn" onclick="showToast('Editing staff record...')" title="Edit staff"><i class="far fa-edit"></i></button>
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
            <button class="drawer-btn-download" onclick="showToast('Downloading report...')"><i class="fas fa-download"></i> DOWNLOAD</button>
        </div>
        <div class="drawer-table-wrap">
            <table class="drawer-table-complex" style="text-align: left;">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Total Fee Amount</th>
                        <th>Collected Amount</th>
                        <th>Due Amount</th>
                    </tr>
                </thead>
                <tbody>
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
    `;
}

function renderDrawerContent(type, data) {
    const body = document.getElementById('drawerBody');
    const drawer = document.getElementById('sideDrawer');
    
    // Reset header class and backgrounds
    drawer.querySelector('.drawer-header').className = 'drawer-header';
    drawer.querySelector('.drawer-header').removeAttribute('style');

    if (!data) {
        body.innerHTML = `
            <div class="drawer-empty">
                <i class="fas fa-folder-open"></i>
                <span>No records found.</span>
            </div>
        `;
        return;
    }

    if (type === 'students' || type === 'staffs' || type === 'send_reminder' || type === 'class_fee_report') {
        drawer.querySelector('.drawer-header').style.background = '#d97706';
    }

    if (type === 'send_reminder') {
        let classesHtml = '';
        if (data.classes) {
            data.classes.forEach(c => {
                classesHtml += `<option value="${c.id}">${c.name}</option>`;
            });
        }

        body.innerHTML = `
            <div style="padding-bottom: 80px;">
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

                <div class="reminder-selector-row">
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
                    <button class="drawer-btn-download" style="border-color:#d97706; color:#d97706; margin-top: 15px;" onclick="showToast('Class picker opened')">+ ADD CLASS</button>
                </div>
            </div>
            
            <div class="reminder-bottom-bar">
                <button class="btn-send-now" onclick="triggerSendReminder()"><i class="fas fa-paper-plane"></i> SEND</button>
            </div>
        `;

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
        html += '<th>Roll No</th><th>Student Name</th><th>Class</th><th>Status</th><th>Remarks</th>';
        html += '</tr></thead><tbody>';
        data.forEach(item => {
            let badgeClass = 'bg-not-marked';
            if (item.status.toLowerCase() === 'present') badgeClass = 'bg-active';
            if (item.status.toLowerCase() === 'absent') badgeClass = 'bg-inactive';
            
            html += `<tr>
                <td>${item.roll}</td>
                <td><strong>${item.name}</strong></td>
                <td>${item.class}</td>
                <td><span class="drawer-badge ${badgeClass}">${item.status}</span></td>
                <td>${item.remark}</td>
            </tr>`;
        });
    } else if (type === 'staff_attendance') {
        html += '<th>Staff Name</th><th>Role</th><th>Status</th><th>Punch In Time</th>';
        html += '</tr></thead><tbody>';
        data.forEach(item => {
            let badgeClass = 'bg-not-marked';
            if (item.status.toLowerCase() === 'present') badgeClass = 'bg-active';
            if (item.status.toLowerCase() === 'absent') badgeClass = 'bg-inactive';
            if (item.status.toLowerCase() === 'halfday' || item.status.toLowerCase() === 'leave') badgeClass = 'bg-pending';
            
            html += `<tr>
                <td><strong>${item.name}</strong></td>
                <td>${item.role}</td>
                <td><span class="drawer-badge ${badgeClass}">${item.status}</span></td>
                <td>${item.punch_in}</td>
            </tr>`;
        });
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
    body.innerHTML = html;
}

function sendReminder() {
    openDrawer('send_reminder');
}

// ── INCOME & EXPENSE CHART (CHART.JS) ─────────────────────────────────────────
let incExpInst = null;
function drawIncomeExpenseChart() {
    const ctx = document.getElementById('incomeExpenseChart').getContext('2d');
    if (incExpInst) incExpInst.destroy();
    
    incExpInst = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: MONTHS_LABELS,
            datasets: [
                {
                    label: 'Income',
                    data: INCOME_DATA,
                    backgroundColor: '#f59e0b',
                    borderRadius: 4,
                    barThickness: 12
                },
                {
                    label: 'Expense',
                    data: EXPENSE_DATA,
                    backgroundColor: '#9ca3af',
                    borderRadius: 4,
                    barThickness: 12
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
                            size: 9,
                            family: 'Inter'
                        },
                        color: '#9ca3af'
                    }
                },
                y: {
                    min: 0,
                    max: 120000,
                    ticks: {
                        stepSize: 20000,
                        font: {
                            size: 9,
                            family: 'Inter'
                        },
                        color: '#9ca3af',
                        callback: function(value) {
                            return '₹' + value;
                        }
                    },
                    grid: {
                        color: '#f1f5f9'
                    }
                }
            }
        }
    });
}

// ── DROPDOWNS ─────────────────────────────────────────────────────────────────
function toggleDrop(id){
    ['notifDrop','userDrop'].forEach(d=>{if(d!==id)document.getElementById(d).classList.remove('open');});
    document.getElementById(id).classList.toggle('open');
}
document.addEventListener('click',e=>{
    if(!e.target.closest('.notif-wrap'))document.getElementById('notifDrop').classList.remove('open');
    if(!e.target.closest('.user-wrap'))document.getElementById('userDrop').classList.remove('open');
});

document.addEventListener('DOMContentLoaded', () => {
    drawIncomeExpenseChart();
    loadCalendarMonthEvents();
    
    // Accordion toggle for sidebar
    document.querySelectorAll('.sb-hdr').forEach(hdr => {
        hdr.addEventListener('click', () => {
            const submenu = hdr.nextElementSibling;
            if (submenu && submenu.classList.contains('sb-submenu')) {
                hdr.classList.toggle('open');
                submenu.classList.toggle('open');
            }
        });
    });

    // Auto-expand current active menu in sidebar
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

</body>
</html>

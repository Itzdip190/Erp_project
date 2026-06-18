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
@media(max-width:480px){
    .stats-row{grid-template-columns:1fr;}
    .pg{padding:14px;}
    .qa-grid{grid-template-columns:repeat(3,1fr);}
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
                <li class="{{ request()->is('school/downloads/status*') ? 'active' : '' }}">
                    <a href="{{ route('school.downloads.status') }}">
                        <span class="sb-submenu-label">Download Status</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/downloads/activity*') ? 'active' : '' }}">
                    <a href="{{ route('school.downloads.activity') }}">
                        <span class="sb-submenu-label">User Session Activity</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/students/export*') ? 'active' : '' }}">
                    <a href="{{ route('school.students.export') }}">
                        <span class="sb-submenu-label">Student Directory Export</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li>
                    <a href="#" onclick="event.preventDefault(); showComingSoon('Staff Directory Export');">
                        <span class="sb-submenu-label">Staff Directory Export</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li>
                    <a href="#" onclick="event.preventDefault(); showComingSoon('Daily Attendance Export');">
                        <span class="sb-submenu-label">Daily Attendance Export</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li>
                    <a href="#" onclick="event.preventDefault(); showComingSoon('Security Audit Export');">
                        <span class="sb-submenu-label">Security Audit Export</span>
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

        <!-- ══ STAT CARDS ══ -->
        <div class="stats-row">

            <!-- 1. Total Students -->
            <div class="stat">
                <div class="stat-top">
                    <div class="stat-ico" style="background:rgba(59,130,246,.12);color:#3b82f6;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-lbl">Total Students</div>
                        <div class="stat-val" data-count="{{ $totalStudents }}">0</div>
                        <div class="stat-trnd {{ $studentTrend>=0?'up':'dn' }}">
                            <i class="fas fa-arrow-{{ $studentTrend>=0?'up':'down' }}"></i>
                            {{ abs($studentTrend) }}%<span>vs last month</span>
                        </div>
                    </div>
                </div>
                <canvas class="stat-spark" id="spk0"></canvas>
            </div>

            <!-- 2. Fee Collection -->
            <div class="stat">
                <div class="stat-top">
                    <div class="stat-ico" style="background:rgba(245,158,11,.12);color:#f59e0b;">
                        <i class="fas fa-indian-rupee-sign"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-lbl">Fee Collection Rate</div>
                        <div class="stat-val">{{ $feeTotal>0 ? $feeRate.'%' : '—' }}</div>
                        <div class="stat-trnd neu">
                            <i class="fas fa-minus"></i>
                            <span>fee module pending</span>
                        </div>
                    </div>
                </div>
                <canvas class="stat-spark" id="spk1"></canvas>
            </div>

            <!-- 3. Attendance -->
            <div class="stat">
                <div class="stat-top">
                    <div class="stat-ico" style="background:rgba(139,92,246,.12);color:#8b5cf6;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-lbl">Attendance Rate</div>
                        <div class="stat-val">
                            @if($attendanceRate!==null)
                                <span data-count="{{ $attendanceRate }}">0</span>%
                            @else <span style="font-size:14px;color:var(--t2);">Not marked</span>
                            @endif
                        </div>
                        <div class="stat-trnd {{ $attendanceTrend>=0?'up':'dn' }}">
                            <i class="fas fa-arrow-{{ $attendanceTrend>=0?'up':'down' }}"></i>
                            {{ abs($attendanceTrend) }}%<span>vs last week</span>
                        </div>
                    </div>
                </div>
                <canvas class="stat-spark" id="spk2"></canvas>
            </div>

            <!-- 4. Revenue -->
            <div class="stat">
                <div class="stat-top">
                    <div class="stat-ico" style="background:rgba(16,185,129,.12);color:#10b981;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-lbl">Total Revenue</div>
                        <div class="stat-val">{{ formatIndianCurrency($monthlyRevenue) }}</div>
                        <div class="stat-trnd neu">
                            <i class="fas fa-minus"></i>
                            <span>fee module pending</span>
                        </div>
                    </div>
                </div>
                <canvas class="stat-spark" id="spk3"></canvas>
            </div>

            <!-- 5. Teachers -->
            <div class="stat">
                <div class="stat-top">
                    <div class="stat-ico" style="background:rgba(59,130,246,.12);color:#3b82f6;">
                        <i class="fas fa-chalkboard-user"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-lbl">Active Teachers</div>
                        <div class="stat-val" data-count="{{ $activeTeachers }}">0</div>
                        <div class="stat-trnd {{ $newStaffThisMonth>0?'up':'neu' }}">
                            @if($newStaffThisMonth>0)
                                <i class="fas fa-arrow-up"></i> {{ $newStaffThisMonth }}
                            @else <i class="fas fa-minus"></i>
                            @endif
                            <span>vs last month</span>
                        </div>
                    </div>
                </div>
                <canvas class="stat-spark" id="spk4"></canvas>
            </div>

            <!-- 6. Complaints -->
            <div class="stat">
                <div class="stat-top">
                    <div class="stat-ico" style="background:rgba(239,68,68,.12);color:#ef4444;">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-lbl">Open Complaints</div>
                        <div class="stat-val" data-count="{{ $openComplaints }}">0</div>
                        <div class="stat-trnd {{ $openComplaints>0?'dn':'up' }}">
                            @if($openComplaints>0)
                                <i class="fas fa-arrow-up"></i> {{ $openComplaints }}
                            @else <i class="fas fa-check"></i>
                            @endif
                            <span>vs last month</span>
                        </div>
                    </div>
                </div>
                <canvas class="stat-spark" id="spk5"></canvas>
            </div>
        </div>

        <!-- ══ CHARTS + AI ══ -->
        <div class="charts-ai">
            <!-- Fee Trend -->
            <div class="card">
                <div class="card-hdr">
                    <div class="card-hdr-left">
                        <div class="card-title">Fee Collection Trend</div>
                        <div class="card-big">{{ formatIndianCurrency($monthlyRevenue) }}</div>
                        <div class="card-trend up">
                            <i class="fas fa-arrow-up"></i> 0%
                            <span class="lbl">vs last month</span>
                        </div>
                    </div>
                    <select class="period-sel" onchange="loadFee(this.value)">
                        <option value="month">This Month</option>
                        <option value="3months">Last 3 Months</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="chart-wrap" style="height:190px;">
                        <canvas id="feeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Attendance Over Time -->
            <div class="card">
                <div class="card-hdr">
                    <div class="card-hdr-left">
                        <div class="card-title">Attendance Over Time</div>
                        <div style="font-size:10.5px;color:var(--t2);margin-top:2px;">Average Attendance</div>
                        <div class="card-big">{{ $avgAttendance }}%</div>
                        <div class="card-trend {{ $attendanceTrend>=0?'up':'dn' }}">
                            <i class="fas fa-arrow-{{ $attendanceTrend>=0?'up':'down' }}"></i> {{ abs($attendanceTrend) }}%
                            <span class="lbl">vs last month</span>
                        </div>
                    </div>
                    <select class="period-sel" onchange="loadAttend(this.value)">
                        <option value="month">This Month</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="chart-wrap" style="height:190px;">
                        <canvas id="attendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- AI Panel -->
            <div class="ai-panel">
                <div class="ai-hdr">
                    <div class="ai-hdr-ico"><i class="fas fa-robot"></i></div>
                    <div>
                        <h3>✦ AI Assistant</h3>
                        <p>Smart insights for your school</p>
                    </div>
                </div>
                <div class="ai-body">
                    @foreach($aiInsights as $ins)
                    <div class="ai-item">
                        <div class="ai-item-ico" style="background:{{ $ins['bg'] }};color:{{ $ins['color'] }};"><i class="fas {{ $ins['icon'] }}"></i></div>
                        <div class="ai-item-txt">{{ $ins['text'] }}</div>
                        <button class="ai-view-btn">View Insight</button>
                    </div>
                    @endforeach
                </div>
                <div class="ai-resp" id="aiResp"></div>
                <div class="ai-chat">
                    <div class="ai-input-row">
                        <input class="ai-input" id="aiIn" placeholder="Ask AI Assistant anything..." onkeydown="if(event.key==='Enter')sendAI()">
                        <button class="ai-send" onclick="sendAI()"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ BOTTOM ROW ══ -->
        <div class="bottom-row">

            <!-- Today's Snapshot -->
            <div class="card">
                <div class="ch">
                    <span style="font-size:13px;font-weight:700;color:var(--t1);">Today's Snapshot</span>
                    <span class="live-badge"><span class="live-dot"></span> Live</span>
                </div>
                <div style="padding:4px 16px 14px;">
                    <ul class="snap-list" id="snapList">
                        <li class="snap-item">
                            <span class="snap-lbl"><i class="fas fa-user-check" style="color:#3b82f6;"></i>Students Present</span>
                            <span class="snap-val {{ $markedToday>0 && ($presentToday/max($markedToday,1))>=.9?'g':($markedToday>0 && ($presentToday/max($markedToday,1))>=.75?'a':'r') }}" id="snapStu">
                                @if($markedToday>0) {{ $presentToday }} / {{ $markedToday }} @else Not marked @endif
                            </span>
                        </li>
                        <li class="snap-item">
                            <span class="snap-lbl"><i class="fas fa-chalkboard-user" style="color:#8b5cf6;"></i>Teachers Present</span>
                            <span class="snap-val g" id="snapStf">{{ $staffPresentToday }} / {{ $totalStaff }}</span>
                        </li>
                        <li class="snap-item">
                            <span class="snap-lbl"><i class="fas fa-sack-dollar" style="color:#10b981;"></i>Fees Collected Today</span>
                            <span class="snap-val" id="snapFee">{{ formatIndianCurrency($feesToday) }}</span>
                        </li>
                        <li class="snap-item">
                            <span class="snap-lbl"><i class="fas fa-bus" style="color:#f59e0b;"></i>Buses On Run</span>
                            <span class="snap-val" style="color:var(--t3);">N/A</span>
                        </li>
                        <li class="snap-item">
                            <span class="snap-lbl"><i class="fas fa-triangle-exclamation" style="color:#ef4444;"></i>Pending Complaints</span>
                            <span class="snap-val {{ $openComplaints>0?'r':'g' }}">{{ $openComplaints }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card">
                <div class="ch">
                    <span style="font-size:13px;font-weight:700;color:var(--t1);">Recent Activities</span>
                    <a href="#" class="view-all">View All</a>
                </div>
                <div style="padding:4px 16px 14px;">
                    <ul class="act-list">
                        @forelse($recentActivities as $a)
                        <li class="act-item">
                            <div class="act-ico" style="background:{{ $a['bg'] }};color:{{ $a['color'] }};"><i class="fas {{ $a['icon'] }}"></i></div>
                            <div class="act-body">
                                <p>{{ $a['text'] }}</p>
                                <span>{{ $a['time'] }}</span>
                            </div>
                            @if($a['amount'])<span class="act-amt">{{ $a['amount'] }}</span>@endif
                        </li>
                        @empty
                        <li style="padding:20px 0;text-align:center;color:var(--t3);font-size:12px;">
                            <i class="fas fa-inbox" style="font-size:22px;display:block;margin-bottom:8px;color:var(--border);"></i>
                            No recent activities
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Fee Due Summary -->
            <div class="card">
                <div class="ch">
                    <span style="font-size:13px;font-weight:700;color:var(--t1);">Fee Due Summary</span>
                    <a href="#" class="view-all">View All</a>
                </div>
                <div style="padding:4px 16px 14px;">
                    <div class="donut-wrap">
                        <div class="donut-rel">
                            <canvas id="donutChart" width="130" height="130"></canvas>
                            <div class="donut-center">
                                <strong>{{ formatIndianCurrency($totalDue) }}</strong>
                                <small>Total Due</small>
                            </div>
                        </div>
                        <div class="legend" style="padding:0 4px;">
                            @php $gt=max($feeDueSummary['paid']+$feeDueSummary['pending']+$feeDueSummary['overdue'],1); @endphp
                            <div class="legend-row">
                                <div class="leg-left"><span class="leg-dot" style="background:#10b981;"></span>Paid</div>
                                <span class="leg-val">{{ formatIndianCurrency($feeDueSummary['paid']) }}</span>
                            </div>
                            <div class="legend-row">
                                <div class="leg-left"><span class="leg-dot" style="background:#f59e0b;"></span>Pending</div>
                                <span class="leg-val">{{ formatIndianCurrency($feeDueSummary['pending']) }}</span>
                            </div>
                            <div class="legend-row">
                                <div class="leg-left"><span class="leg-dot" style="background:#ef4444;"></span>Overdue</div>
                                <span class="leg-val">{{ formatIndianCurrency($feeDueSummary['overdue']) }}</span>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="view-dues-btn">View All Dues →</a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="ch" style="padding-bottom:0;">
                    <span style="font-size:13px;font-weight:700;color:var(--t1);">Quick Actions</span>
                </div>
                <div style="padding:6px 14px 14px;">
                    <div class="qa-grid">
                        <a href="{{ route('school.students.create') }}" class="qa-btn">
                            <div class="qa-ico" style="background:rgba(59,130,246,.12);color:#3b82f6;"><i class="fas fa-user-plus"></i></div>
                            <span class="qa-lbl">Add Student</span>
                        </a>
                        <a href="#" class="qa-btn">
                            <div class="qa-ico" style="background:rgba(16,185,129,.12);color:#10b981;"><i class="fas fa-sack-dollar"></i></div>
                            <span class="qa-lbl">Collect Fee</span>
                        </a>
                        <a href="{{ route('school.attendance.students.index') }}" class="qa-btn">
                            <div class="qa-ico" style="background:rgba(139,92,246,.12);color:#8b5cf6;"><i class="fas fa-calendar-check"></i></div>
                            <span class="qa-lbl">Mark Attend.</span>
                        </a>
                        <a href="#" class="qa-btn">
                            <div class="qa-ico" style="background:rgba(245,158,11,.12);color:#f59e0b;"><i class="fas fa-bullhorn"></i></div>
                            <span class="qa-lbl">Create Notice</span>
                        </a>
                        <a href="#" class="qa-btn">
                            <div class="qa-ico" style="background:rgba(239,68,68,.12);color:#ef4444;"><i class="fas fa-chart-bar"></i></div>
                            <span class="qa-lbl">Generate Rpt</span>
                        </a>
                        <a href="#" class="qa-btn">
                            <div class="qa-ico" style="background:rgba(59,130,246,.12);color:#3b82f6;"><i class="fas fa-paper-plane"></i></div>
                            <span class="qa-lbl">Send Message</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ BANNER ══ -->
        <div class="banner">
            <div class="banner-grad">🎓</div>
            <div class="banner-mid">
                <h3>Streamline Your School Operations</h3>
                <p>Explore advanced features to automate and grow your institution.</p>
            </div>
            <a href="#" class="btn-explore">Explore Features →</a>
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
const FEE_LABELS  = @json($feeChartLabels);
const FEE_DATA    = @json($feeChartData);
const ATT_LABELS  = @json($attendanceChartLabels);
const ATT_DATA    = @json($attendanceChartData);
const SPK         = [@json($studentSparkline),@json($feeSparkline),@json($attendanceSparkline),@json($revenueSparkline),@json($teacherSparkline),@json($complaintSparkline)];
const SPK_COLORS  = ['#3b82f6','#f59e0b','#8b5cf6','#10b981','#3b82f6','#ef4444'];
const CSRF        = document.querySelector('meta[name="csrf-token"]').content;
const SNAP_URL    = "{{ route('school.dashboard.snapshot') }}";
const FEE_URL     = "{{ route('school.dashboard.chart.fee') }}";
const ATT_URL     = "{{ route('school.dashboard.chart.attend') }}";
const BOT_URL     = "{{ route('school.chatbot.send') }}";

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
        const v=parseFloat(el.dataset.count);
        countUp(el,v,1400,v%1!==0);
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
SPK.forEach((data,i)=>{
    const c=document.getElementById('spk'+i);
    if(!c)return;
    const g=c.getContext('2d').createLinearGradient(0,0,0,38);
    g.addColorStop(0,SPK_COLORS[i]+'44');
    g.addColorStop(1,SPK_COLORS[i]+'00');
    new Chart(c,{
        type:'line',
        data:{labels:data.map((_,j)=>j),datasets:[{data,borderColor:SPK_COLORS[i],borderWidth:2,tension:.4,pointRadius:0,fill:true,backgroundColor:g}]},
        options:{responsive:true,maintainAspectRatio:false,animation:{duration:800},plugins:{legend:{display:false},tooltip:{enabled:false}},scales:{x:{display:false},y:{display:false}}}
    });
});

// ── FEE TREND CHART ───────────────────────────────────────────────────────────
let feeInst=null;
function drawFee(labels,data){
    const ctx=document.getElementById('feeChart').getContext('2d');
    if(feeInst)feeInst.destroy();
    const g=ctx.createLinearGradient(0,0,0,190);
    g.addColorStop(0,'rgba(245,158,11,.22)');
    g.addColorStop(1,'rgba(245,158,11,0)');
    feeInst=new Chart(ctx,{
        type:'line',
        data:{labels,datasets:[{data,borderColor:'#f59e0b',borderWidth:2.5,tension:.4,fill:true,backgroundColor:g,pointBackgroundColor:'#f59e0b',pointRadius:0,pointHoverRadius:5}]},
        options:{responsive:true,maintainAspectRatio:false,
            plugins:{legend:{display:false},tooltip:{callbacks:{title:t=>'  '+t[0].label,label:t=>{const v=t.parsed.y;return v>=100000?'  ₹'+(v/100000).toFixed(2)+'L':'  ₹'+v.toLocaleString('en-IN');}}}},
            scales:{
                x:{grid:{display:false},ticks:{font:{size:10},color:'#9ca3af',maxTicksLimit:8}},
                y:{grid:{color:'#f3f4f6'},ticks:{font:{size:10},color:'#9ca3af',callback:v=>v>=100000?'₹'+(v/100000).toFixed(1)+'L':'₹'+v.toLocaleString('en-IN')}}
            }
        }
    });
}
async function loadFee(p){const r=await fetch(FEE_URL+'?period='+p);const j=await r.json();drawFee(j.labels,j.data);}
drawFee(FEE_LABELS,FEE_DATA);

// ── ATTENDANCE BAR CHART ──────────────────────────────────────────────────────
let attInst=null;
function drawAtt(labels,data){
    const ctx=document.getElementById('attendChart').getContext('2d');
    if(attInst)attInst.destroy();
    attInst=new Chart(ctx,{
        type:'bar',
        data:{labels,datasets:[{data,backgroundColor:'#1a1f3c',borderRadius:3,barThickness:7}]},
        options:{responsive:true,maintainAspectRatio:false,
            plugins:{legend:{display:false},tooltip:{callbacks:{label:t=>' '+t.parsed.y+'%'}}},
            scales:{
                x:{grid:{display:false},ticks:{font:{size:10},color:'#9ca3af',maxTicksLimit:10}},
                y:{min:0,max:100,grid:{color:'#f3f4f6'},ticks:{font:{size:10},color:'#9ca3af',callback:v=>v+'%'}}
            }
        }
    });
}
async function loadAttend(p){const r=await fetch(ATT_URL+'?period='+p);const j=await r.json();drawAtt(j.labels,j.data);}
drawAtt(ATT_LABELS,ATT_DATA);

// ── DONUT CHART ───────────────────────────────────────────────────────────────
(()=>{
    const p={{ $feeDueSummary['paid'] }},q={{ $feeDueSummary['pending'] }},o={{ $feeDueSummary['overdue'] }},tot=p+q+o;
    new Chart(document.getElementById('donutChart'),{
        type:'doughnut',
        data:{labels:['Paid','Pending','Overdue'],datasets:[{data:tot>0?[p,q,o]:[1,0,0],backgroundColor:['#10b981','#f59e0b','#ef4444'],borderWidth:0,hoverOffset:5}]},
        options:{cutout:'66%',responsive:false,plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>{const v=c.parsed;return ' ₹'+(v>=100000?(v/100000).toFixed(2)+'L':v.toLocaleString('en-IN'));}}}}
        }
    });
})();

// ── DROPDOWNS ─────────────────────────────────────────────────────────────────
function toggleDrop(id){
    ['notifDrop','userDrop'].forEach(d=>{if(d!==id)document.getElementById(d).classList.remove('open');});
    document.getElementById(id).classList.toggle('open');
}
document.addEventListener('click',e=>{
    if(!e.target.closest('.notif-wrap'))document.getElementById('notifDrop').classList.remove('open');
    if(!e.target.closest('.user-wrap'))document.getElementById('userDrop').classList.remove('open');
});

// ── COMING SOON TOAST ─────────────────────────────────────────────────────────
function showComingSoon(name){
    const t=document.getElementById('toastMsg');
    t.textContent='🚧 '+name+' module coming soon!';
    t.classList.add('show');
    setTimeout(()=>t.classList.remove('show'),3000);
}
function scrollToAI(){
    document.querySelector('.ai-panel').scrollIntoView({behavior:'smooth',block:'center'});
}

// ── AI CHAT ───────────────────────────────────────────────────────────────────
async function sendAI(){
    const inp=document.getElementById('aiIn'),msg=inp.value.trim();
    if(!msg)return; inp.value='';
    const box=document.getElementById('aiResp');
    box.style.display='block';
    box.innerHTML='<div class="dots"><span></span><span></span><span></span></div>';
    try{
        const r=await fetch(BOT_URL,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({message:msg})});
        const j=await r.json();
        box.innerHTML='<i class="fas fa-robot" style="color:#f59e0b;margin-right:6px;"></i>'+j.response;
    }catch{box.innerHTML='Unable to connect. Try again.';}
}

// ── LIVE SNAPSHOT REFRESH ─────────────────────────────────────────────────────
function refreshSnap(){
    fetch(SNAP_URL).then(r=>r.json()).then(d=>{
        const s=document.getElementById('snapStu'),st=document.getElementById('snapStf');
        if(s&&d.students_present!==undefined)s.textContent=d.students_present+' / '+d.students_total;
        if(st&&d.staff_present!==undefined)st.textContent=d.staff_present+' / '+d.staff_total;
    }).catch(()=>{});
}
setInterval(refreshSnap,60000);
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
<div id="toastMsg"></div>

</body>
</html>

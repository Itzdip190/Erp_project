{{-- Shared CSS for all report sub-pages. Include in @section('styles'). --}}
<style>
/* ─── SHARED REPORT STYLES ─── */
:root {
    --sr-text:    #1e293b;
    --sr-text2:   #64748b;
    --sr-border:  #e2e8f0;
    --sr-white:   #ffffff;
    --sr-gray:    #f8fafc;
    --sr-shadow:  0 4px 16px rgba(0,0,0,.08);
}
body.dark-mode {
    --sr-text:   #f8fafc;
    --sr-text2:  #94a3b8;
    --sr-border: #1e293b;
    --sr-white:  #111827;
    --sr-gray:   #1f2937;
}
.sr-hero {
    border-radius: 20px;
    padding: 30px 36px;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    box-shadow: 0 12px 40px rgba(0,0,0,.25);
}
.sr-hero::before {
    content:''; position:absolute; top:-80px; right:-80px;
    width:280px; height:280px;
    background:rgba(255,255,255,.06); border-radius:50%;
}
.sr-hero-left { position:relative; z-index:1; }
.sr-breadcrumb {
    display:flex; align-items:center; gap:8px;
    font-size:12.5px; color:rgba(255,255,255,.65);
    margin-bottom:10px;
}
.sr-breadcrumb a {
    color:rgba(255,255,255,.75); text-decoration:none; font-weight:600;
    transition:color .15s;
}
.sr-breadcrumb a:hover { color:#fff; }
.sr-breadcrumb i { font-size:10px; }
.sr-hero-title {
    font-size:26px; font-weight:800; color:#fff;
    margin:0 0 6px; display:flex; align-items:center; gap:12px;
}
.sr-hero-title i { opacity:.9; }
.sr-hero-subtitle { color:rgba(255,255,255,.72); font-size:13.5px; margin:0; }
.sr-hero-actions { position:relative; z-index:1; display:flex; gap:10px; align-items:center; flex-wrap:wrap; justify-content:flex-end; }
.sr-btn {
    display:inline-flex; align-items:center; gap:7px;
    padding:10px 18px; border-radius:10px;
    font-size:13px; font-weight:700; cursor:pointer;
    border:none; text-decoration:none; transition:all .2s;
}
.sr-btn-white {
    background:rgba(255,255,255,.95); color:#1e293b;
}
.sr-btn-white:hover { background:#fff; transform:translateY(-1px); box-shadow:0 4px 12px rgba(0,0,0,.15); }
.sr-btn-outline {
    background:rgba(255,255,255,.15);
    border:1px solid rgba(255,255,255,.35);
    color:#fff;
    backdrop-filter:blur(4px);
}
.sr-btn-outline:hover { background:rgba(255,255,255,.25); }

/* ─── FILTER CARD ─── */
.sr-filter-card {
    background: var(--sr-white);
    border: 1px solid var(--sr-border);
    border-radius: 16px;
    padding: 22px 24px;
    margin-bottom: 24px;
    box-shadow: var(--sr-shadow);
}
.sr-filter-row { display:flex; flex-wrap:wrap; gap:14px; align-items:flex-end; }
.sr-filter-group { display:flex; flex-direction:column; gap:5px; min-width:160px; flex:1; }
.sr-filter-label { font-size:12px; font-weight:600; color:var(--sr-text2); text-transform:uppercase; letter-spacing:.5px; }
.sr-filter-input {
    padding:9px 13px; border:1.5px solid var(--sr-border); border-radius:9px;
    font-size:13px; font-weight:500; background:var(--sr-gray); color:var(--sr-text);
    transition:border-color .15s;
}
.sr-filter-input:focus { outline:none; border-color:#4f46e5; }
.sr-filter-btn {
    padding:9px 20px; border-radius:9px;
    font-size:13px; font-weight:700; cursor:pointer; border:none;
    background:linear-gradient(135deg,#4f46e5,#818cf8);
    color:#fff; display:flex; align-items:center; gap:8px;
    transition:all .2s; white-space:nowrap;
}
.sr-filter-btn:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(79,70,229,.3); }

/* ─── STAT CARDS ─── */
.sr-stats { display:grid; gap:16px; margin-bottom:24px; }
.sr-stat-card {
    background:var(--sr-white); border:1px solid var(--sr-border); border-radius:14px;
    padding:18px 20px; display:flex; align-items:center; gap:14px;
    box-shadow:0 2px 8px rgba(0,0,0,.05); transition:transform .2s, box-shadow .2s;
}
.sr-stat-card:hover { transform:translateY(-2px); box-shadow:var(--sr-shadow); }
.sr-stat-icon { width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:19px; flex-shrink:0; }
.sr-stat-label { font-size:11.5px; font-weight:600; color:var(--sr-text2); text-transform:uppercase; letter-spacing:.5px; }
.sr-stat-value { font-size:22px; font-weight:800; color:var(--sr-text); }

/* ─── TABLE ─── */
.sr-table-card {
    background:var(--sr-white); border:1px solid var(--sr-border); border-radius:16px;
    overflow:hidden; box-shadow:var(--sr-shadow); margin-bottom:24px;
}
.sr-table-header {
    padding:18px 22px; border-bottom:1px solid var(--sr-border);
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;
}
.sr-table-title { font-size:15px; font-weight:700; color:var(--sr-text); display:flex; align-items:center; gap:8px; }
.sr-table-meta { font-size:12px; color:var(--sr-text2); font-weight:500; }
.sr-table { width:100%; border-collapse:collapse; }
.sr-table th {
    background:var(--sr-gray); padding:11px 16px; font-size:12px;
    font-weight:700; color:var(--sr-text2); text-align:left; white-space:nowrap;
    border-bottom:1.5px solid var(--sr-border); text-transform:uppercase; letter-spacing:.5px;
}
.sr-table td {
    padding:11px 16px; font-size:13px; color:var(--sr-text);
    border-bottom:1px solid var(--sr-border); vertical-align:middle;
}
.sr-table tr:last-child td { border-bottom:none; }
.sr-table tbody tr:hover { background:var(--sr-gray); }
.sr-badge {
    display:inline-flex; align-items:center; padding:3px 10px;
    border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px;
}
.sr-badge-success { background:#d1fae5; color:#065f46; }
.sr-badge-warning { background:#fef3c7; color:#92400e; }
.sr-badge-danger  { background:#fee2e2; color:#991b1b; }
.sr-badge-info    { background:#dbeafe; color:#1e3a8a; }
.sr-badge-gray    { background:#f1f5f9; color:#475569; }

/* ─── CHART CARD ─── */
.sr-chart-card {
    background:var(--sr-white); border:1px solid var(--sr-border); border-radius:16px;
    overflow:hidden; box-shadow:var(--sr-shadow); margin-bottom:24px;
}
.sr-chart-hdr {
    padding:16px 22px; border-bottom:1px solid var(--sr-border);
    font-size:14px; font-weight:700; color:var(--sr-text); display:flex; align-items:center; gap:8px;
}
.sr-chart-body { padding:20px 24px; }

/* ─── GRID LAYOUTS ─── */
.sr-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
.sr-grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; }
.sr-grid-4 { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }

@media(max-width:900px) {
    .sr-grid-2, .sr-grid-3, .sr-grid-4 { grid-template-columns:1fr; }
    .sr-stats { grid-template-columns:1fr 1fr !important; }
}
@media(max-width:600px) {
    .sr-hero { flex-direction:column; }
    .sr-stats { grid-template-columns:1fr !important; }
}

/* ─── EMPTY STATE ─── */
.sr-empty {
    text-align:center; padding:60px 20px; color:var(--sr-text2);
}
.sr-empty i { font-size:48px; opacity:.3; margin-bottom:16px; display:block; }
.sr-empty p { font-size:14px; font-weight:500; }

/* ─── PRINT STYLES ─── */
@media print {
    .sr-hero-actions, .sr-filter-card, .no-print { display:none !important; }
    .sr-hero { box-shadow:none !important; margin-bottom:16px !important; }
}
</style>

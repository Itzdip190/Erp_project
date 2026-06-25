<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Finance & Fees — SchoolCloud ERP</title>
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
    background-color:#fff;border-bottom:1px solid var(--border);
    height:62px;padding:0 22px;
    display:flex;align-items:center;justify-content:space-between;
    position:sticky;top:0;z-index:100;
    box-shadow:0 1px 3px rgba(0,0,0,.05);
}
.topbar-left{display:flex;align-items:center;gap:13px;}
.hamburger{background:none;border:none;color:var(--t2);font-size:17px;cursor:pointer;padding:4px;display:none;}
.greeting h2{font-family:'Plus Jakarta Sans',sans-serif;font-size:15px;font-weight:700;color:var(--t1);line-height:1.2;}
.greeting p{font-size:11.5px;color:var(--t2);}
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

/* STATS */
.stats-row{
    display:grid;
    grid-template-columns:repeat(3, 1fr);
    gap:15px;
    margin-bottom:20px;
}
@media(max-width:768px){
    .stats-row{grid-template-columns:1fr;}
}
.stat{
    background:#fff; border:1px solid var(--border); border-radius:12px;
    padding:20px; display:flex; align-items:center; gap:15px;
    box-shadow:var(--shadow);
}
.stat-ico{
    width:48px; height:48px; border-radius:10px; display:flex;
    align-items:center; justify-content:center; font-size:20px;
}
.stat-val{font-size:24px; font-weight:800; color:var(--navy); font-family:'Plus Jakarta Sans',sans-serif;}
.stat-lbl{font-size:11px; color:var(--t2); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;}

/* CARD */
.card{
    background:#fff;border:1px solid var(--border);
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

.table-responsive{width:100%; overflow-x:auto;}
.table{width:100%; border-collapse:collapse; text-align:left;}
.table th{padding:12px 10px; font-weight:600; color:var(--navy); border-bottom:2px solid var(--border); font-size:13px;}
.table td{padding:12px 10px; border-bottom:1px solid var(--border); font-size:12.5px; color:var(--t2);}
.table tr:last-child td{border-bottom:none;}

/* BADGES */
.badge {
    display:inline-flex; align-items:center; gap:4px;
    font-size:10.5px; font-weight:700; border-radius:12px;
    padding:3px 10px; text-transform:capitalize;
}
.badge-success { background:rgba(16,185,129,0.12); color:var(--green); }
.badge-warning { background:rgba(245,158,11,0.12); color:var(--gold); }
.badge-danger { background:rgba(239,68,68,0.12); color:var(--red); }

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
    @include('parent.partials.topbar', [
        'title' => 'Finance & Fees',
        'subtitle' => 'Fee statements and payment details for student ' . $student->full_name
    ])

    <!-- PAGE -->
    <div class="pg">
        
        <!-- Summary cards -->
        @php
            $totalAmount = $fees->sum('amount');
            $totalPaid = $fees->sum('paid_amount');
            $totalPending = max(0, $totalAmount - $totalPaid);
            $payRate = $totalAmount > 0 ? round(($totalPaid / $totalAmount) * 100, 1) : 0;
        @endphp
        <div class="stats-row">
            <div class="stat">
                <div class="stat-ico" style="background:rgba(59,130,246,0.1); color:var(--blue);"><i class="fas fa-indian-rupee-sign"></i></div>
                <div>
                    <div class="stat-val">₹{{ number_format($totalAmount, 2) }}</div>
                    <div class="stat-lbl">Total Fees Charged</div>
                </div>
            </div>
            <div class="stat">
                <div class="stat-ico" style="background:rgba(16,185,129,0.1); color:var(--green);"><i class="fas fa-circle-check"></i></div>
                <div>
                    <div class="stat-val">₹{{ number_format($totalPaid, 2) }}</div>
                    <div class="stat-lbl">Paid Amount</div>
                </div>
            </div>
            <div class="stat">
                <div class="stat-ico" style="background:rgba(239,68,68,0.1); color:var(--red);"><i class="fas fa-clock"></i></div>
                <div>
                    <div class="stat-val">₹{{ number_format($totalPending, 2) }}</div>
                    <div class="stat-lbl">Pending Balance</div>
                </div>
            </div>
        </div>

        @php
            $payUrl = '#';
            if (isset($config) && $config && $config->payment_url_enabled && $config->payment_url) {
                $payUrl = $config->payment_url;
                $replacements = [
                    '{student_id}' => $student->id,
                    '{student_name}' => urlencode($student->full_name),
                    '{admission_no}' => urlencode($student->admission_id),
                    '{amount}' => $totalPending,
                    '{purpose}' => urlencode('School Fees Payment'),
                    '{school_id}' => $student->school_id,
                ];
                $payUrl = str_replace(array_keys($replacements), array_values($replacements), $payUrl);
                if (strpos($payUrl, '{') === false && strpos($payUrl, 'student_id') === false) {
                    $separator = (strpos($payUrl, '?') === false) ? '?' : '&';
                    $payUrl .= $separator . "student_id={$student->id}&amount={$totalPending}";
                }
            }
        @endphp
        <div class="card">
            <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center; gap: 8px;">
                <span class="card-title"><i class="fas fa-receipt" style="color:var(--gold);margin-right:8px;"></i>Fee Ledger & Statements</span>
                <div style="display:flex; gap:8px; align-items:center;">
                    @if(isset($config) && $config && $config->payment_url_enabled && $config->payment_url && $totalPending > 0)
                        <a href="{{ $payUrl }}" target="_blank" class="btn btn-success" style="padding:6px 12px; font-size:11.5px; border-radius:6px; cursor:pointer; text-decoration:none; display: inline-flex; align-items: center; gap: 4px; background:#10b981; color:#fff;">
                            <i class="fas fa-credit-card"></i> Pay Outstanding Balance
                        </a>
                    @endif
                    <button onclick="window.print()" class="btn btn-outline" style="padding:6px 12px; font-size:11.5px; background:none; border:1px solid var(--border); border-radius:6px; cursor:pointer;"><i class="fas fa-print"></i> Print Statement</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fee Particulars / Category</th>
                                <th>Due Date</th>
                                <th>Amount Charged</th>
                                <th>Amount Paid</th>
                                <th>Due Balance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fees as $fee)
                            @php
                                $due = max(0, $fee->amount - $fee->paid_amount);
                                $badgeClass = 'badge-danger';
                                if ($fee->status === 'paid') {
                                    $badgeClass = 'badge-success';
                                } elseif ($fee->status === 'partially_paid') {
                                    $badgeClass = 'badge-warning';
                                }
                            @endphp
                            <tr>
                                <td style="font-weight:600; color:var(--navy);">{{ $fee->category ? $fee->category->name : 'General Tuition Fee' }}</td>
                                <td>{{ \Carbon\Carbon::parse($fee->due_date)->format('d-M-Y') }}</td>
                                <td style="font-weight:600;">₹{{ number_format($fee->amount, 2) }}</td>
                                <td style="font-weight:600; color:var(--green);">₹{{ number_format($fee->paid_amount, 2) }}</td>
                                <td style="font-weight:700; color:var(--red);">₹{{ number_format($due, 2) }}</td>
                                <td><span class="badge {{ $badgeClass }}">{{ str_replace('_', ' ', $fee->status) }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align:center; padding:40px; color:var(--t3);">No fees assigned or generated for this student yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
    ['userDrop', 'notifDrop'].forEach(d=>{if(d!==id)document.getElementById(d).classList.remove('open');});
    document.getElementById(id).classList.toggle('open');
}
document.addEventListener('click',e=>{
    if(!e.target.closest('.user-wrap'))document.getElementById('userDrop').classList.remove('open');
    if(!e.target.closest('.notif-wrap'))document.getElementById('notifDrop').classList.remove('open');
});
</script>
</body>
</html>

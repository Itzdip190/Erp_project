<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Attendance Calendar — SchoolCloud ERP</title>
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

        /* Student profile card */
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

        /* ─── PAGE CONTENT ────────────────────────────────────────── */
        .pg{padding:20px 22px;}

        /* Header Row */
        .page-hdr-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }
        .page-hdr-row h2 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: var(--t1);
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: var(--white);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 600;
            color: var(--t2);
            text-decoration: none;
            transition: .2s;
            box-shadow: var(--shadow);
        }
        .btn-back:hover {
            color: var(--t1);
            background-color: var(--page);
        }

        /* Card styles */
        .card{
            background:#white;background-color:#fff;border:1px solid var(--border);
            border-radius:13px;box-shadow:var(--shadow);overflow:hidden;
            margin-bottom: 18px;
        }
        .card-hdr{
            padding:14px 17px 0;
            display:flex;align-items:center;justify-content:space-between;
        }
        .card-title{font-size:13.5px;font-weight:700;color:var(--t1);font-family:'Plus Jakarta Sans',sans-serif;}
        .card-body{padding:12px 17px 15px;}

        /* Filters */
        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            align-items: end;
        }
        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: var(--t2);
            margin-bottom: 4px;
        }
        .form-select {
            width: 100%;
            height: 38px;
            padding: 0 10px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background-color: var(--page);
            font-size: 12.5px;
            outline: none;
            color: var(--t1);
            cursor: pointer;
        }
        .btn-refresh {
            height: 38px;
            background-color: var(--navy);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            transition: .2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .btn-refresh:hover {
            background-color: var(--navy2);
        }

        /* Summary Cards */
        .summary-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }
        .sum-card {
            background-color: #fff;
            border: 1px solid var(--border);
            border-radius: 13px;
            padding: 15px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sum-icon {
            width: 44px;
            height: 44px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .sum-val {
            font-size: 22px;
            font-weight: 800;
            color: var(--t1);
            font-family: 'Plus Jakarta Sans', sans-serif;
            line-height: 1.1;
        }
        .sum-lbl {
            font-size: 10px;
            color: var(--t2);
            font-weight: 500;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Calendar grid */
        .cal-header-row {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            text-align: center;
            margin-bottom: 8px;
        }
        .cal-header-day {
            font-size: 11px;
            font-weight: 700;
            color: var(--t2);
            text-transform: uppercase;
            padding: 6px 0;
        }
        .cal-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            text-align: center;
        }
        .cal-cell-pad {
            aspect-ratio: 1.1;
            background-color: transparent;
        }
        .cal-cell {
            aspect-ratio: 1.1;
            border-radius: 10px;
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            font-size: 14px;
            position: relative;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .cal-cell:hover {
            transform: scale(1.02);
            box-shadow: var(--shadow);
            z-index: 2;
        }
        .cal-cell-num {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .cal-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            position: absolute;
            bottom: 6px;
        }

        /* Color schemes */
        .c-none {
            background-color: var(--page);
            border-color: var(--border);
            color: var(--t2);
        }
        .c-present {
            background-color: #e6f4ea;
            border-color: #ceead6;
            color: #137333;
        }
        .c-present .cal-dot { background-color: #137333; }
        
        .c-absent {
            background-color: #fce8e6;
            border-color: #fad2cf;
            color: #c5221f;
        }
        .c-absent .cal-dot { background-color: #c5221f; }

        .c-late {
            background-color: #fef7e0;
            border-color: #feebc8;
            color: #b06000;
        }
        .c-late .cal-dot { background-color: #b06000; }

        .c-leave {
            background-color: #e8f0fe;
            border-color: #d2e3fc;
            color: #1a73e8;
        }
        .c-leave .cal-dot { background-color: #1a73e8; }

        /* Legend key */
        .legend-keys {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-top: 18px;
            justify-content: center;
            padding-top: 14px;
            border-top: 1px solid var(--border);
        }
        .legend-key {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            color: var(--t2);
        }
        .legend-color-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        /* Footer */
        .footer{display:flex;align-items:center;justify-content:space-between;padding:14px 0 6px;border-top:1px solid var(--border);font-size:10.5px;color:var(--t3);}

        /* Responsive */
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
            .summary-row {grid-template-columns: 1fr;}
        }
    </style>
</head>
<body>

@php
use Carbon\Carbon;
$user   = auth()->user();
$hour   = now()->hour;
$greet  = $hour<12 ? 'Good Morning' : ($hour<17 ? 'Good Afternoon' : 'Good Evening');
$stuName   = $student ? $student->full_name : $user->name;
$stuInitials = strtoupper(substr($stuName,0,1).(str_contains($stuName,' ') ? substr($stuName,strrpos($stuName,' ')+1,1) : ''));

// Resolve quick stats for student sidebar
$classDisplay   = optional($student?->class)->name ?? 'N/A';
$sectionDisplay = optional($student?->section)->name ?? 'N/A';
$sessionDisplay = optional($student?->academicSession)->name ?? 'N/A';
$school = $user->school;
@endphp

<!-- ══════════ SIDEBAR ══════════ -->
@include('parent.partials.sidebar')

<!-- ══════════ MAIN ══════════ -->
<div class="main">

    <!-- TOPBAR -->
    @include('parent.partials.topbar', [
        'title' => 'Attendance Calendar',
        'subtitle' => 'Track your attendance, present days, and monthly reports.'
    ])

    <!-- PAGE CONTENT -->
    <div class="pg">

        <!-- Header Row -->
        <div class="page-hdr-row">
            <h2>{{ $student->full_name }}'s Attendance Calendar</h2>
            <a href="{{ route('parent.dashboard') }}" class="btn-back">
                <i class="fa fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <!-- Filter Card -->
        <div class="card">
            <div class="card-hdr">
                <span class="card-title">Select Calendar Month</span>
            </div>
            <div class="card-body">
                <form action="{{ route('parent.attendance.index') }}" method="GET" class="filter-form">
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    
                    <div>
                        <label class="form-label">Month</label>
                        <select name="month" class="form-select" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Year</label>
                        <select name="year" class="form-select" required>
                            @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <button type="submit" class="btn-refresh">
                        <i class="fa fa-calendar-alt"></i> Refresh Calendar
                    </button>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-row">
            <!-- Present -->
            <div class="sum-card">
                <div class="sum-icon" style="background-color: rgba(16,185,129,0.1); color: var(--green);">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div>
                    <div class="sum-val">{{ $summary['present'] }}</div>
                    <div class="sum-lbl">Present Days</div>
                </div>
            </div>

            <!-- Absent -->
            <div class="sum-card">
                <div class="sum-icon" style="background-color: rgba(239,68,68,0.1); color: var(--red);">
                    <i class="fa-solid fa-calendar-xmark"></i>
                </div>
                <div>
                    <div class="sum-val">{{ $summary['absent'] }}</div>
                    <div class="sum-lbl">Absent Days</div>
                </div>
            </div>

            <!-- Rate -->
            <div class="sum-card">
                <div class="sum-icon" style="background-color: rgba(139,92,246,0.1); color: var(--purple);">
                    <i class="fa-solid fa-percentage"></i>
                </div>
                <div>
                    <div class="sum-val">{{ $summary['percentage'] }}%</div>
                    <div class="sum-lbl">Attendance Rate</div>
                </div>
            </div>
        </div>

        <!-- Day-by-Day Calendar -->
        <div class="card">
            <div class="card-hdr">
                <span class="card-title">Day-by-Day Calendar — {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</span>
            </div>
            <div class="card-body">
                
                <!-- Weekday Headers -->
                <div class="cal-header-row">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                        <div class="cal-header-day">{{ $dayName }}</div>
                    @endforeach
                </div>

                <!-- Days grid -->
                <div class="cal-grid">
                    <!-- Day Offset padding -->
                    @php
                        $firstDayWeekday = (int) date('w', mktime(0, 0, 0, $month, 1, $year));
                    @endphp
                    @for($p = 0; $p < $firstDayWeekday; $p++)
                        <div class="cal-cell-pad"></div>
                    @endfor

                    <!-- Active Days -->
                    @foreach($calendar as $dayNum => $info)
                        @php
                            $status = $info['status'];
                            $cellClass = 'c-none';

                            if ($status === 'present') {
                                $cellClass = 'c-present';
                            } elseif ($status === 'absent') {
                                $cellClass = 'c-absent';
                            } elseif ($status === 'late') {
                                $cellClass = 'c-late';
                            } elseif ($status === 'leave') {
                                $cellClass = 'c-leave';
                            }
                        @endphp
                        
                        <div class="cal-cell {{ $cellClass }}" title="{{ $info['remark'] ?? 'No notes' }}">
                            <span class="cal-cell-num">{{ $dayNum }}</span>
                            @if($status !== 'none')
                                <div class="cal-dot"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Legend -->
                <div class="legend-keys">
                    <div class="legend-key">
                        <span class="legend-color-dot" style="background-color: #137333;"></span>
                        <span>Present</span>
                    </div>
                    <div class="legend-key">
                        <span class="legend-color-dot" style="background-color: #c5221f;"></span>
                        <span>Absent</span>
                    </div>
                    <div class="legend-key">
                        <span class="legend-color-dot" style="background-color: #b06000;"></span>
                        <span>Late</span>
                    </div>
                    <div class="legend-key">
                        <span class="legend-color-dot" style="background-color: #1a73e8;"></span>
                        <span>Leave</span>
                    </div>
                    <div class="legend-key">
                        <span class="legend-color-dot" style="background-color: var(--t2);"></span>
                        <span>Unmarked / Weekend</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer -->
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

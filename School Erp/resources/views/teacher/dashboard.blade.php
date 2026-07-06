<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Teacher Portal — SchoolCloud ERP</title>
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

        /* ─── SIDEBAR ─────────────────────────────────────────────── */
        .sidebar{
            width:250px;min-width:250px;background:var(--navy);
            display:flex;flex-direction:column;color:#fff;position:sticky;top:0;height:100vh;overflow-y:auto;
            z-index:100;box-shadow:4px 0 20px rgba(0,0,0,0.15);
        }
        .sb-logo{
            padding:20px 18px;display:flex;align-items:center;gap:12px;
            border-bottom:1px solid rgba(255,255,255,.08);text-decoration:none;color:#fff;
        }
        .sb-logo-icon{
            width:36px;height:36px;background:linear-gradient(135deg,var(--blue),#1d4ed8);
            border-radius:10px;display:flex;align-items:center;justify-content:center;
            font-size:18px;color:#fff;box-shadow:0 2px 8px rgba(37,99,235,.4);
        }
        .sb-logo-text strong{display:block;font-family:'Plus Jakarta Sans',sans-serif;font-size:15px;font-weight:800;letter-spacing:-.3px;}
        .sb-logo-text span{font-size:11px;color:rgba(255,255,255,.5);font-weight:500;}

        /* Profile Badge Box */
        .sb-profile{
            margin:16px;padding:14px;background:rgba(255,255,255,.05);
            border:1px solid rgba(255,255,255,.1);border-radius:14px;display:flex;align-items:center;gap:12px;
        }
        .sb-avatar{
            width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,#7c3aed,#4c1d95);
            color:#fff;font-weight:800;font-size:15px;display:flex;align-items:center;justify-content:center;
            box-shadow:0 2px 8px rgba(124,58,237,.4);flex-shrink:0;
        }
        .sb-prof-info h4{font-size:13.5px;font-weight:700;color:#fff;line-height:1.2;margin-bottom:3px;}
        .sb-prof-info p{font-size:11px;color:rgba(255,255,255,.6);}
        .sb-prof-badge{
            display:inline-block;padding:2px 8px;background:rgba(245,158,11,.2);color:var(--gold);
            border-radius:20px;font-size:10px;font-weight:700;margin-top:4px;
        }

        /* Nav Groups */
        .sb-nav{padding:8px 12px;flex:1;}
        .sb-grp-title{
            font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;
            color:rgba(255,255,255,.4);padding:14px 10px 6px;display:flex;align-items:center;justify-content:space-between;
        }
        .sb-item{
            display:flex;align-items:center;gap:12px;padding:10px 12px;color:rgba(255,255,255,.75);
            text-decoration:none;font-size:13px;font-weight:600;border-radius:10px;margin-bottom:4px;
            transition:all .2s;
        }
        .sb-item:hover{background:rgba(255,255,255,.08);color:#fff;}
        .sb-item.active{background:linear-gradient(90deg,#7c3aed,#6d28d9);color:#fff;box-shadow:0 4px 12px rgba(124,58,237,.3);}
        .sb-item i{font-size:15px;width:20px;text-align:center;color:rgba(255,255,255,.6);}
        .sb-item.active i{color:#fff;}

        /* Submenu */
        .sb-sub-list{padding-left:10px;margin-bottom:8px;}
        .sb-sub-link{
            display:flex;align-items:center;justify-content:space-between;padding:8px 12px;color:rgba(255,255,255,.7);
            text-decoration:none;font-size:12.5px;font-weight:500;border-radius:8px;transition:all .2s;margin-bottom:2px;
        }
        .sb-sub-link:hover{color:#fff;background:rgba(255,255,255,.08);transform:translateX(3px);}
        .sb-sub-link-left{display:flex;align-items:center;gap:8px;}
        .sb-sub-link i.dot{font-size:6px;color:#10b981;}

        .sb-logout{padding:16px;border-top:1px solid rgba(255,255,255,.08);}
        .btn-logout{
            display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:10px;
            background:rgba(239,68,68,.12);color:#fca5a5;border:1px solid rgba(239,68,68,.2);
            border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;transition:all .2s;
        }
        .btn-logout:hover{background:#ef4444;color:#fff;}

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
            width:40px;height:40px;border-radius:10px;background:#fff;border:1px solid var(--border);
            display:flex;align-items:center;justify-content:center;color:var(--t2);font-size:16px;position:relative;cursor:pointer;
        }
        .th-icon-btn .badge-dot{
            position:absolute;top:8px;right:8px;width:8px;height:8px;background:var(--red);border-radius:50%;border:2px solid #fff;
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
    </style>
</head>
<body>

    <!-- ─── SIDEBAR ─────────────────────────────────────────────── -->
    <aside class="sidebar">
        <a href="{{ route('teacher.dashboard') }}" class="sb-logo">
            <div class="sb-logo-icon"><i class="fas fa-shield-halved"></i></div>
            <div class="sb-logo-text">
                <strong>SchoolCloud ERP</strong>
                <span>Teacher Portal</span>
            </div>
        </a>

        <!-- Teacher Profile Badge -->
        <div class="sb-profile">
            <div class="sb-avatar">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="sb-prof-info">
                <h4>{{ $user->name }}</h4>
                <p>{{ $staff?->employee_id ?? 'EMP-TEACHER' }}</p>
                <span class="sb-prof-badge">{{ $staff?->designation?->name ?? 'Mathematics Teacher' }}</span>
            </div>
        </div>

        <!-- Sidebar Nav -->
        <div class="sb-nav">
            <div class="sb-grp-title">Core Navigation</div>
            <a href="{{ route('teacher.dashboard') }}" class="sb-item active">
                <i class="fas fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>

            @if(count($accessibleModules) > 0)
                <div class="sb-grp-title" style="margin-top:12px;">Granted Modules</div>
                @foreach($accessibleModules as $mKey => $m)
                    <div class="sb-grp-title" style="color:var(--gold);text-transform:none;font-size:12px;">
                        <span>{{ $m['label'] }}</span>
                    </div>
                    <div class="sb-sub-list">
                        @foreach($m['features'] as $fKey => $fData)
                            <a href="{{ $fData['url'] }}" class="sb-sub-link" title="Open {{ $fData['label'] }}">
                                <div class="sb-sub-link-left">
                                    <i class="fas fa-circle dot"></i>
                                    <span>{{ $fData['label'] }}</span>
                                </div>
                                <i class="fas fa-chevron-right" style="font-size:9px;opacity:.5;"></i>
                            </a>
                        @endforeach
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Logout -->
        <div class="sb-logout">
            <a href="{{ route('logout') }}" class="btn-logout">
                <i class="fas fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- ─── MAIN CONTENT WRAPPER ────────────────────────────────── -->
    <div class="main-wrapper">
        
        <!-- Top Header -->
        <header class="top-header">
            <div class="th-title">
                <h2>Good Morning, {{ $user->name }}! 🌟</h2>
                <p>Here's your teaching overview for today.</p>
            </div>
            <div class="th-actions">
                <button class="th-date-btn"><i class="fas fa-calendar"></i> May 1 – May 31, 2026 <i class="fas fa-chevron-down" style="font-size:10px;margin-left:4px;"></i></button>
                <button class="th-export-btn"><i class="fas fa-download"></i> Export Report</button>
                <div class="th-icon-btn"><i class="fas fa-bell"></i><span class="badge-dot"></span></div>
                <div class="th-user-pill">
                    <div class="th-user-img">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <div class="th-user-info">
                        <h5>{{ $user->name }}</h5>
                        <p>{{ $staff?->designation?->name ?? 'Mathematics Teacher' }}</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="content-area">

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
                    <div class="sc-trend" style="color:var(--green);"><i class="fas fa-arrow-up"></i> 6.2% <span style="color:var(--t3);font-weight:500;">vs last month</span></div>
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
                    <div class="sc-trend" style="color:var(--green);"><i class="fas fa-arrow-up"></i> 5.6% <span style="color:var(--t3);font-weight:500;">vs last month</span></div>
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
                    <div class="sc-trend" style="color:var(--red);"><i class="fas fa-arrow-down"></i> 2 <span style="color:var(--t3);font-weight:500;">vs last month</span></div>
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
                            <select class="dc-select"><option>This Month</option></select>
                        </div>
                        <div style="position:relative;height:180px;display:flex;align-items:center;justify-content:center;">
                            <canvas id="donutChart"></canvas>
                            <div style="position:absolute;text-align:center;">
                                <div style="font-family:'Plus Jakarta Sans';font-size:24px;font-weight:800;">{{ $attendanceTodayPct }}%</div>
                                <div style="font-size:10px;color:var(--t3);font-weight:700;text-transform:uppercase;">Avg Attendance</div>
                            </div>
                        </div>
                        <div class="donut-legend">
                            <div class="leg-item"><div class="leg-left"><span class="leg-dot" style="background:var(--green);"></span>Present</div><div class="leg-val">{{ $attendanceTodayPct }}% ({{ $presentAttCount }})</div></div>
                            <div class="leg-item"><div class="leg-left"><span class="leg-dot" style="background:var(--red);"></span>Absent</div><div class="leg-val">6% ({{ $absentAttCount }})</div></div>
                            <div class="leg-item"><div class="leg-left"><span class="leg-dot" style="background:var(--gold);"></span>Leave</div><div class="leg-val">2% ({{ $leaveAttCount }})</div></div>
                        </div>
                    </div>
                    <div style="margin-top:20px;padding:12px;background:var(--green-light);border-radius:12px;display:flex;align-items:center;justify-content:space-between;font-size:12px;font-weight:700;color:#065f46;">
                        <span>Great! Your classes attendance is above school average.</span>
                        <span style="background:#fff;padding:4px 8px;border-radius:20px;color:var(--green);">School Avg: 85%</span>
                    </div>
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
                            <a href="{{ Route::has('school.communication.notice') ? route('school.communication.notice') : '#' }}" class="qa-btn">
                                <i class="fas fa-bullhorn" style="color:var(--orange);"></i>
                                <span>Announcements</span>
                            </a>
                            <a href="{{ Route::has('school.diary.create') ? route('school.diary.create') : '#' }}" class="qa-btn">
                                <i class="fas fa-book-open" style="color:var(--green);"></i>
                                <span>Digital Diary</span>
                            </a>
                            <a href="{{ Route::has('school.examination.marks-entry') ? route('school.examination.marks-entry') : '#' }}" class="qa-btn">
                                <i class="fas fa-book-bookmark" style="color:var(--blue);"></i>
                                <span>Gradebook</span>
                            </a>
                            <a href="{{ Route::has('school.communication.chat') ? route('school.communication.chat') : '#' }}" class="qa-btn">
                                <i class="fas fa-paper-plane" style="color:var(--red);"></i>
                                <span>Send Message</span>
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
        new Chart(document.getElementById('donutChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Absent', 'Leave'],
                datasets: [{
                    data: [{{ $attendanceTodayPct }}, 6, 2],
                    backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {legend: {display: false}}
            }
        });

        // Bar Chart
        new Chart(document.getElementById('barChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Class 6A', 'Class 7B', 'Class 8A', 'Class 9A', 'Class 10A'],
                datasets: [{
                    data: [82, 76, 79, 85, 68],
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
    </script>
</body>
</html>

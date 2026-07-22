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
            padding: 12px 14px;
            font-size: 13px;
            border-bottom: 1px solid var(--border);
            text-align: left;
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
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            display: inline-block;
        }
        .badge-pending { background: var(--gold-light); color: var(--gold); }
        .badge-approved { background: var(--green-light); color: var(--green); }
        .badge-rejected { background: var(--red-light); color: var(--red); }
        .badge-type { background: var(--blue-light); color: var(--blue); }
    </style>
</head>
<body>

    <!-- ─── SIDEBAR ─────────────────────────────────────────────── -->
    <aside class="sidebar">
        <!-- Logo -->
        <a href="{{ route('teacher.dashboard') }}" class="sb-logo">
            <div class="sb-logo-icon"><i class="fas fa-graduation-cap"></i></div>
            <div class="sb-logo-text">
                <strong>SchoolCloud</strong>
                <span>ERP SYSTEM</span>
            </div>
        </a>

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

        <!-- Sidebar Nav -->
        <div class="sb-nav">
            <div class="sb-grp-title">Core Navigation</div>
            <a href="{{ route('teacher.dashboard') }}" class="sb-item">
                <i class="fas fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('teacher.leave.apply') }}" class="sb-item active">
                <i class="fas fa-calendar-alt"></i>
                <span>Apply Leave</span>
            </a>
            
            <div class="sb-grp-title" style="margin-top: 12px;">Standard Work</div>
            <a href="{{ route('teacher.assignments.index') }}" class="sb-item">
                <i class="fas fa-tasks"></i>
                <span>Assignments</span>
            </a>
            <a href="{{ route('teacher.study-materials.index') }}" class="sb-item">
                <i class="fas fa-book-open"></i>
                <span>Study Materials</span>
            </a>
            <a href="{{ route('teacher.notices.index') }}" class="sb-item">
                <i class="fas fa-bullhorn"></i>
                <span>Notice Board</span>
            </a>
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
                <h2>Leave Application Portal</h2>
                <p>Apply for leaves and manage your balances</p>
            </div>
            <div class="th-actions">
                <div class="th-user-pill">
                    <div class="th-user-img">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <div class="th-user-info">
                        <h5>{{ $user->name }}</h5>
                        <p>{{ $staff->designation?->name ?? 'Staff Member' }}</p>
                    </div>
                </div>
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
                        
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
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
                                        <div style="font-weight:600;color:var(--t1);">{{ $app->start_date }}</div>
                                        <div style="font-size:11px;color:var(--t3);">to {{ $app->end_date }}</div>
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
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" style="text-align:center; padding:40px; color:var(--t3);">
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

</body>
</html>

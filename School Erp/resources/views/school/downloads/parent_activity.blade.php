@extends('layouts.app')

@section('page-title', 'Parent Activity Logs')

@section('content')
<style>
    /* Premium Blue & White Theme Styles */
    .dl-module {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #1e293b;
    }
    .dl-title-section h1 {
        font-size: 28px !important;
        font-weight: 800 !important;
        color: #1e3a8a !important;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .dl-title-section p {
        font-size: 15px !important;
        color: #475569 !important;
        font-weight: 500;
    }
    .dl-card-filter {
        background: #ffffff;
        border: 1px solid #dbeafe;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(30, 58, 138, 0.04);
        margin-bottom: 25px;
    }
    .dl-filter-body {
        padding: 20px 24px;
    }
    .dl-form-label {
        font-size: 12px !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        color: #1e40af !important;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }
    .dl-select, .dl-input {
        height: 46px !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        border: 2px solid #dbeafe !important;
        border-radius: 8px !important;
        color: #1e293b !important;
        background-color: #f8fafc !important;
        transition: all 0.2s ease-in-out;
    }
    .dl-select:focus, .dl-input:focus {
        border-color: #2563eb !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
    }
    
    /* Action Buttons in blue-white styling */
    .dl-btn {
        height: 46px;
        padding: 0 20px;
        font-size: 15px !important;
        font-weight: 700 !important;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .dl-btn-primary {
        background: #2563eb;
        color: #ffffff !important;
        border: none;
    }
    .dl-btn-primary:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }
    .dl-btn-outline {
        background: #ffffff;
        color: #2563eb !important;
        border: 2px solid #2563eb;
    }
    .dl-btn-outline:hover {
        background: #eff6ff;
        transform: translateY(-1px);
    }

    /* Table with Large Font size */
    .dl-table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(30, 58, 138, 0.03);
        overflow: hidden;
    }
    .dl-table-hdr {
        background: #f8fafc;
        padding: 16px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .dl-table-hdr h3 {
        font-size: 18px !important;
        font-weight: 750 !important;
        color: #1e3a8a;
        margin: 0;
    }
    .dl-badge-blue {
        background: #eff6ff;
        color: #1e40af;
        font-weight: 800;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px !important;
    }
    .dl-table {
        width: 100%;
        border-collapse: collapse;
    }
    .dl-table th {
        background: #1e3a8a !important; /* Premium dark blue header */
        color: #ffffff !important;
        font-size: 15px !important;
        font-weight: 750 !important;
        padding: 16px 20px !important;
        text-align: left;
    }
    .dl-table td {
        padding: 16px 20px !important;
        font-size: 16px !important; /* LARGE FONT SIZE */
        font-weight: 600 !important;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .dl-table tr:hover td {
        background: #f8fafc;
    }
    .dl-parent-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: bold;
        border: 2px solid #dbeafe;
    }
</style>

<div class="dl-module">
    <!-- Header -->
    <div class="dl-title-section" style="margin-bottom: 25px;">
        <h1>
            <i class="fas fa-history" style="color:#2563eb;"></i>
            Parent Activity Logs
        </h1>
        <p>Monitor parent app registrations, last login details, and version details</p>
    </div>

    <!-- Filter Bar -->
    <div class="card dl-card-filter">
        <div class="card-body dl-filter-body">
            <form method="GET" action="{{ route('school.downloads.parent-activity') }}" style="display:flex; justify-content:space-between; align-items:flex-end; gap:20px; flex-wrap:wrap;">
                <div style="display:flex; gap:20px; flex-grow:1; flex-wrap:wrap;">
                    <div class="form-group" style="margin-bottom:0; flex:1; min-width:130px;">
                        <label class="dl-form-label">Academic Year</label>
                        <select name="academic_year" class="form-control dl-select">
                            <option value="2025-2026" selected>Apr 2025 - Mar 2026</option>
                            <option value="2024-2025">Apr 2024 - Mar 2025</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0; flex:1; min-width:130px;">
                        <label class="dl-form-label">Select Class</label>
                        <select name="class_id" class="form-control dl-select" onchange="this.form.submit()">
                            <option value="">All Classes</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0; flex:1; min-width:130px;">
                        <label class="dl-form-label">Select Section</label>
                        <select name="section_id" class="form-control dl-select" onchange="this.form.submit()">
                            <option value="">All Sections</option>
                            @foreach($sections as $s)
                                <option value="{{ $s->id }}" {{ $sectionId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0; flex:2; min-width:180px;">
                        <label class="dl-form-label">Search Student / Parent</label>
                        <input type="text" name="search" class="form-control dl-input" placeholder="Search Name, Student..." value="{{ $search }}">
                    </div>
                </div>
                
                <div style="display:flex; gap:10px;">
                    <button type="submit" class="dl-btn dl-btn-primary">
                        <i class="fas fa-eye"></i> VIEW
                    </button>
                    <button type="button" class="dl-btn dl-btn-outline" onclick="dlExportCSV('parentActTable', 'Parent_Activity_Logs.csv')">
                        <i class="fas fa-download"></i> DOWNLOAD CSV
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Listing -->
    <div class="dl-table-card">
        <div class="dl-table-hdr">
            <h3>Parent Activity Grid</h3>
            <span class="dl-badge-blue">{{ $totalItems }} Active Logs</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="dl-table" id="parentActTable">
                <thead>
                    <tr>
                        <th style="width:80px; text-align:center;">#</th>
                        <th style="width:140px;">Student ID</th>
                        <th>Parent Name</th>
                        <th>Student Name</th>
                        <th>Mobile</th>
                        <th>Last Seen</th>
                        <th>App Version</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paginatedActivities as $index => $act)
                        <tr>
                            <td style="text-align:center; color:#64748b; font-weight:700;">
                                {{ sprintf('%02d.', ($page - 1) * 12 + $loop->iteration) }}
                            </td>
                            <td>
                                <span class="badge dl-badge-blue">{{ $act['student_id'] }}</span>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div class="dl-parent-avatar">
                                        {{ substr($act['parent_name'], 0, 1) }}
                                    </div>
                                    <span style="font-weight: 800; color: #1e3a8a;">{{ $act['parent_name'] }}</span>
                                </div>
                            </td>
                            <td style="font-weight: 700; color: #334155;">
                                {{ $act['student_name'] }}
                            </td>
                            <td style="font-weight: 700; color: #334155;">
                                {{ $act['mobile'] }}
                            </td>
                            <td style="font-weight: 800; color: {{ $act['last_seen'] === 'Never Logged In' ? '#b91c1c' : '#047857' }};">
                                {{ $act['last_seen'] }}
                            </td>
                            <td>
                                @if($act['app_version'])
                                    <span class="dl-badge-blue" style="background:#e0f2fe; color:#0369a1;">
                                        {{ $act['app_version'] }}
                                    </span>
                                @else
                                    <span style="color:#94a3b8;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:50px; color:#64748b;">
                                <i class="fas fa-clock" style="font-size:40px; color:#cbd5e1; margin-bottom:15px; display:block;"></i>
                                No parent activity log entries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div style="padding:15px 24px; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div style="font-weight: 700; color: #475569; font-size: 14px;">
                @if($totalItems > 0)
                    Showing {{ ($page - 1) * 12 + 1 }}–{{ min($page * 12, $totalItems) }} of {{ $totalItems }} Records
                @else
                    No Records
                @endif
            </div>
            @if($totalPages > 1)
            <div style="display:flex; gap:5px; align-items:center; flex-wrap:wrap;">
                @if($page > 1)
                    <a href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}" class="dl-btn dl-btn-outline" style="height:34px; padding:0 12px; font-size:12px !important;"><i class="fas fa-chevron-left"></i></a>
                @else
                    <button class="dl-btn dl-btn-outline" style="height:34px; padding:0 12px; font-size:12px !important;" disabled><i class="fas fa-chevron-left"></i></button>
                @endif
                @for($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++)
                    @if($p === $page)
                        <button class="dl-btn dl-btn-primary" style="height:34px; padding:0 12px; font-size:12px !important;">{{ $p }}</button>
                    @else
                        <a href="{{ request()->fullUrlWithQuery(['page' => $p]) }}" class="dl-btn dl-btn-outline" style="height:34px; padding:0 12px; font-size:12px !important;">{{ $p }}</a>
                    @endif
                @endfor
                @if($page < $totalPages)
                    <a href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}" class="dl-btn dl-btn-outline" style="height:34px; padding:0 12px; font-size:12px !important;"><i class="fas fa-chevron-right"></i></a>
                @else
                    <button class="dl-btn dl-btn-outline" style="height:34px; padding:0 12px; font-size:12px !important;" disabled><i class="fas fa-chevron-right"></i></button>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
<script>
function dlExportCSV(tableId, filename) {
    var table = document.getElementById(tableId);
    if (!table) return;
    var rows = Array.from(table.querySelectorAll('tr'));
    var csv = rows.map(function(row) {
        var cells = Array.from(row.querySelectorAll('th, td'));
        return cells.slice(1).map(function(cell) {
            var text = (cell.innerText || '').trim().replace(/[\r\n\t]+/g, ' ').replace(/\s{2,}/g, ' ').replace(/"/g, '""');
            return '"' + text + '"';
        }).join(',');
    }).join('\n');
    var bom = '\uFEFF';
    var blob = new Blob([bom + csv], {type: 'text/csv;charset=utf-8;'});
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    if (typeof showToast === 'function') showToast('CSV exported successfully!');
}
</script>
@endsection

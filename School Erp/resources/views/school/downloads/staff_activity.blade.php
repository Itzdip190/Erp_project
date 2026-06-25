@extends('layouts.app')

@section('page-title', 'Staff Activity Logs')

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
    
    /* Segmented status tabs */
    .dl-status-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    .dl-status-tab {
        padding: 10px 24px;
        border-radius: 8px;
        font-size: 14px !important;
        font-weight: 800 !important;
        text-transform: uppercase;
        text-decoration: none !important;
        border: 2px solid transparent;
        transition: all 0.2s;
    }
    .dl-status-tab-active {
        background: #2563eb;
        color: #ffffff !important;
    }
    .dl-status-tab-inactive {
        background: #ffffff;
        color: #475569 !important;
        border-color: #cbd5e1;
    }
    .dl-status-tab-inactive:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }

    /* Sub-group Tabs */
    .dl-subgroup-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 25px;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 12px;
    }
    .dl-subgroup-tab {
        padding: 8px 20px;
        border-radius: 6px;
        font-size: 13px !important;
        font-weight: 800 !important;
        text-transform: uppercase;
        text-decoration: none !important;
        transition: all 0.2s;
    }
    .dl-subgroup-tab-active {
        background: #eff6ff;
        color: #2563eb !important;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.1);
    }
    .dl-subgroup-tab-inactive {
        background: transparent;
        color: #64748b !important;
    }
    .dl-subgroup-tab-inactive:hover {
        background: #f1f5f9;
        color: #334155 !important;
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
    .dl-input {
        height: 46px !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        border: 2px solid #dbeafe !important;
        border-radius: 8px !important;
        color: #1e293b !important;
        background-color: #f8fafc !important;
        transition: all 0.2s ease-in-out;
    }
    .dl-input:focus {
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
    .dl-staff-avatar {
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
            Staff Activity Logs
        </h1>
        <p>Monitor portal access activity and dashboard session logs for staff members</p>
    </div>

    <!-- Active / Deactivated tabs -->
    <div class="dl-status-tabs">
        <a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" class="dl-status-tab {{ $status === 'active' ? 'dl-status-tab-active' : 'dl-status-tab-inactive' }}">
            Active Staff
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'deactivated']) }}" class="dl-status-tab {{ $status === 'deactivated' ? 'dl-status-tab-active' : 'dl-status-tab-inactive' }}">
            Deactivated Staff
        </a>
    </div>

    <!-- Sub-group Tabs (Teaching, Non-Teaching, Supporting) -->
    <div class="dl-subgroup-tabs">
        <a href="{{ request()->fullUrlWithQuery(['sub_group' => 'teaching']) }}" class="dl-subgroup-tab {{ $subGroup === 'teaching' ? 'dl-subgroup-tab-active' : 'dl-subgroup-tab-inactive' }}">
            Teaching
        </a>
        <a href="{{ request()->fullUrlWithQuery(['sub_group' => 'non-teaching']) }}" class="dl-subgroup-tab {{ $subGroup === 'non-teaching' ? 'dl-subgroup-tab-active' : 'dl-subgroup-tab-inactive' }}">
            Non-Teaching
        </a>
        <a href="{{ request()->fullUrlWithQuery(['sub_group' => 'supporting']) }}" class="dl-subgroup-tab {{ $subGroup === 'supporting' ? 'dl-subgroup-tab-active' : 'dl-subgroup-tab-inactive' }}">
            Driver / Supporting Staff
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="card dl-card-filter">
        <div class="card-body dl-filter-body">
            <form method="GET" action="{{ route('school.downloads.staff-activity') }}" style="display:flex; justify-content:space-between; align-items:flex-end; gap:20px; flex-wrap:wrap;">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="hidden" name="sub_group" value="{{ $subGroup }}">
                <div style="display:flex; gap:20px; flex-grow:1; flex-wrap:wrap;">
                    <div class="form-group" style="margin-bottom:0; flex:1; min-width:260px;">
                        <label class="dl-form-label">Search Staff</label>
                        <input type="text" name="search" class="form-control dl-input" placeholder="Staff Name, Email..." value="{{ $search }}">
                    </div>
                </div>
                
                <div style="display:flex; gap:10px;">
                    <button type="submit" class="dl-btn dl-btn-primary">
                        <i class="fas fa-search"></i> VIEW
                    </button>
                    <button type="button" class="dl-btn dl-btn-outline" onclick="dlExportCSV('staffActTable', 'Staff_Activity_Logs.csv')">
                        <i class="fas fa-download"></i> DOWNLOAD CSV
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Listing -->
    <div class="dl-table-card">
        <div class="dl-table-hdr">
            <h3>Staff Activity Grid</h3>
            <span class="dl-badge-blue">{{ $totalItems }} Logs Found</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="dl-table" id="staffActTable">
                <thead>
                    <tr>
                        <th style="width:80px; text-align:center;">#</th>
                        <th style="width:120px;">Staff ID</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Highest Qualification</th>
                        <th>Mobile Number</th>
                        <th>Email ID</th>
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
                                <span class="badge dl-badge-blue">{{ $act['employee_id'] }}</span>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div class="dl-staff-avatar">
                                        {{ substr($act['name'], 0, 1) }}
                                    </div>
                                    <span style="font-weight: 800; color: #1e3a8a;">{{ $act['name'] }}</span>
                                </div>
                            </td>
                            <td style="font-weight: 700; color: #334155;">
                                {{ $act['designation'] }}
                            </td>
                            <td style="color: #475569;">
                                {{ $act['highest_qualification'] }}
                            </td>
                            <td style="font-weight: 700; color: #334155;">
                                {{ $act['mobile'] }}
                            </td>
                            <td style="color: #475569; font-size:14px !important;">
                                {{ $act['email'] }}
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
                            <td colspan="9" style="text-align:center; padding:50px; color:#64748b;">
                                <i class="fas fa-clock" style="font-size:40px; color:#cbd5e1; margin-bottom:15px; display:block;"></i>
                                No staff activity log entries found.
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

@extends('layouts.app')

@section('page-title', 'Staff Download Status')

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

    /* Stats Cards with Large Font sizes & Blue accents */
    .dl-stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 25px;
    }
    .dl-stat-card {
        border-radius: 12px;
        padding: 24px;
        text-decoration: none !important;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 4px 15px rgba(30, 58, 138, 0.04);
        transition: all 0.2s ease-in-out;
        border: 2px solid transparent;
    }
    .dl-stat-card:hover {
        transform: translateY(-2px);
    }
    .dl-stat-card-active-blue {
        background: #eff6ff;
        border-color: #3b82f6;
    }
    .dl-stat-card-inactive-blue {
        background: #ffffff;
        border-color: #e2e8f0;
    }
    .dl-stat-card-active-blue .stat-icon {
        background: #2563eb;
        color: #ffffff;
    }
    .dl-stat-card-inactive-blue .stat-icon {
        background: #94a3b8;
        color: #ffffff;
    }
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .stat-info .stat-num {
        font-size: 36px !important;
        font-weight: 850 !important;
        line-height: 1;
        color: #1e3a8a;
        margin-bottom: 4px;
    }
    .stat-info .stat-label {
        font-size: 14px !important;
        font-weight: 700 !important;
        color: #475569;
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
            <i class="fas fa-chalkboard-teacher" style="color:#2563eb;"></i>
            Staff Download Status
        </h1>
        <p>Monitor staff login telemetry and verify who hasn't accessed the portal</p>
    </div>

    <!-- Filter Bar -->
    <div class="card dl-card-filter">
        <div class="card-body dl-filter-body">
            <form method="GET" action="{{ route('school.downloads.staff-status') }}" style="display:flex; justify-content:space-between; align-items:flex-end; gap:20px; flex-wrap:wrap;">
                <div style="display:flex; gap:20px; flex-grow:1; flex-wrap:wrap;">
                    <div class="form-group" style="margin-bottom:0; flex:1; min-width:180px;">
                        <label class="dl-form-label">Select Staff Type</label>
                        <select name="staff_type" class="form-control dl-select" onchange="this.form.submit()">
                            <option value="">All Staff</option>
                            <option value="Teaching" {{ $staffType === 'Teaching' ? 'selected' : '' }}>Teaching Staff</option>
                            <option value="Non-Teaching" {{ $staffType === 'Non-Teaching' ? 'selected' : '' }}>Non-Teaching Staff</option>
                            <option value="Admin" {{ $staffType === 'Admin' ? 'selected' : '' }}>Admin Staff</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0; flex:2; min-width:240px;">
                        <label class="dl-form-label">Search Staff</label>
                        <input type="text" name="search" class="form-control dl-input" placeholder="Search Staff Name, Email..." value="{{ $search }}">
                    </div>
                </div>
                
                <div style="display:flex; gap:10px;">
                    <button type="submit" class="dl-btn dl-btn-primary">
                        <i class="fas fa-search"></i> SEARCH
                    </button>
                    <button type="button" class="dl-btn dl-btn-outline" onclick="dlExportCSV('staffMainTable', 'Staff_Download_Status.csv')">
                        <i class="fas fa-download"></i> DOWNLOAD CSV
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toggle Cards Grid -->
    <div class="dl-stats-grid">
        <!-- Total Staff Card -->
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'total']) }}" class="dl-stat-card {{ $tab === 'total' ? 'dl-stat-card-active-blue' : 'dl-stat-card-inactive-blue' }}">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-num">{{ $totalStaff->count() }}</div>
                <div class="stat-label">Total Staffs</div>
            </div>
        </a>

        <!-- Haven't Logged In Card -->
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'not_logged_in']) }}" class="dl-stat-card {{ $tab === 'not_logged_in' ? 'dl-stat-card-active-blue' : 'dl-stat-card-inactive-blue' }}">
            <div class="stat-icon" style="background:#ef4444;">
                <i class="fas fa-times"></i>
            </div>
            <div class="stat-info">
                <div class="stat-num" style="color:#b91c1c;">{{ $notLoggedIn->count() }}</div>
                <div class="stat-label">Staffs who haven't logged in</div>
            </div>
        </a>
    </div>

    <!-- Table Listing -->
    <div class="dl-table-card">
        <div class="dl-table-hdr">
            <h3>Listing: {{ $tab === 'not_logged_in' ? "Staffs who haven't logged in" : 'Total Staffs' }}</h3>
            <span class="dl-badge-blue">{{ $totalItems }} Records</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="dl-table" id="staffMainTable">
                <thead>
                    <tr>
                        <th style="width:80px; text-align:center;">#</th>
                        <th>Staff Name</th>
                        <th>Staff Type</th>
                        <th>Mobile Number</th>
                        <th>Email ID</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paginatedList as $index => $st)
                        <tr>
                            <td style="text-align:center; color:#64748b; font-weight:700;">
                                {{ sprintf('%02d.', ($page - 1) * 12 + $loop->iteration) }}
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div class="dl-staff-avatar">
                                        {{ substr($st->first_name, 0, 1) }}{{ substr($st->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 800; color: #1e3a8a;">{{ $st->first_name }} {{ $st->last_name }}</div>
                                        <small style="color: #64748b; font-size:12px;">Employee ID: EMP-{{ 1000 + $st->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="dl-badge-blue">
                                    {{ $st->role ?? 'Teaching' }}
                                </span>
                            </td>
                            <td style="font-weight: 700; color: #334155;">
                                {{ $st->mobile_number ?? '—' }}
                            </td>
                            <td style="color: #475569;">
                                {{ $st->email ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:50px; color:#64748b;">
                                <i class="fas fa-user-circle" style="font-size:40px; color:#cbd5e1; margin-bottom:15px; display:block;"></i>
                                No staff records found.
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

@extends('layouts.app')

@section('page-title', 'Student Download Status')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-user-graduate" style="color:var(--gold);margin-right:8px;"></i>Student Download Status</h1>
        <p>Monitor and manage student login status and mobile app download telemetry</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:15px 20px;">
        <form method="GET" action="{{ route('school.downloads.student-status') }}" style="display:flex; justify-content:space-between; align-items:flex-end; gap:15px; flex-wrap:wrap;">
            <div style="display:flex; gap:15px; flex-grow:1;">
                <div class="form-group" style="margin-bottom:0; flex:1; min-width:140px;">
                    <label class="form-label" style="font-size:11px; text-transform:uppercase; color:var(--t2);">Academic Year</label>
                    <select name="academic_year" class="form-control" style="padding:6px 12px; font-size:13px;">
                        <option value="2025-2026" selected>Apr 2025 - Mar 2026</option>
                        <option value="2024-2025">Apr 2024 - Mar 2025</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0; flex:1; min-width:140px;">
                    <label class="form-label" style="font-size:11px; text-transform:uppercase; color:var(--t2);">Select Class</label>
                    <select name="class_id" class="form-control" style="padding:6px 12px; font-size:13px;" onchange="this.form.submit()">
                        <option value="">All Classes</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0; flex:1; min-width:140px;">
                    <label class="form-label" style="font-size:11px; text-transform:uppercase; color:var(--t2);">Select Section</label>
                    <select name="section_id" class="form-control" style="padding:6px 12px; font-size:13px;" onchange="this.form.submit()">
                        <option value="">All Sections</option>
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}" {{ $sectionId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div style="display:flex; gap:8px;">
                <button type="button" class="btn btn-outline" style="border-color:#b45309; color:#b45309; padding:8px 16px; font-size:13px;" onclick="showToast('Loading status view...')">
                    <i class="fas fa-eye"></i> VIEW
                </button>
                <div class="dropdown-wrapper" style="position:relative; display:inline-block;">
                    <button type="button" class="btn btn-outline" style="border-color:#b45309; color:#b45309; padding:8px 16px; font-size:13px;" onclick="toggleDropdown()">
                        <i class="fas fa-download"></i> DOWNLOAD <i class="fas fa-caret-down"></i>
                    </button>
                    <div id="dlDropdown" style="display:none; position:absolute; right:0; top:100%; background:white; border:1px solid var(--border); border-radius:6px; box-shadow:0 4px 12px rgba(0,0,0,0.1); z-index:100; min-width:160px; margin-top:5px;">
                        <a href="#" style="display:block; padding:8px 12px; color:var(--t1); font-size:13px;" onclick="event.preventDefault(); showToast('Downloading PDF...'); closeDropdown();">Export PDF</a>
                        <a href="#" style="display:block; padding:8px 12px; color:var(--t1); font-size:13px;" onclick="event.preventDefault(); showToast('Downloading Excel...'); closeDropdown();">Export Excel</a>
                    </div>
                </div>
                <button type="button" class="btn btn-outline" style="border-color:#b45309; color:#b45309; padding:8px 16px; font-size:13px;" onclick="showToast('SMS dispatch initiated to all matching targets!')">
                    <i class="fas fa-envelope"></i> SEND SMS TO ALL
                </button>
                <button type="button" class="btn" style="background:#10b981; color:white; padding:8px 16px; font-size:13px; border:none;" onclick="showToast('WhatsApp dispatch initiated to all matching targets!')">
                    <i class="fab fa-whatsapp"></i> WHATSAPP TO ALL
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toggle Cards Grid -->
<div class="grid-2" style="margin-bottom:20px; grid-template-columns: 1fr 1fr;">
    <!-- Logged In Card -->
    <a href="{{ request()->fullUrlWithQuery(['tab' => 'logged_in']) }}" style="text-decoration:none;">
        <div class="card" style="margin-bottom:0; background:{{ $tab === 'logged_in' ? 'rgba(16,185,129,0.1)' : 'rgba(16,185,129,0.03)' }}; border:1px solid {{ $tab === 'logged_in' ? '#10b981' : 'transparent' }}; border-left:5px solid #10b981; cursor:pointer; transition:transform 0.2s;">
            <div class="card-body" style="padding:18px; display:flex; align-items:center; gap:15px;">
                <div style="width:42px; height:42px; border-radius:8px; background:#10b981; display:flex; align-items:center; justify-content:center; color:white; font-size:18px;">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h2 style="font-size:24px; font-weight:800; color:#065f46; margin:0;">{{ $loggedIn->count() }}</h2>
                    <span style="font-size:12px; color:#047857; font-weight:700;">Students who have logged in</span>
                </div>
            </div>
        </div>
    </a>

    <!-- Haven't Logged In Card -->
    <a href="{{ request()->fullUrlWithQuery(['tab' => 'not_logged_in']) }}" style="text-decoration:none;">
        <div class="card" style="margin-bottom:0; background:{{ $tab === 'not_logged_in' ? 'rgba(239,68,68,0.1)' : 'rgba(239,68,68,0.03)' }}; border:1px solid {{ $tab === 'not_logged_in' ? '#ef4444' : 'transparent' }}; border-left:5px solid #ef4444; cursor:pointer; transition:transform 0.2s;">
            <div class="card-body" style="padding:18px; display:flex; align-items:center; gap:15px;">
                <div style="width:42px; height:42px; border-radius:8px; background:#ef4444; display:flex; align-items:center; justify-content:center; color:white; font-size:18px;">
                    <i class="fas fa-times"></i>
                </div>
                <div>
                    <h2 style="font-size:24px; font-weight:800; color:#991b1b; margin:0;">{{ $notLoggedIn->count() }}</h2>
                    <span style="font-size:12px; color:#b91c1c; font-weight:700;">Students who haven't logged in</span>
                </div>
            </div>
        </div>
    </a>
</div>

<!-- Table Listing -->
<div class="card">
    <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Listing: {{ $tab === 'logged_in' ? 'Logged In Students' : "Haven't Logged In Students" }}</h3>
        <span class="badge {{ $tab === 'logged_in' ? 'badge-green' : 'badge-red' }}">{{ $activeList->count() }} Records</span>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width:70px; text-align:center;">#</th>
                    <th>Student Name</th>
                    <th>Father's Contact Number</th>
                    <th>Class</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activeList as $index => $st)
                    <tr>
                        <td style="text-align:center; color:var(--t3); font-weight:500;">
                            {{ sprintf('%02d.', $index + 1) }}
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:32px; height:32px; border-radius:50%; background:#e2e8f0; display:flex; align-items:center; justify-content:center; color:#64748b;">
                                    <i class="fas fa-user" style="font-size:14px;"></i>
                                </div>
                                <div>
                                    <span style="font-weight:700; color:var(--navy);">{{ $st->full_name }}</span>
                                    <small style="display:block; color:var(--t3);">Admission ID: {{ $st->admission_number }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="font-weight:500; color:var(--t1);">
                            {{ $st->guardian_phone ?? '—' }}
                        </td>
                        <td>
                            <span class="badge badge-purple" style="font-size:11px;">
                                {{ optional($st->class)->name ?? 'N/A' }} - {{ optional($st->section)->name ?? 'N/A' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:40px; color:var(--t3);">
                            <i class="fas fa-user-circle" style="font-size:32px; color:var(--t3); margin-bottom:12px; display:block;"></i>
                            No students match the criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleDropdown() {
    var dd = document.getElementById('dlDropdown');
    dd.style.display = (dd.style.display === 'none' || dd.style.display === '') ? 'block' : 'none';
}
function closeDropdown() {
    document.getElementById('dlDropdown').style.display = 'none';
}
window.onclick = function(event) {
    if (!event.target.matches('.btn-outline') && !event.target.closest('.dropdown-wrapper')) {
        closeDropdown();
    }
}
</script>
@endsection

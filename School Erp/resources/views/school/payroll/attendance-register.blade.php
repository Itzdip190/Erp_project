@extends('layouts.app')

@section('title', 'Attendance Register — HR Payroll')
@section('page-title', 'ATTENDANCE REGISTER')

@section('content')
<div style="padding: 24px; max-width: 1400px; margin: 0 auto;">

    <!-- Top Breadcrumb & Title Section -->
    <div style="margin-bottom: 20px;">
        <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; tracking: -0.02em; text-transform: uppercase;">
            ATTENDANCE REGISTER
        </h1>
    </div>

    <!-- Metadata Panel -->
    <div style="background: #ffffff; border-radius: 12px; padding: 18px 24px; border: 1px solid #e2e8f0; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div style="display: grid; grid-template-columns: auto 1fr; gap: 12px 24px; font-size: 13.5px; font-weight: 500;">
            <div style="color: #64748b;">Payroll Month</div>
            <div style="color: #1e293b; font-weight: 700;">
                : <span style="background: #e0f2fe; color: #0369a1; padding: 2px 10px; border-radius: 6px; font-size: 13px;">{{ $monthInfo['display_full'] }}</span>
            </div>

            <div style="color: #64748b;">Days in Attendance Cycle</div>
            <div style="color: #1e293b; font-weight: 700;">
                : <span style="background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 6px; font-size: 13px;">{{ $monthInfo['days_in_month'] }}</span>
            </div>

            <div style="color: #64748b;">Period Type</div>
            <div style="color: #1e293b; font-weight: 600;">
                : Calendar Month
            </div>
        </div>
    </div>

    <!-- Actions & Filter Toolbar -->
    <div style="background: #ffffff; border-radius: 12px; padding: 16px 20px; border: 1px solid #e2e8f0; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        
        <!-- Left Filter Form -->
        <form method="GET" action="{{ route('school.payroll.attendance-register') }}" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <!-- Month Selector -->
            <div style="position: relative;">
                <input type="month" name="month" value="{{ $monthInfo['picker_val'] }}" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; font-weight: 600; color: #334155; outline: none; background: #fff; cursor: pointer;">
            </div>

            <!-- Search Bar -->
            <div style="position: relative; width: 260px;">
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Staff Name" style="width: 100%; padding: 8px 12px 8px 34px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none; transition: border-color 0.2s;" onkeydown="if(event.key==='Enter') this.form.submit();">
            </div>

            <button type="submit" class="btn" style="padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer;">
                Filter
            </button>

            @if($search)
                <a href="{{ route('school.payroll.attendance-register', ['month' => $monthInfo['picker_val']]) }}" style="color: #ef4444; text-decoration: none; font-size: 13px; font-weight: 600;">
                    Reset
                </a>
            @endif
        </form>

        <!-- Right Action Button -->
        <div style="display: flex; align-items: center; gap: 16px;">
            <span style="font-size: 13px; font-weight: 600; color: #64748b;">Selected : <span id="selectedCount">0</span></span>

            <form method="POST" action="{{ route('school.payroll.freeze-attendance') }}">
                @csrf
                <input type="hidden" name="month" value="{{ $monthInfo['picker_val'] }}">
                <button type="submit" style="padding: 8px 18px; background: {{ $isFrozen ? '#ef4444' : '#bae6fd' }}; color: {{ $isFrozen ? '#ffffff' : '#0369a1' }}; border: 1px solid {{ $isFrozen ? '#dc2626' : '#7dd3fc' }}; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.2s;">
                    <i class="fas {{ $isFrozen ? 'fa-lock' : 'fa-lock-open' }}"></i>
                    <span>{{ $isFrozen ? 'Unfreeze Attendance Register' : 'Freeze Attendance Register' }}</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Notification Toast -->
    @if(session('success'))
        <div style="padding: 12px 16px; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13.5px; font-weight: 600; margin-bottom: 20px;">
            <i class="fas fa-check-circle" style="margin-right: 8px;"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Data Table -->
    <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 11.5px; letter-spacing: 0.03em;">
                        <th style="padding: 14px 16px; width: 40px; text-align: center;">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" style="cursor: pointer;">
                        </th>
                        <th style="padding: 14px 16px;">EMP ID.</th>
                        <th style="padding: 14px 16px;">NAME (CODE)</th>
                        <th style="padding: 14px 16px;">CONTACT</th>
                        <th style="padding: 14px 16px;">DESIGNATION</th>
                        <th style="padding: 14px 16px; text-align: center;">WORK ON</th>
                        <th style="padding: 14px 16px; text-align: center;">ABSENT</th>
                        <th style="padding: 14px 16px; text-align: center;">PAYABLE</th>
                        <th style="padding: 14px 16px; text-align: right;">ACTION</th>
                    </tr>
                </thead>
                <tbody style="divide-y: 1px solid #f1f5f9;">
                    @forelse($registerData as $row)
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 16px; text-align: center;">
                                <input type="checkbox" class="row-checkbox" value="{{ $row['staff']->id }}" onchange="updateCount()" style="cursor: pointer;">
                            </td>
                            <td style="padding: 14px 16px; font-weight: 600; color: #475569;">
                                {{ $row['emp_id'] }}
                            </td>
                            <td style="padding: 14px 16px; font-weight: 700; color: #1e293b; text-transform: uppercase;">
                                {{ $row['staff']->full_name }}
                            </td>
                            <td style="padding: 14px 16px; color: #64748b;">
                                {{ $row['contact'] }}
                            </td>
                            <td style="padding: 14px 16px; color: #64748b;">
                                {{ $row['designation'] }}
                            </td>
                            <td style="padding: 14px 16px; text-align: center; font-weight: 700; color: #0f172a;">
                                {{ $row['work_on'] }}
                            </td>
                            <td style="padding: 14px 16px; text-align: center; color: {{ $row['absent'] > 0 ? '#ef4444' : '#94a3b8' }}; font-weight: 700;">
                                {{ $row['absent'] > 0 ? $row['absent'] : '-' }}
                            </td>
                            <td style="padding: 14px 16px; text-align: center; font-weight: 700; color: #166534;">
                                <span style="background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 6px;">{{ $row['payable_days'] }} Days</span>
                            </td>
                            <td style="padding: 14px 16px; text-align: right;">
                                <a href="{{ route('school.payroll.generate', ['month' => $monthInfo['picker_val'], 'search' => $row['staff']->first_name]) }}" style="padding: 5px 12px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; font-weight: 600; color: #334155; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-edit" style="font-size: 11px;"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="padding: 40px; text-align: center; color: #94a3b8; font-weight: 500;">
                                No staff records found for {{ $monthInfo['display_full'] }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleSelectAll(master) {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = master.checked);
    updateCount();
}

function updateCount() {
    const checked = document.querySelectorAll('.row-checkbox:checked').length;
    document.getElementById('selectedCount').innerText = checked;
}
</script>
@endsection

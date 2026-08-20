@extends('layouts.app')

@section('title', 'Verify & Generate Salary — HR Payroll')
@section('page-title', 'VERIFY SALARY')

@section('content')
<div style="padding: 24px; max-width: 1400px; margin: 0 auto;">

    <!-- Page Title -->
    <div style="margin-bottom: 20px;">
        <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; tracking: -0.02em; text-transform: uppercase;">
            VERIFY SALARY
        </h1>
    </div>

    <!-- Metadata Box -->
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

    <!-- Batch Finalise Form Container -->
    <form id="generateSalaryForm" method="POST" action="{{ route('school.payroll.process-generate') }}">
        @csrf
        <input type="hidden" name="payroll_month" value="{{ $monthInfo['picker_val'] }}">

        <!-- Toolbar -->
        <div style="background: #ffffff; border-radius: 12px; padding: 16px 20px; border: 1px solid #e2e8f0; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            
            <!-- Left Filters -->
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <input type="month" value="{{ $monthInfo['picker_val'] }}" onchange="window.location.href='{{ route('school.payroll.generate') }}?month=' + this.value" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; font-weight: 600; color: #334155; outline: none; background: #fff; cursor: pointer;">

                <div style="position: relative; width: 260px;">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
                    <input type="text" name="search_filter" value="{{ $search }}" placeholder="Staff Name" style="width: 100%; padding: 8px 12px 8px 34px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none;" onkeydown="if(event.key==='Enter'){ event.preventDefault(); window.location.href='{{ route('school.payroll.generate') }}?month={{ $monthInfo['picker_val'] }}&search=' + this.value; }">
                </div>
            </div>

            <!-- Right Action Button -->
            <div style="display: flex; align-items: center; gap: 16px;">
                <span style="font-size: 13px; font-weight: 600; color: #64748b;">Selected : <span id="selectedCount">0</span></span>

                <button type="submit" id="btnFinalise" style="padding: 9px 20px; background: #93c5fd; color: #ffffff; border: none; border-radius: 8px; font-size: 13.5px; font-weight: 700; cursor: pointer; transition: background 0.2s;" disabled>
                    Finalise Calculation
                </button>
            </div>
        </div>

        @if(session('success'))
            <div style="padding: 12px 16px; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13.5px; font-weight: 600; margin-bottom: 20px;">
                <i class="fas fa-check-circle" style="margin-right: 8px;"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Table -->
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
                            <th style="padding: 14px 16px; text-align: right;">CTC / MONTH</th>
                            <th style="padding: 14px 16px; text-align: right;">PAYABLE AMOUNT</th>
                            <th style="padding: 14px 16px; text-align: right;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody style="divide-y: 1px solid #f1f5f9;">
                        @forelse($salaryList as $row)
                            <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 14px 16px; text-align: center;">
                                    <input type="checkbox" name="staff_ids[]" value="{{ $row['staff']->id }}" class="staff-checkbox" onchange="updateCount()" style="cursor: pointer;">
                                </td>
                                <td style="padding: 14px 16px; font-weight: 600; color: #475569;">
                                    {{ $row['emp_id'] }}
                                </td>
                                <td style="padding: 14px 16px; font-weight: 700; color: #1e293b; text-transform: uppercase;">
                                    {{ $row['staff']->full_name }}
                                </td>
                                <td style="padding: 14px 16px; text-align: right; font-weight: 600; color: #334155;">
                                    ₹ {{ number_format($row['ctc_month'], 2) }}
                                </td>
                                <td style="padding: 14px 16px; text-align: right; font-weight: 700; color: #0284c7;">
                                    ₹ {{ number_format($row['payable_amount'], 2) }}
                                    <div style="font-size: 11px; color: #64748b; font-weight: 500;">({{ $row['payable_days'] }}/{{ $row['total_days'] }} Days)</div>
                                </td>
                                <td style="padding: 14px 16px; text-align: right;">
                                    @if($row['is_generated'])
                                        <span style="background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                                            <i class="fas fa-check-circle"></i> Finalised
                                        </span>
                                    @else
                                        <button type="button" onclick="singleGenerate({{ $row['staff']->id }})" style="padding: 5px 12px; background: #2563eb; color: #ffffff; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">
                                            Finalise
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 40px; text-align: center; color: #94a3b8; font-weight: 500;">
                                    No Data available for {{ $monthInfo['display_full'] }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<script>
function toggleSelectAll(master) {
    const checkboxes = document.querySelectorAll('.staff-checkbox');
    checkboxes.forEach(cb => cb.checked = master.checked);
    updateCount();
}

function updateCount() {
    const checked = document.querySelectorAll('.staff-checkbox:checked').length;
    document.getElementById('selectedCount').innerText = checked;
    
    const btn = document.getElementById('btnFinalise');
    if (checked > 0) {
        btn.disabled = false;
        btn.style.background = '#2563eb';
    } else {
        btn.disabled = true;
        btn.style.background = '#93c5fd';
    }
}

function singleGenerate(staffId) {
    document.querySelectorAll('.staff-checkbox').forEach(cb => cb.checked = false);
    const target = document.querySelector(`.staff-checkbox[value="${staffId}"]`);
    if (target) target.checked = true;
    updateCount();
    document.getElementById('generateSalaryForm').submit();
}
</script>
@endsection

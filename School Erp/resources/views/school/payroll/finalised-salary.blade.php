@extends('layouts.app')

@section('title', 'Finalised Salary — HR Payroll')
@section('page-title', 'FINALISED SALARY')

@section('content')
<div style="padding: 24px; max-width: 1400px; margin: 0 auto;">

    <!-- Page Header -->
    <div style="margin-bottom: 20px;">
        <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; tracking: -0.02em; text-transform: uppercase;">
            FINALISED SALARY
        </h1>
    </div>

    <!-- Summary Metrics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 24px;">
        <!-- Card 1: Total Payable -->
        <div style="background: #ffffff; border-radius: 12px; padding: 20px 24px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.02em; margin-bottom: 6px;">
                    Total Payable Amount
                </div>
                <div style="font-size: 24px; font-weight: 800; color: #0284c7;">
                    ₹ {{ number_format($totalPayableAmount, 2) }}
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fas fa-wallet"></i>
            </div>
        </div>

        <!-- Card 2: Total Paid -->
        <div style="background: #ffffff; border-radius: 12px; padding: 20px 24px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.02em; margin-bottom: 6px;">
                    Total Paid Amount
                </div>
                <div style="font-size: 24px; font-weight: 800; color: #166534;">
                    ₹ {{ number_format($totalPaidAmount, 2) }}
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #dcfce7; color: #166534; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        <!-- Card 3: Remaining Balance -->
        <div style="background: #ffffff; border-radius: 12px; padding: 20px 24px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.02em; margin-bottom: 6px;">
                    Remaining Balance
                </div>
                <div style="font-size: 24px; font-weight: 800; color: #dc2626;">
                    ₹ {{ number_format($remainingBalance, 2) }}
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
    </div>

    <!-- Toolbar Filters -->
    <div style="background: #ffffff; border-radius: 12px; padding: 16px 20px; border: 1px solid #e2e8f0; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        
        <form method="GET" action="{{ route('school.payroll.finalised') }}" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; width: 100%;">
            <!-- Month Selector -->
            <input type="month" name="month" value="{{ $monthInfo['picker_val'] }}" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; font-weight: 600; color: #334155; outline: none; background: #fff; cursor: pointer;">

            <!-- Search Bar -->
            <div style="position: relative; width: 280px;">
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search Staff Name / ID..." style="width: 100%; padding: 8px 12px 8px 34px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none;" onkeydown="if(event.key==='Enter') this.form.submit();">
            </div>

            <button type="submit" style="padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer;">
                Filter
            </button>

            @if($search)
                <a href="{{ route('school.payroll.finalised', ['month' => $monthInfo['picker_val']]) }}" style="color: #ef4444; text-decoration: none; font-size: 13px; font-weight: 600;">
                    Reset Filter
                </a>
            @endif
        </form>
    </div>

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
                        <th style="padding: 14px 16px;">EMP ID.</th>
                        <th style="padding: 14px 16px;">EMPLOYEE NAME</th>
                        <th style="padding: 14px 16px;">DESIGNATION</th>
                        <th style="padding: 14px 16px;">MONTH</th>
                        <th style="padding: 14px 16px; text-align: right;">PAYABLE</th>
                        <th style="padding: 14px 16px; text-align: right;">PAID</th>
                        <th style="padding: 14px 16px; text-align: right;">BALANCE</th>
                        <th style="padding: 14px 16px; text-align: center;">STATUS</th>
                        <th style="padding: 14px 16px; text-align: right;">ACTION</th>
                    </tr>
                </thead>
                <tbody style="divide-y: 1px solid #f1f5f9;">
                    @forelse($finalisedList as $item)
                        @php
                            $empId = $item->staff->employee_id ?: 'emp-' . str_pad($item->staff->id, 4, '0', STR_PAD_LEFT);
                            $designation = $item->staff->designation ? $item->staff->designation->name : 'Staff';
                        @endphp
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 16px; font-weight: 600; color: #475569;">
                                {{ $empId }}
                            </td>
                            <td style="padding: 14px 16px; font-weight: 700; color: #1e293b; text-transform: uppercase;">
                                {{ $item->staff->full_name }}
                            </td>
                            <td style="padding: 14px 16px; color: #64748b;">
                                {{ $designation }}
                            </td>
                            <td style="padding: 14px 16px; font-weight: 600; color: #334155;">
                                {{ $item->payroll_month }}
                            </td>
                            <td style="padding: 14px 16px; text-align: right; font-weight: 700; color: #0284c7;">
                                ₹ {{ number_format($item->net_payable, 2) }}
                            </td>
                            <td style="padding: 14px 16px; text-align: right; font-weight: 700; color: #166534;">
                                ₹ {{ number_format($item->paid_amount, 2) }}
                            </td>
                            <td style="padding: 14px 16px; text-align: right; font-weight: 700; color: {{ $item->remaining_balance > 0 ? '#dc2626' : '#166534' }};">
                                ₹ {{ number_format($item->remaining_balance, 2) }}
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                @if($item->payment_status === 'paid')
                                    <span style="background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700;">PAID</span>
                                @elseif($item->payment_status === 'partially_paid')
                                    <span style="background: #fef9c3; color: #a16207; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700;">PARTIAL</span>
                                @else
                                    <span style="background: #fee2e2; color: #b91c1c; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700;">UNPAID</span>
                                @endif
                            </td>
                            <td style="padding: 14px 16px; text-align: right;">
                                <a href="{{ route('school.payroll.add-payment', ['staff_id' => $item->staff_id, 'month' => $monthInfo['picker_val']]) }}" style="padding: 6px 14px; background: #2563eb; color: #ffffff; border-radius: 6px; font-size: 12px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-hand-holding-dollar"></i> Add Payment
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="padding: 40px; text-align: center; color: #94a3b8; font-weight: 500;">
                                No finalized salary records found for {{ $monthInfo['display_full'] }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

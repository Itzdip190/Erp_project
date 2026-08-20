@extends('layouts.app')

@section('title', 'Add Payment — HR Payroll')
@section('page-title', 'ADD PAYMENT')

@section('content')
<div style="padding: 24px; max-width: 1200px; margin: 0 auto;">

    <!-- Page Header -->
    <div style="margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
        <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; tracking: -0.02em; text-transform: uppercase;">
            ADD PAYMENT
        </h1>
        <a href="{{ route('school.payroll.finalised') }}" style="color: #475569; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px;">
            <i class="fas fa-arrow-left"></i> Back to Finalised Salary
        </a>
    </div>

    <!-- Selection Bar -->
    <div style="background: #ffffff; border-radius: 12px; padding: 20px; border: 1px solid #e2e8f0; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <form method="GET" action="{{ route('school.payroll.add-payment') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)) auto; gap: 16px; align-items: end;">
            
            <!-- Employee Selector -->
            <div>
                <label style="display: block; font-size: 12.5px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 6px;">
                    Select Employee
                </label>
                <select name="staff_id" onchange="this.form.submit()" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; font-weight: 600; color: #1e293b; outline: none; background: #fff;">
                    <option value="">-- Choose Employee --</option>
                    @foreach($staffList as $st)
                        @php $empCode = $st->employee_id ?: 'emp-' . str_pad($st->id, 4, '0', STR_PAD_LEFT); @endphp
                        <option value="{{ $st->id }}" {{ (string)$selectedStaffId === (string)$st->id ? 'selected' : '' }}>
                            {{ $st->full_name }} ({{ $empCode }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Payroll Month Selector -->
            <div>
                <label style="display: block; font-size: 12.5px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 6px;">
                    Payroll Month
                </label>
                <input type="month" name="month" value="{{ $monthInfo['picker_val'] }}" onchange="this.form.submit()" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; font-weight: 600; color: #334155; outline: none; background: #fff;">
            </div>

            <div>
                <button type="submit" style="padding: 10px 20px; background: #2563eb; color: #fff; border: none; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer; width: 100%;">
                    Fetch Details
                </button>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div style="padding: 12px 16px; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13.5px; font-weight: 600; margin-bottom: 24px;">
            <i class="fas fa-check-circle" style="margin-right: 8px;"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="padding: 12px 16px; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 8px; font-size: 13.5px; font-weight: 600; margin-bottom: 24px;">
            <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i> {{ session('error') }}
        </div>
    @endif

    @if($selectedStaffId)
        @php
            $empObj = $staffList->firstWhere('id', $selectedStaffId);
            $empCode = $empObj ? ($empObj->employee_id ?: 'emp-' . str_pad($empObj->id, 4, '0', STR_PAD_LEFT)) : '';
            $payableVal = $selectedPayroll ? $selectedPayroll->net_payable : ($empObj ? $empObj->basic_salary : 0);
            $paidVal = $selectedPayroll ? $selectedPayroll->paid_amount : 0;
            $remainingVal = $selectedPayroll ? $selectedPayroll->remaining_balance : $payableVal;
        @endphp

        <!-- Salary Summary & Add Payment Layout -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-bottom: 30px;">
            
            <!-- Employee Salary Summary Card -->
            <div style="background: #ffffff; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; text-transform: uppercase;">
                            {{ $empObj->full_name }}
                        </h3>
                        <div style="font-size: 12.5px; color: #64748b; font-weight: 600;">
                            ID: {{ $empCode }} | {{ $empObj->designation ? $empObj->designation->name : 'Staff' }}
                        </div>
                    </div>
                    <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                        {{ $monthInfo['display_full'] }}
                    </span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; background: #f8fafc; border-radius: 8px;">
                        <span style="font-size: 13px; font-weight: 600; color: #475569;">Payable Amount</span>
                        <span style="font-size: 16px; font-weight: 800; color: #0284c7;">₹ {{ number_format($payableVal, 2) }}</span>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; background: #f0fdf4; border-radius: 8px;">
                        <span style="font-size: 13px; font-weight: 600; color: #166534;">Paid Amount</span>
                        <span style="font-size: 16px; font-weight: 800; color: #166534;">₹ {{ number_format($paidVal, 2) }}</span>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; background: #fef2f2; border-radius: 8px;">
                        <span style="font-size: 13px; font-weight: 600; color: #991b1b;">Remaining Amount</span>
                        <span style="font-size: 16px; font-weight: 800; color: #dc2626;">₹ {{ number_format($remainingVal, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Entry Form Card -->
            <div style="background: #ffffff; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-money-bill-wave" style="color: #2563eb;"></i> Record Payment Entry
                </h3>

                <form method="POST" action="{{ route('school.payroll.store-payment') }}">
                    @csrf
                    <input type="hidden" name="staff_id" value="{{ $selectedStaffId }}">
                    <input type="hidden" name="payroll_month" value="{{ $monthInfo['picker_val'] }}">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <!-- Payment Type -->
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 4px;">
                                Payment Type
                            </label>
                            <select name="payment_type" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-weight: 600; color: #1e293b;">
                                <option value="salary_payment">Salary Payment</option>
                                <option value="advance_payment">Advance Payment</option>
                            </select>
                        </div>

                        <!-- Amount -->
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 4px;">
                                Amount (₹) *
                            </label>
                            <input type="number" step="0.01" min="1" max="{{ max(1, $remainingVal) }}" name="amount" value="{{ $remainingVal > 0 ? $remainingVal : '' }}" placeholder="Enter Amount" required style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-weight: 700; color: #0f172a;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <!-- Payment Date -->
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 4px;">
                                Payment Date *
                            </label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-weight: 600; color: #1e293b;">
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 4px;">
                                Payment Method *
                            </label>
                            <select name="payment_method" required style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-weight: 600; color: #1e293b;">
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer / NEFT</option>
                                <option value="cheque">Cheque</option>
                                <option value="upi">Online / UPI</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 4px;">
                            Reference No. / Cheque No. / UTR
                        </label>
                        <input type="text" name="reference_no" placeholder="Optional Transaction ID" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 4px;">
                            Notes / Remarks
                        </label>
                        <textarea name="notes" rows="2" placeholder="Optional notes..." style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; outline: none;"></textarea>
                    </div>

                    <button type="submit" style="width: 100%; padding: 11px; background: #2563eb; color: #ffffff; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(37,99,235,0.25); transition: background 0.2s;">
                        Submit Payment Entry
                    </button>
                </form>
            </div>
        </div>

        <!-- Payment History Table -->
        @if($payments->isNotEmpty())
            <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <h3 style="font-size: 15px; font-weight: 800; color: #0f172a; margin-bottom: 14px; text-transform: uppercase;">
                    Payment Receipts History ({{ $monthInfo['display_full'] }})
                </h3>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 11px;">
                                <th style="padding: 10px 12px;">DATE</th>
                                <th style="padding: 10px 12px;">TYPE</th>
                                <th style="padding: 10px 12px; text-align: right;">AMOUNT</th>
                                <th style="padding: 10px 12px;">METHOD</th>
                                <th style="padding: 10px 12px;">REFERENCE</th>
                                <th style="padding: 10px 12px;">RECORDED BY</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $p)
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 10px 12px; font-weight: 600;">{{ $p->payment_date ? $p->payment_date->format('d M Y') : 'N/A' }}</td>
                                    <td style="padding: 10px 12px; text-transform: capitalize;">{{ str_replace('_', ' ', $p->payment_type) }}</td>
                                    <td style="padding: 10px 12px; text-align: right; font-weight: 700; color: #166534;">₹ {{ number_format($p->amount, 2) }}</td>
                                    <td style="padding: 10px 12px; text-transform: uppercase; font-weight: 600; color: #475569;">{{ str_replace('_', ' ', $p->payment_method) }}</td>
                                    <td style="padding: 10px 12px; color: #64748b;">{{ $p->reference_no ?: '-' }}</td>
                                    <td style="padding: 10px 12px; color: #64748b;">{{ $p->creator ? $p->creator->name : 'System' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @else
        <div style="background: #ffffff; border-radius: 12px; padding: 40px; text-align: center; border: 1px solid #e2e8f0; color: #64748b;">
            <i class="fas fa-user-check" style="font-size: 32px; color: #cbd5e1; margin-bottom: 12px;"></i>
            <div style="font-size: 15px; font-weight: 700; color: #334155; margin-bottom: 4px;">Select an Employee to Add Payment</div>
            <div style="font-size: 13px;">Choose an employee from the dropdown above to view salary balance and submit a payment entry.</div>
        </div>
    @endif
</div>
@endsection

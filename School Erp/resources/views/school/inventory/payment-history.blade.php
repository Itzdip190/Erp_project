@extends('layouts.app')

@section('page-title', 'Payment History - Inventory Management')

@section('content')
<style>
    :root {
        --erp-navy:        #1a3a4b;
        --erp-blue:        #0284c7;
        --erp-border:      #e2e8f0;
    }
    .inv-table thead th {
        background: #f8fafc;
        color: #334155;
        font-weight: 700;
        font-size: 13px;
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .inv-table tbody td {
        padding: 12px 14px;
        font-size: 13px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
    }
</style>

<div class="container-fluid py-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h4 font-weight-bold text-gray-800 mb-1">Payment History</h1>
            <p class="text-muted mb-0 small">Inventory Management / Payment & Transaction Records</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('school.inventory.billing') }}" class="btn btn-sm btn-primary shadow-sm" style="background-color: var(--erp-navy); border-color: var(--erp-navy);">
                <i class="fas fa-cart-plus me-1"></i> New Product Sale
            </a>
        </div>
    </div>

    <div class="row">
        @include('school.inventory.nav')

        <div class="col-md-9 col-lg-9 col-xl-10">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 font-weight-bold text-dark">
                        <span class="text-success fw-bold me-2">₹</span> Payment & Receipt Transactions
                    </h5>
                </div>

                <!-- Filter Search -->
                <div class="card-body p-3 border-bottom bg-light">
                    <form method="GET" action="{{ route('school.inventory.payment-history') }}" class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Search by Invoice, Customer, Payment Mode, Ref No..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-sm btn-dark fw-bold">Search</button>
                            <a href="{{ route('school.inventory.payment-history') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table inv-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Transaction Date</th>
                                    <th>Receipt / Invoice No</th>
                                    <th>Customer / Student</th>
                                    <th>Payment Mode</th>
                                    <th>Reference No</th>
                                    <th>Paid Amount</th>
                                    <th>Balance Due</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $index => $pay)
                                    <tr>
                                        <td>{{ is_object($payments) && method_exists($payments, 'firstItem') ? ($payments->firstItem() + $index) : ($index + 1) }}</td>
                                        <td>{{ !empty($pay->created_at) ? \Carbon\Carbon::parse($pay->created_at)->format('d/m/Y h:i A') : '—' }}</td>
                                        <td>
                                            <div class="fw-bold text-primary">{{ $pay->receipt_number ?: $pay->invoice_number }}</div>
                                            <div class="small text-muted">{{ $pay->invoice_number }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $pay->customer_name }}</div>
                                            <div class="small text-muted">{{ $pay->admission_no ? 'Adm: ' . $pay->admission_no : '' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase px-2 py-1">
                                                {{ $pay->payment_mode ?? 'Cash' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $pay->reference_no ?: '—' }}</span>
                                        </td>
                                        <td class="text-success fw-bold">₹ {{ number_format($pay->paid_amount, 2) }}</td>
                                        <td class="{{ $pay->due_amount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                            ₹ {{ number_format($pay->due_amount, 2) }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('school.inventory.billing.receipt', $pay->id) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Print Receipt">
                                                <i class="fas fa-print me-1"></i> Receipt
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="fas fa-money-bill-wave fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                            No payment records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if(is_object($payments) && method_exists($payments, 'hasPages') && $payments->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    {{ $payments->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

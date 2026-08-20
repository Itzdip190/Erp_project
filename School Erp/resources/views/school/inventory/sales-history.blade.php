@extends('layouts.app')

@section('page-title', 'Sales History - Inventory Management')

@section('content')
<style>
    :root {
        --erp-navy:        #1a3a4b;
        --erp-navy-dark:   #122b39;
        --erp-blue:        #0284c7;
        --erp-card-bg:     #ffffff;
        --erp-border:      #e2e8f0;
        --erp-text-dark:   #0f172a;
    }
    .kpi-card {
        background: #ffffff;
        border: 1px solid var(--erp-border);
        border-radius: 8px;
        padding: 16px 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .kpi-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .kpi-sales { background: #e0f2fe; color: #0284c7; }
    .kpi-revenue { background: #dcfce7; color: #15803d; }
    .kpi-collected { background: #fef3c7; color: #b45309; }
    .kpi-due { background: #fee2e2; color: #b91c1c; }

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
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h4 font-weight-bold text-gray-800 mb-1">Sales History</h1>
            <p class="text-muted mb-0 small">Inventory Management / Sales & Billing Records</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('school.inventory.billing') }}" class="btn btn-sm btn-primary shadow-sm" style="background-color: var(--erp-navy); border-color: var(--erp-navy);">
                <i class="fas fa-cart-plus me-1"></i> New Product Sale
            </a>
        </div>
    </div>

    <!-- KPI Summary Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon-box kpi-sales">
                    <i class="fas fa-bag-shopping"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold">Total Orders</div>
                    <h5 class="mb-0 fw-bold text-dark">{{ number_format($totalOrdersCount ?? (is_countable($sales) ? count($sales) : 0)) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon-box kpi-revenue">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold">Total Sales</div>
                    <h5 class="mb-0 fw-bold text-dark">₹ {{ number_format($totalSalesAmount ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon-box kpi-collected">
                    <i class="fas fa-hand-holding-dollar"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold">Total Collected</div>
                    <h5 class="mb-0 fw-bold text-dark">₹ {{ number_format($totalPaidAmount ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon-box kpi-due">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold">Total Due</div>
                    <h5 class="mb-0 fw-bold text-dark">₹ {{ number_format($totalDueAmount ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Navigation Menu -->
        @include('school.inventory.nav')

        <div class="col-md-9 col-lg-9 col-xl-10">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 font-weight-bold text-dark">
                        <i class="fas fa-clipboard-list me-2 text-warning"></i> Sales Records
                    </h5>
                </div>

                <!-- Filters -->
                <div class="card-body p-3 border-bottom bg-light">
                    <form method="GET" action="{{ route('school.inventory.sales-history') }}" class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Search by Invoice, Name, Phone..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date_from" class="form-control form-control-sm" placeholder="From Date" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date_to" class="form-control form-control-sm" placeholder="To Date" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2 d-flex gap-1">
                            <button type="submit" class="btn btn-sm btn-dark w-100 fw-bold">Filter</button>
                            <a href="{{ route('school.inventory.sales-history') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                </div>

                <!-- Table Content -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table inv-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice / Receipt No</th>
                                    <th>Date</th>
                                    <th>Customer / Student</th>
                                    <th>Items</th>
                                    <th>Total Amount</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                    <th>Payment Mode</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $index => $sale)
                                    <tr>
                                        <td>{{ is_object($sales) && method_exists($sales, 'firstItem') ? ($sales->firstItem() + $index) : ($index + 1) }}</td>
                                        <td>
                                            <div class="fw-bold text-primary">{{ $sale->invoice_number }}</div>
                                            <div class="small text-muted">{{ $sale->receipt_number }}</div>
                                        </td>
                                        <td>{{ !empty($sale->created_at) ? \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y h:i A') : '—' }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $sale->customer_name }}</div>
                                            <div class="small text-muted">{{ $sale->admission_no ? 'Adm: ' . $sale->admission_no : ($sale->customer_mobile ?: '') }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $sale->items ? count($sale->items) : 1 }} item(s)</span>
                                        </td>
                                        <td class="fw-bold text-dark">₹ {{ number_format($sale->grand_total, 2) }}</td>
                                        <td class="text-success fw-bold">₹ {{ number_format($sale->paid_amount, 2) }}</td>
                                        <td class="{{ $sale->due_amount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">₹ {{ number_format($sale->due_amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-info bg-opacity-10 text-info fw-semibold text-uppercase px-2 py-1">
                                                {{ $sale->payment_mode ?? 'cash' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('school.inventory.billing.receipt', $sale->id) }}" target="_blank" class="btn btn-outline-primary" title="View & Print Receipt">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <form action="{{ route('school.inventory.sales.delete', $sale->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this sale record?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                            No sales records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if(is_object($sales) && method_exists($sales, 'hasPages') && $sales->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    {{ $sales->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

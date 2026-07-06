@extends('superadmin.layouts.master')

@section('styles')
<style>
    /* Premium Table Cards and Layout */
    .order-table-card {
        border-radius: 20px !important;
        border: none !important;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.02) !important;
        overflow: hidden;
    }
    .badge-gateway {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 30px;
        text-transform: capitalize;
    }
    .gateway-stripe { background-color: rgba(99, 102, 241, 0.1); color: #4f46e5; }
    .gateway-razorpay { background-color: rgba(6, 182, 212, 0.1); color: #0891b2; }
    .gateway-paypal { background-color: rgba(59, 130, 246, 0.1); color: #1d4ed8; }
    .gateway-bank_transfer { background-color: rgba(100, 116, 139, 0.1); color: #334155; }

    .badge-order-status {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 30px;
    }
    .order-completed { background-color: #ecfdf5; color: #047857; }
    .order-pending { background-color: #fffbeb; color: #b45309; }
    .order-failed { background-color: #fef2f2; color: #b91c1c; }

    .btn-approval {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s ease;
    }

    body.dark-mode .order-table-card {
        background-color: #111827;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.3) !important;
    }
    body.dark-mode .form-control {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .form-control:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    }
    body.dark-mode .form-label, body.dark-mode label {
        color: #cbd5e1 !important;
    }
    body.dark-mode .text-dark {
        color: #f8fafc !important;
    }
    body.dark-mode .text-muted {
        color: #94a3b8 !important;
    }
    body.dark-mode table thead {
        background-color: #0f172a !important;
    }
    body.dark-mode table thead th {
        color: #cbd5e1 !important;
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode table tbody td {
        color: #cbd5e1 !important;
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode table tbody tr:hover td {
        background-color: #1a2235 !important;
    }
    body.dark-mode .modal-content {
        background-color: #111827 !important;
        color: #f8fafc !important;
        border: 1px solid #1e293b !important;
    }
    body.dark-mode .modal-header,
    body.dark-mode .modal-footer {
        background-color: #0f172a !important;
        border-color: #1e293b !important;
        color: #f8fafc !important;
    }
    body.dark-mode .modal-header .modal-title {
        color: #f8fafc !important;
    }
    body.dark-mode .modal-header .close {
        color: #f8fafc !important;
        text-shadow: none !important;
        opacity: 0.7;
    }
    body.dark-mode .modal-header .close:hover {
        opacity: 1;
    }
    body.dark-mode .btn-outline-secondary {
        border-color: #374151 !important;
        color: #94a3b8 !important;
    }
    body.dark-mode .btn-outline-secondary:hover {
        background-color: #1f2937 !important;
        color: #f8fafc !important;
    }
    table thead th {
        color: #475569 !important;
    }
    table thead {
        background-color: #f8fafc !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 font-weight-bold text-dark m-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">Orders & Payments</h1>
        <p class="text-muted m-0" style="font-size: 0.85rem;">Review transactional logs, audit payment trails, and approve manual bank receipts.</p>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; font-size: 13.5px; background-color: #ecfdf5; color: #065f46;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Filters and Search Bar -->
    <div class="card p-3 mb-4 border-0 shadow-sm" style="border-radius: 16px;">
        <form action="{{ route('superadmin.orders.index') }}" method="GET">
            <div class="row align-items-end">
                <div class="col-lg-4 col-md-6 form-group mb-2 mb-lg-0">
                    <label class="form-label font-weight-bold" style="font-size: 12px; color: #475569;">Search School</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-transparent border-right-0" style="border-top-left-radius: 10px; border-bottom-left-radius: 10px;"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Search by name or code..." value="{{ request('search') }}" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px; height: 40px;">
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 form-group mb-2 mb-lg-0">
                    <label class="form-label font-weight-bold" style="font-size: 12px; color: #475569;">Gateway</label>
                    <select name="gateway" class="form-control" style="border-radius: 10px; height: 40px;" onchange="this.form.submit()">
                        <option value="">All Gateways</option>
                        <option value="stripe" {{ request('gateway') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                        <option value="razorpay" {{ request('gateway') == 'razorpay' ? 'selected' : '' }}>Razorpay</option>
                        <option value="paypal" {{ request('gateway') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                        <option value="bank_transfer" {{ request('gateway') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-3 form-group mb-2 mb-lg-0">
                    <label class="form-label font-weight-bold" style="font-size: 12px; color: #475569;">Status</label>
                    <select name="status" class="form-control" style="border-radius: 10px; height: 40px;" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-12 mb-2 mb-lg-0 text-right">
                    <a href="{{ route('superadmin.orders.index') }}" class="btn btn-outline-secondary w-100" style="border-radius: 10px; height: 40px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="fas fa-sync-alt"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Orders Table Card -->
    <div class="card order-table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0" style="font-size: 13.5px;">
                <thead class="bg-light" style="background-color: #f8fafc !important;">
                    <tr>
                        <th class="py-3 px-4" style="font-weight: 700; color: #475569;">Order ID</th>
                        <th class="py-3" style="font-weight: 700; color: #475569;">School</th>
                        <th class="py-3" style="font-weight: 700; color: #475569;">Plan</th>
                        <th class="py-3" style="font-weight: 700; color: #475569;">Amount</th>
                        <th class="py-3" style="font-weight: 700; color: #475569;">Gateway</th>
                        <th class="py-3" style="font-weight: 700; color: #475569;">Transaction Date</th>
                        <th class="py-3" style="font-weight: 700; color: #475569;">Status</th>
                        <th class="py-3 text-right px-4" style="font-weight: 700; color: #475569; width: 180px;">Manual Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="py-3 px-4 text-muted" style="font-family: monospace; font-weight: 700;">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-3 font-weight-bold" style="color: #1e293b;">{{ $order->school->name ?? 'Deleted School' }}</td>
                            <td class="py-3"><span class="badge badge-light px-2 py-1" style="border-radius: 6px;">{{ $order->plan->name ?? 'N/A' }}</span></td>
                            <td class="py-3 font-weight-bold" style="color: #0f172a;">₹{{ number_format($order->amount, 2) }}</td>
                            <td class="py-3">
                                <span class="badge-gateway gateway-{{ $order->gateway }}">
                                    @if($order->gateway == 'bank_transfer')
                                        <i class="fas fa-university mr-1"></i> Bank
                                    @elseif($order->gateway == 'stripe')
                                        <i class="fab fa-stripe mr-1"></i> Stripe
                                    @elseif($order->gateway == 'razorpay')
                                        <i class="fas fa-credit-card mr-1"></i> Razorpay
                                    @else
                                        <i class="fas fa-dollar-sign mr-1"></i> {{ $order->gateway }}
                                    @endif
                                </span>
                            </td>
                            <td class="py-3 text-muted">{{ $order->created_at->format('M d, Y — h:i A') }}</td>
                            <td class="py-3">
                                <span class="badge-order-status order-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td class="py-3 text-right px-4">
                                @if($order->status == 'pending')
                                    <div class="d-flex justify-content-end gap-2">
                                        <!-- Approve button -->
                                        <form action="{{ route('superadmin.orders.update-status', $order->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-success btn-approval" onclick="return confirm('Approve this pending payment? This will activate the school\'s subscription.');">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>
                                        <!-- Reject button -->
                                        <form action="{{ route('superadmin.orders.update-status', $order->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="failed">
                                            <button type="submit" class="btn btn-danger btn-approval" onclick="return confirm('Mark this payment as failed/rejected?');">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-muted" style="font-size: 12px; font-style: italic;">No actions required</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No transaction logs matching filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        @if($orders->hasPages())
            <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <span class="text-muted" style="font-size: 12.5px;">Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} payments</span>
                <div>
                    {!! $orders->links('pagination::bootstrap-4') !!}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

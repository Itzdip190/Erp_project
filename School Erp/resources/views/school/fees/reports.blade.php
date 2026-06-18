@extends('layouts.app')

@section('page-title', 'Fee Reports')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-chart-line" style="color:var(--gold);margin-right:8px;"></i>Fee Collections Analytics Reports</h1>
        <p>Analyze collection summaries, revenue cycles, outstanding balances, and channel metrics</p>
    </div>
</div>

<!-- Key Stat Row -->
<div class="grid-3" style="margin-bottom:20px;">
    <div class="card" style="margin-bottom:0; background:linear-gradient(135deg, var(--navy), #1e293b); color:#fff;">
        <div class="card-body">
            <div style="font-size:12px; font-weight:700; text-transform:uppercase; color:rgba(255,255,255,0.7); margin-bottom:6px;">Total Fee Collected</div>
            <div style="font-size:28px; font-weight:800; font-family:'Plus Jakarta Sans',sans-serif;">₹{{ number_format($totalCollected, 2) }}</div>
            <div style="font-size:11px; color:rgba(255,255,255,0.5); margin-top:4px;">Successful transactions logged</div>
        </div>
    </div>
    <div class="card" style="margin-bottom:0; background:linear-gradient(135deg, #1e3a8a, #3b82f6); color:#fff;">
        <div class="card-body">
            <div style="font-size:12px; font-weight:700; text-transform:uppercase; color:rgba(255,255,255,0.7); margin-bottom:6px;">Total Outstanding Dues</div>
            <div style="font-size:28px; font-weight:800; font-family:'Plus Jakarta Sans',sans-serif;">₹{{ number_format($totalDues, 2) }}</div>
            <div style="font-size:11px; color:rgba(255,255,255,0.5); margin-top:4px;">Pending student accounts balance</div>
        </div>
    </div>
    <div class="card" style="margin-bottom:0; background:linear-gradient(135deg, #78350f, #d97706); color:#fff;">
        <div class="card-body">
            <div style="font-size:12px; font-weight:700; text-transform:uppercase; color:rgba(255,255,255,0.7); margin-bottom:6px;">Total Fees Refunded</div>
            <div style="font-size:28px; font-weight:800; font-family:'Plus Jakarta Sans',sans-serif;">₹{{ number_format($totalRefunded, 2) }}</div>
            <div style="font-size:11px; color:rgba(255,255,255,0.5); margin-top:4px;">Refund entries approved</div>
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Payment Modes Split -->
    <div class="card">
        <div class="card-hdr">
            <h3>Collection Channels Split</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Channel Mode</th>
                        <th>Collected Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receiptsByMode as $mode)
                    <tr>
                        <td><strong style="color:var(--navy); text-transform:uppercase;">{{ $mode->payment_mode }}</strong></td>
                        <td><strong>₹{{ number_format($mode->total, 2) }}</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" style="text-align:center; padding:15px; color:var(--t3);">No data found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Class wise Splitting -->
    <div class="card">
        <div class="card-hdr">
            <h3>Collections by Class</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Amount Collected</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($collectionByClass as $cl)
                    <tr>
                        <td><strong style="color:var(--navy);">{{ $cl->class_name }}</strong></td>
                        <td><strong>₹{{ number_format($cl->total, 2) }}</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" style="text-align:center; padding:15px; color:var(--t3);">No data found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

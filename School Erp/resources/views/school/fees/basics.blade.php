@extends('layouts.app')

@section('page-title', 'Fee Basics')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-money-check" style="color:var(--gold);margin-right:8px;"></i>Fee Basics & Rules</h1>
        <p>Configure default fine structures, grace periods, payment notifications and collection modes</p>
    </div>
</div>

<div class="grid-2">
    <!-- Fine & Late Fees Card -->
    <div class="card">
        <div class="card-hdr">
            <h3>Late Payment & Fine Settings</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Default Late Fee Fine (₹)</label>
                <input type="number" class="form-control" value="250" style="margin-bottom:8px;">
                <small style="color:var(--t3);">Applied automatically after the due date passes.</small>
            </div>
            <div class="form-group">
                <label class="form-label">Grace Period (Days)</label>
                <input type="number" class="form-control" value="5" style="margin-bottom:8px;">
                <small style="color:var(--t3);">Number of calendar days after due date before late fees start accumulating.</small>
            </div>
            <div class="form-group">
                <label class="form-label">Fine Accumulation Rule</label>
                <select class="form-control">
                    <option value="fixed">Fixed One-time Fine</option>
                    <option value="daily">Daily Increment (₹10/day)</option>
                    <option value="percentage">Percentage of outstanding amount (1%)</option>
                </select>
            </div>
            <button class="btn btn-navy" onclick="showToast('Fine settings saved successfully!')">
                <i class="fas fa-check-circle"></i> Save Settings
            </button>
        </div>
    </div>

    <!-- Payment Gateways & Notices -->
    <div class="card">
        <div class="card-hdr">
            <h3>Accepted Payment Methods & Reminders</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label" style="display:flex; justify-content:space-between; align-items:center;">
                    <span>Online UPI Gateway Integration</span>
                    <input type="checkbox" checked style="width:16px; height:16px;">
                </label>
            </div>
            <div class="form-group">
                <label class="form-label" style="display:flex; justify-content:space-between; align-items:center;">
                    <span>Credit & Debit Cards (Visa/Mastercard)</span>
                    <input type="checkbox" checked style="width:16px; height:16px;">
                </label>
            </div>
            <div class="form-group">
                <label class="form-label" style="display:flex; justify-content:space-between; align-items:center;">
                    <span>Cash / Cheque Offline Collection</span>
                    <input type="checkbox" checked style="width:16px; height:16px;">
                </label>
            </div>
            <hr style="margin:20px 0; border:none; border-top:1px solid var(--border);">
            <div class="form-group">
                <label class="form-label">Automated SMS Reminders</label>
                <select class="form-control">
                    <option value="3">3 days before due date</option>
                    <option value="1">1 day before due date</option>
                    <option value="0" selected>On due date only</option>
                    <option value="never">Disabled</option>
                </select>
            </div>
            <button class="btn btn-navy" onclick="showToast('Payment and reminder rules updated!')">
                <i class="fas fa-check-circle"></i> Update Rules
            </button>
        </div>
    </div>
</div>
@endsection

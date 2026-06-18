@extends('layouts.app')

@section('page-title', 'Xero Integration')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-file-invoice-dollar" style="color:var(--gold);margin-right:8px;"></i>Xero Accounting Integration</h1>
        <p>Sync invoices, receipts, and ledger items directly with your Xero accounting platform</p>
    </div>
</div>

<div class="grid-2">
    <!-- Sync Card -->
    <div class="card">
        <div class="card-hdr">
            <h3>Sync Operations</h3>
        </div>
        <div class="card-body">
            <div style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.18); border-radius:12px; padding:20px; display:flex; align-items:center; gap:16px; margin-bottom:20px;">
                <div style="width:48px; height:48px; border-radius:50%; background:var(--green); display:flex; align-items:center; justify-content:center; color:#fff; font-size:20px;">
                    <i class="fas fa-link"></i>
                </div>
                <div>
                    <h4 style="font-weight:800; color:var(--navy);">Connected with Xero</h4>
                    <p style="font-size:12px; color:var(--t2); margin-top:2px;">Last synced: 2 hours ago (Automatic sync active)</p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('school.fees.xero-integration') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Synchronize Entities</label>
                    <select class="form-control">
                        <option value="all">All Invoices & Receipts</option>
                        <option value="invoices">Invoices Only</option>
                        <option value="receipts">Receipts Only</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-gold">
                    <i class="fas fa-sync-alt"></i> Sync Now with Xero
                </button>
            </form>
        </div>
    </div>

    <!-- API Config Details -->
    <div class="card">
        <div class="card-hdr">
            <h3>Xero API Status & Configuration</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Client ID</label>
                <input type="text" class="form-control" value="xero_client_id_schoolcloud_erp_2026_prod" disabled>
            </div>
            <div class="form-group">
                <label class="form-label">Organization Name</label>
                <input type="text" class="form-control" value="Yash International School Ltd." disabled>
            </div>
            <div class="form-group">
                <label class="form-label">Auto Sync Interval</label>
                <select class="form-control" disabled>
                    <option>Every 6 Hours</option>
                    <option selected>Daily at 00:00 AM</option>
                    <option>Manual Only</option>
                </select>
            </div>
            <div style="font-size:11.5px; color:var(--t3); text-align:right;">
                To disconnect or update keys, contact superadmin system integrations team.
            </div>
        </div>
    </div>
</div>
@endsection

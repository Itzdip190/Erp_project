@extends('layouts.app')

@section('page-title', 'Statement of Account')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-file-invoice-dollar" style="color:var(--gold);margin-right:8px;"></i>Statement of Account (Ledger)</h1>
        <p>Analyze individual student account balance statements, showing all debit bills, credit payments, and active balances</p>
    </div>
</div>

<div class="grid-3">
    <!-- Student Selector -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Select Student Ledger</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('school.fees.statement-of-account') }}">
                <div class="form-group">
                    <label class="form-label">Student</label>
                    <select name="student_id" class="form-control" onchange="this.form.submit()" required>
                        <option value="">Select Student</option>
                        @foreach($students as $st)
                            <option value="{{ $st->id }}" {{ isset($selectedStudent) && $selectedStudent->id == $st->id ? 'selected' : '' }}>
                                {{ $st->full_name }} ({{ $st->admission_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-navy" style="width:100%; justify-content:center;">
                    <i class="fas fa-search"></i> Load Statement
                </button>
            </form>
        </div>
    </div>

    <!-- Ledger Statement Display -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Statement Ledger Details</h3>
            @if(isset($selectedStudent))
                <button class="btn btn-outline" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Statement
                </button>
            @endif
        </div>
        <div class="card-body" style="padding:0;">
            @if(isset($selectedStudent))
                <div style="padding:20px; border-bottom:1px solid var(--border); background:#f9fafb;">
                    <h4 style="color:var(--navy); font-weight:800;">{{ $selectedStudent->full_name }}</h4>
                    <p style="font-size:12.5px; color:var(--t2); margin-top:4px;">
                        Admission ID: <strong>{{ $selectedStudent->admission_id }}</strong> | Grade Class: <strong>{{ optional($selectedStudent->class)->name ?? 'N/A' }}</strong>
                    </p>
                </div>
                
                <div class="table-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Particulars Description</th>
                                <th>Type</th>
                                <th style="text-align:right;">Debit (₹)</th>
                                <th style="text-align:right;">Credit (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $balance = 0;
                            @endphp
                            @forelse($ledger as $item)
                                @php
                                    if ($item['type'] === 'debit') {
                                        $balance += $item['amount'];
                                    } else {
                                        $balance -= $item['amount'];
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $item['date'] }}</td>
                                    <td>{{ $item['desc'] }}</td>
                                    <td>
                                        @if($item['type'] === 'debit')
                                            <span class="badge badge-danger">Charge</span>
                                        @else
                                            <span class="badge badge-success">Payment</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right;">{{ $item['type'] === 'debit' ? '₹' . number_format($item['amount'], 2) : '—' }}</td>
                                    <td style="text-align:right;">{{ $item['type'] === 'credit' ? '₹' . number_format($item['amount'], 2) : '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center; padding:20px; color:var(--t3);">No ledger transactions recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($ledger) > 0)
                            <tfoot>
                                <tr style="background:#f1f5f9; font-weight:800; border-top:2px solid var(--navy);">
                                    <td colspan="3" style="padding:12px; text-align:right; font-size:14px;">Total Outstanding Balance:</td>
                                    <td colspan="2" style="padding:12px; text-align:right; font-size:16px; color:{{ $balance > 0 ? 'var(--red)' : 'var(--green)' }};">
                                        ₹{{ number_format($balance, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            @else
                <div style="padding:40px; text-align:center; color:var(--t3);">
                    <i class="fas fa-user-circle" style="font-size:48px; margin-bottom:12px; opacity:0.3;"></i>
                    <p>Please select a student from the sidebar dropdown to render statement ledger reports.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

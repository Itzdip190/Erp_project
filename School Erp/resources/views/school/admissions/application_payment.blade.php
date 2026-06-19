@extends('layouts.app')

@section('page-title', 'Application Payment status')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-wallet" style="color:var(--gold);margin-right:8px;"></i>Application & Payment ledger</h1>
        <p>Monitor online registration fee invoices and transactions processed through the public portal gateway</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Recent Transactions</h3>
    </div>
    <div class="card-body">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Applicant Student</th>
                        <th>Parent Email</th>
                        <th>Amount Paid</th>
                        <th>Gateway Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>TXN-2938102</strong></td>
                        <td>Aarav Mehta</td>
                        <td>aarav.mehta@gmail.com</td>
                        <td>INR 1,000.00</td>
                        <td><span class="badge badge-success">Completed</span></td>
                        <td>June 15, 2026</td>
                    </tr>
                    <tr>
                        <td><strong>TXN-2938103</strong></td>
                        <td>Diya Sen</td>
                        <td>diya.sen@gmail.com</td>
                        <td>INR 1,000.00</td>
                        <td><span class="badge badge-success">Completed</span></td>
                        <td>June 18, 2026</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

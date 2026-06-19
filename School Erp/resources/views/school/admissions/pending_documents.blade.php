@extends('layouts.app')

@section('page-title', 'Pending Documents')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-file-shield" style="color:var(--gold);margin-right:8px;"></i>Pending Documents Tracker</h1>
        <p>Monitor missing certificates (Birth Proof, Transfer Certificate, Marksheet) for incoming prospects</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Prospects with Missing Files</h3>
    </div>
    <div class="card-body">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Parent Contact</th>
                        <th>Missing Documents</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr>
                        <td><strong>{{ $lead->student_name }}</strong></td>
                        <td>{{ $lead->phone }}<br><span style="font-size:11px;color:var(--t2);">{{ $lead->email }}</span></td>
                        <td>
                            <span class="badge badge-danger">Birth Certificate</span>
                            <span class="badge badge-warning">Previous School Transfer Cert.</span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('school.admissions.pending-documents') }}">
                                @csrf
                                <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                                <button type="submit" class="btn btn-outline" style="padding:4px 8px; font-size:11px;"><i class="fas fa-envelope"></i> Email Reminder</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:30px; color:var(--t3);">All enrolled students have fully uploaded records.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

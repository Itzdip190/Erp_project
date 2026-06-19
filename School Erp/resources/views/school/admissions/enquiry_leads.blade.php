@extends('layouts.app')

@section('page-title', 'Enquiry Leads')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-filter" style="color:var(--gold);margin-right:8px;"></i>Admission Enquiry Leads</h1>
        <p>Log incoming school visitation details, phone calls, and prospect inquiries</p>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-hdr">
            <h3>Add Enquiry Lead</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.admissions.enquiry-leads') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Student Name</label>
                    <input type="text" class="form-control" name="student_name" required placeholder="e.g. Aarav Mehta">
                </div>
                <div class="form-group">
                    <label class="form-label">Parent Name</label>
                    <input type="text" class="form-control" name="parent_name" required placeholder="e.g. Rajesh Mehta">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="text" class="form-control" name="phone" required placeholder="e.g. 9876543210">
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" placeholder="e.g. aarav@gmail.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Class Interested</label>
                    <input type="text" class="form-control" name="class_interested" placeholder="e.g. Grade 1">
                </div>
                <div class="form-group">
                    <label class="form-label">Enquiry Notes</label>
                    <textarea class="form-control" name="notes" rows="4" placeholder="Type comments here..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                    <i class="fas fa-plus-circle"></i> Save Prospect Lead
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-hdr">
            <h3>Prospect Leads Directory</h3>
        </div>
        <div class="card-body" style="max-height: 520px; overflow-y:auto;">
            @forelse($leads as $lead)
            <div style="padding:15px; background:var(--page); border:1px solid var(--border); border-radius:10px; margin-bottom:15px; display:flex; flex-direction:column; gap:10px;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div>
                        <strong style="color:var(--navy); font-size:14px;">{{ $lead->student_name }}</strong>
                        <div style="font-size:11px; color:var(--t2);">Parent: {{ $lead->parent_name }} | Class: {{ $lead->class_interested ?? 'N/A' }}</div>
                    </div>
                    <span class="badge @if($lead->status === 'new') badge-warning @elseif($lead->status === 'contacted') badge-blue @elseif($lead->status === 'enrolled') badge-success @else badge-danger @endif">
                        {{ ucfirst($lead->status) }}
                    </span>
                </div>
                <div style="font-size:12px; color:var(--t2);">
                    <strong>Phone:</strong> {{ $lead->phone }} <br>
                    <strong>Notes:</strong> {{ $lead->notes ?? 'No comments.' }}
                </div>
                
                @if($lead->status !== 'enrolled')
                <form method="POST" action="{{ route('school.admissions.enquiry-leads') }}" style="display:flex; gap:8px; align-items:center; margin-top:5px;">
                    @csrf
                    <input type="hidden" name="update_status" value="1">
                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                    <select name="status" class="form-control" style="padding:4px 8px; font-size:11.5px; height:auto; width:auto; flex:1;">
                        <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="rejected" {{ $lead->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <button type="submit" class="btn btn-gold" style="padding:4px 10px; font-size:11px;"><i class="fas fa-refresh"></i> Update</button>
                </form>
                @endif
            </div>
            @empty
            <p style="text-align:center; color:var(--t3); padding:40px;">No prospects found.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

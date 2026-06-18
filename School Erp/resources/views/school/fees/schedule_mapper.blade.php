@extends('layouts.app')

@section('page-title', 'Student Class & Fee Schedule Mapper')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-calendar-alt" style="color:var(--gold);margin-right:8px;"></i>Student Class & Fee Schedule Mapper</h1>
        <p>Bulk map fee billing cycles (Monthly, Quarterly, Annually) to fee categories across the institution</p>
    </div>
</div>

<div class="grid-3">
    <!-- Schedule Config Form -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Configure Billing Cycle</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.fees.schedule-mapper') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Fee Category</label>
                    <select name="fee_category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Schedule Frequency</label>
                    <select name="schedule_type" class="form-control" required>
                        <option value="monthly">Monthly Cycle (12 Invoices/yr)</option>
                        <option value="quarterly">Quarterly Cycle (4 Invoices/yr)</option>
                        <option value="annually">Annual Cycle (1 Invoice/yr)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                    <i class="fas fa-sync"></i> Apply Billing Cycle
                </button>
            </form>
        </div>
    </div>

    <!-- Active Class Mappings List -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr">
            <h3>Fee Categories Schedule Allocations</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Class Name</th>
                            <th>Fee Category</th>
                            <th>Amount (₹)</th>
                            <th>Billing Frequency</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($structures as $struct)
                        <tr>
                            <td><strong style="color:var(--navy);">{{ $struct->class->name }}</strong></td>
                            <td>{{ $struct->category->name }}</td>
                            <td><strong>₹{{ number_format($struct->amount, 2) }}</strong></td>
                            <td><span class="badge badge-purple">{{ ucfirst($struct->schedule_type) }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding:20px; color:var(--t3);">No structure schedule mapping defined yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

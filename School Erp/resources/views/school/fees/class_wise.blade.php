@extends('layouts.app')

@section('page-title', 'Class-wise Fee')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-school" style="color:var(--gold);margin-right:8px;"></i>Class-wise Fee Structure</h1>
        <p>Define and update fee category allocations and amounts for specific class grades</p>
    </div>
</div>

<div class="grid-3">
    <!-- Mapping Form -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Define Fee Structure</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.fees.class-wise') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">School Class</label>
                    <select name="class_id" class="form-control" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Fee Category</label>
                    <select name="fee_category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Amount (₹)</label>
                    <input type="number" name="amount" class="form-control" placeholder="e.g. 3000" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Schedule Type</label>
                    <select name="schedule_type" class="form-control" required>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="annually">Annually</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                    <i class="fas fa-save"></i> Save Structure
                </button>
            </form>
        </div>
    </div>

    <!-- Active Class Structures List -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr">
            <h3>Fee Structure Allocations</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Fee Category</th>
                            <th>Amount (₹)</th>
                            <th>Billing Cycle</th>
                            <th>Date Modified</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($structures as $struct)
                        <tr>
                            <td><strong style="color:var(--navy);">{{ $struct->class->name }}</strong></td>
                            <td>{{ $struct->category->name }}</td>
                            <td><strong>₹{{ number_format($struct->amount, 2) }}</strong></td>
                            <td><span class="badge badge-blue">{{ ucfirst($struct->schedule_type) }}</span></td>
                            <td>{{ $struct->updated_at->format('Y-m-d') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:20px; color:var(--t3);">No class-wise fee structures set.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

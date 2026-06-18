@extends('layouts.app')

@section('page-title', 'Collection Follow-Up')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-bullhorn" style="color:var(--gold);margin-right:8px;"></i>Collection Follow-Up</h1>
        <p>Follow up on outstanding balances, track fee aging, and send automated email/SMS payment reminders to parents</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Overdue Outstanding Dues Audit</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Student Details</th>
                        <th>Class & Section</th>
                        <th>Fee category</th>
                        <th>Due Date</th>
                        <th>Balance Due (₹)</th>
                        <th>Reminders Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($overdueFees as $fee)
                    <tr>
                        <td>
                            <strong style="color:var(--navy);">{{ $fee->student->full_name }}</strong>
                            <small style="display:block; color:var(--t3);">{{ $fee->student->admission_id }}</small>
                        </td>
                        <td>{{ optional($fee->student->class)->name ?? 'N/A' }} - {{ optional($fee->student->section)->name ?? 'N/A' }}</td>
                        <td>{{ $fee->category->name }}</td>
                        <td><span style="color:var(--red); font-weight:700;">{{ $fee->due_date }}</span></td>
                        <td><strong>₹{{ number_format($fee->amount - $fee->paid_amount, 2) }}</strong></td>
                        <td>
                            <form method="POST" action="{{ route('school.fees.collection-followup') }}" style="display:inline;">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $fee->student_id }}">
                                <button type="submit" class="btn btn-gold" style="padding:4px 10px; font-size:11px;">
                                    <i class="fas fa-paper-plane"></i> Send Reminder
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:24px; color:var(--t2);">
                            🎉 No overdue accounts found! All collections are up to date.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

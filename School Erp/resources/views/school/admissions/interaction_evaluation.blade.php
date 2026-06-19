@extends('layouts.app')

@section('page-title', 'Interaction & Evaluation')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-file-signature" style="color:var(--gold);margin-right:8px;"></i>Interaction & Evaluation Scheduler</h1>
        <p>Book interview slots and written assessment times for prospects who passed initial screening</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Interview Schedules</h3>
    </div>
    <div class="card-body">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Candidate</th>
                        <th>Parent Phone</th>
                        <th>Class Interest</th>
                        <th>Interaction Schedule Slot</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr>
                        <td><strong>{{ $lead->student_name }}</strong></td>
                        <td>{{ $lead->phone }}</td>
                        <td>{{ $lead->class_interested }}</td>
                        <td>
                            <input type="datetime-local" class="form-control" name="slot" value="2026-06-25T10:00" style="max-width:220px; font-size:12px;">
                        </td>
                        <td>
                            <form method="POST" action="{{ route('school.admissions.interaction-evaluation') }}">
                                @csrf
                                <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                                <button type="submit" class="btn btn-gold" style="padding:4px 8px; font-size:11px;"><i class="fas fa-calendar-alt"></i> Set Slot & Invite</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:30px; color:var(--t3);">No candidates currently waiting for assessment scheduling.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

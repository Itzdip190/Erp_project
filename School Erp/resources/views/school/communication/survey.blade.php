@extends('layouts.app')

@section('page-title', 'Surveys & Opinion Polls')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-square-poll-vertical" style="color:var(--gold);margin-right:8px;"></i>Surveys & Opinion Polls</h1>
        <p>Gather feedback from parents, students, and employees using live questionnaire cards</p>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-hdr">
            <h3>Create a Poll Survey</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.communication.survey') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Poll Question</label>
                    <input type="text" class="form-control" name="question" required placeholder="e.g. Do you support the new school uniform designs?">
                </div>
                <div class="form-group">
                    <label class="form-label">Options (At least 2)</label>
                    <div style="display:flex; flex-direction:column; gap:8px;" id="optWrapper">
                        <input type="text" class="form-control" name="options[]" required placeholder="Option 1">
                        <input type="text" class="form-control" name="options[]" required placeholder="Option 2">
                        <input type="text" class="form-control" name="options[]" placeholder="Option 3 (Optional)">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                    <i class="fas fa-plus"></i> Create Opinion Survey
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-hdr">
            <h3>Active Opinion Polls</h3>
        </div>
        <div class="card-body" style="max-height: 480px; overflow-y:auto;">
            @forelse($surveys as $s)
            <div style="padding:14px; background:var(--page); border:1px solid var(--border); border-radius:9px; margin-bottom:12px;">
                <h4 style="color:var(--navy); font-size:13.5px; font-weight:700; margin-bottom:10px;">{{ $s->question }}</h4>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    @php $totalVotes = $s->responses->count(); @endphp
                    @foreach($s->options as $opt)
                        @php 
                            $optVotes = $opt->responses->count(); 
                            $pct = $totalVotes > 0 ? round(($optVotes / $totalVotes) * 100) : 0;
                        @endphp
                        <div>
                            <div style="display:flex; justify-content:space-between; font-size:11.5px; font-weight:600; color:var(--t2); margin-bottom:3px;">
                                <span>{{ $opt->option_text }}</span>
                                <span>{{ $optVotes }} votes ({{ $pct }}%)</span>
                            </div>
                            <div style="width:100%; height:8px; background:var(--border); border-radius:20px; overflow:hidden;">
                                <div style="width:{{ $pct }}%; height:100%; background:var(--gold); border-radius:20px;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div style="margin-top:12px; font-size:10.5px; color:var(--t3); display:flex; justify-content:space-between;">
                    <span>Total responses: <strong>{{ $totalVotes }}</strong></span>
                    <span>Created: {{ $s->created_at->diffForHumans() }}</span>
                </div>
            </div>
            @empty
            <p style="text-align:center; color:var(--t3); padding:40px;">No surveys found.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

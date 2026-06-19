@extends('layouts.app')

@section('page-title', 'Notice / Circular board')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-bullhorn" style="color:var(--gold);margin-right:8px;"></i>Notice / Circular Bulletin Board</h1>
        <p>Post announcements that appear on the student/parent panels and staff directories</p>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-hdr">
            <h3>Publish New Notice</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.communication.notice') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Notice Title</label>
                    <input type="text" class="form-control" name="title" required placeholder="e.g. Summer Vacation Schedule">
                </div>
                <div class="form-group">
                    <label class="form-label">Target Audience</label>
                    <select class="form-control" name="target_audience">
                        <option value="all">Everyone (All Students, Parents & Staff)</option>
                        <option value="students">Students & Parents Only</option>
                        <option value="staff">School Staff Only</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Content Body</label>
                    <textarea class="form-control" name="content" rows="6" required placeholder="Write details here..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                    <i class="fas fa-paper-plane"></i> Publish Circular Notice
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-hdr">
            <h3>Notice Bulletins History</h3>
        </div>
        <div class="card-body" style="max-height: 480px; overflow-y:auto;">
            @forelse($notices as $n)
            <div style="padding:14px; background:var(--page); border:1px solid var(--border); border-radius:9px; margin-bottom:12px;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:6px;">
                    <h4 style="color:var(--navy); font-size:13.5px; font-weight:700;">{{ $n->title }}</h4>
                    <span class="badge badge-blue" style="font-size:9.5px;">To: {{ ucfirst($n->target_audience) }}</span>
                </div>
                <p style="font-size:12.5px; color:var(--t2); line-height:1.5; margin-bottom:8px;">{{ $n->content }}</p>
                <span style="font-size:10.5px; color:var(--t3);"><i class="fas fa-clock"></i> Published on {{ $n->created_at->format('M j, Y — g:i A') }}</span>
            </div>
            @empty
            <p style="text-align:center; color:var(--t3); padding:40px;">No published notices found.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

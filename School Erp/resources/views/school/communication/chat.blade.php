@extends('layouts.app')

@section('page-title', 'Messenger')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-comments" style="color:var(--gold);margin-right:8px;"></i>Communication Chat Messenger</h1>
        <p>Chat directly with parents, students, and other staff members in real-time</p>
    </div>
</div>

<div class="card" style="height: 600px; display:flex; flex-direction:row; overflow:hidden;">
    <!-- Users list Left -->
    <div style="width: 250px; border-right: 1px solid var(--border); display:flex; flex-direction:column; background: var(--page);">
        <div style="padding:15px; border-bottom: 1px solid var(--border); font-weight:700; color:var(--navy);">Conversations</div>
        <div style="flex:1; overflow-y:auto;">
            @foreach($users as $u)
                <a href="{{ route('school.communication.chat', ['user_id' => $u->id]) }}" 
                   style="display:block; padding:12px 15px; border-bottom: 1px solid var(--border); text-decoration:none; color:var(--t1); transition:.18s; @if($selectedUserId == $u->id) background:rgba(245, 158, 11, 0.1); border-left:4px solid var(--gold); @endif">
                    <div style="font-weight:700; font-size:12.5px;">{{ $u->name }}</div>
                    <span style="font-size:10px; color:var(--t2);">{{ ucfirst($u->roles->first()?->name ?? 'User') }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Active Chat Box Right -->
    <div style="flex:1; display:flex; flex-direction:column; background:#fff;">
        @if($selectedUserId)
            @php $selUser = $users->firstWhere('id', $selectedUserId); @endphp
            <div style="padding:15px; border-bottom: 1px solid var(--border); font-weight:700; color:var(--navy); background:var(--page);">
                Chatting with: {{ $selUser->name }} ({{ ucfirst($selUser->roles->first()?->name) }})
            </div>

            <!-- Messages lists -->
            <div style="flex:1; padding:20px; overflow-y:auto; display:flex; flex-direction:column; gap:12px; background:#fafafa;" id="chatLogs">
                @forelse($messages as $msg)
                    @php $isMe = $msg->sender_id === auth()->id(); @endphp
                    <div style="display:flex; justify-content:{{ $isMe ? 'flex-end' : 'flex-start' }};">
                        <div style="max-width: 60%; padding:10px 14px; border-radius:12px; font-size:12.5px; line-height:1.45; 
                            @if($isMe) background:var(--navy); color:#fff; border-bottom-right-radius:2px; @else background:#e5e7eb; color:var(--t1); border-bottom-left-radius:2px; @endif">
                            <div>{{ $msg->message }}</div>
                            <span style="font-size:9.5px; opacity:0.75; display:block; text-align:right; margin-top:4px;">
                                {{ $msg->created_at->format('g:i A') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p style="text-align:center; color:var(--t3); padding:40px;">No message history. Say Hello!</p>
                @endforelse
            </div>

            <!-- Chat input -->
            <div style="padding:15px; border-top: 1px solid var(--border); background:var(--page);">
                <form method="POST" action="{{ route('school.communication.chat') }}" style="display:flex; gap:10px;">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $selectedUserId }}">
                    <input type="text" class="form-control" name="message" required placeholder="Type a message..." style="flex:1;">
                    <button type="submit" class="btn btn-primary" style="padding:0 20px;"><i class="fas fa-paper-plane"></i> Send</button>
                </form>
            </div>
        @else
            <div style="flex:1; display:flex; align-items:center; justify-content:center; flex-direction:column; color:var(--t3); padding:40px;">
                <i class="fas fa-comments" style="font-size:4rem; margin-bottom:15px; opacity:0.4;"></i>
                <p>Select a contact from the conversation list to start messaging.</p>
            </div>
        @endif
    </div>
</div>

@if($selectedUserId)
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const logs = document.getElementById('chatLogs');
        if (logs) logs.scrollTop = logs.scrollHeight;
    });
</script>
@endif
@endsection

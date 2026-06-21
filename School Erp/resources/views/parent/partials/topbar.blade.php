<!-- TOPBAR -->
<nav class="topbar">
    <div class="topbar-left">
        <button class="hamburger" onclick="document.getElementById('sidebar').classList.toggle('open')">
            <i class="fas fa-bars"></i>
        </button>
        <div class="greeting">
            <h2>{{ $title ?? 'Dashboard' }}</h2>
            <p>{{ $subtitle ?? 'Welcome to SchoolCloud ERP' }}</p>
        </div>
    </div>
    <div class="topbar-right">
        <div class="date-pill">
            <i class="fas fa-calendar-days"></i>
            {{ \Carbon\Carbon::now()->format('M j, Y') }}
        </div>
        <!-- Bell -->
        <div class="notif-wrap">
            <div class="notif-btn" onclick="toggleDrop('notifDrop')">
                <i class="fas fa-bell"></i>
                @if(isset($notifications) && $notifications->count() > 0)
                    <span class="notif-badge">{{ $notifications->count() }}</span>
                @endif
            </div>
            <div class="notif-drop" id="notifDrop">
                <div class="nd-hdr">
                    <strong>Notifications</strong>
                    <span class="nd-mark" onclick="document.getElementById('notifDrop').classList.remove('open')">Dismiss</span>
                </div>
                <div style="max-height: 250px; overflow-y: auto;">
                    @forelse($notifications ?? [] as $notif)
                    <a href="{{ $notif->url }}" @if($notif->type === 'document') target="_blank" @endif class="nd-item" style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-bottom:1px solid var(--border);text-decoration:none;color:var(--t1);">
                        <div style="width:28px;height:28px;border-radius:50%;background:{{ $notif->color_bg }};color:{{ $notif->color }};display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0;">
                            <i class="{{ $notif->icon }}"></i>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:11.5px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $notif->title }}</div>
                            <div style="font-size:10.5px;color:var(--t2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $notif->text }}</div>
                        </div>
                        <div style="font-size:9.5px;color:var(--t3);white-space:nowrap;">{{ \Carbon\Carbon::parse($notif->time)->diffForHumans() }}</div>
                    </a>
                    @empty
                    <div class="nd-empty">No new notifications</div>
                    @endforelse
                </div>
            </div>
        </div>
        <!-- User -->
        <div class="user-wrap">
            <div class="user-btn" onclick="toggleDrop('userDrop')">
                <div class="avatar">{{ $stuInitials }}</div>
                <div class="user-info">
                    <strong>{{ explode(' ',$stuName)[0] }}</strong>
                    <span>Student</span>
                </div>
                <i class="fas fa-chevron-down" style="font-size:9px;color:var(--t2);margin-left:4px;"></i>
            </div>
            <div class="user-drop" id="userDrop">
                <a href="{{ route('logout') }}" class="danger"><i class="fas fa-right-from-bracket"></i> Logout</a>
            </div>
        </div>
    </div>
</nav>

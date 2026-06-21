<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat Messenger — SchoolCloud ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{
    --navy:#1a1f3c;--navy2:#12172e;
    --gold:#f59e0b;--gold-bg:rgba(245,158,11,.15);
    --green:#10b981;--red:#ef4444;--purple:#8b5cf6;--blue:#3b82f6;
    --page:#f8f7f4;--white:#fff;
    --t1:#111827;--t2:#6b7280;--t3:#9ca3af;
    --border:#e5e7eb;
    --shadow:0 1px 4px rgba(0,0,0,.07);
    --shadow-lg:0 8px 32px rgba(0,0,0,.12);
}
body{font-family:'Inter',sans-serif;background:var(--page);color:var(--t1);display:flex;min-height:100vh;overflow-x:hidden;}

/* ─── SIDEBAR ─────────────────────────────────────────────── */
.sidebar{
    width:220px;min-width:220px;background:var(--navy);
    height:100vh;position:fixed;left:0;top:0;
    display:flex;flex-direction:column;z-index:200;
    overflow-y:auto;overflow-x:hidden;transition:width .3s;
}
.sidebar::-webkit-scrollbar{width:3px;}
.sidebar::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:4px;}
.sb-logo{
    padding:18px 14px 14px;display:flex;align-items:center;gap:9px;
    border-bottom:1px solid rgba(255,255,255,.08);text-decoration:none;flex-shrink:0;
}
.sb-logo-icon{
    width:34px;height:34px;border-radius:9px;background:var(--gold);
    display:flex;align-items:center;justify-content:center;font-size:16px;color:var(--navy);flex-shrink:0;
}
.sb-logo-text strong{display:block;color:#fff;font-size:13px;font-weight:800;font-family:'Plus Jakarta Sans',sans-serif;line-height:1.15;}
.sb-logo-text span{color:var(--gold);font-size:9.5px;font-weight:500;}

/* Student profile card in sidebar */
.sb-student{
    margin:12px 10px;
    background:rgba(255,255,255,.07);
    border:1px solid rgba(255,255,255,.1);
    border-radius:10px;padding:12px;flex-shrink:0;
    text-align:center;
}
.sb-stu-avatar{
    width:50px;height:50px;border-radius:50%;
    background:linear-gradient(135deg,var(--gold),#f97316);
    display:flex;align-items:center;justify-content:center;
    color:var(--navy);font-size:18px;font-weight:800;
    margin:0 auto 8px;overflow:hidden;
}
.sb-stu-avatar img{width:100%;height:100%;object-fit:cover;}
.sb-stu-name{color:#fff;font-size:12px;font-weight:700;margin-bottom:2px;}
.sb-stu-class{color:rgba(255,255,255,.5);font-size:10px;}
.sb-admit{
    display:inline-flex;align-items:center;gap:4px;
    background:var(--gold-bg);color:var(--gold);
    font-size:9.5px;font-weight:700;border-radius:20px;padding:2px 8px;margin-top:6px;
}

/* Nav */
.sb-nav{list-style:none;padding:6px 0;flex:1;overflow-y:auto;overflow-x:hidden;}
.sb-group{margin-bottom:8px;border-bottom:1px solid rgba(255,255,255,.03);padding-bottom:8px;}
.sb-group:last-child{border-bottom:none;}
.sb-hdr{
    display:flex;align-items:center;justify-content:space-between;
    padding:8px 10px;cursor:pointer;user-select:none;
    color:rgba(255,255,255,.75);transition:all .2s;border-radius:6px;
    margin:0 6px;
}
.sb-hdr:hover{background:rgba(255,255,255,.05);color:#fff;}
.sb-hdr-left{display:flex;align-items:center;gap:6px;}
.sb-hdr-icon{
    width:22px;height:22px;border-radius:50%;background:#f59e0b;
    display:flex;align-items:center;justify-content:center;
    color:#fff;font-size:9.5px;flex-shrink:0;
}
.sb-hdr-title{font-family:'Plus Jakarta Sans',sans-serif;color:#fff;font-size:11px;font-weight:700;letter-spacing:0.2px;}
.sb-hdr-arrow{font-size:9px;color:rgba(255,255,255,.3);transition:transform .2s;}

.sb-submenu{list-style:none;padding:2px 6px 2px 20px;display:none;}
.sb-submenu.open{display:block;}
.sb-submenu li{margin-bottom:1px;}
.sb-submenu a{
    display:flex;align-items:center;justify-content:space-between;
    padding:6px 8px;border-radius:6px;
    color:rgba(255,255,255,.55);font-size:11px;font-weight:500;
    text-decoration:none;transition:all .18s;
}
.sb-submenu a:hover{color:#fff;background:rgba(255,255,255,.05);}
.sb-submenu li.active a{color:#f59e0b;font-weight:700;}
.sb-submenu-label{display:flex;align-items:center;gap:6px;}
.sb-submenu-icon{font-size:9px;color:#f59e0b;flex-shrink:0;opacity:0.85;}

.sb-bottom{padding:10px;border-top:1px solid rgba(255,255,255,.08);flex-shrink:0;}
.sb-logout{
    display:flex;align-items:center;gap:8px;color:rgba(255,255,255,.4);
    font-size:11.5px;padding:7px 9px;border-radius:7px;text-decoration:none;transition:.2s;
}
.sb-logout:hover{background:rgba(239,68,68,.12);color:#ef4444;}

/* ─── MAIN ────────────────────────────────────────────────── */
.main{margin-left:220px;flex:1;display:flex;flex-direction:column;min-height:100vh;}

/* ─── TOPBAR ─────────────────────────────────────────────── */
.topbar{
    background:#white;background-color:#fff;border-bottom:1px solid var(--border);
    height:62px;padding:0 22px;
    display:flex;align-items:center;justify-content:space-between;
    position:sticky;top:0;z-index:100;
    box-shadow:0 1px 3px rgba(0,0,0,.05);
}
.topbar-left{display:flex;align-items:center;gap:13px;}
.hamburger{background:none;border:none;color:var(--t2);font-size:17px;cursor:pointer;padding:4px;display:none;}
.greeting h2{font-family:'Plus Jakarta Sans',sans-serif;font-size:15px;font-weight:700;color:var(--t1);line-height:1.2;}
.greeting p{font-size:11.5px;color:var(--t2);}
.greeting a{color:var(--gold);text-decoration:none;font-weight:600;}
.topbar-right{display:flex;align-items:center;gap:10px;}
.date-pill{
    display:flex;align-items:center;gap:6px;background:var(--page);
    border:1px solid var(--border);border-radius:8px;padding:6px 11px;
    font-size:11.5px;color:var(--t2);
}
.date-pill i{color:var(--gold);}
.notif-wrap{position:relative;}
.notif-btn{
    background:var(--page);border:1px solid var(--border);border-radius:8px;
    width:37px;height:37px;display:flex;align-items:center;justify-content:center;
    cursor:pointer;color:var(--t2);font-size:15px;transition:.2s;position:relative;
}
.notif-btn:hover{border-color:var(--gold);color:var(--gold);}
.notif-badge{
    position:absolute;top:-5px;right:-5px;background:var(--red);color:#fff;
    font-size:9px;font-weight:700;border-radius:10px;padding:1px 5px;min-width:16px;text-align:center;
}
.notif-drop{
    position:absolute;top:calc(100% + 8px);right:0;width:280px;
    background:#fff;border:1px solid var(--border);border-radius:12px;
    box-shadow:var(--shadow-lg);display:none;z-index:300;overflow:hidden;
}
.notif-drop.open{display:block;}
.nd-hdr{padding:12px 14px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;}
.nd-hdr strong{font-size:12.5px;}
.nd-mark{font-size:11px;color:var(--gold);cursor:pointer;}
.nd-empty{padding:22px;text-align:center;color:var(--t3);font-size:11.5px;}
.user-wrap{position:relative;}
.user-btn{
    display:flex;align-items:center;gap:7px;cursor:pointer;
    padding:4px 7px;border-radius:9px;border:1px solid transparent;transition:.2s;
}
.user-btn:hover{background:var(--page);border-color:var(--border);}
.avatar{
    width:34px;height:34px;border-radius:9px;
    background:linear-gradient(135deg,var(--gold),#f97316);
    display:flex;align-items:center;justify-content:center;
    color:var(--navy);font-size:12px;font-weight:800;overflow:hidden;flex-shrink:0;
}
.user-info strong{display:block;font-size:11.5px;font-weight:700;color:var(--t1);}
.user-info span{font-size:10px;color:var(--t2);}
.user-drop{
    position:absolute;top:calc(100% + 8px);right:0;width:170px;
    background:#fff;border:1px solid var(--border);border-radius:11px;
    box-shadow:var(--shadow-lg);display:none;z-index:300;overflow:hidden;
}
.user-drop.open{display:block;}
.user-drop a{display:flex;align-items:center;gap:9px;padding:10px 13px;font-size:12.5px;color:var(--t1);text-decoration:none;transition:.15s;}
.user-drop a:hover{background:var(--page);}
.user-drop a.danger{color:var(--red);}
.user-drop a i{width:13px;text-align:center;color:var(--t2);font-size:12px;}
.user-drop a.danger i{color:var(--red);}

/* ─── PAGE ────────────────────────────────────────────────── */
.pg{padding:20px 22px; display:flex; flex-direction:column; flex:1;}

/* CHAT CARD LAYOUT */
.chat-container{
    display:grid; grid-template-columns:250px 1fr; border:1px solid var(--border);
    border-radius:13px; background:#fff; height:calc(100vh - 180px); min-height:480px; box-shadow:var(--shadow); overflow:hidden;
}
@media(max-width:768px){
    .chat-container{grid-template-columns:1fr;}
}

/* Chat directory sidebar */
.chat-dir{border-right:1px solid var(--border); display:flex; flex-direction:column; background:var(--page);}
.chat-dir-hdr{padding:15px; border-bottom:1px solid var(--border); font-size:13px; font-weight:700; color:var(--navy);}
.chat-contacts{flex:1; overflow-y:auto; list-style:none;}
.contact-item{
    padding:12px 15px; display:flex; align-items:center; gap:10px; cursor:pointer;
    border-bottom:1px solid rgba(0,0,0,0.03); transition:.18s;
}
.contact-item:hover{background:rgba(245,158,11,0.05);}
.contact-item.active{background:#fff; border-left:4px solid var(--gold);}
.contact-avatar{
    width:36px; height:36px; border-radius:50%; background:var(--gold);
    display:flex; align-items:center; justify-content:center; color:var(--navy); font-size:13px; font-weight:750;
}
.contact-info strong{display:block; font-size:12.5px; color:var(--t1);}
.contact-info span{font-size:10px; color:var(--t2);}

/* Chat message window */
.chat-window{display:flex; flex-direction:column; height:100%;}
.chat-window-hdr{
    padding:15px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:12px;
}
.chat-messages{flex:1; padding:20px; overflow-y:auto; display:flex; flex-direction:column; gap:15px; background:#fafafa;}

/* Bubbles */
.msg-bubble-wrap{display:flex; flex-direction:column; width:fit-content; max-width:70%;}
.msg-bubble-wrap.sent{align-self:flex-end; align-items:flex-end;}
.msg-bubble-wrap.received{align-self:flex-start; align-items:flex-start;}

.msg-bubble{padding:10px 14px; border-radius:12px; font-size:13px; line-height:1.5; word-break:break-word;}
.msg-bubble-wrap.sent .msg-bubble{background:var(--navy); color:#fff; border-bottom-right-radius:2px;}
.msg-bubble-wrap.received .msg-bubble{background:var(--border); color:var(--t1); border-bottom-left-radius:2px;}
.msg-time{font-size:9.5px; color:var(--t3); margin-top:4px;}

/* Form */
.chat-form-wrap{padding:15px; border-top:1px solid var(--border); background:#fff;}
.chat-form{display:flex; gap:10px; align-items:center;}
.chat-input{
    flex:1; height:40px; padding:0 15px; border:1px solid var(--border);
    border-radius:20px; outline:none; font-size:13px; transition:.2s;
}
.chat-input:focus{border-color:var(--gold);}
.chat-send-btn{
    width:40px; height:40px; border-radius:50%; background:var(--gold); border:none;
    color:var(--navy); cursor:pointer; display:flex; align-items:center; justify-content:center;
    font-size:14px; transition:.2s;
}
.chat-send-btn:hover{background:#d97706;}

.footer{display:flex;align-items:center;justify-content:space-between;padding:14px 0 6px;border-top:1px solid var(--border);font-size:10.5px;color:var(--t3);}

@media(max-width:1024px){
    .sidebar{width:54px;}
    .sb-logo-text,.sb-student,.sb-hdr-title,.sb-hdr-arrow,.sb-submenu,.sb-bottom{display:none!important;}
    .sb-hdr{justify-content:center;padding:10px 0;margin:0;}
    .sb-hdr-icon{width:24px;height:24px;font-size:11px;}
    .main{margin-left:54px;}
    .hamburger{display:flex;}
}
@media(max-width:768px){
    .sidebar{transform:translateX(-220px);width:220px;}
    .sidebar.open{transform:translateX(0);}
    .sb-logo-text,.sb-student,.sb-hdr-title,.sb-hdr-arrow,.sb-bottom{display:block!important;}
    .sb-submenu{display:none;}
    .sb-submenu.open{display:block!important;}
    .sb-hdr{justify-content:space-between!important;padding:8px 10px!important;margin:0 6px!important;}
    .sb-hdr-icon{width:22px!important;height:22px!important;font-size:9.5px!important;}
    .main{margin-left:0;}
}
</style>
</head>
<body>

<!-- ══════════ SIDEBAR ══════════ -->
@include('parent.partials.sidebar')

<!-- ══════════ MAIN ══════════ -->
<div class="main">

    <!-- TOPBAR -->
    @include('parent.partials.topbar', [
        'title' => 'Chat Messenger',
        'subtitle' => 'Direct communication with class teachers and erp administrators'
    ])

    <!-- PAGE -->
    <div class="pg">
        @php
            // Find active teacher for chat
            $activeTeacher = null;
            $selectedTeacherId = request()->query('teacher_id');
            if ($selectedTeacherId) {
                $activeTeacher = $teachers->first(fn($t) => $t->user_id == $selectedTeacherId);
            }
            if (!$activeTeacher) {
                $activeTeacher = $teachers->first();
            }
            
            // Filter messages for active teacher conversation
            $activeMessages = collect();
            if ($activeTeacher) {
                $activeMessages = $messages->filter(fn($m) => 
                    ($m->sender_id == $user->id && $m->receiver_id == $activeTeacher->user_id) ||
                    ($m->sender_id == $activeTeacher->user_id && $m->receiver_id == $user->id)
                );
            }
        @endphp
        
        <div class="chat-container">
            <!-- Directory list -->
            <div class="chat-dir">
                <div class="chat-dir-hdr"><i class="fas fa-address-book"></i> Contacts / Teachers</div>
                <ul class="chat-contacts">
                    @forelse($teachers as $teacher)
                    @if($teacher->user)
                    @php
                        $tInitials = strtoupper(substr($teacher->user->name, 0, 1));
                    @endphp
                    <li onclick="window.location.href='?teacher_id={{ $teacher->user_id }}'" class="contact-item @if($activeTeacher && $activeTeacher->user_id == $teacher->user_id) active @endif">
                        <div class="contact-avatar">{{ $tInitials }}</div>
                        <div class="contact-info">
                            <strong>{{ $teacher->user->name }}</strong>
                            <span>{{ $teacher->designation ?? 'Teacher' }}</span>
                        </div>
                    </li>
                    @endif
                    @empty
                    <li style="padding:20px; text-align:center; color:var(--t3); font-size:12px;">No teachers assigned.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Chat Window -->
            <div class="chat-window">
                @if($activeTeacher && $activeTeacher->user)
                <div class="chat-window-hdr">
                    @php
                        $tInitials = strtoupper(substr($activeTeacher->user->name, 0, 1));
                    @endphp
                    <div class="contact-avatar" style="width:38px; height:38px; font-size:14px;">{{ $tInitials }}</div>
                    <div>
                        <strong style="font-size:13.5px; color:var(--navy); font-weight:750;">{{ $activeTeacher->user->name }}</strong>
                        <div style="font-size:10px; color:var(--green); font-weight:600;"><i class="fas fa-circle" style="font-size:8px;"></i> Online</div>
                    </div>
                </div>

                <div class="chat-messages" id="chatMessages">
                    @forelse($activeMessages as $msg)
                    @php
                        $isSent = $msg->sender_id == $user->id;
                    @endphp
                    <div class="msg-bubble-wrap @if($isSent) sent @else received @endif">
                        <div class="msg-bubble">{{ $msg->message }}</div>
                        <span class="msg-time">{{ \Carbon\Carbon::parse($msg->created_at)->format('h:i A') }}</span>
                    </div>
                    @empty
                    <div style="margin:auto; text-align:center; color:var(--t3);">
                        <i class="far fa-comments" style="font-size:42px; display:block; margin-bottom:12px; color:var(--border);"></i>
                        Start a conversation with {{ $activeTeacher->user->name }}
                    </div>
                    @endforelse
                </div>

                <div class="chat-form-wrap">
                    <form class="chat-form" method="POST" action="{{ route('parent.chat.send') }}">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $activeTeacher->user_id }}">
                        <input type="text" name="message" class="chat-input" placeholder="Type your message here..." required autocomplete="off">
                        <button type="submit" class="chat-send-btn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
                @else
                <div style="margin:auto; text-align:center; color:var(--t3);">
                    <i class="far fa-user" style="font-size:42px; display:block; margin-bottom:12px; color:var(--border);"></i>
                    Select a contact to start chatting.
                </div>
                @endif
            </div>
        </div>

        <div class="footer" style="margin-top:20px;">
            <span>© 2026 SchoolCloud ERP. All rights reserved.</span>
            <span>Version 2.0.0 &nbsp;|&nbsp; 🔒 Secure & Trusted</span>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Accordion toggle
    document.querySelectorAll('.sb-hdr').forEach(hdr => {
        hdr.addEventListener('click', () => {
            const submenu = hdr.nextElementSibling;
            if (submenu && submenu.classList.contains('sb-submenu')) {
                hdr.classList.toggle('open');
                submenu.classList.toggle('open');
            }
        });
    });

    // Auto-expand current active menu
    document.querySelectorAll('.sb-submenu').forEach(submenu => {
        if (submenu.querySelector('li.active')) {
            submenu.classList.add('open');
            const hdr = submenu.previousElementSibling;
            if (hdr && hdr.classList.contains('sb-hdr')) {
                hdr.classList.add('open');
            }
        }
    });

    // Scroll chat to bottom
    const chatContainer = document.getElementById('chatMessages');
    if (chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
});

function toggleDrop(id){
    ['userDrop', 'notifDrop'].forEach(d=>{if(d!==id)document.getElementById(d).classList.remove('open');});
    document.getElementById(id).classList.toggle('open');
}
document.addEventListener('click',e=>{
    if(!e.target.closest('.user-wrap'))document.getElementById('userDrop').classList.remove('open');
    if(!e.target.closest('.notif-wrap'))document.getElementById('notifDrop').classList.remove('open');
});
</script>
</body>
</html>

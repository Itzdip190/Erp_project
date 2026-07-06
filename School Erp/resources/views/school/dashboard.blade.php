@extends('layouts.app')

@section('title', 'School Dashboard')

@section('styles')
<style>
/* ─── DASHBOARD WELCOME ─────────────────────────────── */
.dash-welcome {
    background: linear-gradient(135deg, #1a1f3c 0%, #2563eb 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 8px 32px rgba(37,99,235,.25);
}
.dash-welcome::before {
    content: '';
    position: absolute;
    right: -60px; top: -60px;
    width: 220px; height: 220px;
    background: rgba(255,255,255,.05);
    border-radius: 50%;
}
.dash-welcome-text h2 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 20px; font-weight: 800; color: #fff;
    margin-bottom: 6px;
}
.dash-welcome-text p { color: rgba(255,255,255,.7); font-size: 13px; line-height: 1.6; max-width: 500px; }

/* ─── STAT CARD ────────────────────────────────────── */
.stat-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 20px 22px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
    transition: transform .2s, box-shadow .2s;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
.stat-icon {
    width: 52px; height: 52px;
    border-radius: 13px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; flex-shrink: 0;
}
.stat-val { font-size: 2rem; font-weight: 800; color: var(--t1); line-height: 1; }
.stat-lbl { font-size: 11.5px; font-weight: 700; color: var(--t2); text-transform: uppercase; letter-spacing: .5px; margin-top: 4px; }

/* ─── ROBOT CHATBOT ─────────────────────────────────── */
#robot-assistant {
    position: fixed;
    bottom: 28px;
    right: 28px;
    z-index: 9000;
    cursor: pointer;
    user-select: none;
}
.robot-body {
    width: 72px;
    height: 72px;
    background: linear-gradient(145deg, #1e3a8a, #2563eb);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 28px rgba(37,99,235,.55), 0 0 0 3px rgba(37,99,235,.2);
    transition: transform .25s cubic-bezier(.34,1.56,.64,1), box-shadow .25s;
    position: relative;
    overflow: visible;
}
.robot-body:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 36px rgba(37,99,235,.65), 0 0 0 4px rgba(37,99,235,.3);
}
.robot-face {
    width: 54px;
    height: 48px;
    background: linear-gradient(180deg, #1d4ed8, #1e40af);
    border-radius: 12px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
/* Antenna */
.robot-antenna {
    position: absolute;
    top: -14px;
    left: 50%;
    transform: translateX(-50%);
    width: 3px;
    height: 12px;
    background: #93c5fd;
    border-radius: 2px;
}
.robot-antenna::after {
    content: '';
    position: absolute;
    top: -5px; left: 50%;
    transform: translateX(-50%);
    width: 7px; height: 7px;
    background: #60a5fa;
    border-radius: 50%;
    animation: antenna-blink 2s ease-in-out infinite;
}
@keyframes antenna-blink {
    0%, 100% { opacity: 1; box-shadow: 0 0 6px #60a5fa; }
    50% { opacity: .4; box-shadow: none; }
}
/* Eyes */
.robot-eye {
    width: 16px;
    height: 16px;
    background: #fff;
    border-radius: 50%;
    position: relative;
    overflow: visible;
    flex-shrink: 0;
    box-shadow: 0 0 8px rgba(255,255,255,.4);
}
.robot-pupil {
    width: 7px;
    height: 7px;
    background: #1e3a8a;
    border-radius: 50%;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    transition: transform .08s linear;
}
.robot-pupil::after {
    content: '';
    width: 2px;
    height: 2px;
    background: rgba(255,255,255,.9);
    border-radius: 50%;
    position: absolute;
    top: 1px;
    left: 1px;
}
/* Mouth */
.robot-mouth {
    position: absolute;
    bottom: 8px;
    left: 50%;
    transform: translateX(-50%);
    width: 18px;
    height: 4px;
    background: #93c5fd;
    border-radius: 2px;
    transition: all .3s;
}
.robot-body:hover .robot-mouth {
    width: 22px;
    height: 5px;
    background: #60a5fa;
    border-radius: 3px;
    bottom: 7px;
    clip-path: ellipse(11px 4px at 50% 0%);
}

/* Pulse ring */
.robot-pulse {
    position: absolute;
    inset: -6px;
    border-radius: 26px;
    background: transparent;
    border: 2px solid rgba(96,165,250,.5);
    animation: pulse-ring 2.5s ease-out infinite;
}
@keyframes pulse-ring {
    0%   { opacity: .7; transform: scale(1); }
    100% { opacity: 0; transform: scale(1.35); }
}

/* Robot label */
.robot-label {
    position: absolute;
    top: -28px;
    left: 50%;
    transform: translateX(-50%);
    background: #1e3a8a;
    color: #93c5fd;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 20px;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(0,0,0,.3);
    animation: float-label 3s ease-in-out infinite;
}
@keyframes float-label {
    0%, 100% { transform: translateX(-50%) translateY(0); }
    50% { transform: translateX(-50%) translateY(-4px); }
}

/* ─── CHAT PANEL ─────────────────────────────────────── */
#robot-chat-panel {
    position: fixed;
    bottom: 115px;
    right: 28px;
    width: 340px;
    max-height: 450px;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 16px 48px rgba(0,0,0,.2), 0 0 0 1px rgba(37,99,235,.1);
    z-index: 8999;
    display: none;
    flex-direction: column;
    overflow: hidden;
    transform-origin: bottom right;
    animation: chat-open .3s cubic-bezier(.34,1.56,.64,1);
}
@keyframes chat-open {
    from { opacity: 0; transform: scale(.85); }
    to   { opacity: 1; transform: scale(1); }
}
#robot-chat-panel.open { display: flex; }

.chat-header {
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}
.chat-header-avatar {
    width: 32px; height: 32px;
    background: rgba(255,255,255,.15);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; color: #fff;
}
.chat-header-info strong { color: #fff; font-size: 13.5px; display: block; }
.chat-header-info span { color: rgba(255,255,255,.65); font-size: 11px; }
.chat-close-btn {
    margin-left: auto;
    background: rgba(255,255,255,.15);
    border: none; color: #fff;
    width: 28px; height: 28px;
    border-radius: 8px;
    cursor: pointer; font-size: 13px;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s;
}
.chat-close-btn:hover { background: rgba(255,255,255,.3); }

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    background: #f8f9ff;
}
.chat-msg { display: flex; align-items: flex-end; gap: 8px; }
.chat-msg.bot { flex-direction: row; }
.chat-msg.user { flex-direction: row-reverse; }
.chat-bubble {
    max-width: 230px;
    padding: 9px 13px;
    border-radius: 14px;
    font-size: 12.5px;
    line-height: 1.5;
}
.chat-msg.bot .chat-bubble {
    background: #fff;
    color: #1f2937;
    border-radius: 14px 14px 14px 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}
.chat-msg.user .chat-bubble {
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    color: #fff;
    border-radius: 14px 14px 4px 14px;
}
.chat-bot-avatar {
    width: 26px; height: 26px;
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; color: #fff;
    flex-shrink: 0;
}
.chat-typing {
    display: flex; gap: 4px; align-items: center;
    padding: 10px 14px;
}
.chat-typing span {
    width: 6px; height: 6px;
    background: #93c5fd;
    border-radius: 50%;
    animation: typing-dot 1.2s ease-in-out infinite;
}
.chat-typing span:nth-child(2) { animation-delay: .2s; }
.chat-typing span:nth-child(3) { animation-delay: .4s; }
@keyframes typing-dot {
    0%, 100% { transform: translateY(0); opacity: .5; }
    50% { transform: translateY(-4px); opacity: 1; }
}

.chat-input-area {
    padding: 12px 14px;
    background: #fff;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 8px;
    align-items: center;
    flex-shrink: 0;
}
.chat-input {
    flex: 1;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 8px 12px;
    font-size: 13px;
    outline: none;
    font-family: 'Inter', sans-serif;
    background: #f9fafb;
    transition: border-color .2s;
}
.chat-input:focus { border-color: #2563eb; background: #fff; }
.chat-send-btn {
    width: 36px; height: 36px;
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    border: none; border-radius: 10px;
    color: #fff; font-size: 13px;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: opacity .2s;
    flex-shrink: 0;
}
.chat-send-btn:hover { opacity: .85; }
</style>
@endsection

@section('content')
{{-- Welcome Banner --}}
<div class="dash-welcome">
    <div class="dash-welcome-text">
        <h2>👋 Welcome to SchoolCloud ERP</h2>
        <p>You are logged in as a school administrator. Manage student admissions, promote students, mark attendance, and view analytics from the sidebar.</p>
    </div>
    <div style="flex-shrink:0;">
        <i class="fas fa-shield-halved" style="font-size:48px;color:rgba(255,255,255,.15);"></i>
    </div>
</div>

{{-- Stat Cards --}}
<div class="grid-3" style="margin-bottom:22px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,.1);color:#2563eb;">
            <i class="fa fa-graduation-cap"></i>
        </div>
        <div>
            <div class="stat-val">{{ $studentsCount }}</div>
            <div class="stat-lbl">Total Students</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(16,185,129,.1);color:#10b981;">
            <i class="fa fa-user-tie"></i>
        </div>
        <div>
            <div class="stat-val">{{ $staffCount }}</div>
            <div class="stat-lbl">Active Staff</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,.1);color:#f59e0b;">
            <i class="fa fa-chart-bar"></i>
        </div>
        <div>
            <div class="stat-val">{{ $attendanceRate }}%</div>
            <div class="stat-lbl">Daily Attendance</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- ══════════════════════════════════════════════════════════════
     ROBOTIC CHATBOT with Mouse-Tracking Eyes
══════════════════════════════════════════════════════════════ --}}

{{-- Robot HTML --}}
<div id="robot-assistant" title="Click to chat with AI Assistant">
    <div class="robot-label">AI Assistant</div>
    <div class="robot-body" id="robotBody">
        <div class="robot-pulse"></div>
        <div class="robot-face">
            <div class="robot-antenna"></div>
            <div class="robot-eye" id="eyeLeft"><div class="robot-pupil" id="pupilLeft"></div></div>
            <div class="robot-eye" id="eyeRight"><div class="robot-pupil" id="pupilRight"></div></div>
            <div class="robot-mouth"></div>
        </div>
    </div>
</div>

{{-- Chat Panel --}}
<div id="robot-chat-panel">
    <div class="chat-header">
        <div class="chat-header-avatar"><i class="fas fa-robot"></i></div>
        <div class="chat-header-info">
            <strong>ERP Assistant</strong>
            <span>Online · Ready to help</span>
        </div>
        <button class="chat-close-btn" id="chatCloseBtn" title="Close"><i class="fas fa-xmark"></i></button>
    </div>
    <div class="chat-messages" id="chatMessages">
        <div class="chat-msg bot">
            <div class="chat-bot-avatar"><i class="fas fa-robot"></i></div>
            <div class="chat-bubble">👋 Hello! I'm your ERP Assistant. Ask me anything about the system — students, fees, attendance, or reports!</div>
        </div>
    </div>
    <div class="chat-input-area">
        <input type="text" class="chat-input" id="chatInput" placeholder="Ask me anything..." autocomplete="off">
        <button class="chat-send-btn" id="chatSendBtn" title="Send"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>

<script>
(function () {
    // ─── EYE TRACKING ─────────────────────────────────────────────
    const pupils = [document.getElementById('pupilLeft'), document.getElementById('pupilRight')];
    const eyes   = [document.getElementById('eyeLeft'), document.getElementById('eyeRight')];
    const MAX_MOVE = 4; // max px the pupil can travel

    function movePupils(mx, my) {
        eyes.forEach((eye, i) => {
            const pupil = pupils[i];
            if (!eye || !pupil) return;
            const rect = eye.getBoundingClientRect();
            const ex = rect.left + rect.width / 2;
            const ey = rect.top + rect.height / 2;
            const dx = mx - ex;
            const dy = my - ey;
            const dist = Math.sqrt(dx * dx + dy * dy);
            const factor = Math.min(1, MAX_MOVE / (dist || 1));
            const px = dx * factor;
            const py = dy * factor;
            pupil.style.transform = `translate(calc(-50% + ${px}px), calc(-50% + ${py}px))`;
        });
    }

    document.addEventListener('mousemove', e => movePupils(e.clientX, e.clientY));

    // Touch support for eye tracking
    document.addEventListener('touchmove', e => {
        if (e.touches.length > 0) {
            movePupils(e.touches[0].clientX, e.touches[0].clientY);
        }
    }, { passive: true });

    // ─── CHATBOT TOGGLE ───────────────────────────────────────────
    const robot    = document.getElementById('robot-assistant');
    const panel    = document.getElementById('robot-chat-panel');
    const closeBtn = document.getElementById('chatCloseBtn');
    const input    = document.getElementById('chatInput');
    const sendBtn  = document.getElementById('chatSendBtn');
    const msgs     = document.getElementById('chatMessages');

    function openChat() { panel.classList.add('open'); input.focus(); }
    function closeChat() { panel.classList.remove('open'); }

    robot.addEventListener('click', () => panel.classList.contains('open') ? closeChat() : openChat());
    closeBtn.addEventListener('click', e => { e.stopPropagation(); closeChat(); });

    // ─── CHAT RESPONSES ───────────────────────────────────────────
    const responses = {
        student:    "You can manage students under the **Student Management** section in the sidebar. Add, import, or view student directories there.",
        staff:      "Staff details are available in **Staff Management** — you can add staff, upload photos, and manage attendance.",
        fee:        "The **Fee Management** section covers fee basics, class-wise fees, receipts, reports, and invoices.",
        attendance: "Student and staff attendance can be marked individually or in bulk from the respective management sections.",
        report:     "Reports are available under **Fee Reports**, **Student Report**, and the **Daily MIS Report** on the dashboard.",
        certificate:"Visit **Certificate Management** to create templates and generate student certificates.",
        exam:       "Marks entry, grade scales, and report cards are in the **Examination** section.",
        admission:  "Manage the full admission pipeline including enquiry leads, applications, and payments in **Admissions**.",
        timetable:  "Create and manage class, group, and teacher timetables under **Time Table**.",
        leave:      "Leave applications for staff and students are managed under **Leave Management**.",
        help:       "I can help with: students, staff, fees, attendance, reports, certificates, exams, admissions, timetables, and more!",
        default:    "I'm here to help! Ask me about students, fees, attendance, reports, certificates, or any ERP feature. Type 'help' to see what I can do."
    };

    function getBotReply(text) {
        const t = text.toLowerCase();
        if (t.includes('student'))    return responses.student;
        if (t.includes('staff') || t.includes('teacher')) return responses.staff;
        if (t.includes('fee') || t.includes('payment') || t.includes('invoice')) return responses.fee;
        if (t.includes('attendance')) return responses.attendance;
        if (t.includes('report'))     return responses.report;
        if (t.includes('certificate') || t.includes('cert')) return responses.certificate;
        if (t.includes('exam') || t.includes('mark') || t.includes('result')) return responses.exam;
        if (t.includes('admission'))  return responses.admission;
        if (t.includes('timetable') || t.includes('time table') || t.includes('schedule')) return responses.timetable;
        if (t.includes('leave'))      return responses.leave;
        if (t.includes('help') || t.includes('what can')) return responses.help;
        return responses.default;
    }

    function appendMsg(text, type) {
        const msgDiv = document.createElement('div');
        msgDiv.className = 'chat-msg ' + type;

        if (type === 'bot') {
            const av = document.createElement('div');
            av.className = 'chat-bot-avatar';
            av.innerHTML = '<i class="fas fa-robot"></i>';
            msgDiv.appendChild(av);
        }

        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble';
        bubble.textContent = text;
        msgDiv.appendChild(bubble);

        msgs.appendChild(msgDiv);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function showTyping() {
        const typing = document.createElement('div');
        typing.className = 'chat-msg bot';
        typing.id = 'typingIndicator';
        const av = document.createElement('div');
        av.className = 'chat-bot-avatar';
        av.innerHTML = '<i class="fas fa-robot"></i>';
        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble chat-typing';
        bubble.innerHTML = '<span></span><span></span><span></span>';
        typing.appendChild(av);
        typing.appendChild(bubble);
        msgs.appendChild(typing);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function removeTyping() {
        const t = document.getElementById('typingIndicator');
        if (t) t.remove();
    }

    function sendMessage() {
        const text = input.value.trim();
        if (!text) return;
        input.value = '';
        appendMsg(text, 'user');
        showTyping();
        setTimeout(() => {
            removeTyping();
            appendMsg(getBotReply(text), 'bot');
        }, 900 + Math.random() * 600);
    }

    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keydown', e => { if (e.key === 'Enter') sendMessage(); });

    // Close panel when clicking outside
    document.addEventListener('click', e => {
        if (!e.target.closest('#robot-assistant') && !e.target.closest('#robot-chat-panel')) {
            closeChat();
        }
    });
})();
</script>
@endsection

@php
    $chatUrl = '#';
    $chatSendUrl = '#';
    try {
        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if (isset($user->role) && $user->role === 'superadmin') {
                $chatUrl = route('superadmin.ai.chat');
                $chatSendUrl = route('superadmin.ai.chat.send');
                if (!isset($aiSettings)) {
                    $aiSettings = new \App\Models\SchoolAiSetting([
                        'enabled' => (bool) config('services.superadmin_ai.key'),
                        'api_key' => config('services.superadmin_ai.key'),
                        'ai_model' => config('services.superadmin_ai.model', 'gemini-1.5-flash'),
                        'chatbot_name' => 'Platform AI Assistant',
                        'ai_provider' => config('services.superadmin_ai.provider', 'gemini'),
                    ]);
                }
            } else {
                $chatUrl = route('school.ai.chat');
                $chatSendUrl = route('school.ai.chat.send');
                if (!isset($aiSettings) && $user->school_id) {
                    $aiSettings = \App\Models\SchoolAiSetting::where('school_id', $user->school_id)->first();
                }
            }
        }
    } catch (\Exception $e) {}
@endphp
<!-- ══════════ AI ASSISTANT FLOATING CHATBOT ══════════ -->
<style>
@media print {
    #robot-assistant,
    .robot-body,
    .chat-bubble,
    #chat-container,
    .chat-container,
    .ai-chat-bubble,
    .yash-ai-bubble,
    .chat-window,
    .chat-wrapper {
        display: none !important;
    }
}
/* ─── FLOATING BOT BUTTON ────────────────────────────── */
#robot-assistant {
    position: fixed;
    bottom: 28px;
    right: 28px;
    z-index: 9000;
    cursor: grab;
    user-select: none;
    touch-action: none;
}
#robot-assistant.dragging { cursor: grabbing; }

/* Main button container */
.robot-body {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: visible;
    transition: transform .25s cubic-bezier(.34,1.56,.64,1), box-shadow .25s;
}
.robot-body:hover {
    transform: scale(1.1);
}

/* Pure CSS Robot Head */
.robot-head {
    width: 66px;
    height: 66px;
    border-radius: 50%;
    background: radial-gradient(circle at 30% 30%, #1e293b 0%, #0f172a 70%, #020617 100%);
    border: 2px solid #5a6eff;
    box-shadow: 0 8px 24px rgba(0,0,0,0.4), 
                inset 0 4px 8px rgba(255,255,255,0.15), 
                inset 0 -6px 12px rgba(0,0,0,0.6),
                0 0 20px rgba(90,110,255,0.4);
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    overflow: visible;
    transition: filter .3s;
}

/* Glass shine overlay */
.robot-head::after {
    content: '';
    position: absolute;
    top: 3px; left: 10px;
    width: 44px; height: 16px;
    background: linear-gradient(180deg, rgba(255,255,255,0.25) 0%, rgba(255,255,255,0) 100%);
    border-radius: 50% 50% 10px 10px;
    pointer-events: none;
    transform: rotate(-10deg);
}

/* Screen/Visor Faceplate */
.robot-faceplate {
    width: 50px;
    height: 38px;
    background: linear-gradient(180deg, #0d0f14 0%, #030508 100%);
    border-radius: 12px;
    border: 1.5px solid rgba(90,110,255,0.35);
    position: relative;
    box-shadow: inset 0 2px 5px rgba(0,0,0,0.8),
                0 0 8px rgba(90,110,255,0.15);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    padding-top: 5px;
}

/* Eyes Container */
.robot-eyes {
    display: flex;
    justify-content: space-between;
    width: 34px;
    margin-bottom: 2px;
    padding: 0 2px;
}

/* Individual Eye */
.robot-css-eye {
    width: 13px;
    height: 13px;
    background: #ffffff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 6px #60a5fa,
                0 0 12px rgba(96,165,250,0.4);
    position: relative;
    overflow: hidden;
}

/* Pupil */
.robot-css-pupil {
    width: 6px;
    height: 6px;
    background: radial-gradient(circle at 35% 35%, #2563eb, #0c1024);
    border-radius: 50%;
    transition: transform 0.05s linear;
    flex-shrink: 0;
}

/* Smiling mouth */
.robot-css-mouth {
    width: 12px;
    height: 5px;
    border-bottom: 2.5px solid #60a5fa;
    border-radius: 0 0 8px 8px;
    margin-top: 1px;
    box-shadow: 0 1.5px 4px rgba(96,165,250,0.5);
}

/* Stem of antenna */
.robot-antenna-stem {
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 4px;
    height: 10px;
    background: linear-gradient(90deg, #4b5563, #1f2937);
    z-index: 1;
}

/* Glowing ball on antenna */
.robot-antenna-ball {
    position: absolute;
    top: -16px;
    left: 50%;
    transform: translateX(-50%);
    width: 10px;
    height: 10px;
    background: radial-gradient(circle, #93c5fd 0%, #3b82f6 70%, #1d4ed8 100%);
    border-radius: 50%;
    box-shadow: 0 0 10px #3b82f6,
                0 0 20px rgba(59,130,246,0.6);
    z-index: 3;
    animation: antenna-pulse 2s infinite ease-in-out;
}
@keyframes antenna-pulse {
    0%, 100% { transform: translateX(-50%) scale(1); box-shadow: 0 0 10px #3b82f6, 0 0 20px rgba(59,130,246,0.6); }
    50% { transform: translateX(-50%) scale(1.15); box-shadow: 0 0 16px #60a5fa, 0 0 28px rgba(96,165,250,0.8); }
}

/* Side Ears */
.robot-css-ear {
    position: absolute;
    width: 6px;
    height: 12px;
    background: #374151;
    border: 1px solid #5a6eff;
    border-radius: 3px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 1;
}
.robot-css-ear-left { left: -4px; border-radius: 3px 0 0 3px; }
.robot-css-ear-right { right: -4px; border-radius: 0 3px 3px 0; }

/* Pulse ring animation */
.robot-pulse {
    position: absolute;
    inset: -8px;
    border-radius: 50%;
    background: transparent;
    border: 2.5px solid rgba(99,102,241,0.45);
    animation: pulse-ring 2.5s ease-out infinite;
    pointer-events: none;
    z-index: 1;
}
.robot-pulse-2 {
    position: absolute;
    inset: -16px;
    border-radius: 50%;
    background: transparent;
    border: 1.5px solid rgba(139,92,246,0.25);
    animation: pulse-ring 2.5s ease-out infinite;
    animation-delay: 0.8s;
    pointer-events: none;
    z-index: 1;
}
@keyframes pulse-ring {
    0%   { opacity: .75; transform: scale(1); }
    100% { opacity: 0;   transform: scale(1.4); }
}

/* Orbital rings */
.orbital-ring {
    position: absolute;
    border-radius: 50%;
    border: 1.5px solid rgba(99,102,241,0.3);
    pointer-events: none;
    z-index: 0;
}
.orbital-ring-1 {
    width: 100px; height: 100px;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%) rotate(0deg);
    animation: orbit-spin-1 6s linear infinite;
    border-color: rgba(99,102,241,0.35);
}
.orbital-ring-2 {
    width: 120px; height: 120px;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%) rotate(60deg);
    animation: orbit-spin-2 9s linear infinite;
    border-color: rgba(167,139,250,0.25);
}
@keyframes orbit-spin-1 { to { transform: translate(-50%,-50%) rotate(360deg); } }
@keyframes orbit-spin-2 { to { transform: translate(-50%,-50%) rotate(-360deg); } }

/* Floating label */
.robot-label {
    position: absolute;
    top: -34px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: #ffffff;
    font-size: 11px;
    font-weight: 800;
    padding: 4px 14px;
    border-radius: 20px;
    white-space: nowrap;
    box-shadow: 0 4px 14px rgba(79,70,229,.4);
    animation: float-label 3s ease-in-out infinite;
    font-family: 'Plus Jakarta Sans', sans-serif;
    z-index: 3;
}
@keyframes float-label {
    0%,100% { transform: translateX(-50%) translateY(0); }
    50%      { transform: translateX(-50%) translateY(-4px); }
}

/* Glow orb on antenna tip simulation */
.robot-glow {
    position: absolute;
    top: -4px;
    left: 50%;
    transform: translateX(-50%);
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: radial-gradient(circle, #a5b4fc 0%, rgba(99,102,241,0) 70%);
    animation: glow-blink 2s ease-in-out infinite;
    pointer-events: none;
    z-index: 4;
}
@keyframes glow-blink {
    0%,100% { opacity: 1; transform: translateX(-50%) scale(1); }
    50%      { opacity: .35; transform: translateX(-50%) scale(0.6); }
}

/* Sparkle effects */
.robot-sparkle {
    position: absolute;
    width: 4px; height: 4px;
    border-radius: 50%;
    background: #a5b4fc;
    pointer-events: none;
    animation: sparkle-fly 2.5s ease-in-out infinite;
    z-index: 5;
}
.robot-sparkle:nth-child(1) { top: 8px; right: -6px; animation-delay: 0s; }
.robot-sparkle:nth-child(2) { bottom: 10px; left: -4px; animation-delay: 0.8s; background: #c4b5fd; }
.robot-sparkle:nth-child(3) { top: 50%; right: -10px; animation-delay: 1.6s; background: #818cf8; }
@keyframes sparkle-fly {
    0%,100% { opacity: 0; transform: scale(0); }
    30%,70% { opacity: 1; transform: scale(1); }
    50%      { transform: scale(1.4) translateY(-3px); }
}

/* ─── CHAT PANEL ─────────────────────────────────────── */
#robot-chat-panel {
    position: fixed;
    bottom: 115px;
    right: 28px;
    width: 360px;
    max-height: 500px;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,.22), 0 0 0 1px rgba(99,102,241,.15);
    z-index: 8999;
    display: none;
    flex-direction: column;
    overflow: hidden;
    transform-origin: bottom right;
}
#robot-chat-panel.open {
    display: flex;
    animation: chat-pop .3s cubic-bezier(.34,1.56,.64,1);
}
@keyframes chat-pop {
    from { opacity: 0; transform: scale(.85) translateY(12px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}

/* Chat header */
.chat-header {
    background: linear-gradient(135deg, #1e1b4b, #4f46e5);
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
}
.chat-header::before {
    content: '';
    position: absolute;
    width: 120px; height: 120px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(167,139,250,.2) 0%, transparent 70%);
    top: -40px; right: -20px;
    pointer-events: none;
}
.chat-header-avatar {
    width: 36px; height: 36px;
    border-radius: 11px;
    background: rgba(255,255,255,.12);
    border: 1.5px solid rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}
.chat-header-avatar img {
    width: 28px; height: 28px;
    object-fit: contain;
}
.chat-header-info strong {
    color: #fff;
    font-size: 13.5px;
    display: block;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.chat-header-info span {
    color: rgba(255,255,255,.65);
    font-size: 11px;
}
.chat-close-btn {
    margin-left: auto;
    background: rgba(255,255,255,.12);
    border: none;
    color: #fff;
    width: 30px; height: 30px;
    border-radius: 9px;
    cursor: pointer;
    font-size: 13px;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s;
    flex-shrink: 0;
    z-index: 1;
}
.chat-close-btn:hover { background: rgba(255,255,255,.28); }

/* Messages */
.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    background: #f8fafc;
}
.chat-messages::-webkit-scrollbar { width: 3px; }
.chat-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

.chat-msg { display: flex; align-items: flex-end; gap: 8px; }
.chat-msg.bot  { flex-direction: row; }
.chat-msg.user { flex-direction: row-reverse; }

.chat-bubble {
    max-width: 240px;
    padding: 10px 14px;
    border-radius: 15px;
    font-size: 13px;
    line-height: 1.55;
    word-break: break-word;
}
.chat-msg.bot  .chat-bubble {
    background: #fff;
    color: #1f2937;
    border-radius: 15px 15px 15px 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,.07);
    border: 1px solid #f1f5f9;
}
.chat-msg.user .chat-bubble {
    background: linear-gradient(135deg, #312e81, #4f46e5);
    color: #fff;
    border-radius: 15px 15px 4px 15px;
}
.chat-bot-avatar {
    width: 28px; height: 28px;
    background: linear-gradient(135deg, #312e81, #4f46e5);
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}
.chat-bot-avatar img { width: 22px; height: 22px; object-fit: contain; }

/* Typing dots */
.chat-typing { display: flex; gap: 4px; align-items: center; padding: 10px 14px; }
.chat-typing span {
    width: 7px; height: 7px;
    background: #a5b4fc;
    border-radius: 50%;
    animation: typing-dot 1.2s ease-in-out infinite;
}
.chat-typing span:nth-child(2) { animation-delay: .2s; }
.chat-typing span:nth-child(3) { animation-delay: .4s; }
@keyframes typing-dot {
    0%,100% { transform: translateY(0); opacity: .5; }
    50%      { transform: translateY(-5px); opacity: 1; }
}

/* Input area */
.chat-input-area {
    padding: 12px 14px;
    background: #fff;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 8px;
    align-items: center;
    flex-shrink: 0;
}
.chat-input {
    flex: 1;
    border: 1.5px solid #e2e8f0;
    border-radius: 11px;
    padding: 9px 13px;
    font-size: 13px;
    outline: none;
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: #f8fafc;
    transition: border-color .2s, background .2s;
    color: #1f2937;
}
.chat-input:focus { border-color: #6366f1; background: #fff; }
.chat-send-btn {
    width: 38px; height: 38px;
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    border: none;
    border-radius: 11px;
    color: #fff;
    font-size: 14px;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: opacity .2s, transform .15s;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(99,102,241,0.35);
}
.chat-send-btn:hover { opacity: .88; transform: scale(1.05); }

/* Powered by bar */
.chat-powered-by {
    text-align: center;
    font-size: 10px;
    color: #94a3b8;
    padding: 6px 0;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
    flex-shrink: 0;
}
.chat-powered-by a { color: #6366f1; text-decoration: none; font-weight: 600; }

/* ─── DARK MODE ──────────────────────────────────────── */
body.dark-mode #robot-chat-panel {
    background: #111827 !important;
    box-shadow: 0 20px 60px rgba(0,0,0,.6), 0 0 0 1px rgba(129,140,248,.25) !important;
}
body.dark-mode .chat-messages { background: #0b0f19 !important; }
body.dark-mode .chat-msg.bot .chat-bubble {
    background: #1f2937 !important;
    color: #f8fafc !important;
    border-color: #374151 !important;
}
body.dark-mode .chat-input-area,
body.dark-mode .chat-powered-by { background: #111827 !important; border-top-color: #1e293b !important; }
body.dark-mode .chat-input {
    background: #1f2937 !important;
    border-color: #374151 !important;
    color: #f8fafc !important;
}
body.dark-mode .chat-input:focus { border-color: #818cf8 !important; }
</style>

<!-- Floating Robot Button -->
<div id="robot-assistant" title="Click to chat with AI Assistant">
    <div class="robot-label" id="botLabel">AI Assistant</div>
    <div class="robot-body" id="robotBody">
        <!-- Orbital rings -->
        <div class="orbital-ring orbital-ring-1"></div>
        <div class="orbital-ring orbital-ring-2"></div>
        <!-- Pulse rings -->
        <div class="robot-pulse"></div>
        <div class="robot-pulse-2"></div>
        <!-- Sparkles -->
        <div class="robot-sparkle"></div>
        <div class="robot-sparkle"></div>
        <div class="robot-sparkle"></div>
        <!-- Glow orb -->
        <div class="robot-glow"></div>
        <!-- Pure CSS Robot Head -->
        <div class="robot-head" id="robotHead">
            <!-- Antenna -->
            <div class="robot-antenna-stem"></div>
            <div class="robot-antenna-ball"></div>
            <!-- Side Ears -->
            <div class="robot-css-ear robot-css-ear-left"></div>
            <div class="robot-css-ear robot-css-ear-right"></div>
            <!-- Visor Faceplate -->
            <div class="robot-faceplate">
                <div class="robot-eyes">
                    <div class="robot-css-eye">
                        <div class="robot-css-pupil" id="pupilLeft"></div>
                    </div>
                    <div class="robot-css-eye">
                        <div class="robot-css-pupil" id="pupilRight"></div>
                    </div>
                </div>
                <div class="robot-css-mouth"></div>
            </div>
        </div>
    </div>
</div>

<!-- Chat Panel -->
<div id="robot-chat-panel">
    <div class="chat-header">
        <div class="chat-header-avatar">
            <img src="{{ asset('images/ai-assistant.png') }}" alt="AI">
        </div>
        <div class="chat-header-info">
            <strong id="chatBotName">AI Assistant</strong>
            <span>● Online · Ready to help</span>
        </div>
        <button class="chat-close-btn" id="chatCloseBtn" title="Close">
            <i class="fas fa-xmark"></i>
        </button>
    </div>
    <div class="chat-messages" id="chatMessages">
        <div class="chat-msg bot">
            <div class="chat-bot-avatar"><img src="{{ asset('images/ai-assistant.png') }}" alt="AI"></div>
            <div class="chat-bubble">👋 Hello! I'm your AI Assistant. Ask me anything about students, fees, attendance, or go to <strong>AI Chat</strong> for a full experience!</div>
        </div>
    </div>
    <div class="chat-input-area">
        <input type="text" class="chat-input" id="chatInput" placeholder="Ask me anything…" autocomplete="off">
        <button class="chat-send-btn" id="chatSendBtn" title="Send"><i class="fas fa-paper-plane"></i></button>
    </div>
    <div class="chat-powered-by">
        Powered by AI · <a href="{{ $chatUrl }}">Open Full Chat →</a>
    </div>
</div>

<script>
(function() {
    /* ─── EYE TRACKING ──────────────────────────────── */
    const pupilLeft  = document.getElementById('pupilLeft');
    const pupilRight = document.getElementById('pupilRight');
    const robotHead  = document.getElementById('robotHead');
    const MAX_OFFSET = 3.5; // max pixels pupils can move

    document.addEventListener('mousemove', function(e) {
        if (!robotHead || !pupilLeft || !pupilRight) return;
        const rect = robotHead.getBoundingClientRect();
        const cx   = rect.left + rect.width  / 2;
        const cy   = rect.top  + rect.height / 2;
        const dx   = e.clientX - cx;
        const dy   = e.clientY - cy;
        const dist = Math.sqrt(dx * dx + dy * dy) || 1;
        // Clamp to max offset
        const ox = (dx / dist) * Math.min(dist * 0.08, MAX_OFFSET);
        const oy = (dy / dist) * Math.min(dist * 0.08, MAX_OFFSET);
        const transform = `translate(${ox.toFixed(2)}px, ${oy.toFixed(2)}px)`;
        pupilLeft.style.transform  = transform;
        pupilRight.style.transform = transform;
    });

    // Reset pupils when mouse leaves window
    document.addEventListener('mouseleave', function() {
        if (pupilLeft)  pupilLeft.style.transform  = 'translate(0,0)';
        if (pupilRight) pupilRight.style.transform = 'translate(0,0)';
    });

    /* ─── DRAGGABLE ─────────────────────────────────── */
    const bot = document.getElementById('robot-assistant');
    const panel = document.getElementById('robot-chat-panel');
    let isDragging = false, startX, startY, startRight, startBottom, hasMoved = false;

    function getRight()  { return parseInt(bot.style.right  || '28', 10); }
    function getBottom() { return parseInt(bot.style.bottom || '28', 10); }

    function onDown(e) {
        const touch = e.touches ? e.touches[0] : e;
        startX      = touch.clientX;
        startY      = touch.clientY;
        startRight  = getRight();
        startBottom = getBottom();
        isDragging  = true;
        hasMoved    = false;
        bot.classList.add('dragging');
        e.preventDefault();
    }

    function onMove(e) {
        if (!isDragging) return;
        const touch = e.touches ? e.touches[0] : e;
        const dx = touch.clientX - startX;
        const dy = touch.clientY - startY;
        if (Math.abs(dx) > 3 || Math.abs(dy) > 3) hasMoved = true;

        let newRight  = startRight  - dx;
        let newBottom = startBottom - dy;

        // Clamp within viewport
        const vw = window.innerWidth;
        const vh = window.innerHeight;
        const bw = bot.offsetWidth  || 80;
        const bh = bot.offsetHeight || 80;
        newRight  = Math.max(0, Math.min(newRight,  vw - bw));
        newBottom = Math.max(0, Math.min(newBottom, vh - bh));

        bot.style.right  = newRight  + 'px';
        bot.style.bottom = newBottom + 'px';

        // Also move panel relative to bot
        panel.style.right  = newRight  + 'px';
        panel.style.bottom = (newBottom + bh + 12) + 'px';

        e.preventDefault();
    }

    function onUp() {
        isDragging = false;
        bot.classList.remove('dragging');
    }

    bot.addEventListener('mousedown',  onDown, { passive: false });
    bot.addEventListener('touchstart', onDown, { passive: false });
    document.addEventListener('mousemove', onMove);
    document.addEventListener('touchmove', onMove, { passive: false });
    document.addEventListener('mouseup',  onUp);
    document.addEventListener('touchend', onUp);

    /* ─── OPEN / CLOSE PANEL ────────────────────────── */
    const closeBtn = document.getElementById('chatCloseBtn');
    const input    = document.getElementById('chatInput');
    const sendBtn  = document.getElementById('chatSendBtn');
    const msgs     = document.getElementById('chatMessages');

    document.getElementById('robotBody').addEventListener('click', function(e) {
        if (hasMoved) return; // Don't open if user dragged
        panel.classList.toggle('open');
        if (panel.classList.contains('open')) {
            repositionPanel();
            setTimeout(() => input.focus(), 100);
        }
    });

    closeBtn.addEventListener('click', e => { e.stopPropagation(); panel.classList.remove('open'); });

    document.addEventListener('click', e => {
        if (!e.target.closest('#robot-assistant') && !e.target.closest('#robot-chat-panel')) {
            panel.classList.remove('open');
        }
    });

    function repositionPanel() {
        const rect = bot.getBoundingClientRect();
        const bh   = rect.height || 80;
        panel.style.right  = getRight()  + 'px';
        panel.style.bottom = (getBottom() + bh + 12) + 'px';
    }

    /* ─── CHAT LOGIC ────────────────────────────────── */
    const chatRoute  = "{{ $chatSendUrl }}";
    const csrfToken  = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const botNameEl  = document.getElementById('chatBotName');
    const botLabelEl = document.getElementById('botLabel');

    // Try to load chatbot name from settings
    try {
        const storedName = '{{ addslashes($aiSettings->chatbot_name ?? "AI Assistant") }}';
        if (storedName && botNameEl) botNameEl.textContent = storedName;
        if (storedName && botLabelEl) botLabelEl.textContent = storedName;
    } catch(e) {}

    const responses = {
        student:    "Manage students under **Student Management** in the sidebar — add, import, or view directories.",
        staff:      "Staff details are in **Staff Management** — add staff, upload photos, and manage attendance.",
        fee:        "**Fee Management** covers fee basics, class-wise fees, receipts, reports, and invoices.",
        attendance: "Mark student and staff attendance individually or in bulk from their management sections.",
        report:     "Reports are available under **Fee Reports**, **Student Report**, and the **Daily MIS Report**.",
        cert:       "Visit **Certificate Management** to create templates and generate student certificates.",
        exam:       "Marks entry, grade scales, and report cards are in the **Examination** section.",
        admission:  "Manage admissions including enquiry leads, applications, and payments in **Admissions**.",
        timetable:  "Create and manage class, group, and teacher timetables under **Time Table**.",
        leave:      "Leave applications for staff and students are managed under **Leave Management**.",
        help:       "I can help with: students, staff, fees, attendance, reports, certs, exams, admissions, timetables and more! For detailed chat, use **AI Chat** in the sidebar.",
        def:        "I'm here to help! Ask me about students, fees, attendance, or any ERP feature. For a full experience, visit **AI Chat** in the sidebar."
    };

    function getLocalReply(t) {
        t = t.toLowerCase();
        if (t.includes('student'))                              return responses.student;
        if (t.includes('staff') || t.includes('teacher'))      return responses.staff;
        if (t.includes('fee') || t.includes('payment'))        return responses.fee;
        if (t.includes('attendance'))                           return responses.attendance;
        if (t.includes('report'))                               return responses.report;
        if (t.includes('cert'))                                 return responses.cert;
        if (t.includes('exam') || t.includes('mark'))          return responses.exam;
        if (t.includes('admission'))                            return responses.admission;
        if (t.includes('timetable') || t.includes('schedule')) return responses.timetable;
        if (t.includes('leave'))                                return responses.leave;
        if (t.includes('help'))                                 return responses.help;
        return responses.def;
    }

    function appendMsg(text, type) {
        const d = document.createElement('div');
        d.className = 'chat-msg ' + type;
        if (type === 'bot') {
            const av = document.createElement('div');
            av.className = 'chat-bot-avatar';
            av.innerHTML = '<img src="{{ asset("images/ai-assistant.png") }}" alt="AI">';
            d.appendChild(av);
        }
        const b = document.createElement('div');
        b.className = 'chat-bubble';
        b.innerHTML = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
        d.appendChild(b);
        msgs.appendChild(d);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function showTyping() {
        const d = document.createElement('div');
        d.className = 'chat-msg bot';
        d.id = 'typingIndicator';
        const av = document.createElement('div');
        av.className = 'chat-bot-avatar';
        av.innerHTML = '<img src="{{ asset("images/ai-assistant.png") }}" alt="AI">';
        const b = document.createElement('div');
        b.className = 'chat-bubble chat-typing';
        b.innerHTML = '<span></span><span></span><span></span>';
        d.appendChild(av);
        d.appendChild(b);
        msgs.appendChild(d);
        msgs.scrollTop = msgs.scrollHeight;
    }

    const aiEnabled = {{ isset($aiSettings) && $aiSettings->enabled ? 'true' : 'false' }};
    const hasApiKey = {{ isset($aiSettings) && $aiSettings->api_key ? 'true' : 'false' }};

    function send() {
        const text = input.value.trim();
        if (!text) return;
        input.value = '';
        appendMsg(text, 'user');
        showTyping();

        if (aiEnabled && hasApiKey) {
            // Use real AI API
            fetch(chatRoute, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message: text, history: [] }),
            })
            .then(r => r.json())
            .then(data => {
                const t = document.getElementById('typingIndicator');
                if (t) t.remove();
                appendMsg(data.reply || 'No response.', 'bot');
            })
            .catch(() => {
                const t = document.getElementById('typingIndicator');
                if (t) t.remove();
                appendMsg(getLocalReply(text), 'bot');
            });
        } else {
            // Fallback local replies
            setTimeout(() => {
                const t = document.getElementById('typingIndicator');
                if (t) t.remove();
                appendMsg(getLocalReply(text), 'bot');
            }, 800 + Math.random() * 500);
        }
    }

    sendBtn.addEventListener('click', send);
    input.addEventListener('keydown', e => { if (e.key === 'Enter') send(); });
})();
</script>
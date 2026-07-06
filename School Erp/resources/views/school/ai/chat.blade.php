@extends('layouts.app')

@section('title', 'AI Chat — ' . $aiSettings->chatbot_name)

@section('styles')
<style>
/* ─── AI CHAT PAGE ─────────────────────────────────────────── */
.ai-chat-wrap {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 62px);
    padding: 0;
    overflow: hidden;
    position: relative;
}

/* ─── Chat Header ───────────────────────────────────────────── */
.ai-chat-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 24px;
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
    border-bottom: 1px solid rgba(255,255,255,0.08);
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
}
.ai-chat-topbar::before {
    content: '';
    position: absolute;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(167,139,250,0.2) 0%, transparent 70%);
    top: -80px; right: 60px;
    pointer-events: none;
}
.ai-chat-topbar-left {
    display: flex;
    align-items: center;
    gap: 14px;
}
.ai-bot-avatar {
    width: 46px; height: 46px;
    border-radius: 14px;
    background: rgba(255,255,255,0.1);
    border: 2px solid rgba(255,255,255,0.2);
    display: flex; align-items: center; justify-content: center;
    position: relative;
    overflow: hidden;
}
.ai-bot-avatar img {
    width: 36px; height: 36px;
    object-fit: contain;
}
.ai-bot-status-dot {
    position: absolute;
    bottom: 3px; right: 3px;
    width: 10px; height: 10px;
    border-radius: 50%;
    background: #10b981;
    border: 2px solid #312e81;
}
.ai-chat-bot-name {
    color: #fff;
    font-size: 16px;
    font-weight: 800;
    font-family: 'Plus Jakarta Sans', sans-serif;
    margin: 0;
}
.ai-chat-bot-sub {
    color: rgba(255,255,255,0.6);
    font-size: 11.5px;
    margin: 2px 0 0;
}
.ai-chat-topbar-right {
    display: flex;
    align-items: center;
    gap: 10px;
}
.ai-topbar-btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 8px 16px;
    border-radius: 10px;
    border: 1.5px solid rgba(255,255,255,0.2);
    background: rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.9);
    font-size: 12px; font-weight: 700;
    cursor: pointer; text-decoration: none;
    transition: all .2s;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.ai-topbar-btn:hover {
    background: rgba(255,255,255,0.18);
    border-color: rgba(255,255,255,0.4);
    color: #fff;
}
.ai-topbar-icon-btn {
    width: 36px; height: 36px;
    border-radius: 10px;
    border: 1.5px solid rgba(255,255,255,0.2);
    background: rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.85);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
    transition: all .2s;
}
.ai-topbar-icon-btn:hover {
    background: rgba(255,255,255,0.18);
    color: #fff;
}

/* ─── No Key Warning ───────────────────────────────────────── */
.ai-no-key-banner {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 24px;
    background: rgba(245,158,11,0.1);
    border-bottom: 1px solid rgba(245,158,11,0.2);
    flex-shrink: 0;
}
.ai-no-key-banner i { color: #f59e0b; font-size: 16px; flex-shrink: 0; }
.ai-no-key-banner span {
    font-size: 13px;
    font-weight: 600;
    color: #92400e;
    flex: 1;
}
.ai-no-key-banner a {
    color: #d97706;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
}
.ai-no-key-banner a:hover { text-decoration: underline; }

/* ─── Messages Area ─────────────────────────────────────────── */
.ai-messages-area {
    flex: 1;
    overflow-y: auto;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    background: var(--page);
}
.ai-messages-area::-webkit-scrollbar { width: 4px; }
.ai-messages-area::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

/* Welcome state */
.ai-welcome {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex: 1;
    text-align: center;
    padding: 40px 20px;
    gap: 16px;
}
.ai-welcome-img {
    width: 90px; height: 90px;
    object-fit: contain;
    animation: bot-float 3s ease-in-out infinite;
}
@keyframes bot-float {
    0%,100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}
.ai-welcome h2 {
    font-size: 20px;
    font-weight: 800;
    color: var(--t1);
    margin: 0;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.ai-welcome p {
    font-size: 13px;
    color: var(--t2);
    margin: 0;
    max-width: 360px;
    line-height: 1.6;
}
/* Suggestion chips */
.ai-chip-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    margin-top: 8px;
}
.ai-chip {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 16px;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 20px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--t1);
    cursor: pointer;
    transition: all .2s;
    user-select: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.ai-chip:hover {
    border-color: #6366f1;
    color: #6366f1;
    background: rgba(99,102,241,0.05);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(99,102,241,0.15);
}
.ai-chip i { font-size: 12px; }

/* Message bubbles */
.ai-msg {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    max-width: 80%;
    animation: msg-in .25s ease;
}
@keyframes msg-in { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
.ai-msg.user { margin-left: auto; flex-direction: row-reverse; }
.ai-msg-avatar {
    width: 32px; height: 32px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 13px;
}
.ai-msg.bot .ai-msg-avatar {
    background: linear-gradient(135deg, #312e81, #4f46e5);
    overflow: hidden;
}
.ai-msg.bot .ai-msg-avatar img {
    width: 24px; height: 24px; object-fit: contain;
}
.ai-msg.user .ai-msg-avatar {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff;
}
.ai-bubble {
    padding: 12px 16px;
    border-radius: 18px;
    font-size: 13.5px;
    line-height: 1.6;
    max-width: 100%;
    word-break: break-word;
}
.ai-msg.bot .ai-bubble {
    background: #fff;
    color: var(--t1);
    border-radius: 4px 18px 18px 18px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    border: 1px solid #f1f5f9;
}
.ai-msg.user .ai-bubble {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    color: #fff;
    border-radius: 18px 18px 4px 18px;
    box-shadow: 0 4px 16px rgba(99,102,241,0.3);
}
.ai-msg-time {
    font-size: 10px;
    color: var(--t3);
    margin-top: 4px;
    text-align: right;
}
.ai-msg.bot .ai-msg-time { text-align: left; margin-left: 42px; }

/* Typing indicator */
.ai-typing-indicator {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    max-width: 80%;
}
.ai-typing-bubble {
    background: #fff;
    border: 1px solid #f1f5f9;
    border-radius: 4px 18px 18px 18px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    padding: 14px 18px;
    display: flex;
    gap: 5px;
}
.ai-typing-bubble span {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #a5b4fc;
    animation: typing-bounce 1.2s ease-in-out infinite;
}
.ai-typing-bubble span:nth-child(2) { animation-delay: .2s; }
.ai-typing-bubble span:nth-child(3) { animation-delay: .4s; }
@keyframes typing-bounce {
    0%,100% { transform:translateY(0); opacity:.5; }
    50% { transform:translateY(-5px); opacity:1; }
}

/* ─── Input Bar ─────────────────────────────────────────────── */
.ai-input-bar {
    padding: 16px 24px;
    background: #fff;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: flex-end;
    gap: 12px;
    flex-shrink: 0;
}
.ai-chat-input-wrap {
    flex: 1;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    display: flex;
    align-items: flex-end;
    padding: 10px 14px;
    gap: 10px;
    transition: border-color .2s, box-shadow .2s;
}
.ai-chat-input-wrap:focus-within {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    background: #fff;
}
.ai-chat-input {
    flex: 1;
    border: none;
    background: none;
    outline: none;
    font-size: 13.5px;
    color: var(--t1);
    font-family: 'Plus Jakarta Sans', sans-serif;
    resize: none;
    max-height: 120px;
    min-height: 22px;
    line-height: 1.5;
}
.ai-chat-input::placeholder { color: var(--t3); }
.ai-send-btn {
    width: 42px; height: 42px;
    border-radius: 12px;
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    border: none;
    color: #fff;
    font-size: 15px;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all .2s;
    box-shadow: 0 4px 14px rgba(99,102,241,0.4);
    flex-shrink: 0;
}
.ai-send-btn:hover { transform: scale(1.06); box-shadow: 0 6px 20px rgba(99,102,241,0.5); }
.ai-send-btn:active { transform: scale(0.97); }
.ai-send-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
.ai-char-count {
    font-size: 10px;
    color: var(--t3);
    align-self: flex-end;
    white-space: nowrap;
}

/* ─── DARK MODE ─────────────────────────────────────────────── */
body.dark-mode .ai-messages-area { background: #0b0f19 !important; }
body.dark-mode .ai-welcome h2 { color: #f1f5f9 !important; }
body.dark-mode .ai-welcome p { color: #94a3b8 !important; }
body.dark-mode .ai-chip {
    background: #1f2937 !important;
    border-color: #374151 !important;
    color: #e2e8f0 !important;
}
body.dark-mode .ai-chip:hover {
    border-color: #6366f1 !important;
    color: #a5b4fc !important;
    background: rgba(99,102,241,0.1) !important;
}
body.dark-mode .ai-msg.bot .ai-bubble {
    background: #1f2937 !important;
    color: #f1f5f9 !important;
    border-color: #374151 !important;
    box-shadow: 0 2px 12px rgba(0,0,0,0.2) !important;
}
body.dark-mode .ai-input-bar {
    background: #111827 !important;
    border-top-color: #1e293b !important;
}
body.dark-mode .ai-chat-input-wrap {
    background: #1f2937 !important;
    border-color: #374151 !important;
}
body.dark-mode .ai-chat-input-wrap:focus-within {
    border-color: #6366f1 !important;
    background: #111827 !important;
}
body.dark-mode .ai-chat-input { color: #f1f5f9 !important; }
body.dark-mode .ai-typing-bubble {
    background: #1f2937 !important;
    border-color: #374151 !important;
}
body.dark-mode .ai-no-key-banner { background: rgba(245,158,11,0.08) !important; }
body.dark-mode .ai-no-key-banner span { color: #fbbf24 !important; }
body.dark-mode .ai-msg-time { color: #475569 !important; }
</style>
@endsection

@section('content')
<div class="ai-chat-wrap">

    {{-- Chat Top Bar --}}
    <div class="ai-chat-topbar">
        <div class="ai-chat-topbar-left">
            <div class="ai-bot-avatar">
                <img src="{{ asset('images/ai-assistant.png') }}" alt="{{ $aiSettings->chatbot_name }}">
                @if($aiSettings->enabled)
                <div class="ai-bot-status-dot"></div>
                @endif
            </div>
            <div>
                <div class="ai-chat-bot-name">{{ $aiSettings->chatbot_name }}</div>
                <div class="ai-chat-bot-sub">
                    @if($aiSettings->enabled && $aiSettings->api_key)
                        <span style="color:#a7f3d0;">● Online</span> · {{ $aiSettings->ai_model }}
                    @elseif(!$aiSettings->enabled)
                        <span style="color:#fca5a5;">○ Disabled</span> · Configure in AI Settings
                    @else
                        <span style="color:#fbbf24;">⚠ No API Key</span> · Add key in AI Settings
                    @endif
                </div>
            </div>
        </div>
        <div class="ai-chat-topbar-right">
            <button class="ai-topbar-icon-btn" id="clearChatBtn" title="Clear chat">
                <i class="fas fa-trash-can"></i>
            </button>
            <a href="{{ route('school.ai.settings') }}" class="ai-topbar-btn">
                <i class="fas fa-gear"></i> Settings
            </a>
        </div>
    </div>

    {{-- API Key Warning --}}
    @if(!$aiSettings->enabled || !$aiSettings->api_key)
    <div class="ai-no-key-banner">
        <i class="fas fa-triangle-exclamation"></i>
        <span>
            @if(!$aiSettings->enabled)
                AI Assistant is disabled.
            @else
                No API key configured.
            @endif
            <a href="{{ route('school.ai.settings') }}">Go to AI Settings →</a>
        </span>
    </div>
    @endif

    {{-- Messages Area --}}
    <div class="ai-messages-area" id="aiMessages">

        {{-- Welcome State (hidden once chat starts) --}}
        <div class="ai-welcome" id="aiWelcome">
            <img src="{{ asset('images/ai-assistant.png') }}" alt="AI Bot" class="ai-welcome-img">
            <h2>Hello! I'm {{ $aiSettings->chatbot_name }} 👋</h2>
            <p>I'm your intelligent school ERP assistant. Ask me anything about students, fees, attendance, staff, or school management.</p>
            <div class="ai-chip-row">
                <button class="ai-chip" onclick="sendChip(this)" data-text="How many students are enrolled this year?">
                    <i class="fas fa-users"></i> Student Enrollment
                </button>
                <button class="ai-chip" onclick="sendChip(this)" data-text="How do I manage fee collection and track dues?">
                    <i class="fas fa-money-bill-wave"></i> Fee Management
                </button>
                <button class="ai-chip" onclick="sendChip(this)" data-text="How do I take student attendance?">
                    <i class="fas fa-clipboard-check"></i> Attendance
                </button>
                <button class="ai-chip" onclick="sendChip(this)" data-text="How do I generate student reports?">
                    <i class="fas fa-chart-bar"></i> Reports
                </button>
                <button class="ai-chip" onclick="sendChip(this)" data-text="Help me with staff management">
                    <i class="fas fa-user-tie"></i> Staff
                </button>
                <button class="ai-chip" onclick="sendChip(this)" data-text="What ERP features are available for admissions?">
                    <i class="fas fa-door-open"></i> Admissions
                </button>
            </div>
        </div>

    </div>

    {{-- Input Bar --}}
    <div class="ai-input-bar">
        <div class="ai-chat-input-wrap">
            <textarea
                class="ai-chat-input"
                id="aiChatInput"
                placeholder="Ask {{ $aiSettings->chatbot_name }} anything…"
                rows="1"
                maxlength="2000"
            ></textarea>
            <span class="ai-char-count" id="charCount">0/2000</span>
        </div>
        <button class="ai-send-btn" id="aiSendBtn" title="Send message">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>

</div>
@endsection

@section('scripts')
<script>
(function() {
    const messagesArea  = document.getElementById('aiMessages');
    const welcomeEl     = document.getElementById('aiWelcome');
    const input         = document.getElementById('aiChatInput');
    const sendBtn       = document.getElementById('aiSendBtn');
    const clearBtn      = document.getElementById('clearChatBtn');
    const charCount     = document.getElementById('charCount');
    const aiEnabled     = {{ $aiSettings->enabled ? 'true' : 'false' }};
    const hasApiKey     = {{ $aiSettings->api_key ? 'true' : 'false' }};
    const chatRoute     = "{{ route('school.ai.chat.send') }}";
    const csrfToken     = document.querySelector('meta[name="csrf-token"]').content;
    let   chatHistory   = [];
    let   isWaiting     = false;

    // Auto-grow textarea
    input.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        charCount.textContent = this.value.length + '/2000';
    });

    // Send on Enter (Shift+Enter = newline)
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            send();
        }
    });

    sendBtn.addEventListener('click', send);
    clearBtn.addEventListener('click', clearChat);

    // Chip click
    window.sendChip = function(el) {
        input.value = el.dataset.text || '';
        input.dispatchEvent(new Event('input'));
        send();
    };

    function send() {
        const text = input.value.trim();
        if (!text || isWaiting) return;

        hideWelcome();
        appendMsg(text, 'user');
        chatHistory.push({ role: 'user', content: text });
        input.value = '';
        input.style.height = 'auto';
        charCount.textContent = '0/2000';
        isWaiting = true;
        sendBtn.disabled = true;

        // Show typing
        const typingEl = showTyping();

        fetch(chatRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: text, history: chatHistory.slice(-10) }),
        })
        .then(r => r.json())
        .then(data => {
            typingEl.remove();
            const reply = data.reply || 'No response received.';
            appendMsg(reply, 'bot');
            chatHistory.push({ role: 'assistant', content: reply });
        })
        .catch(err => {
            typingEl.remove();
            appendMsg('❌ Network error. Please check your connection and try again.', 'bot');
        })
        .finally(() => {
            isWaiting = false;
            sendBtn.disabled = false;
            input.focus();
        });
    }

    function appendMsg(text, type) {
        const wrap = document.createElement('div');
        wrap.className = 'ai-msg ' + type;

        // Avatar
        const av = document.createElement('div');
        av.className = 'ai-msg-avatar';
        if (type === 'bot') {
            av.innerHTML = '<img src="{{ asset("images/ai-assistant.png") }}" alt="Bot">';
        } else {
            av.innerHTML = '<i class="fas fa-user"></i>';
        }

        // Bubble
        const bubble = document.createElement('div');
        bubble.className = 'ai-bubble';
        bubble.innerHTML = formatText(text);

        wrap.appendChild(av);
        wrap.appendChild(bubble);
        messagesArea.appendChild(wrap);

        // Time
        const time = document.createElement('div');
        time.className = 'ai-msg-time';
        time.textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        messagesArea.appendChild(time);

        messagesArea.scrollTop = messagesArea.scrollHeight;
    }

    function showTyping() {
        const wrap = document.createElement('div');
        wrap.className = 'ai-typing-indicator';
        wrap.innerHTML = `
            <div class="ai-msg-avatar" style="background:linear-gradient(135deg,#312e81,#4f46e5);overflow:hidden;">
                <img src="{{ asset('images/ai-assistant.png') }}" alt="Bot" style="width:24px;height:24px;object-fit:contain;">
            </div>
            <div class="ai-typing-bubble">
                <span></span><span></span><span></span>
            </div>`;
        messagesArea.appendChild(wrap);
        messagesArea.scrollTop = messagesArea.scrollHeight;
        return wrap;
    }

    function hideWelcome() {
        if (welcomeEl && welcomeEl.parentNode) {
            welcomeEl.style.transition = 'opacity .3s';
            welcomeEl.style.opacity = '0';
            setTimeout(() => welcomeEl.remove(), 300);
        }
    }

    function clearChat() {
        chatHistory = [];
        // Remove all messages except recreate welcome
        messagesArea.innerHTML = '';
        const welcome = `
            <div class="ai-welcome" id="aiWelcome">
                <img src="{{ asset('images/ai-assistant.png') }}" alt="AI Bot" class="ai-welcome-img">
                <h2>Hello! I'm {{ $aiSettings->chatbot_name }} 👋</h2>
                <p>I'm your intelligent school ERP assistant. Ask me anything!</p>
                <div class="ai-chip-row">
                    <button class="ai-chip" onclick="sendChip(this)" data-text="How many students are enrolled this year?"><i class="fas fa-users"></i> Students</button>
                    <button class="ai-chip" onclick="sendChip(this)" data-text="How do I manage fee collection?"><i class="fas fa-money-bill-wave"></i> Fees</button>
                    <button class="ai-chip" onclick="sendChip(this)" data-text="How do I take attendance?"><i class="fas fa-clipboard-check"></i> Attendance</button>
                </div>
            </div>`;
        messagesArea.innerHTML = welcome;
    }

    function formatText(text) {
        // Basic markdown-like formatting
        return text
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.+?)\*/g, '<em>$1</em>')
            .replace(/`(.+?)`/g, '<code style="background:rgba(0,0,0,0.06);padding:1px 5px;border-radius:4px;font-size:12px;">$1</code>')
            .replace(/\n/g, '<br>');
    }
})();
</script>
@endsection

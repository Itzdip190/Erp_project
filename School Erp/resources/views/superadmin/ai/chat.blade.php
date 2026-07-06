@extends('superadmin.layouts.master')

@section('styles')
<style>
/* ─── SUPERADMIN AI CHAT ───────────────────────────────────── */
.sa-chat-wrap {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 120px);
    background: #faf8f5;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    position: relative;
    box-shadow: 0 4px 20px rgba(0,0,0,0.02);
}

/* Header */
.sa-chat-header {
    background: linear-gradient(135deg, #0c1024 0%, #1e1b4b 100%);
    padding: 16px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}
.sa-chat-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.sa-chat-avatar {
    width: 42px; height: 42px;
    border-radius: 12px;
    background: rgba(255,255,255,0.08);
    border: 1.5px solid rgba(255,255,255,0.15);
    display: flex; align-items: center; justify-content: center;
}
.sa-chat-avatar img { width: 32px; height: 32px; object-fit: contain; }
.sa-chat-botname { color: #fff; font-size: 15px; font-weight: 800; margin: 0; }
.sa-chat-botsub { color: rgba(255,255,255,0.6); font-size: 11px; margin-top: 1px; }

/* Messages */
.sa-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    background: #f8fafc;
}
.sa-chat-messages::-webkit-scrollbar { width: 4px; }
.sa-chat-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

/* Welcome Screen */
.sa-chat-welcome {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex: 1;
    text-align: center;
    padding: 40px 20px;
}
.sa-chat-welcome img {
    width: 80px; height: 80px;
    object-fit: contain;
    margin-bottom: 16px;
    animation: sa-float 3s ease-in-out infinite;
}
@keyframes sa-float { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-6px); } }
.sa-chat-welcome h2 { font-size: 20px; font-weight: 800; color: #1e1b4b; margin-bottom: 8px; }
.sa-chat-welcome p { font-size: 13px; color: #64748b; max-width: 400px; margin: 0 auto 20px; line-height: 1.5; }

.sa-chip-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    max-width: 600px;
}
.sa-chip {
    padding: 8px 14px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    color: #4b5563;
    cursor: pointer;
    transition: all .2s;
    display: inline-flex; align-items: center; gap: 6px;
}
.sa-chip:hover {
    border-color: #6366f1;
    color: #6366f1;
    background: rgba(99,102,241,0.05);
    transform: translateY(-1px);
}

/* Bubbles */
.sa-msg { display: flex; align-items: flex-end; gap: 10px; max-width: 80%; }
.sa-msg.user { margin-left: auto; flex-direction: row-reverse; }
.sa-msg-avatar {
    width: 32px; height: 32px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 12px;
}
.sa-msg.bot .sa-msg-avatar { background: linear-gradient(135deg, #0c1024, #4f46e5); }
.sa-msg.bot .sa-msg-avatar img { width: 24px; height: 24px; object-fit: contain; }
.sa-msg.user .sa-msg-avatar { background: #f59e0b; color: #fff; font-weight: bold; }
.sa-bubble { padding: 12px 16px; border-radius: 16px; font-size: 13px; line-height: 1.55; }
.sa-msg.bot .sa-bubble { background: #fff; color: #1f2937; border-radius: 4px 16px 16px 16px; border: 1px solid #f1f5f9; box-shadow: 0 2px 8px rgba(0,0,0,.03); }
.sa-msg.user .sa-bubble { background: linear-gradient(135deg, #4f46e5, #6366f1); color: #fff; border-radius: 16px 16px 4px 16px; }

/* Typing indicator */
.sa-typing { display: flex; gap: 4px; align-items: center; padding: 12px 16px; }
.sa-typing span { width: 7px; height: 7px; background: #a5b4fc; border-radius: 50%; animation: sa-type 1.2s infinite; }
.sa-typing span:nth-child(2) { animation-delay: .2s; }
.sa-typing span:nth-child(3) { animation-delay: .4s; }
@keyframes sa-type { 0%,100% { transform:translateY(0); opacity:.5; } 50% { transform:translateY(-5px); opacity:1; } }

/* Input bar */
.sa-chat-input-bar {
    padding: 16px 24px;
    background: #fff;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 12px;
}
.sa-chat-input-wrap {
    flex: 1;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    display: flex;
    align-items: center;
    padding: 8px 14px;
}
.sa-chat-input-wrap:focus-within { border-color: #6366f1; background: #fff; }
.sa-chat-input {
    flex: 1; border: none; background: none; outline: none;
    font-size: 13px; color: #1e1b4b; font-family: 'Lato', sans-serif;
}
.sa-send-btn {
    width: 38px; height: 38px; border-radius: 10px;
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    border: none; color: #fff; font-size: 14px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 12px rgba(99,102,241,.3);
    transition: transform .2s;
}
.sa-send-btn:hover { transform: scale(1.05); }

/* Banner */
.sa-key-banner {
    padding: 10px 20px;
    background: rgba(245,158,11,0.1);
    border-bottom: 1px solid rgba(245,158,11,0.2);
    color: #b45309; font-size: 12px; font-weight: 700;
    display: flex; align-items: center; gap: 8px;
}

/* Dark Mode overrides */
body.dark-mode .sa-chat-wrap { background: #111827 !important; border-color: #1e293b !important; }
body.dark-mode .sa-chat-messages { background: #0b0f19 !important; }
body.dark-mode .sa-chat-welcome h2 { color: #f1f5f9 !important; }
body.dark-mode .sa-chat-welcome p { color: #64748b !important; }
body.dark-mode .sa-chip { background: #1f2937 !important; border-color: #374151 !important; color: #cbd5e1 !important; }
body.dark-mode .sa-chip:hover { border-color: #6366f1 !important; color: #fff !important; }
body.dark-mode .sa-msg.bot .sa-bubble { background: #1f2937 !important; color: #f1f5f9 !important; border-color: #374151 !important; }
body.dark-mode .sa-chat-input-bar { background: #111827 !important; border-top-color: #1e293b !important; }
body.dark-mode .sa-chat-input-wrap { background: #1f2937 !important; border-color: #374151 !important; }
body.dark-mode .sa-chat-input-wrap:focus-within { background: #111827 !important; border-color: #6366f1 !important; }
body.dark-mode .sa-chat-input { color: #f1f5f9 !important; }
body.dark-mode .sa-key-banner { background: rgba(245,158,11,0.08) !important; color: #f59e0b !important; border-bottom-color: #1e293b !important; }

/* Mobile viewport chat overrides */
@media(max-width: 480px) {
    .sa-chat-wrap {
        height: calc(100vh - 160px);
        border-radius: 12px;
    }
    .sa-chat-messages {
        padding: 16px;
    }
    .sa-chat-input-bar {
        padding: 12px 16px;
    }
    .sa-chat-header {
        padding: 12px 16px;
    }
    .sa-chat-botname {
        font-size: 13.5px;
    }
}
</style>
@endsection

@section('content')
<div class="sa-chat-wrap">
    
    {{-- Header --}}
    <div class="sa-chat-header">
        <div class="sa-chat-header-left">
            <div class="sa-chat-avatar">
                <img src="{{ asset('images/ai-assistant.png') }}" alt="AI">
            </div>
            <div>
                <h3 class="sa-chat-botname">SuperAdmin Platform Assistant</h3>
                <div class="sa-chat-botsub">
                    @if($hasKey)
                        <span style="color:#10b981;">● Active</span> · {{ $globalModel }}
                    @else
                        <span style="color:#ef4444;">○ Inactive</span> · No API key configured
                    @endif
                </div>
            </div>
        </div>
        <button class="btn btn-sm btn-outline-light" id="clearBtn"><i class="fas fa-trash-can mr-1"></i> Clear</button>
    </div>

    {{-- Banner --}}
    @if(!$hasKey)
    <div class="sa-key-banner">
        <i class="fas fa-triangle-exclamation"></i>
        <span>No SuperAdmin API key configured. Add <code>SUPERADMIN_AI_API_KEY</code> and <code>SUPERADMIN_AI_PROVIDER</code> to your <code>.env</code> file.</span>
    </div>
    @endif

    {{-- Messages --}}
    <div class="sa-chat-messages" id="chatMsgs">
        <div class="sa-chat-welcome" id="welcomeScreen">
            <img src="{{ asset('images/ai-assistant.png') }}" alt="AI">
            <h2>SuperAdmin Platform Copilot</h2>
            <p>Welcome! Ask me anything about managing school tenants, analyzing registrations, debugging subscription plans, or platform metrics.</p>
            <div class="sa-chip-grid">
                <button class="sa-chip" onclick="useChip(this)" data-text="How can I onboard a new school?">Onboarding Schools</button>
                <button class="sa-chip" onclick="useChip(this)" data-text="What happens when a school's subscription expires?">Subscription Rules</button>
                <button class="sa-chip" onclick="useChip(this)" data-text="How do I troubleshoot SMS gateway configurations?">SMS Gateways</button>
                <button class="sa-chip" onclick="useChip(this)" data-text="What system health parameters can I monitor?">Platform Monitoring</button>
            </div>
        </div>
    </div>

    {{-- Input --}}
    <div class="sa-chat-input-bar">
        <div class="sa-chat-input-wrap">
            <input type="text" class="sa-chat-input" id="chatInput" placeholder="Ask anything about the platform…" autocomplete="off">
        </div>
        <button class="sa-send-btn" id="sendBtn"><i class="fas fa-paper-plane"></i></button>
    </div>

</div>
@endsection

@section('scripts')
<script>
(function() {
    const msgs    = document.getElementById('chatMsgs');
    const welcome = document.getElementById('welcomeScreen');
    const input   = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendBtn');
    const clearBtn= document.getElementById('clearBtn');
    const hasKey  = {{ $hasKey ? 'true' : 'false' }};
    const sendUrl = "{{ route('superadmin.ai.chat.send') }}";
    const csrf    = document.querySelector('meta[name="csrf-token"]').content;
    let history   = [];
    let isWaiting = false;

    input.addEventListener('keydown', e => { if (e.key === 'Enter') send(); });
    sendBtn.addEventListener('click', send);
    clearBtn.addEventListener('click', clear);

    window.useChip = function(btn) {
        input.value = btn.dataset.text;
        send();
    };

    function send() {
        const text = input.value.trim();
        if (!text || isWaiting) return;
        input.value = '';

        if (welcome) welcome.style.display = 'none';

        appendMsg(text, 'user');
        history.push({ role: 'user', content: text });

        isWaiting = true;
        showTyping();

        fetch(sendUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ message: text, history: history.slice(-10) })
        })
        .then(r => r.json())
        .then(data => {
            removeTyping();
            const reply = data.reply || 'No reply.';
            appendMsg(reply, 'bot');
            history.push({ role: 'assistant', content: reply });
        })
        .catch(() => {
            removeTyping();
            appendMsg('❌ Network error communicating with the AI service.', 'bot');
        })
        .finally(() => {
            isWaiting = false;
        });
    }

    function appendMsg(text, type) {
        const wrap = document.createElement('div');
        wrap.className = 'sa-msg ' + type;

        const av = document.createElement('div');
        av.className = 'sa-msg-avatar';
        if (type === 'bot') {
            av.innerHTML = '<img src="{{ asset("images/ai-assistant.png") }}" alt="AI">';
        } else {
            av.innerHTML = 'SA';
        }

        const bubble = document.createElement('div');
        bubble.className = 'sa-bubble';
        bubble.innerHTML = formatMarkdown(text);

        wrap.appendChild(av);
        wrap.appendChild(bubble);
        msgs.appendChild(wrap);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function showTyping() {
        const wrap = document.createElement('div');
        wrap.className = 'sa-msg bot';
        wrap.id = 'saTyping';
        wrap.innerHTML = `
            <div class="sa-msg-avatar"><img src="{{ asset('images/ai-assistant.png') }}" alt="AI"></div>
            <div class="sa-bubble sa-typing"><span></span><span></span><span></span></div>
        `;
        msgs.appendChild(wrap);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function removeTyping() {
        const t = document.getElementById('saTyping');
        if (t) t.remove();
    }

    function clear() {
        history = [];
        msgs.innerHTML = '';
        if (welcome) {
            welcome.style.display = 'flex';
            msgs.appendChild(welcome);
        }
    }

    function formatMarkdown(t) {
        return t
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.+?)\*/g, '<em>$1</em>')
            .replace(/`(.+?)`/g, '<code style="background:rgba(0,0,0,0.05);padding:1px 5px;border-radius:4px;font-size:11px;">$1</code>')
            .replace(/\n/g, '<br>');
    }
})();
</script>
@endsection

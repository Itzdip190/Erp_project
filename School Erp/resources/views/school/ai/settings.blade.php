@extends('layouts.app')

@section('title', 'AI Settings')

@section('styles')
<style>
/* ─── AI SETTINGS PAGE ─────────────────────────────────────── */
:root {
    --ai-primary: #6366f1;
    --ai-primary-dark: #4f46e5;
    --ai-accent: #a78bfa;
    --ai-gold: #f59e0b;
    --ai-green: #10b981;
    --ai-red: #ef4444;
}

.ai-page-wrap {
    padding: 24px 28px;
    max-width: 1100px;
    margin: 0 auto;
}

/* ─── Page Header ───────────────────────────────────────────── */
.ai-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 28px;
    flex-wrap: wrap;
    gap: 16px;
}
.ai-page-title-wrap {
    display: flex;
    align-items: center;
    gap: 16px;
}
.ai-page-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(99,102,241,0.35);
    position: relative;
    overflow: hidden;
}
.ai-page-icon img {
    width: 36px;
    height: 36px;
    object-fit: contain;
}
.ai-page-title-wrap h1 {
    font-size: 22px;
    font-weight: 800;
    color: var(--t1);
    margin: 0;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.ai-page-title-wrap p {
    font-size: 12px;
    color: var(--t2);
    margin: 3px 0 0;
}
.ai-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
}
.ai-status-badge.active {
    background: rgba(16,185,129,0.12);
    color: #10b981;
    border: 1px solid rgba(16,185,129,0.25);
}
.ai-status-badge.inactive {
    background: rgba(239,68,68,0.08);
    color: #ef4444;
    border: 1px solid rgba(239,68,68,0.2);
}
.ai-status-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    animation: pulse-dot 1.5s infinite;
}
.active .ai-status-dot { background: #10b981; }
.inactive .ai-status-dot { background: #ef4444; }
@keyframes pulse-dot {
    0%,100% { opacity:1; }
    50% { opacity:0.4; }
}

/* ─── Layout Grid ──────────────────────────────────────────── */
.ai-settings-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
    align-items: start;
}
@media(max-width: 900px) {
    .ai-settings-layout { grid-template-columns: 1fr; }
}

/* ─── Card ──────────────────────────────────────────────────── */
.ai-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    overflow: hidden;
    margin-bottom: 20px;
}
.ai-card-header {
    padding: 18px 22px 16px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 12px;
}
.ai-card-header-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
}
.ai-card-header h3 {
    font-size: 14px;
    font-weight: 800;
    color: var(--t1);
    margin: 0;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.ai-card-header p {
    font-size: 11px;
    color: var(--t2);
    margin: 2px 0 0;
}
.ai-card-body {
    padding: 22px;
}

/* ─── Form Controls ────────────────────────────────────────── */
.ai-form-group {
    margin-bottom: 20px;
}
.ai-form-group label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: var(--t1);
    margin-bottom: 7px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.ai-form-group label span {
    font-weight: 400;
    color: var(--t2);
    text-transform: none;
    letter-spacing: 0;
}
.ai-input {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 13.5px;
    font-family: 'Inter', sans-serif;
    color: var(--t1);
    background: #f8fafc;
    outline: none;
    transition: border-color .2s, background .2s, box-shadow .2s;
}
.ai-input:focus {
    border-color: var(--ai-primary);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
}
.ai-input.api-key-input {
    font-family: 'Courier New', monospace;
    letter-spacing: 0.5px;
    font-size: 13px;
}
.ai-select {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 13.5px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    color: var(--t1);
    background: #f8fafc;
    outline: none;
    cursor: pointer;
    transition: border-color .2s;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    padding-right: 36px;
}
.ai-select:focus {
    border-color: var(--ai-primary);
    box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
}
.ai-input-hint {
    font-size: 11px;
    color: var(--t2);
    margin-top: 5px;
}
.ai-input-hint a {
    color: var(--ai-primary);
    font-weight: 600;
    text-decoration: none;
}
.ai-input-hint a:hover { text-decoration: underline; }

/* Toggle Switch */
.ai-toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 0;
    border-bottom: 1px solid #f1f5f9;
}
.ai-toggle-row:last-child { border-bottom: none; }
.ai-toggle-info h4 {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--t1);
    margin: 0 0 3px;
}
.ai-toggle-info p {
    font-size: 11.5px;
    color: var(--t2);
    margin: 0;
}
.ai-toggle-switch {
    position: relative;
    width: 46px;
    height: 26px;
    flex-shrink: 0;
}
.ai-toggle-switch input { opacity:0; width:0; height:0; }
.ai-toggle-slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background: #cbd5e1;
    border-radius: 26px;
    transition: .3s;
}
.ai-toggle-slider:before {
    content: '';
    position: absolute;
    width: 20px; height: 20px;
    left: 3px; bottom: 3px;
    background: #fff;
    border-radius: 50%;
    transition: .3s;
    box-shadow: 0 2px 6px rgba(0,0,0,.15);
}
.ai-toggle-switch input:checked + .ai-toggle-slider { background: var(--ai-primary); }
.ai-toggle-switch input:checked + .ai-toggle-slider:before { transform: translateX(20px); }

/* API Key row */
.api-key-row {
    display: flex;
    gap: 10px;
    align-items: flex-start;
}
.api-key-row .ai-input { flex: 1; }
.btn-eye {
    width: 40px; height: 42px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
    color: var(--t2);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
    transition: all .2s;
    flex-shrink: 0;
}
.btn-eye:hover { border-color: var(--ai-primary); color: var(--ai-primary); background: #fff; }

/* Model cards */
.model-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
.model-card {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px;
    cursor: pointer;
    transition: all .2s;
    position: relative;
    background: #f8fafc;
}
.model-card:hover { border-color: var(--ai-primary); background: #fff; }
.model-card.selected {
    border-color: var(--ai-primary);
    background: rgba(99,102,241,0.05);
    box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
}
.model-card input[type="radio"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
}
.model-card-inner {
    display: flex;
    align-items: center;
    gap: 10px;
}
.model-badge {
    font-size: 9.5px;
    font-weight: 800;
    padding: 2px 8px;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.badge-gemini { background: #e8f5e9; color: #2e7d32; }
.badge-openai { background: #e3f2fd; color: #1565c0; }
.model-name { font-size: 13px; font-weight: 700; color: var(--t1); }
.model-desc { font-size: 10.5px; color: var(--t2); margin-top: 2px; }
.model-check {
    width: 18px; height: 18px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    margin-left: auto;
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    transition: all .2s;
}
.model-card.selected .model-check {
    background: var(--ai-primary);
    border-color: var(--ai-primary);
    color: #fff;
    font-size: 10px;
}

/* ─── Save Button ───────────────────────────────────────────── */
.ai-save-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: #fff;
    font-size: 13.5px;
    font-weight: 700;
    padding: 12px 28px;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 6px 20px rgba(99,102,241,0.35);
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.ai-save-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(99,102,241,0.45);
}
.ai-save-btn:active { transform: translateY(0); }

/* ─── Sidebar panels ────────────────────────────────────────── */
.ai-side-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    overflow: hidden;
    margin-bottom: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
}
.ai-side-card-hdr {
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid #f1f5f9;
}
.ai-side-card-hdr h4 {
    font-size: 13px;
    font-weight: 800;
    color: var(--t1);
    margin: 0;
}
.ai-side-card-body { padding: 16px 18px; }
.ai-guide-step {
    display: flex;
    gap: 12px;
    margin-bottom: 14px;
    align-items: flex-start;
}
.ai-guide-step:last-child { margin-bottom: 0; }
.step-num {
    width: 22px; height: 22px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: #fff;
    font-size: 11px;
    font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.step-text { font-size: 12px; color: var(--t2); line-height: 1.5; }
.step-text strong { color: var(--t1); }
.ai-link-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 9px 14px;
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    color: var(--t1);
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    transition: all .2s;
    margin-bottom: 8px;
}
.ai-link-btn:hover {
    border-color: var(--ai-primary);
    background: rgba(99,102,241,0.06);
    color: var(--ai-primary);
}
.ai-link-btn:last-child { margin-bottom: 0; }

/* Alert box */
.ai-alert {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 12px;
    margin-bottom: 20px;
}
.ai-alert.success {
    background: rgba(16,185,129,0.08);
    border: 1px solid rgba(16,185,129,0.2);
    color: #065f46;
}
.ai-alert.success i { color: #10b981; margin-top: 1px; }

/* ─── DARK MODE ─────────────────────────────────────────────── */
body.dark-mode .ai-card,
body.dark-mode .ai-side-card {
    background: #111827 !important;
    border-color: #1e293b !important;
}
body.dark-mode .ai-card-header,
body.dark-mode .ai-side-card-hdr {
    border-bottom-color: #1e293b !important;
}
body.dark-mode .ai-card-header h3,
body.dark-mode .ai-side-card-hdr h4,
body.dark-mode .ai-toggle-info h4,
body.dark-mode .model-name,
body.dark-mode .ai-form-group label,
body.dark-mode .ai-page-title-wrap h1 {
    color: #f1f5f9 !important;
}
body.dark-mode .ai-card-header p,
body.dark-mode .ai-toggle-info p,
body.dark-mode .model-desc,
body.dark-mode .ai-page-title-wrap p,
body.dark-mode .step-text,
body.dark-mode .ai-input-hint {
    color: #94a3b8 !important;
}
body.dark-mode .ai-input,
body.dark-mode .ai-select {
    background: #1f2937 !important;
    border-color: #374151 !important;
    color: #f1f5f9 !important;
}
body.dark-mode .ai-input:focus,
body.dark-mode .ai-select:focus {
    border-color: #6366f1 !important;
    background: #111827 !important;
}
body.dark-mode .btn-eye {
    background: #1f2937 !important;
    border-color: #374151 !important;
    color: #94a3b8 !important;
}
body.dark-mode .model-card {
    background: #1f2937 !important;
    border-color: #374151 !important;
}
body.dark-mode .model-card.selected {
    background: rgba(99,102,241,0.12) !important;
    border-color: #6366f1 !important;
}
body.dark-mode .ai-link-btn {
    background: #1f2937 !important;
    border-color: #374151 !important;
    color: #e2e8f0 !important;
}
body.dark-mode .ai-toggle-row {
    border-bottom-color: #1e293b !important;
}
body.dark-mode .ai-alert.success {
    background: rgba(16,185,129,0.1) !important;
    border-color: rgba(16,185,129,0.25) !important;
    color: #6ee7b7 !important;
}
body.dark-mode .ai-form-group label span { color: #64748b !important; }
</style>
@endsection

@section('content')
<div class="ai-page-wrap">

    {{-- Page Header --}}
    <div class="ai-page-header">
        <div class="ai-page-title-wrap">
            <div class="ai-page-icon">
                <img src="{{ asset('images/ai-assistant.png') }}" alt="AI Assistant">
            </div>
            <div>
                <h1>AI Assistant Settings</h1>
                <p>Configure your AI chatbot, API key, and model preferences</p>
            </div>
        </div>
        <span class="ai-status-badge {{ $aiSettings->enabled ? 'active' : 'inactive' }}">
            <span class="ai-status-dot"></span>
            {{ $aiSettings->enabled ? 'Active' : 'Inactive' }}
        </span>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
    <div class="ai-alert success">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('school.ai.settings.save') }}">
        @csrf
    <div class="ai-settings-layout">

        {{-- ─── MAIN COLUMN ─────────────────────────── --}}
        <div>

            {{-- 1. AI Status --}}
            <div class="ai-card">
                <div class="ai-card-header">
                    <div class="ai-card-header-icon" style="background:rgba(99,102,241,.1);">
                        <i class="fas fa-power-off" style="color:#6366f1;"></i>
                    </div>
                    <div>
                        <h3>AI Status</h3>
                        <p>Enable or disable the AI assistant for this school</p>
                    </div>
                </div>
                <div class="ai-card-body">
                    <div class="ai-toggle-row">
                        <div class="ai-toggle-info">
                            <h4>Enable AI Assistant</h4>
                            <p>When enabled, the floating chat button will be active and usable</p>
                        </div>
                        <label class="ai-toggle-switch">
                            <input type="checkbox" name="enabled" id="enabledToggle" {{ $aiSettings->enabled ? 'checked' : '' }}>
                            <span class="ai-toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- 2. API Key --}}
            <div class="ai-card">
                <div class="ai-card-header">
                    <div class="ai-card-header-icon" style="background:rgba(245,158,11,.1);">
                        <i class="fas fa-key" style="color:#f59e0b;"></i>
                    </div>
                    <div>
                        <h3>API Key</h3>
                        <p>Paste your Google AI Studio or OpenAI API key here</p>
                    </div>
                </div>
                <div class="ai-card-body">
                    <div class="ai-form-group">
                        <label>API Key <span>— kept encrypted</span></label>
                        <div class="api-key-row">
                            <input
                                type="password"
                                id="apiKeyInput"
                                name="api_key"
                                class="ai-input api-key-input"
                                placeholder="Paste your API key here…"
                                value="{{ $aiSettings->api_key ? $aiSettings->masked_key : '' }}"
                                autocomplete="off"
                            >
                            <button type="button" class="btn-eye" id="toggleKeyBtn" title="Show/Hide key">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        <p class="ai-input-hint">
                            Get a free Gemini key at <a href="https://aistudio.google.com/apikey" target="_blank">Google AI Studio</a>
                            &nbsp;|&nbsp; OpenAI keys at <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI Platform</a>
                        </p>
                    </div>
                </div>
            </div>

            {{-- 3. AI Model --}}
            <div class="ai-card">
                <div class="ai-card-header">
                    <div class="ai-card-header-icon" style="background:rgba(139,92,246,.1);">
                        <i class="fas fa-microchip" style="color:#8b5cf6;"></i>
                    </div>
                    <div>
                        <h3>AI Model</h3>
                        <p>Select the AI model to power your chatbot</p>
                    </div>
                </div>
                <div class="ai-card-body">
                    <div class="model-grid">
                        @php
                        $models = [
                            ['id'=>'gemini-2.0-flash', 'provider'=>'gemini', 'badge'=>'Gemini', 'badgeClass'=>'badge-gemini', 'name'=>'Gemini 2.0 Flash', 'desc'=>'Fast & free — recommended'],
                            ['id'=>'gemini-2.5-flash', 'provider'=>'gemini', 'badge'=>'Gemini', 'badgeClass'=>'badge-gemini', 'name'=>'Gemini 2.5 Flash', 'desc'=>'More powerful, best quality'],
                            ['id'=>'gpt-4o-mini',      'provider'=>'openai', 'badge'=>'OpenAI', 'badgeClass'=>'badge-openai', 'name'=>'GPT-4o Mini',       'desc'=>'Fast & affordable'],
                            ['id'=>'gpt-4o',           'provider'=>'openai', 'badge'=>'OpenAI', 'badgeClass'=>'badge-openai', 'name'=>'GPT-4o',             'desc'=>'Most capable OpenAI model'],
                        ];
                        @endphp
                        @foreach($models as $model)
                        <label class="model-card {{ $aiSettings->ai_model === $model['id'] ? 'selected' : '' }}" onclick="selectModel(this, '{{ $model['provider'] }}')">
                            <input type="radio" name="ai_model" value="{{ $model['id'] }}" {{ $aiSettings->ai_model === $model['id'] ? 'checked' : '' }}>
                            <div class="model-card-inner">
                                <div>
                                    <span class="model-badge {{ $model['badgeClass'] }}">{{ $model['badge'] }}</span>
                                    <div class="model-name" style="margin-top:6px;">{{ $model['name'] }}</div>
                                    <div class="model-desc">{{ $model['desc'] }}</div>
                                </div>
                                <div class="model-check">
                                    @if($aiSettings->ai_model === $model['id'])
                                    <i class="fas fa-check"></i>
                                    @endif
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    <input type="hidden" name="ai_provider" id="aiProvider" value="{{ $aiSettings->ai_provider }}">
                </div>
            </div>

            {{-- 4. Chatbot Branding --}}
            <div class="ai-card">
                <div class="ai-card-header">
                    <div class="ai-card-header-icon" style="background:rgba(16,185,129,.1);">
                        <i class="fas fa-robot" style="color:#10b981;"></i>
                    </div>
                    <div>
                        <h3>Chatbot Branding</h3>
                        <p>Customize how your AI assistant appears to users</p>
                    </div>
                </div>
                <div class="ai-card-body">
                    <div class="ai-form-group">
                        <label>Chatbot Name</label>
                        <input type="text" name="chatbot_name" class="ai-input" placeholder="e.g. School Assistant, EduBot…" value="{{ $aiSettings->chatbot_name }}">
                        <p class="ai-input-hint">This name appears in the chatbot header and welcome message</p>
                    </div>
                    <div class="ai-form-group" style="margin-bottom:0;">
                        <label>Max Response Tokens</label>
                        <input type="number" name="max_tokens" class="ai-input" min="256" max="8192" value="{{ $aiSettings->max_tokens }}" placeholder="1024">
                        <p class="ai-input-hint">1,024 tokens ≈ ~750 words. Larger values cost more but allow longer answers.</p>
                    </div>
                </div>
            </div>

            {{-- Save Button --}}
            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" class="ai-save-btn">
                    <i class="fas fa-floppy-disk"></i>
                    Save Settings
                </button>
            </div>

        </div>

        {{-- ─── SIDEBAR COLUMN ──────────────────────── --}}
        <div>

            {{-- Quick Start Guide --}}
            <div class="ai-side-card">
                <div class="ai-side-card-hdr">
                    <i class="fas fa-bolt" style="color:#f59e0b;font-size:15px;"></i>
                    <h4>Quick Start Guide</h4>
                </div>
                <div class="ai-side-card-body">
                    <div class="ai-guide-step">
                        <div class="step-num">1</div>
                        <div class="step-text"><strong>Enable AI</strong> — toggle the switch above</div>
                    </div>
                    <div class="ai-guide-step">
                        <div class="step-num">2</div>
                        <div class="step-text"><strong>Get API Key</strong> — from Google AI Studio (free)</div>
                    </div>
                    <div class="ai-guide-step">
                        <div class="step-num">3</div>
                        <div class="step-text"><strong>Paste Key</strong> — in the API key field above</div>
                    </div>
                    <div class="ai-guide-step">
                        <div class="step-num">4</div>
                        <div class="step-text"><strong>Choose Model</strong> — Gemini 1.5 Flash recommended</div>
                    </div>
                    <div class="ai-guide-step">
                        <div class="step-num">5</div>
                        <div class="step-text"><strong>Save & Test</strong> — open AI Chat and try it!</div>
                    </div>
                </div>
            </div>

            {{-- Useful Links --}}
            <div class="ai-side-card">
                <div class="ai-side-card-hdr">
                    <i class="fas fa-link" style="color:#6366f1;font-size:15px;"></i>
                    <h4>Useful Links</h4>
                </div>
                <div class="ai-side-card-body">
                    <a href="https://aistudio.google.com/apikey" target="_blank" class="ai-link-btn">
                        <i class="fas fa-key" style="color:#f59e0b;"></i>
                        Google AI Studio (API Keys)
                    </a>
                    <a href="https://platform.openai.com/api-keys" target="_blank" class="ai-link-btn">
                        <i class="fas fa-key" style="color:#10b981;"></i>
                        OpenAI Platform (API Keys)
                    </a>
                    <a href="{{ route('school.ai.chat') }}" class="ai-link-btn">
                        <i class="fas fa-comments" style="color:#6366f1;"></i>
                        Open AI Chat
                    </a>
                </div>
            </div>

            {{-- Chatbot Preview --}}
            <div class="ai-side-card">
                <div class="ai-side-card-hdr">
                    <i class="fas fa-eye" style="color:#10b981;font-size:15px;"></i>
                    <h4>Chatbot Preview</h4>
                </div>
                <div class="ai-side-card-body" style="text-align:center;">
                    <img src="{{ asset('images/ai-assistant.png') }}" alt="AI Bot" style="width:80px;height:80px;object-fit:contain;margin:0 auto 12px;display:block;">
                    <div style="font-size:14px;font-weight:800;color:var(--t1);margin-bottom:4px;" id="previewName">{{ $aiSettings->chatbot_name }}</div>
                    <div style="font-size:11px;color:var(--t2);">AI-powered school assistant</div>
                    <div style="margin-top:12px;">
                        <span class="ai-status-badge {{ $aiSettings->enabled ? 'active' : 'inactive' }}" id="previewStatus">
                            <span class="ai-status-dot"></span>
                            {{ $aiSettings->enabled ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </form>

</div>
@endsection

@section('scripts')
<script>
// Toggle API key visibility
document.getElementById('toggleKeyBtn').addEventListener('click', function() {
    const input = document.getElementById('apiKeyInput');
    const icon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
});

// Model selection
function selectModel(card, provider) {
    document.querySelectorAll('.model-card').forEach(c => {
        c.classList.remove('selected');
        const chk = c.querySelector('.model-check');
        if (chk) chk.innerHTML = '';
    });
    card.classList.add('selected');
    const chk = card.querySelector('.model-check');
    if (chk) chk.innerHTML = '<i class="fas fa-check"></i>';
    document.getElementById('aiProvider').value = provider;
}

// Live preview name update
document.querySelector('[name="chatbot_name"]').addEventListener('input', function() {
    const preview = document.getElementById('previewName');
    if (preview) preview.textContent = this.value || 'AI Assistant';
});

// Live preview status update
document.getElementById('enabledToggle').addEventListener('change', function() {
    const badge = document.getElementById('previewStatus');
    if (!badge) return;
    if (this.checked) {
        badge.className = 'ai-status-badge active';
        badge.innerHTML = '<span class="ai-status-dot"></span> Active';
    } else {
        badge.className = 'ai-status-badge inactive';
        badge.innerHTML = '<span class="ai-status-dot"></span> Inactive';
    }
});
</script>
@endsection

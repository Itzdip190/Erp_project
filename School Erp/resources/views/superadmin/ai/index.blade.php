@extends('superadmin.layouts.master')

@section('styles')
<style>
/* ─── AI OVERVIEW PAGE ──────────────────────────────────────── */
.sa-ai-wrap { padding: 0; }

.sa-ai-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 14px;
}
.sa-ai-title-area {
    display: flex;
    align-items: center;
    gap: 16px;
}
.sa-ai-icon {
    width: 52px; height: 52px;
    border-radius: 16px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 8px 24px rgba(99,102,241,.3);
    overflow: hidden;
}
.sa-ai-icon img { width: 36px; height: 36px; object-fit: contain; }
.sa-ai-title-area h1 {
    font-size: 20px; font-weight: 800;
    color: #1e1b4b; margin: 0;
    font-family: 'Lato', sans-serif;
}
.sa-ai-title-area p { font-size: 12px; color: #64748b; margin: 3px 0 0; }

/* Stats row */
.sa-stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
@media(max-width:768px) { .sa-stats-row { grid-template-columns: 1fr 1fr; } }
@media(max-width:480px) {
    .sa-stats-row { grid-template-columns: 1fr; }
    .sa-ai-header { flex-direction: column; align-items: stretch; text-align: center; gap: 14px; }
    .sa-ai-title-area { flex-direction: column; text-align: center; }
    .btn-chat-link { width: 100%; justify-content: center; }
    .sa-table-hdr { flex-direction: column; align-items: stretch; gap: 12px; }
    .sa-table-search { max-width: 100%; }
}
.sa-stat-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 18px 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.04);
    display: flex;
    align-items: center;
    gap: 14px;
}
.sa-stat-icon {
    width: 42px; height: 42px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.sa-stat-val { font-size: 24px; font-weight: 800; color: #1e1b4b; line-height: 1; }
.sa-stat-lbl { font-size: 11.5px; font-weight: 600; color: #64748b; margin-top: 3px; }

/* Table card */
.sa-table-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,.04);
}
.sa-table-hdr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px 16px;
    border-bottom: 1px solid #f1f5f9;
    gap: 12px;
    flex-wrap: wrap;
}
.sa-table-hdr h3 { font-size: 14px; font-weight: 800; color: #1e1b4b; margin: 0; }
.sa-table-search {
    flex: 1; max-width: 260px;
    padding: 8px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 13px;
    outline: none;
    background: #f8fafc;
    color: #1e1b4b;
    transition: border-color .2s;
}
.sa-table-search:focus { border-color: #6366f1; background: #fff; }

.sa-table { width: 100%; border-collapse: collapse; }
.sa-table th {
    padding: 11px 16px;
    font-size: 10.5px;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: #f8fafc;
    text-align: left;
    border-bottom: 1px solid #f1f5f9;
}
.sa-table td {
    padding: 14px 16px;
    font-size: 13px;
    color: #374151;
    border-bottom: 1px solid #f8fafc;
    vertical-align: middle;
}
.sa-table tr:last-child td { border-bottom: none; }
.sa-table tr:hover td { background: #fafafa; }

/* School name cell */
.sa-school-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}
.sa-school-avatar {
    width: 34px; height: 34px;
    border-radius: 10px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
}
.sa-school-name { font-weight: 700; color: #1e1b4b; font-size: 13px; }
.sa-school-code { font-size: 11px; color: #94a3b8; }

/* Status badges */
.sa-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px; font-weight: 700;
}
.sa-badge.active    { background: rgba(16,185,129,.1);  color: #10b981; }
.sa-badge.inactive  { background: rgba(239,68,68,.08);  color: #ef4444; }
.sa-badge.suspended { background: rgba(245,158,11,.1);  color: #f59e0b; }
.sa-badge.ai-on     { background: rgba(99,102,241,.1);  color: #6366f1; }
.sa-badge.ai-off    { background: rgba(148,163,184,.1); color: #94a3b8; }
.sa-badge.has-key   { background: rgba(16,185,129,.1);  color: #10b981; }
.sa-badge.no-key    { background: rgba(239,68,68,.08);  color: #ef4444; }
.sa-badge-dot { width:6px; height:6px; border-radius:50%; background:currentColor; }

/* Toggle switch */
.sa-toggle {
    position: relative;
    width: 42px; height: 24px;
    flex-shrink: 0;
}
.sa-toggle input { opacity:0; width:0; height:0; }
.sa-toggle-slider {
    position: absolute; inset: 0;
    background: #cbd5e1;
    border-radius: 24px;
    cursor: pointer;
    transition: .25s;
}
.sa-toggle-slider:before {
    content:'';
    position: absolute;
    width: 18px; height: 18px;
    left: 3px; bottom: 3px;
    background: #fff;
    border-radius: 50%;
    transition: .25s;
    box-shadow: 0 2px 5px rgba(0,0,0,.15);
}
.sa-toggle input:checked + .sa-toggle-slider { background: #6366f1; }
.sa-toggle input:checked + .sa-toggle-slider:before { transform: translateX(18px); }

.btn-chat-link {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 18px;
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    color: #fff !important;
    border-radius: 10px;
    font-size: 12.5px; font-weight: 700;
    text-decoration: none;
    transition: all .2s;
    box-shadow: 0 4px 14px rgba(99,102,241,.3);
}
.btn-chat-link:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(99,102,241,.4); color:#fff !important; }

/* Toast */
#saToast {
    position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(20px);
    background: #1e1b4b; color: #fff;
    padding: 10px 22px; border-radius: 30px;
    font-size: 13px; font-weight: 600;
    box-shadow: 0 8px 24px rgba(0,0,0,.2);
    opacity: 0; transition: all .3s; z-index: 99999; white-space: nowrap;
}
#saToast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

/* Dark Mode */
body.dark-mode .sa-stat-card,
body.dark-mode .sa-table-card { background: #111827 !important; border-color: #1e293b !important; }
body.dark-mode .sa-table th { background: #0b0f19 !important; color: #475569 !important; border-color: #1e293b !important; }
body.dark-mode .sa-table td { border-color: #1e293b !important; color: #cbd5e1 !important; }
body.dark-mode .sa-table tr:hover td { background: #1a2235 !important; }
body.dark-mode .sa-table-hdr { border-color: #1e293b !important; }
body.dark-mode .sa-table-hdr h3,
body.dark-mode .sa-ai-title-area h1,
body.dark-mode .sa-stat-val,
body.dark-mode .sa-school-name { color: #f1f5f9 !important; }
body.dark-mode .sa-table-search { background: #1f2937 !important; border-color: #374151 !important; color: #f1f5f9 !important; }
body.dark-mode .sa-school-code,
body.dark-mode .sa-stat-lbl,
body.dark-mode .sa-ai-title-area p { color: #64748b !important; }
</style>
@endsection

@section('content')
<div class="sa-ai-wrap">

    {{-- Header --}}
    <div class="sa-ai-header">
        <div class="sa-ai-title-area">
            <div class="sa-ai-icon">
                <img src="{{ asset('images/ai-assistant.png') }}" alt="AI">
            </div>
            <div>
                <h1>AI Intelligence Overview</h1>
                <p>Monitor and control AI settings across all {{ $totalSchools }} schools</p>
            </div>
        </div>
        <a href="{{ route('superadmin.ai.chat') }}" class="btn-chat-link">
            <i class="fas fa-comments"></i> Open AI Chat
        </a>
    </div>

    {{-- Stats --}}
    <div class="sa-stats-row">
        <div class="sa-stat-card">
            <div class="sa-stat-icon" style="background:rgba(99,102,241,.1);">
                <i class="fas fa-school" style="color:#6366f1;"></i>
            </div>
            <div>
                <div class="sa-stat-val">{{ $totalSchools }}</div>
                <div class="sa-stat-lbl">Total Schools</div>
            </div>
        </div>
        <div class="sa-stat-card">
            <div class="sa-stat-icon" style="background:rgba(16,185,129,.1);">
                <i class="fas fa-robot" style="color:#10b981;"></i>
            </div>
            <div>
                <div class="sa-stat-val">{{ $totalWithAi }}</div>
                <div class="sa-stat-lbl">AI Enabled</div>
            </div>
        </div>
        <div class="sa-stat-card">
            <div class="sa-stat-icon" style="background:rgba(245,158,11,.1);">
                <i class="fas fa-key" style="color:#f59e0b;"></i>
            </div>
            <div>
                <div class="sa-stat-val">{{ $totalWithKey }}</div>
                <div class="sa-stat-lbl">Keys Configured</div>
            </div>
        </div>
    </div>

    {{-- Schools Table --}}
    <div class="sa-table-card">
        <div class="sa-table-hdr">
            <h3><i class="fas fa-list" style="color:#6366f1;margin-right:8px;"></i>All Schools — AI Status</h3>
            <input type="text" class="sa-table-search" placeholder="Search school…" id="schoolSearch">
        </div>
        <div style="overflow-x:auto;">
            <table class="sa-table" id="schoolsTable">
                <thead>
                    <tr>
                        <th>School</th>
                        <th>Status</th>
                        <th>AI Enabled</th>
                        <th>API Key</th>
                        <th>Model</th>
                        <th>Bot Name</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schools as $school)
                    <tr data-school-name="{{ strtolower($school->name) }}">
                        <td>
                            <div class="sa-school-cell">
                                <div class="sa-school-avatar">{{ strtoupper(substr($school->name, 0, 2)) }}</div>
                                <div>
                                    <div class="sa-school-name">{{ $school->name }}</div>
                                    <div class="sa-school-code">{{ $school->code }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="sa-badge {{ $school->status ?? 'inactive' }}">
                                <span class="sa-badge-dot"></span>
                                {{ ucfirst($school->status ?? 'Unknown') }}
                            </span>
                        </td>
                        <td>
                            <label class="sa-toggle">
                                <input type="checkbox"
                                    class="ai-toggle-input"
                                    data-school-id="{{ $school->id }}"
                                    {{ $school->ai_enabled ? 'checked' : '' }}>
                                <span class="sa-toggle-slider"></span>
                            </label>
                        </td>
                        <td>
                            <span class="sa-badge {{ $school->has_api_key ? 'has-key' : 'no-key' }}">
                                <i class="fas fa-{{ $school->has_api_key ? 'check' : 'times' }}" style="font-size:10px;"></i>
                                {{ $school->has_api_key ? 'Configured' : 'Not Set' }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:#6366f1;font-weight:600;">
                            {{ $school->ai_model ?? '—' }}
                        </td>
                        <td style="font-size:12px;color:#64748b;">
                            {{ $school->chatbot_name ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">
                            <i class="fas fa-school" style="font-size:28px;display:block;margin-bottom:10px;opacity:.3;"></i>
                            No schools found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<div id="saToast"></div>
@endsection

@section('scripts')
<script>
// Table search
document.getElementById('schoolSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#schoolsTable tbody tr').forEach(tr => {
        const name = tr.dataset.schoolName || '';
        tr.style.display = name.includes(q) ? '' : 'none';
    });
});

// AI toggle
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
document.querySelectorAll('.ai-toggle-input').forEach(function(toggle) {
    toggle.addEventListener('change', function() {
        const schoolId = this.dataset.schoolId;
        const enabled  = this.checked;
        fetch('{{ route("superadmin.ai.toggle") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ school_id: schoolId, enabled: enabled })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) showToast('AI ' + (enabled ? 'enabled' : 'disabled') + ' for school');
        })
        .catch(() => { this.checked = !enabled; showToast('Error updating AI status', true); });
    });
});

function showToast(msg, isError = false) {
    const t = document.getElementById('saToast');
    t.textContent = msg;
    t.style.background = isError ? '#ef4444' : '#1e1b4b';
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2800);
}
</script>
@endsection

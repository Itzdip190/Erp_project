@extends('superadmin.layouts.master')

@section('styles')
<style>
/* ─── MENU MANAGER ────────────────────────────────────────── */
.sa-menu-wrap { padding: 0; }

.sa-menu-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 14px;
}
.sa-menu-title-area {
    display: flex;
    align-items: center;
    gap: 16px;
}
.sa-menu-icon {
    width: 52px; height: 52px;
    border-radius: 16px;
    background: linear-gradient(135deg, #10b981, #059669);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 8px 24px rgba(16,185,129,.25);
    color: #fff;
    font-size: 22px;
}
.sa-menu-title-area h1 {
    font-size: 20px; font-weight: 800;
    color: #1e1b4b; margin: 0;
}
.sa-menu-title-area p { font-size: 12px; color: #64748b; margin: 3px 0 0; }

/* School Selector card */
.sa-select-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(0,0,0,.03);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}
.sa-select-label {
    font-size: 14px;
    font-weight: 700;
    color: #1e1b4b;
    margin: 0;
}
.sa-select-input {
    min-width: 250px;
    padding: 10px 14px;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    outline: none;
    font-size: 13.5px;
    font-weight: 600;
    color: #1e1b4b;
    background-color: #f8fafc;
    transition: border-color .2s;
}
.sa-select-input:focus { border-color: #10b981; background: #fff; }

/* Grid of service cards */
.sa-menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 18px;
    margin-bottom: 30px;
}
.sa-service-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 20px;
    box-shadow: 0 3px 12px rgba(0,0,0,.02);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform .2s, box-shadow .2s, border-color .2s;
}
.sa-service-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,.04);
}
.sa-service-card.disabled-service {
    border-color: #f1f5f9;
    background: #fafbfc;
}
.sa-service-hdr {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
}
.sa-service-info {
    display: flex;
    align-items: center;
    gap: 12px;
}
.sa-service-icon-bg {
    width: 40px; height: 40px;
    border-radius: 10px;
    background: rgba(16,185,129,.1);
    color: #10b981;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.sa-service-card.disabled-service .sa-service-icon-bg {
    background: #e2e8f0;
    color: #94a3b8;
}
.sa-service-title {
    font-size: 14.5px;
    font-weight: 800;
    color: #1e1b4b;
}
.sa-service-card.disabled-service .sa-service-title {
    color: #64748b;
}
.sa-service-desc {
    font-size: 11.5px;
    color: #64748b;
    line-height: 1.5;
    margin-bottom: 15px;
    flex-grow: 1;
}

/* Modern Toggle Switch */
.sa-toggle {
    position: relative;
    width: 46px; height: 24px;
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
.sa-toggle input:checked + .sa-toggle-slider { background: #10b981; }
.sa-toggle input:checked + .sa-toggle-slider:before { transform: translateX(22px); }

/* Save Panel */
.sa-save-panel {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 16px 24px;
    box-shadow: 0 -4px 20px rgba(0,0,0,.02);
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 16px;
}
.btn-save {
    background: linear-gradient(135deg, #10b981, #059669);
    border: none;
    color: #fff;
    padding: 11px 26px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 13.5px;
    box-shadow: 0 4px 14px rgba(16,185,129,.3);
    cursor: pointer;
    transition: all .2s;
}
.btn-save:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(16,185,129,.4);
}

/* Responsiveness overrides */
@media(max-width: 480px) {
    .sa-menu-grid {
        grid-template-columns: 1fr;
    }
    .sa-select-card {
        flex-direction: column;
        align-items: flex-start;
    }
    .sa-select-input {
        width: 100%;
    }
    .sa-save-panel {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
        gap: 12px;
    }
    .btn-save {
        width: 100%;
    }
}

/* Dark Mode overrides */
body.dark-mode .sa-menu-title-area h1,
body.dark-mode .sa-select-label,
body.dark-mode .sa-service-title {
    color: #f8fafc !important;
}
body.dark-mode .sa-select-card,
body.dark-mode .sa-service-card,
body.dark-mode .sa-save-panel {
    background-color: #111827 !important;
    border-color: #1e293b !important;
    color: #cbd5e1 !important;
}
body.dark-mode .sa-service-card.disabled-service {
    background-color: #0f172a !important;
    border-color: #1e293b !important;
}
body.dark-mode .sa-select-input {
    background-color: #1f2937 !important;
    border-color: #374151 !important;
    color: #f8fafc !important;
}
body.dark-mode .sa-select-input:focus {
    border-color: #10b981 !important;
}
body.dark-mode .sa-service-desc {
    color: #94a3b8 !important;
}
body.dark-mode .sa-toggle-slider {
    background-color: #374151 !important;
}
body.dark-mode .sa-service-icon-bg {
    background-color: rgba(16, 185, 129, 0.15) !important;
}
body.dark-mode .sa-service-card.disabled-service .sa-service-icon-bg {
    background-color: #1f2937 !important;
    color: #475569 !important;
}
.sa-submenu-box {
    margin-top: 12px;
    margin-bottom: 12px;
    background: #f8fafc;
    padding: 12px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
}
body.dark-mode .sa-submenu-box {
    background: #1f2937 !important;
    border-color: #374151 !important;
}
body.dark-mode .sa-submenu-box span {
    color: #94a3b8 !important;
}
body.dark-mode .sa-submenu-box input {
    background-color: #111827 !important;
    border-color: #374151 !important;
    color: #f8fafc !important;
}
</style>
@endsection

@section('content')
<div class="sa-menu-wrap">
    
    <!-- Top Header -->
    <div class="sa-menu-header">
        <div class="sa-menu-title-area">
            <div class="sa-menu-icon">
                <i class="fas fa-list"></i>
            </div>
            <div>
                <h1>School Menu & Services Manager</h1>
                <p>Configure which core features and menus are available to teachers, staff, parents and admins in their respective panels.</p>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:12px; border:none; box-shadow:0 4px 15px rgba(16,185,129,0.15); margin-bottom: 24px;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color:inherit;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- School Select Panel -->
    <div class="sa-select-card">
        <div>
            <h2 class="sa-select-label">Select Tenant School Workspace</h2>
            <p style="font-size:12px; color:#64748b; margin:2px 0 0;">Configuration applies instantly to the selected school panel.</p>
        </div>
        <select class="sa-select-input" onchange="window.location.href = '?school_id=' + this.value">
            @foreach($schools as $school)
                <option value="{{ $school->id }}" {{ $school->id == $selectedSchoolId ? 'selected' : '' }}>
                    {{ $school->name }} ({{ $school->code }})
                </option>
            @endforeach
        </select>
    </div>

    @if($selectedSchool)
        <form action="{{ route('superadmin.menu-manager.update') }}" method="POST">
            @csrf
            <input type="hidden" name="school_id" value="{{ $selectedSchool->id }}">

            <!-- Services Grid -->
            <div class="sa-menu-grid">
                @foreach($modules as $key => $mod)
                    @php
                        $isEnabled = !in_array($key, $disabledModules);
                        $customLabel = App\Support\ModuleRegistry::getLabel($key, '');
                    @endphp
                    <div class="sa-service-card {{ $isEnabled ? '' : 'disabled-service' }}" id="card-{{ $key }}">
                        <div style="display: flex; flex-direction: column; height: 100%; justify-content: space-between;">
                            <div>
                                <div class="sa-service-hdr">
                                    <div class="sa-service-info">
                                        <div class="sa-service-icon-bg">
                                            @if(str_contains($mod['icon'], '.png') || str_contains($mod['icon'], '.jpg') || str_contains($mod['icon'], '.jpeg') || str_contains($mod['icon'], '.gif') || str_contains($mod['icon'], '.svg') || str_contains($mod['icon'], '/'))
                                                <img src="{{ $mod['icon'] }}" alt="" style="width: 24px; height: 24px; object-fit: contain; border-radius: 4px;">
                                            @else
                                                <i class="{{ $mod['icon'] }}"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="sa-service-title" style="display: block; font-weight: 800;">{{ $mod['name'] }}</span>
                                            @if($customLabel !== '' && isset($mod['default_title']) && $mod['default_title'] !== $mod['name'])
                                                <small style="font-size: 10px; color: #94a3b8; font-weight: 500; display: block; margin-top: 2px;">Original: {{ $mod['default_title'] }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <label class="sa-toggle">
                                        <input type="checkbox" name="enabled_modules[]" value="{{ $key }}" {{ $isEnabled ? 'checked' : '' }} onchange="toggleCardState('{{ $key }}', this)">
                                        <span class="sa-toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="sa-service-desc" style="margin-bottom: 12px;">
                                    {{ $mod['features'] }}
                                </div>
                                
                                @if(!empty($mod['features_raw']))
                                    <div class="sa-submenu-box">
                                        <label style="font-size: 11.5px; font-weight: 700; color: #475569; display: block; margin-bottom: 8px;"><i class="fas fa-file-lines mr-1" style="color: #6366f1;"></i> Pages / Submenus:</label>
                                        @foreach($mod['features_raw'] as $featKey => $featLabel)
                                            <div style="margin-bottom: 8px;">
                                                <span style="font-size: 10.5px; color: #475569; font-weight: 700; display: block; margin-bottom: 3px;">{{ $featLabel }}</span>
                                                <input type="text" name="feature_names[{{ $key }}][{{ $featKey }}]"
                                                       value="{{ App\Support\ModuleRegistry::getFeatureLabel($key, $featKey, '') }}"
                                                       class="sa-select-input"
                                                       style="font-size: 12px; font-weight: 600; padding: 5px 8px; border-radius: 6px; border: 1.5px solid #cbd5e1; width: 100%; min-width: unset; height: auto;"
                                                       placeholder="Default: {{ $featLabel }}">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            
                            <div style="margin-top: auto; border-top: 1px solid #e2e8f0; padding-top: 12px;">
                                <label style="font-size: 11.5px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Menu Display Name:</label>
                                <input type="text" name="menu_names[{{ $key }}]" 
                                       value="{{ $customLabel }}" 
                                       class="sa-select-input" 
                                       style="font-size: 13px; font-weight: 600; padding: 6px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; width: 100%; min-width: unset; height: auto;" 
                                       placeholder="{{ $mod['default_title'] ?? $mod['name'] }}">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Footer Save Bar -->
            <div class="sa-save-panel">
                <span style="font-size:12.5px; color:#64748b;"><i class="fas fa-info-circle mr-1"></i> Disabling a service hides the menu group in the sidebar and restricts routing access.</span>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save mr-2"></i> Save Configuration
                </button>
            </div>
        </form>
    @else
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:20px; padding:60px 20px; text-align:center; box-shadow: 0 4px 20px rgba(0,0,0,.03);">
            <i class="fas fa-school" style="font-size: 40px; color: #cbd5e1; margin-bottom: 16px;"></i>
            <h3 style="font-size:16px; color:#1e1b4b; font-weight:700;">No School Registered</h3>
            <p style="font-size:13px; color:#64748b; margin-top:4px;">Please create a school first to manage its services.</p>
        </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
function toggleCardState(key, checkbox) {
    const card = document.getElementById('card-' + key);
    if (checkbox.checked) {
        card.classList.remove('disabled-service');
    } else {
        card.classList.add('disabled-service');
    }
}
</script>
@endsection

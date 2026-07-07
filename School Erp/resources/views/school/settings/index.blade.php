@extends('layouts.app')

@section('page-title', 'Settings')

@section('content')

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-gear" style="color:var(--gold);margin-right:8px;"></i>Account Settings</h1>
        <p>Manage your profile, photo and security settings</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:280px 1fr;gap:20px;align-items:start;">

    <!-- LEFT: Profile Card -->
    <div class="card" style="text-align:center;">
        <div class="card-body" style="padding:28px 20px;">
            <div style="position:relative;display:inline-block;margin-bottom:16px;">
                <div id="avatarPreview" style="
                    width:96px;height:96px;border-radius:20px;overflow:hidden;
                    background:linear-gradient(135deg,var(--navy),var(--purple));
                    display:flex;align-items:center;justify-content:center;
                    font-size:32px;font-weight:800;color:#fff;margin:0 auto;
                    border:3px solid var(--gold);
                ">
                    @if(auth()->user()->photo && Storage::disk('public')->exists(auth()->user()->photo))
                        <img src="{{ Storage::disk('public')->url(auth()->user()->photo) }}" style="width:100%;height:100%;object-fit:cover;" id="avatarImg" alt="">
                    @else
                        <span id="avatarInitials">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
                    @endif
                </div>
                <label for="photoInput" style="
                    position:absolute;bottom:-4px;right:-4px;
                    width:28px;height:28px;border-radius:8px;
                    background:var(--gold);color:var(--navy);
                    display:flex;align-items:center;justify-content:center;
                    cursor:pointer;font-size:12px;border:2px solid #fff;
                " title="Change photo">
                    <i class="fas fa-camera"></i>
                </label>
            </div>
            <div style="font-size:16px;font-weight:800;color:var(--t1);margin-bottom:4px;">{{ auth()->user()->name }}</div>
            <div style="font-size:12px;color:var(--t2);">{{ auth()->user()->email }}</div>
            <span class="badge badge-blue" style="margin-top:8px;">
                {{ ucfirst(str_replace('_',' ',auth()->user()->roles->first()?->name ?? 'User')) }}
            </span>
        </div>
    </div>

    <!-- RIGHT: Forms -->
    <div>

        <!-- ── Profile Info ── -->
        <div class="card" style="margin-bottom:18px;">
            <div class="card-hdr">
                <h3><i class="fas fa-user" style="color:var(--gold);margin-right:6px;"></i>Profile Information</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger"><i class="fas fa-circle-xmark"></i> {{ $errors->first() }}</div>
                @endif

                <form action="{{ route('school.settings.profile') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                    @csrf @method('PUT')
                    <input type="file" name="photo" id="photoInput" accept="image/*" style="display:none;" onchange="previewPhoto(this)">

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required placeholder="Your full name">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled style="opacity:.6;cursor:not-allowed;" title="Email cannot be changed here">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Profile Photo</label>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="
                                width:56px;height:56px;border-radius:12px;overflow:hidden;
                                background:linear-gradient(135deg,var(--navy),var(--purple));
                                display:flex;align-items:center;justify-content:center;
                                font-size:18px;font-weight:800;color:#fff;flex-shrink:0;
                            " id="miniAvatar">
                                @if(auth()->user()->photo && Storage::disk('public')->exists(auth()->user()->photo))
                                    <img src="{{ Storage::disk('public')->url(auth()->user()->photo) }}" style="width:100%;height:100%;object-fit:cover;" id="miniAvatarImg" alt="">
                                @else
                                    <span>{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
                                @endif
                            </div>
                            <div style="flex:1;">
                                <label for="photoInput" class="btn btn-outline" style="cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                                    <i class="fas fa-upload"></i> Choose Photo
                                </label>
                                <div style="font-size:11px;color:var(--t3);margin-top:5px;">JPG, PNG or WebP · Max 2 MB</div>
                            </div>
                        </div>
                    </div>

                    <div style="text-align:right;">
                        <button type="submit" class="btn btn-gold"><i class="fas fa-save"></i> Save Profile</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── Change Password ── -->
        <div class="card">
            <div class="card-hdr">
                <h3><i class="fas fa-lock" style="color:var(--gold);margin-right:6px;"></i>Change Password</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('school.settings.password') }}" method="POST">
                    @csrf @method('PUT')

                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <div style="position:relative;">
                            <input type="password" name="current_password" class="form-control" id="curPwd" required placeholder="Enter current password" style="padding-right:40px;">
                            <button type="button" onclick="togglePwd('curPwd',this)" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--t3);cursor:pointer;font-size:13px;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')<div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <div style="position:relative;">
                                <input type="password" name="password" class="form-control" id="newPwd" required placeholder="At least 8 characters" style="padding-right:40px;">
                                <button type="button" onclick="togglePwd('newPwd',this)" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--t3);cursor:pointer;font-size:13px;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirm New Password</label>
                            <div style="position:relative;">
                                <input type="password" name="password_confirmation" class="form-control" id="confPwd" required placeholder="Repeat new password" style="padding-right:40px;">
                                <button type="button" onclick="togglePwd('confPwd',this)" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--t3);cursor:pointer;font-size:13px;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Password strength bar -->
                    <div style="margin-bottom:14px;">
                        <div style="height:4px;border-radius:4px;background:var(--border);overflow:hidden;">
                            <div id="pwdStrengthBar" style="height:100%;width:0;border-radius:4px;transition:.3s;background:var(--red);"></div>
                        </div>
                        <div id="pwdStrengthLabel" style="font-size:11px;color:var(--t3);margin-top:3px;"></div>
                    </div>

                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                        <div style="font-size:12px;color:var(--t2);">
                            <i class="fas fa-shield-halved" style="color:var(--gold);"></i>
                            Use a mix of letters, numbers & symbols
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> Update Password</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── Sidebar Menu Order ── -->
        <div class="card" style="margin-top:18px;">
            <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                <h3><i class="fas fa-arrows-up-down" style="color:var(--gold);margin-right:6px;"></i>Sidebar Menu Order</h3>
                <span style="font-size:12px; color:var(--t2);">Drag items to reorder sidebar links</span>
            </div>
            <div class="card-body">
                <form action="{{ route('school.settings.sidebar-order') }}" method="POST" id="sidebarOrderForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="sidebar_order" id="sidebarOrderInput">
                    
                    <div id="dragDropList" style="display:flex; flex-direction:column; gap:10px; margin-bottom:18px;">
                        @foreach($modules as $key => $module)
                            <div class="draggable-item" draggable="true" data-key="{{ $key }}" style="
                                display:flex; align-items:center; gap:12px; padding:12px 16px;
                                background:var(--page); border:1px solid var(--border); border-radius:12px;
                                cursor:grab; transition:all 0.2s ease; user-select:none;
                            ">
                                <i class="fas fa-bars" style="color:var(--t3); font-size:14px; margin-right:4px; cursor:grab;"></i>
                                <div style="width:32px; height:32px; border-radius:10px; background:rgba(139, 92, 246, 0.1); display:flex; align-items:center; justify-content:center; color:var(--purple); flex-shrink:0;">
                                    @if(str_contains($module['icon'] ?? '', '/'))
                                        <img src="{{ $module['icon'] }}" style="width:18px; height:18px; object-fit:contain;">
                                    @else
                                        <i class="{{ $module['icon'] ?: 'fas fa-cube' }}" style="font-size:14px;"></i>
                                    @endif
                                </div>
                                <div style="flex:1; display:flex; flex-direction:column;">
                                    <span style="font-weight:700; color:var(--t1); font-size:13.5px;">{{ $module['label'] }}</span>
                                    @if(!empty($module['features']))
                                        <span style="font-size:11px; color:var(--t2);">
                                            Contains: {{ implode(', ', array_slice(array_values($module['features']), 0, 4)) }}@if(count($module['features']) > 4)...@endif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div style="text-align:right;">
                        <button type="submit" class="btn btn-gold" id="saveOrderBtn"><i class="fas fa-save"></i> Save Sidebar Order</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const src = e.target.result;
            // Update big avatar
            const bigAvatar = document.getElementById('avatarPreview');
            bigAvatar.innerHTML = '<img src="'+src+'" style="width:100%;height:100%;object-fit:cover;">';
            // Update mini
            const miniAvatar = document.getElementById('miniAvatar');
            miniAvatar.innerHTML = '<img src="'+src+'" style="width:100%;height:100%;object-fit:cover;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function togglePwd(id, btn) {
    const inp = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        inp.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

document.getElementById('newPwd').addEventListener('input', function() {
    const val = this.value;
    let score = 0;
    if (val.length >= 8)  score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const bar = document.getElementById('pwdStrengthBar');
    const lbl = document.getElementById('pwdStrengthLabel');
    const levels = ['','Weak','Fair','Good','Strong'];
    const colors = ['','#ef4444','#f59e0b','#3b82f6','#10b981'];
    bar.style.width = (score * 25) + '%';
    bar.style.background = colors[score] || colors[0];
    lbl.textContent = levels[score] || '';
    lbl.style.color  = colors[score] || '';
});

// Drag and drop sorting logic
const list = document.getElementById('dragDropList');
let draggingItem = null;

list.addEventListener('dragstart', (e) => {
    draggingItem = e.target.closest('.draggable-item');
    if (draggingItem) {
        draggingItem.classList.add('dragging');
    }
});

list.addEventListener('dragend', (e) => {
    if (draggingItem) {
        draggingItem.classList.remove('dragging');
        draggingItem = null;
    }
});

list.addEventListener('dragover', (e) => {
    e.preventDefault();
    const afterElement = getDragAfterElement(list, e.clientY);
    const dragging = document.querySelector('.dragging');
    if (dragging) {
        if (afterElement == null) {
            list.appendChild(dragging);
        } else {
            list.insertBefore(dragging, afterElement);
        }
    }
});

function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.draggable-item:not(.dragging)')];
    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}

document.getElementById('sidebarOrderForm').addEventListener('submit', function(e) {
    const items = [...document.querySelectorAll('#dragDropList .draggable-item')];
    const order = items.map(item => item.getAttribute('data-key'));
    document.getElementById('sidebarOrderInput').value = JSON.stringify(order);
});
</script>

<style>
.draggable-item {
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease, background-color 0.15s ease;
}
.draggable-item:hover {
    border-color: var(--purple) !important;
    background-color: rgba(139, 92, 246, 0.03) !important;
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.08);
}
.draggable-item.dragging {
    opacity: 0.5;
    border: 2px dashed var(--purple) !important;
    background-color: rgba(139, 92, 246, 0.05) !important;
    transform: scale(0.98);
}
</style>
@endsection

@extends('superadmin.layouts.master')

@section('styles')
<style>
    .profile-card {
        border-radius: 16px !important;
        border: 1px solid rgba(229, 231, 235, 0.5) !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.01) !important;
        background-color: #ffffff;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .profile-avatar-container {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto 1.5rem auto;
    }

    .profile-avatar-preview {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #ffffff;
        box-shadow: 0 4px 14px rgba(0,0,0,0.1);
    }

    .avatar-upload-overlay {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e5ba73, #c59b27);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0c1024;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        transition: transform 0.2s;
    }

    .avatar-upload-overlay:hover {
        transform: scale(1.1);
    }

    .profile-input-file {
        display: none;
    }

    .section-title-custom {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0c1024;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 8px;
    }

    .form-group-custom label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .form-control-custom {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 10px 14px;
        font-size: 0.9rem;
        color: #1e1b4b;
        transition: border-color 0.2s;
    }

    .form-control-custom:focus {
        border-color: #e5ba73;
        box-shadow: 0 0 0 3px rgba(229, 186, 115, 0.15);
        outline: none;
    }

    .btn-save-profile {
        background: linear-gradient(135deg, #1d193d, #2f2960);
        color: #ffffff !important;
        border: none;
        font-weight: 700;
        border-radius: 12px;
        padding: 10px 24px;
        font-size: 0.9rem;
        box-shadow: 0 4px 12px rgba(29, 25, 61, 0.15);
        transition: all 0.2s;
    }

    .btn-save-profile:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(29, 25, 61, 0.25);
    }

    /* Dark Mode rules */
    body.dark-mode .profile-card {
        background-color: #111827 !important;
        border-color: #1e293b !important;
    }
    body.dark-mode .section-title-custom {
        color: #f8fafc !important;
        border-color: #1e293b !important;
    }
    body.dark-mode .form-group-custom label {
        color: #cbd5e1 !important;
    }
    body.dark-mode .form-control-custom {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .form-control-custom:focus {
        border-color: #e5ba73 !important;
        box-shadow: 0 0 0 3px rgba(229, 186, 115, 0.15) !important;
    }
    body.dark-mode h2, body.dark-mode h4, body.dark-mode p {
        color: #f8fafc !important;
    }
    body.dark-mode .text-muted {
        color: #94a3b8 !important;
    }
</style>
@endsection

@section('content')
<div class="row pt-4">
    <div class="col-12">
        <h2 class="mb-1 font-weight-extrabold" style="font-size: 1.8rem; letter-spacing: -0.5px;">SuperAdmin Profile</h2>
        <p class="text-muted" style="font-size: 0.88rem;">Manage your credentials, details, and access password.</p>
    </div>
</div>

<div class="row mt-3">
    <!-- Left Column: Avatar and Info Summary -->
    <div class="col-lg-4 mb-4">
        <div class="profile-card text-center">
            <form action="{{ route('superadmin.profile.update') }}" method="POST" enctype="multipart/form-data" id="avatarForm">
                @csrf
                <div class="profile-avatar-container">
                    <img src="{{ $user->photo ? asset($user->photo) : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80' }}" class="profile-avatar-preview" id="avatarPreview" alt="Avatar">
                    <label for="photoInput" class="avatar-upload-overlay">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" name="photo" id="photoInput" class="profile-input-file" accept="image/*" onchange="previewImage(this)">
                </div>
                <h4 class="font-weight-extrabold mb-1">{{ $user->name }}</h4>
                <p class="text-muted font-weight-bold mb-3" style="font-size: 0.8rem; text-transform: uppercase;">{{ $user->role }}</p>
                <div class="badge badge-success px-3 py-2" style="border-radius: 20px; font-size: 0.72rem;">Account Active</div>
            </form>
        </div>
    </div>

    <!-- Right Column: Profile details and Password -->
    <div class="col-lg-8 mb-4">
        <!-- Card 1: Details -->
        <div class="profile-card">
            <h5 class="section-title-custom"><i class="fas fa-user-edit mr-2 text-primary"></i> Basic Information</h5>
            
            @if ($errors->any())
                <div class="alert alert-danger border-0 mb-4" style="border-radius: 10px;">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('superadmin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 form-group form-group-custom">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control form-control-custom" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-md-6 form-group form-group-custom">
                        <label for="email">Email address</label>
                        <input type="email" name="email" id="email" class="form-control form-control-custom" value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>
                
                <div class="row mt-2">
                    <div class="col-md-6 form-group form-group-custom">
                        <label for="phone">Phone Number</label>
                        <input type="text" name="phone" id="phone" class="form-control form-control-custom" value="{{ old('phone', $user->phone) }}">
                    </div>
                </div>

                <div class="text-left mt-4">
                    <button type="submit" class="btn btn-save-profile">
                        <i class="fas fa-check mr-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Card 2: Password Security -->
        <div class="profile-card">
            <h5 class="section-title-custom"><i class="fas fa-lock mr-2 text-danger"></i> Update Password</h5>
            
            <form action="{{ route('superadmin.profile.password') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 form-group form-group-custom">
                        <label for="current_password">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="form-control form-control-custom" required>
                    </div>
                </div>
                
                <div class="row mt-2">
                    <div class="col-md-6 form-group form-group-custom">
                        <label for="password">New Password</label>
                        <input type="password" name="password" id="password" class="form-control form-control-custom" required>
                    </div>
                    <div class="col-md-6 form-group form-group-custom">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-custom" required>
                    </div>
                </div>

                <div class="text-left mt-4">
                    <button type="submit" class="btn btn-save-profile" style="background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 4px 12px rgba(239,68,68,0.15);">
                        <i class="fas fa-key mr-1"></i> Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
            
            // Auto submit avatar form when photo is chosen
            document.getElementById('avatarForm').submit();
        }
    }
</script>
@endsection

@extends('layouts.auth')

@section('content')
<div class="right-panel" style="flex: 1; width: 100vw; height: 100vh;">
    <div class="glass-card" style="text-align: center; max-width: 500px;">
        <div class="card-header" style="margin-bottom: 1.5rem;">
            <div style="width: 70px; height: 70px; border-radius: 50%; background-color: rgba(239, 68, 68, 0.1); display: flex; align-items: center; justify-content: center; color: var(--danger); font-size: 2rem; margin: 0 auto 1.5rem;">
                <i class="fa fa-exclamation-triangle"></i>
            </div>
            <h2 class="card-title" style="font-family: 'Syne', sans-serif;">Subscription Suspended</h2>
            <p class="card-subtitle" style="margin-top: 0.5rem;">The ERP access for your tenant school has expired or has been suspended.</p>
        </div>

        <div style="background-color: rgba(15, 23, 42, 0.6); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid var(--border);">
            <div style="font-size: 0.9rem; color: var(--text-muted); text-align: left; margin-bottom: 0.5rem;">
                <strong>School:</strong> {{ app('currentSchool')?->name ?? 'Your School' }}
            </div>
            <div style="font-size: 0.9rem; color: var(--text-muted); text-align: left;">
                <strong>Status:</strong> <span style="color: var(--danger); font-weight: 700;">Suspended / Trial Expired</span>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <a href="mailto:support@schoolcloud.com?subject=Subscription%20Suspended" class="btn-primary" style="text-decoration: none;">
                <i class="fa fa-envelope"></i> Contact Support
            </a>
            
            <a href="{{ route('logout') }}" style="color: var(--text-muted); font-size: 0.9rem; text-decoration: none; font-weight: 600; padding: 0.5rem;">
                <i class="fa fa-sign-out-alt"></i> Logout from Portal
            </a>
        </div>
    </div>
</div>
@endsection

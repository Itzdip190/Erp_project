@extends('layouts.auth')

@section('content')
<div class="right-panel" style="flex: 1; width: 100vw; height: 100vh;">
    <div class="glass-card" style="text-align: center; max-width: 500px;">
        <div class="card-header" style="margin-bottom: 2rem;">
            <!-- Beautiful custom SVG maintenance icon -->
            <div style="width: 80px; height: 80px; border-radius: 50%; background-color: rgba(245, 158, 11, 0.1); display: flex; align-items: center; justify-content: center; color: #F59E0B; margin: 0 auto 1.5rem; border: 1px solid rgba(245, 158, 11, 0.2);">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <h2 class="card-title" style="font-family: 'Syne', sans-serif; font-size: 2rem; color: var(--text-main);">Under Maintenance</h2>
            <p class="card-subtitle" style="margin-top: 0.5rem; color: var(--text-muted); line-height: 1.5;">We are performing system updates to keep your school portal secure and fast. Please try again in a few moments.</p>
        </div>

        <div style="background-color: rgba(15, 23, 42, 0.6); padding: 1.5rem; border-radius: 16px; margin-bottom: 2.5rem; border: 1px solid var(--border); text-align: left;">
            <div style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.6;">
                <strong>Service Status:</strong> <span style="color: #F59E0B; font-weight: 700;">Temporarily Offline</span><br>
                <strong>Estimate:</strong> <span style="color: var(--text-main);">Will be back online shortly</span>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <button onclick="window.location.reload();" class="btn-primary">
                Refresh Page
            </button>
        </div>
    </div>
</div>
@endsection

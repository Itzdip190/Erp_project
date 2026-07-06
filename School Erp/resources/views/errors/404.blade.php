@extends('layouts.auth')

@section('content')
<div class="right-panel" style="flex: 1; width: 100vw; height: 100vh;">
    <div class="glass-card" style="text-align: center; max-width: 500px;">
        <div class="card-header" style="margin-bottom: 2rem;">
            <!-- Beautiful animated custom SVG icon -->
            <div style="width: 80px; height: 80px; border-radius: 50%; background-color: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; color: var(--accent); margin: 0 auto 1.5rem; border: 1px solid rgba(59, 130, 246, 0.2);">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="card-title" style="font-family: 'Syne', sans-serif; font-size: 2rem; color: var(--text-main);">Page Not Found</h2>
            <p class="card-subtitle" style="margin-top: 0.5rem; color: var(--text-muted); line-height: 1.5;">The link you followed may be broken, or the page has been moved or deleted.</p>
        </div>

        <div style="background-color: rgba(15, 23, 42, 0.6); padding: 1.5rem; border-radius: 16px; margin-bottom: 2.5rem; border: 1px solid var(--border); text-align: left;">
            <div style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.6;">
                <strong>Error Code:</strong> <span style="color: var(--accent); font-weight: 700;">404</span><br>
                <strong>Requested URL:</strong> <span style="font-family: monospace; word-break: break-all; color: var(--text-main);">{{ request()->url() }}</span>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <a href="{{ auth()->check() ? (auth()->user()->hasRole('superadmin') ? '/superadmin/dashboard' : '/school/dashboard') : '/login' }}" class="btn-primary" style="text-decoration: none;">
                Return to Safety
            </a>
        </div>
    </div>
</div>
@endsection

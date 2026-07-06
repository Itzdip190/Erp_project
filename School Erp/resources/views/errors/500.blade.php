@extends('layouts.auth')

@section('content')
<div class="right-panel" style="flex: 1; width: 100vw; height: 100vh;">
    <div class="glass-card" style="text-align: center; max-width: 500px;">
        <div class="card-header" style="margin-bottom: 2rem;">
            <!-- Beautiful custom SVG warning icon -->
            <div style="width: 80px; height: 80px; border-radius: 50%; background-color: rgba(239, 68, 68, 0.1); display: flex; align-items: center; justify-content: center; color: #EF4444; margin: 0 auto 1.5rem; border: 1px solid rgba(239, 68, 68, 0.2);">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h2 class="card-title" style="font-family: 'Syne', sans-serif; font-size: 2rem; color: var(--text-main);">Server Error</h2>
            <p class="card-subtitle" style="margin-top: 0.5rem; color: var(--text-muted); line-height: 1.5;">An unexpected error occurred. The system administrators have been notified.</p>
        </div>

        <div style="background-color: rgba(15, 23, 42, 0.6); padding: 1.5rem; border-radius: 16px; margin-bottom: 2.5rem; border: 1px solid var(--border); text-align: left;">
            <div style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.6;">
                <strong>Error Code:</strong> <span style="color: #EF4444; font-weight: 700;">500</span><br>
                <strong>Details:</strong> <span style="color: var(--text-main);">Internal Server Error</span>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <a href="/" class="btn-primary" style="text-decoration: none;">
                Return to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection

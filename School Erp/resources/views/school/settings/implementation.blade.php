@extends('layouts.app')

@section('title', 'Implementation Process')
@section('page-title', 'Implementation Process')

@section('content')
<div class="card" style="max-width:800px; margin:0 auto;">
    <div class="card-hdr">
        <h3>ERP Setup & Implementation Wizard</h3>
    </div>
    <div class="card-body">
        <p style="font-size:13px; color:var(--t2); margin-bottom:24px;">
            Follow this timeline to configure and deploy the SchoolCloud ERP platform at your school. Steps turn green dynamically once data is detected in the system.
        </p>

        <div style="position:relative; padding-left:36px; display:flex; flex-direction:column; gap:28px;">
            <!-- vertical line background -->
            <div style="position:absolute; left:14px; top:10px; bottom:10px; width:2px; background:#e2e8f0; z-index:1;"></div>

            @foreach($steps as $idx => $step)
            <div style="position:relative; display:flex; gap:16px; align-items:flex-start; z-index:2;">
                <!-- step number circle -->
                <div style="width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700;
                    background:{{ $step['done'] ? '#10b981' : '#f59e0b' }}; color:#fff; flex-shrink:0;">
                    @if($step['done'])
                        <i class="fas fa-check"></i>
                    @else
                        {{ $idx + 1 }}
                    @endif
                </div>

                <!-- step details -->
                <div style="background:#fff; border:1px solid {{ $step['done'] ? '#a7f3d0' : '#fef3c7' }}; border-radius:10px; padding:16px; flex:1; box-shadow:var(--shadow);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                        <h4 style="font-size:13.5px; font-weight:700; color:var(--t1);">{{ $step['title'] }}</h4>
                        <span class="badge {{ $step['done'] ? 'badge-success' : 'badge-warning' }}" style="font-size:10px;">
                            {{ $step['done'] ? 'Configured' : 'Pending' }}
                        </span>
                    </div>
                    <p style="font-size:11.5px; color:var(--t2); margin-bottom:8px;">{{ $step['desc'] }}</p>
                    <span style="font-size:10.5px; font-weight:600; color:{{ $step['done'] ? '#047857' : '#b45309' }}; background:{{ $step['done'] ? 'rgba(16,185,129,0.1)' : 'rgba(245,158,11,0.1)' }}; padding:2px 8px; border-radius:4px;">
                        {{ $step['val'] }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

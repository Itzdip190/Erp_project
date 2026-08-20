@extends('layouts.app')

@section('title', $title . ' - ' . $moduleName)

@section('content')
<div class="container-fluid py-4">
    <!-- Header Banner -->
    <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 16px; padding: 30px; color: #ffffff; margin-bottom: 24px; position: relative; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
        <div style="position: absolute; right: -30px; top: -30px; width: 180px; height: 180px; background: rgba(59, 130, 246, 0.15); border-radius: 50%; pointer-events: none;"></div>
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; position: relative; z-index: 2;">
            <div>
                <div style="display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                    <i class="fas fa-layer-group" style="color: #60a5fa;"></i>
                    <span>{{ $moduleName }}</span>
                    <span>/</span>
                    <span style="color: #60a5fa;">{{ $title }}</span>
                </div>
                <h2 style="font-size: 26px; font-weight: 800; margin: 0; color: #ffffff; letter-spacing: -0.5px;">{{ $title }}</h2>
            </div>
            <div>
                <span style="display: inline-flex; align-items: center; gap: 8px; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(96, 165, 250, 0.3); color: #93c5fd; font-size: 13px; font-weight: 600; padding: 8px 16px; border-radius: 30px;">
                    <i class="fas fa-clock"></i> Upcoming Module
                </span>
            </div>
        </div>
    </div>

    <!-- Main Placeholder Container -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 48px 32px; text-align: center; box-shadow: 0 4px 16px rgba(0,0,0,0.03);">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #eff6ff, #dbeafe); border-radius: 24px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px; color: #2563eb; font-size: 32px; box-shadow: 0 8px 20px rgba(37,99,235,0.12);">
            <i class="fas fa-box-open"></i>
        </div>
        <h3 style="font-size: 22px; font-weight: 700; color: #1e293b; margin-bottom: 10px;">{{ $moduleName }} - {{ $title }}</h3>
        <p style="font-size: 16px; font-weight: 600; color: #475569; max-width: 580px; margin: 0 auto 24px auto; line-height: 1.6;">
            New modules yet to come, please wait for update.
        </p>

        <div style="display: inline-flex; align-items: center; gap: 12px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 14px 20px; color: #475569; font-size: 13.5px; font-weight: 600;">
            <i class="fas fa-info-circle" style="color: #3b82f6; font-size: 16px;"></i>
            <span>Module Integration Active & Available Soon</span>
        </div>
    </div>
</div>
@endsection


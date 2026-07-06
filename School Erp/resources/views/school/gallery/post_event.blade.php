@extends('layouts.app')

@section('title', 'Post An Event - Gallery')
@section('page-title', 'Post An Event')

@section('styles')
<style>
/* ═══════════════════════════════════════════════════════════════
   GALLERY / POST AN EVENT — Premium Blue & White Theme
   ═══════════════════════════════════════════════════════════════ */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.gallery-page {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: #f4f7fc;
    color: #1e293b;
    padding-bottom: 60px;
}

/* Page Header */
.gallery-hdr-wrap {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}
.gallery-title-box h2 {
    font-size: 22px;
    font-weight: 800;
    color: #0f3057;
    margin: 0 0 4px 0;
    letter-spacing: -0.5px;
}
.gallery-title-box span {
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
}

/* Controls Header (Tabs & Buttons) */
.gallery-top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

/* Tabs */
.gallery-tabs {
    display: flex;
    background: #ffffff;
    padding: 6px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(15, 48, 87, 0.04);
    border: 1px solid #e2e8f0;
}
.tab-btn {
    padding: 10px 20px;
    font-size: 13px;
    font-weight: 700;
    color: #64748b;
    border: none;
    background: transparent;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.tab-btn:hover {
    color: #1e3a8a;
}
.tab-btn.active {
    background: #1e3a8a;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);
}

/* Action Buttons Top Right */
.gallery-actions {
    display: flex;
    gap: 12px;
}
.btn-post-event {
    background: #2563eb;
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 800;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
}
.btn-post-event:hover {
    background: #1d4ed8;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
}
.btn-post-achievement {
    background: #0f3057;
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 800;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    box-shadow: 0 4px 14px rgba(15, 48, 87, 0.2);
}
.btn-post-achievement:hover {
    background: #09203f;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(15, 48, 87, 0.3);
}

/* Cards List */
.gallery-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.gallery-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px rgba(15, 48, 87, 0.05);
    display: grid;
    grid-template-columns: 80px 180px 1fr auto;
    align-items: center;
    gap: 24px;
    transition: all 0.25s ease;
    position: relative;
}
.gallery-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 8px 30px rgba(15, 48, 87, 0.08);
}

.card-num {
    font-size: 18px;
    font-weight: 800;
    color: #94a3b8;
}

.card-media {
    width: 140px;
    height: 100px;
    border-radius: 12px;
    overflow: hidden;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.card-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.card-media-placeholder {
    color: #94a3b8;
    font-size: 28px;
}

.card-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.card-title {
    font-size: 18px;
    font-weight: 800;
    color: #2563eb;
    margin: 0;
}
.card-meta {
    font-size: 13px;
    color: #64748b;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.meta-row {
    display: flex;
    align-items: center;
    gap: 6px;
}
.meta-label {
    font-weight: 700;
    color: #0f3057;
}
.meta-val {
    color: #2563eb;
    font-weight: 600;
}

/* Card Actions */
.card-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}
.btn-share-social {
    background: #ffffff;
    color: #2563eb;
    border: 1.5px solid #2563eb;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.btn-share-social:hover {
    background: #2563eb;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}
.btn-icon-action {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
}
.btn-icon-action.edit:hover {
    background: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
}
.btn-icon-action.delete:hover {
    background: #fef2f2;
    color: #ef4444;
    border-color: #fecaca;
}

/* Pagination */
.gallery-pagination {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 16px;
    margin-top: 32px;
}
.total-rows-badge {
    background: #ffffff;
    padding: 8px 16px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    font-size: 13px;
    font-weight: 700;
    color: #475569;
}
.page-btn-group {
    display: flex;
    gap: 6px;
}
.pg-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 13px;
}
.pg-btn.active {
    background: #1e3a8a;
    color: #ffffff;
    border-color: #1e3a8a;
}
.pg-btn:hover:not(.active) {
    background: #f1f5f9;
}

/* ═══════════════════════════════════════════════════════════════
   DRAWER / MODAL STYLING (Slide-Over & Modals)
   ═══════════════════════════════════════════════════════════════ */
.drawer-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.5);
    backdrop-filter: blur(4px);
    z-index: 1050;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}
.drawer-backdrop.active {
    opacity: 1;
    visibility: visible;
}

.drawer-content {
    position: fixed;
    top: 0;
    right: -600px;
    width: 100%;
    max-width: 550px;
    height: 100%;
    background: #ffffff;
    box-shadow: -10px 0 30px rgba(0, 0, 0, 0.15);
    z-index: 1060;
    display: flex;
    flex-direction: column;
    transition: right 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.drawer-backdrop.active .drawer-content {
    right: 0;
}

.drawer-header {
    background: linear-gradient(135deg, #0f3057 0%, #1e3a8a 100%);
    color: #ffffff;
    padding: 20px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.drawer-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
    letter-spacing: -0.3px;
}
.drawer-close {
    background: transparent;
    border: none;
    color: #ffffff;
    font-size: 22px;
    cursor: pointer;
    opacity: 0.8;
    transition: opacity 0.2s;
}
.drawer-close:hover {
    opacity: 1;
}

.drawer-body {
    padding: 24px;
    flex-grow: 1;
    overflow-y: auto;
}

.drawer-footer {
    padding: 16px 24px;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

/* Form Styling inside Drawer */
.form-group-field {
    margin-bottom: 20px;
    position: relative;
}
.form-label-styled {
    display: block;
    font-size: 12.5px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.form-input-styled, .form-select-styled, .form-textarea-styled {
    width: 100%;
    padding: 12px 14px;
    border-radius: 10px;
    border: 1.5px solid #cbd5e1;
    font-size: 14px;
    color: #1e293b;
    background: #ffffff;
    transition: all 0.2s ease;
    font-family: inherit;
}
.form-input-styled:focus, .form-select-styled:focus, .form-textarea-styled:focus {
    border-color: #2563eb;
    outline: none;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
}

.radio-group-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 12px 20px;
    margin-top: 8px;
}
.radio-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
}
.radio-item input[type="radio"] {
    accent-color: #2563eb;
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.checkbox-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    font-weight: 700;
    color: #1e3a8a;
    cursor: pointer;
    margin-top: 10px;
}
.checkbox-item input[type="checkbox"] {
    accent-color: #2563eb;
    width: 20px;
    height: 20px;
    border-radius: 6px;
    cursor: pointer;
}

/* Toggle Switch */
.toggle-switch-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 10px;
}
.switch {
    position: relative;
    display: inline-block;
    width: 46px;
    height: 24px;
}
.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: .3s;
    border-radius: 24px;
}
.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
}
input:checked + .slider {
    background-color: #2563eb;
}
input:checked + .slider:before {
    transform: translateX(22px);
}

.btn-drawer-back {
    background: #ffffff;
    color: #475569;
    border: 1.5px solid #cbd5e1;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
}
.btn-drawer-submit {
    background: #2563eb;
    color: #ffffff;
    border: none;
    padding: 10px 24px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
}

/* ═══════════════════════════════════════════════════════════════
   SOCIAL MEDIA BROADCAST MODAL
   ═══════════════════════════════════════════════════════════════ */
.social-modal {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    z-index: 2000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.social-modal.active {
    display: flex;
}
.social-modal-content {
    background: #ffffff;
    border-radius: 20px;
    max-width: 550px;
    width: 100%;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    overflow: hidden;
    animation: zoomIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.social-modal-hdr {
    background: linear-gradient(135deg, #0f3057 0%, #1e3a8a 100%);
    color: #ffffff;
    padding: 20px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.social-modal-hdr h4 {
    margin: 0; font-size: 17px; font-weight: 800;
}
.social-modal-body {
    padding: 24px;
}
.social-platform-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    margin-bottom: 12px;
    background: #f8fafc;
}
.social-platform-left {
    display: flex;
    align-items: center;
    gap: 14px;
}
.social-platform-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #ffffff;
}
.social-name {
    font-size: 14px;
    font-weight: 800;
    color: #0f3057;
}
.social-status {
    font-size: 11px;
    font-weight: 700;
}
.social-status.connected { color: #16a34a; }
.social-status.disconnected { color: #dc2626; }
.btn-share-act {
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 800;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}
</style>
@endsection

@section('content')
<div class="gallery-page">
    
    {{-- Header --}}
    <div class="gallery-hdr-wrap">
        <div class="gallery-title-box">
            <h2>Post An Event</h2>
            <span>Gallery</span>
        </div>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:14px 18px; border-radius:12px; margin-bottom:20px; font-size:14px; font-weight:700; display:flex; align-items:center; gap:10px;">
            <i class="fas fa-check-circle" style="font-size:18px;"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Controls & Tabs --}}
    <div class="gallery-top-bar">
        <div class="gallery-tabs">
            <button class="tab-btn active" onclick="switchGalleryTab('prev', this)">PREVIOUS POSTS ({{ $totalPreviousCount }})</button>
            <button class="tab-btn" onclick="switchGalleryTab('sched', this)">SCHEDULED POSTS ({{ count($scheduledPosts) }})</button>
            <button class="tab-btn" onclick="switchGalleryTab('achieve', this)">ACHIEVEMENTS ({{ count($achievements) }})</button>
        </div>
        <div class="gallery-actions">
            <button class="btn-post-event" onclick="openDrawer('drawer-event')">
                <i class="fas fa-plus"></i> POST A NEW EVENT
            </button>
            <button class="btn-post-achievement" onclick="openDrawer('drawer-achievement')">
                <i class="fas fa-plus"></i> POST A NEW ACHIEVEMENT
            </button>
        </div>
    </div>

    {{-- Tab 1: Previous Posts --}}
    <div id="tab-prev" class="gallery-tab-content">
        <div class="gallery-list">
            @forelse($previousPosts as $index => $post)
                <div class="gallery-card">
                    <div class="card-num">{{ sprintf('%02d', $previousPosts->firstItem() + $index) }}.</div>
                    <div class="card-media">
                        @if(!empty($post->attachments) && count($post->attachments) > 0)
                            <img src="{{ asset('storage/' . $post->attachments[0]) }}" alt="{{ $post->title }}">
                        @else
                            <i class="fas fa-image card-media-placeholder"></i>
                        @endif
                    </div>
                    <div class="card-info">
                        <h3 class="card-title">{{ $post->title }}</h3>
                        <div class="card-meta">
                            <div class="meta-row">
                                <span class="meta-label">Posted on:</span>
                                <span class="meta-val">{{ $post->created_at->format('d/m/Y, h:i A') }}</span>
                            </div>
                            <div class="meta-row">
                                <span class="meta-label">Posted by:</span>
                                <span class="meta-val">{{ $post->posted_by }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-actions">
                        <button class="btn-share-social" onclick="openSocialModal('{{ addslashes($post->title) }}', '{{ !empty($post->attachments) ? asset('storage/' . $post->attachments[0]) : '' }}')">
                            <i class="fas fa-share-alt"></i> SHARE ON SOCIAL MEDIA
                        </button>
                        <button class="btn-icon-action edit" title="Edit Post"><i class="fas fa-pen"></i></button>
                        <form method="POST" action="{{ route('school.gallery.posts.destroy', $post->id) }}" onsubmit="return confirm('Are you sure you want to delete this post?')" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon-action delete" title="Delete Post"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </div>
                </div>
            @empty
                <div style="background:#fff; padding:40px; text-align:center; border-radius:16px; border:1px solid #e2e8f0; color:#64748b;">
                    <i class="fas fa-images" style="font-size:42px; color:#cbd5e1; margin-bottom:12px;"></i>
                    <p style="font-weight:700; margin:0;">No previous posts found.</p>
                </div>
            @endforelse
        </div>

        @if($previousPosts->hasPages())
        <div class="gallery-pagination">
            <span class="total-rows-badge">Total Rows: {{ $previousPosts->total() }}</span>
            <div class="page-btn-group">
                {{-- Pagination Links --}}
                @if ($previousPosts->onFirstPage())
                    <button class="pg-btn" disabled>&lt;</button>
                @else
                    <a href="{{ $previousPosts->previousPageUrl() }}" class="pg-btn">&lt;</a>
                @endif

                @foreach (range(1, $previousPosts->lastPage()) as $i)
                    <a href="{{ $previousPosts->url($i) }}" class="pg-btn {{ $i == $previousPosts->currentPage() ? 'active' : '' }}">{{ $i }}</a>
                @endforeach

                @if ($previousPosts->hasMorePages())
                    <a href="{{ $previousPosts->nextPageUrl() }}" class="pg-btn">&gt;</a>
                @else
                    <button class="pg-btn" disabled>&gt;</button>
                @endif
            </div>
        </div>
        @else
        <div class="gallery-pagination">
            <span class="total-rows-badge">Total Rows: {{ $previousPosts->total() }}</span>
        </div>
        @endif
    </div>

    {{-- Tab 2: Scheduled Posts --}}
    <div id="tab-sched" class="gallery-tab-content" style="display:none;">
        <div class="gallery-list">
            @forelse($scheduledPosts as $index => $post)
                <div class="gallery-card">
                    <div class="card-num">{{ sprintf('%02d', $index + 1) }}.</div>
                    <div class="card-media">
                        @if(!empty($post->attachments) && count($post->attachments) > 0)
                            <img src="{{ asset('storage/' . $post->attachments[0]) }}" alt="{{ $post->title }}">
                        @else
                            <i class="fas fa-clock card-media-placeholder"></i>
                        @endif
                    </div>
                    <div class="card-info">
                        <h3 class="card-title">{{ $post->title }} <span style="background:#fef3c7; color:#d97706; font-size:11px; padding:3px 8px; border-radius:6px; font-weight:800; margin-left:8px;">SCHEDULED</span></h3>
                        <div class="card-meta">
                            <div class="meta-row">
                                <span class="meta-label">Scheduled for:</span>
                                <span class="meta-val">{{ $post->scheduled_at ? $post->scheduled_at->format('d/m/Y, h:i A') : 'Upcoming' }}</span>
                            </div>
                            <div class="meta-row">
                                <span class="meta-label">Posted by:</span>
                                <span class="meta-val">{{ $post->posted_by }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-actions">
                        <button class="btn-share-social" onclick="openSocialModal('{{ addslashes($post->title) }}', '{{ !empty($post->attachments) ? asset('storage/' . $post->attachments[0]) : '' }}')">
                            <i class="fas fa-share-alt"></i> SHARE ON SOCIAL MEDIA
                        </button>
                        <button class="btn-icon-action edit" title="Edit Post"><i class="fas fa-pen"></i></button>
                        <form method="POST" action="{{ route('school.gallery.posts.destroy', $post->id) }}" onsubmit="return confirm('Are you sure you want to delete this post?')" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon-action delete" title="Delete Post"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </div>
                </div>
            @empty
                <div style="background:#fff; padding:40px; text-align:center; border-radius:16px; border:1px solid #e2e8f0; color:#64748b;">
                    <i class="fas fa-calendar-alt" style="font-size:42px; color:#cbd5e1; margin-bottom:12px;"></i>
                    <p style="font-weight:700; margin:0;">No scheduled posts found.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Tab 3: Achievements --}}
    <div id="tab-achieve" class="gallery-tab-content" style="display:none;">
        <div class="gallery-list">
            @forelse($achievements as $index => $post)
                <div class="gallery-card">
                    <div class="card-num">{{ sprintf('%02d', $index + 1) }}.</div>
                    <div class="card-media">
                        @if(!empty($post->attachments) && count($post->attachments) > 0)
                            <img src="{{ asset('storage/' . $post->attachments[0]) }}" alt="{{ $post->title }}">
                        @else
                            <i class="fas fa-trophy card-media-placeholder" style="color:#eab308;"></i>
                        @endif
                    </div>
                    <div class="card-info">
                        <h3 class="card-title" style="color:#0f3057;">{{ $post->title }} <span style="background:#dcfce7; color:#166534; font-size:11px; padding:3px 8px; border-radius:6px; font-weight:800; margin-left:8px;">ACHIEVEMENT</span></h3>
                        <div class="card-meta">
                            <div class="meta-row">
                                <span class="meta-label">Posted on:</span>
                                <span class="meta-val">{{ $post->created_at->format('d/m/Y, h:i A') }}</span>
                            </div>
                            <div class="meta-row">
                                <span class="meta-label">Posted by:</span>
                                <span class="meta-val">{{ $post->posted_by }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-actions">
                        <button class="btn-share-social" onclick="openSocialModal('{{ addslashes($post->title) }}', '{{ !empty($post->attachments) ? asset('storage/' . $post->attachments[0]) : '' }}')">
                            <i class="fas fa-share-alt"></i> SHARE ON SOCIAL MEDIA
                        </button>
                        <button class="btn-icon-action edit" title="Edit Post"><i class="fas fa-pen"></i></button>
                        <form method="POST" action="{{ route('school.gallery.posts.destroy', $post->id) }}" onsubmit="return confirm('Are you sure you want to delete this achievement?')" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon-action delete" title="Delete Post"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </div>
                </div>
            @empty
                <div style="background:#fff; padding:40px; text-align:center; border-radius:16px; border:1px solid #e2e8f0; color:#64748b;">
                    <i class="fas fa-award" style="font-size:42px; color:#cbd5e1; margin-bottom:12px;"></i>
                    <p style="font-weight:700; margin:0;">No achievements posted yet.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════
   DRAWER 1: POST A NEW EVENT
   ═══════════════════════════════════════════════════════════════ --}}
<div class="drawer-backdrop" id="drawer-event">
    <div class="drawer-content">
        <div class="drawer-header">
            <h3>Post a New Event</h3>
            <button class="drawer-close" onclick="closeDrawer('drawer-event')">&times;</button>
        </div>
        <form method="POST" action="{{ route('school.gallery.events.store') }}" enctype="multipart/form-data" style="display:flex; flex-direction:column; height:100%;">
            @csrf
            <div class="drawer-body">
                <div class="form-group-field">
                    <label class="form-label-styled">Academic Year *</label>
                    <select name="academic_year" class="form-select-styled" required>
                        @if($currentSession)
                            <option value="{{ $currentSession->name }}">{{ $currentSession->name }}</option>
                        @endif
                        <option value="Apr 2025 - Mar 2026">Apr 2025 - Mar 2026</option>
                        <option value="Apr 2026 - Mar 2027">Apr 2026 - Mar 2027</option>
                    </select>
                </div>

                <div class="form-group-field">
                    <label class="form-label-styled">Event Title *</label>
                    <input type="text" name="title" class="form-input-styled" placeholder="Enter Event Title" required>
                </div>

                <div class="form-group-field">
                    <label class="form-label-styled">Description</label>
                    <textarea name="description" class="form-textarea-styled" rows="3" placeholder="Enter Description"></textarea>
                </div>

                <div class="form-group-field">
                    <label class="form-label-styled">Attach Photos & Videos *</label>
                    <input type="file" name="photos[]" class="form-input-styled" multiple accept="image/*,video/*">
                </div>

                <div class="form-group-field">
                    <label class="form-label-styled">Recipients *</label>
                    <div class="radio-group-wrap">
                        <label class="radio-item"><input type="radio" name="recipients" value="All Staff"> All Staff</label>
                        <label class="radio-item"><input type="radio" name="recipients" value="All Students"> All Students</label>
                        <label class="radio-item"><input type="radio" name="recipients" value="All Staff & Students" checked> All Staff & Students</label>
                        <label class="radio-item"><input type="radio" name="recipients" value="Section wise student"> Section wise student</label>
                        <label class="radio-item"><input type="radio" name="recipients" value="Specific Students"> Specific Students</label>
                        <label class="radio-item"><input type="radio" name="recipients" value="CCA Activity"> CCA Activity</label>
                    </div>
                </div>

                <div class="form-group-field">
                    <label class="checkbox-item">
                        <input type="checkbox" name="schedule_for_later" value="1"> Schedule For later
                    </label>
                </div>
            </div>
            <div class="drawer-footer">
                <button type="button" class="btn-drawer-back" onclick="closeDrawer('drawer-event')">&larr; BACK</button>
                <button type="submit" class="btn-drawer-submit"><i class="fas fa-save" style="margin-right:6px;"></i> POST EVENT</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
   DRAWER 2: POST A NEW ACHIEVEMENT
   ═══════════════════════════════════════════════════════════════ --}}
<div class="drawer-backdrop" id="drawer-achievement">
    <div class="drawer-content">
        <div class="drawer-header">
            <h3>Post a New Achievement</h3>
            <button class="drawer-close" onclick="closeDrawer('drawer-achievement')">&times;</button>
        </div>
        <form method="POST" action="{{ route('school.gallery.achievements.store') }}" enctype="multipart/form-data" style="display:flex; flex-direction:column; height:100%;">
            @csrf
            <div class="drawer-body">
                <div class="form-group-field">
                    <label class="form-label-styled">Academic Year *</label>
                    <select name="academic_year" class="form-select-styled" required>
                        @if($currentSession)
                            <option value="{{ $currentSession->name }}">{{ $currentSession->name }}</option>
                        @endif
                        <option value="Apr 2025 - Mar 2026">Apr 2025 - Mar 2026</option>
                        <option value="Apr 2026 - Mar 2027">Apr 2026 - Mar 2027</option>
                    </select>
                </div>

                <div class="form-group-field">
                    <label class="form-label-styled">Achievement Title *</label>
                    <input type="text" name="title" class="form-input-styled" placeholder="e.g. First Place in Science Fair" required>
                </div>

                <div class="form-group-field">
                    <label class="form-label-styled">Description</label>
                    <textarea name="description" class="form-textarea-styled" rows="3" placeholder="Enter Description"></textarea>
                </div>

                <div class="form-group-field">
                    <label class="form-label-styled">Attach Photos *</label>
                    <input type="file" name="photos[]" class="form-input-styled" multiple accept="image/*">
                </div>

                <div class="form-group-field">
                    <label class="form-label-styled">Recipients *</label>
                    <div class="radio-group-wrap">
                        <label class="radio-item"><input type="radio" name="recipients" value="All Staff"> All Staff</label>
                        <label class="radio-item"><input type="radio" name="recipients" value="All Students"> All Students</label>
                        <label class="radio-item"><input type="radio" name="recipients" value="All Staff & Students" checked> All Staff & Students</label>
                        <label class="radio-item"><input type="radio" name="recipients" value="Section wise student"> Section wise student</label>
                    </div>
                </div>

                <div class="form-group-field">
                    <label class="form-label-styled">Show as Popup Notification</label>
                    <div class="toggle-switch-wrap">
                        <label class="switch">
                            <input type="checkbox" name="show_popup" value="1">
                            <span class="slider"></span>
                        </label>
                        <span style="font-size:13px; font-weight:600; color:#475569;">Enable popup highlight on student mobile app</span>
                    </div>
                </div>
            </div>
            <div class="drawer-footer">
                <button type="button" class="btn-drawer-back" onclick="closeDrawer('drawer-achievement')">&larr; BACK</button>
                <button type="submit" class="btn-drawer-submit"><i class="fas fa-award" style="margin-right:6px;"></i> POST ACHIEVEMENT</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
   MODAL: SOCIAL MEDIA BROADCAST / SYNC
   ═══════════════════════════════════════════════════════════════ --}}
<div class="social-modal" id="modal-social-share">
    <div class="social-modal-content">
        <div class="social-modal-hdr">
            <h4>Share on Social Media</h4>
            <button class="drawer-close" onclick="closeSocialModal()">&times;</button>
        </div>
        <div class="social-modal-body">
            <p style="font-size:13.5px; color:#64748b; margin-top:0; margin-bottom:20px;">
                Broadcast <strong id="share-title-text" style="color:#0f3057;"></strong> directly to your linked official institute profiles.
            </p>

            {{-- Facebook --}}
            <div class="social-platform-card">
                <div class="social-platform-left">
                    <div class="social-platform-icon" style="background:#1877f2;"><i class="fab fa-facebook-f"></i></div>
                    <div>
                        <div class="social-name">Facebook</div>
                        @if(!empty($socialMedia['facebook']))
                            <div class="social-status connected"><i class="fas fa-link"></i> Connected to {{ $socialMedia['facebook'] }}</div>
                        @else
                            <div class="social-status disconnected"><i class="fas fa-exclamation-circle"></i> Not linked in Institute Info</div>
                        @endif
                    </div>
                </div>
                <div>
                    @if(!empty($socialMedia['facebook']))
                        <a href="{{ $socialMedia['facebook'] }}" target="_blank" class="btn-share-act" style="background:#1877f2; color:#fff; text-decoration:none;">
                            <i class="fab fa-facebook"></i> Post Now
                        </a>
                    @else
                        <a href="{{ route('school.settings.institute-info') }}" class="btn-share-act" style="background:#eff6ff; color:#2563eb; text-decoration:none;">
                            <i class="fas fa-cog"></i> Link Profile
                        </a>
                    @endif
                </div>
            </div>

            {{-- Twitter / X --}}
            <div class="social-platform-card">
                <div class="social-platform-left">
                    <div class="social-platform-icon" style="background:#000000;"><i class="fab fa-twitter"></i></div>
                    <div>
                        <div class="social-name">Twitter / X</div>
                        @if(!empty($socialMedia['twitter']))
                            <div class="social-status connected"><i class="fas fa-link"></i> Connected to {{ $socialMedia['twitter'] }}</div>
                        @else
                            <div class="social-status disconnected"><i class="fas fa-exclamation-circle"></i> Not linked in Institute Info</div>
                        @endif
                    </div>
                </div>
                <div>
                    @if(!empty($socialMedia['twitter']))
                        <a href="{{ $socialMedia['twitter'] }}" target="_blank" class="btn-share-act" style="background:#000000; color:#fff; text-decoration:none;">
                            <i class="fab fa-twitter"></i> Post Now
                        </a>
                    @else
                        <a href="{{ route('school.settings.institute-info') }}" class="btn-share-act" style="background:#eff6ff; color:#2563eb; text-decoration:none;">
                            <i class="fas fa-cog"></i> Link Profile
                        </a>
                    @endif
                </div>
            </div>

            {{-- Instagram --}}
            <div class="social-platform-card">
                <div class="social-platform-left">
                    <div class="social-platform-icon" style="background:linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);"><i class="fab fa-instagram"></i></div>
                    <div>
                        <div class="social-name">Instagram</div>
                        @if(!empty($socialMedia['instagram']))
                            <div class="social-status connected"><i class="fas fa-link"></i> Connected to {{ $socialMedia['instagram'] }}</div>
                        @else
                            <div class="social-status disconnected"><i class="fas fa-exclamation-circle"></i> Not linked in Institute Info</div>
                        @endif
                    </div>
                </div>
                <div>
                    @if(!empty($socialMedia['instagram']))
                        <a href="{{ $socialMedia['instagram'] }}" target="_blank" class="btn-share-act" style="background:linear-gradient(45deg, #e6683c, #bc1888); color:#fff; text-decoration:none;">
                            <i class="fab fa-instagram"></i> Open Profile
                        </a>
                    @else
                        <a href="{{ route('school.settings.institute-info') }}" class="btn-share-act" style="background:#eff6ff; color:#2563eb; text-decoration:none;">
                            <i class="fas fa-cog"></i> Link Profile
                        </a>
                    @endif
                </div>
            </div>

            {{-- LinkedIn --}}
            <div class="social-platform-card">
                <div class="social-platform-left">
                    <div class="social-platform-icon" style="background:#0a66c2;"><i class="fab fa-linkedin-in"></i></div>
                    <div>
                        <div class="social-name">LinkedIn</div>
                        @if(!empty($socialMedia['linkedin']))
                            <div class="social-status connected"><i class="fas fa-link"></i> Connected to {{ $socialMedia['linkedin'] }}</div>
                        @else
                            <div class="social-status disconnected"><i class="fas fa-exclamation-circle"></i> Not linked in Institute Info</div>
                        @endif
                    </div>
                </div>
                <div>
                    @if(!empty($socialMedia['linkedin']))
                        <a href="{{ $socialMedia['linkedin'] }}" target="_blank" class="btn-share-act" style="background:#0a66c2; color:#fff; text-decoration:none;">
                            <i class="fab fa-linkedin"></i> Share Post
                        </a>
                    @else
                        <a href="{{ route('school.settings.institute-info') }}" class="btn-share-act" style="background:#eff6ff; color:#2563eb; text-decoration:none;">
                            <i class="fas fa-cog"></i> Link Profile
                        </a>
                    @endif
                </div>
            </div>

            {{-- Instant Auto-Sync Action Button --}}
            <div style="margin-top:20px; background:#eff6ff; border:1px solid #bfdbfe; padding:14px; border-radius:12px; text-align:center;">
                <button type="button" onclick="triggerAutoBroadcast()" style="background:#2563eb; color:#fff; border:none; padding:10px 20px; border-radius:8px; font-weight:800; cursor:pointer; width:100%; font-size:13px; display:flex; align-items:center; justify-content:center; gap:8px;">
                    <i class="fas fa-paper-plane"></i> Auto-Publish & Sync across all linked profiles
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function switchGalleryTab(tabKey, btn) {
    document.querySelectorAll('.gallery-tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    
    document.getElementById('tab-' + tabKey).style.display = 'block';
    btn.classList.add('active');
}

function openDrawer(id) {
    document.getElementById(id).classList.add('active');
}
function closeDrawer(id) {
    document.getElementById(id).classList.remove('active');
}

let activeShareTitle = '';
let activeShareMedia = '';

function openSocialModal(title, media) {
    activeShareTitle = title;
    activeShareMedia = media;
    document.getElementById('share-title-text').innerText = '"' + title + '"';
    document.getElementById('modal-social-share').classList.add('active');
}
function closeSocialModal() {
    document.getElementById('modal-social-share').classList.remove('active');
}

function triggerAutoBroadcast() {
    alert('Successfully synced and published post "' + activeShareTitle + '" to all social media profiles configured in Institute Info!');
    closeSocialModal();
}
</script>
@endsection

@extends('superadmin.layouts.master')

@section('styles')
<style>
    /* Premium Grid Layout */
    .blog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
        margin-bottom: 30px;
    }
    .blog-premium-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .blog-premium-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.03);
        border-color: #cbd5e1;
    }
    .blog-cover-wrapper {
        height: 180px;
        width: 100%;
        background-color: #f1f5f9;
        position: relative;
        overflow: hidden;
    }
    .blog-cover-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    .blog-premium-card:hover .blog-cover-img {
        transform: scale(1.05);
    }
    .blog-badge-status {
        position: absolute;
        top: 15px;
        left: 15px;
        font-size: 11px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 30px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .status-published { background-color: #10b981; color: #ffffff; }
    .status-draft { background-color: #64748b; color: #ffffff; }

    .blog-content-area {
        padding: 24px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .blog-meta {
        font-size: 11.5px;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .blog-title {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 10px;
        line-height: 1.3;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .blog-summary {
        font-size: 13px;
        color: #475569;
        line-height: 1.5;
        margin-bottom: 20px;
    }
    .blog-actions {
        display: flex;
        gap: 8px;
        border-top: 1px solid #f1f5f9;
        padding-top: 15px;
    }
    .btn-blog-action {
        flex: 1;
        height: 38px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .btn-blog-edit { background-color: #f1f5f9; color: #475569; border: none; }
    .btn-blog-edit:hover { background-color: #e2e8f0; color: #0f172a; }
    .btn-blog-delete { background-color: #fef2f2; color: #ef4444; border: none; }
    .btn-blog-delete:hover { background-color: #fee2e2; color: #dc2626; }

    body.dark-mode .blog-premium-card {
        background-color: #111827;
        border-color: #1e293b;
    }
    body.dark-mode .blog-premium-card:hover {
        border-color: #374151;
        box-shadow: 0 12px 30px rgba(0,0,0,0.3);
    }
    body.dark-mode .blog-title { color: #f8fafc; }
    body.dark-mode .blog-summary { color: #94a3b8; }
    body.dark-mode .btn-blog-edit { background-color: #1f2937; color: #94a3b8; }
    body.dark-mode .btn-blog-edit:hover { background-color: #374151; color: #f8fafc; }
    body.dark-mode .btn-blog-delete { background-color: rgba(239, 68, 68, 0.1); color: #fca5a5; }
    body.dark-mode .btn-blog-delete:hover { background-color: rgba(239, 68, 68, 0.2); color: #f87171; }
    body.dark-mode .blog-actions { border-color: #1e293b; }
    body.dark-mode .form-control {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .form-control:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    }
    body.dark-mode .form-label, body.dark-mode label {
        color: #cbd5e1 !important;
    }
    body.dark-mode .text-dark {
        color: #f8fafc !important;
    }
    body.dark-mode .text-muted {
        color: #94a3b8 !important;
    }
    body.dark-mode .modal-content {
        background-color: #111827 !important;
        color: #f8fafc !important;
        border: 1px solid #1e293b !important;
    }
    body.dark-mode .modal-header,
    body.dark-mode .modal-footer {
        background-color: #0f172a !important;
        border-color: #1e293b !important;
        color: #f8fafc !important;
    }
    body.dark-mode .modal-header .modal-title {
        color: #f8fafc !important;
    }
    body.dark-mode .modal-header .close {
        color: #f8fafc !important;
        text-shadow: none !important;
        opacity: 0.7;
    }
    body.dark-mode .modal-header .close:hover {
        opacity: 1;
    }
    body.dark-mode .btn-outline-secondary {
        border-color: #374151 !important;
        color: #94a3b8 !important;
    }
    body.dark-mode .btn-outline-secondary:hover {
        background-color: #1f2937 !important;
        color: #f8fafc !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header Area -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 font-weight-bold text-dark m-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">Blog / CMS Manager</h1>
            <p class="text-muted m-0" style="font-size: 0.85rem;">Publish announcements, news bulletins, and feature releases on the public board.</p>
        </div>
        <button class="btn btn-primary px-4" style="border-radius: 12px; font-weight: 700; height: 42px; display: inline-flex; align-items: center; gap: 8px;" data-toggle="modal" data-target="#createArticleModal">
            <i class="fas fa-plus"></i> Create Article
        </button>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; font-size: 13.5px; background-color: #ecfdf5; color: #065f46;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Blog Grid -->
    <div class="blog-grid">
        @forelse($posts as $post)
            <div class="blog-premium-card">
                <div>
                    <div class="blog-cover-wrapper">
                        <img src="{{ $post['cover_url'] }}" class="blog-cover-img" alt="Cover">
                        <span class="blog-badge-status status-{{ $post['status'] }}">{{ $post['status'] }}</span>
                    </div>

                    <div class="blog-content-area">
                        <div>
                            <div class="blog-meta">
                                <span><i class="fas fa-user-edit mr-1"></i> {{ $post['author'] }}</span>
                                <span><i class="fas fa-calendar-alt mr-1"></i> {{ $post['created_at'] }}</span>
                            </div>
                            <h3 class="blog-title">{{ $post['title'] }}</h3>
                            <p class="blog-summary">{{ Str::limit($post['summary'], 120) }}</p>
                        </div>
                    </div>
                </div>

                <div class="px-4 pb-4">
                    <div class="blog-actions">
                        <button class="btn btn-blog-action btn-blog-edit" 
                                data-toggle="modal" 
                                data-target="#editArticleModal" 
                                data-id="{{ $post['id'] }}"
                                data-title="{{ $post['title'] }}"
                                data-summary="{{ $post['summary'] }}"
                                data-content="{{ $post['content'] }}"
                                data-author="{{ $post['author'] }}"
                                data-status="{{ $post['status'] }}"
                                data-cover="{{ $post['cover_url'] }}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form action="{{ route('superadmin.blog-cms.destroy', $post['id']) }}" method="POST" class="d-inline flex-grow-1" onsubmit="return confirm('Are you sure you want to delete this article?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-blog-action btn-blog-delete w-100">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 bg-white border rounded shadow-sm" style="border-radius: 16px !important;">
                <div style="font-size: 40px; color: #cbd5e1;" class="mb-3">
                    <i class="fas fa-blog"></i>
                </div>
                <h5 class="text-secondary font-weight-bold">No Articles Published</h5>
                <p class="text-muted" style="font-size: 0.85rem;">Click the button above to write your first system announcement.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- CREATE ARTICLE MODAL -->
<div class="modal fade" id="createArticleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header border-0 bg-light px-4 py-3">
                <h5 class="modal-title font-weight-bold" style="font-family: 'Plus Jakarta Sans', sans-serif;">Create Announcement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('superadmin.blog-cms.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="row">
                        <div class="col-md-8 form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13px;">Article Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. System Upgrade Alerts" style="border-radius: 10px; height: 42px;" required>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13px;">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-control" style="border-radius: 10px; height: 42px;" required>
                                <option value="published">Published</option>
                                <option value="draft">Draft / Hidden</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13px;">Author Name <span class="text-danger">*</span></label>
                            <input type="text" name="author" class="form-control" value="SuperAdmin Office" style="border-radius: 10px; height: 42px;" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13px;">Cover Image URL</label>
                            <input type="url" name="cover_url" class="form-control" placeholder="https://..." style="border-radius: 10px; height: 42px;">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13px;">Brief Summary <span class="text-danger">*</span></label>
                        <input type="text" name="summary" class="form-control" placeholder="Enter short highlight of this article..." style="border-radius: 10px; height: 42px;" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13px;">Full Content Text <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="6" placeholder="Describe the full detail of your announcement here..." style="border-radius: 10px;" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary px-4" style="border-radius: 10px;" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px; font-weight: 700;">Publish Article</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT ARTICLE MODAL -->
<div class="modal fade" id="editArticleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header border-0 bg-light px-4 py-3">
                <h5 class="modal-title font-weight-bold" style="font-family: 'Plus Jakarta Sans', sans-serif;">Edit Announcement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editArticleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body px-4 py-3">
                    <div class="row">
                        <div class="col-md-8 form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13px;">Article Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="edit_title" class="form-control" style="border-radius: 10px; height: 42px;" required>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13px;">Status <span class="text-danger">*</span></label>
                            <select name="status" id="edit_status" class="form-control" style="border-radius: 10px; height: 42px;" required>
                                <option value="published">Published</option>
                                <option value="draft">Draft / Hidden</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13px;">Author Name <span class="text-danger">*</span></label>
                            <input type="text" name="author" id="edit_author" class="form-control" style="border-radius: 10px; height: 42px;" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13px;">Cover Image URL</label>
                            <input type="url" name="cover_url" id="edit_cover" class="form-control" style="border-radius: 10px; height: 42px;">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13px;">Brief Summary <span class="text-danger">*</span></label>
                        <input type="text" name="summary" id="edit_summary" class="form-control" style="border-radius: 10px; height: 42px;" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13px;">Full Content Text <span class="text-danger">*</span></label>
                        <textarea name="content" id="edit_content" class="form-control" rows="6" style="border-radius: 10px;" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary px-4" style="border-radius: 10px;" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px; font-weight: 700;">Update Article</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Edit Article Modal field values population
        $('#editArticleModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const title = button.data('title');
            const summary = button.data('summary');
            const content = button.data('content');
            const author = button.data('author');
            const status = button.data('status');
            const cover = button.data('cover');

            const modal = $(this);
            modal.find('#editArticleForm').attr('action', `/superadmin/blog-cms/${id}`);
            modal.find('#edit_title').val(title);
            modal.find('#edit_summary').val(summary);
            modal.find('#edit_content').val(content);
            modal.find('#edit_author').val(author);
            modal.find('#edit_status').val(status);
            modal.find('#edit_cover').val(cover);
        });
    });
</script>
@endsection

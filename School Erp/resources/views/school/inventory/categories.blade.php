@extends('layouts.app')

@section('page-title', 'Manage Product Category - Inventory Management')

@section('content')
<style>
    /* ─── Standard ERP Blue & White Theme (Matching Payroll, Expenses, Student Modules) ─── */
    :root {
        --erp-blue-dark:   #1e3a8a;
        --erp-blue:        #2563eb;
        --erp-blue-light:  #3b82f6;
        --erp-blue-soft:   #eff6ff;
        --erp-blue-border: #dbeafe;
        --erp-card-bg:     #ffffff;
        --erp-border:      #e2e8f0;
        --erp-text-dark:   #0f172a;
        --erp-text-muted:  #64748b;
        --erp-active-bg:   #ecfdf5;
        --erp-active-text: #047857;
        --erp-active-dot:  #10b981;
        --erp-inactive-bg: #fef2f2;
        --erp-inactive-text:#b91c1c;
        --erp-inactive-dot:#ef4444;
        --erp-deactivate:  #ea580c;
        --erp-deactivate-hover: #c2410c;
        --erp-activate:    #10b981;
        --erp-activate-hover: #059669;
        --erp-delete:      #ef4444;
        --erp-delete-hover:#dc2626;
    }

    /* ─── Full Screen Container ────────────────────────────────────────── */
    .inv-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 20px 28px 40px !important;
        box-sizing: border-box;
    }

    /* ─── ERP Cards ────────────────────────────────────────────────────── */
    .inv-card {
        background: #ffffff;
        border: 1px solid var(--erp-border);
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.05);
        margin-bottom: 24px;
        overflow: hidden;
        transition: all 0.25s ease;
    }

    .inv-card-header {
        background: linear-gradient(135deg, var(--erp-blue-dark) 0%, var(--erp-blue) 60%, var(--erp-blue-light) 100%);
        color: #ffffff;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        border-top-left-radius: 13px;
        border-top-right-radius: 13px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    }

    .inv-card-header h5 {
        margin: 0;
        font-size: 15.5px;
        font-weight: 800;
        letter-spacing: 0.2px;
        color: #ffffff;
        display: flex;
        align-items: center;
    }

    .inv-card-header .hdr-icon {
        font-size: 16px;
        margin-right: 10px;
        color: #ffffff;
        opacity: 0.95;
    }

    .inv-card-body {
        padding: 22px 24px;
        background: #ffffff;
    }

    /* ─── Buttons ──────────────────────────────────────────────────────── */
    .btn-inv-add {
        background: linear-gradient(135deg, var(--erp-blue) 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 13.5px;
        padding: 10px 24px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.28);
    }
    .btn-inv-add:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.4);
        transform: translateY(-1px);
    }

    /* ─── Table Styling ────────────────────────────────────────────────── */
    .inv-table-wrap {
        border: 1px solid var(--erp-border);
        border-radius: 10px;
        overflow-x: auto;
        background: #ffffff;
    }

    .inv-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13.5px;
    }

    .inv-table thead th {
        background: #f8fafc;
        color: var(--erp-text-dark);
        font-weight: 700;
        font-size: 12.5px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        padding: 14px 20px;
        border-bottom: 1.5px solid var(--erp-border);
        white-space: nowrap;
        vertical-align: middle;
    }

    .inv-table tbody tr {
        transition: background-color 0.15s ease;
    }

    .inv-table tbody tr:hover {
        background-color: #f0f7ff;
    }

    .inv-table tbody td {
        padding: 14px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: var(--erp-text-dark);
        white-space: nowrap;
    }

    .inv-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* ─── Status Badges ────────────────────────────────────────────────── */
    .badge-inv-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }

    .badge-inv-active {
        background: var(--erp-active-bg);
        color: var(--erp-active-text);
        border: 1px solid rgba(16, 185, 129, 0.25);
    }

    .badge-inv-inactive {
        background: var(--erp-inactive-bg);
        color: var(--erp-inactive-text);
        border: 1px solid rgba(239, 68, 68, 0.25);
    }

    .badge-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
    }
    .badge-inv-active .badge-dot {
        background: var(--erp-active-dot);
    }
    .badge-inv-inactive .badge-dot {
        background: var(--erp-inactive-dot);
    }

    /* ─── Action Buttons in Table ──────────────────────────────────────── */
    .btn-row-edit {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #1e40af;
        font-weight: 700;
        font-size: 12px;
        padding: 6px 14px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-row-edit:hover {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #93c5fd;
    }

    .btn-row-deactivate {
        background: var(--erp-deactivate);
        color: #ffffff;
        font-weight: 700;
        font-size: 12px;
        padding: 6px 14px;
        border-radius: 6px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .btn-row-deactivate:hover {
        background: var(--erp-deactivate-hover);
        color: #ffffff;
    }

    .btn-row-activate {
        background: var(--erp-activate);
        color: #ffffff;
        font-weight: 700;
        font-size: 12px;
        padding: 6px 14px;
        border-radius: 6px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .btn-row-activate:hover {
        background: var(--erp-activate-hover);
        color: #ffffff;
    }

    /* Sleek Icon-Only Delete Button */
    .btn-row-delete-icon {
        width: 32px;
        height: 32px;
        min-width: 32px;
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 12.5px;
        flex-shrink: 0;
        padding: 0;
    }
    .btn-row-delete-icon:hover {
        background: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.35);
        transform: translateY(-1px);
    }

    /* ─── Responsive Slider Drawer (Slide-Over Panel) ─────────────────── */
    .inv-slider-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 1050;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .inv-slider-backdrop.open {
        opacity: 1;
        visibility: visible;
    }

    .inv-slider-panel {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        width: 480px;
        max-width: 100vw;
        height: 100vh;
        height: 100dvh;
        background: #ffffff;
        z-index: 1051;
        box-shadow: -8px 0 35px rgba(0, 0, 0, 0.18);
        display: flex;
        flex-direction: column;
        transform: translateX(100%);
        transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
    }

    .inv-slider-panel.open {
        transform: translateX(0);
    }

    @media (max-width: 576px) {
        .inv-slider-panel {
            width: 100vw;
        }
    }

    .inv-slider-header {
        background: linear-gradient(135deg, var(--erp-blue-dark) 0%, var(--erp-blue) 100%);
        color: #ffffff;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .inv-slider-header h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-slider-close {
        background: rgba(255, 255, 255, 0.18);
        border: none;
        color: #ffffff;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 15px;
        transition: background 0.2s ease;
    }
    .btn-slider-close:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .inv-slider-body {
        flex: 1;
        overflow-y: auto;
        padding: 28px 26px;
    }

    .inv-form-group {
        margin-bottom: 22px;
    }

    .inv-form-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: var(--erp-text-dark);
        margin-bottom: 7px;
    }

    .inv-form-input {
        width: 100%;
        padding: 11px 14px;
        font-size: 13.5px;
        color: var(--erp-text-dark);
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .inv-form-input:focus {
        border-color: var(--erp-blue);
        box-shadow: 0 0 0 3.5px rgba(37, 99, 235, 0.14);
    }
    .inv-form-input::placeholder {
        color: #94a3b8;
    }

    /* Checkbox */
    .inv-checkbox-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        user-select: none;
        margin-top: 4px;
    }

    .inv-checkbox-input {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--erp-blue);
        border-radius: 4px;
    }

    .inv-checkbox-label {
        font-size: 13.5px;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        margin-bottom: 0;
    }

    .inv-slider-footer {
        padding: 18px 26px;
        background: #f8fafc;
        border-top: 1px solid var(--erp-border);
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 12px;
        flex-shrink: 0;
    }

    .btn-slider-save {
        background: linear-gradient(135deg, var(--erp-blue) 0%, #1d4ed8 100%);
        color: #ffffff;
        font-weight: 700;
        font-size: 13.5px;
        padding: 10px 24px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }
    .btn-slider-save:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
    }

    .btn-slider-discard {
        background: #ef4444;
        color: #ffffff;
        font-weight: 700;
        font-size: 13.5px;
        padding: 10px 24px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-slider-discard:hover {
        background: #dc2626;
        color: #ffffff;
    }

    /* ─── Delete Confirmation Modal ───────────────────────────────────── */
    .inv-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(4px);
        z-index: 1070;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }
    .inv-modal-overlay.open {
        opacity: 1;
        visibility: visible;
    }

    .inv-modal-card {
        background: #ffffff;
        border-radius: 14px;
        max-width: 420px;
        width: 100%;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.2);
        padding: 26px;
        text-align: center;
        transform: scale(0.95);
        transition: transform 0.25s ease;
    }
    .inv-modal-overlay.open .inv-modal-card {
        transform: scale(1);
    }

    .inv-modal-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #fee2e2;
        color: #dc2626;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin: 0 auto 16px;
    }

    .inv-modal-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--erp-text-dark);
        margin-bottom: 8px;
    }

    .inv-modal-desc {
        font-size: 13.5px;
        color: #64748b;
        margin-bottom: 22px;
        line-height: 1.5;
    }

    .inv-modal-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .btn-modal-cancel {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
        font-weight: 700;
        font-size: 13px;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        flex: 1;
        transition: background 0.2s ease;
    }
    .btn-modal-cancel:hover {
        background: #e2e8f0;
    }

    .btn-modal-delete {
        background: #dc2626;
        color: #ffffff;
        border: none;
        font-weight: 700;
        font-size: 13px;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        flex: 1;
        transition: background 0.2s ease;
    }
    .btn-modal-delete:hover {
        background: #b91c1c;
    }

    /* ─── Toast Notifications ────────────────────────────────────────── */
    .inv-toast-container {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 1080;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }

    .inv-toast {
        min-width: 280px;
        max-width: 380px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 20px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 13.5px;
        font-weight: 700;
        color: #1e293b;
        transform: translateY(20px);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: auto;
    }

    .inv-toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    .inv-toast.toast-success {
        border-left: 4px solid #10b981;
    }
    .inv-toast.toast-success .toast-icon {
        color: #10b981;
    }

    .inv-toast.toast-error {
        border-left: 4px solid #ef4444;
    }
    .inv-toast.toast-error .toast-icon {
        color: #ef4444;
    }
</style>

<div class="inv-container">
    <!-- 1. Top Card: Manage Product Category -->
    <div class="inv-card">
        <div class="inv-card-header">
            <h5><i class="fas fa-tag hdr-icon"></i>Manage Product Category</h5>
        </div>
        <div class="inv-card-body">
            <button type="button" class="btn btn-inv-add" onclick="openAddSlider()">
                <i class="fas fa-plus"></i>
                <span>Add Category</span>
            </button>
        </div>
    </div>

    <!-- 2. Bottom Card: Category List -->
    <div class="inv-card">
        <div class="inv-card-header">
            <h5><i class="fas fa-th-large hdr-icon"></i>Category List</h5>
        </div>
        <div class="inv-card-body p-3">
            <div class="inv-table-wrap">
                <table class="inv-table" id="categoryTable">
                    <thead>
                        <tr>
                            <th style="width: 80px;">S.No</th>
                            <th>Category Name</th>
                            <th style="width: 150px; text-align: center;">Status</th>
                            <th style="width: 110px; text-align: center;">Edit</th>
                            <th style="width: 180px; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="categoryTableBody">
                        @forelse($categories as $index => $cat)
                        <tr id="row-{{ $cat->id }}" data-id="{{ $cat->id }}" data-name="{{ $cat->name }}" data-status="{{ $cat->status ? '1' : '0' }}">
                            <td class="row-sno" style="font-weight: 700; color: #475569;">{{ $index + 1 }}</td>
                            <td class="row-name" style="font-weight: 700; color: #0f172a; font-size: 14px;">{{ $cat->name }}</td>
                            <td style="text-align: center;">
                                @if($cat->status)
                                    <span class="badge-inv-status badge-inv-active" id="badge-{{ $cat->id }}">
                                        <span class="badge-dot"></span> ACTIVE
                                    </span>
                                @else
                                    <span class="badge-inv-status badge-inv-inactive" id="badge-{{ $cat->id }}">
                                        <span class="badge-dot"></span> INACTIVE
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn-row-edit" onclick='openEditSlider({{ json_encode($cat) }})'>
                                    <i class="fas fa-pen" style="font-size: 11px;"></i> Edit
                                </button>
                            </td>
                            <td style="text-align: center;" id="action-cell-{{ $cat->id }}">
                                <div class="d-inline-flex align-items-center justify-content-center" style="gap: 8px; flex-wrap: nowrap; white-space: nowrap;">
                                    @if($cat->status)
                                        <button type="button" class="btn-row-deactivate" onclick="toggleStatus({{ $cat->id }}, true)">
                                            <i class="fas fa-ban" style="font-size: 11px;"></i> Deactivate
                                        </button>
                                    @else
                                        <button type="button" class="btn-row-activate" onclick="toggleStatus({{ $cat->id }}, false)">
                                            <i class="fas fa-check" style="font-size: 11px;"></i> Activate
                                        </button>
                                    @endif

                                    <button type="button" class="btn-row-delete-icon" onclick="confirmDeleteCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}')" title="Delete Category">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyRow">
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div style="padding: 20px;">
                                    <i class="fas fa-box-open fa-2x mb-3 text-muted" style="opacity: 0.5;"></i>
                                    <div style="font-size: 14px; font-weight: 600;">No categories found</div>
                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Click "Add Category" above to create your first product category.</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ─── Responsive Slider Drawer (Slide-Over Panel) ─────────────────── -->
<div class="inv-slider-backdrop" id="sliderBackdrop" onclick="closeSlider()"></div>

<div class="inv-slider-panel" id="sliderPanel" aria-hidden="true">
    <!-- Slider Header -->
    <div class="inv-slider-header">
        <h4>
            <i class="fas fa-tag" style="font-size: 16px;"></i>
            <span id="sliderTitle">Manage Product Category</span>
        </h4>
        <button type="button" class="btn-slider-close" onclick="closeSlider()" title="Close slider">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Slider Body Form -->
    <div class="inv-slider-body">
        <form id="categoryForm" onsubmit="handleCategorySubmit(event)">
            <input type="hidden" id="categoryId" name="id" value="">

            <div class="inv-form-group">
                <label for="categoryName" class="inv-form-label">
                    Category Name <span class="text-danger">*</span>
                </label>
                <input type="text" 
                       class="inv-form-input" 
                       id="categoryName" 
                       name="name" 
                       placeholder="e.g. Uniform, Books, Footwear..." 
                       autocomplete="off" 
                       required>
            </div>

            <div class="inv-form-group">
                <label class="inv-form-label">
                    Status <span class="text-danger">*</span>
                </label>
                <label class="inv-checkbox-wrap" for="categoryStatus">
                    <input type="checkbox" 
                           class="inv-checkbox-input" 
                           id="categoryStatus" 
                           name="status" 
                           value="1" 
                           checked>
                    <span class="inv-checkbox-label">Mark as Active</span>
                </label>
            </div>
        </form>
    </div>

    <!-- Slider Footer Actions -->
    <div class="inv-slider-footer">
        <button type="button" class="btn-slider-save" id="btnSaveCategory" onclick="document.getElementById('categoryForm').requestSubmit()">
            <i class="fas fa-check"></i>
            <span id="btnSaveCategoryText">Save Category</span>
        </button>
        <button type="button" class="btn-slider-discard" onclick="closeSlider()">
            <i class="fas fa-times"></i>
            <span>Discard</span>
        </button>
    </div>
</div>

<!-- ─── Delete Confirmation Modal ───────────────────────────────────── -->
<div class="inv-modal-overlay" id="deleteConfirmModal">
    <div class="inv-modal-card">
        <div class="inv-modal-icon">
            <i class="fas fa-trash-alt"></i>
        </div>
        <h3 class="inv-modal-title">Delete Category?</h3>
        <p class="inv-modal-desc">
            Are you sure you want to delete <strong id="deleteCategoryName" class="text-dark"></strong>? This category will be removed from your school inventory.
        </p>
        <div class="inv-modal-actions">
            <button type="button" class="btn-modal-cancel" onclick="closeDeleteModal()">Cancel</button>
            <button type="button" class="btn-modal-delete" id="btnConfirmDelete" onclick="executeDeleteCategory()">Yes, Delete</button>
        </div>
    </div>
</div>

<!-- Toast Notifications Container -->
<div class="inv-toast-container" id="toastContainer"></div>

<!-- ─── Script Interactions ─────────────────────────────────────────── -->
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    let categoryToDeleteId = null;

    // Open Slider for Adding New Category
    function openAddSlider() {
        document.getElementById('categoryId').value = '';
        document.getElementById('categoryName').value = '';
        document.getElementById('categoryStatus').checked = true;
        document.getElementById('sliderTitle').textContent = 'Manage Product Category';
        document.getElementById('btnSaveCategoryText').textContent = 'Save Category';

        document.getElementById('sliderBackdrop').classList.add('open');
        document.getElementById('sliderPanel').classList.add('open');
        document.getElementById('sliderPanel').setAttribute('aria-hidden', 'false');

        setTimeout(() => {
            document.getElementById('categoryName').focus();
        }, 150);
    }

    // Open Slider for Editing Existing Category
    function openEditSlider(category) {
        document.getElementById('categoryId').value = category.id;
        document.getElementById('categoryName').value = category.name;
        document.getElementById('categoryStatus').checked = Boolean(category.status);
        document.getElementById('sliderTitle').textContent = 'Manage Product Category';
        document.getElementById('btnSaveCategoryText').textContent = 'Save Category';

        document.getElementById('sliderBackdrop').classList.add('open');
        document.getElementById('sliderPanel').classList.add('open');
        document.getElementById('sliderPanel').setAttribute('aria-hidden', 'false');

        setTimeout(() => {
            document.getElementById('categoryName').focus();
        }, 150);
    }

    // Close Slider
    function closeSlider() {
        document.getElementById('sliderBackdrop').classList.remove('open');
        document.getElementById('sliderPanel').classList.remove('open');
        document.getElementById('sliderPanel').setAttribute('aria-hidden', 'true');
    }

    // Escape Key to Close Slider or Modal
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            closeSlider();
            closeDeleteModal();
        }
    });

    // Handle Form Submit (Save / Update Category via AJAX)
    async function handleCategorySubmit(event) {
        event.preventDefault();
        const id = document.getElementById('categoryId').value;
        const name = document.getElementById('categoryName').value.trim();
        const status = document.getElementById('categoryStatus').checked ? 1 : 0;

        if (!name) {
            showToast('Please enter category name.', 'error');
            document.getElementById('categoryName').focus();
            return;
        }

        const isEdit = Boolean(id);
        const url = isEdit 
            ? "{{ url('school/inventory/categories') }}/" + id + "/update"
            : "{{ route('school.inventory.categories.store') }}";

        const saveBtn = document.getElementById('btnSaveCategory');
        const originalText = document.getElementById('btnSaveCategoryText').textContent;
        document.getElementById('btnSaveCategoryText').textContent = 'Saving...';
        saveBtn.disabled = true;

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ name: name, status: status })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showToast(data.message || (isEdit ? 'Category updated successfully!' : 'Category added successfully!'), 'success');
                closeSlider();

                const categoryData = data.category || { id: id || Date.now(), name: name, status: status };

                if (isEdit) {
                    // Update existing row
                    updateTableRow(categoryData);
                } else {
                    // Append new row
                    appendTableRow(categoryData);
                }
            } else {
                showToast(data.message || 'Error saving category. Please try again.', 'error');
            }
        } catch (error) {
            console.error('Save Category error:', error);
            showToast('Network error while saving category.', 'error');
        } finally {
            document.getElementById('btnSaveCategoryText').textContent = originalText;
            saveBtn.disabled = false;
        }
    }

    // Toggle Active / Inactive Status
    async function toggleStatus(id, currentStatus) {
        const url = "{{ url('school/inventory/categories') }}/" + id + "/toggle-status";

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ current_status: currentStatus })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                const newStatus = Boolean(data.status);
                showToast(data.message || 'Status updated successfully!', 'success');

                // Update Row State
                const row = document.getElementById('row-' + id);
                if (row) {
                    row.setAttribute('data-status', newStatus ? '1' : '0');
                    const catName = row.getAttribute('data-name');

                    // Update Badge
                    const badge = document.getElementById('badge-' + id);
                    if (badge) {
                        badge.className = 'badge-inv-status ' + (newStatus ? 'badge-inv-active' : 'badge-inv-inactive');
                        badge.innerHTML = `<span class="badge-dot"></span> ${newStatus ? 'ACTIVE' : 'INACTIVE'}`;
                    }

                    // Update Action Cell in a single horizontal line
                    const actionCell = document.getElementById('action-cell-' + id);
                    if (actionCell) {
                        actionCell.innerHTML = `
                            <div class="d-inline-flex align-items-center justify-content-center" style="gap: 8px; flex-wrap: nowrap; white-space: nowrap;">
                                ${newStatus ? `
                                    <button type="button" class="btn-row-deactivate" onclick="toggleStatus(${id}, true)">
                                        <i class="fas fa-ban" style="font-size: 11px;"></i> Deactivate
                                    </button>
                                ` : `
                                    <button type="button" class="btn-row-activate" onclick="toggleStatus(${id}, false)">
                                        <i class="fas fa-check" style="font-size: 11px;"></i> Activate
                                    </button>
                                `}
                                <button type="button" class="btn-row-delete-icon" onclick="confirmDeleteCategory(${id}, '${escapeJs(catName)}')" title="Delete Category">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        `;
                    }

                    // Update Edit Button onclick payload
                    const editBtn = row.querySelector('.btn-row-edit');
                    if (editBtn) {
                        editBtn.onclick = function () {
                            openEditSlider({ id: id, name: catName, status: newStatus });
                        };
                    }
                }
            } else {
                showToast(data.message || 'Failed to toggle status.', 'error');
            }
        } catch (error) {
            console.error('Toggle status error:', error);
            showToast('Network error while toggling status.', 'error');
        }
    }

    // Confirm Delete Dialog
    function confirmDeleteCategory(id, name) {
        categoryToDeleteId = id;
        document.getElementById('deleteCategoryName').textContent = `"${name}"`;
        document.getElementById('deleteConfirmModal').classList.add('open');
    }

    // Close Delete Modal
    function closeDeleteModal() {
        categoryToDeleteId = null;
        document.getElementById('deleteConfirmModal').classList.remove('open');
    }

    // Execute Delete Action via AJAX
    async function executeDeleteCategory() {
        if (!categoryToDeleteId) return;

        const id = categoryToDeleteId;
        const url = "{{ url('school/inventory/categories') }}/" + id;
        const deleteBtn = document.getElementById('btnConfirmDelete');
        deleteBtn.disabled = true;
        deleteBtn.textContent = 'Deleting...';

        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showToast(data.message || 'Category deleted successfully!', 'success');
                closeDeleteModal();

                // Remove Row with Animation
                const row = document.getElementById('row-' + id);
                if (row) {
                    row.style.opacity = '0';
                    row.style.transform = 'scale(0.95)';
                    row.style.transition = 'all 0.25s ease';
                    setTimeout(() => {
                        row.remove();
                        reindexTableRows();
                    }, 250);
                }
            } else {
                showToast(data.message || 'Failed to delete category.', 'error');
            }
        } catch (error) {
            console.error('Delete category error:', error);
            showToast('Network error while deleting category.', 'error');
        } finally {
            deleteBtn.disabled = false;
            deleteBtn.textContent = 'Yes, Delete';
        }
    }

    // Re-index S.No column after delete
    function reindexTableRows() {
        const tbody = document.getElementById('categoryTableBody');
        const rows = tbody.querySelectorAll('tr:not(#emptyRow)');
        if (rows.length === 0) {
            tbody.innerHTML = `
                <tr id="emptyRow">
                    <td colspan="5" class="text-center py-5 text-muted">
                        <div style="padding: 20px;">
                            <i class="fas fa-box-open fa-2x mb-3 text-muted" style="opacity: 0.5;"></i>
                            <div style="font-size: 14px; font-weight: 600;">No categories found</div>
                            <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Click "Add Category" above to create your first product category.</div>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        rows.forEach((row, index) => {
            const snoCell = row.querySelector('.row-sno');
            if (snoCell) snoCell.textContent = index + 1;
        });
    }

    // Append New Row to Table
    function appendTableRow(category) {
        const tbody = document.getElementById('categoryTableBody');
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow) emptyRow.remove();

        const count = tbody.querySelectorAll('tr:not(#emptyRow)').length + 1;
        const isAct = Boolean(category.status);

        const tr = document.createElement('tr');
        tr.id = 'row-' + category.id;
        tr.setAttribute('data-id', category.id);
        tr.setAttribute('data-name', category.name);
        tr.setAttribute('data-status', isAct ? '1' : '0');

        tr.innerHTML = `
            <td class="row-sno" style="font-weight: 700; color: #475569;">${count}</td>
            <td class="row-name" style="font-weight: 700; color: #0f172a; font-size: 14px;">${escapeHtml(category.name)}</td>
            <td style="text-align: center;">
                <span class="badge-inv-status ${isAct ? 'badge-inv-active' : 'badge-inv-inactive'}" id="badge-${category.id}">
                    <span class="badge-dot"></span> ${isAct ? 'ACTIVE' : 'INACTIVE'}
                </span>
            </td>
            <td style="text-align: center;">
                <button type="button" class="btn-row-edit" onclick='openEditSlider(${JSON.stringify(category)})'>
                    <i class="fas fa-pen" style="font-size: 11px;"></i> Edit
                </button>
            </td>
            <td style="text-align: center;" id="action-cell-${category.id}">
                <div class="d-inline-flex align-items-center justify-content-center" style="gap: 8px; flex-wrap: nowrap; white-space: nowrap;">
                    ${isAct ? `
                        <button type="button" class="btn-row-deactivate" onclick="toggleStatus(${category.id}, true)">
                            <i class="fas fa-ban" style="font-size: 11px;"></i> Deactivate
                        </button>
                    ` : `
                        <button type="button" class="btn-row-activate" onclick="toggleStatus(${category.id}, false)">
                            <i class="fas fa-check" style="font-size: 11px;"></i> Activate
                        </button>
                    `}
                    <button type="button" class="btn-row-delete-icon" onclick="confirmDeleteCategory(${category.id}, '${escapeJs(category.name)}')" title="Delete Category">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </td>
        `;

        tbody.appendChild(tr);
    }

    // Update Existing Row in Table
    function updateTableRow(category) {
        const row = document.getElementById('row-' + category.id);
        if (!row) return;

        const isAct = Boolean(category.status);
        row.setAttribute('data-name', category.name);
        row.setAttribute('data-status', isAct ? '1' : '0');

        const nameCell = row.querySelector('.row-name');
        if (nameCell) nameCell.textContent = category.name;

        const badge = document.getElementById('badge-' + category.id);
        if (badge) {
            badge.className = 'badge-inv-status ' + (isAct ? 'badge-inv-active' : 'badge-inv-inactive');
            badge.innerHTML = `<span class="badge-dot"></span> ${isAct ? 'ACTIVE' : 'INACTIVE'}`;
        }

        const actionCell = document.getElementById('action-cell-' + category.id);
        if (actionCell) {
            actionCell.innerHTML = `
                <div class="d-inline-flex align-items-center justify-content-center" style="gap: 8px; flex-wrap: nowrap; white-space: nowrap;">
                    ${isAct ? `
                        <button type="button" class="btn-row-deactivate" onclick="toggleStatus(${category.id}, true)">
                            <i class="fas fa-ban" style="font-size: 11px;"></i> Deactivate
                        </button>
                    ` : `
                        <button type="button" class="btn-row-activate" onclick="toggleStatus(${category.id}, false)">
                            <i class="fas fa-check" style="font-size: 11px;"></i> Activate
                        </button>
                    `}
                    <button type="button" class="btn-row-delete-icon" onclick="confirmDeleteCategory(${category.id}, '${escapeJs(category.name)}')" title="Delete Category">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            `;
        }

        const editBtn = row.querySelector('.btn-row-edit');
        if (editBtn) {
            editBtn.onclick = function () {
                openEditSlider(category);
            };
        }
    }

    // Escape Helpers
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function (m) { return map[m]; });
    }

    function escapeJs(text) {
        return String(text).replace(/'/g, "\\'").replace(/"/g, '\\"');
    }

    // Toast Notification System
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `inv-toast toast-${type}`;
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        toast.innerHTML = `
            <i class="fas ${icon} toast-icon fa-lg"></i>
            <span>${escapeHtml(message)}</span>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('show');
        }, 10);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }
</script>
@endsection

@extends('layouts.app')

@section('title', 'Catalogue - Library Management')
@section('page-title', 'Catalogue')

@section('content')
<style>
    /* ==========================================================================
       ROYAL COBALT BLUE & WHITE ERP THEME (Matching Library Basics)
       ========================================================================== */
    :root {
        --cat-blue-primary: #0038b8;
        --cat-blue-deep: #002266;
        --cat-blue-midnight: #001233;
        --cat-blue-gradient: linear-gradient(135deg, #002266 0%, #0038b8 55%, #1d4ed8 100%);
        --cat-blue-light: #eff6ff;
        --cat-blue-border: #bfdbfe;
        --cat-blue-glow: rgba(0, 56, 184, 0.18);
        --cat-table-hdr-gradient: linear-gradient(135deg, #001a4d 0%, #002b80 50%, #0038b8 100%);
        --cat-border: #cbd5e1;
        --cat-border-soft: #e2e8f0;
    }

    /* Page Entrance Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(14px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .cat-page-wrapper {
        animation: fadeInUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    /* Top Tabs (Blue & White Theme) */
    .cat-tabs-row {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 20px;
    }

    .cat-tab-btn {
        padding: 11px 24px;
        font-size: 13.5px;
        font-weight: 800;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        border-radius: 10px 10px 0 0;
        border: 1px solid transparent;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .cat-tab-btn.active {
        background: var(--cat-blue-gradient);
        color: #ffffff;
        border-color: var(--cat-blue-primary);
        box-shadow: 0 4px 14px rgba(0, 56, 184, 0.3);
    }

    .cat-tab-btn.inactive {
        background: #ffffff;
        color: #475569;
        border-color: #e2e8f0;
    }

    .cat-tab-btn.inactive:hover {
        background: var(--cat-blue-light);
        color: var(--cat-blue-primary);
        border-color: var(--cat-blue-border);
    }

    /* Main Container Card */
    .cat-main-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 6px 24px rgba(0, 56, 184, 0.05), 0 1px 3px rgba(0, 0, 0, 0.02);
        padding: 24px;
        margin-bottom: 40px;
    }

    /* Search & Action Bar Row (Image 1 Style) */
    .cat-filter-deck {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px;
        margin-bottom: 22px;
    }

    .cat-floating-input-group {
        position: relative;
        flex: 1;
        min-width: 220px;
    }

    .cat-floating-input-group label {
        position: absolute;
        top: -9px;
        left: 14px;
        background: #ffffff;
        padding: 0 6px;
        font-size: 11.5px;
        font-weight: 700;
        color: #475569;
        z-index: 2;
    }

    .cat-floating-input {
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 13.5px;
        font-weight: 600;
        color: #0f172a;
        width: 100%;
        background: #ffffff;
        outline: none;
        transition: all 0.2s ease;
    }

    .cat-floating-input:focus {
        border-color: var(--cat-blue-primary);
        box-shadow: 0 0 0 3px rgba(0, 56, 184, 0.12);
    }

    .cat-floating-select {
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 32px 10px 14px;
        font-size: 13.5px;
        font-weight: 600;
        color: #0f172a;
        width: 100%;
        background: #ffffff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%230038b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") no-repeat right 12px center/11px;
        appearance: none;
        outline: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .cat-floating-select:focus {
        border-color: var(--cat-blue-primary);
        box-shadow: 0 0 0 3px rgba(0, 56, 184, 0.12);
    }

    /* Action Buttons (Blue & White) */
    .btn-cat-outline {
        border: 1.5px solid var(--cat-blue-primary);
        color: var(--cat-blue-primary);
        background: #ffffff;
        font-size: 12.5px;
        font-weight: 800;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        padding: 10px 18px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-cat-outline:hover {
        background: var(--cat-blue-light);
        color: var(--cat-blue-deep);
        border-color: var(--cat-blue-deep);
        transform: translateY(-1px);
    }

    .btn-cat-primary {
        background: var(--cat-blue-gradient);
        color: #ffffff;
        font-size: 12.5px;
        font-weight: 800;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        box-shadow: 0 4px 14px rgba(0, 56, 184, 0.28);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-cat-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(0, 56, 184, 0.38);
        color: #ffffff;
    }

    .btn-cat-icon {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        color: var(--cat-blue-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-cat-icon:hover {
        background: var(--cat-blue-light);
        border-color: var(--cat-blue-primary);
    }

    /* Table Toolbar (Columns / Filters / Density Bar) */
    .cat-table-toolbar {
        background: var(--cat-table-hdr-gradient);
        color: #ffffff;
        padding: 11px 20px;
        border-radius: 10px 10px 0 0;
        display: flex;
        align-items: center;
        gap: 22px;
        font-size: 12.5px;
        font-weight: 800;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        box-shadow: inset 0 -1px 0 rgba(255, 255, 255, 0.15);
    }

    .cat-toolbar-item {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        cursor: pointer;
        color: rgba(255, 255, 255, 0.9);
        transition: color 0.15s ease;
        user-select: none;
    }

    .cat-toolbar-item:hover {
        color: #ffffff;
    }

    /* Data Table */
    .cat-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid #002266;
        border-top: none;
        border-radius: 0 0 10px 10px;
        overflow: hidden;
    }

    .cat-table thead th {
        background: #002b80;
        color: #ffffff;
        font-size: 12.5px;
        font-weight: 800;
        letter-spacing: 0.3px;
        padding: 12px 16px;
        border-right: 1px solid rgba(255, 255, 255, 0.12);
        border-bottom: 2px solid #001a4d;
        vertical-align: middle;
    }

    .cat-table thead th:last-child {
        border-right: none;
    }

    .cat-table tbody td {
        padding: 14px 16px;
        font-size: 13.5px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f8fafc;
        vertical-align: middle;
        background: #ffffff;
        transition: all 0.15s ease;
    }

    .cat-table tbody td:last-child {
        border-right: none;
    }

    .cat-table tbody tr:hover td {
        background: #f8fbff;
    }

    /* Density Styles */
    .cat-table.density-compact tbody td {
        padding: 7px 12px;
        font-size: 12.5px;
    }

    .cat-table.density-spacious tbody td {
        padding: 20px 18px;
        font-size: 14px;
    }

    /* Empty state */
    .cat-empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
        background: #ffffff;
    }

    /* =========================================================================
       CUSTOM SLIDE-OVER DRAWERS & MODALS (Cross-Device Responsive Architecture)
       ========================================================================= */
    .cat-drawer-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(5px);
        z-index: 99990;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.3s;
    }

    .cat-drawer-overlay.open {
        opacity: 1;
        visibility: visible;
    }

    .cat-drawer {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        max-width: 660px;
        height: 100vh;
        max-height: 100vh;
        background: #ffffff;
        z-index: 99999;
        box-shadow: -14px 0 50px rgba(0, 34, 102, 0.32);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transform: translateX(100%);
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .cat-drawer.open {
        transform: translateX(0);
    }

    .cat-drawer-lg {
        max-width: 860px;
    }

    .cat-drawer-header {
        height: 64px;
        min-height: 64px;
        max-height: 64px;
        background: var(--cat-blue-gradient);
        color: #ffffff;
        padding: 0 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 16px rgba(0, 56, 184, 0.25);
        flex-shrink: 0;
        z-index: 20;
    }

    .cat-drawer-header h5 {
        margin: 0;
        font-size: 17.5px;
        font-weight: 800;
        letter-spacing: -0.2px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #ffffff;
    }

    .cat-drawer-close {
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: #ffffff;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .cat-drawer-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .cat-drawer-step {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
        height: calc(100vh - 64px);
        max-height: calc(100vh - 64px);
        overflow: hidden;
    }

    .cat-drawer-form {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
        height: 100%;
        max-height: 100%;
        margin: 0;
        overflow: hidden;
    }

    .cat-drawer-body {
        padding: 24px;
        overflow-y: auto !important;
        overflow-x: hidden;
        flex: 1 1 auto;
        min-height: 0;
        background: #fbfcfe;
        -webkit-overflow-scrolling: touch;
    }

    /* Custom Scrollbar for Drawer Body */
    .cat-drawer-body::-webkit-scrollbar {
        width: 7px;
    }
    .cat-drawer-body::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    .cat-drawer-body::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 4px;
    }
    .cat-drawer-body::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }

    .cat-drawer-footer {
        height: 72px;
        min-height: 72px;
        max-height: 72px;
        padding: 0 24px;
        background: #ffffff;
        border-top: 1.5px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        flex-shrink: 0;
        position: relative;
        z-index: 20;
        box-shadow: 0 -6px 20px rgba(0, 34, 102, 0.08);
    }

    @media (max-width: 768px) {
        .cat-drawer, .cat-drawer-lg {
            max-width: 100vw !important;
        }
        .cat-drawer-step {
            height: calc(100vh - 58px) !important;
            max-height: calc(100vh - 58px) !important;
        }
        .cat-drawer-body {
            padding: 16px !important;
        }
        .cat-drawer-header {
            padding: 0 16px !important;
            min-height: 58px !important;
            height: 58px !important;
        }
        .cat-drawer-footer {
            padding: 0 16px !important;
            min-height: 64px !important;
            height: 64px !important;
        }
    }

    /* =========================================================================
       FORM CARD CONTAINERS & PREMIUM INPUTS (Add Book & Bulk Upload)
       ========================================================================= */
    .drawer-card-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 20px;
        margin-bottom: 18px;
        box-shadow: 0 2px 8px rgba(0, 56, 184, 0.03);
    }

    .drawer-card-box-title {
        font-size: 13px;
        font-weight: 800;
        color: var(--cat-blue-deep);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 8px;
    }

    /* Mode Pill Radios */
    .cat-mode-selector {
        display: flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 10px;
        gap: 4px;
        margin-bottom: 16px;
    }

    .cat-mode-pill {
        flex: 1;
        text-align: center;
        padding: 9px 12px;
        font-size: 12.5px;
        font-weight: 700;
        color: #64748b;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        user-select: none;
    }

    .cat-mode-pill.active {
        background: #ffffff;
        color: var(--cat-blue-primary);
        box-shadow: 0 2px 6px rgba(0, 56, 184, 0.12);
    }

    /* Custom Form Fields */
    .cat-field-label {
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 5px;
        display: block;
    }

    .cat-form-control {
        border: 1.5px solid #cbd5e1;
        border-radius: 9px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
        background: #ffffff;
        width: 100%;
        outline: none;
        transition: all 0.2s ease;
    }

    .cat-form-control:focus {
        border-color: var(--cat-blue-primary);
        box-shadow: 0 0 0 3px var(--cat-blue-glow);
    }

    .cat-form-select {
        border: 1.5px solid #cbd5e1;
        border-radius: 9px;
        padding: 10px 34px 10px 14px;
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
        background: #ffffff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%230038b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2.2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") no-repeat right 14px center/11px;
        appearance: none;
        width: 100%;
        outline: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .cat-form-select:focus {
        border-color: var(--cat-blue-primary);
        box-shadow: 0 0 0 3px var(--cat-blue-glow);
    }

    /* Bulk Upload Interactive Dropzone */
    .cat-dropzone {
        border: 2px dashed #93c5fd;
        border-radius: 12px;
        padding: 32px 20px;
        text-align: center;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .cat-dropzone:hover, .cat-dropzone.dragover {
        border-color: var(--cat-blue-primary);
        background: #f0f7ff;
        transform: translateY(-2px);
    }

    .cat-dropzone-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--cat-blue-light);
        color: var(--cat-blue-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin-bottom: 12px;
    }

    /* Popover Menus */
    .cat-popover-menu {
        position: absolute;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.18);
        padding: 12px 16px;
        z-index: 100;
        display: none;
        min-width: 220px;
        color: #0f172a;
    }

    .cat-popover-menu.show {
        display: block;
    }

    /* Barcode sticker card */
    .barcode-sticker {
        border: 1.5px dashed #93c5fd;
        border-radius: 10px;
        padding: 14px;
        text-align: center;
        background: #ffffff;
        margin-bottom: 14px;
        box-shadow: 0 2px 8px rgba(0, 56, 184, 0.06);
    }

    /* Toast */
    .cat-toast {
        position: fixed;
        bottom: 28px;
        right: 28px;
        z-index: 999999;
        background: #0f172a;
        color: #ffffff;
        border-left: 4px solid #10b981;
        padding: 14px 22px;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 13.5px;
        font-weight: 600;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
    }

    .cat-toast.show {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }
</style>

<!-- Load JsBarcode for dynamic barcode generation -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<div class="container-fluid px-0 cat-page-wrapper">
    <!-- Top Tabs (Royal Blue & White Match) -->
    <div class="cat-tabs-row">
        <a href="{{ route('school.library.catalogue', ['tab' => 'book_id_wise']) }}" class="cat-tab-btn {{ $activeTab === 'book_id_wise' ? 'active' : 'inactive' }}">
            <i class="fas fa-hashtag"></i> BOOK ID WISE ({{ $totalBooksCount }})
        </a>
        <a href="{{ route('school.library.catalogue', ['tab' => 'accession_wise']) }}" class="cat-tab-btn {{ $activeTab === 'accession_wise' ? 'active' : 'inactive' }}">
            <i class="fas fa-barcode"></i> ACCESSION NO. WISE
        </a>
    </div>

    <!-- Main Container Card -->
    <div class="cat-main-card">
        <!-- Search & Action Deck (Exact match to Image 1) -->
        <form method="GET" action="{{ route('school.library.catalogue') }}" id="catalogueFilterForm">
            <input type="hidden" name="tab" value="{{ $activeTab }}">

            <div class="cat-filter-deck">
                <!-- Search Input -->
                <div class="cat-floating-input-group" style="flex: 2;">
                    <label>Search</label>
                    <input type="text" name="search" id="catalogSearchInput" class="cat-floating-input" value="{{ $search }}" placeholder="Search by Title/Name/ID/Publish/ISBN/Author">
                </div>

                <!-- Material Type Dropdown -->
                <div class="cat-floating-input-group" style="flex: 1.2;">
                    <label>Material Type</label>
                    <select name="material_type" class="cat-floating-select" onchange="this.form.submit()">
                        <option value="">Search Material Type</option>
                        @foreach($bookTypes as $bt)
                        <option value="{{ $bt->id }}" {{ $bookTypeId == $bt->id ? 'selected' : '' }}>{{ $bt->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Section Dropdown -->
                <div class="cat-floating-input-group" style="flex: 1.2;">
                    <label>Library Section</label>
                    <select name="section_id" class="cat-floating-select" onchange="this.form.submit()">
                        <option value="">All Sections</option>
                        @foreach($sections as $sec)
                        <option value="{{ $sec->id }}" {{ $sectionId == $sec->id ? 'selected' : '' }}>{{ $sec->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons (Image 1 Match) -->
                <button type="button" class="btn-cat-outline" onclick="openBarcodeDrawer()">
                    <i class="fas fa-barcode"></i> Generate Barcode
                </button>

                <a href="{{ route('school.library.catalogue.export-csv', request()->query()) }}" class="btn-cat-outline">
                    <i class="fas fa-download"></i> Bulk Book Download
                </a>

                <button type="button" class="btn-cat-outline" onclick="openUploadDrawer()">
                    <i class="fas fa-upload"></i> Bulk Book Upload
                </button>

                <button type="button" class="btn-cat-primary" onclick="openAddBookDrawer()">
                    <i class="fas fa-plus"></i> Add New Book
                </button>

                <a href="{{ route('school.library.catalogue') }}" class="btn-cat-icon" title="Reset Filters & Settings">
                    <i class="fas fa-gear"></i>
                </a>
            </div>
        </form>

        <!-- Table Customization Toolbar (Columns / Filters / Density - Image 1) -->
        <div class="cat-table-toolbar position-relative">
            <!-- Columns dropdown trigger -->
            <div class="cat-toolbar-item" onclick="toggleColumnsMenu(event)">
                <i class="fas fa-table-columns"></i> Columns <i class="fas fa-chevron-down ms-1" style="font-size: 10px;"></i>
            </div>

            <!-- Filters trigger -->
            <div class="cat-toolbar-item" onclick="toggleFilterDrawer(event)">
                <i class="fas fa-filter"></i> Filters
            </div>

            <!-- Density switcher trigger -->
            <div class="cat-toolbar-item" onclick="toggleDensityMenu(event)">
                <i class="fas fa-bars-staggered"></i> Density <i class="fas fa-chevron-down ms-1" style="font-size: 10px;"></i>
            </div>

            <!-- Columns Popover Menu -->
            <div class="cat-popover-menu" id="columnsPopoverMenu" style="top: 42px; left: 18px;">
                <div class="fw-bold mb-2 pb-1 border-bottom small text-uppercase text-muted">Toggle Columns</div>
                <div class="form-check mb-1">
                    <input class="form-check-input col-toggle" type="checkbox" data-col="col-title" checked id="col_chk_title">
                    <label class="form-check-label small" for="col_chk_title">Book Title</label>
                </div>
                <div class="form-check mb-1">
                    <input class="form-check-input col-toggle" type="checkbox" data-col="col-id" checked id="col_chk_id">
                    <label class="form-check-label small" for="col_chk_id">Book ID</label>
                </div>
                <div class="form-check mb-1">
                    <input class="form-check-input col-toggle" type="checkbox" data-col="col-author" checked id="col_chk_author">
                    <label class="form-check-label small" for="col_chk_author">Author</label>
                </div>
                <div class="form-check mb-1">
                    <input class="form-check-input col-toggle" type="checkbox" data-col="col-edition" checked id="col_chk_edition">
                    <label class="form-check-label small" for="col_chk_edition">Edition</label>
                </div>
                <div class="form-check mb-1">
                    <input class="form-check-input col-toggle" type="checkbox" data-col="col-location" checked id="col_chk_location">
                    <label class="form-check-label small" for="col_chk_location">Location / Rack</label>
                </div>
                <div class="form-check mb-1">
                    <input class="form-check-input col-toggle" type="checkbox" data-col="col-copies" checked id="col_chk_copies">
                    <label class="form-check-label small" for="col_chk_copies">Copies (Avail/Total)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input col-toggle" type="checkbox" data-col="col-action" checked id="col_chk_action">
                    <label class="form-check-label small" for="col_chk_action">Action</label>
                </div>
            </div>

            <!-- Density Popover Menu -->
            <div class="cat-popover-menu" id="densityPopoverMenu" style="top: 42px; left: 240px;">
                <div class="fw-bold mb-2 pb-1 border-bottom small text-uppercase text-muted">Table Density</div>
                <button type="button" class="btn btn-sm btn-light w-100 text-start mb-1" onclick="setDensity('compact')">
                    <i class="fas fa-compress me-2 text-primary"></i> Compact
                </button>
                <button type="button" class="btn btn-sm btn-light w-100 text-start mb-1" onclick="setDensity('normal')">
                    <i class="fas fa-table-list me-2 text-primary"></i> Normal (Standard)
                </button>
                <button type="button" class="btn btn-sm btn-light w-100 text-start" onclick="setDensity('spacious')">
                    <i class="fas fa-expand me-2 text-primary"></i> Spacious
                </button>
            </div>
        </div>

        <!-- Data Table (Image 1 Match) -->
        <div class="table-responsive">
            <table class="cat-table" id="catalogueTable">
                <thead>
                    <tr>
                        <th class="col-title" style="width: 28%;">Book Title</th>
                        <th class="col-id" style="width: 14%;">Book ID</th>
                        <th class="col-author" style="width: 16%;">Author</th>
                        <th class="col-edition" style="width: 12%;">Edition</th>
                        <th class="col-location" style="width: 12%;">Location</th>
                        <th class="col-copies" style="width: 10%;">Copies (Available/Total)</th>
                        <th class="col-action" style="width: 8%; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                    <tr id="book-row-{{ $book->id }}">
                        <!-- Book Title -->
                        <td class="col-title">
                            <div class="d-flex align-items-center gap-2">
                                <div class="badge rounded-circle p-2" style="background: var(--cat-blue-light); color: var(--cat-blue-primary); width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="{{ $book->bookType?->icon ?: 'fas fa-book' }}"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 14px;">{{ $book->title }}</div>
                                    <div class="text-muted small" style="font-size: 11.5px;">
                                        <span>ISBN: {{ $book->isbn ?: 'N/A' }}</span>
                                        @if($book->section)
                                        <span class="ms-2 badge" style="background: var(--cat-blue-light); color: var(--cat-blue-primary); border: 1px solid var(--cat-blue-border);">{{ $book->section->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Book ID / Accession No -->
                        <td class="col-id">
                            <span class="badge" style="background: #f8fafc; border: 1px solid #cbd5e1; color: var(--cat-blue-deep); font-family: monospace; font-size: 12px; font-weight: 700;">
                                {{ $activeTab === 'accession_wise' ? ($book->accession_no ?: 'ACC-'.$book->id) : ('BK-'.str_pad($book->id, 5, '0', STR_PAD_LEFT)) }}
                            </span>
                        </td>

                        <!-- Author -->
                        <td class="col-author">
                            <div class="fw-semibold text-dark">{{ $book->author }}</div>
                            @if($book->publisher)
                            <div class="text-muted small" style="font-size: 11.5px;">{{ $book->publisher }}</div>
                            @endif
                        </td>

                        <!-- Edition -->
                        <td class="col-edition">
                            <span class="text-secondary fw-medium">{{ $book->edition ?: 'Standard' }}</span>
                        </td>

                        <!-- Location / Rack -->
                        <td class="col-location">
                            <span class="text-muted small">
                                <i class="fas fa-location-dot text-danger me-1"></i> {{ $book->rack_location ?: ($book->section?->rack_location ?: 'General Bay') }}
                            </span>
                        </td>

                        <!-- Copies Available / Total -->
                        <td class="col-copies">
                            @php
                                $avail = $book->available_copies;
                                $total = $book->total_copies;
                                $badgeClass = $avail == 0 ? 'bg-danger-subtle text-danger' : ($avail < 2 ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success');
                            @endphp
                            <span class="badge {{ $badgeClass }} fw-bold px-2 py-1" style="font-size: 12.5px;">
                                {{ $avail }} / {{ $total }}
                            </span>
                        </td>

                        <!-- Action -->
                        <td class="col-action" style="text-align: right;">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <button type="button" class="btn btn-sm btn-light text-primary" onclick="editBook({{ json_encode($book) }})" title="Edit Book">
                                    <i class="fas fa-pen-to-square"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light text-secondary" onclick="printSingleBarcode({{ json_encode($book) }})" title="Print Barcode">
                                    <i class="fas fa-barcode"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light text-danger" onclick="deleteBook({{ $book->id }}, '{{ addslashes($book->title) }}')" title="Delete Book">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-0">
                            <div class="cat-empty-state">
                                <div class="mb-2"><i class="fas fa-book-bookmark fa-3x text-muted opacity-40"></i></div>
                                <h6 class="fw-bold text-dark">No books found in this school library</h6>
                                <p class="small text-muted mb-3">Add books to your school catalogue or adjust your search filter.</p>
                                <button type="button" class="btn-cat-primary" onclick="openAddBookDrawer()">
                                    <i class="fas fa-plus"></i> Add First Book
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($books->hasPages())
        <div class="d-flex align-items-center justify-content-between pt-3 mt-2 border-top">
            <div class="text-muted small">
                Showing {{ $books->firstItem() ?? 0 }} to {{ $books->lastItem() ?? 0 }} of {{ $books->total() }} books
            </div>
            <div>
                {{ $books->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- =========================================================================
     GLOBAL BACKDROP OVERLAY
     ========================================================================= -->
<div class="cat-drawer-overlay" id="globalDrawerOverlay" onclick="closeAllDrawers()"></div>

<!-- =========================================================================
     1. ADD NEW BOOK SLIDE-OVER DRAWER (2-Step Dynamic Wizard)
     ========================================================================= -->
<div class="cat-drawer" id="addBookDrawer">
    <div class="cat-drawer-header">
        <h5 id="drawerTitle"><i class="fas fa-book-medical"></i> Add New Book</h5>
        <button type="button" class="cat-drawer-close" onclick="closeAllDrawers()">&times;</button>
    </div>

    <!-- STEP 1: BOOK DETAILS FORM -->
    <div id="addBookStep1" class="cat-drawer-step">
        <form id="addBookForm" class="cat-drawer-form">
            @csrf
            <input type="hidden" id="book_id" name="id">

            <div class="cat-drawer-body">
                <!-- Progress banner -->
                <div class="d-flex align-items-center justify-content-between p-2.5 px-3 mb-3 rounded-3" style="background: #eff6ff; border: 1px solid #bfdbfe; flex-shrink: 0;">
                    <div class="fw-bold text-primary small d-flex align-items-center gap-2">
                        <span class="badge bg-primary px-2 py-1" style="font-size: 10px; letter-spacing: 0.5px;">STEP 1</span>
                        <span>Book Details & Placement</span>
                    </div>
                    <span class="text-muted small fw-semibold">Step 1 of 2</span>
                </div>

                <!-- Primary Book Details Card -->
                <div class="drawer-card-box">
                    <div class="drawer-card-box-title">
                        <i class="fas fa-book-open text-primary"></i> Book Information
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="cat-field-label">Book Title <span class="text-danger">*</span></label>
                            <input type="text" id="b_title" name="title" class="cat-form-control" placeholder="e.g. Concepts of Physics (Vol 1)" required>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="cat-field-label">Author Name <span class="text-danger">*</span></label>
                            <input type="text" id="b_author" name="author" class="cat-form-control" placeholder="e.g. H.C. Verma" required>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="cat-field-label">Accession Number</label>
                            <input type="text" id="b_accession" name="accession_no" class="cat-form-control" placeholder="Auto-generated if empty">
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="cat-field-label">ISBN / ISSN Code</label>
                            <input type="text" id="b_isbn" name="isbn" class="cat-form-control" placeholder="e.g. 978-81-7709-187-7">
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="cat-field-label">Edition / Volume</label>
                            <input type="text" id="b_edition" name="edition" class="cat-form-control" placeholder="e.g. 1st Edition">
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="cat-field-label">Publisher</label>
                            <input type="text" id="b_publisher" name="publisher" class="cat-form-control" placeholder="e.g. Bharati Bhawan">
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="cat-field-label">Publication Year</label>
                            <input type="text" id="b_year" name="publication_year" class="cat-form-control" placeholder="e.g. 2024">
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="cat-field-label">Language</label>
                            <input type="text" id="b_language" name="language" class="cat-form-control" value="English">
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="cat-field-label">Page Count</label>
                            <input type="number" id="b_pages" name="pages" class="cat-form-control" placeholder="e.g. 462" min="1">
                        </div>
                    </div>
                </div>

                <!-- Library Placement & Inventory Card -->
                <div class="drawer-card-box">
                    <div class="drawer-card-box-title">
                        <i class="fas fa-boxes-stacked text-primary"></i> Placement & Inventory
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 col-12">
                            <label class="cat-field-label d-flex align-items-center justify-content-between">
                                <span>Library Section <span class="text-danger">*</span></span>
                                <a href="{{ route('school.library.basics') }}" target="_blank" class="text-primary text-decoration-none fw-bold" style="font-size: 11px;"><i class="fas fa-plus"></i> New Section</a>
                            </label>
                            <select id="b_section" name="section_id" class="cat-form-select" required>
                                <option value="">Choose Library Section</option>
                                @foreach($sections as $sec)
                                <option value="{{ $sec->id }}">{{ $sec->name }} ({{ $sec->rack_location ?: 'General' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="cat-field-label d-flex align-items-center justify-content-between">
                                <span>Material / Book Type <span class="text-danger">*</span></span>
                                <a href="{{ route('school.library.basics') }}" target="_blank" class="text-primary text-decoration-none fw-bold" style="font-size: 11px;"><i class="fas fa-plus"></i> New Type</a>
                            </label>
                            <select id="b_type" name="book_type_id" class="cat-form-select" required>
                                <option value="">Choose Material Type</option>
                                @foreach($bookTypes as $bt)
                                <option value="{{ $bt->id }}">{{ $bt->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="cat-field-label">Rack / Shelf Location</label>
                            <input type="text" id="b_rack" name="rack_location" class="cat-form-control" placeholder="e.g. Rack A1-Shelf 2">
                        </div>

                        <div class="col-md-3 col-6">
                            <label class="cat-field-label">Price (₹)</label>
                            <input type="number" step="0.5" id="b_price" name="price" class="cat-form-control" value="0.00" min="0">
                        </div>

                        <div class="col-md-3 col-6">
                            <label class="cat-field-label">Total Copies <span class="text-danger">*</span></label>
                            <input type="number" id="b_copies" name="total_copies" class="cat-form-control" value="1" min="1" required>
                        </div>

                        <div class="col-12">
                            <label class="cat-field-label">Description / Remarks</label>
                            <textarea id="b_description" name="description" class="cat-form-control" rows="2" placeholder="Add book summary or condition notes..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cat-drawer-footer">
                <button type="button" class="btn btn-outline-secondary px-4 fw-bold" onclick="closeAllDrawers()" style="border-radius: 8px;">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary px-4 fw-bold" id="btnSaveBook" style="background: var(--cat-blue-gradient); border: none; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 56, 184, 0.25); display: inline-flex; align-items: center; gap: 8px;">
                    Next: Save & Generate Barcode <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- STEP 2: GENERATED BARCODE & BOOK DETAILS -->
    <div id="addBookStep2" class="cat-drawer-step" style="display: none;">
        <div class="cat-drawer-body text-center p-4">
            <!-- Progress banner -->
            <div class="d-flex align-items-center justify-content-between p-2 px-3 mb-3 rounded-3 text-start" style="background: #ecfdf5; border: 1px solid #a7f3d0;">
                <div class="fw-bold text-success small"><i class="fas fa-check-circle me-1"></i> Step 2: Book Saved & Barcode Ready</div>
                <span class="badge bg-success px-2 py-1">Step 2 of 2</span>
            </div>

            <div class="mb-3">
                <div class="badge rounded-circle p-3 mb-2" style="background: #dcfce7; color: #16a34a; width: 60px; height: 60px; display: inline-flex; align-items: center; justify-content: center; font-size: 24px;">
                    <i class="fas fa-check"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1" id="successBookTitle">Book Saved Successfully!</h5>
                <p class="text-muted small mb-0">Code-128 Barcode sticker generated for your school library inventory.</p>
            </div>

            <!-- Key Identifiers -->
            <div class="d-flex justify-content-center gap-2 mb-3">
                <span class="badge bg-light text-dark border p-2 px-3 fw-bold" style="font-size: 13px;" id="badgeBookId">
                    <i class="fas fa-hashtag text-primary me-1"></i> ID: BK-00001
                </span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle p-2 px-3 fw-bold" style="font-size: 13px;" id="badgeAccessionNo">
                    <i class="fas fa-barcode me-1"></i> ACC-00001
                </span>
            </div>

            <!-- Generated Barcode Sticker Card -->
            <div class="barcode-sticker p-4 mx-auto my-3" id="singleCreatedBarcodeCard" style="max-width: 480px; background: #ffffff; border: 2px dashed #93c5fd; border-radius: 12px; box-shadow: 0 4px 16px rgba(0, 56, 184, 0.08);">
                <div class="fw-bold text-primary mb-1" style="font-size: 16px;" id="createdStickerTitle">Book Title</div>
                <div class="text-muted small mb-2" id="createdStickerAuthor">Author Name</div>
                
                <div class="py-2">
                    <svg id="createdBarcodeSvg" style="max-width: 100%; height: 55px;"></svg>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-2 border-top text-muted small mt-2" style="font-size: 11.5px;">
                    <span id="createdStickerSection"><i class="fas fa-layer-group text-primary me-1"></i> Section</span>
                    <span id="createdStickerRack"><i class="fas fa-location-dot text-danger me-1"></i> Rack</span>
                    <span id="createdStickerCopies" class="badge bg-primary-subtle text-primary">Copies: 1</span>
                </div>
            </div>

            <div class="d-flex flex-column gap-2 max-w-sm mx-auto" style="max-width: 380px;">
                <button type="button" class="btn btn-primary py-2.5 fw-bold" onclick="printCreatedBarcode()" style="background: var(--cat-blue-gradient); border: none; border-radius: 8px; font-size: 14px;">
                    <i class="fas fa-print me-2"></i> Print Barcode Sticker
                </button>
                <button type="button" class="btn btn-outline-primary py-2.5 fw-bold" onclick="resetFormForNextBook()" style="border-radius: 8px; font-size: 13.5px;">
                    <i class="fas fa-plus me-1"></i> Add Next Book
                </button>
            </div>
        </div>

        <div class="cat-drawer-footer">
            <button type="button" class="btn btn-light px-4 fw-bold w-100" onclick="finishAndReload()" style="border-radius: 8px; border: 1.5px solid #cbd5e1;">
                <i class="fas fa-check-double text-success me-1"></i> Done & View in Catalogue
            </button>
        </div>
    </div>
</div>

<!-- =========================================================================
     2. BULK BOOK UPLOAD SLIDE-OVER DRAWER (Polished & Dynamic)
     ========================================================================= -->
<div class="cat-drawer" id="bulkUploadDrawer">
    <div class="cat-drawer-header">
        <h5><i class="fas fa-cloud-arrow-up"></i> Bulk Book Upload</h5>
        <button type="button" class="cat-drawer-close" onclick="closeAllDrawers()">&times;</button>
    </div>

    <form id="bulkUploadForm" class="cat-drawer-form" enctype="multipart/form-data">
        @csrf
        <div class="cat-drawer-body">
            <!-- Sample Template Banner -->
            <div class="p-3 mb-4 rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: var(--cat-blue-light); border-color: var(--cat-blue-border) !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="badge rounded-circle p-3" style="background: #ffffff; color: var(--cat-blue-primary); width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0, 56, 184, 0.1);">
                        <i class="fas fa-file-csv fa-lg"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 13.5px;">Download Sample CSV Template</div>
                        <div class="text-muted" style="font-size: 12px;">Pre-formatted with example rows & correct header names</div>
                    </div>
                </div>
                <a href="{{ route('school.library.catalogue.sample-csv') }}" class="btn btn-sm btn-primary fw-bold px-3 py-2" style="background: var(--cat-blue-gradient); border: none; border-radius: 8px;">
                    <i class="fas fa-download me-1"></i> Sample CSV
                </a>
            </div>

            <!-- Drag & Drop Upload Zone -->
            <div class="drawer-card-box">
                <label class="cat-field-label mb-2">Select Spreadsheet (.CSV or .TXT) <span class="text-danger">*</span></label>
                
                <div class="cat-dropzone" id="csvDropZone" onclick="document.getElementById('csv_file_input').click()">
                    <div class="cat-dropzone-icon">
                        <i class="fas fa-cloud-arrow-up"></i>
                    </div>
                    <div class="fw-bold text-dark" style="font-size: 14.5px;">Click to browse or drop CSV file here</div>
                    <div class="text-muted small mt-1" id="selectedFileName">Standard CSV files supported (Max: 10MB)</div>
                    <input type="file" id="csv_file_input" name="csv_file" class="d-none" accept=".csv, .txt" required onchange="handleFileSelect(this)">
                </div>
            </div>

            <!-- Guidance Info -->
            <div class="drawer-card-box" style="background: #f8fafc;">
                <div class="drawer-card-box-title" style="color: #334155;">
                    <i class="fas fa-table-columns text-primary"></i> Supported Column Headers
                </div>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-white text-dark border px-2 py-1">Title *</span>
                    <span class="badge bg-white text-dark border px-2 py-1">Author *</span>
                    <span class="badge bg-white text-dark border px-2 py-1">Accession_No</span>
                    <span class="badge bg-white text-dark border px-2 py-1">ISBN</span>
                    <span class="badge bg-white text-dark border px-2 py-1">Section</span>
                    <span class="badge bg-white text-dark border px-2 py-1">Material_Type</span>
                    <span class="badge bg-white text-dark border px-2 py-1">Edition</span>
                    <span class="badge bg-white text-dark border px-2 py-1">Publisher</span>
                    <span class="badge bg-white text-dark border px-2 py-1">Publication_Year</span>
                    <span class="badge bg-white text-dark border px-2 py-1">Rack_Location</span>
                    <span class="badge bg-white text-dark border px-2 py-1">Price</span>
                    <span class="badge bg-white text-dark border px-2 py-1">Total_Copies</span>
                </div>
                <div class="small text-muted" style="font-size: 11.5px; line-height: 1.5;">
                    <i class="fas fa-shield-halved text-primary me-1"></i> All uploaded books are strictly isolated and saved into your current school's private database.
                </div>
            </div>
        </div>

        <div class="cat-drawer-footer">
            <button type="button" class="btn btn-outline-secondary px-4 fw-bold" onclick="closeAllDrawers()" style="border-radius: 8px;">
                Cancel
            </button>
            <button type="submit" class="btn btn-primary px-4 fw-bold" id="btnUploadSubmit" style="background: var(--cat-blue-gradient); border: none; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 56, 184, 0.25);">
                <i class="fas fa-cloud-arrow-up me-1"></i> Upload & Import Books
            </button>
        </div>
    </form>
</div>

<!-- =========================================================================
     3. GENERATE BARCODES SLIDE-OVER DRAWER
     ========================================================================= -->
<div class="cat-drawer cat-drawer-lg" id="barcodeDrawer">
    <div class="cat-drawer-header">
        <h5><i class="fas fa-barcode text-warning"></i> School Library Barcode Generator</h5>
        <button type="button" class="cat-drawer-close" onclick="closeAllDrawers()">&times;</button>
    </div>

    <div class="cat-drawer-body" id="barcodePrintArea">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2 flex-wrap gap-2">
            <div>
                <div class="fw-bold text-dark" style="font-size: 14px;">Barcode Label Preview</div>
                <div class="text-muted small" style="font-size: 11.5px;">Code-128 standard barcodes ready for label sticker printing</div>
            </div>
            <button type="button" class="btn btn-sm btn-primary px-3 fw-bold" onclick="printBarcodeSheet()" style="background: var(--cat-blue-gradient); border: none; border-radius: 7px;">
                <i class="fas fa-print me-1"></i> Print All Labels
            </button>
        </div>

        <div class="row g-3" id="barcodeListContainer">
            <!-- Populated dynamically via JS -->
        </div>
    </div>

    <div class="cat-drawer-footer">
        <button type="button" class="btn btn-outline-secondary px-4 fw-bold" onclick="closeAllDrawers()" style="border-radius: 8px;">
            Close
        </button>
        <button type="button" class="btn btn-primary px-4 fw-bold" onclick="printBarcodeSheet()" style="background: var(--cat-blue-gradient); border: none; border-radius: 8px;">
            <i class="fas fa-print me-1"></i> Print Labels
        </button>
    </div>
</div>

<!-- Dynamic Toast -->
<div id="catToast" class="cat-toast">
    <i class="fas fa-circle-check text-success fa-lg"></i>
    <span id="catToastMsg">Saved successfully!</span>
</div>

<!-- =========================================================================
     CLIENT-SIDE JAVASCRIPT LOGIC (Drawer Controls & Barcode Engine)
     ========================================================================= -->
<script>
    // TOAST NOTIFICATIONS
    function showToast(msg, isError = false) {
        const toast = document.getElementById('catToast');
        const msgEl = document.getElementById('catToastMsg');
        msgEl.innerText = msg;
        if (isError) {
            toast.style.borderLeftColor = '#ef4444';
            toast.querySelector('i').className = 'fas fa-circle-xmark text-danger fa-lg';
        } else {
            toast.style.borderLeftColor = '#10b981';
            toast.querySelector('i').className = 'fas fa-circle-check text-success fa-lg';
        }
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3500);
    }

    // DRAWER CONTROLLER
    function closeAllDrawers() {
        document.getElementById('globalDrawerOverlay').classList.remove('open');
        document.getElementById('addBookDrawer').classList.remove('open');
        document.getElementById('bulkUploadDrawer').classList.remove('open');
        document.getElementById('barcodeDrawer').classList.remove('open');
    }

    let lastCreatedBook = null;

    // 1. ADD / EDIT BOOK DRAWER
    function openAddBookDrawer() {
        closeAllDrawers();
        document.getElementById('addBookForm').reset();
        document.getElementById('book_id').value = '';
        
        // Ensure Step 1 is displayed, Step 2 hidden
        document.getElementById('addBookStep1').style.display = 'flex';
        document.getElementById('addBookStep2').style.display = 'none';

        document.getElementById('drawerTitle').innerHTML = '<i class="fas fa-book-medical"></i> Add New Book';
        document.getElementById('globalDrawerOverlay').classList.add('open');
        document.getElementById('addBookDrawer').classList.add('open');
        
        setTimeout(() => document.getElementById('b_title').focus(), 200);
    }

    function editBook(b) {
        closeAllDrawers();
        document.getElementById('book_id').value = b.id;
        document.getElementById('b_title').value = b.title || '';
        document.getElementById('b_author').value = b.author || '';
        document.getElementById('b_accession').value = b.accession_no || '';
        document.getElementById('b_isbn').value = b.isbn || '';
        document.getElementById('b_section').value = b.section_id || '';
        document.getElementById('b_type').value = b.book_type_id || '';
        document.getElementById('b_edition').value = b.edition || '';
        document.getElementById('b_publisher').value = b.publisher || '';
        document.getElementById('b_year').value = b.publication_year || '';
        document.getElementById('b_rack').value = b.rack_location || '';
        document.getElementById('b_price').value = b.price || '0.00';
        document.getElementById('b_copies').value = b.total_copies || 1;
        document.getElementById('b_language').value = b.language || 'English';
        document.getElementById('b_pages').value = b.pages || '';
        document.getElementById('b_description').value = b.description || '';

        // Ensure Step 1 is displayed, Step 2 hidden
        document.getElementById('addBookStep1').style.display = 'flex';
        document.getElementById('addBookStep2').style.display = 'none';

        document.getElementById('drawerTitle').innerHTML = '<i class="fas fa-pen-to-square"></i> Edit Book: ' + b.title;
        document.getElementById('globalDrawerOverlay').classList.add('open');
        document.getElementById('addBookDrawer').classList.add('open');
    }

    document.getElementById('addBookForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('book_id').value;
        const btn = document.getElementById('btnSaveBook');
        const origHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> SAVING & GENERATING BARCODE...';
        btn.disabled = true;

        const url = id ? `{{ url('/school/library/catalogue/books') }}/${id}/update` : '{{ route("school.library.catalogue.books.store") }}';
        const formData = new FormData(this);

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = origHTML;
            btn.disabled = false;
            if (data.success) {
                const book = data.data;
                lastCreatedBook = book;
                
                // Populate Instant Barcode Screen
                document.getElementById('createdStickerTitle').innerText = book.title;
                document.getElementById('createdStickerAuthor').innerText = book.author || 'Author';
                document.getElementById('createdStickerSection').innerHTML = '<i class="fas fa-layer-group text-primary me-1"></i> ' + (book.section ? book.section.name : 'General');
                document.getElementById('createdStickerRack').innerHTML = '<i class="fas fa-location-dot text-danger me-1"></i> ' + (book.rack_location || 'Rack General');
                document.getElementById('createdStickerCopies').innerText = 'Copies: ' + (book.total_copies || 1);

                document.getElementById('badgeBookId').innerHTML = '<i class="fas fa-hashtag text-primary me-1"></i> ID: BK-' + String(book.id).padStart(5, '0');
                document.getElementById('badgeAccessionNo').innerHTML = '<i class="fas fa-barcode me-1"></i> ' + (book.accession_no || ('ACC-' + book.id));

                // Render Barcode
                const codeVal = book.accession_no || ('BK-' + String(book.id).padStart(5, '0'));
                renderBarcodeSvgElement('createdBarcodeSvg', codeVal);

                // Transition Step 1 -> Step 2
                document.getElementById('addBookStep1').style.display = 'none';
                document.getElementById('addBookStep2').style.display = 'flex';
                document.getElementById('drawerTitle').innerHTML = '<i class="fas fa-circle-check text-success"></i> Barcode & ID Ready';

                showToast(data.message || 'Book saved & barcode generated!');
            } else {
                showToast(data.message || 'Error saving book.', true);
            }
        })
        .catch(err => {
            btn.innerHTML = origHTML;
            btn.disabled = false;
            showToast('Network error saving book.', true);
        });
    });

    function printCreatedBarcode() {
        if (!lastCreatedBook) return;
        const printContent = document.getElementById('singleCreatedBarcodeCard').outerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Print Book Barcode - ${lastCreatedBook.title}</title>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
                    <style>
                        body { padding: 30px; font-family: sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
                        .barcode-sticker { border: 2px dashed #0038b8; border-radius: 10px; padding: 20px; text-align: center; max-width: 400px; width: 100%; }
                    </style>
                </head>
                <body onload="window.print();window.close();">
                    ${printContent}
                </body>
            </html>
        `);
        printWindow.document.close();
    }

    function resetFormForNextBook() {
        document.getElementById('addBookForm').reset();
        document.getElementById('book_id').value = '';
        document.getElementById('addBookStep1').style.display = 'flex';
        document.getElementById('addBookStep2').style.display = 'none';
        document.getElementById('drawerTitle').innerHTML = '<i class="fas fa-book-medical"></i> Add New Book';
        setTimeout(() => document.getElementById('b_title').focus(), 150);
    }

    function finishAndReload() {
        closeAllDrawers();
        window.location.reload();
    }

    function deleteBook(id, title) {
        if (!confirm(`Are you sure you want to delete "${title}" from your catalogue?`)) return;

        fetch(`{{ url('/school/library/catalogue/books') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById(`book-row-${id}`);
                if (row) row.remove();
                showToast(data.message);
            } else {
                showToast(data.message, true);
            }
        })
        .catch(err => {
            showToast('Error deleting book.', true);
        });
    }

    // 2. BULK UPLOAD DRAWER
    function openUploadDrawer() {
        closeAllDrawers();
        document.getElementById('bulkUploadForm').reset();
        document.getElementById('selectedFileName').innerText = 'Standard CSV files supported (Max: 10MB)';
        document.getElementById('globalDrawerOverlay').classList.add('open');
        document.getElementById('bulkUploadDrawer').classList.add('open');
    }

    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            document.getElementById('selectedFileName').innerHTML = '<span class="text-success fw-bold"><i class="fas fa-circle-check me-1"></i> Selected: ' + input.files[0].name + ' (' + (input.files[0].size / 1024).toFixed(1) + ' KB)</span>';
        }
    }

    // Drag and drop event listeners
    const dropZone = document.getElementById('csvDropZone');
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        }, false);
    });
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
        }, false);
    });
    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length) {
            document.getElementById('csv_file_input').files = files;
            handleFileSelect(document.getElementById('csv_file_input'));
        }
    });

    document.getElementById('bulkUploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('btnUploadSubmit');
        const origHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Importing...';
        btn.disabled = true;

        const formData = new FormData(this);

        fetch('{{ route("school.library.catalogue.bulk-upload") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = origHTML;
            btn.disabled = false;
            if (data.success) {
                closeAllDrawers();
                showToast(data.message);
                setTimeout(() => window.location.reload(), 800);
            } else {
                showToast(data.message || 'Error during bulk upload.', true);
            }
        })
        .catch(err => {
            btn.innerHTML = origHTML;
            btn.disabled = false;
            showToast('Network error during upload.', true);
        });
    });

    // 3. BARCODE GENERATION ENGINE
    function renderBarcodeSvgElement(elemId, text) {
        if (typeof JsBarcode === 'function') {
            try {
                JsBarcode('#' + elemId, text, {
                    format: "CODE128",
                    height: 38,
                    width: 1.5,
                    fontSize: 11,
                    displayValue: true
                });
                return;
            } catch (e) {
                console.warn('JsBarcode render fallback:', e);
            }
        }
        const svg = document.getElementById(elemId);
        if (svg) {
            svg.setAttribute('viewBox', '0 0 200 45');
            svg.innerHTML = `
                <rect width="100%" height="32" fill="#000000"/>
                <text x="50%" y="42" font-size="10" font-family="monospace" text-anchor="middle" fill="#000000">${text}</text>
            `;
        }
    }

    function openBarcodeDrawer() {
        closeAllDrawers();
        const container = document.getElementById('barcodeListContainer');
        container.innerHTML = '';

        const booksData = @json($books->items());

        if (!booksData || booksData.length === 0) {
            container.innerHTML = '<div class="col-12 text-center text-muted py-5">No books available in your school library to generate barcodes.</div>';
        } else {
            booksData.forEach((b, idx) => {
                const codeValue = b.accession_no || ('BK-' + String(b.id).padStart(5, '0'));
                const col = document.createElement('div');
                col.className = 'col-md-6 col-12';
                col.innerHTML = `
                    <div class="barcode-sticker">
                        <div class="fw-bold small text-truncate mb-1 text-primary" title="${b.title}">${b.title}</div>
                        <svg id="barcode-svg-${idx}" style="max-width: 100%; height: 50px;"></svg>
                        <div class="text-muted mt-1" style="font-size: 11px;">${b.author || ''} • Rack: ${b.rack_location || 'General'}</div>
                    </div>
                `;
                container.appendChild(col);

                setTimeout(() => {
                    renderBarcodeSvgElement(`barcode-svg-${idx}`, codeValue);
                }, 40 * (idx % 10));
            });
        }

        document.getElementById('globalDrawerOverlay').classList.add('open');
        document.getElementById('barcodeDrawer').classList.add('open');
    }

    function printSingleBarcode(b) {
        closeAllDrawers();
        const container = document.getElementById('barcodeListContainer');
        container.innerHTML = '';
        const codeValue = b.accession_no || ('BK-' + String(b.id).padStart(5, '0'));
        const col = document.createElement('div');
        col.className = 'col-md-8 mx-auto col-12';
        col.innerHTML = `
            <div class="barcode-sticker p-4">
                <div class="fw-bold mb-2 text-primary" style="font-size: 16px;">${b.title}</div>
                <svg id="barcode-single-svg" style="max-width: 100%; height: 60px;"></svg>
                <div class="text-muted mt-2 small">${b.author || ''} • Loc: ${b.rack_location || 'General'}</div>
            </div>
        `;
        container.appendChild(col);

        setTimeout(() => {
            renderBarcodeSvgElement('barcode-single-svg', codeValue);
        }, 50);

        document.getElementById('globalDrawerOverlay').classList.add('open');
        document.getElementById('barcodeDrawer').classList.add('open');
    }

    function printBarcodeSheet() {
        const printContent = document.getElementById('barcodePrintArea').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Print School Library Barcodes</title>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
                    <style>
                        body { padding: 20px; font-family: sans-serif; }
                        .barcode-sticker { border: 1px dashed #0038b8; border-radius: 6px; padding: 10px; text-align: center; margin-bottom: 12px; page-break-inside: avoid; }
                        @media print { .btn { display: none !important; } }
                    </style>
                </head>
                <body onload="window.print();window.close();">
                    ${printContent}
                </body>
            </html>
        `);
        printWindow.document.close();
    }

    // 4. COLUMNS TOGGLE & DENSITY
    function toggleColumnsMenu(e) {
        e.stopPropagation();
        document.getElementById('densityPopoverMenu').classList.remove('show');
        document.getElementById('columnsPopoverMenu').classList.toggle('show');
    }

    function toggleDensityMenu(e) {
        e.stopPropagation();
        document.getElementById('columnsPopoverMenu').classList.remove('show');
        document.getElementById('densityPopoverMenu').classList.toggle('show');
    }

    function toggleFilterDrawer(e) {
        document.getElementById('catalogSearchInput').focus();
    }

    document.querySelectorAll('.col-toggle').forEach(chk => {
        chk.addEventListener('change', function() {
            const colClass = this.dataset.col;
            const cells = document.querySelectorAll(`.${colClass}`);
            cells.forEach(c => c.style.display = this.checked ? '' : 'none');
        });
    });

    function setDensity(level) {
        const table = document.getElementById('catalogueTable');
        table.classList.remove('density-compact', 'density-spacious');
        if (level === 'compact') table.classList.add('density-compact');
        if (level === 'spacious') table.classList.add('density-spacious');
        document.getElementById('densityPopoverMenu').classList.remove('show');
    }

    document.addEventListener('click', function() {
        document.getElementById('columnsPopoverMenu').classList.remove('show');
        document.getElementById('densityPopoverMenu').classList.remove('show');
    });

    function handleQuickLookup(val) {
        if (!val || val.length < 2) return;
        const booksData = @json($books->items());
        const match = booksData.find(b => 
            (b.accession_no && b.accession_no.toLowerCase() === val.toLowerCase()) || 
            (b.isbn && b.isbn.toLowerCase() === val.toLowerCase()) ||
            ('bk-' + String(b.id).padStart(5, '0')).toLowerCase() === val.toLowerCase()
        );
        if (match) {
            editBook(match);
        }
    }

    function triggerLookupAdd() {
        const val = document.getElementById('drawerLookupInput').value.trim();
        if (!val) {
            showToast('Please enter a Book ID or Barcode to lookup or add.', true);
            return;
        }
        const booksData = @json($books->items());
        const match = booksData.find(b => 
            (b.accession_no && b.accession_no.toLowerCase() === val.toLowerCase()) || 
            (b.isbn && b.isbn.toLowerCase() === val.toLowerCase()) ||
            ('bk-' + String(b.id).padStart(5, '0')).toLowerCase() === val.toLowerCase() ||
            String(b.id) === val
        );
        if (match) {
            editBook(match);
            showToast('Found and loaded book: ' + match.title);
        } else {
            const isBarcode = document.getElementById('mode_pill_barcode').classList.contains('active');
            if (isBarcode) {
                document.getElementById('b_isbn').value = val;
            } else {
                document.getElementById('b_accession').value = val;
            }
            document.getElementById('b_title').focus();
            showToast('Code "' + val + '" populated. Fill remaining details and click "+ ADD BOOK"');
        }
    }
</script>
@endsection

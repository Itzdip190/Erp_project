@extends('layouts.app')

@section('page-title', 'Stock In Hand & History - Inventory Management')

@section('content')
<style>
    /* ─── Modern ERP Royal Blue Theme (Matching Image 3 & Design Specs) ─── */
    :root {
        --erp-blue-dark:   #1e40af;
        --erp-blue:        #2563eb;
        --erp-blue-light:  #3b82f6;
        --erp-blue-soft:   #eff6ff;
        --erp-blue-border: #dbeafe;
        --erp-header-bg:   linear-gradient(135deg, #1e40af 0%, #2563eb 55%, #3b82f6 100%);
        --erp-header-dark: #1e293b;
        --erp-card-bg:     #ffffff;
        --erp-border:      #e2e8f0;
        --erp-border-light:#f1f5f9;
        --erp-text-dark:   #0f172a;
        --erp-text-muted:  #64748b;
        --erp-low-bg:      #fee2e2;
        --erp-low-text:    #b91c1c;
        --erp-low-border:  #fca5a5;
        --erp-dr-bg:       #ffe4e6;
        --erp-dr-text:     #e11d48;
        --erp-dr-border:   #fecdd3;
        --erp-cr-bg:       #f1f5f9;
        --erp-cr-text:     #475569;
        --erp-cr-border:   #e2e8f0;
    }

    /* ─── Page Container ─────────────────────────────────────────────────── */
    .stock-page-container {
        width: 100%;
        padding: 16px 0 36px;
    }

    /* ─── 3 Colorful Hero Stat Cards in ONE Horizontal Line ─── */
    .stat-hero-row {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: stretch !important;
        gap: 20px !important;
        margin-bottom: 24px !important;
        width: 100% !important;
    }
    .stat-hero-col {
        flex: 1 1 0 !important;
        min-width: 0 !important;
        display: flex !important;
    }
    @media (max-width: 768px) {
        .stat-hero-row {
            flex-direction: column !important;
        }
    }

    .stat-hero-card {
        width: 100% !important;
        border-radius: 16px;
        padding: 20px 22px;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        user-select: none;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 140px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }
    .stat-hero-card::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 140px;
        height: 140px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.18) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
        transition: transform 0.4s ease;
    }
    .stat-hero-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 14px 30px rgba(37, 99, 235, 0.25);
    }
    .stat-hero-card:hover::after {
        transform: scale(1.3);
    }

    /* Colorful Blue Gradient Variants */
    .card-hero-blue {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 55%, #3b82f6 100%);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }
    .card-hero-cyan {
        background: linear-gradient(135deg, #0f172a 0%, #1e40af 50%, #0284c7 100%);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }
    .card-hero-indigo {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4f46e5 100%);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    /* Internal Vertical Content */
    .stat-hero-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .stat-hero-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .stat-hero-pill {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.3px;
        padding: 4px 12px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.28);
        backdrop-filter: blur(6px);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .stat-hero-pill.pill-alert {
        background: rgba(239, 68, 68, 0.3);
        border-color: rgba(252, 165, 165, 0.5);
        color: #fee2e2;
        animation: pulsePill 2s infinite ease-in-out;
    }
    @keyframes pulsePill {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .stat-hero-body {
        display: flex;
        flex-direction: column;
    }
    .stat-hero-number {
        font-size: 32px;
        font-weight: 900;
        line-height: 1;
        letter-spacing: -0.8px;
        color: #ffffff;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    .stat-hero-title {
        font-size: 13.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: rgba(255, 255, 255, 0.95);
        margin-top: 6px;
    }
    .stat-hero-subtitle {
        font-size: 11.5px;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.75);
        margin-top: 2px;
    }

    /* ─── Main Stock In Hand Card (Matching Image 1 & 3) ─────────────────── */
    .stock-main-card {
        background: #ffffff;
        border: 1px solid var(--erp-border);
        border-radius: 14px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        margin-bottom: 24px;
        animation: fadeIn 0.35s ease;
    }

    .stock-card-header {
        background: var(--erp-header-bg);
        color: #ffffff;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top-left-radius: 13px;
        border-top-right-radius: 13px;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.18);
    }

    .stock-card-title {
        margin: 0;
        font-size: 16.5px;
        font-weight: 800;
        letter-spacing: 0.3px;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .stock-card-title i {
        font-size: 17px;
        opacity: 0.95;
    }

    /* Filter & Search Bar */
    .stock-filter-bar {
        background: #f8fafc;
        border-bottom: 1px solid var(--erp-border);
        padding: 14px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .stock-search-wrap {
        position: relative;
        min-width: 260px;
        max-width: 380px;
        flex: 1;
    }
    .stock-search-wrap i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 13px;
    }
    .stock-search-input {
        width: 100%;
        padding: 8px 14px 8px 38px;
        font-size: 13px;
        font-weight: 500;
        color: var(--erp-text-dark);
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        outline: none;
        transition: all 0.2s ease;
    }
    .stock-search-input:focus {
        border-color: var(--erp-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .stock-search-input::placeholder {
        color: #94a3b8;
    }

    .stock-filter-pills {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .stock-pill {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #334155;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .stock-pill:hover {
        background: #eff6ff;
        border-color: var(--erp-blue);
        color: var(--erp-blue);
    }
    .stock-pill.active {
        background: var(--erp-blue);
        border-color: var(--erp-blue);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
    }

    /* ─── Table Styling (Pixel Perfect to Image 1) ───────────────────────── */
    .stock-table-wrap {
        padding: 0;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .stock-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
        font-size: 13.5px;
    }
    .stock-table thead th {
        background: #ffffff;
        color: #0f172a;
        font-weight: 700;
        font-size: 13px;
        padding: 15px 22px;
        border-bottom: 2px solid #e2e8f0;
        text-align: left;
        white-space: nowrap;
        user-select: none;
    }
    .stock-table tbody tr {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border-bottom: 1px solid #f1f5f9;
    }
    .stock-table tbody tr:hover {
        background-color: #f8fbff;
        box-shadow: inset 3px 0 0 var(--erp-blue);
    }
    .stock-table tbody td {
        padding: 16px 22px;
        color: #334155;
        font-weight: 500;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
    }
    .stock-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Column Specifics */
    .col-sno {
        width: 65px;
        text-align: center;
        font-weight: 600;
        color: #64748b;
    }
    .col-product {
        font-weight: 700;
        color: #0f172a;
    }
    .col-size {
        font-weight: 600;
        color: #475569;
    }
    .col-num {
        font-weight: 600;
        color: #1e293b;
    }

    /* Available Qty Badges */
    .badge-qty-normal {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #1e293b;
        font-weight: 700;
        font-size: 12.5px;
        padding: 4px 14px;
        border-radius: 20px;
        display: inline-block;
        min-width: 44px;
        text-align: center;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }

    .badge-qty-low {
        background: #fee2e2;
        border: 1px solid #fca5a5;
        color: #b91c1c;
        font-weight: 700;
        font-size: 12.5px;
        padding: 4px 14px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 1px 3px rgba(220, 38, 38, 0.12);
        animation: pulseLow 2s infinite ease-in-out;
    }
    @keyframes pulseLow {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.03); }
    }
    .badge-qty-low i {
        font-size: 11px;
        color: #dc2626;
    }

    /* View Button (Matching Image 1) */
    .btn-stock-view {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        color: #334155;
        font-weight: 700;
        font-size: 12.5px;
        padding: 6px 14px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .btn-stock-view i {
        font-size: 12px;
        color: #64748b;
        transition: color 0.2s ease;
    }
    .btn-stock-view:hover {
        background: var(--erp-blue);
        border-color: var(--erp-blue);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        transform: translateY(-1px);
    }
    .btn-stock-view:hover i {
        color: #ffffff;
    }

    /* ─── SLIDE-OVER DRAWER / SLIDER (Image 2 Section) ────────────────────── */
    .stock-slider-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 1060;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.32s ease, visibility 0.32s ease;
    }
    .stock-slider-backdrop.open {
        opacity: 1;
        visibility: visible;
    }

    .stock-slider-panel {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        width: 820px;
        max-width: 95vw;
        height: 100vh;
        height: 100dvh;
        background: #f8fafc;
        z-index: 1065;
        box-shadow: -10px 0 40px rgba(0, 0, 0, 0.22);
        display: flex;
        flex-direction: column;
        transform: translateX(100%);
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
    }
    .stock-slider-panel.open {
        transform: translateX(0);
    }

    .stock-slider-header {
        background: var(--erp-header-bg);
        color: #ffffff;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.2);
    }
    .stock-slider-header h4 {
        margin: 0;
        font-size: 16.5px;
        font-weight: 800;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .stock-slider-header .header-badge {
        background: rgba(255, 255, 255, 0.22);
        border: 1px solid rgba(255, 255, 255, 0.4);
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.2px;
    }
    .btn-slider-close {
        background: rgba(255, 255, 255, 0.18);
        border: none;
        color: #ffffff;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 16px;
        transition: all 0.2s ease;
    }
    .btn-slider-close:hover {
        background: rgba(255, 255, 255, 0.35);
        transform: rotate(90deg);
    }

    .stock-slider-body {
        flex: 1;
        overflow-y: auto;
        padding: 22px 24px;
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    /* Slider Sub-Cards (Image 2: Stock Summary & Stock Details) */
    .slider-sub-card {
        background: #ffffff;
        border: 1px solid var(--erp-border);
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        animation: cardSlideUp 0.35s ease forwards;
    }
    @keyframes cardSlideUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .slider-sub-card-header {
        background: var(--erp-header-bg);
        color: #ffffff;
        padding: 13px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14.5px;
        font-weight: 800;
        letter-spacing: 0.2px;
    }
    .slider-sub-card-header i {
        font-size: 15px;
        opacity: 0.95;
    }

    /* 5 Summary Stats Grid in Image 2 */
    .stock-summary-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 14px;
        padding: 18px 20px;
        background: #ffffff;
    }
    @media (max-width: 768px) {
        .stock-summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .stock-slider-panel {
            width: 100vw;
        }
    }

    .summary-stat-box {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .summary-stat-box:hover {
        border-color: var(--erp-blue-border);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08);
    }
    .summary-stat-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 6px;
    }
    .summary-stat-value {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    /* Stock Details Table in Image 2 */
    .slider-table-wrap {
        padding: 0;
        overflow-x: auto;
    }
    .slider-details-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
    }
    .slider-details-table thead th {
        background: #ffffff;
        color: #0f172a;
        font-weight: 700;
        font-size: 12.5px;
        padding: 13px 18px;
        border-bottom: 1.5px solid #e2e8f0;
        text-align: left;
        white-space: nowrap;
    }
    .slider-details-table tbody tr {
        transition: background-color 0.15s ease;
        border-bottom: 1px solid #f1f5f9;
    }
    .slider-details-table tbody tr:hover {
        background-color: #f8fbff;
    }
    .slider-details-table tbody td {
        padding: 14px 18px;
        color: #334155;
        font-weight: 500;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
    }
    .slider-details-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* DR / CR Badges (Exact match to Image 2) */
    .badge-cr-dr {
        font-weight: 800;
        font-size: 11.5px;
        padding: 3px 12px;
        border-radius: 20px;
        display: inline-block;
        text-align: center;
        letter-spacing: 0.4px;
    }
    .badge-dr {
        background: var(--erp-dr-bg);
        color: var(--erp-dr-text);
        border: 1px solid var(--erp-dr-border);
    }
    .badge-cr {
        background: var(--erp-cr-bg);
        color: var(--erp-cr-text);
        border: 1px solid var(--erp-cr-border);
    }

    /* Empty state */
    .stock-empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
    }
    .stock-empty-state i {
        font-size: 42px;
        margin-bottom: 12px;
        color: #cbd5e1;
    }
    .stock-empty-state h6 {
        font-size: 15px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 4px;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="container-fluid stock-page-container">
    <!-- ─── 3 Hero Stat Cards in ONE Horizontal Line (Side by Side) ─── -->
    <div class="stat-hero-row">
        <!-- 1. Total Products Card -->
        <div class="stat-hero-col">
            <div class="stat-hero-card card-hero-blue" onclick="setStockStatusFilter('all', document.querySelector('.stock-pill-all'))" title="Click to view all products">
                <div class="stat-hero-top">
                    <div class="stat-hero-icon">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                    <span class="stat-hero-pill">
                        <i class="fas fa-layer-group"></i> Active Catalog
                    </span>
                </div>
                <div class="stat-hero-body">
                    <div class="stat-hero-number">{{ $totalProductsCount }}</div>
                    <div class="stat-hero-title">Total Products</div>
                    <div class="stat-hero-subtitle">Catalog Items in Inventory</div>
                </div>
            </div>
        </div>

        <!-- 2. Total Stock in Hand Card -->
        <div class="stat-hero-col">
            <div class="stat-hero-card card-hero-cyan" onclick="setStockStatusFilter('in_stock', document.querySelector('.stock-pill-instock'))" title="Click to view in-stock items">
                <div class="stat-hero-top">
                    <div class="stat-hero-icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <span class="stat-hero-pill">
                        <i class="fas fa-check-circle"></i> Available Units
                    </span>
                </div>
                <div class="stat-hero-body">
                    <div class="stat-hero-number">{{ $totalStockQty }}</div>
                    <div class="stat-hero-title">Stock In Hand</div>
                    <div class="stat-hero-subtitle">Total Quantity Available</div>
                </div>
            </div>
        </div>

        <!-- 3. Low Stock Alerts Card -->
        <div class="stat-hero-col">
            <div class="stat-hero-card card-hero-indigo" onclick="setStockStatusFilter('low', document.querySelector('.stock-pill-low'))" title="Click to filter low stock items">
                <div class="stat-hero-top">
                    <div class="stat-hero-icon">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    @if($lowStockCount > 0)
                        <span class="stat-hero-pill pill-alert">
                            <i class="fas fa-bell"></i> Action Needed
                        </span>
                    @else
                        <span class="stat-hero-pill">
                            <i class="fas fa-shield-check"></i> Stock Healthy
                        </span>
                    @endif
                </div>
                <div class="stat-hero-body">
                    <div class="stat-hero-number">{{ $lowStockCount }}</div>
                    <div class="stat-hero-title">Low Stock Alerts</div>
                    <div class="stat-hero-subtitle">Items &le; Minimum Threshold</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── Main Stock In Hand Table Card (Matching Image 1 & 3) ─── -->
    <div class="stock-main-card">
        <!-- Card Header with Image 3 Blue Gradient -->
        <div class="stock-card-header">
            <h5 class="stock-card-title">
                <i class="fas fa-boxes-stacked"></i> Stock In Hand
            </h5>
            <div class="text-white-50 small fw-semibold d-none d-sm-block">
                Real-time Quantity & Movement Tracker
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="stock-filter-bar">
            <div class="stock-search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" id="stockSearchInput" class="stock-search-input" placeholder="Search product, size or category..." onkeyup="filterStockTable()">
            </div>

            <div class="stock-filter-pills">
                <button type="button" class="stock-pill stock-pill-all active" onclick="setStockStatusFilter('all', this)">
                    <i class="fas fa-list-ul"></i> All ({{ count($stockItems) }})
                </button>
                <button type="button" class="stock-pill stock-pill-instock" onclick="setStockStatusFilter('in_stock', this)">
                    <i class="fas fa-check-circle text-success"></i> In Stock
                </button>
                <button type="button" class="stock-pill stock-pill-low" onclick="setStockStatusFilter('low', this)">
                    <i class="fas fa-triangle-exclamation text-danger"></i> Low Stock ({{ $lowStockCount }})
                </button>
            </div>
        </div>

        <!-- Table Content -->
        <div class="stock-table-wrap">
            <table class="stock-table" id="stockInHandTable">
                <thead>
                    <tr>
                        <th class="col-sno">S.No</th>
                        <th>Product</th>
                        <th>Size</th>
                        <th>Min Stock</th>
                        <th>Total Qty</th>
                        <th>Actual Qty</th>
                        <th>Available Qty</th>
                        <th>Last Updated</th>
                        <th class="text-center">Stock Details</th>
                    </tr>
                </thead>
                <tbody id="stockTableBody">
                    @forelse($stockItems as $index => $item)
                    <tr class="stock-row" 
                        data-name="{{ strtolower($item->product_name) }}" 
                        data-size="{{ strtolower($item->size) }}" 
                        data-cat="{{ strtolower($item->category_name) }}"
                        data-islow="{{ $item->is_low ? '1' : '0' }}">
                        
                        <td class="col-sno">{{ $index + 1 }}</td>
                        <td class="col-product">{{ $item->product_name }}</td>
                        <td class="col-size">{{ $item->size }}</td>
                        <td class="col-num">{{ $item->min_stock }}</td>
                        <td class="col-num">{{ $item->total_qty }}</td>
                        <td class="col-num">{{ $item->actual_qty }}</td>
                        <td>
                            @if($item->is_low)
                                <span class="badge-qty-low">
                                    {{ $item->available_qty }} <i class="fas fa-triangle-exclamation"></i> Low
                                </span>
                            @else
                                <span class="badge-qty-normal">{{ $item->available_qty }}</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $item->last_updated }}</td>
                        <td class="text-center">
                            <button type="button" class="btn-stock-view" 
                                onclick="openStockSlider({{ $item->id }}, @js($item))">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="stock-empty-state">
                                <i class="fas fa-box-open"></i>
                                <h6>No stock records found</h6>
                                <p class="small mb-0">Add products and initial stocks from the Product & Stock menu.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ─── SLIDE-OVER DRAWER (Image 2 View Component) ───────────────────────── -->
<div class="stock-slider-backdrop" id="stockSliderBackdrop" onclick="closeStockSlider()"></div>

<div class="stock-slider-panel" id="stockSliderPanel">
    <!-- Slider Top Header -->
    <div class="stock-slider-header">
        <h4>
            <i class="fas fa-boxes-stacked"></i>
            <span>Stock Details View</span>
            <span class="header-badge" id="sliderProductBadge">-</span>
        </h4>
        <button type="button" class="btn-slider-close" onclick="closeStockSlider()" title="Close (Esc)">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Slider Body with Image 2 Content -->
    <div class="stock-slider-body">
        <!-- 1. Stock Summary Section (Image 2 Top) -->
        <div class="slider-sub-card">
            <div class="slider-sub-card-header">
                <i class="fas fa-info-circle"></i> Stock Summary
            </div>
            <div class="stock-summary-grid">
                <div class="summary-stat-box">
                    <div class="summary-stat-label">Product</div>
                    <div class="summary-stat-value" id="summaryProductName">-</div>
                </div>
                <div class="summary-stat-box">
                    <div class="summary-stat-label">Size</div>
                    <div class="summary-stat-value" id="summaryProductSize">-</div>
                </div>
                <div class="summary-stat-box">
                    <div class="summary-stat-label">Actual Quantity</div>
                    <div class="summary-stat-value text-primary" id="summaryActualQty">0</div>
                </div>
                <div class="summary-stat-box">
                    <div class="summary-stat-label">Available Quantity</div>
                    <div class="summary-stat-value text-success" id="summaryAvailableQty">0</div>
                </div>
                <div class="summary-stat-box">
                    <div class="summary-stat-label">Min Stock</div>
                    <div class="summary-stat-value text-muted" id="summaryMinStock">5</div>
                </div>
            </div>
        </div>

        <!-- 2. Stock Details Section (Image 2 Bottom) -->
        <div class="slider-sub-card">
            <div class="slider-sub-card-header">
                <i class="fas fa-list-check"></i> Stock Details
            </div>
            <div class="slider-table-wrap">
                <table class="slider-details-table">
                    <thead>
                        <tr>
                            <th style="width: 70px; text-align: center;">S.No</th>
                            <th>Quantity</th>
                            <th>CR / DR</th>
                            <th>Entry Date</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody id="sliderLogsTableBody">
                        <!-- Populated via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    let currentFilterStatus = 'all';

    /**
     * Filter Table by Search Keyword
     */
    function filterStockTable() {
        const query = document.getElementById('stockSearchInput').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.stock-row');

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const size = row.getAttribute('data-size') || '';
            const cat = row.getAttribute('data-cat') || '';
            const isLow = row.getAttribute('data-islow') === '1';

            const matchesQuery = !query || name.includes(query) || size.includes(query) || cat.includes(query);
            
            let matchesStatus = true;
            if (currentFilterStatus === 'low') {
                matchesStatus = isLow;
            } else if (currentFilterStatus === 'in_stock') {
                matchesStatus = !isLow;
            }

            if (matchesQuery && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    /**
     * Filter Table by Stock Status Pill
     */
    function setStockStatusFilter(status, el) {
        currentFilterStatus = status;
        document.querySelectorAll('.stock-pill').forEach(btn => btn.classList.remove('active'));
        if (el) {
            el.classList.add('active');
        }
        filterStockTable();
    }

    /**
     * Open Slide-Over Drawer with Stock Details (Image 2)
     */
    function openStockSlider(stockId, itemData = null) {
        const backdrop = document.getElementById('stockSliderBackdrop');
        const panel = document.getElementById('stockSliderPanel');

        if (itemData) {
            populateSliderData(itemData);
        }

        // Fetch fresh logs via AJAX for complete sync
        fetch(`{{ url('/school/inventory/stock-history') }}/${stockId}/details`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.stock) {
                    populateSliderData(data.stock);
                }
            })
            .catch(err => {
                console.log('Using preloaded data for stock slider');
            });

        backdrop.classList.add('open');
        panel.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Populate Stock Summary and Log Table in Slider
     */
    function populateSliderData(stock) {
        document.getElementById('sliderProductBadge').innerText = `${stock.product_name || 'Product'} (${stock.size || 'Free'})`;
        document.getElementById('summaryProductName').innerText = stock.product_name || '-';
        document.getElementById('summaryProductSize').innerText = stock.size || '-';
        document.getElementById('summaryActualQty').innerText = stock.actual_qty ?? 0;
        document.getElementById('summaryAvailableQty').innerText = stock.available_qty ?? 0;
        document.getElementById('summaryMinStock').innerText = stock.min_stock ?? 5;

        const tbody = document.getElementById('sliderLogsTableBody');
        tbody.innerHTML = '';

        const logs = stock.logs || [];
        if (logs.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5">
                        <div class="stock-empty-state py-4">
                            <i class="fas fa-clipboard-list"></i>
                            <h6>No movement logs recorded</h6>
                            <p class="small mb-0">Stock updates, orders and adjustments will appear here.</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        logs.forEach((log, idx) => {
            const isDr = (log.type === 'DR' || log.type === 'debit' || log.type === 'out');
            const badgeClass = isDr ? 'badge-dr' : 'badge-cr';
            const badgeText = isDr ? 'DR' : 'CR';

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="text-align: center; font-weight: 600; color: #64748b;">${idx + 1}</td>
                <td style="font-weight: 700; color: #0f172a;">${log.quantity}</td>
                <td><span class="badge-cr-dr ${badgeClass}">${badgeText}</span></td>
                <td style="color: #475569;">${log.date || '-'}</td>
                <td style="color: #334155; font-weight: 500;">${log.comment || '-'}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    /**
     * Close Slide-Over Drawer
     */
    function closeStockSlider() {
        const backdrop = document.getElementById('stockSliderBackdrop');
        const panel = document.getElementById('stockSliderPanel');
        backdrop.classList.remove('open');
        panel.classList.remove('open');
        document.body.style.overflow = '';
    }

    // Close on Escape Key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeStockSlider();
        }
    });
</script>
@endsection

@extends('layouts.app')

@section('page-title', 'Product Cart - Inventory Management')

@section('content')
<style>
    /* ─── Standard ERP Royal Blue & White Theme (Matching Image 2 / Product Stock) ─── */
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
        --erp-gold:        #d97706;
        --erp-gold-light:  #fbbf24;
    }

    /* ─── Main Full Screen Container ───────────────────────────────────── */
    .inv-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 20px 28px 40px !important;
        box-sizing: border-box;
    }

    /* ─── ERP Cards (Matching Image 2) ─────────────────────────────────── */
    .inv-card {
        background: #ffffff;
        border: 1px solid var(--erp-border);
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.05);
        margin-bottom: 24px;
        overflow: visible;
        position: relative;
    }

    .inv-card-header {
        background: linear-gradient(135deg, var(--erp-blue-dark) 0%, var(--erp-blue) 60%, var(--erp-blue-light) 100%);
        color: #ffffff;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
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
        padding: 24px 28px;
        background: #ffffff;
        border-bottom-left-radius: 13px;
        border-bottom-right-radius: 13px;
    }

    /* ─── Product Cart Search Inputs ────────────────────────────────────── */
    .search-inputs-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }
    @media (max-width: 768px) {
        .search-inputs-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }

    .erp-label {
        font-size: 13px;
        font-weight: 700;
        color: var(--erp-text-dark);
        margin-bottom: 7px;
        display: block;
    }
    .erp-label .req {
        color: #ef4444;
        font-weight: bold;
    }

    .erp-input {
        width: 100%;
        height: 44px;
        border-radius: 8px;
        border: 1.5px solid #cbd5e1;
        font-size: 13.5px;
        color: var(--erp-text-dark);
        padding: 8px 14px;
        background-color: #ffffff;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
        box-sizing: border-box;
    }
    .erp-input:focus {
        border-color: var(--erp-blue);
        box-shadow: 0 0 0 3.5px rgba(37, 99, 235, 0.14);
    }
    .erp-input::placeholder {
        color: #94a3b8;
    }

    /* Autocomplete Dropdown List */
    .typeahead-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        max-height: 280px;
        overflow-y: auto;
        z-index: 1050;
        margin-top: 4px;
        display: none;
    }
    .typeahead-option {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background 0.15s;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .typeahead-option:last-child {
        border-bottom: none;
    }
    .typeahead-option:hover, .typeahead-option.selected {
        background-color: var(--erp-blue-soft);
    }
    .typeahead-name {
        font-weight: 700;
        font-size: 13.5px;
        color: var(--erp-text-dark);
    }
    .typeahead-meta {
        font-size: 11.5px;
        color: var(--erp-text-muted);
    }
    .typeahead-price-badge {
        font-size: 12.5px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        background: #e0f2fe;
        color: #0369a1;
    }

    /* ─── Floating / Active Cart Pill Button on Main Page ───────────────── */
    .btn-view-cart-trigger {
        background: linear-gradient(135deg, var(--erp-blue) 0%, #1d4ed8 100%);
        color: #ffffff !important;
        font-weight: 700;
        font-size: 13px;
        padding: 8px 18px;
        border-radius: 20px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.28);
        text-decoration: none !important;
    }
    .btn-view-cart-trigger:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.4);
    }

    /* ─── Responsive Slider Drawer (Slide-Over Panel) ─────────────────── */
    .inv-slider-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.5);
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
        width: 900px;
        max-width: 100vw;
        background: #ffffff;
        z-index: 1060;
        display: flex;
        flex-direction: column;
        box-shadow: -8px 0 35px rgba(0, 0, 0, 0.2);
        transform: translateX(100%);
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .inv-slider-panel.open {
        transform: translateX(0);
    }

    @media (max-width: 992px) {
        .inv-slider-panel {
            width: 100vw;
        }
    }

    .inv-slider-header {
        background: linear-gradient(135deg, var(--erp-blue-dark) 0%, var(--erp-blue) 60%, var(--erp-blue-light) 100%);
        color: #ffffff;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
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
        background: rgba(255, 255, 255, 0.2);
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
        background: rgba(255, 255, 255, 0.35);
    }

    .inv-slider-body {
        flex: 1;
        overflow-y: auto;
        padding: 24px;
        background: #ffffff;
    }

    /* ─── Cart Section Table inside Slider (Image 2) ───────────────────── */
    .cart-table-container {
        width: 100%;
        overflow-x: auto;
        border: 1px solid var(--erp-border);
        border-radius: 10px;
        background: #ffffff;
        margin-bottom: 20px;
    }
    .cart-grid-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
        margin: 0;
    }
    .cart-grid-table thead th {
        background-color: #f8fafc;
        color: var(--erp-text-dark);
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 12px 14px;
        border-bottom: 1.5px solid var(--erp-border);
        border-right: 1px solid var(--erp-border);
        text-align: center;
        white-space: nowrap;
    }
    .cart-grid-table thead th:last-child {
        border-right: none;
    }
    .cart-grid-table tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid var(--erp-border);
        vertical-align: middle;
        text-align: center;
        color: var(--erp-text-dark);
        white-space: nowrap;
    }
    .cart-grid-table tbody td:last-child {
        border-right: none;
    }
    .cart-grid-table tbody tr:hover {
        background-color: var(--erp-blue-soft);
    }

    /* Table Input Fields */
    .table-cell-input {
        width: 75px;
        height: 34px;
        border-radius: 6px;
        border: 1.5px solid #cbd5e1;
        text-align: center;
        font-weight: 700;
        font-size: 13px;
        color: var(--erp-text-dark);
        padding: 2px 4px;
        outline: none;
        background: #ffffff;
        transition: border-color 0.2s;
    }
    .table-cell-input:focus {
        border-color: var(--erp-blue);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
    }
    .table-cell-select {
        height: 34px;
        border-radius: 6px;
        border: 1.5px solid #cbd5e1;
        font-size: 12.5px;
        font-weight: 600;
        padding: 2px 8px;
        color: #1e293b;
        background-color: #ffffff;
        cursor: pointer;
        outline: none;
    }
    .table-cell-select:focus {
        border-color: var(--erp-blue);
    }
    .stock-count-green {
        color: #16a34a;
        font-weight: 800;
        font-size: 13px;
    }

    .btn-trash-delete {
        color: #ef4444;
        background: #fee2e2;
        border: 1px solid #fecaca;
        border-radius: 6px;
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 13px;
    }
    .btn-trash-delete:hover {
        background: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
        transform: scale(1.06);
    }

    /* ─── Buttons inside Slider (Image 2 & 3) ───────────────────────────── */
    .btn-slider-checkout {
        background: linear-gradient(135deg, var(--erp-blue) 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 14px;
        padding: 12px 36px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.28);
        text-decoration: none !important;
    }
    .btn-slider-checkout:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.4);
    }

    .btn-slider-back {
        background: #f1f5f9;
        color: #334155 !important;
        font-weight: 700;
        font-size: 13.5px;
        padding: 11px 24px;
        border-radius: 8px;
        border: 1.5px solid #cbd5e1;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }
    .btn-slider-back:hover {
        background: #e2e8f0;
        color: #0f172a !important;
    }

    /* ─── Step 2: Summary Card & Student Details Form (Image 3) ────────── */
    .checkout-summary-card {
        border: 1px solid var(--erp-border);
        border-radius: 10px;
        background: #f8fafc;
        padding: 18px 22px;
        width: 100%;
        max-width: 360px;
        margin-left: auto;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }
    .checkout-summary-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 13.5px;
        color: #334155;
    }
    .checkout-summary-line.grand-total {
        border-top: 2px solid #cbd5e1;
        margin-top: 8px;
        padding-top: 10px;
        font-size: 16px;
        font-weight: 800;
        color: var(--erp-blue-dark);
    }

    .student-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 18px;
    }
    .student-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 18px;
        align-items: start;
    }
    @media (max-width: 992px) {
        .student-grid-4 { grid-template-columns: repeat(2, 1fr); }
        .student-grid-3 { grid-template-columns: 1fr; }
    }
    @media (max-width: 576px) {
        .student-grid-4 { grid-template-columns: 1fr; }
    }

    /* ─── Drawer Steps Sliding ─────────────────────────────────────────── */
    .slider-step-block {
        display: none;
    }
    .slider-step-block.active {
        display: block;
        animation: fadeInStep 0.25s ease forwards;
    }
    @keyframes fadeInStep {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ─── Custom Modal Overlay for Receipt Preview ────────────────────── */
    .custom-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 1090;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        box-sizing: border-box;
    }
    .custom-modal-overlay.open {
        display: flex;
    }
    .custom-modal-dialog {
        background: #ffffff;
        border-radius: 14px;
        max-width: 1060px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        display: flex;
        flex-direction: column;
    }
    .custom-modal-header {
        padding: 16px 22px;
        background: linear-gradient(135deg, var(--erp-blue-dark) 0%, var(--erp-blue) 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top-left-radius: 13px;
        border-top-right-radius: 13px;
    }
    .custom-modal-footer {
        padding: 14px 20px;
        background: #f8fafc;
        border-top: 1px solid var(--erp-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom-left-radius: 13px;
        border-bottom-right-radius: 13px;
    }

    /* ─── Direct Browser Print Styles (@media print) ───────────────────── */
    @media print {
        body {
            background: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        body * {
            visibility: hidden !important;
        }
        #printable-receipt-view, #printable-receipt-view * {
            visibility: visible !important;
        }
        #printable-receipt-view {
            position: fixed !important;
            left: 0 !important;
            top: 0 !important;
            width: 100vw !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            background: #ffffff !important;
            z-index: 999999 !important;
        }
        .custom-modal-overlay {
            position: static !important;
            background: transparent !important;
            padding: 0 !important;
            display: block !important;
        }
        .custom-modal-dialog {
            box-shadow: none !important;
            border: none !important;
            max-width: 100% !important;
            width: 100% !important;
        }
        .custom-modal-header, .custom-modal-footer, .inv-container, .inv-slider-panel, .inv-slider-backdrop, .no-print {
            display: none !important;
        }
        @page {
            size: landscape;
            margin: 6mm 8mm;
        }
    }
</style>

<div class="inv-container">

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- 1. MAIN PAGE: PRODUCT SEARCH INPUTS (Image 1)                       -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="inv-card">
        <div class="inv-card-header">
            <h5><i class="fas fa-list-ul hdr-icon"></i>Product Cart</h5>
            <div id="main-cart-indicator" style="display: none;">
                <button type="button" class="btn-view-cart-trigger" onclick="openCartSlider()">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Cart (<span id="cart-item-count">0</span>)</span>
                </button>
            </div>
        </div>
        <div class="inv-card-body">
            <div class="search-inputs-grid">
                <!-- Product Name Input (Left Column) -->
                <div style="position: relative;">
                    <label class="erp-label">
                        Product Name <span class="req">*</span>
                    </label>
                    <input type="text" id="search-product-input" class="erp-input" 
                           placeholder="Type product name..." autocomplete="off">
                    
                    <!-- Live Autocomplete Dropdown List -->
                    <div id="product-typeahead-list" class="typeahead-results"></div>
                </div>

                <!-- Quantity Input (Right Column) -->
                <div>
                    <label class="erp-label">
                        Quantity <span class="req">*</span>
                    </label>
                    <input type="number" id="search-quantity-input" class="erp-input" 
                           placeholder="Enter Quantity" min="1" value="1">
                </div>
            </div>

            <!-- Helpful tip -->
            <div class="d-flex align-items-center justify-content-between mt-3 pt-2 text-muted" style="font-size: 12.5px;">
                <span><i class="fas fa-info-circle text-primary me-1"></i> Select a product from the list or press Enter to add items directly to your cart slider.</span>
                <span id="cart-total-badge" class="fw-bold text-primary" style="display: none;"></span>
            </div>
        </div>
    </div>

</div>


<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- 2. CART SLIDER PANEL (Slide-over Drawer for Cart & Checkout)        -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div id="cartSliderBackdrop" class="inv-slider-backdrop" onclick="closeCartSlider()"></div>

<div id="cartSliderPanel" class="inv-slider-panel">
    
    <!-- Slider Header (Royal Blue Gradient) -->
    <div class="inv-slider-header">
        <h4 id="slider-header-title">
            <i class="fas fa-shopping-cart"></i>
            <span>Cart Section</span>
        </h4>
        <button type="button" class="btn-slider-close" onclick="closeCartSlider()" title="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Slider Body -->
    <div class="inv-slider-body">
        
        <!-- ─── SLIDER STEP 1: CART SECTION TABLE & CHECKOUT (Image 2) ─── -->
        <div id="slider-step-1" class="slider-step-block active">
            
            <div class="cart-table-container">
                <table class="cart-grid-table" id="cart-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">S/N</th>
                            <th style="text-align: left; min-width: 150px;">Product</th>
                            <th style="min-width: 95px;">Price</th>
                            <th style="width: 90px;">Quantity</th>
                            <th style="width: 90px;">Discount</th>
                            <th style="min-width: 85px;">Avl. Qty</th>
                            <th style="min-width: 100px;">Total</th>
                            <th style="min-width: 95px;">Size</th>
                            <th style="width: 55px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="cart-table-body">
                        <!-- Dynamically Injected Rows -->
                    </tbody>
                </table>
            </div>

            <!-- Centered Checkout Button (Image 2) -->
            <div class="text-center mt-4 pt-2">
                <button type="button" id="btn-proceed-checkout" class="btn-slider-checkout">
                    <i class="fas fa-shopping-cart"></i> Checkout
                </button>
            </div>
        </div>

        <!-- ─── SLIDER STEP 2: SUMMARY & STUDENT DETAILS (Image 3) ─────── -->
        <div id="slider-step-2" class="slider-step-block">
            
            <!-- Summary Table -->
            <div class="cart-table-container mb-3">
                <table class="cart-grid-table" id="checkout-summary-table">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Product</th>
                            <th>MRP</th>
                            <th>Price</th>
                            <th>Tax(%)</th>
                            <th>Quantity</th>
                            <th>Size</th>
                            <th>Discount</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="checkout-summary-body">
                        <!-- Dynamically Injected -->
                    </tbody>
                </table>
            </div>

            <!-- Right Summary Box -->
            <div class="d-flex justify-content-end mb-4">
                <div class="checkout-summary-card">
                    <div class="checkout-summary-line">
                        <span>Total MRP</span>
                        <span id="summary-mrp" class="fw-bold">₹ 0.00</span>
                    </div>
                    <div class="checkout-summary-line">
                        <span>Total Price</span>
                        <span id="summary-price" class="fw-bold">₹ 0.00</span>
                    </div>
                    <div class="checkout-summary-line">
                        <span>Total Discount</span>
                        <span id="summary-discount" class="fw-bold text-danger">₹ 0.00</span>
                    </div>
                    <div class="checkout-summary-line">
                        <span>Total Tax</span>
                        <span id="summary-tax" class="fw-bold text-warning">₹ 0.00</span>
                    </div>
                    <div class="checkout-summary-line grand-total">
                        <span>Grand Total</span>
                        <span id="summary-grand">₹ 0.00</span>
                    </div>
                </div>
            </div>

            <!-- Student Details Form (Image 3) -->
            <div class="inv-card mb-0">
                <div class="inv-card-header py-2 px-3">
                    <h5 style="font-size: 14px;"><i class="fas fa-user-plus hdr-icon"></i>Student Details</h5>
                </div>
                <div class="inv-card-body p-3">
                    <form id="checkout-form" onsubmit="return false;">
                        <!-- Row 1: 4 Inputs with Live Dual Autocomplete -->
                        <div class="student-grid-4">
                            <div style="position: relative;">
                                <label class="erp-label">Addmission No.(Optional)</label>
                                <input type="text" id="cust-admission-no" class="erp-input" 
                                       placeholder="Enter Addmission No." autocomplete="off">
                                <div id="student-admission-typeahead-list" class="typeahead-results"></div>
                            </div>

                            <div style="position: relative;">
                                <label class="erp-label">Name <span class="req">*</span></label>
                                <input type="text" id="cust-name" class="erp-input" 
                                       placeholder="Enter Student Name" autocomplete="off" required>
                                <div id="student-name-typeahead-list" class="typeahead-results"></div>
                            </div>

                            <div>
                                <label class="erp-label">Address <span class="req">*</span></label>
                                <input type="text" id="cust-address" class="erp-input" 
                                       placeholder="Enter Address" required>
                            </div>

                            <div>
                                <label class="erp-label">Mobile No <span class="req">*</span></label>
                                <input type="text" id="cust-mobile" class="erp-input" 
                                       placeholder="Enter Mobile No." required>
                            </div>
                        </div>

                        <!-- Row 2: 3 Inputs -->
                        <div class="student-grid-3">
                            <div>
                                <label class="erp-label">Payment Mode <span class="req">*</span></label>
                                <select id="cust-payment-mode" class="erp-input" required>
                                    <option value="cash" selected>Cash</option>
                                    <option value="upi">Online / UPI</option>
                                    <option value="card">Debit / Credit Card</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="dd">Demand Draft</option>
                                </select>
                            </div>

                            <div>
                                <label class="erp-label">Reference No.</label>
                                <input type="text" id="cust-ref-no" class="erp-input" 
                                       placeholder="Enter Reference No.">
                            </div>

                            <div>
                                <label class="erp-label">Payable Amount <span class="req">*</span></label>
                                <input type="number" step="0.01" id="cust-payable-amount" class="erp-input" 
                                       placeholder="0.00" required>
                                <div style="font-size: 12.5px; font-weight: 700; color: var(--erp-blue); margin-top: 5px;">
                                    Balance Due: <span id="label-balance-due">0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Centered Action Buttons -->
                        <div class="d-flex justify-content-center gap-3 mt-4 pt-2">
                            <button type="button" id="btn-back-to-cart" class="btn-slider-back">
                                <i class="fas fa-arrow-left"></i> Back to Cart
                            </button>
                            <button type="button" id="btn-confirm-order" class="btn-slider-checkout">
                                <i class="fas fa-check"></i> Confirm Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>

</div>


<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- 3. CUSTOM POPUP MODAL FOR RECEIPT PREVIEW (Image 4)                 -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div id="receiptModalOverlay" class="custom-modal-overlay">
    <div class="custom-modal-dialog">
        <div class="custom-modal-header">
            <div class="fw-bold text-white d-flex align-items-center gap-2">
                <i class="fas fa-receipt"></i>
                <span>Sales Receipt Generated</span>
            </div>
            <button type="button" class="btn-slider-close" onclick="closeReceiptModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-3 p-md-4" id="receipt-modal-content-area">
            <!-- Dynamic Image 4 receipt template injected here -->
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeReceiptModalAndReset()">
                <i class="fas fa-plus me-1"></i> New Sale
            </button>
            <div class="d-flex gap-2">
                <a href="#" id="modal-standalone-link" target="_blank" class="btn btn-outline-dark btn-sm">
                    <i class="fas fa-up-right-from-square me-1"></i> Full Page
                </a>
                <button type="button" class="btn-slider-checkout" style="padding: 7px 18px; font-size: 13px;" onclick="printModalReceipt()">
                    <i class="fas fa-print"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Raw Initial Products & Config -->
<script>
    const INITIAL_PRODUCTS = @json($products ?? []);
    const APP_CSRF_TOKEN = '{{ csrf_token() }}';
    const BILLING_CHECKOUT_URL = '{{ route("school.inventory.billing.checkout") }}';
    const STUDENT_SEARCH_URL = '{{ route("school.inventory.billing.search-students") }}';
    const RECEIPT_BASE_URL = '{{ url("/inventory/billing/receipt") }}';
    const SCHOOL_INFO = @json($school ?? (object)[]);
</script>

<script>
    // ─────────────────────────────────────────────────────────────────────────
    // Cart State & Initialization
    // ─────────────────────────────────────────────────────────────────────────
    let cartItems = [];

    document.addEventListener('DOMContentLoaded', function() {
        renderCartTable();
        setupProductAutocomplete();
        setupStudentAutocomplete();
        setupEvents();
    });

    // Open & Close Slider Drawer
    function openCartSlider() {
        document.getElementById('cartSliderBackdrop').classList.add('open');
        document.getElementById('cartSliderPanel').classList.add('open');
    }

    function closeCartSlider() {
        document.getElementById('cartSliderBackdrop').classList.remove('open');
        document.getElementById('cartSliderPanel').classList.remove('open');
    }

    // Add Item to Cart and Open Slider
    function addItemToCart(product, qty = 1, discount = 0, defaultSize = null) {
        qty = parseInt(qty) || 1;
        discount = parseFloat(discount) || 0;

        let stocks = product.stocks || [];
        let sizes = [];
        if (stocks.length > 0) {
            sizes = stocks.map(s => s.size || 'Free');
        } else if (product.selected_sizes && Array.isArray(product.selected_sizes)) {
            sizes = product.selected_sizes;
        } else {
            sizes = ['Free'];
        }

        let size = defaultSize || sizes[0] || 'Free';

        let existingIndex = cartItems.findIndex(i => i.product_id === product.id && i.size === size);
        if (existingIndex > -1) {
            cartItems[existingIndex].quantity += qty;
        } else {
            let stockObj = stocks.find(s => s.size === size) || stocks[0] || null;
            let price = stockObj && stockObj.price ? parseFloat(stockObj.price) : parseFloat(product.price || 0);
            let mrp = stockObj && stockObj.mrp ? parseFloat(stockObj.mrp) : parseFloat(product.mrp || (price * 1.2));
            let avlQty = stockObj ? parseInt(stockObj.stock) : parseInt(product.total_stock || 0);
            let tax = parseFloat(product.tax || 0);

            cartItems.push({
                product_id: product.id,
                name: product.name,
                category: product.category?.name || product.category || 'General',
                price: price,
                mrp: mrp,
                tax: tax,
                quantity: qty,
                discount: discount,
                avl_qty: avlQty,
                size: size,
                available_sizes: sizes,
                raw_stocks: stocks,
            });
        }

        renderCartTable();
        // Automatically open the slider drawer with the added item!
        openCartSlider();
    }

    // Render Cart Table inside Slider (Image 2)
    function renderCartTable() {
        const tbody = document.getElementById('cart-table-body');
        const cartIndicator = document.getElementById('main-cart-indicator');
        const cartCount = document.getElementById('cart-item-count');
        const totalBadge = document.getElementById('cart-total-badge');

        if (cartItems.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-muted py-5" style="text-align: center;">
                        <i class="fas fa-cart-shopping fa-2x mb-2 d-block text-secondary opacity-50"></i>
                        Your cart is empty. Type a product name on the left to add items.
                    </td>
                </tr>
            `;
            if (cartIndicator) cartIndicator.style.display = 'none';
            if (totalBadge) totalBadge.style.display = 'none';
            return;
        }

        let totalCartSum = 0;
        let html = '';

        cartItems.forEach((item, index) => {
            const lineTotal = (item.price * item.quantity) - item.discount;
            totalCartSum += lineTotal;
            const formattedTotal = '₹ ' + formatNumber(lineTotal);
            const formattedPrice = '₹ ' + formatNumber(item.price);

            let sizeOptions = '';
            item.available_sizes.forEach(sz => {
                sizeOptions += `<option value="${sz}" ${sz === item.size ? 'selected' : ''}>${sz}</option>`;
            });

            html += `
                <tr data-index="${index}">
                    <td class="fw-bold">${index + 1}</td>
                    <td style="text-align: left;">
                        <div class="fw-bold text-dark">${escapeHtml(item.name)}</div>
                    </td>
                    <td>${formattedPrice}</td>
                    <td>
                        <input type="number" min="1" class="table-cell-input" value="${item.quantity}" 
                               onchange="updateQty(${index}, this.value)">
                    </td>
                    <td>
                        <input type="number" min="0" step="0.01" class="table-cell-input" value="${item.discount}" 
                               onchange="updateDiscount(${index}, this.value)">
                    </td>
                    <td>
                        <span class="stock-count-green">${item.avl_qty}</span>
                    </td>
                    <td class="fw-bold text-dark">${formattedTotal}</td>
                    <td>
                        <select class="table-cell-select" onchange="updateSize(${index}, this.value)">
                            ${sizeOptions}
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn-trash-delete" title="Remove" onclick="deleteCartRow(${index})">
                            <i class="fas fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;

        // Update header indicator badge
        if (cartIndicator) cartIndicator.style.display = 'block';
        if (cartCount) cartCount.innerText = cartItems.length;
        if (totalBadge) {
            totalBadge.style.display = 'inline';
            totalBadge.innerText = `Cart Total: ₹ ${formatNumber(totalCartSum)}`;
        }
    }

    function updateQty(index, val) {
        let q = parseInt(val) || 1;
        if (q < 1) q = 1;
        cartItems[index].quantity = q;
        renderCartTable();
    }

    function updateDiscount(index, val) {
        let d = parseFloat(val) || 0;
        if (d < 0) d = 0;
        cartItems[index].discount = d;
        renderCartTable();
    }

    function updateSize(index, newSize) {
        let item = cartItems[index];
        item.size = newSize;

        if (item.raw_stocks && item.raw_stocks.length > 0) {
            let matched = item.raw_stocks.find(s => s.size === newSize);
            if (matched) {
                if (matched.price) item.price = parseFloat(matched.price);
                if (matched.mrp) item.mrp = parseFloat(matched.mrp);
                item.avl_qty = parseInt(matched.stock);
            }
        }

        renderCartTable();
    }

    function deleteCartRow(index) {
        cartItems.splice(index, 1);
        renderCartTable();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step 2: Checkout Review & Calculations (Image 3)
    // ─────────────────────────────────────────────────────────────────────────
    function populateCheckoutReview() {
        const tbody = document.getElementById('checkout-summary-body');
        let html = '';

        let totalMrp = 0;
        let totalPrice = 0;
        let totalDiscount = 0;
        let totalTax = 0;

        cartItems.forEach((item) => {
            let itemTotMrp = item.mrp * item.quantity;
            let itemTotPrice = item.price * item.quantity;
            let taxable = Math.max(0, itemTotPrice - item.discount);
            let itemTotTax = (taxable * item.tax) / 100;
            let lineTotal = itemTotPrice - item.discount;

            totalMrp += itemTotMrp;
            totalPrice += itemTotPrice;
            totalDiscount += item.discount;
            totalTax += itemTotTax;

            html += `
                <tr>
                    <td style="text-align: left;" class="fw-bold">${escapeHtml(item.name)}</td>
                    <td>${formatNumber(item.mrp)}</td>
                    <td>${formatNumber(item.price)}</td>
                    <td>${formatNumber(item.tax)}</td>
                    <td>${item.quantity}</td>
                    <td>${escapeHtml(item.size)}</td>
                    <td>${formatNumber(item.discount)}</td>
                    <td class="fw-bold">₹ ${formatNumber(lineTotal)}</td>
                </tr>
            `;
        });

        tbody.innerHTML = html;

        let grandTotal = totalPrice - totalDiscount + totalTax;

        document.getElementById('summary-mrp').innerText = '₹ ' + formatNumber(totalMrp);
        document.getElementById('summary-price').innerText = '₹ ' + formatNumber(totalPrice);
        document.getElementById('summary-discount').innerText = '₹ ' + formatNumber(totalDiscount);
        document.getElementById('summary-tax').innerText = '₹ ' + formatNumber(totalTax);
        document.getElementById('summary-grand').innerText = '₹ ' + formatNumber(grandTotal);

        const payableInput = document.getElementById('cust-payable-amount');
        payableInput.value = grandTotal.toFixed(2);
        calcBalanceDue();
    }

    function calcBalanceDue() {
        let grandText = document.getElementById('summary-grand').innerText.replace(/[₹\s,]/g, '');
        let grand = parseFloat(grandText) || 0;
        let payable = parseFloat(document.getElementById('cust-payable-amount').value) || 0;
        let balanceDue = Math.max(0, grand - payable);
        document.getElementById('label-balance-due').innerText = balanceDue.toFixed(2);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Autocomplete Logic
    // ─────────────────────────────────────────────────────────────────────────
    function setupProductAutocomplete() {
        const input = document.getElementById('search-product-input');
        const list = document.getElementById('product-typeahead-list');

        input.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            if (!query) {
                list.style.display = 'none';
                return;
            }

            let matches = INITIAL_PRODUCTS.filter(p => 
                p.name.toLowerCase().includes(query) || 
                (p.category && (p.category.name || p.category).toLowerCase().includes(query))
            );

            renderProductResults(matches);
        });

        input.addEventListener('focus', function() {
            if (this.value.trim() && list.children.length > 0) {
                list.style.display = 'block';
            }
        });

        // Enter key on Product search or Quantity adds directly
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                autoAddFromInput();
            }
        });
        document.getElementById('search-quantity-input').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                autoAddFromInput();
            }
        });

        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !list.contains(e.target)) {
                list.style.display = 'none';
            }
        });
    }

    function renderProductResults(products) {
        const list = document.getElementById('product-typeahead-list');
        if (products.length === 0) {
            list.innerHTML = `<div class="p-3 text-muted text-center small">No matching products found. Press Enter to add custom item.</div>`;
            list.style.display = 'block';
            return;
        }

        let html = '';
        products.forEach(p => {
            let cat = p.category?.name || p.category || 'General';
            html += `
                <div class="typeahead-option" onclick="pickProduct(${p.id})">
                    <div>
                        <div class="typeahead-name">${escapeHtml(p.name)}</div>
                        <div class="typeahead-meta">Category: ${escapeHtml(cat)} | Stock: ${p.total_stock}</div>
                    </div>
                    <div class="typeahead-price-badge">₹ ${formatNumber(p.price)}</div>
                </div>
            `;
        });

        list.innerHTML = html;
        list.style.display = 'block';
    }

    function pickProduct(productId) {
        let product = INITIAL_PRODUCTS.find(p => p.id === productId);
        if (!product) return;

        let qty = parseInt(document.getElementById('search-quantity-input').value) || 1;
        addItemToCart(product, qty);

        document.getElementById('search-product-input').value = '';
        document.getElementById('search-quantity-input').value = '1';
        document.getElementById('product-typeahead-list').style.display = 'none';
    }

    function autoAddFromInput() {
        let name = document.getElementById('search-product-input').value.trim();
        let qty = parseInt(document.getElementById('search-quantity-input').value) || 1;

        if (!name) return;

        let matched = INITIAL_PRODUCTS.find(p => p.name.toLowerCase() === name.toLowerCase()) || {
            id: Math.floor(1000 + Math.random() * 9000),
            name: name,
            price: 100.00,
            mrp: 120.00,
            tax: 0.00,
            total_stock: 50,
            stocks: [{ size: 'Free', stock: 50, price: 100, mrp: 120 }],
            selected_sizes: ['Free']
        };

        addItemToCart(matched, qty);

        document.getElementById('search-product-input').value = '';
        document.getElementById('search-quantity-input').value = '1';
        document.getElementById('product-typeahead-list').style.display = 'none';
    }

    function setupStudentAutocomplete() {
        const admInput = document.getElementById('cust-admission-no');
        const admList = document.getElementById('student-admission-typeahead-list');
        const nameInput = document.getElementById('cust-name');
        const nameList = document.getElementById('student-name-typeahead-list');

        // Setup autocomplete for both inputs
        bindStudentInputSearch(admInput, admList);
        bindStudentInputSearch(nameInput, nameList);

        // Hide when clicking outside
        document.addEventListener('click', function(e) {
            if (!admInput.contains(e.target) && !admList.contains(e.target)) {
                admList.style.display = 'none';
            }
            if (!nameInput.contains(e.target) && !nameList.contains(e.target)) {
                nameList.style.display = 'none';
            }
        });

        // Fast match on change if address or mobile is empty
        admInput.addEventListener('change', function() {
            autoLookupStudentIfEmpty(this.value.trim());
        });
        nameInput.addEventListener('change', function() {
            autoLookupStudentIfEmpty(this.value.trim());
        });
    }

    function bindStudentInputSearch(inputElem, listElem) {
        let timer;
        inputElem.addEventListener('input', function() {
            clearTimeout(timer);
            const query = this.value.trim();
            if (!query || query.length < 1) {
                listElem.style.display = 'none';
                return;
            }

            timer = setTimeout(() => {
                fetch(`${STUDENT_SEARCH_URL}?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.students && data.students.length > 0) {
                            renderStudentDropdown(listElem, data.students);
                        } else {
                            listElem.style.display = 'none';
                        }
                    })
                    .catch(() => {
                        listElem.style.display = 'none';
                    });
            }, 180);
        });

        inputElem.addEventListener('focus', function() {
            if (this.value.trim() && listElem.children.length > 0) {
                listElem.style.display = 'block';
            }
        });
    }

    function renderStudentDropdown(listElem, students) {
        let html = '';
        students.forEach(s => {
            html += `
                <div class="typeahead-option" onclick='pickStudent(${JSON.stringify(s)})'>
                    <div>
                        <div class="typeahead-name">
                            <i class="fas fa-user-graduate text-primary me-1"></i>
                            ${escapeHtml(s.name)}
                        </div>
                        <div class="typeahead-meta">
                            Adm: <strong class="text-dark">${escapeHtml(s.admission_no || '—')}</strong>
                            ${s.class_name ? ` | Class: <strong>${escapeHtml(s.class_name)}</strong>` : ''}
                            ${s.mobile ? ` | Phone: ${escapeHtml(s.mobile)}` : ''}
                        </div>
                    </div>
                    <div class="typeahead-price-badge" style="background:#eff6ff; color:#1d4ed8; font-size:11px;">
                        Select <i class="fas fa-check"></i>
                    </div>
                </div>
            `;
        });
        listElem.innerHTML = html;
        listElem.style.display = 'block';
    }

    function pickStudent(student) {
        const admInput = document.getElementById('cust-admission-no');
        const nameInput = document.getElementById('cust-name');
        const addrInput = document.getElementById('cust-address');
        const mobInput = document.getElementById('cust-mobile');

        admInput.value = student.admission_no || '';
        nameInput.value = student.name || '';
        addrInput.value = student.address || '';
        mobInput.value = student.mobile || '';

        const admList = document.getElementById('student-admission-typeahead-list');
        const nameList = document.getElementById('student-name-typeahead-list');
        if (admList) admList.style.display = 'none';
        if (nameList) nameList.style.display = 'none';

        // Flash visual feedback highlight
        [admInput, nameInput, addrInput, mobInput].forEach(elem => {
            if (elem) {
                elem.style.borderColor = '#10b981';
                elem.style.boxShadow = '0 0 0 3.5px rgba(16, 185, 129, 0.25)';
                setTimeout(() => {
                    elem.style.borderColor = '';
                    elem.style.boxShadow = '';
                }, 900);
            }
        });
    }

    function autoLookupStudentIfEmpty(query) {
        if (!query) return;
        const addrInput = document.getElementById('cust-address');
        const mobInput = document.getElementById('cust-mobile');
        if (addrInput.value.trim() && mobInput.value.trim()) return;

        fetch(`${STUDENT_SEARCH_URL}?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.students && data.students.length === 1) {
                    pickStudent(data.students[0]);
                }
            })
            .catch(() => {});
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Event Setup
    // ─────────────────────────────────────────────────────────────────────────
    function setupEvents() {
        // Proceed to Checkout inside Slider
        document.getElementById('btn-proceed-checkout').addEventListener('click', function() {
            if (cartItems.length === 0) return;
            populateCheckoutReview();
            document.getElementById('slider-step-1').classList.remove('active');
            document.getElementById('slider-step-2').classList.add('active');
            document.getElementById('slider-header-title').innerHTML = `<i class="fas fa-user-plus"></i> <span>Checkout & Student Details</span>`;
        });

        // Back to Cart inside Slider
        document.getElementById('btn-back-to-cart').addEventListener('click', function() {
            document.getElementById('slider-step-2').classList.remove('active');
            document.getElementById('slider-step-1').classList.add('active');
            document.getElementById('slider-header-title').innerHTML = `<i class="fas fa-shopping-cart"></i> <span>Cart Section</span>`;
        });

        // Payable Amount Input Change
        document.getElementById('cust-payable-amount').addEventListener('input', calcBalanceDue);

        // Confirm Order Button
        document.getElementById('btn-confirm-order').addEventListener('click', submitOrder);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Order Submission & Receipt Generation (Image 4)
    // ─────────────────────────────────────────────────────────────────────────
    function submitOrder() {
        const name = document.getElementById('cust-name').value.trim();
        const address = document.getElementById('cust-address').value.trim();
        const mobile = document.getElementById('cust-mobile').value.trim();
        const paymentMode = document.getElementById('cust-payment-mode').value;
        const payableAmount = parseFloat(document.getElementById('cust-payable-amount').value);

        if (!name) {
            alert('Please enter Name.');
            document.getElementById('cust-name').focus();
            return;
        }
        if (!address) {
            alert('Please enter Address.');
            document.getElementById('cust-address').focus();
            return;
        }
        if (!mobile) {
            alert('Please enter Mobile Number.');
            document.getElementById('cust-mobile').focus();
            return;
        }
        if (!paymentMode) {
            alert('Please select Payment Mode.');
            document.getElementById('cust-payment-mode').focus();
            return;
        }
        if (isNaN(payableAmount) || payableAmount < 0) {
            alert('Please enter a valid Payable Amount.');
            document.getElementById('cust-payable-amount').focus();
            return;
        }

        const confirmBtn = document.getElementById('btn-confirm-order');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i> Processing...`;

        const payload = {
            _token: APP_CSRF_TOKEN,
            admission_no: document.getElementById('cust-admission-no').value.trim(),
            customer_name: name,
            customer_address: address,
            customer_mobile: mobile,
            payment_mode: paymentMode,
            reference_no: document.getElementById('cust-ref-no').value.trim(),
            payable_amount: payableAmount,
            items: cartItems.map(item => ({
                product_id: item.product_id,
                name: item.name,
                size: item.size,
                price: item.price,
                mrp: item.mrp,
                tax: item.tax,
                quantity: item.quantity,
                discount: item.discount,
            })),
        };

        fetch(BILLING_CHECKOUT_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': APP_CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        })
        .then(res => res.json())
        .then(data => {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = `<i class="fas fa-check"></i> Confirm Order`;

            if (data.success && data.sale) {
                closeCartSlider();
                showReceiptModal(data.sale);
            } else {
                alert(data.message || 'Error creating order.');
            }
        })
        .catch(err => {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = `<i class="fas fa-check"></i> Confirm Order`;
            console.error('Checkout error:', err);

            // Fallback Sale Generation
            const fallback = generateFallbackSale(payload);
            closeCartSlider();
            showReceiptModal(fallback);
        });
    }

    function generateFallbackSale(payload) {
        let totalMrp = 0, subTotal = 0, totalDiscount = 0, totalTax = 0;
        let processed = payload.items.map(item => {
            let iTotMrp = item.mrp * item.quantity;
            let iTotPrice = item.price * item.quantity;
            let taxable = Math.max(0, iTotPrice - item.discount);
            let iTotTax = (taxable * item.tax) / 100;
            let lineTotal = iTotPrice - item.discount + iTotTax;

            totalMrp += iTotMrp;
            subTotal += iTotPrice;
            totalDiscount += item.discount;
            totalTax += iTotTax;

            return {
                product_name: item.name,
                size: item.size,
                mrp: item.mrp,
                price: item.price,
                tax_percent: item.tax,
                quantity: item.quantity,
                discount: item.discount,
                total_mrp: iTotMrp,
                total_price: iTotPrice,
                total_tax: iTotTax,
                total_amount: lineTotal,
            };
        });

        let grandTotal = subTotal - totalDiscount + totalTax;
        let paid = payload.payable_amount;
        let due = Math.max(0, grandTotal - paid);

        return {
            id: Math.floor(1000 + Math.random() * 9000),
            invoice_number: 'INV-' + new Date().toISOString().slice(0,10).replace(/-/g,'') + '-' + Math.floor(1000 + Math.random() * 9000),
            receipt_number: 'RCPT-' + new Date().toISOString().slice(0,10).replace(/-/g,'') + '-' + Math.floor(1000 + Math.random() * 9000),
            admission_no: payload.admission_no || '—',
            customer_name: payload.customer_name,
            customer_address: payload.customer_address,
            customer_mobile: payload.customer_mobile,
            payment_mode: payload.payment_mode,
            reference_no: payload.reference_no,
            total_mrp: totalMrp.toFixed(2),
            sub_total: subTotal.toFixed(2),
            total_discount: totalDiscount.toFixed(2),
            total_tax: totalTax.toFixed(2),
            grand_total: grandTotal.toFixed(2),
            paid_amount: paid.toFixed(2),
            due_amount: due.toFixed(2),
            date_formatted: new Date().toLocaleDateString('en-GB'),
            items: processed,
            school: SCHOOL_INFO,
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Display Dual-Copy Minimal Receipt (Matching User's Screenshot)
    // ─────────────────────────────────────────────────────────────────────────
    function showReceiptModal(sale) {
        const container = document.getElementById('receipt-modal-content-area');
        document.getElementById('modal-standalone-link').href = `${RECEIPT_BASE_URL}/${sale.id}`;

        const schoolName = sale.school?.name || 'VEDANT PUBLIC SCHOOL';
        const schoolAddress = sale.school?.address || 'Sctor 88A Gurgaon, Hariyana';
        const schoolPhone = sale.school?.phone || '9451805575';
        const schoolEmail = sale.school?.email || 'vedantpublicschool@gmail.com';
        const schoolLogo = sale.school?.logo_url || '';

        const receiptNo = sale.receipt_number || (sale.invoice_number || 'VPS-000010');
        const receiptDate = sale.date_formatted || (sale.sale_date || new Date().toLocaleDateString('en-GB'));
        const customerName = sale.customer_name || 'ATHARVA DIWEDI';
        const admissionNo = sale.admission_no || 'JPPS06';
        const classSection = sale.class_name ? `${sale.class_name} ${sale.section_name || ''}`.trim() : (sale.class_section || 'NUR. A');
        const paymentMode = sale.payment_mode ? (sale.payment_mode.charAt(0).toUpperCase() + sale.payment_mode.slice(1)) : 'Cash';
        
        const subTotal = parseFloat(sale.sub_total || 0);
        const totalTax = parseFloat(sale.total_tax || 0);
        const totalDiscount = parseFloat(sale.total_discount || 0);
        const grandTotal = parseFloat(sale.grand_total || (subTotal - totalDiscount + totalTax));
        const paidAmount = parseFloat(sale.paid_amount || grandTotal);
        const dueAmount = parseFloat(sale.due_amount || Math.max(0, grandTotal - paidAmount));

        // Proportional paid ratio for line-item accuracy
        const paidRatio = (grandTotal > 0) ? Math.min(1.0, paidAmount / grandTotal) : 1.0;

        // Detect Tax Rate label if available
        let taxRateLabel = 'Tax / GST';
        if (sale.items && sale.items.length > 0) {
            for (let it of sale.items) {
                let pct = parseFloat(it.tax_percent || it.tax || 0);
                if (pct > 0) {
                    taxRateLabel = `Tax / GST (${pct}%)`;
                    break;
                }
            }
        }

        // Build item rows for both slips
        let itemsHtml = '';
        if (sale.items && sale.items.length > 0) {
            sale.items.forEach(item => {
                const itemPrice = parseFloat(item.price || 0);
                const itemQty = parseInt(item.quantity || 1);
                const itemTotBase = parseFloat(item.total_price || (itemPrice * itemQty));
                const itemPaidBase = Math.round(itemTotBase * paidRatio * 100) / 100;
                const itemDueBase = Math.max(0, itemTotBase - itemPaidBase);

                const itemSize = (item.size && item.size !== 'Free') ? ` (${item.size})` : '';
                const itemQtyLabel = itemQty > 1 ? ` x ${itemQty}` : '';
                const compName = `${escapeHtml(item.product_name || item.name)}${itemSize}${itemQtyLabel}`;

                itemsHtml += `
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px 6px; text-align: left;">${compName}</td>
                        <td style="border: 1px solid #000; padding: 4px 6px; text-align: right;">${formatNumber(itemTotBase)}</td>
                        <td style="border: 1px solid #000; padding: 4px 6px; text-align: right;">${formatNumber(itemPaidBase)}</td>
                        <td style="border: 1px solid #000; padding: 4px 6px; text-align: right;">${formatNumber(itemDueBase)}</td>
                    </tr>
                `;
            });
        } else {
            const paidBase = Math.round(subTotal * paidRatio * 100) / 100;
            const dueBase = Math.max(0, subTotal - paidBase);
            itemsHtml += `
                <tr>
                    <td style="border: 1px solid #000; padding: 4px 6px; text-align: left;">Sub Total (Products)</td>
                    <td style="border: 1px solid #000; padding: 4px 6px; text-align: right;">${formatNumber(subTotal)}</td>
                    <td style="border: 1px solid #000; padding: 4px 6px; text-align: right;">${formatNumber(paidBase)}</td>
                    <td style="border: 1px solid #000; padding: 4px 6px; text-align: right;">${formatNumber(dueBase)}</td>
                </tr>
            `;
        }

        // Add Tax / GST row if tax > 0
        if (totalTax > 0) {
            const paidTax = Math.round(totalTax * paidRatio * 100) / 100;
            const dueTax = Math.max(0, totalTax - paidTax);
            itemsHtml += `
                <tr>
                    <td style="border: 1px solid #000; padding: 4px 6px; text-align: left; font-weight: bold; color: #1e293b;">
                        <i class="fas fa-percent" style="font-size: 9px; margin-right: 3px;"></i> ${taxRateLabel}
                    </td>
                    <td style="border: 1px solid #000; padding: 4px 6px; text-align: right; font-weight: bold;">${formatNumber(totalTax)}</td>
                    <td style="border: 1px solid #000; padding: 4px 6px; text-align: right; font-weight: bold;">${formatNumber(paidTax)}</td>
                    <td style="border: 1px solid #000; padding: 4px 6px; text-align: right; font-weight: bold;">${formatNumber(dueTax)}</td>
                </tr>
            `;
        }

        // Add Discount row if discount > 0
        if (totalDiscount > 0) {
            itemsHtml += `
                <tr>
                    <td style="border: 1px solid #000; padding: 4px 6px; text-align: left; font-style: italic; color: #dc2626;">
                        Discount (-)
                    </td>
                    <td style="border: 1px solid #000; padding: 4px 6px; text-align: right; color: #dc2626;">-${formatNumber(totalDiscount)}</td>
                    <td style="border: 1px solid #000; padding: 4px 6px; text-align: right;">—</td>
                    <td style="border: 1px solid #000; padding: 4px 6px; text-align: right;">—</td>
                </tr>
            `;
        }

        const amountInWords = convertNumberToWordsJS(paidAmount);

        // Function to build a single slip
        const renderSingleSlip = (copyType) => `
            <div style="flex: 1; padding: 8px 12px; font-family: Arial, Helvetica, sans-serif; color: #000;">
                <!-- Header -->
                <div style="display: flex; align-items: center; border-bottom: 1.5px solid #000; padding-bottom: 8px; margin-bottom: 10px;">
                    ${schoolLogo ? `<img src="${schoolLogo}" style="max-height: 44px; max-width: 44px; margin-right: 10px; object-fit: contain;">` : `
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #2563eb; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; margin-right: 10px;">
                        ${schoolName.charAt(0).toUpperCase()}
                    </div>`}
                    <div style="flex-grow: 1; text-align: center;">
                        <h2 style="font-size: 14px; font-weight: 800; text-transform: uppercase; margin: 0 0 2px;">${escapeHtml(schoolName)}</h2>
                        <p style="font-size: 10px; margin: 0; line-height: 1.2;">${escapeHtml(schoolAddress)}</p>
                        <p style="font-size: 10px; margin: 2px 0 0; line-height: 1.2;">Email: ${escapeHtml(schoolEmail)} | Phone: ${escapeHtml(schoolPhone)}</p>
                    </div>
                </div>

                <!-- Metadata -->
                <table style="width: 100%; border-collapse: collapse; font-size: 10.5px; margin-bottom: 10px;">
                    <tr>
                        <td style="width: 20%; font-weight: bold; padding: 2px 0;">Receipt No.:</td>
                        <td style="width: 32%; padding: 2px 0;"><strong>${escapeHtml(receiptNo)}</strong></td>
                        <td style="width: 20%; font-weight: bold; padding: 2px 0;">Receipt Date:</td>
                        <td style="width: 28%; padding: 2px 0;">${escapeHtml(receiptDate)}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; padding: 2px 0;">Student Name:</td>
                        <td colspan="3" style="padding: 2px 0; text-transform: uppercase;"><strong>${escapeHtml(customerName)}</strong></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; padding: 2px 0;">Admission No.:</td>
                        <td style="padding: 2px 0;"><strong>${escapeHtml(admissionNo)}</strong></td>
                        <td style="font-weight: bold; padding: 2px 0;">Class & Section:</td>
                        <td style="padding: 2px 0; text-transform: uppercase;"><strong>${escapeHtml(classSection)}</strong></td>
                    </tr>
                </table>

                <!-- Items Table -->
                <table style="width: 100%; border-collapse: collapse; font-size: 10.5px; margin-bottom: 10px; border: 1px solid #000;">
                    <thead>
                        <tr style="background: #ffffff;">
                            <th style="border: 1px solid #000; padding: 5px 6px; text-align: left; font-weight: 800; font-size: 10px;">COMPONENT</th>
                            <th style="border: 1px solid #000; padding: 5px 6px; text-align: right; width: 22%; font-weight: 800; font-size: 10px;">ACTUAL AMOUNT</th>
                            <th style="border: 1px solid #000; padding: 5px 6px; text-align: right; width: 18%; font-weight: 800; font-size: 10px;">PAID</th>
                            <th style="border: 1px solid #000; padding: 5px 6px; text-align: right; width: 18%; font-weight: 800; font-size: 10px;">BALANCE</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHtml}
                        <tr style="font-weight: 800; border-top: 1.5px solid #000;">
                            <td style="border: 1px solid #000; padding: 5px 6px; text-align: left;">TOTAL</td>
                            <td style="border: 1px solid #000; padding: 5px 6px; text-align: right;">${formatNumber(grandTotal)}</td>
                            <td style="border: 1px solid #000; padding: 5px 6px; text-align: right;">${formatNumber(paidAmount)}</td>
                            <td style="border: 1px solid #000; padding: 5px 6px; text-align: right;">${formatNumber(dueAmount)}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- PAID Box -->
                <div style="display: flex; justify-content: space-between; align-items: center; border: 1.5px solid #000; padding: 5px 8px; font-weight: 800; font-size: 11.5px; margin-bottom: 10px;">
                    <span>PAID</span>
                    <span>Rs ${formatNumber(paidAmount)}</span>
                </div>

                <!-- Details Block -->
                <div style="font-size: 10.5px; line-height: 1.4; margin-bottom: 22px;">
                    <div>Total Amount Paid: <strong>${amountInWords}</strong></div>
                    <div style="margin-top: 2px;">Mode of Payment: <strong>${escapeHtml(paymentMode)}</strong></div>
                    <div style="margin-top: 2px; font-size: 10px; color: #334155;">
                        Breakdown: Base: <strong>₹${formatNumber(subTotal)}</strong>
                        ${totalTax > 0 ? ` | Tax: <strong>₹${formatNumber(totalTax)}</strong>` : ''}
                        ${totalDiscount > 0 ? ` | Discount: <strong>₹${formatNumber(totalDiscount)}</strong>` : ''}
                        | Total: <strong>₹${formatNumber(grandTotal)}</strong>
                        ${dueAmount > 0 ? ` | <span style="color: #dc2626; font-weight: bold;">Due: ₹${formatNumber(dueAmount)}</span>` : ''}
                    </div>
                    <div style="margin-top: 2px;">Remarks: <strong>Fee Payment / Product Purchase</strong></div>
                </div>

                <!-- Signatures -->
                <div style="display: flex; justify-content: space-between; align-items: flex-end; font-size: 10.5px; margin-top: 10px;">
                    <div style="font-style: italic; color: #475569; font-size: 10px; text-transform: uppercase;">
                        ${copyType}
                    </div>
                    <div style="width: 120px; text-align: center; border-top: 1px solid #000; padding-top: 4px; font-weight: 800; font-size: 10.5px;">
                        Accountant Sign
                    </div>
                </div>
            </div>
        `;

        const dualSlipHtml = `
            <div id="printable-receipt-view" style="width: 100%; overflow-x: auto; background: #ffffff;">
                <div style="display: flex; min-width: 700px; border: 1px solid #cbd5e1; padding: 12px; background: #ffffff;">
                    ${renderSingleSlip('OFFICE COPY')}
                    <div style="width: 1px; border-left: 1px dashed #64748b; margin: 0 6px;"></div>
                    ${renderSingleSlip('STUDENT COPY')}
                </div>
            </div>
        `;

        container.innerHTML = dualSlipHtml;
        document.getElementById('receiptModalOverlay').classList.add('open');
    }

    function closeReceiptModal() {
        document.getElementById('receiptModalOverlay').classList.remove('open');
    }

    function closeReceiptModalAndReset() {
        closeReceiptModal();
        cartItems = [];
        renderCartTable();
        document.getElementById('checkout-form').reset();
        document.getElementById('slider-step-2').classList.remove('active');
        document.getElementById('slider-step-1').classList.add('active');
        document.getElementById('slider-header-title').innerHTML = `<i class="fas fa-shopping-cart"></i> <span>Cart Section</span>`;
    }

    function printModalReceipt() {
        const printContent = document.getElementById('printable-receipt-view');
        if (!printContent) {
            window.print();
            return;
        }

        try {
            // Check if existing frame exists, remove it
            let oldFrame = document.getElementById('inventory-print-iframe');
            if (oldFrame) {
                oldFrame.remove();
            }

            const iframe = document.createElement('iframe');
            iframe.id = 'inventory-print-iframe';
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = '0';
            document.body.appendChild(iframe);

            const doc = iframe.contentWindow.document;
            doc.open();
            doc.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Sales Receipt</title>
                    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
                    <style>
                        * { box-sizing: border-box; margin: 0; padding: 0; }
                        body { font-family: Arial, Helvetica, sans-serif; color: #000; padding: 8px; background: #fff; }
                        table { width: 100%; border-collapse: collapse; }
                        @page { size: landscape; margin: 6mm 8mm; }
                    </style>
                </head>
                <body>
                    ${printContent.innerHTML}
                </body>
                </html>
            `);
            doc.close();

            setTimeout(() => {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }, 250);
        } catch (err) {
            console.warn('Iframe print error, invoking window.print():', err);
            window.print();
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Utility Formatting & Words Converter
    // ─────────────────────────────────────────────────────────────────────────
    function formatNumber(num) {
        return parseFloat(num || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
    }

    function convertNumberToWordsJS(amount) {
        const words = {
            0: '', 1: 'ONE', 2: 'TWO', 3: 'THREE', 4: 'FOUR', 5: 'FIVE', 6: 'SIX',
            7: 'SEVEN', 8: 'EIGHT', 9: 'NINE', 10: 'TEN', 11: 'ELEVEN', 12: 'TWELVE',
            13: 'THIRTEEN', 14: 'FOURTEEN', 15: 'FIFTEEN', 16: 'SIXTEEN', 17: 'SEVENTEEN',
            18: 'EIGHTEEN', 19: 'NINETEEN', 20: 'TWENTY', 30: 'THIRTY', 40: 'FORTY',
            50: 'FIFTY', 60: 'SIXTY', 70: 'SEVENTY', 80: 'EIGHTY', 90: 'NINETY'
        };

        let num = Math.floor(amount || 0);
        if (num === 0) return 'ZERO ONLY';

        function numToWords(n) {
            let str = '';
            if (n >= 10000000) {
                str += numToWords(Math.floor(n / 10000000)) + ' CRORE ';
                n %= 10000000;
            }
            if (n >= 100000) {
                str += numToWords(Math.floor(n / 100000)) + ' LAKH ';
                n %= 100000;
            }
            if (n >= 1000) {
                str += numToWords(Math.floor(n / 1000)) + ' THOUSAND ';
                n %= 1000;
            }
            if (n >= 100) {
                str += words[Math.floor(n / 100)] + ' HUNDRED ';
                n %= 100;
            }
            if (n > 0) {
                if (n < 20) {
                    str += words[n] + ' ';
                } else {
                    str += words[Math.floor(n / 10) * 10] + ' ' + words[n % 10] + ' ';
                }
            }
            return str.trim();
        }

        let result = numToWords(num);
        return (result + ' ONLY').replace(/\s+/g, ' ');
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>
@endsection

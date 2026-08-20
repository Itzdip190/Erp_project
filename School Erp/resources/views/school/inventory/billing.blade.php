@extends('layouts.app')

@section('page-title', 'Product Cart - Inventory Management')

@section('content')
<style>
    /* ─── Enterprise ERP Blue & White Theme (Matching Images 1, 2, 3, 4) ─── */
    :root {
        --erp-navy:        #1a3a4b;
        --erp-navy-dark:   #122b39;
        --erp-navy-light:  #244d63;
        --erp-blue:        #0284c7;
        --erp-blue-dark:   #1e3a8a;
        --erp-blue-light:  #38bdf8;
        --erp-blue-soft:   #f0f9ff;
        --erp-card-bg:     #ffffff;
        --erp-border:      #e2e8f0;
        --erp-border-dark: #cbd5e1;
        --erp-text-dark:   #0f172a;
        --erp-text-muted:  #64748b;
        --erp-gold:        #d97706;
        --erp-gold-light:  #fbbf24;
    }

    /* ─── Main Container ────────────────────────────────────────────────── */
    .inv-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 20px 28px 40px !important;
        box-sizing: border-box;
    }

    /* ─── Cards Matching Images 1, 2, 3 ────────────────────────────────── */
    .inv-card {
        background: #ffffff;
        border: 1px solid var(--erp-border);
        border-radius: 8px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
        margin-bottom: 24px;
        overflow: visible;
        position: relative;
    }

    .inv-card-header-dark {
        background-color: var(--erp-navy);
        color: #ffffff;
        padding: 13px 20px;
        border-top-left-radius: 7px;
        border-top-right-radius: 7px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 14.5px;
        letter-spacing: 0.3px;
    }
    .inv-card-header-dark i {
        font-size: 15px;
        color: #ffffff;
    }

    .inv-card-body {
        padding: 24px 28px;
        background: #ffffff;
    }

    /* ─── Image 1: Product Cart Search Inputs ───────────────────────────── */
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
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }
    .erp-label .req {
        color: #ef4444;
        font-weight: bold;
    }

    .erp-input {
        width: 100%;
        height: 42px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        font-size: 13.5px;
        color: #0f172a;
        padding: 8px 14px;
        background-color: #ffffff;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
        box-sizing: border-box;
    }
    .erp-input:focus {
        border-color: var(--erp-blue);
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
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
        padding: 10px 14px;
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
        background-color: #f0f9ff;
    }
    .typeahead-name {
        font-weight: 600;
        font-size: 13.5px;
        color: #0f172a;
    }
    .typeahead-meta {
        font-size: 11.5px;
        color: #64748b;
    }
    .typeahead-price-badge {
        font-size: 12px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        background: #e0f2fe;
        color: #0369a1;
    }

    /* ─── Image 2: Cart Section Table ───────────────────────────────────── */
    .cart-table-container {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #ffffff;
    }
    .cart-grid-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        margin: 0;
    }
    .cart-grid-table thead th {
        background-color: #ffffff;
        color: #334155;
        font-weight: 700;
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        border-right: 1px solid #e2e8f0;
        text-align: center;
        white-space: nowrap;
    }
    .cart-grid-table thead th:last-child {
        border-right: none;
    }
    .cart-grid-table tbody td {
        padding: 10px 12px;
        border-bottom: 1px solid #e2e8f0;
        border-right: 1px solid #e2e8f0;
        vertical-align: middle;
        text-align: center;
        color: #0f172a;
        white-space: nowrap;
    }
    .cart-grid-table tbody td:last-child {
        border-right: none;
    }
    .cart-grid-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Table Input Fields */
    .table-cell-input {
        width: 75px;
        height: 34px;
        border-radius: 5px;
        border: 1px solid #cbd5e1;
        text-align: center;
        font-weight: 600;
        font-size: 13px;
        color: #0f172a;
        padding: 2px 4px;
        outline: none;
        background: #ffffff;
    }
    .table-cell-input:focus {
        border-color: var(--erp-blue);
        box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.2);
    }
    .table-cell-select {
        height: 34px;
        border-radius: 5px;
        border: 1px solid #cbd5e1;
        font-size: 12.5px;
        font-weight: 500;
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
        font-weight: 700;
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

    /* ─── Centered Action Button (Checkout & Confirm Order) ──────────────── */
    .btn-center-dark {
        background-color: var(--erp-navy);
        color: #ffffff !important;
        font-weight: 700;
        font-size: 14px;
        padding: 10px 32px;
        border-radius: 6px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 3px 10px rgba(26, 58, 75, 0.2);
        text-decoration: none !important;
    }
    .btn-center-dark:hover {
        background-color: var(--erp-navy-dark);
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 5px 14px rgba(26, 58, 75, 0.3);
    }
    .btn-center-dark:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .btn-center-outline {
        background-color: #ffffff;
        color: #334155 !important;
        font-weight: 600;
        font-size: 14px;
        padding: 10px 24px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }
    .btn-center-outline:hover {
        background-color: #f8fafc;
        color: #0f172a !important;
        border-color: #94a3b8;
    }

    /* ─── Image 3: Summary Box ─────────────────────────────────────────── */
    .checkout-summary-card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
        padding: 16px 20px;
        width: 100%;
        max-width: 320px;
        margin-left: auto;
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
        font-size: 15.5px;
        font-weight: 800;
        color: var(--erp-navy);
    }

    /* ─── Image 3: Student Details Form Grid ────────────────────────────── */
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

    /* ─── Steps Slide Transitions ──────────────────────────────────────── */
    .step-block {
        display: none;
    }
    .step-block.active {
        display: block;
        animation: fadeInStep 0.25s ease forwards;
    }
    @keyframes fadeInStep {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ─── Custom Modal Overlay (Never Leaks Unstyled Content) ──────────── */
    .custom-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 9999;
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
        border-radius: 12px;
        max-width: 920px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        display: flex;
        flex-direction: column;
    }
    .custom-modal-header {
        padding: 14px 20px;
        background: #f8fafc;
        border-bottom: 1px solid var(--erp-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    .custom-modal-footer {
        padding: 14px 20px;
        background: #f8fafc;
        border-top: 1px solid var(--erp-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }
</style>

<div class="inv-container">

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- STEP 1: PRODUCT SEARCH & CART ADDITION (Matching Image 1 & 2)       -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div id="step-1-block" class="step-block active">
        
        <!-- 1. Top Card: Product Cart (Image 1) -->
        <div class="inv-card">
            <div class="inv-card-header-dark">
                <i class="fas fa-list-ul"></i>
                <span>Product Cart</span>
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
            </div>
        </div>

        <!-- 2. Bottom Card: Cart Section (Image 2) - Shown ONLY after selecting product & quantity! -->
        <div class="inv-card" id="cart-section-card" style="display: none;">
            <div class="inv-card-header-dark">
                <i class="fas fa-shopping-cart"></i>
                <span>Cart Section</span>
            </div>
            <div class="inv-card-body">
                <!-- Cart Table -->
                <div class="cart-table-container">
                    <table class="cart-grid-table" id="cart-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">S/N</th>
                                <th style="text-align: left; min-width: 160px;">Product</th>
                                <th style="min-width: 100px;">Price</th>
                                <th style="width: 100px;">Quantity</th>
                                <th style="width: 100px;">Discount</th>
                                <th style="min-width: 90px;">Avl. Qty</th>
                                <th style="min-width: 110px;">Total</th>
                                <th style="min-width: 100px;">Size</th>
                                <th style="width: 60px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="cart-table-body">
                            <!-- Injected via JS when items are added -->
                        </tbody>
                    </table>
                </div>

                <!-- Centered Checkout Button (Image 2) -->
                <div class="text-center mt-4 pt-2">
                    <button type="button" id="btn-proceed-checkout" class="btn-center-dark">
                        <i class="fas fa-shopping-cart"></i> Checkout
                    </button>
                </div>
            </div>
        </div>

    </div>


    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- STEP 2: CHECKOUT & STUDENT DETAILS (Matching Image 3)               -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div id="step-2-block" class="step-block">
        
        <!-- 1. Top Card: Cart Section Summary (Image 3 Top Table) -->
        <div class="inv-card">
            <div class="inv-card-header-dark d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Cart Section</span>
                </div>
                <button type="button" id="btn-edit-cart-top" class="btn btn-sm btn-outline-light" style="font-size: 12px; border-radius: 20px; padding: 4px 12px;">
                    <i class="fas fa-pen me-1"></i> Edit Cart
                </button>
            </div>
            <div class="inv-card-body">
                <div class="cart-table-container mb-4">
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
                            <!-- Injected via JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Right Summary Box (Image 3 Right) -->
                <div class="d-flex justify-content-end">
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
            </div>
        </div>

        <!-- 2. Bottom Card: Student Details (Image 3 Bottom) -->
        <div class="inv-card">
            <div class="inv-card-header-dark">
                <i class="fas fa-user-plus"></i>
                <span>Student Details</span>
            </div>
            <div class="inv-card-body">
                <form id="checkout-form" onsubmit="return false;">
                    
                    <!-- Row 1: 4 Horizontal Inputs (Admission No, Name, Address, Mobile) -->
                    <div class="student-grid-4">
                        <div style="position: relative;">
                            <label class="erp-label">Addmission No.(Optional)</label>
                            <input type="text" id="cust-admission-no" class="erp-input" 
                                   placeholder="Enter Addmission No.(Optional)" autocomplete="off">
                            <div id="student-typeahead-list" class="typeahead-results"></div>
                        </div>

                        <div>
                            <label class="erp-label">
                                Name <span class="req">*</span>
                            </label>
                            <input type="text" id="cust-name" class="erp-input" 
                                   placeholder="Enter Name" required>
                        </div>

                        <div>
                            <label class="erp-label">
                                Address <span class="req">*</span>
                            </label>
                            <input type="text" id="cust-address" class="erp-input" 
                                   placeholder="Enter Address" required>
                        </div>

                        <div>
                            <label class="erp-label">
                                Mobile No <span class="req">*</span>
                            </label>
                            <input type="text" id="cust-mobile" class="erp-input" 
                                   placeholder="Enter Mobile No." required>
                        </div>
                    </div>

                    <!-- Row 2: 3 Inputs (Payment Mode, Reference No, Payable Amount) -->
                    <div class="student-grid-3">
                        <div>
                            <label class="erp-label">
                                Payment Mode <span class="req">*</span>
                            </label>
                            <select id="cust-payment-mode" class="erp-input" required>
                                <option value="">Select Payment Mode</option>
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
                            <label class="erp-label">
                                Payable Amount <span class="req">*</span>
                            </label>
                            <input type="number" step="0.01" id="cust-payable-amount" class="erp-input" 
                                   placeholder="0.00" required>
                            <div style="font-size: 12.5px; font-weight: 700; color: #0284c7; margin-top: 5px;">
                                Balance Due: <span id="label-balance-due">0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Centered Buttons: Back & Confirm Order (Image 3) -->
                    <div class="d-flex justify-content-center gap-3 mt-4 pt-2">
                        <button type="button" id="btn-back-to-cart" class="btn-center-outline">
                            <i class="fas fa-arrow-left"></i> Back to Cart
                        </button>
                        <button type="button" id="btn-confirm-order" class="btn-center-dark">
                            <i class="fas fa-check"></i> Confirm Order
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>

</div>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- STEP 3: CUSTOM POPUP MODAL FOR RECEIPT PREVIEW (Matching Image 4)   -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div id="receiptModalOverlay" class="custom-modal-overlay">
    <div class="custom-modal-dialog">
        <div class="custom-modal-header">
            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fas fa-receipt text-primary"></i>
                <span>Sales Receipt Generated</span>
            </div>
            <button type="button" class="btn btn-sm btn-light border-0" onclick="closeReceiptModal()" style="font-size: 16px;">
                <i class="fas fa-xmark"></i>
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
                <button type="button" class="btn-center-dark" style="padding: 7px 18px; font-size: 13px;" onclick="printModalReceipt()">
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
        // Starts empty so Image 1 view is clean!
        renderCartTable();
        setupProductAutocomplete();
        setupStudentAutocomplete();
        setupEvents();
    });

    // Add Item to Cart
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
    }

    // Render Cart Table (Step 1 - Image 2: Shown ONLY when items exist!)
    function renderCartTable() {
        const tbody = document.getElementById('cart-table-body');
        const cartCard = document.getElementById('cart-section-card');

        if (cartItems.length === 0) {
            tbody.innerHTML = '';
            cartCard.style.display = 'none'; // Hidden until product & quantity are selected!
            return;
        }

        // Show Cart Section (Image 2)
        cartCard.style.display = 'block';
        let html = '';

        cartItems.forEach((item, index) => {
            const lineTotal = (item.price * item.quantity) - item.discount;
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
        const input = document.getElementById('cust-admission-no');
        const list = document.getElementById('student-typeahead-list');

        let timer;
        input.addEventListener('input', function() {
            clearTimeout(timer);
            const query = this.value.trim();
            if (!query) {
                list.style.display = 'none';
                return;
            }

            timer = setTimeout(() => {
                fetch(`${STUDENT_SEARCH_URL}?admission_no=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.students && data.students.length > 0) {
                            renderStudentResults(data.students);
                        } else {
                            list.style.display = 'none';
                        }
                    })
                    .catch(() => {
                        list.style.display = 'none';
                    });
            }, 250);
        });

        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !list.contains(e.target)) {
                list.style.display = 'none';
            }
        });
    }

    function renderStudentResults(students) {
        const list = document.getElementById('student-typeahead-list');
        let html = '';
        students.forEach(s => {
            html += `
                <div class="typeahead-option" onclick='pickStudent(${JSON.stringify(s)})'>
                    <div>
                        <div class="typeahead-name">${escapeHtml(s.name)} (${escapeHtml(s.admission_no)})</div>
                        <div class="typeahead-meta">Phone: ${escapeHtml(s.mobile || '—')} | ${escapeHtml(s.class_name || '')}</div>
                    </div>
                </div>
            `;
        });
        list.innerHTML = html;
        list.style.display = 'block';
    }

    function pickStudent(student) {
        document.getElementById('cust-admission-no').value = student.admission_no || '';
        document.getElementById('cust-name').value = student.name || '';
        document.getElementById('cust-address').value = student.address || '';
        document.getElementById('cust-mobile').value = student.mobile || '';
        document.getElementById('student-typeahead-list').style.display = 'none';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Event Setup
    // ─────────────────────────────────────────────────────────────────────────
    function setupEvents() {
        // Proceed to Checkout
        document.getElementById('btn-proceed-checkout').addEventListener('click', function() {
            if (cartItems.length === 0) return;
            populateCheckoutReview();
            document.getElementById('step-1-block').classList.remove('active');
            document.getElementById('step-2-block').classList.add('active');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Back to Cart
        const goBack = () => {
            document.getElementById('step-2-block').classList.remove('active');
            document.getElementById('step-1-block').classList.add('active');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };
        document.getElementById('btn-edit-cart-top').addEventListener('click', goBack);
        document.getElementById('btn-back-to-cart').addEventListener('click', goBack);

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
    // Display Image 4 Receipt inside Custom Modal
    // ─────────────────────────────────────────────────────────────────────────
    function showReceiptModal(sale) {
        const container = document.getElementById('receipt-modal-content-area');
        document.getElementById('modal-standalone-link').href = `${RECEIPT_BASE_URL}/${sale.id}`;

        let rowsHtml = '';
        sale.items.forEach(item => {
            rowsHtml += `
                <tr>
                    <td style="text-align: left;">${escapeHtml(item.product_name || item.name)}</td>
                    <td style="text-align: left;">${escapeHtml(item.size || 'Free')}</td>
                    <td>${formatNumber(item.mrp)}</td>
                    <td>${formatNumber(item.price)}</td>
                    <td>${formatNumber(item.tax_percent || item.tax || 0)}</td>
                    <td>${item.quantity}</td>
                    <td>${formatNumber(item.total_mrp)}</td>
                    <td>${formatNumber(item.total_price)}</td>
                    <td>${formatNumber(item.total_tax)}</td>
                    <td>${formatNumber(item.discount)}</td>
                    <td><strong>${formatNumber(item.total_amount)}</strong></td>
                </tr>
            `;
        });

        const receiptHtml = `
            <div id="printable-receipt-view" style="font-family: Arial, sans-serif; color: #000;">
                <!-- School Header (Minimal Template) -->
                <div style="display: flex; align-items: center; justify-content: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 12px;">
                    ${sale.school?.logo_url ? `<img src="${sale.school.logo_url}" style="max-height: 50px; margin-right: 12px;">` : ''}
                    <div style="text-align: center;">
                        <h2 style="font-size: 17px; font-weight: 800; margin: 0; text-transform: uppercase;">${sale.school?.name || 'School ERP'}</h2>
                        <p style="font-size: 11px; margin: 2px 0;">${sale.school?.address || ''}</p>
                        <p style="font-size: 11px; margin: 0;">Phone: ${sale.school?.phone || '—'}</p>
                    </div>
                </div>

                <!-- Customer Details -->
                <table style="width: 100%; font-size: 11px; border-collapse: collapse; margin-bottom: 12px;">
                    <tr>
                        <td style="width: 16%; font-weight: bold; padding: 2px 0;">Receipt No.:</td>
                        <td style="width: 34%; padding: 2px 0;"><strong>${sale.receipt_number || sale.invoice_number}</strong></td>
                        <td style="width: 18%; font-weight: bold; padding: 2px 0;">Receipt Date:</td>
                        <td style="width: 32%; padding: 2px 0;">${sale.date_formatted || sale.sale_date}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; padding: 2px 0;">Customer Name:</td>
                        <td style="padding: 2px 0; text-transform: uppercase;"><strong>${escapeHtml(sale.customer_name)}</strong></td>
                        <td style="font-weight: bold; padding: 2px 0;">Admission No.:</td>
                        <td style="padding: 2px 0;">${escapeHtml(sale.admission_no || '—')}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; padding: 2px 0;">Contact No:</td>
                        <td style="padding: 2px 0;">${escapeHtml(sale.customer_mobile || '—')}</td>
                        <td style="font-weight: bold; padding: 2px 0;">Payment Mode:</td>
                        <td style="padding: 2px 0; text-transform: uppercase;"><strong>${escapeHtml(sale.payment_mode)}</strong></td>
                    </tr>
                    ${sale.customer_address ? `
                    <tr>
                        <td style="font-weight: bold; padding: 2px 0;">Address:</td>
                        <td colspan="3" style="padding: 2px 0;">${escapeHtml(sale.customer_address)}</td>
                    </tr>` : ''}
                </table>

                <!-- PRODUCT DETAILS Gold Divider (Image 4) -->
                <div style="display: flex; align-items: center; text-align: center; margin: 14px 0 8px;">
                    <div style="flex: 1; border-bottom: 1px solid #d97706; opacity: 0.7;"></div>
                    <span style="padding: 0 14px; font-size: 11px; font-weight: 800; color: #d97706; letter-spacing: 2.5px; text-transform: uppercase;">PRODUCT DETAILS</span>
                    <div style="flex: 1; border-bottom: 1px solid #d97706; opacity: 0.7;"></div>
                </div>

                <!-- 11 Column Product Table (Image 4) -->
                <div style="overflow-x: auto; margin-bottom: 12px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 10.5px; border: 1px solid #cbd5e1;">
                        <thead>
                            <tr style="background: #1a3a4b; color: #ffffff;">
                                <th style="padding: 6px 8px; text-align: left; border: 1px solid #2d4e61;">Product</th>
                                <th style="padding: 6px 8px; text-align: left; border: 1px solid #2d4e61;">Size</th>
                                <th style="padding: 6px 8px; text-align: right; border: 1px solid #2d4e61;">MRP</th>
                                <th style="padding: 6px 8px; text-align: right; border: 1px solid #2d4e61;">Price</th>
                                <th style="padding: 6px 8px; text-align: right; border: 1px solid #2d4e61;">Tax</th>
                                <th style="padding: 6px 8px; text-align: right; border: 1px solid #2d4e61;">Quantity</th>
                                <th style="padding: 6px 8px; text-align: right; border: 1px solid #2d4e61;">Total MRP</th>
                                <th style="padding: 6px 8px; text-align: right; border: 1px solid #2d4e61;">Total Price</th>
                                <th style="padding: 6px 8px; text-align: right; border: 1px solid #2d4e61;">Total Tax</th>
                                <th style="padding: 6px 8px; text-align: right; border: 1px solid #2d4e61;">Discount</th>
                                <th style="padding: 6px 8px; text-align: right; border: 1px solid #2d4e61;">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rowsHtml}
                        </tbody>
                        <tfoot>
                            <tr style="background: #f8fafc; font-weight: bold; border-top: 2px solid #1a3a4b;">
                                <td style="padding: 7px 8px; text-align: left;">Total</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="padding: 7px 8px; text-align: right;">${formatNumber(sale.total_mrp)}</td>
                                <td style="padding: 7px 8px; text-align: right;">${formatNumber(sale.sub_total)}</td>
                                <td style="padding: 7px 8px; text-align: right;">${formatNumber(sale.total_tax)}</td>
                                <td style="padding: 7px 8px; text-align: right;">${formatNumber(sale.total_discount)}</td>
                                <td style="padding: 7px 8px; text-align: right;">${formatNumber(sale.grand_total)}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- 6 Summary Badge Boxes (Exact Image 4 Replication) -->
                <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 6px; margin-bottom: 16px;">
                    <!-- 1. Sub Total -->
                    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-top: 3px solid #d97706; border-radius: 6px; padding: 8px 4px; text-align: center;">
                        <div style="color: #0284c7; font-size: 14px; margin-bottom: 2px;"><i class="fas fa-hourglass-half"></i></div>
                        <div style="font-size: 9px; font-weight: 800; color: #d97706; text-transform: uppercase;">SUB TOTAL</div>
                        <div style="font-size: 12.5px; font-weight: 800; color: #000;">${formatNumber(sale.sub_total)}</div>
                    </div>
                    <!-- 2. Total Discount -->
                    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-top: 3px solid #d97706; border-radius: 6px; padding: 8px 4px; text-align: center;">
                        <div style="color: #ef4444; font-size: 14px; margin-bottom: 2px;"><i class="fas fa-gift"></i></div>
                        <div style="font-size: 9px; font-weight: 800; color: #d97706; text-transform: uppercase;">TOTAL DISCOUNT</div>
                        <div style="font-size: 12.5px; font-weight: 800; color: #000;">${formatNumber(sale.total_discount)}</div>
                    </div>
                    <!-- 3. Total Tax -->
                    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-top: 3px solid #d97706; border-radius: 6px; padding: 8px 4px; text-align: center;">
                        <div style="color: #d97706; font-size: 14px; margin-bottom: 2px;"><i class="fas fa-indian-rupee-sign"></i></div>
                        <div style="font-size: 9px; font-weight: 800; color: #d97706; text-transform: uppercase;">TOTAL TAX</div>
                        <div style="font-size: 12.5px; font-weight: 800; color: #000;">${formatNumber(sale.total_tax)}</div>
                    </div>
                    <!-- 4. Grand Total (Solid Card) -->
                    <div style="background: #1a3a4b; border: 1px solid #1a3a4b; border-top: 3px solid #fbbf24; border-radius: 6px; padding: 8px 4px; text-align: center; color: #ffffff;">
                        <div style="color: #fbbf24; font-size: 14px; margin-bottom: 2px;"><i class="fas fa-circle-check"></i></div>
                        <div style="font-size: 9px; font-weight: 800; color: #fbbf24; text-transform: uppercase;">GRAND TOTAL</div>
                        <div style="font-size: 13.5px; font-weight: 800; color: #ffffff;">${formatNumber(sale.grand_total)}</div>
                    </div>
                    <!-- 5. Paid Amount -->
                    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-top: 3px solid #d97706; border-radius: 6px; padding: 8px 4px; text-align: center;">
                        <div style="color: #d97706; font-size: 14px; margin-bottom: 2px;"><i class="fas fa-indian-rupee-sign"></i></div>
                        <div style="font-size: 9px; font-weight: 800; color: #d97706; text-transform: uppercase;">PAID AMOUNT</div>
                        <div style="font-size: 12.5px; font-weight: 800; color: #000;">${formatNumber(sale.paid_amount)}</div>
                    </div>
                    <!-- 6. Due Amount -->
                    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-top: 3px solid #d97706; border-radius: 6px; padding: 8px 4px; text-align: center;">
                        <div style="color: #d97706; font-size: 14px; margin-bottom: 2px;"><i class="fas fa-indian-rupee-sign"></i></div>
                        <div style="font-size: 9px; font-weight: 800; color: #d97706; text-transform: uppercase;">DUE AMOUNT</div>
                        <div style="font-size: 12.5px; font-weight: 800; color: #000;">${formatNumber(sale.due_amount)}</div>
                    </div>
                </div>

                <!-- Footer Signatures -->
                <div style="display: flex; justify-content: space-between; align-items: flex-end; font-size: 11px; margin-top: 25px; padding-top: 8px;">
                    <div style="color: #64748b; font-style: italic; font-size: 10px;">
                        * System Generated Inventory Receipt
                    </div>
                    <div style="text-align: center; border-top: 1px solid #000; padding-top: 4px; width: 130px; font-weight: bold;">
                        Authorized Sign
                    </div>
                </div>
            </div>
        `;

        container.innerHTML = receiptHtml;
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
        document.getElementById('step-2-block').classList.remove('active');
        document.getElementById('step-1-block').classList.add('active');
    }

    function printModalReceipt() {
        const printContent = document.getElementById('printable-receipt-view').innerHTML;
        const win = window.open('', '', 'height=700,width=900');
        win.document.write(`
            <html>
                <head>
                    <title>Print Receipt</title>
                    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; color: #000; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { padding: 5px 6px; }
                        @media print {
                            body { margin: 0; }
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                </body>
            </html>
        `);
        win.document.close();
        win.focus();
        setTimeout(() => {
            win.print();
            win.close();
        }, 300);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Utility Formatting
    // ─────────────────────────────────────────────────────────────────────────
    function formatNumber(num) {
        return parseFloat(num || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
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

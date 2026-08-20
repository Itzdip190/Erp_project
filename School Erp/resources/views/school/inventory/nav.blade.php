@use('App\Support\StaffAccessHelper')

<style>
    .custom-inv-nav .nav-link {
        color: #334155;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        font-weight: 500;
        font-size: 14px;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .custom-inv-nav .nav-link:hover {
        background: #f8fafc;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    .custom-inv-nav .nav-link.active {
        background: #ffffff;
        color: #0284c7;
        border-color: #0284c7;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.08);
        font-weight: 600;
    }

    .custom-inv-nav .nav-icon {
        width: 22px;
        text-align: center;
        margin-right: 10px;
    }
</style>

<div class="col-md-3 col-lg-3 col-xl-2 mb-4">
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-2">
            <div class="nav flex-column nav-pills custom-inv-nav">
                @if(\App\Support\StaffAccessHelper::hasAccess('inventory_management', 'item_category'))
                    <a href="{{ route('school.inventory.categories') }}"
                        class="nav-link d-flex align-items-center {{ request()->routeIs('school.inventory.categories') ? 'active' : '' }}">
                        <i class="fas fa-tag nav-icon text-primary"></i>
                        <span>Category</span>
                    </a>
                @endif

                @if(\App\Support\StaffAccessHelper::hasAccess('inventory_management', 'product_stock'))
                    <a href="{{ route('school.inventory.product-stock') }}"
                        class="nav-link d-flex align-items-center {{ request()->routeIs('school.inventory.product-stock') || request()->routeIs('school.inventory.stock') ? 'active' : '' }}">
                        <i class="fas fa-square-plus nav-icon text-success"></i>
                        <span>Product & Stock</span>
                    </a>
                @endif

                @if(\App\Support\StaffAccessHelper::hasAccess('inventory_management', 'billing'))
                    <a href="{{ route('school.inventory.billing') }}"
                        class="nav-link d-flex align-items-center {{ request()->routeIs('school.inventory.billing') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart nav-icon text-info"></i>
                        <span>Billing</span>
                    </a>
                @endif

                @if(\App\Support\StaffAccessHelper::hasAccess('inventory_management', 'sales_history'))
                    <a href="{{ route('school.inventory.sales-history') }}"
                        class="nav-link d-flex align-items-center {{ request()->routeIs('school.inventory.sales-history') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list nav-icon text-warning"></i>
                        <span>Sales History</span>
                    </a>
                @endif

                @if(\App\Support\StaffAccessHelper::hasAccess('inventory_management', 'stock_history'))
                    <a href="{{ route('school.inventory.stock-history') }}"
                        class="nav-link d-flex align-items-center {{ request()->routeIs('school.inventory.stock-history') ? 'active' : '' }}">
                        <i class="fas fa-boxes-stacked nav-icon text-secondary"></i>
                        <span>Stock History</span>
                    </a>
                @endif

                @if(\App\Support\StaffAccessHelper::hasAccess('inventory_management', 'payment_history'))
                    <a href="{{ route('school.inventory.payment-history') }}"
                        class="nav-link d-flex align-items-center {{ request()->routeIs('school.inventory.payment-history') ? 'active' : '' }}">
                        <span class="nav-icon fw-bold text-dark" style="font-size:13px;">Rs</span>
                        <span>Payment History</span>
                    </a>
                @endif

                @if(\App\Support\StaffAccessHelper::hasAccess('inventory_management', 'issue_item'))
                    <a href="{{ route('school.inventory.issue') }}"
                        class="nav-link d-flex align-items-center {{ request()->routeIs('school.inventory.issue') ? 'active' : '' }}">
                        <i class="fas fa-hand-holding nav-icon text-indigo"></i>
                        <span>Issue Item</span>
                    </a>
                @endif

                @if(\App\Support\StaffAccessHelper::hasAccess('inventory_management', 'suppliers'))
                    <a href="{{ route('school.inventory.suppliers') }}"
                        class="nav-link d-flex align-items-center {{ request()->routeIs('school.inventory.suppliers') ? 'active' : '' }}">
                        <i class="fas fa-truck nav-icon text-purple"></i>
                        <span>Suppliers</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@extends('layouts.app')

@section('page-title', 'Stock History - Inventory Management')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 font-weight-bold text-gray-800 mb-1">Stock History</h1>
            <p class="text-muted mb-0 small">Inventory Management / Stock History</p>
        </div>
    </div>

    <div class="row">
        @include('school.inventory.nav')

        <div class="col-md-9 col-lg-9 col-xl-10">
            <div class="card border-0 shadow-sm rounded-3" style="min-height: 450px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 font-weight-bold text-dark"><i class="fas fa-boxes-stacked me-2 text-secondary"></i>Stock History</h5>
                </div>
                <div class="card-body p-4">
                    <!-- Blank Page Content -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

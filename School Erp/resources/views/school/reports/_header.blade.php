{{--
    Shared Report Page Header Partial
    Usage: @include('school.reports._header', ['title'=>'...','icon'=>'fa-...','badge'=>'...','gradient'=>'linear-gradient(...)'])
--}}
<div style="max-width:1200px; margin:0 auto; padding:0 4px;">

    {{-- HERO HEADER --}}
    <div class="sr-hero" style="background: {{ $gradient ?? 'linear-gradient(135deg, #1e1b4b, #4f46e5)' }};">
        <div class="sr-hero-left">
            <div class="sr-breadcrumb">
                <a href="{{ route('school.reports.index') }}"><i class="fas fa-chart-pie"></i> All Reports</a>
                <i class="fas fa-chevron-right"></i>
                <span>{{ $title ?? 'Report' }}</span>
            </div>
            <h1 class="sr-hero-title">
                <i class="fas {{ $icon ?? 'fa-chart-bar' }}"></i>
                {{ $title ?? 'Report' }}
            </h1>
            <p class="sr-hero-subtitle">{{ $subtitle ?? 'Detailed analytics and data export' }}</p>
        </div>
        <div class="sr-hero-actions">
            <span style="font-size:12px; color:rgba(255,255,255,.65); font-weight:500;">
                <i class="fas fa-clock"></i> {{ now()->format('d M Y, h:i A') }}
            </span>
            <button class="sr-btn sr-btn-white no-print" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
            <button class="sr-btn sr-btn-outline no-print" id="downloadCsvBtn" onclick="downloadTableCSV()">
                <i class="fas fa-download"></i> Download CSV
            </button>
        </div>
    </div>

</div>

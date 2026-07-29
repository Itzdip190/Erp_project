@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="custom-pagination">
        <!-- Desktop Pagination View (Unchanged for Desktop) -->
        <div class="desktop-pagination-inner">
            <div class="pagination-info">
                Showing <span style="font-weight:700;">{{ $paginator->firstItem() }}</span> to <span style="font-weight:700;">{{ $paginator->lastItem() }}</span> of <span style="font-weight:700;">{{ $paginator->total() }}</span> results
            </div>

            <ul class="pagination-list">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="fas fa-chevron-left"></i></a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next"><i class="fas fa-chevron-right"></i></a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                    </li>
                @endif
            </ul>
        </div>

        <!-- Mobile Pagination View (2 Arrows ONLY for Mobile) -->
        <div class="mobile-pagination-inner">
            <div class="pagination-info">
                {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} of {{ $paginator->total() }}
            </div>
            
            <div class="mobile-arrow-btns">
                {{-- Previous Arrow Button --}}
                @if ($paginator->onFirstPage())
                    <span class="mobile-arrow-btn disabled" title="Previous Page">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="mobile-arrow-btn" title="Previous Page">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif

                <span class="mobile-page-indicator">
                    {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
                </span>

                {{-- Next Arrow Button --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="mobile-arrow-btn" title="Next Page">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <span class="mobile-arrow-btn disabled" title="Next Page">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif

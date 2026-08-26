@if ($paginator->hasPages())
    <style>
        .custom-pagination {
            width: 100%;
            display: block;
        }
        .pagination-container {
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            gap: 16px;
            flex-wrap: wrap;
        }
        .pagination-info {
            font-size: 13px;
            color: var(--t2, #64748b);
            font-weight: 500;
        }
        .pagination-info strong {
            color: var(--t1, #1e293b);
            font-weight: 700;
        }
        .pagination-list {
            display: flex;
            align-items: center;
            gap: 6px;
            list-style: none;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }
        .pagination-list .page-item {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .pagination-list .page-item .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            border-radius: 8px;
            border: 1px solid var(--border, #cbd5e1);
            background: var(--card, #ffffff);
            color: var(--t1, #1e293b);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s ease;
            box-sizing: border-box;
            line-height: 1;
            cursor: pointer;
        }
        .pagination-list .page-item.active .page-link {
            background: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(29, 78, 216, 0.3);
            font-weight: 700;
        }
        .pagination-list .page-item.disabled .page-link {
            opacity: 0.45;
            cursor: not-allowed;
            background: var(--page, #f8fafc);
            color: var(--t3, #94a3b8);
            border-color: var(--border, #cbd5e1);
        }
        .pagination-list .page-item .page-link:hover:not(.disabled) {
            border-color: #1d4ed8;
            color: #1d4ed8;
            background: rgba(29, 78, 216, 0.05);
        }
        .pagination-list .page-item.active .page-link:hover {
            background: #1e40af !important;
            color: #ffffff !important;
            border-color: #1e40af !important;
        }

        /* Dark mode overrides */
        body.dark-mode .pagination-info {
            color: #94a3b8;
        }
        body.dark-mode .pagination-info strong {
            color: #f1f5f9;
        }
        body.dark-mode .pagination-list .page-item .page-link {
            background: #1e293b;
            border-color: #334155;
            color: #cbd5e1;
        }
        body.dark-mode .pagination-list .page-item.active .page-link {
            background: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            color: #ffffff !important;
        }
        body.dark-mode .pagination-list .page-item.disabled .page-link {
            background: #0f172a;
            border-color: #1e293b;
            color: #475569;
        }
    </style>

    <nav role="navigation" aria-label="Pagination Navigation" class="custom-pagination">
        <div class="pagination-container">
            {{-- Results counter --}}
            <div class="pagination-info">
                Showing <strong>{{ $paginator->firstItem() ?? 1 }}</strong> to <strong>{{ $paginator->lastItem() ?? $paginator->total() }}</strong> of <strong>{{ $paginator->total() }}</strong> results
            </div>

            {{-- Numbered Page Buttons 1, 2, 3, 4... --}}
            <ul class="pagination-list">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" aria-label="Previous page"><i class="fas fa-chevron-left"></i></span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page"><i class="fas fa-chevron-left"></i></a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                    @endif

                    {{-- Array Of Links (1, 2, 3, 4...) --}}
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
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page"><i class="fas fa-chevron-right"></i></a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" aria-label="Next page"><i class="fas fa-chevron-right"></i></span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif

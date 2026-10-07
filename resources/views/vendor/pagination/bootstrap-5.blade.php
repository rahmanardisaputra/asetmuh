@if ($paginator->hasPages())
    <style>
        .custom-pagination { display: flex; list-style: none; padding: 0; margin: 0; gap: 0.25rem; justify-content: center; }
        .custom-pagination li a, .custom-pagination li span { 
            position: relative; display: block; padding: 0.375rem 0.75rem; 
            color: var(--primary); background-color: #fff; border: 1px solid #dee2e6; 
            border-radius: 4px; text-decoration: none; transition: 0.2s;
        }
        .custom-pagination li a:hover { background-color: #f1f5f9; }
        .custom-pagination li.active span { 
            background-color: var(--primary) !important; 
            color: #fff !important; 
            border-color: var(--primary) !important; 
        }
        .custom-pagination li.disabled span { color: #aaa; background-color: #fafafa; }
    </style>
    <nav style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
        <div style="font-size: 0.85rem; color: var(--text-muted);">
            Menampilkan <span style="font-weight: 600; color: var(--heading-color);">{{ $paginator->firstItem() }}</span> hingga <span style="font-weight: 600; color: var(--heading-color);">{{ $paginator->lastItem() }}</span> dari <span style="font-weight: 600; color: var(--heading-color);">{{ $paginator->total() }}</span> entri
        </div>
        
        <ul class="custom-pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
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
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif

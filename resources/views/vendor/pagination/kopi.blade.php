@if ($paginator->hasPages())
    <nav class="pager" role="navigation" aria-label="Navigasi halaman">
        @if ($paginator->onFirstPage())
            <span class="pager-btn disabled" aria-disabled="true" aria-label="Halaman sebelumnya"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></span>
        @else
            <a class="pager-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pager-btn disabled">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pager-btn active" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="pager-btn" href="{{ $url }}" aria-label="Ke halaman {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="pager-btn" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></a>
        @else
            <span class="pager-btn disabled" aria-disabled="true" aria-label="Halaman berikutnya"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></span>
        @endif
    </nav>
@endif

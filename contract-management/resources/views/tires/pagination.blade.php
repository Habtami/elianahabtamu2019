@if ($paginator->onFirstPage())
    <button type="button" class="tsr-page-btn" disabled>&laquo; Prev</button>
@else
    <a href="{{ $paginator->previousPageUrl() }}" class="tsr-page-btn" rel="prev">&laquo; Prev</a>
@endif

@if ($paginator->hasPages())
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="tsr-page-dots">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <button type="button" class="tsr-page-btn tsr-active" aria-current="page">{{ $page }}</button>
                @else
                    <a href="{{ $url }}" class="tsr-page-btn">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach
@else
    <button type="button" class="tsr-page-btn tsr-active" aria-current="page">1</button>
@endif

@if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" class="tsr-page-btn" rel="next">Next &raquo;</a>
@else
    <button type="button" class="tsr-page-btn" disabled>Next &raquo;</button>
@endif

{{-- Page links for a blog listing. Real links rather than the old AJAX
     ones (href="javascript:void(0)"), so search engines can reach every
     page past the first and each page has its own address. --}}
@if($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        // First, last, and two either side of the current page; gaps become "…".
        $pages = collect([1, $last])->merge(range(max(1, $current - 2), min($last, $current + 2)))->unique()->sort()->values();
    @endphp
    <nav class="g4b-pages" aria-label="Blog pages">
        @if($paginator->onFirstPage())
            <span class="g4b-page g4b-page--step is-off" aria-hidden="true">Previous</span>
        @else
            <a class="g4b-page g4b-page--step" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>
        @endif

        <ol>
            @foreach($pages as $i => $page)
                @if($i > 0 && $page - $pages[$i - 1] > 1)
                    <li class="g4b-page-gap" aria-hidden="true">…</li>
                @endif
                <li>
                    @if($page === $current)
                        <span class="g4b-page is-current" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="g4b-page" href="{{ $paginator->url($page) }}" aria-label="Page {{ $page }}">{{ $page }}</a>
                    @endif
                </li>
            @endforeach
        </ol>

        @if($paginator->hasMorePages())
            <a class="g4b-page g4b-page--step" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
        @else
            <span class="g4b-page g4b-page--step is-off" aria-hidden="true">Next</span>
        @endif
    </nav>
@endif

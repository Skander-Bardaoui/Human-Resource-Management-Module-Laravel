@props(['paginator'])

@if ($paginator->hasPages())
    <nav class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" aria-label="Pagination">
        <p class="font-label-sm text-label-sm text-on-surface-variant">
            Page {{ $paginator->currentPage() }} sur {{ $paginator->lastPage() }}
        </p>

        <div class="flex flex-wrap items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="rounded-lg px-3 py-1.5 font-label-md text-label-md text-on-surface-variant/50" aria-disabled="true">Précédent</span>
            @else
                <a class="rounded-lg px-3 py-1.5 font-label-md text-label-md text-on-surface-variant transition-colors hover:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" href="{{ $paginator->previousPageUrl() }}" rel="prev">Précédent</a>
            @endif

            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                @if ($page === $paginator->currentPage())
                    <span class="rounded-lg bg-primary-container px-3 py-1.5 font-label-md text-label-md font-semibold text-on-primary" aria-current="page">{{ $page }}</span>
                @else
                    <a class="rounded-lg px-3 py-1.5 font-label-md text-label-md text-on-surface-variant transition-colors hover:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" href="{{ $url }}" aria-label="Page {{ $page }}">{{ $page }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="rounded-lg px-3 py-1.5 font-label-md text-label-md text-on-surface-variant transition-colors hover:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" href="{{ $paginator->nextPageUrl() }}" rel="next">Suivant</a>
            @else
                <span class="rounded-lg px-3 py-1.5 font-label-md text-label-md text-on-surface-variant/50" aria-disabled="true">Suivant</span>
            @endif
        </div>
    </nav>
@endif

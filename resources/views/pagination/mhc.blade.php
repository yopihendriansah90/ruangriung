@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-4" aria-label="Navigasi pagination">
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center rounded-xl border border-[#d5eaf5] bg-[#f0f9ff] px-4 py-2 text-sm font-semibold text-[#91aebe]">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center rounded-xl border border-[#cfe5f2] bg-white px-4 py-2 text-sm font-semibold text-[#356b78] transition hover:border-[#147eaf] hover:text-[#147eaf]">Sebelumnya</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center rounded-xl border border-[#cfe5f2] bg-white px-4 py-2 text-sm font-semibold text-[#356b78] transition hover:border-[#147eaf] hover:text-[#147eaf]">Berikutnya</a>
            @else
                <span class="inline-flex items-center rounded-xl border border-[#d5eaf5] bg-[#f0f9ff] px-4 py-2 text-sm font-semibold text-[#91aebe]">Berikutnya</span>
            @endif
        </div>

        <div class="hidden flex-1 items-center justify-between sm:flex">
            <p class="text-sm text-[#5d7d86]">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</p>

            <div class="flex items-center gap-1">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-[#d5eaf5] bg-[#f0f9ff] text-[#91aebe]"><x-heroicon-o-chevron-left class="h-4 w-4" /></span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" aria-label="Halaman sebelumnya" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-[#cfe5f2] bg-white text-[#356b78] transition hover:border-[#147eaf] hover:text-[#147eaf]"><x-heroicon-o-chevron-left class="h-4 w-4" /></a>
                @endif

                @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg bg-[#65c6f1] px-2 text-sm font-semibold text-[#104b68]">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-[#cfe5f2] bg-white px-2 text-sm font-semibold text-[#356b78] transition hover:border-[#147eaf] hover:text-[#147eaf]">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" aria-label="Halaman berikutnya" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-[#cfe5f2] bg-white text-[#356b78] transition hover:border-[#147eaf] hover:text-[#147eaf]"><x-heroicon-o-chevron-right class="h-4 w-4" /></a>
                @else
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-[#d5eaf5] bg-[#f0f9ff] text-[#91aebe]"><x-heroicon-o-chevron-right class="h-4 w-4" /></span>
                @endif
            </div>
        </div>
    </nav>
@endif

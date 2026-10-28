@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-4 border-t border-[#d7e1df] pt-7" aria-label="Navigasi pagination">
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center rounded-xl border border-[#d7e1df] bg-[#ecefed] px-4 py-2 text-sm font-bold text-[#91a0a2]">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center rounded-xl border border-[#bfd8df] bg-white px-4 py-2 text-sm font-bold text-[#356b78] transition hover:border-[#147eaf] hover:text-[#147eaf]">Sebelumnya</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center rounded-xl border border-[#bfd8df] bg-white px-4 py-2 text-sm font-bold text-[#356b78] transition hover:border-[#147eaf] hover:text-[#147eaf]">Berikutnya</a>
            @else
                <span class="inline-flex items-center rounded-xl border border-[#d7e1df] bg-[#ecefed] px-4 py-2 text-sm font-bold text-[#91a0a2]">Berikutnya</span>
            @endif
        </div>

        <div class="hidden flex-1 items-center justify-between sm:flex">
            <p class="text-sm font-medium text-[#5d7d86]">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</p>

            <div class="flex items-center gap-1">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-[#d7e1df] bg-[#ecefed] text-[#91a0a2]"><i class="fa-solid fa-chevron-left text-xs" aria-hidden="true"></i></span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" aria-label="Halaman sebelumnya" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-[#bfd8df] bg-white text-[#356b78] transition hover:border-[#147eaf] hover:text-[#147eaf]"><i class="fa-solid fa-chevron-left text-xs" aria-hidden="true"></i></a>
                @endif

                @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="inline-flex h-10 min-w-10 items-center justify-center rounded-lg bg-[#104b68] px-2 text-sm font-bold text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-10 min-w-10 items-center justify-center rounded-lg border border-[#bfd8df] bg-white px-2 text-sm font-bold text-[#356b78] transition hover:border-[#147eaf] hover:text-[#147eaf]">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" aria-label="Halaman berikutnya" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-[#bfd8df] bg-white text-[#356b78] transition hover:border-[#147eaf] hover:text-[#147eaf]"><i class="fa-solid fa-chevron-right text-xs" aria-hidden="true"></i></a>
                @else
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-[#d7e1df] bg-[#ecefed] text-[#91a0a2]"><i class="fa-solid fa-chevron-right text-xs" aria-hidden="true"></i></span>
                @endif
            </div>
        </div>
    </nav>
@endif

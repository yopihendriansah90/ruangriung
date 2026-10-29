@extends('layouts.public')

@section('content')
@php
    $filterUrl = fn (string $value) => route('events.index', array_filter([
        'search' => $search,
        'period' => $value,
        'category' => $category,
    ], fn ($item) => $item !== ''));
    $categoryUrl = fn (string $value) => route('events.index', array_filter([
        'search' => $search,
        'period' => $period !== 'all' ? $period : null,
        'category' => $value !== '' ? $value : null,
    ], fn ($item) => $item !== ''));
    $adminContactUrl = app(\App\Services\AdminContactService::class)->whatsappUrl();
@endphp

@if (! $isFiltered)
@if ($banners->isNotEmpty())
    <section class="mt-2" aria-label="Banner informasi">
        <div class="public-container relative">
            <div class="group relative overflow-hidden rounded-2xl" data-banner-slider data-interval="6000">
                <div class="flex aspect-[16/5] transition-transform duration-500 ease-out" data-banner-track>
                    @foreach ($banners as $banner)
                        <div class="relative min-w-full overflow-hidden bg-[#104b68]">
                            @if ($banner->cta_url)
                                <a href="{{ $banner->cta_url }}" target="_blank" rel="noopener" aria-label="{{ $banner->name }}" class="block h-full w-full">
                                    <img src="{{ $banner->imageUrl() }}" alt="{{ $banner->name }}" class="h-full w-full object-cover">
                                </a>
                            @else
                                <img src="{{ $banner->imageUrl() }}" alt="{{ $banner->name }}" class="h-full w-full object-cover">
                            @endif
                        </div>
                    @endforeach
                </div>

                @if ($banners->count() > 1)
                    <button type="button" data-banner-prev class="absolute left-4 top-1/2 z-20 hidden h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white text-[#496d78] shadow-lg transition hover:text-[#104b68] md:flex lg:left-8" aria-label="Banner sebelumnya">
                        <i class="fa-solid fa-chevron-left text-sm" aria-hidden="true"></i>
                    </button>
                    <button type="button" data-banner-next class="absolute right-4 top-1/2 z-20 hidden h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white text-[#496d78] shadow-lg transition hover:text-[#104b68] md:flex lg:right-8" aria-label="Banner berikutnya">
                        <i class="fa-solid fa-chevron-right text-sm" aria-hidden="true"></i>
                    </button>
                    <div class="absolute bottom-4 left-1/2 z-20 flex -translate-x-1/2 gap-2">
                        @foreach ($banners as $bannerDot)
                            <button type="button" data-banner-dot class="h-2 w-2 rounded-full bg-white/50 transition-all duration-200" aria-label="Ke banner {{ $loop->iteration }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif

    <section class="public-container mt-4 sm:mt-6">
        <div class="rounded-3xl border border-[#d7e1df]/70 bg-white/60 p-4 shadow-[0_12px_58px_rgba(16,75,104,0.08)] sm:p-6">
            <form method="GET" action="{{ route('events.index') }}" class="flex gap-2">
                <input type="hidden" name="period" value="all">
                <label for="home-search" class="sr-only">Cari event</label>
                <div class="relative w-full">
                    <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[#78959b]" aria-hidden="true"></i>
                    <input id="home-search" name="search" class="field-input min-h-12 pl-11" placeholder="Mau cari event apa hari ini?">
                </div>
                <button class="btn-primary min-w-fit border-4 border-[#104b68] px-4" type="submit"><i class="fa-solid fa-magnifying-glass md:mr-1" aria-hidden="true"></i> <span class="hidden md:block">Cari Event</span></button>
            </form>

            <div class="mt-3 flex items-center gap-2" aria-label="Pilih kategori event">
                <button type="button" data-chip-scroll="prev" class="hidden h-11 w-11 shrink-0 items-center justify-center rounded-2xl border-2 border-[#e3f0f3] bg-white text-[#104b68] shadow-[0_6px_28px_rgba(61,120,216,0.06)] transition hover:border-[#65c6f1] md:flex" aria-label="Gulir kategori ke kiri">
                    <i class="fa-solid fa-chevron-left text-xs" aria-hidden="true"></i>
                </button>
                <div class="min-w-0 flex-1 overflow-hidden">
                    <div class="no-scrollbar -mx-1 flex items-center gap-1.5 overflow-x-auto px-1 py-0.5" data-chip-track>
                        @foreach (\App\Models\Event::CATEGORIES as $value => $item)
                            <a href="{{ $categoryUrl($value) }}" class="flex shrink-0 items-center gap-2 rounded-xl border-2 border-white bg-[#f7f5ef] p-2 pe-3 transition hover:border-[#65c6f1] hover:bg-white">
                                <span class="flex h-5 w-5 items-center justify-center rounded-lg bg-[#147eaf]/10 text-[9px] text-[#147eaf] md:h-6 md:w-6 md:text-[11px]"><i class="{{ $item['icon'] }}" aria-hidden="true"></i></span>
                                <span class="whitespace-nowrap text-xs text-[#104b68] md:text-sm">{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                <button type="button" data-chip-scroll="next" class="hidden h-11 w-11 shrink-0 items-center justify-center rounded-2xl border-2 border-[#e3f0f3] bg-white text-[#104b68] shadow-[0_6px_28px_rgba(61,120,216,0.06)] transition hover:border-[#65c6f1] md:flex" aria-label="Gulir kategori ke kanan">
                    <i class="fa-solid fa-chevron-right text-xs" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </section>

    @if ($featuredEvent)
        @php $featuredPoster = $featuredEvent->getFirstMediaUrl('event-posters'); @endphp
        <section id="event-terdekat" class="public-container scroll-mt-28 pt-20 lg:pt-28">
            <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
                <div>
                    <p class="section-eyebrow">Jangan sampai terlewat</p>
                    <h2 class="section-title">Event terdekat</h2>
                </div>
                <a href="{{ route('events.index', ['period' => 'upcoming']) }}" class="-m-2 inline-flex items-center gap-2 p-2 text-sm font-bold text-[#147eaf] hover:text-[#104b68]">Lihat semua</a>
            </div>
            <article class="mt-10">
                <a href="{{ route('events.show', $featuredEvent) }}" class="group grid overflow-hidden rounded-[1.75rem] bg-white shadow-[0_24px_70px_rgba(16,75,104,0.13)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_28px_80px_rgba(16,75,104,0.18)] sm:grid-cols-[minmax(220px,0.8fr)_1fr]" aria-label="Lihat detail {{ $featuredEvent->name }}">
                    <div class="aspect-[4/5] overflow-hidden bg-[#dfecee] sm:aspect-auto sm:min-h-[450px]">
                        @if ($featuredPoster)
                            <img src="{{ $featuredPoster }}" alt="Poster {{ $featuredEvent->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]">
                        @else
                            <div class="flex h-full flex-col items-center justify-center gap-3 px-8 text-center text-[#6f8d91]">
                                <i class="fa-regular fa-image text-4xl" aria-hidden="true"></i>
                                <span class="text-sm font-bold">Poster segera hadir</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col justify-between p-6 sm:p-8">
                        <div>
                            <x-public.event-status-badge :event="$featuredEvent" />
                            <h3 class="mt-5 text-2xl font-bold leading-tight text-[#104b68] sm:text-3xl">{!! $featuredEvent->titleHtml() !!}</h3>
                            <x-public.event-meta :event="$featuredEvent" :show-capacity="true" class="mt-6" />
                        </div>
                        <span class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-[#147eaf] transition group-hover:text-[#104b68]">
                            Lihat detail event <i class="fa-solid fa-arrow-right transition group-hover:translate-x-1" aria-hidden="true"></i>
                        </span>
                    </div>
                </a>
            </article>
        </section>
    @endif

    <section id="event-mendatang" class="public-container scroll-mt-28 pt-20 pb-16 lg:pt-24 lg:pb-24">
        <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
            <div>
                <p class="section-eyebrow">Buat ruang di kalendermu</p>
                <h2 class="section-title">Event yang akan datang</h2>
                <p class="section-copy">Pilih pertemuan yang terasa dekat denganmu, lalu hadir dan bertumbuh bersama.</p>
            </div>
            <a href="{{ route('events.index', ['period' => 'upcoming']) }}" class="-m-2 inline-flex items-center gap-2 p-2 text-sm font-bold text-[#147eaf] hover:text-[#104b68]">Lihat semua</a>
        </div>

        @if ($upcomingEvents->isNotEmpty())
            <div class="mt-10 grid gap-x-7 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($upcomingEvents as $event)
                    <x-public.event-card :event="$event" />
                @endforeach
            </div>
        @else
            <div class="empty-state mt-10">Belum ada event lain yang dijadwalkan. Pantau kembali halaman ini untuk kabar pertemuan berikutnya.</div>
        @endif
    </section>

    <section class="mt-10 bg-[#103f56] py-20 text-white sm:mt-14 lg:py-24">
        <div class="public-container">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#65c6f1] sm:text-sm">Sederhana dari awal sampai hadir</p>
            <h2 class="mt-3 max-w-2xl text-3xl font-bold leading-tight tracking-[-0.025em] sm:text-4xl">Tiga langkah untuk ikut berkumpul</h2>
            <div class="mt-12 grid gap-10 md:grid-cols-3">
                @foreach ([
                    ['01', 'fa-compass', 'Pilih event', 'Temukan pertemuan yang sesuai dengan minat, waktu, dan ruang yang kamu cari.'],
                    ['02', 'fa-pen-to-square', 'Isi pendaftaran', 'Lengkapi data singkat agar panitia dapat menyiapkan pengalaman terbaik untukmu.'],
                    ['03', 'fa-qrcode', 'Tunjukkan QR Code', 'Simpan undangan yang dikirimkan dan tunjukkan QR Code saat check-in di lokasi.'],
                ] as [$number, $icon, $heading, $copy])
                    <article class="border-t border-white/20 pt-6">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-[#65c6f1]">{{ $number }}</span>
                            <i class="fa-solid {{ $icon }} text-2xl text-[#65c6f1]" aria-hidden="true"></i>
                        </div>
                        <h3 class="mt-8 text-xl font-bold">{{ $heading }}</h3>
                        <p class="mt-3 text-sm leading-7 text-[#c6dbe1]">{{ $copy }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="cerita" class="public-container scroll-mt-28 py-20 lg:py-28">
        <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
            <div>
                <p class="section-eyebrow">Jejak pertemuan</p>
                <h2 class="section-title">Cerita dari Ruangriung</h2>
                <p class="section-copy">Setiap pertemuan boleh selesai, tetapi percakapan dan koneksi yang tumbuh bisa berjalan lebih jauh.</p>
            </div>
            <a href="{{ route('events.index', ['period' => 'past']) }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#147eaf] hover:text-[#104b68]">Lihat cerita lainnya</a>
        </div>

        @if ($pastEvents->isNotEmpty())
            <div class="mt-10 grid gap-x-7 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($pastEvents as $event)
                    <x-public.event-card :event="$event" :story="true" />
                @endforeach
            </div>
        @else
            <div class="empty-state mt-10">Cerita pertemuan sebelumnya akan hadir di sini setelah event pertama berlangsung.</div>
        @endif
    </section>

    <section class="public-container pb-20 lg:pb-28">
        <div class="grid overflow-hidden rounded-[1.75rem] bg-[#65c6f1] lg:grid-cols-[1fr_auto] lg:items-center">
            <div class="p-7 sm:p-10 lg:p-12">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#104b68]">Ruang Riung</p>
                <h2 class="mt-4 max-w-2xl text-3xl font-bold leading-tight text-[#103f56] sm:text-4xl">Masih mencari pertemuan yang tepat untukmu?</h2>
                <p class="mt-4 max-w-xl text-sm leading-7 text-[#255b70] sm:text-base">Jelajahi semua event atau bicara langsung dengan admin jika ada hal yang ingin kamu tanyakan.</p>
            </div>
            <div class="flex flex-col gap-3 border-t border-[#104b68]/15 p-7 sm:flex-row lg:border-l lg:border-t-0 lg:p-12">
                <a href="{{ route('events.index', ['period' => 'all']) }}" class="btn-primary">Semua event</a>
                @php $contactUrl = app(\App\Services\AdminContactService::class)->whatsappUrl(); @endphp
                @if ($contactUrl)
                    <a href="{{ $contactUrl }}" target="_blank" rel="noopener" class="btn-secondary"><i class="fa-brands fa-whatsapp text-lg" aria-hidden="true"></i> Tanya admin</a>
                @endif
            </div>
        </div>
    </section>
@else
    <section class="border-b border-[#d7e1df] bg-[#edf5f3]">
        <div class="public-container py-12 sm:py-16">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#147eaf] hover:text-[#104b68]">Kembali ke beranda</a>
            <div class="mt-8 max-w-3xl">
                <p class="section-eyebrow">Jelajahi pertemuan</p>
                <h1 class="section-title">Temukan event Ruangriung</h1>
                <p class="section-copy">Cari berdasarkan nama atau lokasi, lalu pilih event mendatang maupun cerita pertemuan sebelumnya.</p>
            </div>
        </div>
    </section>

    <section class="public-container py-10 lg:py-14">
        <form method="GET" action="{{ route('events.index') }}" class="grid gap-3 bg-white p-4 shadow-[0_12px_36px_rgba(16,75,104,0.08)] sm:grid-cols-[1fr_auto] sm:p-5">
            <input type="hidden" name="period" value="{{ $period }}">
            <input type="hidden" name="category" value="{{ $category }}">
            <label for="event-search" class="sr-only">Cari event</label>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[#78959b]" aria-hidden="true"></i>
                <input id="event-search" name="search" value="{{ $search }}" class="field-input min-h-12 pl-11" placeholder="Cari nama event atau lokasi">
            </div>
            <button class="btn-accent" type="submit">Cari Event</button>
        </form>

        <div class="mt-5 space-y-3">
            <div class="no-scrollbar -mx-1 flex items-center gap-1.5 overflow-x-auto px-1 py-0.5" aria-label="Filter kategori event" data-chip-track>
                @foreach (\App\Models\Event::CATEGORIES as $value => $item)
                    <a href="{{ $categoryUrl($value) }}" @class([
                        'flex shrink-0 items-center gap-2 rounded-xl border-2 p-2 pe-3 transition',
                        'border-[#104b68] bg-[#104b68] text-white' => $category === $value,
                        'border-white bg-[#f7f5ef] text-[#104b68] hover:border-[#65c6f1] hover:bg-white' => $category !== $value,
                    ]) @if ($category === $value) aria-current="page" @endif>
                        <span @class([
                            'flex h-5 w-5 items-center justify-center rounded-lg text-[9px] md:h-6 md:w-6 md:text-[11px]',
                            'bg-white/15 text-white' => $category === $value,
                            'bg-[#147eaf]/10 text-[#147eaf]' => $category !== $value,
                        ])><i class="{{ $item['icon'] }}" aria-hidden="true"></i></span>
                        <span class="whitespace-nowrap text-xs md:text-sm">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
            <div class="no-scrollbar -mx-1 flex items-center gap-2 overflow-x-auto px-1 py-0.5" aria-label="Filter waktu event">
                @foreach (['all' => 'Semua', 'upcoming' => 'Mendatang', 'past' => 'Sebelumnya'] as $value => $label)
                <a href="{{ $filterUrl($value) }}" @class([
                    'inline-flex min-h-11 items-center rounded-full px-5 text-sm font-bold transition',
                    'bg-[#104b68] text-white' => $period === $value,
                    'border border-[#bfd8df] bg-white text-[#496d78] hover:border-[#147eaf] hover:text-[#147eaf]' => $period !== $value,
                ]) @if ($period === $value) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
            @if ($search !== '')
                <a href="{{ route('events.index', ['period' => $period]) }}" class="inline-flex min-h-11 items-center gap-2 px-3 text-sm font-bold text-[#147eaf] hover:text-[#104b68]"><i class="fa-solid fa-xmark" aria-hidden="true"></i> Hapus pencarian</a>
            @endif
        </div>

        <div class="mt-12 flex items-end justify-between gap-5">
            <div>
                <p class="section-eyebrow">Hasil pencarian</p>
                <h2 class="mt-2 text-2xl font-bold text-[#104b68]">{{ $events?->total() ?? 0 }} event ditemukan</h2>
            </div>
        </div>

        @if ($events?->isNotEmpty())
            <div class="mt-8 grid gap-x-7 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($events as $event)
                    <x-public.event-card :event="$event" :story="$event->isEnded()" />
                @endforeach
            </div>
            @if ($events->hasPages())
                <div class="mt-14">{{ $events->onEachSide(1)->links('pagination.mhc') }}</div>
            @endif
        @else
            <div class="empty-state mt-8">
                <i class="fa-regular fa-calendar-xmark mb-4 text-3xl text-[#65c6f1]" aria-hidden="true"></i>
                <p>Belum ada event yang sesuai dengan pencarian dan filter ini.</p>
            </div>
        @endif
    </section>
@endif

<x-public.whatsapp-floating-button />
@endsection

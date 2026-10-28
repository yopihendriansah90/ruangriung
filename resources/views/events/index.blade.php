@extends('layouts.public')

@section('content')
@php
    $filterUrl = fn (string $value) => route('events.index', array_filter([
        'search' => $search,
        'period' => $value,
    ], fn ($item) => $item !== ''));
@endphp

@if (! $isFiltered)
    <section class="overflow-hidden border-b border-[#d7e1df] bg-[#edf5f3]">
        <div class="public-container grid items-center gap-12 py-12 lg:grid-cols-[0.85fr_1.15fr] lg:py-20">
            <div class="max-w-xl">
                <p class="section-eyebrow">Ruang untuk bertemu dan bertumbuh</p>
                <h1 class="mt-5 text-4xl font-bold leading-[1.02] tracking-[-0.04em] text-[#104b68] sm:text-5xl lg:text-6xl">Temukan pertemuan yang membuat kita lebih dekat.</h1>
                <p class="mt-6 text-base leading-8 text-[#496d78] sm:text-lg">Datang sebagai diri sendiri, pulang dengan cerita dan koneksi baru. Jelajahi ruang belajar, berbagi, dan bertumbuh bersama komunitas Ruang Riung.</p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    @if ($featuredEvent)
                        <a href="{{ route('events.show', $featuredEvent) }}" class="btn-primary">Lihat event terdekat <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    @endif
                    <a href="{{ route('events.index', ['period' => 'all']) }}" class="btn-secondary">Jelajahi semua event</a>
                </div>
            </div>

            @if ($featuredEvent)
                @php $featuredPoster = $featuredEvent->getFirstMediaUrl('event-posters'); @endphp
                <article class="grid overflow-hidden rounded-[1.75rem] bg-white shadow-[0_24px_70px_rgba(16,75,104,0.13)] sm:grid-cols-[minmax(220px,0.8fr)_1fr]">
                    <div class="aspect-[4/5] bg-[#dfecee] sm:aspect-auto sm:min-h-[450px]">
                        @if ($featuredPoster)
                            <img src="{{ $featuredPoster }}" alt="Poster {{ $featuredEvent->name }}" class="h-full w-full object-contain">
                        @else
                            <div class="flex h-full flex-col items-center justify-center gap-3 px-8 text-center text-[#6f8d91]">
                                <i class="fa-regular fa-image text-4xl" aria-hidden="true"></i>
                                <span class="text-sm font-bold">Poster segera hadir</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col justify-between p-6 sm:p-8">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#147eaf]">Event terdekat</p>
                            <x-public.event-status-badge :event="$featuredEvent" class="mt-4" />
                            <h2 class="mt-5 text-2xl font-bold leading-tight text-[#104b68] sm:text-3xl">{{ $featuredEvent->name }}</h2>
                            <x-public.event-meta :event="$featuredEvent" :show-capacity="true" class="mt-6" />
                        </div>
                        <a href="{{ route('events.show', $featuredEvent) }}" class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-[#147eaf] hover:text-[#104b68]">
                            Lihat detail event <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                </article>
            @else
                <div class="flex min-h-[360px] items-center justify-center rounded-[1.75rem] border border-dashed border-[#a9c7cc] bg-white/60 p-10 text-center">
                    <div class="max-w-sm">
                        <i class="fa-regular fa-calendar-plus text-5xl text-[#65c6f1]" aria-hidden="true"></i>
                        <h2 class="mt-5 text-2xl font-bold text-[#104b68]">Pertemuan berikutnya sedang disiapkan</h2>
                        <p class="mt-3 text-sm leading-7 text-[#5d7d86]">Sementara itu, kamu tetap bisa melihat cerita dari pertemuan Ruang Riung sebelumnya.</p>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <section class="public-container relative z-10 -mt-5">
        <form method="GET" action="{{ route('events.index') }}" class="grid gap-3 rounded-2xl bg-white p-4 shadow-[0_16px_45px_rgba(16,75,104,0.1)] sm:grid-cols-[1fr_auto] sm:p-5">
            <input type="hidden" name="period" value="all">
            <label for="home-search" class="sr-only">Cari event</label>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[#78959b]" aria-hidden="true"></i>
                <input id="home-search" name="search" class="field-input min-h-12 pl-11" placeholder="Cari nama event atau lokasi">
            </div>
            <button class="btn-accent" type="submit">Cari Event <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
        </form>
    </section>

    <section id="event-mendatang" class="public-container scroll-mt-28 py-20 lg:py-28">
        <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
            <div>
                <p class="section-eyebrow">Buat ruang di kalendermu</p>
                <h2 class="section-title">Event yang akan datang</h2>
                <p class="section-copy">Pilih pertemuan yang terasa dekat denganmu, lalu hadir dan bertumbuh bersama.</p>
            </div>
            <a href="{{ route('events.index', ['period' => 'upcoming']) }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#147eaf] hover:text-[#104b68]">Lihat semua <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
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

    <section class="bg-[#103f56] py-20 text-white lg:py-24">
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
            <a href="{{ route('events.index', ['period' => 'past']) }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#147eaf] hover:text-[#104b68]">Lihat cerita lainnya <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
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
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#147eaf] hover:text-[#104b68]"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Kembali ke beranda</a>
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
            <label for="event-search" class="sr-only">Cari event</label>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[#78959b]" aria-hidden="true"></i>
                <input id="event-search" name="search" value="{{ $search }}" class="field-input min-h-12 pl-11" placeholder="Cari nama event atau lokasi">
            </div>
            <button class="btn-accent" type="submit">Cari Event <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
        </form>

        <div class="mt-5 flex flex-wrap gap-2" aria-label="Filter waktu event">
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
                <h2 class="mt-2 text-2xl font-bold text-[#104b68]">{{ $events->total() }} event ditemukan</h2>
            </div>
        </div>

        @if ($events->isNotEmpty())
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

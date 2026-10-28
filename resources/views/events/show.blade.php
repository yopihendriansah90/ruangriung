@extends('layouts.public')

@section('content')
@php
    $poster = $event->getFirstMediaUrl('event-posters');
    $isClosed = $event->status === \App\Models\Event::STATUS_CLOSED;
    $isEnded = $event->isEnded();
    $isFull = $event->isFull();
    $isRegistrationOpen = ! $isClosed && ! $isEnded && ! $isFull;
    $descriptionHtml = trim($event->description) === strip_tags($event->description)
        ? nl2br(e($event->description))
        : $event->description;
    $unavailableMessage = match (true) {
        $isClosed => 'Pendaftaran untuk event ini sudah ditutup.',
        $isEnded => 'Event ini sudah selesai.',
        $isFull => 'Seluruh tempat untuk event ini sudah terisi.',
        default => null,
    };
@endphp

<section class="border-b border-[#d7e1df] bg-[#edf5f3]">
    <div class="public-container py-8 lg:py-14">
        <a href="{{ route('events.index', ['period' => 'all']) }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#147eaf] hover:text-[#104b68]"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Semua event</a>

        <article class="mt-7 grid overflow-hidden bg-white shadow-[0_24px_70px_rgba(16,75,104,0.1)] lg:grid-cols-[minmax(320px,0.82fr)_minmax(0,1.18fr)]">
            <div class="bg-[#dfecee] p-4 sm:p-6 lg:p-8">
                <div class="mx-auto aspect-[4/5] max-w-[520px] overflow-hidden bg-[#e8f2f3]">
                    @if ($poster)
                        <button type="button" data-poster-open class="group relative block h-full w-full cursor-zoom-in" aria-label="Perbesar poster {{ $event->name }}">
                            <img src="{{ $poster }}" alt="Poster {{ $event->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]">
                            <span class="absolute bottom-4 right-4 flex h-11 w-11 items-center justify-center rounded-full bg-white text-[#104b68] shadow-lg transition group-hover:bg-[#65c6f1]" aria-hidden="true"><i class="fa-solid fa-magnifying-glass-plus"></i></span>
                        </button>
                    @else
                        <div class="flex h-full flex-col items-center justify-center gap-3 text-[#6f8d91]">
                            <i class="fa-regular fa-image text-4xl" aria-hidden="true"></i>
                            <span class="text-sm font-bold">Poster segera hadir</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-col justify-between p-6 sm:p-9 lg:p-12">
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        @unless ($event->isEnded())
                            <x-public.event-status-badge :event="$event" />
                        @endunless
                    </div>
                    <h1 class="mt-6 max-w-3xl text-3xl font-bold leading-[1.08] tracking-[-0.035em] text-[#104b68] sm:text-4xl lg:text-5xl">{{ $event->name }}</h1>
                    <x-public.event-meta :event="$event" :show-capacity="true" class="mt-8 border-y border-[#d7e1df] py-7" />

                    @if ($event->location_address)
                        <p class="mt-5 text-sm leading-7 text-[#5d7d86]">{{ $event->location_address }}</p>
                    @endif
                </div>

                <div class="mt-8">
                    @if ($isRegistrationOpen)
                        <a href="{{ route('events.register', $event) }}" class="btn-primary w-full sm:w-auto">Daftar sekarang <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    @else
                        <div class="border-l-4 {{ $isFull ? 'border-red-400 bg-red-50 text-red-700' : 'border-amber-400 bg-amber-50 text-amber-800' }} px-5 py-4 text-sm font-semibold" role="status">{{ $unavailableMessage }}</div>
                    @endif

                    @if ($event->google_maps_url)
                        <a href="{{ $event->google_maps_url }}" target="_blank" rel="noopener" class="mt-3 inline-flex min-h-11 items-center gap-2 px-1 text-sm font-bold text-[#147eaf] hover:text-[#104b68]"><i class="fa-solid fa-map-location-dot" aria-hidden="true"></i> Buka Google Maps <i class="fa-solid fa-arrow-up-right-from-square text-xs" aria-hidden="true"></i></a>
                    @endif
                </div>
            </div>
        </article>
    </div>
</section>

<section class="public-container grid gap-12 py-16 lg:grid-cols-[minmax(0,1fr)_340px] lg:py-24">
    <article>
        <p class="section-eyebrow">Tentang event</p>
        <h2 class="mt-3 text-3xl font-bold tracking-[-0.025em] text-[#104b68]">Cerita di balik pertemuan ini</h2>
        <div class="event-description mt-8">{!! $descriptionHtml !!}</div>
    </article>

    <aside class="space-y-8 lg:sticky lg:top-28 lg:self-start">
        <div class="border-t-4 border-[#65c6f1] bg-white p-6 shadow-[0_12px_36px_rgba(16,75,104,0.07)]">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#147eaf]">Informasi event</p>
            <dl class="mt-6 space-y-5 text-sm">
                <div>
                    <dt class="text-xs font-bold uppercase tracking-[0.12em] text-[#78959b]">Tanggal</dt>
                    <dd class="mt-1 font-semibold leading-6 text-[#255b70]">{{ $event->event_date->translatedFormat('l, d F Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-[0.12em] text-[#78959b]">Waktu</dt>
                    <dd class="mt-1 font-semibold leading-6 text-[#255b70]">{{ substr($event->start_time, 0, 5) }}{{ $event->end_time ? ' – '.substr($event->end_time, 0, 5) : '' }} WIB</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-[0.12em] text-[#78959b]">Lokasi</dt>
                    <dd class="mt-1 font-semibold leading-6 text-[#255b70]">{{ $event->location_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-[0.12em] text-[#78959b]">Penyelenggara</dt>
                    <dd class="mt-1 font-semibold leading-6 text-[#255b70]">Ruang Riung</dd>
                </div>
            </dl>
        </div>

        <div class="bg-[#e8f2f3] p-6">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#147eaf]">Yang perlu kamu tahu</p>
            <ul class="mt-5 space-y-4 text-sm leading-6 text-[#496d78]">
                <li class="flex gap-3"><i class="fa-regular fa-envelope mt-1 w-4 text-[#147eaf]" aria-hidden="true"></i><span>Gunakan email aktif agar undangan dapat diterima.</span></li>
                <li class="flex gap-3"><i class="fa-solid fa-qrcode mt-1 w-4 text-[#147eaf]" aria-hidden="true"></i><span>Simpan QR Code dan tunjukkan saat check-in.</span></li>
                <li class="flex gap-3"><i class="fa-solid fa-location-dot mt-1 w-4 text-[#147eaf]" aria-hidden="true"></i><span>Periksa kembali detail lokasi sebelum berangkat.</span></li>
            </ul>
        </div>
    </aside>
</section>

@if ($recommendedEvents->isNotEmpty())
    <section class="border-t border-[#d7e1df] bg-[#edf5f3] py-16 lg:py-24">
        <div class="public-container">
            <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
                <div>
                    <p class="section-eyebrow">Lanjut berkumpul</p>
                    <h2 class="mt-3 text-3xl font-bold tracking-[-0.025em] text-[#104b68] sm:text-4xl">Event lain dari Ruangriung</h2>
                </div>
                <a href="{{ route('events.index', ['period' => 'upcoming']) }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#147eaf] hover:text-[#104b68]">Lihat semua <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>
            <div class="mt-10 grid gap-x-7 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($recommendedEvents as $recommendedEvent)
                    <x-public.event-card :event="$recommendedEvent" />
                @endforeach
            </div>
        </div>
    </section>
@endif

@if ($isRegistrationOpen)
    <div class="fixed inset-x-0 bottom-0 z-40 border-t border-[#bfd8df] bg-[#f7f5ef]/95 p-3 shadow-[0_-10px_30px_rgba(16,75,104,0.12)] backdrop-blur lg:hidden">
        <a href="{{ route('events.register', $event) }}" class="btn-primary w-full">Daftar sekarang <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </div>
@endif

<x-public.whatsapp-floating-button :event="$event" :mobile-cta="$isRegistrationOpen" />

@if ($poster)
    <dialog id="poster-viewer" class="m-auto max-h-[94vh] max-w-[min(94vw,1100px)] overflow-auto rounded-2xl bg-[#103f56] p-2 text-white shadow-2xl backdrop:bg-[#103f56]/80">
        <div class="relative">
            <div class="flex min-h-[70vh] min-w-[min(88vw,1040px)] items-center justify-center overflow-auto rounded-xl bg-[#092f40] p-4">
                <img id="poster-image" src="{{ $poster }}" alt="Poster {{ $event->name }}" class="max-h-[84vh] max-w-full origin-center object-contain transition-transform duration-200">
            </div>
            <button type="button" data-poster-close aria-label="Tutup poster" class="absolute right-3 top-3 flex h-10 w-10 items-center justify-center rounded-full bg-[#103f56]/90 text-white hover:bg-[#147eaf]"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
            <div class="absolute bottom-5 left-1/2 flex -translate-x-1/2 items-center gap-1 rounded-full bg-[#103f56]/95 p-1 shadow-lg">
                <button type="button" data-poster-zoom-out aria-label="Perkecil poster" class="flex h-10 w-10 items-center justify-center rounded-full hover:bg-[#147eaf]"><i class="fa-solid fa-minus" aria-hidden="true"></i></button>
                <button type="button" data-poster-zoom-reset aria-label="Reset ukuran poster" class="flex h-10 w-10 items-center justify-center rounded-full hover:bg-[#147eaf]"><i class="fa-solid fa-rotate-left" aria-hidden="true"></i></button>
                <button type="button" data-poster-zoom-in aria-label="Perbesar poster" class="flex h-10 w-10 items-center justify-center rounded-full hover:bg-[#147eaf]"><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
            </div>
        </div>
    </dialog>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const viewer = document.getElementById('poster-viewer');
            const posterImage = document.getElementById('poster-image');
            let zoom = 1;
            const updateZoom = () => { if (posterImage) posterImage.style.transform = `scale(${zoom})`; };
            document.querySelector('[data-poster-open]')?.addEventListener('click', () => { zoom = 1; updateZoom(); viewer?.showModal(); });
            document.querySelector('[data-poster-close]')?.addEventListener('click', () => viewer?.close());
            document.querySelector('[data-poster-zoom-in]')?.addEventListener('click', () => { zoom = Math.min(zoom + 0.25, 5); updateZoom(); });
            document.querySelector('[data-poster-zoom-out]')?.addEventListener('click', () => { zoom = Math.max(zoom - 0.25, 1); updateZoom(); });
            document.querySelector('[data-poster-zoom-reset]')?.addEventListener('click', () => { zoom = 1; updateZoom(); });
            viewer?.addEventListener('click', (event) => { if (event.target === viewer) viewer.close(); });
            viewer?.addEventListener('wheel', (event) => {
                if (!viewer.open) return;
                event.preventDefault();
                zoom = Math.min(Math.max(zoom + (event.deltaY < 0 ? 0.1 : -0.1), 1), 5);
                updateZoom();
            }, { passive: false });
        });
    </script>
@endif
@endsection

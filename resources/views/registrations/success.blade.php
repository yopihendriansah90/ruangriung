@extends('layouts.public')

@section('content')
@php
    $event = $registration->event;
    $poster = $event->getFirstMediaUrl('event-posters');
@endphp

<section class="border-b border-[#d7e1df] bg-[#edf5f3]">
    <div class="public-container py-14 text-center sm:py-20">
        <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-[#104b68] text-3xl text-[#65c6f1] shadow-[0_14px_36px_rgba(16,75,104,0.18)]"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
        <p class="section-eyebrow mt-7">Pendaftaran berhasil</p>
        <h1 class="mx-auto mt-4 max-w-3xl text-4xl font-bold leading-tight tracking-[-0.035em] text-[#104b68] sm:text-5xl">Sampai bertemu, {{ $registration->name }}!</h1>
        <p class="mx-auto mt-5 max-w-2xl text-base leading-8 text-[#496d78]">Tempatmu di <strong class="text-[#104b68]">{{ $event->name }}</strong> sudah tercatat. Simpan informasi berikut agar perjalananmu menuju event tetap mudah.</p>
    </div>
</section>

<section class="public-container grid gap-8 py-12 lg:grid-cols-[minmax(0,1fr)_380px] lg:items-start lg:py-18">
    <div class="space-y-8">
        <article class="bg-white p-6 shadow-[0_18px_50px_rgba(16,75,104,0.08)] sm:p-9">
            <div class="flex items-start gap-4 border-b border-[#d7e1df] pb-6">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#dff4fd] text-[#147eaf]"><i class="fa-regular fa-envelope" aria-hidden="true"></i></span>
                <div>
                    <h2 class="text-2xl font-bold text-[#104b68]">Periksa emailmu</h2>
                    <p class="mt-2 text-sm leading-7 text-[#5d7d86]">Undangan dan QR Code akan dikirimkan oleh admin ke <strong class="text-[#255b70]">{{ $registration->email }}</strong>.</p>
                </div>
            </div>

            <ol class="mt-8 grid gap-6 sm:grid-cols-3">
                @foreach ([
                    ['fa-inbox', 'Cek kotak masuk', 'Periksa inbox dan folder spam secara berkala.'],
                    ['fa-qrcode', 'Simpan QR Code', 'Unduh atau screenshot QR Code dari undangan.'],
                    ['fa-door-open', 'Tunjukkan saat hadir', 'Siapkan QR Code ketika melakukan check-in.'],
                ] as [$icon, $heading, $copy])
                    <li class="border-t-2 border-[#65c6f1] pt-5">
                        <i class="fa-solid {{ $icon }} text-xl text-[#147eaf]" aria-hidden="true"></i>
                        <h3 class="mt-4 font-bold text-[#104b68]">{{ $heading }}</h3>
                        <p class="mt-2 text-sm leading-6 text-[#5d7d86]">{{ $copy }}</p>
                    </li>
                @endforeach
            </ol>
        </article>

        @if ($event->whatsapp_group_url)
            <article class="grid gap-6 bg-[#65c6f1] p-6 sm:grid-cols-[1fr_auto] sm:items-center sm:p-8">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#104b68]">Tetap terhubung</p>
                    <h2 class="mt-3 text-2xl font-bold text-[#103f56]">Gabung grup WhatsApp event</h2>
                    <p class="mt-2 text-sm leading-7 text-[#255b70]">Dapatkan informasi dan pembaruan terbaru langsung dari panitia.</p>
                </div>
                <a href="{{ $event->whatsapp_group_url }}" target="_blank" rel="noopener" class="btn-primary"><i class="fa-brands fa-whatsapp text-lg" aria-hidden="true"></i> Gabung grup</a>
            </article>
        @endif
    </div>

    <aside class="overflow-hidden bg-[#103f56] text-white lg:sticky lg:top-28">
        @if ($poster)
            <div class="aspect-[4/5] bg-[#dfecee]"><img src="{{ $poster }}" alt="Poster {{ $event->name }}" class="h-full w-full object-contain"></div>
        @endif
        <div class="p-6 sm:p-8">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#65c6f1]">Ringkasan event</p>
            <h2 class="mt-3 text-2xl font-bold leading-tight">{{ $event->name }}</h2>
            <div class="mt-6 space-y-3 text-sm leading-6 text-[#d7e8ec]">
                <p class="flex gap-3"><i class="fa-regular fa-calendar mt-1 w-4 text-[#65c6f1]" aria-hidden="true"></i><span>{{ $event->event_date->translatedFormat('l, d F Y') }}</span></p>
                <p class="flex gap-3"><i class="fa-regular fa-clock mt-1 w-4 text-[#65c6f1]" aria-hidden="true"></i><span>{{ substr($event->start_time, 0, 5) }}{{ $event->end_time ? ' – '.substr($event->end_time, 0, 5) : '' }} WIB</span></p>
                <p class="flex gap-3"><i class="fa-solid fa-location-dot mt-1 w-4 text-[#65c6f1]" aria-hidden="true"></i><span>{{ $event->location_name }}</span></p>
            </div>
            <div class="mt-7 border-t border-white/15 pt-6">
                <p class="text-xs uppercase tracking-[0.14em] text-[#9fbfc8]">Kode pendaftaran</p>
                <p class="mt-2 break-all font-mono text-sm font-bold text-[#65c6f1]">{{ $registration->registration_code }}</p>
            </div>
        </div>
    </aside>

    <div class="flex flex-col gap-3 sm:flex-row lg:col-span-2">
        <a href="{{ route('events.show', $event) }}" class="btn-primary"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Kembali ke detail event</a>
        <a href="{{ route('events.index', ['period' => 'upcoming']) }}" class="btn-secondary">Jelajahi event lain <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </div>
</section>

<x-public.whatsapp-floating-button :event="$event" />
@endsection

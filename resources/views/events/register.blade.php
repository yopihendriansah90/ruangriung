@extends('layouts.public')

@section('content')
@php $poster = $event->getFirstMediaUrl('event-posters'); @endphp

<section class="border-b border-[#d7e1df] bg-[#edf5f3]">
    <div class="public-container py-10 sm:py-14">
        <a href="{{ route('events.show', $event) }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#147eaf] hover:text-[#104b68]"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Kembali ke detail event</a>
        <div class="mt-8 max-w-3xl">
            <p class="section-eyebrow">Pendaftaran peserta</p>
            <h1 class="section-title">Siapkan tempatmu di pertemuan ini</h1>
            <p class="section-copy">Isi data dengan benar. Informasi undangan dan QR Code akan dikirimkan melalui email yang kamu daftarkan.</p>
        </div>
    </div>
</section>

<section class="public-container grid gap-10 py-12 lg:grid-cols-[380px_minmax(0,1fr)] lg:items-start lg:py-18">
    <aside class="overflow-hidden bg-[#103f56] text-white lg:sticky lg:top-28">
        <div class="aspect-[4/5] bg-[#dfecee]">
            @if ($poster)
                <img src="{{ $poster }}" alt="Poster {{ $event->name }}" class="h-full w-full object-cover">
            @else
                <div class="flex h-full flex-col items-center justify-center gap-3 text-[#496d78]">
                    <i class="fa-regular fa-image text-4xl" aria-hidden="true"></i>
                    <span class="text-sm font-bold">Poster segera hadir</span>
                </div>
            @endif
        </div>
        <div class="p-6 sm:p-8">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#65c6f1]">Kamu akan mendaftar ke</p>
            <h2 class="mt-3 text-2xl font-bold leading-tight">{{ $event->name }}</h2>
            <div class="mt-6 space-y-3 text-sm leading-6 text-[#d7e8ec]">
                <p class="flex gap-3"><i class="fa-regular fa-calendar mt-1 w-4 text-[#65c6f1]" aria-hidden="true"></i><span>{{ $event->event_date->translatedFormat('l, d F Y') }}</span></p>
                <p class="flex gap-3"><i class="fa-regular fa-clock mt-1 w-4 text-[#65c6f1]" aria-hidden="true"></i><span>{{ substr($event->start_time, 0, 5) }}{{ $event->end_time ? ' – '.substr($event->end_time, 0, 5) : '' }} WIB</span></p>
                <p class="flex gap-3"><i class="fa-solid fa-location-dot mt-1 w-4 text-[#65c6f1]" aria-hidden="true"></i><span>{{ $event->location_name }}</span></p>
            </div>
        </div>
    </aside>

    <div class="bg-white p-6 shadow-[0_18px_50px_rgba(16,75,104,0.08)] sm:p-9 lg:p-12">
        @if ($isFull)
            <div class="border-l-4 border-red-400 bg-red-50 p-5 text-sm leading-7 text-red-700" role="alert">
                <p class="font-bold"><i class="fa-solid fa-users mr-2" aria-hidden="true"></i>Kapasitas sudah penuh</p>
                <p class="mt-1">Pendaftaran untuk event ini tidak dapat menerima peserta baru.</p>
            </div>
        @else
            <div class="flex items-start gap-4 border-b border-[#d7e1df] pb-7">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#dff4fd] text-[#147eaf]"><i class="fa-regular fa-user" aria-hidden="true"></i></span>
                <div>
                    <h2 class="text-2xl font-bold text-[#104b68]">Data peserta</h2>
                    <p class="mt-1 text-sm leading-6 text-[#5d7d86]">Kolom bertanda <span class="font-bold text-red-600">*</span> wajib diisi.</p>
                </div>
            </div>

            @if ($errors->has('event'))
                <div class="mt-6 flex gap-3 border-l-4 border-red-400 bg-red-50 px-5 py-4 text-sm text-red-700" role="alert">
                    <i class="fa-solid fa-circle-exclamation mt-0.5" aria-hidden="true"></i>
                    <span>{{ $errors->first('event') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('registrations.store', $event) }}" class="mt-8 space-y-6">
                @csrf
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="field-label" for="name">Nama lengkap <span class="text-red-600">*</span></label>
                        <input class="field-input" id="name" name="name" value="{{ old('name') }}" autocomplete="name" required aria-describedby="name-error">
                        @error('name')<p class="field-error" id="name-error"><i class="fa-solid fa-circle-exclamation mt-0.5" aria-hidden="true"></i>{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label" for="email">Email aktif <span class="text-red-600">*</span></label>
                        <input class="field-input" id="email" name="email" type="email" inputmode="email" value="{{ old('email') }}" autocomplete="email" required aria-describedby="email-error">
                        @error('email')<p class="field-error" id="email-error"><i class="fa-solid fa-circle-exclamation mt-0.5" aria-hidden="true"></i>{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label" for="phone">Nomor WhatsApp <span class="text-red-600">*</span></label>
                        <input class="field-input" id="phone" name="phone" type="tel" inputmode="tel" value="{{ old('phone') }}" autocomplete="tel" required aria-describedby="phone-error">
                        @error('phone')<p class="field-error" id="phone-error"><i class="fa-solid fa-circle-exclamation mt-0.5" aria-hidden="true"></i>{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label" for="gender">Jenis kelamin <span class="text-red-600">*</span></label>
                        <select class="field-input" id="gender" name="gender" required aria-describedby="gender-error">
                            <option value="">Pilih jenis kelamin</option>
                            <option value="male" @selected(old('gender') === 'male')>Laki-laki</option>
                            <option value="female" @selected(old('gender') === 'female')>Perempuan</option>
                        </select>
                        @error('gender')<p class="field-error" id="gender-error"><i class="fa-solid fa-circle-exclamation mt-0.5" aria-hidden="true"></i>{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label" for="city">Kota/domisili</label>
                        <input class="field-input" id="city" name="city" value="{{ old('city') }}" autocomplete="address-level2">
                        @error('city')<p class="field-error"><i class="fa-solid fa-circle-exclamation mt-0.5" aria-hidden="true"></i>{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label" for="organization">Asal komunitas/instansi</label>
                        <input class="field-input" id="organization" name="organization" value="{{ old('organization') }}" autocomplete="organization">
                        @error('organization')<p class="field-error"><i class="fa-solid fa-circle-exclamation mt-0.5" aria-hidden="true"></i>{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label" for="referral_source">Tahu event ini dari</label>
                        <select class="field-input" id="referral_source" name="referral_source">
                            <option value="">Pilih sumber</option>
                            @foreach (['Instagram', 'WhatsApp', 'Teman', 'Komunitas', 'Lainnya'] as $source)
                                <option value="{{ $source }}" @selected(old('referral_source') === $source)>{{ $source }}</option>
                            @endforeach
                        </select>
                        @error('referral_source')<p class="field-error"><i class="fa-solid fa-circle-exclamation mt-0.5" aria-hidden="true"></i>{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="field-label" for="notes">Catatan tambahan</label>
                        <textarea class="field-input" id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
                        @error('notes')<p class="field-error"><i class="fa-solid fa-circle-exclamation mt-0.5" aria-hidden="true"></i>{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="border-t border-[#d7e1df] pt-6">
                    <label class="flex cursor-pointer items-start gap-3 text-sm leading-6 text-[#496d78]">
                        <input class="mt-1 h-4 w-4 shrink-0 accent-[#147eaf]" type="checkbox" name="data_consent" value="1" @checked(old('data_consent')) required>
                        <span>Saya menyetujui data digunakan untuk pendaftaran, komunikasi event, dan absensi. <span class="text-red-600">*</span></span>
                    </label>
                    @error('data_consent')<p class="field-error"><i class="fa-solid fa-circle-exclamation mt-0.5" aria-hidden="true"></i>{{ $message }}</p>@enderror
                </div>

                <button class="btn-primary w-full sm:w-auto" type="submit">Kirim pendaftaran <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
            </form>
        @endif
    </div>
</section>

<x-public.whatsapp-floating-button :event="$event" />
@endsection

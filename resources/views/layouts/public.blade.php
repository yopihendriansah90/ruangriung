<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('images/ruang-riung-icon.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Ruang Riung' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Ruang Riung — temukan event dan pendaftaran komunitas.' }}">
    <meta property="og:site_name" content="Ruang Riung">
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:title" content="{{ $ogTitle ?? ($title ?? 'Ruang Riung') }}">
    <meta property="og:description" content="{{ $ogDescription ?? ($metaDescription ?? 'Ruang Riung — temukan event dan pendaftaran komunitas.') }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('images/ruang-riung-logo.png') }}">
    @if (! empty($ogImageWidth) && ! empty($ogImageHeight))
        <meta property="og:image:width" content="{{ $ogImageWidth }}">
        <meta property="og:image:height" content="{{ $ogImageHeight }}">
    @endif
    <meta property="og:url" content="{{ $ogUrl ?? url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitle ?? ($title ?? 'Ruang Riung') }}">
    <meta name="twitter:description" content="{{ $ogDescription ?? ($metaDescription ?? 'Ruang Riung — temukan event dan pendaftaran komunitas.') }}">
    <meta name="twitter:image" content="{{ $ogImage ?? asset('images/ruang-riung-logo.png') }}">
    @stack('meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    @php
        $adminWhatsAppUrl = app(\App\Services\AdminContactService::class)->whatsappUrl();
    @endphp

    <a href="#main-content" class="sr-only z-[100] bg-white px-4 py-3 font-bold text-[#104b68] focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Lewati ke konten utama</a>

    <header class="fixed inset-x-0 top-0 z-50 border-b border-[#d7e1df] bg-[#f7f5ef]/95 backdrop-blur-xl">
        <div class="public-container flex min-h-20 items-center justify-between gap-5">
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3 rounded-lg" aria-label="Ruang Riung — Beranda">
                <img src="{{ asset('images/ruang-riung-icon.png') }}" alt="" class="h-11 w-11 object-contain">
                <span class="text-sm font-extrabold uppercase tracking-[0.12em] text-[#104b68]">Ruang Riung</span>
            </a>

            <nav class="hidden items-center gap-7 text-sm font-bold text-[#496d78] lg:flex" aria-label="Navigasi utama">
                <a href="{{ route('home') }}" class="transition hover:text-[#147eaf]">Beranda</a>
                <a href="{{ route('events.index', ['period' => 'upcoming']) }}" class="transition hover:text-[#147eaf]">Event</a>
                <a href="{{ route('home') }}#cerita" class="transition hover:text-[#147eaf]">Cerita</a>
                <a href="{{ route('home') }}#tentang" class="transition hover:text-[#147eaf]">Tentang Ruangriung</a>
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ route('events.index', ['period' => 'all']) }}" class="btn-primary hidden lg:inline-flex">
                    Jelajahi Event <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
                <button type="button" class="flex h-11 w-11 items-center justify-center rounded-xl border border-[#bfd8df] bg-white text-[#104b68] lg:hidden" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="public-mobile-menu" data-public-menu-button>
                    <i class="fa-solid fa-bars" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <nav id="public-mobile-menu" class="hidden border-t border-[#d7e1df] bg-[#f7f5ef] lg:hidden" aria-label="Navigasi mobile" data-public-menu>
            <div class="public-container flex flex-col gap-1 py-4 text-sm font-bold text-[#496d78]">
                <a href="{{ route('home') }}" class="rounded-xl px-3 py-3 hover:bg-white hover:text-[#147eaf]">Beranda</a>
                <a href="{{ route('events.index', ['period' => 'upcoming']) }}" class="rounded-xl px-3 py-3 hover:bg-white hover:text-[#147eaf]">Event</a>
                <a href="{{ route('home') }}#cerita" class="rounded-xl px-3 py-3 hover:bg-white hover:text-[#147eaf]">Cerita</a>
                <a href="{{ route('home') }}#tentang" class="rounded-xl px-3 py-3 hover:bg-white hover:text-[#147eaf]">Tentang Ruangriung</a>
                <a href="{{ route('events.index', ['period' => 'all']) }}" class="btn-primary mt-2">Jelajahi Event <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>
        </nav>
    </header>

    <main id="main-content" class="pt-20">
        @if (session('success'))
            <div class="public-container mt-5">
                <div class="flex items-start gap-3 border-l-4 border-[#147eaf] bg-white px-5 py-4 text-sm text-[#255b70] shadow-sm" role="status">
                    <i class="fa-solid fa-circle-check mt-0.5 text-[#147eaf]" aria-hidden="true"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @yield('content')
    </main>

    <footer id="tentang" class="border-t border-[#d7e1df] bg-[#103f56] text-white">
        <div class="public-container grid gap-12 py-14 md:grid-cols-[1.35fr_0.65fr_0.8fr] lg:py-18">
            <div>
                <a href="{{ route('home') }}" class="flex w-fit items-center gap-3 rounded-lg">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-white">
                        <img src="{{ asset('images/ruang-riung-icon.png') }}" alt="" class="h-10 w-10 object-contain">
                    </span>
                    <span class="text-sm font-extrabold uppercase tracking-[0.12em]">Ruang Riung</span>
                </a>
                <p class="mt-5 max-w-md text-sm leading-7 text-[#c6dbe1]">Ruang aman untuk berbagi cerita, membangun koneksi yang sehat, dan bertumbuh bersama melalui pertemuan komunitas yang bermakna.</p>
                @if ($adminWhatsAppUrl)
                    <a href="{{ $adminWhatsAppUrl }}" target="_blank" rel="noopener" class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-[#65c6f1] transition hover:text-white">
                        <i class="fa-brands fa-whatsapp text-lg" aria-hidden="true"></i> Hubungi admin
                    </a>
                @endif
            </div>

            <nav aria-label="Navigasi footer">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#65c6f1]">Jelajahi</p>
                <ul class="mt-5 space-y-3 text-sm font-semibold text-[#d7e8ec]">
                    <li><a href="{{ route('home') }}" class="transition hover:text-white">Beranda</a></li>
                    <li><a href="{{ route('events.index', ['period' => 'upcoming']) }}" class="transition hover:text-white">Event mendatang</a></li>
                    <li><a href="{{ route('events.index', ['period' => 'past']) }}" class="transition hover:text-white">Cerita sebelumnya</a></li>
                </ul>
            </nav>

            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#65c6f1]">Temukan kami</p>
                <p class="mt-5 flex items-start gap-3 text-sm leading-7 text-[#d7e8ec]">
                    <i class="fa-solid fa-location-dot mt-1 text-[#65c6f1]" aria-hidden="true"></i>
                    <span>Tasikmalaya, Priangan Timur<br>Jawa Barat, Indonesia</span>
                </p>
            </div>
        </div>

        <div class="border-t border-white/10">
            <div class="public-container flex flex-col items-center justify-between gap-2 py-5 text-center text-xs text-[#9fbfc8] sm:flex-row sm:text-left">
                <p>© {{ date('Y') }} Ruang Riung.</p>
                <p>Dibuat oleh <a href="https://yopihendriansah.com" target="_blank" rel="noopener" class="font-bold text-[#65c6f1] hover:text-white">Yopi Hendriansah</a>.</p>
            </div>
        </div>
    </footer>
</body>
</html>

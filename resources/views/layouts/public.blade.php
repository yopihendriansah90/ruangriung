<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('images/ruang-riung-icon.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Ruang Riung' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Ruang Riung — temukan event dan pendaftaran komunitas.' }}">
    {{-- Open Graph default (fallback). Halaman detail event menimpa via @push('meta'). --}}
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
<body class="min-h-screen bg-[#f5fbff] text-[#104b68] antialiased">
    <header class="fixed inset-x-0 top-0 z-50 border-b border-[#d5eaf5] bg-white/95 shadow-sm backdrop-blur">
        <div class="mx-auto flex min-h-16 max-w-7xl items-center justify-between gap-6 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3">
                <img src="{{ asset('images/ruang-riung-icon.png') }}" alt="Ruang Riung" class="h-10 w-10 rounded-lg object-contain sm:h-11 sm:w-11">
                <span class="leading-tight">
                    <span class="block text-[0.8rem] font-bold uppercase tracking-[0.08em] text-[#104b68]">Ruang Riung</span>
                </span>
            </a>

        </div>
    </header>

    <main class="pt-16">
        @if (session('success'))
            <div class="mx-auto mt-4 max-w-2xl px-4">
                <div class="rounded-xl border border-sky-400/30 bg-sky-400/10 p-4 text-sm text-sky-800">{{ session('success') }}</div>
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="mt-16 border-t border-[#d5eaf5] bg-white">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)_minmax(0,1fr)] lg:px-8">
            <div>
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/ruang-riung-icon.png') }}" alt="Ruang Riung" class="h-11 w-11 rounded-xl object-contain">
                    <span class="leading-tight">
                        <span class="block text-sm font-bold uppercase tracking-[0.08em] text-[#104b68]">Ruang Riung</span>
                    </span>
                </a>
                <p class="mt-4 max-w-sm text-sm leading-6 text-[#5d7d86]">Ruang aman untuk berbagi cerita, membangun koneksi yang sehat, dan bertumbuh bersama. Temukan event dan pendaftaran komunitas di sini.</p>
            </div>

            <nav aria-label="Navigasi footer">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#104b68]">Jelajahi</p>
                <ul class="mt-4 space-y-3 text-sm font-medium text-[#3f7183]">
                    <li><a href="{{ route('events.index') }}" class="transition hover:text-[#147eaf]">Semua event</a></li>
                    <li><a href="{{ route('home') }}" class="transition hover:text-[#147eaf]">Beranda</a></li>
                </ul>
            </nav>

            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#104b68]">Komunitas</p>
                <p class="mt-4 text-sm leading-6 text-[#5d7d86]">Tasikmalaya, Priangan Timur<br>Jawa Barat, Indonesia</p>
            </div>
        </div>

        <div class="border-t border-[#e4f1f9]">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 py-5 text-center text-xs leading-5 text-[#7a9a9d] sm:flex-row sm:px-6 sm:text-left lg:px-8">
                <p>© {{ date('Y') }} Ruang Riung.</p>
                <p>Dibuat oleh <a href="https://yopihendriansah.com" target="_blank" rel="noopener" class="font-semibold text-[#147eaf] hover:underline">Yopi Hendriansah</a>.</p>
            </div>
        </div>
    </footer>
</body>
</html>

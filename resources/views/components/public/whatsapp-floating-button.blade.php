@props(['event' => null, 'mobileCta' => false])

@php
    $url = app(\App\Services\AdminContactService::class)->whatsappUrl($event);
@endphp

@if ($url)
    <a
        href="{{ $url }}"
        target="_blank"
        rel="noopener"
        aria-label="Hubungi admin Ruang Riung via WhatsApp"
        class="fixed right-4 {{ $mobileCta ? 'bottom-24 lg:bottom-6' : 'bottom-6' }} z-50 flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-2xl text-white shadow-[0_12px_30px_rgba(16,75,104,0.25)] transition hover:-translate-y-1 hover:scale-105 hover:bg-[#1fbd5b] sm:right-6"
    >
        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
    </a>
@endif

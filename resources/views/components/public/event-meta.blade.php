@props(['event', 'showCapacity' => false, 'showAddress' => false, 'showMap' => false])

<div {{ $attributes->class('space-y-3 text-sm leading-6 text-[#496d78]') }}>
    <p class="flex items-start gap-3">
        <i class="fa-regular fa-calendar mt-1 w-4 shrink-0 text-center text-[#147eaf]" aria-hidden="true"></i>
        <span>{{ $event->event_date->translatedFormat('l, d F Y') }}</span>
    </p>
    <p class="flex items-start gap-3">
        <i class="fa-regular fa-clock mt-1 w-4 shrink-0 text-center text-[#147eaf]" aria-hidden="true"></i>
        <span>{{ $event->timeRange() }}</span>
    </p>
    <p class="flex items-start gap-3">
        <i class="fa-solid fa-location-dot mt-1 w-4 shrink-0 text-center text-[#147eaf]" aria-hidden="true"></i>
        <span>{{ $event->location_name }}@if ($showAddress && filled($event->location_address)) - {{ trim($event->location_address) }}@endif</span>
    </p>
    @if ($showCapacity)
        <p class="flex items-start gap-3">
            <i class="fa-solid fa-users mt-1 w-4 shrink-0 text-center text-[#147eaf]" aria-hidden="true"></i>
            <span>
                @if ($event->capacity_type === \App\Models\Event::CAPACITY_LIMITED)
                    {{ $event->isFull() ? 'Kapasitas sudah penuh' : 'Tersisa '.number_format($event->remainingSeats()).' dari '.number_format($event->capacity).' peserta' }}
                @else
                    Kapasitas tidak dibatasi
                @endif
            </span>
        </p>
    @endif
    @if ($showMap && filled($event->google_maps_url))
        <p class="flex items-start gap-3">
            <i class="fa-solid fa-map-location-dot mt-1 w-4 shrink-0 text-center text-[#147eaf]" aria-hidden="true"></i>
            <a href="{{ $event->google_maps_url }}" target="_blank" rel="noopener" class="inline-flex min-h-6 items-center gap-2 font-bold text-[#147eaf] hover:text-[#104b68] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#147eaf]">
                Buka Google Maps
                <i class="fa-solid fa-arrow-up-right-from-square text-xs" aria-hidden="true"></i>
            </a>
        </p>
    @endif
</div>

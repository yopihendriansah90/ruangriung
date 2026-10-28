@props(['event', 'story' => false])

@php
    $poster = $event->getFirstMediaUrl('event-posters');
    $summary = \Illuminate\Support\Str::of(strip_tags($event->description ?? ''))->squish()->limit(116);
@endphp

<article {{ $attributes->class('event-card group') }}>
    <a href="{{ route('events.show', $event) }}" class="block focus:outline-none" aria-label="Lihat detail {{ $event->name }}">
        <div class="relative aspect-[4/5] overflow-hidden rounded-[1.25rem] bg-[#e8f2f3]">
            @if ($poster)
                <img src="{{ $poster }}" alt="Poster {{ $event->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.025]" loading="lazy" decoding="async">
            @else
                <div class="flex h-full flex-col items-center justify-center gap-3 px-6 text-center text-[#6f8d91]">
                    <i class="fa-regular fa-image text-3xl" aria-hidden="true"></i>
                    <span class="text-sm font-semibold">Poster segera hadir</span>
                </div>
            @endif
            <div class="absolute left-4 top-4">
                <x-public.event-status-badge :event="$event" />
            </div>
            <span class="absolute bottom-4 right-4 flex h-10 w-10 items-center justify-center rounded-full bg-white text-[#104b68] shadow-sm transition group-hover:-rotate-12 group-hover:bg-[#65c6f1]" aria-hidden="true">
                <i class="fa-solid fa-arrow-up-right-from-square text-sm"></i>
            </span>
        </div>

        <div class="pt-5">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#147eaf]">
                {{ $story ? 'Cerita pertemuan' : $event->event_date->translatedFormat('d F Y') }}
            </p>
            <h3 class="mt-2 text-xl font-bold leading-snug text-[#104b68] transition group-hover:text-[#147eaf]">{{ $event->name }}</h3>
            @if ($story)
                <p class="mt-2 line-clamp-2 text-sm leading-6 text-[#5d7d86]">{{ $summary }}</p>
            @endif
            <p class="mt-3 flex items-start gap-2 text-sm text-[#496d78]">
                <i class="fa-solid fa-location-dot mt-1 text-[#147eaf]" aria-hidden="true"></i>
                <span>{{ $event->location_name }}</span>
            </p>
        </div>
    </a>
</article>

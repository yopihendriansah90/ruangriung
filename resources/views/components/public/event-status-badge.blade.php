@props(['event'])

@php
    $status = match (true) {
        $event->isEnded() => [
            'label' => 'Sudah berakhir',
            'icon' => 'fa-clock-rotate-left',
            'class' => 'bg-amber-100 text-amber-800',
        ],
        $event->event_date?->isToday() => [
            'label' => 'Sedang berlangsung',
            'icon' => 'fa-play',
            'class' => 'bg-emerald-100 text-emerald-700',
        ],
        $event->status === \App\Models\Event::STATUS_CLOSED => [
            'label' => 'Pendaftaran ditutup',
            'icon' => 'fa-lock',
            'class' => 'bg-slate-100 text-slate-700',
        ],
        $event->isFull() => [
            'label' => 'Kapasitas penuh',
            'icon' => 'fa-users',
            'class' => 'bg-red-100 text-red-700',
        ],
        default => [
            'label' => 'Pendaftaran dibuka',
            'icon' => 'fa-circle-check',
            'class' => 'bg-[#dff4fd] text-[#0d668e]',
        ],
    };
@endphp

<span {{ $attributes->class(['inline-flex w-fit items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold', $status['class']]) }}>
    <i class="fa-solid {{ $status['icon'] }}" aria-hidden="true"></i>
    {{ $status['label'] }}
</span>

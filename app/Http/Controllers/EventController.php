<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $requestedPeriod = (string) $request->query('period', 'all');
        $period = in_array($requestedPeriod, ['all', 'upcoming', 'past'], true) ? $requestedPeriod : 'all';
        $category = (string) $request->query('category');
        if ($category !== '' && ! array_key_exists($category, Event::CATEGORIES)) {
            $category = '';
        }
        $isFiltered = $search !== '' || $request->has('period') || $request->has('category');

        if ($isFiltered) {
            $events = $this->publicEventsQuery()
                ->when($search !== '', function (Builder $query) use ($search): void {
                    $query->where(function (Builder $query) use ($search): void {
                        $query->where('name', 'like', '%'.$search.'%')
                            ->orWhere('description', 'like', '%'.$search.'%')
                            ->orWhere('location_name', 'like', '%'.$search.'%')
                            ->orWhere('location_address', 'like', '%'.$search.'%');
                    });
                })
                ->when($category !== '', fn (Builder $query): Builder => $query->where('category', $category))
                ->when($period === 'upcoming', fn (Builder $query): Builder => $query->whereDate('event_date', '>=', today()))
                ->when($period === 'past', fn (Builder $query): Builder => $query->whereDate('event_date', '<', today()))
                ->when(
                    $period === 'upcoming',
                    fn (Builder $query): Builder => $query->orderBy('event_date')->orderBy('start_time')->orderBy('id'),
                    fn (Builder $query): Builder => $query->latest('event_date')->latest('id'),
                )
                ->paginate(9)
                ->withQueryString();

            return view('events.index', [
                'events' => $events,
                'featuredEvent' => null,
                'upcomingEvents' => collect(),
                'pastEvents' => collect(),
                'search' => $search,
                'period' => $period,
                'category' => $category,
                'isFiltered' => true,
                'title' => 'Jelajahi Event — Ruang Riung',
            ]);
        }

        $featuredEvent = $this->publicEventsQuery()
            ->where('status', Event::STATUS_PUBLISHED)
            ->whereDate('event_date', '>=', today())
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->orderBy('id')
            ->first();

        $upcomingEvents = $this->publicEventsQuery()
            ->whereDate('event_date', '>=', today())
            ->when($featuredEvent, fn (Builder $query): Builder => $query->whereKeyNot($featuredEvent->getKey()))
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->orderBy('id')
            ->limit(6)
            ->get();

        $pastEvents = $this->publicEventsQuery()
            ->whereDate('event_date', '<', today())
            ->latest('event_date')
            ->latest('id')
            ->limit(3)
            ->get();

        return view('events.index', [
            'events' => null,
            'featuredEvent' => $featuredEvent,
            'upcomingEvents' => $upcomingEvents,
            'pastEvents' => $pastEvents,
            'search' => '',
            'period' => 'all',
            'category' => '',
            'isFiltered' => false,
            'title' => 'Ruang Riung — Event dan Pertemuan Komunitas',
        ]);
    }

    public function show(Event $event)
    {
        abort_unless($event->isPubliclyVisible(), 404);

        $event->loadCount([
            'registrations as registered_registrations_count' => fn (Builder $query) => $query->where('status', Registration::STATUS_REGISTERED),
        ]);

        $recommendedEvents = $this->publicEventsQuery()
            ->where('status', Event::STATUS_PUBLISHED)
            ->whereDate('event_date', '>=', today())
            ->whereKeyNot($event->getKey())
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->limit(3)
            ->get();

        // Open Graph untuk preview link WhatsApp/Telegram/FB.
        // Prioritas: gambar preview WhatsApp (event-og-images) -> poster -> logo.
        // Aturan resmi Meta/Facebook (dipakai juga oleh WhatsApp):
        // - format JPG/PNG (hindari WebP untuk OG), maksimal 8 MB (kita batasi 2 MB di form)
        // - minimal 200x200 px, ideal 1200x630 px rasio 1.91:1, minimal besar 600x315 px
        $ogMedia = $event->getFirstMedia('event-og-images') ?: $event->getFirstMedia('event-posters');
        $posterUrl = $ogMedia?->getUrl() ?: asset('images/ruang-riung-logo.png');
        $ogImageWidth = null;
        $ogImageHeight = null;
        if ($ogMedia) {
            try {
                $size = @getimagesize($ogMedia->getPath());
                if (is_array($size)) {
                    $ogImageWidth = $size[0];
                    $ogImageHeight = $size[1];
                }
            } catch (\Throwable $e) {
                // abaikan, width/height opsional
            }
        }
        $plainDescription = Str::of(strip_tags($event->description ?? ''))->squish()->limit(160)->toString();
        $eventDate = $event->event_date ? $event->event_date->translatedFormat('d F Y') : null;

        return view('events.show', [
            'event' => $event,
            'title' => $event->name.' — Ruang Riung',
            'metaDescription' => $plainDescription !== '' ? $plainDescription : $event->name.($eventDate ? ' — '.$eventDate : ''),
            'ogType' => 'article',
            'ogTitle' => $event->name,
            'ogDescription' => $plainDescription !== '' ? $plainDescription : $event->name.($eventDate ? ' — '.$eventDate : ''),
            'ogImage' => $posterUrl,
            'ogImageWidth' => $ogImageWidth,
            'ogImageHeight' => $ogImageHeight,
            'ogUrl' => route('events.show', $event),
            'recommendedEvents' => $recommendedEvents,
        ]);
    }

    public function register(Event $event)
    {
        abort_unless($event->status === Event::STATUS_PUBLISHED && ! $event->isEnded(), 404);

        $event->loadCount([
            'registrations as registered_registrations_count' => fn (Builder $query) => $query->where('status', Registration::STATUS_REGISTERED),
        ]);
        $isFull = $event->isFull();

        return view('events.register', [
            'event' => $event,
            'isFull' => $isFull,
            'title' => 'Daftar '.$event->name.' — Ruang Riung',
        ]);
    }

    private function publicEventsQuery(): Builder
    {
        return Event::query()
            ->whereIn('status', [Event::STATUS_PUBLISHED, Event::STATUS_CLOSED])
            ->withCount([
                'registrations as registered_registrations_count' => fn (Builder $query) => $query->where('status', Registration::STATUS_REGISTERED),
            ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        Event::create([
            'name' => 'MHC Test Event',
            'slug' => 'mhc-test-event',
            'event_date' => today()->addDay(),
            'start_time' => '10:00',
            'description' => 'Test event.',
            'location_name' => 'Test location',
            'capacity_type' => Event::CAPACITY_UNLIMITED,
            'status' => Event::STATUS_PUBLISHED,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_public_index_shows_closed_and_ended_events_as_history(): void
    {
        Event::create([
            'name' => 'Closed History Event',
            'slug' => 'closed-history-event',
            'event_date' => today()->subDay(),
            'start_time' => '10:00',
            'description' => 'Closed event.',
            'location_name' => 'History location',
            'capacity_type' => Event::CAPACITY_UNLIMITED,
            'status' => Event::STATUS_CLOSED,
        ]);

        Event::create([
            'name' => 'Ended Published Event',
            'slug' => 'ended-published-event',
            'event_date' => today()->subDays(2),
            'start_time' => '10:00',
            'description' => 'Ended event.',
            'location_name' => 'Ended location',
            'capacity_type' => Event::CAPACITY_UNLIMITED,
            'status' => Event::STATUS_PUBLISHED,
        ]);

        $this->get(route('events.index'))
            ->assertOk()
            ->assertSee('Closed History Event')
            ->assertSee('Ended Published Event')
            ->assertSee('Sudah berakhir');
    }

    public function test_closed_event_detail_is_visible_but_registration_page_is_not_available(): void
    {
        $event = Event::create([
            'name' => 'Closed Event Detail',
            'slug' => 'closed-event-detail',
            'event_date' => today()->subDay(),
            'start_time' => '10:00',
            'description' => 'Closed event.',
            'location_name' => 'History location',
            'capacity_type' => Event::CAPACITY_UNLIMITED,
            'status' => Event::STATUS_CLOSED,
        ]);

        $this->get(route('events.show', $event))
            ->assertOk()
            ->assertSee('Pendaftaran untuk event ini sudah ditutup.');

        $this->get(route('events.register', $event))->assertNotFound();
    }

    public function test_public_index_orders_events_from_newest_to_oldest_and_can_search(): void
    {
        Event::create([
            'name' => 'Event Lama',
            'slug' => 'event-lama',
            'event_date' => today()->subDays(3),
            'start_time' => '10:00',
            'description' => 'Diskusi lama.',
            'location_name' => 'Ruang Lama',
            'capacity_type' => Event::CAPACITY_UNLIMITED,
            'status' => Event::STATUS_PUBLISHED,
        ]);

        Event::create([
            'name' => 'Event Baru',
            'slug' => 'event-baru',
            'event_date' => today()->addDays(2),
            'start_time' => '10:00',
            'description' => 'Diskusi baru.',
            'location_name' => 'Ruang Baru',
            'capacity_type' => Event::CAPACITY_UNLIMITED,
            'status' => Event::STATUS_PUBLISHED,
        ]);

        $this->get(route('events.index'))
            ->assertOk()
            ->assertSeeInOrder(['Event Baru', 'Event Lama']);

        $this->get(route('events.index', ['search' => 'Lama']))
            ->assertOk()
            ->assertSee('Event Lama')
            ->assertDontSee('Event Baru');
    }

    public function test_public_landing_selects_nearest_featured_and_separates_upcoming_and_past_events(): void
    {
        $nearest = $this->event([
            'name' => 'Pertemuan Terdekat',
            'slug' => 'pertemuan-terdekat',
            'event_date' => today()->addDay(),
        ]);
        $later = $this->event([
            'name' => 'Pertemuan Berikutnya',
            'slug' => 'pertemuan-berikutnya',
            'event_date' => today()->addDays(5),
        ]);
        $past = $this->event([
            'name' => 'Cerita Sebelumnya',
            'slug' => 'cerita-sebelumnya',
            'event_date' => today()->subDay(),
        ]);
        $draft = $this->event([
            'name' => 'Event Rahasia',
            'slug' => 'event-rahasia',
            'event_date' => today(),
            'status' => Event::STATUS_DRAFT,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertViewHas('featuredEvent', fn (?Event $event): bool => $event?->is($nearest) === true)
            ->assertViewHas('upcomingEvents', fn ($events): bool => $events->contains($later) && ! $events->contains($nearest))
            ->assertViewHas('pastEvents', fn ($events): bool => $events->contains($past))
            ->assertSeeInOrder(['Event terdekat', 'Pertemuan Terdekat', 'Event yang akan datang', 'Pertemuan Berikutnya', 'Cerita dari Ruangriung', 'Cerita Sebelumnya'])
            ->assertSee('aria-label="Lihat detail Pertemuan Terdekat"', false)
            ->assertDontSee($draft->name);
    }

    public function test_public_landing_has_useful_empty_states_without_events(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Belum ada event lain yang dijadwalkan')
            ->assertSee('Cerita pertemuan sebelumnya akan hadir di sini');
    }

    public function test_period_filter_separates_upcoming_and_past_and_normalizes_invalid_values(): void
    {
        $upcoming = $this->event([
            'name' => 'Event Masa Depan',
            'slug' => 'event-masa-depan',
            'event_date' => today()->addDay(),
        ]);
        $past = $this->event([
            'name' => 'Event Masa Lalu',
            'slug' => 'event-masa-lalu',
            'event_date' => today()->subDay(),
        ]);

        $this->get(route('events.index', ['period' => 'upcoming']))
            ->assertOk()
            ->assertSee($upcoming->name)
            ->assertDontSee($past->name);

        $this->get(route('events.index', ['period' => 'past']))
            ->assertOk()
            ->assertSee($past->name)
            ->assertDontSee($upcoming->name);

        $this->get(route('events.index', ['period' => 'nanti']))
            ->assertOk()
            ->assertViewHas('period', 'all')
            ->assertSee($upcoming->name)
            ->assertSee($past->name);
    }

    public function test_event_detail_recommends_other_upcoming_published_events_only(): void
    {
        $current = $this->event([
            'name' => 'Event Saat Ini',
            'slug' => 'event-saat-ini',
            'event_date' => today()->addDay(),
        ]);
        $recommended = $this->event([
            'name' => 'Event Rekomendasi',
            'slug' => 'event-rekomendasi',
            'event_date' => today()->addDays(2),
        ]);
        $draft = $this->event([
            'name' => 'Draft Rekomendasi',
            'slug' => 'draft-rekomendasi',
            'event_date' => today()->addDays(3),
            'status' => Event::STATUS_DRAFT,
        ]);

        $this->get(route('events.show', $current))
            ->assertOk()
            ->assertViewHas('recommendedEvents', function ($events) use ($current, $recommended, $draft): bool {
                return $events->contains($recommended)
                    && ! $events->contains($current)
                    && ! $events->contains($draft);
            })
            ->assertSee($recommended->name)
            ->assertDontSee($draft->name);
    }

    public function test_event_detail_places_registration_after_description_and_map_after_capacity(): void
    {
        $event = $this->event([
            'name' => 'Event Dengan Susunan Detail',
            'slug' => 'event-dengan-susunan-detail',
            'description' => 'Baca penjelasan event ini sebelum mendaftar.',
            'google_maps_url' => 'https://maps.google.com/?q=Ruang+Riung',
            'capacity_type' => Event::CAPACITY_LIMITED,
            'capacity' => 20,
        ]);

        $this->get(route('events.show', $event))
            ->assertOk()
            ->assertSeeInOrder([
                'Tersisa 20 dari 20 peserta',
                'Buka Google Maps',
                'Baca penjelasan event ini sebelum mendaftar.',
                'Daftar sekarang',
            ])
            ->assertSee('href="'.route('events.register', $event).'"', false);
    }

    public function test_filtered_event_pagination_keeps_search_and_period_query(): void
    {
        foreach (range(1, 10) as $index) {
            $this->event([
                'name' => 'Event Khusus '.$index,
                'slug' => 'event-khusus-'.$index,
                'event_date' => today()->addDays($index),
            ]);
        }

        $this->get(route('events.index', ['search' => 'Khusus', 'period' => 'upcoming']))
            ->assertOk()
            ->assertViewHas('events', function ($events): bool {
                $secondPageUrl = $events->url(2);

                return $events->hasPages()
                    && str_contains($secondPageUrl, 'search=Khusus')
                    && str_contains($secondPageUrl, 'period=upcoming');
            });
    }

    private function event(array $attributes = []): Event
    {
        return Event::create(array_merge([
            'name' => 'Ruang Riung Event',
            'slug' => 'ruang-riung-event-'.uniqid(),
            'event_date' => today()->addDay(),
            'start_time' => '10:00',
            'description' => 'Pertemuan komunitas Ruang Riung.',
            'location_name' => 'Ruang Bersama',
            'capacity_type' => Event::CAPACITY_UNLIMITED,
            'status' => Event::STATUS_PUBLISHED,
        ], $attributes));
    }
}

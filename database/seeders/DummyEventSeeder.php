<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DummyEventSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Dummy event untuk uji tampilan beranda & dashboard Filament.
     * 5 event: hari ini (2), besok (2), lusa (1) — jam 08:00 sampai 12:00.
     */
    public function run(): void
    {
        $images = collect(glob('/home/popes/Pictures/Wallpapers/*.{jpg,jpeg,png}', GLOB_BRACE))
            ->values();
        if ($images->isEmpty()) {
            $this->command->warn('Tidak ada gambar di /home/popes/Pictures/Wallpapers — poster dilewati.');
        }

        $events = [
            [
                'name' => 'Kopdari Mingguan Ruang Riung',
                'category' => 'komunitas-networking',
                'day' => 0,
                'location_name' => 'Kafe Sambil Kopi, Tasikmalaya',
                'location_address' => 'Jl. Kijang No. 12, Tasikmalaya',
                'capacity_type' => Event::CAPACITY_UNLIMITED,
            ],
            [
                'name' => 'Workshop Menulis Cerita Pertemuan',
                'category' => 'seminar-workshop',
                'day' => 0,
                'location_name' => 'Ruang Belajar Mandiri Kota Tasikmalaya',
                'location_address' => 'Jl. Sekar Kadalepan No. 30, Tasikmalaya',
                'capacity_type' => Event::CAPACITY_LIMITED,
                'capacity' => 25,
            ],
            [
                'name' => 'Senam Pagi Bareng Komunitas',
                'category' => 'olahraga',
                'day' => -1, // kemarin — agar terisi sebagai "cerita" di beranda
                'location_name' => 'Lapangan Bola Taman Bendungan',
                'location_address' => 'Perumahan Bendungan Cihideung, Tasikmalaya',
                'capacity_type' => Event::CAPACITY_UNLIMITED,
            ],
            [
                'name' => 'Kelas Koding Dasar untuk Anak Muda',
                'category' => 'pendidikan',
                'day' => 1,
                'location_name' => 'Coworking Space Tasik',
                'location_address' => 'Jl. Aceh No. 55, Tasikmalaya',
                'capacity_type' => Event::CAPACITY_LIMITED,
                'capacity' => 30,
            ],
            [
                'name' => 'Rihalah: Jelajah Taman Wisata',
                'category' => 'wisata-rekreasi',
                'day' => 2,
                'location_name' => 'Taman Wisata Kota Tasikmalaya',
                'location_address' => 'Jl. GHOSA No. 81, Tasikmalaya',
                'capacity_type' => Event::CAPACITY_UNLIMITED,
            ],
        ];

        $eventIndex = 0;
        foreach ($events as $data) {
            $slug = Str::slug($data['name']);
            $eventDate = Carbon::today()->addDays($data['day']);

            $event = Event::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'category' => $data['category'],
                    'event_date' => $eventDate->toDateString(),
                    'start_time' => '08:00',
                    'end_time' => '12:00',
                    'description' => $this->description($data),
                    'location_name' => $data['location_name'],
                    'location_address' => $data['location_address'],
                    'google_maps_url' => 'https://maps.google.com/?q='.urlencode($data['location_name']),
                    'whatsapp_group_url' => null,
                    'capacity_type' => $data['capacity_type'],
                    'capacity' => $data['capacity'] ?? null,
                    'status' => Event::STATUS_PUBLISHED,
                ],
            );

            // Poster dari wallpaper lokal — ambil gambar bergiliran.
            if ($images->isNotEmpty() && $event->getMedia('event-posters')->isEmpty()) {
                $image = $images[$eventIndex % $images->count()];
                $event->addMedia($image)
                    ->preservingOriginal()
                    ->toMediaCollection('event-posters');

                $event->addMedia($image)
                    ->preservingOriginal()
                    ->toMediaCollection('event-og-images');
            }
            $eventIndex++;
        }

        $this->command->info('Seed dummy '.count($events).' event selesai.');
    }

    private function description(array $data): string
    {
        return '<h2>Tentang pertemuan ini</h2>'
            .'<p>'.e($data['name']).' adalah pertemuan komunitas Ruang Riung yang dirancang untuk saling mengenal, berbagi cerita, dan bertumbuh bersama. Datanglah sebagai dirimu sendiri — pulang dengan koneksi baru.</p>'
            .'<h3>Yang perlu kamu siapkan</h3>'
            .'<ul><li>Nametag dan semangat positif</li><li>Catatan atau gadget kalau ingin mendokumentasikan</li><li>Salemba kecil untuk ibadah/konsumsi pribadi</li></ul>'
            .'<h3>Rundown singkat</h3>'
            .'<p>08:00 –Registrasi dan tepa tepar<br>09:00 –Sesi perkenalan<br>10:30 –Aktivitas utama<br>11:45 – Foto bersama dan tutup</p>'
            .'<blockquote>"Pertemuan boleh singkat, percakapan yang tumbuh bisa berjalan lebih dekat."</blockquote>';
    }
}

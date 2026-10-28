<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Event extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_CLOSED = 'closed';

    public const CAPACITY_LIMITED = 'limited';

    public const CAPACITY_UNLIMITED = 'unlimited';

    /** Daftar kategori event, tampil di beranda & halaman event. */
    public const CATEGORIES = [
        '' => ['label' => 'Semua Event', 'icon' => 'fa-solid fa-grip'],
        'seminar-workshop' => ['label' => 'Seminar & Workshop', 'icon' => 'fa-solid fa-microphone'],
        'komunitas-networking' => ['label' => 'Komunitas & Networking', 'icon' => 'fa-solid fa-user-group'],
        'keagamaan' => ['label' => 'Keagamaan', 'icon' => 'fa-solid fa-mosque'],
        'olahraga' => ['label' => 'Olahraga', 'icon' => 'fa-solid fa-person-running'],
        'seni-budaya' => ['label' => 'Seni & Budaya', 'icon' => 'fa-solid fa-palette'],
        'pendidikan' => ['label' => 'Pendidikan', 'icon' => 'fa-solid fa-graduation-cap'],
        'kompetisi-lomba' => ['label' => 'Kompetisi & Lomba', 'icon' => 'fa-solid fa-trophy'],
        'wisata-rekreasi' => ['label' => 'Wisata & Rekreasi', 'icon' => 'fa-solid fa-mountain-sun'],
        'santun-anak-muda' => ['label' => 'Santun Anak Muda', 'icon' => 'fa-solid fa-comments'],
        'lainnya' => ['label' => 'Lainnya', 'icon' => 'fa-solid fa-ellipsis'],
    ];

    protected $fillable = [
        'name',
        'slug',
        'category',
        'event_date',
        'start_time',
        'end_time',
        'description',
        'location_name',
        'location_address',
        'google_maps_url',
        'whatsapp_group_url',
        'capacity_type',
        'capacity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'capacity' => 'integer',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category]['label'] ?? self::CATEGORIES['lainnya']['label'];
    }

    public function getCategoryIconAttribute(): string
    {
        return self::CATEGORIES[$this->category]['icon'] ?? self::CATEGORIES['lainnya']['icon'];
    }

    public static function categoryOptions(): array
    {
        return collect(self::CATEGORIES)
            ->filter(fn (array $item, string $key): bool => $key !== '')
            ->mapWithKeys(fn (array $item, string $key): array => [$key => $item['label']])
            ->all();
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('event-posters')->singleFile();
        $this->addMediaCollection('event-og-images')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')->width(800)->height(1000)->nonQueued();
    }

    public function hasAvailableCapacity(): bool
    {
        return ! $this->isFull();
    }

    public function registeredCount(): int
    {
        if (array_key_exists('registered_registrations_count', $this->attributes)) {
            return (int) $this->attributes['registered_registrations_count'];
        }

        return $this->registrations()->where('status', Registration::STATUS_REGISTERED)->count();
    }

    public function isFull(): bool
    {
        return $this->capacity_type === self::CAPACITY_LIMITED
            && $this->registeredCount() >= (int) $this->capacity;
    }

    public function remainingSeats(): ?int
    {
        if ($this->capacity_type !== self::CAPACITY_LIMITED) {
            return null;
        }

        return max((int) $this->capacity - $this->registeredCount(), 0);
    }

    public function isEnded(): bool
    {
        return $this->event_date?->isPast() && ! $this->event_date?->isToday();
    }

    public function isPubliclyVisible(): bool
    {
        return in_array($this->status, [self::STATUS_PUBLISHED, self::STATUS_CLOSED], true);
    }

    public function isRegistrationOpen(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && ! $this->isEnded()
            && $this->hasAvailableCapacity();
    }
}

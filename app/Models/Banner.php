<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Banner extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'cta_url',
        'sort_order',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('banner-images')
            ->singleFile();
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true)->orderBy('sort_order')->orderBy('id');
    }

    public function imageUrl(): string
    {
        return $this->getFirstMediaUrl('banner-images');
    }
}

<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail iklan banner')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama iklan')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('banner_image')
                            ->label('Gambar banner')
                            ->helperText('Tampil utuh sebagai banner (tanpa teks tambahan). Rekomendasi rasio memanjang 16:5, ukuran 1600 × 500 px atau lebih besar. Format JPG/PNG/WebP.')
                            ->collection('banner-images')
                            ->image()
                            ->maxSize(4096)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('cta_url')
                            ->label('Link CTA')
                            ->url()
                            ->hintIcon('heroicon-m-link')
                            ->helperText('Halaman tujuan ketika banner diklik (event, WhatsApp, atau link lain). Kosongkan jika banner tidak perlu diklik.')
                            ->columnSpanFull(),
                        TextInput::make('sort_order')
                            ->label('Urutan tampil')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->default(0)
                            ->helperText('Angka kecil tampil lebih dulu.'),
                        Toggle::make('is_enabled')
                            ->label('Aktifkan banner')
                            ->default(true)
                            ->helperText('Banner nonaktif tidak ditampilkan di halaman publik.'),
                    ]),
            ]);
    }
}

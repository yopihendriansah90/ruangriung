<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Models\Event;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama event')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Sisipkan &lt;br&gt; di nama untuk memecah judul menjadi dua baris pada halaman detail event. Slug tidak lagi otomatis — gunakan tombol generate di sebelah field slug.'),
                TextInput::make('slug')
                    ->label('Slug URL')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->suffixAction(
                        \Filament\Actions\Action::make('generateSlug')
                            ->label('Generate otomatis dari nama event')
                            ->icon('heroicon-m-arrow-path')
                            ->color('gray')
                            ->action(function (Get $get, Set $set): void {
                                $name = trim((string) $get('name'));
                                // Buang tag <br> agar tidak mengganggu slug.
                                $name = preg_replace('/<br\s*\/?>/i', ' ', $name);
                                $slug = Str::slug((string) $name);
                                if ($slug !== '') {
                                    $set('slug', $slug);
                                }
                            })
                    ),
                Select::make('category')
                    ->label('Kategori')
                    ->options(Event::categoryOptions())
                    ->default('lainnya')
                    ->required(),
                DatePicker::make('event_date')->label('Tanggal event')->required()->native(false),
                TimePicker::make('start_time')->label('Jam mulai')->required()->seconds(false),
                TimePicker::make('end_time')->label('Jam selesai')->seconds(false),
                RichEditor::make('description')
                    ->label('Deskripsi lengkap')
                    ->helperText('Gunakan heading, paragraf, list, link, atau kutipan untuk menyusun informasi event.')
                    ->placeholder('Tulis deskripsi event di sini...')
                    ->required()
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'link',
                        'bulletList',
                        'orderedList',
                        'h2',
                        'h3',
                        'blockquote',
                        'undo',
                        'redo',
                    ])
                    ->columnSpanFull(),
                TextInput::make('location_name')->label('Nama lokasi')->required()->maxLength(255),
                Textarea::make('location_address')->label('Alamat/keterangan lokasi')->rows(3),
                TextInput::make('google_maps_url')->label('Link Google Maps')->url()->maxLength(500),
                TextInput::make('whatsapp_group_url')->label('Link grup WhatsApp')->url()->maxLength(500),
                Select::make('capacity_type')
                    ->label('Tipe kapasitas')
                    ->options(['limited' => 'Terbatas', 'unlimited' => 'Unlimited'])
                    ->default(Event::CAPACITY_UNLIMITED)
                    ->required()
                    ->live(),
                TextInput::make('capacity')
                    ->label('Maksimal peserta')
                    ->numeric()
                    ->minValue(1)
                    ->required(fn (Get $get): bool => $get('capacity_type') === Event::CAPACITY_LIMITED)
                    ->visible(fn (Get $get): bool => $get('capacity_type') === Event::CAPACITY_LIMITED),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        Event::STATUS_DRAFT => 'Draft',
                        Event::STATUS_PUBLISHED => 'Published',
                        Event::STATUS_CLOSED => 'Closed',
                    ])
                    ->default(Event::STATUS_DRAFT)
                    ->required(),
                SpatieMediaLibraryFileUpload::make('poster')
                    ->label('Poster event')
                    ->helperText('Tampil di halaman web. Rekomendasi format Instagram portrait 4:5, ukuran 1080 × 1350 px.')
                    ->collection('event-posters')
                    ->image()
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('og_image')
                    ->label('Poster WhatsApp (portrait)')
                    ->helperText('Rekomendasi portrait 1080 × 1350 px (rasio 4:5, sama seperti poster IG). Minimal 600 × 750 px, JPG/PNG, wajib di bawah 600 KB agar keload di WA. Usahakan teks/judul penting di area tengah agar aman tidak terpotong saat jadi preview link. Kalau kosong, otomatis pakai poster event.')
                    ->collection('event-og-images')
                    ->image()
                    ->maxSize(600)
                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                    ->columnSpanFull(),
            ]);
    }
}

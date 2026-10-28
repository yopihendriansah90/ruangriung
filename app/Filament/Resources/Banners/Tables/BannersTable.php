<?php

namespace App\Filament\Resources\Banners\Tables;

use App\Models\Banner;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modelLabel('Iklan banner')
            ->pluralModelLabel('Iklan Banner')
            ->columns([
                ImageColumn::make('banner_image')
                    ->label('Banner')
                    ->circular(false)
                    ->getStateUsing(fn (Banner $record): ?string => $record->imageUrl() ?: null),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('cta_url')
                    ->label('Link CTA')
                    ->limit(40)
                    ->toggleable()
                    ->placeholder('—'),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable()
                    ->alignCenter(),
                ToggleColumn::make('is_enabled')
                    ->label('Aktif')
                    ->afterStateUpdated(fn (): Notification => Notification::make()->title('Status banner diperbarui.')->success()),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->label('Hapus')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus iklan banner')
                    ->modalDescription(fn (Banner $record): string => "Yakin ingin menghapus iklan banner {$record->name}? Gambar ikut terhapus. Tindakan ini tidak bisa dibatalkan.")
                    ->modalSubmitActionLabel('Ya, hapus')
                    ->successNotification(fn (Banner $record): Notification => Notification::make()->title("Iklan banner {$record->name} berhasil dihapus.")->success()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang dipilih')
                        ->modalHeading('Hapus iklan banner terpilih')
                        ->modalDescription('Semua iklan banner yang dipilih akan dihapus permanen beserta gambarnya. Tindakan ini tidak bisa dibatalkan.')
                        ->modalSubmitActionLabel('Ya, hapus')
                        ->successNotification(fn (): Notification => Notification::make()->title('Iklan banner terpilih berhasil dihapus.')->success()),
                ]),
            ]);
    }
}

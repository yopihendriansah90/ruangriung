<?php

namespace App\Filament\Resources\Attendances\Tables;

use App\Models\Attendance;
use App\Models\Event;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rowIndex')->label('No.')->rowIndex()->alignCenter(),
                TextColumn::make('registration.name')->label('Peserta')->searchable()->sortable()->weight('semibold'),
                TextColumn::make('registration.event.name')->label('Event')->searchable()->sortable()->wrap(),
                TextColumn::make('registration.email')->label('Email')->searchable()->toggleable(),
                TextColumn::make('registration.phone')->label('WhatsApp')->toggleable(),
                TextColumn::make('checked_in_at')->label('Waktu check-in')->dateTime('d M Y H:i')->suffix(' WIB')->sortable(),
                TextColumn::make('checkedInBy.name')->label('Admin')->placeholder('—'),
                TextColumn::make('check_in_method')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        Attendance::METHOD_QR_CODE => 'QR Code',
                        Attendance::METHOD_MANUAL => 'Manual',
                        default => (string) $state,
                    }),
            ])
            ->filters([
                SelectFilter::make('event_id')
                    ->label('Event')
                    ->options(fn (): array => Event::query()->orderBy('event_date')->pluck('name', 'id')->all())
                    ->searchable()
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $query, $eventId): Builder => $query->whereHas('registration', fn (Builder $query): Builder => $query->where('event_id', $eventId)),
                    )),
                SelectFilter::make('check_in_method')->label('Metode')->options([
                    Attendance::METHOD_QR_CODE => 'QR Code',
                    Attendance::METHOD_MANUAL => 'Manual',
                ]),
                Filter::make('checked_in_date')
                    ->label('Tanggal check-in')
                    ->form([
                        DatePicker::make('date')->label('Tanggal')->native(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['date'] ?? null,
                        fn (Builder $query, $date): Builder => $query->whereDate('checked_in_at', $date),
                    )),
            ])
            ->headerActions([
                Action::make('openCheckIn')
                    ->label('Buka scan QR')
                    ->icon('heroicon-o-qr-code')
                    ->url('/check-in')
                    ->openUrlInNewTab(),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->label('Hapus kehadiran')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus data kehadiran')
                    ->modalDescription(fn (Attendance $record): string => "Yakin ingin menghapus data kehadiran {$record->registration->name}? Data registrasi peserta tetap tersimpan dan peserta dapat check-in ulang. Tindakan ini tidak bisa dibatalkan.")
                    ->modalSubmitActionLabel('Ya, hapus')
                    ->successNotificationTitle('Data kehadiran berhasil dihapus.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang dipilih')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus data kehadiran terpilih')
                        ->modalDescription('Semua data kehadiran yang dipilih akan dihapus permanen. Data registrasi peserta tetap tersimpan dan peserta dapat check-in ulang. Tindakan ini tidak bisa dibatalkan.')
                        ->modalSubmitActionLabel('Ya, hapus')
                        ->successNotificationTitle('Data kehadiran terpilih berhasil dihapus.'),
                ]),
            ])
            ->defaultSort('checked_in_at', 'desc');
    }
}

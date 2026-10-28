<?php

namespace App\Filament\Resources\Events\Tables;

use App\Models\Registration;
use App\Services\InvitationService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modelLabel('Peserta')
            ->pluralModelLabel('Peserta')
            ->columns([
                TextColumn::make('rowIndex')->label('No.')->rowIndex()->alignCenter(),
                TextColumn::make('name')->label('Peserta')->searchable()->sortable()->weight('semibold'),
                TextColumn::make('email')->searchable()->toggleable(),
                TextColumn::make('phone')->label('WhatsApp')->toggleable(),
                TextColumn::make('gender')->label('Jenis kelamin')->formatStateUsing(fn (?string $state): string => $state === 'male' ? 'Laki-laki' : 'Perempuan')->toggleable(),
                TextColumn::make('status')->label('Pendaftaran')->badge()->formatStateUsing(fn (?string $state): string => $state === Registration::STATUS_REGISTERED ? 'Terdaftar' : 'Dibatalkan'),
                TextColumn::make('email_invitation_status')
                    ->label('Pengiriman Email')
                    ->badge()
                    ->state(fn (Registration $record): ?string => match ($record->invitation_status) {
                        Registration::INVITATION_SENT => 'Sudah dikirim via email',
                        Registration::INVITATION_FAILED => 'Gagal dikirim via email',
                        default => null,
                    })
                    ->placeholder(''),
                TextColumn::make('whatsapp_invitation_sent_at')
                    ->label('Pengiriman WhatsApp')
                    ->badge()
                    ->state(fn (Registration $record): ?string => $record->whatsapp_invitation_sent_at ? 'Sudah dikirim via WhatsApp' : null)
                    ->placeholder(''),
                TextColumn::make('attendance.checked_in_at')->label('Check-in')->dateTime('d M Y H:i')->suffix(' WIB')->placeholder('Belum hadir')->sortable(),
                TextColumn::make('created_at')->label('Tanggal daftar')->dateTime('d M Y H:i')->suffix(' WIB')->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status pendaftaran')->options([
                    Registration::STATUS_REGISTERED => 'Terdaftar',
                    Registration::STATUS_CANCELLED => 'Dibatalkan',
                ]),
                SelectFilter::make('invitation_status')->options([
                    Registration::INVITATION_PENDING => 'Pending',
                    Registration::INVITATION_SENT => 'Terkirim',
                    Registration::INVITATION_FAILED => 'Gagal',
                ]),
                SelectFilter::make('gender')->label('Jenis kelamin')->options([
                    'male' => 'Laki-laki',
                    'female' => 'Perempuan',
                ]),
                TernaryFilter::make('checked_in')
                    ->label('Status check-in')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereHas('attendance'),
                        false: fn (Builder $query): Builder => $query->whereDoesntHave('attendance'),
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->label('Lihat detail'),
                    Action::make('viewTransferProof')
                        ->label('Lihat bukti transfer')
                        ->icon('heroicon-m-photo')
                        ->visible(fn (Registration $record): bool => filled($record->transfer_proof_path))
                        ->url(fn (Registration $record): string => route('exports.transfer-proof', $record))
                        ->openUrlInNewTab(),
                    Action::make('downloadQrCode')
                        ->label('Download QR Code')
                        ->icon('heroicon-m-qr-code')
                        ->url(fn (Registration $record): string => route('exports.registration-qr-code', $record)),
                    Action::make('sendWhatsAppInvitation')
                        ->label('Kirim WhatsApp')
                        ->icon('heroicon-m-chat-bubble-left-right')
                        ->url(fn (Registration $record): string => route('admin.invitations.whatsapp', $record))
                        ->openUrlInNewTab(),
                    Action::make('sendEmailInvitation')
                        ->label(fn (Registration $record): string => $record->invitation_status === Registration::INVITATION_SENT ? 'Kirim ulang Email' : 'Kirim Email')
                        ->icon('heroicon-m-envelope')
                        ->requiresConfirmation()
                        ->action(function (Registration $record, InvitationService $service): void {
                            try {
                                $service->send($record);
                                Notification::make()->title('Undangan email berhasil dikirim.')->success()->send();
                            } catch (\Throwable $exception) {
                                Notification::make()->title('Pengiriman email gagal.')->body($exception->getMessage())->danger()->send();
                            }
                        }),
                    EditAction::make(),
                    self::deleteAction(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang dipilih')
                        ->modalHeading('Hapus peserta terpilih')
                        ->modalDescription('Semua data peserta yang dipilih akan dihapus permanen beserta data check-in-nya. Tindakan ini tidak bisa dibatalkan.')
                        ->modalSubmitActionLabel('Ya, hapus')
                        ->successNotification(fn (): Notification => Notification::make()->title('Peserta terpilih berhasil dihapus.')->success()),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function deleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->label('Hapus peserta')
            ->requiresConfirmation()
            ->modalHeading('Hapus peserta')
            ->modalDescription(fn (Registration $record): string => "Yakin ingin menghapus data peserta {$record->name}? Data check-in peserta ini juga ikut terhapus. Tindakan ini tidak bisa dibatalkan.")
            ->modalSubmitActionLabel('Ya, hapus')
            ->successNotification(fn (Registration $record): Notification => Notification::make()->title("Peserta {$record->name} berhasil dihapus.")->success());
    }
}

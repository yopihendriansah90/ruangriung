<?php

namespace App\Mail;

use App\Models\Registration;
use App\Services\RegistrationQrCodeService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class EventInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $qrCodeCid;

    public string $logoCid;

    private string $qrCode;

    private string $logo;

    public function __construct(public Registration $registration)
    {
        $this->qrCode = app(RegistrationQrCodeService::class)->generate($registration);
        $this->qrCodeCid = 'qr-code-'.$registration->registration_code.'@ruangriung.local';
        $this->logo = file_get_contents(public_path('images/ruang-riung-icon.png'));
        $this->logoCid = 'ruang-riung-logo@ruangriung.local';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Undangan '.$this->registration->event->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.event-invitation',
            with: [
                'qrCodeCid' => $this->qrCodeCid,
                'logoCid' => $this->logoCid,
            ],
        );
    }

    public function build(): void
    {
        $this->withSymfonyMessage(function (Email $message): void {
            $message->addPart(
                (new DataPart($this->qrCode, 'qr-code.png', 'image/png'))
                    ->asInline()
                    ->setContentId($this->qrCodeCid),
            );
            $message->addPart(
                (new DataPart($this->logo, 'ruang-riung-logo.png', 'image/png'))
                    ->asInline()
                    ->setContentId($this->logoCid),
            );
        });
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn (): string => $this->qrCode,
                str($this->registration->name)->slug().'-'.str($this->registration->event->name)->slug().'.png',
            )->withMime('image/png'),
        ];
    }
}

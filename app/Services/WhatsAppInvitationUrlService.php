<?php

namespace App\Services;

use App\Models\Registration;

class WhatsAppInvitationUrlService
{
    public function make(Registration $registration): string
    {
        $registration->loadMissing('event');

        $phone = $this->normalizePhoneNumber($registration->phone);
        $message = $this->makeMessage($registration);

        return 'https://wa.me/'.$phone.'?text='.rawurlencode($message);
    }

    private function normalizePhoneNumber(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }

    private function makeMessage(Registration $registration): string
    {
        $event = $registration->event;

        $lines = [
            'Halo '.$registration->name.',',
            '',
            'Terima kasih sudah mendaftar event Ruang Riung.',
            '',
            'Event: '.$event->namePlain(),
            'Tanggal: '.$event->event_date->translatedFormat('d F Y'),
            'Waktu: '.$event->timeRange(),
            'Lokasi: '.$event->location_name,
        ];

        if ($event->location_address) {
            $lines[] = $event->location_address;
        }

        if ($event->google_maps_url) {
            $lines[] = '';
            $lines[] = 'Google Maps: '.$event->google_maps_url;
        }

        $lines[] = '';
        $lines[] = 'Download QR Code check-in: '.route('registrations.qr-code', $registration);
        $lines[] = 'Tunjukkan QR Code tersebut saat check-in di lokasi.';

        if ($event->whatsapp_group_url) {
            $lines[] = '';
            $lines[] = 'Yuk, gabung grup WhatsApp event '.$event->namePlain().' untuk mendapatkan informasi dan pembaruan terbaru dari panitia!';
            $lines[] = 'Grup WhatsApp event: '.$event->whatsapp_group_url;
        }

        return implode("\n", $lines);
    }
}

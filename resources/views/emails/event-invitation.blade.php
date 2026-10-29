<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan {{ $registration->event->namePlain() }}</title>
</head>
<body style="margin:0;background:#f4faff;color:#174d68;font-family:Arial,Helvetica,sans-serif;line-height:1.6">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4faff">
        <tr>
            <td style="padding:28px 16px">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;margin:0 auto">
                    <tr>
                        <td style="padding:0 8px 18px">
                            <table role="presentation" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding-right:12px;vertical-align:middle"><img src="cid:{{ $logoCid }}" alt="Logo Ruang Riung" width="52" height="52" style="display:block;width:52px;height:52px;object-fit:contain;border-radius:10px"></td>
                                    <td style="vertical-align:middle">
                                        <div style="font-size:12px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:#174d68">Ruang Riung</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#174d68;border-radius:18px 18px 0 0;padding:30px 32px;color:#fff">
                            <div style="font-size:12px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:#65c6f1">Undangan event komunitas</div>
                            <h1 style="margin:12px 0 6px;font-size:30px;line-height:1.2;color:#fff">Hai, {{ $registration->name }}!</h1>
                            <p style="margin:0;color:#d9effb;font-size:15px">Terima kasih sudah mendaftar. Kami menantikan kehadiranmu di event {{ $registration->event->namePlain() }}.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fff;border:1px solid #d5eaf5;border-top:0;border-radius:0 0 18px 18px;padding:30px 32px">
                            <div style="font-size:12px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#147eaf">Detail event</div>
                            <h2 style="margin:8px 0 22px;font-size:24px;line-height:1.3;color:#174d68">{{ $registration->event->namePlain() }}</h2>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;color:#3f7183">
                                <tr>
                                    <td width="92" style="padding:0 0 9px;font-weight:700;color:#174d68">Tanggal</td>
                                    <td style="padding:0 0 9px">{{ $registration->event->event_date->translatedFormat('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td width="92" style="padding:0 0 9px;font-weight:700;color:#174d68">Waktu</td>
                                    <td style="padding:0 0 9px">{{ $registration->event->timeRange() }}</td>
                                </tr>
                                <tr>
                                    <td width="92" style="padding:0;vertical-align:top;font-weight:700;color:#174d68">Lokasi</td>
                                    <td style="padding:0">{{ $registration->event->location_name }}<br>{{ $registration->event->location_address }}</td>
                                </tr>
                            </table>
                            @if ($registration->event->google_maps_url)
                                <p style="margin:18px 0 0"><a href="{{ $registration->event->google_maps_url }}" style="color:#147eaf;font-weight:700;text-decoration:none">Buka lokasi di Google Maps →</a></p>
                            @endif
                            @if ($registration->event->whatsapp_group_url)
                                <p style="margin:20px 0 0"><a href="{{ $registration->event->whatsapp_group_url }}" style="display:inline-block;background:#65c6f1;color:#104b68;text-decoration:none;border-radius:8px;padding:11px 16px;font-weight:700">Gabung grup WhatsApp event</a></p>
                            @endif
                            <div style="height:1px;background:#dcecf6;margin:26px 0"></div>
                            <div style="background:#eff9ff;border:1px solid #cce8f6;border-radius:14px;padding:22px;text-align:center">
                                <div style="font-size:14px;font-weight:700;color:#174d68">Tunjukkan QR Code ini saat check-in</div>
                                <p style="margin:5px 0 16px;font-size:13px;color:#5c8593">Simpan email ini dan tunjukkan kode kepada admin di lokasi.</p>
                                <img src="cid:{{ $qrCodeCid }}" alt="QR Code {{ $registration->registration_code }}" width="360" height="600" style="display:block;width:360px;height:auto;max-width:100%;margin:0 auto;background:#fff">
                                <div style="margin-top:14px;font-family:monospace;font-size:14px;letter-spacing:1px;color:#174d68">{{ $registration->registration_code }}</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 8px 0;text-align:center;font-size:12px;color:#6a909b">Sampai jumpa di event. Sehat mental, hidup lebih bermakna.</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

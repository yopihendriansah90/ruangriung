<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    use HasFactory;

    public const STATUS_REGISTERED = 'registered';

    public const STATUS_CANCELLED = 'cancelled';

    public const INVITATION_PENDING = 'pending';

    public const INVITATION_SENT = 'sent';

    public const INVITATION_FAILED = 'failed';

    protected $fillable = [
        'event_id',
        'registration_code',
        'name',
        'email',
        'phone',
        'instagram_username',
        'gender',
        'city',
        'organization',
        'notes',
        'referral_source',
        'transfer_proof_path',
        'data_consent_at',
        'status',
        'invitation_status',
        'invitation_sent_at',
        'invitation_send_count',
        'whatsapp_invitation_sent_at',
        'whatsapp_invitation_send_count',
        'last_invitation_error',
    ];

    protected function casts(): array
    {
        return [
            'data_consent_at' => 'datetime',
            'invitation_sent_at' => 'datetime',
            'invitation_send_count' => 'integer',
            'whatsapp_invitation_sent_at' => 'datetime',
            'whatsapp_invitation_send_count' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /** Username Instagram tanpa prefiks @ — data bisa diisi dengan atau tanpa "@". */
    public function instagramUsername(): string
    {
        return ltrim((string) $this->instagram_username, '@');
    }

    public function instagramUrl(): ?string
    {
        $username = $this->instagramUsername();

        return $username !== '' ? 'https://instagram.com/'.$username : null;
    }

    /**
     * Daftar platform sosmed → URL profil berdasarkan username di kolom ini.
     * Dipakai admin untuk memilih sosmed mana yang dibuka saat check-in/absensi.
     */
    public function sosmedLinks(): array
    {
        $username = $this->instagramUsername();

        if ($username === '') {
            return [];
        }

        return [
            'Instagram' => 'https://instagram.com/'.$username,
            'TikTok' => 'https://tiktok.com/@'.$username,
            'X (Twitter)' => 'https://x.com/'.$username,
            'Facebook' => 'https://facebook.com/'.$username,
            'YouTube' => 'https://youtube.com/@'.$username,
        ];
    }

    public function attendance(): HasOne
    {
        return $this->hasOne(Attendance::class);
    }
}

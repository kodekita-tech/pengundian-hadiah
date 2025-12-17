<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $table = 'event';

    // Status constants
    public const STATUS_ACTIVE = 'aktif';
    public const STATUS_INACTIVE = 'tidak_aktif';

    protected $fillable = [
        'nm_event',
        'opd_id',
        'status',
        'tgl_mulai',
        'tgl_selesai',
        'deskripsi',
        'qr_token',
        'passkey',
        'shortlink',
    ];

    protected $hidden = [
        'passkey',
    ];

    protected $casts = [
        'tgl_mulai' => 'datetime',
        'tgl_selesai' => 'datetime',
    ];

    /**
     * Get the OPD that owns the event.
     */
    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    /**
     * Scope a query to only include active events.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include inactive events.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    /**
     * Check if event is currently active (within date range).
     */
    public function isActive(): bool
    {
        if (!$this->tgl_mulai || !$this->tgl_selesai) {
            return false;
        }
        
        $now = Carbon::now('Asia/Jakarta');
        $tglMulai = Carbon::createFromFormat('Y-m-d H:i:s', $this->tgl_mulai->format('Y-m-d H:i:s'), 'Asia/Jakarta');
        $tglSelesai = Carbon::createFromFormat('Y-m-d H:i:s', $this->tgl_selesai->format('Y-m-d H:i:s'), 'Asia/Jakarta');

        return $tglMulai->lte($now) && $tglSelesai->gte($now);
    }

    /**
     * Check if event registration period has ended (date has passed).
     */
    public function hasRegistrationPeriodEnded(): bool
    {
        if (!$this->tgl_selesai) {
            return false;
        }

        $now = Carbon::now('Asia/Jakarta');
        $tglSelesai = Carbon::createFromFormat('Y-m-d H:i:s', $this->tgl_selesai->format('Y-m-d H:i:s'), 'Asia/Jakarta');
        return $tglSelesai->lt($now);
    }

    /**
     * Check if event registration period has started.
     */
    public function hasRegistrationPeriodStarted(): bool
    {
        if (!$this->tgl_mulai) {
            return false;
        }

        $now = Carbon::now('Asia/Jakarta');
        $tglMulai = Carbon::createFromFormat('Y-m-d H:i:s', $this->tgl_mulai->format('Y-m-d H:i:s'), 'Asia/Jakarta');
        return $tglMulai->lte($now);
    }

    /**
     * Check if event is currently within registration period.
     */
    public function isWithinRegistrationPeriod(): bool
    {
        return $this->isActive();
    }

    /**
     * Alias for isActive() for consistency.
     */
    public function isRegistrationOpen(): bool
    {
        return $this->isActive();
    }

    /**
     * Check if event registration is closed.
     */
    public function isRegistrationClosed(): bool
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    /**
     * Generate unique QR token for this event.
     */
    public function generateQrToken(): string
    {
        $this->qr_token = Str::random(32);
        $this->save();

        return $this->qr_token;
    }

    /**
     * Get status label in Indonesian.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'Aktif',
            self::STATUS_INACTIVE => 'Tidak Aktif',
            default => 'Unknown'
        };
    }

    /**
     * Get status badge class for UI.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'bg-success text-white',
            self::STATUS_INACTIVE => 'bg-secondary text-white',
            default => 'bg-secondary text-white'
        };
    }

    /**
     * Generate unique shortlink for this event.
     */
    public function generateShortlink(): string
    {
        do {
            $shortlink = Str::random(8);
        } while (self::where('shortlink', $shortlink)->exists());

        $this->shortlink = $shortlink;
        $this->save();

        return $this->shortlink;
    }

    /**
     * Check if event has passkey.
     */
    public function hasPasskey(): bool
    {
        return !empty($this->passkey);
    }

    /**
     * Verify passkey for this event.
     */
    public function verifyPasskey(?string $inputPasskey): bool
    {
        if (!$this->hasPasskey()) {
            return true; // No passkey set, always allow
        }

        if (empty($inputPasskey)) {
            return false;
        }

        return password_verify($inputPasskey, $this->passkey);
    }

    /**
     * Get the participants for the event.
     */
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Get the prizes for the event.
     */
    public function prizes()
    {
        return $this->hasMany(Prize::class);
    }

    /**
     * Get the winners for the event.
     */
    public function winners()
    {
        return $this->hasMany(Winner::class);
    }

    /**
     * Auto-update status based on date range.
     * Call this method whenever event is accessed to ensure status is up-to-date.
     * Uses Jakarta timezone for date comparison.
     * Status akan otomatis menjadi "aktif" jika dalam rentang tanggal, "tidak aktif" jika di luar rentang.
     */
    public function autoUpdateStatus(): void
    {
        if (!$this->tgl_mulai || !$this->tgl_selesai) {
            // Jika tanggal tidak ada, set status menjadi tidak aktif
            if ($this->status !== self::STATUS_INACTIVE) {
                $this->status = self::STATUS_INACTIVE;
                $this->saveQuietly();
            }
            return;
        }

        $now = Carbon::now('Asia/Jakarta');
        // Ambil nilai datetime dan asumsikan timezone Jakarta
        $tglMulai = Carbon::createFromFormat('Y-m-d H:i:s', $this->tgl_mulai->format('Y-m-d H:i:s'), 'Asia/Jakarta');
        $tglSelesai = Carbon::createFromFormat('Y-m-d H:i:s', $this->tgl_selesai->format('Y-m-d H:i:s'), 'Asia/Jakarta');

        // Cek apakah dalam rentang tanggal
        $isWithinDateRange = $tglMulai->lte($now) && $tglSelesai->gte($now);

        // Update status berdasarkan rentang tanggal
        if ($isWithinDateRange) {
            // Dalam rentang tanggal = aktif
            if ($this->status !== self::STATUS_ACTIVE) {
                $this->status = self::STATUS_ACTIVE;
                $this->saveQuietly();
            }
        } else {
            // Di luar rentang tanggal = tidak aktif
            if ($this->status !== self::STATUS_INACTIVE) {
                $this->status = self::STATUS_INACTIVE;
            $this->saveQuietly();
            }
        }
    }
}

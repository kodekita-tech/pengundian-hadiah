<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $table = 'event';

    // Status constants
    public const STATUS_DRAFT = 'draft';
    public const STATUS_REGISTRATION_OPEN = 'pendaftaran_dibuka';
    public const STATUS_REGISTRATION_CLOSED = 'pendaftaran_ditutup';
    public const STATUS_DRAWING = 'pengundian';
    public const STATUS_COMPLETED = 'selesai';

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
     * Scope a query to only include events open for registration.
     */
    public function scopeOpenForRegistration($query)
    {
        return $query->where('status', self::STATUS_REGISTRATION_OPEN);
    }

    /**
     * Scope a query to only include events with closed registration.
     */
    public function scopeRegistrationClosed($query)
    {
        return $query->where('status', self::STATUS_REGISTRATION_CLOSED);
    }

    /**
     * Check if event is open for registration.
     * Date range is checked FIRST, then status.
     */
    public function isOpenForRegistration(): bool
    {
        // First check date range - if date has passed, always return false
        if ($this->tgl_selesai < now()) {
            return false;
        }
        
        if ($this->tgl_mulai > now()) {
            return false;
        }
        
        // Then check status
        return $this->status === self::STATUS_REGISTRATION_OPEN || 
               $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if event registration period has ended (date has passed).
     */
    public function hasRegistrationPeriodEnded(): bool
    {
        return $this->tgl_selesai < now();
    }

    /**
     * Check if event registration period has started.
     */
    public function hasRegistrationPeriodStarted(): bool
    {
        return $this->tgl_mulai <= now();
    }

    /**
     * Check if event is currently within registration period.
     */
    public function isWithinRegistrationPeriod(): bool
    {
        return $this->hasRegistrationPeriodStarted() && !$this->hasRegistrationPeriodEnded();
    }

    /**
     * Alias for isOpenForRegistration() for consistency.
     */
    public function isRegistrationOpen(): bool
    {
        return $this->isOpenForRegistration();
    }

    /**
     * Check if event registration is closed.
     */
    public function isRegistrationClosed(): bool
    {
        return $this->status === self::STATUS_REGISTRATION_CLOSED;
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
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_REGISTRATION_OPEN => 'Pendaftaran Dibuka',
            self::STATUS_REGISTRATION_CLOSED => 'Pendaftaran Ditutup',
            self::STATUS_DRAWING => 'Pengundian',
            self::STATUS_COMPLETED => 'Selesai',
            default => 'Unknown'
        };
    }

    /**
     * Get status badge class for UI.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'bg-secondary text-white',
            self::STATUS_REGISTRATION_OPEN => 'bg-success text-white',
            self::STATUS_REGISTRATION_CLOSED => 'bg-warning text-dark',
            self::STATUS_DRAWING => 'bg-info text-white',
            self::STATUS_COMPLETED => 'bg-primary text-white',
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
     */
    public function autoUpdateStatus(): void
    {
        $now = now();

        // If registration period has ended but status is still "pendaftaran_dibuka"
        if ($this->status === self::STATUS_REGISTRATION_OPEN && $this->tgl_selesai < $now) {
            $this->status = self::STATUS_REGISTRATION_CLOSED;
            $this->saveQuietly(); // Save without triggering events
        }

        // Optional: Auto-open registration when date starts (uncomment if needed)
        /*
        if ($this->status === self::STATUS_DRAFT && 
            $this->tgl_mulai <= $now && 
            $this->tgl_selesai >= $now) {
            $this->status = self::STATUS_REGISTRATION_OPEN;
            $this->saveQuietly();
        }
        */
    }
}

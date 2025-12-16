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
     */
    public function isOpenForRegistration(): bool
    {
        return $this->status === self::STATUS_REGISTRATION_OPEN &&
               $this->tgl_mulai <= now() &&
               $this->tgl_selesai >= now();
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
}

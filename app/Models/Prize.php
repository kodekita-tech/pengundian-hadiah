<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'stock',
    ];

    protected $casts = [
        'stock' => 'integer',
    ];

    /**
     * Get the event that owns the prize.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Check if prize has stock limit (not null).
     */
    public function hasStockLimit(): bool
    {
        return !is_null($this->stock);
    }

    /**
     * Check if prize is available (unlimited or stock > 0).
     */
    public function isAvailable(): bool
    {
        if (!$this->hasStockLimit()) {
            return true;
        }
        return $this->stock > 0;
    }

    /**
     * Decrement stock if limited.
     */
    public function decrementStock(): void
    {
        if ($this->hasStockLimit()) {
            $this->decrement('stock');
        }
    }
}

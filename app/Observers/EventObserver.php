<?php

namespace App\Observers;

use App\Models\Event;

class EventObserver
{
    /**
     * Handle the Event "updating" event.
     * Auto-update status before saving based on date range.
     */
    public function updating(Event $event): void
    {
        // Auto-update status akan dilakukan oleh method autoUpdateStatus()
        // yang dipanggil di controller, jadi tidak perlu di sini
    }
}

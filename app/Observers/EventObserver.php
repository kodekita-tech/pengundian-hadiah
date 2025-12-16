<?php

namespace App\Observers;

use App\Models\Event;

class EventObserver
{
    /**
     * Handle the Event "updating" event.
     * Auto-update status before saving if date has passed.
     */
    public function updating(Event $event): void
    {
        // Auto-update status if registration period has ended
        if ($event->status === Event::STATUS_REGISTRATION_OPEN && $event->tgl_selesai < now()) {
            $event->status = Event::STATUS_REGISTRATION_CLOSED;
        }
    }
}

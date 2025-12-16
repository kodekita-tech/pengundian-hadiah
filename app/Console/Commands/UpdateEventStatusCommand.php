<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;

class UpdateEventStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-update event status based on date range';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $updated = 0;

        // Update events where registration period has ended but status is still "pendaftaran_dibuka"
        $eventsToClose = Event::where('status', Event::STATUS_REGISTRATION_OPEN)
            ->where('tgl_selesai', '<', $now)
            ->get();

        foreach ($eventsToClose as $event) {
            $event->status = Event::STATUS_REGISTRATION_CLOSED;
            $event->save();
            $updated++;
            $this->info("Event '{$event->nm_event}' status updated to 'pendaftaran_ditutup' (registration period ended)");
        }

        // Update events where registration period has started but status is still "draft"
        // Note: This is optional - you might want to keep draft status even if date has started
        // Uncomment if you want auto-open registration when date starts
        /*
        $eventsToOpen = Event::where('status', Event::STATUS_DRAFT)
            ->where('tgl_mulai', '<=', $now)
            ->where('tgl_selesai', '>=', $now)
            ->get();

        foreach ($eventsToOpen as $event) {
            $event->status = Event::STATUS_REGISTRATION_OPEN;
            $event->save();
            $updated++;
            $this->info("Event '{$event->nm_event}' status updated to 'pendaftaran_dibuka' (registration period started)");
        }
        */

        if ($updated === 0) {
            $this->info('No events need status update.');
        } else {
            $this->info("Updated {$updated} event(s).");
        }

        return Command::SUCCESS;
    }
}


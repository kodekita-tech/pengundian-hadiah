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
        $updated = 0;

        // Update semua event berdasarkan tanggal menggunakan method autoUpdateStatus()
        $events = Event::all();

        foreach ($events as $event) {
            $oldStatus = $event->status;
            $event->autoUpdateStatus();
            
            if ($oldStatus !== $event->status) {
                $updated++;
                $this->info("Event '{$event->nm_event}' status updated from '{$oldStatus}' to '{$event->status}'");
            }
        }

        if ($updated === 0) {
            $this->info('No events need status update.');
        } else {
            $this->info("Updated {$updated} event(s).");
        }

        return Command::SUCCESS;
    }
}


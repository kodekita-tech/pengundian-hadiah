<?php
use App\Models\Event;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$events = Event::whereNull('shortlink')->get();
foreach($events as $event) {
    echo "Updating event " . $event->id . ": " . $event->nm_event . "\n";
    $event->generateShortlink();
}
echo "Done.\n";

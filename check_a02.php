<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$loc = App\Models\Location::where('code', 'A-02')->first();
echo "Location: " . $loc->code . "\n";
echo "Capacity: " . $loc->capacity . "\n";
echo "Current fill: " . $loc->current_fill . "\n";
echo "Items:\n";
foreach ($loc->items as $item) {
    echo " - " . $item->name . ": " . $item->pivot->quantity . "\n";
}

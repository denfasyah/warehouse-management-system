<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Location;

foreach(Location::whereColumn('current_fill', '>', 'capacity')->get() as $loc) {
    echo $loc->code . ' : ' . $loc->current_fill . '/' . $loc->capacity . PHP_EOL;
}
echo "Done.\n";

<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach(['C-01', 'C-02', 'B-01', 'A-01', 'A-02', 'A-03'] as $c) {
    echo $c . ': ' . App\Models\Location::where('code', $c)->first()->storage_class . PHP_EOL;
}

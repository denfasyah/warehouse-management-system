<?php
// Update CBS thresholds untuk distribusi lebih realistis
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;

Setting::updateOrCreate(['key' => 'cbs_fast_threshold'], ['value' => '82']);
Setting::updateOrCreate(['key' => 'cbs_medium_threshold'], ['value' => '95']);

echo "Thresholds updated: Fast <= 82%, Medium <= 95%, Slow > 95%\n";

// Recalculate all
$counts = App\Services\CBSService::recalculateAll();
echo "Recalculated:\n";
foreach ($counts as $class => $count) {
    echo "  $class: $count items\n";
}

echo "\nDetail:\n";
App\Models\Item::all()->each(function($item) {
    echo "  {$item->name} -> {$item->storage_class} (score: {$item->frequency_score})\n";
});

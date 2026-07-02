<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tasks = App\Models\RelocationTask::all();
foreach($tasks as $t) {
    echo "Task {$t->id}: Item {$t->item->name} from {$t->fromLocation->code} to {$t->toLocation->code} (qty {$t->quantity}) - Status: {$t->status}\n";
}

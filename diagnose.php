<?php
// Diagnose semua lokasi vs pivot table
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Location;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

echo "=== CEK SEMUA LOKASI DAN ISI NYA ===\n\n";

$locations = Location::orderBy('code')->get();
foreach ($locations as $loc) {
    $pivotItems = DB::table('item_location')
        ->join('items', 'items.id', '=', 'item_location.item_id')
        ->where('location_id', $loc->id)
        ->select('items.name', 'item_location.quantity')
        ->get();
    
    $realTotal = $pivotItems->sum('quantity');
    $mismatch = $realTotal != $loc->current_fill ? ' *** MISMATCH ***' : '';
    
    echo "Lokasi {$loc->code}: current_fill={$loc->current_fill}, pivot_total={$realTotal}{$mismatch}\n";
    foreach ($pivotItems as $pi) {
        echo "    -> {$pi->name}: {$pi->quantity}\n";
    }
}

echo "\n=== CEK SEMUA ITEM DAN LOKASI NYA ===\n\n";
$items = Item::with('locations')->get();
foreach ($items as $item) {
    $locs = $item->locations->pluck('code')->join(', ');
    $pivotQtys = $item->locations->map(fn($l) => "{$l->code}:{$l->pivot->quantity}")->join(', ');
    echo "{$item->name} (stok:{$item->stock}): [{$locs}]\n  Pivot: {$pivotQtys}\n";
}

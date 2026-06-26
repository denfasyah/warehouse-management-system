<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Item;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

$item = Item::where('name', 'Engine Oil')->first();
$oldLocationIds = $item->locations()->pluck('locations.id')->toArray(); // B-03 and BLK-01

$newLocationIds = Location::whereIn('code', ['C-01', 'C-02', 'BLK-02'])->pluck('id')->toArray();

$selectedLocations = Location::whereIn('id', $newLocationIds)
    ->orderByRaw("zone = 'BLK' ASC")
    ->orderBy('zone')
    ->orderBy('rack')
    ->get();

$remainingStock = $item->stock;
$syncData = [];

foreach ($selectedLocations as $loc) {
    if ($loc->is_bulk_zone) {
        $syncData[$loc->id] = ['quantity' => $remainingStock];
        $remainingStock = 0;
    } else {
        $otherItemsQty = DB::table('item_location')
            ->where('location_id', $loc->id)
            ->where('item_id', '!=', $item->id)
            ->sum('quantity');
        
        $availableSlot = max(0, $loc->capacity - $otherItemsQty);
        $qtyForThisLoc = min($remainingStock, $availableSlot);
        
        $syncData[$loc->id] = ['quantity' => $qtyForThisLoc];
        $remainingStock -= $qtyForThisLoc;
    }
}

$item->locations()->sync($syncData);

$allAffectedIds = array_unique(array_merge($oldLocationIds, $newLocationIds));
Location::syncFill($allAffectedIds);

// Check B-03
$b3 = Location::where('code', 'B-03')->first();
echo "B-03 current fill: {$b3->current_fill}\n";

<?php
// Fix location overcapacity
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Location;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

echo "Starting capacity balancing...\n";

DB::transaction(function () {
    // Cari semua lokasi yang over capacity
    $overfilledLocations = Location::whereColumn('current_fill', '>', 'capacity')->get();

    foreach ($overfilledLocations as $loc) {
        echo "Location {$loc->code} is overfilled: {$loc->current_fill} / {$loc->capacity}\n";
        $excess = $loc->current_fill - $loc->capacity;

        // Ambil item yang ada di lokasi ini (prioritaskan item dengan qty paling banyak untuk dipindah)
        $itemsInLoc = DB::table('item_location')
            ->where('location_id', $loc->id)
            ->orderByDesc('quantity')
            ->get();

        foreach ($itemsInLoc as $pivot) {
            if ($excess <= 0) break;

            $item = Item::find($pivot->item_id);
            if (!$item) continue;

            // Berapa qty yang akan dipindah dari item ini?
            $qtyToMove = min($pivot->quantity, $excess);
            
            // Kurangi qty di lokasi saat ini
            $newQty = $pivot->quantity - $qtyToMove;
            if ($newQty > 0) {
                DB::table('item_location')
                    ->where('id', $pivot->id)
                    ->update(['quantity' => $newQty]);
            } else {
                DB::table('item_location')
                    ->where('id', $pivot->id)
                    ->delete();
            }

            // Cari lokasi tujuan untuk menaruh $qtyToMove
            // 1. Coba cari lokasi dengan storage_class yang SAMA dan ada kapasitas
            $targetLocs = Location::where('storage_class', $loc->storage_class)
                ->where('id', '!=', $loc->id)
                ->whereColumn('current_fill', '<', 'capacity')
                ->orderBy('current_fill', 'asc') // yang paling kosong
                ->get();
            
            // Jika kosong, cari lokasi BLK/general
            if ($targetLocs->isEmpty()) {
                $targetLocs = Location::where('storage_class', 'general')
                    ->orWhere('zone', 'BLK')
                    ->whereColumn('current_fill', '<', 'capacity')
                    ->orderBy('current_fill', 'asc')
                    ->get();
            }

            $remainingToMove = $qtyToMove;
            
            foreach ($targetLocs as $targetLoc) {
                if ($remainingToMove <= 0) break;

                $availableSpace = max(0, $targetLoc->capacity - $targetLoc->current_fill);
                if ($availableSpace <= 0) continue;

                $qtyToPut = min($remainingToMove, $availableSpace);

                // Tambah atau update pivot di lokasi target
                $existingPivot = DB::table('item_location')
                    ->where('item_id', $item->id)
                    ->where('location_id', $targetLoc->id)
                    ->first();

                if ($existingPivot) {
                    DB::table('item_location')
                        ->where('id', $existingPivot->id)
                        ->update(['quantity' => $existingPivot->quantity + $qtyToPut]);
                } else {
                    DB::table('item_location')->insert([
                        'item_id' => $item->id,
                        'location_id' => $targetLoc->id,
                        'quantity' => $qtyToPut,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Update fill virtual di dalam memori agar perhitungan availableSpace jalan terus
                $targetLoc->current_fill += $qtyToPut;
                $remainingToMove -= $qtyToPut;
            }

            // Jika masih ada sisa dan tidak muat di mana-mana (gudang penuh semua),
            // secara logika kita paksakan ke BLK-01 atau biarkan saja (tapi di sini kita paksakan ke BLK-01 meskipun over)
            if ($remainingToMove > 0) {
                $blk = Location::where('zone', 'BLK')->first();
                if ($blk) {
                    $existingPivot = DB::table('item_location')
                        ->where('item_id', $item->id)
                        ->where('location_id', $blk->id)
                        ->first();

                    if ($existingPivot) {
                        DB::table('item_location')
                            ->where('id', $existingPivot->id)
                            ->update(['quantity' => $existingPivot->quantity + $remainingToMove]);
                    } else {
                        DB::table('item_location')->insert([
                            'item_id' => $item->id,
                            'location_id' => $blk->id,
                            'quantity' => $remainingToMove,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            $excess -= $qtyToMove;
        }
    }

    // Rekalkulasi current_fill untuk SEMUA lokasi
    $allLocs = Location::all();
    foreach ($allLocs as $loc) {
        Location::syncFill($loc->id);
    }
});

echo "Balancing complete.\n";

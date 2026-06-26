<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $cat = Category::first();

        // Ambil semua lokasi, index by code
        $locs = Location::all()->keyBy('code');

        $items = [
            // Format: [nama, stok, [kode_lokasi => qty, ...]]
            [
                'name'  => 'Transmisi Oil',
                'stock' => 3241,
                // A-01 penuh (2500), sisanya (741) ke BLK-01
                'locs'  => ['A-01' => 2500, 'BLK-01' => 741],
            ],
            [
                'name'  => 'Gear Oil',
                'stock' => 624,
                // Muat di A-02 saja (< 2500)
                'locs'  => ['A-02' => 624],
            ],
            [
                'name'  => 'Brake Fluid',
                'stock' => 1044,
                // Muat di A-03 saja
                'locs'  => ['A-03' => 1044],
            ],
            [
                'name'  => 'Brake Disc',
                'stock' => 967,
                // Muat di A-04 saja
                'locs'  => ['A-04' => 967],
            ],
            [
                'name'  => 'Brake Pad',
                'stock' => 4655,
                // B-01 penuh (2500), sisanya (2155) ke BLK-01
                'locs'  => ['B-01' => 2500, 'BLK-01' => 2155],
            ],
            [
                'name'  => 'Battery',
                'stock' => 4821,
                // B-02 penuh (2500), sisanya (2321) ke BLK-01
                'locs'  => ['B-02' => 2500, 'BLK-01' => 2321],
            ],
            [
                'name'  => 'Engine Oil',
                'stock' => 56293,
                // C-01 (2500) + C-02 (2500) picking, sisanya (51293) ke BLK-02
                'locs'  => ['C-01' => 2500, 'C-02' => 2500, 'BLK-02' => 51293],
            ],
        ];

        foreach ($items as $itemData) {
            $item = Item::create([
                'category_id'   => $cat->id,
                'name'          => $itemData['name'],
                'slug'          => Str::slug($itemData['name']),
                'sku'           => Item::generateSku($cat->code),
                'barcode'       => Item::generateBarcode(),
                'unit'          => 'pcs',
                'stock'         => $itemData['stock'],
                'min_stock'     => 10,
                'storage_class' => 'unclassified',
                'description'   => 'Deskripsi untuk ' . $itemData['name'],
            ]);

            // Attach ke lokasi dengan quantity yang sudah ditentukan
            $attachData = [];
            foreach ($itemData['locs'] as $code => $qty) {
                $loc = $locs->get($code);
                if ($loc) {
                    $attachData[$loc->id] = ['quantity' => $qty];
                }
            }

            if (!empty($attachData)) {
                $item->locations()->attach($attachData);
            }
        }

        // --- SINKRONISASI CURRENT_FILL ---
        // Update current_fill tiap lokasi berdasarkan jumlah nyata di pivot
        Location::all()->each(function ($loc) {
            $totalQty = DB::table('item_location')
                ->where('location_id', $loc->id)
                ->sum('quantity');
            $loc->update(['current_fill' => $totalQty]);
        });
    }
}

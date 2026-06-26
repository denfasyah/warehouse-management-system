<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        // Picking zone locations (A-01 s/d C-04) — kapasitas 100 per rak
        $pickingLocations = Location::where('zone', '!=', 'BLK')->orderBy('code')->get();
        $bulkLoc = Location::where('code', 'BLK-01')->first();
        
        $items = [
            // [nama, stok, picking_zone_code]
            ['name' => 'Transmisi Oil', 'stock' => 3241,  'picking' => 'A-01'],
            ['name' => 'Gear Oil',      'stock' => 624,   'picking' => 'A-02'],
            ['name' => 'Brake Fluid',   'stock' => 1044,  'picking' => 'A-03'],
            ['name' => 'Brake Disc',    'stock' => 967,   'picking' => 'A-04'],
            ['name' => 'Brake Pad',     'stock' => 4655,  'picking' => 'B-01'],
            ['name' => 'Battery',       'stock' => 4821,  'picking' => 'B-02'],
            ['name' => 'Engine Oil',    'stock' => 56293, 'picking' => 'B-03'],
        ];

        $pickingCapacity = 100; // Kapasitas rak picking zone per rak

        foreach ($items as $itemData) {
            $cat = $categories->first();
            
            $item = Item::create([
                'category_id' => $cat->id,
                'name'        => $itemData['name'],
                'slug'        => Str::slug($itemData['name']),
                'sku'         => Item::generateSku($cat->code),
                'barcode'     => Item::generateBarcode(),
                'unit'        => 'pcs',
                'stock'       => $itemData['stock'],
                'min_stock'   => 10,
                'storage_class' => 'unclassified',
                'description' => 'Deskripsi untuk ' . $itemData['name'],
            ]);

            $pickingLoc = $pickingLocations->where('code', $itemData['picking'])->first();
            $totalStock = $itemData['stock'];

            if ($totalStock <= $pickingCapacity) {
                // Stok muat di picking zone saja
                if ($pickingLoc) {
                    $item->locations()->attach([$pickingLoc->id => ['quantity' => $totalStock]]);
                }
            } else {
                // Stok melebihi kapasitas picking zone — sebagian taruh di Bulk (overflow)
                $pickingQty = $pickingCapacity;
                $bulkQty    = $totalStock - $pickingCapacity;

                $attachData = [];
                if ($pickingLoc) {
                    $attachData[$pickingLoc->id] = ['quantity' => $pickingQty];
                }
                if ($bulkLoc) {
                    $attachData[$bulkLoc->id] = ['quantity' => $bulkQty];
                }
                $item->locations()->attach($attachData);
            }
        }
    }
}

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
        $locations = Location::orderBy('code')->get();
        
        $items = [
            ['name' => 'Transmisi Oil', 'stock' => 3241],
            ['name' => 'Gear Oil', 'stock' => 624],
            ['name' => 'Brake Fluid', 'stock' => 1044],
            ['name' => 'Brake Disc', 'stock' => 967],
            ['name' => 'Brake Pad', 'stock' => 4655],
            ['name' => 'Battery', 'stock' => 4821],
            ['name' => 'Engine Oil', 'stock' => 56293],
        ];

        foreach ($items as $index => $itemData) {
            $cat = $categories->first(); // Since we only have 1 category now
            $loc = $locations[$index] ?? $locations->first(); // Assign sequentially (A-01, A-02, etc)
            
            $item = Item::create([
                'category_id' => $cat->id,
                'name' => $itemData['name'],
                'slug' => Str::slug($itemData['name']),
                'sku' => Item::generateSku($cat->code),
                'barcode' => Item::generateBarcode(),
                'unit' => 'pcs',
                'stock' => $itemData['stock'],
                'min_stock' => rand(5, 15),
                'storage_class' => 'unclassified', // Diisi oleh CBS engine nanti
                'description' => 'Deskripsi untuk ' . $itemData['name'],
            ]);

            // Assign locations
            if ($itemData['name'] === 'Engine Oil') {
                $loc1 = $locations->where('code', 'B-03')->first();
                $loc2 = $locations->where('code', 'B-04')->first();
                
                $attachData = [];
                if ($loc1) $attachData[$loc1->id] = ['quantity' => 30000];
                if ($loc2) $attachData[$loc2->id] = ['quantity' => 26293];
                
                $item->locations()->attach($attachData);
            } else {
                $item->locations()->attach([$loc->id => ['quantity' => $itemData['stock']]]);
            }
        }
    }
}

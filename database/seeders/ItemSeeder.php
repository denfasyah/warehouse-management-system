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
        $locations = Location::all();
        
        $items = [
            ['name' => 'Transmisi Oil', 'stock' => 3241],
            ['name' => 'Gear Oil', 'stock' => 624],
            ['name' => 'Brake Fluid', 'stock' => 1044],
            ['name' => 'Brake Disc', 'stock' => 967],
            ['name' => 'Brake Pad', 'stock' => 4655],
            ['name' => 'Battery', 'stock' => 4821],
            ['name' => 'Engine Oil', 'stock' => 56293],
        ];

        foreach ($items as $itemData) {
            $cat = $categories->random();
            $loc = $locations->random();
            
            Item::create([
                'category_id' => $cat->id,
                'location_id' => $loc->id,
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
        }
    }
}

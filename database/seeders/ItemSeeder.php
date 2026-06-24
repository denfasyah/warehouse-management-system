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
        
        $itemNames = [
            'Kabel NYY 4x10mm',
            'Box Panel Listrik 30x40',
            'Isolasi Nitto Black',
            'Lampu LED 12W Philips',
            'Bearing SKF 6204',
            'Baut L M8x20',
            'Oli Pertamina Meditran',
            'WD-40 400ml',
            'Sarung Tangan Karet',
            'Kacamata Safety',
            'Helm Proyek Kuning',
            'Masker 3M N95',
            'Lem Alteco',
            'Cat Semprot Pilox Hitam',
            'Thinner Impala',
            'Obeng Plus Minus Set',
            'Tang Potong',
            'Kunci Pas Ring Set',
            'Multitester Digital',
            'Isolasi Pipa AC'
        ];

        foreach ($itemNames as $name) {
            $cat = $categories->random();
            $loc = $locations->random();
            
            Item::create([
                'category_id' => $cat->id,
                'location_id' => $loc->id,
                'name' => $name,
                'slug' => Str::slug($name),
                'sku' => Item::generateSku($cat->code),
                'barcode' => Item::generateBarcode(),
                'unit' => 'pcs',
                'stock' => rand(10, 100),
                'min_stock' => rand(5, 15),
                'storage_class' => 'unclassified', // Diisi oleh CBS engine nanti
                'description' => 'Deskripsi untuk ' . $name,
            ]);
        }
    }
}

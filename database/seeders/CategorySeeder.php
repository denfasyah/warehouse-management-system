<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik', 'code' => 'ELK', 'description' => 'Barang elektronik dan komponen'],
            ['name' => 'Mekanikal', 'code' => 'MKN', 'description' => 'Suku cadang mekanik'],
            ['name' => 'Kimia', 'code' => 'KIM', 'description' => 'Bahan kimia dan cairan'],
            ['name' => 'Peralatan', 'code' => 'PRL', 'description' => 'Alat kerja dan perkakas'],
            ['name' => 'Umum', 'code' => 'UMM', 'description' => 'Barang umum lainnya'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}

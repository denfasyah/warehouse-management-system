<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Sparepart', 'code' => 'SPR', 'description' => 'Suku cadang kendaraan'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}

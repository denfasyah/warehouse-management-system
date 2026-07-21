<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('settings')->insert([
            [
                'key' => 'cbs_fast_threshold',
                'value' => '82',
                'label' => 'Batas Persentase Kumulatif Fast Moving (Kelas A)',
                'description' => 'Persentase kumulatif pergerakan maksimal (s.d nilai ini) untuk kategori Fast Moving (Kelas A).',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'cbs_medium_threshold',
                'value' => '95',
                'label' => 'Batas Persentase Kumulatif Medium Moving (Kelas B)',
                'description' => 'Persentase kumulatif pergerakan maksimal (s.d nilai ini) untuk kategori Medium Moving (Kelas B). Di atas ini diklasifikasikan sebagai Slow Moving (Kelas C).',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}

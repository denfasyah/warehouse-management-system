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
                'value' => '50',
                'label' => 'Batas Frekuensi Fast Moving',
                'description' => 'Barang dengan frekuensi pengeluaran >= nilai ini dalam 30 hari masuk kelas Fast.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'cbs_medium_threshold',
                'value' => '10',
                'label' => 'Batas Frekuensi Medium Moving',
                'description' => 'Barang dengan frekuensi pengeluaran >= nilai ini dan < batas Fast masuk kelas Medium.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // Zona Picking (A, B, C) — kapasitas terbatas, tempat ambil sehari-hari
        $zones = [
            'A' => 'fast',
            'B' => 'medium',
            'C' => 'slow',
        ];

        foreach ($zones as $zone => $class) {
            for ($pos = 1; $pos <= 4; $pos++) {
                $posStr = str_pad($pos, 2, '0', STR_PAD_LEFT);
                
                Location::create([
                    'zone' => $zone,
                    'rack' => $posStr,
                    'bin' => '',
                    'code' => Location::generateCode($zone, $posStr, ''),
                    'storage_class' => $class,
                    'capacity' => 2500,
                    'current_fill' => 0,
                    'is_active' => true,
                ]);
            }
        }

        // Zona BLK (Bulk Storage / Timbunan) — kapasitas sangat besar, untuk stok overflow
        for ($pos = 1; $pos <= 2; $pos++) {
            $posStr = str_pad($pos, 2, '0', STR_PAD_LEFT);

            Location::create([
                'zone' => 'BLK',
                'rack' => $posStr,
                'bin' => '',
                'code' => 'BLK-' . $posStr,
                'storage_class' => 'general',
                'capacity' => 999999, // Kapasitas sangat besar untuk bulk storage
                'current_fill' => 0,
                'description' => 'Bulk Storage / Area Timbunan Stok Berlebih',
                'is_active' => true,
            ]);
        }
    }
}

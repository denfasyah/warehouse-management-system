<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
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
                    'capacity' => 100,
                    'current_fill' => rand(0, 80),
                    'is_active' => true,
                ]);
            }
        }
    }
}

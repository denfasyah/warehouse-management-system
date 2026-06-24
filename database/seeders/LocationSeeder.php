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
            'D' => 'general'
        ];

        foreach ($zones as $zone => $class) {
            for ($rack = 1; $rack <= 5; $rack++) {
                for ($bin = 1; $bin <= 2; $bin++) {
                    $rackStr = str_pad($rack, 2, '0', STR_PAD_LEFT);
                    $binStr = str_pad($bin, 2, '0', STR_PAD_LEFT);
                    
                    Location::create([
                        'zone' => $zone,
                        'rack' => $rackStr,
                        'bin' => $binStr,
                        'code' => Location::generateCode($zone, $rack, $bin),
                        'storage_class' => $class,
                        'capacity' => 100,
                        'current_fill' => rand(0, 80),
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}

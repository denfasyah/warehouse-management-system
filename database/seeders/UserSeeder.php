<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'admin@wms.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        // Petugas 1
        User::create([
            'name' => 'Agus Pratama',
            'email' => 'agus@wms.com',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'phone' => '081234567891',
            'warehouse_sector' => 'Sektor A',
            'is_active' => true,
        ]);

        // Petugas 2
        User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@wms.com',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'phone' => '081234567892',
            'warehouse_sector' => 'Sektor B',
            'is_active' => true,
        ]);
    }
}

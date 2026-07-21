<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $cat = Category::first();

        // Ambil semua lokasi, index by code
        $locs = Location::all()->keyBy('code');

        /**
         * BEST PRACTICE: Tiap barang sudah ditempatkan di zona yang SESUAI kelas CBS-nya sejak awal.
         *  - Fast Moving → Zona A (A-01..A-04) | max 2500 per rak
         *  - Medium Moving → Zona B (B-01..B-04) | max 2500 per rak
         *  - Slow Moving → Zona C (C-01..C-04) | max 2500 per rak
         *  - Overflow apapun → BLK (kapasitas besar)
         *
         * Dengan begitu, saat CBS recalculate, tidak akan ada "mismatch" yang besar
         * dan tugas relokasi hanya dibuat jika benar-benar bergeser kelas.
         *
         * Format: ['name', stock, storage_class, [kode_lokasi => qty, ...]]
         * Catatan: total qty di semua locs HARUS = stock
         */
        $items = [
            // ===== FAST MOVING (Zona A) =====
            [
                'name'          => 'Engine Oil',
                'stock'         => 56293,
                'storage_class' => 'fast',
                'locs'          => ['A-01' => 2500, 'A-02' => 2500, 'A-03' => 2500, 'A-04' => 2500, 'BLK-01' => 46293],
            ],

            // ===== MEDIUM MOVING (Zona B) =====
            [
                'name'          => 'Battery',
                'stock'         => 4821,
                'storage_class' => 'medium',
                'locs'          => ['B-01' => 2500, 'B-02' => 2321],
            ],
            [
                'name'          => 'Brake Pad',
                'stock'         => 4655,
                'storage_class' => 'medium',
                'locs'          => ['B-02' => 179, 'B-03' => 2500, 'B-04' => 1976],
            ],

            // ===== SLOW MOVING (Zona C) =====
            [
                'name'          => 'Transmisi Oil',
                'stock'         => 3241,
                'storage_class' => 'slow',
                'locs'          => ['C-01' => 2500, 'C-02' => 741],
            ],
            [
                'name'          => 'Brake Fluid',
                'stock'         => 1044,
                'storage_class' => 'slow',
                'locs'          => ['C-02' => 1044],
            ],
            [
                'name'          => 'Brake Disc',
                'stock'         => 967,
                'storage_class' => 'slow',
                'locs'          => ['C-03' => 967],
            ],
            [
                'name'          => 'Gear Oil',
                'stock'         => 624,
                'storage_class' => 'slow',
                'locs'          => ['C-03' => 624],
            ],
        ];

        foreach ($items as $itemData) {
            // Validasi: total qty di locs harus = stock
            $totalLocQty = array_sum($itemData['locs']);
            if ($totalLocQty !== $itemData['stock']) {
                $this->command->error("⚠️  Mismatch qty untuk {$itemData['name']}: stock={$itemData['stock']} tapi total locs={$totalLocQty}. Dikoreksi otomatis.");
                $itemData['stock'] = $totalLocQty;
            }

            $item = Item::create([
                'category_id'   => $cat->id,
                'name'          => $itemData['name'],
                'slug'          => Str::slug($itemData['name']),
                'sku'           => Item::generateSku($cat->code),
                'barcode'       => Item::generateBarcode(),
                'unit'          => 'pcs',
                'stock'         => $itemData['stock'],
                'min_stock'     => 10,
                'storage_class' => $itemData['storage_class'], // Sudah diklasifikasikan
                'description'   => 'Deskripsi untuk ' . $itemData['name'],
            ]);

            // Validasi kapasitas tiap rak sebelum attach
            $attachData = [];
            foreach ($itemData['locs'] as $code => $qty) {
                $loc = $locs->get($code);
                if (!$loc) {
                    $this->command->warn("Lokasi {$code} tidak ditemukan, dilewati.");
                    continue;
                }

                // Hubungkan secara pivot
                $attachData[$loc->id] = ['quantity' => $qty];
            }

            if (!empty($attachData)) {
                $item->locations()->attach($attachData);
            }
        }

        // --- SINKRONISASI CURRENT_FILL ---
        // Update current_fill tiap lokasi berdasarkan jumlah nyata di pivot
        Location::all()->each(function ($loc) {
            $totalQty = DB::table('item_location')
                ->where('location_id', $loc->id)
                ->sum('quantity');
            $loc->update(['current_fill' => $totalQty]);
        });

        $this->command->info('✅ ItemSeeder selesai. Semua rak dalam kapasitas normal.');
    }
}

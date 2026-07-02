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
                'stock'         => 6500,
                'storage_class' => 'fast',
                // Picking: A-01 (2500) + A-02 (2500) | Overflow: BLK-01 (1500)
                'locs'          => ['A-01' => 2500, 'A-02' => 2500, 'BLK-01' => 1500],
            ],
            [
                'name'          => 'Brake Fluid',
                'stock'         => 2200,
                'storage_class' => 'fast',
                // Muat di A-03 saja (< 2500)
                'locs'          => ['A-03' => 2200],
            ],
            [
                'name'          => 'Brake Pad',
                'stock'         => 4800,
                'storage_class' => 'fast',
                // Picking: A-04 (2500) | Overflow: BLK-01 (2300)
                'locs'          => ['A-04' => 2500, 'BLK-01' => 2300],
            ],

            // ===== MEDIUM MOVING (Zona B) =====
            [
                'name'          => 'Brake Disc',
                'stock'         => 2100,
                'storage_class' => 'medium',
                // Muat di B-01 saja (< 2500)
                'locs'          => ['B-01' => 2100],
            ],
            [
                'name'          => 'Gear Oil',
                'stock'         => 1800,
                'storage_class' => 'medium',
                // Muat di B-02 saja (< 2500)
                'locs'          => ['B-02' => 1800],
            ],

            // ===== SLOW MOVING (Zona C) =====
            [
                'name'          => 'Transmisi Oil',
                'stock'         => 1400,
                'storage_class' => 'slow',
                // Muat di C-01 saja (< 2500)
                'locs'          => ['C-01' => 1400],
            ],
            [
                'name'          => 'Battery',
                'stock'         => 900,
                'storage_class' => 'slow',
                // Muat di C-02 saja (< 2500)
                'locs'          => ['C-02' => 900],
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

                // Pastikan qty tidak melebihi kapasitas rak (kecuali BLK)
                $isBulk     = $loc->zone === 'BLK' || $loc->storage_class === 'general';
                $locFillSoFar = DB::table('item_location')->where('location_id', $loc->id)->sum('quantity');
                $maxQty     = $isBulk ? $qty : min($qty, $loc->capacity - $locFillSoFar);

                if ($maxQty <= 0) {
                    $this->command->warn("Rak {$code} sudah penuh saat seeding {$item->name}. Dilewati.");
                    continue;
                }

                $attachData[$loc->id] = ['quantity' => $maxQty];
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

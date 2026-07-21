<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

class ThesisItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada kategori Sparepart
        $category = Category::firstOrCreate(
            ['name' => 'Sparepart Otomotif'],
            ['code' => 'AUTO', 'description' => 'Suku Cadang Otomotif', 'is_active' => true]
        );

        // Ambil lokasi BLK-01 (Bulk) yang dibuat oleh LocationSeeder
        $bulkLocation = Location::where('zone', 'BLK')->where('rack', '01')->first();

        if (!$bulkLocation) {
            echo "ERROR: Lokasi BLK-01 tidak ditemukan. Pastikan LocationSeeder berjalan lebih dulu.\n";
            return;
        }

        // Data barang sesuai tabel Excel Skripsi
        $items = [
            ['name' => 'Engine Oil',    'stock' => 56293],
            ['name' => 'Battery',       'stock' => 4821],
            ['name' => 'Brake Pad',     'stock' => 4655],
            ['name' => 'Transmisi Oil', 'stock' => 3241],
            ['name' => 'Brake Fluid',   'stock' => 1044],
            ['name' => 'Brake Disc',    'stock' => 967],
            ['name' => 'Gear Oil',      'stock' => 624],
        ];

        DB::beginTransaction();
        try {
            foreach ($items as $data) {
                // Buat Barang (skip jika sudah ada)
                $item = Item::firstOrCreate(
                    ['name' => $data['name']],
                    [
                        'category_id'   => $category->id,
                        'stock'         => $data['stock'],
                        'unit'          => 'pcs',
                        'min_stock'     => 10,
                        'slug'          => Item::generateSlug($data['name']),
                        'sku'           => Item::generateSku($category->code),
                        'barcode'       => Item::generateBarcode(),
                        'storage_class' => 'unclassified',
                    ]
                );

                // Taruh seluruh stok barang di lokasi Bulk (BLK-01)
                $item->locations()->attach($bulkLocation->id, [
                    'quantity' => $data['stock'],
                ]);
            }

            // Update current_fill lokasi Bulk sesuai total stok yang masuk
            $totalFill = $bulkLocation->items()->sum('item_location.quantity');
            $bulkLocation->update(['current_fill' => $totalFill]);

            DB::commit();
            echo "Berhasil: " . count($items) . " barang skripsi dimasukkan ke zona BLK-01.\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}

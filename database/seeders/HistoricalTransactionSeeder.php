<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Location;
use App\Models\OutgoingGood;
use App\Models\IncomingGood;
use App\Models\User;
use App\Services\CBSService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HistoricalTransactionSeeder extends Seeder
{
    /**
     * Buat data transaksi historis 60 hari ke belakang.
     * 
     * PENTING: Seeder ini hanya membuat RIWAYAT (history) transaksi.
     * Seeder ini TIDAK mengubah pivot item_location maupun stock item —
     * itu sudah dihandle oleh ItemSeeder sebelumnya.
     *
     * Pola pergerakan yang disimulasikan:
     *  - Engine Oil   → keluar sangat sering → akan menjadi FAST ✓
     *  - Brake Fluid  → keluar sering         → akan menjadi FAST ✓
     *  - Brake Pad    → keluar sering          → akan menjadi FAST ✓
     *  - Brake Disc   → keluar sedang          → akan menjadi MEDIUM ✓
     *  - Gear Oil     → keluar sedang          → akan menjadi MEDIUM ✓
     *  - Transmisi Oil → keluar jarang         → akan menjadi SLOW ✓
     *  - Battery      → keluar jarang          → akan menjadi SLOW ✓
     */
    public function run(): void
    {
        $admin   = User::where('role', 'admin')->first();
        $petugas = User::where('role', 'petugas')->first();

        if (!$admin || !$petugas) {
            $this->command->warn('Admin atau Petugas tidak ditemukan. Seeder dilewati.');
            return;
        }

        $items = Item::all()->keyBy('name');

        // Definisi target pergerakan 30 hari terakhir sesuai skripsi
        $targets = [
            'Engine Oil'     => 42150,
            'Battery'        => 3250,
            'Brake Pad'      => 2800,
            'Transmisi Oil'  => 1950,
            'Brake Fluid'    => 650,
            'Brake Disc'     => 420,
            'Gear Oil'       => 310,
        ];

        $this->command->info('Membuat data historis transaksi 30 hari...');

        DB::transaction(function () use ($items, $targets, $admin, $petugas) {
            foreach ($targets as $itemName => $targetQty) {
                $item = $items->get($itemName);
                if (!$item) {
                    continue;
                }

                // Cari lokasi picking yang terkait dengan item (zona non-BLK)
                $itemLocs  = DB::table('item_location')->where('item_id', $item->id)->pluck('location_id');
                $pickingLoc = Location::whereIn('id', $itemLocs)
                    ->where('zone', '!=', 'BLK')
                    ->first();

                $locId = $pickingLoc?->id ?? Location::whereIn('id', $itemLocs)->first()?->id;

                if (!$locId) {
                    continue;
                }

                // Bagi targetQty menjadi 5 bagian agar sum-nya persis
                $part = (int) floor($targetQty / 5);
                $parts = [$part, $part, $part, $part, $targetQty - ($part * 4)];

                $days = [2, 7, 12, 17, 24];

                foreach ($parts as $index => $qty) {
                    $date = Carbon::now()->subDays($days[$index]);

                    // --- Catat Barang Keluar (Outgoing) ---
                    $processedAt = $date->copy()->setTime(14, rand(0, 59));
                    OutgoingGood::create([
                        'item_id'      => $item->id,
                        'requested_by' => $petugas->id,
                        'approved_by'  => $admin->id,
                        'location_id'  => $locId,
                        'quantity'     => $qty,
                        'status'       => 'approved',
                        'destination'  => 'Distribusi rutin - data historis skripsi',
                        'note'         => 'Data historis 30 hari',
                        'requested_at' => $date->copy()->setTime(9, rand(0, 59)),
                        'processed_at' => $processedAt,
                        'created_at'   => $date->copy()->setTime(9, rand(0, 59)),
                        'updated_at'   => $processedAt,
                    ]);

                    // --- Catat Barang Masuk (Incoming) ---
                    IncomingGood::create([
                        'item_id'    => $item->id,
                        'user_id'    => $petugas->id,
                        'location_id'=> $locId,
                        'quantity'   => $qty,
                        'note'       => 'Penerimaan rutin - data historis skripsi',
                        'created_at' => $date->copy()->setTime(8, rand(0, 59)),
                        'updated_at' => $date->copy()->setTime(8, rand(0, 59)),
                    ]);
                }
            }
        });


        $this->command->newLine();
        $this->command->info('Data historis berhasil dibuat. Menjalankan kalkulasi CBS...');

        // Jalankan kalkulasi CBS berdasarkan data historis yang baru dibuat
        $counts = CBSService::recalculateAll();

        $this->command->info('CBS selesai dikalkulasi:');
        $this->command->table(['Kelas', 'Jumlah Item'], [
            ['Fast Moving',   $counts['fast']],
            ['Medium Moving', $counts['medium']],
            ['Slow Moving',   $counts['slow']],
        ]);

        // Verifikasi kapasitas rak — tidak boleh ada yang melebihi kapasitas
        $this->command->info('Verifikasi kapasitas rak...');
        $overflow = Location::all()->filter(fn($loc) => $loc->current_fill > $loc->capacity);
        if ($overflow->count() > 0) {
            $this->command->error('⚠️  Ada rak yang melebihi kapasitas:');
            foreach ($overflow as $loc) {
                $this->command->line("  - {$loc->code}: {$loc->current_fill} / {$loc->capacity}");
            }
        } else {
            $this->command->info('✅ Semua rak dalam batas kapasitas normal.');
        }
    }
}

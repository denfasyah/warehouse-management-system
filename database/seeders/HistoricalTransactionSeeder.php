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

        // Definisi frekuensi keluar (untuk membentuk skor CBS)
        // qty: jumlah per transaksi keluar | days_per_week: berapa hari/minggu ada transaksi
        $patterns = [
            // --- FAST: qty besar, hampir setiap hari ---
            'Engine Oil'     => ['min' => 150, 'max' => 400, 'days_per_week' => 7],
            'Brake Fluid'    => ['min' => 80,  'max' => 200, 'days_per_week' => 5],
            'Brake Pad'      => ['min' => 60,  'max' => 150, 'days_per_week' => 5],
            // --- MEDIUM: qty sedang, beberapa hari per minggu ---
            'Brake Disc'     => ['min' => 20,  'max' => 60,  'days_per_week' => 3],
            'Gear Oil'       => ['min' => 15,  'max' => 50,  'days_per_week' => 3],
            // --- SLOW: qty kecil, sangat jarang ---
            'Transmisi Oil'  => ['min' => 2,   'max' => 8,   'days_per_week' => 1],
            'Battery'        => ['min' => 3,   'max' => 10,  'days_per_week' => 1],
        ];

        $this->command->info('Membuat data historis transaksi 60 hari...');
        $bar = $this->command->getOutput()->createProgressBar(60 * count($patterns));

        // Semua insert dilakukan dalam 1 transaksi DB untuk performa
        DB::transaction(function () use ($items, $patterns, $admin, $petugas, $bar) {
            for ($daysAgo = 60; $daysAgo >= 0; $daysAgo--) {
                $date      = Carbon::now()->subDays($daysAgo);
                $dayOfWeek = (int) $date->dayOfWeek; // 0=Sun, 6=Sat

                foreach ($patterns as $itemName => $pattern) {
                    $bar->advance();

                    $item = $items->get($itemName);
                    if (!$item) {
                        continue;
                    }

                    // Tentukan apakah hari ini adalah hari aktif untuk item ini
                    $isActiveDay = match(true) {
                        $pattern['days_per_week'] >= 7 => true,
                        $pattern['days_per_week'] >= 5 => !in_array($dayOfWeek, [0, 6]),      // skip weekend
                        $pattern['days_per_week'] >= 3 => in_array($dayOfWeek, [1, 3, 5]),    // Sen, Rab, Jum
                        $pattern['days_per_week'] >= 2 => in_array($dayOfWeek, [1, 4]),        // Sen, Kam
                        default                        => $dayOfWeek === 2,                     // Selasa saja
                    };

                    if (!$isActiveDay) {
                        continue;
                    }

                    $qty = rand($pattern['min'], $pattern['max']);

                    // Cari lokasi picking yang terkait dengan item (zona non-BLK)
                    $itemLocs  = DB::table('item_location')->where('item_id', $item->id)->pluck('location_id');
                    $pickingLoc = Location::whereIn('id', $itemLocs)
                        ->where('zone', '!=', 'BLK')
                        ->first();

                    $locId = $pickingLoc?->id ?? Location::whereIn('id', $itemLocs)->first()?->id;

                    if (!$locId) {
                        continue;
                    }

                    // --- Catat Barang Keluar (Outgoing) ---
                    // Status: approved → dihitung ke skor CBS
                    $processedAt = $date->copy()->setTime(rand(13, 17), rand(0, 59));
                    OutgoingGood::create([
                        'item_id'      => $item->id,
                        'requested_by' => $petugas->id,
                        'approved_by'  => $admin->id,
                        'location_id'  => $locId,
                        'quantity'     => $qty,
                        'status'       => 'approved',
                        'destination'  => 'Distribusi rutin - data historis',
                        'note'         => 'Data historis 60 hari',
                        'requested_at' => $date->copy()->setTime(rand(9, 12), rand(0, 59)),
                        'processed_at' => $processedAt,
                        'created_at'   => $date->copy()->setTime(rand(9, 12), rand(0, 59)),
                        'updated_at'   => $processedAt,
                    ]);

                    // --- Catat Barang Masuk (Incoming) — pengisian ulang stok ---
                    // Hanya sebagai catatan historis, tidak mengubah pivot/stok saat ini
                    IncomingGood::create([
                        'item_id'    => $item->id,
                        'user_id'    => $petugas->id,
                        'location_id'=> $locId,
                        'quantity'   => $qty,
                        'note'       => 'Penerimaan rutin - data historis',
                        'created_at' => $date->copy()->setTime(rand(7, 9), rand(0, 59)),
                        'updated_at' => $date->copy()->setTime(rand(7, 9), rand(0, 59)),
                    ]);
                }
            }
        });

        $bar->finish();
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

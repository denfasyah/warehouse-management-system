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
     * Buat data transaksi palsu selama 60 hari ke belakang
     * agar skor CBS terlihat bervariasi (Fast, Medium, Slow).
     *
     * Pola yang dibuat:
     *  - Engine Oil  → laku keras setiap hari  → FAST
     *  - Gear Oil    → laku moderat             → MEDIUM
     *  - Brake Pad   → laku moderat             → MEDIUM
     *  - Transmisi Oil → jarang keluar           → SLOW
     *  - Battery     → jarang keluar             → SLOW
     */
    public function run(): void
    {
        $admin  = User::where('role', 'admin')->first();
        $petugas = User::where('role', 'petugas')->first();

        if (!$admin || !$petugas) {
            $this->command->warn('Admin atau Petugas tidak ditemukan. Seeder dilewati.');
            return;
        }

        $items = Item::with('locations')->get()->keyBy('name');

        // Definisi pola frekuensi keluar per item (qty per kejadian, berapa kali per minggu)
        $patterns = [
            'Engine Oil'      => ['min' => 50, 'max' => 200, 'days_per_week' => 7],  // FAST
            'Brake Fluid'     => ['min' => 20, 'max' => 80,  'days_per_week' => 5],  // FAST  
            'Gear Oil'        => ['min' => 10, 'max' => 40,  'days_per_week' => 3],  // MEDIUM
            'Brake Pad'       => ['min' => 15, 'max' => 60,  'days_per_week' => 3],  // MEDIUM
            'Brake Disc'      => ['min' => 5,  'max' => 20,  'days_per_week' => 2],  // SLOW
            'Transmisi Oil'   => ['min' => 2,  'max' => 10,  'days_per_week' => 1],  // SLOW
            'Battery'         => ['min' => 3,  'max' => 15,  'days_per_week' => 1],  // SLOW
        ];

        $this->command->info('Membuat data historis transaksi 60 hari...');
        
        $bar = $this->command->getOutput()->createProgressBar(60 * count($patterns));

        DB::transaction(function () use ($items, $patterns, $admin, $petugas, $bar) {
            for ($daysAgo = 60; $daysAgo >= 0; $daysAgo--) {
                $date = Carbon::now()->subDays($daysAgo);
                $dayOfWeek = (int) $date->dayOfWeek; // 0=Sun, 6=Sat

                foreach ($patterns as $itemName => $pattern) {
                    $item = $items->get($itemName);
                    if (!$item) {
                        $bar->advance();
                        continue;
                    }

                    // Tentukan apakah hari ini aktif untuk item ini
                    $isActiveDay = false;
                    $daysPerWeek = $pattern['days_per_week'];
                    
                    // Distribusi hari aktif berdasarkan frekuensi per minggu
                    if ($daysPerWeek >= 7) {
                        $isActiveDay = true;
                    } elseif ($daysPerWeek >= 5) {
                        $isActiveDay = !in_array($dayOfWeek, [0, 6]); // skip weekend
                    } elseif ($daysPerWeek >= 3) {
                        $isActiveDay = in_array($dayOfWeek, [1, 3, 5]); // Mon, Wed, Fri
                    } elseif ($daysPerWeek >= 2) {
                        $isActiveDay = in_array($dayOfWeek, [1, 4]); // Mon, Thu
                    } else {
                        $isActiveDay = ($dayOfWeek === 2); // hanya Selasa
                    }

                    if (!$isActiveDay) {
                        $bar->advance();
                        continue;
                    }

                    $qty = rand($pattern['min'], $pattern['max']);

                    // Buat Incoming Good (saat barang masuk)
                    $primaryLoc = $item->locations->where('is_bulk_zone', false)->first()
                        ?? $item->locations->first();

                    if ($primaryLoc) {
                        IncomingGood::create([
                            'item_id'     => $item->id,
                            'location_id' => $primaryLoc->id,
                            'user_id'     => $petugas->id,
                            'quantity'    => $qty,
                            'note'        => 'Penerimaan rutin - simulasi data historis',
                            'created_at'  => $date->copy()->setTime(rand(8, 10), rand(0, 59)),
                            'updated_at'  => $date->copy()->setTime(rand(8, 10), rand(0, 59)),
                        ]);
                    }

                    // Buat Outgoing Good (barang keluar dan langsung di-approve)
                    $processedAt = $date->copy()->setTime(rand(13, 17), rand(0, 59));
                    OutgoingGood::create([
                        'item_id'      => $item->id,
                        'requested_by' => $petugas->id,
                        'approved_by'  => $admin->id,
                        'location_id'  => $primaryLoc?->id,
                        'quantity'     => $qty,
                        'status'       => 'approved',
                        'destination'  => 'Distribusi rutin - simulasi',
                        'note'         => 'Data historis 60 hari',
                        'requested_at' => $date->copy()->setTime(rand(10, 12), rand(0, 59)),
                        'processed_at' => $processedAt,
                        'created_at'   => $date->copy()->setTime(rand(10, 12), rand(0, 59)),
                        'updated_at'   => $processedAt,
                    ]);

                    $bar->advance();
                }
            }
        });

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Data historis berhasil dibuat. Menjalankan kalkulasi CBS...');

        // Jalankan kalkulasi CBS setelah semua data masuk
        $counts = CBSService::recalculateAll();

        $this->command->info("CBS selesai dikalkulasi:");
        $this->command->table(['Kelas', 'Jumlah Item'], [
            ['Fast Moving', $counts['fast']],
            ['Medium Moving', $counts['medium']],
            ['Slow Moving', $counts['slow']],
        ]);
    }
}

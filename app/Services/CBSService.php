<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Location;
use App\Models\Setting;
use App\Models\OutgoingGood;
use Illuminate\Support\Facades\DB;

class CBSService
{
    /**
     * Hitung ulang storage_class untuk SATU item berdasarkan history 30 hari.
     */
    public static function recalculate(Item $item): void
    {
        $fastThreshold   = (int) Setting::getValue('cbs_fast_threshold', 50);
        $mediumThreshold = (int) Setting::getValue('cbs_medium_threshold', 10);

        $thirtyDaysAgo = now()->subDays(30);

        $frequencyScore = OutgoingGood::where('item_id', $item->id)
            ->where('status', 'approved')
            ->where('processed_at', '>=', $thirtyDaysAgo)
            ->sum('quantity');

        if ($frequencyScore >= $fastThreshold) {
            $storageClass = 'fast';
        } elseif ($frequencyScore >= $mediumThreshold) {
            $storageClass = 'medium';
        } else {
            $storageClass = 'slow';
        }

        $item->update([
            'frequency_score' => $frequencyScore,
            'storage_class'   => $storageClass,
        ]);
    }

    /**
     * Hitung ulang storage_class untuk SEMUA item.
     * Dipanggil dari Artisan command atau saat threshold berubah.
     */
    public static function recalculateAll(): array
    {
        $fastThreshold   = (int) Setting::getValue('cbs_fast_threshold', 50);
        $mediumThreshold = (int) Setting::getValue('cbs_medium_threshold', 10);

        $thirtyDaysAgo = now()->subDays(30);
        $counts = ['fast' => 0, 'medium' => 0, 'slow' => 0];

        $items = Item::all();
        foreach ($items as $item) {
            $frequencyScore = OutgoingGood::where('item_id', $item->id)
                ->where('status', 'approved')
                ->where('processed_at', '>=', $thirtyDaysAgo)
                ->sum('quantity');

            if ($frequencyScore >= $fastThreshold) {
                $storageClass = 'fast';
            } elseif ($frequencyScore >= $mediumThreshold) {
                $storageClass = 'medium';
            } else {
                $storageClass = 'slow';
            }

            $item->update([
                'frequency_score' => $frequencyScore,
                'storage_class'   => $storageClass,
            ]);

            $counts[$storageClass]++;
        }

        return $counts;
    }

    /**
     * Dapatkan lokasi yang direkomendasikan untuk sebuah item berdasarkan kelas CBS.
     * Mengembalikan lokasi yang storage_class-nya cocok dan masih ada kapasitas.
     */
    public static function suggestLocations(Item $item): \Illuminate\Database\Eloquent\Collection
    {
        $class = $item->storage_class;

        if ($class === 'unclassified') {
            return collect();
        }

        // Ambil lokasi picking (non-BLK) yang sesuai kelas dan masih ada kapasitas
        return Location::where('storage_class', $class)
            ->where('is_active', true)
            ->whereColumn('current_fill', '<', 'capacity')
            ->orderBy('current_fill') // yang paling kosong dulu
            ->get();
    }

    /**
     * Dapatkan statistik CBS untuk dashboard.
     */
    public static function getDashboardStats(): array
    {
        $today = now()->toDateString();
        $sevenDaysAgo = now()->subDays(6)->startOfDay();

        // Hitung barang per kelas
        $classCounts = Item::selectRaw('storage_class, COUNT(*) as total')
            ->groupBy('storage_class')
            ->pluck('total', 'storage_class')
            ->toArray();

        // Kapasitas per zona
        $zoneStats = Location::selectRaw("
                zone,
                SUM(capacity) as total_capacity,
                SUM(current_fill) as total_fill
            ")
            ->where('zone', '!=', 'BLK')
            ->groupBy('zone')
            ->get();

        // Incoming hari ini
        $incomingToday = \App\Models\IncomingGood::whereDate('created_at', $today)->sum('quantity');

        // Outgoing hari ini (approved)
        $outgoingToday = OutgoingGood::where('status', 'approved')
            ->whereDate('processed_at', $today)
            ->sum('quantity');

        // Pending approvals
        $pendingCount = OutgoingGood::where('status', 'pending')->count();

        // Low stock items
        $lowStockCount = Item::whereColumn('stock', '<=', 'min_stock')->count();

        // Total stok semua item
        $totalStock = Item::sum('stock');

        // Data grafik 7 hari (masuk vs keluar)
        $chartDays = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $chartDays[] = [
                'date'     => now()->subDays($i)->format('D, d M'),
                'incoming' => \App\Models\IncomingGood::whereDate('created_at', $date)->sum('quantity'),
                'outgoing' => OutgoingGood::where('status', 'approved')
                    ->whereDate('processed_at', $date)
                    ->sum('quantity'),
            ];
        }

        // Mismatch items (perlu relokasi)
        $mismatchCount = Item::with('locations')
            ->where('storage_class', '!=', 'unclassified')
            ->get()
            ->filter(fn($item) => $item->is_location_mismatch)
            ->count();

        return [
            'class_counts'    => $classCounts,
            'zone_stats'      => $zoneStats,
            'incoming_today'  => $incomingToday,
            'outgoing_today'  => $outgoingToday,
            'pending_count'   => $pendingCount,
            'low_stock_count' => $lowStockCount,
            'total_stock'     => $totalStock,
            'chart_days'      => $chartDays,
            'mismatch_count'  => $mismatchCount,
        ];
    }
}

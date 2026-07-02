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
     */
    public static function suggestLocations(Item $item): \Illuminate\Database\Eloquent\Collection
    {
        $class = $item->storage_class;

        if ($class === 'unclassified') {
            return collect();
        }

        return Location::where('storage_class', $class)
            ->where('is_active', true)
            ->whereColumn('current_fill', '<', 'capacity')
            ->orderBy('current_fill')
            ->get();
    }

    /**
     * Backward-compatible: selalu gunakan 7 hari.
     */
    public static function getDashboardStats(): array
    {
        return self::getDashboardStatsByPeriod(7);
    }

    /**
     * Statistik dashboard dengan dukungan filter periode.
     *
     * @param int $days  1 = hari ini, 7 = 7 hari terakhir, 30 = 30 hari terakhir
     */
    public static function getDashboardStatsByPeriod(int $days = 7): array
    {
        $today = now()->toDateString();

        // ── Stat cards berdasarkan periode ───────────────────────────
        if ($days === 1) {
            $incomingPeriod = \App\Models\IncomingGood::whereDate('created_at', $today)->sum('quantity');
            $outgoingPeriod = OutgoingGood::where('status', 'approved')
                ->whereDate('processed_at', $today)->sum('quantity');
            $periodLabel = 'Hari Ini';
        } else {
            $from = now()->subDays($days - 1)->startOfDay();
            $incomingPeriod = \App\Models\IncomingGood::where('created_at', '>=', $from)->sum('quantity');
            $outgoingPeriod = OutgoingGood::where('status', 'approved')
                ->where('processed_at', '>=', $from)->sum('quantity');
            $periodLabel = $days . ' Hari Terakhir';
        }

        // ── Distribusi kelas CBS ──────────────────────────────────────
        $classCounts = Item::selectRaw('storage_class, COUNT(*) as total')
            ->groupBy('storage_class')
            ->pluck('total', 'storage_class')
            ->toArray();

        // ── Kapasitas per zona ────────────────────────────────────────
        $zoneStats = Location::selectRaw(
            "zone, SUM(capacity) as total_capacity, SUM(current_fill) as total_fill"
        )->where('zone', '!=', 'BLK')->groupBy('zone')->get();

        // ── Global stats (tidak bergantung periode) ───────────────────
        $pendingCount  = OutgoingGood::where('status', 'pending')->count();
        $lowStockCount = Item::whereColumn('stock', '<=', 'min_stock')->count();
        $totalStock    = Item::sum('stock');

        // ── Data grafik per hari ──────────────────────────────────────
        $chartDays = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date  = now()->subDays($i)->toDateString();
            $label = ($days <= 7)
                ? now()->subDays($i)->format('D, d M')
                : now()->subDays($i)->format('d M');

            $chartDays[] = [
                'date'     => $label,
                'incoming' => \App\Models\IncomingGood::whereDate('created_at', $date)->sum('quantity'),
                'outgoing' => OutgoingGood::where('status', 'approved')
                    ->whereDate('processed_at', $date)->sum('quantity'),
            ];
        }

        // ── Mismatch (perlu relokasi) ─────────────────────────────────
        $mismatchCount = Item::with('locations')
            ->where('storage_class', '!=', 'unclassified')
            ->get()
            ->filter(fn($item) => $item->is_location_mismatch)
            ->count();

        return [
            'class_counts'    => $classCounts,
            'zone_stats'      => $zoneStats,
            'incoming_today'  => $incomingPeriod,
            'outgoing_today'  => $outgoingPeriod,
            'period_label'    => $periodLabel,
            'period_days'     => $days,
            'pending_count'   => $pendingCount,
            'low_stock_count' => $lowStockCount,
            'total_stock'     => $totalStock,
            'chart_days'      => $chartDays,
            'mismatch_count'  => $mismatchCount,
        ];
    }
}

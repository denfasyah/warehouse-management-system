<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('cbs:calculate')]
#[Description('Calculate storage class for all items based on frequency of outgoing goods in the last 30 days.')]
class CalculateStorageClass extends Command
{
    public function handle()
    {
        $this->info('Starting CBS Calculation...');

        $fastThreshold = (int) \App\Models\Setting::getValue('cbs_fast_threshold', 50);
        $mediumThreshold = (int) \App\Models\Setting::getValue('cbs_medium_threshold', 10);

        $this->info("Thresholds - Fast: >= {$fastThreshold}, Medium: >= {$mediumThreshold}, Slow: < {$mediumThreshold}");

        $items = \App\Models\Item::all();
        $thirtyDaysAgo = now()->subDays(30);

        $fastCount = 0;
        $mediumCount = 0;
        $slowCount = 0;

        $bar = $this->output->createProgressBar(count($items));
        $bar->start();

        foreach ($items as $item) {
            // Hitung total quantity outgoing dalam 30 hari terakhir
            $frequencyScore = \App\Models\OutgoingGood::where('item_id', $item->id)
                ->where('status', 'approved')
                ->where('processed_at', '>=', $thirtyDaysAgo)
                ->sum('quantity');

            // Tentukan class
            if ($frequencyScore >= $fastThreshold) {
                $storageClass = 'fast';
                $fastCount++;
            } elseif ($frequencyScore >= $mediumThreshold) {
                $storageClass = 'medium';
                $mediumCount++;
            } else {
                $storageClass = 'slow';
                $slowCount++;
            }

            // Update item
            $item->update([
                'frequency_score' => $frequencyScore,
                'storage_class' => $storageClass,
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("CBS Calculation Completed!");
        $this->table(
            ['Class', 'Count'],
            [
                ['Fast', $fastCount],
                ['Medium', $mediumCount],
                ['Slow', $slowCount],
            ]
        );
        
        return self::SUCCESS;
    }
}

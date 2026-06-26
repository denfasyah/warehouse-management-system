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

        $counts = \App\Services\CBSService::recalculateAll();

        $fastCount = $counts['fast'] ?? 0;
        $mediumCount = $counts['medium'] ?? 0;
        $slowCount = $counts['slow'] ?? 0;
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

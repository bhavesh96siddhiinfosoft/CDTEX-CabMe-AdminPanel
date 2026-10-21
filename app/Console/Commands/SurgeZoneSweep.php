<?php

namespace App\Console\Commands;

use App\Services\SurgeService;
use Illuminate\Console\Command;

class SurgeZoneSweep extends Command
{
    protected $signature = 'surge:sweep';
    protected $description = 'Recompute demand/supply per surge zone and purge inactive zones';

    public function handle(SurgeService $surge): int
    {
        $surge->sweepAll();
        $this->info('Surge zones swept');
        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Experiment;
use Illuminate\Console\Command;

class SyncExperimentStatuses extends Command
{
    protected $signature = 'experiments:sync-status';
    protected $description = 'Sync experiment.status with current reservations';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:sync-experiment-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Experiment::query()->chunkById(200, function ($chunk) {
            foreach ($chunk as $exp) {
                $exp->refreshStatus();
            }
        });

        $this->info('Experiment statuses synced.');
        return self::SUCCESS;
    }
}

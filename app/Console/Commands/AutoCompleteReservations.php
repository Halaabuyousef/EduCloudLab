<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Reservation;
use Illuminate\Console\Command;

class AutoCompleteReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
   

    /**
     * The console command description.
     *
     * @var string
     */


    /**
     * Execute the console command.
     */
    protected $signature = 'reservations:auto-complete';
    protected $description = 'Mark reservations as completed if end_time has passed';

    public function handle()
    {
        $count = Reservation::where('status', 'active')
            ->where('end_time', '<=', Carbon::now())
            ->update(['status' => 'completed']);

        $this->info("Updated {$count} reservations to completed.");
    }
}

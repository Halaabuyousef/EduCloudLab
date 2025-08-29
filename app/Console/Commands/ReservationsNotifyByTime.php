<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Notifications\ReservationStarted;
use App\Notifications\ReservationCompleted;
use App\Notifications\ReservationWillStartSoon;

class ReservationsNotifyByTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:notify-by-time {--window=10 : Minutes before start}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send start/complete notifications based on time window';

    /**
     * Execute the console command.
     */


}

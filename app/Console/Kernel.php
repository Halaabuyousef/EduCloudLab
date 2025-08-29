<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {

        // 1) مزامنة حالة التجارب – الأمر جاهز ويقبل withoutOverlapping بدون اسم
        $schedule->command('experiments:sync-status')
            ->everyMinute()->timezone('Asia/Gaza')
            ->withoutOverlapping();

        // 2) Pending -> Active
        $schedule->call(function () {
            $now = now();

            \App\Models\Reservation::with(['user', 'experiment'])
                ->where('status', 'pending')
                ->where('start_time', '<=', $now)
                ->where('end_time', '>', $now)
                ->chunkById(200, function ($chunk) {
                    foreach ($chunk as $reservation) {
                        $old = $reservation->status;
                        $reservation->update(['status' => 'active']);

                        if ($old === 'pending') {
                            $reservation->user->notify(
                                new \App\Notifications\ReservationStarted($reservation)
                            );
                        }
                    }
                });
        })
            ->everyMinute()
            ->name('reservations-activate')->timezone('Asia/Gaza')
            ->withoutOverlapping();


        $schedule->command('reservations:notify-by-time')
            ->everyMinute()
            ->timezone('Asia/Gaza')
            ->withoutOverlapping()
            ->name('reservations-notify-by-time');

        // 3) Active -> Completed
        $schedule->call(function () {
            $now = now();

            \App\Models\Reservation::with(['user', 'experiment'])
                ->where('status', 'active')
                ->where('end_time', '<=', $now)
                ->chunkById(200, function ($chunk) {
                    foreach ($chunk as $reservation) {
                        $old = $reservation->status;
                        $reservation->update(['status' => 'completed']);

                        if ($old === 'active') {
                            $reservation->user->notify(
                                new \App\Notifications\ReservationCompleted($reservation)
                            );
                        }
                    }
                });
        })
            ->everyMinute()
            ->name('reservations-complete')->timezone('Asia/Gaza')
            ->withoutOverlapping();

        $schedule->command('reservations:notify-by-time')
            ->everyMinute()
            ->timezone('Asia/Gaza')
            ->withoutOverlapping()
            ->name('reservations-notify-by-time');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}

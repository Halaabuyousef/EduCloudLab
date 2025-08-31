<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * اجعل كل الجدولة على نفس المنطقة الزمنية
     */
    protected function scheduleTimezone()
    {
        return 'Asia/Gaza'; // أو 'Asia/Hebron' لو بتحب توحّدها
    }

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // 1) Sync experiments
        $schedule->command('experiments:sync-status')
            ->everyMinute()
            ->withoutOverlapping()     // للأوامر (command) لا تحتاج name
            ->runInBackground();

        
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

                        if ($old === 'pending' && $reservation->user) {
                            $reservation->user->notify(
                                new \App\Notifications\ReservationStarted($reservation)
                            );
                        }
                    }
                });
        })
            ->everyMinute()
            ->name('reservations-activate')
            ->withoutOverlapping()
            ->runInBackground();

        // 3) إشعارات الوقت (مرة واحدة فقط — أزل التكرار)
        $schedule->command('reservations:notify-by-time', ['--window' => 10])
            ->everyMinute()
            ->name('reservations-notify-by-time')
            ->withoutOverlapping()
            ->runInBackground();

        // 4) Active -> Completed  (Closure)
        $schedule->call(function () {
            $now = now();

            \App\Models\Reservation::with(['user', 'experiment'])
                ->where('status', 'active')
                ->where('end_time', '<=', $now)
                ->chunkById(200, function ($chunk) {
                    foreach ($chunk as $reservation) {
                        $old = $reservation->status;
                        $reservation->update(['status' => 'completed']);

                        if ($old === 'active' && $reservation->user) {
                            $reservation->user->notify(
                                new \App\Notifications\ReservationCompleted($reservation)
                            );
                        }
                    }
                });
        })
            ->everyMinute()
            ->name('reservations-complete')
            ->withoutOverlapping()
            ->runInBackground();
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

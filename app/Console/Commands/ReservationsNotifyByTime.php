<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Notifications\ReservationStarted;
use App\Notifications\ReservationCompleted;
use App\Notifications\ReservationWillStartSoon;

class ReservationsNotifyByTime extends Command
{
    protected $signature = 'reservations:notify-by-time {--window=10 : Minutes before start}';
    protected $description = 'Send start/complete notifications based on time window';

    public function handle()
    {
        $window = (int) $this->option('window');
        if ($window < 0) $window = 0;

        // استخدم تايمزون غزة/الخليل لو محدده بتطبيقك
        $now = now(); // أو: Carbon::now('Asia/Gaza');

        $soonFrom = $now->copy();
        $soonTo   = $now->copy()->addMinutes($window);

        $sentSoon = 0;
        $sentStarted = 0;
        $sentCompleted = 0;

        // 1) سيبدأ قريباً: start_time بين الآن و now+window ولم يُرسل سابقًا
        Reservation::query()
            ->with(['user:id,name,email'])
            ->whereBetween('start_time', [$soonFrom, $soonTo])
            ->whereNull('prestart_notified_at')
            // ->where('status','pending') // فعّلها إن أردت تقييدها
            ->orderBy('start_time')
            ->chunkById(200, function ($chunk) use (&$sentSoon, $window) {
                foreach ($chunk as $res) {
                    if ($res->user) {
                        $res->user->notify(new ReservationWillStartSoon($res, $window));
                        $res->forceFill(['prestart_notified_at' => now()])->save();
                        $sentSoon++;
                    }
                }
            });

        // 2) بدأ الآن: start_time دخل نافذة الدقيقة الحالية ولم يُرسل “بدأت” من قبل
        $startedFrom = $now->copy()->subMinute();
        Reservation::query()
            ->with(['user:id,name,email'])
            ->whereBetween('start_time', [$startedFrom, $now])
            ->whereNull('notified_started_at')
            // ->where('status','active') // فعّلها إن أردت
            ->orderBy('start_time')
            ->chunkById(200, function ($chunk) use (&$sentStarted) {
                foreach ($chunk as $res) {
                    if ($res->user) {
                        $res->user->notify(new ReservationStarted($res));
                        $res->forceFill(['notified_started_at' => now()])->save();
                        $sentStarted++;
                    }
                }
            });

        // 3) انتهى الآن: end_time دخل نافذة الدقيقة الحالية ولم يُرسل “اكتمل” من قبل
        $endedFrom = $now->copy()->subMinute();
        Reservation::query()
            ->with(['user:id,name,email'])
            ->whereBetween('end_time', [$endedFrom, $now])
            ->whereNull('notified_completed_at')
            // ->where('status','completed') // فعّلها إن أردت
            ->orderBy('end_time')
            ->chunkById(200, function ($chunk) use (&$sentCompleted) {
                foreach ($chunk as $res) {
                    if ($res->user) {
                        $res->user->notify(new ReservationCompleted($res));
                        $res->forceFill(['notified_completed_at' => now()])->save();
                        $sentCompleted++;
                    }
                }
            });

        $this->info('Time-based notifications dispatched.');
        $this->info("Soon: {$sentSoon}, Started: {$sentStarted}, Completed: {$sentCompleted}");
        Log::info('reservations:notify-by-time', [
            'soon' => $sentSoon,
            'started' => $sentStarted,
            'completed' => $sentCompleted,
            'at' => $now->toDateTimeString(),
        ]);

        return self::SUCCESS;
    }
}

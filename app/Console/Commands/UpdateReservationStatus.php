<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;

class UpdateReservationStatus extends Command
{
    protected $signature = 'reservations:update-status';
    protected $description = 'Update reservation status automatically';

    public function handle()
    {
        // استخدم توقيت التطبيق (config/app.php) لتوحيد المقارنات
        $now = now();

        // ==== Pending -> Active ====
        $eligiblePending = Reservation::query()
            ->where('status', 'pending')
            ->where('start_time', '<=', $now)
            ->where('end_time', '>',  $now)
            ->count();

        Log::info("[reservations:update-status] eligible pending->active: {$eligiblePending} @ {$now}");

        $updatedPending = Reservation::query()
            ->where('status', 'pending')
            ->where('start_time', '<=', $now)
            ->where('end_time', '>',  $now)
            ->update(['status' => 'active']);

        Log::info("[reservations:update-status] updated pending->active: {$updatedPending}");

        // ==== Active -> Completed ====
        $eligibleActive = Reservation::query()
            ->where('status', 'active')
            ->where('end_time', '<=', $now)
            ->count();

        Log::info("[reservations:update-status] eligible active->completed: {$eligibleActive} @ {$now}");

        $updatedActive = Reservation::query()
            ->where('status', 'active')
            ->where('end_time', '<=', $now)
            ->update(['status' => 'completed']);

        Log::info("[reservations:update-status] updated active->completed: {$updatedActive}");

        $this->info('Reservation statuses updated successfully.');
        return self::SUCCESS;
    }
}

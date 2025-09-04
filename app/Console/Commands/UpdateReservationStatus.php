<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;
use App\Models\Experiment;

class UpdateReservationStatus extends Command
{
    protected $signature = 'reservations:update-status';
    protected $description = 'Update reservation status automatically';

    public function handle()
    {
        $now = now();

        // ===== Pending -> Active =====
        $toActiveQ = Reservation::query()
            ->where('status', 'pending')
            ->where('start_time', '<=', $now)
            ->where('end_time',   '>',  $now);

        $toActiveExpIds = $toActiveQ->clone()->pluck('experiment_id')->all();
        $eligiblePending = $toActiveQ->clone()->count();
        Log::info("[reservations:update-status] eligible pending->active: {$eligiblePending} @ {$now}");

        $updatedPending = $toActiveQ->update(['status' => 'active']);
        Log::info("[reservations:update-status] updated pending->active: {$updatedPending}");

        // ===== Active -> Completed =====
        $toCompletedQ = Reservation::query()
            ->where('status', 'active')
            ->where('end_time', '<=', $now);

        $toCompletedExpIds = $toCompletedQ->clone()->pluck('experiment_id')->all();
        $eligibleActive = $toCompletedQ->clone()->count();
        Log::info("[reservations:update-status] eligible active->completed: {$eligibleActive} @ {$now}");

        $updatedActive = $toCompletedQ->update(['status' => 'completed']);
        Log::info("[reservations:update-status] updated active->completed: {$updatedActive}");

     
       
        $affectedExperimentIds = array_values(array_unique(array_merge($toActiveExpIds, $toCompletedExpIds)));
        if (!empty($affectedExperimentIds)) {

          
            $reservedCount = Experiment::query()
                ->whereIn('id', $affectedExperimentIds)
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('reservations')
                        ->whereColumn('reservations.experiment_id', 'experiments.id')
                        ->whereIn('reservations.status', ['pending', 'active']);
                })
                ->update(['status' => 'reserved']);


            $availableCount = Experiment::query()
                ->whereIn('id', $affectedExperimentIds)
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('reservations')
                        ->whereColumn('reservations.experiment_id', 'experiments.id')
                        ->whereIn('reservations.status', ['pending', 'active']);
                })
                ->update(['status' => 'available']);

            Log::info("[reservations:update-status] experiments synced => reserved: {$reservedCount}, available: {$availableCount}");
        } else {
            Log::info("[reservations:update-status] no affected experiments to sync.");
        }

        $this->info('Reservation & experiment statuses updated successfully.');
        return self::SUCCESS;
    }
}

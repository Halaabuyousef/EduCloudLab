<?php

namespace App\Support;

use App\Models\Reservation;
use App\Models\ReservationHold;
use Illuminate\Support\Carbon;

class ReservationOverlap
{
    public static function exists(
        int $experimentId,
        $startAt,
        $endAt,
        ?int $ignoreReservationId = null
    ): bool {
        $start = Carbon::parse($startAt);
        $end   = Carbon::parse($endAt);

        // 1) تحقق من الحجوزات الفعّالة
        $resOverlap = Reservation::where('experiment_id', $experimentId)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->when($ignoreReservationId, fn($q) => $q->where('id', '<>', $ignoreReservationId))
            ->where('start_time', '<', $end)
            ->where('end_time',   '>', $start)
            ->exists();

        if ($resOverlap) return true;

        // 2) تحقق من الـ Holds الفعّالة
        return ReservationHold::where('experiment_id', $experimentId)
            ->where('expires_at', '>', now())
            ->where('starts_at', '<', $end)
            ->where('ends_at',   '>', $start)
            ->exists();
    }
}

<?php

namespace App\Observers;

use App\Models\Reservation;

class ReservationObserver
{
    
    /**
     * Handle the Reservation "created" event.
     */
    public function created(Reservation $r)
    {
        optional($r->experiment)->refreshStatus();
    }

    /**
     * Handle the Reservation "updated" event.
     */
    public function updated(Reservation $r)
    {
        optional($r->experiment)->refreshStatus();
    }

    /**
     * Handle the Reservation "deleted" event.
     */
    public function deleted(Reservation $r)
    {
        optional($r->experiment)->refreshStatus();
    }

    /**
     * Handle the Reservation "restored" event.
     */
    public function restored(Reservation $r)
    {
        optional($r->experiment)->refreshStatus();
    }

    /**
     * Handle the Reservation "force deleted" event.
     */
    public function forceDeleted(Reservation $reservation): void
    {
        //
    }
}

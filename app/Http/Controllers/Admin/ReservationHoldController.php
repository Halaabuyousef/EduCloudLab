<?php

namespace App\Http\Controllers\Admin;

use App\Models\Experiment;
use App\Models\Reservation;
use App\Models\ReservationHold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Support\ReservationOverlap;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class ReservationHoldController extends Controller
{
    public function store(Request $request)
    {
        $data = $this->validateData($request, true);

        return DB::transaction(function () use ($data) {
            Reservation::where('experiment_id', $data['experiment_id'])
                ->where('start_time', '<', $data['end_time'])
                ->where('end_time',   '>', $data['start_time'])
                ->lockForUpdate()
                ->exists();

            if (ReservationOverlap::exists($data['experiment_id'], $data['start_time'], $data['end_time'])) {
                throw ValidationException::withMessages([
                    'start_time' => 'The selected time conflicts with another reservation or hold.',
                ]);
            }

            Reservation::create($data);

            return back()->with(['msg' => 'Reservation created successfully.', 'type' => 'success']);
        });
    }

    public function destroy(ReservationHold $hold)
    {
        $this->authorize('delete', $hold);
        $hold->delete();
        return response()->json(['ok' => true]);
    }

    public function update(Request $request, Reservation $reservation)
    {
        $data = $this->validateData($request, false);

        return DB::transaction(function () use ($reservation, $data) {
            Reservation::where('experiment_id', $reservation->experiment_id)
                ->where('id', '<>', $reservation->id)
                ->where('start_time', '<', $data['end_time'])
                ->where('end_time',   '>', $data['start_time'])
                ->lockForUpdate()
                ->exists();

            if (ReservationOverlap::exists($reservation->experiment_id, $data['start_time'], $data['end_time'], $reservation->id)) {
                throw ValidationException::withMessages([
                    'start_time' => 'The selected time conflicts with another reservation or hold.',
                ]);
            }

            $reservation->update($data);

            return back()->with(['msg' => 'Reservation updated.', 'type' => 'success']);
        });
    }

    public function availability(Request $request, Experiment $experiment)
    {
        $request->validate([
            'from' => ['required', 'date'],
            'to'   => ['required', 'date', 'after:from'],
        ]);

        $from = Carbon::parse($request->from);
        $to   = Carbon::parse($request->to);

        $busyReservations = Reservation::where('experiment_id', $experiment->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->where('start_time', '<', $to)
            ->where('end_time', '>', $from)
            ->get(['start_time as start', 'end_time as end', 'status']);

        $busyHolds = ReservationHold::where('experiment_id', $experiment->id)
            ->where('expires_at', '>', now())
            ->where('starts_at', '<', $to)
            ->where('ends_at', '>', $from)
            ->get(['starts_at as start', 'ends_at as end']);

        return response()->json([
            'busy' => [
                'reservations' => $busyReservations,
                'holds'        => $busyHolds,
            ]
        ]);
    }

    protected function validateData(Request $request, bool $isCreate = true): array
    {
        return $request->validate([
            'experiment_id' => ['required', 'exists:experiments,id'],
            'user_id'       => ['required', 'exists:users,id'],
            'start_time'    => ['required', 'date'],
            'end_time'      => ['required', 'date', 'after:start_time'],
            'status'        => ['required', 'in:pending,active,postponed,completed,cancelled'],
            'notes'         => ['nullable', 'string', 'max:1000'],
        ]);
    }
}

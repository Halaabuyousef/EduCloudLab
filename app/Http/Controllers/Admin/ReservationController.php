<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Experiment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Support\ReservationOverlap;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $base = Reservation::with([
            'experiment:id,title',
            'user:id,name,email,supervisor_id',
            'user.supervisor:id,name'
        ])->when($q, function ($qq) use ($q) {
            $qq->where(function ($w) use ($q) {
                $w->whereHas('user', fn($u) => $u->where('name', 'like', "%$q%")->orWhere('email', 'like', "%$q%"))
                    ->orWhereHas('experiment', fn($e) => $e->where('title', 'like', "%$q%"));
            });
        });

        $current   = (clone $base)->where('status', 'active')->where('start_time', '>=', now())->orderBy('start_time')->paginate(10, ['*'], 'current_page');
        $booked    = (clone $base)->whereIn('status', ['pending', 'postponed'])->latest('id')->paginate(10, ['*'], 'booked_page');
        $postponed = (clone $base)->where('status', 'postponed')->latest('start_time')->paginate(10, ['*'], 'postponed_page');
        $finished  = (clone $base)->whereIn('status', ['completed', 'cancelled'])->latest('end_time')->paginate(10, ['*'], 'finished_page');

        $experiments = Experiment::orderBy('title')->get(['id', 'title']);
        $users       = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('reservations.index', compact('current', 'booked', 'postponed', 'finished', 'experiments', 'users', 'q'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request, true);

        if (ReservationOverlap::exists($data['experiment_id'], $data['start_time'], $data['end_time'])) {
            return back()->withErrors(['start_time' => 'The selected time conflicts with another reservation or hold.'])->withInput();
        }

        Reservation::create($data);
        return back()->with(['msg' => 'Reservation created successfully.', 'type' => 'success']);
    }

    public function update(Request $request, Reservation $reservation)
    {
        $data = $this->validateData($request, false);

        if (ReservationOverlap::exists($data['experiment_id'], $data['start_time'], $data['end_time'], $reservation->id)) {
            return back()->withErrors(['start_time' => 'The selected time conflicts with another reservation or hold.'])->withInput();
        }

        $reservation->update($data);
        return back()->with(['msg' => 'Reservation updated.', 'type' => 'success']);
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return back()->with(['msg' => 'Reservation deleted.', 'type' => 'success']);
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'active', 'postponed', 'completed', 'cancelled'])],
        ]);

        $reservation->update(['status' => $request->status]);
        return back()->with(['msg' => 'Status updated.', 'type' => 'success']);
    }

    public function postpone(Request $request, Reservation $reservation)
    {
        $request->validate([
            'new_start_at' => ['required', 'date'],
            'new_end_at'   => ['required', 'date', 'after:new_start_at'],
            'reason'       => ['nullable', 'string', 'max:500'],
        ]);

        $start = Carbon::parse($request->new_start_at);
        $end   = Carbon::parse($request->new_end_at);

        if (ReservationOverlap::exists($reservation->experiment_id, $start, $end, $reservation->id)) {
            return back()->withErrors(['new_start_at' => 'The selected time conflicts with another reservation or hold.'])->withInput();
        }

        $reservation->update([
            'start_time' => $start,
            'end_time'   => $end,
            'status'     => 'postponed',
            'notes'      => trim(($reservation->notes ? $reservation->notes . "\n" : '') . 'Postponed: ' . ($request->reason ?? ''))
        ]);

        return back()->with(['msg' => 'Reservation postponed.', 'type' => 'success']);
    }

    protected function validateData(Request $request, bool $isCreate = true): array
    {
        return $request->validate([
            'experiment_id' => ['required', 'exists:experiments,id'],
            'user_id'       => ['required', 'exists:users,id'],
            'start_time'    => ['required', 'date'],
            'end_time'      => ['required', 'date', 'after:start_time'],
            'status'        => ['required', Rule::in(['pending', 'active', 'postponed', 'completed', 'cancelled'])],
            'notes'         => ['nullable', 'string', 'max:1000'],
        ]);
    }
}

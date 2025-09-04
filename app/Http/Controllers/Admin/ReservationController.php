<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Experiment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Support\ReservationOverlap;
use App\Http\Controllers\Controller;
use App\Notifications\ReservationStarted;
use App\Notifications\ReservationApproved;
use App\Notifications\ReservationCancelled;
use App\Notifications\ReservationCompleted;
// use App\Models\DeviceToken;                  
// use App\Services\FcmService;                 

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
        $current   = (clone $base)->timeActive()->orderBy('start_time')->paginate(10, ['*'], 'current_page');

        $booked    = (clone $base)->timePending()->latest('id')->paginate(10, ['*'], 'booked_page');

        $postponed = (clone $base)->where('status', 'postponed')
            ->latest('start_time')->paginate(10, ['*'], 'postponed_page');

        $finished  = (clone $base)->timeFinished()
            ->latest('end_time')->paginate(10, ['*'], 'finished_page');

        $experiments = Experiment::orderBy('title')->get(['id', 'title']);
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('reservations.index', compact('current', 'booked', 'postponed', 'finished', 'experiments', 'users', 'q'));
    }
    public function show(Reservation $reservation)
    {
        $reservation->load([
            'experiment:id,title',
            'user:id,name,email,supervisor_id',
            'user.supervisor:id,name',
        ]);

        return view('reservations.show', compact('reservation'));
    }
    public function store(Request $request)
    {
        $data = $this->validateData($request, true);

        if (ReservationOverlap::exists($data['experiment_id'], $data['start_time'], $data['end_time'])) {
            return back()->withErrors(['start_time' => 'The selected time conflicts with another reservation or hold.'])->withInput();
        }

        // اختياري: اجعل الـ DB default('pending') لعمود status
        $reservation = Reservation::create($data + ['status' => 'pending']);

        // إشعار موافقة
        $reservation->user->notify(new ReservationApproved($reservation));

        // لو الجلسة بدأت الآن → أرسل "بدأت جلستك" (بدون تعديل status)
        if (now()->between($reservation->start_time, $reservation->end_time)) {
            $reservation->user->notify(new ReservationStarted($reservation));
        }

        return back()->with(['msg' => 'Reservation created successfully.', 'type' => 'success']);
    }


    public function update(Request $request, Reservation $reservation)
    {
        $data = $this->validateData($request, false);

        if (ReservationOverlap::exists($data['experiment_id'], $data['start_time'], $data['end_time'], $reservation->id)) {
            return back()->withErrors(['start_time' => 'The selected time conflicts with another reservation or hold.'])->withInput();
        }

        $oldStatus = $reservation->status;

        // 🔒 منع التفعيل قبل وقت البداية
        if ($data['status'] === 'active' && now()->lt($reservation->start_time)) {
            return back()->withErrors([
                'status' => 'You cannot activate this reservation before its start time.'
            ])->withInput();
        }

        $reservation->update($data);

        $this->fireStatusNotifications($reservation, $oldStatus);

        return back()->with(['msg' => 'Reservation updated.', 'type' => 'success']);
    }


    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        // اختياري: إرسال إشعار الإلغاء
        $reservation->user->notify(new ReservationCancelled($reservation));

        return back()->with(['msg' => 'Reservation deleted.', 'type' => 'success']);
    }
    public function updateStatus(Request $request, Reservation $reservation)
    {
        $request->validate([
            'status' => ['required', Rule::in(['postponed', 'completed', 'cancelled'])], // لا نسمح بـ active/pending
        ]);

        $old = $reservation->status;
        $reservation->update(['status' => $request->status]);

        $this->fireStatusNotifications($reservation, $old);

        return back()->with(['msg' => 'Status updated.', 'type' => 'success']);
    }

    // public function updateStatus(Request $request, Reservation $reservation)
    // {
    //     $request->validate([
    //         'status' => ['required', Rule::in(['pending', 'active', 'postponed', 'completed', 'cancelled'])],
    //     ]);

    //     $oldStatus = $reservation->status;

    //     // 🔒 منع التفعيل قبل وقت البداية
    //     if ($request->status === 'active' && now()->lt($reservation->start_time)) {
    //         return back()->withErrors([
    //             'status' => 'You cannot activate this reservation before its start time.'
    //         ])->withInput();
    //     }

    //     $reservation->update(['status' => $request->status]);

    //     $this->fireStatusNotifications($reservation, $oldStatus);

    //     return back()->with(['msg' => 'Status updated.', 'type' => 'success']);
    // }
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

        // إزالة الشرط الخاطئ على status هنا — هذا الأكشن فقط للتأجيل
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
          
            'notes'         => ['nullable', 'string', 'max:1000'],
        ]);
    }

    /**
     * أطلق إشعارات الحالة حسب الانتقال الفعلي.
     */
    protected function fireStatusNotifications(Reservation $reservation, string $oldStatus): void
    {
        // بدأ الحجز
        if ($reservation->status === 'active' && $oldStatus !== 'active') {
            $reservation->user->notify(new ReservationStarted($reservation));
        }

        // انتهى الحجز
        if ($reservation->status === 'completed' && $oldStatus !== 'completed') {
            $reservation->user->notify(new ReservationCompleted($reservation));
        }

        // أُلغي الحجز
        if ($reservation->status === 'cancelled' && $oldStatus !== 'cancelled') {
            $reservation->user->notify(new ReservationCancelled($reservation));
        }
    }


}

<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Experiment;
use App\Models\Reservation;
use App\Services\FcmService;
use Illuminate\Http\Request;
use App\Models\ReservationHold;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Notifications\ReservationCreatedNotification;

class ReservationController extends Controller
{
  
    private const DURATION_MINUTES = 30;

  
    public function store(Request $request)
    {
       
        $data = $request->validate([
            'experiment_id' => ['required', Rule::exists('experiments', 'id')],
            'date'          => ['required', 'date_format:Y-m-d'],
            'start_time'    => ['required', 'date_format:H:i'],
        ]);

        
        $start = Carbon::parse("{$data['date']} {$data['start_time']}");
        $end   = $start->copy()->addMinutes(self::DURATION_MINUTES);

       
        if ($start->lt(now())) {
            throw ValidationException::withMessages([
                'start_time' => 'Start time must be in the future.',
            ]);
        }

    
        $experiment = Experiment::where('id', $data['experiment_id'])
            ->where('status', 'available') 
            ->first();

        if (!$experiment) {
            throw ValidationException::withMessages([
                'experiment_id' => 'Selected experiment is not available for reservation.',
            ]);
        }

        // الترانزاكشن + القفل لمنع التعارض أثناء الضغط
        return DB::transaction(function () use ($request, $data, $start, $end) {
    
            Reservation::where('experiment_id', $data['experiment_id'])
                ->where('start_time', '<', $end)
                ->where('end_time',   '>', $start)
                ->lockForUpdate()
                ->get();

            if (class_exists(ReservationHold::class)) {
                ReservationHold::where('experiment_id', $data['experiment_id'])
                    ->where('start_time', '<', $end)
                    ->where('end_time',   '>', $start)
                    ->lockForUpdate()
                    ->get();
            }

            // فحص التعارض الفعلي
            $hasOverlap = Reservation::where('experiment_id', $data['experiment_id'])
                ->where('start_time', '<', $end)
                ->where('end_time',   '>', $start)
                ->exists()
                || (class_exists(ReservationHold::class) && ReservationHold::where('experiment_id', $data['experiment_id'])
                    ->where('start_time', '<', $end)
                    ->where('end_time',   '>', $start)
                    ->exists());

            if ($hasOverlap) {
                throw ValidationException::withMessages([
                    'start_time' => 'The selected time conflicts with another reservation.',
                ]);
            }

        
            $reservation = Reservation::create([
                'user_id'       => $request->user()->id,
                'experiment_id' => $data['experiment_id'],
                'start_time'    => $start,
                'end_time'      => $end,
                'status'        => 'pending', 
            ]);
            $request->user()->notify(new ReservationCreatedNotification($reservation));
     

        //     $fcm = new FcmService();
        //     $fcm->sendToUser(
        //         $reservation->user,
        //         "Reservation Confirmed 🎉",
        //         "Your reservation #{$reservation->id} has been approved.",
        //         ['reservation_id' => $reservation->id]
        //     );


            return response()->json([
                'success' => true,
                'message' => 'Reservation created successfully.',
                'data'    => [
                    'id'            => $reservation->id,
                    'experiment_id' => $reservation->experiment_id,
                    'start_time'    => $reservation->start_time->toIso8601String(),
                    'end_time'      => $reservation->end_time->toIso8601String(),
                    'status'        => $reservation->status,
                    'duration_min'  => self::DURATION_MINUTES,
                ],
            ], 201);
        });

      
    }

}

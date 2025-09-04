<?php

namespace App\Http\Controllers\Api;


use Carbon\Carbon;
use App\Models\Experiment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\ReservationHold;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExperimentResource;
use Illuminate\Validation\ValidationException;

class ExperimentController extends Controller
{
    public function index(Request $request)
    {
        $items = Experiment::query()
            ->where('status', 'available')
            ->latest('id')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => [
                'items' => ExperimentResource::collection($items),
            ],
        ]);
    }
    
    public function show($id)
    {
        $exp = Experiment::where('id', $id)
            ->where('status', 'available')
            ->first();

        if (!$exp) {
            return response()->json([
                'success' => false,
                'message' => 'Experiment not found or not available'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => new ExperimentResource($exp),
        ]);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureExperimentSessionIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $experimentId = (int) $request->route('experiment'); // حسب اسم باراميتر الروت

     
        $token = $request->user()?->currentAccessToken();
        if (!$token || !$token->can('experiment:control')) {
            return response()->json(['success' => false, 'message' => 'No experiment control permission.'], 403);
        }

 
        $now = now();
        $res = Reservation::query()
            ->where('user_id', $request->user()->id)
            ->where('experiment_id', $experimentId)
            ->where('status', 'approved')
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->where('session_token_id', $token->id) 
            ->first();

        if (!$res) {
            return response()->json(['success' => false, 'message' => 'Reservation window is not active.'], 403);
        }

        return $next($request);
    }
      
    
}

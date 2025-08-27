<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EmailVerifiedByGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard): Response
    {
        $user = Auth::guard($guard)->user();
        if (!$user) {
            return redirect()->route($guard . '.login');
        }

        if (is_null($user->email_verified_at)) {
            return redirect()->route('con');
        }
        return $next($request);
    }
}

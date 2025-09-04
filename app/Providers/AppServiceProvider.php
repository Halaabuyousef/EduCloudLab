<?php

namespace App\Providers;

use App\Models\Reservation;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Observers\ReservationObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Support\Facades\RateLimiter;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->when(FcmChannel::class)
            ->needs(FcmService::class)
            ->give(fn() => new FcmService());

            
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
        Reservation::observe(ReservationObserver::class);
        View::composer('admin.*', function ($view) {
            if (Auth::guard('admin')->check()) {
                $admin = Auth::guard('admin')->user();
                $view->with('adminUnreadCount', $admin->unreadNotifications()->count());
                $view->with('adminLatestNotifications', $admin->notifications()->latest()->limit(10)->get());
            }
        });
    }
}

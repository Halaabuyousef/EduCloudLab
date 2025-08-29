<?php

namespace App\Providers;

use App\Models\Reservation;
use App\Observers\ReservationObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
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

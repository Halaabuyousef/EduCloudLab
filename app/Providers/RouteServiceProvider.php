<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });


        Route::macro('authGuard', function (string $prefix, string $name, string $guard, array $options = []) {

            Route::prefix($prefix)->controller(AuthController::class)->name($name . '.')->group(function () use ($guard, $options) {

                Route::middleware('guest:' . $guard)->group(function () use ($guard, $options) {
                    Route::get('login',  'indexLogin')->name('login')->defaults('guard', $guard);
                    Route::post('login',  'login')->name('login.submit')->defaults('guard', $guard);

                    if (!isset($options['register']) || $options['register'] !== false) {
                        Route::get('register',  'indexRegister')->name('register')->defaults('guard', $guard);
                        Route::post('register',  'register')->name('register.submit')->defaults('guard', $guard);
                    }

                    Route::get('forget-password',  'indexForgetPassword')->name('forget-password')->defaults('guard', $guard);
                    Route::post('forget-password', 'forgetPassword')->name('forget-password.submit')->defaults('guard', $guard);

                    Route::get('reset-password/{token}',  'showResetForm')->name('password.reset')->defaults('guard', $guard);
                    Route::post('reset-password', 'resetPassword')->name('password.update')->defaults('guard', $guard);


                    Route::get('/google', 'redirectToGoogle')
                        ->name('google.login')->defaults('guard', $guard);

                    Route::get('/google/callback', 'handleGoogleCallback')->defaults('guard', $guard);
                });
                Route::middleware(['verfied.guard:' . $guard, 'authin:' . $guard])->group(function () use ($guard) {
                    Route::post('logout', 'logout')->name('logout')->defaults('guard', $guard);
                    Route::get('dashboard', 'dashboard')->name('dashboard')->defaults('guard', $guard);
                });
            });
        });
    
    }
}

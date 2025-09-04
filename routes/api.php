<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\PrivacyController;
use App\Http\Controllers\Api\ExperimentController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Auth\UserAuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Api\Auth\SupervisorAuthController;


Route::prefix('v1/auth/users')->group(function () {
    Route::post('register', [UserAuthController::class, 'register']);
    Route::post('login',    [UserAuthController::class, 'login']);

    Route::get('email/verify/{id}/{hash}', [UserAuthController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('api.users.verify');

    Route::middleware(['auth:sanctum'])->group(function () {

        //تسجيل الخروج
        Route::post('logout',   [UserAuthController::class, 'logout']);
        //من انا
        Route::get('whoami',    [UserAuthController::class, 'whoami']);
        Route::post('email/resend', [UserAuthController::class, 'resendVerification']);
        
        Route::middleware('verified')->group(function () {
            //بروفايل
            Route::get('profile', [UserAuthController::class, 'showProfile'])
                ->name('api.users.profile.show');
            Route::match(['PUT', 'POST'],'profile', [UserAuthController::class, 'updateProfile'])
                ->name('api.users.profile.update');
            //تغيير كلمة المرور
            Route::put('profile/password', [UserAuthController::class, 'updatePassword'])
                ->name('api.users.password.update');
            //التجارب
            Route::get('experiments', [ExperimentController::class, 'index'])
                ->name('api.experiments.index');
            Route::get('experiments/{idOrSlug}', [ExperimentController::class, 'show'])
                ->name('api.experiments.show');
            Route::middleware(['auth:sanctum', 'experiment.session'])
                ->post('experiments/{experiment}/control', [DeviceController::class, 'handle']);
          //contact us
            Route::post('contact', [ContactController::class, 'store'])
                ->middleware('throttle:5,1')
                ->name('api.contact.store');
            //about us

            Route::get('about', [AboutController::class, 'show'])
                ->name('api.about.show');

            //privacy-policy
            Route::get('privacy-policy', [PrivacyController::class, 'privacy'])
                ->name('api.pages.privacy');
            //الحجوزات
            Route::post('reservations', [ReservationController::class, 'store'])
                ->name('ReservationCreatedNotification');
        });
        // الإشعارات
        Route::get('notifications', [NotificationController::class, 'index'])
            ->name('api.users.notifications.index');

        Route::post('notifications/{id}/read', [NotificationController::class, 'markRead'])
            ->name('api.users.notifications.read');

        Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])
            ->name('api.users.notifications.readAll');
      
    });
});

Route::prefix('v1/auth/supervisors')->group(function () {
    Route::post('register', [SupervisorAuthController::class, 'register']);
    Route::post('login',    [SupervisorAuthController::class, 'login']);
    Route::post('logout',   [SupervisorAuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('whoami',    [SupervisorAuthController::class, 'whoami'])->middleware('auth:sanctum');

    // Email verification for supervisors
    Route::post('email/resend', [SupervisorAuthController::class, 'resendVerification'])
        ->middleware('auth:sanctum');
    Route::get('email/verify/{id}/{hash}', [SupervisorAuthController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('api.supervisors.verify');
});



   


// Route::middleware(['auth:sanctum', 'throttle:api'])->get('/v1/whoami', function (Request $request) {
//     return response()->json([
//         'id' => $request->user()->id,
//         'name' => $request->user()->name,
//         'email' => $request->user()->email,
//         'roles' => $request->user()->getRoleNames() ?? [],
//     ]);
// });
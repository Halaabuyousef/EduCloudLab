<?php

use App\Models\Admin;
use App\Models\Experiment;
use App\Notifications\TestPing;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\TextMailController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExperimentController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\SupervisorController;
use App\Http\Controllers\Admin\UniversityController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\ReservationHoldController;
use App\Http\Controllers\Auth\EmailVerificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';


// Admin 

// Route::get('/{guard}/verify-email', [EmailVerificationController::class, 'verify'])->name('verification.verify')->where('guard', 'web|supervisor');

// Route::get('confirm', function () {
//     return view('auth.confirmation');
// })->name('con');



Route::prefix('admin')->name('admin.')->middleware(['guest:admin'])->group(function () {
    Route::get('login', [AuthController::class, 'indexLogin'])->name('indexLogin')->defaults('guard', 'admin');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit')->defaults('guard', 'admin');
});
Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('auth:admin');
    Route::get('experiments/trash', [ExperimentController::class, 'trash'])->name('experiments.trash');

    Route::post('experiments/{id}/restore',      [ExperimentController::class, 'restore'])->name('experiments.restore');
    Route::delete('experiments/{id}/force-delete', [ExperimentController::class, 'forceDelete'])->name('experiments.forceDelete');


    Route::resource('experiments', ExperimentController::class);

    Route::get('devices/trash', [DeviceController::class, 'trash'])->name('devices.trash');

    Route::post('devices/{id}/restore',      [DeviceController::class, 'restore'])->name('devices.restore');
    Route::delete('devices/{id}/force-delete', [DeviceController::class, 'forceDelete'])->name('devices.forceDelete');
    Route::resource('devices', DeviceController::class);

    Route::resource('reservations', ReservationController::class);


    Route::patch('/reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservations.updateStatus');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');


    Route::patch('reservations/{reservation}/postpone', [ReservationController::class, 'postpone'])->name('reservations.postpone');

    
    Route::patch('users/{user}/attach-supervisor', [UserController::class, 'attachSupervisor'])
        ->name('users.attachSupervisor');
    Route::patch('users/{user}/detach-supervisor', [UserController::class, 'detachSupervisor'])
        ->name('users.detachSupervisor');
    Route::get('users/trash', [AdminController::class, 'trash'])->name('users.trash');
    Route::post('users/{id}/restore', [AdminController::class, 'restore'])->name('users.restore');
    Route::delete('users/{id}/force-delete', [AdminController::class, 'forceDelete'])->name('users.forceDelete');
    Route::resource('users', UserController::class);

  
    Route::post('supervisors/{supervisor}/attach',                     [SupervisorController::class, 'attachMember'])->name('supervisors.attach');
    Route::delete('supervisors/{supervisor}/members/{user}',           [SupervisorController::class, 'detachMember'])->name('supervisors.detach');
    Route::get('supervisors/trash', [AdminController::class, 'trash'])->name('supervisors.trash');
    Route::post('supervisors/{id}/restore', [AdminController::class, 'restore'])->name('supervisors.restore');
    Route::delete('supervisors/{id}/force-delete', [AdminController::class, 'forceDelete'])->name('supervisors.forceDelete');

    Route::resource('supervisors', SupervisorController::class);



    Route::get('universities',              [UniversityController::class, 'index'])->name('universities.index');
    Route::post('universities',             [UniversityController::class, 'store'])->name('universities.store');
    Route::put('universities/{university}', [UniversityController::class, 'update'])->name('universities.update');
    Route::delete('universities/{university}', [UniversityController::class, 'destroy'])->name('universities.destroy');
    Route::get('universities/trash', [AdminController::class, 'trash'])->name('universities.trash');
    Route::post('universities/{id}/restore', [AdminController::class, 'restore'])->name('universities.restore');
    Route::delete('universities/{id}/force-delete', [AdminController::class, 'forceDelete'])->name('universities.forceDelete');
 
    Route::post('/reservations/holds', [ReservationHoldController::class, 'store'])->name('reservations.holds.store');
    Route::delete('/reservations/holds/{hold}', [ReservationHoldController::class, 'destroy'])->name('reservations.holds.destroy');
    Route::resource('reservations', \App\Http\Controllers\Admin\ReservationController::class);

    Route::get('/experiments/{experiment}/availability', [ReservationHoldController::class, 'availability'])
        ->name('experiments.availability');



    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::patch('/profile', [AdminController::class, 'profile_update'])->name('profile.update');
    Route::post('/profile/image', [AdminController::class, 'profile_image'])->name('profile_image');

    Route::get('/profile/password', [AdminController::class, 'profile_password'])->name('profile_password.edit');
    Route::post('/profile/password', [AdminController::class, 'profile_password_update']);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    // Route::resource('permissions', PermissionController::class, [
    //     'as' => 'admin'   // أسماء مثل admin.permissions.index ...
    // ])->parameters([
    //     'permissions' => 'permission'
    // ]);
 
    Route::get('admins/trash', [AdminController::class, 'trash'])->name('admins.trash');
    Route::post('admins/{id}/restore', [AdminController::class, 'restore'])->name('admins.restore');
    Route::delete('admins/{id}/force-delete', [AdminController::class, 'forceDelete'])->name('admins.forceDelete');
    Route::resource('admins', AdminController::class);


    Route::middleware('throttle:5,1')->group(function () {
        Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
        Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
    });
    Route::get('/admin/contacts', ContactMessageController::class)
        ->name('admin.contacts.index');
    Route::get('notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');




    Route::get('notifications',           [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read_all');
    Route::get('notifications/badge',     [NotificationController::class, 'badge'])->name('notifications.badge');
    Route::get('notifications/dropdown', [NotificationController::class, 'dropdown'])
        ->name('notifications.dropdown'); // يرجع HTML للقائمة

});
Route::post('admin/text-mails', [TextMailController::class, 'store'])->name('admin.text-mails.store');


// Route::get('/mail-test', function () {

//     $to = config('mail.contact_inbox') ?: config('mail.from.address');

//     if (!$to) {
//         abort(500, 'CONTACT_INBOX is not set. Please set it in .env and clear config cache.');
//     }

//     Mail::raw('This is a test email from Laravel via Gmail SMTP', function ($m) use ($to) {
//         $m->to($to)->subject('Gmail SMTP Test');
//     });

//     return '✅ Test email sent to ' . $to;
// });



Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
Route::get('/test-notification', function () {
    $admin = Admin::first(); // أو Auth::guard('admin')->user()
    $admin->notify(new TestPing('إشعار تجريبي من Route!'));
    return 'Notification sent!';
});
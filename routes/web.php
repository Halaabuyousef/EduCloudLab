<?php

use App\Models\Experiment;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\ExperimentController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\SupervisorController;
use App\Http\Controllers\Admin\UniversityController;
use App\Http\Controllers\Admin\ReservationController;
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
    Route::get('dashboard', [AuthController::class, 'dashboard'])->name('dashboard')->defaults('guard', 'admin');
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

    Route::resource('users', UserController::class);
    Route::patch('users/{user}/attach-supervisor', [UserController::class, 'attachSupervisor'])
        ->name('users.attachSupervisor');
    Route::patch('users/{user}/detach-supervisor', [UserController::class, 'detachSupervisor'])
        ->name('users.detachSupervisor');

    Route::resource('supervisors', SupervisorController::class);
    Route::post('supervisors/{supervisor}/attach',                     [SupervisorController::class, 'attachMember'])->name('supervisors.attach');
    Route::delete('supervisors/{supervisor}/members/{user}',           [SupervisorController::class, 'detachMember'])->name('supervisors.detach');

    Route::get('universities',              [UniversityController::class, 'index'])->name('universities.index');
    Route::post('universities',             [UniversityController::class, 'store'])->name('universities.store');
    Route::put('universities/{university}', [UniversityController::class, 'update'])->name('universities.update');
    Route::delete('universities/{university}', [UniversityController::class, 'destroy'])->name('universities.destroy');

    Route::post('/reservations/holds', [ReservationHoldController::class, 'store'])->name('reservations.holds.store');
    Route::delete('/reservations/holds/{hold}', [ReservationHoldController::class, 'destroy'])->name('reservations.holds.destroy');


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
});


Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

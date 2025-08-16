<?php

use App\Http\Controllers\Admin\DeviceController;
use App\Models\Experiment;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\ExperimentController;

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


// Route::get('/{guard}/verify-email', [EmailVerificationController::class, 'verify'])->name('verification.verify')->where('guard', 'web|freelancer');

// Route::get('confirm', function () {
//     return 'تحقق من البريد يا شاطر ';
// })->name('con');

// Admin 

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('dashboard', [AuthController::class, 'dashboard'])->name('dashboard')->defaults('guard', 'admin');


    Route::get('experiments/trash', [ExperimentController::class, 'trash'])->name('experiments.trash');

    Route::post('experiments/{id}/restore',      [ExperimentController::class, 'restore'])->name('experiments.restore');
    Route::delete('experiments/{id}/force-delete', [ExperimentController::class, 'forceDelete'])->name('experiments.forceDelete');
    
    // Route::get('experiments/getdata', [ExperimentController::class, 'getData'])
    //     ->name('experiments.getdata');

    Route::resource('experiments', ExperimentController::class);

    Route::get('devices/trash', [DeviceController::class, 'trash'])->name('devices.trash');

    Route::post('devices/{id}/restore',      [DeviceController::class, 'restore'])->name('devices.restore');
    Route::delete('devices/{id}/force-delete', [DeviceController::class, 'forceDelete'])->name('devices.forceDelete');
    Route::resource('devices', DeviceController::class);


   

});


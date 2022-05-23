<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::post('/payments/pay', [DashboardController::class, 'pay'])->middleware(['auth'])->name('pay');
Route::get('/payments/approval', [DashboardController::class, 'approval'])->middleware(['auth'])->name('approval');
Route::get('/payments/cancelled', [DashboardController::class, 'cancelled'])->middleware(['auth'])->name('cancelled');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'unsubscribed'])->prefix('subscribe')->name('subscribe.')->group(function () {
    Route::get('/', [SubscriptionController::class, 'show'])->name('show');
    Route::post('/', [SubscriptionController::class, 'store'])->name('store');
    Route::get('/approval', [SubscriptionController::class, 'approval'])->name('approval');
    Route::get('/cancelled', [SubscriptionController::class, 'cancelled'])->name('cancelled');
});

require __DIR__ . '/auth.php';

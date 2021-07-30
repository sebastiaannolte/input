<?php

use App\Http\Controllers\BetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\UserSettingController;
use App\Lib\GamesApi;
use App\Models\Bet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

require __DIR__ . '/auth.php';

Route::get('/', function () {
    if (Auth::user()) {
        return Redirect::route('userhome', Auth::user()->username);
    }
})->name('index')->middleware(['auth', 'verified']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/bet', [BetController::class, 'store'])->name('bet.store');
    Route::put('/bet', [BetController::class, 'update'])->name('bet.update');
    Route::put('/bet/status', [BetController::class, 'updateStatus'])->name('bet.updateStatus');
    Route::get('/bet/{id}', [BetController::class, 'show'])->name('bet.show');
    Route::delete('/bet/{id}', [BetController::class, 'delete'])->name('bet.delete');
    Route::get('/bet/{id}/edit', [BetController::class, 'edit'])->name('bet.edit');

    Route::get('{username}/stats', [StatsController::class, 'index'])->name('stats.index');
    Route::get('{username}/settings', [UserSettingController::class, 'index'])->name('userSettings.index');
    Route::post('{username}/settings', [UserSettingController::class, 'store'])->name('userSettings.store');
    Route::get('{username}/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('{username}/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/{username}', [BetController::class, 'index'])->name('userhome');
});

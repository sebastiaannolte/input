<?php

use App\Http\Controllers\BetController;
use App\Http\Controllers\StatsController;
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



Route::get('/', function () {
    if(Auth::user()){
        return Redirect::route('userhome', Auth::user()->username);
    }

})->name('index')->middleware(['auth', 'verified']);

Route::post('/bet', [BetController::class, 'store'])->name('bet.store');
Route::put('/bet', [BetController::class, 'update'])->name('bet.update');
Route::put('/bet/status', [BetController::class, 'updateStatus'])->name('bet.updateStatus');
Route::get('/bet/{id}', [BetController::class, 'show'])->name('bet.show');
Route::delete('/bet/{id}', [BetController::class, 'delete'])->name('bet.delete');
Route::get('/bet/{id}/edit', [BetController::class, 'edit'])->name('bet.edit');

Route::get('{username}/stats', [StatsController::class, 'index'])->name('stats.index');
Route::get('/stats/{key}', [StatsController::class, 'stats'])->name('stats.stats');

Route::get('/match/{matchId}', function ($matchId) {
    return GamesApi::match($matchId);
})->name('event.match');

Route::post('/details', [BetController::class, 'details'])->name('bet.details');


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/auth.php';

Route::get('/{username}', [BetController::class, 'index'])->name('userhome');

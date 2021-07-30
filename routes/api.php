<?php

use App\Http\Controllers\StatsController;
use App\Lib\GamesApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware(['auth'])->group(function () {
    Route::get('/match/{matchId}', function ($matchId) {
        return GamesApi::match($matchId);
    })->name('event.match');

    Route::get('/search/{search}', function ($search) {
        return GamesApi::search($search);
    })->name('event.search');

    Route::post('/stats', [StatsController::class, 'stats'])->name('stats.stats');
// });

<?php

use App\Http\Controllers\SpecialStatsController;
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

Route::middleware(['auth'])->group(function () {
    Route::get('/match/{matchId}', function ($matchId) {
        return GamesApi::match($matchId);
    })->name('event.match');

    Route::get('/search/{search}', function ($search) {
        return GamesApi::search($search);
    })->name('event.search');

    Route::get('/games/{date}', function ($search) {
        // dd($search);
        return GamesApi::get($search);
    })->name('games.get');

    Route::post('/stats', [StatsController::class, 'stats'])->name('stats.stats');
    Route::post('/comps', [SpecialStatsController::class, 'competitions'])->name('competitions.get');
    Route::post('/comp', [SpecialStatsController::class, 'competition'])->name('competition.get');
    Route::post('/special', [SpecialStatsController::class, 'specialStats'])->name('api.special');

    
});

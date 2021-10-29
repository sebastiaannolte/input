<?php

use App\Http\Controllers\SpecialStatsController;
use App\Http\Controllers\StatsController;
use App\Lib\GamesApi;
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

Route::middleware(['auth', 'isHost'])->group(function () {
    Route::get('/match/{id}', function ($id) {
        return GamesApi::match($id);
    })->name('event.match');

    Route::get('/search/{search}/{type}', function ($search, $type) {
        return GamesApi::search($search, $type);
    })->name('event.search');

    Route::get('/games/{date}', function ($search) {
        return GamesApi::get($search);
    })->name('games.get');

    Route::get('/find-bet/{id}', function ($id) {
        return GamesApi::findPageOfBet($id);
    })->name('bet.pageNumber');

    Route::post('/{username}/stats', [StatsController::class, 'stats'])->name('stats.stats');
    Route::post('/{username}/comps', [SpecialStatsController::class, 'competitions'])->name('competitions.get');
    Route::post('/{username}/comp', [SpecialStatsController::class, 'competitionStats'])->name('competition.get');
    Route::post('/{username}/special', [SpecialStatsController::class, 'specialStats'])->name('api.special');
});

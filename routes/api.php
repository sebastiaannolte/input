<?php

use App\Http\Controllers\SpecialStatsController;
use App\Http\Controllers\StatsController;
use App\Lib\GamesApi;
use App\Models\Import;
use Illuminate\Support\Facades\Request;
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

Route::middleware(['authKey'])->group(function () {
    Route::post('/import', function () {
        Import::create([
            'data' => json_encode(Request::all()),
        ]);
    })->name('import');
});

Route::middleware(['auth', 'isHost'])->group(function () {
    Route::get('/match/{id}', function ($id) {
        return GamesApi::match($id);
    })->name('event.match');

    Route::get('/search/{search}/{type}/{sport}', function ($search, $type, $sport) {
        return GamesApi::search($search, $type, $sport);
    })->name('event.search');

    Route::get('/games/{date}/{sport}', function ($search, $sport) {
        $dates = json_decode($search, true);
        $message = '';
        foreach ($dates as $date) {
           $response = GamesApi::get($date, $sport);
           $message .= key($response).': '.$response[key($response)].'<br>';
        }

        return response()->json(['message' => $message]);
    })->name('games.get');

    Route::get('/find-bet/{id}', function ($id) {
        return GamesApi::findPageOfBet($id);
    })->name('bet.pageNumber');

    Route::post('/{username}/stats', [StatsController::class, 'stats'])->name('stats.stats');
    Route::post('/{username}/comps', [SpecialStatsController::class, 'competitions'])->name('competitions.get');
    Route::post('/{username}/comp', [SpecialStatsController::class, 'competitionStats'])->name('competition.get');
    Route::post('/{username}/special', [SpecialStatsController::class, 'specialStats'])->name('api.special');
});

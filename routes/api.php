<?php

use App\Http\Controllers\SpecialStatsController;
use App\Http\Controllers\StatsController;
use App\Lib\BetHelper;
use App\Lib\Category;
use App\Lib\GamesApi;
use App\Models\Bet;
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
        $category = new Category();
        foreach (Request::all() as $item) {
            if (isset($item['bookieId']) && !empty($item['bookieId'])) {
                foreach ($item['games'] as &$game) {
                    $categoryId = $category->get($game['selection']);
                    if ($categoryId) {
                        $game['category'] = $categoryId;
                    }
                }
                // if bet_id, update, if bet_id not exist, create
                Import::updateOrCreate(
                    ['bookie_id' => $item['bookieId']],
                    ['data' => json_encode($item)]
                );
            } else {
                Import::create(['data' => json_encode($item)]);
            }
        }
    })->name('import');

    Route::post('/status-update', function () {
        foreach (Request::all() as $item) {
            $bet = Bet::where('bookie_id', $item['bookieId'])->first();
            if ($bet) {
               (new BetHelper)->updateStatus($bet, $item['status']);
            }
        }
    })->name('import');
});

Route::middleware(['auth', 'isHost'])->group(function () {
    Route::get('/match/{id}', function ($id) {
        return GamesApi::match($id);
    })->name('event.match');

    Route::get('/search/{search}/{type}/{sport}', function ($search, $type, $sport) {
        return GamesApi::search($search, $type, $sport);
    })->name('event.search');

    Route::post('/globalsearch', function () {
        return GamesApi::globalSearch();
    })->name('event.global-search');

    Route::get('/games/{date}/{sport}', function ($search, $sport) {
        ini_set('max_execution_time', '3000');
        $dates = json_decode($search, true);
        $message = '';
        foreach ($dates as $date) {
            $response = GamesApi::get($date, $sport);
            $message .= key($response) . ': ' . $response[key($response)] . '<br>';
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

<?php

use App\Http\Controllers\SpecialStatsController;
use App\Http\Controllers\StatsController;
use App\Lib\BetHelper;
use App\Lib\Category;
use App\Lib\GamesApi;
use App\Models\Bet;
use App\Models\Import;
use App\Models\Team;
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
        $total = 0;
        foreach (Request::all() as $item) {
            if (isset($item['bookieId']) && !empty($item['bookieId'])) {
                foreach ($item['games'] as &$game) {
                    $categoryId = $category->get($game['selection']);
                    if ($categoryId) {
                        $game['category'] = $categoryId;
                    }
                }

                $import = Import::where('bookie_id', $item['bookieId'])->first();
                if (!$import) {
                    Import::create(['bookie_id' => $item['bookieId'], 'data' => json_encode($item)]);
                    $total++;
                } else {
                    $import->update(['data' => json_encode($item)]);
                    $bet = Bet::where('bookie_id', $item['bookieId'])->first();
                    if ($bet) {
                        (new BetHelper)->updateStatus($bet, $item['status']);
                    }
                }
            } else {
                Import::create(['data' => json_encode($item)]);
            }
        }

        return response()->json(['message' => $total . ' bets imported']);
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

    Route::post('/search', function () {
        return GamesApi::search(Request::all());
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

    Route::post('teams', function () {
        $data = Request::all();
        return Team::where('sport', 'football')
            ->where('name', 'like', '%' . $data['query'] . '%')
            ->get();
    })->name('teams.get');

    Route::post('/{username}/stats', [StatsController::class, 'stats'])->name('stats.stats');
    Route::post('/{username}/comps', [SpecialStatsController::class, 'competitions'])->name('competitions.get');
    Route::post('/{username}/comp', [SpecialStatsController::class, 'competitionStats'])->name('competition.get');
    Route::post('/{username}/special', [SpecialStatsController::class, 'specialStats'])->name('api.special');
});

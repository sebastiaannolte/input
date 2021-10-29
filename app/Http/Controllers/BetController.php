<?php

namespace App\Http\Controllers;

use App\Lib\StatsHelper;
use App\Models\Bet;
use App\Models\BetFixture;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class BetController extends Controller
{
    public function index($username)
    {
        $user = User::where('username', $username)->first();

        if ($user === null) {
            abort(404);
        }
        $userId = $user->id;
        $filters = Request::get('filters');

        $bets = Bet::user($userId);
        $betStats = $bets->clone()->select((new StatsHelper)->statsSelect())->whereNotNull('result')->first();
        $allBets = $bets
            ->clone()
            ->bets()
            ->with('betFixture.fixture')
            ->filters($filters)
            ->paginate()
            ->withQueryString();

        $upcommintBets = Bet::joinBets()
            ->select('bets.stake', 'bets.odds', 'bets.type', 'result', 'bets.status', 'bets.id', 'bookie', 'sport', 'tipster', DB::raw('max(date) as date, GROUP_CONCAT(selection SEPARATOR ", ") as selection, GROUP_CONCAT(event SEPARATOR ", ") as event'))
            ->whereNull('result')
            ->orderBy('date')
            ->orderBy('bets.id')
            ->groupBy('bet_fixtures.bet_id', 'bets.stake',  'bets.odds', 'bets.type', 'result', 'bets.status', 'bets.id')
            ->get()
            ->take(3);

        return Inertia::render('Home', [
            'stats' => [
                'totalBets' => $betStats->bets,
                'wonbets' => $betStats->won,
                'winprecentage' => $betStats->bets ? round($betStats->won / $betStats->bets * 100, 2) : 0,
                'units' => round($betStats->profit, 2),
                'roi' => round($betStats->roi, 2),
                'username' => $username,
            ],
            'bets' => $allBets,
            'upcommingBets' => $upcommintBets,
            'filters' => $filters,
        ]);
    }

    public function store()
    {
        $games = collect(Request::get('games'));
        $statsHelper = new StatsHelper;

        Request::validate(
            [
                'bookie' => ['required', 'max:50'],
                'tipster' => ['required', 'max:50'],
                'sport' => ['required', 'max:50'],
                'type' => ['required', 'max:50'],
                'stake' => ['required', 'max:50'],
                'odds' => ['required', 'max:50'],
                //games
                'games.*.event' => ['required', 'max:50'],
                'games.*.selection' => ['required', 'max:50'],
                'games.*.date' => ['required', 'max:50'],
                'games.*.category' => ['required', 'max:50'],

            ],
            $statsHelper->betValidationTranslations(),
        );

        $bet = Bet::create([
            'bookie' => Request::get('bookie'),
            'tipster' => Request::get('tipster'),
            'sport' => Request::get('sport'),
            'type' => Request::get('type'),
            'stake' => Request::get('stake'),
            'odds' => Request::get('odds'),
            'user_id' => Auth::user()->id,
            'status' => 'new',
        ]);

        foreach ($games as $key => $game) {
            $event = $game['event']['event'];
            BetFixture::create([
                'bet_id' => $bet->id,
                'fixture_id' => is_int($event['value']) ? $event['value'] : null,
                'event' => $event['label'],
                'selection' => $game['selection'],
                'date' => $game['date'] ?: now(),
                'category' => json_encode($game['category']),
                'status' => 'new',
            ]);
        }


        return Redirect::back()->with('success', 'Bet created');
    }

    public function update()
    {

        $bet = Bet::find(Request::get('id'));
        $statsHelper = new StatsHelper;

        if (Request::user()->cannot('update', $bet)) {
            abort(403);
        }

        $games = collect(Request::get('games'));

        Request::validate(
            [
                'bookie' => ['required', 'max:50'],
                'tipster' => ['required', 'max:50'],
                'sport' => ['required', 'max:50'],
                'type' => ['required', 'max:50'],
                'stake' => ['required', 'max:50'],
                'odds' => ['required', 'max:50'],
                //games
                'games.*.selection' => ['required', 'max:50'],
                'games.*.date' => ['required', 'max:50'],
                'games.*.category' => ['required', 'max:50'],

            ],
            $statsHelper->betValidationTranslations(),
        );

        $bet->update([
            'bookie' => Request::get('bookie'),
            'tipster' => Request::get('tipster'),
            'sport' => Request::get('sport'),
            'type' => Request::get('type'),
            'stake' => Request::get('stake'),
            'odds' => Request::get('odds'),
            'user_id' => Auth::user()->id,
        ]);

        $betFixtureIds = $bet->betFixture->pluck('id');
        $gameIds = $games->pluck('id');
        $idsToDelete = $betFixtureIds->diff($gameIds);
        if (!$idsToDelete->isEmpty()) {
            $bet->betFixture()->whereIn('id', $idsToDelete)->delete();
        }

        foreach ($games as $key => $game) {
            $event = $game['event']['event'];

            $data = [
                'fixture_id' => is_int($event['value']) ? $event['value'] : null,
                'event' => $event['label'],
                'selection' => $game['selection'],
                'date' => $game['date'] ?: now(),
                'category' => json_encode($game['category']),
            ];

            if (array_key_exists('id', $game)) {
                $betFixture = BetFixture::find($game['id']);
                $betFixture->update($data);
            } else {
                $betFixture = new BetFixture;
                $data['bet_id'] = $bet->id;
                $data['status'] = 'new';
                $betFixture->create($data);
            }
        }

        return Redirect::route('bet.show', Request::get('id'))->with('success', 'Bet updated');
    }

    public function updateStatus()
    {
        if (Request::get('bet_id')) {
            $bet = BetFixture::find(Request::get('id'));
            $bet->update([
                'status' => Request::get('status')
            ]);
        } else {

            $bet = Bet::find(Request::get('id'));
            // update all fixtures
            $bet->betFixture()->update(['status' => Request::get('status')]);
            $bet->update([
                'result' => Request::get('result'),
                'status' => Request::get('status')
            ]);
        }

        // if (Request::user()->cannot('update', $bet)) {
        //     abort(403);
        // }



        return Redirect::back();
    }

    public function show($id)
    {
        $bet = Bet::bet($id)->first();

        if (Request::user()->cannot('view', $bet)) {
            abort(403);
        }

        return Inertia::render('Bet', [
            'bet' => $bet,
        ]);
    }

    public function delete(Bet $bet)
    {
        if (Request::user()->cannot('delete', $bet)) {
            abort(403);
        }

        $bet->delete();
        return Redirect::route('userhome', Auth::user()->username)->with('success', 'Bet deleted');;
    }
}

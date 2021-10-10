<?php

namespace App\Http\Controllers;

use App\Lib\StatsHelper;
use App\Models\Bet;
use App\Models\BetType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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

        return Inertia::render('Home', [
            'stats' => [
                'totalBets' => $betStats->bets,
                'wonbets' => $betStats->won,
                'winprecentage' => $betStats->bets ? round($betStats->won / $betStats->bets * 100, 2) : 0,
                'units' => round($betStats->profit, 2),
                'roi' => round($betStats->roi, 2),
                'username' => $username,
            ],

            'bets' => $bets->clone()->bets()->filters($filters)->paginate()->withQueryString(),
            'upcommingBets' => $bets->clone()->whereNull('result')->orderBy('date')->orderBy('id')->take(3)->get(),
            'filters' => $filters,
        ]);
    }

    public function store()
    {
        Request::validate([
            'event' => ['required', 'max:50'],
            'selection' => ['required', 'max:50'],
            'category' => ['required', 'max:50'],
            'bookie' => ['required', 'max:50'],
            'stake' => ['required', 'max:50'],
            'odds' => ['required', 'max:50'],
            'sport' => ['required', 'max:50'],
            'type' => ['required', 'max:50'],
        ]);

        Bet::create([
            'match_id' => Request::get('match_id'),
            'event' => Request::get('event')['label'],
            'selection' => Request::get('selection'),
            'category' => json_encode(Request::get('category')),
            'bookie' => Request::get('bookie'),
            'stake' => Request::get('stake'),
            'odds' => Request::get('odds'),
            'tipster' => Request::get('tipster'),
            'sport' => Request::get('sport'),
            'type' => Request::get('type'),
            'date' => (Request::get('date') ? Carbon::parse(Request::get('date')) : now()),
            'user_id' => Auth::user()->id,
            'status' => 'new',
        ]);

        return Redirect::back()->with('success', 'Bet created');;
    }

    public function update()
    {
        $bet = Bet::find(Request::get('id'));

        if (Request::user()->cannot('update', $bet)) {
            abort(403);
        }
        Request::validate([
            'event' => ['required', 'max:50'],
            'selection' => ['required', 'max:50'],
            'category' => ['required', 'max:50'],
            'bookie' => ['required', 'max:50'],
            'stake' => ['required', 'max:50'],
            'odds' => ['required', 'max:50'],
            'sport' => ['required', 'max:50'],
            'type' => ['required', 'max:50'],
        ]);

        $bet->update([
            'match_id' => Request::get('match_id'),
            'event' => Request::get('event')['label'],
            'selection' => Request::get('selection'),
            'category' => Request::get('category'),
            'bookie' => Request::get('bookie'),
            'stake' => Request::get('stake'),
            'odds' => Request::get('odds'),
            'tipster' => Request::get('tipster'),
            'sport' => Request::get('sport'),
            'type' => Request::get('type'),
            'date' => (Request::get('date') ? Carbon::parse(Request::get('date')) : now()),
            'user_id' => Auth::user()->id,
            'result' => Request::get('result'),
            'status' => Request::get('status')
        ]);

        return Redirect::route('bet.show', Request::get('id'))->with('success', 'Bet updated');;
    }

    public function updateStatus()
    {
        $bet = Bet::find(Request::get('id'));
        if (Request::user()->cannot('update', $bet)) {
            abort(403);
        }

        $bet->update([
            'result' => Request::get('result'),
            'status' => Request::get('status')
        ]);

        return Redirect::back();
    }

    public function show(Bet $bet)
    {
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

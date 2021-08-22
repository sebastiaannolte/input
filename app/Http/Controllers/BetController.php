<?php

namespace App\Http\Controllers;

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
            return;
        }
        $userId = $user->id;
        $filters = Request::get('filters');

        $bets = Bet::user($userId);
        return Inertia::render('Home', [
            'stats' => [
                'totalBets' => $bets->clone()->count(),
                'wonbets' => $bets->clone()->wonBets(),
                'winprecentage' => $bets->clone()->winprecentage(2),
                'units' => $bets->clone()->units(2),
                'roi' => $bets->clone()->roi(2),
                'username' => $username,
            ],
            'bets' => $bets->clone()->bets()->filters($filters)->paginate()->withQueryString(),
            'upcommingBets' => $bets->clone()->whereNull('result')->orderBy('date')->take(3)->get(),
            'filters' => $filters,
            'betTypes' => BetType::get(),
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
        // dd(Request::all());
        Bet::create([
            'match_id' => Request::get('match_id'),
            'event' => Request::get('event'),
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

        return Redirect::route('userhome', Auth::user()->username)->with('success', 'Bet created');;
    }

    public function update()
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

        Bet::updateOrCreate(
            [
                'id' => Request::get('id')
            ],
            [
                'match_id' => Request::get('match_id'),
                'event' => Request::get('event'),
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
            ]
        );

        return Redirect::route('bet.show', Request::get('id'))->with('success', 'Bet updated');;
    }

    public function updateStatus()
    {
        Bet::updateOrCreate(
            [
                'id' => Request::get('id')
            ],
            [
                'result' => Request::get('result'),
                'status' => Request::get('status')
            ]
        );

        return Redirect::route('userhome', Auth::user()->username);
    }

    public function show($id)
    {
        $bet = Bet::find($id);

        return Inertia::render('Bet', [
            'bet' => $bet,
            'betTypes' => BetType::get()->mapWithKeys(function ($item) {
                return [$item->id => $item->name];
            }),
        ]);
    }

    public function edit($id)
    {
        $bet = Bet::find($id);
        return Inertia::render('BetEdit', [
            'bet' => $bet,
            'betTypes' => BetType::get(),
        ]);
    }

    public function delete($id)
    {
        Bet::find($id)->delete();
        return Redirect::route('userhome', Auth::user()->username)->with('success', 'Bet deleted');;
    }
}

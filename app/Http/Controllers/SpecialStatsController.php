<?php

namespace App\Http\Controllers;

use App\Lib\GamesApi;
use App\Lib\Stats;
use App\Lib\StatsHelper;
use App\Models\League;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class SpecialStatsController extends Controller
{
    public function competition($username, $id)
    {
        $filters = Request::get('filters');
        $sort = Request::get('sort') ?: [
            'sortType' => "Bets",
            'sortOrder' => "DESC"
        ];

        return Inertia::render('Competition', [
            'competition' => League::find($id),
            'sort' => $sort,
            'filters' => $filters,
        ]);
    }

    public function team($username, $teamId, $leagueId = false)
    {
        $userId = User::where('username', $username)->first()->id;
        $filters = Request::get('filters');
        $stats = new Stats($userId, $filters);
        $teamTable = $stats->teamTable($teamId, $leagueId);

        return Inertia::render('Team', [
            'bets' => $teamTable,
            'team' => Team::find($teamId),
            'league' => $leagueId ? League::find($leagueId) : '',
            'filters' => $filters,
        ]);
    }

    public function competitionStats($username)
    {
        $competitionId = Request::get('competition');
        $filters = Request::get('filters');
        $sort = Request::get('sort');
        $userId = User::where('username', $username)->first()->id;
        $stats = new Stats($userId, $filters, $sort);
        return $stats->competitionTable($competitionId);
    }

    public function special($username)
    {
        $statsHelper = new StatsHelper;
        $filters = Request::get('filters');
        $type = Request::get('type');
        $sort = Request::get('sort') ?: [
            'sortType' => "Bets",
            'sortOrder' => "DESC"
        ];

        $tabs = $statsHelper->getSpecialStatsTabs();

        return Inertia::render('SpecialStats', [
            'tabs' => $tabs,
            'type' => $type,
            'filters' => $filters,
            'sort' => $sort,
        ]);
    }

    public function specialStats($username)
    {
        $userId = User::where('username', $username)->first()->id;
        $filters = Request::get('filters');
        $key = Request::get('key');
        $sort = Request::get('sort');
        $stats = new Stats($userId, $filters, $sort);
        return $stats->getSpecialStats($key, $sort);
    }

    public function referee($username, $referee)
    {
        $userId = User::where('username', $username)->first()->id;
        $filters = Request::get('filters');
        $stats = new Stats($userId, $filters);
        $bets = $stats->refereeBets($referee);

        return Inertia::render('Table', [
            'bets' => $bets,
            'type' => ['name' => $referee, 'id' => $referee],
            'filters' => $filters,
            'filterRoute' => 'referee',
            'title' => 'referee',
        ]);
    }

    public function venue($username, $venue)
    {
        $userId = User::where('username', $username)->first()->id;
        $filters = Request::get('filters');
        $stats = new Stats($userId, $filters);
        $bets = $stats->venueBets($venue);
        return Inertia::render('Table', [
            'bets' => $bets,
            'type' => Venue::find($venue),
            'filters' => $filters,
            'filterRoute' => 'venue',
            'title' => 'venue',
        ]);
    }

    public function competitionBets($username, $leagueId)
    {
        $userId = User::where('username', $username)->first()->id;
        $filters = Request::get('filters');
        $stats = new Stats($userId, $filters);
        $bets = $stats->competitionBets($leagueId);
        return Inertia::render('Table', [
            'bets' => $bets,
            'type' => League::find($leagueId),
            'filters' => $filters,
            'filterRoute' => 'competition.bets',
            'title' => 'competition',
        ]);
    }
}

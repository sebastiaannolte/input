<?php

namespace App\Http\Controllers;

use App\Lib\GamesApi;
use App\Lib\Stats;
use App\Models\League;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class SpecialStatsController extends Controller
{
    public function show($username, $id)
    {
        return Inertia::render('Competition', [
            'competition' => League::find($id),
        ]);
    }

    public function team($username, $teamId)
    {
        $filters = Request::get('filters');

        $stats = new Stats(1, $filters);
        $teamTable = $stats->teamTable($teamId);
        $showFilter = Request::get('showFilter') === 'true' ? true : false;

        return Inertia::render('Team', [
            'teamTable' => $teamTable,
            'team' => Team::find($teamId),
            'filters' => $filters,
            'showFilter' => $showFilter,
        ]);
    }

    // public function competitions()
    // {
    //     $filters = Request::get('filters');

    //     $userId = Auth::user()->id;
    //     $stats = new Stats($userId, $filters);
    //     return $stats->competitionsTable();
    // }

    public function competition()
    {
        $competitionId = Request::get('competition');
        $filters = Request::get('filters');

        $userId = Auth::user()->id;
        $stats = new Stats($userId, $filters);
        return $stats->competitionTable($competitionId);
    }

    public function special($username)
    {
        $filters = Request::get('filters');
        $showFilter = Request::get('showFilter') === 'true' ? true : false;
        $type = Request::get('type');

        return Inertia::render('SpecialStats', [
            'tabs' => ['referee', 'venue', 'competitions'],
            'type' => $type,
            'filters' => $filters,
            'showFilter' => $showFilter,

        ]);
    }

    public function specialStats()
    {
        $filters = Request::get('filters');
        $key = Request::get('key');
        $sort = Request::get('sort');

        $userId = Auth::user()->id;
        $stats = new Stats($userId, $filters);
        return $stats->getSpecialStats($key, $sort);
    }

    public function referee($username, $referee)
    {
        $filters = Request::get('filters');

        $stats = new Stats(1, $filters);
        $teamTable = $stats->refereeBets($referee);
        $showFilter = Request::get('showFilter') === 'true' ? true : false;

        return Inertia::render('Referee', [
            'refereeBets' => $teamTable,
            'referee' => $referee,
            'filters' => $filters,
            'showFilter' => $showFilter,
        ]);
    }

    public function venue($username, $venue)
    {
        $filters = Request::get('filters');

        $stats = new Stats(1, $filters);
        $venueBets = $stats->venueBets($venue);
        $showFilter = Request::get('showFilter') === 'true' ? true : false;
        return Inertia::render('Venue', [
            'venueBets' => $venueBets,
            'venue' => Venue::find($venue),
            'filters' => $filters,
            'showFilter' => $showFilter,
        ]);
    }
}

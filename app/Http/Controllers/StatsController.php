<?php

namespace App\Http\Controllers;

use App\Lib\Stats;
use App\Models\Bet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Inertia\Inertia;

class StatsController extends Controller
{
    public function index($username)
    {
        $userId = User::where('username', $username)->first()->id;
        $filters = FacadesRequest::get('filters');
        $stats = new Stats($userId, $filters);
        $showFilter = FacadesRequest::get('showFilter') === 'true' ? true : false;

        $bets = Bet::user($userId)->filters($filters);
        return Inertia::render('Stats', [
            'tabs' => $stats->getTabs(),
            'filters' => $filters,
            'showFilter' => $showFilter,
            'stats' => [
                'roi' => $bets->clone()->roi(2),
                'bets' => $bets->clone()->betCount(),
                'totalStake' => $bets->clone()->totalStaked(),
                'winRate' => $bets->clone()->winprecentage(2),

                'profit' => $bets->clone()->units(2),
                'avgStake' => $bets->clone()->avgStake(2),
                'avgOdds' => $bets->clone()->avgOdds(2),
                'avgResult' => $bets->clone()->avgResult(2),
            ],
        ]);
    }

    public function calculateInterval($start, $end)
    {
        $diff = $start->diffInDays($end);

        $intervals = [
            '1 day' => [
                'value' => '40',
                'days' => [
                    'function' => 'addDay',
                    'format' => 'Y-m-d',
                ],
            ],
            '1 month' => [
                'value' => '365',
                'days' => [
                    'function' => 'addMonth',
                    'format' => 'Y-m',
                ],
            ],
            '1 year' => [
                'value' => '2333232',
                'days' => [
                    'function' => 'addYear',
                    'format' => 'Y',
                ],
            ],
        ];
        foreach ($intervals as $key => $interval) {
            if ($diff <= $interval['value']) {
                return [$key => $intervals[$key]];
            }
        }
    }

    public function stats(Request $request)
    {
        $key = $request->get('key');
        $filters = $request->get('filters');

        $start = now()->subDays(200)->startOfDay();
        $end = now();

        $defaultFilters = [
            'from' => [
                'value' => now()->subDays(200)->startOfDay(),
                'type' => 'min',
                'col' => 'date',
            ],
            'to' => [
                'value' =>  now(),
                'type' => 'max',
                'col' => 'date',
            ],
            'interval' => $this->calculateInterval($start, $end),
        ];

        $filters = array_merge($filters, $defaultFilters);
        $userId = 1;
        $stats = new Stats($userId, $filters);
        return $stats->renderStats($key, $request);
    }
}

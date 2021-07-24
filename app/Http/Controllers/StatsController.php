<?php

namespace App\Http\Controllers;

use App\Lib\Stats;
use App\Models\Bet;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StatsController extends Controller
{

    public function index($username)
    {
        $userId = User::where('username', $username)->first()->id;
        $start = now()->subDays(200)->startOfDay();
        $end = now();
        $interval = $this->calculateInterval($start, $end);
        $stats = new Stats($userId, $start, $end, $interval);


        $bets = Bet::user($userId);
        return Inertia::render('Stats', [
            // 'graphs' =>
            // $stats->renderStats(),
           
            'tabs' => $stats->getTabs(),
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

    public function stats($key)
    {
        // $userId = Auth::user()->id;
        $userId = 1;
        $start = now()->subDays(200)->startOfDay();
        $end = now();
        $interval = $this->calculateInterval($start, $end);
        $stats = new Stats($userId, $start, $end, $interval);
        return $stats->renderStats($key);
    }
}

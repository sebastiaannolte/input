<?php

namespace App\Http\Controllers;

use App\Lib\Stats;
use App\Models\Bet;
use App\Models\User;
use Carbon\Carbon;
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

        $bets = Bet::user($userId)->filters($filters);
        return Inertia::render('Stats', [
            'tabs' => $stats->getTabs(),
            'filters' => $filters,
            'stats' => [
                'roi' => $bets->clone()->roi(2),
                'bets' => $bets->clone()->betCount(),
                'totalStake' => $bets->clone()->totalStaked(2),
                'winRate' => $bets->clone()->winprecentage(2),
                'profit' => $bets->clone()->units(2),
                'avgStake' => $bets->clone()->avgStake(2),
                'avgOdds' => $bets->clone()->avgOdds(2),
                'avgOddsStake' => $bets->clone()->avgOddsStake(2),
            ],
        ]);
    }

    public function calculateInterval($start, $end)
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
        $diff = $start->diffInDays($end);

        $intervals = [
            '1 day' => [
                'value' => '40',
                'formatting' => [
                    'format' => 'Y-m-d',
                    'select' => 'DATE_FORMAT(`date`, "%Y-%m-%d") as formatted_date'
                ],
            ],
            '1 month' => [
                'value' => '365',
                'formatting' => [
                    'format' => 'Y-m',
                    'select' => 'DATE_FORMAT(`date`, "%Y-%m") as formatted_date'
                ],
            ],
            '1 year' => [
                'value' => '2333232',
                'formatting' => [
                    'format' => 'Y',
                    'select' => 'DATE_FORMAT(`date`, "%Y") as formatted_date'
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

        $start = now()->subMonths(5)->startOfMonth()->format('Y-m-d H:i:s');
        $end = now()->format('Y-m-d H:i:s');

        $defaultFilters = [
            'from' => [
                'value' => $start,
                'type' => 'min',
                'col' => 'date',
            ],
            'to' => [
                'value' => $end,
                'type' => 'max',
                'col' => 'date',
            ],
        ];
        
        if (!$filters['from']['value']) {
            $filters['from'] = $defaultFilters['from'];
        }

        if (!$filters['to']['value']) {
            $filters['to'] = $defaultFilters['to'];
        }
        $filters['interval'] = $this->calculateInterval($filters['from']['value'], $filters['to']['value']);

        $userId = 1;
        $stats = new Stats($userId, $filters);
        return $stats->renderStats($key, $request);
    }
}

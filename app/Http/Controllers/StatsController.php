<?php

namespace App\Http\Controllers;

use App\Lib\Stats;
use App\Lib\StatsHelper;
use App\Models\Bet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Inertia\Inertia;

class StatsController extends Controller
{
    public function index(Request $request, $username)
    {
        $statsHelper = new StatsHelper;
        $userId = User::where('username', $username)->first()->id;
        $filters = FacadesRequest::get('filters');
        $tabs = $statsHelper->getStatsTabs();
        $type = FacadesRequest::get('type') ?? $tabs[0];
        $sort = FacadesRequest::get('sort') ?: [
            'sortType' => "Bets",
            'sortOrder' => "DESC"
        ];

        $request->merge([
            'type' => $type,
            'userId' => $userId,
            'sort' => $sort
        ]);
        
        $bets = Bet::user($userId)->filters($filters);
        $betStats = $bets->clone();


        $betStats = $betStats->clone()->select((new StatsHelper)->statsSelect(), (new StatsHelper)->advancedStats())->whereNotNull('result')->first();

        return Inertia::render('Stats', [
            'tabs' => $tabs,
            'filters' => $filters,
            'type' => $type,
            'sort' => $sort,
            'stats' => $this->stats($username, $request),
            'generalStats' => [
                'totalBets' => $betStats->bets,
                'wonbets' => $betStats->won,
                'units' => round($betStats->profit, 2),
                'roi' => round($betStats->roi, 2),
                'bets' => $betStats->bets,
                'totalStake' => $betStats->staked,
                'winRate' => $betStats->bets ? round($betStats->won / $betStats->bets * 100, 2) : 0,
                'profit' =>  round($betStats->profit, 2),
                'avgStake' => round($betStats->avgStake, 2),
                'avgOdds' => round($betStats->avgOdds, 2),
                'avgOddsStake' => round($betStats->avgOddsStake, 2),
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
                    'select' => 'DATE_FORMAT(bet_fixtures.date, "%Y-%m") as formatted_date'
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

    public function stats($username, Request $request)
    {
        $userId = $request->get('userId');
        $type = $request->get('type');
        $filters = $request->get('filters');
        $sort = $request->get('sort');

        $start = now()->subMonths(5)->startOfMonth()->format('Y-m-d H:i:s');
        $end = now()->format('Y-m-d H:i:s');


        $defaultFilters = [
            'from' => [
                'value' => $start,
                'type' => 'min',
                'col' => 'date',
                'specialType' => 'date',
            ],
            'to' => [
                'value' => $end,
                'type' => 'max',
                'col' => 'date',
                'specialType' => 'date',
            ],
        ];

        if (!$filters || !array_key_exists('from', $filters) || !$filters['from']['value']) {
            $filters['from'] = $defaultFilters['from'];
        }

        if (!$filters || !array_key_exists('to', $filters) || !$filters['to']['value']) {
            $filters['to'] = $defaultFilters['to'];
        }
        $filters['interval'] = $this->calculateInterval($filters['from']['value'], $filters['to']['value']);
        
        $stats = new Stats($userId, $filters, $sort);
        // dd($stats->renderStats($type)[$type]);
        return $stats->renderStats($type)[$type];
    }
}

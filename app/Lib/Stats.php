<?php

namespace App\Lib;

use App\Models\Bet;
use App\Models\BetFixture;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Stats
{
    protected $userId = null;
    protected $odds = null;
    protected $filters = null;
    protected $sort = null;
    protected $statsHelper = null;

    public function __construct($userId, $filters, $sort = false)
    {
        $this->userId = $userId;
        $this->filters = $filters;
        $this->sort = $sort;
        $this->statsHelper = (new StatsHelper);
    }

    public function oddsGraph()
    {
        $type = 'odds';
        $carbonDates = CarbonPeriod::create($this->filters['from']['value'], key($this->filters['interval']), $this->filters['to']['value']);
        $dateSelect = $this->dateSelect();

        $query = Bet::select(DB::raw("bets.id, stake, bets.status, odds as odd, case
        when odds > 0 and odds <= 1.5 then '< 1.5'
        when odds > 1.5 and odds <= 1.75 then '1.5-1.75'
        when odds > 1.75 and odds <= 2 then '1.75-2'
        when odds > 2 and odds <= 2.25 then '2-2.25'
        when odds > 2.25 and odds <= 2.50 then '2-2.25'
        when odds > 2.50 and odds <= 2.75 then '2.50-2.75'
        else '2.75+'
        end AS odds"),  $this->statsHelper->statsSelect(), $dateSelect['select'])
            ->filters($this->filters)
            ->joinBets()
            ->user($this->userId)
            ->groupBy('formatted_date', 'bets.id');

        $bets = DB::table(DB::raw("({$query->toSql()}) as bets"))
            ->mergeBindings($query->getQuery())
            ->leftJoin('bet_fixtures', 'bets.id', '=', 'bet_fixtures.bet_id')
            ->select(DB::raw('count(bets.id), odds, formatted_date'), $this->statsHelper->statsSelect())
            ->groupBy('odds', 'formatted_date')
            ->orderBy($this->sort['sortType'], $this->sort['sortOrder'])
            ->get();
        
        $labels = [];
        foreach ($carbonDates as $key => $date) {
            foreach ($bets as $key => $odd) {
                $labels[$date->format($dateSelect['format'])][$odd->$type] = 0;
            }
        }

        foreach ($bets as $key => $value) {
            $date = $value->formatted_date;
            $key = $value->$type;
            $labels[$date][$key] = $value->profit;
        }

        $table = $this->oddsTable();

        $vals = [];
        foreach ($labels as $date => $values) {
            foreach ($values as $key => $value) {
                $vals[$key][] = $value;
            }
        }

        return [
            'labels' => array_keys($labels),
            'values' => $vals,
            'table' => $table,
        ];
    }


    public function createGraphAndTable($type)
    {
        return $this->simpleGraph($type);
    }

    public function simpleGraph($column)
    {
        $type = $column;
        $carbonDates = CarbonPeriod::create($this->filters['from']['value'], key($this->filters['interval']), $this->filters['to']['value']);
        $dateSelect = $this->dateSelect();

        $bets = Bet::
            joinBets();

        $columnsTable = $bets
            ->clone()
            ->select($type, $this->statsHelper->statsSelect())
            ->whereNotNull($type)
            ->groupBy($type)
            ->orderBy($this->sort['sortType'], $this->sort['sortOrder'])
            ->get();

        $columns = $bets
            ->clone()
            ->select($type,  $dateSelect['select'], $this->statsHelper->statsSelect())
            ->whereNotNull($type)
            ->groupBy($type, 'formatted_date')
            ->orderBy($this->sort['sortType'], $this->sort['sortOrder'])
            ->get();

        $labels = [];

        foreach ($carbonDates as $key => $date) {
            foreach ($columns as $columnValue) {
                $labels[$date->format($dateSelect['format'])][(string)$columnValue->$type] = 0;
            }
        }

        foreach ($columns as $key => $value) {
            $date = $value->formatted_date;
            $labels[$date][(string)$value->$type] = $value->profit;
        }

        $table = $this->simpleTable($type, $columnsTable);

        $vals = [];
        foreach ($labels as $date => $values) {
            foreach ($values as $key => $value) {
                $vals[ucfirst($key)][] = $value;
            }
        }

        return [
            'labels' => array_keys($labels),
            'values' => $vals,
            'table' => $table,
        ];
    }

    public function profitPerDayGraph()
    {
        $carbonDates = CarbonPeriod::create(Carbon::parse(Auth::user()->created_at), '1 day', now()->endOfDay());

        $labels = [];
        $columns = [
            'Profit per day',
            'Total profit',
        ];

        $bets = Bet::user($this->userId)
            // ->filters($this->filters)
            ->joinBets()
            ->groupBy('formatted_date')
            ->select([
                DB::raw("DATE_FORMAT(bet_fixtures.date, '%Y-%m-%d') as formatted_date"),
                $this->statsHelper->statsSelect()

            ])
            ->orderBy('formatted_date')->get()->mapWithKeys(function ($values) {
                return [$values->formatted_date => $values];
            });

        foreach ($carbonDates as $key => $date) {
            foreach ($columns as $columnValue) {
                $labels[$date->format('Y-m-d')][(string)$columnValue] = 0;
            }
        }


        $totalProfit = 0;
        foreach ($labels as $date => $values) {
            $dateExists = $bets->has($date);
            $totalProfit += $dateExists ? $bets[$date]->profit : 0;
            $labels[$date]['Profit per day'] = $dateExists ? $bets[$date]->profit : 0;
            $labels[$date]['Total profit'] = $totalProfit;
        }

        foreach ($labels as $date => $values) {
            if ($date < now()->subMonth()) {
                unset($labels[$date]);
            }
        }

        $vals = [];
        foreach ($labels as $date => $values) {
            foreach ($values as $key => $value) {
                $vals[$key][] = $value;
            }
        }

        return [
            'labels' => array_keys($labels),
            'values' => $vals,
            'table' => [],
        ];
    }

    public function getFormattedDate($date, $i)
    {
        $date = Carbon::parse($date);
        $formattedDate = $date->format('F');
        if ($i == 0 || $date->copy()->startOfYear() == $date) {
            $formattedDate = $date->format('F Y');
        }
        return $formattedDate;
    }

    public function selectionGraph()
    {
        $type = 'selection';
        $carbonDates = CarbonPeriod::create($this->filters['from']['value'], key($this->filters['interval']), $this->filters['to']['value']);
        $dateSelect = $this->dateSelect();

        $bets = Bet::joinAllBets()->leftJoin('bet_types', function ($join) {
            $join->on('category', 'bet_types.id');
        })
            ->whereRaw('category <> ""')
            ->whereNotNull('bets.result')
            ->filters($this->filters)
            ->user($this->userId);

        $selections = $bets
            ->clone()
            ->select([
                'bet_types.id',
                DB::raw('bet_types.name as selection'),
                $this->statsHelper->statsSelect('')
            ])->orderBy($this->sort['sortType'], $this->sort['sortOrder'])
            ->groupBy('bet_types.id')
            ->whereNotNull('name')
            ->get();


        $selectionsGraph = $bets
            ->clone()
            ->select([
                'bet_types.id',
                DB::raw('bet_types.name as selection'),
                $dateSelect['select'],
                $this->statsHelper->statsSelect()
            ])->orderBy($this->sort['sortType'], $this->sort['sortOrder'])
            ->groupBy('bet_types.id', 'formatted_date')
            ->whereNotNull('name')
            ->get();

        $labels = [];
        foreach ($carbonDates as $key => $date) {
            foreach ($selectionsGraph as $key => $selection) {
                $labels[$date->format($dateSelect['format'])][$selection->$type] = 0;
            }
        }

        foreach ($selectionsGraph as $key => $value) {
            $date = $value->formatted_date;
            $labels[$date][$value->$type] = $value->profit;
        }

        $table = $this->selectionTable($selections);

        $vals = [];
        foreach ($labels as $date => $values) {
            foreach ($values as $key => $value) {
                $vals[$key][] = $value;
            }
        }

        return [
            'labels' => array_keys($labels),
            'values' => $vals,
            'table' => $table,
        ];
    }

    public function tableHeader($type)
    {
        $headerItems = [
            $type,
            'Bets',
            'Won',
            'Staked',
            'Profit',
            'ROI',
        ];

        return $headerItems;
    }

    public function tableBodyRendered($typeValue, $output, $type, $specialId = '')
    {
        $output = [
            $type => ['value' => $typeValue, 'type' => '', 'specialId' => $specialId],
            'Bets' => ['value' => $output->bets, 'type' => ''],
            'Won' => ['value' => $output->won, 'type' => ''],
            'Staked' => ['value' => round($output->staked, 2), 'type' => ''],
            'Profit' => ['value' => round($output->profit, 2), 'type' => ''],
            'ROI' => ['value' => round($output->roi, 2), 'type' => '%'],
        ];

        return $output;
    }

    public function customPaginationOutput($head, $body, $collection)
    {
        return ['table' => ['head' => $head, 'body' => array_values($body)], 'totalResults' => $collection->total(), 'perPage' => $collection->perPage()];
    }

    public function simpleTable($column, $types)
    {
        $type = $column;
        $head = $this->tableHeader($type);
        $output = [];
        foreach ($types as $key => $typeValue) {
            $bets = $typeValue;
            $typeValue = ucfirst($typeValue->$type);
            $output[$typeValue] = $this->tableBodyRendered($typeValue, $bets, $type);
        }

        return ['head' => $head, 'body' => array_values($output)];
    }

    public function selectionTable($selections)
    {
        $type = 'selection';
        $head = $this->tableHeader($type);

        $output = [];
        foreach ($selections as $key => $selection) {
            $key = $selection->$type;
            $output[$key] = $this->tableBodyRendered($key, $selection, $type);
        }

        return ['head' => $head, 'body' => array_values($output)];
    }


    public function oddsTable()
    {
        $type = 'odds';
        $head = $this->tableHeader($type);
        $bets = Bet::filters($this->filters)
            ->completedBets()
            ->user($this->userId);

        $query = $bets->select(DB::raw("bets.id, bets.status, stake, odds as odd, case
        when odds > 0 and odds <= 1.5 then '< 1.5'
        when odds > 1.5 and odds <= 1.75 then '1.5-1.75'
        when odds > 1.75 and odds <= 2 then '1.75-2'
        when odds > 2 and odds <= 2.25 then '2-2.25'
        when odds > 2.25 and odds <= 2.50 then '2-2.25'
        when odds > 2.50 and odds <= 2.75 then '2.50-2.75'
        else '2.75+'
        end AS odds"),  $this->statsHelper->statsSelect())

            ->groupBy('id', 'bet_fixtures.date');


        $bets = DB::table(DB::raw("({$query->toSql()}) as bets"))
            ->mergeBindings($query->getQuery())
            ->select(DB::raw('count(bets.id), odds'), $this->statsHelper->statsSelect())
            ->orderBy($this->sort['sortType'], $this->sort['sortOrder'])
            ->groupBy($type);

        $output = [];
        foreach ($bets->get() as $typeValue => $odd) {
            $typeValue = $odd->$type;
            $output[$typeValue] = $this->tableBodyRendered($typeValue, $odd, $type);
        }

        return ['head' => $head, 'body' => array_values($output)];
    }

    public function renderStats($key)
    {
        if ($key == 'odds') {
            $current = $this->oddsGraph();
        } elseif ($key == 'selection') {
            $current =  $this->selectionGraph();
        } elseif ($key == 'profit') {
            $current = $this->profitPerDayGraph();
        } else {
            $current =  $this->createGraphAndTable($key);
        }

        $colors = [
            "#d2ab00",
            "#4f00c2",
            "#bfff4e",
            "#c130ff",
            "#00ff97",
            "#e000cc",
            "#005a00",
            "#ad0091",
            "#00f1dd",
            "#cc0000",
            "#00fff8",
            "#ce0042",
            "#00e6ff",
            "#ff3771",
            "#00d9ff",
            "#da7600",
            "#0081ff",
            "#ffd75c",
            "#0047c0",
            "#7cc671",
            "#ff9aff",
            "#004c00",
            "#ffa7ff",
            "#004100",
            "#ff7ec8",
            "#004000",
            "#ffa2e9",
            "#003700",
            "#ff6993",
            "#004d22",
            "#416cd2",
            "#ff7756",
            "#00a6ff",
            "#720000",
            "#00cff9",
            "#6b0006",
            "#00aeff",
            "#2f0800",
            "#afe8ff",
            "#550132",
            "#3e996f",
            "#3f9fff",
            "#00462f",
            "#ffc9ff",
            "#002e36",
            "#bdbcd3",
            "#202b2b",
            "#00b0e8",
            "#004184",
            "#006285"
        ];

        $datasets = [];
        $i = 0;
        foreach ($current['values'] as $key1 => $value) {
            $datasets[] = [
                'backgroundColor' => $colors[$i],
                'pointHitRadius' => 20,
                'borderColor' => $colors[$i],
                'data' => $value,
                'fill' => false,
                'label' => $key1,
            ];
            $i++;
        }

        $graphs[$key]['graph'] = [
            'type' => "line",
            'data' => [
                'labels' => $current['labels'],
                'datasets' => $datasets

            ],

            'options' => [
                'maintainAspectRatio' => false,
                'interaction' => [
                    'mode' => "index",
                    'intersect' => "true",
                ],

                'animation' => [
                    'duration' => '0',
                ]
            ],
        ];

        $graphs[$key]['table'] = $current['table'];

        return $graphs;
    }

    public function competitionsTable()
    {
        $type = 'country/competition';
        $head = $this->tableHeader($type);

        $bets = Bet::user($this->userId)
            ->filters($this->filters);
        $types = $bets->clone()
            ->join('bet_fixtures', 'bets.id', '=', 'bet_fixtures.bet_id')
            ->join('fixtures', 'bet_fixtures.fixture_id', '=', 'fixtures.id')
            ->join('leagues', 'fixtures.league_id', '=', 'leagues.id')
            ->groupBy('fixtures.league_id')
            ->whereNotNull('fixtures.league_id')
            ->where('leagues.sport', 'football')
            ->select([
                'leagues.id as league_id',
                'leagues.country as country/competition',
                'leagues.name as competition',
                $this->statsHelper->statsSelect()
            ])
            ->groupBy('leagues.country', 'leagues.name')
            ->orderBy($this->sort['sortType'], $this->sort['sortOrder'])
            ->paginate(15);

        $output = [];
        foreach ($types as $typeValue) {
            $league = $typeValue;
            $typeValue = $typeValue->league_id;

            $typeValues = $league[$type] . ' - ' . $league->competition;
            $output[$typeValues] = $this->tableBodyRendered($typeValues, $league, $type, $typeValue);
        }

        return $this->customPaginationOutput($head, $output, $types);
    }

    public function competitionTable($id)
    {
        $type = 'Team';
        $head = $this->tableHeader($type);

        $bets = Bet::filters($this->filters)->user($this->userId);
        $types = $bets
            ->join('bet_fixtures', 'bets.id', '=', 'bet_fixtures.bet_id')
            ->join('fixtures', function ($join) use ($id) {
                $join->on('bet_fixtures.fixture_id', '=', 'fixtures.id')->where('league_id', $id);
            })
            ->leftjoin('teams', function ($join) {
                $join->on('teams.id', '=', 'fixtures.home_team');
                $join->orOn('teams.id', '=', 'fixtures.away_team');
            })
            ->where('fixtures.sport', 'football')
            ->groupBy('teams.id', 'league_id', 'teams.name')
            ->select(['teams.id as team_id', 'teams.name as team', 'league_id', $this->statsHelper->statsSelect()])
            ->orderBy($this->sort['sortType'], $this->sort['sortOrder'])
            ->paginate(15);

        $output = [];
        foreach ($types as $teamId => $bets) {
            $typeValue = $bets->team;
            $output[$typeValue] = $this->tableBodyRendered($typeValue, $bets, $type, [$bets->team_id, $bets->league_id]);
        }


        return $this->customPaginationOutput($head, $output, $types);
    }

    public function teamTable($teamId, $leagueId)
    {
        //@TODO home/away filter for teams
        $bets = BetFixture::whereHas('fixture', function ($q) use ($teamId, $leagueId) {
            $q->where('home_team', $teamId)->orWhere('away_team', $teamId);
            if ($leagueId) {
                $q->where('league_id', $leagueId);
            }
        });

        $bets = Bet::joinAllBets()
            ->whereIn('fixture_id', $bets->pluck('fixture_id'))
            ->filters($this->filters)
            ->user($this->userId)
            ->paginate(15);

        $bets = [
            'bets' => $bets,
        ];


        return $bets;
    }

    public function refereeTable()
    {
        $type = 'referee';
        $head = $this->tableHeader($type);

        $bets = Bet::user($this->userId)
            ->filters($this->filters);

        $types = $bets->clone()
            ->join('bet_fixtures', 'bets.id', '=', 'bet_fixtures.bet_id')
            ->join('fixtures', 'bet_fixtures.fixture_id', '=', 'fixtures.id')
            ->groupBy('fixtures.referee')
            ->whereNotNull('fixtures.referee')
            ->select(['referee', $this->statsHelper->statsSelect()])
            ->orderBy($this->sort['sortType'], $this->sort['sortOrder'])
            ->paginate(15);
        $output = [];
        foreach ($types as $key => $typeValue) {
            $referee = $typeValue;
            $typeValue = $referee->$type;

            $output[$typeValue] = $this->tableBodyRendered($typeValue, $referee, $type, $typeValue);
        }

        return $this->customPaginationOutput($head, $output, $types);
    }

    public function venueTable()
    {
        $type = 'venue';
        $head = $this->tableHeader($type);

        $bets = Bet::user($this->userId)
            ->filters($this->filters);

        $types = $bets->clone()
            ->join('bet_fixtures', 'bets.id', '=', 'bet_fixtures.bet_id')
            ->join('fixtures', 'bet_fixtures.fixture_id', '=', 'fixtures.id')
            ->join('venues', 'fixtures.venue_id', '=', 'venues.id')
            ->groupBy('fixtures.venue_id', 'venues.name')
            ->whereNotNull('fixtures.venue_id')
            ->select(['venues.id as venue_id', 'venues.name as venue', $this->statsHelper->statsSelect()])
            ->orderBy($this->sort['sortType'], $this->sort['sortOrder'])
            ->paginate(15);

        $output = [];
        foreach ($types as $key => $typeValue) {
            $venue = $typeValue;

            $typeValue = $venue->$type;
            $output[$typeValue] = $this->tableBodyRendered($typeValue, $venue, $type, $venue->venue_id);
        }

        return $this->customPaginationOutput($head, $output, $types);
    }

    public function getSpecialStats($key)
    {
        if ($key == 'referee') {
            return $this->refereeTable();
        } elseif ($key == 'venue') {
            return $this->venueTable();
        } elseif ($key == 'competitions') {
            return $this->competitionsTable();
        } elseif ($key == 'teams') {
            return $this->teamsTable();
        }
    }

    public function refereeBets($referee)
    {
        $bets = BetFixture::whereHas('fixture', function ($q) use ($referee) {
            $q->where('referee', $referee);
        })->get();

        $bets = Bet::joinAllBets()->whereIn('fixture_id', $bets->pluck('fixture_id'))->filters($this->filters)->user($this->userId)->paginate(15);
        $betsByCompetition['bets'] = $bets;

        return $betsByCompetition;
    }

    public function venueBets($venueId)
    {
        $bets = Bet::user($this->userId)
            ->filters($this->filters);

        $bets = $bets
            ->joinAllBets()
            ->join('fixtures', 'bet_fixtures.fixture_id', '=', 'fixtures.id')
            ->join('venues', 'fixtures.venue_id', '=', 'venues.id')
            ->groupBy('bets.id', 'bet_fixtures.id')
            ->whereNotNull('fixtures.venue_id')
            ->where('venues.id', $venueId)
            ->paginate(15);

        $betsByVenue['bets'] =
            $bets;

        return $betsByVenue;
    }

    public function competitionBets($league_id)
    {
        $bets = Bet::user($this->userId)
            ->filters($this->filters);

        $bets = $bets
            ->joinAllBets()
            ->join('fixtures', 'bet_fixtures.fixture_id', '=', 'fixtures.id')
            ->join('leagues', 'league_id', '=', 'leagues.id')
            ->groupBy('bets.id', 'bet_fixtures.fixture_id')
            ->whereNotNull('fixtures.league_id')
            ->where('fixtures.league_id', $league_id)
            ->paginate(15);

        $betsByLeague['bets'] =
            $bets;

        return $betsByLeague;
    }

    public function teamsTable()
    {
        $type = 'team';
        $head = $this->tableHeader($type);

        $bets = Bet::user($this->userId)
            ->filters($this->filters);

        $home = $bets->clone()
            ->joinFixtures()
            ->leftjoin('teams', function ($join) {
                $join->on('teams.id', '=', 'fixtures.home_team');
            })
            ->select(['teams.id as id', 'teams.name as team', 'bet_fixtures.status', 'stake', 'odds']);

        $homeAndAway = $bets->clone()
            ->joinFixtures()
            ->leftjoin('teams', function ($join) {
                $join->on('teams.id', '=', 'fixtures.away_team');
            })
            ->select(['teams.id as id', 'teams.name as team', 'bet_fixtures.status', 'stake', 'odds'])
            ->unionAll($home);

        $types = DB::table(DB::raw("({$homeAndAway->toSql()}) as bets"))
            ->mergeBindings($homeAndAway->getQuery())
            ->select(DB::raw('id, team'), $this->statsHelper->statsSelect(''))
            ->groupBy('team', 'id')
            ->orderBy($this->sort['sortType'], $this->sort['sortOrder'])
            ->paginate(15);

        $output = [];
        foreach ($types as $key => $typeValue) {
            $referee = $typeValue;
            $typeValue = $referee->team;

            $output[$typeValue] = $this->tableBodyRendered($typeValue, $referee, $type, $referee->id);
        }

        return $this->customPaginationOutput($head, $output, $types);
    }

    public function dateSelect()
    {
        $interval = reset($this->filters['interval']);
        return ['select' => DB::raw($interval['formatting']['select']), 'format' => $interval['formatting']['format']];
    }
}

<?php

namespace App\Lib;

use App\Models\Bet;
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

    public function __construct($userId, $filters, $sort = false) //maybe optional
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

        $query = Bet::select(DB::raw("id, status, stake, odds, case
        when odds > 0 and odds <= 1.5 then '< 1.5'
        when odds > 1.5 and odds <= 1.75 then '1.5-1.75'
        when odds > 1.75 and odds <= 2 then '1.75-2'
        when odds > 2 and odds <= 2.25 then '2-2.25'
        when odds > 2.25 and odds <= 2.50 then '2-2.25'
        when odds > 2.50 and odds <= 2.75 then '2.50-2.75'
        else '2.75+'
        end AS odd_range"),  $this->statsHelper->statsSelect(), $dateSelect['select'])
            ->filters($this->filters)
            ->groupBy('formatted_date', 'id');


        $bets = DB::table(DB::raw("({$query->toSql()}) as bets"))
            ->mergeBindings($query->getQuery())
            ->select(DB::raw('count(id), odd_range, formatted_date'), $this->statsHelper->statsSelect())
            ->groupBy('odd_range', 'formatted_date')
            ->orderBy($this->sort['sortType'], $this->sort['sortOrder'])
            ->get();

        $labels = [];
        foreach ($carbonDates as $key => $date) {
            foreach ($bets as $key => $odd) {
                $labels[$date->format($dateSelect['format'])][$odd->odd_range] = 0;
            }
        }

        foreach ($bets as $key => $value) {
            $date = $value->formatted_date;
            $key = $value->odd_range;
            $labels[$date][$key] = $value->profit;
        }

        $table = $this->oddsTable();
        // $tableValues = array_column($table['body'], $type);
        $vals = [];
        foreach ($labels as $date => $values) {
            // $sortedLabels = $this->sortArrayByArray($values, $tableValues);
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
        $bets = Bet::user($this->userId)
            ->filters($this->filters);

        $columnsTable = $bets->clone()
            ->groupBy($type)
            ->select([
                $type,
                $this->statsHelper->statsSelect(),
            ])
            ->whereNotNull($type)
            ->orderBy($this->sort['sortType'], $this->sort['sortOrder']);

        $columns = $bets->clone()
            ->groupBy($type, 'formatted_date')
            ->select([
                $type,
                $dateSelect['select'],
                $this->statsHelper->statsSelect()

            ])
            ->whereNotNull($type)
            ->orderBy($this->sort['sortType'], $this->sort['sortOrder'])->get();

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
        // $tableValues = array_column($table['body'], 'Type');

        $vals = [];
        foreach ($labels as $date => $values) {
            // $sortedLabels = $this->sortArrayByArray($values, $tableValues);
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
            ->filters($this->filters);

        $bets = $bets
            ->groupBy('formatted_date')
            ->select([
                DB::raw("DATE_FORMAT(`date`, '%Y-%m-%d') as formatted_date"),
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

        $selections = Bet::leftJoin('bet_types', function ($join) {
            $join->whereRaw('JSON_CONTAINS(bets.category, CAST(bet_types.id as JSON), "$")');
        })
            ->whereRaw('category <> ""')
            ->groupBy('bet_types.id', 'formatted_date')
            ->filters($this->filters)
            ->select([
                'bet_types.id',
                'bet_types.name',
                $dateSelect['select'],
                $this->statsHelper->statsSelect()
            ])->orderBy($this->sort['sortType'], $this->sort['sortOrder'])
            ->whereNotNull('name')
            ->get();

        $selectionsTable = Bet::leftJoin('bet_types', function ($join) {
            $join->whereRaw('JSON_CONTAINS(bets.category, CAST(bet_types.id as JSON), "$")');
        })
            ->whereRaw('category <> ""')
            ->groupBy('bet_types.id')
            ->filters($this->filters)
            ->select([
                'bet_types.id',
                'bet_types.name',
                $this->statsHelper->statsSelect()
            ])->orderBy($this->sort['sortType'], $this->sort['sortOrder'])
            ->whereNotNull('name');

        $labels = [];
        foreach ($carbonDates as $key => $date) {
            foreach ($selections as $key => $selection) {
                $labels[$date->format($dateSelect['format'])][$selection->name] = 0;
            }
        }

        foreach ($selections as $key => $value) {
            $date = $value->formatted_date;
            $labels[$date][$value->name] = $value->profit;
        }

        $table = $this->selectionTable($selectionsTable);
        // $tableValues = array_column($table['body'], $type);

        $vals = [];
        foreach ($labels as $date => $values) {
            // $sortedLabels = $this->sortArrayByArray($values, $tableValues);
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

    // function sortArrayByArray(array $array, array $orderArray)
    // {
    //     $ordered = array();
    //     foreach ($orderArray as $key) {
    //         if (array_key_exists($key['value'], $array)) {
    //             $ordered[$key['value']] = $array[$key['value']];
    //             unset($array[$key['value']]);
    //         }
    //     }
    //     return $ordered + $array;
    // }


    public function selectionMapping($selections)
    {
        $selectionMapping = [
            'Corners' => [
                'type' => [
                    'end' => [
                        'corners'
                    ]
                ]
            ],
            'Total goals' => [
                'type' => [
                    'end' => [
                        'goals'
                    ]
                ]
            ],
            'FH goals' => [
                'type' => [
                    'end' => [
                        'goals FH'
                    ]
                ]
            ],
            '1x2' => [
                'type' => [
                    'match' => [
                        'home win',
                        'away win',
                    ]
                ]
            ],
            'Cards' => [
                'type' => [
                    'end' => [
                        'cards'
                    ]
                ]
            ],
            'BTTS' => [
                'type' => [
                    'match' => [
                        'btts'
                    ]
                ]
            ],
            'Booking points' => [
                'type' => [
                    'match' => [
                        'booking points'
                    ]
                ]
            ],
            'AH' => [
                'type' => [
                    'match' => [
                        'ah'
                    ]
                ]
            ],
        ];
        $bets = [];
        foreach ($selectionMapping as $keyer => $type) {
            foreach ($type as $type => $types) {
                foreach ($types as $type => $typeValues) {
                    if ($type == 'start') {
                        foreach ($typeValues as $key => $typeValue) {
                            $bets[$keyer] = $selections->clone()->where('selection', 'like', strtolower($typeValue) . '%');
                            break;
                        }
                    } elseif ($type == 'end') {
                        foreach ($typeValues as $key => $typeValue) {
                            $bets[$keyer] = $selections->clone()->where('selection', 'like', '%' . strtolower($typeValue));
                            break;
                        }
                    } elseif ($type == 'match') {
                        foreach ($typeValues as $key => $typeValue) {
                            if ($key == 0) {
                                $bets[$keyer] = $selections->clone()->where('selection', strtolower($typeValue));
                            } else {
                                $bets[$keyer] = $bets[$keyer]->orWhere('selection', strtolower($typeValue));
                            }
                        }
                    }
                }
            }
        }

        return $bets;
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

    public function tableBodyRendered($typeValue, $output, $type, $specialId = '',)
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
        foreach ($types->get() as $key => $typeValue) {
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
        foreach ($selections->get() as $key => $selection) {
            $key = $selection->name;
            $output[$key] = $this->tableBodyRendered($key, $selection, $type);
        }

        return ['head' => $head, 'body' => array_values($output)];
    }


    public function oddsTable()
    {
        $type = 'odds';
        $head = $this->tableHeader($type);

        $query = Bet::select(DB::raw("id, status, stake, odds, case
        when odds > 0 and odds <= 1.5 then '< 1.5'
        when odds > 1.5 and odds <= 1.75 then '1.5-1.75'
        when odds > 1.75 and odds <= 2 then '1.75-2'
        when odds > 2 and odds <= 2.25 then '2-2.25'
        when odds > 2.25 and odds <= 2.50 then '2-2.25'
        when odds > 2.50 and odds <= 2.75 then '2.50-2.75'
        else '2.75+'
        end AS odd_range"),  $this->statsHelper->statsSelect())
            ->filters($this->filters)
            ->groupBy('id');


        $bets = DB::table(DB::raw("({$query->toSql()}) as bets"))
            ->mergeBindings($query->getQuery())
            ->select(DB::raw('count(id), odd_range'), $this->statsHelper->statsSelect())
            ->orderBy($this->sort['sortType'], $this->sort['sortOrder'])
            ->groupBy('odd_range');

        $output = [];
        foreach ($bets->get() as $typeValue => $odd) {
            $typeValue = $odd->odd_range;
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
            '#3366cc',
            '#dc3912',
            '#ff9900',
            '#109618',
            '#990099',
            '#0099c6',
            '#dd4477',
            '#66aa00',
            '#b82e2e',
            '#316395',
            '#994499',
            '#22aa99',
            '#aaaa11',
            '#6633cc',
            '#e67300',
            '#8b0707',
            '#651067',
            '#329262',
            '#5574a6',
            '#3b3eac',
            '#b77322',
            '#16d620',
            '#b91383',
            '#f4359e',
            '#9c5935',
            '#a9c413',
            '#2a778d',
            '#668d1c',
            '#bea413',
            '#0c5922',
            '#743411',
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
            ->join('fixtures', 'match_id', '=', 'fixtures.id')
            ->join('leagues', 'league_id', '=', 'leagues.id')
            ->groupBy('fixtures.league_id')
            ->whereNotNull('fixtures.league_id')
            ->select([
                'league_id',
                'leagues.country as country/competition',
                'leagues.name as competition',
                $this->statsHelper->statsSelect()
            ])
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

        $bets = Bet::filters($this->filters);
        $types = $bets
            ->join('fixtures', function ($join) use ($id) {
                $join->on('match_id', '=', 'fixtures.id')->where('league_id', $id);
            })
            ->leftjoin('teams', function ($join) {
                $join->on('teams.id', '=', 'fixtures.home_team');
                $join->orOn('teams.id', '=', 'fixtures.away_team');
            })
            ->groupBy('teams.id', 'league_id')
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
        $bets = Bet::whereHas('fixture', function ($q) use ($teamId, $leagueId) {
            $q->where('home_team', $teamId)->orWhere('away_team', $teamId);
            if ($leagueId) {
                $q->where('league_id', $leagueId);
            }
        })->whereNotNull('match_id')
            ->filters($this->filters)
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
            ->join('fixtures', 'match_id', '=', 'fixtures.id')
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
            ->join('fixtures', 'match_id', '=', 'fixtures.id')
            ->join('venues', 'venue_id', '=', 'venues.id')
            ->groupBy('fixtures.venue_id')
            ->whereNotNull('fixtures.venue_id')
            ->select(['venue_id', 'venues.name as venue', $this->statsHelper->statsSelect()])
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
        $bets = Bet::whereHas('fixture', function ($q) use ($referee) {
            $q->where('referee', $referee);
        })->whereNotNull('match_id')->get();

        $bets = Bet::whereIn('id', $bets->pluck('id'))->filters($this->filters)->paginate(15);
        $betsByCompetition['bets'] =
            $bets;

        return $betsByCompetition;
    }

    public function venueBets($venueId)
    {
        $bets = Bet::user($this->userId)
            ->filters($this->filters);

        $bets = $bets
            ->join('fixtures', 'match_id', '=', 'fixtures.id')
            ->join('venues', 'venue_id', '=', 'venues.id')
            ->groupBy('bets.id')
            ->whereNotNull('fixtures.venue_id')
            ->where('fixtures.venue_id', $venueId)
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
            ->join('fixtures', 'match_id', '=', 'fixtures.id')
            ->join('leagues', 'league_id', '=', 'leagues.id')
            ->groupBy('bets.id')
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
            ->join('fixtures', 'match_id', '=', 'fixtures.id')
            ->leftjoin('teams', function ($join) {
                $join->on('teams.id', '=', 'fixtures.home_team');
            })
            ->select(['teams.id as id', 'teams.name as team', 'status', 'stake', 'odds']);

        $homeAndAway = $bets->clone()
            ->join('fixtures', 'match_id', '=', 'fixtures.id')
            ->leftjoin('teams', function ($join) {
                $join->on('teams.id', '=', 'fixtures.away_team');
            })
            ->select(['teams.id as id', 'teams.name as team', 'status', 'stake', 'odds'])
            ->unionAll($home);



        $types = DB::table(DB::raw("({$homeAndAway->toSql()}) as bets"))
            ->mergeBindings($homeAndAway->getQuery())
            ->select(DB::raw('id, team'), $this->statsHelper->statsSelect())
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

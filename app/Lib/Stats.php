<?php

namespace App\Lib;

use App\Helpers\PaginationHelper;
use App\Models\Bet;
use App\Models\BetType;
use App\Models\Fixture;
use App\Models\League;
use App\Models\Team;
use App\Models\TeamLeague;
use App\Models\Venue;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class Stats
{
    protected $userId = null;
    protected $odds = null;
    protected $filters = null;
    protected $tabs = null;

    public function __construct($userId, $filters)
    {
        $this->userId = $userId;
        $this->filters = $filters;
        $this->odds = [
            '< 1.50' => [
                'min' => 1,
                'max' => 1.50,
            ],
            '1.50-1.75' => [
                'min' => 1.50,
                'max' => 1.75,
            ],
            '1.75-2' => [
                'min' => 1.75,
                'max' => 2,
            ],
            '2-2.25' => [
                'min' => 2,
                'max' => 2.25,
            ],
            '>2.25' => [
                'min' => 2.25,
                'max' => 1000,
            ],
        ];

        $this->tabs = [
            'tipster',
            'odds',
            'selection',
            'stake',
            'sport',
            'bookie',
            'profit',
            'type',
        ];
    }

    public function oddsGraph()
    {
        $carbonDates = CarbonPeriod::create($this->filters['from']['value'], key($this->filters['interval']), $this->filters['to']['value']);

        $intervalFunctions = reset($this->filters['interval'])['days'];
        $addFunction = $intervalFunctions['function'];
        $labels = [];
        $odds = $this->odds;

        foreach ($carbonDates as $key => $date) {
            foreach ($odds as $name => $value) {
                $labels[$date->format('Y-m-d')][$name] = 0;
            }
        }

        foreach ($labels as $date => $values) {
            foreach ($values as $key => $value) {
                $minMaxOdds = $odds[$key];
                $bets = Bet::user($this->userId)
                    ->filters($this->filters)
                    ->where('odds', '>', $minMaxOdds['min'])
                    ->where('odds', '<=', $minMaxOdds['max'])
                    ->where('date', '>=', $date)
                    ->where('date', '<', Carbon::parse($date)->$addFunction());
                if ($bets->count() > 0) {
                    $labels[$date][$key] = $bets->units(2);
                }
            }
        }
        $table = $this->oddsTable();
        $tableValues = array_column($table['body'], 'Type');

        $values = [];
        $vals = [];
        foreach ($labels as $date => $values) {
            $formattedLabels[Carbon::parse($date)->format(reset($this->filters['interval'])['days']['format'])] = $values;
            $sortedLabels = $this->sortArrayByArray($values, $tableValues);
            foreach ($sortedLabels as $key => $value) {
                $vals[$key][] = $value;
            }
        }

        return [
            'labels' => array_keys($formattedLabels),
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
        $intervalFunctions = reset($this->filters['interval'])['days'];
        $addFunction = $intervalFunctions['function'];

        $carbonDates = CarbonPeriod::create($this->filters['from']['value'], key($this->filters['interval']), $this->filters['to']['value']);

        $bets = Bet::user($this->userId)
            ->filters($this->filters);

        $columns = $bets->clone()
            ->groupBy($type)
            ->select([
                $type,
                $this->statsSelect()
            ])
            ->whereNotNull($type)
            ->orderByDesc('bets');

        $labels = [];
        foreach ($carbonDates as $key => $date) {
            $date = $date->format('Y-m-d');
            foreach ($columns->get() as $columnValue => $value) {
                $bet = $columns->clone()
                    ->where($type, $value->$type)
                    ->where('date', '>=', $date)
                    ->where('date', '<', Carbon::parse($date)->$addFunction())->first();
                if ($bet) {
                    $labels[$date][(string)$value->$type] = $bet->profit;
                } else {
                    $labels[$date][(string)$value->$type] = 0;
                }
            }
        }

        $table = $this->simpleTable($type, $columns);
        $tableValues = array_column($table['body'], 'Type');

        $vals = [];
        $formattedLabels = [];

        foreach ($labels as $date => $values) {
            $formattedLabels[Carbon::parse($date)->format(reset($this->filters['interval'])['days']['format'])] = $values;
            $sortedLabels = $this->sortArrayByArray($values, $tableValues);
            foreach ($sortedLabels as $key => $value) {
                $vals[$key][] = $value;
            }
        }

        return [
            'labels' => array_keys($formattedLabels),
            'values' => $vals,
            'table' => $table,
        ];
    }

    public function profitPerDayGraph()
    {
        $intervalFunctions = reset($this->filters['interval'])['days'];
        $addFunction = $intervalFunctions['function'];

        $carbonDates = CarbonPeriod::create(now()->subMonth(), '1 day', now());

        $labels = [];
        $columns = [
            'Profit per day',
            'Total profit',
        ];

        foreach ($carbonDates as $key => $date) {
            foreach ($columns as $columnValue) {
                $labels[$date->format('Y-m-d')][(string)$columnValue] = 0;
            }
        }
        $totalProfit = 0;
        foreach ($labels as $date => $values) {
            foreach ($values as $columnValue => $value) {
                $bets = Bet::user($this->userId)
                    ->filters($this->filters)
                    ->where('date', '>=', $date)
                    ->where('date', '<', Carbon::parse($date)->addDay());
                if ($bets->count() > 0) {
                    if ($columnValue == 'Profit per day') {
                        $labels[$date]['Profit per day'] = $bets->units(2);
                        $totalProfit += $bets->units();
                    }
                }
                if ($columnValue == 'Total profit') {
                    $labels[$date][$columnValue] += round($totalProfit, 2);
                }
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
        $intervalFunctions = reset($this->filters['interval'])['days'];
        $addFunction = $intervalFunctions['function'];

        $selections = Bet::leftJoin('bet_types', function ($join) {
            $join->whereRaw('JSON_CONTAINS(bets.category, CAST(bet_types.id as JSON), "$")');
        })
            ->whereRaw('category <> ""')
            ->groupBy('bet_types.id')
            ->filters($this->filters)
            ->select([
                'bet_types.id',
                'bet_types.name',
                $this->statsSelect()
            ])->orderBy('bets', 'DESC');


        $labels = [];
        foreach ($carbonDates as $key => $date) {
            foreach ($selections->get() as $selection) {
                $bets = $selections->clone()
                    ->where('date', '>=', Carbon::parse($date)->startOfMonth())
                    ->where('date', '<=', Carbon::parse($date)->startOfMonth()->addMonth())
                    ->where('bet_types.id', $selection->id)->first();

                if ($bets) {
                    $labels[$date->format('Y-m-d')][$selection->name] = $bets->profit;
                } else {
                    $labels[$date->format('Y-m-d')][$selection->name] = 0;
                }
            }
        }

        $table = $this->selectionTable($selections);
        $tableValues = array_column($table['body'], $type);

        $vals = [];
        $formattedLabels = [];
        foreach ($labels as $date => $values) {
            $formattedLabels[Carbon::parse($date)->format(reset($this->filters['interval'])['days']['format'])] = $values;
            $sortedLabels = $this->sortArrayByArray($values, $tableValues);
            foreach ($sortedLabels as $key => $value) {
                $vals[$key][] = $value;
            }
        }

        return [
            'labels' => array_keys($formattedLabels),
            'values' => $vals,
            'table' => $table,
        ];
    }

    function sortArrayByArray(array $array, array $orderArray)
    {
        $ordered = array();
        foreach ($orderArray as $key) {
            if (array_key_exists($key['value'], $array)) {
                $ordered[$key['value']] = $array[$key['value']];
                unset($array[$key['value']]);
            }
        }
        return $ordered + $array;
    }


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

    public function tableBody($typeValue, $bets, $type, $specialId = '',)
    {
        $output[$typeValue] = [
            $type => ['value' => $typeValue, 'type' => '', 'specialId' => $specialId],
            'Bets' => ['value' => $bets->clone()->betCount(), 'type' => ''],
            'Won' => ['value' => $bets->clone()->wonBets(), 'type' => ''],
            'Staked' => ['value' => $bets->clone()->totalStaked(2), 'type' => ''],
            'Profit' => ['value' => $bets->clone()->units(2), 'type' => ''],
            'ROI' => ['value' => $bets->clone()->roi(2), 'type' => '%'],
        ];

        return $output;
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

        $query = Bet::user($this->userId)->where('date', '>=', $this->filters['from']['value'])
            ->where('date', '<', $this->filters['to']['value']);
        $bets = $query;

        foreach ($this->odds as $typeValue => $odd) {
            $minMaxOdds = $odd;
            $bet1s = $bets->clone()
                ->filters($this->filters)
                ->where('odds', '>', $minMaxOdds['min'])
                ->where('odds', '<=', $minMaxOdds['max'])
                ->where('date', '>=', now()->subMonth())
                ->where('date', '<', now());
            $output[$typeValue] = $this->tableBody($typeValue, $bet1s, $type)[$typeValue];
        }

        usort($output, function ($a, $b) {
            return $b['Bets'] <=> $a['Bets'];
        });


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

    public function getTabs()
    {
        return $this->tabs;
    }

    public function competitionsTable($sort)
    {
        $type = 'competition';
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
                'leagues.country',
                'leagues.name',
                $this->statsSelect()
            ])
            ->orderBy($sort['sortType'], $sort['sortOrder'])
            ->paginate(15);

        $output = [];
        foreach ($types as $typeValue) {
            $league = $typeValue;
            $typeValue = $typeValue->league_id;

            $typeValues = $league->country . ' - ' . $league->name;
            $output[$typeValues] = $this->tableBodyRendered($typeValues, $league, $type, $typeValue);
        }

        return $this->customPaginationOutput($head, $output, $types);
    }

    public function competitionTable($id)
    {
        $type = 'Team';
        $head = $this->tableHeader($type);

        $competition = League::find($id);

        $bets = Bet::whereHas('fixture', function ($q) use ($competition) {
            $q->where('league_id', $competition->id);
        })->whereNotNull('match_id')->get();

        $homeBets = $bets->groupBy('fixture.home_team')->toArray();
        $awayBets = $bets->groupBy('fixture.away_team')->toArray();
        $allbets = [];

        foreach ($homeBets as $key => $values) {
            if (array_key_exists($key, $awayBets)) {
                $allbets[$key] = array_merge($values, $awayBets[$key]);
            } else {
                $allbets[$key] = $values;
            }
        }

        foreach ($awayBets as $key => $values) {
            if (array_key_exists($key, $homeBets)) {
                $allbets[$key] = array_merge($values, $homeBets[$key]);
            } else {
                $allbets[$key] = $values;
            }
        }

        foreach ($allbets as $teamId => $bets) {
            $team = Team::find($teamId);
            $betIds = array_column($bets, 'id');
            $bet1s = Bet::whereIn('id', $betIds)->filters($this->filters);

            $typeValue = $team->name;
            $output[$typeValue] = $this->tableBody($typeValue, $bet1s, $type, $team->id)[$typeValue];
        }

        usort($output, function ($a, $b) {
            return $b['Bets'] <=> $a['Bets'];
        });


        return ['head' => $head, 'body' => array_values($output)];
    }

    public function teamTable($teamId)
    {
        $leagues = League::get()->mapWithKeys(function ($league, $key) {
            return [$league->id => $league];
        });

        $bets = Bet::whereHas('fixture', function ($q) use ($teamId) {
            $q->where('home_team', $teamId)->orWhere('away_team', $teamId);
        })->whereNotNull('match_id')->get();

        $betsByLeague = $bets->groupBy('fixture.league_id');
        foreach ($betsByLeague as $key => $bets) {
            $bets = Bet::whereIn('id', $bets->pluck('id'))->filters($this->filters)->paginate(15);

            $betsByCompetition[$leagues[$key]->name] = [
                'bets' => $bets,
                'competition' => $leagues[$key],
            ];
        }

        return $betsByCompetition;
    }

    public function statsSelect()
    {
        return DB::raw('count(bets.id) as bets, 
        COUNT(CASE WHEN status = "won" THEN 1 END) AS won, 
        
        sum(stake) AS staked,

        (SUM(CASE WHEN status = "won" THEN odds*stake - stake ELSE 0 END) + 
        SUM(CASE WHEN status = "lost" THEN -stake ELSE 0 END) +
        SUM(CASE WHEN status = "halfwon" THEN ((stake / 2) * odds) - stake ELSE 0 END) +
        SUM(CASE WHEN status = "halflost" THEN -(stake / 2) ELSE 0 END)) as profit,

        ((SUM(CASE WHEN status = "won" THEN odds*stake - stake ELSE 0 END) + 
        SUM(CASE WHEN status = "lost" THEN -stake ELSE 0 END) +
        SUM(CASE WHEN status = "halfwon" THEN ((stake / 2) * odds) - stake ELSE 0 END) +
        SUM(CASE WHEN status = "halflost" THEN -(stake / 2) ELSE 0 END)) / sum(bets.stake) * 100) as roi
        ');
    }

    public function refereeTable($sort)
    {
        $type = 'referee';
        $head = $this->tableHeader($type);

        $bets = Bet::user($this->userId)
            ->filters($this->filters);

        $types = $bets->clone()
            ->join('fixtures', 'match_id', '=', 'fixtures.id')
            ->groupBy('fixtures.referee')
            ->whereNotNull('fixtures.referee')
            ->select(['referee', $this->statsSelect()])
            ->orderBy($sort['sortType'], $sort['sortOrder'])
            ->paginate(15);
        $output = [];
        foreach ($types as $key => $typeValue) {
            $referee = $typeValue;
            $typeValue = $referee->$type;

            $output[$typeValue] = $this->tableBodyRendered($typeValue, $referee, $type, $typeValue);
        }

        return $this->customPaginationOutput($head, $output, $types);
    }

    public function venueTable($sort)
    {
        $type = 'venue_id';
        $head = $this->tableHeader($type);

        $bets = Bet::user($this->userId)
            ->filters($this->filters);

        $types = $bets->clone()
            ->join('fixtures', 'match_id', '=', 'fixtures.id')
            ->join('venues', 'venue_id', '=', 'venues.id')
            ->groupBy('fixtures.venue_id')
            ->whereNotNull('fixtures.venue_id')
            ->select(['venue_id', 'venues.name', $this->statsSelect()])
            ->orderBy($sort['sortType'], $sort['sortOrder'])
            ->paginate(15);

        $output = [];
        foreach ($types as $key => $typeValue) {
            $venue = $typeValue;

            $typeValue = $venue->name;
            $output[$typeValue] = $this->tableBodyRendered($typeValue, $venue, $type, $venue->venue_id);
        }

        return $this->customPaginationOutput($head, $output, $types);
    }

    public function getSpecialStats($key, $sort)
    {
        if ($key == 'referee') {
            return $this->refereeTable($sort);
        } elseif ($key == 'venue') {
            return $this->venueTable($sort);
        } elseif ($key == 'competitions') {
            return $this->competitionsTable($sort);
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

        // $bets = Bet::whereIn('id', $bets->pluck('id'))->filters($this->filters)->paginate(15);
        $betsByVenue['bets'] =
            $bets;

        return $betsByVenue;
    }
}

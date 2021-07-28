<?php

namespace App\Lib;

use App\Models\Bet;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

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
            'profit'
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
        return $this->simpleGraph($type, $this->simpleTable($type));
    }

    public function simpleGraph($column, $table)
    {
        $intervalFunctions = reset($this->filters['interval'])['days'];
        $addFunction = $intervalFunctions['function'];

        $carbonDates = CarbonPeriod::create($this->filters['from']['value'], key($this->filters['interval']), $this->filters['to']['value']);

        $labels = [];
        $columns = Bet::user($this->userId)->select($column)->distinct()->pluck($column)->sort();

        foreach ($carbonDates as $key => $date) {
            foreach ($columns as $columnValue) {
                $labels[$date->format('Y-m-d')][(string)$columnValue] = 0;
            }
        }

        foreach ($labels as $date => $values) {
            foreach ($values as $columnValue => $value) {
                $bets = Bet::user($this->userId)
                    ->filters($this->filters)
                    ->where($column, $columnValue)
                    ->where('date', '>=', $date)
                    ->where('date', '<', Carbon::parse($date)->$addFunction());
                if ($bets->count() > 0) {
                    $labels[$date][$columnValue] = $bets->units(2);
                }
            }
        }

        $tableValues = array_column($table['body'], 'Type');

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
                    } else {
                        $totalProfit += $labels[$date]['Profit per day'];
                        $labels[$date][$columnValue] += $totalProfit;
                    }
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
        $carbonDates = CarbonPeriod::create($this->filters['from']['value'], key($this->filters['interval']), $this->filters['to']['value']);
        $intervalFunctions = reset($this->filters['interval'])['days'];
        $addFunction = $intervalFunctions['function'];

        $labels = [];
        $selections1 = Bet::user($this->userId);
        $bets = $this->selectionMapping($selections1);
        $selections = $bets;
        foreach ($carbonDates as $key => $date) {
            foreach (array_keys($selections) as $selection) {
                $labels[$date->format('Y-m-d')][$selection] = 0;
            }
        }

        foreach ($labels as $date => $values) {
            foreach ($values as $columnValue => $value) {
                $bets = $selections[$columnValue]->clone()
                    ->filters($this->filters)
                    ->where('date', '>=', Carbon::parse($date)->startOfMonth())
                    ->where('date', '<=', Carbon::parse($date)->startOfMonth()->addMonth());
                if ($bets->count() > 0) {
                    $labels[$date][$columnValue] = $bets->units(2);
                }
            }
        }

        foreach ($selections as $key => $selection) {
            $output[$key] = $this->tableBody($key, $selection)[$key];
        }

        $table = $this->selectionTable($selections);
        $tableValues = array_column($table['body'], 'Type');

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

    function sortArrayByArray(array $array, array $orderArray)
    {
        $ordered = array();
        foreach ($orderArray as $key) {
            if (array_key_exists($key, $array)) {
                $ordered[$key] = $array[$key];
                unset($array[$key]);
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

    public function tableBody($typeValue, $bets)
    {
        $output[$typeValue] = [
            'Type' => $typeValue,
            'Bets' => $bets->clone()->betCount(),
            'Won' => $bets->clone()->wonBets(),
            'Staked' => $bets->clone()->totalStaked(),
            'Profit' => $bets->clone()->units(2),
            'ROI' => $bets->clone()->roi(2) . '%',
        ];

        return $output;
    }

    public function simpleTable($column)
    {
        $type = $column;
        $head = $this->tableHeader($type);
        // dd($this->filters);
        $query = Bet::user($this->userId)
            ->filters($this->filters)
            ->where('date', '>=', $this->filters['from']['value'])
            ->where('date', '<', $this->filters['to']['value']);
        $bets = clone $query;
        $types = $query->select($type)->distinct()->pluck($type)->sort(); // not efficient
        $output = [];
        foreach ($types as $key => $typeValue) {
            $output[(string)$typeValue] = $this->tableBody($typeValue, $bets->clone()->where($type, $typeValue))[$typeValue];
        }

        usort($output, function ($a, $b) {
            return $b['Bets'] <=> $a['Bets'];
        });

        return ['head' => $head, 'body' => array_values($output)];
    }

    public function selectionTable($selections)
    {
        $type = 'selection';
        $head = $this->tableHeader($type);

        $output = [];

        foreach ($selections as $key => $selection) {
            $output[$key] = $this->tableBody($key, $selection->filters($this->filters))[$key];
        }


        usort($output, function ($a, $b) {
            return $b['Bets'] <=> $a['Bets'];
        });

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
            $output[$typeValue] = $this->tableBody($typeValue, $bet1s)[$typeValue];
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
            "#a6aea9",
            "#9500fd",
            "#313447",
            "#408040",
            "#11670b",
            "#ffbf00",
            "#ebeefd",
            "#ffd2c5",
            "#9584ab",
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
}

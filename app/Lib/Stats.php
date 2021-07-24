<?php

namespace App\Lib;

use App\Models\Bet;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class Stats
{
    protected $userId = null;
    protected $odds = null;
    protected $start = null;
    protected $end = null;
    protected $interval = null;
    protected $tabs = null;

    public function __construct($userId, $start, $end, $interval)
    {
        $this->userId = $userId;
        $this->start = $start;
        $this->end = $end;
        $this->interval = $interval;
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
        ];
    }

    public function oddsGraph()
    {
        $carbonDates = CarbonPeriod::create($this->start, key($this->interval), $this->end);
        $intervalFunctions = reset($this->interval)['days'];
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
                $bets = Bet::user($this->userId)->where('odds', '>', $minMaxOdds['min'])
                    ->where('odds', '<=', $minMaxOdds['max'])
                    ->where('created_at', '>=', $date)
                    ->where('created_at', '<', Carbon::parse($date)->$addFunction());
                if ($bets->count() > 0) {
                    $labels[$date][$key] = $bets->units();
                }
            }
        }
        $table = $this->oddsTable();
        $tableValues = array_column($table['body'], 'Type');

        $values = [];
        $vals = [];
        foreach ($labels as $date => $values) {
            $formattedLabels[Carbon::parse($date)->format(reset($this->interval)['days']['format'])] = $values;
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
        $intervalFunctions = reset($this->interval)['days'];
        $addFunction = $intervalFunctions['function'];

        $carbonDates = CarbonPeriod::create($this->start, key($this->interval), $this->end);

        $labels = [];
        $columns = Bet::user($this->userId)->select($column)->distinct()->pluck($column)->sort();

        foreach ($carbonDates as $key => $date) {
            foreach ($columns as $columnValue) {
                $labels[$date->format('Y-m-d')][(string)$columnValue] = 0;
            }
        }

        foreach ($labels as $date => $values) {
            foreach ($values as $columnValue => $value) {
                $bets = Bet::user($this->userId)->where($column, $columnValue)
                    ->where('created_at', '>=', $date)
                    ->where('created_at', '<', Carbon::parse($date)->$addFunction());
                if ($bets->count() > 0) {
                    $labels[$date][$columnValue] = $bets->units();
                }
            }
        }

        $table = $this->simpleTable($column);
        $tableValues = array_column($table['body'], 'Type');

        $vals = [];
        foreach ($labels as $date => $values) {
            $formattedLabels[Carbon::parse($date)->format(reset($this->interval)['days']['format'])] = $values;
            $sortedLabels = $this->sortArrayByArray($values, $tableValues);
            foreach ($sortedLabels as $key => $value) {
                $vals[$key][] = $value;
            }
        }
        // dd(array_keys($labels));
        return [
            'labels' => array_keys($formattedLabels),
            'values' => $vals,
            'table' => $table,
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
        $carbonDates = CarbonPeriod::create($this->start, key($this->interval), $this->end);
        $intervalFunctions = reset($this->interval)['days'];
        $addFunction = $intervalFunctions['function'];

        $labels = [];
        $selections = Bet::user($this->userId)->get();
        $bets = $this->selectionMapping($selections);

        $selections = $bets;
        foreach ($carbonDates as $key => $date) {
            foreach (array_keys($selections) as $selection) {
                $labels[$date->format('Y-m-d')][$selection] = 0;
            }
        }

        foreach ($labels as $date => $values) {
            foreach ($values as $sel => $value) {
                $bets = $selections[$sel];
                $ids = [];
                foreach ($bets as $key => $bet) {
                    $ids[] = $bet->id;
                }

                $bets = Bet::user($this->userId)->where('created_at', '>=', $date)
                    ->where('created_at', '<', Carbon::parse($date)->$addFunction()) //correct?
                    ->whereIn('id', $ids);

                if ($bets->count() > 0) {
                    $labels[$date][$sel] = $bets->units();
                }
            }
        }
        $table = $this->selectionTable();
        $tableValues = array_column($table['body'], 'Type');


        $vals = [];
        foreach ($labels as $date => $values) {
            $formattedLabels[Carbon::parse($date)->format(reset($this->interval)['days']['format'])] = $values;
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
        ];
        $bets = [];
        foreach ($selections as $key => $bet) {

            foreach ($selectionMapping as $keyer => $type) {
                foreach ($type as $type => $types) {
                    foreach ($types as $type => $typeValues) {
                        if ($type == 'start') {
                            foreach ($typeValues as $key => $typeValue) {
                                if (str_starts_with(strtolower($bet->selection), strtolower($typeValue))) {
                                    $bets[$keyer][] = $bet;
                                    break;
                                }
                            }
                        } elseif ($type == 'end') {
                            foreach ($typeValues as $key => $typeValue) {
                                if (str_ends_with(strtolower($bet->selection), strtolower($typeValue))) {
                                    $bets[$keyer][] = $bet;
                                    break;
                                }
                            }
                        } elseif ($type == 'match') {
                            foreach ($typeValues as $key => $typeValue) {
                                if (strtolower($bet->selection) == strtolower($typeValue)) {
                                    $bets[$keyer][] = $bet;
                                    break;
                                }
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

        $query = Bet::user($this->userId)->where('created_at', '>=', $this->start)
            ->where('created_at', '<', $this->end);
        $bets = clone $query;
        $types = $query->select($type)->distinct()->pluck($type)->sort();
        $output = [];
        foreach ($types as $key => $typeValue) {
            $output[(string)$typeValue] = $this->tableBody($typeValue, $bets->clone()->where($type, $typeValue))[$typeValue];
        }

        usort($output, function ($a, $b) {
            return $b['Bets'] <=> $a['Bets'];
        });

        return ['head' => $head, 'body' => array_values($output)];
    }

    public function selectionTable()
    {
        $type = 'selection';
        $head = $this->tableHeader($type);

        $query = Bet::user($this->userId)->where('created_at', '>=', $this->start)
            ->where('created_at', '<', $this->end);
        $bets = clone $query;
        $selections = $this->selectionMapping($query->get());

        $types = array_keys($selections);

        $output = [];
        foreach ($types as $key => $typeValue) {
            $betz = $selections[$typeValue];
            $ids = [];
            foreach ($betz as $key => $bet) {
                $ids[] = $bet->id;
            }

            $bets = Bet::user($this->userId)->where('created_at', '>=', $this->start)
                ->where('created_at', '<', $this->end)
                ->whereIn('id', $ids);

            $output[$typeValue] = $this->tableBody($typeValue, $bets)[$typeValue];
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

        $query = Bet::user($this->userId)->where('created_at', '>=', $this->start)
            ->where('created_at', '<', $this->end);
        $bets = clone $query;

        foreach ($this->odds as $typeValue => $odd) {
            $minMaxOdds = $odd;
            $bets = Bet::user($this->userId)->where('odds', '>', $minMaxOdds['min'])
                ->where('odds', '<=', $minMaxOdds['max'])
                ->where('created_at', '>=', now()->subMonth())
                ->where('created_at', '<', now());
            $output[$typeValue] = $this->tableBody($typeValue, $bets)[$typeValue];
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

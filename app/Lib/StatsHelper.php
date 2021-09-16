<?php

namespace App\Lib;

class StatsHelper
{
    public function getSpecialStatsTabs()
    {
        return  [
            ['name' => 'referee', 'route' => 'referee'],
            ['name' => 'venue', 'route' => 'venue'],
            ['name' => 'competitions', 'route' => 'competition'],
            ['name' => 'teams', 'route' => 'team']
        ];
    }

    public function getStatsTabs()
    {
        return [
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
}

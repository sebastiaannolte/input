<?php

namespace App\Lib;

use Illuminate\Support\Facades\DB;

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
            'profit',
            'odds',
            'selection',
            'bookie',
            'type',
            'stake',
            'sport',
            'tipster',
        ];
    }

    public function statsSelect()
    {
        return DB::raw('count(bets.id) as bets, 
        COUNT(CASE WHEN status = "won" THEN 1 END) AS won, 
        
        sum(stake) AS staked,

        (SUM(CASE WHEN status = "won" THEN odds*stake - stake ELSE 0 END) + 
        SUM(CASE WHEN status = "lost" THEN -stake ELSE 0 END) +
        SUM(CASE WHEN status = "halfwon" THEN ((stake / 2) + (odds / 2)) - stake ELSE 0 END) +
        SUM(CASE WHEN status = "halflost" THEN -(stake / 2) ELSE 0 END)) as profit,

        ((SUM(CASE WHEN status = "won" THEN odds*stake - stake ELSE 0 END) + 
        SUM(CASE WHEN status = "lost" THEN -stake ELSE 0 END) +
        SUM(CASE WHEN status = "halfwon" THEN ((stake / 2) + (odds / 2)) - stake ELSE 0 END) +
        SUM(CASE WHEN status = "halflost" THEN -(stake / 2) ELSE 0 END)) / sum(bets.stake) * 100) as roi
        ');
    }
}

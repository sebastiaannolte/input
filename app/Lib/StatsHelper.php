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

    public function statsSelect($distinct = 'distinct')
    {
        return DB::raw('count(' . $distinct . ' bets.id) as bets, 
        COUNT(CASE WHEN bets.status = "won" or bets.status = "halfwon" THEN 1 END) AS won, 
        
        IFNULL(sum(stake), 0) AS staked,

        ifnull(SUM(CASE WHEN bets.status = "won" THEN odds*stake - stake ELSE 0 END) + 
        SUM(CASE WHEN bets.status = "lost" THEN -stake ELSE 0 END) +
        SUM(CASE WHEN bets.status = "halfwon" THEN ((stake / 2) + (odds / 2)) - stake ELSE 0 END) +
        SUM(CASE WHEN bets.status = "halflost" THEN -(stake / 2) ELSE 0 END), 0) as profit,
        ifnull((
            SUM(CASE WHEN bets.status = "won" THEN odds*stake - stake ELSE 0 END) + 
            SUM(CASE WHEN bets.status = "lost" THEN -stake ELSE 0 END) +
            SUM(CASE WHEN bets.status = "halfwon" THEN ((stake / 2) + (odds / 2)) - stake ELSE 0 END) +
            SUM(CASE WHEN bets.status = "halflost" THEN -(stake / 2) ELSE 0 END)
        ) / sum(stake) * 100, 0) as roi
        ');
    }

    public function advancedStats()
    {
        return DB::raw('
        sum(odds) / count(*) as avgOdds,
        sum(stake) / count(8) as avgStake,
        avg(odds * (stake / 1)) as avgOddsStake
        ');
    }

    public function betValidationTranslations()
    {
        return [
            'games.*.event.required' => 'The event field is required.',
            'games.*.selection.required' => 'The selection field is required.',
            'games.*.date.required' => 'The date field is required.',
            'games.*.category.required' => 'The category field is required.',
        ];
    }
}

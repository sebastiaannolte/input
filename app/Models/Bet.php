<?php

namespace App\Models;

use App\Lib\StatsHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Bet extends Model
{
    use HasFactory;

    public $fillable = ['match_id', 'event', 'selection', 'category', 'bookie', 'stake', 'odds', 'tipster', 'sport', 'date', 'user_id', 'status', 'result', 'type'];

    public function betFixture()
    {
        return $this->hasMany(BetFixture::class, 'bet_id', 'id');
    }

    public function scopeUser($query, $id)
    {
        return $query->where('user_id', $id);
    }

    public function scopeBets($query)
    {
        return $query
            ->leftJoin('bet_fixtures', 'bets.id', '=', 'bet_fixtures.bet_id')
            ->select('bets.stake', 'bets.odds', 'bets.type', 'result', 'bets.status', 'bets.id', 'bookie', 'sport', 'tipster', DB::raw('max(date) as date, GROUP_CONCAT(selection SEPARATOR ", ") as selection, GROUP_CONCAT(event SEPARATOR ", ") as event'))
            ->groupBy('bet_fixtures.bet_id', 'bet.bets.stake',  'bets.odds', 'bets.type', 'result', 'bets.status', 'bets.id')
            ->orderByDesc('date')->orderByDesc('bets.id');
    }

    public function scopeBetCount($query)
    {
        return $query->count();
    }

    public function scopeWonBets($query)
    {
        return $query->where('status', 'won')->orWhere('status', 'halfwon')->count();
    }

    public function scopeWinprecentage($query, $round = false)
    {
        $betCount = $query->clone()->betCount();
        if ($betCount) {
            $winPercentage = $query->clone()->wonBets() / $betCount * 100;
            if ($round) {
                return round($winPercentage, $round);
            }
            return $winPercentage;
        }
        return 0;
    }

    public function scopeBet($query, $id)
    {
        return $query->find($id)
            ->joinBets()
            ->with('betFixture.fixture')
            ->where('bets.id', $id)
            ->select('bets.stake', 'bets.odds', 'bets.type', 'result', 'bets.status', 'bets.id', 'user_id', 'bookie', 'tipster', 'sport', 'bets.created_at', DB::raw('max(date) as date, GROUP_CONCAT(selection SEPARATOR ", ") as selection, GROUP_CONCAT(event SEPARATOR ", ") as event, REPLACE(GROUP_CONCAT(category SEPARATOR "%SEP_ARRAY%"), "]%SEP_ARRAY%[",",") as category'))
            ->groupBy('bet_fixtures.bet_id', 'bet.bets.stake',  'bets.odds', 'bets.type', 'result', 'status', 'bets.id')
            ->orderByDesc('date')->orderByDesc('bets.id');
    }

    public function scopeJoinBets($query)
    {
        return $query
            ->leftJoin('bet_fixtures', 'bets.id', '=', 'bet_fixtures.bet_id');
    }

    public function scopejoinFixtures($query)
    {
        return $query
            ->join('bet_fixtures', 'bets.id', '=', 'bet_fixtures.bet_id')
            ->join('fixtures', 'bet_fixtures.fixture_id', '=', 'fixtures.id');
    }

    public function scopeUnits($query, $round = false)
    {
        $wonUnits = 0;

        foreach ($query->get() as $bet) {
            if ($bet->status == 'won') {
                $wonUnits += ($bet->stake * $bet->odds) - $bet->stake;
            } elseif ($bet->status == 'lost') {
                $wonUnits -= $bet->stake;
            } elseif ($bet->status == 'halfwon') {
                $wonUnits += (($bet->stake / 2) + ($bet->odds / 2)) - $bet->stake;
            } elseif ($bet->status == 'halflost') {
                $wonUnits -= ($bet->stake / 2);
            }
        }
        if ($round) {
            return round($wonUnits, $round);
        }
        return  $wonUnits;
    }

    public function scopeTotalStaked($query, $round = false)
    {
        $totalStaked = $query->sum('stake');

        if ($round) {
            return round($totalStaked, $round);
        }

        return $totalStaked;
    }

    public function scopeAvgStake($query, $round = false)
    {
        $betCount = $query->clone()->betCount();
        if ($betCount) {
            $avgStake = $query->clone()->totalStaked() / $betCount;
            if ($round) {
                return round($avgStake, $round);
            }
            return $avgStake;
        }
        return 0;
    }

    public function scopeAvgOdds($query, $round = false)
    {
        $betCount = $query->clone()->betCount();
        if ($betCount) {
            $avgOdds = $query->sum('odds') / $betCount;
            if ($round) {
                return round($avgOdds, $round);
            }
            return $avgOdds;
        }
        return 0;
    }

    public function scopeAvgResult($query, $round = false)
    {
        $betCount = $query->clone()->betCount();
        if ($betCount) {
            $avgResult = $query->units() / $betCount;
            if ($round) {
                return round($avgResult, $round);
            }
            return $avgResult;
        }

        return 0;
    }

    public function scopeAvgOddsStake($query, $round = false)
    {
        $betCount = $query->clone()->betCount();

        if ($betCount) {

            $avgOddsStake =  $query->selectRaW('avg(odds * (stake / 1)) as odds')->pluck('odds')->first();
            if ($round) {
                return round($avgOddsStake, $round);
            }
            return $avgOddsStake;
        }
        return 0;
    }

    public function scopeRoi($query, $round = false)
    {
        $wonUnits = $query->clone()->units();
        $roi = 0;
        if ($wonUnits) {
            $roi = $wonUnits / $query->clone()->totalStaked() * 100;
        }
        if ($round) {
            return round($roi, $round);
        }
        return $roi;
    }

    public function scopeRound($query)
    {
        return round($query, 2);
    }

    public function scopeFilters($query, $filters)
    {
        $filtersWithValue = collect($filters)->filter(function ($filter) {
            if (is_array($filter) && array_key_exists('value', $filter) && $filter['value'] != null) {
                return $filter;
            }
        });

        foreach ($filtersWithValue as $key => $filter) {
            if ($filter['type'] == 'match') {
                $query->where($filter['col'], $filter['value']);
            } elseif ($filter['type'] == 'like') {
                $query->where($filter['col'], 'LIKE', "%{$filter['value']}%");
            } elseif ($filter['type'] == 'max') {
                if (array_key_exists('specialType', $filter) && $filter['specialType'] == 'date') {
                    $query->where($filter['col'], '<=', Carbon::parse($filter['value'])->endOfDay());
                } else {
                    $query->where($filter['col'], '<=', $filter['value']);
                }
            } elseif ($filter['type'] == 'min') {
                if (array_key_exists('specialType', $filter) && $filter['specialType'] == 'date') {
                    $query->where($filter['col'], '>=', Carbon::parse($filter['value'])->startOfDay());
                } else {
                    $query->where($filter['col'], '>=', $filter['value']);
                }
            }
        }
        return $query;
    }
}

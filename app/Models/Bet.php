<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Bet extends Model
{
    use HasFactory;

    public $fillable = ['match_id', 'event', 'selection', 'bookie', 'stake', 'odds', 'tipster', 'sport', 'date', 'user_id', 'status', 'result'];

    public function scopeUser($query, $id)
    {
        return $query->where('user_id', $id);
    }

    public function scopeBets($query)
    {
        return $query->orderByDesc('date')->orderByDesc('id')->get();
    }

    public function scopeBetCount($query)
    {
        return $query->count();
    }

    public function scopeWonBets($query)
    {
        return $query->where('status', 'won')->count();
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

    public function scopeUnits($query, $round = false)
    {
        $wonUnits = 0;

        foreach ($query->get() as $bet) {
            if ($bet->status == 'won') {
                $wonUnits += ($bet->stake * $bet->odds) - $bet->stake;
            } elseif ($bet->status == 'lost') {
                $wonUnits -= $bet->stake;
            }
        }
        if($round){
            return round($wonUnits, $round);
        }
        return  $wonUnits;
    }

    public function scopeTotalStaked($query)
    {
        return $query->sum('stake');
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
}

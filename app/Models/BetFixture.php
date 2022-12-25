<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BetFixture extends Model
{
    use HasFactory;

    public $fillable = ['bet_id', 'fixture_id', 'event', 'selection', 'stake', 'odds', 'date', 'category', 'status', 'created_at', 'updated_at'];

    public function fixture()
    {
        return $this->hasOne(Fixture::class, 'id', 'fixture_id');
    }

    public function bet()
    {
        return $this->hasOne(Bet::class, 'id', 'bet_id');
    }

    public function scopeSearchFilter($query, $allBets)
    {
        if ($allBets) {
            return $query;
        }

        return $query->where('status', 'new');
    }

}

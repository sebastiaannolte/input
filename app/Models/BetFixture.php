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

    public function scopeSearchFilter($query, $filters)
    {
        if (!$filters['allBets']) {
            $query->where('status', 'new');
        }

        if($filters['type'] == 'event'){
            $query->where('event', 'like', '%'.$filters['query'].'%');
        } elseif($filters['type'] == 'competition'){
            $query->whereHas('fixture.league', function ($query) use ($filters){
                return $query->where('name', 'LIKE', '%'.$filters['query'].'%');
            });
        }


        return $query;
    }

}

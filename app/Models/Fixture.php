<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{
    use HasFactory;

    public $fillable = ['id', 'home_team', 'away_team', 'referee', 'timezone', 'date', 'league_id', 'venue_id'];

    public function homeTeam()
    {
        return $this->hasOne(Team::class, 'id', 'home_team');
    }

    public function awayTeam()
    {
        return $this->hasOne(Team::class, 'id', 'away_team');
    }

    public function bets()
    {
        return $this->hasMany(Bet::class, 'match_id', 'id');
    }

    public function venue()
    {
        return $this->hasOne(Venue::class, 'id', 'venue_id');
    }
}

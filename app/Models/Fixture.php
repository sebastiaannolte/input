<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{
    use HasFactory;

    public $fillable = ['id', 'home_team', 'away_team', 'referee', 'timezone', 'date', 'first_half', 'second_half'];

    public function homeTeam()
    {
        return $this->hasOne(Team::class, 'id', 'home_team');
    }

    public function awayTeam()
    {
        return $this->hasOne(Team::class, 'id', 'away_team');
    }
}

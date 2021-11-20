<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamLeague extends Model
{
    use HasFactory;

    public $fillable = ['team_id', 'league_id', 'sport'];

    public $table = 'teams_leagues';
}

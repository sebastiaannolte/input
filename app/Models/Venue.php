<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    public $fillable = ['id', 'venue_id', 'name', 'city', 'sport'];

    public function fixtures()
    {
        return $this->hasMany(Fixture::class, 'league_id', 'id');
    }
}

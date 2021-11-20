<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    use HasFactory;

    public $fillable = ['id', 'league_id', 'name', 'country', 'logo', 'flag', 'season', 'round', 'sport'];

    public function fixtures()
    {
        return $this->hasMany(Fixture::class, 'league_id', 'id');
    }

    public function betCount()
    {
        $fixtures = $this->fixtures();
    }
}

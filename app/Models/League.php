<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    use HasFactory;

    public $fillable = ['id', 'name', 'country', 'logo', 'flag', 'season', 'round'];

    public function fixtures()
    {
        return $this->hasMany(Fixture::class, 'league_id', 'id');
    }

    public function betCount()
    {
        $fixtures = $this->fixtures();
        dd($fixtures);
    }
}

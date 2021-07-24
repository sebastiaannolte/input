<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    use HasFactory;

    public $fillable = ['id', 'name', 'country', 'logo', 'flag', 'season', 'round'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    use HasFactory;

    public $fillable = ['bookie_id', 'data', 'is_completed'];

    protected $casts = [
        'data' => 'object',
        'is_completed' => 'boolean',
    ];
}

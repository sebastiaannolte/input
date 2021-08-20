<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BetType extends Model
{
    use HasFactory;

    public $fillable = ['id', 'name'];

    public function scopeBets()
    {
        return $this->whereJsonContains('category', $this->id);
    }
}

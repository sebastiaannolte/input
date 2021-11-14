<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BetType extends Model
{
    use HasFactory;

    public $fillable = ['id', 'name', 'type_id', 'sport'];

    public function scopeBets()
    {
        return $this->whereJsonContains('category', $this->id);
    }
}

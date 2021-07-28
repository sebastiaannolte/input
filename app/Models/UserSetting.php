<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    use HasFactory;
    public $fillable = ['user_id', 'setting_id', 'value'];
    protected $primaryKey = 'setting_id';

    public function setting()
    {
        return $this->hasOne(Setting::class, 'id', 'setting_id');
    }
}

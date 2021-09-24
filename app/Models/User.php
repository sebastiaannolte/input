<?php

namespace App\Models;

use App\Lib\StatsHelper;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userSettings()
    {
        return $this->hasMany(UserSetting::class);
    }

    public function userSettingsFormatted()
    {

        $userSettings = $this->userSettings()->get();
        $userSettings = $userSettings->mapWithKeys(function ($setting) {
            return [$setting->setting_id => $setting->value];
        });
        $settings = Setting::get();
        
        return $settings->mapWithKeys(function ($setting) use ($userSettings) {
            return [$setting->name => ['value' => array_key_exists($setting->id, $userSettings->toArray()) ? ($setting->type == 'array' ? json_decode($userSettings[$setting->id]) : $userSettings[$setting->id]) : null, 'type' => $setting->type]];
        });
    }
}

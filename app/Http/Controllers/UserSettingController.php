<?php

namespace App\Http\Controllers;

use App\Lib\StatsHelper;
use App\Models\Bookmaker;
use App\Models\Setting;
use App\Models\UserSetting;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class UserSettingController extends Controller
{
    public function index()
    {
        return Inertia::render('UserSettings', [
            'bookmakers' => Bookmaker::get(),
        ]);
    }

    public function store()
    {
        foreach (Request::all() as $key => $value) {
            $setting = Setting::where('name', $key)->first();
            $value = $value['value'];
            if ($setting->type == 'array') {
                $value = json_encode($value);
            }
            UserSetting::updateOrCreate(
                ['user_id' => Auth::user()->id, 'setting_id' => $setting->id],
                ['value' => $value]
            );
        }
        return Redirect::back()->with('success', 'Settings updated');
    }
}

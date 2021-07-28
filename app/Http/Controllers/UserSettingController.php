<?php

namespace App\Http\Controllers;

use App\Models\Bet;
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
        return Inertia::render('UserSettings', []);
    }

    public function store()
    {
        foreach (Request::all() as $key => $value) {

            $setting = Setting::where('name', $key)->first();
            $userSetting = UserSetting::updateOrCreate(
                ['user_id' => Auth::user()->id, 'setting_id' => $setting->id],
                ['value' => $value]
            );
        }

        return Redirect::route('userSettings.index', Auth::user()->username);
    }
}

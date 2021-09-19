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
        $stats = new StatsHelper;
        $specialTabNames = collect($stats->getSpecialStatsTabs())->map(function ($value) {
            return $value['name'];
        });
        $specialTabsSettings = Auth::user()->userSettingsFormatted()['special_tabs']['value'];
        $statsTabsSettings = Auth::user()->userSettingsFormatted()['stats_tabs']['value'];
        $specialTabs = array_diff($specialTabNames->toArray(), $specialTabsSettings );
        $statsTabs = array_diff($stats->getStatsTabs(), $statsTabsSettings );

        return Inertia::render('UserSettings', [
            'bookmakers' => Bookmaker::get(),
            'specialTabs' => array_values($specialTabs),
            'statsTabs' => array_values($statsTabs),
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

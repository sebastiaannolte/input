<?php

namespace App\Http\Middleware;

use App\Models\Bet;
use App\Models\BetType;
use App\Models\Import;
use App\Models\Setting;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        $authenticatedUser =  User::where('username',  explode("/", $request->path())[0])->first();
        $user = $request->user() && $request->user()->username != $authenticatedUser && $authenticatedUser ? $authenticatedUser : $request->user();

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
                'settings' => $request->user() ? $request->user()->userSettingsFormatted() : null,
            ],
            'userInfo' =>
            [
                'myPage' => $request->user() ? $request->user()->username == explode("/", $request->path())[0] : false,
                'user' => $user,
            ],
            'betTypes' => BetType::get()->groupBy('sport'),
            'sports' => Sport::get(),
            'importCounter' => Import::where('is_completed', 0)->count(),
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                ];
            },
            'openBetCount' => Bet::whereNull('result')->count()
        ]);
    }
}

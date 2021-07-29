<?php

namespace App\Lib;

use App\Models\Fixture;
use App\Models\League;
use App\Models\Team;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GamesApi
{
    public static function api()
    {
        $response = Http::withHeaders([
            'x-rapidapi-host' => 'api-football-v1.p.rapidapi.com',
            'x-rapidapi-key' => '***REMOVED***'
        ])->get('https://api-football-v1.p.rapidapi.com/v3/fixtures', [
            'date' => '2021-07-30',
            "timezone" => "Europe/Amsterdam"
        ]);

        // dd($response);
        // echo ($response->body());
        return $response->body();
    }

    public static function get()
    {
        // $json = file_get_contents(storage_path() . '/games.json');
        $json = self::api();
        $games = json_decode($json, true)['response'];

        foreach ($games as $key => $game) {
            $venue = $game['fixture']['venue'];
            $league = $game['league'];
            $teams = $game['teams'];
            $fixture = $game['fixture'];
            if ($venue['id']) {
                Venue::updateOrCreate(
                    [
                        'id' => $venue['id'],
                    ],
                    [
                        'name' => $venue['name'],
                        'city' => $venue['city'],
                    ]
                );
            }


            League::updateOrCreate(
                [
                    'id' => $league['id'],
                ],
                [
                    'name' => $league['name'],
                    'country' => $league['country'],
                    'logo' => $league['logo'],
                    'flag' => $league['flag'],
                    'season' => $league['season'],
                    'round' => $league['round'],
                ]
            );
            // Home team
            Team::updateOrCreate(
                [
                    'id' => $teams['home']['id'],
                ],
                [
                    'name' => $teams['home']['name'],
                    'logo' => $teams['home']['logo'],
                ]
            );

            // Away team
            Team::updateOrCreate(
                [
                    'id' => $teams['away']['id'],
                ],
                [
                    'name' => $teams['away']['name'],
                    'logo' => $teams['away']['logo'],
                ]
            );

            Fixture::updateOrCreate(
                [
                    'id' => $fixture['id'],
                ],
                [
                    'home_team' => $teams['home']['id'],
                    'away_team' => $teams['away']['id'],
                    'referee' => $fixture['referee'],
                    'timezone' => $fixture['timezone'],
                    'date' => Carbon::parse($fixture['date']),
                    'first_half' => $fixture['periods']['first'],
                    'second_half' => $fixture['periods']['second'],

                ]
            );
        }
    }

    public static function search($keyword)
    {
        $fixtures = Fixture::with(['homeTeam', 'awayTeam'])->where('date', '>', now()->subDays(1)->startOfDay()->format('Y-m-d H:i:s'))->get();

        $fixtures = $fixtures->filter(function ($item) use ($keyword) {
            $hasV = array_filter(explode(' v ', $keyword));
            $hasV2 = array_filter(explode(' v', $keyword));

            if (count($hasV) == 2) {
                if (strpos(strtolower($item->homeTeam->name), strtolower($hasV[0])) !== false && strpos(strtolower($item->awayTeam->name), strtolower($hasV[1])) !== false) {
                    return $item;
                }

                if (strpos(strtolower($item->awayTeam->name), strtolower($keyword)) !== false) {
                    return $item;
                }
            } elseif (count($hasV2) == 1) {
                if (!$item->homeTeam) {
                    dd($item);
                }
                if (strpos(strtolower($item->homeTeam->name), strtolower($hasV2[0])) !== false) {
                    return $item;
                }
            }

            if (strpos(strtolower($item->homeTeam->name), strtolower($keyword)) !== false) {
                return $item;
            }

            if (strpos(strtolower($item->awayTeam->name), strtolower($keyword)) !== false) {
                return $item;
            }
        });

        $output = $fixtures->map(function ($item) {
            return ['match' => $item->homeTeam->name . ' v ' . $item->awayTeam->name, 'id' => $item->id];
        });

        return $output;
    }

    public static function match($matchId)
    {
        return Fixture::find($matchId);
    }
}

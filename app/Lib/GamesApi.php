<?php

namespace App\Lib;

use App\Models\Bet;
use App\Models\BetType;
use App\Models\Bookmaker;
use App\Models\Fixture;
use App\Models\League;
use App\Models\Team;
use App\Models\TeamLeague;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

class GamesApi
{
    public static function api($date)
    {
        $response = Http::withHeaders([
            'x-rapidapi-host' => 'api-football-v1.p.rapidapi.com',
            'x-rapidapi-key' => '***REMOVED***'
        ])->get('https://api-football-v1.p.rapidapi.com/v3/fixtures', [
            'date' => $date,
            "timezone" => "Europe/Amsterdam"
        ]);


        return $response->body();
    }

    public static function get($date)
    {
        // $json = file_get_contents(storage_path() . '/games.json');
        $json = self::api($date);
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

            TeamLeague::updateOrCreate(
                [
                    'team_id' => $teams['home']['id'],
                    'league_id' => $league['id'],
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

            TeamLeague::updateOrCreate(
                [
                    'team_id' => $teams['away']['id'],
                    'league_id' => $league['id'],
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
                    'league_id' => $league['id'],
                    'venue_id' => $venue['id'] ?: NULL,
                    'timezone' => $fixture['timezone'],
                    'date' => Carbon::parse($fixture['date']),

                ]
            );
        }
        return response()
            ->json(['message' => count($games) . ' games found']);
    }

    public static function getBookmakers($parameters = false)
    {
        $response = Http::withHeaders([
            'x-rapidapi-host' => 'api-football-v1.p.rapidapi.com',
            'x-rapidapi-key' => '***REMOVED***'
        ])->get('https://api-football-v1.p.rapidapi.com/v3/odds/bookmakers');


        $json =  $response->body();

        $bookmakers = json_decode($json, true)['response'];

        foreach ($bookmakers as $key => $bookmaker) {

            Bookmaker::updateOrCreate(
                [
                    'id' => $bookmaker['id'],
                ],
                [
                    'name' => $bookmaker['name'],
                ]
            );
        }
    }

    public static function getBetTypes()
    {
        $response = Http::withHeaders([
            'x-rapidapi-host' => 'api-football-v1.p.rapidapi.com',
            'x-rapidapi-key' => '***REMOVED***'
        ])->get('https://api-football-v1.p.rapidapi.com/v3/odds/bets');


        $json =  $response->body();

        $betTypes = json_decode($json, true)['response'];
        foreach ($betTypes as $key => $betType) {
            if (!$betType['name']) {
                continue;
            }
            BetType::updateOrCreate(
                [
                    'id' => $betType['id'],
                ],
                [
                    'name' => $betType['name'],
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

    public static function findPageOfBet($id)
    {
        $filters = Request::get('filters');
        $bets = Bet::bets()->filters($filters)->get();

        foreach ($bets as $index => $bet) {
            if ($bet->id == $id) {
                $index++;
                return ceil($index / 15);
            }
        }
    }
}

<?php

namespace App\Lib;

use App\Models\Bet;
use App\Models\BetFixture;
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
    public static function get($date, $sport)
    {
        // $json = file_get_contents(storage_path() . '/games.json');
        $responses = [
            'football' => Http::withHeaders([
                'x-rapidapi-host' => 'api-football-v1.p.rapidapi.com',
                'x-rapidapi-key' => 'd6161131a5msh70042df8c8732dbp158a44jsn4011b4c5834b'
            ])->get('https://api-football-v1.p.rapidapi.com/v3/fixtures', [
                'date' => $date,
                "timezone" => "Europe/Amsterdam"
            ]),

            'basketball' => Http::withHeaders([
                'x-rapidapi-host' => 'api-basketball.p.rapidapi.com',
                'x-rapidapi-key' => 'd6161131a5msh70042df8c8732dbp158a44jsn4011b4c5834b'
            ])->get('https://api-basketball.p.rapidapi.com/games', [
                'date' => $date,
                "timezone" => "Europe/Amsterdam"
            ])
        ];

        foreach ($responses as $responseSport => $response) {
            if($sport != $responseSport){
                continue;
            }

            $json = $response->body();
            $games = json_decode($json, true)['response'];

            foreach ($games as $key => $game) {
                $fixture = array_key_exists('fixture', $game) ? $game['fixture']: $game;
                $venue = array_key_exists('venue', $fixture)  ? $fixture['venue'] : NULL;
                $league = $game['league'];
                $teams = $game['teams'];
               
                if ($venue && $venue['id']) {
                    $addedVenue = Venue::updateOrCreate(
                        [
                            'venue_id' => $venue['id'],
                            'sport' => $sport,
                        ],
                        [
                            'name' => $venue['name'],
                            'city' => $venue['city'],
                        ]
                    );
                }


                $addedLeague = League::updateOrCreate(
                    [
                        'league_id' => $league['id'],
                        'sport' => $sport
                    ],
                    [
                        'name' => $league['name'],
                        'country' => array_key_exists('country', $league) ? $league['country'] :  $game['country']['name'],
                        'logo' => $league['logo'],
                        'flag' => array_key_exists('flag', $league) ? $league['flag'] :  $game['country']['flag'],
                        'season' => $league['season'],
                        'round' => array_key_exists('round', $league) ? $league['round'] : NULL,
                    ]
                );
                // Home team
                $addedHomeTeam = Team::updateOrCreate(
                    [
                        'team_id' => $teams['home']['id'],
                        'sport' => $sport
                    ],
                    [
                        'name' => $teams['home']['name'],
                        'logo' => $teams['home']['logo'],
                    ]
                );

                TeamLeague::updateOrCreate(
                    [
                        'team_id' => $addedHomeTeam->id,
                        'league_id' => $addedLeague->id,
                        'sport' => $sport,
                    ]
                );

                // Away team
                $addedAwayTeam = Team::updateOrCreate(
                    [
                        'team_id' => $teams['away']['id'],
                        'sport' => $sport
                    ],
                    [
                        'name' => $teams['away']['name'],
                        'logo' => $teams['away']['logo'],
                    ]
                );

                TeamLeague::updateOrCreate(
                    [
                        'team_id' => $addedAwayTeam->id,
                        'league_id' => $addedLeague->id,
                        'sport' => $sport,
                    ]
                );


                Fixture::updateOrCreate(
                    [
                        'fixture_id' => $fixture['id'],
                        'sport' => $sport,
                    ],
                    [
                        'home_team' => $addedHomeTeam->id,
                        'away_team' => $addedAwayTeam->id,
                        'referee' => array_key_exists('referee', $fixture) ? $fixture['referee'] ?: null : NULL,
                        'league_id' => $addedLeague->id,
                        'venue_id' => array_key_exists('venue_id', $fixture) ? $addedVenue->id  ?: null : NULL,
                        'timezone' => $fixture['timezone'],
                        'date' => Carbon::parse($fixture['date']),

                    ]
                );
            }
        }
        return response()
            ->json(['message' => count($games) . ' games found']);
    }

    public static function getBookmakers($parameters = false)
    {
        $response = Http::withHeaders([
            'x-rapidapi-host' => 'api-football-v1.p.rapidapi.com',
            'x-rapidapi-key' => 'd6161131a5msh70042df8c8732dbp158a44jsn4011b4c5834b'
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
        $responses = [
            'football' => Http::withHeaders([
                'x-rapidapi-host' => 'api-football-v1.p.rapidapi.com',
                'x-rapidapi-key' => 'd6161131a5msh70042df8c8732dbp158a44jsn4011b4c5834b'
            ])->get('https://api-football-v1.p.rapidapi.com/v3/odds/bets'),

            'basketball' => Http::withHeaders([
                'x-rapidapi-host' => 'api-basketball.p.rapidapi.com',
                'x-rapidapi-key' => 'd6161131a5msh70042df8c8732dbp158a44jsn4011b4c5834b'
            ])->get('https://api-basketball.p.rapidapi.com/bets')
        ];

        foreach ($responses as $sport => $response) {
            $json =  $response->body();
            $betTypes = json_decode($json, true)['response'];
            foreach ($betTypes as $key => $betType) {
                if (!$betType['name']) {
                    continue;
                }

                BetType::updateOrCreate(
                    [
                        'type_id' => $betType['id'],
                        'name' => $betType['name'],
                        'sport' => $sport,
                    ],
                    [
                        'type_id' => $betType['id'],
                        'name' => $betType['name'],
                        'sport' => $sport,
                    ]
                );
            }
        }
    }

    public static function search($keyword, $type, $sport)
    {
        $daysBefore = 1;
        if ($type == "full") {
            $daysBefore = 10;
        }
        $fixtures = Fixture::with(['homeTeam', 'awayTeam'])
            ->where('date', '>', now()->subDays($daysBefore)->startOfDay()->format('Y-m-d H:i:s'))
            ->where('sport', $sport)
            ->get();

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
            return ['match' => $item->homeTeam->name . ' v ' . $item->awayTeam->name, 'id' => $item->id, 'icon' => $item->league->flag ?: $item->league->logo];
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

    public static function fixUpdatev2()
    {
        $bets = Bet::get();

        foreach ($bets as $key => $bet) {
            BetFixture::create([
                'bet_id' => $bet->id,
                'fixture_id' => $bet->match_id,
                'event' => $bet->event,
                'selection' => $bet->selection,
                'category' => $bet->category,
                'status' => $bet->status,
                'date' => $bet->date,
                'created_at' => $bet->created_at,
                'updated_at' => $bet->updated_at,
            ]);
        }
    }
}

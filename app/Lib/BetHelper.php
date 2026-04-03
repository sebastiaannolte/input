<?php

namespace App\Lib;

use App\Models\Bet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class BetHelper
{
    public function updateStatus(Bet $bet, $status)
    {
        switch ($status) {
            case 'won':
                $result = '+' . number_format(($bet->stake * $bet->odds), 2, '.', '');
                break;
            case 'lost':
                $result = '-' . number_format($bet->stake, 2, '.', '');
                break;
            case 'void':
                $result = 0;
                break;
            case 'halfwon':
                if ($bet->bookie == 'Unibet') {
                    $newOdds = number_format((($bet->odds + 1) / 2), 2, '.', '');
                    $result = '+' . number_format($newOdds * $bet->stake, 2, '.', '');
                    break;
                }
                $result = '+' . number_format(($bet->stake / 2 + $bet->odds / 2), 2, '.odds', '');
                break;
            case 'halflost':
                $result = '-' . number_format(($bet->stake / 2), 2, '.', '');
                break;
            default:
                $result = null;
                break;
        }

        $bet->betFixture()->update(['status' => $status]);
        $bet->update([
            'result' => $result,
            'status' => $status,
        ]);
    }

    public function validateBet($data)
    {
        $data['sport'] = 'football';
        $data['type'] = 'prematch';
        $validator = Validator::make($data, [
            'bookie' => ['required', 'max:50'],
            'tipster' => ['required', 'max:50'],
            'sport' => ['required', 'max:50'],
            'type' => ['required', 'max:50'],
            'stake' => ['required', 'max:50'],
            'odds' => ['required', 'max:50'],
            // games
            'games' => ['required', 'array'],
            'games.*.event' => ['required', 'max:50'],
            'games.*.selection' => ['required', 'max:255'],
            'games.*.date' => ['required', 'max:50'],
            'games.*.category' => ['required', 'max:50'],
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return true;
    }

    public function createBet($data)
    {
        $data['sport'] = 'football';
        $data['type'] = 'prematch';
        $bet = Bet::create([
            'bookie_id' => $data['bookieId'],
            'bookie' => $data['bookie'],
            'tipster' => $data['tipster'],
            'sport' => $data['sport'],
            'type' => $data['type'],
            'stake' => $data['stake'],
            'odds' => $data['odds'],
            'user_id' => 1,
            'status' => $data['status'],
        ]);

        foreach ($data['games'] as $game) {
            $bet->betFixture()->create([
                'fixture_id' => null,
                'event' => $game['event'],
                'selection' => $game['selection'],
                'date' => Carbon::parse($game['date'])->setTimezone(config('app.timezone'))->toDateTimeString(),
                'category' => $game['category'],
                'status' => 'new',
            ]);
        }

        return $bet;
    }
}

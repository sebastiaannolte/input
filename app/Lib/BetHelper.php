<?php

namespace App\Lib;

use App\Models\Bet;

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
                $result = '+' . number_format(($bet->stake / 2 + $bet->odds / 2), 2, '.', '');
                break;
            case 'halflost':
                $result = '-' . number_format(($bet->stake / 2), 2, '.', '');
                break;
            default:
                $result = 0;
                break;
        }

        $bet->betFixture()->update(['status' => $status]);
        $bet->update([
            'result' => $result,
            'status' => $status,
        ]);
    }
}

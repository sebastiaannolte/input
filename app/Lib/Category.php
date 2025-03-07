<?php

namespace App\Lib;

class Category
{
    public function get($input)
    {
        if (empty($input)) {
            return null;
        }

        $mapping = [
            'Asian Totaal: ' => 5,
            'Asian Handicap: ' => 4,
            'Reguliere Speeltijd: ' => 1,
            'Scoort: ' => 83,
            'Scoort ten minste 2 doelpunten: ' => 323,
            'Scoort ten minste 3 doelpunten: ' => 324,
            'Draw No Bet:  ' => 544,
            'Scoort of Geeft Een Assist ' => 364,
            'Schoten van Speler op Doel, ' => 371,
        ];

        foreach ($mapping as $key => $value) {
            if (str_starts_with($input, $key)) {
                return $value;
            }
        }
    }
}

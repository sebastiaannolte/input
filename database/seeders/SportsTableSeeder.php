<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sports')->insert(
            [
                [
                    'id' => 1,
                    'name' => 'football',
                ],
                [
                    'id' => 2,
                    'name' => 'basketball',
                ],
                [
                    'id' => 3,
                    'name' => 'american_football',
                ]
            ]
        );
    }
}
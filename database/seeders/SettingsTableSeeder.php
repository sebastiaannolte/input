<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert(
            [
                [
                    'id' => 1,
                    'name' => 'tipster',
                    'type' => 'string',
                ],
                [
                    'id' => 3,
                    'name' => 'bookmakers',
                    'type' => 'array',
                ],
                [
                    'id' => 4,
                    'name' => 'special_tabs',
                    'type' => 'array',
                ],
                [
                    'id' => 5,
                    'name' => 'stats_tabs',
                    'type' => 'array',
                ]
            ]
        );
    }
}
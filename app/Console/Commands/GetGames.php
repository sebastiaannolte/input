<?php

namespace App\Console\Commands;

use App\Lib\GamesApi;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;

class GetGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:games';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $stats = new GamesApi;

        // $period = CarbonPeriod::create('2021-08-09', '2021-08-14');
        // foreach ($period as $date) {
            // dump($date->format('Y-m-d'));
            $stats->get('2021-11-10');
        // }
    }
}

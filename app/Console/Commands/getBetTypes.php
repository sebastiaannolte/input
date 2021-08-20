<?php

namespace App\Console\Commands;

use App\Lib\GamesApi;
use Illuminate\Console\Command;

class getBetTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:bettypes';

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
        $stats->getBetTypes();
    }
}

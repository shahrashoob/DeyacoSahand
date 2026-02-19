<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1004Controller;
use App\Http\Controllers\Utility\Script\Script1005Controller;
use Illuminate\Console\Command;

class Script1005Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1005';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Script 1005: Update Report1010MachineLogs Value';

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
        Script1005Controller::handle();
    }
}

<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1003Controller;
use App\Http\Controllers\Utility\Script\ScriptController;
use Illuminate\Console\Command;

class Script1003Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1003';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Script 1003: Calculate Inventory and set to Product_Inventory table';

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
        Script1003Controller::handle();
    }
}

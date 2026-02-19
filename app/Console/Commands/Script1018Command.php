<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1018Controller;
use App\Http\Controllers\Utility\Script\ScriptController;
use Illuminate\Console\Command;

class Script1018Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1018';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Script 1018: Calculate Inventory For Report 1003';

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
        Script1018Controller::handle();
    }
}

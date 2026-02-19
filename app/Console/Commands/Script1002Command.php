<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1002Controller;
use App\Http\Controllers\Utility\Script\ScriptController;
use Illuminate\Console\Command;

class Script1002Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1002';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Script 1002: Send Inventory SMS to Post';

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
        Script1002Controller::handle();
    }
}

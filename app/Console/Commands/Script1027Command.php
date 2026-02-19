<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1027Controller;
use Illuminate\Console\Command;

class Script1027Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1027';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MachineType: Check Max Stop in production status for machines';

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
        Script1027Controller::handle(); return 0;
    }
}

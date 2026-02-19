<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1006Controller;
use Illuminate\Console\Command;

class Script1006Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1006';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Script 1006: Calculate Efficiency(randoman) From Datetime';

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
        Script1006Controller::handle();
    }
}

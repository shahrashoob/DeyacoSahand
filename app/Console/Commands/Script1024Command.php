<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1024Controller;
use Illuminate\Console\Command;

class Script1024Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature ='script:s1024';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Contractors: Calculate Actual Consumption';

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
        Script1024Controller::handle();
    }
}

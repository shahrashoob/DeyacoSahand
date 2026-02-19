<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1001Controller;
use Illuminate\Console\Command;

class Script1001Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1001';

    /**
     * The console command description.
     * پیش بینی مدت زمان تولید، عملی و تثئری
     * @var string
     */
    protected $description = 'Script 1001: Prediction of production time, practical and theoretical';

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
        Script1001Controller::handle();
    }
}

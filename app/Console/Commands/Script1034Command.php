<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1034Controller;
use Illuminate\Console\Command;

class Script1034Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1034';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Digital expert calculates average consumption amount';

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
        Script1034Controller::handle(); return 0;
    }
}
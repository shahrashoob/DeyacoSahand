<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1030Controller;
use Illuminate\Console\Command;

class Script1030Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1030';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Digital Supply Planning Specialist at Diako';

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
        Script1030Controller::handle(); return 0;
    }
}
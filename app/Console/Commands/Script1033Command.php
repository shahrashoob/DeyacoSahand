<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1033Controller;
use Illuminate\Console\Command;

class Script1033Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1033';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Digital Planning Specialist at Diyaco';

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
        Script1033Controller::handle();
        return 0;
    }
}
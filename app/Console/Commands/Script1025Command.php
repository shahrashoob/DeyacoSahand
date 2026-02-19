<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1025Controller;
use Illuminate\Console\Command;

class Script1025Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1025';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'HR: Update User Status';

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
        Script1025Controller::handle(); return 0;
    }
}

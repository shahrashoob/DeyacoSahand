<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1026Controller;
use Illuminate\Console\Command;

class Script1026Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1026';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Production: Create And Allocation Production Card';

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
        Script1026Controller::handle(); return 0;
    }
}

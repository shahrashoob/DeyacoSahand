<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1004Controller;
use Illuminate\Console\Command;

class Script1004Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1004';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Script 1004: Calculate Production From Datetime';

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
        Script1004Controller::handle();
    }
}

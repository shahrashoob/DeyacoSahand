<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1009Controller;
use Illuminate\Console\Command;

class Script1009Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1009';

    /**
     * The console command description.
     * شارژ انبارک ماشین
     * @var string
     */
    protected $description = 'Script 1009: Register consumption transaction ';

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
        Script1009Controller::handle();
    }
}

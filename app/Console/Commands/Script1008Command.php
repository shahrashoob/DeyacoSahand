<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1008Controller;
use Illuminate\Console\Command;

class Script1008Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1008';

    /**
     * The console command description.
     * شارژ انبارک ماشین
     * @var string
     */
    protected $description = 'Script 1008: Entry Form ';

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
        Script1008Controller::handle();
    }
}

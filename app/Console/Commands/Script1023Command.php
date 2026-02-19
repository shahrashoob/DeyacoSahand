<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1023Controller;
use Illuminate\Console\Command;

class Script1023Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1023';

    /**
     * The console command description.
     * ناظر هوشمند
     * @var string
     */
    protected $description = 'HR : Calculating User Operation Time';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        Script1023Controller::handle();
    }
}

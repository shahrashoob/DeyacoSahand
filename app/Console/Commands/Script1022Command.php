<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1022Controller;
use Illuminate\Console\Command;

class Script1022Command extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1022';

    /**
     * The console command description.
     * ناظر هوشمند
     * @var string
     */
    protected $description = 'PackingForm : Calculating the status of packages';

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
        Script1022Controller::handle();
    }
}

<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1020Controller;
use Illuminate\Console\Command;

class Script1020Command extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1020';

    /**
     * The console command description.
     * ناظر هوشمند
     * @var string
     */
    protected $description = 'From Item Currency : Calculate Currency For Each FromItem';

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

        Script1020Controller::handle();
    }
}

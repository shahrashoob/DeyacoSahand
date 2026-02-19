<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1021Controller;
use Illuminate\Console\Command;

class Script1021Command extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1021';

    /**
     * The console command description.
     * ناظر هوشمند
     * @var string
     */
    protected $description = 'Queue For Large : Queue for large calculation';

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

        Script1021Controller::handle();
    }
}

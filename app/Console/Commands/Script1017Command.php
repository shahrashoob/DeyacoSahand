<?php

namespace App\Console\Commands;


use App\Http\Controllers\Utility\Script\Script1016Controller;
use App\Http\Controllers\Utility\Script\Script1017Controller;
use Illuminate\Console\Command;

class Script1017Command extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1017';

    /**
     * The console command description.
     * ناظر هوشمند
     * @var string
     */
    protected $description = 'Shift Delivery : Shift Delivery 1017';

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

        Script1017Controller::handle();
    }
}

<?php

namespace App\Console\Commands;


use App\Http\Controllers\Utility\Script\Script1019Controller;
use Illuminate\Console\Command;

class Script1019Command extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1019';

    /**
     * The console command description.
     * ناظر هوشمند
     * @var string
     */
    protected $description = 'Production Module : Return To Warehouse Module';

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

        Script1019Controller::handle();
    }
}

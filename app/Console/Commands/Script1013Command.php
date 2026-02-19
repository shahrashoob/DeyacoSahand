<?php

namespace App\Console\Commands;


use App\Http\Controllers\Utility\Script\Script1013Controller;
use App\Models\Utility\Transport\Transport;
use Illuminate\Console\Command;

class Script1013Command extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1013';

    /**
     * The console command description.
     * شارژ انبارک ماشین
     * @var string
     */
    protected $description = 'Register : Register From to Financial Software ';

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

        Script1013Controller::handle();
    }
}

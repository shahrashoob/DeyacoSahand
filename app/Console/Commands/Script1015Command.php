<?php

namespace App\Console\Commands;


use App\Http\Controllers\Utility\Script\Script1014Controller;
use App\Http\Controllers\Utility\Script\Script1015Controller;
use App\Models\Utility\Transport\Transport;
use Illuminate\Console\Command;

class Script1015Command extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1015';

    /**
     * The console command description.
     * شارژ انبارک ماشین
     * @var string
     */
    protected $description = 'Exist From Transaction : Exist From Transaction ';

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

        Script1015Controller::handle();
    }
}

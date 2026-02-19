<?php

namespace App\Console\Commands;

use App\Events\Utility\TransportLogEvent;
use App\Http\Controllers\Utility\Script\Script1009Controller;
use App\Http\Controllers\Utility\Script\Script1010Controller;
use App\Http\Controllers\Utility\Script\Script1011Controller;
use App\Http\Controllers\Utility\Script\Script1012Controller;
use App\Models\Utility\Transport\Transport;
use Illuminate\Console\Command;

class Script1012Command extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1012';

    /**
     * The console command description.
     * شارژ انبارک ماشین
     * @var string
     */
    protected $description = 'Return of raw materials : Assistant Storekeeper For Return Material From Machine To Warehouse ';

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

        Script1012Controller::handle();
    }
}

<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1006Controller;
use App\Http\Controllers\Utility\Script\Script1007Controller;
use Illuminate\Console\Command;

class Script1007Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1007';

    /**
     * The console command description.
     * شارژ انبارک ماشین
     * @var string
     */
    protected $description = 'Script 1007: Machine Warehouse Charging';

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
        Script1007Controller::handle();
    }
}

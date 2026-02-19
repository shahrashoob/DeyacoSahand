<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1029Controller;
use Illuminate\Console\Command;

class Script1029Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1029';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cost of Sms: Calculate cost of Sms  And Create Factor';

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
        Script1029Controller::handle(); return 0;
    }
}
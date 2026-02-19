<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utility\Script\Script1028Controller;
use Illuminate\Console\Command;

class Script1028Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:s1028';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cost of Product: Calculate cost of products';

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
        Script1028Controller::handle(); return 0;
    }
}
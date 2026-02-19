<?php

namespace App\Console\Commands;

use App\Models\LineProduct\Machine\Machine;
use App\Models\Utility\Address\Address;
use App\Notifications\SMSNotification;
use Aws\Sms\SmsClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class DemoCron extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Sample Sms to 09130656899';

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
        Notification::send( "09130656899",
            new SMSNotification( "resetpass", "علیرضا جلایق","","","","test script" ) );

    }
}

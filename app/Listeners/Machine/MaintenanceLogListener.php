<?php

namespace App\Listeners\Machine;

use App\Events\Machine\MaintenanceLogEvent;
use App\Models\LineProduct\Machine\Maintenance\MaintenanceLog;
use App\Models\Utility\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class MaintenanceLogListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(MaintenanceLogEvent $event)
    {
        //

        $msg = null;
        if ( $event->text != "" ) {
            $msg = Message::create(
                [
                    "text"            => $event->text,
                    "other_id"        => $event->maintenance->id,
                    "message_type_id" => 160
                ]
            );
        }

        MaintenanceLog::create( [
            "allocation_id"    => $event->maintenance->allocation_id,
            "machine_id"    => $event->maintenance->machine_id,
            "maintenance_id"    => $event->maintenance->id,
            "status_id" => $event->maintenance->status_id,
            "event_id" => $event->event_id,
            "user_id"   => Auth::user()->id,
            "message_id"=>$msg->id??null
        ] );


    }
}

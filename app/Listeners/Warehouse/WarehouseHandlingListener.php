<?php

namespace App\Listeners\Warehouse;

use App\Events\Warehouse\WarehouseHandlingEvent;
use App\Models\Utility\Message;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandlingLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class WarehouseHandlingListener
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
    public function handle(WarehouseHandlingEvent $event)
    {
        $log                          = new WarehouseHandlingLog();
        $log->warehouse_handling_id      = $event->warehouse_handling->id;
        $log->user_id                 =$event->user_id?? Auth::user()->id;
        $log->status_id               =$event->warehouse_handling->status_id;

        $log->event_id=$event->event_id;
        $msg                          = null;
        if ( $event->text != "" ) {
            $msg = Message::create(
                [
                    "text"            => $event->text,
                    "other_id"        => $event->warehouse_handling->id,
                    "message_type_id" => 270
                ]
            );
        }

        $log->message_id = $msg->id ?? null;
        $log->save();
    }
}

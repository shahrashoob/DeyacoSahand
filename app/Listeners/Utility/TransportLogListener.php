<?php

namespace App\Listeners\Utility;

use App\Events\Order\OrderLogEvent;
use App\Events\Utility\TransportLogEvent;
use App\Models\Order\OrderLog;
use App\Models\Utility\Message;
use App\Models\Utility\Transport\TransportLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class TransportLogListener
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
    public function handle(TransportLogEvent $event)
    {
        //
        $msg = null;
        if ( $event->text != "" ) {
            $msg = Message::create(
                [
                    "text"            => $event->text,
                    "other_id"        => $event->transport->id,
                    "message_type_id" => 240
                ]
            );
        }

         TransportLog::create( [
            "transport_id"            => $event->transport->id,
            "status_id"           =>  $event->transport->status_id,
            "event_id"           => $event->event_id,
            "message_id"          => $msg->id ?? null,
            "user_id"             =>$event->user_id?? Auth::user()->id
        ] );

    }
}

<?php

namespace App\Listeners\ProductionCard;

use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Models\Production\ProductionLog;
use App\Models\Utility\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class ProductionCardLogListener
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
     * @param object $event
     * @return void
     */
    public function handle(ProductionCardLogEvent $event)
    {
        $log = new ProductionLog();
        $log->production_id = $event->production->id;
        $log->user_id = $event->user_id ?? Auth::user()->id;
        $log->status_id = $event->production->status_id;
        $log->waiting_status_id = $event->production->waiting_status_id;
        $log->event_id = $event->event_id;
        $log->save();
        $msg = null;
        if ($event->text != "") {
            $msg = Message::create(
                [
                    "text" => $event->text,
                    "other_id" => $log->id,
                    "message_type_id" => 180
                ]
            );
        }

        $log->message_id = $msg->id ?? null;
        $log->save();
    }
}

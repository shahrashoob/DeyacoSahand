<?php

namespace App\Listeners\Warps;

use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestFormLog;
use App\Models\Utility\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class WarpsRequestFormLogListener
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
    public function handle(WarpsRequestFormLogEvent $event)
    {
        $msg = null;
        if ( $event->text != "" ) {
            $msg = Message::create(
                [
                    "text"            => $event->text,
                    "other_id"        => $this->id,
                    "message_type_id" => 160
                ]
            );
        }

        WarpsRequestFormLog::create( [
            "product_request_form_id"    => $event->request_form->id,

            "status_id" => $event->request_form->status_id,
            "user_id"   => Auth::user()->id,
            "message_id"=>$msg->id??null
        ] );
    }
}

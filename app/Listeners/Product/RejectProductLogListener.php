<?php

namespace App\Listeners\Product;

use App\Events\Product\RejectProductLogEvent;
use App\Models\LineProduct\Product\RejectProduct\RejectProductLog;
use App\Models\Utility\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class RejectProductLogListener {
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle( RejectProductLogEvent $event ) {
        //
        $msg = null;
        if ( $event->text != "" ) {
            $msg = Message::create(
                [
                    "text"            => $event->text,
                    "other_id"        => $event->reject_product_form->id,
                    "message_type_id" => 210
                ]
            );
        }

        RejectProductLog::create( [
            "reject_product_form_id" => $event->reject_product_form->id,
            "status_id"  => $event->reject_product_form->status_id,
            "user_id"    =>$event->user_id?? Auth::user()->id,
            "message_id" => $msg->id ?? null,
            "event_id"   => $event->event_id
        ] );
    }
}

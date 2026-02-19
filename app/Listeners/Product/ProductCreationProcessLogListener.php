<?php

namespace App\Listeners\Product;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcessLog;
use App\Models\Utility\Message;
use Illuminate\Support\Facades\Auth;

class ProductCreationProcessLogListener {
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle( ProductCreationProcessLogEvent $event ) {
        //
        $msg = null;
        if ( $event->text != "" ) {
            $msg = Message::create(
                [
                    "text"            => $event->text,
                    "other_id"        => $event->product_creation_process->id,
                    "message_type_id" => 220
                ]
            );

        }

        ProductCreationProcessLog::create( [
            "product_creation_process_id" => $event->product_creation_process->id,
            "user_id"                     => $event->user_id == null ? Auth::id() : $event->user_id,
            "status_id"                   => $event->product_creation_process->status_id,
            "event_id"                    => $event->event_id,
            "message_id"                  => $msg->id ?? null,
        ] );
    }
}


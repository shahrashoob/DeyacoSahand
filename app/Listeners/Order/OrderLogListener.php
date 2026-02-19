<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderLogEvent;
use App\Models\Order\OrderLog;
use App\Models\Utility\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class OrderLogListener {
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
    public function handle( OrderLogEvent $event ) {
        //
        $msg = null;
        if ( $event->text != "" ) {
            $msg = Message::create(
                [
                    "text"            => $event->text,
                    "other_id"        => $event->order->id,
                    "message_type_id" => 120
                ]
            );
        }
        $customer_msg = null;
        if ( $event->customer_text != "" ) {
            $customer_msg = Message::create(
                [
                    "text"            => $event->customer_text,
                    "other_id"        => $event->order->id,
                    "message_type_id" => 120
                ]
            );
        }


        OrderLog::create( [
            "order_id"            => $event->order->id,
            "status_id"           => $event->order->status_id,
            "event_id"            => $event->event_id,
            "form_id"             => $event->form_id,
            "exit_status_id"      => $event->order->exit_status_id ?? 0,
            "message_id"          => $msg->id ?? 0,
            "customer_message_id" => $customer_msg->id ?? 0,
            "user_id"             => $event->user_id ?? Auth::user()->id
        ] );

    }
}

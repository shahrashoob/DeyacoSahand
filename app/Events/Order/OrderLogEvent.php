<?php

namespace App\Events\Order;

use App\Listeners\Order\OrderLogListener;
use App\Models\Order\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderLogEvent {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $text;
    public $customer_text;
    public $order;
    public $event_id;
    public $form_id;
    public $user_id;

    public function __construct( Order $order, $event_id, $text = "", $customer_text = "", $form_id = null, $user_id = null ) {
        //
        $this->order         = $order;
        $this->text          = $text;
        $this->customer_text = $customer_text;
        $this->event_id      = $event_id;
        $this->form_id       = $form_id;
        $this->user_id       = $user_id;

        OrderLogListener::class;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel( 'channel-name' );
    }
}

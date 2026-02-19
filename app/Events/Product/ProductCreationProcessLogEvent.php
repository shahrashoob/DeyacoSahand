<?php

namespace App\Events\Product;

use App\Listeners\Product\ProductCreationProcessLogListener;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductCreationProcessLogEvent {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $text;
    public $event_id;
    public $product_creation_process;
    public $user_id;

    public function __construct( $product_creation_process, $event_id, $text = "", $user_id = null ) {
        //
        $this->product_creation_process = $product_creation_process;
        $this->event_id                 = $event_id;
        $this->text                     = $text;
        $this->user_id                  = $user_id;

        new ProductCreationProcessLogListener();
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

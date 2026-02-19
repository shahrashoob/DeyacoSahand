<?php

namespace App\Events\Product;

use App\Listeners\Product\RejectProductLogListener;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RejectProductLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $text;
    public $event_id;
    public $reject_product_form;
    public function __construct($reject_product_form, $event_id,$text="",$user_id=null)
    {
        //
        $this->text=$text;
        $this->event_id=$event_id;
        $this->user_id=$user_id;
        $this->reject_product_form=$reject_product_form;

        new RejectProductLogListener();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

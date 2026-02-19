<?php

namespace App\Events\Warps;

use App\Listeners\Product\ProductRequestFormLogListener;
use App\Listeners\Warps\WarpsRequestFormLogListener;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WarpsRequestFormLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request_form;
    public $text;
    public function __construct( $request_form,$text="")
    {
        //
        new ProductRequestFormLogListener();
        $this->request_form=$request_form;
        $this->text=$text;
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

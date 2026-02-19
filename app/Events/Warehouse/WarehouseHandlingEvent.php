<?php

namespace App\Events\Warehouse;


use App\Listeners\Warehouse\WarehouseHandlingListener;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandling;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WarehouseHandlingEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $text;
    public $event_id;
    public $user_id;
    public $warehouse_handling;
    public function __construct(WarehouseHandling $warehouse_handling,$event_id,$text="",$user_id=null)
    {
        //
        $this->warehouse_handling=$warehouse_handling;
        $this->event_id=$event_id;
        $this->text=$text;
        $this->user_id=$user_id;
        WarehouseHandlingListener::class;
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

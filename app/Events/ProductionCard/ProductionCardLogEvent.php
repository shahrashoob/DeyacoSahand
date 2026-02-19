<?php

namespace App\Events\ProductionCard;

use App\Listeners\ProductionCard\ProductionCardLogListener;
use App\Models\LineProduct\Machine\Machine;
use App\Models\Production\Production;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductionCardLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $production;
    public $text;
    public $user_id;
    public $event_id;
    public function __construct(Production $production,$text="",$user_id=null,$event_id=null)
    {
        ProductionCardLogListener::class;
        $this->text=$text;
        $this->production=$production;
        $this->user_id=$user_id;
        $this->event_id=$event_id;
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

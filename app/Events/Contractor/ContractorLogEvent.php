<?php

namespace App\Events\Contractor;

use App\Listeners\Contractor\ContractorLogListener;
use App\Models\Contractor\Contractor;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use phpDocumentor\Reflection\Types\Null_;

class ContractorLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $production;
    public $contractor;
    public $machine_allocation;
    public $text;
    public $event_id;
    public $user_id;
    public $order;
    public function __construct( $contractor,$event_id,$production=null,$machine_allocation=null,$text=null,$user_id=null,$order=null)
    {
        $this->contractor=$contractor;
        $this->production=$production;
        $this->machine_allocation=$machine_allocation;
        $this->text=$text;
        $this->event_id=$event_id;
        $this->user_id=$user_id;
        $this->order=$order;
        new ContractorLogListener();
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

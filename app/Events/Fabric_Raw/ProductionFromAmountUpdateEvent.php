<?php

namespace App\Events\Fabric_Raw;

use App\Listeners\Fabric_Raw\ProductionFromAmountUpdateListener;
use App\Models\LineProduct\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductionFromAmountUpdateEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $machine_id;
    public $end_of_production_form;
    public function __construct($machine_id,$end_of_production_form)
    {
        //
        $this->machine_id=$machine_id;
        $this->end_of_production_form=$end_of_production_form;
        new ProductionFromAmountUpdateListener();
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

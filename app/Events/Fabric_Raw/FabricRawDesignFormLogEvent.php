<?php

namespace App\Events\Fabric_Raw;

use App\Listeners\Fabric_Raw\FabricRawDesignFormLogListener;
use App\Models\GoodsKindProcess\Fabric_Raw\Desing\FabricRawDesignForm;
use App\Models\GoodsKindProcess\Fabric_Raw\Desing\FabricRawDesignFormLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FabricRawDesignFormLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $design_form;
    public $text;
    public function __construct(FabricRawDesignForm $design_form,$text="")
    {
        new FabricRawDesignFormLogListener();
        $this->design_form=$design_form;
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

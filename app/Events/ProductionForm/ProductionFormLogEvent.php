<?php

namespace App\Events\ProductionForm;

use App\Listeners\ProductionForm\ProductionFormLogListener;
use App\Models\Production\ProductionForm;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductionFormLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $production_form;
    public $production_form_item;
    public $text;
    public $event_id;
    public function __construct(ProductionForm $production_form,$event_id,$production_form_item=null,$text="")
    {
        //
        $this->production_form=$production_form;
        $this->production_form_item=$production_form_item;
        $this->event_id=$event_id;
        $this->text=$text;
        new ProductionFormLogListener();
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

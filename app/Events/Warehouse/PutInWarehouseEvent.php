<?php

namespace App\Events\Warehouse;

use App\Listeners\Warehouse\PutInWarehouseListener;
use App\Models\Form\Form;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PutInWarehouseEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    var $form;
    var $packing_form_id;
    var $parent_packing_form;
    var $change_warehouse_status;
    var $user_id;
    public function __construct(Form $form,$master_packing_form=null,$packing_form_id=null,$change_warehouse_status=true,$user_id=null)
    {
        $this->form=$form;
        $this->packing_form_id=$packing_form_id;
        $this->master_packing_form=$master_packing_form;
        $this->change_warehouse_status=$change_warehouse_status;
        $this->user_id=$user_id;
        new PutInWarehouseListener();
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

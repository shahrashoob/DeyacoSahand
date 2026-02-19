<?php

namespace App\Events\Form;

use App\Listeners\Form\PackingLogListener;
use App\Models\Form\Packing\PackingForm;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PackingLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $text;
    public $event_id;
    public $packing_form;
    public $packing_form_item;
    public $form_id;
    public $user_id;
    public $packing_form_master_id;
    public function __construct(PackingForm $packing_form,$event_id,$packing_form_item=null,$text="",$form_id=null,$user_id=null,$packing_form_master_id=null)
    {
        $this->packing_form=$packing_form;
        $this->packing_form_item=$packing_form_item;
        $this->event_id=$event_id;
        $this->text=$text;
        $this->form_id=$form_id;
        $this->user_id=$user_id;
        $this->packing_form_master_id=$packing_form_master_id;
        PackingLogListener::class;
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

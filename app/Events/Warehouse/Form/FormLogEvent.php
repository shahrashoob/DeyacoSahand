<?php

namespace App\Events\Warehouse\Form;

use App\Listeners\Form\FormLogListener;
use App\Models\Form\Form;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FormLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $form;
    public $text="";
    public $user_id;
    public function __construct(Form $form,$text="",$user_id=null)
    {
        //
        $this->form=$form;
        $this->text=$text;
        $this->user_id=$user_id;
        \App\Listeners\Warehouse\Form\FormLogListener::class;

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

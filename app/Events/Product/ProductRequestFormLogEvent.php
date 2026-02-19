<?php

namespace App\Events\Product;

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

class ProductRequestFormLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request_form;
    public $text;
    public $form_id;
    public $event_id;
    public $user_id;
    public $json_data;
    public function __construct( $request_form,$text="",$form_id=null,$event_id=null,$user_id=null,$json_data="")
    {
        //
        new ProductRequestFormLogListener();
        $this->request_form=$request_form;
        $this->form_id=$form_id;
        $this->text=$text;
        $this->json_data=$json_data;
        $this->event_id=$event_id;
        $this->user_id=$user_id;

     //   ProductRequestFormLogListener::class;
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

<?php

namespace App\Events\Utility;

use App\Listeners\Order\OrderLogListener;
use App\Listeners\Utility\TransportLogListener;
use App\Models\Order\Order;
use App\Models\Utility\Transport\Transport;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransportLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $text;
    public $transport;
    public $event_id;
    public $user_id;
    public function __construct(Transport $transport,$event_id,$text="",$user_id=null)
    {
        //
        $this->transport=$transport;
        $this->text=$text;
        $this->event_id=$event_id;
        $this->user_id=$user_id;

       new TransportLogListener();
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

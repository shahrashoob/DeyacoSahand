<?php

namespace App\Events\HR;

use App\Listeners\HR\LeaveLogListener;
use App\Models\HR\LeaveOvertime\LeaveOvertime;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaveLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $leave;
    public $text;
    public $event_id;
    public $post_id;
    public $user_id;
    public function __construct(LeaveOvertime $leave,$event_id,$text="",$user_id=null)
    {
        //
        $this->leave=$leave;
        $this->text=$text;
        $this->event_id=$event_id;
        $this->user_id=$user_id;
        new LeaveLogListener();
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

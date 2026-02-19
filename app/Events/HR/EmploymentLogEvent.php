<?php

namespace App\Events\HR;

use App\Listeners\HR\EmploymentLogListener;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmploymentLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $employment;
    public $event_id;
    public $text = "";
    public $employment_selection_id;
    public $user_id = null;

    public function __construct(Employment $employment, $event_id, $text = "", $employment_selection_id=null, $user_id = null)
    {
        $this->employment=$employment;
        $this->event_id=$event_id;
        $this->text=$text;
        $this->employment_selection_id=$employment_selection_id;
        $this->user_id=$user_id;
        EmploymentLogListener::class;

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

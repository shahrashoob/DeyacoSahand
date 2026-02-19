<?php

namespace App\Events\HR;

use App\Listeners\HR\OfficeAutomationListener;
use App\Listeners\HR\OfficeAutomationLogListener;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfficeAutomationLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $office_automation_work;
    public $office_automation_to_do_list;
    public $office_automation_action;
    public $text;
    public $event_id;
    public $request;
    public function __construct($office_automation_work,$office_automation_to_do_list,$office_automation_action,$event_id,$text="",$request=null)
    {
        //
        $this->office_automation_work=$office_automation_work;
        $this->office_automation_to_do_list=$office_automation_to_do_list;
        $this->office_automation_action=$office_automation_action;
        $this->text=$text;
        $this->event_id=$event_id;
        $this->request=$request;
        new OfficeAutomationLogListener();
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

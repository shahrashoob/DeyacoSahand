<?php

namespace App\Events\Utility;

use App\Listeners\Utility\SpecialLicenseListener;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SpecialLicenseEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $text;
    public $special_license;
    public $event_id;
    public $user_id;
    public $post_id;
    public $committee_id;
    public function __construct(SpecialLicense $special_license,$event_id,$post_id,$committee_id,$user_id=null,$text=null)
    {
        //
        $this->special_license=$special_license;
        $this->event_id=$event_id;
        $this->post_id=$post_id;
        $this->committee_id=$committee_id;
        $this->user_id=$user_id;
        $this->text=$text;
        new SpecialLicenseListener();
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

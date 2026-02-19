<?php

namespace App\Events\Machine;

use App\Listeners\Machine\MachineLogListener;
use App\Listeners\Machine\MaintenanceLogListener;
use App\Models\LineProduct\Machine\Maintenance\Maintenance;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MaintenanceLogEvent {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $maintenance;
    public $event_id;
    public $text;

    public function __construct( Maintenance $maintenance, $event_id,$text="" ) {
        //
        $this->maintenance = $maintenance;
        $this->event_id    = $event_id;
        $this->text    = $text;
        MaintenanceLogListener::class;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel( 'channel-name' );
    }
}

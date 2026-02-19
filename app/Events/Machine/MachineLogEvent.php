<?php

namespace App\Events\Machine;

use App\Listeners\Machine\MachineLogListener;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MachineLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $machine;
    public $log;
    public $text;
    public $end_of_production_form;
    public $last_row_log;
    public $user_id;
    public function __construct(Machine $machine,MachineLog $log,$text="",$end_of_production_form=false,$last_row_log=null,$user_id=null)
    {


        $this->machine=$machine;
        $this->log=$log;
        $this->text=$text;
        $this->end_of_production_form=$end_of_production_form;
        $this->user_id=$user_id;
        $this->last_row_log=$last_row_log;
          new MachineLogListener();
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

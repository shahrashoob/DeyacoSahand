<?php

namespace App\Events\HR;

use App\Listeners\HR\EmploymentLogListener;
use App\Listeners\HR\EvaluationFormLogListener;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Evaluation\EvaluationForm\EvaluationForm;
use App\Models\HR\Evaluation\EvaluationForm\EvaluationFormLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EvaluationFormLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $evaluation_form;
    public $event_id;
    public $text = "";
    public $user_id = null;
    public function __construct(EvaluationForm $evaluation_form, $event_id, $text = "", $user_id = null)
    {
        $this->evaluation_form=$evaluation_form;
        $this->event_id=$event_id;
        $this->text=$text;
        $this->user_id=$user_id;
       EvaluationFormLogListener::class;
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

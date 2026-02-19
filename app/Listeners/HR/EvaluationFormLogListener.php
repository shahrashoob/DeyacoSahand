<?php

namespace App\Listeners\HR;

use App\Events\HR\EvaluationFormLogEvent;
use App\Models\HR\Evaluation\EvaluationForm\EvaluationFormLog;
use App\Models\Utility\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class EvaluationFormLogListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\HR\EvaluationFormLogEvent  $event
     * @return void
     */
    public function handle(EvaluationFormLogEvent $event)
    {
        $log                          = new EvaluationFormLog();
        $log->evaluation_form_id             = $event->evaluation_form->id;
        $log->user_id                 =$event->user_id?? Auth::user()->id;
        $log->status_id               = $event->evaluation_form->status_id;
        $log->event_id=$event->event_id;
        $msg                          = null;
        if ( $event->text != "" ) {
            $msg = Message::create(
                [
                    "text"            => $event->text,
                    "other_id"        => $event->evaluation_form->id,
                    "message_type_id" => 300
                ]
            );
        }

        $log->message_id = $msg->id ?? null;
        $log->save();
    }
}

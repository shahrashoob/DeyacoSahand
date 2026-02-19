<?php

namespace App\Listeners\HR;

use App\Events\HR\EmploymentLogEvent;
use App\Models\HR\Employment\EmploymentLog;
use App\Models\Utility\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class EmploymentLogListener
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
     * @param  \App\Events\HR\EmploymentLogEvent  $event
     * @return void
     */
    public function handle(EmploymentLogEvent $event)
    {
        $log                          = new EmploymentLog();
        $log->employment_id             = $event->employment->id;
        $log->user_id                 =$event->user_id?? Auth::user()->id;
        $log->status_id               = $event->employment->status_id;
        $log->event_id=$event->event_id;
        $msg                          = null;
        if ( $event->text != "" ) {
            $msg = Message::create(
                [
                    "text"            => $event->text,
                    "other_id"        => $event->employment->id,
                    "message_type_id" => 280
                ]
            );
        }

        $log->message_id = $msg->id ?? null;
        $log->employment_selection_id=$event->employment_selection_id?? null;
        $log->save();
    }
}

<?php

namespace App\Listeners\HR;

use App\Events\HR\LeaveLogEvent;
use App\Models\HR\LeaveOvertime\LeaveOvertimeLog;
use App\Models\Utility\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LeaveLogListener
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
     * @param object $event
     *
     * @return void
     */
    public function handle(LeaveLogEvent $event)
    {
        $log = new LeaveOvertimeLog();
        $log->leave_overtime_id = $event->leave->id;
        $log->user_id = Auth::user()->id ?? $event->user_id;
        $log->status_id = $event->leave->status_id;
        $log->event_id = $event->event_id;

        $msg = null;
        if ($event->text != "") {
            $msg = Message::create(
                [
                    "text" => $event->text,
                    "other_id" => $event->leave->id,
                    "message_type_id" => 200
                ]
            );
        }

        $log->message_id = $msg->id ?? null;
        $log->save();
    }
}

<?php

namespace App\Listeners\Warehouse\Form;

use App\Events\Warehouse\Form\FormLogEvent;
use App\Models\Form\FormLog;
use App\Models\Utility\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class FormLogListener
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
     * @param  object  $event
     * @return void
     */
    public function handle(FormLogEvent $event)
    {
        $log=new FormLog();
        $log->form_id               = $event->form->id;
        $log->user_id               =$event->user_id?? Auth::user()->id;
        $log->status_id         = $event->form->status_id;
        $msg = null;
        if ( $event->text != "" ) {
            $msg = Message::create(
                [
                    "text"            => $event->text,
                    "other_id"        => $event->form->id,
                    "message_type_id" => 160
                ]
            );
        }

        $log->message_id = $msg->id ?? null;
        $log->save();
    }
}

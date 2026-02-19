<?php

namespace App\Listeners\Form;

use App\Events\Form\PackingLogEvent;
use App\Models\Form\Packing\PackingFormLog;
use App\Models\Form\Packing\PackingLog;
use App\Models\Utility\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class PackingLogListener
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
    public function handle(PackingLogEvent $event)
    {
        $log                          = new PackingFormLog();
        $log->packing_form_id      = $event->packing_form->id;
        $log->user_id                 =$event->user_id?? Auth::user()->id;
        $log->status_id               = isset( $event->packing_form_item ) ?
            $event->packing_form_item->status_id :
            $event->packing_form->status_id;
        $log->packing_form_item_id = $event->packing_form_item->id ?? null;
        $log->event_id=$event->event_id;
        $msg                          = null;
        if ( $event->text != "" ) {
            $msg = Message::create(
                [
                    "text"            => $event->text,
                    "other_id"        => $event->packing_form->id,
                    "message_type_id" => 190
                ]
            );
        }

        $log->message_id = $msg->id ?? null;
        $log->form_id = $event->form_id ?? null;
        $log->packing_form_master_id = $event->packing_form_master_id ?? null;
        $log->save();
    }
}

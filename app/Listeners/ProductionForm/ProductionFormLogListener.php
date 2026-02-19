<?php

namespace App\Listeners\ProductionForm;

use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Models\Production\ProductionFormLog;
use App\Models\Utility\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class ProductionFormLogListener {
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle( ProductionFormLogEvent $event ) {
        $log                          = new ProductionFormLog();
        $log->production_form_id      = $event->production_form->id;
        $log->user_id                 = Auth::user()->id??2;
        $log->status_id               = isset( $event->production_form_item ) ?
            $event->production_form_item->status_id :
            $event->production_form->status_id;
        $log->production_form_item_id = $event->production_form_item->id ?? null;
        $log->band_code               = $event->production_form_item->band_code ?? null;
        $log->event_id=$event->event_id;
        $log->machine_id=$event->production_form->machine_id;
        $msg                          = null;
        if ( $event->text != "" ) {
            $msg = Message::create(
                [
                    "text"            => $event->text,
                    "other_id"        => $event->production_form->id,
                    "message_type_id" => 170
                ]
            );
        }

        $log->message_id = $msg->id ?? null;
        $log->save();
    }
}

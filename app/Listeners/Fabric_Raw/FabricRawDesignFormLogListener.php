<?php

namespace App\Listeners\Fabric_Raw;

use App\Events\Fabric_Raw\FabricRawDesignFormLogEvent;
use App\Models\GoodsKindProcess\Fabric_Raw\Desing\FabricRawDesignForm;
use App\Models\GoodsKindProcess\Fabric_Raw\Desing\FabricRawDesignFormLog;
use App\Models\Utility\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class FabricRawDesignFormLogListener {
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
    public function handle( FabricRawDesignFormLogEvent $event ) {
        $msg = null;
        if ( $event->text != "" ) {
            $msg = Message::create(
                [
                    "text"            => $event->text,
                    "other_id"        => $this->id,
                    "message_type_id" => 160
                ]
            );
        }

        FabricRawDesignFormLog::create( [
            "fabric_raw_design_form_id"    => $event->design_form->id,

            "design_available"      => $event->design_form->design_available,
            "it_has_pinning"        => $event->design_form->it_has_pinning,
            "warps_is_in_warehouse" => $event->design_form->warps_is_in_warehouse,
            "need_to_convert"       => $event->design_form->need_to_convert,

            "status_id" => $event->design_form->status_id,
            "user_id"   => Auth::user()->id,
            "message_id"=>$msg->id??null
        ] );
    }
}

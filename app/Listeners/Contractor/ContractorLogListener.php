<?php

namespace App\Listeners\Contractor;

use App\Events\Contractor\ContractorLogEvent;
use App\Models\Contractor\MachineAllocationLog;
use App\Models\Utility\Message;
use Illuminate\Support\Facades\Auth;

class ContractorLogListener
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

    public function handle(ContractorLogEvent $event)
    {


        $log = MachineAllocationLog::create([
            "contractor_id" => $event->contractor->id??null,
            "order_id" => $event->order->id??null,
            "machine_allocation_id" => $event->machine_allocation->id ?? null,
            "production_id" => $event->production->id ?? null,
            "production_status_id" => $event->production->waiting_status_id ?? null,
            "machine_allocation_status_id" => $event->machine_allocation->status_id ?? null,
            "user_id" => $event->user_id ?? Auth::user()->id,
            "event_id" => $event->event_id,
            "line_product_station_id"=>$event->machine_allocation->line_product_station_id ??null
        ]);
        $msg = null;
        if ($event->text != "") {
            $msg = Message::create(
                [
                    "text" => $event->text,
                    "other_id" => $log->id,
                    "message_type_id" => 160
                ]
            );
            $log->message_id = $msg->id;
            $log->save();
        }
    }
}

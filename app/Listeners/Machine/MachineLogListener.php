<?php

namespace App\Listeners\Machine;

use App\Events\Fabric_Raw\ProductionFromAmountUpdateEvent;
use App\Events\Machine\MachineLogEvent;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Utility\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class MachineLogListener {
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
    public function handle( MachineLogEvent $event ) {
        $log                        = $event->log;
        $log->machine_id            = $event->machine->id;
        $log->current_production_id = $event->machine->current_production_id ?? null;
        $log->user_id               = $event->user_id ?? Auth::user()->id;

        $log->active_status_id      = $event->machine->active_status_id;
        $log->on_status_id          = $event->machine->on_status_id;
        $log->production_status_id  = $event->machine->production_status_id;
        $log->maintenance_status_id = $event->machine->maintenance_status_id;
        $log->machine_off_reason_id = $event->machine->machine_off_reason_id ?? null;


        $log->machine_event_type_id = $event->log->machine_event_type_id ?? null;
        $log->contour_1_value       = $event->log->contour_1_value ?? null;
        $log->contour_2_value       = $event->log->contour_2_value ?? null;
        $log->contour_3_value       = $event->log->contour_3_value ?? null;
        $log->contour_4_value       = $event->log->contour_4_value ?? null;
        $log->contour_5_value       = $event->log->contour_5_value ?? null;
        $log->shift_work_id         = $event->log->shift_work_id ?? null;
        $log->allocation_id         = $event->log->allocation_id ?? null;

        if ( isset( $event->log->contour_1_value ) ) {
            $sum_latest               = isset( $event->last_row_log ) ?
                $event->last_row_log->sumCounter( "calculate" ) :
                0;
            $contour_sum_value_latest = $event->last_row_log->contour_sum_value ?? 0;
            $log->contour_sum_value   = $contour_sum_value_latest +
                                        (
                                            $log->sumCounter( "calculate" ) - $sum_latest
                                        );

            if ( ! $event->log->shift_work_id ) {
                $log->shift_work_id = 1; // ستون شیفت باید از جدول لاگ خذف شود ولی چون در بعضی از کوءری ها از آن استفاده شده، فقط مقدار غیر نال به آن می دهیم.
            }
        }
        $log->save();

        $msg = null;
        if ( $event->text != "" ) {
            $msg = Message::create(
                [
                    "text"            => $event->text,
                    "other_id"        => $log->id,
                    "message_type_id" => 160
                ]
            );
        }

        $log->message_id = $msg->id ?? null;

        // مشخص کردن اپراتور
        $last_log_row = MachineLog::where( "machine_id", $event->machine->id )->whereNotNull( "operator_id" )->orderByDesc( "id" )->first();
        if ( ! $log->operator_id && $last_log_row ) {
            $log->operator_id = $last_log_row->operator_id;
        }

        $log->save();

        if ( $log->contour_1_value != null ) {

            event( new ProductionFromAmountUpdateEvent( $log->machine_id, $event->end_of_production_form ) );
        }

    }
}

<?php

namespace App\Listeners\Fabric_Raw;

use App\Events\Fabric_Raw\ProductionFromAmountUpdateEvent;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProductionFromAmountUpdateListener {
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
    public function handle( ProductionFromAmountUpdateEvent $event ) {
        //

        $production_forms = ProductionForm::where( [
            "machine_id" => $event->machine_id,
            "status_id"  => 7002001 // در حال تکمیل فرم پارچه خام
        ] )->first();

        if ( ! $production_forms ) {
            return;
        }
        $allocation = $production_forms->machine->getCurrentAllocation();
        if ( ! $allocation ) {
            return;
        }
        foreach ( $production_forms->items as $item ) {
            if ( $item->allocation_id == $allocation->id ) {
                $item->updateItemAmount( true, $event->end_of_production_form );
            }
        }
    }
}

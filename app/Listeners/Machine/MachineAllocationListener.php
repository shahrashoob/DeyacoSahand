<?php

namespace App\Listeners\Machine;

use App\Events\Fabric_Raw\Design\CreateDesingFormEvent;
use App\Events\Machine\MachineAllocationEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\GoodsKindProcess\Fabric_Raw;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\MachineAllocation;
use Illuminate\Support\Facades\Auth;

class MachineAllocationListener
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
    public function handle(MachineAllocationEvent $event)
    {

        $allocation = Allocation::firstOrCreate(
            [
                "machine_id" => $event->machine->id,
                "status_id" => 5310005,
            ],
            ["allocation_unit_type_id" => $event->allocation_unit_type_id]
        );

        if (!$event->parent_allocation_id) {
            MachineAllocation::create(
                [
                    "allocation_id" => $allocation->id,
                    "production_id" => $event->production->id,
                    "machine_id" => $event->machine->id,
                    "user_id" => $event->user_id ?? Auth::user()->id,
                    "status_id" => $allocation->status_id,
                    "band_code" => $event->band_code,
                    "product_id" => $event->production->product_id,

                    "allocation_amount" => $event->allocation_amount,
                    "allocation_sub_amount" => $event->allocation_sub_amount,
                    "number_of_packing_form" => $event->number_of_packing_form,

                    "number_of_doffs_done" => $event->number_of_doffs_done,
                    "max_number_of_doffs" => $event->max_number_of_doffs,
                    "amount_of_each_doffs" => $event->amount_of_each_doffs,
                    "parent_allocation_id" => $event->parent_allocation_id,
                    "line_product_station_id" => $event->line_product_station_id,
                    "version_code" => $event->production->product->version->version_code ?? null,
                ]
            );
        } else {
            $machine_allocation = MachineAllocation::find($event->machine_allocation_id);
            // اگر مقدار تخصیص داده شده کمتر از مقدار کل تخصیص مجدد بود، برای مابعی آن را یک تخصیص مجدد دیگر ایجاد می کنیم.
            if ($event->allocation_amount < $machine_allocation->allocation_amount) {
                $new_machine_allocation = $machine_allocation->toArray();
                $new_amount = $machine_allocation->allocation_amount - $event->allocation_amount;
                $new_machine_allocation["allocation_amount"] = $new_amount;
                $new_machine_allocation["amount_of_each_doffs"] = $new_amount;

                $new_machine_allocation_obj=MachineAllocation::create($new_machine_allocation);
                $new_machine_allocation_obj->parent_allocation_id=$event->parent_allocation_id;
                $new_machine_allocation_obj->save();

                event(new ProductionCardLogEvent($event->production, $new_amount, $event->user_id ?? Auth::user()->id, 7008009));
            }
            // برای تخصیص های در انتظار تخصیص فقط شناسه تخصیص را جابجا می کنیم.
            MachineAllocation::where("id", $event->machine_allocation_id)->update([
                "allocation_id" => $allocation->id,
                "machine_id" => $event->machine->id,
                "allocation_amount" => $event->allocation_amount,
                "amount_of_each_doffs" => $event->allocation_amount,
                "created_at" => now() // ساعتی که تخصیص دادند
            ]);
        }
        if ($event->packing_type_doffs) {
            Allocation\AllocationDoffs::where("allocation_id", $allocation->id)->delete();
            Allocation\AllocationDoffs::AddList($allocation->id, $event->packing_type_doffs);
        }


    }
}

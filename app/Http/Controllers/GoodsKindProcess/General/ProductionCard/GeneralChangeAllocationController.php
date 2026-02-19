<?php

namespace App\Http\Controllers\GoodsKindProcess\General\ProductionCard;

use App\Events\Machine\MachineAllocationEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utility\Script\Script1007Controller;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\CurrentMachineMaterialFlow;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachinePropertyValue;
use App\Models\LineProduct\Machine\MachineStatus;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeInputBand;
use App\Models\LineProduct\Machine\MachineTypeOutputBand;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\MaterialFlow;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\Post\Post;
use App\Models\Post\PostUser;
use App\Models\Production\Production;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\Setting;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\View;

/**
 * جابجایی تخصیص کارت های تولید بین ماشین های هم گروه
 */
class GeneralChangeAllocationController extends Controller
{

    public function change_allocation_in_machine(Allocation $allocation, $change_type_id,)
    {
        $error_message = "";
        if (!in_array($allocation->status_id, [5310040, 5310010])) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه وضعیت تخصیص " . $allocation->id . " " . $allocation->status->caption . " می باشد، امکان جابجایی تخصیص برای آن امکان پذیر نمی باشد."
            ];
        }

        if ($allocation->status_id == 5310010) {
            $result_production_amount = self::GetProductionAmount($allocation);
            if (!$result_production_amount["result"]) {
                return $result_production_amount;
            }
        }

        $machine = $allocation->machine;


        if ($machine->machine_type->machine_module_type_id != 4) {
            return [
                "result" => false,
                "error" => "جابجایی تخصیص برای فقط برای گروه ماشین های عمومی طراحی شده است."
            ];
        }

        $current_allocation = $machine->getCurrentAllocation();


        $reserve_allocation_list = $machine->ReserveAllocation()->get();

        self::UpdatePriority($machine, $current_allocation, $reserve_allocation_list);

        switch ($change_type_id) {
            case "101": // یگی به سمت بالا -> اگر اولین رزور است، می شود جاری و جاری می شود اولین رزرو
                if ($current_allocation && $current_allocation->id == $allocation->id) {
                    return [
                        "result" => true,
                        "error" => "با توجه به اینکه وضعیت تخصیص " . $current_allocation->id . "، جاری است، امکان افزایش اولویت آن وجود ندارد. "
                    ];
                }
                if ($allocation->priority_number == 1) {
                    // تخصیص جاری دارد بابد جاری بشود رزرو و تخصیص انتخاب شده بشود جاری
                    // 1 / 0;
                    if ($current_allocation) {
                        $result_production_amount = self::GetProductionAmount($current_allocation);
                        if (!$result_production_amount["result"]) {
                            return $result_production_amount;
                        }
                    }
                    if ($current_allocation) {
                        // اگر جاری وجود دارد، جاری را به اولین رزو انتقال می دهیم.
                        $current_allocation->priority_number = .9;
                        self::ChangeAllocationStatus($current_allocation, 5310040);

                    }
                    $allocation->priority_number = 0;
                    self::ChangeAllocationStatus($allocation, 5310010);
                    self::UpdateMachineStatus($allocation);
                    self::UpdatePriority($machine, $current_allocation, null);
                } else {
                    $allocation->priority_number -= 1.2;
                    $allocation->save();
                    self::UpdatePriority($machine, $current_allocation, null);
                }
//                $current_allocation->priority_number = $current_allocation->priority_number - .5;
//                $current_allocation->save();
                // return $priority_number = $machine->GetProductionAmount();
                break;

            case "201": // یکی اولویت به سمت پایین می رود، اگر  تخصیص جاری است، می شود اولین روزو و اولین روزور می شود جاری
            case "211": // رزرو انتخاب شده به آخرین رزرو می رود
                if ($current_allocation && $current_allocation->id == $allocation->id) {

                    if (count($reserve_allocation_list) == 0) {
                        return [
                            "result" => false,
                            "error" => "با توجه به اینکه هیچ کارت رزروی برای ماشین وجود ندارد،  جابجایی تخصیص امکان پذیر نیست"
                        ];
                    }
                    // اگر جاری وجود دارد، جاری را به اولین رزو انتقال می دهیم.


                    // اولین رزرو را جاری می کنیم.
                    $first_reserve_allocation = $machine->ReserveAllocation()->first();
                    if ($first_reserve_allocation) {
                        $first_reserve_allocation->priority_number = 300;
                        self::ChangeAllocationStatus($first_reserve_allocation, 5310010);
                    }
                    $current_allocation1 = $machine->getCurrentAllocation();

                    self::UpdateMachineStatus($current_allocation1);

                    $current_allocation->priority_number = $change_type_id == 201 ? .2 : 1000;
                    self::ChangeAllocationStatus($current_allocation, 5310040);

                    self::UpdatePriority($machine, $current_allocation1, null);


                } else {
                    $allocation->priority_number += $change_type_id == 201 ? 1.2 : 10000;
                    $allocation->save();
                    self::UpdatePriority($machine, $current_allocation, null);
                }
                break;


                break;
        }

        return [
            "result" => true,
        ];


    }

    public function change_allocation_other_machine($allocation_list, Machine $machine, Machine $new_machine)
    {
        $key = 1;
        foreach ($allocation_list as $allocation) {


            $allocation->machine_id = $new_machine->id;
            $allocation->priority_number = 1000 + $key;
            $allocation->status_id = 5310040;
            $allocation->save();

            foreach ($allocation->items as $machine_allocation_item) {
                $machine_allocation_item->machine_id = $new_machine->id;
                $machine_allocation_item->status_id = 5310040;
                $machine_allocation_item->save();

                event(new ProductionCardLogEvent($machine_allocation_item->production, "جابجایی تخصیص از ".$machine->caption." به ".$new_machine->caption, null, 7002025,));
            }

        }

        // لیست ورودی های ماشین را هم عوض می کنیم.
        CurrentMachineInput::where([
            "allocation_id" => $allocation->id,
            "machine_id" => $machine->id,
             // انبار مصرف کالا هم عوض می شود.
        ])->update(["machine_id"=> $new_machine->id, "consume_warehouse_id"=>$new_machine->warehouse_id]);


        // اگر ماشین جاری هیج تخصیصی جاری و رزور ندارد، نداشتن سفارش، در غیر این صورت اگر جاری دارد، که هیچ و اگر جاری ندارد، اولین رزور را جاری کند.
        self::UpdateStatusAfterOtherMachine($machine);

        $new_machine = Machine::find($new_machine->id);
        self::UpdateStatusAfterOtherMachine($new_machine);

        Script1007Controller::handle($machine->id,true);
        Script1007Controller::handle($new_machine->id,true);

        return [
            "result" => 1,

        ];
    }


    public static function UpdateStatusAfterOtherMachine(Machine $machine)
    {
        $gcac = new GeneralChangeAllocationController();

        $allocations =
            Allocation::where("machine_id", $machine->id)->
            whereIn("status_id", [5310010, 5310040])->get()->keyBy("priority_number");

        if (count($allocations) == 0) {
            // ماشین باید خاموش شود.
            $machine->setStatus(
                null,
                53001,
                7303001,
                null);
        } else {
            $current_allocation = Allocation::where("machine_id", $machine->id)->
            whereIn("status_id", [5310010,])->first();

            if (!$current_allocation) {

                $allocation = Allocation::where("machine_id", $machine->id)->
                whereIn("status_id", [5310040,])->
                orderBy("priority_number", "asc")->
                first();


                $allocation->priority_number = 0;
                $allocation->status_id = 5310010;
                $allocation->save();

                $k = 1;
                // در تخصیص هایی که چند تخصیص با هم هست، اولین تخصیص را باید جاری کنیم، مابقی را رزرو کنیم.
                foreach ($allocation->items as $machine_allocation_item) {
                    $machine_allocation_item->status_id = $k == 1 ? 5310010 : 5310040;
                    $machine_allocation_item->save();

                    $k++;
                }

                self::UpdatePriority($machine, $allocation, []);
                self::UpdateMachineStatus($allocation);
            } else {
                self::UpdatePriority($machine, $current_allocation, null);
            }


        }

    }

    public static function GetProductionAmount(Allocation $allocation)
    {
        $production_amount = ProductionFormItem::where("allocation_id", $allocation->id)->sum('amount');
        if ($production_amount > 0) {
            $production_form_item = ProductionFormItem::where("allocation_id", $allocation->id)->first();
            return [
                "result" => false,
                "error" => " با توجه به اینکه فرم تولید " . $production_form_item->getCode() . " برای تخصیص " . $allocation->id . " ایجاد شده است و بخضی از تخصیص تولید شده است، امکان جابجایی تخصیص برای آن وجود ندارد."
            ];
        }
        return [
            "result" => true,
        ];
    }

    public static function UpdatePriority(Machine $machine, $current_allocation, $reserve_allocation)
    {
        // اولویت تخصیص جاری باید صفر باشد.
        if ($current_allocation) {
            $current_allocation->priority_number = 0;
            $current_allocation->save();
        }
        if (!$reserve_allocation) {
            $reserve_allocation = $machine->ReserveAllocation()->get();
        }
        $priority_number = 1;
        foreach ($reserve_allocation as $reserve_allocation_item) {
            $reserve_allocation_item->priority_number = $priority_number;
            $reserve_allocation_item->save();
            $priority_number++;
//            echo $reserve_allocation_item->id."=>".$reserve_allocation_item->priority_number."<br/>";
        }
    }

    public static function ChangeAllocationStatus(Allocation $allocation, $status_id)
    {
        $allocation->status_id = $status_id;
        $allocation->save();
        MachineAllocation::where("allocation_id", $allocation->id)->update(["status_id" => $status_id]);
    }

    public static function UpdateMachineStatus(Allocation $allocation)
    {
        $machine_allocation = $allocation->items()->first();
        $machine_status_id = 7303902;

        if ($machine_allocation->line_product_station) {
            if ($machine_allocation->product->have_testing_before_production) {
                $machine_status_id = 7303906;
            }
            if ($machine_allocation->is_need_start_setup) {
                $machine_status_id = 7303901;
            }
        }

        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 5310912; // جابجایی تخصیص
        $machineLog->save();

        $machine_allocation->machine->setStatus(
            null,
            53001,
            $machine_status_id,
            null);
        event(new MachineLogEvent($machine_allocation->machine, $machineLog));

    }
}
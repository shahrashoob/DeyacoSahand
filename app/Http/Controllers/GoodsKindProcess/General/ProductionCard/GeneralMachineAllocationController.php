<?php

namespace App\Http\Controllers\GoodsKindProcess\General\ProductionCard;

use App\Events\Machine\MachineAllocationEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
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
use App\Models\Utility\Setting;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\View;

class GeneralMachineAllocationController extends Controller {

    /**
     * @param $permutation_list
     * @param $production
     *
     * @return string
     */
    public static function GetMessagePermutation( $permutation_list, $production,$machine ) {

        $max_permutation_list = $permutation_list["end_result"][0];
        // اگر هیچ پیشنهادی نداشیتم، از بین حالت ها حالتی که بیشترین مقدار تولید را می توانیم انجام دهیم، انتخاب می کنیم.
        foreach ( $permutation_list["end_result"] as $item ) {
            if ( $item["amount_can_be_produced"] > $max_permutation_list["amount_can_be_produced"] ) {
                $max_permutation_list = $item;
            }
        }


        $message = " موجودی انبار جهت تخصیص    " . "   کارت تولید " .
                   "کافی نمی باشد، شما می توانید حداکثر  " . $max_permutation_list["amount_can_be_produced"] . " " . $production->product->unit->caption . " " .
                   "از کارت تولید را به ماشین تخصیص دهید." . "<br/>";


        $product_list = [];
        foreach ( $max_permutation_list["material"] as $material ) {
            $product = Product::find( $material["material_id"] );
            if ( $max_permutation_list["amount_can_be_produced"] == $material["amount_for_production"] && ! isset( $product_list[ $product->id ] ) ) {
                $message .= "موجودی " . $product->caption . "(با کد:" . $product->code . "): " . round( $material["active_inventory"], 2 ) . " " . $product->unit->caption .
                            "  و حداکثر مقدار قابل تخصیص " .
                            round( $material["amount_for_production"], 2 )
                            . " " . $production->product->unit->caption . " می باشد." . "<br/>";

                $product_list[ $product->id ] = $product;
            }
        }

        return $message."<a href='".route("utility.json_view.allocation_data_type_200",[$production->id??0,$machine->id??0,0])."'>مشاهده اطلاعات تخصیص</a>";
    }



    public static function GetReserveAllocationList($reserve_allocation)
    {
        $reserve_allocation_list=[];
        foreach ($reserve_allocation as $r_allocation) {
            foreach ($r_allocation->items as $item) {
                if (!isset($reserve_allocation_list[$item->allocation_id])) {
                    $reserve_allocation_list[$item->allocation_id] = $item;
                } elseif ($item->production_id != $reserve_allocation_list[$item->allocation_id]->production_id) {
                    if (!isset($reserve_allocation_list[$item->allocation_id]->other_allocation_count)) {
                        $reserve_allocation_list[$item->allocation_id]->other_allocation_count = 0;
                    }

                    // دیگر تخصیص های همراه کالا اگر کارت تولید آن متفاوت بود
                    $reserve_allocation_list[$item->allocation_id]->other_allocation_count++;
                    $other = "other_allocation_" . $reserve_allocation_list[$item->allocation_id]->other_allocation_count;
                    $reserve_allocation_list[$item->allocation_id]->$other = $item;
                }

            }

        }
        return $reserve_allocation_list;
    }

}


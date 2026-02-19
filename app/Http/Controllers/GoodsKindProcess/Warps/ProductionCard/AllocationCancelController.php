<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\ProductionCard;

use App\Events\Machine\MachineLogEvent;
use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\Warps\ProductionCardController;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Production\Production;
use App\Models\Production\ProductionFormItem;
use Illuminate\Http\Request;

class AllocationCancelController extends Controller {
    public static $info = [
        "route"         => "warps.allocation_cancel.",
        "enable_status" => [ "001", "002", "003", ],
        "next_status"   => [],
        "button"        => [ "caption" => "کنسل کردن تخصیص ", "class" => "btn-danger" ],
        "view_path"     => ""
    ];

    public function index(Allocation $allocation, Production $production){

        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }

        $machine_allocation_info = ( $allocation->machine->machine_type->machine_module_type->directory_namespace . "\ProductionCard\AllocationCancelController" )::$info;

        return redirect()->route(
            $machine_allocation_info["route"] . "index",
            [ $allocation, $production ]
        );
    }

    public function checkPermission( Production $production ) {

         $result = ProductionCardController::checkPermissionConditions( $production, AllocationCancelController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }

    public static function GetIndex(Allocation $allocation, Production $production)
    {


        $has_before_allocation = Allocation::
        where("id", ">", $allocation->id)->
        where("machine_id", $allocation->machine_id)->
        where("status_id", 5310040)->
        get();
        if (count($has_before_allocation) > 0) {

            $list = "";
            foreach ($has_before_allocation as $item) {
                $list .= "<b>" . $item->items()->first()->production->serial() . "</b>,&nbsp;&nbsp;";
            }

            return [
                "result" => false,
                "error" => "لطفا ابتدا  " . count($has_before_allocation) .
                    "  کارت تولید که قبل از کارت " . $production->serial() .
                    " به ماشین " . $allocation->machine->fullCaption() .
                    " تخصیص داده شده اند را حذف نموده و سپس اقدام نمایید. " .
                    "<br/>" . $list
            ];

        }

        $list = [-1]; // در همه وضعیت های ماشین، امکان کنسل کردن وجود دارد.
        if (in_array($allocation->machine->production_status_id, $list)) {
            return [
                "result" => false,
                "error" => "وضعیت تولید ماشین جهت کنسل کردن تخصیص معتبر نیست."
            ];

        }

        if ($allocation->status_id != 5310040) {
            return [
                "result" => false,
                "error" => "تنها تخصیص های در حالت رزرو امکان حذف دارند."
            ];
        }

        //چک کردن اینکه برای تخصیص هیچ فرم تولیدی ایجاد نشده باشد
        $production_form_item = ProductionFormItem::where("allocation_id", $allocation->id)->first();
        if ($production_form_item > 0) {
            return [
                "result" => false,
                "error" => "با توجه به فرم تولید به شماره " . $production_form_item->production_form->code . " برای تخصیص ایجاد شده است، امکان حذف تخصیص وجود ندارد."
            ];

        }

        // چک کردن اینکه فرم درخواست چله برای کارت تولید نرفته باشد
        $warps_form = ProductRequestForm::where([
            "applicant_id" => $allocation->machine->id,
            "applicant_type_id" => 10,
            "allocation_id" => $allocation->id
        ])->
        orderByDesc("id")->first();

        if ($warps_form) {
            if (in_array($warps_form->status_id, [7005001, 7005003, 7005006])) {
                $warps_form->status_id = 7005006;
                $warps_form->save();
                event(new ProductRequestFormLogEvent($warps_form, "", null, 7005008));
            } else {
                return [
                    "result" => false,
                    "error" => "با توجه به اینکه  نخ برای درخواست کالا از انبار به شماره " . $warps_form->getCode() . " مربوط به این کارت تولید تحویل شده است، امکان حذف تخصیص وجود ندارد."
                ];

            }
        }


        $machineLog = MachineLog::create();
        $machineLog->machine_event_type_id = 95; // کنسل کردن تخصیص
        $machineLog->allocation_id = $allocation->id;
        event(new MachineLogEvent($allocation->machine, $machineLog));

        // کنسل کردن تخصیص
//        MachineAllocation::where( "allocation_id", $allocation->id )->delete();
        $allocation->status_id = 5310030;// کنسل شده
        $allocation->save();

        foreach ($allocation->items as $item) {
            $item->status_id = 5310030;// کنسل شده
            $item->save();
        }

        // بررسی وضعیت کارت تولید
        if ($production->get_allocation_amount() == 0) {
            $production->status_id = 500;
            //در انتظار تخصیص ماشین، .
            $production->waiting_status_id = 7201001;
            $production->save();
            event(new ProductionCardLogEvent($production));
        }

        // ارسال پیامک حذف تخصیص
        Production::sendSmsAfterAllocationCancel($production, $allocation->machine);

        return [
            "result" => true,
            "message" => "تخصیص ماشین با موفقیت کنسل شد."
        ];

    }
}

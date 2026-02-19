<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypePropertyValue;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use function back;
use function event;
use function redirect;

class RegisterProductionController extends Controller
{
    public static $info = [
        "route" => "fabric.finishing_machine.machine.register_production.",
        "enable_status" => ["903"],
        "button" => ["caption" => "ثبت تولید", "class" => "btn-primary"],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric.finishing_machine.machine.dashboard.";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
    }

    public function index(Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();
        if (!$allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین یافت نشد");
        }

        $machine_allocation = MachineAllocation::
        where([
            "allocation_id" => $allocation->id,
            "status_id" => 5310010
        ])->first();

        if (!$machine_allocation) {
            return back()->withErrors("اطلاعات تخصیص نامتعبر است، لطفا با پشیتبانی تماس بگیرید.");
        }
//        if ($machine_allocation->line_product_station->station_operation->station_operation_type_id == 1) {
//            return back()->withErrors("ثبت تولید برای عملیات های پیوسته طراحی شده است و برای عملیات های بچ طراحی نشده است، لطفا با پشتیبانی تماس بگیرید.");
//        }

        // گرفتن فرم تولید پارچه که در انتظار تزریق به ماشین می باشد:
        // 1- باید از تخیصیص قبلی باشد
        // 2- در انتظار تخیصیص باشد


//        $source_production_form_item=ProductionFormItem::
//        where("allocation_id", $machine_allocation->allocation_id)->
//        where("status_id", 7302001)-> // در حال تکمیل
//        first();
//
//        if(!$source_production_form_item){
//            return back()->withErrors("فرم تولید در حال تولید برای ماشین یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
//        }
        // گرفتن لات کالا


//        $current_production_form = $machine->getCurrentProductionForm();
//        if (!$current_production_form) {
//            // اگر فرم جاری وجود نداشت، آخرین فرم خاتمه یافته از کارت تولید را بر می داریم و مثل آن ایجاد می کنیم.
////            $checklist =[7302002]; //خاتمه یافته
////
////            return ProductionForm::
////            join("production_form_item","production_form_item.id","production_form_item_id") ->
////            whereIn(
////                "status_id",
////                $checklist
////            )->
////            where("machine_id", $this->id)->
////            orderByDesc("id")->
////            first();
//
//
//            return back()->withErrors("فرم تولید جاری برای ماشین یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
//        }

//        $production_form_item = ProductionFormItem::where([
//            "production_form_id" => $current_production_form->id,
//            "production_id" => $machine_allocation->production_id,
//            "status_id" => 7302001 // در حال تکمیل
//        ])->first();
//
//        if (!$production_form_item) {
//            return back()->withErrors("هیچ آیتم فرم تولیدی برای ماشین یافت نشد، قبلا همه آیتم های فرم تولید، بسته بندی شده اند");
//        }


//
//        $lot_number_id = $production_form_item->lot_numbers()->first()->lot_number_id;
//
//        session(["default_lot_number_" . $machine_allocation->id => $lot_number_id]);


        return redirect()->route("production.public_module.register_production.index", [$machine_allocation]);


    }


    public function checkPermission(Machine $machine)
    {

        $result = DashboardController::checkPermissionConditions($machine, self::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}

<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\ProductionCard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\Fabric\ProductionCardController;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\Production\Production;
use Illuminate\Http\Request;

class MachineAllocationController extends Controller
{
    public static $perfix_production_status_code = "7301";
    public static $info = [
        "route" => "fabric.machine_allocation.",
        "enable_status" => ["001", "002", "003","005"],
        "enable_route" => [
            "fabric.special_production.machine_allocation.index",
        ],
        "next_status" => [],
        "button" => ["caption" => "تخصیص ماشین", "class" => "btn-success"],
        "view_path" => "goods_kind_process.fabric.production_card.machine_allocation."
    ];


    public $dashboard_route = "fabric.machine_allocation.";
    public $controller_info;

    public function __construct()
    {
        $this->controller_info = MachineAllocationController::$info;
    }

    public function index(Production $production)
    {

//        $result = $this->checkPermission($production);
//        if ($result != "") {
//            return $result;
//        }

        if ($production->get_allocation_amount() >= $production->number) {
            return back()->withErrors("با توجه به اینکه مقدار تخصیص داده شده به اندازه مقدار کارت تولید می باشد، امکان تخصیص جدید وجود ندارد.");
        }


        return view($this->controller_info["view_path"] . "index", compact("production"));
    }

    public function reallocation(Production $production, MachineAllocation $machine_allocation)
    {

//        $result = $this->checkPermission($production);
//        if ($result != "") {
//            return $result;
//        }

        if ($machine_allocation->status_id != 5310050) {
            return back()->withErrors("وضعیت تخصیص معتبر نمی باشد.");
        }
        if ($machine_allocation->production_id != $production->id) {
            return back()->withErrors("اطلاعات تخصیص و کارت تولید نامعتبر است، لطفا با واحد پشتیبانی تماس بگرید.");
        }
        if (!$machine_allocation->line_product_station) {
            return back()->withErrors("اولین مسیر محضول تخصیص مشخص نشده است، لطفا با پشتیبانی تماس بگیرید.");
        }
        if (in_array($machine_allocation->production->status_id, [520])) {
            return back()->withErrors( "با توجه به اینکه وضعیت کارت تولید " . $machine_allocation->production->status->caption . " می باشد، امکان ادامه تخصیص کارت وجود ندارد.");

        }
        $current_line_product_station_list =[];

       $current_line_product_station_list=LineProductStation::where([
           "product_id"=>$machine_allocation->product_id,
           "station_operation_id"=>$machine_allocation->line_product_station->station_operation_id,
           "station_id"=>$machine_allocation->line_product_station->station_id,
       ])->
           where("machine_type_id","!=",$machine_allocation->line_product_station->machine_type_id)->
           get();

        $current_line_product_station_list[]=$machine_allocation->line_product_station;

        return view($this->controller_info["view_path"] . "index", compact("production", "machine_allocation","current_line_product_station_list"));

    }

    public function select_machine_type(Request $request, Production $production, MachineType $machine_type,LineProductStation $line_product_station,$current_machina_allocation_id)
    {


        $machine_id_name = "machine_type_" . $machine_type->id;

        if (!isset($request->$machine_id_name)) {
            return redirect()->
            route($this->dashboard_route . "index", $production)->withErrors("لطفا یک ماشین جهت تخصیص انتخاب نمایید.");
        }
        $machine_id = $request->$machine_id_name;

        // $dashboard_info=  ($machine_type->machine_module_type->directory_namespace."\ProductionCard\DashboardController")::$info;
        $machine_allocation_info = ($machine_type->machine_module_type->directory_namespace . "\ProductionCard\MachineAllocationController")::$info;



        return redirect()->route(
            $machine_allocation_info["route"] . "select_band",
            [$machine_id, $machine_type, $production, true,$line_product_station,$current_machina_allocation_id]
        );
    }

    public function checkPermission(Production $production)
    {

        $result = ProductionCardController::checkPermissionConditions($production, MachineAllocationController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}

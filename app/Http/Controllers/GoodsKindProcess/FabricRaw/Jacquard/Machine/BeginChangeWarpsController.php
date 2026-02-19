<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralInjectionOfMaterialController;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralMaterialReturnToWarehouseController;
use App\Models\Form\Packing\PackingForm;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\CurrentMachineInputLog;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormItem;
use Illuminate\Http\Request;

class BeginChangeWarpsController extends Controller
{
    public static $info = [
        "route" => "fabric_raw.jacquard.machine.begin_change_warps.",
        "enable_status" => ["022"],
        "button" => ["caption" => "شروع تعویض چله", "class" => "btn-primary"],
        "view_path" => "goods_kind_process.fabric_raw.jacquard.machine.begin_change_warps.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct()
    {
        $this->route_path = BeginChangeWarpsController::$info["route"];
        $this->view_path = BeginChangeWarpsController::$info["view_path"];
    }

    public function index(Machine $machine)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }
        $publicController = $this->getGeneralController();

        $allocation = $machine->getCurrentAllocation();

        if (!$allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین وجود ندارد، لطفا دکمه پایان بافت کارت تولید را بزنید.");
        }
        // کالاهایی که در درخواست است
        $warps_list = CurrentMachineInput::where([
            "allocation_id" => $allocation->id,
            "goods_kind_id" => 3,
            "replacement_status_id" => 3359002 // نیاز به تعویض دارد.
        ])->get();

        if (count($warps_list) == 0) {
            return back()->withErrors("لیست ورودی های ماشین برای تخصیص جاری نامعتبر است، لطفا با پشتیبانی تماس بگیرید.");
        }

//        ProductRequestFormItem::where("")

// درخواست های چله و باید روش تزریق مواد اولیه برای رسته کالایی به صورت دستی غیر فعال باشد.
        return $publicController->index($machine, 0, 3, false, null, 3359002);
    }

    public function submit(Request $request, Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();

        //پیدا کردن کد چله قبلی و ثبت اینکه کاملا مصرف شده است.
        $before_warp_input_list = CurrentMachineInput::
        where(["machine_id" => $machine->id, "allocation_id" => ($allocation->id ?? -1)])->
        where("goods_kind_id", 3)->
        where("replacement_status_id", 3359002)-> // نیاز به تعویض دارد.
        orderBy("goods_kind_id")->
        orderBy("input_line_code")->
        get();

        $input_number = CurrentMachineInput::
        where(["machine_id" => $machine->id, "allocation_id" => ($allocation->id ?? -1)])->
        where("goods_kind_id", 3)-> // چله
        where("replacement_status_id", 3359002)-> // نیاز به تعویض دارد.
        count();

        $result = InjectionOfMaterialController::SetInjectionMaterial(
            $request, $machine, $allocation, $input_number,
            null,
            null,
            true,
            3359002
        );
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }


        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 280;
//
        $machine->setStatus(
            null,
            53002,
            DashboardController::$perfix_production_status_code . "023",
            1618);


        event(new MachineLogEvent($machine, $machineLog));


        //پیدا کردن کد چله قبلی و ثبت اینکه کاملا مصرف شده است.
        self::AddModification($before_warp_input_list, $machine, $allocation);


        // ورودی های ماشین که قبلا در انتظار تعویض بودند، به نیاز به تعویض مشخص نشده تغییر دهیم.
        CurrentMachineInput::
        where(["machine_id" => $machine->id, "allocation_id" => ($allocation->id ?? -1)])->
        where("goods_kind_id", 3)-> // چله
        where("replacement_status_id", 3359002)-> // ; // نیاز به تعویض مشخص نشده.
        update(["replacement_status_id"=> 3359001]); // نیاز به تعویض دارد

        return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "عملیات با موفقیت انجام شد."]);

    }

    public static function AddModification($before_warp_input_list, $machine, $allocation)
    {


        $packing_form_ids = [];
        foreach ($before_warp_input_list as $input) {
            if ($input->packing_form) {
                $data[$input->packing_form_id]["consumed_status_id"] = 6021103;
                $packing_form_ids[] = $input->packing_form_id;
            }

        }

        if (count($packing_form_ids) > 0) {

            GeneralMaterialReturnToWarehouseController::AddNewModificationForPacking($machine, $packing_form_ids, $data, $allocation);
        }
//        $now_packing_forms = CurrentMachineInput::where( "allocation_id", $allocation->id )->
//        where( "goods_kind_id", 3 )->
//        pluck( "packing_form_id" )->
//        toArray();
//        if ( count( $now_packing_forms ) ) {
//            return [ "result" => true ]; // ورودی جاری چله ندارد،
//        }
//
//        $packing_form_ids = CurrentMachineInputLog::
//        join( "products", "products.id", "material_id" )->
//        join( "packing_forms", "packing_forms.id", "entry_packing_form_id" )->
//        where( [
//            "allocation_id" => $allocation->id,
//            "goods_kind_id" => 3,
//        ] )->
//        whereNotIn( "entry_packing_form_id", $now_packing_forms )->
//        where( [
//            "warehouse_status_id"        => 4201,
//            "packing_forms.status_id"    => 7007003,
//            "packing_forms.warehouse_id" => $machine->warehouse_id,
//        ] )->
//        groupBy( "current_machine_input_id" )->
//        orderByDesc( "current_machine_input_logs.id" )->
//        pluck( "entry_packing_form_id", "current_machine_input_id" );

//        foreach ( $packing_form_ids as $packing_form_id ) {
//            $data[ $packing_form_id ]["consumed_status_id"] = 6021103;
//        }


    }

    public function getGeneralController()
    {
        $publicController = new GeneralInjectionOfMaterialController();
        $publicController->route_path = $this->route_path;
        $publicController->dashboard_route = $this->dashboard_route;

        return $publicController;
    }

    public function checkPermission(Machine $machine)
    {

        $result = DashboardController::checkPermissionConditions($machine, BeginChangeWarpsController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}

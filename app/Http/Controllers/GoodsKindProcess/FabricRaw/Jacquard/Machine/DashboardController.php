<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Fabric_Raw\ProductionFromAmountUpdateEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\Warps\WarpsAvailableEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine\StartToStartMachineController;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralMaterialReturnToWarehouseLogController;
use App\Http\Controllers\GoodsKindProcess\General\ProductionCard\GeneralMachineAllocationController;
use App\Models\Form\Form;
use App\Models\Form\Packing\PackingForm;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Product\BOM\BOMFaultIllegal;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Post\PostStatus;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use function back;
use function redirect;
use function session;
use function view;

class DashboardController extends Controller
{
    public static $perfix_production_status_code = "7003";
    public static $info = [
        "route" => "fabric_raw.jacquard.machine.dashboard."
    ];
    var $view_path = "goods_kind_process.fabric_raw.jacquard.machine.dashboard.";
    var $route_path = "production.machine.";

    public function __construct()
    {

        View::share("perfix_status_code", DashboardController::$perfix_production_status_code);
    }

    public function view(Machine $machine)
    {


        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $controller_info = DashboardController::get_controller_info("view");
        $special_condition = DashboardController::enable_special_condition($machine);

        $allocation = $machine->getCurrentAllocation();

//if($machine->id ==  330){
//    return
//    $productionFromItemLot = FabricRaw::getCurrentLot($allocation, false, true);
//}

        $reserve_allocation = $machine->ReserveAllocation()->orderBy("priority_number")->get();
        $reserve_allocation_list = GeneralMachineAllocationController::GetReserveAllocationList($reserve_allocation);

//return $reserve_allocation_list;
        $productionFromItemLot = null;
        if ($allocation) {
            $productionFromItemLot = FabricRaw::getCurrentLot($allocation, false, true);

            // اگر به هر دلیل لات تولید نشده بود، دوباره لات را ایجاد می کند.
            if (count($productionFromItemLot) == 0) {
                FabricRaw::ChangeLot($allocation);
                $productionFromItemLot = FabricRaw::getCurrentLot($allocation, false, true);
                // اگر نتوانست دوباره تولید کند، خطا بدهد.
                if (count($productionFromItemLot) == 0) {
                    // return back()->withErrors("فرم تولید جاری ماشین، به درستی پردازش نشده است، لطفا با واحد پشتیبانی تماس بگیرید.");
                }
            }


        }

        // اگر در انتظار آماده سازی چله است و چله تحویل شده به ماشین
        if (in_array($machine->production_status_id, [7003019, 7003021, 7003029])) {

            $warps_check_allocation_id = 0;

            if ($allocation) {
                $warps_check_allocation_id = $allocation->id;
            } elseif (isset($reserve_allocation[0])) {
                $warps_check_allocation_id = $reserve_allocation[0]->id;
            }


            $warps_machine_input = CurrentMachineInput::where(
                [
                    "allocation_id" => $warps_check_allocation_id,
                    "goods_kind_id" => 3
                ])->
            groupBy("material_id", "input_line_code")->
            get();

            $warps_is_in_warehouse = true;

            foreach ($warps_machine_input as $input) {
                $warps_count_in_warehouse = PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_id")->where(
                    [
                        "product_id" => $input->material_id,
                        "warehouse_id" => $machine->warehouse_id,
                        "warehouse_status_id" => 4201
                    ]
                )->count();

                $warps_is_in_warehouse = $warps_is_in_warehouse && $warps_count_in_warehouse >= $input->number;
            }

            if ($warps_is_in_warehouse) {
                $new_production_status = 0;

                if ($machine->production_status_id == "7003019") {
                    $new_production_status = MachineModuleType::getChecklist($machine->machine_type->machine_module_type_id, "warps_delivery_7003019");
                }
                // ویرایش وضعیت ماشین اگر در انتظار آماده سازی چله جهت تعویض بود است
                if ($machine->production_status_id == "7003021") {
                    $new_production_status = MachineModuleType::getChecklist($machine->machine_type->machine_module_type_id, "warps_delivery_7003021");
                }
                // ویرایش وضعیت ماشین اگر در انتظار اماده سازی چله جهت تغییر کالیته بود
                if ($machine->production_status_id == "7003029") {
                    $new_production_status = MachineModuleType::getChecklist($machine->machine_type->machine_module_type_id, "warps_delivery_7003029");

                }

                if ($new_production_status != 0) {
                    $machine->on_status_id = 53002;
                    $machine->machine_off_reason_id = 1617;
                    $machine->production_status_id = $new_production_status;
                    $machine->save();

                    $machineLog = new MachineLog();
                    $machineLog->machine_event_type_id = 250;
                    event(new MachineLogEvent($machine, $machineLog));
                }
            }

        }


        $production_form = $machine->getCurrentProductionForm();
        $production_form_reserve = $machine->getReserveProductionForm();

        $production_form_carrier_code = ($production_form->carrier->code ?? "---") . " (" . ($production_form->carrier->carrier_type->caption ?? "") . ")";
        $production_form_reserve_carrier_code = ($production_form_reserve->carrier->code ?? null) . " " . ($production_form_reserve->carrier->carrier_type->caption ?? "");


        $current_input_list = CurrentMachineInput::
        where(["machine_id" => $machine->id, "allocation_id" => ($allocation->id ?? -1)])->
        groupBy("material_id", "input_line_code")->
        orderBy("goods_kind_id")->
        orderBy("input_line_code")->
        selectRaw("*, sum(amount_required) as amount_required, sum(amount) as amount")->
        get();


        $form_list = Form::where([
            "applicant_type_id" => 10, // ماشین
            "applicant_id" => $machine->id
        ])->paginate(10);


        return view($this->view_path . "view", compact(
            "production_form_reserve_carrier_code",
            "current_input_list", "production_form_carrier_code",
            "productionFromItemLot",
            "allocation",
            "machine",
            "controller_info",
            "special_condition",
            "reserve_allocation_list",
            "form_list",
            "production_form",

        ));
    }


    public function short_link(Machine $machine)
    {
        return redirect()->route("production.machine.short_link", $machine);
    }

    public function checkPermission(Machine $machine)
    {
        $result = DashboardController::checkPermissionConditions($machine);
        if (!$result["result"]) {
            $message = \Session::get('success');
            if (isset($message)) {
                return redirect()->route($this->route_path . "index")->with(["success" => $message]);
            }

            return redirect()->route($this->route_path . "index")->withErrors($result["message"]);
        }

    }

    public static function checkPermissionConditions(Machine $machine, $info = false, $all_status = false)
    {

        $allowed_status_ids = PostStatus::getAllowedStatus(3);
        if (!in_array($machine->production_status_id, $allowed_status_ids)) {
            return [
                "result" => false,
                "message" => "شما اجازه مشاهده ماشین را ندارید.",
            ];
        }

        $allowed_machine_ids = Line::getAllowedMachine();
        if (!in_array($machine->id, $allowed_machine_ids)) {
            return [
                "result" => false,
                "message" => "شما اجازه مشاهده ماشین را ندارید.",
            ];
        }
        if ($info != false) {
            foreach ($info["enable_status"] as &$value) {
                $value = DashboardController::$perfix_production_status_code . $value;
            }
            unset($value);
            if (!$all_status && !in_array($machine->production_status_id, $info["enable_status"])) {
                return [
                    "result" => false,
                    "message" => "وضعیت ماشین جهت عملیات نامعتبر است",
                    "error_type" => "for_machine_status"
                ];
            }

            $post_user = Auth::user()->posts->first();
            if (!$post_user->checkButtonPermission($info["route"] . "index")) {
                return [
                    "result" => false,
                    "message" => "دسترسی  عملیات برای شما تعریف نشده است",
                ];
            }
        }

        return [
            "result" => true,
        ];

    }

    public static function enable_special_condition(Machine $machine)
    {

        // $design_form  = FabricRawDesignForm::getDesignFormFromMachine( $machine );
        $result = [];
        //$result["12"] = isset( $design_form->design_available ) ? ! $design_form->design_available : false;

// با وصل کردن چله به انبار این ماژول های حذف شدند.
//        $allocation = Allocation::where( "machine_id", $machine->id )->whereIn( "status_id", [
//            5310010,
//            5310040
//        ] )->orderBy( "id" )->first();
//        if ( isset( $allocation ) ) {
//            $warps_request_form = WarpsRequestForm::where( [
//                "status_id"     => 7005004, // در انتظار تایید درخواست کننده
//                "allocation_id" => $allocation->id
//            ] )->first();
//        }
//        $result["05"] = isset( $warps_request_form );
//        $result["06"] = isset( $warps_request_form );


        // فرم در انتظار تایید انبارک
        $product_request_form_count = ProductRequestForm::where([
            "applicant_type_id" => 40,
            "applicant_id" => $machine->warehouse_id,
            "status_id" => 7005004,// در انتظار تایید برگ خروج
        ])->count();
        $result["32"] = $product_request_form_count > 0;
        $result["33"] = $product_request_form_count > 0;


        $current_allocation = $machine->getCurrentAllocation();
        $result["21"] = $current_allocation ? true : false;
        $result["34"] = $current_allocation ? true : false;

        $last_log = MachineLog::where("machine_id", $machine->id)->orderByDesc("id")->first();

        if ($last_log && $last_log->machine_event_type_id == 650) { // در حال تحویل شیفت
            for ($k = 1; $k < 90; $k++) {
                if ($k != 27) {
                    $result[($k < 10 ? "0" : "") . $k] = false;
                }
            }
        }

        return $result;


    }

    public function change_lot_confirmation(Machine $machine)
    {

        $allocation = $machine->getCurrentAllocation();
        $productionFromItemLot = FabricRaw::getCurrentLot($allocation, true);

        return view($this->view_path . "change_lot_confirmation", compact("machine", "productionFromItemLot"));

    }

    public static function get_controller_info($type = "")
    {
        $controller_info = [
            "01" => EndOfChangeDesignController::$info,
            "02" => EndOfProductionCardTextureController::$info,
            "03" => BeginWarpsExtractionAndPutController::$info,
            "04" => EndOfWarpsExtractionAndPutController::$info,
            // با وصل کردن چله به انبار این ماژول های حذف شدند.
//            "05" => WarpsDeliveryConfirmationController::$info,
//            "06" => WarpsDeliveryRejectController::$info,
            "07" => WarpsDeliveryToWarehouseController::$info,
            "08" => BeginWarpingForChangeDesignController::$info,
            "09" => EndOfWarpingForChangeDesignController::$info,
            "10" => BeginPinningController::$info,
            "11" => EndOfPinningController::$info,
            "12" => BeginForChangeDesignController::$info,
            "13" => BeginLaunchController::$info,
            "14" => EndOfLaunchController::$info,
            "15" => RequestChangeWarpsController::$info,
            "16" => BeginChangeWarpsController::$info,
            "17" => EndOfChangeWarpsController::$info,
            "18" => BeginWarpingForChangeWarpsController::$info,
            "19" => EndOfWarpingForChangeWarpsController::$info,
            "20" => RequestChangeWarpsCancelController::$info,
            "21" => AllocationCardController::$info,
            "22" => ChangeAllocationAmountController::$info,
            "23" => BeginCombingController::$info,
            "24" => EndOfCombingController::$info,
            "25" => BeginChangeMachineBarController::$info,
            "26" => EndOfChangeMachineBarController::$info,
            "27" => OperatorController::$info,
            "28" => MachineCardController::$info,
            "29" => FailureToLaunchController::$info,
            "30" => BeginSamplingController::$info,
            "31" => EndOfSamplingController::$info,
            "32" => MaterialDeliveryConfirmationController::$info,
            "33" => MaterialDeliveryRejectController::$info,
            "34" => InjectionOfMaterialController::$info,
            "35" => MaterialReturnToWarehouseController::$info,
            "36" => WasteCollectionController::$info,
            "37" => ReLaunchController::$info,
            "38" => RequestRawMaterialController::$info,
            "39" => MachineFaultNotificationController::$info,
            "40" => MachineMaintenanceConfirmController::$info,
            "41" => ProductionChannelManagementController::$info,
            "42" => FailureChangeDesignController::$info,
            "43" => MaterialReturnToWarehouseLogController::$info,
            "44" => RegisterBrandController::$info,
            "98" => FinishedAllocationController::$info,
            "99" => LogController::$info,
        ];

        return $controller_info;
    }
}

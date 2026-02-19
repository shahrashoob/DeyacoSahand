<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\Matthys\ProductionCard;

use App\Events\Machine\MachineAllocationEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\ProductionCard\GeneralMachineAllocationController;
use App\Http\Controllers\GoodsKindProcess\General\ProductionCard\GeneralProductionChannelController;
use App\Http\Controllers\GoodsKindProcess\Warps\Matthys\Machine\EndOfBeamingController;
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
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\MaterialFlow;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\Post\Post;
use App\Models\Post\PostUser;
use App\Models\Production\Production;
use App\Models\Utility\Setting;
use App\Notifications\SMSNotification;
use http\Exception\BadConversionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\View;
use function Symfony\Component\Translation\t;

class MachineAllocationController extends Controller
{
    //
    public static $info = [
        "route" => "warps.matthys.machine_allocation.",
        "enable_status" => ["001", "002", "003"],
        "next_status" => [],
        "button" => ["caption" => "تخصیص ماشین (چله کشی Matthys)", "class" => "btn-success"],
        "view_path" => "goods_kind_process.warps.matthys.production_card.machine_allocation."
    ];

    public $dashboard_route = "warps.machine_allocation.";
    public $controller_info;

    public function __construct()
    {
        $perfix_status_code = DashboardController::$perfix_status_code;
        $this->view_path = View::share("perfix_status_code", $perfix_status_code);
        $this->controller_info = MachineAllocationController::$info;
    }

    public function select_band(Request $request, $machine_id, MachineType $machine_type, Production $production, $is_first_production = false)
    {
        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }

        $key = "machine_type_" . $machine_type->id;
        $machine = Machine::find($request->$key ?? $machine_id);
        $allocation_amount_list = [];

        if (!isset($machine)) {
            return redirect()->
            route($this->dashboard_route . "index", $production)->withErrors("لطفا یک ماشین جهت تخصیص انتخاب نمایید.");
        }


        // حذف کارت تولید های در انتظار تایید
        if ($is_first_production) {
            $allocation_delete_ids = MachineAllocation::where([
                "machine_id" => $machine->id,
                "status_id" => 5310005
            ])->pluck("allocation_id")->toArray();
            Allocation\AllocationData::whereIn("allocation_id", $allocation_delete_ids)->delete();
            MachineAllocation::where(["machine_id" => $machine->id, "status_id" => 5310005])->delete();

        }

        // چک کردن اینکه گروه ماشین در خط محصول وجود داشته باشد.
        if (!$production->product->line_product_station()->where("machine_type_id", $machine_type->id)->exists()) {
            return redirect()->
            route($this->dashboard_route . "index", $production)->
            withErrors("ماشین در مسیر - محصول های تعریف شده برای محصول وجود ندارد.");
        }
        $machine_check = Machine::
        join("machine_status", "machines.production_status_id", "machine_status.production_status_id")->
        where([
            "machine_type_id" => $machine_type->id,
            "possibility_of_allocation_machine" => 1,
            "machines.id" => $machine->id,
            "machines.active_status_id" => 1200
        ])->first();

        // محاسبه برای باند 1
        /**
         * محاسبه حداکثر مقدار قابل تخصیص برای کارت تولید
         * در  باند خروجی
         */
        $open_band_list = [1];
        $band_count_allocation = 1;// تعداد باند خورجی ماشین های matthys یک می باشد.
        $sum_allocation_amount = MachineAllocation::where("production_id", $production->id)->
        whereIn("status_id", ["5310010", "5310020", "5310040"])->sum("allocation_amount");

        if ($production->number - $sum_allocation_amount <= 0) {
            return redirect()->
            route($this->dashboard_route . "index", $production)->
            withErrors("با توجه به مقدار کارت تولید و تخصیصی ها انجام شده، امکان تخصیص جدید برای کارت تولید   وجود ندارد.");

        }


        //مقدار تخصیص را به تعداد باندهای مشابه تقسیم می کنیم
        $allocation_amount_list[1] = round(($production->number - $sum_allocation_amount) / $band_count_allocation, 2);


        $reserve_after_allocation_option = Production::getReserveAfterAllocationOption($production, $machine);

        if ($reserve_after_allocation_option["result"] == false) {
            return back()->withErrors($reserve_after_allocation_option["message"]);
        }

        $reserve_after_allocation_option = $reserve_after_allocation_option["list"];

        return \view($this->controller_info["view_path"] . "select_band", compact("allocation_amount_list", "band_count_allocation", "machine", "production", "allocation_amount_list", 'open_band_list', "reserve_after_allocation_option"));


    }


    public function confirm_submit(Request $request, Machine $machine)
    {

        $production = Production::find($request->production_id ?? 0);
        if (!$production) {
            return back()->withErrors("کارت تولید جهت تخصیص یافت نشد.");
        }

        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }

        // در صورتی که کارت نمونه گیری باشد، این متغیر در select_band مقدار دهی می شود.
        $reserve_after_allocation_id = session("reserve_after_allocation_id");


        /**
         * حدف تخصیص های معلق قبلی
         */
        MachineAllocation::where([
            "machine_id" => $machine->id,
            "status_id" => 5310005
        ])->delete();


        // اگر وضعیت ماشین  نداشتن سفارش باشد، باید ماژول پایان برگردان اجرا شود، در اینجا چک می شود که در اجرا ماژول خطایی وجود نداشته باشد.
        if ($machine->production_status_id == 7203001) {
            // چک کردن اینکه در ماژول پایان برگردان خطایی نداشته باشیم
            $resultEndOfBeamingController = EndOfBeamingController::submitHasAnError($request, $machine);
            if (!$resultEndOfBeamingController["result"]) {
                return back()->withErrors($resultEndOfBeamingController["error"]);;
            }
        }

        // ثبت تخصیص
        $result_confirm_submit = \App\Http\Controllers\GoodsKindProcess\Warps\ProductionCard\MachineAllocationController::
        ConfirmSubmit($request, $machine, $production, $reserve_after_allocation_id);

        if (!$result_confirm_submit["result"]) {
            return redirect()->route("warps.production_card.view_card",$production)->withErrors( $result_confirm_submit["error"]);

        }

        // اگر وضعیت ماشین نداشتن سفارش باشد ماژول پایان برگردان اجرا می شود.
        if ($machine->production_status_id == 7203001) {
            EndOfBeamingController::submitConfirm($request, $resultEndOfBeamingController);
        }

        return redirect()->route("warps.production_card.view_card",$production)->with(["success" => $result_confirm_submit["message"]]);

    }

    public function checkPermission(Production $production)
    {

//         بررسی دسترسی در ماژول
        $result = DashboardController::checkPermissionConditions($production, null,);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

    // Production Channel
//    public function create_new_channel(Machine $machine, Production $production, $allocation_amount)
//    {
//
//        // از طریق کنترلر جنرال یک کانال جدید ایجاد می کنیم.
//        $GPCC = new GeneralProductionChannelController();
//        $GPCC->route_path = $this->controller_info["route"];
//        $GPCC->dashboard_route = $this->dashboard_route;
//
//        $result = $this->checkPermission($production);
//        if ($result != "") {
//            return $result;
//        }
//
//        return $GPCC->create_new_channel($machine, $production, $allocation_amount);
//    }
//
//    public function store_new_channel(Request $request, Machine $machine, Production $production)
//    {
//        // از طریق کنترلر جنرال یک کانال جدید ایجاد می کنیم.
//        $GPCC = new GeneralProductionChannelController();
//        $GPCC->route_path = $this->controller_info["route"];
//        $GPCC->dashboard_route = $this->dashboard_route;
//
//        $result = $this->checkPermission($production);
//        if ($result != "") {
//            return $result;
//        }
//
//        return $GPCC->store_new_channel($request, $machine, $production);
//    }
}


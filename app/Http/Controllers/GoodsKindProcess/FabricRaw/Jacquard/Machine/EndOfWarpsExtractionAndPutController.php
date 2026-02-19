<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralInjectionOfMaterialController;
use App\Models\Form\Form;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EndOfWarpsExtractionAndPutController extends Controller
{
    public static $info = [
        "route" => "fabric_raw.jacquard.machine.end_of_warps_extraction_and_put.",
        "enable_status" => ["045"],
        "button" => ["caption" => "پایان استخراج چله و چله گذاری", "class" => "btn-primary"],
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct()
    {
        $this->route_path = EndOfWarpsExtractionAndPutController::$info["route"];
    }

    public function index(Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();
        $reserve_allocation = Allocation::where("machine_id", $machine->id)->whereIn("status_id", [
            5310010,
            5310040
        ])->orderBy("id")->first();

        if (!$reserve_allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین یافت نشد.");
        }

// درخواست های چله و باید روش تزریق مواد اولیه برای رسته کالایی به صورت دستی غیر فعال باشد.
        return $publicController->index($machine, 0, 3, false, $reserve_allocation);

    }

    public function submit(Request $request, Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $allocation = Allocation::where("machine_id", $machine->id)->whereIn("status_id", [
            5310010,
            5310040
        ])->orderBy("id")->first();


        // در تعویض چله یک کانال تولید مشابه کانال تولید جاری ماشین داریم که بعد از آن رزرو است،
        // کانال تولید جاری را تولید شده و کانال رزرو را به جاری تبدیل می کنیم.
        $current_production_channel = $machine->getCurrentProductionChannel();
//        if (!$current_production_channel) {
//            return back()->withErrors("کانال تولید جاری برای ماشین، وجود ندارد، لطفا با پشتیبانی تماس بگیرید.");
//        }

        $reserve_production_channel_list = $machine->ReserveProductionChannel();
        $result_new_channel = false;
        $reserve_production_channel = null;
        foreach ($reserve_production_channel_list as $reserve_channel) {
            if (!$current_production_channel) {
                $result_new_channel = true;
                $reserve_production_channel = $reserve_channel;
                break;
            }
            if (
                $reserve_channel->production_channel_type_id != $current_production_channel->production_channel_type_id
            ) {
                $result_new_channel = true;
                $reserve_production_channel = $reserve_channel;
            }
        }
        if (!$result_new_channel) {
            return back()->withErrors("کانال تولید رزور برای ماشین، وجود ندارد، لطفا با پشتیبانی تماس بگیرید.");
        }
        if (
            $current_production_channel &&
            $current_production_channel->production_channel_type_id
            ==
            $reserve_production_channel->production_channel_type_id
        ) {
            return back()->withErrors("نوع کانال تولید جاری و کانال تولید رزرو با هم برابر است، لطفا با پشتیبانی تماس بگیرید.");
        }

//        if ( $allocation ) {
//            // چک کردن اینکه فرم ورود به انبار تحویل شده باشد
//            $warps_form = ProductRequestForm::where( [
//                "applicant_id"      => $machine->id,
//                "applicant_type_id" => 10,
//                "allocation_id"     => $allocation->id
//            ] )->where( "status_id", "!=", WarpsRequestForm::$perfix_status_code . "002" )->
//            orderByDesc( "id" )->first();
//
//            if ( isset( $warps_form ) ) {
//                return back()->withErrors( "لطفا ابتدا چله را از انبار تحویل گرفته و فرم مربوطه را تایید نمایید." );
//            }
//        }
//
//
//        // چک کردن اینکه فرم های خروج از انبار تایید شده باشد
//        $form_count = Form::where( "status_id", "!=", 500000200 )->
//        where( [
//            "ic"                => 1, // بافندگی
//            "applicant_type_id" => 10, // ماشین
//            "applicant_id"      => $machine->id
//        ] )->count();


//        if ( $form_count > 0 ) {
//            return back()->withErrors( "لطفا ابتدا چله های قبلی را به انبار تحویل داده و تاییدیه انبار را دریافت نمایید." );
//        }

        $input_number = CurrentMachineInput::
        where(["machine_id" => $machine->id, "allocation_id" => ($allocation->id ?? -1)])->
        where("goods_kind_id", 3)-> // چله
          groupBy("input_line_code","material_id")->
        orderBy("goods_kind_id")->
        orderBy("input_line_code")->
        count();

        $result = InjectionOfMaterialController::SetInjectionMaterial($request, $machine, $allocation, $input_number, null, null, true);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        // تغییر کانال تولید
        if ($current_production_channel) {
            $current_production_channel->status_id = 3358003;// تولید شده
            $current_production_channel->save();
        }

        $reserve_production_channel->status_id = 3358001;// کانال جاری
        $reserve_production_channel->save();

        // کانال هایی که اضافه تولید شده است را تولید شده می کنیم.
        // کانال های تولیدی که بعد از کانال جاری و قبل از کانال رزروی که جاری می شود را تولید شده می کنیم، چون ممکن است یک کانال تولید اشتباه تولید شده باشد.
        foreach ($reserve_production_channel_list as $reserve_channel) {
            if (
                $current_production_channel &&
                $reserve_channel->production_channel_type_id == $current_production_channel->production_channel_type_id
                &&
                $reserve_channel->id < $reserve_production_channel->id

            ) {
                $reserve_channel->status_id = 3358003;// تولید شده
                $reserve_channel->save();
            }
        }

        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 540;
//
        $machine->setStatus(
            null,
            53002,
            7003031, // در انتظار شروع گره زنی جهت تغییر کالیته
            1660,
            "Fabric_Raw");

//        // بروزرسانی حامل ها در ورودی های ماشین
//        Warps::updateCarrierInCurrentInputOutputBand( $machine, "setCurrentCarrier", $allocation );

        event(new MachineLogEvent($machine, $machineLog));


        return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "عملیات با موفقیت انجام شد."]);

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

        $result = DashboardController::checkPermissionConditions($machine, EndOfWarpsExtractionAndPutController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}

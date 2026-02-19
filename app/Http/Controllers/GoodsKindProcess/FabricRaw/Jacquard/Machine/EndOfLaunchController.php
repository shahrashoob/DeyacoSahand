<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationMaterialConsumed;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypePropertyValue;
use App\Models\LineProduct\Product;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;

use Illuminate\Http\Request;

class EndOfLaunchController extends Controller
{
    public static $info = [
        "route" => "fabric_raw.jacquard.machine.end_of_launch.",
        "enable_status" => ["048"],
        "button" => ["caption" => "پایان راه اندازی شیفت", "class" => "btn-primary"],
//        "message"       => [ "confirm" => "آیا از پایان راه انداری شیفت اطمینان دارید؟" ],
        "view_path" => "goods_kind_process.fabric_raw.jacquard.machine.end_of_launch.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct()
    {
        $this->route_path = EndOfLaunchController::$info["route"];
        $this->view_path = EndOfLaunchController::$info["view_path"];
    }

    public function index(Machine $machine)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }


        return view($this->view_path . "index", compact("machine"));

    }

    public function submit(Request $request, Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $message = "";
        $last_row_log = MachineLog::getLastLogWithContour($machine);

        $contour_result = $last_row_log->checkMinContour(
            $request->contour_1_value,
            $request->contour_2_value,
            $request->contour_3_value,
            $request->contour_4_value,
            $request->contour_5_value);
        if (
            isset($last_row_log) && !$contour_result["result"]
        ) {
            $message .= $contour_result["error"];
        }

        if ($message != "") {
            return back()->withErrors($message);
        }


        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 230;
        $machineLog->contour_1_value = $request->contour_1_value * $contour_result["ratio"];
        $machineLog->contour_2_value = $request->contour_2_value * $contour_result["ratio"];
        $machineLog->contour_3_value = $request->contour_3_value * $contour_result["ratio"];
        $machineLog->contour_4_value = $request->contour_4_value * $contour_result["ratio"];
        $machineLog->contour_5_value = $request->contour_5_value * $contour_result["ratio"];

        // بررسی اینکه مقداری از پارچه که باید در راه اندازی شیف باید بافته شود، بافته شده است یا خیر
        $some_of_fabric_that_needs_to_woven = MachineModuleTypePropertyValue::getValue("70030021401", $machine->machine_type_id);
        $the_contour_error_value = MachineModuleTypePropertyValue::getValue("70030021402", $machine->machine_type_id);
        $number_of_doff_need_to_woven = MachineModuleTypePropertyValue::getValue("70030021404", $machine->machine_type_id);
        //در صورتی که واحد فرعی 2، کالایی قاب است، <br/>حداقل مقدار راه اندازی ( 1- تعداد قاب 2- مقدار راه اندازی) باشد.
        $min_of_frame_or_value = MachineModuleTypePropertyValue::getValue("70030021405", $machine->machine_type_id);

        if ($some_of_fabric_that_needs_to_woven == "" || $the_contour_error_value == "" || $number_of_doff_need_to_woven == "" || $number_of_doff_need_to_woven == "0" || $min_of_frame_or_value == "") {
            return back()->withErrors("تنظیمات راه اندازی شیف انجام نشده است، لطفا با پشتیبانی تماس بگیرید.");
        }


        $some_of_fabric_that_needs_to_woven = $some_of_fabric_that_needs_to_woven / 100; // متر
        $current_production_form = $machine->getCurrentProductionForm("current_production_form_status_with_reserve");
        if (!$current_production_form) {
            return back()->withErrors("فرم تولید جاری ماشین یافت نشده، لطفا با پشتیبانی تماس بگیرید.");
        }
        $current_production_form_item = $current_production_form->items()->orderByDesc("id")->first();
        $product = $current_production_form_item->product;

        $result=self::GetWovenAmount($product,$number_of_doff_need_to_woven,$some_of_fabric_that_needs_to_woven,$min_of_frame_or_value);
        if(!$result["result"]){
            return back()->withErrors($result["error"]);
        }
        $some_of_fabric_that_needs_to_woven=$result["amount"];

        // بررسی مقدار پیک ماشین که در راه اندازی شیفت باید زده شود
        // مقدار
        $contour_need_to_woven = GoodsKind::getMachineContourValueFromAmount($product, $some_of_fabric_that_needs_to_woven);

        $current_contour = $machineLog->sumCounter("calculate");

        $checklist = [
            290, //پایان تعویض چله
            530, // شروع استخراج چله و چله گذاری (جهت تغییر کالیته)
            570, // پایان تغییر کالیته
            238 // راه اندازی مجدد
        ];
        $before_log = MachineLog::
        where("machine_id", $machine->id)->
        whereIn("machine_event_type_id", $checklist)->
        orderByDesc("id")->
        first();
        if (!$before_log) {
            return back()->withErrors("پیک قبل از وضعیت راه اندازی شیف یافت نشد، لطفا با پشتیبانی تماس بگیرد.");
        }
        $before_contour = $before_log->sumCounter();

        $allow_launch = $contour_need_to_woven - $the_contour_error_value <= $current_contour - $before_contour
            && $contour_need_to_woven + $the_contour_error_value >= $current_contour - $before_contour;

        if ($allow_launch) {
            $reserve_allocation = $machine->getFirstReserveAllocation();
            $allocation = $machine->getCurrentAllocation();

            $machine->setStatus(
                null,
                53001,
                RequestChangeWarpsController::GetNextStatusAfterWarping($allocation, $reserve_allocation),
                null,
                "Fabric_Raw"
            );


            // تغییر وضعیت کارت تولید
            foreach ($allocation->items as $item) {
                if ($item->production->waiting_status_id == 7001002) {
                    $item->production->waiting_status_id = 7001003;
                    $item->production->save();
                    event(new ProductionCardLogEvent($item->production));
                }
            }

            event(new MachineLogEvent($machine, $machineLog));

            // تولید لات پارچه
            FabricRaw:: ChangeLot($allocation);


            $production_form = $machine->getCurrentProductionForm();
            if ($production_form) {
                // بروزرسانی مقادیر فرم تولید
                $last_machine_log = MachineLog::getLastLogWithContour($machine);
                ProductionForm::UpdateAmountWithLastContour($production_form, $last_machine_log);
            }

            // ثبت مقدار مصرف
            MachineAllocationMaterialConsumed::registerNewConsumed($allocation, $machine, $last_row_log, $machineLog);


            return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "عملیات با موفقیت انجام شد."]);
        } else {
            return back()->withErrors( "امکان راه اندازی شیفت وجود ندارد، لطفا با مسئول مربوطه تماس حاصل فرمایید." );
        }
    }

    // محاسبه مقدار داف با توجه به مقدار قاب و تنظیمات گروه ماشین
    public static function GetWovenAmount(Product $product, $number_of_doff_need_to_woven, $some_of_fabric_that_needs_to_woven, $min_of_frame_or_value)
    {

        // اگر واحد فرعی 2 کال قاب است است و نیاز است تا به اندازه داف بافته شده
        if ($product->sub_unit2_id == 1400) {
            if (!$product->frame_ratio_unit2) {
                return [
                    "result" => false,
                    "error" => "لطفا نسبت قاب به واحد اصلی را برای " . $product->caption . " ثبت نمایید."
                ];

            }


            $some_of_fabric_that_needs_to_woven_frame = ((int)$number_of_doff_need_to_woven * $product->frame_ratio_unit2);
            // اگر مقدار قاب از مقدار راه اندازی کمتر است، با توجه به تنظیمات تصمییم می گیریم که چه مقدار باید بافته شود؟

            if ($some_of_fabric_that_needs_to_woven > $some_of_fabric_that_needs_to_woven_frame) {

                if ($min_of_frame_or_value == 2) {
                    // تعداد قاب که بزرگتر از مقدار راه اندازی است.

                    $k = 1;
                    while ($k < 102) {

                        $some_of_fabric_that_needs_to_woven_frame = ((int)$number_of_doff_need_to_woven * $product->frame_ratio_unit2) * $k;
                        if ($some_of_fabric_that_needs_to_woven <= $some_of_fabric_that_needs_to_woven_frame) {
                            $some_of_fabric_that_needs_to_woven = $some_of_fabric_that_needs_to_woven_frame;
                            break;
                        }
                        $k++;

                        if ($k > 100) {
                            return [
                                "result" => false,
                                "error" =>"در زمان محاسبه مقدار راه اندازی متراژ داف قابل محاسبه نمی باشد، لطفا با واحد پشتیبانی تماس بگیرید."
                            ];

                        }
                    }

                } elseif ($min_of_frame_or_value == 1) {
                    // مقدار راه اندازی که هیچ تغییر نیاز نیست.
                } else {
                    return [
                        "result" => false,
                        "error" =>"تنظیمات راه اندازی شیف انجام نشده است، لطفا با پشتیبانی تماس بگیرید."
                    ];

                }
            } else {
                $some_of_fabric_that_needs_to_woven = $some_of_fabric_that_needs_to_woven_frame;
            }


        }


        $message= "عملیات با موفقیت انجام شد."."<br/>"."برای تایید راه اندازی شیف باید  $some_of_fabric_that_needs_to_woven   متر از کالا تولید شود.";
        if($product->sub_unit2_id == 1400){
            $message= "عملیات با موفقیت انجام شد."."<br/>"."برای تایید راه اندازی شیف باید ".
                floor(round($some_of_fabric_that_needs_to_woven_frame / $product->frame_ratio_unit2)) ." قاب - ".
                $some_of_fabric_that_needs_to_woven_frame." متر "
                ." از کالا تولید شود.";

        }
        return [
            "result" => true,
            "amount" => round($some_of_fabric_that_needs_to_woven,6),
            "message"=>$message
        ];
    }

    public function checkPermission(Machine $machine)
    {

        $result = DashboardController::checkPermissionConditions($machine, EndOfLaunchController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}

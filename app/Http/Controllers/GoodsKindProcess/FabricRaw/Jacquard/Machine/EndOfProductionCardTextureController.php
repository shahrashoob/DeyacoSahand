<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\ProductionCard\AllocationCancelController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\ProductionCard\MachineAllocationController;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationMaterialConsumed;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Machine\MachinePropertyValue;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Post\Post;
use App\Models\Production\Production;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Production\ProductionFormItemLotNumber;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Option;
use App\Models\Utility\QueueOfLargeOperation;
use App\Models\Utility\Setting;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class EndOfProductionCardTextureController extends Controller
{
    public static $info = [
        "route" => "fabric_raw.jacquard.machine.end_of_production_card_texture.",
        "enable_status" => ["042", "016", "017"],
        "button" => ["caption" => "پایان بافت کارت تولید", "class" => "btn-primary"],
        "view_path" => "goods_kind_process.fabric_raw.jacquard.machine.end_of_production_card_texture.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct()
    {
        $this->route_path = EndOfProductionCardTextureController::$info["route"];
        $this->view_path = EndOfProductionCardTextureController::$info["view_path"];
    }

    public function index(Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $contour_1_value = session("contour_1_value");
        $contour_2_value = session("contour_2_value");
        $contour_3_value = session("contour_3_value");
        $contour_4_value = session("contour_4_value");
        $contour_5_value = session("contour_5_value");
        $carrier_id = session("carrier_id");
        $shift_work_id = session("shift_work_id");

        $shift_work_option = Option::get("shift_work", $shift_work_id);
        $reserve_allocation = $machine->getFirstReserveAllocation();
        $allocation = $machine->getCurrentAllocation();

        // اگر کارت تولید فرم رزرو داشت اجازه داف ندهد
        $reserve_production = ProductionForm::where([
            "machine_id" => $machine->id
        ])->whereIn(
            "status_id", [
            7002011,// در انتظار بارگذاری
        ])->first();
        if ($reserve_production) {

            $current_production = ProductionForm::where([
                "machine_id" => $machine->id
            ])->whereIn(
                "status_id", [
                7002008, // در حال بافت پارچه پایانی)
            ])->first();

            if (!$current_production) {
                return back()->withErrors("غلطک پارچه برای ماشین یافت نشد، لطفا با پشتیبانی تماس بگیرید. ");
            }

            return back()->withErrors(" لطفا غلطک" .
                $current_production->carrier->code
                . " را استخراج و غلطک " .
                $reserve_production->carrier->code . " را بارگذاری نمایید.");

        }
        //    اگر داف آخر نیست خطا می دهد که تعداد داف ها برای اجرای ماژول به حد نصاب نرسیده است.
        if ($allocation) {
            if (!self::HasSpecialLicense($allocation)) {

                foreach ($allocation->items as $allocation_item) {

                    if ($allocation_item->number_of_doffs_done < $allocation_item->max_number_of_doffs - 1) {
                        $woven_amount = ProductionFormItem::where([
                            "allocation_id" => $allocation->id,
                            "production_id" => $allocation_item->production_id
                        ])->sum("amount");;
                        return back()->withErrors(
                            " تعداد داف های انجام شده برای اجرای ماژول به حد نصاب نرسیده است" . " " .
                            SpecialLicense::GetLink(2, $allocation->id, "ثبت مجوز جهت پایان بافت", round($woven_amount, 2))
                        );
                    }
                }
            } else {
                $has_special_licence = true; // با مجوز پایان بافت را زدند، پس اگر پایان بافت موفقیت آمیز بود، مقدار تخصیص را
                // به حداقل مقدار بافت یا مقدار تخصیص کاهش دهیم.
            }

        }


        $packing_type_option = Option::get("packing_type_from_output_band", 0, $machine->machine_type_id);
        if (!$packing_type_option["items"]) {
            return back()->withErrors("با توجه به اینکه تنظیمات خروجی ماشین انجام نشده است, امکان ثبت درخواست وجود ندارد لطفا با  پشتیبانی تماس بگیرید.");
        }
        $has_requirement_for_doffs_result = EndOfProductionCardTextureController::has_requirement_for_doffs($machine);
        if ($has_requirement_for_doffs_result["result"]) {
            $has_requirement_for_doffs = $has_requirement_for_doffs_result["doffs"];
        } else {
            return back()->withErrors($has_requirement_for_doffs_result["error"]);
        }


        return view($this->view_path . "index", compact(
            "machine",
            "shift_work_option",
            "has_requirement_for_doffs",
            "packing_type_option",
            "reserve_allocation",
            "contour_1_value",
            "contour_2_value",
            "contour_3_value",
            "contour_4_value",
            "contour_5_value",
            "carrier_id"
        ));

    }

    public function submit(Request $request, Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }


        $result = EndOfProductionCardTextureController::submitHasAnError($request, $machine);
        if (!$result["result"]) {
            if (isset($result["warning"])) {
                return back();
            }

            return back()->withErrors($result["error"]);
        }

        $has_requirement_for_doffs = $result["has_requirement_for_doffs"];


        EndOfProductionCardTextureController::submitConfirm($request, $result);

        //تغییر مقدار تخصیص به مقدار پایان بافت در صورتی که مجوز ثبت کرده بوده است.
        if ($result["has_special_licence"]) {
            $allocation = $result["allocation"];
            $machine_allocation = $result["machine_allocation_band_code1"];
            if (!$machine_allocation->production) {
                return redirect()->route($this->dashboard_route . "view", $machine)->withErrors("ثبت پایان بافت با موفقیت انجام شد، ولی به دلایل الحاقی مقدار تخصیص به مقدار بافته شده تغییر پیدا نکرد.");
            }
            $new_amount = $machine_allocation->production->get_production_amount($allocation->id) /  $allocation->items()->count();
            if($new_amount < $machine_allocation->allocation_amount) {
                $new_amount = round($new_amount, 2);
                ChangeAllocationAmountController::ChangeAllocationAmount($allocation, $new_amount, false, "<br/>تغییر مقدار تخصیص به مقدار بافته شده با مجوز  ");
            }
        }

        if ($has_requirement_for_doffs) {
            return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["warning" => "بافنده عزیز  پس از رسیدن پارچه به محل برش، غلطک پارچه را استخراج نمایید."]);
        } else {
            return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "عملیات با موفقیت انجام شد."]);
        }
    }

    public static function submitHasAnError(Request $request, Machine $machine, $no_doff_is_force = 0, $cheek_woven = true, $current_machine_fault_ids = [-1])
    {

        $has_special_licence = false;
        // لیست تخصیص هایی که به دلیل وجود نقص در ماشین امکان تولید آنها بر روی ماشین نیست.
        $reserve_allocation_illegal_fault = [];

        $has_requirement_for_doffs = EndOfProductionCardTextureController::has_requirement_for_doffs($machine, $current_machine_fault_ids);
        if ($has_requirement_for_doffs["result"]) {
            $reserve_allocation_illegal_fault = $has_requirement_for_doffs["allocation_illegal"];
            $has_requirement_for_doffs = $has_requirement_for_doffs["doffs"];

        } else {
            return [
                "result" => false,
                "error" => $has_requirement_for_doffs["error"]
            ];

        }
        if ($no_doff_is_force == 1) { // الزام به داف نباشده به صورت الزامی

            $has_requirement_for_doffs = false;
        }

        if ($no_doff_is_force == -1) {

            $has_requirement_for_doffs = true;
        }


        $allocation = $machine->getCurrentAllocation();

        $allocation_illegal_fault_ids = [-1];
        foreach ($reserve_allocation_illegal_fault as $item) {
            $allocation_illegal_fault_ids[] = $item->id;
        }
        $reserve_allocation = $machine->getFirstReserveAllocation(false, $allocation_illegal_fault_ids);

        /** کارت تولید آخرین رکورد فرم تولید ماشین*/
        $current_production_form = $machine->getCurrentProductionForm("current_production_form_status_with_reserve");

// دریافت قطب ها

        $message = "";
        $last_row_log = MachineLog::getLastLogWithContour($machine);

        $contour_result = isset($last_row_log) ? $last_row_log->checkMinContour(
            $request->contour_1_value,
            $request->contour_2_value,
            $request->contour_3_value,
            $request->contour_4_value,
            $request->contour_5_value) : MachineLog::getInitResult($machine);

        if (isset($last_row_log) && !$contour_result["result"]
        ) {
            $message .= $contour_result["error"];
        }


        if ($message != "") {
            return [
                "result" => false,
                "error" => $message
            ];

        }

        // اگر الزام به داف بود، شماره حامل جدید دریافت می شود.
        if ($has_requirement_for_doffs) {

            // پیدا کردن نوع حامل از روی نوع بسته بندی
            $packing_type = PackingType::find($request->packing_type_id);
            if (!$packing_type) {
                return [
                    "result" => false,
                    "error" => "نوع بسته بندی معتبر نمی باشد."
                ];

            }
            $first_layer = $packing_type->layers->where("layer_code", 1)->first();
            if (!$first_layer) {
                return [
                    "result" => false,
                    "error" => "تعریف نوع حامل در  بسته بندی معتبر نمی باشد، لطفا با پشتیبانی تماس بگیرید."
                ];


            }


            // بررسی حامل
            $result = Carrier::firstOrCreate($request->carrier_id, $first_layer->carrier_type_id, 5320001, null);
            if (!$result["result"] && $current_production_form && $request->carrier_id != ($current_production_form->carrier->code ?? -1)) {
                return [
                    "result" => false,
                    "error" => $result["message"]
                ];

            }

            if (isset($result["warning"])) {
                return [
                    "result" => false,
                    "error" => $result["warning"]
                ];
            }
            $carrier = $result["carrier"];


            if ($carrier->status_id != 5320001 && $current_production_form && isset($carrier) && $carrier->id != $current_production_form->carrier_id) {
                return [
                    "result" => false,
                    "error" => $result["message"]
                ];

            }
            // اگر حامل قابل شماره گذاری است باید وزن آن ثبت شده باشد
            $carrier_weight_result = CarrierType::getWeight($carrier->carrier_type, $carrier);
            if ($machine->check_inventory_for_allocation && !$carrier_weight_result["result"]) {
                return [
                    "result" => false,
                    "error" => $carrier_weight_result["error"]
                ];
            }
            // اگر شماره حامل، همان شماره حاملی باشد که روی ماشین است، باید تاییدیه بگیرد که شماره حامل وارد شده صحیح است.
            if ($current_production_form && $carrier->id == $current_production_form->carrier_id && !session("carrier_id")) {
// اگر حامل قبلی با حامل فعلی یکی باشد، باید تاییدیه بگیرد.
                session([
                    "contour_1_value" => $request->contour_1_value,
                    "contour_2_value" => $request->contour_2_value,
                    "contour_3_value" => $request->contour_3_value,
                    "contour_4_value" => $request->contour_4_value,
                    "contour_5_value" => $request->contour_5_value,
                    "carrier_id" => $request->carrier_id,
                    "shift_work_id" => $request->shift_work_id

                ]);

                return [
                    "result" => false,
                    "warning" => true,
                    "error" => "شماره حامل قبلی و حامل جدید با هم برابر است، آیا از ثبت فرم اطمینان دارید؟"
                ];

            }
            session([
                "contour_1_value" => null,
                "contour_2_value" => null,
                "contour_3_value" => null,
                "contour_4_value" => null,
                "contour_5_value" => null,
                "fabric_raw_type_of_cut_id" => null,
                "carrier_id" => null,
                "shift_work_id" => null,

            ]);

        }


        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 520; //پایان بافت (کارت تولید جاری)
        $machineLog->contour_1_value = $request->contour_1_value * $contour_result["ratio"];
        $machineLog->contour_2_value = $request->contour_2_value * $contour_result["ratio"];
        $machineLog->contour_3_value = $request->contour_3_value * $contour_result["ratio"];
        $machineLog->contour_4_value = $request->contour_4_value * $contour_result["ratio"];
        $machineLog->contour_5_value = $request->contour_5_value * $contour_result["ratio"];
        $machineLog->shift_work_id = $request->shift_work_id;
        $machineLog->save();

        $machine_allocation_band_code1 = null;

        if ($allocation) {

            $machine_allocation_band_code1 = $allocation->items()->where("band_code", 1)->first();

            // بررسی درصد مجاز اختلاف کالای تولید شده با مقدار تخصیص کارت در زمان پایان بافت کارت تولید
            // بررسی فقط برای باند خروجی 1 انجام میشود
            $allow_diff_percent = $machine_allocation_band_code1->product->goods_kind->min_diff_of_production_and_allocation_in_the_end_of_production;


            // ذخیره کارت تولید در رویداد پایان بافت کارت تولید جاری
            $machineLog->allocation_id = $allocation->id;
            $machineLog->save();

            if (!$machine_allocation_band_code1) {
                $machineLog->delete();

                return [
                    "result" => false,
                    "error" => "تخصیص باند 1 یافت نشد، لطفا با پشتیبانی تماس بگیرید."
                ];

            }

            $allocation_amount = $allocation->getAllocationAmount();

// متراژ کل بافته شده همراه با فرم جاری
            $woven_amount = ProductionFormItem::where([
                "allocation_id" => $allocation->id,
                "production_id" => $machine_allocation_band_code1->production_id
            ])->sum("amount");;

            foreach ($current_production_form->items()->where("production_id", $machine_allocation_band_code1->production_id)->get() as $item) {
                $woven_amount -= $item->amount;
                $woven_amount += GoodsKind::getAmountFromMachineLog($item->product, $item->start_machine_log, $machineLog);

//                echo "amount".$item->amount.", other=". GoodsKind::getAmountFromMachineLog($item->product, $item->start_machine_log, $machineLog)."<br/>";
            }

            // اگر تنظیمات اولیه مبنی بر چک کردن متراژ تخصیص T بود، چک می شود.
            $cheek_allocation_amount_with_production_amount = Setting::getIntegerValue("cheek_allocation_amount_with_production_amount");
            if (
                $cheek_allocation_amount_with_production_amount &&
                $machine_allocation_band_code1->production->production_type_id != 2 &&
                ($allocation_amount * (1 - $allow_diff_percent / 100)) > $woven_amount &&
                $cheek_woven
            ) {

                // اگر مجوز نداشت پیام بدهد.
                if (!self::HasSpecialLicense($allocation)) {

                    $machineLog->delete();

                    return [
                        "result" => false,
                        "error" => "مقدار بافت به حد نصاب نرسیده است، لازم است تا " . round($allocation_amount - $woven_amount, 1) . " متر دیگر  بافته شود." .
                            SpecialLicense::GetLink(2, $allocation->id, "ثبت مجوز جهت پایان بافت", round($woven_amount, 2))
                    ];
                } else {
                    $has_special_licence = true; // با مجوز پایان بافت را زدند، پس اگر پایان بافت موفقیت آمیز بود، مقدار تخصیص را
                    // به حداقل مقدار بافت یا مقدار تخصیص کاهش دهیم.
                }

            }


        }

        // بررسی اینکه برای کنسل کردن تخصیص های رزور امکان کنسل کردن وجود داشته باشد.
        foreach ($reserve_allocation_illegal_fault as $item) {
            $result_cancel = AllocationCancelController::CheckForChancel($item, true);
            if (!$result_cancel["result"]) {
                return [
                    "result" => false,
                    "error" => "با توجه به اینکه امکان کنسل کردن تخصیص شماره  " . $item->id .
                        " از ماشین به دلیل زیر وجود ندارد، ادامه عملیات مقدور نمی باشد. "
                        . "<<br/>" . $result_cancel["error"]
                ];
            }
        }


        // اگر در تخصیص جدد چله تغییر می کند، لازم است تا خطاهای درخواست چله بررسی شود.
        if ($reserve_allocation && $reserve_allocation->has_warps_change) {

            $requestChangeWarps = new RequestChangeWarpsController();
            $result_warps_change = $requestChangeWarps->request_warps_has_error($request, $reserve_allocation, $machine, true);
            if (!$result_warps_change["result"]) {
                return [
                    "result" => false,
                    "error" => "با توجه به اینکه چله تغییر می کند، در زمان درخواست چله جدید با خطای زیر مواجه می شویم:" . "<br/>" .
                        $result_warps_change["error"]
                ];
            }
        }


        // امکان ثبت پایان بافت کارت تولید بلامانع است.
        return [
            "result" => true,
            "machine" => $machine,
            "has_requirement_for_doffs" => $has_requirement_for_doffs,
            "machineLog" => $machineLog,
            "carrier" => $carrier ?? null,
            "packing_type" => $packing_type ?? null,
            "allocation" => $allocation,
            "reserve_allocation" => $reserve_allocation,
            "current_production_form" => $current_production_form ?? null,
            "last_row_log" => $last_row_log,
            "allocation_illegal_fault" => $reserve_allocation_illegal_fault,
            "woven_amount" => $woven_amount ?? null,
            "allocation_amount" => $allocation_amount ?? 0,
            "result_warps_change" => $result_warps_change ?? null,
            "has_special_licence" => $has_special_licence,
            "machine_allocation_band_code1" => $machine_allocation_band_code1
        ];
    }

    public static function submitConfirm(Request $request, $resultHasAnError)
    {

        $machine = $resultHasAnError["machine"];
        $has_requirement_for_doffs = $resultHasAnError["has_requirement_for_doffs"];
        $machineLog = $resultHasAnError["machineLog"];
        $carrier = $resultHasAnError["carrier"];
        $packing_type = $resultHasAnError["packing_type"];

        $allocation = $resultHasAnError["allocation"];
        $reserve_allocation = $resultHasAnError["reserve_allocation"];
        $current_production_form = $resultHasAnError["current_production_form"];
        $last_row_log = $resultHasAnError["last_row_log"];
        $reserve_allocation_illegal_faults = $resultHasAnError["allocation_illegal_fault"];
        $result_warps_change = $resultHasAnError["result_warps_change"];

        // بروز رسانی مقدار فرم های تولید، برای اینکه وقتی می خواهد خاتمه یافته کند، به مشکل بر نخورد.
        $production_form_update = $machine->getCurrentProductionForm();
        if ($production_form_update) {
            // بروزرسانی مقادیر فرم تولید
            ProductionForm::UpdateAmountWithLastContour($production_form_update, $machineLog);
        }

        // لیست کارت های رزروی که نمی توانند به دلیل نقص ماشین روی ماشین بافته شوند، مقدار تخصیص آنها را کنسل می کنیم و به یک ماشین دیگری تخصیص می دهیم.
        foreach ($reserve_allocation_illegal_faults as $reserve_allocation_illegal_fault) {

            $data["allocation_id"] = $reserve_allocation_illegal_fault->id;
            $data["allocation_amount"] = $reserve_allocation_illegal_fault->getAllocationAmount();
            QueueOfLargeOperation::AddToQueue($data, 1);

            AllocationCancelController::AutoCancel($reserve_allocation_illegal_fault);

        }

        // تغییر وضعیت تخصیص جاری به پایان یافته
        if ($allocation) {
            $allocation->status_id = 5310020;
            $allocation->save();
            foreach ($allocation->items as $item) {
                $item->status_id = 5310020;
                if (!$has_requirement_for_doffs) {
                    $item->number_of_doffs_done = $item->number_of_doffs_done + 1;
                }
                $item->save();
                // خاتمه یافته کردن کارت تولید در صورت نیاز
                FabricRaw::ProductionTerminated($item);


            }

            // یک درخواست ثبت می کنیم، تا در صورت نیاز یک تخصیص مجدد به خط بعدی موجود در مسیر محصول بدهد.
            //سپس در اسکریپت پردازش درخواست های بزرگ بررسی می کنیم، در صورت نیاز یک تخصیص می دهیم.
            // Script1021Controller
            QueueOfLargeOperation::AddToQueue(
                [
                    "allocation_id" => $allocation->id,
                ],
                600
            );

            // ثبت مقدار مصرف
            MachineAllocationMaterialConsumed::registerNewConsumed($allocation, $machine, $last_row_log, $machineLog);


        }

        // اگر الزام به داف بود و فرم تولید جاری داشت، وضعیت آن را به در حال بافت پارچه پایانی تبدیل می کنیم.
        if ($has_requirement_for_doffs) {
            // تغییر وضعیت فرم جاری از درحال بافت به در انتظار استخراج پارچه یایانی
            $current_production_form = $machine->getCurrentProductionForm("current_production_form_status_with_reserve");
            if ($current_production_form) {
                $current_production_form->ChangeStatus(7002008, "", 7002016); // در حال بافت پارچه پایانی
                $current_production_form->in_the_finishing_weaving_machine_log_id = $machineLog->id; // قطب شروع در حال بافت پارچه پایانی
                $current_production_form->end_of_machine_log_id = $machineLog->id; // قطب پایان
                $current_production_form->save();
            }
        }


        // سفارشی پشت ماشین نیست
        if (!$reserve_allocation) {
            $machine->setStatus(
                null,
                53002,
                7003017, // نداشتن سفارش
                1615,
                "Fabric_Raw"
            );

            EndOfProductionCardTextureController::turnOffMachineNotification($machine);

        } // الزام به داف هست
        elseif ($has_requirement_for_doffs) {
            // وضعیت حامل از خالی به رزرو برای ماشین تغییر می یابد
            $carrier->SetStatus(5320011, $machine->fullCaption(), 5320104, null, $machine->id, null); // رزور برای ماشین

            // اگر وضعیت ماشین نداشتن سفارش است: درحال بافت در غیر این صورت در انتظار بارگذاری
            $production_form_status_id = $machine->production_status_id == 7003017 ? 7002001 : 7002011;
            // ایجاد فرم تولید جدید با وضعیت در انتظار بارگذاری
            $production_form = ProductionForm::AddNewForm($machine->id, $carrier->id, $machineLog->id, $packing_type->id, null, $production_form_status_id);

            $production_form->loading_machine_log_id = $machineLog->id; // قطب شروع در انتظار بارگذاری
            if ($machine->production_status_id == 7003017) {
                $production_form->in_the_weaving_machine_log_id = $machineLog->id; // قطب شروع در حال بافت پارچه
            }
            $production_form->save();


// اضافه کردن یک رکورد در فرم تولید
            foreach ($reserve_allocation->items as $item) {
                ProductionFormItem::AddNewItem(
                    $reserve_allocation->id,
                    $production_form->id,
                    $item->production_id,
                    $item->product_id,
                    $item->band_code,
                    $production_form_status_id,
                    $item->amount_of_each_doffs,
                    $item->version_code??null
                );
                $carrier->addProduct($item->product_id);

            }


            if ($reserve_allocation->has_warps_change) {

                $warps_is_in_machine_warehouse = Warps::warpsExistInMachineWarehouse($reserve_allocation);

                if ($warps_is_in_machine_warehouse) {
                    // چله در انبارک هست و فقط وضعیت ماشین عوض می شود.
                    $machine->setStatus(
                        null,
                        53002,
                        7003044, // در انتظار شروع استخراج چله (جهت تغییر کالیته)
                        1750,
                        "Fabric_Raw"
                    );
                } else {
                    // پیدا کردن کد چله
                    $warps_is_in_warehouse = Warps::warpsExistInWarehouse($reserve_allocation);

                    if ($warps_is_in_warehouse) {
                        // چله در انبار هست
                        $machine->setStatus(
                            null,
                            53002,
                            7003044, // در انتظار شروع استخراج چله (جهت تغییر کالیته)
                            1750,
                            "Fabric_Raw"
                        );

                    } else {
                        // چله در انبار نیست
                        $machine->setStatus(
                            null,
                            53002,
                            7003019, //در انتظار آماده سازی چله
                            1614,
                            "Fabric_Raw"
                        );
                    }

                    // ران شدن مازول درخواست چله
                    $requestChangeWarps = new RequestChangeWarpsController();
                    $requestChangeWarps->request_warps($request, $reserve_allocation, $machine, $result_warps_change,
                        $warps_is_in_warehouse ? 7005001 : 7005003);
                }
            } else {
                $machine->setStatus(
                    null,
                    53002,
                    7003043, // در انتظار شروع تغییر کالیته
                    1740,
                    "Fabric_Raw"
                );

            }
            // الزام به داف نیست
        } // الزام به داف نیست
        elseif (!$has_requirement_for_doffs) {
            // در صورتی که ماشین نداشتن سفارش باشد و الزام به داف نباشد، بنابراین کارت جاری ندارد و نیاز نیست که مقدار پایان آیتم های فرم تولید ست شوند.
            if (isset($current_production_form)) {
                // بروزرسانی مقدار پایان آیتم های فرم تولید در هر باند در صورت الزام به داف نبودن
                // فکر می کنم اینجا باید $current_production_form باشد، در فرم تولید 1886 این مشکل داشت.
                for ($band_code = 1; $band_code < count($current_production_form->items); $band_code++) {
                    //
                    $production_form_item = $current_production_form->items()->where("band_code", $band_code)->orderByDesc("id")->first();
                    if ($production_form_item) {
                        $production_form_item->end_of_machine_log_id = $machineLog->id;
                        $production_form_item->save();
                    }
                }

                // اضافه کردن یک رکورد به فرم تولید جاری
                foreach ($reserve_allocation->items as $item) {
                    ProductionFormItem::AddNewItem(
                        $reserve_allocation->id,
                        $current_production_form->id,
                        $item->production_id,
                        $item->product_id,
                        $item->band_code,
                        7002001, // در حال بافت
                        $item->amount_of_each_doffs,
                        $item->version_code??null
                    );
                    $current_production_form->carrier->addProduct($item->product_id);

                }
            }


            if ($reserve_allocation->has_warps_change) {


                $warps_is_in_machine_warehouse = Warps::warpsExistInMachineWarehouse($reserve_allocation);

                if ($warps_is_in_machine_warehouse) {
                    // چله در انبارک هست و فقط وضعیت ماشین عوض می شود.
                    $machine->setStatus(
                        null,
                        53002,
                        7003044, // در انتظار شروع استخراج چله (جهت تغییر کالیته)
                        1750,
                        "Fabric_Raw"
                    );
                } else {
                    // پیدا کردن کد چله
                    $warps_is_in_warehouse = Warps::warpsExistInWarehouse($reserve_allocation);

                    if ($warps_is_in_warehouse) {
                        // چله در انبار هست
                        $machine->setStatus(
                            null,
                            53002,
                            7003044, // در انتظار شروع استخراج چله (جهت تغییر کالیته)
                            1750,
                            "Fabric_Raw"
                        );

                    } else {
                        // چله در انبار نیست
                        $machine->setStatus(
                            null,
                            53002,
                            7003019, //در انتظار آماده سازی چله
                            1614,
                            "Fabric_Raw"
                        );
                    }

                    // ران شدن ماژول چله
                    // ران شدن مازول درخواست چله
                    $requestChangeWarps = new RequestChangeWarpsController();
                    $requestChangeWarps->request_warps($request, $reserve_allocation, $machine, $result_warps_change,
                        $warps_is_in_warehouse ? 7005001 : 7005003);
                }
            } else {
                $machine->setStatus(
                    null,
                    53002,
                    7003043, // در انتظار شروع تغییر کالیته
                    1740,
                    "Fabric_Raw"
                );

            }

        }
        event(new MachineLogEvent($machine, $machineLog, ("الزام به داف:" . ($has_requirement_for_doffs ? "بله" : "خیر").($reserve_allocation?"- تخصیص رزور".$reserve_allocation->id:""))));


//        // تغییر کانال جاری ماشین
//        if ( $reserve_allocation ) {
//            ProductionChannel::ChangeChannel( $reserve_allocation );
//        }
        // تولید لات پارچه و بروز رسانی متراژ
        FabricRaw:: ChangeLot($allocation);

        // بروز رسانی لیست اولویت ها
        Allocation::updatePriorityNumber($machine);
    }

    public static function has_requirement_for_doffs(Machine $machine, $current_machine_fault_ids = [-1])
    {

        $machine_module_type_id = $machine->machine_type->machine_module_type_id;
        $shear_checklist = MachineModuleType::getChecklist($machine_module_type_id, "shear");
        $completing_checklist = MachineModuleType::getChecklist($machine_module_type_id, "completing");
        $is_takmili_item_id = MachineModuleType::getChecklist($machine_module_type_id, "is_takmili_item_id");

        //  به دست آوردن تخصیص های رزروی که با توجه به عیب های ماشین، نباید بر روی ماشین جاری شوند.
        $result_allocation_fault = Allocation::CheckAllocationFault($machine, null, $machine->ReserveAllocation()->get(), $current_machine_fault_ids);
        $allocation_illegal = isset($result_allocation_fault["allocation_illegal"]) ? $result_allocation_fault["allocation_illegal"] : [];


        /* کنترل متراژ پارچه های بر روی حامل **/

        //         حداکثر مقدار جهت داف (کیلوگرم)
        $machine_max_doffs = MachinePropertyValue::
        where(["machine_type_id" => $machine->machine_type->id, "machine_property_id" => 5])->
        first();
        if (!isset($machine_max_doffs) || $machine_max_doffs->value <= 0) {
            return [
                "result" => false,
                "allocation_illegal" => $allocation_illegal,
                "error" => " حداکثر مقدار جهت داف (کیلوگرم) ثبت نشده است"
            ];

        }
        $machine_max_doffs = $machine_max_doffs->value;


        /** فرم تولید آخرین رکورد فرم تولید جاری ماشین*/
        $production_form = $machine->getCurrentProductionForm("current_production_form_status_with_reserve");
        if (!$production_form) {
            return ["result" => true, "allocation_illegal" => $allocation_illegal, "doffs" => true];
        }

        $reserve_allocation_productive = $machine->getFirstReserveAllocation(1, $allocation_illegal); // گرفتن اولین کارت رزور تولیدی
        /** اگر کارت تولید رزرو موجود نیست نیازی به داف نمی باشد */
        if (!$reserve_allocation_productive) {
            return ["result" => true, "allocation_illegal" => $allocation_illegal, "doffs" => false];
        }

        $production_form_list = [];
        foreach ($production_form->items as $item) {
            $production_form_list[$item->band_code] = $item; // به ازای هر باند، آخرین آیتم باند را در آرایه قرار می دهد.
        }


        // اگر اولین کارت رزرو، کارت نمونه گیری باشد، لازم نیست، الزام به داف چک شود، و الزام به داف خیر است.
        $first_reserve_allocation = $machine->getFirstReserveAllocation(false, $allocation_illegal);
        $first_reserve_allocation_item = $first_reserve_allocation->items()->first();
        if (!$first_reserve_allocation_item) {
            return [
                "result" => false,
                "allocation_illegal" => $allocation_illegal,
                "error" => " اولین آیتم تخصیص رزور یافت نشد، لطفا با پشتیبانی تماس بگیرید."
            ];
        }
        if ($first_reserve_allocation_item->production->production_type_id == 2) {
            return ["result" => true, "allocation_illegal" => $allocation_illegal, "doffs" => false];
        }

        // گرفتن فرم تولید جاری
        $current_production_form = ProductionForm::where("machine_id", $machine->id)->
        whereIn("status_id", [
            7002001, // درحال تکمیل
            7002008 // فرم رزرو
        ])->orderBy("id")->first();
        /**
         * چک شود کیلوگرم کالا بیش از حد مجاز نباشد.
         */
        $sum_weight = EndOfProductionCardTextureController::get_weight_current_production_form($machine, $current_production_form);

        if ($sum_weight > $machine_max_doffs) {
            return ["result" => true, "allocation_illegal" => $allocation_illegal, "doffs" => true];
        }

        // اگر مقدار آیتم های فرم تولید برابر صفر بود، یعنی هیچ کالایی بر روی فرم بافته نشده یا عدم راه اندازی شده است، پس لازم به داف نیست.
        $sum_production_form_item_amount = ProductionFormItemLotNumber::where("production_form_id", $current_production_form->id)->sum("amount");
        if ($sum_production_form_item_amount <= 0) {

            return ["result" => true, "allocation_illegal" => $allocation_illegal, "doffs" => false];
        }
        // به ازای هر باند چک می شود.
        foreach ($reserve_allocation_productive->items as $reserve_allocation_item) {

            if (!isset($production_form_list[$reserve_allocation_item->band_code])) {
                return [
                    "result" => false,
                    "allocation_illegal" => $allocation_illegal,
                    "error" => " فرم تولید جاری برای ماشین یافت نشد."
                ];
            }
            $production_form_item = $production_form_list[$reserve_allocation_item->band_code];


            // اگر کد سفارش های کارت تولید متفاوت باشد
            if ($reserve_allocation_item->production->order_id != $production_form_item->production->order_id) {
                return ["result" => true, "allocation_illegal" => $allocation_illegal, "doffs" => true];
            }
            $has_grading_and_control = Setting::getIntegerValue( "has_grading_and_control" );
            if ( $has_grading_and_control ) {
                // کارخانه دارای واحد کنترل کیفیت می باشد
                return ["result" => true, "allocation_illegal" => $allocation_illegal, "doffs" => false];

            }

            // اگر AC True باشد
            // طرح عوض می شود
            if ($reserve_allocation_productive->has_article_change) {
                return ["result" => true, "allocation_illegal" => $allocation_illegal, "doffs" => true];
            }

            /** بررسی شییر */

            // شییر کالای رزور
            $reserve_shear_value_product = GoodsKindPropertyValue::
            where("product_id", $reserve_allocation_item->product_id)->
            whereIn("goods_kind_property_id", $shear_checklist)->
            pluck("value", "goods_kind_property_id")->toArray();
            if (count($reserve_shear_value_product) == 0) {
                return [
                    "result" => false,
                    "allocation_illegal" => $allocation_illegal,
                    "error" => "مشخصه شییر برای کالای " . $reserve_allocation_item->product->fullCaption() . " ثبت نشده است."
                ];
            }


            // شییر کلای فرم تولید
            $production_form_shear_value_product = GoodsKindPropertyValue::
            where("product_id", $production_form_item->product_id)->
            whereIn("goods_kind_property_id", $shear_checklist)->
            pluck("value", "goods_kind_property_id")->toArray();
            if (count($production_form_shear_value_product) == 0) {
                return [
                    "result" => false,
                    "allocation_illegal" => $allocation_illegal,
                    "error" => "مشخصه شییر برای کالای " . $production_form_item->product->fullCaption() . " ثبت نشده است."
                ];
            }

            /** اگر مشخص شیر برای دو کالا متفاوت باشد */
            if (count(array_diff($reserve_shear_value_product, $production_form_shear_value_product)) > 0) {
                return ["result" => true, "allocation_illegal" => $allocation_illegal, "doffs" => true];
            }


            /** بررسی  نوع پروسه تکمیل*/

// نوع پروسه تکمیل کالای رزور
            $reserve_completing_value_product = GoodsKindPropertyValue::
            where("product_id", $reserve_allocation_item->product_id)->
            whereIn("goods_kind_property_id", $completing_checklist)->
            pluck("value", "goods_kind_property_id")->toArray();
            if (count($reserve_completing_value_product) == 0) {
                return [
                    "result" => false,
                    "allocation_illegal" => $allocation_illegal,
                    "error" => "مشخصه نوع پروسه تکمیل برای کالای کارت رزور ثبت نشده است."
                ];
            }


            // نوع پروسه تکمیل کلای فرم تولید
            $production_form_completing_value_product = GoodsKindPropertyValue::
            where("product_id", $production_form_item->product_id)->
            whereIn("goods_kind_property_id", $completing_checklist)->
            pluck("value", "goods_kind_property_id")->toArray();
            if (count($production_form_completing_value_product) == 0) {
                return [
                    "result" => false,
                    "allocation_illegal" => $allocation_illegal,
                    "error" => "مشخصه نوع پروسه تکمیل برای کالای فرم تولید ثبت نشده است."
                ];
            }

            /** اگر هر دو تکمیلی باشند، الزام به داف نیست */
            if (in_array($is_takmili_item_id, $reserve_completing_value_product) &&
                in_array($is_takmili_item_id, $production_form_completing_value_product)
            ) {
                return ["result" => true, "allocation_illegal" => $allocation_illegal, "doffs" => false];
            }


        }

        return ["result" => true, "allocation_illegal" => $allocation_illegal, "doffs" => true];
    }
//
//    public static function has_requirement_for_doffs_update(Machine $machine,$has_requirement_for_doffs){
//        // اگر فرم تولید جاری وجود دارد و تخصیص آخرین رکورد آن کنسل شده و الزم به داف شده است، الزام به داف را کنسل می کنیم.
//        if($has_requirement_for_doffs) {
//            $current_production = ProductionForm::where( [
//                "machine_id" => $machine->id
//            ] )->whereIn(
//                "status_id", [
//                7002001, // در حال بافت
//            ] )->first();
//            if ( $current_production ) {
//                $current_production_item = $current_production->items()->where( "band_code", 1 )->orderByDesc( "id" )->first();
//
//                if ( $current_production_item->allocation && $current_production_item->allocation->status_id == 5310030 ) {
//                    $has_requirement_for_doffs = false;
//                }
//            }
//        }
//        return $has_requirement_for_doffs;
//    }

    public static function get_weight_current_production_form(Machine $machine, ProductionForm $production_form)
    {


        if (!$production_form) {
            return 1000000000; // فرم پیدا نشد، یک جایی غیر منطقی است.
        }

        $sum_weight = 0;
        foreach ($production_form->items as $item) {
// مقدار پیش بینی * وزن یک واحد
            $sum_weight += $item->forecast_amount * $item->product->weight;
        }

        if ($machine->getFirstReserveAllocation() != null) {
            // متراژ داف تخصیص جاری
            $sum_weight += $machine->getFirstReserveAllocation()->items()->sum("amount_of_each_doffs") * $machine->getFirstReserveAllocation()->items()->first()->product->weight;
        }

        return $sum_weight;

    }

    public function checkPermission(Machine $machine)
    {
        $dc = new DashboardController();

        $result = $dc->checkPermissionConditions($machine, EndOfProductionCardTextureController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

    public static function turnOffMachineNotification(Machine $machine)
    {
        //ارسال پیامک برای ناظر ها
        $supervisor = Post::where("is_system_supervisor", 1)->get();

        foreach ($supervisor as $item) {

            foreach ($item->worker as $super_worker) {

                Notification::send(
                    "00" . ($super_worker->mobile_country->area_code ?? "98") . $super_worker->mobile,
                    new SMSNotification("supervisoralertmachineoff", $machine->getCode(), null, null, $super_worker->fullname(), $machine->caption));

            }

        }
    }

    public static function HasSpecialLicense(Allocation $allocation)
    {

        $special_license = SpecialLicense::where([
            "special_license_type_id" => 2,
            "reference_id" => $allocation->id,
            "status_id" => 6040002, // تایید شده
        ])->first();
        if (!$special_license) {
            return false;
        } else {
            return true;
        }
    }
}

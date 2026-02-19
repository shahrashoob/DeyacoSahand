<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine;

use App\Events\Contractor\ContractorLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\ProductionCard\MachineAllocationController;
use App\Http\Controllers\GoodsKindProcess\General\ProductionCard\GeneralMachineAllocationController;
use App\Models\Form\Form;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypePropertyValue;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Post\PostStatus;
use App\Models\Production\ProductionFormItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller
{
    // goods_kind_process/fabric/finishing_machine/machine/dashboard/
    public static $perfix_production_status_code = "7303";
    public static $info = [
        "route" => "fabric.finishing_machine.machine.dashboard."
    ];
    var $view_path = "goods_kind_process.fabric.finishing_machine.machine.dashboard.";
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

        // چگ کردن اینکه تمامی مشخصات ماژول ماشین به درستی تعریف شده است.
        $result = MachineModuleTypePropertyValue::getValues($machine->machine_type);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }


        // آیا ماژول فرم تولید در ماشین فعال است.
        $value_202 = MachineModuleTypePropertyValue::getValue("73030011202", $machine->machine_type_id);


        $controller_info = DashboardController::get_controller_info("view");
        $special_condition = DashboardController::enable_special_condition($machine, $value_202);

        $allocation = $machine->getCurrentAllocation();
        $machine_allocation = null;
        $start_to_start_line_product_station = null;
        $current_input_list = [];

        $reserve_allocation = $machine->ReserveAllocation()->get();
        $reserve_allocation_list = $reserve_allocation_list = GeneralMachineAllocationController::GetReserveAllocationList($reserve_allocation);


        $productionFromItemLot = null;
        if ($allocation) {
            $productionFromItemLot = []; // Fabric::getCurrentLot( $allocation );

            // اگر به هر دلیل لات تولید نشده بود، دوباره لات را ایجاد می کند.
//            if ( count( $productionFromItemLot ) == 0 ) {
//                Fabric::ChangeLot( $allocation );
//                $productionFromItemLot = FabricRaw::getCurrentLot( $allocation );
//            }

            // آیتمی که تخصیص جاری است.
            $machine_allocation = $allocation->items()->
            where("status_id", 5310010)-> // تخصیص جاری
            first();

            $current_input_list = CurrentMachineInput::
            where([
                "machine_id" => $machine->id,
                "allocation_id" => ($allocation->id ?? -1),
                "production_id" => $machine_allocation->production_id ?? -1
            ])->
            orderBy("goods_kind_id")->
            orderBy("input_line_code")->
            get();


            //چک کردن اینکه نوع پیش نیازی شروع عملیات ماشین در مسیر محصول بعدی STS است یا خیر
            // اگر STS است باید بعد از مدت مشخصی عملیات شروع ماشین بعدی استارت شود.
            // مثلا بعد از سفت پیچی باید میز بسته بندی استارت شود.
            if ($machine_allocation) {
                // return $machine_allocation->line_product_station;
                $start_to_start_line_product_station = self::GetStartToStartLineProductStation($machine_allocation);

            }
        }


        $production_form = $machine->getCurrentProductionForm();


        $production_form_carrier_code = $production_form->carrier->code ?? "---";
        $production_form_reserve_carrier_code = $production_form_reserve->carrier->code ?? null;


        $form_list = Form::where([
            "applicant_type_id" => 10, // ماشین
            "applicant_id" => $machine->id
        ])->paginate(10);


//     return   MachineAllocationController::UpdateMaterialFlowV209_3($allocation, $machine, ProductionFormItem::find(136));
        //  return self::GetNextStatus($allocation, 7303902, 7303901,false);
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
            "value_202",
            "machine_allocation",
            "start_to_start_line_product_station"

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

    public static function enable_special_condition(Machine $machine, $value_202)
    {

        $result = [];
        // $result["01"] = false;

        // فرم در انتظار تایید انبارک
        $product_request_form_count = ProductRequestForm::where([
            "applicant_type_id" => 40,
            "applicant_id" => $machine->warehouse_id,
            "status_id" => 7005004,// در انتظار تایید برگ خروج
        ])->count();
        $result["93"] = $product_request_form_count > 0;
        $result["94"] = $product_request_form_count > 0;
        $result["06"] = 0; // به صورت دس
        $result["07"] = $value_202;
        return $result;


    }


    public static function get_controller_info($type = "")
    {
        $controller_info = [
            "01" => StartSetupController::$info,
            "02" => StartOperationController::$info,
            "03" => EndOfOperationController::$info,
            "04" => MakeFinalSettingController::$info,
//            "05" => ConfirmQualityControlController::$info,
            "06" => InjectionOfMaterialController::$info,
            "07" => RegisterProductionController::$info,
            "08" => StartTestingController::$info,
            "09" => EndOfTestingController::$info,
            "10" => ConfirmTestingController::$info,
            "11" => EndOfMachineAllocationController::$info,
            "12" => MachineCardController::$info,

//            ""=>FinishedAllocationController::$info,
            "91" => RequestRawMaterialController::$info,
            "92" => OperatorController::$info,
            "93" => MaterialDeliveryConfirmationController::$info,
            "94" => MaterialDeliveryRejectController::$info,
            "95" => MaterialReturnToWarehouseController::$info,
            "96" => MaterialReturnToWarehouseLogController::$info,
//            "97" => AllocationCardController::$info,
            "99" => LogController::$info,
        ];

        return $controller_info;
    }

    // گرفتن اولین وضعیت بعدی تخصیص
    public static function GetNextStatus(
        Machine    $machine,
        Allocation $allocation,
                   $current_status_id,
                   $event_id,
                   $allow_action,
                   $check_status_id = [5310010],
                   $change_reserve_to_current_allocation = false,
                   $current_station_sub_operation_id = null,
                   $number_of_loop = 0
    )
    {
      //  echo "start" . "<br/>";
        // گرفتن اطلاعات تخصیص
        $machine_allocation_data = Allocation\AllocationData::getData(400, $allocation->id);
        // عملیات بعدی زا از روی مسیر محصول اولین آیتم تخصیص تشخیص می دهیم.

        $list = $allocation->
        items()->
        whereIn("status_id", $check_status_id)-> // تخصیص جاری
        with("line_product_station")->
        get();
        $is_batch_operation = -1;
        foreach ($list as $machine_allocation) {
          //  echo $machine_allocation->id . "-" . $current_status_id . "<br/>";
            $line_product_station = $machine_allocation->line_product_station;

            // اولین عملیات فرعی را می گیریم و برای لاگ ماشین استفاده می کنیم.
            if (!$current_station_sub_operation_id) {
                $current_station_sub_operation_id = $line_product_station->station_sub_operation_id;
            }
            //آیا عملیات بچ است یا خیر، اگر عملیات بچ است، نیاز به نگاه کردن به تنظیمات نمی باشد.
            $is_batch_operation = $machine_allocation->line_product_station->station_operation->station_operation_type_id == 1;
            if (isset($machine_allocation_data["is_force_batch"])) {
                $is_batch_operation = true;
            }
            if (!$line_product_station) {
                return [
                    "result" => false,
                    "error" => "مسیر محصول برای تخصیص یافت نشده و یا از مسیر محصول های " .
                        $machine_allocation->product->caption .
                        " حذف گردید است.، لطفا با پشیتیبانی تماس بگیرید."
                ];
            }

            switch ($current_status_id) {
                case 0: // اولین عملیات

                    // در مواردی که تخصیص تخصیص رزرو است و انتظار داریم که با شروع اولین عملیات تخصیص از رزرو به جاری انتقال یابد.
                    if ($change_reserve_to_current_allocation && $allow_action) {
                        $allocation->status_id = 5310010;
                        $allocation->save();

                        // آیا ماژول ثبت تولید در ماشین فعال است.
                        $value_202 = MachineModuleTypePropertyValue::getValue("73030011202", $machine->machine_type_id);
                        $number_allocation = 0;
                        foreach ($allocation->items as $item) {

                            $number_allocation++;
                            if ($number_allocation != 1 && $value_202) {
                                // اگر مازول تولید در کالا فعال است، پس فقط اولین آیتم آن جاری می شود و مابعی بعد از ثبت تولید فعال می شوند.
                                // این برای تنظیمات تلاش رنگ در دوره پیاده سازی اضافه گردید.
                                $item->status_id = 5310040; // تخصیص رزور
                            } else {
                                $item->status_id = 5310010; // تخصیص جاری
                            }
                            $item->save();

                        }

                    }

                    if ($line_product_station->product->have_testing_before_production &&
                        $line_product_station->product->testing_is_on_line_production ==
                        $line_product_station->priority_number
                    ) {
                        return [
                            "result" => true,
                            "status_id" => 7303906, // در انتظار شروع تست
                            "on_status_id" => 53002, // خاموش
                            "machine_off_reason_id" => 5906, // انتظار برای شروع تست
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }
                    if ($line_product_station->is_need_start_setup
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_start_setup'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_start_setup']
                            )
                        )
                    ) {
                        return [
                            "result" => true,
                            "status_id" => 7303901, // در انتظار شروع ستاب
                            "on_status_id" => 53002, // خاموش
                            "machine_off_reason_id" => 5901, // انتظار برای انجام ستاپ
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }

                    if ($line_product_station->is_need_start_of_operation
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_start_of_operation'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_start_of_operation']
                            )
                        )
                    ) {

                        return [
                            "result" => true,
                            "status_id" => 7303902, // در انتظار شروع عملیات
                            "on_status_id" => 53002, // خاموش
                            "machine_off_reason_id" => 5902, // انتظار برای شروع عملیات
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }
                    if ($line_product_station->is_need_end_of_operation
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_end_of_operation'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_end_of_operation']
                            )
                        )
                    ) {

                        return [
                            "result" => true,
                            "status_id" => 7303903, // در انتظار پایان عملیات
                            "on_status_id" => 53001, // روشن
                            "machine_off_reason_id" => null,
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }
                    if ($line_product_station->is_need_final_setting
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_final_setting'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_final_setting']
                            )
                        )
                    ) {

                        return [
                            "result" => true,
                            "status_id" => 7303904, // در انتظار انجام تنظیمات نهایی
                            "on_status_id" => 53002, // خاموش
                            "machine_off_reason_id" => 5904, //در انتظار تنظیمات نهایی
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }
//                    if ($line_product_station->is_need_for_quality_control
//                        && (
//                            $is_batch_operation
//                            ||
//                            (
//                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_for_quality_control'])
//                                &&
//                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_for_quality_control']
//                            )
//                        )
//                    ) {
//
//                        return [
//                            "result" => true,
//                            "status_id" => 7303905,  // در انتظار تایید کنترل کیفیت
//                            "on_status_id" => 53002, // خاموش
//                            "machine_off_reason_id" => 5905, //انتظار برای تایید کنترل کیفیت
//                            "current_station_sub_operation_id" => $current_station_sub_operation_id
//                        ];
//                    }
                    break;
                case 7303901: // شروع ستاب

                    if ($line_product_station->is_need_start_of_operation
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_start_of_operation'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_start_of_operation']
                            )
                        )
                    ) {

                        return [
                            "result" => true,
                            "status_id" => 7303902, // در انتظار شروع عملیات
                            "on_status_id" => 53002, // خاموش
                            "machine_off_reason_id" => 5902, // انتظار برای شروع عملیات
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }

                    if ($line_product_station->is_need_end_of_operation
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_end_of_operation'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_end_of_operation']
                            )
                        )
                    ) {

                        return [
                            "result" => true,
                            "status_id" => 7303903, // در انتظار پایان عملیات
                            "on_status_id" => 53001, // روشن
                            "machine_off_reason_id" => null,
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }
                    if ($line_product_station->is_need_final_setting
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_final_setting'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_final_setting']
                            )
                        )
                    ) {
                        return [
                            "result" => true,
                            "status_id" => 7303904, // در انتظار انجام تنظیمات نهایی
                            "on_status_id" => 53002, // خاموش
                            "machine_off_reason_id" => 5904, //در انتظار تنظیمات نهایی
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }


                    break;

                case 7303902: // شروع عملیات

                    if ($line_product_station->is_need_end_of_operation
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_end_of_operation'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_end_of_operation']
                            )
                        )
                    ) {
                        return [
                            "result" => true,
                            "status_id" => 7303903, // در انتظار پایان عملیات
                            "on_status_id" => 53001, // روشن
                            "machine_off_reason_id" => null,
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }

                    if ($line_product_station->is_need_final_setting
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_final_setting'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_final_setting']
                            )
                        )
                    ) {
                        return [
                            "result" => true,
                            "status_id" => 7303904, // در انتظار انجام تنظیمات نهایی
                            "on_status_id" => 53002, // خاموش
                            "machine_off_reason_id" => 5904, //در انتظار تنظیمات نهایی
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }


                    break;

                case 7303903: // پایان عملیات

                 //   echo "is_need_final_setting:" . $line_product_station->is_need_final_setting .
                   //     "is_batch_operation:$is_batch_operation" . "<br/>";

                    if ($line_product_station->is_need_final_setting
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_final_setting'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_final_setting']
                            )
                        )
                    ) {
                        return [
                            "result" => true,
                            "status_id" => 7303904, // در انتظار انجام تنظیمات نهایی
                            "on_status_id" => 53002, // خاموش
                            "machine_off_reason_id" => 5904, //در انتظار تنظیمات نهایی
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }


                    break;

                case 7303904: // تنظیمات نهایی

//                    if ($line_product_station->is_need_for_quality_control
//                        && (
//                            $is_batch_operation
//                            ||
//                            (
//                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_for_quality_control'])
//                                &&
//                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_for_quality_control']
//                            )
//                        )
//                    ) {
//
//                        return [
//                            "result" => true,
//                            "status_id" => 7303905,  // در انتظار تایید کنترل کیفیت
//                            "on_status_id" => 53002, // خاموش
//                            "machine_off_reason_id" => 5905, //انتظار برای تایید کنترل کیفیت
//                            "current_station_sub_operation_id" => $current_station_sub_operation_id
//                        ];
//                    }


                    break;
                case 7303906: // در انتظار شروع تست کالا
                    return [
                        "result" => true,
                        "status_id" => 7303907,  // در انتظار پایان  تست کالا
                        "on_status_id" => 53001, // روشن
                        "machine_off_reason_id" => null,
                        "current_station_sub_operation_id" => $current_station_sub_operation_id
                    ];
                    break;
                case 7303907: // در انتظار پایان تست کالا

                    return [
                        "result" => true,
                        "status_id" => 7303908,  // در انتظار تایید تست
                        "on_status_id" => 53005, // خاموش
                        "machine_off_reason_id" => 5907, // در انتظار تایید تست
                        "current_station_sub_operation_id" => $current_station_sub_operation_id
                    ];
                    break;
                case 7303908: // بررسی تست
                    if ($event_id == 5310908) { // تست تایید است
                        if ($line_product_station->is_need_start_setup) {
                            return [
                                "result" => true,
                                "status_id" => 7303901,  // در انتظار شروع ستاپ
                                "on_status_id" => 53002, // خاموش
                                "machine_off_reason_id" => 5901, // انتظار برای شروع ستاپ
                                "current_station_sub_operation_id" => $current_station_sub_operation_id
                            ];
                        } else {
                            return [
                                "result" => true,
                                "status_id" => 7303902,  // در انتظار شروع عملیات
                                "on_status_id" => 53002, // خاموش
                                "machine_off_reason_id" => 5902, // انتظار برای شروع عملیات
                                "current_station_sub_operation_id" => $current_station_sub_operation_id
                            ];
                        }

                    } elseif ($event_id == 5310909) { // تست تایید نیست
                        return [
                            "result" => true,
                            "status_id" => 7303906,  // در انتظار شروع تست کالا
                            "on_status_id" => 53001, // روشن
                            "machine_off_reason_id" => null,
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    } else {
                        1 / 0; // وضعیت نامشخص
                    }
                    break;
            }


            if ($allow_action) {

                if (!$is_batch_operation) {
                    // عملیات پیوسته است.

                    $machine_allocation->status_id = 5310060; //در انتظار شروع عملیات بعدی (در عملیات های پیوسته)
                    $machine_allocation->save();

                    // ردیف دیگری هست که جاری باشد.
                    $next_machine_allocation_in_continuous = MachineAllocation::
                    where([
                        "allocation_id" => $allocation->id,
                        "status_id" => 5310010, // تخصیص جاری
                    ])->
                    first();


                    // باید برود آیتم های ماشین بعدی را اجرا کند.
                    if ($next_machine_allocation_in_continuous) {

                        return self::GetNextStatus($machine, $allocation, 0, $event_id, $allow_action, $check_status_id, $change_reserve_to_current_allocation, $current_station_sub_operation_id);
                    }


                }
                // تمام عملیات های آیتم تخیصیص انجام شده است، در صورت نیاز باید تخصیص را بگذاریم
                // در انتظار تخصیص مجدد و یا آن ردیف را خاتمه یافته کرده
                $next_line_product_station = self::GetNextLineProductStation($line_product_station);
                if ($next_line_product_station) {

                    if (!$is_batch_operation) {

                        // یک عملیات بر روی کل آیتم ها انجام شده است، پست دوباره همه می گذاریم، جاری و عملیات بعدی را از صفر شروع می کنیم.
                        MachineAllocation::
                        where([
                            "allocation_id" => $allocation->id,
                            "status_id" => 5310060
                        ])->
                        update(["status_id" => 5310010]);

                        $first_line_product_station_x = null;
                        // عملیات بعدی اولین آیتم را یکی افزایش می دهیم.
                        foreach ($allocation->items as $machine_allocation_666) {
                            $line_product_station = $machine_allocation_666->line_product_station;
                            if (!$first_line_product_station_x) {
                                $first_line_product_station_x = $line_product_station;
                            }
                            $next_line_product_station = self::GetNextLineProductStation($line_product_station);
                            // میریم روی خط بعدی
                            $machine_allocation_666->line_product_station_id = $next_line_product_station->id;
                            $machine_allocation_666->save();

                        }

                        $next_machine_allocation_in_batch = $allocation->items()->first();
// اگر نیاز به تخصیص است، پس خاتمه یافته می شود.
                        if ($next_machine_allocation_in_batch->line_product_station->is_need_allocation_at_first) {

                            //گام بعدی روی همین ماشین وجود دارد ولی چون نیاز به تخصیص وجود دارد، به مرحله بعد نمی رویم.
                            self::TerminateMachineAllocation($allocation, $next_machine_allocation_in_batch, $event_id, $is_batch_operation);
                            // یا باید اولین عملیات تخصیص رزرو بعدی را جاری کند و یا ماشین خاموش می شود.
//                            echo "685"."<br/>";
                            return self::GetNextStatusAfterTerminateOperation($machine, $event_id, $allow_action, $current_station_sub_operation_id, 0, null);

                        } else {
//                            echo "689"."<br/>";
                            return self::GetNextStatus($machine, $allocation, 0, $event_id, $allow_action, $check_status_id, $change_reserve_to_current_allocation, $current_station_sub_operation_id);
                        }
                    }

                    // میریم روی خط بعدی
                    $machine_allocation->line_product_station_id = $next_line_product_station->id;
                    $machine_allocation->save();
                    // باید عملیات را عوض کنیم، اگر عملیات بعدی نیاز به تخصیص داشت، دیگر ادامه نمی دهیم.
                    if ($next_line_product_station->is_need_allocation_at_first) {

                        //گام بعدی روی همین ماشین وجود دارد ولی چون نیاز به تخصیص وجود دارد، به مرحله بعد نمی رویم.
                        self::TerminateMachineAllocation($allocation, $machine_allocation, $event_id, $is_batch_operation);
                        // یا باید اولین عملیات تخصیص رزرو بعدی را جاری کند و یا ماشین خاموش می شود.
//                        echo "703"."<br/>";
                        return self::GetNextStatusAfterTerminateOperation($machine, $event_id, $allow_action, $current_station_sub_operation_id, 0, null);

                    }

//                    echo "708"."<br/>";
                    //یک عملیات فرعی انجام شد و یاید برویم روی عملیات فرعی بعدی
                    return self::GetNextStatus($machine, $allocation, 0, $event_id, $allow_action, $check_status_id, $change_reserve_to_current_allocation, $current_station_sub_operation_id);

                } else { // گام بعدی ندارد و باید برویم روی ماشین بعدی
                    self::TerminateMachineAllocation($allocation, $machine_allocation, $event_id, $is_batch_operation);
                }
            }

            $current_status_id = 0; // یعنی اگر دو آیتم وجود داشت، و عملیات های اولین آیتم تمام شده بود باید برورد عملیات های آیتم دومی را چک کند.

        }

        if ($number_of_loop > 10) {
//            return [
//                "result" => true,
//                "status_id" => 7303902, // در انتظار شروع عملیات
//                "on_status_id" => 53002, // خاموش
//                "machine_off_reason_id" => 5902, // انتظار برای شروع عملیات
//                "current_station_sub_operation_id" => $current_station_sub_operation_id
//            ];
            return [
                "result" => false,
                "error" => "تعداد تلاش برای مشخص کردن وضعیت بعدی کارت بیش از حد مجاز می باشد، لطفا با واحد پشتیبانی تماس بگیرد."
            ];
        }
//        echo "720"."<br/>";
        // یا باید اولین عملیات تخصیص رزرو بعدی را جاری کند و یا ماشین خاموش می شود.
        return self::GetNextStatusAfterTerminateOperation($machine, $event_id, $allow_action, $current_station_sub_operation_id, $number_of_loop, $allocation);

    }

    public static function GetNextStatusAfterTerminateOperation(
        Machine $machine,
                $event_id,
                $allow_action,
                $current_station_sub_operation_id = null,
                $number_of_loop = 0,
                $befor_allocation
    )
    {

//        $first_reserve_allocation = $machine->ReserveAllocation(false, null, "Asc", null)->
//        when($befor_allocation && $befor_allocation->status_id == 5310040,function ($query) use($befor_allocation){
//            return $query->where("priority_number",">",$befor_allocation->priority_number);
//        })->
//        first();

        $first_reserve_allocation = $machine->getFirstReserveAllocation();
       // echo "<br/>first_reserve_allocation:" . $first_reserve_allocation->id;
        if (!$first_reserve_allocation) {
            return [
                "result" => true,
                "status_id" => 7303001,  // نداشتن سفارش
                "on_status_id" => 53002, // خاموش
                "machine_off_reason_id" => 1615, // نداشتن سفارش
                "current_station_sub_operation_id" => $current_station_sub_operation_id
            ];
        } else {

            // اگر بعد از انجام عملیات تخصیص خاتمه یافته می شود، باید بتواند عملیات بعدی را اجرا کند.
            return self::GetNextStatus($machine, $first_reserve_allocation, 0, $event_id, $allow_action, [5310040], true, $current_station_sub_operation_id, ++$number_of_loop);
        }
    }

    public static function TerminateMachineAllocation(Allocation $allocation, MachineAllocation $machineAllocation, $event_id_900, $is_batch_operation)
    {

        // در عملیات بچ همه را خاتمه یافته می کنیم.
        foreach ($allocation->items as $item) {
            $item->status_id = 5310020;
            $item->save();
            self::SetNextLineProductStationByNewMachineType($item, $event_id_900);
        }


//        $list_new = MachineAllocation::
//        where([
//            "allocation_id" => $allocation->id,
//            "status_id" => 5310010 // تخصی جاری
//        ])->
//        count();
//        if ($list_new == 0 || $is_batch_operation) { // در عملیات بچ همه کارت ها با هم خاتمه یافته می شوند.

        // فرم تولید را خاتمه یافته می کنیم. اگر تنظیمات برابر 1 باشد.
        //آیا فرم تولید با تزریق مواد اولیه ایجاد می شود.
        $current_production_form = $allocation->machine->getCurrentProductionForm();

        // اولین خط محصول برای گروه ماشین بعدی
        $new_line_product_station = LineProductStation::where([
            "product_id" => $machineAllocation->product_id,
            "product_route_id" => $machineAllocation->line_product_station->product_route_id,
            "status_id" => 1200,
            //پیش فرض ETS است .
            "line_product_start_status_id" => 3410002
        ])->
        // اولویت بالاتر وجود داشته باشد.
        where("priority_number", ">", $machineAllocation->line_product_station->priority_number)->
        // از همین گروه ماشین نیست.
        where("machine_type_id", "!=", $allocation->machine->machine_type_id)->
        // باید عملیات هم فرق داشته باشد
        orderBy("priority_number")->
        first();

        if ($current_production_form && $new_line_product_station) {

            $value_201 = MachineModuleTypePropertyValue::getValue("73030011201", $new_line_product_station->machine_type_id);
            if ($value_201 == 1) {
                $current_production_form->status_id = 7302002; // خاتمه یافته

            } else {
                // مقدار متغیر برای ماشین جدید صفر است.
                $current_production_form->status_id = 7302003; // استخراج شده در انتظار تزریق به ماشین
            }
            $current_production_form->save();

            ProductionFormItem::where("production_form_id", $current_production_form->id)->
            update(["status_id" => $current_production_form->status_id]);

            event(new  ProductionFormLogEvent(
                $current_production_form,
                $event_id_900
            ));
        }

        $allocation->status_id = 5310020; //  خاتمه یافته
        $allocation->save();


//        }
    }

    public static function GetNextLineProductStation(LineProductStation $line_product_station)
    {
        return LineProductStation::where([
            "product_id" => $line_product_station->product_id,
            "machine_type_id" => $line_product_station->machine_type_id,
            "product_route_id" => $line_product_station->product_route_id,
        ])->
        where("priority_number", ">", $line_product_station->priority_number)->
        orderBy("priority_number")->first();
    }

    public static function GetStartToStartLineProductStation(MachineAllocation $machine_allocation)
    {
        return LineProductStation::where([
            "product_id" => $machine_allocation->product_id,
            "product_route_id" => $machine_allocation->line_product_station->product_route_id,
            "status_id" => 1200,
            //پیش فرض ETS است .
            "line_product_start_status_id" => 3410002
        ])->
        // اولویت بالاتر وجود داشته باشد.
        where("priority_number", $machine_allocation->line_product_station->priority_number + 1)->
        // باید عملیات هم فرق داشته باشد
        where("station_operation_id", "!=", $machine_allocation->line_product_station->station_operation_id)->
        orderBy("priority_number")->
        first();;
    }

    public static function SetNextLineProductStationByNewMachineType(MachineAllocation $machineAllocation, $event_id_900)
    {
        $new_line_product_station = LineProductStation::where([
            "product_id" => $machineAllocation->product_id,
            "product_route_id" => $machineAllocation->line_product_station->product_route_id,
            "status_id" => 1200,
            //پیش فرض ETS است .
            "line_product_start_status_id" => 3410001
        ])->
        // اولویت بالاتر وجود داشته باشد.
        where("priority_number", ">", $machineAllocation->line_product_station->priority_number)->
        // باید عملیات هم فرق داشته باشد
        where("station_operation_id", "!=", $machineAllocation->line_product_station->station_operation_id)->
        orderBy("priority_number")->
        first();
        // یک مسیر محصول از تخصیص باقی مانده است و باید مسیر را تخصیص بدهیم.
        if ($new_line_product_station) {


            event(new ContractorLogEvent(
                null,
                $event_id_900, //
                $machineAllocation->production,
                $machineAllocation
            ));

            // یک تخصیص در انتظار تخصیص مجدد
            $new_machine_allocation = MachineAllocation::create($machineAllocation->toArray());
            $new_machine_allocation->allocation_id = -1;
            $new_machine_allocation->status_id = 5310050; // در انتظار تخصیص مجدد
            $new_machine_allocation->parent_allocation_id = $machineAllocation->allocation_id;
            $new_machine_allocation->machine_id = null;
            $new_machine_allocation->created_at = now();
            $new_machine_allocation->updated_at = now();

            $new_machine_allocation->max_number_of_doffs = 1;
            $new_machine_allocation->amount_of_each_doffs = $machineAllocation->allocation_amount;
            $new_machine_allocation->number_of_doffs_done = 0;

            $new_machine_allocation->line_product_station_id = $new_line_product_station->id;
            $new_machine_allocation->save();


            event(new ContractorLogEvent(
                null,
                5310502 //ایجاد تخصیص مجدد
                ,
                $new_machine_allocation->production,
                $new_machine_allocation
            ));
        }

        return $new_line_product_station;
    }
}

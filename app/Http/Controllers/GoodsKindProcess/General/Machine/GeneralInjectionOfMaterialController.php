<?php

namespace App\Http\Controllers\GoodsKindProcess\General\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationMaterialConsumed;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\CurrentMachineInputLog;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class GeneralInjectionOfMaterialController extends Controller
{
// تزریق مواد اولیه
    var $view_path = "goods_kind_process.general.machine.injection_of_material.";
    var $route_path;
    var $dashboard_route;

    public function __construct()
    {

    }

    public function index(
        Machine $machine,
                $allowing_raw_materials_to_be_injected_manually = 1,
                $goods_kind_id = null,
                $allow_get_contour = true,
                $other_allocation = null,
                $replacement_status_id = 3359001 // نیاز به تعویض مشخص نشده
    )
    {


        $allocation = $machine->getCurrentAllocation();
        if ($other_allocation) {
            $allocation = $other_allocation;
        }

        if (!$allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین یافت نشد.");
        }


        // ارسال درخواست برای کدام رسته های کالایی فعال است.
        $goods_kind_ids = MachineTypeInputBandGoodsKind:: getGoodsKindIdsWhereRequestFromRobot($machine, null,
            $allowing_raw_materials_to_be_injected_manually, $goods_kind_id
        );

        $current_input_list = CurrentMachineInput::
        where(["machine_id" => $machine->id, "allocation_id" => ($allocation->id ?? -1)])->
        whereIn("goods_kind_id", $goods_kind_ids)->
        where("replacement_status_id", $replacement_status_id)->
        orderBy("goods_kind_id")->
        orderBy("input_line_code")->
        groupBy("input_line_code","material_id")->
        get();

        if (count($current_input_list) == 0) {
            return back()->withErrors("هیچکدام از ورودی های ماشین جهت تزریق مواد اولیه فعال نیست.");
        }

        $shift_work_option = Option::get("shift_work");

        $route_path = $this->route_path;
        $dashboard_route = $this->dashboard_route;

        return view($this->view_path . "index", compact("current_input_list", "machine", "shift_work_option", "route_path", "dashboard_route", "allow_get_contour"));


    }

    public function submit(Request $request, Machine $machine, $checkContour = true)
    {

        $allocation = $machine->getCurrentAllocation();
        if (!$allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین یافت نشد.");
        }
        $current_production = $machine->getCurrentProductionForm();

        // دریافت قطب ها
        $message = "";
        $last_row_log = MachineLog::getLastLogWithContour($machine);
        if ($checkContour) {
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
        }

        if ($message != "") {
            return back()->withErrors($message);
        }

        // لاگ ماشین
        $new_machine_log = new MachineLog();
        $new_machine_log->machine_event_type_id = 691; // تزریق مواد اولیه
        if ($checkContour) {
            $new_machine_log->contour_1_value = $request->contour_1_value * $contour_result["ratio"];
            $new_machine_log->contour_2_value = $request->contour_2_value * $contour_result["ratio"];
            $new_machine_log->contour_3_value = $request->contour_3_value * $contour_result["ratio"];
            $new_machine_log->contour_4_value = $request->contour_4_value * $contour_result["ratio"];
            $new_machine_log->contour_5_value = $request->contour_5_value * $contour_result["ratio"];
            $new_machine_log->shift_work_id = $request->shift_work_id;
        }

        $result = self::SetInjectionMaterial($request, $machine, $allocation, 0, $new_machine_log, $current_production);
        if ($result["result"]) {

            $allocation = $machine->getCurrentAllocation();
            FabricRaw:: ChangeLot($allocation);

            $production_form = $machine->getCurrentProductionForm();
            if ($production_form) {
                // بروزرسانی مقادیر فرم تولید
                $last_machine_log = MachineLog::getLastLogWithContour($machine);
                ProductionForm::UpdateAmountWithLastContour($production_form, $last_machine_log);
            }


            // ثبت مقدار مصرف
            MachineAllocationMaterialConsumed::registerNewConsumed($allocation, $machine, $last_row_log, $new_machine_log);


            return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "عملیات با موفقیت انجام شد."]);

        } else {
            return back()->withErrors($result["error"]);

        }


    }

    public static function SetInjectionMaterial(
        Request $request, Machine $machine, Allocation $allocation,
                $input_number = 0,  // تعداد ورودی هایی که باید مقدار بسته بندی وارد کرده باشند.
                $new_machine_log = null,
                $current_production = null,
                $checkPackingIsOnlyEntrance = false, // در هر ورودی دقیقا یک بسته بندی وارد شده باشد.
                $replacement_status_id = 3359001, // این برای چله است؟ که کدام ورودی باید عوض کینم وقتی دو چله داریم و یکیش را درخواست می دهیم.
    )
    {

        $current_input_list = CurrentMachineInput::
        where([
            "machine_id" => $machine->id,
            "replacement_status_id" => $replacement_status_id,
            "allocation_id" => ($allocation->id ?? -1),
            "bill_of_material_entering_type_id" => 1 // ناپیوسته با بسته بندی
        ])->
        orderBy("goods_kind_id")->
        orderBy("input_line_code")->
        get();

        $k = 0;
        $error = "";
        $data = $request->data;
        $packing_code_list = [];

        $data_other_doblicate=[]; // لیست ورودی هایی که نباید چک شود که تکراری هستند ( مثل چله ها )
        // return $request->all();
        foreach ($current_input_list as $item) {
            if (isset($data["input"][$item->id])) {

                $packing_code = "DCPK/" . $data["input"][$item->id];


                $packing_form = PackingForm::where("code", $packing_code)->first();
                if (!$packing_form) {
                    $error .= "بسته بندی " . $packing_code . " وجود ندارد." . "<br/>";
                    continue;
                }
                if ($packing_form->status_id != 7007003) {
                    $error .= "وضعیت بسته بندی " . $packing_code . " معتبر نمی باشد، لطفا بسته بندی دیگری را جایگزین کنید." . "<br/>";
                    continue;
                }

// چک کردن انبار بسته بندی با انبار انبارک ماشین
                $consume_warehouse = $item->consume_warehouse;
                if (!$consume_warehouse) {

                    $error .= " انبار مصرف کالا در ورودی های ماشین، نامعتبر است، لطفا با پشتیبانی تماس بگیرید.";
                    continue;
                }
                // echo $packing_code." - ".$packing_form->warehouse_id." - ".($consume_warehouse->id)."<br/>";
                if ($packing_form->warehouse_id != $consume_warehouse->id) {


                    if ($item->production->production_type_id == 2) {
                        // اگر کارت نمونه گیری است باید انبار مصرف در یکی از انبارک های ماشین، گروه ماشین ایستگاه کاری یا خط تولید باشد.
                        $warehouse_type_id = $packing_form->warehouse->warehouse_type_id ?? 0;
                        if (!in_array($warehouse_type_id, [2, 3, 4, 5])) {
                            $error .= "بسته بندی " . $packing_code . " در  " . $consume_warehouse->caption . " وجود ندارد." . "<br/>";
                            continue;
                        }
                    } else {
                        // اگر کارت تولیدی است حتما باید بسته بندی در انبار مصرف قرار داشته باشد.
                        $error .= "بسته بندی " . $packing_code . " در  " . $consume_warehouse->caption . " وجود ندارد." . "<br/>";
                        continue;

                    }
                }

                // لیست بسته بندی های آخرین سطح
                $lowest_level_packing_form_ids = PackingForm::LowestLevelOfPackingFormIds([0 => $packing_form]);

                //چک کردن اینکه حداقل یک کالا داخل بسته بندی باشد، که با کالای ووردی یک است.
                $count_material = PackingFormItem::whereIn("packing_form_id", $lowest_level_packing_form_ids)->
                where("product_id", $item->material_id)->count();
                if ($count_material == 0) {
                    $error .= "در بسته بندی " . $packing_code . " " . $item->material->caption . " وجود ندارد." . "<br/>";
                    continue;
                }

                //چک کردن اینکه بسته بندی در حال برگشت به انبار مواد اولیه نباشد
                $modification_packing = Allocation\Modification\MachineAllocationModificationPackingForm::
                join("machine_allocation_modifications",
                    "machine_allocation_modifications.id",
                    "machine_allocation_modification_id"
                )->
                where("status_id", "!=", 6021003)-> // خاتمه یافته
                where("packing_form_id", $packing_form->id)->
                select("machine_allocation_modification_packing_form.*")->
                first();
                // اگر کاملا مصرف شده بود خطا بدهد
                // این در حالتی پیش می آید که بسته بندی در انبارگردانی باشد و بخواند برای آن تزریق انجام دهند.
                if ($modification_packing && $modification_packing->consumed_status_id == 6021103) {
                    $error .= " بسته بندی " . $packing_code . " در درخواست برگشت از انبار شماره " .
                        $modification_packing->machine_allocation_modification_id .
                        "وجود دارد  و در وضعیت کاملا مصرف شده می باشد بنابراین امکان انتخاب آن وجود ندارد." .
                        "<br/>" . "لطفا وضعیت بسته بندی را در برگشت مواد اولیه اصلاح فرمایید.";
                }

                if ($checkPackingIsOnlyEntrance) {
                    $result_only_entrance = GeneralInjectionOfMaterialController::CheckPackingIsOnlyEntranceToMachine($machine->id, $packing_code, $packing_code_list);
                    if (!$result_only_entrance["result"]) {
                        $error .= $result_only_entrance["error"];
                    }
                }

                $k++;
                $packing_code_list[] = $packing_code;

            }
            else{

               // این در صورتی پیش می آید که ماشین برای یک ورودی
             $c= CurrentMachineInput::
                 where("allocation_id", $item->allocation_id)->
             where("material_id", $item->material_id)->
              where("input_line_code", $item->input_line_code)->
              where("id","!=", $item->id)->first();
             if($c && isset( $data["input"][$c->id])){
                 $data["input"][$item->id]=      $data["input"][$c->id];
                 $data_other_doblicate[$item->id]=1;
                 $k++;
             }
            }
        }

        if ($error != "") {
            return ["result" => false, "error" => $error];
        }

        if ($k == 0 && $input_number == 0) {
            // احتمالا لازم نبوده است که ورودی تزریق شود.
            return ["result" => true, "message" => "هیچ ورودی تزریق نشده و حداقل آن هم صفر بوده است"];
        }
        if ($k == 0) {
            return ["result" => false, "error" => "لطفا حداقل یک بسته بندی را وارد نمایید."];
        }


        if ($k < $input_number) {
            return ["result" => false, "error" => "لطفا همه ورودی ها را تکمیل نمایید.".$k];
        }
//        if ($k > $input_number) {
//            return ["result" => false, "error" => "تعداد بسته بندی های وارد شده بیش از ورودی های ماشین می باشد."];
//        }


        // اگر لاگ ماشین ارسال شده است، اول آن را ذخیره میکنیم.
        if ($new_machine_log) {
            $new_machine_log->save();
            event(new MachineLogEvent($machine, $new_machine_log));
        }

        foreach ($current_input_list as $item) {
            if (isset($data["input"][$item->id])) {
                $packing_code = "DCPK/" . $data["input"][$item->id];

                $entry_packing_form = PackingForm::where("code", $packing_code)->first();


                // لیست بسته بندی های آخرین سطح
                $lowest_level_packing_form_ids = PackingForm::LowestLevelOfPackingFormIds([0 => $entry_packing_form]);

                $packing_form = $entry_packing_form;
                if ($packing_form->packing_form_contents()->count() > 0) {
                    $packing_form = $entry_packing_form->packing_form_contents()->first();
                }

                $item->packing_form_id = $entry_packing_form->id;

                $packing_form_item = PackingFormItem::whereIn("packing_form_id", $lowest_level_packing_form_ids)->
                where("product_id", $item->material_id)->first();;
                if (!$packing_form_item) {
                    return [
                        "result" => false,
                        "error" => "اطلاعات آیتم های بسته بندی نادرست است، لطفا با پشتیبانی تماس بگیرید."
                    ];
                }

                // خالی کردن حامل قبلی
                if ($item->carrier) {
                    $item->carrier->SetEmpty();
                }
                $item->lot_number_id = $packing_form_item->lot_number_id;
                $item->carrier_id = $entry_packing_form->carrier_id;

                // فقط برای کارت نمونه گیری انبار مصرف می تواند تغییر کند.
                if ($item->production->production_type_id == 2) {
                    $item->consume_warehouse_id = $entry_packing_form->warehouse_id;
                }

                $item->save();

                // Log Data
                CurrentMachineInputLog::create([
                    "current_machine_input_id" => $item->id,
                    "machine_id" => $item->machine_id,
                    "allocation_id" => $item->allocation_id,
                    "production_id" => $item->production_id,
                    "production_form_id" => $current_production->id ?? null,
                    "product_id" => $item->product_id,
                    "material_id" => $item->material_id,
                    "lot_number_id" => $item->lot_number_id,
                    "entry_packing_form_id" => $entry_packing_form->id,
                    "packing_form_id" => $packing_form->id,
                    "user_id" => Auth::id(),
                    "machine_log_id" => $new_machine_log->id ?? null
                ]);

            }
        }

        return ["result" => true, "new_machine_log" => $new_machine_log ?? null];
    }

    /**
     * @param $machine_id
     * @param $packing_code
     * @param $packing_code_list
     * @return array|true[]
     * چک کردن اینکه در هر ورودی دقیقا یک بسته بندی وارد شده باشد.
     */
    public static function CheckPackingIsOnlyEntranceToMachine($machine_id, $packing_code, $packing_code_list)
    {
        $packing_form = PackingForm::where("code", $packing_code)->first();

        if (!$packing_form) {
            return [
                "result" => false,
                "error" => "کد بسته بندی نامعتبر است."
            ];
        }
        $current_machine_input = CurrentMachineInput::
        where("machine_id", $machine_id)->
        where("packing_form_id", $packing_form->id)->first();
        if ($current_machine_input) {
            return [
                "result" => false,
                "error" => "بسته بندی " . $packing_form->code . " قبلا در ورودی های ماشین انتخاب شده و امکان انتخاب مجدد آنها وجود ندارد."
            ];
        }

        if (in_array($packing_code, $packing_code_list)) {
            return [
                "result" => false,
                "error" => "بسته بندی " . $packing_form->code . " قبلا در ورودی های ماشین انتخاب شده و به ازای هر شماره ورودی یک بسته بندی یکتا وارد نمایید."
            ];
        }

        return [
            "result" => true
        ];
    }

}

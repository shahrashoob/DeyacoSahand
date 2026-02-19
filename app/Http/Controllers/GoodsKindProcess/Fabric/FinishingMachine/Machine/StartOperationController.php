<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\ProductionCard\MachineAllocationController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\InjectionOfMaterialController;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Fabric\Fabric;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationMaterialConsumed;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypePropertyValue;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Machine\MachineTypeOutputBandPackingType;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Packing\PackingTypeLayer;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Production\ProductionFormItemLotNumber;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class StartOperationController extends Controller
{
    // goods_kind_process/fabric/finishing_machine/machine/start_operation
    public static $info = [
        "route" => "fabric.finishing_machine.machine.start_operation.",
        "enable_status" => ["902"],
        "button" => ["caption" => "شروع عملیات", "class" => "btn-primary"],
        "view_path" => "goods_kind_process.fabric.finishing_machine.machine.start_operation.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric.finishing_machine.machine.dashboard.";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view_path"];
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

        // آیا ماهیت تغییر می کند یا خیر
        $value_203 = MachineModuleTypePropertyValue::getValue("73030011203", $machine->machine_type_id);
// آیا ماهییت کالا در ماشین بعدی تغییر می کند.
        $value_203_next_result = self::GetValue203Next($allocation->items()->first());
        if (
            // ماهییت عوض می شود و ماهیت
            ($value_203 && !$value_203_next_result["value_203_next"] && $value_203_next_result["next_line_product_station"]) ||
            // برای زمان هایی که یک مسیر محسول بیشتر نداریم.
            ($value_203 && !$value_203_next_result["value_203_next"] && !$value_203_next_result["next_line_product_station"]) ||
            (!$value_203 && !$value_203_next_result["value_203_next"])) {
            // اگر ماهیت کالا تغییر می کند و آخرین ماشینی است که ماهیت را تغییر می دهد کالای نهایی را در نظر می گریم،
            // در غیر این صورت همان کالا(ماده اولیه میانی) را در نظر می گیریم.
            $goods_kind_list = [$allocation->items()->first()->production->product->goods_kind_id];
        } else {
            $product = $allocation->items()->first()->production->product;
            $structure_bom_item = BOMItem::where([
                "product_id" => $product->id,
                "is_structure_product" => 1
            ])->first();
            if (!$structure_bom_item) {
                return back()->withErrors("کالای ساختاری در تعریف BOM کالای " . $product->fullCaption() . " مشخص نشده است.");
            }
            $goods_kind_list = [$structure_bom_item->material->goods_kind_id];
            if (count($goods_kind_list) == 0) {
                return back()->withErrors("در BOM کالا هیچ ماده اولیه به عنوان ماده اولیه ساختاری انتخاب نشده است.");
            }
        }

        // بسته بندی های خروجی ماشین
        $packing_type_option = Option::get("packing_type_from_output_band", 0, $machine->machine_type_id, $goods_kind_list);
        $carrier_id = null;
        $carrier_has_number_ability = false; // آیا حامل از کاربر گرفته شود؟
        if (!is_array($packing_type_option["items"]) || count($packing_type_option["items"]) == 0) {
            $goods_kind_list_caption = "";
            foreach ($goods_kind_list as $goods_kind_id_) {
                $goods_kind_list_caption .= GoodsKind::find($goods_kind_id_)->caption;
            }
            return back()->withErrors("بسته بندی های باند خروجی در گروه ماشین مشخص نشده است، لطفا با واحد پشتیبانی تماس بگیرید."
                . "خروجی ماشین باید از رسته کالایی های زیر باشد:" . "<br/>" . $goods_kind_list_caption
            );
        }

        // فرض می کنیم که
        // حامل همه بسته بندی های خروجی ماشین یا قابل شماره گذاری اند، یا غیر قابل شماره گذاری،
        // بنابراین فقط اولین بسته بندی را بررسی کرده و برای همه در نظر می گیریم.
        if (count($packing_type_option["items"]) >= 1) {
            $packing_type_id = $packing_type_option["items"][0]["value"];
            $packing_type_layer = PackingTypeLayer::where("packing_type_id", $packing_type_id)->orderBy("id", "desc")->first();
            if (!$packing_type_layer) {
                return back()->withErrors("اطلاعات لایه های بسته بندی با کد $packing_type_id
                 نامعتبر است، لطفا با واحد پشتیبانی تماس بگیرید.");
            }
            if ($packing_type_layer->carrier_type_id != 0 && !$packing_type_layer->carrier_type) {
                return back()->withErrors(" نوع حامل بسته بندی " . $packing_type_id
                    . " در سامانه وجود ندارد، لطفا حامل بسته بندی با کد " .
                    $packing_type_layer->carrier_type_id . " را از منظومه داده ای به سامانه اضافه کنید.");
            }
            $carrier_has_number_ability = $packing_type_layer->carrier_type->has_number_ability ?? 0;
        }

        $current_production_form = $machine->getCurrentProductionForm();
        // آیتمی که تخصیص جاری است.
        $machine_allocation = $allocation->items()->
        where("status_id", 5310010)-> // تخصیص جاری
        first();

        // آیا مقدار فرم تولید با تزریق مواد اولیه تکمیل می شود؟
        $value_201 = MachineModuleTypePropertyValue::getValue("73030011201", $machine->machine_type_id);
        // آیا ماژول ثبت تولید در ماشین فعال است.
        $value_202 = MachineModuleTypePropertyValue::getValue("73030011202", $machine->machine_type_id);
        // آیا تزریق مواد اولیه به صورت پیوست با ماشین قبل می باشد
        $value_204 = MachineModuleTypePropertyValue::getValue("73030011204", $machine->machine_type_id);

        // آیا آیتم های مواد اولیه دقیقا در فرم تولید منعکس می گردد؟
        $value_205 = MachineModuleTypePropertyValue::getValue("73030011205", $machine->machine_type_id);


        if ($value_201 == 1) {
            $result = CurrentMachineInput::CheckInventoryAndPackingForms($allocation);
            if (!$result["result"]) {
                return back()->withErrors($result["error"]);
            }
            $current_input_list = [];

            $current_input_list =
                CurrentMachineInput::
                where([
                    "machine_id" => $machine->id,
                    "allocation_id" => ($allocation->id ?? -1),
                    "production_id" => $machine_allocation->production_id ?? -1
                ])->
                get();


            return view($this->view_path . "index", compact(["machine", "allocation", "machine_allocation", "current_input_list", "packing_type_option", "carrier_id", "current_production_form", "carrier_has_number_ability"]));

        } elseif ($value_202 == 1 && !($value_201 == 1 || $value_204 == 1)) {

            // به دست آوردن نوع حرکت مواد در ماشین

            $current_input_list = [];
            $packing_type_option = null; //  در این حالت بسته بندی خروجی می شود بسته بندی کارت و نیاز نیست از بسته بندی های خروجی ماشین استفاده کنیم.
            return view($this->view_path . "index", compact(["machine", "allocation", "machine_allocation", "packing_type_option", 'current_input_list', "carrier_id", "current_production_form", "carrier_has_number_ability"]));

//            // در این حالت کلا فرم تولید نداریم و مثل ماژول ویژه دوره پیاده سازی عمل می کنیم.
//            // ثبت وضعیت بعدی
//            $next_status_result = DashboardController::GetNextStatus($machine, $allocation, $machine->production_status_id, 5310902, true);
//
//            $machine->setStatus(
//                null,
//                $next_status_result["on_status_id"],
//                $next_status_result["status_id"],
//                $next_status_result["machine_off_reason_id"]);
//
//            $machineLog = new MachineLog();
//            $machineLog->machine_event_type_id = 5310902; // شروع عملیات
//            $machineLog->save();
//            $machineLog->station_sub_operation_id = $next_status_result["current_station_sub_operation_id"];
//            event(new MachineLogEvent($machine, $machineLog));
//            return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "عملیات با موفقیت انجام شد."]);

        } else {


            $result_production_form_item = self::GetProductionFromItem($allocation,$value_202);
//             return    $result_production_form_item = self::GetProductionFromItemByMachineAllocation($machine_allocation, 2);
            if (!$result_production_form_item["result"]) {
                return back()->withErrors($result_production_form_item["error"]);
            }
            $production_form_item_list =[];// $result_production_form_item["production_form_item_list"];

            $current_input_list = [];
            return view($this->view_path . "index", compact(["machine", "allocation", "machine_allocation", "current_input_list", "production_form_item_list", "packing_type_option", "carrier_id", "current_production_form", "carrier_has_number_ability"]));


//            return back()->withErrors("بخشی از ماژول برای حالتی که مشخصه 73030011201  برابر با 0 باشد پیاده سازی نشده است.");
        }
    }

    public function submit(Request $request, Machine $machine)
    {


        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();
        if (!$allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین یافت نشد");
        }
        $machine_allocation_count = 0;
        foreach ($allocation->items as $machine_allocation_item) {
            $machine_allocation_count++;
            if ($machine_allocation_item->production->packing_types()->count() == 0) {
                return back()->withErrors("نوع بسته بندی برای کارت تولید " . $machine_allocation_item->production->serial . " مشخص نشده است.");
            }
        }

        // آیتمی که تخصیص جاری است.
        $machine_allocation = $allocation->items()->
        where("status_id", 5310010)-> // تخصیص جاری
        first();

        if ($machine_allocation_count == 0) {
            return back()->withErrors("هیچ آیتم تخصیصی برای تخصیص جاری یافت نشد، لطفا با مسئول مربوطه تماس بگیرید.");
        }

        // به دست آوردن نوع حرکت مواد در ماشین
        $machine_operation_discharge_type_id = $machine_allocation->line_product_station->station_operation->discharge_type_id ?? -1;
        if ($machine_operation_discharge_type_id == -1) {
            return back()->withErrors("نوع حرکت مواد اولیه در ماشین، قابل تشخیص نمی باشد، لطفا با پشتیبانی تماس بگیرید.");
        }

        // آیا مقدار فرم تولید با تزریق مواد اولیه تکمیل می شود؟
        $value_201 = MachineModuleTypePropertyValue::getValue("73030011201", $machine->machine_type_id);
        // آیا ماژول ثبت تولید در ماشین فعال است.
        $value_202 = MachineModuleTypePropertyValue::getValue("73030011202", $machine->machine_type_id);
        // آیا آیتم های مواد اولیه دقیقا در فرم تولید منعکس می گردد؟
        $value_205 = MachineModuleTypePropertyValue::getValue("73030011205", $machine->machine_type_id);

        //محاسبه مقدار مواد اولیه براساس مقدار تخصیص (۱) است
        //مقدار فرم تولید استخراج شده از ماشین قبل (۲)
        //بر اساس مقدار مواد اولیه تحویل داده شده به ماشین (۳)
        $value_209 = MachineModuleTypePropertyValue::getValue("73030011209", $machine->machine_type_id);


        if ($machine->check_inventory_for_allocation) {
            // ارسال درخواست برای کدام رسته های کالایی فعال است.
            // $goods_kind_ids = MachineTypeInputBandGoodsKind:: getGoodsKindIdsWhereRequestFromRobot($machine, $active = 1);

//            if ($value_205) {

            $result = InjectionOfMaterialController::SetInjectionMaterial(
                $request, $machine, $allocation,
                $value_201 == 1 ? 1 : 0, null, null,
                false,
                3359001
            );
            if (!$result["result"]) {
                return back()->withErrors("نتیجه بررسی ماژول تزریق مواد اولیه:" . "<br/>" . $result["error"]);
            }
//            }

        } else {
            return back()->withErrors("لطفا تنظیمات 'موجودی مواد اولیه برای انبارک چک شود؟' را برای ماشین فعال کنید.");
        }

        // وضعیت بعدی معتبر است.
        $next_status_result = DashboardController::GetNextStatus($machine, $allocation, $machine->production_status_id, -1, false);

        if (!$next_status_result["result"]) {
            return back()->withErrors($next_status_result["error"]);
        }

        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 5310902; // شروع عملیات
        $machineLog->save();

        if ($value_201 == 1) {
            /**
             * آیا مقدار فرم تولید با تزریق مواد اولیه تکمیل می شود؟ بله
             * در این حالت هر آیتمی بسته بندی برابر است با یک آیتم در فرم تولید
             * اگر مسیر فقط یک مسیر دارد و فرم ثبت تولید دارد، باید نوع عملیات آن بچی باشد
             */
            $result = self::AddItemToCurrentProductionFrom($request, $machine, $allocation, $machineLog, $value_201 == 1 ? 1 : 0, $machine_operation_discharge_type_id, $value_205, $value_209, $value_202);
            if (!$result["result"]) {
                $machineLog->delete();
                return back()->withErrors($result["error"]);
            }


        } else {


            if ($value_202 == 0) {
                // فرم تولید جاری
                $production_form_status_id = 7302001;
                $current_production_form_result = self::GetCurrentProductionFrom($request, $machine, $machineLog, $production_form_status_id);
                if (!$current_production_form_result["result"]) {
                    return back()->withErrors($current_production_form_result["error"]);
                }
                $current_production_form = $current_production_form_result["production_form"];
                $is_new_form = $current_production_form_result["is_new_form"];
            } else {
                $is_new_form = 0;
            }

            //اگر عملیات بچ است و قبلا تکمیل شده است باید از این مرحله رد شود.
            // فقط زمانی باید اضافه کند که یا فرم جدید باشد یا عملیات پیوست که قبلا وارد نکرده باشد.
            // در زمان هایی که فقط یک مسیر محصول داریم ( مثل بسته بندی) که نوع عملیات آن پیوست است، دیگر فرم قبلی نداریم و لازم نیست که فرم قبلی را پیدا کنیم و وضعیت آن را تزریق شده به ماشین در نظر بگیریم.
            if ($is_new_form || ($machine_allocation->line_product_station->station_operation->station_operation_type_id == 2 && $machine_allocation->parent_allocation_id)) {

                $production_form_item_list_all = [];
                foreach ($allocation->items as $machine_allocation_item) {
                    // لیست آیتم های فرم تولید که از ماشین قبلی آمده
                    $result_production_form_item = self::GetProductionFromItemByMachineAllocation($machine_allocation_item, $machine_operation_discharge_type_id,$value_202);
                    if (!$result_production_form_item["result"]) {
                        return back()->withErrors($result_production_form_item["error"]);
                    }
                    $production_form_item_list = $result_production_form_item["production_form_item_list"];

                    $production_form_item_list_all[$machine_allocation_item->id] = $production_form_item_list;

                }

                // آیا ماهییت کالا در ماشین تغییر می کند.
                $value_203 = MachineModuleTypePropertyValue::getValue("73030011203", $machine->machine_type_id);

                // آیا ماهییت کالا در ماشین بعدی تغییر می کند.
                $value_203_next = self::GetValue203Next($machine_allocation);

                $old_production_form_ids = []; // لیست فرم های تزریق مواد اولیه

                // اگر چند تخصیص با هم بود، همه فرم های تخصیص را در فرم جدید ایجاد می کنیم.
                foreach ($allocation->items as $machine_allocation_item) {
                    $production_form_item_list = $production_form_item_list_all[$machine_allocation_item->id];
                    foreach ($production_form_item_list as $production_form_item) {

                        // اگر ماهیت کالا تغییر می کند و آخرین ماشینی است که ماهیت را تغییر می دهد کالای نهایی را در نظر می گریم،
                        // در غیر این صورت همان کالا(ماده اولیه میانی) را در نظر می گیریم.
                        $product = $value_203 && !$value_203_next ?
                            $production_form_item->production->product : // کالای نهایی
                            $production_form_item->product; // کالای ساختاری(مواد اولیه)

                        $product_id = $product->id;

                        if ($value_202 == 0) { // مازول ثبت تولید نداریم.
                            $new_production_form_item = ProductionFormItem::AddNewItem(
                                $allocation->id,
                                $current_production_form->id,
                                $production_form_item->production_id,
                                $product_id,
                                $production_form_item->band_code,
                                $production_form_status_id,
                                0,
                                $product->version->version_code ?? null
                            );
                            $new_production_form_item->forecast_amount = $production_form_item->forecast_amount;
                            $new_production_form_item->amount = $production_form_item->amount;
                            $new_production_form_item->final_amount = $production_form_item->final_amount;
                            $new_production_form_item->sub_amount = $production_form_item->sub_amount;
                            $new_production_form_item->packing_form_item_id = $production_form_item->packing_form_item_id;
                            $new_production_form_item->machine_allocation_id = $machine_allocation_item->id;
                            $new_production_form_item->save();

                            $result_lot_number = Fabric::ChangeLot($machine, $product_id);
                            if (!$result_lot_number["result"]) {
                                return back()->withErrors($result_lot_number["error"]);
                            }

                            $new_production_form_item->AddLotItem($result_lot_number["lot_number"]->id, $machineLog);
                        }

                        $production_form_item->status_id = 7302004; // تزریق شده به ماشین بعدی
                        $production_form_item->save();

                        $old_production_form_ids[$production_form_item->production_form_id] = $production_form_item->production_form_id;


                        // وقتی مقدار مواد اولیه ساختاری را تحویل می گیریم، اگر مقدار تخصیص نال باشد، مقدار تخصیص را برابر با مقدار فرم تولید قرار می دهیم.
                        // چون ممکن است یک تخصیص چند کالا داشته باشد، به ازای هر کالا این اقدام صورت می گیرید.
                        $machine_allocation_where_amount_is_null = MachineAllocation::where([
                            "allocation_id" => $allocation->id,
                        ])->
                        // کالای ساختاری مواد اولیه ( اگر فرم جاری کالای نهایی باشد، بعنی قبلا مواد ساختاری را تحویل گرفته ایم)
                        whereIn("product_id", // کالا یا کالای ساختیار یا کالای محصول است.
                            [
                                $production_form_item->production->product_id, // کالای نهایی
                                $production_form_item->product_id // کالای ساختاری(مواد اولیه)]
                            ]
                        )->
                        whereNull("allocation_amount")->
                        first();

                        if ($machine_allocation_where_amount_is_null) {
                            $machine_allocation_where_amount_is_null->allocation_amount = $production_form_item->final_amount;
                            $machine_allocation_where_amount_is_null->allocation_sub_amount = $production_form_item->sub_amount;
                            $machine_allocation_where_amount_is_null->save();
                            event(new ProductionCardLogEvent($machine_allocation_where_amount_is_null->production, "ثبت مقدار " . $production_form_item->final_amount . " برای تخصیص", null,));
                        }

                    }
                }
                // فرم های تولید که  آیتم های آن به ماشین تزریق  شده اند، را کلا می گذاریم تزریق شده به ماشین
                foreach ($old_production_form_ids as $old_production_form_id) {
                    $count = ProductionFormItem::where([
                        "production_form_id" => $old_production_form_id,
                    ])->
                    where("status_id", 7302003)->
                    count();

                    if ($count == 0) {
                        $old_production_form = ProductionForm::find($old_production_form_id);
                        $old_production_form->status_id = 7302004; // تزریق شده به ماشین بعدی)
                        $old_production_form->save();
                        // خالی کردن حامل فرم تولید
                        if ($old_production_form->carrier) {
                            Carrier::StaticSetEmpty($old_production_form->carrier);
                        }
                    }

                }


            }
        }

        // ثبت وضعیت بعدی
        $next_status_result = DashboardController::GetNextStatus($machine, $allocation, $machine->production_status_id, 5310902, true);

        $machine->setStatus(
            null,
            $next_status_result["on_status_id"],
            $next_status_result["status_id"],
            $next_status_result["machine_off_reason_id"]);

        $machineLog->station_sub_operation_id = $next_status_result["current_station_sub_operation_id"];
        $machineLog->allocation_id = $allocation->id;
        event(new MachineLogEvent($machine, $machineLog));


        // چک کردن مالک بسته بندی
        // اگر حداقل یکی از بسته بندی های مالک داشت، مالک تخصیص را مالک تخصیص قرار می دهیم.
        $packing_form_applicant = CurrentMachineInput::join("packing_forms", "packing_forms.id", "packing_form_id")->
        whereNotNull("applicant_type_id")->
        where("allocation_id", $allocation->id)->
        select("applicant_type_id", "applicant_id")->
        first();
        if ($packing_form_applicant) {
            $allocation->applicant_type_id = $packing_form_applicant->applicant_type_id;
            $allocation->applicant_id = $packing_form_applicant->applicant_id;
            $allocation->save();
        }

        // استارت T استارت برای ماشین ها
        $result = StartToStartMachineController::PostSubmit($machine);
        if (!$result["result"] && !isset($result["warning"])) {
            return redirect()->route($this->dashboard_route . "view", compact("machine"))->withErrors($result["error"]);

        }

        // همه کارت های تخصیص بشود در حال تولید
        foreach ($allocation->items as $item) {
            $new_status = Fabric::GetProductionStatus($item->production);
            $item->production->waiting_status_id = $new_status;
            if($new_status == 7301004){
                $item->production->status_id = 520;
            }
            $item->production->save();
            event(new ProductionCardLogEvent($item->production, "", null, 7301003));
        }

        if ($value_202 == 0) { // اکر مازول ثبت تولید در ماشین نداریم، به اندازه مقدار فرم تولید باید مصرف ثبت گردد.
            // ثبت مقدار مصرف
            MachineAllocationMaterialConsumed::registerNewConsumed($allocation, $machine, null, $machineLog, -1);
        }

        return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "عملیات با موفقیت انجام شد."]);

    }

    public static function GetProductionFromItem(Allocation $allocation,$value_202)
    {
        $machine_allocation_where_no_parent = $allocation->items()->
        where(function ($query) use ($allocation) {
            return $query->whereNull('parent_allocation_id')->
            orWhere("parent_allocation_id", 0);
        })->
        first();
        if ($machine_allocation_where_no_parent) {
            return [
                "result" => false,
                "error" => "آیتم  تخصیص با شماره " . $machine_allocation_where_no_parent->id . " نامعتبر است، لطفا با پشتیبانی تماس بگیرید."
            ];

        }

        $production_form_item_list = [];
        foreach ($allocation->items as $machine_allocation) {
            $production_form_items = ProductionFormItem::where([
                "allocation_id" => $machine_allocation->parent_allocation_id,
                "production_id" => $machine_allocation->production_id
            ])->
//            where(function ($query) use ($machine_allocation) {
//                return $query->whereNull('machine_allocation_id')->orWhere("machine_allocation_id", $machine_allocation->id);
//            })->
            get();
            if (count($production_form_items) == 0) {

                // اگر تخصیص پدر جاری باشد و هنوز پایان یافته نباشد یا روزو باشد باید پیام بدهد که ابتدا تخصیص ** بر روی ماشین ** را تکمیل نمایید.
                $allocation_parent=Allocation::find($machine_allocation->parent_allocation_id);
                if($allocation_parent && in_array( $allocation_parent->status_id , [5310010,5310040]) ){



                    return [
                        "result" => false,
                        "error"=>" با توجه به اینکه تخصیص ".$allocation_parent->id." بر روی ماشین ".$allocation_parent->machine->caption." در وضعیت ".$allocation_parent->status->caption." می باشد و هنوز فرایند تولید آن تکمیل نشده است، امکان شروع تولید بر روی این ماشین نمی باشد، لطفا ابتدا پایان عملیات تخصیص ذکر شده را  تکمیل نمایید."
                    ];
                }
                if($machine_allocation->parent_allocation && $value_202) {
$machine_allocation_parent=  $machine_allocation->parent_allocation->items()->first();
//                    return
//                        $machine_allocation_parent = MachineAllocation::where("parent_allocation_id",->parent_allocation_id)->first();
                // در حالتی که تزریق مواد اولیه با ماشین قبل می باشد و ممکن است، در زمانی که تخصیص را تقسیم کرده اند، برای اولین بار فرم وجود دارد ولی برای بار های بعدی فرمی وجود ندارد.
                // مثال: تغییر مخقدار تخصیص در زمان خروج از رنگرزی

                    $production_form_items = ProductionFormItem::where([
                        "allocation_id" => $machine_allocation_parent->parent_allocation_id??0,
                        "production_id" => $machine_allocation_parent->production_id??0
                    ])->get ();
                if (count($production_form_items)>0 && $value_202) {
                    return [
                        "result" => true,
                        "warning" => "احتمالا فرم تولید قبلا توسط تخصیص دیگری به ماشین تزریق شده است.",
                        "production_form_item_list" => $production_form_items
                    ];
                }
                }


                return [
                    "result" => false,
                    "error" => "فرم تولید برای آیتم تخصیص شماره " . $machine_allocation->allocation_id . " یافت نشد، لطفا با پشیتبانی تماس بگیرید."
                ];

            }
            foreach ($production_form_items as $production_form_item) {
                $production_form_item_list[] = $production_form_item;
            }
        }
        return [
            "result" => true,
            "production_form_item_list" => $production_form_item_list
        ];
    }

    // گرفتن آیتم های فرم تولید با توجه به آیتم های تخصیص
    public static function GetProductionFromItemByMachineAllocation(MachineAllocation $machine_allocation, $machine_operation_discharge_type_id,$value_202)
    {

        if (!$machine_allocation->parent_allocation_id) {
            return [
                "result" => false,
                "error" => "آیتم  تخصیص با شماره " . $machine_allocation->id . " نامعتبر است، لطفا با پشتیبانی تماس بگیرید."
            ];

        }

        $production_form_item_list = [];

        $production_form_items = ProductionFormItem::where([
            "allocation_id" => $machine_allocation->parent_allocation_id,
        ])->
//        where(function ($query) use ($machine_allocation) {
//            return $query->whereNull('machine_allocation_id')->orWhere("machine_allocation_id", $machine_allocation->id);
//        })->
        // وقتی پیوسته است، فقط ردیف های همان کارت را نمایش می دهد.
        when($machine_allocation->line_product_station->station_operation->station_operation_type_id == 2,
            function ($query) use ($machine_allocation) {
                return $query->where("production_id", $machine_allocation->production_id);
            }
        )->
        get();
        if (count($production_form_items) == 0 && $value_202==0) {
            return [
                "result" => false,
                "error" => "فرم تولید برای آیتم تخصیص شماره " . $machine_allocation->id . " یافت نشد، لطفا با پشیتبانی تماس بگیرید."
            ];

        }
        $packing_type = null;
        foreach ($production_form_items as $production_form_item) {
            if ($production_form_item->status_id != 7302004) {
                $production_form_item_list[] = $production_form_item;

                if (!$packing_type) {
                    $packing_type = $production_form_item->production_form->packing_type;
                }

            }

        }

        if ($packing_type) { //  ممکن است هیچ آیتمی برای خروج وجود نداشته باشد.
            $result_stack_display_order = PackingForm::GetFirstStackDisplayOrder($packing_type, $machine_operation_discharge_type_id);

            if (!$result_stack_display_order["result"]) {
                return $result_stack_display_order;
            }

            if ($result_stack_display_order["asc_or_desc"] == "desc") {

                $production_form_item_list = array_reverse($production_form_item_list);
            }
        }
//

        return [
            "result" => true,
            "production_form_item_list" => $production_form_item_list,
        ];
    }

    public static function CheckPackingTypeAndCarrier(Request $request)
    {
        // پیدا کردن نوع حامل از روی نوع بسته بندی
        $packing_type = PackingType::find($request->packing_type_id);
        if (!$packing_type) {
            return [
                "result" => false,
                "error" => "نوع بسته بندی معتبر نمی باشد."
            ];

        }
        $first_layer = $packing_type->layers()->orderBy("layer_code", "desc")->first();
        if (!$first_layer) {
            return [
                "result" => false,
                "error" => "تعریف نوع حامل در  بسته بندی معتبر نمی باشد، لطفا با پشتیبانی تماس بگیرید."
            ];


        }
        $carrier = null;

        if ($first_layer->carrier_type && $first_layer->carrier_type->has_number_ability) {
            // بررسی حامل
            $result = Carrier::firstOrCreate($request->carrier_id, $first_layer->carrier_type_id, 5320001, null);
            if (!$result["result"]) {
                return [
                    "result" => false,
                    "error" => $result["message"]
                ];

            }
            $carrier = $result["carrier"];
            if ($carrier->status_id != 5320001) { // خالی
                return [
                    "result" => false,
                    "error" => $result["message"]
                ];

            }
            // اگر حامل قابل شماره گذاری است باید وزن آن ثبت شده باشد
            $carrier_weight_result = CarrierType::getWeight($carrier->carrier_type, $carrier);
            if (!$carrier_weight_result["result"]) {
                return [
                    "result" => false,
                    "error" => $carrier_weight_result["error"]
                ];
            }
        }


        return [
            "result" => true,
            "packing_type" => $packing_type,
            "carrier" => $carrier,
        ];
    }

    public function checkPermission(Machine $machine)
    {

        $result = DashboardController::checkPermissionConditions($machine, self::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

    /**
     * @param Request $request
     * @param Machine $machine
     * @param $current_production_form
     * @param Allocation $allocation
     * @param MachineLog $machineLog
     * @param $input_number
     * @param $machine_operation_discharge_type_id
     * @return array
     * اضافه کردن آیتم های بسته بندی به فرم تولید
     */

    public static function AddItemToCurrentProductionFrom(
        Request    $request,
        Machine    $machine,
        Allocation $allocation,
        MachineLog $machineLog, $input_number,
                   $machine_operation_discharge_type_id,
                   $value_205,
                   $value_209,
                   $value_202
    )
    {
        $production_form_status_id = 7302001;// در حال تکمیل
        if ($value_202 == 1) {
            $production_form_status_id = 7302004;// تزریق شده به ماشین
        }

        $current_production_form_result = self::GetCurrentProductionFrom($request, $machine, $machineLog, $production_form_status_id);
        if (!$current_production_form_result["result"]) {
            return $current_production_form_result;
        } else {
            $current_production_form = $current_production_form_result["production_form"];
        }


        // با توجه به تنظیمات ماشین اگر
        $allocation_items = [];
        foreach ($allocation->items as $item) {
            $allocation_items[$item->production_id] = $item;
        }

        if ($value_205 == 0) { // سرجمع فرم تولید قبلی یا برگ خروج قبلی به سیستم اضافه می گردد.

            // 10/9/1404 یک تغییر در این بخش اتفاق اوفتاد و اینکه مقدار فرم تولید را برابر با سرجمع مقدار ورودی های ماشین در نظر می گیریم.
            // یعنی اگر چند چیز را با هم میکس می کنند، جمع کل آنها می شود مقدار فرم تولید.

            $sum_current_machine_input = CurrentMachineInput::
            where("allocation_id", $allocation->id)->
            sum("amount_required");


            foreach ($allocation->items as $item) {

                $before_item_exists = ProductionFormItem::where([
                    "allocation_id" => $allocation->id,
                    "production_form_id" => $current_production_form->id,
                    "production_id" => $item->production_id,
                    "product_id" => $item->product_id, // این کد کالا نادرست است، چون ماهیت کالا عوض نمی شود.
                ])->first();

                if ($before_item_exists) {
                    // قبلا فرم تولید وجود داشته است.
                    $before_item_exists->forecast_amount = $sum_current_machine_input;
                    $before_item_exists->amount = $sum_current_machine_input;
                    $before_item_exists->final_amount = $sum_current_machine_input;
                    // $before_item_exists->sub_amount= $form_item_sum->sub_amount;
                    //  $before_item_exists->packing_form_item_id = $packing_form_item->id;
                    $before_item_exists->save();


                } else {
                    $production_form_item = ProductionFormItem::AddNewItem(
                        $allocation->id,
                        $current_production_form->id,
                        $item->production_id,
                        $item->product_id,
                        $item->band_code,
                        $production_form_status_id,
                        0,
                        $packing_form_item->product->version->version_code ?? null
                    );

                    $production_form_item->forecast_amount = $sum_current_machine_input;
                    $production_form_item->amount = $sum_current_machine_input;
                    $production_form_item->final_amount = $sum_current_machine_input;
//                    $production_form_item->sub_amount = $sum_current_machine_input;
//                    $production_form_item->packing_form_item_id = $packing_form_item->id;
                    $production_form_item->save();

                    // این قسمت نادرست است ولی بعدا اصلاح می شود
                    $lot_number_code = Fabric::getCurrentLot();
                    $lot_number_exists = LotNumber::ExistsCode("0", $item->product_id);
                    if (!$lot_number_exists) {
                        $lot_number = LotNumber::create([
                            "product_id" => $item->product_id,
                            "code" => $lot_number_code,
                            "user_id" => Auth::id(),
                        ]);
                    }
                    $lot_number = LotNumber::where("product_id", $item->product->id)->
                    where("code", $lot_number_code)->
                    first();


                    $production_form_item->AddLotItem($lot_number->id, $machineLog);
                }

            }


            // اگر یک به یک است، بیشتر از یکبار اجازه تزریق به ماشین داده نشود، چون ممکن است به خطا بخوریم.
// اضافه کردن یک رکورد در فرم تولید
//            foreach ($current_machine_input as $item) {
//
//                $packing_form_item = $item->packing_form->items()->first();
//                // اگر یک به یک است، بیشتر از یکبار اجازه تزریق به ماشین داده نشود، چون ممکن است به خطا بخوریم.
//                $before_item_exists = ProductionFormItem::where([
//                    "allocation_id" => $allocation->id,
//                    "production_form_id" => $current_production_form->id,
//                    "production_id" => $item->production_id,
//                    "product_id" => $packing_form_item->product_id,
//                ])->first();
//
////                if ($before_item_exists) {
////                    return [
////                        "result" => false,
////                        "error"=>" با توجه به اینکه تنظیمات 'آیا آیتم های مواد اولیه دقیقا در فرم تولید منعکس می گردد؟' بله می باشد، بیش از یکبار امکان تزریق به ماشین وجود ندارد."
////                    ];
////                }
//
//
//                // مقدار فرم تولید را با توجه به آخرین برگ خروجی که بسته بندی داخل آن بوده است به دست می آوریم.
//                $form_item_last = FormItem::
//                join("packing_form_item", "packing_form_item_id", "packing_form_item.id")->
//                where("packing_form_id", $packing_form_item->packing_form_id)->orderBy("form_id", "desc")->first();
//                if (!$form_item_last) {
//                    return [
//                        "result" => false,
//                        "error" => "برگ خروج متناظر با بسته بندی *** برای تزریق به فرم تولید یافت نشد." .
//                            "<br/>fun: AddItemToCurrentProductionFrom"
//                    ];
//                }
//
//                $form_item_sum = FormItem::where(["product_id" => $packing_form_item->product_id, "form_id" => $form_item_last->form_id])->selectRaw("sum(amount) as amount , sum(sub_amount) as sum_amount")->first();
//                if ($before_item_exists) {
//                    // قبلا فرم تولید وجود داشته است.
//                    $before_item_exists->forecast_amount += $form_item_sum->amount;
//                    $before_item_exists->amount += $form_item_sum->amount;
//                    $before_item_exists->final_amount += $form_item_sum->amount;
//                    $before_item_exists->sub_amount += $form_item_sum->sub_amount;
//                    $before_item_exists->packing_form_item_id = $packing_form_item->id;
//                    $before_item_exists->save();
//
//
//                } else {
//                    $production_form_item = ProductionFormItem::AddNewItem(
//                        $allocation->id,
//                        $current_production_form->id,
//                        $item->production_id,
//                        $packing_form_item->product_id,
//                        $allocation_items[$item->production_id]->band_code,
//                        $production_form_status_id,
//                        0,
//                        $packing_form_item->product->version->version_code ?? null
//                    );
//
//                    $production_form_item->forecast_amount = $form_item_sum->amount;
//                    $production_form_item->amount = $form_item_sum->amount;
//                    $production_form_item->final_amount = $form_item_sum->amount;
//                    $production_form_item->sub_amount = $form_item_sum->sub_amount;
//                    $production_form_item->packing_form_item_id = $packing_form_item->id;
//                    $production_form_item->save();
//
//                    $production_form_item->AddLotItem($packing_form_item->lot_number_id, $machineLog);
//                }
//
//            }

        } else {

            if (!isset($request->data["input"]) && $input_number == 0) {
                return [
                    "result" => true,
                    "error" => "هیچ ردیفی برای تزریق مواد اولیه وارد نشده است و حداقل ورودی هم صفر است."
                ];
            }
            if (!isset($request->data["input"])) {
                return [
                    "result" => false,
                    "error" => "هیچ ردیفی برای تزریق مواد اولیه وارد نشده است."
                ];
            }
            $data = $request->data["input"];
            $current_machine_input = CurrentMachineInput::
            whereIn("id", array_keys($data))->
            where("allocation_id", $allocation->id)->
            get();
            if (count($current_machine_input) == 0) {
                return [
                    "result" => false,
                    "error" => "هیچ بسته بندی جهت ثبت در فرم تولید جاری ماشین، انتخاب نشده است، لطفا یکبار دیگر تلاش کنید."
                ];
            }
            foreach ($current_machine_input as $item) {
                $count_production = PackingFormItem::
                join("production_form_item", "production_form_item_id", "production_form_item.id")->
                where("packing_form_id", $item->packing_form_id)->
                get();
                //   pluck("production_id","production_id")->toArray();

                if (count($count_production) > 1) {
                    return [
                        "result" => false,
                        "error" => "با توجه به اینکه بسته بندی " . $item->packing_form->code . " دارای بیش از یک آیتم می باشد، امکان تشخیص کارت تولید مرتبط با آن وجود ندارد، لطفا با واحد پشتیبانی تماس بگیرید. "
                    ];
                }

            }

            if ($value_209 == 3) {
                // مقدار فرم تولید تحویل شده را به درخواست اضافه می کنیم.
                if (count($current_machine_input) != 1) {
                    return [
                        "result" => false,
                        "error" => "محاسبه مقدار مواد اولیه براساس مقدار مواد اولیه تحویل داده شده به ماشین ( متغیر ۷۳۰۳۰۰۱۱۲۰۹ در تنظیمات گروه ماشین) می باشد، و آیتم های ورودی بیش از یک ردیف دارد، امکان ثبت فرم تولید وجود ندارد. "
                    ];
                }

                $product_request_form = ProductRequestForm::where([
                    "allocation_id" => $allocation->id,
                    "status_id" => 7005002
                ])->first();
                if (!$product_request_form) {
                    $product_request_forms = ProductRequestForm::where([
                        "allocation_id" => $allocation->id,
                    ])->get();
                    $message = "";
                    foreach ($product_request_forms as $prf) {
                        $message .= $prf->code . " (" . $prf->status->caption . ")";
                    }
                    return [
                        "result" => false,
                        "error" => "هیچ درخواست کالا از انبار برای تخصیص شماره " . $allocation->id . " یافت نشد، برای تایید فرم می بایست یک درخواست کالا از انبار برای تخصیص با وضعیت تحویل شده وجود داشته باشد." .
                            "<br/>" . $message
                    ];
                }

                $form_ids = ProductRequestFormForm::join("forms", "form_id", "forms.id")->
                where([
                    "product_request_form_id" => $product_request_form->id,
                    "forms.status_id" => 500000200
                ])->pluck("forms.id");

                if (count($form_ids) == 0) {
                    return [
                        "result" => false,
                        "error" => "هیچ برگ خروج تایید شده ای برای درخواست " . $product_request_form->code . " وجود ندارد، لطفا قبل از شروع عملیات از تایید همه برگ های خروج اطمینان حاصل کنید."
                    ];
                }

                $form_items_sum = FormItem::whereIn("form_id", $form_ids)->
                selectRaw("sum(amount) as amount, sum(sub_amount) as sub_amount, product_id,lot_number_id")->
                groupBy("product_id")->
                get();

                if (count($form_items_sum) != 1) {
                    return [
                        "result" => false,
                        "error" => "تعداد آیتم های برگ های خروج درخواست خروج از انبار " . $product_request_form->code() . " نامعتبر است، "
                    ];
                }
                $form_item_sum = $form_items_sum[0];

                // چون پیش فرض گرفتیم که فقط یک آیتم تخصیص داریم، بنابراین فقط یک ردیف فرم تولید می بایست اضافه کنیم.
                $production_id = $allocation->items()->first()->production_id;
                $production_form_item = ProductionFormItem::AddNewItem(
                    $allocation->id,
                    $current_production_form->id,
                    $production_id,
                    $form_item_sum->product_id,
                    1,
                    $production_form_status_id,
                    0,
                    null
                );
                $production_form_item->forecast_amount = $form_item_sum->amount;
                $production_form_item->amount = $form_item_sum->amount;
                $production_form_item->final_amount = $form_item_sum->amount;
                $production_form_item->sub_amount = $form_item_sum->sub_amount;
//                $production_form_item->packing_form_item_id = $packing_form_item->id;
                $production_form_item->save();

                $production_form_item->AddLotItem($form_item_sum->lot_number_id, $machineLog);

                // مقدار ردیف فرم تولید را بروز می کنیم.
                CurrentMachineInput::
                whereIn("id", array_keys($data))->
                where("allocation_id", $allocation->id)->
                where("material_id", $form_item_sum->product_id)->
                update(["amount_required" => $form_item_sum->amount]);

                MachineAllocationController::UpdateMaterialFlowV209_3($allocation, $machine, $production_form_item);


            } else {
                // اضافه کردن یک رکورد در فرم تولید
                foreach ($current_machine_input as $item) {

                    //با توجه به نوع تخلیه و نوع حرکت مواد تصمیم می گیریم که بسته بندی ها به چه شکلی به فرم تولید اضافه شوند.
                    $result_discharge = PackingForm::GetFirstStackDisplayOrder($item->packing_form->packing_type, $machine_operation_discharge_type_id);
                    if (!$result_discharge["result"]) {
                        return $result_discharge;
                    }

                    foreach ($item->packing_form->items()->orderBy("id", $result_discharge["asc_or_desc"])->get() as $packing_form_item) {


                        $production_form_item = ProductionFormItem::AddNewItem(
                            $allocation->id,
                            $current_production_form->id,
                            $item->production_id,
                            $packing_form_item->product_id,
                            isset($band_code_list[$item->production_id]) ? $band_code_list[$item->production_id] : 1,
                            $production_form_status_id,
                            0,
                            $packing_form_item->product->version->version_code ?? null
                        );
                        $production_form_item->forecast_amount = $packing_form_item->final_amount;
                        $production_form_item->amount = $packing_form_item->final_amount;
                        $production_form_item->final_amount = $packing_form_item->final_amount;
                        $production_form_item->sub_amount = $packing_form_item->sub_amount;
                        $production_form_item->packing_form_item_id = $packing_form_item->id;
                        $production_form_item->save();

                        $production_form_item->AddLotItem($packing_form_item->lot_number_id, $machineLog);

                    }
                }
            }

        }
        event(new  ProductionFormLogEvent(
            $current_production_form,
            7302001 // تزریق مواد اولیه
        ));
        return [
            "result" => true,
            "production_form" => $current_production_form
        ];
    }

    public static function GetCurrentProductionFrom(Request $request, Machine $machine, MachineLog $machineLog, $production_form_status_id)
    {
        $is_new_form = false;
        $min_stack_display_number = 1;
        $max_stack_display_number = 1;
        $current_production_form = $machine->getCurrentProductionForm();
        if (!$current_production_form) {
            // بررسی وضعیت بسته بندی
            $result_packing_type = self::CheckPackingTypeAndCarrier($request);
            if (!$result_packing_type["result"]) {
                return $result_packing_type;
            }
            $packing_type = $result_packing_type["packing_type"];
            $carrier = $result_packing_type["carrier"];
            // ایجاد فرم تولید جدید در صورتی که ماشین فرم جاری ندارد.

            $current_production_form = ProductionForm::AddNewForm($machine->id, $carrier->id ?? null, $machineLog->id, $packing_type->id, null, $production_form_status_id);
            if ($carrier) {
                $carrier->SetStatus(5320006, $log_message = null, $event_id = 5320113, $product_id = null, $machine_id = $machine->id, $contractor_id = null, $user_id = Auth::id());
            }
            $is_new_form = true;

        } else {
            // فرم از قبل وجود داشته است و باید ترتیب نمایش کوچکتر و بزرگتر را به دست آوریم.
            $min_stack_display_number = $current_production_form->items()->min("stack_display_number");
        }

        return [
            "result" => true,
            "production_form" => $current_production_form,
            "is_new_form" => $is_new_form,
            "min_stack_display_number" => $min_stack_display_number,
            "max_stack_display_number" => $max_stack_display_number
        ];
    }

    public static function GetValue203Next(MachineAllocation $machine_allocation)
    {

        $new_line_product_station = LineProductStation::where([
            "product_id" => $machine_allocation->product_id,
            "product_route_id" => $machine_allocation->line_product_station->product_route_id,
            "status_id" => 1200
        ])->
        // اولویت بالاتر وجود داشته باشد.
        where("priority_number", ">", $machine_allocation->line_product_station->priority_number)->
        // از همین گروه ماشین نیست.
        where("machine_type_id", "!=", $machine_allocation->machine->machine_type_id)->
        // باید عملیات هم فرق داشته باشد
        orderBy("priority_number")->
        first();

        if ($new_line_product_station) {
            $value_203_next = MachineModuleTypePropertyValue::getValue("73030011203", $new_line_product_station->machine_type_id);
        } else {
            $value_203_next = 0;
        }

        return [
            "value_203_next" => $value_203_next,
            "next_line_product_station" => $new_line_product_station->id ?? 0
        ];
    }
}

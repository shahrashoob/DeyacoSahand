<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\ProductionForm;

use App\Events\Form\PackingLogEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\Form\Packing\PackingFormLayer;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Machine\Allocation\AllocationDoffs;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationMaterialConsumed;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineStatus;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\Option;
use App\Models\Utility\QueueOfLargeOperation;
use App\Models\Utility\Setting;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FabricExtractionController extends Controller
{
    public static $info = [
        "route" => "fabric_raw.jacquard.production_form.fabric_extraction.",
        "enable_status" => ["از کلاس بالاتر گرفته می شود."],
        "button" => ["caption" => "استخراج پارچه", "class" => "btn-primary"],
        "view_path" => "goods_kind_process.fabric_raw.jacquard.production_form.fabric_extraction.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "production.production_form.index";

    public function __construct()
    {
        $this->route_path = FabricExtractionController::$info["route"];
        $this->view_path = FabricExtractionController::$info["view_path"];
    }

    public function index(ProductionForm $production_form)
    {

        $result = $this->checkPermission($production_form);
        if ($result != "") {
            return $result;
        }

        // باید قبل از ورود به این صفحه پرینتر پیش فرض اوکی شده باشد تا پرینت ها به مشکل بر نخورند
        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }

        $contour_1_value = session("contour_1_value" . $production_form->id);
        $contour_2_value = session("contour_2_value" . $production_form->id);
        $contour_3_value = session("contour_3_value" . $production_form->id);
        $contour_4_value = session("contour_4_value" . $production_form->id);
        $contour_5_value = session("contour_5_value" . $production_form->id);
        $fabric_raw_type_of_cut_id = session("fabric_raw_type_of_cut_id" . $production_form->id);
        $carrier_id = session("carrier_id" . $production_form->id);
        $shift_work_id = session("shift_work_id" . $production_form->id);

        $shift_work_option = Option::get("shift_work", $shift_work_id);
        $type_of_cut_option = Option::get("fabric_raw_type_of_cut", $fabric_raw_type_of_cut_id ?? 2);

        $machine_status = MachineStatus::where([
            "machine_module_type_id" => $production_form->machine->machine_type->machine_module_type_id,
            "production_status_id" => $production_form->machine->production_status_id
        ])->first();

        if ($machine_status && !$machine_status->possibility_of_extraction_production_form) {
            return redirect()->route($this->dashboard_route)->withErrors("وضعیت ماشین جهت استخراج پارچه معتبر نمی باشد.");
        }
        $last_log = MachineLog::where("machine_id", $production_form->machine->id)->orderByDesc("id")->first();
        if ($last_log && $last_log->machine_event_type_id == 650) { // در حال تحویل شیفت
            return back()->withErrors("با توجه به اینکه ماشین " . $production_form->machine->caption . " در حال تحویل شیفت می باشید، امکان استخراج وجود ندارد، لطفا به اپراتور مسئول (" . $last_log->operator->fullname() . ") جهت تایید تحویل شیفت اطلاع دهید.");
        }

        $latest_production_form_item = $production_form->getLatestItem();
        if (!$latest_production_form_item) {
            return back()->withErrors("آخرین رکورد فرم تولید یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
        }
        $allocation_item = MachineAllocation::where([
            "allocation_id" => $latest_production_form_item->allocation_id,
            "production_id" => $latest_production_form_item->production_id,
            "machine_id" => $production_form->machine_id
        ])->
        where("status_id", "!=", 5310005)-> // معلق
        orderByDesc("id")->
        first();

        if (!$allocation_item) {
            return back()->withErrors("تخصیص آخرین رکورد فرم تولید یافت نشد، لطفا با پشتیبانی تماس بگیرد.");
        }

        // اگر آخرین فرم تولید از یک کارت تولید باشه، باید حتما وضعیت آن  در حال بافت بخش پایانی پارچه باشد
        if (
            $allocation_item->number_of_doffs_done == $allocation_item->max_number_of_doffs - 1 &&
            $production_form->status_id != 7002008 // در حال بافت بخش پایانی پارچه
        ) {
            return back()->withErrors("لطفا ابتدا پایان بافت کارت تولید را ثبت نموده و سپس نسبت به استخراج پارچه اقدام نمایید.");

        }

        // چک کردن اینکه هیچ بسته بندی دیگری در انتظار تغییر بسته بندی وجود نداشته باشد.
        $result_change = self::CheckChangePackingForm($production_form);
        if (!$result_change["result"]) {
            return back()->withErrors($result_change["error"]);
        }

        /** فرم تولید رزرو*/
        $reserve_production_form_count = ProductionForm::where([
            "machine_id" => $production_form->machine_id,
            "status_id" => 7002011 // در انتظار بارگذاری
        ])->count();

        // بررسی وزن حامل
        // اگر حامل قابل شماره گذاری است باید وزن آن ثبت شده باشد
        $carrier_weight_result = CarrierType::getWeight($production_form->carrier->carrier_type, $production_form->carrier);
        if ($production_form->machine->check_inventory_for_allocation && !$carrier_weight_result["result"]) {
            return back()->withErrors($carrier_weight_result["error"]);

        }

        return view($this->view_path . "index", compact(
            "allocation_item",
            "production_form",
            "reserve_production_form_count",
            "shift_work_option",
            "type_of_cut_option",
            "carrier_id",
            "contour_1_value",
            "contour_2_value",
            "contour_3_value",
            "contour_4_value",
            "contour_5_value",
        ));

    }

    public function submit(Request $request, ProductionForm $production_form)
    {


        $result = $this->checkPermission($production_form);
        if ($result != "") {
            return $result;
        }


        // کارخانه دارای واحد کنترل کیفیت می باشد
        $has_grading_and_control = Setting::getIntegerValue("has_grading_and_control");

        // دریافت قطب ها
        $last_row_log = MachineLog::getLastLogWithContour($production_form->machine);

        $contour_result = $last_row_log->checkMinContour(
            $request->contour_1_value,
            $request->contour_2_value,
            $request->contour_3_value,
            $request->contour_4_value,
            $request->contour_5_value);
        if (
            isset($last_row_log) && !$contour_result["result"]
        ) {
            return redirect()->back()->withErrors($contour_result["error"]);
        }


        $latest_production_form_item = $production_form->getLatestItem();
        if (!$latest_production_form_item) {
            return back()->withErrors("آخرین رکورد فرم تولید یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
        }
        $allocation_item = MachineAllocation::where([
            "allocation_id" => $latest_production_form_item->allocation_id,
            "production_id" => $latest_production_form_item->production_id,
            "machine_id" => $production_form->machine_id
        ])->
        where("status_id", "!=", 5310005)-> // معلق
        orderByDesc("id")->
        first();

        if (!$allocation_item) {
            return back()->withErrors("تخصیص آخرین رکورد فرم تولید یافت نشد، لطفا با پشتیبانی تماس بگیرد.");
        }


        if ($allocation_item->number_of_doffs_done < $allocation_item->max_number_of_doffs - 1 && $request->fabric_raw_type_of_cut_id == 1) {
            return back()->withErrors("برای استخراج پارچه در این داف، محل استخراج پارچه الزاما باید از چروک گیر باشد.");

        }

        /** فرم تولید رزرو*/
        $reserve_production_form_count = ProductionForm::where([
            "machine_id" => $production_form->machine_id,
            "status_id" => 7002011 // در انتظار بارگذاری
        ])->count();

        if ($allocation_item->number_of_doffs_done < $allocation_item->max_number_of_doffs - 1 && $reserve_production_form_count == 0) {
            // پیدا کردن نوع حامل از روی نوع بسته بندی
            $first_layer = $production_form->packing_type->layers->where("layer_code", 1)->first();
            if (!$first_layer) {
                return redirect()->route($this->route_path . "index")->withErrors("تعریف نوع حامل در  بسته بندی معتبر نمی باشد، لطفا با پشتیبانی تماس بگیرید.");

            }

            $result = Carrier::firstOrCreate($request->carrier_id, $first_layer->carrier_type_id, 5320001, null);

            $carrier = isset($result["carrier"]) ? $result["carrier"] : null;

            // اگر شماره حامل، همان شماره حاملی باشد که روی ماشین است، باید تاییدیه بگیرد که شماره حامل وارد شده صحیح است.
            if ($carrier && $carrier->status_id == 5320006 && $carrier->id == $production_form->carrier_id && !session("carrier_id" . $production_form->id)) {
// اگر حامل قبلی با حامل فعلی یکی باشد، باید تاییدیه بگیرد.
                session([
                    "contour_1_value" . $production_form->id => $request->contour_1_value,
                    "contour_2_value" . $production_form->id => $request->contour_2_value,
                    "contour_3_value" . $production_form->id => $request->contour_3_value,
                    "contour_4_value" . $production_form->id => $request->contour_4_value,
                    "contour_5_value" . $production_form->id => $request->contour_5_value,
                    "fabric_raw_type_of_cut_id" . $production_form->id => $request->fabric_raw_type_of_cut_id,
                    "carrier_id" . $production_form->id => $request->carrier_id,
                    "shift_work_id" => $request->shift_work_id

                ]);

                return back();
            } else {
                // یا سشن حامل ست نشده باشد، و اگر ست شده، باید مقدار آن با مقدار ورودی جدید یکی باشد تا چک نشود،
                // در غیر این صورت باید چک کنیم اگر حامل جدید همان حامل با حامل فرم تولید یکی است، رد بشده در غیر این صورت نتیجه را چک کند.
                if (!session("carrier_id" . $production_form->id) || session("carrier_id" . $production_form->id) != ($production_form->carrier_id ?? 0)) {
                    if (!$result["result"]) {
                        return redirect()->back()->withErrors($result["message"]);
                    }
                }
            }

            // اگر حامل قابل شماره گذاری است باید وزن آن ثبت شده باشد
            $carrier_weight_result = CarrierType::getWeight($carrier->carrier_type, $carrier);
            if ($production_form->machine->check_inventory_for_allocation && !$carrier_weight_result["result"]) {
                return redirect()->back()->withErrors($carrier_weight_result["error"]);
            }

        }

// اگر حامل قبلی با حامل فعلی یکی باشد، این متغیر T می باشد.
        $is_the_same_carrier = (isset($carrier) && $carrier->id == $production_form->carrier_id) ? 1 : 0;
        session([
            "contour_1_value" . $production_form->id => null,
            "contour_2_value" . $production_form->id => null,
            "contour_3_value" . $production_form->id => null,
            "contour_4_value" . $production_form->id => null,
            "contour_5_value" . $production_form->id => null,
            "fabric_raw_type_of_cut_id" . $production_form->id => null,
            "carrier_id" . $production_form->id => null,
            "shift_work_id" . $production_form->id => null,

        ]);


        /** فرم تولید رزرو*/
        $reserve_production_form = ProductionForm::where([
            "machine_id" => $production_form->machine_id,
            "status_id" => 7002011 // در انتظار بارگذاری
        ])->first();

        $is_the_same_carrier = $is_the_same_carrier || ($reserve_production_form && $reserve_production_form->carrier_id == $production_form->carrier_id);

        // به دست آوردن وضعیت بسته بندی
        $packing_form_status_id = self::GetPackingFormStatus($production_form, $is_the_same_carrier, $has_grading_and_control);


        // چک کردن اینکه هیچ بسته بندی دیگری در انتظار تغییر بسته بندی وجود نداشته باشد.
        $result_change = self::CheckChangePackingForm($production_form);
        if (!$result_change["result"]) {
            return back()->withErrors($result_change["error"]);
        }

        // اگر درجه بندی نداریم، باید درجه اصلی در رسته کالایی را انتخاب کنیم
        $main_degree = Degree::where([
            "degree_type_id" => 1, // درجه اصلی
            "goods_kind_id" => 4, // پارچه خام
            "active_status_id" => 1200, // فعال
        ])->first();
        if (!$main_degree) {
            return back()->withErrors("درجه اصلی در رسته کالایی پارچه خام مشخص نشده است.");
        }

        $machineLog = MachineLog::create([]);
        $machineLog->machine_event_type_id = 450;
        $machineLog->contour_1_value = $request->contour_1_value * $contour_result["ratio"];
        $machineLog->contour_2_value = $request->contour_2_value * $contour_result["ratio"];
        $machineLog->contour_3_value = $request->contour_3_value * $contour_result["ratio"];
        $machineLog->contour_4_value = $request->contour_4_value * $contour_result["ratio"];
        $machineLog->contour_5_value = $request->contour_5_value * $contour_result["ratio"];
        $machineLog->shift_work_id = $request->shift_work_id;
        $machineLog->save();
        /************************************************************/
// متراژ کل بافته شده همراه با فرم جاری
        $woven_amount = 0;

        foreach ($production_form->items()->where("allocation_id", $allocation_item->allocation_id)->get() as $item) {
           // $woven_amount -= $item->amount;
            $woven_amount += GoodsKind::getAmountFromMachineLog($item->product, $item->start_machine_log, $machineLog);

//                echo "amount".$item->amount.", other=". GoodsKind::getAmountFromMachineLog($item->product, $item->start_machine_log, $machineLog)."<br/>";
        }
        // اگر مجوز نداشت پیام بدهد.
        $allow_diff_percent = $allocation_item->product->goods_kind->min_diff_of_production_and_allocation_in_the_end_of_production;
        $amount_of_each_doffs = $allocation_item->amount_of_each_doffs;
        if (
            $amount_of_each_doffs * (1 - $allow_diff_percent / 100) > $woven_amount &&
            !GoodsKindProcess\FabricRaw\Jacquard\Machine\EndOfProductionCardTextureController::
            HasSpecialLicense($allocation_item->allocation)
        ) {

            $machineLog->delete();


            return back()->withErrors("مقدار بافت به حد نصاب نرسیده است، لازم است تا " . round($amount_of_each_doffs - $woven_amount, 1) . " متر دیگر  بافته شود.");
        }
      
        event(new MachineLogEvent($production_form->machine, $machineLog, "", true, $last_row_log));

        // قطب استخراج پارچه
        $production_form->extraction_machine_log_id = $machineLog->id;
        $production_form->save();


        // برورز رسانی نوع برش پارچه و فاصله شانه تا چروک گیر برای استخراج
        $production_form->updateExtraAmountForExtraction($request->fabric_raw_type_of_cut_id);

        // بروز رسانی مقدار فرم ها
        foreach ($production_form->items as $item) {
            $item->updateItemAmount(true, true);
        }

        // وضعیت داشتن یا نداشتن واحد کنترل و درجه بندی
        $carrier_status_id = $is_the_same_carrier ?
            5320012 //  پر در انتظار تغییر بسته بندی
            :
            (
            $has_grading_and_control ?
                5320013  //  پر شده در انتظار کنترل کیفیت
                :
                5320012 //  پر در انتظار تغییر بسته بندی
            );


        $production_form->status_id = DashboardController::$perfix_status_code . "006";// بسته بندی شده
        $production_form->save();


        // ایجاد فرم بسته بندی
        $packing_form = PackingForm::create([
            "packing_type_id" => $production_form->packing_type_id,
            "carrier_id" => $production_form->carrier_id,
            "status_id" => $packing_form_status_id,
            "degree_id" => $main_degree->id
        ]);

        event(new PackingLogEvent($packing_form, 7007001));

        // لاگ حامل
        $production_form->carrier->SetStatus(
            $carrier_status_id,
            null,
            5320105,
            null,
            $production_form->machine->id,
            null,
            null,
            $packing_form->id
        );

        // چاپ بسته بندی

// ثبت درخواست پرینت بسته بندی ها
        $data_print = [
            "packing_form_ids" => [$packing_form->id],
            "worker_id" => Auth::id(),
        ];
        QueueOfLargeOperation::AddToQueue($data_print, 300);


        foreach ($production_form->items as $production_form_item) {

            $production_form_item->amount_after_control = $production_form_item->amount;
            $production_form_item->final_amount = $production_form_item->amount;
            $production_form_item->sub_amount = $production_form_item->amount *
                $production_form_item->product->weight;
            $production_form_item->save();

            foreach ($production_form_item->lot_numbers as $production_form_item_lot_number) {

                // اگر قبلا توسط استخراج آیتم ها استخراج شده است، دیگر نیاز نیست که بسته بندی شود.
                if (!$production_form_item_lot_number->getPackingFormItem() && $production_form_item_lot_number->amount > 0) {
                    // به ازای هر آیتم بسته بندی یک ردیف ایجاد می کنیم
                    $ini_sub_amount = $production_form_item_lot_number->amount *
                        $production_form_item->product->weight;

                    // واحد فرعی 2 برای پارچه
                    $sub_amount2 = $production_form_item->product->frame_ratio_unit2 ?
                        floor($production_form_item_lot_number->amount / $production_form_item->product->frame_ratio_unit2) : null;


                    $packing_form_item = PackingFormItem::create([
                        "packing_form_id" => $packing_form->id,
                        "production_form_item_id" => $production_form_item->id,
                        "production_form_item_lot_number_id" => $production_form_item_lot_number->id,
                        "fabric_raw_grading_id" => null,
                        "product_id" => $production_form_item->product_id,
                        "lot_number_id" => $production_form_item_lot_number->lot_number_id,
                        "degree_id" => $main_degree->id,
                        "amount" => $production_form_item_lot_number->amount,
                        "amount_after_control" => $production_form_item_lot_number->amount,
                        "final_amount" => $production_form_item_lot_number->amount,
                        "sub_amount" => $ini_sub_amount,
                        "init_sub_amount" => $ini_sub_amount,
                        "sub_amount2" => $sub_amount2
                        ,
                        "status_id" => 7006003, // بسته بندی شده
                        "band_code" => $production_form_item->band_code,
                        "version_code"=>$production_form_item->version_code??null,
                    ]);
                    $packing_form_item->getCode($production_form_item->band_code, 1);


                    // به ازای هر آیتم اطلاعات باند و حامل را ذخیره می کنیم
                    PackingFormLayer::create([
                        "packing_form_item_id" => $production_form_item->id,
                        "carrier_id" => $production_form->carrier_id,
                        "band_code" => $production_form_item->band_code,
                        "layer_code" => 1
                    ]);
                }
            }
        }


        event(new ProductionFormLogEvent(
            $production_form,
            7002005 // استخراج پارچه
        ));


        // مقدار داف را با توجه به آخرین داف بروز می کنیم.
        $result = AllocationDoffs::DoffDownAndGetNextDoff($allocation_item->allocation_id);

        // یکی به تعداد داف های هر باند اضافه شود.
        foreach ($allocation_item->allocation->items as $a_item) {
            // برای اینکه تعداد داف اضافه ثبت نشود، و برای تخصیص های متولی یک کارت روی یک ماشین مشکلی پیش نیاید.
            if ($a_item->max_number_of_doffs >= $a_item->number_of_doffs_done + 1) {
                $a_item->number_of_doffs_done = $a_item->number_of_doffs_done + 1;
                if ($result["new_amount_of_each_doffs"] != null) {
                    $a_item->amount_of_each_doffs = $result["new_amount_of_each_doffs"];
                }

                $a_item->save();
            }

        }


        // گرفتن دوباره تعداد داف
        // آخرین کارت تولید - ماشین همیشه برای آخرین تخصیص کارت است.
        // تخصیص های قبلی فرم تولید قبلا در پایان بافت خاتمه یافته شده اند.
        //اگر هم کارت کنسل شده باشد که اصلا نیاز به استخراج پارچه ندارد.
        $allocation_item = MachineAllocation::where([
            "allocation_id" => $latest_production_form_item->allocation_id,
            "production_id" => $latest_production_form_item->production_id,
            "machine_id" => $production_form->machine_id
        ])->
        where("status_id", "!=", 5310005)-> // معلق
        orderByDesc("id")->
        first();

        // خاتمه یافته کردن کارت تولید در صورت نیاز
        FabricRaw::ProductionTerminated($allocation_item);


        // اگر تعداد داف های انجام شده به حد نصاب نرسیده
        if ($allocation_item->number_of_doffs_done < $allocation_item->max_number_of_doffs && $reserve_production_form_count == 0) {

            if (!isset($production_form_new)) {


                $carrier_status_id = $is_the_same_carrier ?
                    5320012 //  پر در انتظار تغییر بسته بندی
                    :
                    5320006   //  پر در حال تکمیل
                ;
                // حامل در حال تکمیل
                $carrier->SetStatus($carrier_status_id, $production_form->machine->fullCaption(), 5320105, null, $production_form->machine->id);

                // ایجاد فرم تولید جدید
                $production_form_new = ProductionForm::AddNewForm(
                    $production_form->machine->id,
                    $carrier->id,
                    $machineLog->id,
                    $production_form->packing_type_id
                );

                // از آنجایی که این فرم قطب شروع در انتظار بارگذاری ندارد، بنابراین آن را نال و فقط شروع در حال بافت را قرار می دهیم.
                $production_form_new->fabric_raw_type_of_cut_for_create_form_id = $request->fabric_raw_type_of_cut_id;
                $production_form_new->in_the_weaving_machine_log_id = $machineLog->id;
                $production_form_new->start_machine_log_id = 0; // طبق الگوریتم باید حساب شود
                $production_form_new->save();

            }
            foreach ($allocation_item->allocation->items as $a_item) {

                ProductionFormItem::AddNewItem(
                    $a_item->allocation_id,
                    $production_form_new->id,
                    $a_item->production_id,
                    $a_item->product_id,
                    $a_item->band_code,
                    7002001, // در حال تکمیل
                    $allocation_item->amount_of_each_doffs,
                    $a_item->version_code??null
                );
                $carrier->addProduct($item->product_id);
            }

            // ثبت مقدار مصرف
            MachineAllocationMaterialConsumed::registerNewConsumed($allocation_item->allocation, $production_form->machine, null, $machineLog);

        } else {
            // اگر فرم رزرو موجود باشد.

            if ($reserve_production_form) {
                // قطب شروع در حال بافت
                $reserve_production_form->in_the_weaving_machine_log_id = $machineLog->id;
                // نوع استخراج(برش) پارچه در هنگام تولید فرم
                $reserve_production_form->fabric_raw_type_of_cut_for_create_form_id = $request->fabric_raw_type_of_cut_id;
                $reserve_production_form->save();
                $reserve_production_form->ChangeStatus(7002001, "", 7002017); // در حال بافت

                $carrier_status_id = $is_the_same_carrier ?
                    5320012 //  پر در انتظار تغییر بسته بندی
                    :
                    5320006   //  پر در حال تکمیل
                ;
                $reserve_production_form->carrier->SetStatus($carrier_status_id, null, 5320105, null, $production_form->machine->id); // پر در حال تکمیل

                foreach ($reserve_production_form->items as $reserve_production_form_item) {
                    $reserve_production_form_item->status_id = 7002001; // در حال بافت
                }
            }
        }

        // بروزرسانی لات پارچه
        FabricRaw::ChangeLot($allocation_item->allocation);


        return redirect()->route($this->dashboard_route)->with(["success" => "عملیات با موفقیت انجام شد."]);

    }


    public static function CheckChangePackingForm(ProductionForm $production_form)
    {
        $packing_form_in_change_status_ids = PackingForm::where("status_id", 7007013)->pluck("id")->toArray();
        if (count($packing_form_in_change_status_ids) > 0) {
            $packing_form_in_change_status_ids_in_machine = PackingFormItem::
            join("production_form_item", "production_form_item.id", "production_form_item_id")->
            join("production_forms", "production_forms.id", "production_form_id")->
            where("packing_form_id", $packing_form_in_change_status_ids)->
            where("machine_id", $production_form->machine_id)->
            pluck("packing_form_id")->
            toArray();

            if (count($packing_form_in_change_status_ids_in_machine) > 0) {
                $packing_form_in_change_status = PackingForm::whereIn("id", $packing_form_in_change_status_ids_in_machine)->first();
                return [
                    "result" => false,
                    "error" =>
                        "بسته بندی " . $packing_form_in_change_status->getCode() . " در وضعیت در انتظار تغییر بسته بندی می باشد، لطفا ابتدا آن را تغییر دهید."
                ];
            }
        }

        return [
            "result" => true
        ];
    }

    public static function GetPackingFormStatus(ProductionForm $production_form, $is_the_same_carrier, $has_grading_and_control)
    {

        return $is_the_same_carrier ?
            7007013 //  پر در انتظار تغییر بسته بندی
            :
            (
            $has_grading_and_control ?
                (
                7007026 // در انتظار کنترل کیفیت
                ) :
                ( // اگر موجودی انبار برای ماشین چک می شود، باید وزن خالص و ناخالص بسته بندی تکمیل گردد
                $production_form->machine->check_inventory_for_allocation ?
                    7007020 : //در انتظار تکمیل اطلاعات بسته بندی
                    7007005 // در انتظار تحویل به انبار
                )
            );

    }

    public function checkPermission(ProductionForm $production_form)
    {

        $result = DashboardController::checkPermissionConditions($production_form, GoodsKindProcess\FabricRaw\ProductionForm\FabricExtractionController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}

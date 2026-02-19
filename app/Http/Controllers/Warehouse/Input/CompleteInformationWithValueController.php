<?php

namespace App\Http\Controllers\Warehouse\Input;

use App\Events\Form\PackingLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\PrintQRController;
use App\Http\Controllers\Utility\Script\Script1008Controller;
use App\Http\Controllers\Warehouse\DashboardController;
use App\Models\Accounting\CostCenter;
use App\Models\Form\Form;
use App\Models\Form\FormGeneralItem;
use App\Models\Form\FormGeneralItemPackingForm;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Order\OppKind;
use App\Models\Order\TransKind;
use App\Models\Utility\Option;
use App\Models\Utility\SmartObject;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProductBlock;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompleteInformationWithValueController extends Controller
{

    public $view_path = "warehouse.input.complete_information_with_value.";
    public $route_path = "wh.input.complete_information_with_value.";


    public function index(Form $form)
    {
        $result = DashboardController::check_permission($form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        // بررسی اینکه مقدار کالا در زمان ورود به انبار چک شود


        // بررسی اینکه کالا در انبارگردانی نباشد
        $warehouse_ids = [];
        $warehouse_ids[] = $form->warehouse_id;

        $product_ids_for_check = $form->general_items()->pluck("product_id")->toArray();
        $result_warehouse = WarehouseProductBlock::CheckProduct($warehouse_ids, $product_ids_for_check);

        if (!$result_warehouse["result"]) {
            $error_message = $result_warehouse["error"];
            return back()->withErrors($error_message);

        }

        if ($form->status_id != 500000430) {
            return back()->withErrors("وضعیت فرم در انتظار تکمیل اطلاعات نمی باشد.");
        }
        if (count($form->general_items) == 0) {
            return back()->withErrors("برای فرم ورود هیچ آیتمی برای تفکیک وجود ندارد.");
        }

        //چک کردن پرینتر
        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }

        $show_enter_unit_amount = true;
        $product_reservoirs = [];
//        foreach ($form->general_items as $form_general_item) {
//            if ($form_general_item->product->unit->weight_conversion_rate > 0) {
//                $show_enter_unit_amount = false;
//            }
//
//        }

        $route_path = $this->route_path;
        return view($this->view_path . "index", compact('form', 'route_path'));
    }


    public function quality_control(FormGeneralItem $form_general_item)
    {
        $result = DashboardController::check_permission($form_general_item->form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }
        $product = $form_general_item->product;

        /************************************/
        // گرفتن باسکول

        $url_scale = route("hr.personal.select_smart_object", [2, $this->route_path . "quality_control", $form_general_item->id]);
        $result_smart_object = SmartObject::GetScaleValue();
        if (!$result_smart_object["result"]) {
            if (isset($result_smart_object["warning"])) {
                return redirect()->route("hr.personal.select_smart_object", [2, $this->route_path . "quality_control", $form_general_item->id])->
                withErrors("با توجه به اینکه برای شما چند باسکول  تعریف شده است، لطفا یکی از باسکول ها را انتخاب نمایید.");
            } else {
                return redirect()->back()->withErrors($result_smart_object["error"]);
            }
        }
        $smart_object = $result_smart_object["smart_object"];
        /**********************************/
        $resul_check_quality = FormGeneralItemPackingForm::CheckQualityStatus($form_general_item);
        if (!$resul_check_quality["result"]) {
            return back()->withErrors($resul_check_quality["error"]);
        }
        $route_path = $this->route_path;
        $user_id = Auth::id();
        return view($this->view_path . "quality_control", compact('form_general_item', "resul_check_quality", "user_id", "route_path", "product", 'smart_object'));
    }

    public function quality_confirmed(FormGeneralItem $form_general_item)
    {
        $result = DashboardController::check_permission($form_general_item->form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }
        if ($form_general_item->status_id != 5002002) {
            return back()->withErrors("وضعیت فرم انبار جهت تایید کنترل کیفی نامعتبر است.");
        }

        $result = FormGeneralItemPackingForm::CheckQualityStatus($form_general_item);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }
        $form_general_item->status_id = 5002006; // در انتظار تخلیه بار
        $form_general_item->kg_in_meter = $result["mean"]; // در انتظار تخلیه بار
        $form_general_item->save();

        event(new FormLogEvent($form_general_item->form, "تایید کنترل کیفی"));

        return redirect()->route($this->route_path . "separation", $form_general_item)->with(["success" => "تایید کنترل کیفی با موفقیت انجام شد."]);
    }

    public function separation(FormGeneralItem $form_general_item)
    {
        $result = DashboardController::check_permission($form_general_item->form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }
        $product = $form_general_item->product;

        /************************************/
        // گرفتن باسکول

        $url_scale = route("hr.personal.select_smart_object", [2, $this->route_path . "quality_control", $form_general_item->id]);
        $result_smart_object = SmartObject::GetScaleValue();
        if (!$result_smart_object["result"]) {
            if (isset($result_smart_object["warning"])) {
                return redirect()->route("hr.personal.select_smart_object", [2, $this->route_path . "quality_control", $form_general_item->id])->
                withErrors("با توجه به اینکه برای شما چند باسکول  تعریف شده است، لطفا یکی از باسکول ها را انتخاب نمایید.");
            } else {
                return redirect()->back()->withErrors($result_smart_object["error"]);
            }
        }
        $smart_object = $result_smart_object["smart_object"];
        /**********************************/

        $route_path = $this->route_path;
        $user_id = Auth::id();
        return view($this->view_path . "separation", compact('form_general_item', "user_id", "route_path", "product", 'smart_object'));

    }

    public function end_of_separation(FormGeneralItem $form_general_item)
    {
        $result = DashboardController::check_permission($form_general_item->form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }
        $product = $form_general_item->product;
        if ($form_general_item->status_id != 5002006) {
            return back()->withErrors("وضعیت فرم انبار جهت تایید کنترل کیفی نامعتبر است.");
        }
        $packing_form_ids = $form_general_item->form_general_item_packing_form()->pluck("packing_form_id")->toArray();
        $packing_form_ids[] = -1;

        $sum_final_amount_for_checking = PackingFormItem::whereIn("packing_form_id", $packing_form_ids)->sum("final_amount");

        $allowed_percentage = $form_general_item->product->goods_kind->allowed_percentage_in_complete_form_information;
        if (
            $sum_final_amount_for_checking > (1 + $allowed_percentage / 100) * $form_general_item->amount ||
            $sum_final_amount_for_checking < (1 - $allowed_percentage / 100) * $form_general_item->amount
        ) {

            if (!isset($sum_final_amount_for_checking)) {
                return redirect()->route($this->route_path . "separation", $form_general_item)->withErrors("نوع انبار کالا (" . $form_general_item->product->capiton . ") نامعتبر است، لطفا با واحد اطلاعات پایه تماس بگیرد. ");

            }
            $message = "مقدار وارد شده توسط انبار:" . $sum_final_amount_for_checking . " " . $form_general_item->product->unit->caption . "<br/>";
            $message .= "مقدار ارسال شده توسط " . (($form_general_item->machine_allocation->contractor->caption ?? "کاربر") . " " . ($form_general_item->machine_allocation->supplier->caption ?? "")) . ": " . $form_general_item->amount . " " . $form_general_item->product->unit->caption . "<br/>";
            return redirect()->route($this->route_path . "separation", $form_general_item)->withErrors("جمع کل مقدار وارد شده برای بسته بندی ها  " . " نا معتبر است، لطفا اطلاعات " . ($form_general_item->product->unit->caption) . " را به درست وارد نمایید،" . "<br/>" . $message);

        }
        $form_general_item->status_id = 5002007; //  پایان تخلیه در انتظار ثبت تراکنش
        $form_general_item->save();

        return redirect()->route($this->route_path . "index", $form_general_item->form)->with(["success" => "تایید کنترل کیفی با موفقیت انجام شد."]);

    }

    public function confirm_form(Form $form)
    {
        $result = DashboardController::check_permission($form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }
        if (!$form->general_items->every(fn($item) => $item->status_id == 5002007)) {
            return back()->withErrors("لطفا همه ردیف های فرم ورود به انبار را به صورت کامل تخلیه نمایید و سپس نسبت به ثبت تراکنش انبار اقدام کنید.");

        }

        $description = "";
        $production_status_id = null;
        if (isset($form->allocation->contractor)) {
            $description = "دریافت کالا از طرف " . $form->allocation->contractor->caption . " - بسته بندی";
            $production_status_id = 5310106; // خاتمه یافته
        }
        if (isset($form->allocation->supplier)) {
            $description = "دریافت کالا از طرف " . $form->allocation->supplier->caption . " - بسته بندی";
            $production_status_id = 7010001; // خاتمه یافته
        }
        if (isset($form->allocation->order)) {
            $description = "دریافت امانی کالا از طرف " . $form->allocation->order->customer->caption . " - بسته بندی";
            $production_status_id = 7010001; // خاتمه یافته
        }

        foreach ($form->general_items as $form_general_item) {

            if (!$description && isset($form_general_item->customer)) {
                $description = "دریافت امانی کالا از طرف " . $form_general_item->customer->caption . " - بسته بندی";
                $production_status_id = 7010001; // خاتمه یافته
            }
            foreach ($form_general_item->form_general_item_packing_form as $form_general_item_packing_form) {
                $master_packing_form = $form_general_item_packing_form->packing_form;
                foreach ($master_packing_form->items as $master_packing_form_item) {
                    // به ازای بسته بندی اصلی یک ردیف در فرم ورود به انبار اضافه می کنیم.
                    FormItem::create([
                        "form_id" => $form->id,
                        "general_form_item_id" => $form_general_item->id,
                        "packing_form_item_id" => $master_packing_form_item->id,
                        "packing_type_id" => $master_packing_form->packing_type_id,
                        "product_id" => $master_packing_form_item->product_id,
                        "amount" => $master_packing_form_item->amount,
                        "sub_amount" => $master_packing_form_item->sub_amount,
                        "carrier_id" => null,
                        "degree_id" => $master_packing_form_item->degree_id,
                        "lot_number_id" => $master_packing_form_item->lot_number_id,
                        "description" => $description . " - " . ($master_packing_form_item->code ?? ""),


                    ]);
                }
                $master_packing_form->warehouse_id = $form->warehouse_id;
                $master_packing_form->warehouse_status_id = 4201; // بسته داخل انبار است
                $master_packing_form->status_id = 7007003; // تحویل شده به انبار
                $master_packing_form->save();
            }
            $form_general_item->status_id = 5002003;
            $form_general_item->save();
        }


        $form = Form::find($form->id); // چون آیتم های فرم تولید به صورت نادرست شناخته می شوند، یک بار دیگر فراخوانی می کنیم.
        event(new PutInWarehouseEvent($form));
        $form->status_id = 500000200;
        $form->save();
        event(new FormLogEvent($form, ""));

        return redirect()->route("wh.dashboard.index")->with(["فرم " . $form->code . " با موقیت تایید گردید."]);
    }

    public function delete_packing_form(FormGeneralItem $form_general_item, FormGeneralItemPackingForm $formGeneralItemPackingForm)
    {
        $result = DashboardController::check_permission($form_general_item->form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }
        if ($form_general_item->status_id == 5002003) {
            return back()->withErrors("با توجه به وضعیت فرم ورود به انبار امکان حذف بسته بندی وجود ندارد.");
        }
        if ($formGeneralItemPackingForm->packing_form->status_id != 7007006) { //معلق
            return back()->withErrors("با توجه به وضعیت بسته بندی امکان حذف بسته بندی وجود ندارد.");
        }

        $formGeneralItemPackingForm->packing_form->items()->delete();
        $formGeneralItemPackingForm->packing_form->delete();
        $formGeneralItemPackingForm->delete();

        return back()->with(["success" => "حذف بسته بندی از بار با موفیت انجام شد."]);


    }

    public function print_packing_form(FormGeneralItemPackingForm $formGeneralItemPackingForm)
    {
        $worker = Worker::find(Auth::id());
        PrintQRController::direct_print($formGeneralItemPackingForm->packing_form, $worker->id);
        return back()->with(["success" => "پرینت بسته بندی به پرینتر پیش فرض ارسال گردید."]);
    }

    public function add_packing_form_api(Request $request, FormGeneralItem $form_general_item)
    {

        $product = $form_general_item->product;
        $amount = $request->amount;
        $sub_amount = $request->sub_amount;
        $gross_weight = $request->gross_weight;
        $action_type = $request->action_type;
        $number_of_sub_packing = $request->number_of_sub_packing ?? 1;
        $user_id = $request->user_id;

        if ($product->unit->weight_conversion_rate != 0) {
            $gross_weight = $amount;
        }
        if ($product->sub_unit && $product->sub_unit->weight_conversion_rate != 0) {
            $gross_weight = $sub_amount;
        }


        $before_amount_is_gross_weight = null; // مقدار اصلی بسته بندی وزن ناخالص است؟
        $packing_type = $form_general_item->packing_type;

        $packing_type_weight_result = PackingType::getWeight($packing_type);
        if (!$packing_type_weight_result["result"]) {
            return "<div class='alert alert-danger' >" . $packing_type_weight_result["error"] . "</div>"
                . "<a class='btn btn-dark' href='" . route($this->route_path . "index", $form_general_item->form) . "'> بارگشت</a>";;

        }
        $packing_type_weight = $packing_type_weight_result["weight"];

        if ($form_general_item->status_id == 5002006) { // در انتظار تخلیه بار
            $gross_weight = $request->gross_weight;
            $sub_amount = $gross_weight - $packing_type_weight; // مقدار فرعی همان وزن خالص
            $amount = round($sub_amount / $form_general_item->kg_in_meter , 1); // متراژ را از روی کرماژ به دست می آوریم
        }

        $get_amount_from_weight_result = Product::getAmountFromWeight($form_general_item->product, $gross_weight, $packing_type_weight, $amount, $sub_amount);

        if (!$get_amount_from_weight_result["result"]) {
            return "<div class='alert alert-danger' >" . $get_amount_from_weight_result["error"] . "</div>"
                . "<a class='btn btn-dark' href='" . route($this->route_path . "index", $form_general_item->form) . "'> بارگشت</a>";

        }
        $gross_weight = $get_amount_from_weight_result["gross_weight"];
        $final_amount = $get_amount_from_weight_result["final_amount"];
        $sub_amount = $get_amount_from_weight_result["sub_amount"];
        $weight = $get_amount_from_weight_result["weight"];

        /********************************************/
        //new Packing form
        $new_packing_form = PackingForm::create([
            "packing_type_id" => $packing_type->id,
            "carrier_id" => null,
            "status_id" => 7007006, //معلق
            "sub_packing_form_number" => 0,
            "applicant_type_id" => 30, //مشتری
            "applicant_id" => $form_general_item->customer_id,
            "weight" => $weight,
            "gross_weight" => $gross_weight,

        ]);

        $new_packing_form->getCode();
        event(new PackingLogEvent($new_packing_form, 7007001, null, "", null, $user_id));


        $new_packing_form_item = PackingFormItem::create([
            "packing_form_id" => $new_packing_form->id,
            "product_id" => $form_general_item->product_id,
            "lot_number_id" => $form_general_item->lot_number_id,
            "degree_id" => $form_general_item->degree_id,
            "amount" => $amount,
            "amount_after_control" => $amount,
            "final_amount" => $amount,
            "sub_amount" => $sub_amount,
            "init_sub_amount" => $sub_amount,
            "status_id" => 7007006, //معلق
            "band_code" => 1
        ]);


        $new_general_item_packing_form = FormGeneralItemPackingForm::create([
            "form_id" => $form_general_item->form_id,
            "packing_form_id" => $new_packing_form->id,
            "form_general_item_id" => $form_general_item->id,
            "check_quality_status_id" => 5003001, // نا مشخص
        ]);

        /*******************************************/
        $form_general_item = FormGeneralItem::find($form_general_item->id);
        $resul_check_quality = null;
        if ($form_general_item->status_id == 5002002) { // در حال تفکیک یا همان کنترل کیفی
            $resul_check_quality = FormGeneralItemPackingForm::CheckQualityStatus($form_general_item);
        }
        if ($action_type == "register_print_continue" || $action_type == "register_print_back") {
            $packing_form_print = PackingForm::find($new_packing_form->id);
            $worker = Worker::find($user_id);
            PrintQRController::direct_print($packing_form_print, $worker);

        }
        $route_path = $this->route_path;

        $product = $form_general_item->product;
        $smart_object = SmartObject::find($request->smart_object_id);
        $message = "ثبت با موفقیت انجام شد.";

        return view($this->view_path .

            ($form_general_item->status_id == 5002002 ? "_quality_control_input_value" : "_separation_input_value"),

            compact('form_general_item', "message", "resul_check_quality", "user_id", "route_path", "product", 'smart_object'));


    }


    public function final_step_to_separation(FormGeneralItem $general_item)
    {
        return view($this->view_path . "separation." . "index", compact('general_item'));
    }


    public function view_list_of_separation(Form $form)
    {
        return view($this->view_path . "show_list." . "index", compact('form'));

    }

    public function add_data(Request $request)
    {
        session(['separation_list' => json_decode($request->data, true)]);
        return response()->json(['ok' => true]);
    }


    public function data_to_view_api(Request $request)
    {

        $data = $request->input('data');


        return view($this->view_path . "_view", compact(
            'data'
        ));


    }


}







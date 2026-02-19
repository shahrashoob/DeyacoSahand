<?php

namespace App\Http\Controllers\Warehouse\Input;

use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utility\Script\Script1008Controller;
use App\Http\Controllers\Warehouse;
use App\Models\Form\Form;
use App\Models\Form\FormGeneralItem;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product\ProductReservoir;
use App\Models\Utility\JsonDataList;
use App\Models\Warehouse\WarehouseProductBlock;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function GuzzleHttp\Psr7\_parse_request_uri;

class CompleteInformationController extends Controller
{
    //
    var $view_path = "warehouse.input.complete_information.";
    var $route_path = "wh.input.complete_information.";
    var $dashboard_path = "wh.dashboard.index";

    public function index(Form $form)
    {
        $result = Warehouse\DashboardController::check_permission($form);
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
        foreach ($form->general_items as $form_general_item) {
            if ($form_general_item->product->unit->weight_conversion_rate > 0) {
                $show_enter_unit_amount = false;
            }
            $product_reservoirs[$form_general_item->product_id] = ProductReservoir::where("product_id", $form_general_item->product_id)->get();

        }

        if(count($form->general_items) == 1 ){
            redirect()->route('wh.input.complete_information_with_value.quality_control',$form);
        }else {
            redirect()->route('wh.input.complete_information_with_value.index',$form);
        }


        if (!session("packing_form_rows" . $form->id)) {
            // اگر اطلاعاتی قبلا ذخیره شده بود آنها را برمی گردانیم.
            $before_data = JsonDataList::where([
                "message_type_id" => 290,
                "other_id" => $form->id
            ])->first();
            if ($before_data) {
                $before_data = json_decode($before_data->data, true);
                session([
                    "enter_gross_weight" => $before_data["enter_gross_weight"],
                    "enter_weight" => $before_data["enter_weight"],
                    "enter_unit_amount" => $before_data["enter_unit_amount"],
                    "packing_form_rows" . $form->id => $before_data["packing_form_rows"],
                    "product_reservoir" . $form->id => $before_data["product_reservoir"],
                    "product_amount" . $form->id => isset($before_data["product_amount"])?$before_data["product_amount"]:"",
                ]);
            } else {
                session([
                    "enter_gross_weight" => 1,
                    "enter_weight" => 0,
                    "enter_unit_amount" => $show_enter_unit_amount,
                ]);
            }
        }

        $enter_gross_weight = session("enter_gross_weight");
        $enter_weight = session("enter_weight");
        $enter_unit_amount = session("enter_unit_amount");
        $packing_form_rows = $this->getJsonFromArray(session("packing_form_rows" . $form->id));
        $product_reservoir_rows = $this->getJsonFromArrayReservoirs(session("product_reservoir" . $form->id));
        $product_amount_rows = $this->getJsonFromArrayReservoirs(session("product_amount" . $form->id));

        $first_packing_type_layers = PackingType::pluck("first_packing_type_id", "id")->toArray();


        return view($this->view_path . "index", compact("form",
            "enter_weight", "enter_gross_weight", "enter_unit_amount", "first_packing_type_layers", "product_amount_rows",
            "packing_form_rows", "show_enter_unit_amount", 'product_reservoirs', 'product_reservoir_rows'
        ));
    }

    public function submit(Request $request, Form $form)
    {

        $result = Warehouse\DashboardController::check_permission($form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        if ($form->status_id != 500000430) {
            return back()->withErrors("وضعیت فرم در انتظار تکمیل اطلاعات نمی باشد.");
        }
        if (count($form->general_items) == 0) {
            return back()->withErrors("برای فرم ورود هیچ آیتمی برای تفکیک وجود ندارد.");
        }

        $packing_form_rows = $request->packing_form_rows;
        $product_reservoir = $request->product_reservoir;
        $product_amount = $request->product_amount;

        $form_general_item = $form->general_items()->get()->keyBy("id");

        $packing_form_rows_for_session = $this->getArrayFromJson($request->packing_form_rows);
        $reservoirs_for_session = $this->getArrayFromJsonReservoirs($request->product_reservoir);
        session([
            "packing_form_rows" . $form->id => $packing_form_rows_for_session,
            "product_reservoir" . $form->id => $reservoirs_for_session,
            "enter_gross_weight" => $request->enter_gross_weight,
            "enter_weight" => $request->enter_weight,
            "enter_unit_amount" => $request->enter_unit_amount,
            "product_amount" => $request->product_amount,
        ]);
        /// ذخیره اطلاعات در جدول json list
        $json_data = [
            "packing_form_rows" => $packing_form_rows_for_session,
            "product_reservoir" => $reservoirs_for_session,
            "enter_gross_weight" => $request->enter_gross_weight,
            "enter_weight" => $request->enter_weight,
            "enter_unit_amount" => $request->enter_unit_amount,
        ];
        JsonDataList::where([
            "message_type_id" => 290,
            "other_id" => $form->id
        ])->delete();
        JsonDataList::create([
            "message_type_id" => 290,
            "other_id" => $form->id,
            "data" => json_encode($json_data)
        ]);

        if ($request->save_data_status == "save_data") {
            return back()->with(["warning" => "اطلاعات بسته بندی ها(مخزن ها) به صورت موقت در سامانه ذخیره گردید."]);
        }

        $enter_gross_weight = $request->enter_gross_weight;
        $enter_weight = $request->enter_weight;
        $enter_unit_amount = $request->enter_unit_amount;
        $new_packing_form_rows = [];
        $new_reservoirs_form_rows = $reservoirs_for_session;
        // اطلاعات همه ردیف های بسته بندی به صورت کامل وارد شده است.
        if ($packing_form_rows) {
            foreach ($packing_form_rows as $item) {
                if (isset($item["gross_weight"]) || isset($item["weight"]) || isset($item["unit_amount"])) {
                    if ($form_general_item[$item["form_general_item_id"]]->product->unit->weight_conversion_rate == 0 && !isset($item["unit_amount"])) {

                        return redirect()->route($this->route_path . "index", $form)->withErrors("ثبت مقدار واحد اصلی برای کالا الزامی است.");

                    }
                }

                if (!isset($item["weight"]) && !isset($item["gross_weight"])) {
                    if ($form_general_item[$item["form_general_item_id"]]->product->unit->weight_conversion_rate == 0) { // واحد اصلی وزنی نیست.
                        return redirect()->route($this->route_path . "index", $form)->withErrors("لطفا یکی از موارد وزن خالص / وزن ناخالص را انتخاب نمایید.");

                    }
                }

            }


            $k = 0;

            foreach ($packing_form_rows as &$item_row) {
                $form_general_item = FormGeneralItem::find($item_row["form_general_item_id"]);

                $result_weight = $this->get_packing_type_weight($form_general_item->packing_type);
                if (!$result_weight["result"]) {
                    return redirect()->route($this->route_path . "index", $form)->withErrors($result_weight["error"]);

                }
                $packing_type_weight = $result_weight["packing_type_weight"];
                $packing_type_sub_weight = $result_weight["packing_type_sub_weight"];

// واحد اصلی وزنی است و از روی آن مقدار خالص ناخالص را به دست می آوریم.
                if (!$enter_weight && !$enter_gross_weight) {
                    $item_row["weight"] = $item_row["unit_amount"] * $form_general_item->product->unit->weight_conversion_rate;
                }
                if (($enter_weight && !$enter_gross_weight) || (!$enter_weight && !$enter_gross_weight)) {
                    $item_row["gross_weight"] = $item_row["weight"] + $packing_type_weight + (isset($item_row["sub_packing_form_number"]) ? $packing_type_sub_weight * $item_row["sub_packing_form_number"] : 0);
                }

                $item_row["unit_amount"] = isset($item_row["unit_amount"]) ? $item_row["unit_amount"] : null;
                $result = PackingType::getAmountFromWeight(
                    $form_general_item->product,
                    $form_general_item->packing_type,
                    $item_row["gross_weight"],
                    isset($item_row["sub_packing_form_number"]) ? $item_row["sub_packing_form_number"] : 0,
                    null,
                    $item_row["unit_amount"]


                );

                if (!$result["result"]) {
                    return redirect()->route($this->route_path . "index", $form)->withErrors($result["error"]);

                }
                if ($result["weight"] <= 0) {
                    return redirect()->route($this->route_path . "index", $form)->withErrors("مقدار وزن خالص باید عددی بزرگتر از صفر باشد.");

                }

                if ($result["final_amount"] <= 0) {
                    return redirect()->route($this->route_path . "index", $form)->withErrors("مقدار نهایی باید عددی بزرگتر از صفر باشد.");

                }
                $item_row["final_amount"] = $result["final_amount"];
                $item_row["sub_amount"] = $result["sub_amount"];
                $item_row["weight"] = $result["weight"];

                $new_packing_form_rows[$k++] = $item_row;

                if (!isset($sum_final_amount_for_checking[$form_general_item->id])) {
                    $sum_final_amount_for_checking[$form_general_item->id] = 0;
                }
                $sum_final_amount_for_checking[$form_general_item->id] += $result["final_amount"];
            }

        } elseif ($product_reservoir) { // مخزن است

            foreach ($product_reservoir as $item) {

                $form_general_item = FormGeneralItem::find($item["form_general_item_id"]);

                if (!isset($item["unit_amount"])) {
                    return redirect()->route($this->route_path . "index", $form)->withErrors("ثبت مقدار واحد اصلی برای کالا الزامی است.");
                }

                if ($form_general_item->product->unit->weight_conversion_rate == 0) {
                    // اگر واحد اصلی غیر از کیلوگرم است، خطا بدهد تا مورد آن پیدا شود و بعد فرایند را تکمیل کنیم.
                    return redirect()->route($this->route_path . "index", $form)->withErrors("واحد اصلی کالا نامعتبر است، لطفا با واحد پشتیبانی تماس بگیرید.");

                }

                if (!isset($sum_final_amount_for_checking[$form_general_item->id])) {
                    $sum_final_amount_for_checking[$form_general_item->id] = 0;
                }
                $sum_final_amount_for_checking[$form_general_item->id] += $item["unit_amount"];

            }


        } else {
            // انبارش بدون بسته بندی
            // انبارش با دپو


        }
        $show_enter_unit_amount = true;
        //  بررسی درصد مجاز اختلاف (بین مقدار وارد شده توسط پیمانکار/تامین کننده و انبار) قابل قبول جهت تکمیل و ثبت اطلاعات در انبار
        foreach ($form->general_items as $form_general_item) {

            $allowed_percentage = $form_general_item->product->goods_kind->allowed_percentage_in_complete_form_information;
            //return  $sum_final_amount_for_checking[$form_general_item->id];
            if(!isset($sum_final_amount_for_checking[$form_general_item->id])){
                return redirect()->route($this->route_path . "index", $form)->withErrors("نوع انبار کالا (".$form_general_item->product->capiton.") نامعتبر است، لطفا با واحد اطلاعات پایه تماس بگیرد. ");

            }
            $sum_final_amount_for_checking[$form_general_item->id] = round($sum_final_amount_for_checking[$form_general_item->id], 10);
            if (
                $sum_final_amount_for_checking[$form_general_item->id] > (1 + $allowed_percentage / 100) * $form_general_item->amount ||
                $sum_final_amount_for_checking[$form_general_item->id] < (1 - $allowed_percentage / 100) * $form_general_item->amount
            ) {

                if(!isset($sum_final_amount_for_checking[$form_general_item->id])){
                    return redirect()->route($this->route_path . "index", $form)->withErrors("نوع انبار کالا (".$form_general_item->product->capiton.") نامعتبر است، لطفا با واحد اطلاعات پایه تماس بگیرد. ");

                }
                $message = "مقدار وارد شده توسط انبار:" . $sum_final_amount_for_checking[$form_general_item->id] . " " . $form_general_item->product->unit->caption . "<br/>";
                $message .= "مقدار ارسال شده توسط " . (($form_general_item->machine_allocation->contractor->caption ?? "") . " " . ($form_general_item->machine_allocation->supplier->caption ?? "")) . ": " . $form_general_item->amount . " " . $form_general_item->product->unit->caption . "<br/>";
                return redirect()->route($this->route_path . "index", $form)->withErrors("جمع کل مقدار وارد شده برای بسته بندی ها  " . " نا معتبر است، لطفا اطلاعات " . ($form_general_item->product->unit->caption) . " را به درست وارد نمایید،" . "<br/>" . $message);

            }

        }


        $new_packing_form_rows = json_encode($new_packing_form_rows);
        $new_reservoirs_form_rows = json_encode($new_reservoirs_form_rows);
        session([
            "final_packing_form_rows" . $form->id => $new_packing_form_rows,
            "final_reservoirs_form_rows" . $form->id => $new_reservoirs_form_rows,
        ]);


        return redirect()->route($this->route_path . "confirm", $form);
    }


    public function confirm(Form $form)
    {

        $result = Warehouse\DashboardController::check_permission($form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        if ($form->status_id != 500000430) {
            return back()->withErrors("وضعیت فرم در انتظار تکمیل اطلاعات نمی باشد.");
        }
        $packing_form_rows = session("final_packing_form_rows" . $form->id);
        $reservoirs_rows = session("final_reservoirs_form_rows" . $form->id);

        if (!$packing_form_rows) {
            return redirect()->route($this->route_path . "index", $form)->withErrors("نشست شما به پایان رسیده است، لطفا دوباره تلاش کنید.");
        }

        $form_general_items = $form->general_items()->get()->keyBy("id");


        return view($this->view_path . "confirm",
            compact("packing_form_rows", "form", "form_general_items", 'reservoirs_rows'));

    }

    public function submit_confirm(Request $request, Form $form)
    {

        $worker = Worker::find(Auth::id());
        $result = Warehouse\DashboardController::check_permission($form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        if ($form->status_id != 500000430) {
            return back()->withErrors("وضعیت فرم در انتظار تکمیل اطلاعات نمی باشد.");
        }

        $packing_form_rows = json_decode($request->packing_form_rows, true);
        $reservoirs_rows = json_decode($request->reservoirs_rows, true);

        $data["form_id"] = $form->id;
        $data["packing_form_rows"] = json_encode($packing_form_rows);
        $data["reservoirs_rows"] = json_encode($reservoirs_rows);
        $data["print"] = $request->print;
        $data["user_id"] = $worker->id;


        $form->status_id = 500000450; // در حال پردازش اطلاعات تکمیلی
        $form->save();
        event(new FormLogEvent($form));


        DB::table("new_entry_form_for_script_1008")->insert($data);

        return redirect()->route($this->dashboard_path)->with(["success" => "اطلاعات بسته بندی ها(مخزن ها) " . " در انبار با موفقیت ثبت گردید و در انتظار پردازش قرار گرفت."]);


        new Script1008Controller();
    }

    public function get_packing_type_weight(PackingType $packing_type)
    {
        $packing_type_weight = 0;
        $packing_type_sub_weight = 0;
        // وزن بسته بندی اصلی
        $packing_type_weight_result = PackingType::getWeight($packing_type);
        if (!$packing_type_weight_result["result"]) {
            return [
                "result" => false,
                "error" => $packing_type_weight_result["error"]
            ];

        } else {
            $packing_type_weight = $packing_type_weight_result["weight"];
        }

        if ($packing_type->first_packing_type) {
            // وزن بسته بندی فرعی
            $packing_type_weight_result = PackingType::getWeight($packing_type->first_packing_type);
            if (!$packing_type_weight_result["result"]) {
                return [
                    "result" => false,
                    "error" => $packing_type_weight_result["error"]
                ];

            } else {
                $packing_type_sub_weight = $packing_type_weight_result["weight"];
            }
        }

        return [
            "result" => true,
            "packing_type_sub_weight" => $packing_type_sub_weight,
            "packing_type_weight" => $packing_type_weight
        ];
    }

    public function getArrayFromJsonReservoirs($data)
    {
        if (!isset($data)) {
            return null;
        }
        $may_array = [];

        $k = 0;
        foreach ($data as $key => $item) {

            $may_array[$key]["form_general_item_id"] = isset($item["form_general_item_id"]) ? $item["form_general_item_id"] : "";
            $may_array[$key]["gross_weight"] = isset($item["gross_weight"]) ? $item["gross_weight"] : "";
            $may_array[$key]["weight"] = isset($item["weight"]) ? $item["weight"] : "";
            $may_array[$key]["unit_amount"] = isset($item["unit_amount"]) ? $item["unit_amount"] : "";
            $may_array[$key]["reservoir_id"] = $key;

        }
        return $may_array;
    }

    public function getArrayFromJson($data)
    {
        if (!isset($data)) {
            return null;
        }
        $may_array["form_general_item_id"] = [];
        $may_array["gross_weight"] = [];
        $may_array["weight"] = [];
        $may_array["unit_amount"] = [];
        $may_array["sub_packing_form_number"] = [];

        $k = 0;
        foreach ($data as $item) {
            $may_array["form_general_item_id"][$k] = isset($item["form_general_item_id"]) ? $item["form_general_item_id"] : "";
            $may_array["gross_weight"][$k] = isset($item["gross_weight"]) ? $item["gross_weight"] : "";
            $may_array["weight"][$k] = isset($item["weight"]) ? $item["weight"] : "";
            $may_array["unit_amount"][$k] = isset($item["unit_amount"]) ? $item["unit_amount"] : "";
            $may_array["sub_packing_form_number"][$k] = isset($item["sub_packing_form_number"]) ? $item["sub_packing_form_number"] : "";
            $k++;
        }
        return $may_array;
    }

    public function getJsonFromArray($data)
    {
        if (!isset($data) || !$data) {
            return null;
        }
        $may_array = [];
        for ($k = 0; $k < count($data["form_general_item_id"]); $k++) {
            $may_array[$k]["form_general_item_id"] = $data["form_general_item_id"][$k];
            $may_array[$k]["gross_weight"] = $data["gross_weight"][$k];
            $may_array[$k]["weight"] = $data["weight"][$k];
            $may_array[$k]["unit_amount"] = $data["unit_amount"][$k];
            $may_array[$k]["sub_packing_form_number"] = $data["sub_packing_form_number"][$k];
        }

        return $may_array;
    }

    public function getJsonFromArrayReservoirs($data)
    {
        if (!isset($data) || !$data) {
            return null;
        }
        $may_array = [];
        foreach ($data as $k => $item) {

            $may_array[$k]["form_general_item_id"] = $item["form_general_item_id"];
            $may_array[$k]["unit_amount"] = $item["unit_amount"];
        }

        return $may_array;
    }
}

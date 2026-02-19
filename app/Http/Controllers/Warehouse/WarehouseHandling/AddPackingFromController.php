<?php

namespace App\Http\Controllers\Warehouse\WarehouseHandling;

use App\Http\Controllers\Controller;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandling;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandlingPackingForm;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AddPackingFromController extends Controller
{
    public static $info = [
        "route" => "wh.warehouse_handling.add_packing_form.",
        "enable_status" => ["301", "302"],
        "button" => ["caption" => "انبارگردانی", "class" => "btn-primary"],
        "view_path" => "warehouse.warehouse_handling.add_packing_form.",
        "allowed_status_ids" => [524000301, 524000302]

    ];
    var $view_path;
    var $route_path;
    var $dashboard_path = "wh.warehouse_handling.dashboard.index";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view_path"];
    }

    public function index(WarehouseHandling $warehouse_handling)
    {
        $result = $this->checkPermission($warehouse_handling);
        if ($result != "") {
            return $result;
        }
        if (!in_array($warehouse_handling->status_id, self::$info["allowed_status_ids"])) {
            return redirect()->route($this->dashboard_path)->withErrors("با توجه به اینکه انبارگردانی انجام شده است، امکان تغییر در بسته بندی های ثبت شده وجود ندارد.");
        }
        if ($warehouse_handling->status_id == 524000301) {
            $warehouse_handling->status_id = 524000302; // در حال انبارگردانی
            $warehouse_handling->save();
        }
        WarehouseHandling::RemoveUnReadPackingForm($warehouse_handling);
        $packing_form_count = $warehouse_handling->packing_forms()->count();
        return view($this->view_path . "index", compact("warehouse_handling", "packing_form_count"));
    }

    public function packing_form_list(WarehouseHandling $warehouse_handling)
    {
        $result = $this->checkPermission($warehouse_handling);
        if ($result != "") {
            return $result;
        }
        if (!in_array($warehouse_handling->status_id, self::$info["allowed_status_ids"])) {
            return redirect()->route($this->dashboard_path)->withErrors("با توجه به اینکه انبارگردانی انجام شده است، امکان تغییر در بسته بندی های ثبت شده وجود ندارد.");
        }

        $list = $warehouse_handling->packing_forms()->
        where("status_id", "!=", 524000403)-> // خوانده نشده (داخل انبار)
        paginate(50);

        return view($this->view_path . "packing_form_list", compact("warehouse_handling", "list"));
    }

    /*
     * اضافه کردن بسته بندی های خوانده شده در انبارگردانی به جدول لیست بسته بندی ها
     */
    public function add_packing_form_api(Request $request)
    {
        $warehouse_handling = WarehouseHandling::find($request->warehouse_handling_id);

        if (!$warehouse_handling->start_datetime || $warehouse_handling->status_id == 524000301) {
            $warehouse_handling->start_datetime = Carbon::now();
            $warehouse_handling->status_id = 524000302; // در حال انبارگردانی
            $warehouse_handling->save();
        }


        $count_all_packing_form = 0;
        if (!$warehouse_handling) {

            $status_list = $warehouse_handling->packing_forms()->
            groupBy("status_id")->
            selectRaw("count(id) as count , status_id")->
            pluck("count", "status_id");

            $error_insert = "فرم انبار گردانی مورد نظر در سامانه تعریف نشده است، لطفا با واحد پشتیبانی تماس بگیرید.";

            return view("warehouse.warehouse_handling.dashboard._table_status_list", compact("warehouse_handling", "status_list", "error_insert", "count_all_packing_form"))->render();

        }

        if (!in_array($warehouse_handling->status_id, self::$info["allowed_status_ids"])) {

            $count_all_packing_form = $warehouse_handling->packing_forms()->count();

            $status_list = $warehouse_handling->packing_forms()->
            groupBy("status_id")->
            selectRaw("count(id) as count , status_id")->
            pluck("count", "status_id");

            $error_insert = "با توجه به اینکه انبارگردانی شماره " . $warehouse_handling->id
                . " در وضعیت '" . $warehouse_handling->status->caption . "' می باشد، امکان اضافه کردن بسته بندی جدید برای آن وجود ندارد.";

            return view("warehouse.warehouse_handling.dashboard._table_status_list", compact("warehouse_handling", "status_list", "error_insert", "count_all_packing_form"))->render();

        }

        $error_insert = "";
        $warehouse_handling_packing_form = null;
        $packing_form_ids = [];
        if ($request->packing_form_id_1) {
            $packing_form_ids[] = $request->packing_form_id_1;
        }
        $allow_entry_with_pin = $warehouse_handling->warehouse->allow_entry_with_pin;

        $packing_form_list = PackingForm::
        whereIn($allow_entry_with_pin ? "pin1" : "code", $packing_form_ids)->
        get()->
        keyBy("id");
        $packing_form_id=0;
        if(count($packing_form_list)==1){
            foreach ($packing_form_list as $key=>$x) {
                $packing_form_id=$key;
            }

        }

        $warehouse_handling_packing_form_result =
            self::AddPackingForm($warehouse_handling, $packing_form_list, $packing_form_id, $request->gross_weight, $request->packing_form_id_1,$allow_entry_with_pin);

        if ($warehouse_handling_packing_form_result["result"]) {
            $warehouse_handling_packing_form = $warehouse_handling_packing_form_result["packing_form_wh"];
        } else {
            $error_insert .= $warehouse_handling_packing_form_result["error"];
        }
        $packing_form_reading = null; // بسته بندی که خوانده شده
        if (isset($warehouse_handling_packing_form_result["packing_form"])) {
            $packing_form_reading = $warehouse_handling_packing_form_result["packing_form"];
        }

        // بررسی اینکه در بسته بندی های درج شده خطایی وجود ندارد یا خیر
//        $count = WarehouseHandlingPackingForm::GetErrorCount($warehouse_handling);
//
//        if ($count > 0) {
//            $error_insert = " " . $count . " بسته بندی خوانده شده در انبارگردانی مغایرت دارد  ";
//        }

        $status_list = $warehouse_handling->packing_forms()->
        groupBy("status_id")->
        selectRaw("count(id) as count , status_id")->
        pluck("count", "status_id");
        $count_all_packing_form = $warehouse_handling->packing_forms()->count();

        $html = view("warehouse.warehouse_handling.dashboard._table_status_list", compact("warehouse_handling", "status_list", "error_insert", "count_all_packing_form", "warehouse_handling_packing_form", "packing_form_reading"))->render();
        return json_encode([
            "result" => $warehouse_handling_packing_form_result["result"],
            "html" => $html,
            "error" => $error_insert
        ]);
    }

    public static function AddPackingForm(WarehouseHandling $warehouseHandling, $packing_form_list, $packing_form_id, $gross_weight, $packing_form_full_code,$allow_entry_with_pin)
    {
        $status_id = null;

        if (!isset($packing_form_list[$packing_form_id])) {
            // کد بسته بندی در سامانه وجود ندارد.
            $status_id = 524000405; // خوانده شده (در سامانه وجود ندارد)
        } else {
            $packing_form = $packing_form_list[$packing_form_id];

            if ($packing_form->warehouse_status_id == 4201) {
                if ($warehouseHandling->warehouse_id == $packing_form->warehouse_id) {
                    // خوانده شده / داخل انبار
                    $status_id = 524000401; //  خوانده نشده (داخل انبار)

                    if ($packing_form->status_id != 7007003) { // تحویل شده به انبار
                        $status_id = 524000406; // خوانده شده ( نامعتبر)
                    }

                } else {
                    $status_id = 524000404; // خوانده شده (داخل انبار دیگر)
                }
            } elseif ($packing_form->warehouse_status_id == 4204 && ($packing_form->status_id == 7007002 || $packing_form->status_id == 7007008)) {
                // بسته بندی در انتظار تایید انبار است.

                $packing_form_item = FormItem::join("packing_form_item", "packing_form_item.id", "form_item.packing_form_item_id")->
                where("packing_form_item.packing_form_id", $packing_form_id)->
                orderBy("form_item.id", "desc")->first();
                if (!$packing_form_item) {
                    return [
                        "result" => false,
                        "error" => "اطلاعات فرم ورود به انبار بسته بندی " . $packing_form->code . " نامعتبر است، لطفا با واحد پشتیبانی تماس بگیرید."
                    ];
                }

                if ($packing_form_item->form->warehouse_id != $warehouseHandling->warehouse_id) {
                    return [
                        "result" => false,
                        "error" => "فرم ورود به انبار (" . $packing_form_item->form->code . ") که بسته بندی " . $packing_form->code . " در آن قرار دارد مربوط به  " .
                            $packing_form_item->form->warehouse->caption . " می باشد."
                    ];
                }

                $status_id = 524000408; // خوانده شده (در انتظار تایید انبار)


            } elseif ($packing_form->warehouse_status_id == 4202) {
                $status_id = 524000402; // خوانده شده (خارج از انبار)

                if (!in_array($packing_form->status_id, [7007019, 7007017, 7007016, 7007014, 7007012, 7007005])) { // تحویل شده به انبار
                    $status_id = 524000406; // خوانده شده ( نامعتبر)
                }


            } else {
                $status_id = 524000406; // خوانده شده ( نامعتبر)
            }
        }


        $packing_form_wh = WarehouseHandlingPackingForm::where([
            "warehouse_handling_id" => $warehouseHandling->id,
            "packing_form_id" => $packing_form_id,
            "status_id" => $status_id
        ])->first();
        if ($packing_form_wh) {
            return [
                "result" => false,
                "error" => "بسته بندی " . $packing_form_wh->packing_form->code . "قبلا خوانده شده است."
            ];
        }

        switch ($status_id) {
            case 524000404: // خوانده شده (داخل انبار دیگر)
                $packing_form = $packing_form_list[$packing_form_id];
                return [
                    "result" => false,
                    "error" => "بسته بندی " . $packing_form->code . " در " . ($packing_form->warehouse->caption ?? "نامشخص") . " قرار دارد، لطفا این بسته بندی را در آن انبار قرار دهید.",
                    "packing_form" => $packing_form
                ];
                break;
            case 524000405: // خوانده شده کد نامعتبر

                return [
                    "result" => false,
                    "error" => "بسته بندی با " . ($allow_entry_with_pin?"پین ":"گد ") . ($packing_form_full_code) . "در سامانه وجود ندارد."
                ];
                break;
            case 524000406: // خوانده شده  وضعیت نامعتبر
                $packing_form = $packing_form_list[$packing_form_id];
                return [
                    "result" => false,
                    "error" => "وضعیت بسته بندی " . "(" . ($packing_form->status->caption ?? "") . ")" . $packing_form->code . " نامعتبر است. ",
                    "packing_form" => $packing_form
                ];
                break;
        }

        if ($warehouseHandling->products()->count() > 0) {
            $warehouse_handling_product_ids = $warehouseHandling->products()->pluck("product_id")->toArray();
            $error_product = "";
            foreach ($packing_form->items()->groupBy("product_id")->get() as $packing_form_item) {
                if (!in_array($packing_form_item->product_id, $warehouse_handling_product_ids)) {
                    $error_product .= "کالای " . $packing_form_item->product->fullCaption() . " که در بسته بندی " .
                        $packing_form->code .
                        " قرار دارد، جزء کالاهای مجاز در این انبار گردانی نمی باشد.";
                }
            }

            if ($error_product != "") {

                return [
                    "result" => false,
                    "error" => $error_product,
                    "packing_form" => $packing_form
                ];
            }
        }

        $final_amount = $packing_form->getFinalAmount();
        if ($final_amount <= 0) {
            return [
                "result" => false,
                "error" => "مقدار نهایی (" . $final_amount . ") بسته بندی " . $packing_form->code . " نامعتبر است. ",
                "packing_form" => $packing_form
            ];
        }

        // محاسبه مقدار نهایی با توجه به وزن
        $wh_final_amount = null;
        $wh_has_diff_in_amount = false;
        if ($warehouseHandling->check_diff_in_amount) {
            $product = $packing_form->items()->first()->product;
            $result_amount_form_weight = PackingType::getAmountFromWeight($product, $packing_form->packing_type, $gross_weight, $packing_form->sub_packing_form_number);
            if (!$result_amount_form_weight["result"]) {
                return $result_amount_form_weight;
            }
            $wh_final_amount = $result_amount_form_weight["final_amount"];
            $wh_has_diff_in_amount = self::HasDiff($packing_form->getFinalAmount(), $wh_final_amount, $warehouseHandling->max_diff_allowed);
        }

        $packing_form_wh = WarehouseHandlingPackingForm::create([
            "warehouse_handling_id" => $warehouseHandling->id,
            "packing_form_id" => $packing_form_id,
            "status_id" => $status_id,
            "final_amount" => $wh_final_amount,
            "has_diff_in_amount" => $wh_has_diff_in_amount
        ]);
        return [
            "result" => true,
            "packing_form_wh" => $packing_form_wh
        ];


    }


    public function checkPermission(WarehouseHandling $warehouseHandling)
    {

        $result = DashboardController::checkPermissionConditions($warehouseHandling, self::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

    public static function HasDiff($number, $new_number, $max_diff)
    {

        $max_number_dif = $number * $max_diff / 100;
        if ($number - $max_number_dif <= $new_number && $new_number <= $number + $max_number_dif) {
            return false;
        }
        return true;
    }
}

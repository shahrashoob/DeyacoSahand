<?php

namespace App\Http\Controllers\Warehouse\ProductionWarehouse;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\LineProduct\Station;
use App\Models\Warehouse\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    var $view_path = "warehouse.production_warehouse.dashboard.";
    var $route_path = "wh.production_warehouse.dashboard.";

    public function index(Request $request)
    {

        $allowed_machine_ids = Line::getAllowedMachine();
        $allowed_machine_ids [] = -1;


        $machine_warehouse_ids = Machine::whereIn("id", $allowed_machine_ids)->pluck("warehouse_id")->toArray();

        $allowed_machine_type_ids = MachineType::
        join("machines", "machine_types.id", "machine_type_id")->
        groupBy("machine_type_id")->
        whereIn("machines.id", $allowed_machine_ids)->
        pluck("machine_type_id")->toArray();
        $allowed_machine_type_ids[] = -1;

        $machine_type_warehouse_ids = Warehouse::
        where("warehouse_type_id", 3)->
        whereIn("belonging_to_id", $allowed_machine_type_ids)->
        pluck("id")->toArray();

        $allowed_station_ids = Station::
        join("machines", "stations.id", "station_id")->
        groupBy("station_id")->
        whereIn("machines.id", $allowed_machine_ids)->
        pluck("station_id")->toArray();
        $allowed_machine_ids[] = -1;

        $station_warehouse_ids = Warehouse::
        where("warehouse_type_id", 4)->
        whereIn("belonging_to_id", $allowed_station_ids)->
        pluck("id")->toArray();

        $allowed_line_ids = Station::
        join("machines", "stations.id", "station_id")->
        join("lines", "lines.id", "line_id")->
        groupBy("line_id")->
        whereIn("machines.id", $allowed_machine_ids)->
        pluck("line_id")->toArray();
        $allowed_line_ids[] = -1;

        $line_warehouse_ids = Warehouse::
        where("warehouse_type_id", 5)->
        whereIn("belonging_to_id", $allowed_line_ids)->
        pluck("id")->toArray();


        $warehouse_ids = array_merge($machine_warehouse_ids, $machine_type_warehouse_ids, $station_warehouse_ids, $line_warehouse_ids, [-1]);

        $material_delivery_confirmation = self::has_allow_to_confirm();
        $material_delivery_reject = self::has_allow_to_reject();


        $list = Form::
        join("product_request_form_form", "forms.id", "product_request_form_form.form_id")->
        join("product_request_forms", "product_request_forms.id", "product_request_form_id")->
        join("warehouses", "warehouses.id", "product_request_forms.applicant_id")->
        where([
            "product_request_forms.applicant_type_id" => 40, //  درخواست انبارک های تولید
        ])->
        whereNotIn("forms.status_id", [500000100, 500000200])->
        select("forms.*", "warehouses.caption", "product_request_forms.id as product_request_form_id", "warehouses.id as warehouse_id", "warehouses.caption as warehouse_caption", "product_request_forms.code as product_request_form_code")->
        whereIn("warehouses.id", $warehouse_ids)->
        paginate(20);

        return view($this->view_path . "index", compact("list", "material_delivery_confirmation", "material_delivery_reject"));

    }

    public function confirm(Form $form, ProductRequestForm $product_request_form, Warehouse $warehouse)
    {

        // چک کردن دسترسی تحویل کالا
        $post_user = Auth::user()->posts->first();
        $permission_confirm_packing = $post_user->checkButtonPermission("wh.production_warehouse.dashboard.confirm_packing");
        if (!$permission_confirm_packing) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید.");
        }

        if (in_array($form->status_id, [500000100, 500000200])) {
            return back()->withErrors("وضعیت فرم جهت تایید معتبر نمی باشد، لطفا مجدد سعی کنید.");
        }
        if (!self::has_allow_to_confirm()) {
            return back()->withErrors("شما اجازه مشاهده فرم را ندارید.");
        }

        $product_request_form_form = ProductRequestFormForm::where([
            "form_id" => $form->id,
            "product_request_form_id" => $product_request_form->id
        ])->get();


        $result = DashboardController::GetPackingListFroConfirm($product_request_form_form, "material_delivery_" . $warehouse->id);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $form_info = $result["form_info"];
        $packing_form_codes = $result["packing_form_codes"];
        $packing_codes_reading = $result["packing_codes_reading"];
        $goods_kind_ids = $result["goods_kind_ids"];

        $route_path = $this->route_path;
        $dashboard_route = $this->route_path;
        $warehouse_entry_confirmation_in_altogether = 0;
        if ($warehouse->warehouse_type_id) {
            $machine = Machine::find($warehouse->belonging_to_id);
            // آیا لازم است که کل بسته بندی ها را وارد کند و یا یکی را وارد کند کافی است.
            $warehouse_entry_confirmation_in_altogether = MachineTypeInputBandGoodsKind::join("machine_type_input_bands", "machine_type_input_bands.id", "machine_type_input_band_goods_kind.machine_type_input_band_id")->
            whereIn("goods_kind_id", $goods_kind_ids)->
            where("machine_type_id", $machine->machine_type_id)->
            sum("warehouse_entry_confirmation_in_altogether");
        }


        return view($this->view_path . "confirm", compact("form", "product_request_form",
            "warehouse", "form_info", "route_path", "dashboard_route", "packing_form_codes",
            "packing_codes_reading", "warehouse_entry_confirmation_in_altogether"));

    }

    public function submit_confirm(Request $request, Form $form, ProductRequestForm $product_request_form, Warehouse $warehouse)
    {

        if (in_array($form->status_id, [500000100, 500000200])) {
            return back()->withErrors("وضعیت فرم جهت تایید معتبر نمی باشد، لطفا مجدد سعی کنید.");
        }
        if (!self::has_allow_to_confirm()) {
            return back()->withErrors("شما اجازه مشاهده فرم را ندارید.");
        }

        $product_request_form_forms = ProductRequestFormForm::where([
            "form_id" => $form->id,
            "product_request_form_id" => $product_request_form->id
        ])->get();


        $result = self::SubmitPackingListFromConfirm($request, $product_request_form_forms, $warehouse, "material_delivery_" . $warehouse->id);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        return redirect()->route($this->route_path . "index")->with(["success" => "برگ خروج با موفقیت تایید گردید."]);

    }

    public function reject(Form $form, ProductRequestForm $product_request_form, Warehouse $warehouse)
    {
        // چک کردن دسترسی تحویل کالا
        $post_user = Auth::user()->posts->first();
        $permission_confirm_packing = $post_user->checkButtonPermission("wh.production_warehouse.dashboard.confirm_packing");
        if (!$permission_confirm_packing) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید.");
        }

        if (in_array($form->status_id, [500000100, 500000200])) {
            return back()->withErrors("وضعیت فرم جهت تایید معتبر نمی باشد، لطفا مجدد سعی کنید.");
        }
        if (!self::has_allow_to_reject()) {
            return back()->withErrors("شما اجازه مشاهده فرم را ندارید.");
        }

        $product_request_form_form = ProductRequestFormForm::where([
            "form_id" => $form->id,
            "product_request_form_id" => $product_request_form->id
        ])->first();

        if (!$product_request_form_form) {
            return back()->withErrors("برگ خروج از انبار یافت نشده، لطفا با پشتیبانی تماس بگیرید.");
        }

        $route_path = $this->route_path;
        $dashboard_route = $this->route_path;

        return view($this->view_path . "reject", compact("form", "product_request_form", "warehouse", "route_path", "dashboard_route"));

    }

    public function submit_reject(Request $request, Form $form, ProductRequestForm $product_request_form, Warehouse $warehouse)
    {

        if (in_array($form->status_id, [500000100, 500000200])) {
            return back()->withErrors("وضعیت فرم جهت تایید معتبر نمی باشد، لطفا مجدد سعی کنید.");
        }
        if (!self::has_allow_to_reject()) {
            return back()->withErrors("شما اجازه مشاهده فرم را ندارید.");
        }
        $product_request_form_form = ProductRequestFormForm::where([
            "form_id" => $form->id,
            "product_request_form_id" => $product_request_form->id
        ])->first();

        $machine = Machine::where("warehouse_id", $warehouse->id)->first();
        if ($warehouse->warehouse_type_id == 2) { // انبارک ماشین
            $last_log = MachineLog::where("machine_id", $machine->id)->orderByDesc("id")->first();
            if ($last_log && $last_log->machine_event_type_id == 650) {
                // در حال تحویل شیفت
                return back()->withErrors("با توجه به اینکه ماشین " . $machine->caption . " در حال تحویل شیفت می باشید، امکان عدم تایید تحویل کالا وجود ندارد، لطفا به اپراتور مسئول (" . $last_log->operator->fullname() . ") جهت تایید تحویل شیفت اطلاع دهید.");
            }
        }


        $result = $product_request_form->rejectRequest($product_request_form_form->form, null, $request->description);
        // در این تابع وضعیت جدید فرم ثبت می شود.

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        if ($warehouse->warehouse_type_id == 2) { // انبارک ماشین

            // لاگ ماشین
            $machine_log = new MachineLog();
            $machine_log->machine_event_type_id = 705;// عدم تایید تحویل مواد اولیه

            event(new MachineLogEvent($machine, $machine_log));
        }

        return redirect()->route($this->route_path . "index")->with(["success" => "برگ خروج با موفقیت تایید گردید."]);

    }


    public static function GetPackingListFroConfirm($product_request_form_form_forms, $var_session)
    {


        if (count($product_request_form_form_forms) == 0) {
            return ["result" => false, "error" => "برگ خروج از انبار یافت نشده، لطفا با پشتیبانی تماس بگیرید."];
        }

        $all_master_packing_form_list = [];
        $form_info = [];
        $product_ids = [];
        foreach ($product_request_form_form_forms as $product_request_form_form) {

            if ($product_request_form_form->form->status_id == 500000535) { // در انتظار تایید کنترل کیفیت

                // کل بسته بندی ها
                $packing_form_list = PackingForm::
                join("packing_form_item", "packing_form_id", "packing_forms.id")->
                join("form_item", "packing_form_item_id", "packing_form_item.id")->
                where("form_item.form_id", $product_request_form_form->form_id)->
                selectRaw("packing_forms.*,form_item.product_id")->
                get();
                $master_packing_forms = PackingForm::MasterPackingFormIds($packing_form_list);

                // بسته بندی هایی که اصلی هستند و بسته بندی فرعی ندارند
                $packing_form_list_master_is_null = PackingForm::MasterIsNullPackingForms($packing_form_list);
                $all_master_packing_form_list = array_merge($all_master_packing_form_list, $packing_form_list_master_is_null);

                $form_info[$product_request_form_form->form_id] = count($master_packing_forms);
                foreach ($packing_form_list as $packing_form) {
                    $product_ids[$packing_form->product_id] = $packing_form->product_id;
                }
            }

        }

        $packing_form_codes = [];
        foreach ($all_master_packing_form_list as $item) {
            $packing_form_codes[$item->getCodeNumber()] = $item->getCodeNumber();
        }

        $product_ids[] = -1;
        $goods_kind_ids = Product::whereIn("id", $product_ids)->pluck("goods_kind_id")->toArray();
        $goods_kind_ids[] = -1;

        $packing_codes_reading = session($var_session);

        return [
            "result" => true,
            "form_info" => $form_info,
            "packing_form_codes" => $packing_form_codes,
            "packing_codes_reading" => $packing_codes_reading,
            "goods_kind_ids" => $goods_kind_ids,
        ];


    }

    public static function SubmitPackingListFromConfirm(Request $request, $product_request_form_forms, $warehouse, $var_session)
    {


        if ($request->warehouse_entry_confirmation_in_altogether) {
            $one_packing_code_is_ok = false; // بسته بنیدی وارد شده درست است یا خیر
$packing_form_list=[];
            foreach ($product_request_form_forms as $product_request_form_form) {
                if ($product_request_form_form->form->status_id == 500000535) { // در انتظار تایید کنترل کیفیت

                    $form_id = $product_request_form_form->form_id;
                    // اگر به صورت تجمیعی می تواند ورود را تایید کند.
                    $packing_codes_s = FormItem::join("packing_form_item", "packing_form_item_id", "packing_form_item.id")->
                    where("form_id", $form_id)->
                    pluck("packing_form_id")->toArray();
                    $packing_codes_check = [];
                    $k = 0;
                    foreach ($packing_codes_s as $packing_code) {
                        $packing_codes_check[$form_id][++$k] = $packing_code + 1000;
                        $packing_form_list[$packing_code]=1;
                    }

                    if (in_array($request->packing_code, $packing_codes_check[$form_id]) ) {
                        $one_packing_code_is_ok = true;
                    }

                }
            }
                //  اگر یک بسته بنیدی که وارد کرده بود درست بود و تعداد بسته بندی ها هم درست بود، همه تایید می شوند.
            if ($one_packing_code_is_ok && $request->packing_form_number == count($packing_form_list)) {
                $packing_codes = $packing_codes_check;
            }
            else{
                return ["result" => false, "error" => "لطفا کد بسته بندی ها و یا تعداد بسته بندی وارد شده معتبر نمی باشد.."];
            }
        } else {
            $data = $request->data;
            if (!isset($data["packing_code"])) {
                return ["result" => false, "error" => "لطفا کد بسته بندی ها جهت تایید برگ خروج را وارد نمایید."];
            }

            $packing_codes = $data["packing_code"];
        }

        session([$var_session => $packing_codes]);


        if (count($product_request_form_forms) == 0) {
            return ["result" => false, "error" => "برگ خروج از انبار یافت نشده، لطفا با پشتیبانی تماس بگیرید."];
        }


        // چک کردن اینکه بسته بندی های درست وارد شده باشد.
        $result = self::CheckPackingForm($product_request_form_forms, $packing_codes);
        if (!$result["result"]) {
            return $result;
        }

        $reading_forms = $result["reading_forms"];

        foreach ($product_request_form_forms as $product_request_form_form) {
            if (in_array($product_request_form_form->form_id, $reading_forms) || in_array($product_request_form_form->input_form_id, $reading_forms)) {
                switch ($product_request_form_form->form->status_id) {
                    case 500000535:            // در انتظار تایید کنترل کیفیت)

                        if ($warehouse->warehouse_type_id == 2) { // انبارک ماشین

                            $machine = Machine::where("warehouse_id", $warehouse->id)->first();

                            $last_log = MachineLog::where("machine_id", $machine->id)->orderByDesc("id")->first();
                            if ($last_log && $last_log->machine_event_type_id == 650) {
                                // در حال تحویل شیفت
                                return [
                                    "result" => false,
                                    "error" => "با توجه به اینکه ماشین " . $machine->caption . " در حال تحویل شیفت می باشید، امکان تایید تحویل کالا وجود ندارد، لطفا به اپراتور مسئول (" . $last_log->operator->fullname() . ") جهت تایید تحویل شیفت اطلاع دهید."
                                ];

                            }
                            // لاگ ماشین
                            $machine_log = new MachineLog();
                            $machine_log->machine_event_type_id = 700;//  تایید تحویل مواد اولیه
                            event(new MachineLogEvent($machine, $machine_log));
                        }


                        // در صورت مجاز بودن فرم تایید و تراکنش انبار ثبت شود.
                        $product_request_form_form->product_request_form->checkIfValidConfirmRequest($product_request_form_form->form_id);

//                // در این تابع وضعیت جدید فرم ثبت می شود.
                        $product_request_form_form->product_request_form->updateExistFormStatusForm($product_request_form_form->form, 7005002); // تایید دریافت مواد اولیه


                        break;

                    default:
                        return [
                            "result" => false,
                            "error" => "وضعیت فرم جهت تایید معتبر نمی باشد." . $product_request_form_form->form->code
                        ];

                }

            }
        }

        return ["result" => true];
    }

    public static function CheckPackingForm($product_request_form_forms, $packing_codes)
    {
        $reading_forms = [];
        $message = "";
        foreach ($product_request_form_forms as $product_request_form_form) {
            if ($product_request_form_form->form->status_id == 500000535) { // در انتظار تایید کنترل کیفیت

                $checking_form_id = $product_request_form_form->form_id;


                $reading_forms[$checking_form_id] = $checking_form_id;
                $packing_form_list = PackingForm::
                join("packing_form_item", "packing_form_id", "packing_forms.id")->
                join("form_item", "packing_form_item_id", "packing_form_item.id")->
                where("form_item.form_id", $checking_form_id)->
                select("packing_forms.*")->
                get()->keyBy("code");

                $packing_form_list_array = [];
                foreach ($packing_form_list as $packing_form_item) {
                    $packing_form_list_array[$packing_form_item->code] = $packing_form_item;
                }

                $packing_form_ids = [];
                $packing_form_codes = array_keys($packing_form_list_array);
                if (isset($packing_codes[$checking_form_id])) {
                    $packing_codes_form = $packing_codes[$checking_form_id];
                    foreach ($packing_codes_form as $item) {

                        if (!in_array("DCPK/" . $item, $packing_form_codes)) {
                            $packing_form = PackingForm::where("code", "DCPK/" . $item)->first();
                            if ($packing_form) {
                                continue;
                            }
                            return ["result" => false, "error" => "بسته بندی " . "DCPK/" . $item . " در سامانه وجود ندارد و یا جزء بسته بندی های مجاز نیست."];
                        }
                        $packing_form_ids[] = $packing_form_list_array["DCPK/" . $item]->id;

                    }
                }

                $master_packing_form_ids = PackingForm::MasterPackingFormIds($packing_form_list);
                $master_packing_form_codes = PackingForm::
                whereIn("id", $master_packing_form_ids)->
                pluck("code", "code")->toArray();

                $master_packing_form_reading_codes = [];

                if (isset($packing_codes[$checking_form_id])) {

                    foreach ($packing_codes[$checking_form_id] as $code) {
                        if (!in_array("DCPK/" . $code, $master_packing_form_codes)) {
                            $message .= "بسته بندی " . "DCPK/" . $code . " در لیست بسته بندی های برگ خروج " . $product_request_form_form->form->code . " وجود ندارد." . "<br/>";

                        }
                        if (isset($master_packing_form_reading_codes[$code])) {
                            $message .= "بسته بندی " . "DCPK/" . $code . "  در برگ خروج " . $product_request_form_form->form->code . "  تکراری وارد شده است." . "<br/>";

                        }
                        $master_packing_form_reading_codes[$code] = $code;

                    }
                } else {
                    $message .= "هیچ بسته بندی از برگ خروچ" . $product_request_form_form->form->code . " وارد نشده است." . "<br/>";
                }

            }
        }


        if ($message != "") {
            return ["result" => false, "error" => $message];
        }

        return [
            "result" => true,
            "reading_forms" => $reading_forms
        ];
    }

    public static function has_allow_to_reject()
    {

        // دسترسی به ما؟زول عدم تایید
        $material_delivery_reject = true;
        $post_user = Auth::user()->posts->first();
        if (
            !$post_user->checkButtonPermission("fabric_raw.jacquard.machine.material_delivery_reject." . "index")
            &&
            !$post_user->checkButtonPermission("warps.matthys.machine.material_delivery_reject." . "index")

        ) {

            $material_delivery_reject = false;
        }

        return $material_delivery_reject;
    }

    public static function has_allow_to_confirm()
    {
        // دسترسی به ما؟زول تایید
        $material_delivery_confirmation = true;
        $post_user = Auth::user()->posts->first();
        if (
            !$post_user->checkButtonPermission("fabric_raw.jacquard.machine.material_delivery_confirmation." . "index")
            &&
            !$post_user->checkButtonPermission("warps.matthys.machine.material_delivery_confirmation." . "index")
            &&
            !$post_user->checkButtonPermission("warps.karl_mayer.machine.material_delivery_confirmation." . "index")
            &&
            !$post_user->checkButtonPermission("fabric.finishing_machine.machine.material_delivery_confirmation." . "index")
        ) {

            $material_delivery_confirmation = false;
        }

        return $material_delivery_confirmation;
    }
}

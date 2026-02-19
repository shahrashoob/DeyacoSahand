<?php

namespace App\Http\Controllers\Warehouse\Input;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utility\Script\Script1008Controller;
use App\Models\Accounting\CostCenter;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Order\OppKind;
use App\Models\Order\TransKind;
use App\Models\Utility\Option;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProductBlock;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EntryFormController extends Controller
{
    //
    public $route_path = "wh.input.entry_form.";
    public $view_path = "warehouse.input.entry_form.";

    public function index($is_edit = false)
    {

        //چک کردن پرینتر
        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }

        $request = null;
        if ($is_edit && session("request_data")) {
            $request = session("request_data");

        }
        $warehouse_id = isset($request["warehouse_id"]) ? $request["warehouse_id"] : 0;
        $degree_id = isset($request["degree_id"]) ? $request["degree_id"] : 0;
        $opp_kind_id = isset($request["opp_kind_id"]) ? $request["opp_kind_id"] : 0;
        $trans_kind_id = isset($request["trans_kind_id"]) ? $request["trans_kind_id"] : 0;
        $product_id = 0;
        $packing_type_id = isset($request["packing_type_id"]) ? $request["packing_type_id"] : 0;
        $cost_center_id = isset($request["cost_center_id"]) ? $request["cost_center_id"] : 0;
        $packing_form_rows = isset($request["packing_form_rows"]) ? $request["packing_form_rows"] : 0;
        $lot_number_code = isset($request["lot_number_code"]) ? $request["lot_number_code"] : "";
        $description = isset($request["description"]) ? $request["description"] : "";
        $enter_gross_weight = isset($request["enter_gross_weight"]) ? $request["enter_gross_weight"] : 1;
        $enter_weight = isset($request["enter_weight"]) ? $request["enter_weight"] : 0;
        $enter_unit_amount = isset($request["enter_unit_amount"]) ? $request["enter_unit_amount"] : 0;;
        $warehouse_option = Option::get("degree_warehouse", $warehouse_id, $degree_id);

        $opp_kind_option = Option::get("opp_kind", $opp_kind_id);
        $trans_kind_option = Option::get("trans_kind", $trans_kind_id, 1);

        $product_option = Option::get("product_record_entry_into_warehouse_manually", $product_id);
        $packing_type_option = Option::get("packing_type", $packing_type_id, -1);
        $degree_option = Option::get("degree", $degree_id, -1);
        $cost_center_option = Option::get("cost_center", $cost_center_id);


        $list_goods_kind_product = [];
        foreach (Product::all() as $item) {
            $list_goods_kind_product[$item->id] = $item->goods_kind_id;
        }


        session([
            "one_time_token" => rand(0, 1000000)
        ]);


        $first_packing_type_layers = PackingType::pluck("first_packing_type_id", "id")->toArray();

        return view($this->view_path . "index",
            compact("opp_kind_option",
                "warehouse_option", "degree_option", "lot_number_code",
                "description", "lot_number_code", "first_packing_type_layers",
                "trans_kind_option", "product_option", "packing_type_option",
                "cost_center_option", "packing_form_rows", "list_goods_kind_product",
                "enter_weight", "enter_gross_weight", "enter_unit_amount"));


    }

    public function submit(Request $request)
    {


        $product = Product::find($request->product_id);
        $packing_type = PackingType::find($request->packing_type_id);
        $degree = Degree::find($request->degree_id);
        $lot_number_code = $request->lot_number_code;

        $warehouse = Warehouse::find($request->warehouse_id);
        $opp_kind = OppKind::find($request->opp_kind_id);

        $trans_kind = TransKind::find($request->trans_kind_id);
        $cost_center = CostCenter::find($request->cost_center_id);
        $description = $request->description;
        $enter_gross_weight = $request->enter_gross_weight;
        $enter_weight = $request->enter_weight;
        $unit_amount = $request->unit_amount;
        $packing_form_rows = [];
        $packing_form_rows_request = ($request->packing_form_rows);

        session([
            "request_data" => $request->all()
        ]);

        // بررسی اینکه کالا در انبارگردانی نباشد
        $warehouse_ids = [];
        $warehouse_ids[] = $warehouse->id;

        $product_ids_for_check [] = $product->id;
        $result_warehouse = WarehouseProductBlock::CheckProduct($warehouse_ids, $product_ids_for_check);

        if (!$result_warehouse["result"]) {
            $error_message = $result_warehouse["error"];
            return redirect()->route($this->route_path . "index", 1)->withErrors($error_message);

        }
        // چک کردن اینکه وزن خالص/ناخالص/مقدار را وارد کرده باشند.
        foreach ($packing_form_rows_request as $item) {
            if (isset($item["gross_weight"]) || isset($item["weight"]) || isset($item["unit_amount"])) {
                $packing_form_rows[] = $item;
                if ($product->unit->weight_conversion_rate == 0 && !isset($item["unit_amount"])) {
                    return redirect()->route($this->route_path . "index", 1)->withErrors("ثبت مقدار واحد اصلی برای کالا الزامی است.");

                }
            }

        }

        // حداقل یک سطر وجود داشته باشد.
        if (count($packing_form_rows) == 0) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("لطفا حداقل یک بسته بندی اضافه کنید.");

        }

        $lot_number = LotNumber::where([
            "product_id" => $product->id ?? 0,
            "code" => $request->lot_number_code
        ])->first();

        $packing_type_weight = 0;
        $packing_type_sub_weight = 0;
        // وزن بسته بندی اصلی
        $packing_type_weight_result = PackingType::getWeight($packing_type);
        if (!$packing_type_weight_result["result"]) {
            return redirect()->route($this->route_path . "index", 1)->withErrors($packing_type_weight_result["error"]);

        } else {
            $packing_type_weight = $packing_type_weight_result["weight"];
        }

        if ($packing_type->first_packing_type) {
            // وزن بسته بندی فرعی
            $packing_type_weight_result = PackingType::getWeight($packing_type->first_packing_type);
            if (!$packing_type_weight_result["result"]) {
                return redirect()->route($this->route_path . "index", 1)->withErrors($packing_type_weight_result["error"]);

            } else {
                $packing_type_sub_weight = $packing_type_weight_result["weight"];
            }
        }


        if (!$enter_weight && !$enter_gross_weight) {
            if ($product->unit->weight_conversion_rate == 0) { // واحد اصلی وزنی نیست.
                return redirect()->route($this->route_path . "index", 1)->withErrors("لطفا یکی از موارد وزن خالص / وزن ناخالص را انتخاب نمایید.");

            }
        }

        foreach ($packing_form_rows as &$item_row) {


// واحد اصلی وزنی است و از روی آن مقدار خالص ناخالص را به دست می آوریم.
            if (!$enter_weight && !$enter_gross_weight) {
                $item_row["weight"] = $item_row["unit_amount"] * $product->unit->weight_conversion_rate;
            }
            if (($enter_weight && !$enter_gross_weight) || (!$enter_weight && !$enter_gross_weight)) {
                $item_row["gross_weight"] = $item_row["weight"] + $packing_type_weight + $packing_type_sub_weight * $item_row["sub_packing_form_number"];
            }

            $item_row["unit_amount"] = isset($item_row["unit_amount"]) ? $item_row["unit_amount"] : null;
            $result = PackingType::getAmountFromWeight(
                $product,
                $packing_type,
                $item_row["gross_weight"],
                isset($item_row["sub_packing_form_number"]) ? $item_row["sub_packing_form_number"] : 0,
                null,
                $item_row["unit_amount"]


            );

            if (!$result["result"]) {
                return redirect()->route($this->route_path . "index", 1)->withErrors($result["error"]);

            }
            if ($result["weight"] <= 0) {
                return redirect()->route($this->route_path . "index", 1)->withErrors("مقدار وزن خالص باید عددی بزرگتر از صفر باشد.");

            }
            if ($result["final_amount"] <= 0) {
                return redirect()->route($this->route_path . "index", 1)->withErrors("مقدار نهایی باید عددی بزرگتر از صفر باشد.");

            }
            $item_row["final_amount"] = $result["final_amount"];
            $item_row["sub_amount"] = $result["sub_amount"];
            $item_row["weight"] = $result["weight"];


        }


        $packing_form_rows = json_encode($packing_form_rows);

        return view($this->view_path . "submit", compact("lot_number",
            "product", "packing_type", "degree", "lot_number_code",
            "warehouse", "opp_kind", "trans_kind", "cost_center", "description",
            "packing_form_rows", "enter_gross_weight", "enter_weight", "unit_amount"
        ));
    }

    public function confirm(Request $request)
    {

        session([
            "enter_weight" => $request->enter_weight,
            "enter_gross_weight" => $request->enter_gross_weight,
            "request_data" => $request->all()
        ]);

        if ( ! session( "one_time_token" ) ) {
            return redirect()->route( $this->route_path . "index" )->withErrors( "این فرم قبلا ثبت شده است." );
        }
        session([
            "one_time_token" => null
        ]);

        $worker = Worker::find(Auth::id());

        $product = Product::find($request->product_id);
        if (!$product) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("اطلاعات کالا نا معتبر است.");
        }
        if (!$product->goods_kind->record_entry_into_warehouse_manually) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("ثبت ورود به انبار به صورت دستی توسط اپراتور برای کالا امکان پذیر نیست.");

        }
        $packing_type = PackingType::find($request->packing_type_id);
        if (!$packing_type) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("اطلاعات بسته بندی نا معتبر است.");
        }
        $degree = Degree::find($request->degree_id);
        if (!$degree) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("اطلاعات درجه نا معتبر است.");
        }


        $warehouse = Warehouse::find($request->warehouse_id);
        if (!$warehouse) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("اطلاعات انبار نا معتبر است.");
        }
        $opp_kind = OppKind::find($request->opp_kind_id);
        if (!$opp_kind) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("اطلاعات طرف حساب نا معتبر است.");
        }
        $trans_kind = TransKind::find($request->trans_kind_id);
        if (!$trans_kind) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("اطلاعات تراکنش نا معتبر است.");
        }
        $cost_center = CostCenter::find($request->cost_center_id);
        if (!$cost_center) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("اطلاعات مرکز هزینه نا معتبر است.");
        }

        $description = $request->description;

        $packing_form_rows = json_decode($request->packing_form_rows, true);
//        foreach ($packing_form_rows as $item){
//            echo $item->amount;
//        }
//        return is_array($packing_form_rows);
        if (!is_array($packing_form_rows) || count($packing_form_rows) == 0) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("اطلاعات لیست بسته ها نا معتبر است.");
        }

        // اگر تعداد کپی یک بسته بندی بیش از صفر بود، به تعداد، از بسته بندی کپی می کیریم.
        $k = 1;
        $packing_form_copy = $request->packing_form_copy;
        foreach ($packing_form_rows as $item) {

            if (isset($packing_form_copy[$k]) && $packing_form_copy[$k] > 1) {
                for ($i = 1; $i < $packing_form_copy[$k]; $i++) {
                    $packing_form_rows[] = $item;
                }
            }
            $k++;
        }

       if(count($packing_form_rows)> 60 ){
           return redirect()->route($this->route_path . "index", 1)->withErrors("در هر بار ثبت انبار حداکثر می توانید 60 بسته بندی را به صورت یکجا ورود بزنید.");
       }
        if ($request->lot_number_code == "") {
            return redirect()->route($this->route_path . "index", 1)->withErrors("اطلاعات لات کالا نا معتبر است.");

        }
//        if ( ! $packing_type->first_packing_type_id ) {
//            return redirect()->route( $this->route_path . "index" )->withErrors( "فرم ورود به انبار برای بسته بندی های تک لایه پیاده سازی نشده است." );
//
//        }


        $packing_type_layers = $packing_type->layers()->orderBy("layer_code")->get();

        if (count($packing_type_layers) > 2) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("ایجاد فرم ورود به انبار برای بسته بندی هایی با بیش از 2 لایه تعریف نشده است.");
        }
        if (count($packing_type_layers) == 0) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("لایه های بسته بندی برای بسته بندی تعریف نشده است.");
        }


        $lot_number = LotNumber::where([
            "product_id" => $product->id,
            "code" => $request->lot_number_code,
        ])->first();

        if (!$lot_number) {
            $lot_number = new LotNumber();
            $lot_number->code = $request->lot_number_code;
            $lot_number->product_id = $product->id;
            $lot_number->user_id = Auth::user()->id;
            $lot_number->save();
        }

        $data["product_id"] = $request->product_id;
        $data["packing_type_id"] = $request->packing_type_id;
        $data["degree_id"] = $request->degree_id;
        $data["lot_number_id"] = $lot_number->id;
        $data["warehouse_id"] = $request->warehouse_id;
        $data["trans_kind_id"] = $request->trans_kind_id;
        $data["opp_kind_id"] = $request->opp_kind_id;
        $data["cost_center_id"] = $request->cost_center_id;
        $data["description"] = $request->description;
        $data["packing_form_rows"] = json_encode($packing_form_rows);
        $data["print"] = $request->print;
        $data["user_id"] = $worker->id;

        DB::table("new_entry_form_for_script_1008")->insert($data);

        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات بسته بندی ها " . " در انبار با موفقیت ثبت گردید و در انتظار پردازش قرار گرفت."]);


        new Script1008Controller();


    }

    public function app_packing_api(Request $request)
    {

        $product = Product::find($request->product_id);

        $product_option = Option::get("product_active", $request->product_id);
        $packing_type_option = Option::get("packing_type", $request->packing_type_id, $product->goods_kind_id ?? 0);
        $degree_option = Option::get("degree", $request->degree_id, $product->goods_kind_id ?? 0);
        $lot_number_code = $request->lot_number_code;


        $warehouse_option = Option::get("degree_warehouse", $request->warehouse_id, $request->degree_id ?? 0);
        $opp_kind_option = Option::get("opp_kind", $request->opp_kind_id);

        $trans_kind = TransKind::find($request->trans_kind_id);
        $trans_kind_option = Option::get("trans_kind", $trans_kind->id ?? "", 1);

        $cost_center_option = Option::get("cost_center", $request->cost_center_id);

        $description = $request->description;

        $enter_gross_weight = $request->enter_gross_weight;
        $enter_weight = $request->enter_weight;
        $enter_unit_amount = $request->enter_unit_amount;

        $first_packing_type_layers = PackingType::pluck("first_packing_type_id", "id")->toArray();

        $packing_form_rows = $request->packing_form_rows ?? [];
        $list_goods_kind_product = [];
        foreach (Product::all() as $item) {
            $list_goods_kind_product[$item->id] = $item->goods_kind_id;
        }

        if ($request->weight || $request->gross_weight) {
            $packing_form_rows[] = [
                "weight" => $request->weight,
                "gross_weight" => $request->gross_weight,
                "unit_amount" => $request->unit_amount,
                "sub_packing_form_number" => $request->sub_packing_form_number
            ];
        }


        return view($this->view_path . "_packing_form_rows",
            compact("opp_kind_option", "warehouse_option", "degree_option", "description", "lot_number_code",
                "trans_kind_option", "product_option",
                "packing_type_option", "cost_center_option", "packing_form_rows", "list_goods_kind_product",
                "enter_gross_weight",
                "enter_weight",
                "enter_unit_amount", "first_packing_type_layers"

            ));


    }

    /************************ ورود کالاهای بدون انبارش ********************/
    public function index2($is_edit = false)
    {

        //چک کردن پرینتر
        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }

        $request = null;
        if ($is_edit && session("request_data")) {
            $request = session("request_data");

        }
        $warehouse_id = isset($request["warehouse_id"]) ? $request["warehouse_id"] : 0;
        $degree_id = isset($request["degree_id"]) ? $request["degree_id"] : 0;
        $opp_kind_id = isset($request["opp_kind_id"]) ? $request["opp_kind_id"] : 0;
        $trans_kind_id = isset($request["trans_kind_id"]) ? $request["trans_kind_id"] : 0;
        $product_id = 0;
        $packing_type_id = isset($request["packing_type_id"]) ? $request["packing_type_id"] : 0;
        $cost_center_id = isset($request["cost_center_id"]) ? $request["cost_center_id"] : 0;
        $packing_form_rows = isset($request["packing_form_rows"]) ? $request["packing_form_rows"] : 0;
        $lot_number_code = isset($request["lot_number_code"]) ? $request["lot_number_code"] : "";
        $description = isset($request["description"]) ? $request["description"] : "";
        $enter_gross_weight = isset($request["enter_gross_weight"]) ? $request["enter_gross_weight"] : 1;
        $enter_weight = isset($request["enter_weight"]) ? $request["enter_weight"] : 0;
        $enter_unit_amount = isset($request["enter_unit_amount"]) ? $request["enter_unit_amount"] : 0;;
        $warehouse_option = Option::get("degree_warehouse", $warehouse_id, $degree_id);

        $opp_kind_option = Option::get("opp_kind", $opp_kind_id);
        $trans_kind_option = Option::get("trans_kind", $trans_kind_id, 1);

        $product_option = Option::get("product_record_entry_into_warehouse_manually", $product_id);
        $packing_type_option = Option::get("packing_type", $packing_type_id, -1);
        $degree_option = Option::get("degree", $degree_id, -1);
        $cost_center_option = Option::get("cost_center", $cost_center_id);


        $list_goods_kind_product = [];
        foreach (Product::all() as $item) {
            $list_goods_kind_product[$item->id] = $item->goods_kind_id;
        }


        session([
            "one_time_token" => rand(0, 1000000)
        ]);


        $first_packing_type_layers = PackingType::pluck("first_packing_type_id", "id")->toArray();

        return view($this->view_path . "index2",
            compact("opp_kind_option",
                "warehouse_option", "degree_option", "lot_number_code",
                "description", "lot_number_code", "first_packing_type_layers",
                "trans_kind_option", "product_option", "packing_type_option",
                "cost_center_option", "packing_form_rows", "list_goods_kind_product",
                "enter_weight", "enter_gross_weight", "enter_unit_amount"));


    }


    public function submit2(Request $request)
    {


        $product = Product::find($request->product_id);

        $degree = Degree::find($request->degree_id);
        $lot_number_code = $request->lot_number_code;

        $warehouse = Warehouse::find($request->warehouse_id);
        $opp_kind = OppKind::find($request->opp_kind_id);

        $trans_kind = TransKind::find($request->trans_kind_id);
        $cost_center = CostCenter::find($request->cost_center_id);
        $description = $request->description;
        $enter_gross_weight = $request->enter_gross_weight;
        $enter_weight = $request->enter_weight;
        $unit_amount = $request->unit_amount;
        $packing_form_rows = [];
        $packing_form_rows_request = ($request->packing_form_rows);

        session([
            "request_data" => $request->all()
        ]);

        $product_storage_type_id = Product\ProductWarehouseStorageType::
        where("product_id", $request->product_id)->
        whereIn("warehouse_storage_type_id", [1, 4])->
        first();
        if (!$product_storage_type_id) {
            return back()->withErrors("با توجه به نوع انبارش کالا امکان وورد برای کالا وجود ندارد، نوع انبارش باید از نوع 'بدون انبارش' یا 'انبارش بدون بسته بندی و مخزن(دپو)' باشد ");
        }

        // بررسی اینکه کالا در انبارگردانی نباشد
        $warehouse_ids = [];
        $warehouse_ids[] = $warehouse->id;

        $product_ids_for_check [] = $product->id;
        $result_warehouse = WarehouseProductBlock::CheckProduct($warehouse_ids, $product_ids_for_check);

        if (!$result_warehouse["result"]) {
            $error_message = $result_warehouse["error"];
            return redirect()->route($this->route_path . "index", 1)->withErrors($error_message);

        }
//        // چک کردن اینکه وزن خالص/ناخالص/مقدار را وارد کرده باشند.
//        foreach ( $packing_form_rows_request as $item ) {
//            if ( isset( $item["gross_weight"] ) || isset( $item["weight"] ) ||   isset( $item["unit_amount"] )) {
//                $packing_form_rows[] = $item;
//                if($product->unit->weight_conversion_rate==0 && ! isset( $item["unit_amount"] )){
//                    return redirect()->route( $this->route_path . "index",1 )->withErrors( "ثبت مقدار واحد اصلی برای کالا الزامی است." );
//
//                }
//            }
//
//        }

//        // حداقل یک سطر وجود داشته باشد.
//        if ( count( $packing_form_rows ) == 0 ) {
//            return redirect()->route( $this->route_path . "index",1 )->withErrors( "لطفا حداقل یک بسته بندی اضافه کنید." );
//
//        }

        // حداقل یک سطر وجود داشته باشد.
        if (isset($request->amount) && $request->amount <= 0) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("لطفا مقدار کالا را به درستی وارد کنید.");

        }
        $amount = $request->amount;


        $lot_number = LotNumber::where([
            "product_id" => $product->id ?? 0,
            "code" => $request->lot_number_code
        ])->first();

        $packing_type_weight = 0;
        $packing_type_sub_weight = 0;


        $packing_form_rows = json_encode($packing_form_rows);

        return view($this->view_path . "submit2", compact("lot_number",
            "product", "degree", "lot_number_code", "amount",
            "warehouse", "opp_kind", "trans_kind", "cost_center", "description",
            "packing_form_rows", "enter_gross_weight", "enter_weight", "unit_amount"
        ));
    }


    public function confirm2(Request $request)
    {

        session([
            "enter_weight" => $request->enter_weight,
            "enter_gross_weight" => $request->enter_gross_weight,
            "request_data" => $request->all()
        ]);

        if (!session("one_time_token")) {
            return redirect()->route($this->route_path . "index")->withErrors("این فرم قبلا ثبت شده است.");
        }
        session([
            "one_time_token" => null
        ]);

        $worker = Worker::find(Auth::id());

        $product = Product::find($request->product_id);
        if (!$product) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("اطلاعات کالا نا معتبر است.");
        }
        if (!$product->goods_kind->record_entry_into_warehouse_manually) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("ثبت ورود به انبار به صورت دستی توسط اپراتور برای کالا امکان پذیر نیست.");

        }

        $degree = Degree::find($request->degree_id);
        if (!$degree) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("اطلاعات درجه نا معتبر است.");
        }


        $warehouse = Warehouse::find($request->warehouse_id);
        if (!$warehouse) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("اطلاعات انبار نا معتبر است.");
        }
        $opp_kind = OppKind::find($request->opp_kind_id);
        if (!$opp_kind) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("اطلاعات طرف حساب نا معتبر است.");
        }
        $trans_kind = TransKind::find($request->trans_kind_id);
        if (!$trans_kind) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("اطلاعات تراکنش نا معتبر است.");
        }
        $cost_center = CostCenter::find($request->cost_center_id);
        if (!$cost_center) {
            return redirect()->route($this->route_path . "index", 1)->withErrors("اطلاعات مرکز هزینه نا معتبر است.");
        }

        $description = $request->description;

        $packing_form_rows = json_decode($request->packing_form_rows, true);
//        foreach ($packing_form_rows as $item){
//            echo $item->amount;
//        }
//        return is_array($packing_form_rows);
//        if ( ! is_array( $packing_form_rows ) || count( $packing_form_rows ) == 0 ) {
//            return redirect()->route( $this->route_path . "index",1 )->withErrors( "اطلاعات لیست بسته ها نا معتبر است." );
//        }

//        foreach ($packing_form_rows as $item){
//
//        }
        if ($request->lot_number_code == "") {
            return redirect()->route($this->route_path . "index", 1)->withErrors("اطلاعات لات کالا نا معتبر است.");

        }
//        if ( ! $packing_type->first_packing_type_id ) {
//            return redirect()->route( $this->route_path . "index" )->withErrors( "فرم ورود به انبار برای بسته بندی های تک لایه پیاده سازی نشده است." );
//
//        }


//        $packing_type_layers = $packing_type->layers()->orderBy( "layer_code" )->get();
//
//        if ( count( $packing_type_layers ) > 2 ) {
//            return redirect()->route( $this->route_path . "index",1 )->withErrors( "ایجاد فرم ورود به انبار برای بسته بندی هایی با بیش از 2 لایه تعریف نشده است." );
//        }
//        if ( count( $packing_type_layers ) == 0 ) {
//            return redirect()->route( $this->route_path . "index",1 )->withErrors( "لایه های بسته بندی برای بسته بندی تعریف نشده است." );
//        }


        $lot_number = LotNumber::where([
            "product_id" => $product->id,
            "code" => $request->lot_number_code,
        ])->first();

        if (!$lot_number) {
            $lot_number = new LotNumber();
            $lot_number->code = $request->lot_number_code;
            $lot_number->product_id = $product->id;
            $lot_number->user_id = Auth::user()->id;
            $lot_number->save();
        }

        $data["product_id"] = $request->product_id;
        $data["packing_type_id"] = $request->packing_type_id;
        $data["degree_id"] = $request->degree_id;
        $data["lot_number_id"] = $lot_number->id;
        $data["warehouse_id"] = $request->warehouse_id;
        $data["trans_kind_id"] = $request->trans_kind_id;
        $data["opp_kind_id"] = $request->opp_kind_id;
        $data["cost_center_id"] = $request->cost_center_id;
        $data["description"] = $request->description;
        $data["final_amount"] = $request->amount;
        $data["sub_amount"] = $request->sub_amount ?? "";
        $data["print"] = $request->print;
        $data["user_id"] = $worker->id;

        DB::table("new_entry_form_for_script_1008")->insert($data);

        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات بسته بندی ها " . " در انبار با موفقیت ثبت گردید و در انتظار پردازش قرار گرفت."]);


        new Script1008Controller();


    }
}

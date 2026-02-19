<?php

namespace App\Http\Controllers\Warehouse\Out;

use App\Events\Form\PackingLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Http\Controllers\Controller;
use App\Models\Accounting\CostCenter;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormSessionData;
use App\Models\LineProduct\Product\ProductWarehouseStorageType;
use App\Models\Order\OppKind;
use App\Models\Order\TransKind;
use App\Models\Utility\Option;
use App\Models\Utility\QueueOfLargeOperation;
use App\Models\Utility\Transport\TransportPackingForm;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;
use App\Models\Warehouse\WarehouseProductBlock;
use App\Models\Warehouse\WarehouseStorageType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\ExecutionStatus;

class ProductRequestFormImplementationController extends Controller
{
    // ثبت درخواست خروج از انبار ویژه دوره پیاده سازی
    public $route_path = "wh.out.product_request_form_implementation.";
    public $view_path = "warehouse.out.product_request_form_implementation.";

    public $read_product_info_type4 = 3;

    public function index(Request $request)
    {
        $product_option = Option::get("product_all", 0);
        $warehouse_option = Option::get("warehouse", 0, 0, [1]);
        $trans_kind_option = Option::get("trans_kind", -1, 2);
        $cost_center_option = Option::get("cost_center", 0);
        return view($this->view_path . "index", compact("warehouse_option", "product_option", "trans_kind_option", "cost_center_option"));


    }

    public function submit(Request $request)
    {


        $warehouse = Warehouse::find($request->warehouse_id);
        if (!$warehouse) {
            return back()->withErrors("لطفا انبار را انتخاب کنید.");
        }

        $cost_center = CostCenter::find($request->cost_center_id);
        if (!$cost_center) {
            return back()->withErrors("لطفا مرکز هزینه را انتخاب کنید.");
        }


        $trans_kind = TransKind::find($request->trans_kind_id);
        if (!$trans_kind) {
            return back()->withErrors("لطفا نوع تراکنش را انتخاب کنید.");
        }

        $products = Product::whereIn("id", $request->product_ids)->get();
        if (count($products) == 0) {
            return back()->withErrors("لطفا حداقل یک بسته بندی را انتخاب نمایید.");
        }

        $error = "";
        foreach ($products as $item) {
            if (!$item->goods_kind->record_out_of_warehouse_manually) {
                $error .= "امکان ثبت خروج از انبار به صورت دستی برای بسته بندی که کالای " . $item->caption . " در آن قرار دارد، وجود ندارد. " . "<br/>";
            }
        }

        if ($error != "") {
            return back()->withErrors($error);
        }

        session([
            "product_ids" => $products->pluck("id")->toArray(),
            "warehouse_id" => $warehouse->id,
            "cost_center_id" => $cost_center->id,
            "trans_kind_id" => $trans_kind->id,
        ]);

        return redirect()->route($this->route_path . "product_request_form_item");

    }

    public function product_request_form_item()
    {
        $product_ids = session("product_ids");
        $warehouse_id = session("warehouse_id");

        $products = Product::whereIn("id", $product_ids)->get();

        if (count($products) == 0) {
            return redirect()->route($this->route_path . "index")->withErrors("اطلاعات کالا به درستی ثبت نشده است، لطفا مجدد تلاش کنید.");
        }

        $warehouse = Warehouse::find($warehouse_id);
        if (!$warehouse) {
            return redirect()->route($this->route_path . "index")->withErrors("شناسه انبار به درستی ثبت نشده است، لطفا مجدد تلاش کنید.");
        }

        $packing_form_types = Product\ProductPackingType::
        whereIn("product_id", $product_ids)->
        with("packing_type")->
        get();

        $packing_type_list = [];
        foreach ($packing_form_types as $packing_type) {
            $packing_type_list[$packing_type->product_id][$packing_type->packing_type_id] = $packing_type->packing_type->caption;
        }

        $degrees = Degree::where("active_status_id", 1200)->get();

        $degree_list = [];
        foreach ($degrees as $degree) {
            $degree_list[$degree->goods_kind_id][$degree->id] = $degree;
        }

        return view($this->view_path . "product_request_form_item", compact("products", "degree_list", "packing_type_list", "warehouse"));
    }

    public function submit_product_request_form(Request $request)
    {
        $product_ids = session("product_ids");
        $warehouse_id = session("warehouse_id");
        $cost_center_id = session("cost_center_id");
        $trans_kind_id = session("trans_kind_id");

        $cost_center = CostCenter::find($cost_center_id);
        if (!$cost_center) {
            return back()->withErrors("لطفا مرکز هزینه را انتخاب کنید.");
        }


        $trans_kind = TransKind::find($trans_kind_id);
        if (!$trans_kind) {
            return back()->withErrors("لطفا نوع تراکنش را انتخاب کنید.");
        }

        $products = Product::whereIn("id", $product_ids)->get();

        if (count($products) == 0) {
            return redirect()->route($this->route_path . "index")->withErrors("اطلاعات کالا به درستی ثبت نشده است، لطفا مجدد تلاش کنید.");
        }

        $warehouse = Warehouse::find($warehouse_id);
        if (!$warehouse) {
            return redirect()->route($this->route_path . "index")->withErrors("شناسه انبار به درستی ثبت نشده است، لطفا مجدد تلاش کنید.");
        }

        $packing_form_types = Product\ProductPackingType::
        whereIn("product_id", $product_ids)->
        with("packing_type")->
        get();

        $packing_type_list = [];
        foreach ($packing_form_types as $packing_type) {
            $packing_type_list[$packing_type->product_id][$packing_type->packing_type_id] = $packing_type->packing_type->caption;
        }

        $degrees = Degree::where("active_status_id", 1200)->get();

        $degree_list = [];
        foreach ($degrees as $degree) {
            $degree_list[$degree->goods_kind_id][$degree->id] = $degree;
        }

        $product_amount_list = [];

        foreach ($products as $product) {

            // چک کردن مقدار کالا
            $product_amount_key = "product_amount_" . $product->id;
            if (!$request->$product_amount_key || $request->$product_amount_key <= 0) {
                return back()->withErrors("لطفا مقدار خروج برای کالای " . $product->caption . " را مشخص کنید.");
            }
            $product_amount_list[$product->id] = $request->$product_amount_key;
            // چک کردندرجه های کالا
            if (!isset($request->degree[$product->id])) {
                return back()->withErrors("لطفا حداقل یک درجه برای کالای " . $product->caption . "  مشخص کنید.");
            }

            // چک کردن بسته بندی هاا
            if (!isset($request->packing_type[$product->id])) {
                return back()->withErrors("لطفا حداقل یک نوع بسته بندی برای کالای " . $product->packing_type . " مشخص کنید.");
            }
        }

        $degree_list_select = $request->degree;
        $packing_type_list_select = $request->packing_type;
        session([
            "product_amount_list" => $product_amount_list,
            "degree_list_select" => $degree_list_select,
            "packing_type_list_select" => $packing_type_list_select,
        ]);

        return view($this->view_path . "product_request_form_item_confirm", compact("product_amount_list", "products", "degree_list", "packing_type_list", "warehouse", "degree_list_select", "packing_type_list_select", "cost_center", "trans_kind"));

    }

    public function confirm_product_request_form()
    {
        $product_ids = session("product_ids");
        $warehouse_id = session("warehouse_id");

        $cost_center_id = session("cost_center_id");
        $trans_kind_id = session("trans_kind_id");

        $product_amount_list = session("product_amount_list");
        $degree_list_select = session("degree_list_select");
        $packing_type_list_select = session("packing_type_list_select");

        $products = Product::whereIn("id", $product_ids)->get();

        if (count($products) == 0) {
            return redirect()->route($this->route_path . "index")->withErrors("اطلاعات کالا به درستی ثبت نشده است، لطفا مجدد تلاش کنید.");
        }

        $warehouse = Warehouse::find($warehouse_id);
        if (!$warehouse) {
            return redirect()->route($this->route_path . "index")->withErrors("شناسه انبار به درستی ثبت نشده است، لطفا مجدد تلاش کنید.");
        }


        $other["product_ids"] = $product_ids;
        $other["warehouse_id"] = $warehouse_id;
        $other["cost_center_id"] = $cost_center_id;
        $other["trans_kind_id"] = $trans_kind_id;
        $other["product_amount_list"] = $product_amount_list;
        $other["degree_list_select"] = $degree_list_select;
        $other["packing_type_list_select"] = $packing_type_list_select;

        $other["user_id"] = Auth::user()->id;

        ProductRequestForm::newRequest(
            null, Auth::id(),
            80, 1, $other,
            Carbon::now(),
        );

        return redirect()->route($this->route_path."index")->with(["success" => "ثبت درخواست با موفقیت انجام شد."]);
    }


}
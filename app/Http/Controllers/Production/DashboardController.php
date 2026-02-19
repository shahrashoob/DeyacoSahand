<?php

namespace App\Http\Controllers\Production;

use App\Exports\Production\ProductionDetailsReportExport;
use App\Exports\ProductionCard500Export;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\ProductionCard\GeneralChangeAllocationController;
use App\Http\Controllers\Report\Report1003Controller;
use App\Http\Controllers\Sales\ProductRequestPermissionController;
use App\Http\Controllers\Utility\Script\Script1021Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKindProperty;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ConsumedProduct\ConsumedProduct;
use App\Models\Order\Order;
use App\Models\Order\OrderLog;
use App\Models\Post\PostStatus;
use App\Models\Production\Production;
use App\Models\Production\ProductionDetailsReport;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\QueueOfLargeOperation;
use App\Models\Utility\Setting;
use App\Models\Warehouse\WarehouseProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use function Sodium\compare;

class DashboardController extends Controller
{

    //
    public function index(Request $request)
    {
        // مدیریت دسترسی به صفحه بازگشت: آخرین داشبورد همیشه ذخیره می گردد.
        $last_production_dashboard_view = session("last_production_dashboard_view");
        if (!$last_production_dashboard_view || $request->view_form_menu) {
            session([
                "last_production_dashboard_view" => "index"
            ]);
        }

        if ($last_production_dashboard_view == "index_details") {
            return redirect()->route('production.dashboard.index_details');
        }
        /*******************************************************/


        $lines_id_permission = \Auth::user()->posts->first()->post->get_lines_id_permission();

        $allowed_status_ids = PostStatus::getAllowedStatus();
        $allowed_status_ids_all = $allowed_status_ids;
        if ($request->waiting_status_id != 0 && !in_array($request->waiting_status_id, $allowed_status_ids)) {
            return back()->withErrors("شما اجازه دسترسی به مشاهده کارت های تولید با وضعیت انتخاب شده را ندارید");
        }
        if (count($lines_id_permission) == 0) {
            return back()->withErrors("هیچ گونه دسترسی خط برای شما در کارتابل تولید تعریف نشده است.");
        }

        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
            $waiting_status_id = $request->waiting_status_id;
            $goods_kind_property_id = $request->goods_kind_property_id;
            $goods_kind_id = $request->goods_kind_id;
            $search_production_channel_type = $request->search_production_channel_type;

        } else {
            $search = session("search_production_card");
            $order_by = session("order_by_production_card") ?? "production_cards.updated_at__desc";
            $waiting_status_id = session("waiting_status_id_production_card");
            $goods_kind_property_id = session("goods_kind_property_id_production_card");
            $goods_kind_id = session("goods_kind_id_production_card");
            $search_production_channel_type = session("search_production_channel_type_production_card");

        }
        session([
            "search_production_card" => $search,
            "order_by_production_card" => $order_by,
            "waiting_status_id_production_card" => $waiting_status_id,
            "goods_kind_property_id_production_card" => $goods_kind_property_id,
            "goods_kind_id_production_card" => $goods_kind_id,
            "search_production_channel_type_production_card" => $search_production_channel_type,
        ]);

        // search
        if ($waiting_status_id != 0) {
            $allowed_status_ids = [];
            $allowed_status_ids[] = $waiting_status_id;
        }

        // جستجوی بر اساس مشخصه کالا
        $product_ids = [];

        if ($goods_kind_property_id) {
            $product_ids = GoodsKindPropertyValue::where([
                "goods_kind_property_id" => $goods_kind_property_id,
                "value" => $search
            ])->pluck("product_id", "product_id")->toArray();
            $product_ids[] = -1;

        }

        // بررسی جستجو با کانال تولید
        if ($search_production_channel_type) {
            $production_channel_type_product_ids = LineProductStation::
            join("production_channel_types", "production_channel_types.id", "production_channel_type_id")->
            where("production_channel_types.caption", "like", "%" . $search_production_channel_type . "%")->
            pluck("product_id", "product_id")->toArray();

            // اگر برای کالای چیزی سرچ کردیم که از سرچ ها کم می کنیم وگر نه مقدار آن را برابر با مقدار کالاهای کانال تولید قرار می دهیم.
            if (count($product_ids) > 0) {
                foreach ($product_ids as $key => $product_id) {
                    if (!in_array($product_id, $production_channel_type_product_ids)) {
                        unset($product_ids[$key]);
                    }
                }
            } else {
                $product_ids = $production_channel_type_product_ids;
                $product_ids[] = -1;
            }

        }

        $list = Production::search($goods_kind_property_id ? "" : $search, $order_by, $lines_id_permission, [
            "parent_production_id",
            "production_cards.number_in_carton",
            "production_cards.product_id",
            "serial",
            "production_cards.number",
            "production_cards.status_id",
            "order_list_id",
            "nth_in_day",
            "production_cards.created_at",
            "production_cards.prioriry_id",
            "production_cards.waiting_status_id",
            "production_type_id", "order_id", "production_cards.customer_id"
        ], $allowed_status_ids,
            1,
            $product_ids,
            [],
            $goods_kind_id ?? false
        );

        $list = $list->paginate(30);

        // لیست کالاهایی که در صفحه وجود دارند
        $property_product_ids = [];
        foreach ($list as $item) {
            $property_product_ids[] = $item->product_id;
        }
        // مشخص کردن رنگ مشخصه در رسته کالایی

        // آیا مقدار مشخصه اصلی در داشبورد نمایش داده شود؟
        $setting_value = Setting::getIntegerValueList([
            "property1_show_in_production_dashboard",
            "property2_show_in_production_dashboard",
            "production_channel_type_show_in_production_dashboard"
        ]);

        $property1_show_in_production_dashboard = $setting_value["property1_show_in_production_dashboard"];
        $property2_show_in_production_dashboard = $setting_value["property2_show_in_production_dashboard"];
        $production_channel_type_show_in_production_dashboard = $setting_value["production_channel_type_show_in_production_dashboard"];
        $goods_kind_property_values_products = [];

        if ($property1_show_in_production_dashboard || $property2_show_in_production_dashboard) {


            $property_list = [];

            // کدام مشخصه باید نمایش داده شود.
            if ($property1_show_in_production_dashboard) {
                $property_list[] = "property1_id";
            }
            if ($property2_show_in_production_dashboard) {
                $property_list[] = "property2_id";
            }

            $property_ids_goods_kind = GoodsKind::where("active_status_id", 1200)->select($property_list)->get();
            $property_ids = [];
            foreach ($property_ids_goods_kind as $item) {
                if ($item->property1_id)
                    $property_ids[] = $item->property1_id;
                if ($item->property2_id)
                    $property_ids[] = $item->property2_id;
            }

            $goods_kind_property_values = GoodsKindPropertyValue::
            leftJoin('goods_kind_property_options', function ($join) {
                $join->on("goods_kind_property_values.goods_kind_property_id", "=", "goods_kind_property_options.goods_kind_property_id")
                    ->on("goods_kind_property_values.value", "=", "goods_kind_property_options.id");
            })->
            whereIn("goods_kind_property_values.product_id", $property_product_ids)->
            whereIn("goods_kind_property_values.goods_kind_property_id", $property_ids)->select("goods_kind_property_values.product_id", "goods_kind_property_values.goods_kind_property_id", "value", "color", "caption")->get();

            $goods_kind_property_values_products = [];
            foreach ($goods_kind_property_values as $goods_kind_property_value) {
                $goods_kind_property_values_products[$goods_kind_property_value->product_id][$goods_kind_property_value->goods_kind_property_id] = $goods_kind_property_value;
            }
//        return $goods_kind_property_values_products;

        }

        // نمایش لیست کانال تولید ها در داشتبورد مدیریت تولید
        $production_channel_type_list = [];
        if ($production_channel_type_show_in_production_dashboard) {
            $production_channel_type_list_query = LineProductStation::
            whereIn("product_id", $property_product_ids)->
            groupBy("product_id")->with("production_channel_type")->
            select("product_id", "production_channel_type_id")->
            get();
            foreach ($production_channel_type_list_query as $production_channel_type) {
                $production_channel_type_list[$production_channel_type->product_id] = [
                    "caption" => $production_channel_type->production_channel_type->caption ?? "",
                    "color" => $production_channel_type->production_channel_type->color ?? ""
                ];
            }
        }

        $machine_re_allocation = MachineAllocation::where("status_id", 5310050)->
        whereNotNull("parent_allocation_id")->groupBy("production_id")->pluck("id", "production_id")->toArray();
        $order_by_Option = Option::OrderBy("production", $order_by);

        $goods_kind_option = Option::get("goods_kind", $goods_kind_id);

        $property_option = Option::get("get_property_by_goods_kind", $goods_kind_property_id, $goods_kind_id);

        //نمایش کانال تولید سطح بالا
        $parent_production_product_ids = [];
        foreach ($list as $item) {
            if ($item->parent_production_id) {
                $parent_production_product_ids[$item->parent_production_id] = $item->parent_production->product_id;
            }
        }
        $parent_production_product_ids[] = -1;
        $production_channel_type_parent_list =
            LineProductStation::
            join("production_channel_types", "production_channel_type_id", "=", "production_channel_types.id")->
            whereIn("product_id", $parent_production_product_ids)->groupBy("product_id")->select("production_channel_types.caption", "product_id")->
            pluck("caption", "product_id")->toArray();


        $waiting_status_option = Option::get("production_waiting_status", $waiting_status_id, [7001, 7201, 7301], $allowed_status_ids_all);

        return view("production.dashboard.list", compact("property_option", "machine_re_allocation",
            "goods_kind_property_values_products", "property1_show_in_production_dashboard", "production_channel_type_show_in_production_dashboard",
            "property2_show_in_production_dashboard", "production_channel_type_list", "production_channel_type_parent_list",
            "production_channel_type_show_in_production_dashboard", "search_production_channel_type",
            "goods_kind_option", "waiting_status_option", "order_by_Option", "list", "search", "waiting_status_id"));
    }

    public function view_card(Production $production, $back_url_type = "")
    {

        if (!in_array($production->product->goods_kind->caption_en, ["Fabric_Raw", "Warps", "Fabric"])) {
            return redirect()->back()->withErrors("فرایند کارت تولید مربوط به رسته کالا یافت نشد.  ");
        }

        switch ($production->product->supply_type_id) {
            case 1: // تولید داخل
                return redirect()->route(
                    Str::lower($production->product->goods_kind->caption_en) . ".production_card.view_card",
                    [$production, $back_url_type]
                );
            case 3:
                return redirect()->route("contractor.admin.dashboard.view_card", [$production, $back_url_type]);
            default:
                return back()->withErrors("با توجه به نوع تامین کالا، داشبورد مورد نظر یافت نشد.");
        }


    }

    public function allocation_cancel(Allocation $allocation, Production $production)
    {

        if (!in_array($production->product->goods_kind->caption_en, ["Fabric_Raw", "Warps", "Fabric"])) {
            return redirect()->back()->withErrors("فرایند کارت تولید مربوط به رسته کالا یافت نشد.  ");
        }

        return redirect()->route(
            Str::lower($production->product->goods_kind->caption_en) . ".allocation_cancel.index",
            [$allocation, $production]
        );

    }

    public function machine_allocation(Production $production)
    {

        if (!in_array($production->product->goods_kind->caption_en, ["Fabric_Raw", "Warps", "Fabric"])) {
            return redirect()->back()->withErrors("فرایند کارت تولید مربوط به رسته کالا یافت نشد.  ");
        }

        return redirect()->route(
            Str::lower($production->product->goods_kind->caption_en) . ".machine_allocation.index",
            $production
        );

    }

    public function reallocation(Production $production, MachineAllocation $machine_allocation)
    {

        if (!in_array($production->product->goods_kind->caption_en, ["Fabric"])) {
            return redirect()->back()->withErrors("فرایند کارت تولید مربوط به رسته کالا یافت نشد.  ");
        }

        return redirect()->route(
            Str::lower($production->product->goods_kind->caption_en) . ".machine_allocation.reallocation",
            [$production, $machine_allocation]
        );

    }

    public function print_card(Production $production)
    {

        $html = view("production.print.production_card", compact("production"))->render();
        return Pdf::createAsHtml($html, "P", $production->serial());

    }

    //
    public function index_details(Request $request)
    {
// مدیریت دسترسی به صفحه بازگشت: آخرین داشبورد همیشه ذخیره می گردد.
        $last_production_dashboard_view = session("last_production_dashboard_view");
        if (!$last_production_dashboard_view || $request->view_form_menu) {
            session([
                "last_production_dashboard_view" => "index_details"
            ]);
        }

        if ($last_production_dashboard_view == "index") {
            return redirect()->route('production.dashboard.list');
        }
        /************************************************************/

        // فایل گزارش

        // فایل هایی که قبلا دانلود کرده است را حذف می کنیم.
        $list_large_operation_for_delete = QueueOfLargeOperation::where([
            "status_id" => 3500002,
            "large_operation_type_id" => 800,
            "result" => Auth::id()
        ])->
        where("created_at", "<", Carbon::now()->addDay(-1))->
        get();
        foreach ($list_large_operation_for_delete as $item) {

            $data = json_decode($item->data);
            $filePath = 'production_details_files/' . ($data->file_name ?? "0"); // Replace this with the actual path to your file

            // Check if the file exists
            if (Storage::exists($filePath)) {
                Storage::delete($filePath); // Delete the file
            }

            $item->delete();
        }


        $large_operation = QueueOfLargeOperation::where([
            "status_id" => 3500002,
            "large_operation_type_id" => 800,
            "result" => Auth::id()
        ])->first();


        /************************************************************/

        $lines_id_permission = \Auth::user()->posts->first()->post->get_lines_id_permission();

        $allowed_status_ids = PostStatus::getAllowedStatus();
        $allowed_status_ids_all = $allowed_status_ids;
        if ($request->waiting_status_id != 0 && !in_array($request->waiting_status_id, $allowed_status_ids)) {
            return back()->withErrors("شما اجازه دسترسی به مشاهده کارت های تولید با وضعیت انتخاب شده را ندارید");
        }
        if (count($lines_id_permission) == 0) {
            return back()->withErrors("هیچ گونه دسترسی خط برای شما در کارتابل تولید تعریف نشده است.");
        }

        if ($request->isMethod('post')) {
            $parent_production_equal_to = $request->parent_production_equal_to;
            $production_equal_to = $request->production_equal_to;
            $order_equal_to = $request->order_equal_to;
            $order_customer_equal_to = $request->order_customer_equal_to;
            $order_by = $request->order_by;
            $waiting_status_id = $request->waiting_status_id;
            $goods_kind_property_id = $request->goods_kind_property_id;
            $goods_kind_property_id2 = $request->goods_kind_property_id2;
            $goods_kind_id = $request->goods_kind_id;
            $search_production_channel_type = $request->search_production_channel_type;
            $product_equal_to = $request->product_equal_to;
            $order_status_equal_to = $request->order_status_equal_to;
            $search_consumed_product = $request->search_consumed_product;
            $parent_product_equal_to = $request->parent_product_equal_to;
            $parent_production_channel_type = $request->parent_production_channel_type;
            $search_goods_kind_property = $request->search_goods_kind_property;
            $search_goods_kind_property2 = $request->search_goods_kind_property2;
            $parent_waiting_status_id = $request->parent_waiting_status_id;


        } else {
            $parent_production_equal_to = session("parent_production_equal_to");
            $product_equal_to = session("search_product_equal_to");
            $production_equal_to = session("search_production_equal_to");
            $order_equal_to = session("search_order_equal_to");
            $order_customer_equal_to = session("order_customer_equal_to");
            $order_by = session("order_by_production_card") ?? "production_cards.updated_at__desc";
            $waiting_status_id = session("waiting_status_id_production_card");
            $goods_kind_property_id = session("goods_kind_property_id_production_card");
            $goods_kind_property_id2 = session("goods_kind_property_id2_production_card");
            $goods_kind_id = session("goods_kind_id_production_card");
            $search_production_channel_type = session("search_production_channel_type_production_card");
            $order_status_equal_to = session("search_order_status_equal_to");
            $search_consumed_product = session("search_search_consumed_product");
            $parent_product_equal_to = session("search_parent_product_equal_to");
            $parent_production_channel_type = session("search_parent_production_channel_type");
            $search_goods_kind_property2 = session("search_goods_kind_property2");
            $search_goods_kind_property = session("search_goods_kind_property");
            $parent_waiting_status_id = session("parent_waiting_status_id");


        }
        session([
            "parent_production_equal_to" => $parent_production_equal_to,
            "search_production_equal_to" => $production_equal_to,
            "product_equal_to" => $product_equal_to,
            "search_order_equal_to" => $order_equal_to,
            "order_customer_equal_to" => $order_customer_equal_to,
            "order_by_production_card" => $order_by,
            "waiting_status_id_production_card" => $waiting_status_id,
            "goods_kind_property_id_production_card" => $goods_kind_property_id,
            "goods_kind_property_id2_production_card" => $goods_kind_property_id2,
            "goods_kind_id_production_card" => $goods_kind_id,
            "search_production_channel_type_production_card" => $search_production_channel_type,
            "search_order_status_equal_to" => $order_status_equal_to,
            "search_consumed_product" => $search_consumed_product,
            "parent_product_equal_to" => $parent_product_equal_to,
            "search_parent_production_channel_type" => $parent_production_channel_type,
            "search_goods_kind_property" => $search_goods_kind_property,
            "search_goods_kind_property2" => $search_goods_kind_property2,
            "parent_waiting_status_id" => $parent_waiting_status_id,

        ]);

        $search = "";

        // search
        if ($waiting_status_id != 0) {
            $allowed_status_ids = [];
            $allowed_status_ids[] = $waiting_status_id;
        }

// ذخیره اطلاعات فیلتر ثابت

        $data_fixed = null;
        if ($request->delete_filter && $request->delete_filter == 2) {
            // حذف همه فیلتر های ثابت
            $data_fixed = [
                "order_status_fixed" => []
            ];
        } else {
            if (isset($request->order_status_fixed)) {
                $order_status_fixed = array_keys($request->order_status_fixed);
                $data_fixed = [
                    "order_status_fixed" => $order_status_fixed
                ];
            }
        }
        if ($data_fixed != null) {
            JsonDataList::SetFilter(Auth::id(), 800, $data_fixed);
        }

        $data_fixed = JsonDataList::GetData(Auth::id(), 800);
        // لیست وضعیت های سفارش
        $order_status_option = Option::get("status", $order_status_equal_to, 350);

        // جستجوی بر اساس مشخصه کالا
        $product_ids = [];

        if ($goods_kind_property_id) {
            $goods_kind_property = GoodsKindProperty::find($goods_kind_property_id);

            $product_ids = GoodsKindPropertyValue::where([
                "goods_kind_property_id" => $goods_kind_property_id

            ])->
            when($goods_kind_property->field_type_id == 3, function ($query) use ($search_goods_kind_property) {
                return $query->where("value", $search_goods_kind_property);
            })->
            when($goods_kind_property->field_type_id != 3, function ($query) use ($search_goods_kind_property) {
                return $query->where("value", "like", "%" . $search_goods_kind_property . "%");
            })->
            pluck("product_id", "product_id")->toArray();
            $product_ids[] = -1;

        }

        if ($goods_kind_property_id2) {
            $goods_kind_property = GoodsKindProperty::find($goods_kind_property_id2);

            $product_ids2 = GoodsKindPropertyValue::where([
                "goods_kind_property_id" => $goods_kind_property_id2

            ])->
            when($goods_kind_property->field_type_id == 3, function ($query) use ($search_goods_kind_property2) {
                return $query->where("value", $search_goods_kind_property2);
            })->
            when($goods_kind_property->field_type_id != 3, function ($query) use ($search_goods_kind_property2) {
                return $query->where("value", "like", "%" . $search_goods_kind_property2 . "%");
            })->
            pluck("product_id", "product_id")->toArray();

            if (count($product_ids) > 0) {
                $product_ids = array_intersect($product_ids, $product_ids2);
            }
            $product_ids[] = -1;

        }

        // بررسی جستجو با کانال تولید
        if ($search_production_channel_type) {
            $production_channel_type_product_ids = LineProductStation::
            join("production_channel_types", "production_channel_types.id", "production_channel_type_id")->
            where("production_channel_types.caption", "like", "%" . $search_production_channel_type . "%")->
            pluck("product_id", "product_id")->toArray();

            // اگر برای کالای چیزی سرچ کردیم که از سرچ ها کم می کنیم وگر نه مقدار آن را برابر با مقدار کالاهای کانال تولید قرار می دهیم.
            if (count($product_ids) > 0) {
                foreach ($product_ids as $key => $product_id) {
                    if (!in_array($product_id, $production_channel_type_product_ids)) {
                        unset($product_ids[$key]);
                    }
                }
            } else {
                $product_ids = $production_channel_type_product_ids;
                $product_ids [] = -1;
            }

        }
        // جستجوی نام کالای سطح بالا
        $parent_product_ids = [];
        if ($parent_product_equal_to) {

            $parent_product_ids = Product::where("code", "like", "%" . $parent_product_equal_to . "%")->
            orWhere("caption", "like", "%" . $parent_product_equal_to . "%")->
            pluck("id")->toArray();
            $parent_product_ids[] = -1;
        }
        // بررسی جستجو با کانال تولید کارت تولید سطح بالا
        if ($parent_production_channel_type) {
            $parent_production_channel_type_product_ids = LineProductStation::
            join("production_channel_types", "production_channel_types.id", "production_channel_type_id")->
            where("production_channel_types.caption", "like", "%" . $parent_production_channel_type . "%")->
            pluck("product_id", "product_id")->toArray();

            if (count($parent_product_ids) > 0) {
                $parent_product_ids = array_intersect($parent_production_channel_type_product_ids, $parent_product_ids);
            } else {
                $parent_product_ids = $parent_production_channel_type_product_ids;
            }

            $parent_product_ids[] = -1;


        }

        // جستجوی کارت سطح بالا
        $parent_production_ids = [];
        if ($parent_production_equal_to) {
            $parent_production_ids = Production::
            where("serial", "like", "%" . $parent_production_equal_to . "%")->
            pluck("id", "id")->toArray();

            $parent_production_ids[] = -1;


        }

        // جستجو بر اساس مواد مصرفی
        if ($search_consumed_product) {
            $consume_product_ids = Product::join("consumed_products", "material_id", "products.id")->
            where("products.caption", "like", "%" . $search_consumed_product . "%")->
            orWhere("products.code", "like", "%" . $search_consumed_product . "%")->
            where("product_id", 335)->
            pluck("product_id")->toArray();
            if (count($product_ids) > 0) {
                $product_ids = array_intersect($product_ids, $consume_product_ids);
            } else {
                $product_ids = $consume_product_ids;
            }

        }

        // بررسی جستجو با کد سفارش
        $order_ids = [];
        if ($order_equal_to || $order_customer_equal_to || $order_status_equal_to || (isset($data_fixed["order_status_fixed"]) && count($data_fixed["order_status_fixed"]) > 0)) {
            $order_ids = Order::
            join("customers", "customers.id", "=", "orders.customer_id")->
            when($order_equal_to, function ($query, $order_equal_to) {
                return $query->where(function ($query) use ($order_equal_to) {
                    if (strpos($order_equal_to, '/') !== false) {// ایا متن شامل ممیز است اگر بود به این صورت اگر نبود سرچ عادی که از قبل داشتیم
                        list($series, $code) = explode('/', $order_equal_to, 2);// مقدار را به دو بخش تبدیل می کند.
                        return $query->where("orders.series", "like", "%" . $series . "%")
                            ->where("orders.code", "like", "%" . $code . "%");
                    } else {
                        return $query->
                        where(function ($query) use ($order_equal_to) {
                            return $query->
                            where("orders.code", "like", "%" . $order_equal_to . "%")
                                ->orWhere("orders.series", "like", "%" . $order_equal_to . "%");
                        });
                    }
                });
            })->
            when($order_customer_equal_to, function ($query, $order_equal_to) {
                return $query->
                where("customers.caption", "like", "%" . $order_equal_to . "%")
                    ->orWhere("customers.code", "like", "%" . $order_equal_to . "%");
            })->
            when($order_status_equal_to, function ($query) use ($order_status_equal_to) {
                return $query->where("orders.status_id", $order_status_equal_to);
            })->
            when((isset($data_fixed["order_status_fixed"]) && count($data_fixed["order_status_fixed"]) > 0), function ($query) use ($data_fixed) {
                return $query->whereIn("orders.status_id", $data_fixed["order_status_fixed"]);
            })->
            pluck("orders.id", "orders.id");
            $order_ids[] = -1;
        }


        if ($request->export_excel) {

            $list_large_operation_for_delete = QueueOfLargeOperation::where([
                "status_id" => 3500002,
                "large_operation_type_id" => 800,
                "result" => Auth::id()
            ])->
            delete();
            // درخواست خروجی اکسل
            $query = self::search($production_equal_to, $product_equal_to, $order_by, $lines_id_permission, [
                "production_cards.id",
            ], $allowed_status_ids,
                1,
                $product_ids,
                $parent_product_ids,
                $parent_production_ids,
                $parent_waiting_status_id,
                $order_ids,
                $goods_kind_id ?? false,
                false,
                true,
                Auth::id()

            );

            ProductionDetailsReport::where("user_id", Auth::id())->delete();
            DB::table('production_details_reports')->insertUsing(
                ['user_id', 'production_id'],
                $query
            );
            $count_row = ProductionDetailsReport::where("user_id", Auth::id())->count();
            if ($count_row <= ProductionDetailsReport::$max) {
                ProductionDetailsReport::CompleteData(Auth::id());
                $export = new ProductionDetailsReportExport();
                $export->user_id = Auth::id();


                return Excel::download($export, "production_details_" . jdate(Carbon::now()->timestamp)->format('Y_m_d') . '.xlsx');
            } else {


                $report_request["user_id"] = Auth::id();
                QueueOfLargeOperation::AddToQueue($report_request, 800);
                new Script1021Controller();
                return back()->with(["warning" => "با توجه به اینکه تعداد رکورد های گزارش زیاد می باشد، لطفا پس از 5 دقیقه برای دانلود گزارش به همین صفحه مراجعه بفرمایید."]);
            }

        }

        $list = self::search($production_equal_to, $product_equal_to, $order_by, $lines_id_permission, [
            "production_cards.parent_production_id",
            "production_cards.number_in_carton",
            "production_cards.product_id",
            "production_cards.serial",
            "production_cards.number",
            "production_cards.status_id",
            "production_cards.order_list_id",
            "production_cards.nth_in_day",
            "production_cards.created_at",
            "production_cards.prioriry_id",
            "production_cards.waiting_status_id",
            "production_cards.production_type_id", "production_cards.order_id", "production_cards.customer_id"
        ], $allowed_status_ids,
            1,
            $product_ids,
            $parent_product_ids,
            $parent_production_ids,
            $parent_waiting_status_id,
            $order_ids,
            $goods_kind_id ?? false,


        );

        $list = $list->paginate(30);

        // لیست کالاهایی که در صفحه وجود دارند
        $property_product_ids = [];
        $order_ids_in_list = [];
        foreach ($list as $item) {
            $property_product_ids[] = $item->product_id;
            $orders_ids_in_list[] = $item->order_id;
        }
        $property_product_ids[] = -1;
        // مشخص کردن رنگ مشخصه در رسته کالایی

        // آیا مقدار مشخصه اصلی در داشبورد نمایش داده شود؟
        $setting_value = Setting::getIntegerValueList([
            "property1_show_in_production_dashboard",
            "property2_show_in_production_dashboard",
            "production_channel_type_show_in_production_dashboard",
            "allow_show_customer_caption_in_production_dashboard"
        ]);

        $property1_show_in_production_dashboard = $setting_value["property1_show_in_production_dashboard"];
        $property2_show_in_production_dashboard = $setting_value["property2_show_in_production_dashboard"];
        $production_channel_type_show_in_production_dashboard = $setting_value["production_channel_type_show_in_production_dashboard"];
        $allow_show_customer_caption_in_production_dashboard = $setting_value["allow_show_customer_caption_in_production_dashboard"];
        $goods_kind_property_values_products = [];

        if ($property1_show_in_production_dashboard || $property2_show_in_production_dashboard) {


            $property_list = [];

            // کدام مشخصه باید نمایش داده شود.
            if ($property1_show_in_production_dashboard) {
                $property_list[] = "property1_id";
            }
            if ($property2_show_in_production_dashboard) {
                $property_list[] = "property2_id";
            }

            $property_ids_goods_kind = GoodsKind::where("active_status_id", 1200)->select($property_list)->get();
            $property_ids = [];
            foreach ($property_ids_goods_kind as $item) {
                if ($item->property1_id)
                    $property_ids[] = $item->property1_id;
                if ($item->property2_id)
                    $property_ids[] = $item->property2_id;
            }

            $goods_kind_property_values = GoodsKindPropertyValue::
            leftJoin('goods_kind_property_options', function ($join) {
                $join->on("goods_kind_property_values.goods_kind_property_id", "=", "goods_kind_property_options.goods_kind_property_id")
                    ->on("goods_kind_property_values.value", "=", "goods_kind_property_options.id");
            })->
            whereIn("goods_kind_property_values.product_id", $property_product_ids)->
            whereIn("goods_kind_property_values.goods_kind_property_id", $property_ids)->select("goods_kind_property_values.product_id", "goods_kind_property_values.goods_kind_property_id", "value", "color", "caption")->get();

            $goods_kind_property_values_products = [];
            foreach ($goods_kind_property_values as $goods_kind_property_value) {
                $goods_kind_property_values_products[$goods_kind_property_value->product_id][$goods_kind_property_value->goods_kind_property_id] = $goods_kind_property_value;
            }
//        return $goods_kind_property_values_products;

        }

        // نمایش لیست کانال تولید ها در داشتبورد مدیریت تولید
        $production_channel_type_list = [];
        if ($production_channel_type_show_in_production_dashboard) {
            $production_channel_type_list_query = LineProductStation::
            whereIn("product_id", $property_product_ids)->
            groupBy("product_id")->with("production_channel_type")->
            select("product_id", "production_channel_type_id")->
            get();
            foreach ($production_channel_type_list_query as $production_channel_type) {
                $production_channel_type_list[$production_channel_type->product_id] = [
                    "caption" => $production_channel_type->production_channel_type->caption ?? "",
                    "color" => $production_channel_type->production_channel_type->color ?? ""
                ];
            }
        }

        $machine_re_allocation = MachineAllocation::where("status_id", 5310050)->
        whereNotNull("parent_allocation_id")->groupBy("production_id")->pluck("id", "production_id")->toArray();
        $order_by_Option = Option::OrderBy("production", $order_by);


        $goods_kind_option = Option::get("goods_kind", $goods_kind_id);

        $property_option = Option::get("get_property_by_goods_kind", $goods_kind_property_id, $goods_kind_id);

        $property_option2 = Option::get("get_property_by_goods_kind", $goods_kind_property_id2, $goods_kind_id);

        // نمایش لیست کالاهای مصرفی

        $consume_products_query = ConsumedProduct::whereIn("product_id", $property_product_ids)->
        with("material")->get();

        $consume_products = [];
        foreach ($consume_products_query as $consume_product) {
            if (!isset($consume_products[$consume_product->product_id])) {
                $consume_products[$consume_product->product_id] = [];
            }
            $consume_products[$consume_product->product_id][] = $consume_product->material->caption;
        }


        //نمایش وضعیت کارت های تولید سطح 1 و سطح 2
        $parent_production_product_ids = [];
        $production_ids = [];
        $goods_kind_waiting_master_status = [];
        $goods_kind_parent_waiting_master_status = [];
        foreach ($list as $item) {
            if ($item->parent_production_id) {
                $parent_production_product_ids[$item->parent_production_id] = $item->parent_production->product_id;
            }
            $production_ids[] = $item->id;
            if ($item->parent_production_id) {
                $production_ids[] = $item->parent_production_id;
            }
            if (!isset($goods_kind_waiting_master_status[$item->product->goods_kind_id])) {
                $goods_kind_waiting_master_status[$item->product->goods_kind_id] = array_keys($item->product->goods_kind->getModuleList());
                $goods_kind_waiting_master_status[$item->product->goods_kind_id] = array_intersect($goods_kind_waiting_master_status[$item->product->goods_kind_id], [7001, 7201, 7301]);
            }
            if ($item->parent_production && !isset($goods_kind_parent_waiting_master_status[$item->parent_production->product->goods_kind_id])) {

                if (substr($item->parent_production->waiting_status_id . "", 0, -3) == 7008) { // کارت سطح بالا پیمانکاری است.
                    $goods_kind_parent_waiting_master_status[$item->parent_production->product->goods_kind_id] = [7008];
                    $goods_kind_parent_waiting_master_status[$item->parent_production->product->goods_kind_id] = array_intersect($goods_kind_parent_waiting_master_status[$item->parent_production->product->goods_kind_id], [7001, 7201, 7301, 7008]);

                } else {
                    $goods_kind_parent_waiting_master_status[$item->parent_production->product->goods_kind_id] = array_keys($item->parent_production->product->goods_kind->getModuleList());
                    $goods_kind_parent_waiting_master_status[$item->parent_production->product->goods_kind_id] = array_intersect($goods_kind_parent_waiting_master_status[$item->parent_production->product->goods_kind_id], [7001, 7201, 7301, 7008]);

                }
            }
        }

        $main_status_ids[] = -1;
        foreach ($goods_kind_waiting_master_status as $items) {
            $main_status_ids = array_merge($main_status_ids, $items);
        }
        $main_parent_status_ids[] = -1;
        foreach ($goods_kind_parent_waiting_master_status as $items) {
            $main_parent_status_ids = array_merge($main_parent_status_ids, $items);
        }


        $waiting_status_option = Option::get("production_waiting_status", $waiting_status_id, $main_status_ids, $allowed_status_ids_all);

        $parent_waiting_status_option = Option::get("production_waiting_status", $parent_waiting_status_id, $main_parent_status_ids);


        $allocation_amount_list = ProductionDetailsReport::GetAllocationList($production_ids);

        $production_amount_list = ProductionDetailsReport::GetProductionAmountList($production_ids);

        $parent_production_product_ids[] = -1;
        $production_channel_type_parent_list =
            LineProductStation::
            join("production_channel_types", "production_channel_type_id", "=", "production_channel_types.id")->
            whereIn("product_id", $parent_production_product_ids)->groupBy("product_id")->select("production_channel_types.caption", "product_id")->
            pluck("caption", "product_id")->toArray();


        // محاسبه موجودی کالا
        $product_inventory = array_merge($parent_production_product_ids, $property_product_ids);
        $product_inventory = WarehouseProduct::getProductInventoryList($product_inventory);
        //تاریخ تایید پیش فاکتور توسط مشتری
        //304030
        $orders_ids_in_list[] = -1;
        $order_logs = OrderLog::whereIn("order_id", $orders_ids_in_list)->
        where("event_id", 304030)->groupBy("order_id")->with("order")->
        get()->keyby("order_id");

        // باقی مانده سفارش ها
       $product_request_form_list_group_by= Product\ProductRequest\ProductRequestForm::join("product_request_form_item",
            "product_request_form_item.product_request_form_id", "product_request_forms.id")->
        where("applicant_type_id", 30)->
        whereIn("order_id", $orders_ids_in_list)->
        whereIn("product_request_forms.status_id", [7005001, 7005004, 7005008,7005009 , 7005002])->
        selectRaw("order_id , product_id , sum(amount_request)  as amount_request, sum(amount_sent) as amount_sent, sum(amount_remaining) as amount_remaining")->
       groupby("order_id","product_id")->
       get();
        $product_request_form_list=[];
       foreach ($product_request_form_list_group_by as $item_request_form_list){
           if(!isset($product_request_form_list[$item_request_form_list->order_id])){
               $product_request_form_list[$item_request_form_list->order_id]=[];
           }
           if(!isset($product_request_form_list[$item_request_form_list->order_id][$item_request_form_list->product_id])){
               $product_request_form_list[$item_request_form_list->order_id][$item_request_form_list->product_id]["amount_sent"]=$item_request_form_list->amount_sent;
               $product_request_form_list[$item_request_form_list->order_id][$item_request_form_list->product_id]["amount_request"]=$item_request_form_list->amount_request;
           }
       }
//
//       if(Auth::id() == 1){
//           return $product_request_form_list;
//       }


        $datetime_color = [];
        foreach ($order_logs as $order_log) {
            $top = Carbon::now()->diffInDays($order_log->created_at);
            $botom = Carbon::parse($order_log->order->delivery_datetime)->diffInDays($order_log->created_at);
            $datetime_color[$order_log->order_id] = $botom == 0 ? null : round($top / $botom, 2);
        }


        $user = \Auth::user();
        $post_user = \Auth::user()->posts->first();

        $token_api = $user->createToken('web-token')->plainTextToken;

        return view("production.dashboard.index_details", compact("property_option", "machine_re_allocation",
            "goods_kind_property_values_products", "property1_show_in_production_dashboard", "production_channel_type_show_in_production_dashboard",
            "property2_show_in_production_dashboard", "production_channel_type_list", "production_channel_type_parent_list",
            "production_channel_type_show_in_production_dashboard", "search_production_channel_type", "search", "product_equal_to",
            "goods_kind_option", "waiting_status_option", "order_by_Option", "list", "parent_production_equal_to", "goods_kind_property_id", "goods_kind_property_id2",
            "allocation_amount_list", "production_amount_list", "consume_products", "order_status_equal_to", "parent_product_equal_to",
            "order_status_option", "data_fixed", "product_inventory", "token_api", "search_consumed_product", "goods_kind_id",
            "parent_production_channel_type", "waiting_status_id", "order_customer_equal_to", "data_fixed", "search_goods_kind_property",
            "production_equal_to", "order_equal_to", "order_by", "allow_show_customer_caption_in_production_dashboard",
            "parent_waiting_status_id", "parent_waiting_status_option", "property_option2", "search_goods_kind_property2",
            "order_logs", "datetime_color", "large_operation","product_request_form_list"
        ));
    }

    public function download_excel(QueueOfLargeOperation $queue_of_large_operation)
    {

        $export = new ProductionDetailsReportExport();
        $export->user_id = Auth::id();


        return Excel::download($export, "production_details_" . jdate(Carbon::now()->timestamp)->format('Y_m_d') . '.xlsx');

    }

    public static function search(
        $production_equal_to = "", $product_equal_to = "", $order_by = "production_cards.created_at__desc", $lines = [],
        $select = [
            "production_cards.parent_production_id",
            "production_cards.number_in_carton",
            "production_cards.product_id",
            "production_cards.serial",
            "production_cards.number",
            "production_cards.status_id",
            "production_cards.order_list_id",
            "production_cards.nth_in_day",
            "production_cards.created_at",
            "production_cards.prioriry_id",
            "production_cards.waiting_status_id",
            "production_cards.order_id",
        ],
        $productionStatusIds = [],
        $supply_type_id = 1,
        $product_ids = [],
        $parent_product_ids = [],
        $parent_production_ids = [],
        $parent_waiting_status_id = false,
        $order_ids = [],
        $goods_kind_id = false,
        $production_status_equal_to = false,
        $export_for_excel = false,
        $export_user_id = null
    )
    {

        return Production::join("products", "production_cards.product_id", "products.id")->
        when(count($lines) > 0, function ($query) {
            return $query->join("line_product_station", "products.id", "line_product_station.product_id");
        })->
        where("supply_type_id", $supply_type_id)->
        when($production_equal_to != "", function ($query) use ($production_equal_to) {
            return $query->where(function ($query) use ($production_equal_to) {
                return $query->where("production_cards.serial", "like", "%" . $production_equal_to . "%");
            });

        })->
        when($product_equal_to != "", function ($query) use ($product_equal_to) {
            return $query->where(function ($query) use ($product_equal_to) {
                return $query
                    ->orWhere("products.code", "like", "%" . $product_equal_to . "%")
                    ->orWhere("products.caption", "like", "%" . $product_equal_to . "%");
            });

        })->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");

            return $query->orderBy($order_by[0], $order_by[1]);

        })->
        when($select != [] && !$export_for_excel, function ($query) use ($select) {
            $select[] = "production_cards.id as id";

            return $query->select($select);
        })->
        when($production_status_equal_to, function ($query) use ($production_status_equal_to) {

            return $query->where("production_cards.status_id", $production_status_equal_to);
        })->
        when($goods_kind_id, function ($query) use ($goods_kind_id) {
            return $query->where("products.goods_kind_id", $goods_kind_id);
        })->
        when($productionStatusIds != [], function ($query) use ($productionStatusIds) {
            return $query->where(function ($query) use ($productionStatusIds) {
                $query->whereIn("production_cards.status_id", $productionStatusIds)->
                OrwhereIn("production_cards.waiting_status_id", $productionStatusIds);
            });
        })->
        when($product_ids != [], function ($query) use ($product_ids) {
            return $query->whereIn("production_cards.product_id", $product_ids);

        })->
        when($parent_product_ids != [] || $parent_waiting_status_id, function ($query) use ($parent_product_ids, $parent_waiting_status_id) {
            return $query->
            join('production_cards as parent_production_cards', 'production_cards.parent_production_id', '=', 'parent_production_cards.id')->
            when($parent_product_ids != [], function ($query) use ($parent_product_ids) {
                return $query->whereIn("parent_production_cards.product_id", $parent_product_ids);
            })->
            when($parent_waiting_status_id, function ($query) use ($parent_waiting_status_id) {
                return $query->where("parent_production_cards.waiting_status_id", $parent_waiting_status_id);
            });

        })->
        when($parent_production_ids != [], function ($query) use ($parent_production_ids) {
            return $query->
            whereIn("production_cards.parent_production_id", $parent_production_ids);

        })->
        when($order_ids != [], function ($query) use ($order_ids) {
            return $query->whereIn("production_cards.order_id", $order_ids);

        })->
        // آیا گوئری برای اکسل است یا خیر
        when($export_for_excel, function ($query) use ($export_user_id) {
            return $query->selectRaw("? as user_id, production_cards.id as production_id", [$export_user_id]);
        })->
        when(!$export_for_excel, function ($query) {
            return $query->addSelect(DB::raw("production_cards.id as production_card_id"));
        })->

        groupBy("production_cards.id")->
        when(!$export_for_excel, function ($query) {
            return $query->with("order_list", "order_list.order.status", "parent_production.order_list");
        });

        // ->
        // with(["product"=>function( $product) use($product_info) {
        //   $product->orWhere("cod","like","%".$product_info."%")->orWhere("caption","like","%".$product_info."%");
        // }])
        ;
    }


    public function change_allocation(Request $request, $allocation, Machine $machine)
    {


        $key = "change_type_" . $machine->id;
        $change_type_id = $request->$key;
        $allocation_priority = [];


        $key = "other_machine_" . $machine->id;
        $other_machine_id = $request->$key;

        $key = "one_allocation_" . $machine->id;
        $one_allocation = $request->$key ?? null;
        $new_machine = Machine::find($other_machine_id);

        $gcac = new GeneralChangeAllocationController();
        if (!$request->allocation_priority) {

            if ($one_allocation) {
                $allocation = Allocation::find($one_allocation);
                if ($allocation) {
                    if ($change_type_id == 301) {
                        if (!$new_machine) {
                            return back()->withErrors("لطفا نام ماشین جهت جابجایی تخصیص را انتخاب نمایید.");
                        }
                        $result = $gcac->change_allocation_other_machine([$allocation], $machine, $new_machine);
                        if (!$result["result"]) {
                            return back()->withErrors($result["error"]);
                        } else {
                            return back()->with(["success" => "تغییرات با موفقیت انجام شد."]);
                        }
                    }
                    $result = $gcac->change_allocation_in_machine($allocation, $change_type_id);
                    if (!$result["result"]) {
                        return back()->withErrors($result["error"]);
                    } else {
                        return back()->with(["success" => "تغییرات با موفقیت انجام شد."]);
                    }
                }
            }

            return back()->withErrors("لطفا ابتدا یک تخصیص را انتخاب کرده و سپس نسبت به جابجایی تخصیص اقدام نمایید.");
        }

        $machine_allocation =
            Allocation::where("machine_id", $machine->id)->
            whereIn("status_id", [5310010, 5310040])->get()->keyBy("priority_number");

        if ($other_machine_id) {


            if (!$new_machine) {
                return back()->withErrors("لطفا نام ماشین جهت جابجایی تخصیص را انتخاب نمایید.");
            }

            $allocation_list = [];
            foreach ($request->allocation_priority as $key => $item) {
                $allocation_priority[$key] = 1;
                $allocation_list[] = $machine_allocation[$key];

            }

            $result = $gcac->change_allocation_other_machine($allocation_list, $machine, $new_machine);
            if (!$result["result"]) {
                return back()->withErrors($result["error"]);
            } else {
                return back()->with(["success" => "تغییرات با موفقیت انجام شد."]);
            }
        }


        $number_of_steps = count($request->allocation_priority);


        foreach ($request->allocation_priority as $key => $item) {
            $allocation_priority[$key] = 1;

            $allocation = $machine_allocation[$key];
            for ($k = 0; $k < $number_of_steps; $k++) {

                $allocation = Allocation::find($allocation->id);
                $result = $gcac->change_allocation_in_machine($allocation, $change_type_id);
                if (!$result["result"]) {
                    return back()->withErrors($result["error"]);
                } else {

                }

            }

            // return back()->with(["success" => "تغییرات با موفقیت انجام شد."]);

        }

        return back()->with(["success" => "تغییرات با موفقیت انجام شد."]);


    }

}

//        $post_ids = \Auth::user()->posts()->pluck("post_id");
//
//
//        $lines_id_permission = Post::GetAllLinePermission($post_ids);
//        if (count($lines_id_permission) == 0) {
//            return back()->withErrors("هیچ گونه دسترسی برای شما در کارتابل تولید تعریف نشده است.");
//        }
//        $lines = LineProductStation::where([ "product_id" => $production->product_id])->
//        whereIn("line_id", $lines_id_permission)->pluck("line_id")->toArray();
//
//        if (count($lines) <= 0) {
//            return back()->withErrors("شما اجازه مشاهده این کارت تولید را ندارید");
//        }
//
//        return PostStatus::whereIn("post_id", $post_ids)->where("status_id", $production->status_id)->exists();

//  }
//    public function request_material(Production $production)
//    {
//        $post_user = \Auth::user()->posts->first();
//        if (!($post_user->checkButtonPermission("production.500010") && $production->waiting_status_id == 500010)) {
//            return back()->withErrors("شما مجاز به مشاهده صفحه نمی باشید");
//        }
//
//        $rfw = RequestFromWarehouse::where("production_card_id", $production->id)->whereNull("master_production_id")->first();
//        if (!$rfw) {
//            return back()->withErrors("درخواست کالا از انبار قبلا برای این کارت ثبت شده است. یا این کارت، کارت فرعی می باشد");
//        }
//        $list = Production::where(["status_id" => 500, "waiting_status_id" => 500010, "product_id" => $production->product_id])->
//        where("id", "!=", $production->id)->get();
//
//
//        return view("production.dashboard.request_material", compact("production", "list"));
//
//    }
//
//    public function request_material_submit(Request $request, Production $production)
//    {
//        // check
//        $rfw = RequestFromWarehouse::where("production_card_id", $production->id)->whereNull("master_production_id")->first();
//        if (!$rfw) {
//            return back()->withErrors("درخواست کالا از انبار قبلا برای این کارت ثبت شده است.");
//        }
//
//
//        $list = Production::
//        where(["status_id" => 500, "waiting_status_id" => 500010, "product_id" => $production->product_id])->
//        get();
//        $production_ids = [];
//        foreach ($list as $item) {
//            $pc = "pc_" . $item->id;
//            if (isset($request->$pc) && $request->$pc == 1) {
//                $production_ids[] = $item->id;
//                Production::where("id", $item->id)->update(["waiting_status_id" => 500020]);
//            }
//        }
//        RequestFromWarehouse::whereIn("production_card_id", $production_ids)->
//        update(["master_production_id" => $production->id]);
//
//        $rfw_list = RequestFromWarehouse::whereIn("production_card_id", $production_ids)->get();
//        foreach ($rfw_list as $rfw) {
//            $rfw->log("", 380010);
//        }
//
//        $production->log("", 500, 500010);
//        $production->is_master_of_rfw = 1;
//        $production->save();
//
//        return redirect()->route("production.dashboard.request_material_result", $production);
//    }
//
//    public function request_material_result(Production $production)
//    {
//
//        return view("production.dashboard.request_material_result", compact("production"));
//    }
//
//    public function confirm_material_form(Production $production)
//    {
//        $this->checkPermission($production);
//
//        $form =
//            Form::where(
//                [
//                    "order_id" => $production->order_id,
//                    "order_list_id" => $production->order_list_id,
//                    "production_card_id" => $production->id,
//                    "status_id" => 500000110,
//                    "trans_kind" => 8
//                ]
//            )->first();
//        if (!$form) {
//            return back()->withErrors("هیچ فرمی برای تایید وجود ندارد.");
//        }
//        return view("production.dashboard.material_form", compact("production", "form"));
//
//    }
//
//    public function confirm_material_form_submit(Request $request, Production $production, Form $form)
//    {
//        $this->checkPermission($production);
//
//        $error = "";
//        $count_item_ok = 0;
//        foreach ($form->item as $item) {
//            if (isset($request->data[$item->product_id]) && $item->amount == $request->data[$item->product_id]) {
//                $count_item_ok++;
//            } else {
//                $error .= "مقدار ثبت شده برای " . $item->product->code . " - " . $item->product->caption . " با مقدار تحویل شده انبار مغایرت دارد." . "<br/>";
//            }
//        }
//
//        if ($error != "") {
//            return back()->withErrors($error);
//        }
//
//        $form->status_id = 500000120;
//        $form->save();
//
//        $form = Form::find($form->id);
//
//        foreach ($form->item as $item) {
//
//
//            $WP = WarehouseProduct::create([
//                "warehouse_id" => $item->product->warehouse_id,
//                "product_id" => $item->product->id,
//                "form_id" => $form->id,
//                "output" => $item->amount,
//                "ic" => $production->product->ic,
//                "opp_kind" => 1,
//                "trans_kind" => 8
//            ]);
//
//            $WP->update_remaining();
//
//            $RFW_master = RequestFromWarehouse::find($item->rfw_id);
//
//            $RFW_list = RequestFromWarehouse::where("master_production_id", $production->id)->
//            where("material_id", $RFW_master->material_id)->orderBy("amount")->get();
//
//            $amount = $item->amount;
//            foreach ($RFW_list as $RFW_item) {
//
//                $amount_item = $amount > $RFW_item->amount_remaining ? $RFW_item->amount_remaining : $amount;
//
//                if ($amount != 0 && $amount_item == 0) {
//                    $amount_item = $amount;
//                }
//
//                $amount -= $amount_item;
//
//
//                if ($amount_item > 0) {
//
//                    $RFW_item->amount_sent += $amount_item;
//                    $RFW_item->amount_remaining -= $amount_item;
//
//                    $RFW_item->amount_remaining = $RFW_item->amount_remaining < 0 ? 0 : $RFW_item->amount_remaining;
//                    $RFW_item->status_id = $RFW_item->amount_remaining == 0 ? 386 : 384;
//
//                    $RFW_item->save();
//                }
//
//            }
//
//            // اگر کل تحویل از مقدار مورد نیاز همه بیشتر باشد، به مقدار ارسال شده اولی اضافه می کند.
//            if ($amount > 0) {
//
//                $RFW_item_extra = RequestFromWarehouse::where("master_production_id", $production->id)->
//                where("material_id", $RFW_master->material_id)->first();
//
//                $RFW_item_extra->amount_sent += $amount;
//                $RFW_item_extra->save();
//            }
//
//        }
//
//        $production->changeProductionStatusGroupInForm(500030, 500040);
//
//        return redirect()->route("production.dashboard.list")->with(["success" => "فرم مواد اولیه با موفقیت تایید شد."]);
//
//    }
//
//    public function reject_material_form(Production $production)
//    {
//
//        $this->checkPermission($production);
//
//        $form =
//            Form::where(
//                [
//                    "order_id" => $production->order_id,
//                    "order_list_id" => $production->order_list_id,
//                    "production_card_id" => $production->id,
//                    "status_id" => 500000110,
//                    "trans_kind" => 8
//                ]
//            )->first();
//        if (!$form) {
//            return back()->withErrors("هیچ فرمی برای تایید/عدم تایید وجود ندارد.");
//        }
//
//        $form->status_id = 500000150;
//        $form->save();
//
//        $production->changeProductionStatusGroupInForm(500025, 500025);
//
//        return redirect()->route("production.dashboard.list")->with(["success" => "عملیات با موفقیت انجام شد"]);
//    }
//

//
//    public function confirm_quality_control(Production $production)
//    {
//        if ($production->status->id != 500) {
//            return back()->withErrors("امکان تایید کنترل کیفیت برای کارت وجود ندارد");
//        }
//        $production->log("", 500, 500050);
//        $production->waiting_status_id = 500060;
//        $production->save();
//
//        return redirect()->route("production.dashboard.list")->with(["success" => "عملیات با موفقیت انجام شد"]);
//
//    }
//
//    public function confirm_delivery_to_warehouse(Production $production)
//    {
//        if ($production->waiting_status_id != 500060) {
//            return "شما اجازه مشاهده فرم را ندارید.";
//            // return back()->withErrors("امکان تحویل به انبار محصول برای کارت3 وجود ندارد");
//        }
//        $production->log("", 500, 500060);
//        $production->waiting_status_id = 500070;
//        $production->save();
//
//        return view("production.dashboard.confirm_delivery_to_warehouse", compact("production"));
//
//    }
//
//    public function edit_number_product(Production $production)
//    {
//        if ($production->waiting_status_id != 500080) {
//            return back()->withErrors("امکان  تغییر مقداری برای کارت وجود ندارد");
//        }
//
//        return view("production.dashboard.production_card.edit_product_number", compact("production"));
//    }
//
//    public function edit_number_product_submit(Request $request, Production $production)
//    {
//        if ($production->waiting_status_id != 500080) {
//            return back()->withErrors("امکان  تغییر مقداری برای کارت وجود ندارد");
//        }
//        if ($request->number_product <= 0) {
//            return back()->withErrors("مقدار وارد شده نا معتبر است.");
//        }
//        $production->log($request->number_product, 500, 500080);
//        $production->waiting_status_id = 500070;
//        $production->number_product = $request->number_product;
//        $production->save();
//        $production->evaluation_indicator();
//
//        // Save Extra
//        $extraP = ExtraProduction::where("product_id", $production->product_id)->
//        firstOrCreate(["product_id" => $production->product_id]);
//        $extraP->amount += ($request->number_product - $production->number_product);
//        $extraP->save();
//
//        return redirect()->route("production.dashboard.view_card", compact("production"))->with(["success" => "مقدار جدید با موفقیت ثبت شد."]);
//
//    }
//
//
//    ############################################## تایید محصول نهایی
//
//    public function confirm_warehouse(Production $production)
//    {
//        if ($production->status->id != 500 || $production->waiting_status_id != 500070) {
//            return back()->withErrors("امکان تایید تحویل به انبار محصول برای کارت وجود ندارد");
//        }
//        $factory_option = Option::get("factory", 1);
//        $warehouse_option = Option::get("warehouse");
//        $opp_kind_option = Option::get("opp_kind", 1);
//        $trans_kind_option = Option::get("trans_kind", -1, 1);
//        return view("production.dashboard.confirm_warehouse", compact("production", "factory_option", "warehouse_option", "opp_kind_option", "trans_kind_option"));
//    }
//
//    public function confirm_warehouse_submit(Request $request, Production $production)
//    {
//        $post_user = \Auth::user()->posts->first();
//        if ($production->waiting_status_id != 500070 || !$post_user->checkButtonPermission("production.500070")) {
//            return back()->withErrors("امکان تایید انبار برای کارت وجود ندارد");
//        }
//
//        if ($production->number_product != $request->warehouse_number) {
//            return back()->withErrors("تعداد وارد شده با تعداد ثبت شده توسط تولید مغایرت دارد");
//        }
//
//        return redirect()->route("production.dashboard.confirm_warehouse_submit_show", $production);
//
//    }
//
//    public function confirm_warehouse_submit_show(Production $production)
//    {
//        $post_user = \Auth::user()->posts->first();
//        if (!$post_user->checkButtonPermission("production.500070")) {
//            return back()->withErrors("امکان تایید انبار برای کارت وجود ندارد");
//        }
//        return view("production.dashboard.confirm_entry_form_to_warehouse", compact("production"));
//
//    }
//
//    public function confirm_warehouse_form_submit(Request $request, Production $production)
//    {
//        sleep(2);
//
//        $production = $production->refresh();
//        if ($production->waiting_status_id != 500070) {
//            return back()->withErrors("امکان تایید انبار برای کارت وجود ندارد");
//        }
//        $form = Form::create([
//            "user_id" => \Auth::user()->id,
//            "status_id" => 500000100,
//            "trans_kind" => 2,
//            "warehouse_id" => $production->product->warehouse->id,
//            "production_card_id" => $production->id
//        ]);
//        DB::transaction(function () use ($production, $form) {
//            $production->waiting_status_id = 500090;
//            $production->status_id = 520;
//            $production->save();
//            $production->RFWTerminate();
//            $production->log("", 520, 500090);
//
//
//            // send to warehouse
//            $production = Production::find($production->id);
//            $form->status_id = 500000200;
//            $form->save();
//
//
//            $wp = WarehouseProduct::create([
//                "warehouse_id" => $production->product->warehouse->id,
//                "product_id" => $production->product->id,
//                "form_id" => $form->id,
//                "factory_id" => $production->product->warehouse->factory->id,
//                "input" => $production->number_product,
//                "output" => 0,
//                "ic" => $production->product->ic,
//                "trans_kind" => 2,
//                "opp_kind" => 1
//            ]);
//            $wp->update_remaining();
//
//            $production->register_card2();
//
//            $replace_pc=ProductionReplace::where("production_replace_id",$production->id)->get();
//            foreach ($replace_pc as $replace){
//                $replace_production=Production::find($replace->production_id);
//                $replace_production->status_id = 530;
//                $replace_production->waiting_status_id = 500090;
//                $replace_production->save();
//                $replace_production->log("", 530, 500090);
//            }
//
//
//        });
//        return view("production.dashboard.confirm_entry_form_to_warehouse_show", compact("production", "form"));
//
//    }
//
//    public function reject_warehouse(Production $production)
//    {
//
//        if ($production->waiting_status_id != 500070) {
//            return back()->withErrors("امکان ثبت مغایرت برای کارت وجود ندارد");
//        }
//
//        $production->log("", 500, 500080);
//        $production->waiting_status_id = 500080;
//        $production->save();
//        return redirect()->route("production.dashboard.list", $production)->with(["success" => "سرپرست محترم عدم، تایید محصول را به سرپرست خط اعلام نمایید."]);
//    }
//
//
//    /**
//     * برای داشبورد تولید
//     */
//
//    public function form1(Production $production)
//    {
//
//        if ($production->status->id != 500 && $production->waiting_status_id != 500040) {
//            return back()->withErrors("کارت تولید مورد نظر قبلا ثبت شده است");
//        }
//        $worker_option = Option::get("worker", $production->supervisor_worker_id);
//        $line_option = Option::get("line_product", $production->line_product_id, $production->product_id);
//        /****************** */
//        return view("production.dashboard.production_card.form1", compact("production", "line_option", "worker_option"));
//    }
//
//    public function submit_form1(Production $production, Request $request)
//    {
//
//        if ($production->status->id != 500 && $production->waiting_status_id != 500040) {
//            return back()->withErrors("کارت تولید مورد نظر قبلا ثبت شده است");
//        }
//        $line_product = LineProductStation::exist($production->product_id, $request->line_id);
//        if (!$line_product) {
//            return redirect()->back()->withErrors("کد محصول خط ثبت  نادرست است.");
//        }
//
//        $request["line_product_id"] = $line_product->id;
//        $production->update($request->all());
//
//        return redirect()->route("production.dashboard.datetime.create", [$production, 1]);
//
//    }
//
//    #################################### datetime
//    public function datetime(Production $production, $version, $action = "form1")
//    {
//
//        $worker_option = Option::get("worker");
//
//        if ($production->status->id != 500 && $action == "form1") {
//            return back()->withErrors("کارت تولید مورد نظر قبلا ثبت شده است");
//        }
//
//        if ($version < 1) {
//            return redirect()->route("production.dashboard.form1", $production);
//        }
//
//        $production_date_times = ProductionDateTime::where([
//                "production_card_id" => $production->id,
//                "status_id" => 510000100]
//        )->get();
//        foreach ($production_date_times as $production_date_time_item) {
//            ProductionWorker::
//            where(["production_card_id" => $production->id,
//
//                "production_date_time_id" => $production_date_time_item->id])
//                ->delete();
//            $production_date_time_item->delete();
//
//        }
//
//        $production_date_time = ProductionDateTime::where([
//                "production_card_id" => $production->id,
//                "version" => $version
//            ]
//        )->firstOrCreate(
//            [
//                "production_card_id" => $production->id,
//                "version" => $version
//            ]
//        );
//
//        $production_workers = ProductionWorker::
//        where(["production_card_id" => $production->id,
//            "production_date_time_id" => $production_date_time->id])
//            ->get();
//
//        $start_datetime["h"] = null;
//        $start_datetime["m"] = "";
//
//        $end_datetime["h"] = null;
//        $end_datetime["m"] = "";
//
//        $shift_time["h"] = null;
//        $shift_time["m"] = "";
//
//        if ($production_date_time->status_id != 510000200) {
//
//            $start_datetime["h"] = Carbon::create($production_date_time->start_datetime)->format("H");
//            $start_datetime["m"] = Carbon::create($production_date_time->start_datetime)->format("i");
//
//            $end_datetime["h"] = Carbon::create($production_date_time->end_datetime)->format("H");
//            $end_datetime["m"] = Carbon::create($production_date_time->end_datetime)->format("i");
//
//            $shift_time["h"] = floor($production_date_time->shift_time / 60);
//            $shift_time["m"] = $production_date_time->shift_time % 60;
//        }
//
//        return view("production.dashboard.production_card.datetime", compact("action", "shift_time", "end_datetime", "start_datetime", "production_date_time", "worker_option", "production_workers", "production", "version"));
//
//    }
//
//    public function datetime_store(Request $request, Production $production, $version, $action = "create")
//    {
//        if ($production->status->id != 500) {
//            return back()->withErrors("کارت تولید مورد نظر قبلا ثبت شده است");
//        }
//
//        $production_time = Carbon::create($request->production_date, 0, 0, 0);
//        $start_datetime = Carbon::create($production_time->year, $production_time->month, $production_time->day, $request->start_datetime_h, $request->start_datetime_m, 0);
//        $end_datetime = Carbon::create($production_time->year, $production_time->month, $production_time->day, $request->end_datetime_h, $request->end_datetime_m, 0);
//
//        $diff = ($request->end_datetime_h * 60 + $request->end_datetime_m) - (
//                $request->start_datetime_h * 60 + $request->start_datetime_m);
//        if ($diff <= 0) {
//            return back()->withErrors("تاریخ شروع و پایان به درستی وارد نشده است");
//        };
//
//        $production_date_time = ProductionDateTime::firstOrCreate([
//            "production_card_id" => $production->id,
//            "version" => $version,
//        ]);
//
//        $shift_time = ($request->shift_time_h * 60 + $request->shift_time_m);
//        $production_date_time->shift_time = $shift_time;
//        $production_date_time->start_datetime = $start_datetime;
//        $production_date_time->end_datetime = $end_datetime;
//        $production_date_time->status_id =
//            $production_date_time->status_id == 510100100 ? 510000200 : 510000300;
//        $production_date_time->save();
//        $production_date_time->log();
//        $errors = "";
//
//        if ($shift_time <= 0) {
//            return back()->withErrors(" زمان شیفت به درستی وارد نشده است.");
//        }
//        ProductionWorker::
//        where(["production_card_id" => $production->id,
//            "production_date_time_id" => $production_date_time->id])
//            ->delete();
//
//        $worker_number = 0;
//        foreach ($request->data as $item) {
//
//            $worker_id = "worker_id_" . ($item["worker_id"] ?? 0);
//            if ($request->$worker_id != 0) {
//                $result = $production_date_time->checkForAddWorker($request->$worker_id);
//                if (isset($result)) {
//                    $errors .= "شاغل " . $item["worker_id"] . ": " .
//                        Worker::find($request->$worker_id)->fullname()
//                        . " در این ساعت در حال تولید محصول " . ($result->production_card->product->caption ?? "") . " با سریال کارت تولید " . $result->production_card->serial() . " بودند." . "<br/>";
//                }
//                ProductionWorker::firstOrCreate([
//                    "production_card_id" => $production->id,
//                    "post_name" => $item["post_name"],
//                    "worker_id" => $request->$worker_id,
//                    "production_date_time_id" => $production_date_time->id,
//                    "status_id" => 510100200,
//                ]);
//                $worker_number++;
//            }
//        }
//
//        $errors .= $worker_number == 0 ? "حداقل اطلاعات یک شاغل را تکمیل نمایید." . "<br/>" : "";
//        if ($errors != "") {
//
//            return back()->withErrors($errors);
//        }
//
//
//        $errors = $production->evaluation_indicator();
//        if ($errors != "") {
//            return back()->withErrors($errors);
//        }
//
//        if ($request->new_version != 0) {
//            return redirect()->route("production.dashboard.datetime.create", [$production, $version + 1, $action])->with(["success" => "زمان تولید ثبت شد، لطفا اطلاعات  زمان جدید  را وارد کنید"]);
//        } else {
//            return redirect()->route("production.dashboard.confirm", $production)->with(["success" => "زمان تولید ثبت شد،  لطفا اطلاعات را تایید کنید"]);
//        }
//
//    }
//
//    public function datetime_delete(Production $production, $version)
//    {
//        if ($production->status->id != 500) {
//            return back()->withErrors("کارت تولید مورد نظر قبلا ثبت شده است");
//        }
//
//        $production_date_times = ProductionDateTime::
//        where("production_card_id", $production->id)->
//        where("version", ">=", $version)->get();
//
//        foreach ($production_date_times as $production_date_time_item) {
//            ProductionWorker::
//            where(["production_card_id" => $production->id,
//
//                "production_date_time_id" => $production_date_time_item->id])
//                ->delete();
//            $production_date_time_item->delete();
//
//        }
//
//        if ($version <= 1) {
//            return redirect()->route("production.dashboard.form1", $production)->with(["success" => "زمان شیف مورد نظر حذف گردید"]);
//        } else {
//
//            return redirect()->route("production.dashboard.datetime.create", [$production, $version - 1])->with(["success" => "زمان شیفت مورد نظر حذف گردید"]);
//
//
//        }
//
//    }
//
//    ################################################## confirm
//
//    public function confirm(Production $production)
//    {
//        return view("production.dashboard.production_card.confirm", compact("production"));
//    }
//
//    public function submit_confirm(Production $production)
//    {
//        if ($production->status->id != 500 || !
//            in_array($production->waiting_status_id, [500040, 500050, 500060, 500070, 500080])
//        ) {
//            return back()->withErrors("کارت تولید مورد نظر قبلا ثبت شده است");
//        }
//
//        $production->waiting_status_id = 500050;
//        $production->save();
//        $production->log("", 500, 500040);
//
//        // Save Extra
//        $extraP = ExtraProduction::where("product_id", $production->product_id)->
//        firstOrCreate(["product_id" => $production->product_id]);
//        $extraP->amount += ($production->number_product - $production->number);
//        $extraP->save();
//
//
//        return redirect()->route("production.dashboard.result_confirm", $production)->with(["success" => "زمان تولید ثبت شد،  لطفا اطلاعات را تایید کنید"]);
//
//    }
//
//    public function result_confirm(Production $production)
//    {
//        $extraP = ExtraProduction::where("product_id", $production->product_id)->
//        firstOrCreate(["product_id" => $production->product_id]);
//
//        $replace_list = $this->replace_list($production, $extraP);
//
//        return view("production.dashboard.production_card.result_confirm", compact("replace_list", "production", "extraP"));
//    }
//
//    #################################################### replace
//
//    public function replace_list($production, $extraP)
//    {
//        return Production::
//        where(["status_id" => 500, "waiting_status_id" => 500040, "product_id" => $production->product_id])->
//        where("number", "<=", ($extraP->amount ?? 0))->
//        get();
//    }
//
//    public function replace(Production $production)
//    {
//        return view("production.dashboard.production_card.replace", compact("production"));
//    }
//
//    public function submit_replace(Request $request, Production $production)
//    {
//
//
//        $replace = Production::getFromSerial($request->replace_serial);
//
//        if (!$replace) {
//            return back()->withErrors("کارت تولید جایگزین به سریال " . $request->replace_serial . " یافته نشد");
//
//        }
//
//        $check = $this->validate_replace($request, $production, $replace);
//        if ($check) {
//            return $check;
//        }
//
//        return view("production.dashboard.production_card.replace_confirm", compact("production", "replace"));
//
//
//    }
//
//    public function submit_confirm_replace(Request $request, Production $production)
//    {
//        $masterCard = Production::find($request->replace_id);
//
//        $check = $this->validate_replace($request, $production, $masterCard);
//        if ($check) {
//            return $check;
//        }
//
//        if ($masterCard->replace($production)) {
//
//            $production->status_id = 530;
//            $production->waiting_status_id = 500090;
//            $production->save();
//            $production->log("", 530, 500090);
//
//            return redirect()->route("production.dashboard.list")->with(["success" => "کارت با موفقیت جایگزین شد"]);
//
//        } else {
//            return redirect()->route("production.dashboard.replace", $production)->
//            withErrors("با توجه به تعداد کارت مورد نظر امکان کنسل کردن کارت برای محصول وجود ندارد");
//
//
//        }
//
//    }
//
//    public function validate_replace(Request $request, Production $production, Production $replace)
//    {
//
//
//        if (!isset($replace)) {
//            return redirect()->route("production.dashboard.replace", $production)->withErrors("کارت با سریال " . $request->replace_serial . " یافت نشد");
//        }
//
//        if (!in_array($replace->waiting_status_id ,[ 500050,500060,500070,500080,500090])) {
//            return redirect()->route("production.dashboard.replace", $production)->withErrors("کارت تولید " . $replace->serial() . "  " . $replace->getStatus() . " است");
//        }
//
//        if ($replace->id == $production->id) {
//            return redirect()->route("production.dashboard.replace", $production)->withErrors("کارت با سریال " . $request->replace_serial . " را نمی توان با خودش جایگزین کرد");
//        }
//
//
//        if ($replace->product_id != $production->product_id) {
//            return redirect()->route("production.dashboard.replace", $production)->withErrors(" محصول کارت تولید " . $replace->serial() . " " . $replace->product->caption . "  می باشد و نمی تواند جایگزین " . $production->product->caption . " شود");
//        }
//
//        return null;
//    }
//
//    public function replace_group(Request $request, Production $production)
//    {
//        $extraP = ExtraProduction::where("product_id", $production->product_id)->
//        firstOrCreate(["product_id" => $production->product_id]);
//
//        $replace_list = $this->replace_list($production, $extraP);
//
//        foreach ($replace_list as $item) {
//            $pc = "pc_" . $item->id;
//            if (isset($request->$pc) && $request->$pc == 1) {
//
//                $check = $this->validate_replace($request, $item, $production);
//                if ($check) {
//                    return $check;
//                }
//
//                if (!$production->replace($item)) {
//
//                    return redirect()->route("production.dashboard.list", $production)->
//                    withErrors("با توجه به تعداد کارت مورد نظر امکان کنسل کردن کارت برای محصول وجود ندارد");
//
//                }
//            }
//        }
//        return redirect()->route("production.dashboard.list")->with(["success" => "کارت ها با موفقیت جایگزین شد"]);
//    }
//
//    ################################################## edit
//    public function edit(Production $production)
//    {
//        if (!\Auth::user()->posts->first()->checkButtonPermission("production.edit")) {
//            return back()->withErrors("شما به این عملیات دسترسی ندارید");
//        }
//        if ($production->status->id != 500 || !
//            in_array($production->waiting_status_id, [500040, 500050, 500060, 500070, 500080])) {
//            return redirect()->route("production.dashboard.view_card", $production);
//        }
//
//        $production->supervisor_worker_id;
//        $worker_option = Option::get("worker", $production->supervisor_worker_id);
//        $line_option = Option::get("line_product", $production->line_product_id, $production->product_id);
//        /****************** */
//        return view("production.dashboard.production_card.edit", compact("production", "line_option", "worker_option"));
//    }
//
//    public function submit_edit(Production $production, Request $request)
//    {
//
//        if (!\Auth::user()->posts->first()->checkButtonPermission("production.edit")) {
//            return back()->withErrors("شما به این عملیات دسترسی ندارید");
//        }
//        if ($production->status->id != 500 || !
//            in_array($production->waiting_status_id, [500040, 500050, 500060, 500070, 500080])) {
//            return redirect()->route("production.dashboard.view_card", $production);
//        }
//
//        $line_product = LineProductStation::exist($production->product_id, $request->line_id);
//        if (!$line_product) {
//            return redirect()->back()->withErrors("کد محصول خط ثبت  نادرست است.");
//        }
//
//        $request["line_product_id"] = $line_product->id;
//        $production->update($request->all());
//
//
//        $production->evaluation_indicator();
//        $status_id = $production->status_id;
//        $production->update(["status_id" => 540]);
//        $production->log("", 540);
//        $production->update(["status_id" => $status_id]);
//
//
//        return redirect()->route("production.dashboard.datetime.create", [$production, 1, "edit"]);
//    }
//
//    ############################################## cancel
//
//    public function cancel(Production $production)
//    {
//
//        if (!\Auth::user()->posts->first()->checkButtonPermission("production.cancel")) {
//            return back()->withErrors("شما به این عملیات دسترسی ندارید");
//        }
//        if ($production->status->id != 500 || !in_array($production->waiting_status_id, [500010, 500020, 500025, 500030, 500040])) {
//            return back()->withErrors("کارت تولید مورد نظر قبلا ثبت شده است و امکان کنسل کردن وجود ندارد");
//        }
//
//        return view("production.dashboard.production_card.cancel", compact("production"));
//    }
//
//    public function submit_cancel(Production $production, Request $request)
//    {
//
//
//        $production->update(["status_id" => 510, "waiting_status_id" => 500090]);
//        $production->RFWTerminate();
//        $production->log($request->description, 510, 500090);
//
//        return redirect()->route("production.dashboard.list")->
//        with(["success" => "کنسل کردن با موفقیت انجام شد."]);
//    }
//


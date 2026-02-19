<?php
//
//namespace App\Http\Controllers\Sales;
//
//use App\Events\Order\OrderLogEvent;
//use App\Events\Product\ProductRequestFormLogEvent;
//use App\Http\Controllers\Controller;
//use App\Http\Controllers\Warehouse\Out\DashboardController;
//use App\Http\Controllers\Warehouse\Out\ExitFormController;
//use App\Models\Customer\AccountBalance;
//use App\Models\Customer\ChannelTypePost;
//use App\Models\Customer\Customer;
//use App\Models\Customer\CustomerAddress;
//use App\Models\File\File;
//use App\Models\Form\Form;
//use App\Models\Form\FormItem;
//use App\Models\Form\Packing\PackingForm;
//use App\Models\LineProduct\Packing\PackingType;
//use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
//use App\Models\LineProduct\Product;
//use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
//use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
//use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormItem;
//use App\Models\LineProduct\Product\RejectProduct\RejectProductForm;
//use App\Models\Order\OrderListPackingType;
//use App\Models\Order\Permision\OrderPermissionPost;
//use App\Models\Post\PostStatus;
//use App\Models\Post\PostUser;
//use App\Models\Utility\Address\Address;
//use App\Models\Utility\Address\ProvincePost;
//use App\Models\Utility\Pdf;
//use App\Models\Utility\Setting;
//use App\Models\Warehouse\WarehouseProduct;
//use App\Models\Worker;
//use Illuminate\Http\Request;
//use App\Models\Order\OrderList;
//use App\Models\Order\Order;
//use App\Models\Utility\Option;
//use Carbon\Carbon;
//use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Str;
//use function Psy\debug;
//
//class ProductRequestPermissionControllerTest extends Controller
//{
//    public $view_path = 'sales.product_request_permission.';
//    public $route_path = 'sales.product_request_permission.';
//    public static $order_status_list = [35030, 35040, 35090];
//    public static $product_request_forms_status_list = [7005001, 7005004, 7005008];
//
//    public function index_customer($customer_id)
//    {
//
//        $order = Order::where('customer_id', $customer_id)->orderBy("id", "desc")->first();
//        if (!$order) {
//            return back()->withErrors("هیچ سفارشی برای مشتری یافت نشد.");
//        }
//        return $this->index($order);
//    }
//
//    public function index(Order $order)
//    {/**/
//1/0;
//        $result = self::check_permission($order);
//        if (!$result["result"]) {
//            return back()->withErrors($result["message"]);
//        }
//        $back_url = session("back_url");
//        $setting_data = Setting::getIntegerValueList([
//            "does_sales_need_to_register_a_loading_permit_form",
//            "does_sales_view_inventory_on_the_way",
//            "does_sales_set_permission_for_inventory_on_the_way",
//            "method_of_calculating_active_inventory_on_sales"
//        ]);
//        $does_sales_need_to_register_a_loading_permit_form = $setting_data["does_sales_need_to_register_a_loading_permit_form"];
//        $does_sales_view_inventory_on_the_way = $setting_data["does_sales_view_inventory_on_the_way"];
//        $does_sales_set_permission_for_inventory_on_the_way = $setting_data["does_sales_set_permission_for_inventory_on_the_way"];
//        $method_of_calculating_active_inventory_on_sales = $setting_data["method_of_calculating_active_inventory_on_sales"];
//
//
//        $result_product = self::GetProductData($order, [], $does_sales_set_permission_for_inventory_on_the_way, $method_of_calculating_active_inventory_on_sales);
//        if (!$result_product["result"]) {
//            return back()->withErrors($result_product["error"]);
//        }
//        $product_request_form_items = $result_product["product_request_form_items"];
//        $permission_amount = $result_product["permission_amount"];
//        $inventory_list = $result_product["inventory_list"];
//        $packing_form_inventory_by_order_list = $result_product["packing_form_inventory_by_order_list"];
//        $order_list_packing_type_cation_list = $result_product["order_list_packing_type_cation_list"];
//        $order_list_product_request_forms = $result_product["order_list_product_request_forms"];
//        $list_current_delivery_amount = $result_product["list_current_delivery_amount"];
//        $list_current_delivery_amount_order_list = $result_product["list_current_delivery_amount_order_list"];
//        $packing_form_on_the_way_by_order_list = $result_product["packing_form_on_the_way_by_order_list"];
//        $all_order_list = $result_product["all_order_list"];
//        $all_product_request_remaining_list = $result_product["all_product_request_remaining_list"];
//        $order_ids_count = $result_product["order_ids_count"];
//        $other_customer_order_list = $result_product["other_customer_order_list"];
//        $active_inventory_list = $result_product["active_inventory_list"];
//
//        if (session('new_permission_list_' . $order->customer_id)) {
//            $new_permission_list = session('new_permission_list_' . $order->customer_id);
//        } else {
//            $new_permission_list = [];
//        }
//
//        return view($this->view_path . "index_test", compact(
//            "packing_form_on_the_way_by_order_list", "list_current_delivery_amount",
//            "order_list_product_request_forms", "order_list_packing_type_cation_list",
//            "packing_form_inventory_by_order_list", "product_request_form_items",
//            "permission_amount", "order", "inventory_list", "list_current_delivery_amount_order_list",
//            "does_sales_need_to_register_a_loading_permit_form", "does_sales_view_inventory_on_the_way",
//            "does_sales_set_permission_for_inventory_on_the_way", "active_inventory_list",
//            "all_order_list", "all_product_request_remaining_list",
//            "new_permission_list", "order_ids_count", "back_url", "other_customer_order_list"
//        ));
//
//    }
//
//    public function index_product(Order $order)
//    {
//
//        $result = self::check_permission($order);
//        if (!$result["result"]) {
//            return back()->withErrors($result["message"]);
//        }
//        $result_product = self::GetProductDataByProduct($order);
//        if (!$result_product["result"]) {
//            return back()->withErrors($result_product["error"]);
//        }
//        $product_request_form_items = $result_product["product_request_form_items"];
//        $permission_amount = $result_product["permission_amount"];
//        $inventory_list = $result_product["inventory_list"];
//
//        return view($this->view_path . "index_product", compact("product_request_form_items", "permission_amount", "order", "inventory_list"));
//
//    }
//
//    public function create(Order $order)
//    {
//
//        $result = self::check_permission($order);
//        if (!$result["result"]) {
//            return back()->withErrors($result["message"]);
//        }
//        $result_product = self::GetProductData($order);
//        $product_request_form_items = $result_product["product_request_form_items"];
//        $permission_amount = $result_product["permission_amount"];
//        $inventory_list = $result_product["inventory_list"];
//        1 / 0;
//        return view($this->view_path . "create", compact("product_request_form_items", "permission_amount", "order", "inventory_list"));
//
//    }
//
//    public function submit(Request $request, Order $order)
//    {
//        $result = self::check_permission($order);
//        if (!$result["result"]) {
//            return back()->withErrors($result["message"]);
//        }
//
//        $setting_data = Setting::getIntegerValueList([
//            "does_sales_need_to_register_a_loading_permit_form",
//            "does_sales_view_inventory_on_the_way",
//            "does_sales_set_permission_for_inventory_on_the_way",
//            "method_of_calculating_active_inventory_on_sales"
//        ]);
//        $does_sales_need_to_register_a_loading_permit_form = $setting_data["does_sales_need_to_register_a_loading_permit_form"];
//        $does_sales_view_inventory_on_the_way = $setting_data["does_sales_view_inventory_on_the_way"];
//        $does_sales_set_permission_for_inventory_on_the_way = $setting_data["does_sales_set_permission_for_inventory_on_the_way"];
//        $method_of_calculating_active_inventory_on_sales = $setting_data["method_of_calculating_active_inventory_on_sales"];
//
//
//        $result_product = self::GetProductData($order, [], $does_sales_set_permission_for_inventory_on_the_way, $method_of_calculating_active_inventory_on_sales);
//
//        $product_request_form_items = $result_product["product_request_form_items"];
//        $permission_amount = $result_product["permission_amount"];
//        $inventory_list = $result_product["inventory_list"];
//        $active_inventory_list = $result_product["active_inventory_list"];
//        $packing_form_on_the_way_by_product = $result_product["packing_form_on_the_way_by_product"];
//
//        if (session('new_permission_list_' . $order->customer_id)) {
//            $new_permission_list = session('new_permission_list_' . $order->customer_id);
//        } else {
//            $new_permission_list = [];
//        }
//
//        // // چون ممکن است برای چند سفارش مقدار یک کالا را سفارش دهد، باید چک کنیم مجموع آن از مقدار موجودی فعال کمتر باشد.
//        $check_max_product = [];
//        foreach ($product_request_form_items as $product_request_form_item) {
//            if (isset($request->product_permission[$product_request_form_item->order_list_id]) && isset($request->checkbox_permission[$product_request_form_item->order_list_id][0])) {
//                $amount = $request->product_permission[$product_request_form_item->order_list_id] + 0;
//
//                $new_permission_list[$product_request_form_item->order_id][$product_request_form_item->order_list_id] = $amount;
//                $in_the_way = 0;
//                if ($does_sales_view_inventory_on_the_way && isset($packing_form_on_the_way_by_product[$product_request_form_item->product_id])) {
//
//                    $in_the_way = $packing_form_on_the_way_by_product[$product_request_form_item->product_id];
//                }
//                if (round($amount, 5) + 0 > round($permission_amount[$product_request_form_item->order_list_id], 5) + 0) {
//
//                    $product = Product::find($product_request_form_item->product_id);
//                    return back()->withErrors("با توجه به اینکه موجودی فعال کالای ( " . $product->caption . ")، " . ($permission_amount[$product_request_form_item->order_list_id]) . "- $amount " . $product->unit->caption . " می باشد، امکان ثبت مجوز خروج برای " . " " . $product->unit->caption . " از کالا امکان پذیر نیست.");
//
//                }
//            } else {
//                unset($new_permission_list[$product_request_form_item->order_id][$product_request_form_item->order_list_id]);
//                if (isset($new_permission_list[$product_request_form_item->order_id]) && count($new_permission_list[$product_request_form_item->order_id]) == 0) {
//                    unset($new_permission_list[$product_request_form_item->order_id]);
//                }
//            }
//
//
//        }
//
//
//        $inventory_list_check = json_decode(json_encode($active_inventory_list), true);;
//        $inventory_list_check_products = []; // چون موجودی فعال همه کالا ها نمایش داده می شود، فقط آنهایی را بررسی می کنیم که الان می خواهد براشون مجوز ثبت کند.
//        foreach ($product_request_form_items as $product_request_form_item) {
//
//            if (!isset($new_permission_list[$product_request_form_item->order_id][$product_request_form_item->order_list_id])) {
//                continue;
//            }
//            $inventory_list_check[$product_request_form_item->product_id] -= $new_permission_list[$product_request_form_item->order_id][$product_request_form_item->order_list_id];
//            $inventory_list_check_products[] = $product_request_form_item->product_id;
//        }
////if($order->id == 392){
////    return $inventory_list_check_products;
////}
//        foreach ($inventory_list_check as $product_id => $inventory_list_item) {
//
//            if (!in_array($product_id, $inventory_list_check_products)) {
//                continue;
//            }
//
//            if (round($inventory_list_item, 5) < 0) {
//                $product = Product::find($product_id);
//                return back()->withErrors(" با توجه به اینکه مقدار موجودی فعال کالای " .
//                    $product->caption
//                    . " به میزان " .
//                    ($active_inventory_list[$product_id])
//                    . " " .
//                    $product->unit->caption
//                    . " می باشد، امکان ثبت مجوز برای بیش از این مقدار مقدور نمی باشد.");
//            }
//        }
//
//
//        if (count($new_permission_list) == 0) {
//            return back()->withErrors("لطفا حداقل یک ردیف جهت ثبت مجوز بارگیری انتخاب کنید.");
//        }
//
//        session(["new_permission_list_" . $order->customer_id => $new_permission_list]);
//
//        if ($request->save_status == 1) {
//            return back()->with(["warning" => "اطلاعات با موفقیت ذخیره گردید"]);
//        }
//
//        return redirect()->route($this->route_path . "show_permission", $order);
//    }
//
//
//    public function show_permission(Order $order)
//    {
//
//        $result = self::check_permission($order);
//        if (!$result["result"]) {
//            return back()->withErrors($result["message"]);
//        }
//        if (!session('new_permission_list_' . $order->customer_id)) {
//            return redirect()->route($this->route_path . "index", $order)->withErrors("اطلاعات ثبت مجوز خروج کامل نیست، لطفا مجدد تلاش کنید.");
//        }
//
//        $new_permission_list = session('new_permission_list_' . $order->customer_id);
//        $order_list_ids = [];
//        foreach ($new_permission_list as $new_permission_list_item) {
//
//            foreach ($new_permission_list_item as $key => $amount) {
//                $order_list_ids[] = $key;
//            }
//        }
//
//        $result_product = self::GetProductData($order, $order_list_ids);
//        $product_request_form_items = $result_product["product_request_form_items"];
//        $permission_amount = $result_product["permission_amount"];
//        $inventory_list = $result_product["inventory_list"];
//        $order_list_packing_type_list = $result_product["order_list_packing_type_list"];
//
//// بررسی مقدار وزن و حجم و پیشنهاد ماشین های مجاز
//        $allow_get_shipping_method_in_product_permission = $setting_data = Setting::getIntegerValue("allow_get_shipping_method_in_product_permission");;
//        $car_type_option = [];
//        $shipping_method_option = Option::get("shipping_methods", $order->shipping_method_id, 0);
//        $delivery_point_type_option = [];
//        if ($allow_get_shipping_method_in_product_permission) {
//            // در صورتی که تنظیمات دریافت روش های ارسال بار بله است، مقادیر آنها را می گیریم.
//            $bar_weight = array_sum($permission_amount) / 1000; // ton
//            $bar_volume = 0;
//            $packing_type_volume = [];
//            foreach ($product_request_form_items as $item) {
//                $first_packing_type_id = $order_list_packing_type_list[$item->order_list_id][0];
//                if (!isset($packing_type_volume[$first_packing_type_id])) {
//                    $packing_type_volume[$first_packing_type_id] = PackingType::find($first_packing_type_id);;
//                }
//                if (!$packing_type_volume[$first_packing_type_id]->normal_amount) {
//                    return back()->withErrors("با توجه به اینکه مقدار نرمال بسته بندی " . $packing_type_volume[$first_packing_type_id]->caption . " مشخص نشده است، امکان محاسبه و حجم ارسال بار امکان پذیر نمی باشد.");
//                }
//                $volume = PackingType::GetVolume($packing_type_volume[$first_packing_type_id]);
//                $bar_volume += $volume * $permission_amount[$key] / $packing_type_volume[$first_packing_type_id]->normal_amount;
//                $bar_weight += $permission_amount[$item->order_list_id] * $item->product->weight / 1000;
//            }
//
//            $car_type_option = Option::get("car_type_with_weight_volume", $order->car_type_id, 0, ["volume" => $bar_volume, "weight" => $bar_weight]);
//            $shipping_method_option = Option::get("shipping_methods", $order->shipping_method_id, 0);
//            $delivery_point_type_option = Option::get("delivery_point_type", $order->delivery_point_type_id, 0);
//        }
//        $address_list =
//            Order::
//            where("customer_id", $order->customer_id)->
//            select("address_id", "orders.id")->
//            groupBy("address_id")->
//            get();
//        $province_option = Option::get("province", $order->address->province_id ?? 0);
//        $country_option = Option::get("country", $order->address->country_id ?? 112);
//
//        return view($this->view_path . "show_permission", compact("address_list", "allow_get_shipping_method_in_product_permission", "new_permission_list", "country_option", "province_option", "shipping_method_option", "car_type_option", "product_request_form_items", "permission_amount", "order", "inventory_list", "delivery_point_type_option"));
//
//
//    }
//
//    public function confirm(Request $request, Order $order)
//    {
//
//        $result = self::check_permission($order);
//        if (!$result["result"]) {
//            return back()->withErrors($result["message"]);
//        }
//        if (!session('new_permission_list_' . $order->customer_id)) {
//            return redirect()->route($this->route_path . "index", $order)->withErrors("اطلاعات ثبت مجوز خروج کامل نیست، لطفا مجدد تلاش کنید.");
//        }
//
//        if (!$request->delivery_datetime) {
//            return back()->withErrors("لطفا تاریخ تحویل بار را ثبت نمایید.");
//        }
//
//        ############# Address
//        $address_id = null;
//        if ($request->address_id != 0) {
//            $address_id = $request->address_id;
//        } else {
//            if (
//                $request->country_id == 0 ||
//                $request->province_id == 0 ||
//                $request->city_name == null ||
//                $request->phone == null ||
//                $request->mobile == null ||
//                $request->postal_code == null ||
//                $request->address == null) {
//                return back()->withErrors("لطفا اطلاعات آدرس را به صورت کامل وارد نمایید.");
//            }
//            $address = Address::create($request->all());
//            $address_id = $address->id;
//            //آدرس مشتری
//            CustomerAddress::create([
//                'customer_id' => $order->customer_id,
//                'address_id' => $address_id,
//                "is_default" => 0,
//            ]);
//        }
//
//
//        $new_permission_list = session('new_permission_list_' . $order->customer_id);
//        session(["new_permission_list_" . $order->customer_id => null]);
//        $product_request_permission = Product\ProductRequestPermission\ProductRequestPermission::create([
//            "status_id" => 7015001
//        ]);
//        $request["insurance_amount"] = str_replace(",", "", $request["insurance_amount"]);
//        $request["shipping_cost"] = str_replace(",", "", $request["shipping_cost"]);
//        $product_request_permission->update($request->all());
//        $dcprlp = $product_request_permission->getCode();
//        $dcrp_list_code = "";
//        foreach ($new_permission_list as $order_id => $new_permission_list_item) {
//
//            $order = Order::find($order_id);
//            $other = ["order_list" => $new_permission_list_item];
//            $result = ProductRequestForm::newRequest(
//                $order,
//                $order->customer_id,
//                30,
//                1,
//                $other,
//                $request->delivery_datetime,
//                $dcprlp);
//
//            if (!$result["result"]) {
//                return redirect()->route($this->route_path . "index", $order)->withErrors($result["error"]);
//            }
//            if ($result["product_request_forms"]) {
//                foreach ($result["product_request_forms"] as $product_request_form) {
//                    Product\ProductRequestPermission\ProductRequestPermissionItem::create([
//                        "product_request_permission_id" => $product_request_permission->id,
//                        "product_request_form_id" => $product_request_form->id,
//                        "order_id" => $order_id,
//                    ]);
//                    $dcrp_list_code .= $product_request_form->code . " برای سفارش " . $order->code() . "<br/>";
//                }
//            }
//        }
//
//        return redirect()->route("sales.dashboard.index")->with(["success" => "مجوز خروج از انبار با شماره " . $product_request_permission->code . " صادر گردید." . "<br/>" .
//            "شماره (های) درخواست از انبار به شرح زیر می باشد:" . "<br/>" . $dcrp_list_code
//        ]);
//
//    }
//
//    public function get_other_customer_permission(Order $order, Product $product)
//    {
//
//        $list_result = self::GetOtherCustomerPermission(2, $order, [$product->id]);
//
//        $list_current_delivery = self::GetCurrentDelivery([$product->id], []);
//
//        $list_current_delivery_product_request_form = [];
//        foreach ($list_current_delivery as $item) {
//            $list_current_delivery_product_request_form[$item->product_request_form_id] = $item->amount;
//        }
//        $list = $list_result["list"];
//        return view($this->view_path . "get_other_customer_permission", compact("order", "list_current_delivery_product_request_form", "list", "product"));
//    }
//
//    public function get_other_order(Order $order, Product $product)
//    {
//
//        // به دست آوردن مقدار سفارش سایر مشتریان
//        $list = Order::join("order_list", "orders.id", "=", "order_id")->
//        whereIn("orders.status_id", self::$order_status_list)->
//        where("product_id", $product->id)->
//        where("order_id", "!=", $order->id)->
//        where("orders.customer_id", "!=", $order->customer_id)->
//
//        paginate();
//
//        return view($this->view_path . "get_other_order", compact("order", "list", "product"));
//    }
//
//    public function get_other_customer_order(Order $order, Product $product)
//    {
//
//        // به دست آوردن مقدار سایر سفارشات مشتری
//        $list = Order::join("order_list", "orders.id", "=", "order_id")->
//        whereIn("orders.status_id", self::$order_status_list)->
//        where("product_id", $product->id)->
//        where("order_id", "!=", $order->id)->
//        where("orders.customer_id", $order->customer_id)->
//
//        paginate();
//
//        return view($this->view_path . "get_other_customer_order", compact("order", "list", "product"));
//    }
//
//    public function remove_product_request_form_item(ProductRequestForm $product_request_form, Product $product, $back_type, $back_order_id = 0)
//    {
//
//
//        $result = self::RemoveProductRequestFormItem($product_request_form, $product);
//
//        if (!$result["result"]) {
//            return back()->withErrors($result["error"]);
//        }
//
//        $message = $result["message"];
//        // برور رسانی وضعیت درخواست
//        switch ($back_type) {
//            case "back_to_order":
//                return redirect()->route("sales.dashboard.view_product_request_form", [$product_request_form->order, $product_request_form])->with(["success" => $message]);
//
//            case "back_to_permission":
//                return redirect()->route($this->view_path . "get_other_customer_permission", [$back_order_id, $product])->with(["success" => $message]);
//
//        }
//        return back()->with(["success" => $message]);
//
//    }
//
//
//    public function remove_product_request_form(ProductRequestForm $product_request_form, $product_id, $back_type)
//    {
//
//
//        $message = "نتیجه خروجی تعدیل درخواست به ازای هر ردیف به شرح زیر می باشد: ";
//        foreach ($product_request_form->items as $product_request_item) {
//            $result = self::RemoveProductRequestFormItem($product_request_form, $product_request_item->product);
//
//
//            if (!$result["result"]) {
//                $message .= "<br/>" . $product_request_item->product->code . ": " . $result["error"];
//            } else {
//                $message .= "<br/>" . $product_request_item->product->code . ": " . $result["message"];
//            }
//        }
//
//
//        $product_request_form->updateApplicantStatus();
//
//        // برور رسانی وضعیت درخواست
//        switch ($back_type) {
//            case "back_to_order":
//                return redirect()->route("sales.dashboard.view_product_request_form", [$product_request_form->order, $product_request_form])->with(["success" => $message]);
//
//            case "back_to_permission":
//                return redirect()->route($this->view_path . "get_other_customer_permission", [$product_request_form->order, $product_id])->with(["success" => $message]);
//
//            case "test":
//                return "OK";
//        }
//
//
//    }
//
//    public function remove_permission_order_list(OrderList $order_list, Product $product)
//    {
//
//        // کنسل کردن مقدار مجوز صادر شده بر اساس ردیف سفارش
//
//        $product_request_form_item_count = ProductRequestFormItem::where([
//            "order_list_id" => $order_list->id,
//            "product_id" => $product->id
//        ])->count();
//        if ($product_request_form_item_count == 0) {
//            return back()->withErrors("تا کنون هیچ درخواستی برای کالا ثبت نشده است.");
//        }
//
//        $product_request_form_item = ProductRequestFormItem::where([
//            "order_list_id" => $order_list->id,
//            "product_id" => $product->id
//        ])->get();
//
//
//        $product_request_form_ids = [];
//        foreach ($product_request_form_item as $item) {
//            $product_request_form_ids[] = $item->product_request_form_id;
//        }
//
//        $list_result = self::GetOtherCustomerPermission(3, null, [$product->id], $product_request_form_ids);
//
//        $list_current_delivery = self::GetCurrentDelivery([$product->id], $product_request_form_ids);
//
//        $list_current_delivery_product_request_form = [];
//        foreach ($list_current_delivery as $item) {
//            $list_current_delivery_product_request_form[$item->product_request_form_id] = $item->amount;
//        }
//        $list = $list_result["list"];
//        return view($this->view_path . "remove_permission_order_list", compact("order_list", "list_current_delivery_product_request_form", "list", "product"));
//
//    }
//
//    // نمایش لیست سایر بسته بندی ها
//    public function show_packing_form_inventory(OrderList $order_list, Product $product, $type)
//    {
//        $valid_packing_type_ids = $order_list->getPackingType("packing_type_ids");
//
//        switch ($type) {
//            case "other_packing_inventory" : // سایر بسته بندی ها
//                //به دست آوردن بسته بندی های تحویل شده
//                $list = PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_item.packing_form_id")->
//                where(
//                    "packing_forms.status_id", 7007003 //  تحویل شده به انبار
//                )->
//                where("product_id", $product->id)->whereNotIn("packing_type_id", $valid_packing_type_ids)->
//                groupBy("packing_forms.id")->
//                selectRaw("packing_forms.code,packing_forms.status_id as status_id, packing_forms.id as id ,packing_type_id, product_id, sum(final_amount) as final_amount")->
//                with("status", "packing_type")->
//                paginate(30);
//
//                break;
//            case "packing_inventory" : //  بسته بندی ها
//                //به دست آوردن بسته بندی های تحویل شده
//                $list = PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_item.packing_form_id")->
//                where(
//                    "packing_forms.status_id", 7007003 //  تحویل شده به انبار
//                )->
//                where("product_id", $product->id)->whereIn("packing_type_id", $valid_packing_type_ids)->
//                groupBy("packing_forms.id")->
//                selectRaw("packing_forms.code,packing_forms.status_id as status_id, packing_forms.id as id ,packing_type_id, product_id, sum(final_amount) as final_amount")->
//                with("status", "packing_type")->
//                paginate(30);
//
//                break;
//
//
//        }
//
//        return view($this->view_path . "show_packing_form_inventory", compact("order_list", "product", "list", "type"));
//    }
//
//    public static function RemoveProductRequestFormItem(ProductRequestForm $product_request_form, Product $product)
//    {
//        // کنسل کردن مقدار مجوز صادر شده: مقدار درخواست برابر می شود با مقدار تحویل شده + مقدار در حال تحویل
//
//        $product_request_form_item_count = ProductRequestFormItem::where([
//            "product_request_form_id" => $product_request_form->id,
//            "product_id" => $product->id
//        ])->count();
//        if ($product_request_form_item_count != 1) {
//            return [
//                "result" => false,
//                "error" => "ردیف  کالای " . $product->fullCaption() . " در درخواست " . $product_request_form->code . " نامتبر است."
//            ];
//
//        }
//
//        $product_request_form_item = ProductRequestFormItem::where([
//            "product_request_form_id" => $product_request_form->id,
//            "product_id" => $product->id
//        ])->first();
//
//        $list = self::GetCurrentDelivery([$product->id], [$product_request_form->id]);
//
//        $current_delivery = 0;
//        foreach ($list as $item) {
//            if ($item->product_id == $product->id) {
//                $current_delivery = $item->amount;
//            }
//        }
//        $before_amount = $product_request_form_item->amount_request;
//        $new_amount_request = $product_request_form_item->amount_sent + $current_delivery;
//        $new_amount_remaining = $current_delivery;
//
//        if ($new_amount_remaining >= $before_amount) {
//            return [
//                "result" => false,
//                "error" => "با توجه به اینکه برای درخواست مورد نظر توسط انباردار برگ خروج صادر گردید، امکان کنسل کردن درخواست وجود ندارد. "
//            ];
//
//        }
//
//        $product_request_form_item->amount_request = $new_amount_request;
//        $product_request_form_item->amount_remaining = $new_amount_remaining;
//
//        $product_request_form_item->save();
//
//        $message = "تغییر مقدار درخواست " . $product->fullCaption() . ": از " . $before_amount . " به $new_amount_remaining" . " " . $product->unit->caption;
//        event(new ProductRequestFormLogEvent($product_request_form, $message, null, 7005026));
//
//        $message .= " با موفقیت انجام شد.";
//
//        $product_request_form = ProductRequestForm::find($product_request_form->id);
//        $product_request_form->updateApplicantStatus();
//
//        return [
//            "result" => true,
//            "message" => $message
//        ];
//    }
//
//    public static function GetProductData(Order $order, $order_list_ids = [], $does_sales_set_permission_for_inventory_on_the_way = 0, $method_of_calculating_active_inventory_on_sales = 0)
//    {
//
//        $product_request_form_items = OrderList::
//        join("orders", "orders.id", "order_list.order_id")->
//        leftJoin("product_request_form_item", 'product_request_form_item.order_list_id', 'order_list.id')->
//        when(count($order_list_ids) > 0, function ($query) use ($order_list_ids) {
//            $query->whereIn("order_list.id", $order_list_ids);
//        })->
//        where("orders.customer_id", $order->customer_id)->
//        whereIn("orders.status_id", self::$order_status_list)->
//        whereNull("from_order_id")-> // ردیف سفارش اصلی باشد.
//        whereNull("prefactor_number")-> // ردیف سفارش اصلی باشد.
//        // whereNotIn("product_request_forms.status_id", [7005006])->
//        selectRaw(
//            "order_list.order_id as order_id  ,order_list.id as order_list_id,order_list.product_id,product_request_form_id,orders.order_datetime,
//            orders.code as order_code,orders.series as order_series,
//            sum(product_request_form_item.amount_remaining) as amount_remaining,sum(product_request_form_item.amount_sent) as amount_sent,sum(product_request_form_item.amount_request) as amount_request, order_list.amount as order_amount
//            ")->
//        groupBy("orders.id", "order_list.product_id")->
//        with("product")->
//        orderBy("orders.order_datetime", "desc")->
//        orderBy("amount_remaining", "asc")->
//        orderBy("order_list.product_id", "desc")->
//        paginate(max(count($order_list_ids), 50)); //
//
//        if (count($product_request_form_items) == 0) {
//            return [
//                "result" => false,
//                "error" => "هنوز درخواست کالا از انبار برای سفارش های مشتری ایجاد نشده است ویا تمامی سفارش ها ارسال شده اند."
//            ];
//        }
//        $product_ids = [];
//        $permission_amount = []; // مقدار مجوز حهت خروج
//        $order_list_product = [];
//        $order_ids_count = []; // تعداد سفارش هایی که در صفحه نمایش داده می شود (جهت مایش تیک)
//        $customer_amount_request = [];
//        $customer_amount_sent = []; // مقداری که برای مشتری ارسال شده است.
//        foreach ($product_request_form_items as $product_request_form_item) {
//            $product_ids[] = $product_request_form_item->product_id;
//            $order_list_product  [$product_request_form_item->order_list_id] = $product_request_form_item->product_id;
//            if (!isset($customer_amount_request[$product_request_form_item->product_id])) {
//                $customer_amount_request[$product_request_form_item->product_id] = 0;
//                $customer_amount_sent[$product_request_form_item->product_id] = 0;
//            }
//
//            $customer_amount_request[$product_request_form_item->product_id] += $product_request_form_item->amount_request;
//            $customer_amount_sent[$product_request_form_item->product_id] += $product_request_form_item->amount_sent;
//
//        }
//        $product_ids[] = -1;
//
//
//        // به دست آوردن مقدار بسته بندی ها با توجه به نوع بسته بندی
//        $order_list_ids = array_keys($order_list_product);
//        $order_list_ids[] = -1;
//        $order_list_packing_types = OrderListPackingType::
//        whereIn("order_list_id", $order_list_ids)->
//        select("order_list_id", "packing_type_id")->
//        with("packing_type")->
//        get();
//        $order_list_packing_type_list = [];
//        $order_list_packing_type_cation_list = [];
//
//        $packing_type_list = [];
//        foreach ($order_list_packing_types as $order_list_packing_type) {
//            $order_list_packing_type_list[$order_list_packing_type->order_list_id][] = $order_list_packing_type->packing_type_id;
//            $order_list_packing_type_cation_list[$order_list_packing_type->order_list_id][] = $order_list_packing_type->packing_type->caption;
//            $packing_type_list[] = $order_list_packing_type->packing_type_id;
//
//        }
//
//        //به دست آوردن بسته بندی های تحویل شده
//        $packing_form_inventory = PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_item.packing_form_id")->
//        where(
//            "packing_forms.status_id", 7007003 //  تحویل شده به انبار
//        )->
//        whereIn("product_id", $product_ids)->whereIn("packing_type_id", $packing_type_list)->
//        groupBy("packing_type_id", "product_id")->
//        selectRaw("packing_type_id, product_id, sum(final_amount) as final_amount")->
//        get();
//
//        $packing_form_inventory_by_order_list = [];
//        foreach ($order_list_ids as $order_list_id) {
//            $packing_form_inventory_by_order_list[$order_list_id] = 0;
//        }
//        $order_list_packing_type_checked = []; // جهت اینکه مقدار هر نوع بسته بندی فقط در یک ردیف اسفاده شود و تکراری نشود.
//        foreach ($packing_form_inventory as $packing_form_inventory_item) {
//            foreach ($order_list_packing_types as $order_list_packing_type) {
//                if (
//                    in_array($packing_form_inventory_item->packing_type_id, $order_list_packing_type_list[$order_list_packing_type->order_list_id])
//                    && $order_list_product[$order_list_packing_type->order_list_id] == $packing_form_inventory_item->product_id
//                    && !isset($order_list_packing_type_checked[$order_list_packing_type->order_list_id][$packing_form_inventory_item->packing_type_id])
//
//                ) {
//
//                    $packing_form_inventory_by_order_list[$order_list_packing_type->order_list_id] += $packing_form_inventory_item->final_amount;
//                    $order_list_packing_type_checked[$order_list_packing_type->order_list_id][$packing_form_inventory_item->packing_type_id] = 1;
//                }
//            }
//
//        }
//
//        // بسته بندی های در راه
//        //به دست آوردن بسته بندی های تحویل شده
//        $packing_form_on_the_way = PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_item.packing_form_id")->
//        whereIn(
//            "packing_forms.status_id", PackingForm::$OnTheyWayStatus//  در راه
//        )->
//        whereIn("product_id", $product_ids)->
//        groupBy("packing_type_id", "product_id")->
//        selectRaw("packing_type_id, product_id, sum(final_amount) as final_amount")->
//        get();
//
//        $packing_form_on_the_way_by_order_list = [];
//        $packing_form_on_the_way_by_product = [];
//        foreach ($order_list_ids as $order_list_id) {
//            $packing_form_on_the_way_by_order_list[$order_list_id] = [
//                "allowed" => 0,
//                "now_allowed" => 0,
//            ];
//        }
//        $order_list_packing_type_checked = []; // جهت اینکه مقدار هر نوع بسته بندی فقط در یک ردیف اسفاده شود و تکراری نشود.
//        foreach ($packing_form_on_the_way as $packing_form_on_the_way_item) {
//
//
//            foreach ($order_list_packing_types as $order_list_packing_type) {
//                if (
//                    in_array($packing_form_on_the_way_item->packing_type_id, $order_list_packing_type_list[$order_list_packing_type->order_list_id])
//                    && $order_list_product[$order_list_packing_type->order_list_id] == $packing_form_on_the_way_item->product_id
//                    && !isset($order_list_packing_type_checked[$order_list_packing_type->order_list_id][$packing_form_on_the_way_item->packing_type_id])
//
//                ) {
//
//                    $packing_form_on_the_way_by_order_list[$order_list_packing_type->order_list_id]["allowed"] += $packing_form_on_the_way_item->final_amount;
//                    $order_list_packing_type_checked[$order_list_packing_type->order_list_id][$packing_form_on_the_way_item->packing_type_id] = 1;
//
//                    if (!isset($packing_form_on_the_way_by_product[$packing_form_on_the_way_item->product_id])) {
//                        $packing_form_on_the_way_by_product[$packing_form_on_the_way_item->product_id] = 0;
//                    }
//                    $packing_form_on_the_way_by_product[$packing_form_on_the_way_item->product_id] += $packing_form_on_the_way_item->final_amount;
//
//
//                } elseif (
//                    !in_array($packing_form_on_the_way_item->packing_type_id, $order_list_packing_type_list[$order_list_packing_type->order_list_id])
//                    && $order_list_product[$order_list_packing_type->order_list_id] == $packing_form_on_the_way_item->product_id
//                    && !isset($order_list_packing_type_checked[$order_list_packing_type->order_list_id][$packing_form_on_the_way_item->packing_type_id])
//
//                ) {
//
//                    $packing_form_on_the_way_by_order_list[$order_list_packing_type->order_list_id]["now_allowed"] += $packing_form_on_the_way_item->final_amount;
//                    $order_list_packing_type_checked[$order_list_packing_type->order_list_id][$packing_form_on_the_way_item->packing_type_id] = 1;
//                }
//            }
//
//        }
//
//        // موجودی کل بسته بندی ها
//        $inventory_list = WarehouseProduct::getProductInventoryList($product_ids);
//
//
//        // به دست آوردن درخواست های کالا از انبار
//        $product_request_form_list = ProductRequestFormItem::whereIn("order_list_id", $order_list_ids)->
//        select("order_list_id", "product_request_form_id")->with("product_request_form")->get();
//
//        $order_list_product_request_forms = [];
//        $product_request_form_ids = [];
//        $product_request_form_ids[] = -1;
//        $list_current_delivery_amount_order_list = [];
//        foreach ($product_request_form_list as $product_request_form_list_item) {
//            $order_list_product_request_forms[$product_request_form_list_item->order_list_id] [$product_request_form_list_item->product_request_form_id] =
//                ["code" => $product_request_form_list_item->product_request_form->code,
//                    "id" => $product_request_form_list_item->product_request_form_id
//                ];
//
//            $product_request_form_ids[] = $product_request_form_list_item->product_request_form_id;
//            $list_current_delivery_amount_order_list  [$product_request_form_list_item->order_list_id] = 0;
//        }
//
//
//        // به دست آوردن مقدار سفارش سایر سفارشات
//        $all_order_list = Order::join("order_list", "orders.id", "=", "order_id")->
//        whereIn("orders.status_id", self::$order_status_list)->
//        whereIn("product_id", $product_ids)->
//        selectRaw("sum(amount) as amount, product_id")->
//        groupBy("product_id")->pluck("amount", "product_id");
//
//
//        // به دست آوردن مقدار سفارش سایر مشتریان
//        $other_customer_order_list = Order::join("order_list", "orders.id", "=", "order_id")->
//        whereIn("orders.status_id", self::$order_status_list)->
//        whereIn("product_id", $product_ids)->
//        where("orders.customer_id", "!=", $order->customer_id)->
//        selectRaw("sum(amount) as amount, product_id")->
//        groupBy("product_id")->pluck("amount", "product_id");
//
//        // به دست آوردن مقدار درخواست های مجوز باقی مانده سایر مشتریان
//        $all_product_request_remaining_list = self::GetOtherCustomerPermission(1, $order, $product_ids);
//
//        $list_current_delivery_amount = []; // مقدار در حال تحویل کل درخواست های سایر مشتریان
//        $list_current_delivery_amount_all = []; // مقدار در حال تحویل کل درخواست ها
//
//        $list_current_delivery_amount_product_request_form = [];
//
//        // اگر کالایی وجود نداشت آن را صفر در نظر می گیریم.
//        foreach ($product_ids as $product_id) {
//            if (!isset($all_product_request_remaining_list[$product_id])) {
//                $all_product_request_remaining_list[$product_id] = 0;
//            }
//            if (!isset($all_order_list[$product_id])) {
//                $all_order_list[$product_id] = 0;
//            }
//            if (!isset($list_current_delivery_amount[$product_id])) {
//                $list_current_delivery_amount[$product_id] = 0;
//            }
//        }
//
//        // به دست آوردن مقدار درخواست های در حال برای کل درخواست ها
//        $list_current_delivery = self::GetCurrentDelivery($product_ids);
//
//        // لیست کل درخواست های در حال تحویل به تفکیک کالا
//        $list_current_delivery_amount_all = self::GetCurrentDeliveryAll($product_ids);
//
//        $list_current_delivery_product_ids = array_keys($list_current_delivery_amount_all);
//
//        foreach ($list_current_delivery as $list_current_delivery_item) {
//
//            if (!isset($list_current_delivery_amount[$list_current_delivery_item->product_id])) {
//                $list_current_delivery_amount[$list_current_delivery_item->product_id] = 0;
//            }
////            if (!isset($list_current_delivery_amount_all[$list_current_delivery_item->product_id])) {
////                $list_current_delivery_amount_all[$list_current_delivery_item->product_id] = 0;
////            }
//            if (in_array($list_current_delivery_item->product_request_form_id, $product_request_form_ids)) {
//                if (!isset($list_current_delivery_amount_product_request_form[$list_current_delivery_item->product_request_form_id][$list_current_delivery_item->product_id])) {
//                    $list_current_delivery_amount_product_request_form[$list_current_delivery_item->product_request_form_id][$list_current_delivery_item->product_id] = 0;
//                }
//                $list_current_delivery_amount_product_request_form[$list_current_delivery_item->product_request_form_id][$list_current_delivery_item->product_id] += $list_current_delivery_item->amount;
//                $list_current_delivery_product_ids[] = $list_current_delivery_item->product_id;
//            } else {
//                $list_current_delivery_amount[$list_current_delivery_item->product_id] += $list_current_delivery_item->amount;
//            }
//
//            //  $list_current_delivery_amount_all[$list_current_delivery_item->product_id] += $list_current_delivery_item->amount;
//
//
//        }
//
//        // به دست آوردن مقدار درخواست در حال تحویل ردیف های مشتری
//        if (count($list_current_delivery_amount_product_request_form) > 0) {
//
//            $list_prf_item = ProductRequestFormItem::
//            whereIn("product_id", $list_current_delivery_product_ids)->
//            whereIn("product_request_form_id", array_keys($list_current_delivery_amount_product_request_form))->
//            select("product_request_form_id", "order_list_id", "product_id")->
//            get();
//
//            foreach ($list_prf_item as $item_k) {
//                if (!isset($list_current_delivery_amount_order_list[$item_k->order_list_id])) {
//                    $list_current_delivery_amount_order_list[$item_k->order_list_id] = 0;
//                }
//                $list_current_delivery_amount_order_list[$item_k->order_list_id] +=
//                    isset($list_current_delivery_amount_product_request_form[$item_k->product_request_form_id][$item_k->product_id]) ?
//                        $list_current_delivery_amount_product_request_form[$item_k->product_request_form_id][$item_k->product_id] :
//                        0;
//            }
//        }
//
//        $active_inventory_list = []; // موجودی فعال کالا ها
//        // محاسبه حداکثر مقدار قابل درخواست مجوز
//        foreach ($product_request_form_items as $product_request_form_item) {
//
//            // موجودی فعال: موجودی کل + موجودی در راه - (کل مجوز ها - ( در حال تحویل + تحویل شده))
//            $active_inventory =
//                // مقدار موجودی - مقدار مجوز صادر شده
//                (
//                $does_sales_set_permission_for_inventory_on_the_way ?
//                    $packing_form_on_the_way_by_order_list[$product_request_form_item->order_list_id]["allowed"] + $inventory_list[$product_request_form_item->product_id]
//                    :
//
//                    ( // آیا کل موجودی را در نظر بگیرد یا فقط موجودی بسته بندی های مجاز را
//                    $method_of_calculating_active_inventory_on_sales == 0 ?
//                        $inventory_list[$product_request_form_item->product_id]
//                        :
//                        $packing_form_inventory_by_order_list[$product_request_form_item->order_list_id]
//                    )
//
//                ) - (
//                    // موجودی فعال: موجودی کل + موجودی در راه - (کل مجوز ها - ( در حال تحویل + تحویل شده))
//
//                    $customer_amount_request[$product_request_form_item->product_id] + // مقدار درخواست های خود مشتری
//                    $all_product_request_remaining_list[$product_request_form_item->product_id] + // مقدار درخواست سایر مشتریان
//                    -
//                    (
//                        $customer_amount_sent[$product_request_form_item->product_id] + // مقدار تحویل شده همه مشتریان
//
//                        (isset($list_current_delivery_amount_all[$product_request_form_item->product_id]) ? $list_current_delivery_amount_all[$product_request_form_item->product_id] : 0)
//                    )
//                );
//
//            $active_inventory_list[$product_request_form_item->product_id] = $active_inventory;
//            $permission_amount[$product_request_form_item->order_list_id] =
//                min(
//                    $product_request_form_item->order_amount - $product_request_form_item->amount_request, // مقدار درخواست
//
//
//                    $active_inventory
//
//                );
//
//            if (!isset($order_ids_count[$product_request_form_item->order_id])) {
//                $order_ids_count[$product_request_form_item->order_id] = 0;
//            }
//            if ($permission_amount[$product_request_form_item->order_list_id] > 0) {
//                $order_ids_count[$product_request_form_item->order_id]++;
//            }
//
//        }
////
////        return $order_list_ids;;
//
//        return [
//            "result" => true,
//            "product_request_form_items" => $product_request_form_items,
//            "permission_amount" => $permission_amount,
//            "inventory_list" => $inventory_list,
//            "packing_form_inventory_by_order_list" => $packing_form_inventory_by_order_list,
//            "order_list_packing_type_cation_list" => $order_list_packing_type_cation_list,
//            "order_list_packing_type_list" => $order_list_packing_type_list,
//            "order_list_product_request_forms" => $order_list_product_request_forms,
//            "list_current_delivery_amount" => $list_current_delivery_amount,
//            "list_current_delivery_amount_order_list" => $list_current_delivery_amount_order_list,
//            "packing_form_on_the_way_by_order_list" => $packing_form_on_the_way_by_order_list,
//            "packing_form_on_the_way_by_product" => $packing_form_on_the_way_by_product,
//            "all_order_list" => $all_order_list,
//            "all_product_request_remaining_list" => $all_product_request_remaining_list,
//            "order_ids_count" => $order_ids_count,
//            "active_inventory_list" => $active_inventory_list,
//            "other_customer_order_list" => $other_customer_order_list,
//            "customer_amount_request" => $customer_amount_request,
//            "customer_amount_sent" => $customer_amount_sent,
//
//        ];
//    }
//
//    public static function GetOtherCustomerPermission($type, $order, $product_ids = [], $product_request_form_ids = [])
//    {
//        switch ($type) {
//            case 1:
//                return ProductRequestForm::join("product_request_form_item", "product_request_forms.id", "=", "product_request_form_id")->
//                whereIn("product_request_forms.status_id", self::$product_request_forms_status_list)->
//                whereIn("product_id", $product_ids)->
//                where("applicant_id", "!=", $order->customer_id)->
//                where("applicant_type_id", 30)->
//                selectRaw("sum(amount_remaining) as amount, product_id")->
//                groupBy("product_id")->pluck("amount", "product_id");
//                break;
//            case 2:
//                $list["list"] = ProductRequestForm::join("product_request_form_item", "product_request_forms.id", "=", "product_request_form_id")->
//                whereIn("product_request_forms.status_id", self::$product_request_forms_status_list)->
//                where("product_id", $product_ids[0])->
//                where("applicant_id", "!=", $order->customer_id)->
//                where("applicant_type_id", 30)->
//                selectRaw("product_request_forms.code as prf_code,product_request_forms.id as prf_id, product_request_form_item.id as prf_item_id, order_id , amount_request, amount_sent,amount_remaining, applicant_id")->
//                with("order", "order.customer")->
//                paginate(50);
//
//                $list["customers"] = Customer::pluck("caption", "id")->toArray();
//
//                return $list;
//                break;
//
//            case 3: // لیست درخواست های
//                $list["list"] = ProductRequestForm::join("product_request_form_item", "product_request_forms.id", "=", "product_request_form_id")->
////                whereIn("product_request_forms.status_id", self::$product_request_forms_status_list)->
//                where("product_id", $product_ids[0])->
//                whereIn("product_request_forms.id", $product_request_form_ids)->
//                selectRaw("product_request_forms.code as prf_code,product_request_forms.id as prf_id, product_request_form_item.id as prf_item_id, order_id , amount_request, amount_sent,amount_remaining, applicant_id")->
//                with("order", "order.customer")->
//                paginate(50);
//
//
//                return $list;
//                break;
//        }
//
//        return null;
//
//    }
//
//    public static function GetCurrentDelivery($product_ids = [], $product_request_form_ids = [])
//    {
//
//        // کل درحال تحویل ها
//// وضعیت هایی که در آن برگ خروج کشیده شده است.
//        $status_were_transaction_not_ok = ProductRequestForm::Get_CurrentExistFromForDashboard(30);
//        // $status_were_transaction_not_ok = [500000514, 500000515, 500000520, 500000525, 500000530];
//
//
//        return $list_query = ProductRequestFormForm::join("forms", "forms.id", "product_request_form_form.form_id")->
//        join("form_item", "form_item.form_id", "product_request_form_form.form_id")->
//        whereIn("forms.status_id", $status_were_transaction_not_ok)->
//
//        when(count($product_ids) > 0, function ($query) use ($product_ids) {
//            return $query->whereIn("form_item.product_id", $product_ids);
//        })->
//        when(count($product_request_form_ids) > 0, function ($query) use ($product_request_form_ids) {
//            return $query->whereIn("product_request_form_id", $product_request_form_ids);
//        })->
//
//        selectRaw("product_id,product_request_form_id,  amount")->
//        //   groupBy("product_request_form_id", "product_id")->
//        get();
//
////        $end_list = [[]];
////        foreach ($list_query as $item) {
////            if (!isset($end_list[$item->product_id][$item->product_request_form_id])) {
////                $end_list[$item->product_id . "_" . $item->product_request_form_id] = $item;
////            } else {
////                $end_list[$item->product_id . "_" . $item->product_request_form_id]->amount = $item->amount;
////            }
////        }
////        return $end_list;
//    }
//
//
//    public static function GetCurrentDeliveryAll($product_ids = [], $product_request_form_ids = [])
//    {
//
//        // کل درحال تحویل ها
//// وضعیت هایی که در آن برگ خروج کشیده شده است.
//        $status_were_transaction_not_ok = ProductRequestForm::Get_CurrentExistFromForDashboard(30);
//        // $status_were_transaction_not_ok = [500000514, 500000515, 500000520, 500000525, 500000530];
//
//        $form_ids = Form::whereIn("status_id", $status_were_transaction_not_ok)->pluck("id")->toArray();
//
//        $product_ids = FormItem::whereIn("form_id", $form_ids)->
//        selectRaw("product_id, sum(amount) as amount")->
//        pluck("amount", "product_id")->toArray();
//
//        return $product_ids;
//
//    }
//
//    public static function GetProductDataByProduct(Order $order, $order_list_ids = [])
//    {
//        $product_request_form_items = OrderList::
//        join("orders", "orders.id", "order_list.order_id")->
//        leftJoin("product_request_form_item", 'product_request_form_item.order_list_id', 'order_list.id')->
//        when(count($order_list_ids) > 0, function ($query) use ($order_list_ids) {
//            $query->whereIn("order_list.id", $order_list_ids);
//        })->
//        where("orders.customer_id", $order->customer_id)->
//        whereNull("from_order_id")-> // ردیف سفارش اصلی باشد.
//        whereIn("orders.status_id", [35030, 35040, 35090])->
//        selectRaw(
//            "orders.id as order_id  ,order_list.id as order_list_id,product_request_form_item.product_id,
//            orders.code as order_code,orders.series as order_series,
//            sum(product_request_form_item.amount_remaining) as amount_remaining,sum(product_request_form_item.amount_sent) as amount_sent,sum(product_request_form_item.amount_request) as amount_request, order_list.amount as order_amount
//            ")->
//        groupBy("product_request_form_item.product_id")->
//        with("product")->
//        orderBy("amount_remaining", "desc")->
//        orderBy("order_list.product_id", "desc")->
//        orderBy("orders.id", "desc")->
//        paginate(max(count($order_list_ids), 30));
//
//        if (count($product_request_form_items) == 0) {
//            return [
//                "result" => false,
//                "error" => "هنوز درخواست کالا از انبار برای سفارش های مشتری ایجاد نشده است ویا تمامی سفارش ها ارسال شده اند."
//            ];
//        }
//        $product_ids = [];
//        $permission_amount = []; // مقدار مجوز حهت خروج
//        $order_list_product = [];
//        foreach ($product_request_form_items as $product_request_form_item) {
//            $product_ids[] = $product_request_form_item->product_id;
//            $order_list_product  [$product_request_form_item->order_list_id] = $product_request_form_item->product_id;
//        }
//
//        $inventory_list = WarehouseProduct::getProductInventoryList($product_ids);
//
//        return [
//            "result" => true,
//            "product_request_form_items" => $product_request_form_items,
//            "permission_amount" => $permission_amount,
//            "inventory_list" => $inventory_list,
//        ];
//    }
//
//
//    public function address_edit(Order $order, Address $address)
//    {
//        $result = self::check_permission($order);
//        if (!$result["result"]) {
//            return back()->withErrors($result["message"]);
//        }
//        $address_exist =
//            Order::
//            where("customer_id", $order->customer_id)->
//            where("address_id", $address->id)->
//            first();
//
//        if (!$address_exist) {
//            return back()->withErrors("مجوز تغییر در آدرس مورد نظر برای شما وجود ندارد.");
//        }
//        $province_option = Option::get("province", $address->province_id ?? 0);
//        $country_option = Option::get("country", $address->country_id ?? 112);
//
//
//        return view($this->view_path . "address_edit", compact("order", "address", "province_option", 'country_option'));
//
//    }
//
//    public function address_edit_submit(Request $request, Order $order, Address $address)
//    {
//        $result = self::check_permission($order);;
//        if (!$result["result"]) {
//            return back()->withErrors($result["message"]);
//        }
//        $address_exist =
//            Order::
//            where("customer_id", $order->customer_id)->
//            where("address_id", $address->id)->
//            first();
//
//        if (!$address_exist) {
//            return back()->withErrors("مجوز تغییر در آدرس مورد نظر برای شما وجود ندارد.");
//        }
//        $address->update($request->all());
//
//        return redirect()->route($this->route_path . "show_permission", $order);
//    }
//
//
//    public static function check_permission(Order $order)
//    {
//        return [
//            "result" => true
//        ];
//    }
//
//
//}

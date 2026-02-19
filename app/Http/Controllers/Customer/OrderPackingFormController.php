<?php

namespace App\Http\Controllers\Customer;

use App\Events\Order\OrderLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Sales\CustomerController;
use App\Models\Accounting\Tariff\ProductTariff;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerPaymentMethod;
use App\Models\Form\Form;
use App\Models\Form\FormGeneralItem;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKind\GoodsKindDisplayProperty;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Order\Order;
use App\Models\Order\OrderConsumedProduct;
use App\Models\Order\OrderFactor;
use App\Models\Order\OrderList;
use App\Models\Order\OrderListPackingType;
use App\Models\Order\OrderPackingForm;
use App\Models\Order\OrderPaymentMethod;
use App\Models\Utility\Address\Address;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderPackingFormController extends Controller
{
    //customer/group/buy

    var $route_path = "customer_group.buy.order_packing_form.";
    var $view_path = "customer.group.buy.order_packing_form.";

    // دریافت بسته بندی ها برای مواد اولیه کارمزدی
    public function index(Order $order, $orderConsumedProduct_id)
    {

        if (!$order->customer->get_packing_form_details) {
            return $this->general($order, $orderConsumedProduct_id);
        }
        $orderConsumedProduct = OrderConsumedProduct::find($orderConsumedProduct_id);
        $packing_type_option = Option::get("packing_type", 0, $orderConsumedProduct->material->goods_kind_id ?? 0);
        $degree_option = Option::get("degree", 0, $orderConsumedProduct->material->goods_kind_id ?? 0);

        $has_sub_packing_type = PackingType::HasSubPackingType([$orderConsumedProduct->material_id]);
        return view($this->view_path . "index", compact("order", "orderConsumedProduct", "degree_option", "packing_type_option", "has_sub_packing_type"));

    }

    public function submit_new_packing_form(Request $request, Order $order, OrderConsumedProduct $orderConsumedProduct)
    {
        if (!$order->customer->get_packing_form_details) {

            $lot_number = LotNumber::firstOrCreate([
                "product_id" => $orderConsumedProduct->material_id,
                "code" => $request->lot_number_code
            ], [
                "user_id" => Auth::id(),
            ]);
            $product_warehouse_storage_type = Product\ProductWarehouseStorageType::where("product_id", $orderConsumedProduct->material_id)->
            first();
            if (!$product_warehouse_storage_type) {
                return back()->withErrors("نوع انبارش " . $orderConsumedProduct->material->caption . " مشخص نشده است،لطفا با واحد اطلاعات پایه تماس بگیرید.");
            }
            $line_product_station = LineProductStation::where([
                "product_id" => $orderConsumedProduct->material_id,
                "customer_id" => $order->customer_id
            ])->first();
            if (!$line_product_station) {
                return back()->withErrors("با توجه به اینکه مسیر محصول کالا (" . $orderConsumedProduct->material->capiton . ") برای " . $order->customer->caption . " تعریف نشده است، امکان ثبت فرم وجود ندارد، لطفا با واحد اطلاعات پایه تماس گرفته و درخواست ثبت مسیر محصول برای کالا را اعلام فرمایید.");
            }
            if ($line_product_station && !$line_product_station->applicant_warehouse_id) {
                return back()->withErrors("با توجه به اینکه انبار تحویل کالا در مسیر محصول  (" . $orderConsumedProduct->material->capiton . ") برای " . $order->customer->caption . " مشخص نشده است، امکان ثبت فرم وجود ندارد، لطفا با واحد اطلاعات پایه تماس گرفته و درخواست ثبت مسیر محصول برای کالا را اعلام فرمایید.");
            }
            if ($request->packing_form_number <= 0) {
                return back()->withErrors("لطفا تعداد بسته بندی را وارد نمایید.");
            }
            if ($orderConsumedProduct->form_general_item) {
                $orderConsumedProduct->form_general_item->delete();
            }

            $form_general_item = FormGeneralItem::create([

                "production_form_item_id" => null,
                "order_id" => $order->id,
                "product_id" => $orderConsumedProduct->material_id,
                "degree_id" => $request->degree_id,
                "lot_number_id" => $lot_number->id,
                "packing_type_id" => $request->packing_type_id,
                "warehouse_storage_type_id" => $product_warehouse_storage_type->warehouse_storage_type_id,
                "packing_form_number" => $request->packing_form_number ?? 0,
                "amount" => $request->amount,
                "sub_amount" => $request->sub_amount,
                "status_id" => 5002005, // عدم نیاز به تفکیک ( ذخیره کالای مصرفی سفارش)
                "price_registration_status_id" => 5105400 // عدم نیاز به ثبت
            ]);

            $orderConsumedProduct->form_general_item_id = $form_general_item->id;
            $orderConsumedProduct->save();


            return back()->with(["یک ردیف فرم ورود به انبار با موفقیت ایجاد شد، "]);

        } else {
            OrderPackingForm::create([
                "customer_id" => $order->customer_id,
                "order_id" => $order->id,
                "order_list_id" => $orderConsumedProduct->order_list_id,
                "product_id" => $orderConsumedProduct->product_id,
                "material_id" => $orderConsumedProduct->material_id,
                "packing_form_code" => $request->packing_form_code,
                "degree_id" => $request->degree_id,
                "lot_number_code" => $request->lot_number_code,
                "packing_type_id" => $request->packing_type_id,
                "sub_packing_form_number" => $request->sub_packing_form_number ?? 0,
                "amount" => $request->amount
            ]);
        }
        return back()->with(["success" => "یک بسته بندی با موفقیت ثبت گردید."]);
    }

    public function delete_order_packing_form(Order $order, OrderPackingForm $orderPackingForm)
    {
        if ($order->id != $orderPackingForm->order_id) {
            return back()->withErrors("اطلاعات بسته بندی نامعتبر است، لطفا مجدد تلاش کنید.");
        }
        $orderPackingForm->delete();
        return back()->with(["success" => "اطلاعات با موفقیت حذف گردید"]);
    }

    /** General **/
    public function general(Order $order, $orderConsumedProduct_id)
    {
        $orderConsumedProduct = OrderConsumedProduct::find($orderConsumedProduct_id);
        // اگر چند ماده اولیه باید ارسال شود.
        $next_order_consumed = OrderConsumedProduct::where("order_id", $order->id)->
        where("material_id","!=",$orderConsumedProduct->material_id)->where("id", ">", $orderConsumedProduct_id)->
        orderBy("id")->first();


        $packing_type_option = Option::get("packing_type", 0, $orderConsumedProduct->material->goods_kind_id ?? 0);
        $degree_option = Option::get("degree", 0, $orderConsumedProduct->material->goods_kind_id ?? 0);


        return view($this->view_path . "general", compact("order", "orderConsumedProduct", "next_order_consumed", "degree_option", "packing_type_option"));


    }

    public function delete_general_item(Order $order, OrderConsumedProduct $orderConsumedProduct)
    {
        if ($orderConsumedProduct->form_general_item) {
            $orderConsumedProduct->form_general_item->delete();
            $orderConsumedProduct->form_general_item_id = null;
            $orderConsumedProduct->save();
        }
        return back()->with(["success" => "حذف فرم تجمیعی با موفقیت انجام شد."]);
    }
}
<?php

namespace App\Http\Controllers\Sales;

use App\Events\Utility\TransportLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\Form\FormGeneralItem;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Supplier\Supplier;
use App\Models\Utility\Car\Car;
use App\Models\Utility\Option;
use App\Models\Utility\QueueOfLargeOperation;
use App\Models\Utility\Setting;
use App\Models\Utility\Transport\Transport;
use App\Models\Utility\Transport\TransportForm;
use App\Models\Warehouse\WarehouseStorageType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteGroup;
use Illuminate\Support\Facades\Auth;

class LoadingImplementationController extends Controller
{
    //
    var $view_path = "sales.loading_implementation.";
    var $route_path = "sales.loading_implementation.";
    var $dashboard_path = "sales.loading_implementation.";

    public function index($customer_id = 0)
    {
        $request = null;
        if (session("request_data_index")) {
            $request = session("request_data_index");
        }


        $product_id = isset($request["product_id"]) ? $request["product_id"] : 0;
        $product = Product::find($product_id);
        $gods_kind_id = $product->goods_kind_id ?? 0;
        $product_option = Option::get("product_customer_line_product_station", isset($request["product_id"]) ? $request["product_id"] : 0, $customer_id);


        $warehouse_storage_type_option = Option::get("warehouse_storage_type_option", isset($request["warehouse_storage_type_id"]) ? $request["warehouse_storage_type_id"] : 0, $product_id);

        $packing_type_option = Option::get("packing_type_product", isset($request["packing_type_id"]) ? $request["packing_type_id"] : 0, $product_id);

        $degree_option = Option::get("degree", isset($request["degree_id"]) ? $request["degree_id"] : 0, $gods_kind_id);
        $customer_option = Option::get("customer", $customer_id);

        if (isset($customer_id)) {
            $customer = Customer::find($customer_id);
        }

        $lot_number_code = isset($request["lot_number_code"]) ? $request["lot_number_code"] : "";
        $packing_form_number = isset($request["packing_form_number"]) ? $request["packing_form_number"] : "";
        $amount = isset($request["amount"]) ? $request["amount"] : "";
        $sub_amount = isset($request["sub_amount"]) ? $request["sub_amount"] : "";
        $sale_info_complete_later = isset($request["sale_info_complete_later"]) ? $request["sale_info_complete_later"] : "";

        $create_new_lot_number = isset($request["create_new_lot_number"]) ? $request["create_new_lot_number"] : null;

        $list_goods_kind_product = [];
        $list_sub_unit_product = [];
        $list_unit_product = [];
        foreach (Product::where("supply_type_id", 4)->get() as $item) {
            $list_goods_kind_product[$item->id] = $item->goods_kind_id;
            $list_sub_unit_product[$item->id] = $item->sub_unit->caption ?? "";
            $list_unit_product[$item->id] = $item->unit->caption ?? "";
        }

        return view($this->view_path . "index", compact(
            "degree_option", "product_option", "packing_type_option", "customer_option", "list_unit_product", "list_sub_unit_product",
            "list_goods_kind_product", "packing_form_number", "lot_number_code",
            "create_new_lot_number", "warehouse_storage_type_option", "sub_amount", "amount", "customer"
        ));


    }

    public function submit(Request $request)
    {


        $product = Product::find($request->product_id);
        $packing_form_number = $request->packing_form_number;
        $warehouse_storage_type_id = $request->warehouse_storage_type_id;
        $lot_number_code = $request->lot_number_code;
        $amount = $request->amount;
        $sub_amount = $request->sub_amount ?? null;
        $lot_number = LotNumber::where(["product_id" => $product->id, "code" => $lot_number_code])->first();
        if (!$lot_number) {
            $lot_number = LotNumber::create([
                "product_id" => $product->id,
                "code" => $lot_number_code,
                "user_id" => Auth::id()
            ]);

        }
        if (!$lot_number && $request->create_new_lot_number != $lot_number_code) {
            $request["create_new_lot_number"] = $lot_number_code;
        }


        if (!$lot_number) {
            return back();
        }

        if ($product->sub_unit && !$sub_amount) {
            return back()->withErrors("لطفا مقدار فرعی را تکمیل نمایید.");
        }
        if ($amount <= 0) {
            return back()->withErrors("مقدار کالا نامعتبر است.");
        }
        if ($warehouse_storage_type_id == 2 && $packing_form_number <= 0) {
            return back()->withErrors("تعداد بسته بندی نامعتبر است.");
        }

        $product_id = $request->product_id;
        $request_data = [];

        if (session("request_data")) {

            $request_data = session("request_data");
        }

        $request_data[$product_id] = $request->all();

        session([
            "request_data" => $request_data
        ]);

        if ($request->add_new_row == 0) {
            return redirect()->route($this->route_path . "confirm");
        } else {
            return redirect()->route($this->route_path . "index", $request->customer_id);
        }


    }

    public function confirm()
    {

        if (!session("request_data")) {
            return redirect()->route($this->route_path . "index")->withErrors("نشست شما به پایان رسیده است، لطفا یک بار دیگر تلاش کنید.");
        }

        $request_data = session("request_data");
        if (!is_array($request_data)) {
            session("request_data", null);
            return redirect()->route($this->route_path . "index",)->withErrors("اطلاعات ثبت بار به درستی ذخیره نشده است، لطفا یکبار دیگر تلاش کنید.");
        }

        foreach ($request_data as $product_id => $request_item) {
            // return $product_id;
            $products[$product_id] = Product::find($request_item["product_id"]);
            $warehouse_storage_types[$product_id] = WarehouseStorageType::find($request_item["warehouse_storage_type_id"]);
            $packing_types[$product_id] = PackingType::find($request_item["packing_type_id"]);
            $degrees[$product_id] = Degree::find($request_item["degree_id"]);
            $customers[$product_id] = Customer::find($request_item["customer_id"]);
            $lot_number_codes[$product_id] = $request_item["lot_number_code"];
            $packing_form_numbers[$product_id] = $request_item["packing_form_number"];
            $amounts[$product_id] = $request_item["amount"];
            $sub_amounts[$product_id] = $request_item["sub_amount"];

            if (!$warehouse_storage_types) {
                return back()->withErrors("لطفا نوع انبارش کالا را برای کالای " . $products[product_id]->caption . " مشخص نمایید.");
            }
            // return $warehouse_storage_types;

            if ($warehouse_storage_types[$product_id]->id != 2) {
                $packing_types = null;


            }

        }

        $request = session("request_data");

        // $amount[$product_id] = $request["amount"];
        //

        // دریافت اطلاعات بارگیری
        $car_type_option = Option::get("car_types");
        $car_list = Car::get();
        $car_option["items"] = [];
        foreach ($car_list as $car) {
            $car_option["items"][] = [
                "id" => $car->id,
                "value" => $car->id,
                "text" => $car->driver_firstname . " " .
                    $car->driver_lastname . "-" .
                    $car->car_type->caption . "-" .
                    $car->car_plaque
            ];
        }

        return view($this->view_path . "confirm", compact("products", "packing_types", "degrees", "customers",
            "lot_number_codes", "packing_form_numbers", "amounts", "sub_amounts", "car_list", "car_option", "car_type_option",
            'warehouse_storage_types', "request_data", "products"
        ));

    }

    public function submit_confirm(Request $request)
    {
        if (!session("request_data")) {
            return redirect()->route($this->route_path . "index")->withErrors("نشست شما به پایان رسیده است، لطفا یک بار دیگر تلاش کنید.");
        }

        $request_data = session("request_data");

        //چک کردن مشتری
        $customer_id = -1;
        foreach ($request_data as $product_id => $request_item) {
            if ($customer_id != -1 && $customer_id != $request_item["customer_id"] + 0) {
                return back()->withErrors("با توجه به اینکه ثبت ارسال بار در هر بار فقط باید برای یک مشتری باشد، لطفا یک مشتری را انتخاب نمایید و دیگر ردیف ها را حذف نمایید");
           }
            $customer_id = $request_item["customer_id"] + 0;
        }

        foreach ($request_data as $product_id => $request_item) {
            $products[$product_id] = Product::find($request_item["product_id"]);
            $warehouse_storage_types[$product_id] = WarehouseStorageType::find($request_item["warehouse_storage_type_id"]);
            $packing_types[$product_id] = PackingType::find($request_item["packing_type_id"]);
            $degrees[$product_id] = Degree::find($request_item["degree_id"]);
            $customers[$product_id] = Customer::find($request_item["customer_id"]);
            $lot_number_codes[$product_id] = $request_item["lot_number_code"];
            $packing_form_numbers[$product_id] = $request_item["packing_form_number"];
            $amounts[$product_id] = $request_item["amount"];
            $sub_amounts[$product_id] = $request_item["sub_amount"];

            $lot_numbers[$product_id] = LotNumber::where(["product_id" => $products[$product_id]->id, "code" => $lot_number_codes[$product_id]])->first();
            $request_data[$product_id];
            if (!$lot_numbers[$product_id]) {
                return redirect()->route($this->route_path . "index")->withErrors("کد لات در سامانه تعریف نشده است، لطفا یک بار دیگر تلاش کنید.");
            }

            $car_id = null;
            if ($customers[$product_id]->input_form_loading_require) {
                $car_id = $request->car_id;
                if ($car_id) {
                    $car = Car::find($car_id);
                } else {
                    $request["user_id"] = Auth::id();
                    $car = Car::create($request->all());
                    $car_id = $car->id;
                }
            }

            if ($customers[$product_id]->input_form_loading_require && !$car_id) {
                return redirect()->route($this->route_path . "index")->withErrors("اطلاعات بارگیری ثبت نشده است، لطفا یک بار دیگر تلاش کنید.");

            }

        }

        // پیدا کردن انبار تحویل مواد اولیه با توجه به مسیر محصول مشتری
        foreach ($request_data as $product_id => $request_item) {
            $line_product_station = LineProductStation::where([
                "product_id" => $request_item["product_id"],
                "customer_id" => $request_item["customer_id"]
            ])->first();
            if (!$line_product_station) {
                $product = Product::find($request_item["product_id"]);
                $customer = Customer::find($request_item["customer_id"]);
                return back()->withErrors("با توجه به اینکه مسیر محصول کالا (" . $product->caption . ") برای " . $customer->caption . " تعریف نشده است، امکان ثبت فرم وجود ندارد، لطفا با واحد اطلاعات پایه تماس گرفته و درخواست ثبت مسیر محصول برای کالا را اعلام فرمایید.");
            }
            if ($line_product_station && !$line_product_station->applicant_warehouse_id) {
                $product = Product::find($request_item["product_id"]);
                $customer = Customer::find($request_item["customer_id"]);
                return back()->withErrors("با توجه به اینکه انبار تحویل کالا در مسیر محصول  (" . $product->caption . ") برای " . $customer->caption . " مشخص نشده است، امکان ثبت فرم وجود ندارد، لطفا با واحد اطلاعات پایه تماس گرفته و درخواست ثبت مسیر محصول برای کالا را اعلام فرمایید.");
            }
        }

        $form = Form::CreateFrom([
            "order_id" => 0,
            "order_list_id" => 0,
            "user_id" => Auth::user()->id,
            "form_type_id" => 304,
            "trans_kind" => 4,
            "warehouse_id" => $line_product_station->applicant_warehouse_id,
            "status_id" => $customers[$product_id]->nextStatusForInputForm(0, true),
            "ic" => $customers[$product_id]->getIC(),
            "applicant_type_id" => 30,
            "applicant_id" => $customers[$product_id]->id,
        ]);


        $form->getCode("DCRF");

        event(new FormLogEvent($form));

        foreach ($request_data as $product_id => $request_item) {
            //  dd($request_item);
            //return $customers;

            // return $request_item;
            FormGeneralItem::create([
                "form_id" => $form->id,
                "customer_id" => $request_item["customer_id"],
                "production_form_item_id" => null,
                "product_id" => $request_item["product_id"],
                "degree_id" => $request_item["degree_id"],
                "lot_number_id" => $lot_numbers[$product_id]->id,
                "packing_type_id" => $request_item['warehouse_storage_type_id'] == 2 ? $request_item['packing_type_id'] : null,
                "warehouse_storage_type_id" => $request_item["warehouse_storage_type_id"],
                "packing_form_number" => $request_item['packing_form_number'] ?? 0,
                "amount" => $request_item["amount"],
                "sub_amount" => $request_item["sub_amount"],
                "status_id" => 5002002, // در انتظار تفکیک

            ]);
        }


        if ($car_id) {
            $transport = Transport::create([
                "car_id" => $car_id,
                "user_id" => Auth::id(),
                "transport_type_id" => 1, // ورود
                "status_id" => 6010104,// در انتظار ورود به سازمان
            ]);
            $transport->getCode();
            event(new TransportLogEvent($transport, 6010101));
            TransportForm::create(["transport_id" => $transport->id, "form_id" => $form->id]);

        }

        session([
            "request_data" => null
        ]);

        return redirect()->route($this->route_path . "index")->with(["success" => "یک فرم تامین با موفقیت ثبت گردید."]);

    }

    public function delete($product_id)
    {
        if (!session("request_data")) {
            return redirect()->route($this->route_path . "index")->withErrors("نشست شما به پایان رسیده است، لطفا یک بار دیگر تلاش کنید.");
        }

        $request_data = session("request_data");
        if ($request_data == null) {
            return back()->withErrors("سطری برای حذف وجود ندارد");
        }
        $request_count = count($request_data);

        unset($request_data[$product_id]);
        session([
            "request_data" => $request_data
        ]);

        if ($request_count > 1) {
            return redirect()->route($this->route_path . "confirm")->with(["success" => "محصول مورد نظر با موفقیت حذف گردید."]);

        } else {
            return redirect()->route($this->route_path . "index");
        }
        session([
            "request_data" => $request_data
        ]);


    }

}


<?php

namespace App\Http\Controllers\Supplier\Admin;

use App\Events\Utility\TransportLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\Form\FormGeneralItem;
use App\Models\LineProduct\Degree;
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

class SupplierRegisterController extends Controller
{
    //
    var $view_path = "supplier.admin.supplier_register.";
    var $route_path = "supplier.admin.supplier_register.";
    var $dashboard_path = "supplier.admin.dashboard.index";

    public function index()
    {

        $request = null;
        if (session("request_data")) {
            $request = session("request_data");
        }

        // چک کردن اینکه آیا اطلاعات ناقص در مدت زمان مناسب تکمیل شده است یا خیر
        $max_time_for_supplier_to_register_factor_info =
            Setting::getIntegerValue("max_time_for_supplier_to_register_factor_info");

        $form_general_items_list = FormGeneralItem::
        join("machine_allocation", "machine_allocation.id", "machine_allocation_id")->
        whereNull("price")->
        where("machine_allocation.created_at", "<", Carbon::now()->addDay(-$max_time_for_supplier_to_register_factor_info))->
        select("machine_allocation.*")->first();
        if ($form_general_items_list) {
            return redirect()->route($this->dashboard_path)->
            withErrors("با توجه به اینکه اطلاعات مالی تخصیص شماره " . $form_general_items_list->allocation_id . " تکمیل نشده است، ثبت تامین جدید امکان پذیر نیست.");
        }


        $product_id = isset($request["product_id"]) ? $request["product_id"] : 0;
        $product = Product::find($product_id);
        $gods_kind_id = $product->goods_kind_id ?? 0;
        $product_option = Option::get("product_active", $product_id, 0, ["supply_type_ids" => [2]]);
        $warehouse_storage_type_option = Option::get("warehouse_storage_type_option", isset($request["warehouse_storage_type_id"]) ? $request["warehouse_storage_type_id"] : 0, $product_id);

        $packing_type_option = Option::get("packing_type_product", isset($request["packing_type_id"]) ? $request["packing_type_id"] : 0, $product_id);
        $degree_option = Option::get("degree", isset($request["degree_id"]) ? $request["degree_id"] : 0, $gods_kind_id);
        $supplier_option = Option::get("supplier_product", isset($request["supplier_id"]) ? $request["supplier_id"] : 0, $product_id);
        $debt_or_supply_option = Option::get("debt_or_supply", isset($request["debt_or_supply_id"]) ? $request["debt_or_supply_id"] : 1, $product_id);

        $lot_number_code = isset($request["lot_number_code"]) ? $request["lot_number_code"] : "";
        $packing_form_number = isset($request["packing_form_number"]) ? $request["packing_form_number"] : "";
        $price = isset($request["price"]) ? $request["price"] : "";
        $tax_price = isset($request["tax_price"]) ? $request["tax_price"] : "";
        $amount = isset($request["amount"]) ? $request["amount"] : "";
        $sub_amount = isset($request["sub_amount"]) ? $request["sub_amount"] : "";
        $sale_info_complete_later = isset($request["sale_info_complete_later"]) ? $request["sale_info_complete_later"] : "";

        $create_new_lot_number = isset($request["create_new_lot_number"]) ? $request["create_new_lot_number"] : null;

        $list_goods_kind_product = [];
        $list_sub_unit_product = [];
        $list_unit_product = [];
        foreach (Product::where("supply_type_id", 2)->get() as $item) {
            $list_goods_kind_product[$item->id] = $item->goods_kind_id;
            $list_sub_unit_product[$item->id] = $item->sub_unit->caption ?? "";
            $list_unit_product[$item->id] = $item->unit->caption ?? "";
        }

        return view($this->view_path . "index", compact(
            "degree_option", "product_option", "packing_type_option", "supplier_option", "list_unit_product", "list_sub_unit_product",
            "list_goods_kind_product", "tax_price", "price", "packing_form_number", "lot_number_code", "amount",
            "sub_amount", "create_new_lot_number", "sale_info_complete_later", "debt_or_supply_option", "warehouse_storage_type_option"
        ));
    }

    public function submit(Request $request)
    {


        $product = Product::find($request->product_id);
//        $packing_type = PackingType::find($request->packing_type_id);
//        $degree = Degree::find($request->degree_id);
//        $supplier = Supplier::find($request->supplier_id);
        $lot_number_code = $request->lot_number_code;
        $packing_form_number = $request->packing_form_number;
        $warehouse_storage_type_id = $request->warehouse_storage_type_id;
        $price = $request->price;
        $tax_price = $request->tax_price;
        $amount = $request->amount;
        $sub_amount = $request->sub_amount ?? null;


        $lot_number = LotNumber::where(["product_id" => $product->id, "code" => $lot_number_code])->first();

        if (!$lot_number && $request->create_new_lot_number == $lot_number_code) {
            $lot_number = LotNumber::create([
                "product_id" => $product->id,
                "code" => $lot_number_code,
                "user_id" => Auth::id()
            ]);

        }
        if (!$lot_number && $request->create_new_lot_number != $lot_number_code) {
            $request["create_new_lot_number"] = $lot_number_code;
        }
        session([
            "request_data" => $request->all()
        ]);

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

        //اگر اطلاعات خرید را بعدا تکمیل می کنند، پس نباید چک کنیم.
        if (!$request->sale_info_complete_later) {
            if ($price <= 0) {
                return back()->withErrors("مبلغ نامعتبر است.");
            }
            if ($tax_price < 0) {
                return back()->withErrors("ارزش افزوده نامعتبر است.");
            }
        }

        return redirect()->route($this->route_path . "confirm");
    }

    public function confirm()
    {

        if (!session("request_data")) {
            return redirect()->route($this->route_path . "index")->withErrors("نشست شما به پایان رسیده است، لطفا یک بار دیگر تلاش کنید.");
        }
        $request = session("request_data");
        $product = Product::find($request["product_id"]);
        $warehouse_storage_type = WarehouseStorageType::find($request["warehouse_storage_type_id"]);
        $packing_type = PackingType::find($request["packing_type_id"]);
        $degree = Degree::find($request["degree_id"]);
        $supplier = Supplier::find($request["supplier_id"]);
        $lot_number_code = $request["lot_number_code"];
        $packing_form_number = $request["packing_form_number"];
        $debt_or_supply_id = $request["debt_or_supply_id"];
        $price = null;
        $tax_price = null;

        if ($debt_or_supply_id == 2 && !$supplier->can_i_borrow_from_this_supplier) {
            return redirect()->route($this->route_path . "index")->withErrors("امکان ثبت قرض برای تامین کننده (" . $supplier->caption . ") وجود ندارد");
        }

        $sale_info_complete_later = isset($request["sale_info_complete_later"]) ? true : false;
        if (!$sale_info_complete_later) {
            $price = $request["price"];
            $tax_price = $request["tax_price"];
        }

        if (!$warehouse_storage_type) {
            return back()->withErrors("لطفا نوع انبارش کالا را مشخص نمایید.");
        }
        if ($warehouse_storage_type->id != 2) {
            $packing_type = null;
        }

        $amount = $request["amount"];
        $sub_amount = $request["sub_amount"];

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

        return view($this->view_path . "confirm", compact("product", "packing_type", "degree", "supplier",
            "lot_number_code", "packing_form_number", "sale_info_complete_later",
            "price", "tax_price", "amount", "sub_amount", "car_list", "car_option", "car_type_option", "debt_or_supply_id", 'warehouse_storage_type'
        ));
    }

    public function submit_confirm(Request $request)
    {
        if (!session("request_data")) {
            return redirect()->route($this->route_path . "index")->withErrors("نشست شما به پایان رسیده است، لطفا یک بار دیگر تلاش کنید.");
        }

        $request_session = session("request_data");
        $product = Product::find($request_session["product_id"]);
        $warehouse_storage_type = WarehouseStorageType::find($request_session["warehouse_storage_type_id"]);
        $packing_type = PackingType::find($request_session["packing_type_id"]);
        $degree = Degree::find($request_session["degree_id"]);
        $supplier = Supplier::find($request_session["supplier_id"]);
        $lot_number_code = $request_session["lot_number_code"];
        $packing_form_number = $request_session["packing_form_number"];
        $debt_or_supply_id = $request_session["debt_or_supply_id"];
        $sale_info_complete_later = isset($request_session["sale_info_complete_later"]) ? true : false;
        $price = null;
        $tax_price = null;
        $tans_kind_id = $this->getTransKind($debt_or_supply_id, $supplier);

        if (!$sale_info_complete_later) {
            $price = $request_session["price"];
            $tax_price = $request_session["tax_price"];
        }
        $amount = $request_session["amount"];
        $sub_amount = $request_session["sub_amount"];
        

        $lot_number = LotNumber::where(["product_id" => $product->id, "code" => $lot_number_code])->first();
        if (!$lot_number && $request_session->create_new_lot_number == 0) {
            return redirect()->route($this->route_path . "index")->withErrors("کد لات در سامانه تعریف نشده است، لطفا یک بار دیگر تلاش کنید.");
        }

        $car_id = null;
        if ($supplier->input_form_loading_require) {
            $car_id = $request->car_id;
            if ($car_id) {
                $car = Car::find($car_id);
            } else {
                $request["user_id"] = Auth::id();
                $car = Car::create($request->all());
                $car_id = $car->id;
            }
        }

        if ($supplier->input_form_loading_require && !$car_id) {
            return redirect()->route($this->route_path . "index")->withErrors("اطلاعات بارگیری ثبت نشده است، لطفا یک بار دیگر تلاش کنید.");

        }

        $allocation = Allocation::create([
            "supplier_id" => $supplier->id,
            "status_id" => 5310020,// خاتمه یافته

        ]);

        $machine_allocation = MachineAllocation::create([
            "production_id" => null,
            "supplier_id" => $supplier->id,
            "user_id" => Auth::id(),
            "status_id" => 5310020,// خاتمه یافته
            "product_id" => $product->id,
            "allocation_id" => $allocation->id,
            "allocation_amount" => $amount,
        ]);

        $form = Form::CreateFrom([
            "order_id" => 0,
            "order_list_id" => 0,
            "allocation_id" => $machine_allocation->allocation_id,
            "user_id" => Auth::user()->id,
            "form_type_id" => 304,
            "trans_kind" => $tans_kind_id,
            "warehouse_id" => $degree->warehouse_id,
            "status_id" => $supplier->nextStatusForInputForm(0, true),
            "ic" => $supplier->getIC()
        ]);

        $form->getCode("DCRF");

        event(new FormLogEvent($form));

        FormGeneralItem::create([
            "form_id" => $form->id,
            "production_form_item_id" => null,
            "machine_allocation_id" => $machine_allocation->id,
            "product_id" => $machine_allocation->product_id,
            "degree_id" => $degree->id,
            "lot_number_id" => $lot_number->id,
            "packing_type_id" => $warehouse_storage_type->id == 2 ? $packing_type->id : null,
            "warehouse_storage_type_id" => $warehouse_storage_type->id,
            "packing_form_number" => $packing_form_number ?? 0,
            "amount" => $amount,
            "sub_amount" => $sub_amount,
            "price" => !$sale_info_complete_later ? $price : null,
            "tax_price" => !$sale_info_complete_later ? $tax_price : null,
            "total_price_with_tax" => !$sale_info_complete_later ? $price + $tax_price : null,
            "status_id" => 5002002, // در انتظار تفکیک
            "price_registration_status_id" => $sale_info_complete_later ? 5105200 : 5105300
        ]);


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
//            $form->status_id=$supplier->nextStatusForInputForm( $form->status_id, true );
//            $form->save();
//            event( new FormLogEvent( $form ) );
        }

        if ($debt_or_supply_id == 2) {
            // اگر فرایند قرض است، باید یک فرم درخواست خروج کالا هم برای مورد ثبت شود.
            $other["user_id"] = Auth::user()->id;
            $other["form_id"] = $form->id;

            Product\ProductRequest\ProductRequestForm::newRequest(
                $allocation, $supplier->id,
                60, 1, $other,
                Carbon::now()
            );

        }

        session([
            "request_data" => null
        ]);

        return redirect()->route($this->route_path . "index")->with(["success" => "یک فرم تامین با موفقیت ثبت گردید."]);

    }

    public function complete_financial_info(FormGeneralItem $form_general_item)
    {
        if (isset($form_general_item->price)) {
            return back()->withErrors("اطلاعات مالی این فرم قبلا ثبت شده است و امکان ثبت مجدد آن وجود ندارد.");
        }
        $allocation = $form_general_item->machine_allocation->allocation ?? null;
        if (!$allocation) {
            return back()->withErrors("اطلاعات تخصیص مربوط به فرم یافت نشد.");
        }
        return view($this->view_path . "complete_financial_info", compact("form_general_item", "allocation"));

    }

    public function submit_complete_financial_info(Request $request, FormGeneralItem $form_general_item)
    {
        if (isset($form_general_item->price)) {
            return back()->withErrors("اطلاعات مالی این فرم قبلا ثبت شده است و امکان ثبت مجدد آن وجود ندارد.");
        }
        $allocation = $form_general_item->machine_allocation->allocation ?? null;
        if (!$allocation) {
            return back()->withErrors("اطلاعات تخصیص مربوط به فرم یافت نشد.");
        }

        if ($request->price <= 0) {
            return back()->withErrors("مبلغ نامعتبر است.");
        }
        if ($request->tax_price < 0) {
            return back()->withErrors("ارزش افزوده نامعتبر است.");
        }


//        $machine_allocation_actual_cost = null;
//        if (count($machine_allocation_actual_cost_weight_list) == 1) {
//            $machine_allocation_actual_cost = $machine_allocation_actual_cost_weight_list[0];
//        }

        $form_general_item->update([
            "price" => $request->price,
            "tax_price" => $request->tax_price,
            "total_price_with_tax" => $request->price + $request->tax_price,
            "price_registration_status_id" => 5105300 // اطلاعات مالی ثبت شده
        ]);


        QueueOfLargeOperation::AddToQueue([
            "form_id" => $form_general_item->form->id,
            "allocation_id" => $form_general_item->machine_allocation->allocation_id,
            "type" => "supplier_register_cost"
        ], 500);

        return redirect()->route("supplier.admin.dashboard.view", $form_general_item->machine_allocation)->
        with(["success" => "اطلاعات مالی با موفقیت ثبت گردید"]);

    }

    public function getTransKind($debt_or_supply_id, $supplier)
    {
        switch ($debt_or_supply_id) {
            case 1: // خرید
                return $supplier->supplier_type->trans_kind_id; // خرید
                break;
            case 2: //
                return 4; // دریافت امانی
        }
        1 / 0;

    }
}

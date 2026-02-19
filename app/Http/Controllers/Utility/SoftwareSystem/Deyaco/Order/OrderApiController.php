<?php

namespace App\Http\Controllers\Utility\SoftwareSystem\Deyaco\Order;

use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Contractor\Panel\CoordinationForSendingController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\BuyController;
use App\Http\Controllers\LineProductStation\Product\ProductCreation\NewFormController;
use App\Http\Controllers\Production\PublicModule\RegisterProductionController;
use App\Http\Controllers\Sales\CustomerController;
use App\Http\Controllers\Sales\RejectProductController;
use App\Http\Controllers\Warehouse\Out\ExitFormController;
use App\Models\Accounting\Tariff\ProductTariff;
use App\Models\Contractor\ContractorAllocation;
use App\Models\Contractor\MachineAllocationPackingForm;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerPaymentMethod;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Order\Order;
use App\Models\Order\OrderConsumedProduct;
use App\Models\Order\OrderFactor;
use App\Models\Order\OrderPackingForm;
use App\Models\Order\OrderPaymentMethod;
use App\Models\Production\Production;
use App\Models\User;
use App\Models\Utility\Address\Address;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Setting;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class OrderApiController extends Controller
{
    /**
     * @param Request $request
     * @return array
     * ثبت سفارش برای پیمانکار و تخصیص پیمانکار
     */
    public static $user_id = 2;

    public function call_order_registration(Request $request)
    {

//                $product_data=[
//                    [
//                            "product_code" => $product_code_in_contractor_system,
//                            "packing_type_ids" => $production->packing_types()->pluck("packing_type_id")->toArray(),
//                            "degree_codes" => $degree_codes,
//                            "production_id_in_source" => $production->id,
//                            "product_id_in_source" => $production->product_id,
//                            "amount" => $allocation_amount,
//                            "message" => $message
//                        ]
//                ];

        $username = $request->username;
        $delivery_datetime = $request->delivery_datetime;
        $product_data = json_decode($request->product_data, 1);
        $address = json_decode($request->address, 1);
        $software_name = Setting::getStringValue("software_name");
//        return [
//            "result" => false,
//            "error" =>$request->product_data
//        ];
        if (!$product_data) {
            return [
                "result" => false,
                "error" => " اطلاعات کالای برای  ثبت سفارش نامعتبر است."
            ];
        }
        $user = User::where("email", $username)->first();
        if (!$user) {
            return [
                "result" => false,
                "error" => "نام کاربری $username در سامانه پیمانکار وجود ندارد. "
            ];
        }
        $customer = Customer::where("user_id", $user->id)->first();
        if (!$customer) {
            return [
                "result" => false,
                "error" => "مشخصات مشتری با نام کاربری $username در سامانه پیمانکار یافت نشد. "
            ];
        }

        // بررسی اعتبار تعرفه
        $result_tariff = $customer->valid_tariff();
        if (!$result_tariff["result"]) {
            return $result_tariff;
        }


        // بررسی درستی اطلاعات کالاها
        $message = "";
        $child_product = [];
        $degree_list = [];
        $order_consumed_product_list = [];
        $error_material_code = [];
        foreach ($product_data as &$product_data_item) {
            // کد کالا
            if ($product_data_item["product_code"] == "" || !$product_data_item["product_code"]) {
                $message .= " کد کالای " . $product_data_item["product_code"] . " نامعتبر است." . "<br/>";
                continue;
            }
            $product = Product::where("code", $product_data_item["product_code"])->first();
            if (!$product) {
                $message .= " کد کالای " . $product_data_item["product_code"] . " نامعتبر است." . "<br/>";
                continue;
            }

            $product_data_item["product"] = $product;
            // درجه
            if (count($product_data_item["degree_codes"]) == 0) {
                $message .= "نوع درجه برای کالای " .
                    $product_data_item["product_code"] . " مشخص نشده است." . "<br/>";
                continue;
            }
            $degree_ids = Degree::
            whereIn("code", $product_data_item["degree_codes"])->
            where("goods_kind_id", $product->goods_kind_id)->
            pluck("id", "code");
            if (count($degree_ids) != count($product_data_item["degree_codes"])) {
                $message .= "نوع درجه برای کالای " .
                    $product_data_item["product_code"] . " معتبر نمی باشد و یا در سامانه وجود ندارد." . "<br/>";
                continue;
            }

            $product_data_item["degree_ids"] = $degree_ids;

            // نوع بسته بندی
            if (count($product_data_item["packing_type_ids"]) == 0) {
                $message .= "نوع بسته بندی برای کالای " .
                    $product_data_item["product_code"] . " مشخص نشده است." . "<br/>";
                continue;
            }
            $packing_type_count = PackingType::whereIn("id", $product_data_item["packing_type_ids"])->count();
            if ($packing_type_count != count($product_data_item["packing_type_ids"])) {
                $message .= "نوع بسته بندی برای کالای " .
                    $product_data_item["product_code"] . " معتبر نمی باشد و یا در سامانه وجود ندارد." . "<br/>";
                continue;
            }

            // پیدا کردن ردیف تعرفه
            $product_tariff_list = ProductTariff::where([
                "product_id" => $product->id,
                "tariff_id" => $customer->tariff_id
            ])->
            whereIn("degree_id", $product_data_item["degree_ids"])->
            whereIn("packing_type_id", $product_data_item["packing_type_ids"])->
            get();

            if (count($product_tariff_list) == 0) {
                $message .= " در تعرفه " . $customer->tariff_id . " برای کالای " .
                    $product_data_item["product_code"] . " هیچ ردیفی یافت نشد." . "<br/>";
                continue;
            }
            if (count($product_tariff_list) != 1) {
                $message .= " در تعرفه " . $customer->tariff_id . " برای کالای " .
                    $product_data_item["product_code"] . " بیش از یک ردیف یافت شد و امکان انتخاب برای دستیار دیجیتال وجود ندارد.." . "<br/>";
                continue;
            }

            $product_data_item["product_tariff"] = $product_tariff_list[0];

            // بررسی کالاهای مصرفی که توسط مشتری تامین می شود.
            if (isset($product_data_item["packing_form_data"])) {


                foreach ($product_data_item["packing_form_data"] as $packing_form_data) {

                    foreach ($packing_form_data["packing_form_item"] as $packing_form_item) {

                        $line_product_station_child = LineProductStation::
                        where("customer_id", $customer->id)->
                        where("product_code_in_contractor_system", $packing_form_item["product_code"])->
                        get();

                        if (count($line_product_station_child) == 0) {
                            if (!isset($error_material_code[$packing_form_item["product_code"]])) {
                                $message .= "در هیچ کدام از مسیر محصول های " . $software_name . " کد کالای " .
                                    $packing_form_item["product_code"] .
                                    " به عنوان کد کالا در سامانه مشتری تعریف نشده است. " . "<br/>";
                                $error_material_code[$packing_form_item["product_code"]] = 1;
                            }


                        } else if (count($line_product_station_child) > 1) {
                            $message .= "بیش از یک مسیر محصول در " . $software_name . " وجود دارد که کد کالای " .
                                $packing_form_item["product_code"] .
                                " به عنوان کد کالا در سامانه مشتری تعریف نشده است. " . "<br/>";

                        } else {
                            $child_product[$packing_form_item["product_code"]] = $line_product_station_child[0]->product_id;


                            // کلید: شناسه کالا --- مقدار: شناسه ماده اولیه
                            $order_consumed_product_list[$product->id] = $line_product_station_child[0]->product_id;
                        }

                        // بررسی درجه های آیتم های بسته بندی، اگر یکبار به دست آوردیم، بارهای دیگر لازم نیست، چک کنیم.
                        if (!isset($degree_list[$packing_form_item["goods_kind_id"]][$packing_form_item["degree_code"]])) {
                            $degree = Degree::where([
                                "code" => $packing_form_item["degree_code"],
                                "goods_kind_id" => $packing_form_item["goods_kind_id"]
                            ])->first();
                            if (!$degree) {
                                $message .= "درجه کالا" .
                                    $packing_form_item["product_code"] .
                                    " برای" .
                                    $packing_form_item["product_code"] .
                                    " در سامانه تعریف نشده است. " . "<br/>";
                            } else {
                                $degree_list[$packing_form_item["goods_kind_id"]][$packing_form_item["degree_code"]] = $degree->id;

                            }
                        }
                    }
                }
            }

        }

        if ($message != "") {
            return [
                "result" => false,
                "error" => $message
            ];
        }


        $result_order = CustomerController::GetOrderForCustomer($customer, $user->id);
        if (!$result_order["result"]) {
            return $result_order;
        }
        $order = $result_order["order"];

        // اضافه کردن کالا ها به سفارش
        foreach ($product_data as $product_data_item) {

            $product = $product_data_item["product"];
            $product_tariff = $product_data_item["product_tariff"];

            $data_tariff = [
                $product_tariff->id => $product_data_item["amount"],
                "packing_type" => [
                    $product_tariff->id => [
                        $product_tariff->packing_type_id
                    ],
                ],
                "tracking_code1" => [
                    $product_tariff->id => $product_data_item["tracking_code1"]
                ],
                "tracking_code2" => [
                    $product_tariff->id => $product_data_item["tracking_code2"]
                ]
            ];

            $result_add_to_shopping_card = BuyController::PostAddToShoppingCard($order, $product, $customer, $data_tariff);
            if (!$result_add_to_shopping_card["result"]) {
                return $result_add_to_shopping_card;
            }
            if ($result_add_to_shopping_card["result"] && $result_add_to_shopping_card["order_added"] == 0) {
                return [
                    "result" => false,
                    "error" => "برای ثبت سفارش کالا با " . $product->code . " هیچ ردیفی به سفارش اضافه نشد، عدم مطابقت اطلاعات ارسالی با تعرفه کالا"
                ];
            }

            // پر کردن جدول نوع تامین مواد اولیه
            if (isset($order_consumed_product_list[$product->id])) {
                OrderConsumedProduct::firstOrCreate(
                    [
                        "order_id" => $order->id,
                        "product_id" => $product->id,
                        "material_id" => $order_consumed_product_list[$product->id],
                        "order_list_id" => 0,
                    ],
                    [
                        "contractor_supply_type_id" => 1
                    ]);
            }
        }

        // مشخص کردن نوع فاکتور
        $order = Order::SellingTypeRefresh($order);
        // بروز رسانی سری سفارش
        //
        $sale_series = Setting::getIntegerValue("sale_series_type_" . $order->selling_type_id);
        if (!$sale_series || $sale_series < 1) {
            return [
                "result" => false,
                "error" => " سری سفارش به درستی در تنظیمات فروش ثبت نشده است."
            ];
        }
        Order::UpdateCode($order, $sale_series);

        $order->factorRefresh();

        $order->updatePercentOff();

        $order = Order::find($order->id);

        $order->updatePrice();

        $order = Order::find($order->id);

        $order->updateFormalOff();

        $order->updateRoundOff();

        $order->updatePrice();
        $order = Order::find($order->id);

        $order->updatePercentCash();

        $order->updateRoundOff();

        $order->updatePrice();

        $order->nextOrderPermission();

        // ثبت آدرس
        $address = Address::GetAddressFromString(
            $address["country_id"], $address["province_id"], $address["address"],
            $address["postal_code"], $address["city_name"], $address["phone"]
        );

        $order->address_id = $address->id;
        $order->delivery_datetime = $delivery_datetime;
        $order->save();

        // ثبت روش پرداخت سفارش
        $sum_order_factor_with_tax = OrderFactor::where("order_id", $order->id)->sum("total_price_with_tax");
        $sum_order_factor_with_tax_remaining = $sum_order_factor_with_tax;
        $customer_payment_method = CustomerPaymentMethod::join("payment_method_types", "payment_method_type_id", "payment_method_types.id")->
        where([
            "customer_id" => $order->customer_id
        ])->
        orderBy("payment_method_type_id")->
        get();

        OrderPaymentMethod::where("order_id", $order->id)->delete();

        foreach ($customer_payment_method as $item) {

            $order_payment_method_value = round(min($sum_order_factor_with_tax_remaining, $sum_order_factor_with_tax * $item->max_percentage / 100));
            if ($order_payment_method_value > 0) {
                OrderPaymentMethod::create(
                    [
                        "order_id" => $order->id,
                        "customer_id" => $order->customer_id,
                        "payment_method_type_id" => $item->payment_method_type_id,
                        "amount" => $order_payment_method_value,
                        "check_delivery_days" => $item->max_check_delivery_time_in_days
                    ]);

                $sum_order_factor_with_tax_remaining -=
                    $order_payment_method_value;
            }

        }


        // ثبت کردن مواد اولیه که توسط پیمانکار باید تامین شود.
        foreach ($product_data as $product_data_item) {

            // بررسی کالاهای مصرفی که توسط مشتری تامین می شود.
            if (isset($product_data_item["packing_form_data"])) {

                foreach ($product_data_item["packing_form_data"] as $packing_form_data) {

                    $order_packing_form = OrderPackingForm::firstOrCreate([
                        "customer_id" => $order->customer_id,
                        "order_id" => $order->id,
                        "product_id" => $product_data_item["product"]->id,
                        "packing_form_code" => $packing_form_data["code"], // کد بسته بندی مشتری
                    ], [
                        "degree_id" => 0,
                        "lot_number_code" => "",
                        "amount" => 0

                    ]);
                    // بسته بندی که برای ردیف ایجاد شده است، ممکن است از قبل ایجاد شده باشد.
                    $packing_form_new = $order_packing_form->packing_form;
                    // ایجاد فرم بسته بندی نزد مشتری
                    if (!$packing_form_new) {
                        $packing_form_new = PackingForm::create([
                            "packing_type_id" => $packing_form_data["packing_type_id"],
                            "carrier_id" => null,
                            "status_id" => 7007023, // نزد مشتری (در انتظار ارسال)
                            "sub_packing_form_number" => 0
                        ]);
                    } else {
                        $packing_form_new->update([
                            "packing_type_id" => $packing_form_data["packing_type_id"],
                            "carrier_id" => null,
                            "status_id" => 7007023, // نزد مشتری (در انتظار ارسال)
                            "sub_packing_form_number" => 0
                        ]);
                    }
                    foreach ($packing_form_data["packing_form_item"] as $packing_form_item) {

                        // لات
                        $lot_number = LotNumber::where([
                            "product_id" => $child_product[$packing_form_item["product_code"]],
                            "code" => $packing_form_item["lot_number_code"],
                        ])->
                        first();
                        if (!$lot_number) {
                            $lot_number = LotNumber::create([
                                "product_id" => $child_product[$packing_form_item["product_code"]],
                                "code" => $packing_form_item["lot_number_code"],
                                "user_id" => $user->id
                            ]);
                        }
//                        return [
//                            "result" => false,
//                            "order_id" => $order->id,
//                            "error" => $lot_number->code
//                        ];
                        // اضافه کردن یک ردیف
                        $new_master_packing_form_item = PackingFormItem::create([
                            "packing_form_id" => $packing_form_new->id,
                            "product_id" => $child_product[$packing_form_item["product_code"]], // کد ماده اولیه,
                            "lot_number_id" => $lot_number->id,
                            "degree_id" => $degree_list[$packing_form_item["goods_kind_id"]][$packing_form_item["degree_code"]],
                            "amount" => $packing_form_item["amount"],
                            "amount_after_control" => $packing_form_item["amount"],
                            "final_amount" => $packing_form_item["amount"],
                            "sub_amount" => $packing_form_item["sub_amount"],
                            "init_sub_amount" => $packing_form_item["sub_amount"],
                            "status_id" => 7007023, // نزد مشتری (در انتظار ارسال)
                            "band_code" => $packing_form_item["band_code"]
                        ]);

                        $order_packing_form->material_id = $new_master_packing_form_item->product_id;
                        $order_packing_form->degree_id = $new_master_packing_form_item->degree_id;
                        $order_packing_form->packing_type_id = $new_master_packing_form_item->packing_type_id;
                        $order_packing_form->lot_number_code = $lot_number->code;
                        $order_packing_form->packing_form_id = $new_master_packing_form_item->packing_form_id;
                        $order_packing_form->amount = $packing_form_new->getFinalAmount();
                        $order_packing_form->save();
                    }
                }
            }

        }

        return [
            "result" => true,
            "order_id" => $order->id,
            "message" => "ثبت سفارش در سامانه پیمانکار با شماره سفارش " . $order->code() . " ثبت گردید."
        ];
    }

    /**
     * @param Request $request
     * @return array
     * هماهنگی ارسال مواد اولیه و تخصیص کارت تامین (کالای امانی)
     */
    public function call_coordination_for_sending(Request $request)
    {

        $order_code = $request->tracking_code1; // کد سفارش
        $production_code = $request->tracking_code2; // کد کارت تولید سطح بالا
        $datetime = $request->datetime; // تاریخ هماهنگی
        $message = $request->message;
        $other_data = json_decode($request->other_data, 1);

        $production = Production::where("serial", $production_code)->first();
        if (!$production) {
            return [
                "result" => false,
                "error" => "کارت تولید " . $production_code . " در سامانه یافت نشد."
            ];
        }

        if (!$production->order || $production->order->code() != $order_code) {
            return [
                "result" => false,
                "error" => "کارت تولید " . $production_code . " و سفارش $order_code مطابق ندارد."
            ];
        }
        $contractor_allocation = ContractorAllocation::where("production_id", $production->id)->first();
        if (!$contractor_allocation) {
            return [
                "result" => false,
                "error" => "برای کارت تولید " . $production_code . "هیچ تخصیصی یافت نشد."
            ];
        }
        if (!$contractor_allocation->contractor) {
            return [
                "result" => false,
                "error" => "شناسه پیمانکار در تخصیص کارت تولید " . $production_code . "معتبر نمی باشد."
            ];
        }

        $result = CoordinationForSendingController::PostSubmit($datetime, $contractor_allocation->contractor, $contractor_allocation, $message, $other_data);


        return $result;
    }

    /**
     * @param Request $request
     * @return array
     * ایجاد بسته بندی ها در سامانه
     * پیمانکار در زمانی که برگ خروج می شود در انتظار تایید درخواست کننده
     */
    public function add_input_form_for_contractor(Request $request)
    {

        if (!$request->packing_form_codes) {
            return [
                "result" => false,
                "error" => "لیست بسته بندی ها جهت ایجاد فرم ورود به انبار خالی است."
            ];
        }
        if (!$request->other_data) {
            return [
                "result" => false,
                "error" => "اطلاعات رهگیری درخواست تکمیل نشده است."
            ];
        }
        $exist_form_codes_data = $request->exist_form_codes;
        $message = $request->message;
        $other_data = json_decode($request->other_data, 1);
        $packing_form_codes = json_decode($request->packing_form_codes, 1);

        $warehouse_id = -1;
        $customer_id = -1;
        $order_id = -1;
        if (isset($other_data["customer_id"])) {
            $customer_id = $other_data["customer_id"];
        }
        if (isset($other_data["order_id"])) {
            $order_id = $other_data["order_id"];
        }
        $order = Order::find($order_id);
        if (!$order) {
            return [
                "result" => false,
                "error" => "سفارش  با شناسه $order_id  نامعتبر است."
            ];
        }
        $customer = Customer::find($customer_id);
        if (!$customer) {
            return [
                "result" => false,
                "error" => "مشتری با شناسه $customer  نامعتبر است."
            ];
        }

        // پیدا کردن بسته بندی معادل
        $moadel_packing_forms = [];
        foreach ($packing_form_codes as $packing_form_code) {
            $order_packing_form = OrderPackingForm::where([
                    "packing_form_code" => $packing_form_code,
//                    "order_id" => $order_id
                ]
            )->first();
            if (!$order_packing_form) {
                $software_name = Setting::getStringValue("software_name");
                return [
                    "result" => false,
                    "error" => "معادل بسته بندی $packing_form_code در $software_name  برای سفارش " . $order->code() . " یافت نشد."
                ];
            }
            if ($order_packing_form->packing_form->status_id != 7007023) { // نزد مشتری (در انتظار دریافت)
                $software_name = Setting::getStringValue("software_name");
                return [
                    "result" => false,
                    "error" => " وضعیت بسته بندی " . $order_packing_form->packing_form->code . " در   $software_name نامعتبر است."
                ];
            }

            $moadel_packing_forms[] = $order_packing_form->packing_form;
        }


        $ic = $customer->code ?? null;
        if (!$ic) {
            return [
                "result" => false,
                "error" => "مرکز هزینه " . $customer->caption . " نامعتبر است."
            ];
        }
        //ایجاد فرم ورود به انبار
        $form = Form::CreateFrom([
            "order_id" => 0,
            "order_list_id" => 0,
            "production_card_id" => 0,
            "user_id" => Auth::user()->id,
            "form_type_id" => 304,
            "trans_kind" => 4, // دریافت امانی
            "warehouse_id" => $warehouse_id,
            "status_id" => 500000410, // در انتظار تایید انبار
            "ic" => $ic,
            "applicant_type_id" => 30,
            "applicant_id" => $customer_id
        ]);
        $form->getCode();

        foreach ($moadel_packing_forms as $packing_form) {
            foreach ($packing_form->items as $item) {
                FormItem::create([
                    "form_id" => $form->id,
                    "packing_form_item_id" => $item->id,
                    "packing_type_id" => $packing_form->packing_type_id,
                    "product_id" => $item->product_id,
                    "amount" => $item->final_amount,
                    "sub_amount" => $item->sub_amount,
                    "carrier_id" => $packing_form->carrier_id,
                    "degree_id" => $item->degree_id,
                    "lot_number_id" => $item->lot_number_id,
                    "description" => "دریافت امانی کالا با بسته بندی " . ($item->code ?? "")
                ]);
                $item->status_id = 7007002; //  در انتظار تایید انبار
                $item->save();

                // پیدا کردن انبار
                if ($warehouse_id == -1) {
                    $result = Degree::getMainDegree($item->product->goods_kind_id);
                    if (!$result["result"]) {
                        return $result;
                    }
                    $warehouse_id = $result["degree"]->warehouse_id;
                }
            }
            $packing_form->status_id = 7007002; //  در انتظار تایید انبار
            $packing_form->save();
        }

        // ذخیره کردن انبار
        $form->warehouse_id = $warehouse_id;
        $form->save();

        event(new FormLogEvent($form, $message,));

        $jsn_data_list = JsonDataList::create([
            "other_id" => $form->id,
            "message_type_id" => 340, // اطلاعات اضافه درخواست
            "data" => $exist_form_codes_data
        ])->first();

        return [
            "result" => true,
        ];
    }

    /***
     * @param Request $request
     * @return array|true[]
     * بسته بندی هایی که در سامانه پیمانکار ایجاد شده اند و برگ خروج برای آنها صادر شده است، حال باید در سامانه مشتری هم
     * تحویل به تولید آنها صادر شود.
     */
    public function add_input_form_for_customer(Request $request)
    {

        if (!$request->packing_form_codes) {
            return [
                "result" => false,
                "error" => "لیست بسته بندی ها جهت ایجاد فرم ورود به انبار خالی است."
            ];
        }

        $customer_exist_form_id = $request->customer_exist_form_id;
        $packing_form_codes = json_decode($request->packing_form_codes, 1);

        $contractor_exist_form = Form::find($customer_exist_form_id);
        if (!$contractor_exist_form) {
            return [
                "result" => false,
                "error" => "برگ خروج با شناسه $customer_exist_form_id در سامانه یافت نشد."
            ];
        }
        $json_data_before = JsonDataList::where([
            "other_id" => $customer_exist_form_id,
            "message_type_id" => 350, // لیست فرم های ورودی که برای یک برگ خروج صادر شده است.
        ])->first();

        if ($json_data_before) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه قبلا یک درخواست برای برگ خروج از انبار با شناسه $contractor_exist_form_id به صورت کامل یا نیمه کامل انجام شده است، امکان ثبت مجدد آن وجود ندارد، لطفا با پشتیبانی تماس بگیرید. "
            ];
        }

        // پیدا کردن بسته بندی معادل
        $moadel_packing_forms = [];
        foreach ($packing_form_codes as $packing_form_code) {

            $packing_form = PackingForm::where("source_packaging_form_code", $packing_form_code)->first();
            if (!$packing_form) {
                return [
                    "result" => false,
                    "error" => "هیچ بسته بندی وجود ندارد که شناسه مبدا آن کد " . $packing_form_code . " باشد."
                ];
            }
            $moadel_packing_forms[$packing_form->id] = $packing_form;
        }

        if (count($moadel_packing_forms) == 0) {
            return [
                "result" => false,
                "error" => "هیچ بسته بندی انتخاب نشده است و یا هیچ بسته بندی با مبدا های انتخاب شده وجود ندارد."
            ];
        }

        if (count($moadel_packing_forms) != count($packing_form_codes)) {
            return [
                "result" => false,
                "error" => "در لیست بسته بندی های ارسال شده، بسته تکراری وجود دارد."
            ];
        }
        $machine_allocation_packing_form_list = MachineAllocationPackingForm::whereIn("packing_form_id", array_keys($moadel_packing_forms))->
        get();
        $machine_allocation_data_list = []; // machine_allocation/machine_allocation_packing_forms
        $input_form_ids = [];
        foreach ($machine_allocation_packing_form_list as $machine_allocation_packing_form) {


            // اگر متغیر وجود ندارد اضافه می کند.
            if (!isset($machine_allocation_data_list[$machine_allocation_packing_form->machine_allocation_id])) {

                $machine_allocation_data_list
                [$machine_allocation_packing_form->machine_allocation_id]
                ["machine_allocation"] =
                    $machine_allocation_packing_form->machine_allocation;

                $machine_allocation_data_list
                [$machine_allocation_packing_form->machine_allocation_id]
                ["machine_allocation_packing_forms"] = [];
            }

            $machine_allocation_data_list
            [$machine_allocation_packing_form->machine_allocation_id]
            ["machine_allocation_packing_forms"][] =
                $machine_allocation_packing_form;
        }
        $error_message = "";
        $input_forms_code = [];
        foreach ($machine_allocation_data_list as $machine_allocation_id => $machine_allocation_data) {
            $result_machine_allocation = RegisterProductionController::SendingPackingFromToWarehouse(
                $machine_allocation_data["machine_allocation"],
                [],
                null,
                $machine_allocation_data["machine_allocation_packing_forms"]
            );

            if (!$result_machine_allocation["result"]) {
                $error_message .="تخصیص ".$machine_allocation_data["machine_allocation"]->id."=>". $result_machine_allocation["error"];
            } else {
                $input_forms_code[$machine_allocation_id] = $result_machine_allocation["form_list"];
                foreach ($input_forms_code[$machine_allocation_id] as $input_form_item) {
                    $input_form_ids[$input_form_item->id] = "*" . $input_form_item->id . "*,";
                }
            }
        }

        // اگر حداقل یک بسته بندی ثبت گرده
        if ($input_form_ids != []) {
            // اطلاعات برگ خروج پیمانکار و بسته بندی های مشتری را ثبت می کنیم تا در زمان تایید فرم ورود بتوانیم
            // تشخصیص دهیم که چند فرم ورود باید با هم تایید شوند تا این بسته بندی ها هم تایید. شوند.
            $data["input_form_ids"] = $input_form_ids;
            $data["output_form_code"] = $contractor_exist_form->code;
            JsonDataList::create([
                "other_id" => $customer_exist_form_id,
                "message_type_id" => 350, // لیست فرم های ورودی که برای یک برگ خروج صادر شده است.
                "data" => json_encode($data)
            ]);
        }

        if ($error_message != "") {
            return [
                "result" => false,
                "error" => $error_message
            ];
        }


        return [
            "result" => true,
        ];
    }

    public function confirm_applicant_for_input_form(Request $request)
    {

        if (!$request->other_data) {
            return [
                "result" => false,
                "error" => "اطلاعات رهگیری درخواست تکمیل نشده است."
            ];
        }

        $message_api = $request->message;
        $other_data = json_decode($request->other_data, 1);

        $exist_form_list = [];
        $error = "";
        foreach ($other_data as $exit_form_code) {
            $exit_form = Form::where("code", $exit_form_code)->first();
            if (!$exit_form) {
                $error .= " برگ خروج از انبار " . $exit_form_code . " در سامانه یافت نشد." . "<br/>";
                continue;
            }
            if ($exit_form->status_id != 500000500) {
                $error .= " وضعیت برگ خروج از انبار " . $exit_form_code . "(" . $exit_form->status->caption . ")" . " نامعتبر است، وضعیت برگ خروج باید در انتظار تایید درخواست کننده باشد." . "<br/>";
                continue;
            }

            $result = ExitFormController::ConfirmApplicant($exit_form, $message_api);
            if (!$result["result"]) {
                $error .= "در زمان تایید برگ خروج " . $exit_form_code . ": " . $result["error"];
            }
        }

        if ($error != "") {
            return [
                "result" => false,
                "error" => $error
            ];
        }
        return [
            "result" => true,
        ];
    }

    /**
     * @param Request $request
     * @return array
     * ثبت بسته بندی ها در سامانه مشتری/پیمانکار
     */
    public function register_packing_in_to_system_software(Request $request)
    {

        if (!$request->packing_form_data) {
            return [
                "result" => false,
                "error" => "اطلاعات بسته بندی نامعتبر است."
            ];
        }
        $packing_form_data = json_decode($request->packing_form_data, 1);

        // اطلاعات آیتم های بسته بندی
        if (!$request->packing_form_item_data) {
            return [
                "result" => false,
                "error" => "اطلاعات بسته بندی نامعتبر است."
            ];
        }
        $packing_form_item_data = json_decode($request->packing_form_item_data, 1);

        $other_data = json_decode($request->other_data, 1);
        $production_code = $other_data["tracking_code2"];
        $user_id = Auth::user()->id;

        return RegisterProductionController::AddNewPackingFormFromAPI($packing_form_data, $packing_form_item_data, $production_code, $user_id);


    }

}

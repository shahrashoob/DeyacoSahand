<?php

namespace App\Models\SoftwareSystem;

use App\Http\Controllers\Customer\BuyController;
use App\Http\Controllers\Sales\CustomerController;
use App\Http\Controllers\Utility\SoftwareSystem\Deyaco\Order\OrderApiController;
use App\Models\Accounting\Tariff\ProductTariff;
use App\Models\Contractor\Contractor;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerPaymentMethod;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Order\Order;
use App\Models\Order\OrderFactor;
use App\Models\Order\OrderList;
use App\Models\Order\OrderPaymentMethod;
use App\Models\Production\Production;
use App\Models\User;
use App\Models\Utility\Address\Address;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Setting;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\MultipartStream;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use mysql_xdevapi\Exception;

class SoftwareSystem extends Model
{
    use HasFactory;

    protected $fillable = [
        "caption",

    ];
    protected $table = 'software_systems';

    public static function Login(SoftwareSystem $softwareSystem, $url, $username, $password, $api_key)
    {
        // return ["result" => true];
        switch ($softwareSystem->id) {
            case 1: // دیاکو
                try {

                    $settings = [
                        'base_uri' => "$url/",
                        'headers' => [
                            'Accept' => 'application/json',
                        ],
                        'query' => [
                            'email' => $username,
                            'password' => $password,
                            'api_key' => $api_key,
                            "cooperation_type_id" => 2
                        ]
                    ];
                    $client = new \GuzzleHttp\Client($settings);
                    $request = $client->request(
                        'POST',
                        "api/login"
                    );
                    $response = $request->getBody();

                    return json_decode($response, 1);
                } catch (Exception $exception) {
                    return ["result" => false];
                }
                break;
        }
        1 / 0;
    }

    public static function Logout(SoftwareSystem $softwareSystem, $url, $bearer_token)
    {

        switch ($softwareSystem->id) {
            case 1: // دیاکو
                // ایجاد نمونه کلاینت Guzzle
                $client = new Client();
                $url = $url . "/api/logout";
                $headers = [
                    'Authorization' => " Bearer " . $bearer_token,
                ];

                // ایجاد یک درخواست POST با فایل
                $request = new \GuzzleHttp\Psr7\Request('POST', $url, $headers);

                // ارسال درخواست
                $response = $client->send($request);

                // گرفتن بدنه پاسخ
                $body = $response->getBody();
                $body = json_decode($body, 1);
                return $body;
                break;
        }
        1 / 0;
    }

    /***
     * ثبت درخواست طراحی جدید
     * @param SoftwareSystem $softwareSystem
     * @param $url
     * @param $bearer_token
     * @param Production $production
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function CallNewProductCreation(SoftwareSystem $softwareSystem, $url, $bearer_token, Production $production)
    {
        switch ($softwareSystem->id) {

            case 1: // دیاکو
                // ایجاد نمونه کلاینت Guzzle
                $client = new Client();
                $url = $url . "/api/software_system/" . $softwareSystem->route . "/product/product_creation/new_form_api/submit";
                $vars = [];
                $headers = [
                    'Authorization' => " Bearer " . $bearer_token,
                ];

                if ($production->product->image && Storage::exists($production->product->image->path)) {
                    // باز کردن یک جریان به فایل
                    $stream = fopen($production->product->image->path, 'r');
                    $vars[] = [
                        'name' => 'image_file',
                        'contents' => $stream,
                    ];
                }

                $vars[] = [
                    "name" => "caption",
                    "contents" => $production->product->caption];
                $vars[] = [
                    "name" => "goods_kind_id",
                    "contents" => $production->product->goods_kind_id];
                $vars[] = [

                    "name" => "method_of_sending_product_id",
                    "contents" => 1 // تحویل حضوری
                ];
                $vars[] = [
                    "name" => "product_service_type_id",
                    "contents" => 1, // کالا
                ];
                $vars[] = [
                    "name" => "has_physical_sample",
                    "contents" => 0,
                ];

                // ساختن بدنه درخواست
                $body = new MultipartStream($vars);

                // ایجاد یک درخواست POST با فایل
                $request = new \GuzzleHttp\Psr7\Request('POST', $url, $headers, $body);

                // ارسال درخواست
                $response = $client->send($request);

                // گرفتن بدنه پاسخ
                $body = $response->getBody();
                $body = json_decode($body, 1);
                return $body;
                break;

        }
        1 / 0;
    }

    /***
     * @param SoftwareSystem $softwareSystem
     * @param $url
     * @param $bearer_token
     * @return array
     * ثبت سفارش توسط API
     */
    public static function CallOrderRegistration(
        SoftwareSystem $softwareSystem,
                       $url,
                       $username,
                       $bearer_token,
                       $product_data,
        Address        $address,
                       $delivery_datetime,
    )
    {
        switch ($softwareSystem->id) {

            case 1: // دیاکو
                //new OrderApiController();
                // ایجاد نمونه کلاینت Guzzle
                $client = new Client();
                $url = $url . "/api/software_system/" . $softwareSystem->route . "/order/call_order_registration";
                $vars = [];
                $headers = [
                    'Authorization' => " Bearer " . $bearer_token,
                ];


                $vars[] = [
                    "name" => "username",
                    "contents" => $username];

                $vars[] = [
                    "name" => "delivery_datetime",
                    "contents" => $delivery_datetime];
                $vars[] = [

                    "name" => "product_data",
                    "contents" => json_encode($product_data)
                ];
                $vars[] = [
                    "name" => "address",
                    "contents" => json_encode($address->toArray())
                ];

                // ساختن بدنه درخواست
                $body = new MultipartStream($vars);

                // ایجاد یک درخواست POST با فایل
                $request = new \GuzzleHttp\Psr7\Request('POST', $url, $headers, $body);

                // ارسال درخواست
                $response = $client->send($request);

                // گرفتن بدنه پاسخ
                $body = $response->getBody();
                $body = json_decode($body, 1);
                return $body;
                break;
        }
        1 / 0;
    }

    /***
     * هماهنگی ارسال بار
     * @param SoftwareSystem $softwareSystem
     * @param $url
     * @param $bearer_token
     * @param $product_data
     * @param Address $address
     * @param $delivery_datetime
     * @return array
     */
    public static function CallCoordinationForSending(SoftwareSystem $softwareSystem, $url, $bearer_token, $tracking_code1, $tracking_code2, $predict_of_production_start_date_practical, $message, $suggested_packing_codes, $customer_id, $order_id)
    {
        switch ($softwareSystem->id) {

            case 1: // دیاکو
                //new OrderApiController();
                //   Guzzle
                $client = new Client();
                $url = $url . "/api/software_system/" . $softwareSystem->route . "/order/call_coordination_for_sending";
                $vars = [];
                $headers = [
                    'Authorization' => " Bearer " . $bearer_token,
                ];

                $other_data["customer_id"] = $customer_id;
                $other_data["order_id"] = $order_id;
                $other_data["suggested_packing_codes"] = $suggested_packing_codes;
                $other_data["tracking_code1"] = $tracking_code1;
                $other_data["tracking_code2"] = $tracking_code2;


                $vars[] = [
                    "name" => "tracking_code1",
                    "contents" => $tracking_code1
                ];

                $vars[] = [
                    "name" => "tracking_code2",
                    "contents" => $tracking_code2
                ];


                $vars[] = [
                    "name" => "datetime",
                    "contents" => $predict_of_production_start_date_practical
                ];
                $vars[] = [
                    "name" => "message",
                    "contents" => $message
                ];

                $vars[] = [
                    "name" => "other_data",
                    "contents" => json_encode($other_data)
                ];


                // ساختن بدنه درخواست
                $body = new MultipartStream($vars);

                // ایجاد یک درخواست POST با فایل
                $request = new \GuzzleHttp\Psr7\Request('POST', $url, $headers, $body);

                // ارسال درخواست
                $response = $client->send($request);

                // گرفتن بدنه پاسخ
                $body = $response->getBody();
                $body = json_decode($body, 1);
                return $body;
                break;
        }
        1 / 0;
    }

    /**
     * @param SoftwareSystem $softwareSystem
     * @param $url
     * @param $bearer_token
     * @param Product\ProductRequest\ProductRequestForm $productRequestForm
     * @param $exist_form
     * @param $transport
     * @param $message
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * ایجاد فرم ورود در سامانه مشتری برای فرم های خروج از انبار پیمانکار
     */
    public static function CallAddInputFormForContractor(SoftwareSystem $softwareSystem, $url, $bearer_token, Product\ProductRequest\ProductRequestForm $productRequestForm, $exist_form, $transport, $message)
    {
        switch ($softwareSystem->id) {

            case 1: // دیاکو
                $client = new Client();
                $url = $url . "/api/software_system/" . $softwareSystem->route . "/order/add_input_form_for_contractor";
                $vars = [];
                $headers = [
                    'Authorization' => " Bearer " . $bearer_token,
                ];

                $jsn_data_list = JsonDataList::where([
                    "other_id" => $productRequestForm->id,
                    "message_type_id" => 330, // اطلاعات اضافه درخواست
                ])->first();
                $other_data = $jsn_data_list->data ?? null;

                $vars[] = [
                    "name" => "other_data",
                    "contents" => $other_data
                ];

                $vars[] = [
                    "name" => "message",
                    "contents" => $message
                ];

                $exist_form_codes = [];
                // بسته بندی های داخل برگ خروج از انبار
                if ($exist_form) {
                    $form_items = FormItem::where("form_id", $exist_form->id)->
                    with("packing_form_item.packing_form")->get();
                    $packing_form_codes = [];
                    foreach ($form_items as $item) {
                        $packing_form_codes[$item->packing_form_item->packing_form_id] = $item->packing_form_item->packing_form->code;
                    }

                    $exist_form_codes[$exist_form->id] = $exist_form->code;
                }
                // اگر بار بود، کل برگ های خروج بار می شود یک فرم ورود به انبار
                if ($transport) {
                    $packing_form_codes = [];
                    foreach ($transport->transport_forms as $t_form) {
                        $t_exist_form = $t_form->form;
                        $form_items = FormItem::where("form_id", $t_exist_form->id)->
                        with("packing_form_item.packing_form")->get();

                        foreach ($form_items as $item) {
                            $packing_form_codes[$item->packing_form_item->packing_form_id] = $item->packing_form_item->packing_form->code;
                        }

                        $exist_form_codes[$t_exist_form->id] = $t_exist_form->code;
                    }
                }


                $vars[] = [
                    "name" => "exist_form_codes",
                    "contents" => json_encode($exist_form_codes)
                ];
                $vars[] = [
                    "name" => "packing_form_codes",
                    "contents" => json_encode($packing_form_codes)
                ];


                // ساختن بدنه درخواست
                $body = new MultipartStream($vars);

                // ایجاد یک درخواست POST با فایل
                $request = new \GuzzleHttp\Psr7\Request('POST', $url, $headers, $body);

                // ارسال درخواست
                $response = $client->send($request);

                // گرفتن بدنه پاسخ
                $body = $response->getBody();
                $body = json_decode($body, 1);
                return $body;
                break;
        }
    }

    /**
     * @param SoftwareSystem $softwareSystem
     * @param $url
     * @param $bearer_token
     * @param Product\ProductRequest\ProductRequestForm $productRequestForm
     * @param $exist_form
     * @param $transport
     * @param $message
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * ایجاد فرم ورود در سامانه پیمانکار برای فرم های خروج از انبار مشتری
     */
    public static function CallAddInputFormForCustomer(SoftwareSystem $softwareSystem, $url, $bearer_token, Product\ProductRequest\ProductRequestForm $productRequestForm, $exist_form, $transport, $message)
    {
        switch ($softwareSystem->id) {

            case 1: // دیاکو
                $client = new Client();
                $url = $url . "/api/software_system/" . $softwareSystem->route . "/order/add_input_form_for_customer";
                $vars = [];
                $headers = [
                    'Authorization' => " Bearer " . $bearer_token,
                ];

                $jsn_data_list = JsonDataList::where([
                    "other_id" => $productRequestForm->id,
                    "message_type_id" => 330, // اطلاعات اضافه درخواست
                ])->first();
                $other_data = $jsn_data_list->data ?? null;

                $vars[] = [
                    "name" => "other_data",
                    "contents" => $other_data
                ];

                $vars[] = [
                    "name" => "message",
                    "contents" => $message
                ];


                $exist_form_codes = [];
                // بسته بندی های داخل برگ خروج از انبار
                if ($exist_form) {
                    $form_items = FormItem::where("form_id", $exist_form->id)->
                    with("packing_form_item.packing_form")->get();
                    $packing_form_codes = [];
                    foreach ($form_items as $item) {
                        $packing_form_codes[$item->packing_form_item->packing_form_id] = $item->packing_form_item->packing_form->code;
                    }

                    $exist_form_codes[$exist_form->id] = $exist_form->code;

                }
                // اگر بار بود، کل برگ های خروج بار می شود یک فرم ورود به انبار
                if ($transport) {
                    $packing_form_codes = [];
                    foreach ($transport->transport_forms as $t_form) {
                        $t_exist_form = $t_form->form;

                        // وقتی به صورت بارگیری فرم های خروج ارسال می شود، اولین فرم را به عنوان فرم خروج ثبت می کنیم.
                        if (!$exist_form) {
                            $exist_form = $t_exist_form;
                        }
                        $form_items = FormItem::where("form_id", $t_exist_form->id)->
                        with("packing_form_item.packing_form")->get();

                        foreach ($form_items as $item) {
                            $packing_form_codes[$item->packing_form_item->packing_form_id] = $item->packing_form_item->packing_form->code;
                        }

                        $exist_form_codes[$t_exist_form->id] = $t_exist_form->code;


                    }
                }


                $vars[] = [
                    "name" => "customer_exist_form_id",
                    "contents" => $exist_form->id
                ];
                $vars[] = [
                    "name" => "exist_form_codes",
                    "contents" => json_encode($exist_form_codes)
                ];
                $vars[] = [
                    "name" => "packing_form_codes",
                    "contents" => json_encode($packing_form_codes)
                ];


                // ساختن بدنه درخواست
                $body = new MultipartStream($vars);

                // ایجاد یک درخواست POST با فایل
                $request = new \GuzzleHttp\Psr7\Request('POST', $url, $headers, $body);

                // ارسال درخواست
                $response = $client->send($request);

                // گرفتن بدنه پاسخ
                $body = $response->getBody();
                $body = json_decode($body, 1);
                return $body;
                break;
        }
    }

    /**
     * @param SoftwareSystem $softwareSystem
     * @param $url
     * @param $bearer_token
     * @param Form $input_form
     * @param $message
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * تایید فرم ورود و تایید شدن برگ های خروج معادل آن در مبدا
     */
    public static function CallConfirmApplicantForInputForm(SoftwareSystem $softwareSystem, $url, $bearer_token, Form $input_form, $message, $other_data_json = null)
    {
        switch ($softwareSystem->id) {

            case 1: // دیاکو
                $client = new Client();
                $url = $url . "/api/software_system/" . $softwareSystem->route . "/order/confirm_applicant_for_input_form";
                $vars = [];
                $headers = [
                    'Authorization' => " Bearer " . $bearer_token,
                ];

                if (!$other_data_json) {
                    $jsn_data_list = JsonDataList::where([
                        "other_id" => $input_form->id,
                        "message_type_id" => 340, // اطلاعات اضافه برای فرم های انبار
                    ])->first();
                    $other_data_json = $jsn_data_list->data ?? null;
                }

                $vars[] = [
                    "name" => "other_data",
                    "contents" => $other_data_json
                ];

                $vars[] = [
                    "name" => "message",
                    "contents" => $message
                ];


                // ساختن بدنه درخواست
                $body = new MultipartStream($vars);

                // ایجاد یک درخواست POST با فایل
                $request = new \GuzzleHttp\Psr7\Request('POST', $url, $headers, $body);

                // ارسال درخواست
                $response = $client->send($request);

                // گرفتن بدنه پاسخ
                $body = $response->getBody();
                $body = json_decode($body, 1);
                return $body;
                break;
        }
    }

    /**
     * @param SoftwareSystem $softwareSystem
     * @param $url
     * @param $bearer_token
     * @param PackingForm $packingForm
     * @param Order $order
     * @return array
     *  اگر مشتری سامانه جامع داشته باشد، میتوان با استفاده از این قابلیت هر بسته بندی که در زمان تولید ثبت می شود، آن را در سامانه مشتری هم ثبت کرد.
     */
    public static function RegisterPackingInToSystemSoftware(SoftwareSystem $softwareSystem, $url, $bearer_token, PackingForm $packingForm, Order $order)
    {
        switch ($softwareSystem->id) {

            case 1: // دیاکو
                $client = new Client();
                $url = $url . "/api/software_system/" . $softwareSystem->route . "/order/register_packing_in_to_system_software";
                $vars = [];
                $headers = [
                    'Authorization' => " Bearer " . $bearer_token,
                ];

                $other_data = $jsn_data_list->data ?? null;

                $packing_form_data = $packingForm->toArray();
                // بسته بندی مبدا
                $packing_form_data["source_packaging_form_code"] = $packing_form_data["code"]; //

                // بسته بندی مقصد
                unset($packing_form_data["destination_packing_form_code"]);
                unset($packing_form_data["id"]);
                unset($packing_form_data["form_id"]);
                unset($packing_form_data["created_at"]);
                unset($packing_form_data["updated_at"]);
                unset($packing_form_data["code"]);
                unset($packing_form_data["random"]);
                unset($packing_form_data["packing_form_master_id"]);
                unset($packing_form_data["created_at_number"]);
                unset($packing_form_data["created_at_month_number"]);
                $vars[] = [
                    "name" => "packing_form_data",
                    "contents" => json_encode($packing_form_data)
                ];
                $packing_form_item_data = $packingForm->items()->get();
                $first_product_id = null;
                // گرفتن لات آیتم های بسته بندی
                foreach ($packing_form_item_data as &$item) {
                    if ($first_product_id != null && $item->product_id != $first_product_id) {
                        return [
                            "result" => false,
                            "error" => "با توجه به اینکه کالاهای داخل بسته بندی متفاوت است، امکان اجرای Api وجود ندارد."
                        ];
                    }
                    $first_product_id = $item->product_id;
                    $item->lot_number_code = $item->lot_number->code;
                    unset($item->lot_number);
                    unset($item->id);
                    unset($item->code);
                    unset($item->packing_form_id);
                    unset($item->production_form_item_id);
                    unset($item->production_form_item_lot_number_id);
                    unset($item->status_id);
                    unset($item->created_at);
                    unset($item->updated_at);
                    unset($item->product_id);
                    unset($item->lot_number_id);
                    unset($item->machine_allocation_actual_cost_id);
                }

                $vars[] = [
                    "name" => "packing_form_item_data",
                    "contents" => json_encode($packing_form_item_data)
                ];
                if (count($packing_form_item_data) == 0) {
                    return [
                        "result" => false,
                        "error" => "اطلاعات بسته بندی جهت ثبت نادرست است - 1"
                    ];
                }

                $order_list = OrderList::where(["order_id" => $order->id, "product_id" => $first_product_id])->first();
                if (!$order_list) {
                    return [
                        "result" => false,
                        "error" => "ردیف سفارش متناظر با بسته بندی " . $packingForm->code . " در سفارش " . $order->code() . " یافت نشد."
                    ];
                }

                $other_data["tracking_code1"] = $order_list->tracking_code1;
                $other_data["tracking_code2"] = $order_list->tracking_code2;
                $vars[] = [
                    "name" => "other_data",
                    "contents" => json_encode($other_data)
                ];

                // ساختن بدنه درخواست
                $body = new MultipartStream($vars);

                // ایجاد یک درخواست POST با فایل
                $request = new \GuzzleHttp\Psr7\Request('POST', $url, $headers, $body);

                // ارسال درخواست
                $response = $client->send($request);

                // گرفتن بدنه پاسخ
                $body = $response->getBody();
                $body = json_decode($body, 1);
                return $body;
                break;
        }
    }

}

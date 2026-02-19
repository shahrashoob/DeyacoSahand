<?php

namespace App\Models\Order;

use App\Events\Order\OrderLogEvent;
use App\Models\Accounting\CheckDeliveryTimeType;
use App\Models\Accounting\HeadOfCheckType;
use App\Models\Accounting\Offer;
use App\Models\Accounting\PaymentMethod;
use App\Models\Accounting\SellingType;
use App\Models\Accounting\Tariff\ProductTariff;
use App\Models\Accounting\Tariff\ProductTariffLog;
use App\Models\Accounting\Tariff\Tariff;
use App\Models\Accounting\Tariff\TariffLog;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\LineProduct\ProductInventory;
use App\Models\Order\Loading\LoadingProcesses;
use App\Models\Order\Permision\OrderPermissionCustomer;
use App\Models\Order\Permision\OrderPermissionType;
use App\Models\Post\Post;
use App\Models\Post\PostUser;
use App\Models\User;
use App\Models\Utility\Address\Address;
use App\Models\Utility\Car\CarType;
use App\Models\Utility\Car\DeliveryPointType;
use App\Models\Utility\Car\ShippingMethod;
use App\Models\Utility\Message;
use App\Models\Utility\Script\Script;
use App\Models\Utility\Setting;
use App\Models\Warehouse\WarehouseProduct;
use App\Notifications\SMSNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Utility\Status;
use App\Models\Utility\Priority;
use App\Models\Customer\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Haruncpi\LaravelUserActivity\Traits\Loggable;

use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;
    use Loggable;

    protected $fillable = [
        "code",
        "series",
        "customer_id",
        "register_user_id",
        "order_datetime",
        "exit_datetime",
        "priority_id",
        "status_id",
        "cash_amount",
        "prepayment_amount",
        "loading_status_id",

        "shipping_method_id",
        "car_type_id",
        "delivery_point_type_id",
        "insurance_amount",
        "shipping_cost"
    ];
    private $base_code = 100000;

    public function code()
    {

        if ($this->code == "") {

            $order = Order::UpdateCode($this, "");
            return $code = ($order->series) . "/" . $order->code;
        }

        return $this->series . "/" . $this->code;
    }

    public static function UpdateCode($order, $series)
    {
        if ($series == "") {
            $code = $order->id;
            $order->code = $code;
            $order->series = "0";
            $order->save();
            return $order;
        }
        if ($order->series == $series) {
            return $order;
        }

        // تنها در صورتی کد سفارش را عوض می کنیم که سری  آن عوض شده باشد.
        $max_order_code = Order::
        where("series", $series)->max("code");
        $code = $max_order_code + 1;

        event(new OrderLogEvent($order, 35097, $series . "/" . $code));

        $order->code = $code;
        $order->series = $series;
        $order->save();
        return $order;
    }

    /***
     * @param $order
     * @param $product_id
     * @param $packing_type_id
     * گرفتن ردیف سفارش با توجه به کالا
     *
     * @return string
     */
    public function getCodeByProductRow($product_id, $packing_type_id)
    {
        //
        $order_list_packing_type = OrderListPackingType::where([
            "order_id" => $this->id,
            "packing_type_id" => $packing_type_id
        ])->first();
        $order_factor = OrderFactor::where([
            "order_id" => $this->id,
            "product_id" => $product_id,
            // "order_list_id" => $order_list_packing_type->order_list_id??0
        ])->first();

        $row = OrderFactor::
        where("order_id", $this->id)->
        where("id", "<=", $order_factor->id ?? 0)->
        count();

        return ($row) . "-" . $this->code();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, "customer_id", "id");

    }

    public function register_user()
    {
        return $this->belongsTo(User::class, "register_user_id", "id");

    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function shipping_method()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function car_type()
    {
        return $this->belongsTo(CarType::class);
    }

    public function delivery_point_type()
    {
        return $this->belongsTo(DeliveryPointType::class);
    }

    public function selling_type()
    {
        return $this->belongsTo(SellingType::class);
    }

    public function loadingProcess()
    {
        return $this->hasMany(LoadingProcesses::class, "order_id", "id");
    }

    public function getStatus()
    {

        if ($this->status->status_type_id == 351) {
            return "در انتظار " . $this->status->caption;
        }
        if ($this->status_id == 35090) {
            $list = ProductRequestFormForm::
            join("product_request_forms", "product_request_form_id", "product_request_forms.id")->
            join("forms", "product_request_form_form.form_id", "forms.id")->
            where("product_request_forms.order_id", $this->id)->
            where("product_request_forms.applicant_type_id", 30)->
            whereIn("forms.status_id", ProductRequestForm::getFormWaitingStatusList())->
            select("forms.code", "forms.status_id")->
            get();
            $msg = "";
            foreach ($list as $item) {
                $msg .= $item->code . "(" . ProductRequestForm::getFormWaitingStatusList("caption", $item->status_id) . ")" . ", ";
            }
            $msg = trim($msg, ", ");

            return $this->status->caption . " " . $msg;
        }

        return $this->status->caption;
    }

    public function getExistFormList()
    {

        return ProductRequestFormForm::
        join("product_request_forms", "product_request_form_id", "product_request_forms.id")->
        join("forms", "product_request_form_form.form_id", "forms.id")->
        where("product_request_forms.order_id", $this->id)->
        where("product_request_forms.applicant_type_id", 30)->
        whereIn("forms.status_id", ProductRequestForm::getFormWaitingStatusList())->get();
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, "payment_method_id", "id");
    }

    public function address()
    {
        return $this->belongsTo(Address::class, "address_id", "id");
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class, "priority_id", "id");
    }

    public function tariff_log()
    {
        return $this->belongsTo(TariffLog::class);
    }

    public function head_of_check_type()
    {
        return $this->belongsTo(HeadOfCheckType::class);
    }

    public function check_delivery_time_type()
    {
        return $this->belongsTo(CheckDeliveryTimeType::class);
    }

    public function order_date()
    {
        return jdate(Carbon::parse($this->order_datetime)->timestamp)->format('Y/m/d');

    }

    public function order_datetime()
    {
        return jdate(Carbon::parse($this->order_datetime)->timestamp)->format('H:i Y/m/d ');

    }

    public function create_date()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('Y/m/d ');

    }

    public function create_datetime()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');

    }

    public function loading_date()
    {
        if ($this->exit_datetime) {
            return jdate(Carbon::parse($this->exit_datetime)->timestamp)->format('Y/m/d');
        }

        return "ندارد";

    }

    public function delivery_datetime()
    {
        if ($this->delivery_datetime) {
            return jdate(Carbon::parse($this->delivery_datetime)->timestamp)->format('Y/m/d ');
        }
    }

    public function exit_status()
    {
        if ($this->exit_datetime) {
            return 460000200;
        }

        return 460000100;

    }

    public function orderList($hideBOMOrderlist = false)
    {

        $orderList = $this->hasMany(OrderList::class)->where("customer_id", $this->customer_id)->orderBy("product_id");
        if (!$hideBOMOrderlist) {
            // برای رکوردهایی که از BOM آمده
            $orderList = $orderList->where("customer_id", $this->customer_id)->whereNull("from_order_id");
        }

        return $orderList;
    }

    public function orderFactor()
    {
        return $this->hasMany(OrderFactor::class)->with("product");
    }

    public function consumedProduct()
    {
        return $this->hasMany(OrderConsumedProduct::class)->orderBy("product_id");
    }


    public function forms()
    {
        return $this->hasMany(Form::class, "order_id", "id")->
        where(["order_list_id" => 0, "production_card_id" => 0]);

    }

    public function description_request()
    {
        return $this->belongsTo(Message::class, "description_request_id", "id");
    }

    public function description_sheet()
    {
        return $this->belongsTo(Message::class, "description_sheet_id", "id");

    }

    public function order_payment_method()
    {
        return $this->hasMany(OrderPaymentMethod::class);
    }

    public function order_packing_forms()
    {
        return $this->hasMany(OrderPackingForm::class);
    }

    /**************************** */
    public function number_of_days_waiting()
    {
        $start_datetime = Carbon::create($this->order_datetime);
        $end_dateitme = Carbon::now();

        $diffInDays = $end_dateitme->diffInDays($start_datetime);

        return $diffInDays;
    }

    public static function search(
        $search, $order_by,
        $select = [
            "orders.code",
            "orders.priority_id",
            "status_id"
            ,
            "customer_id",
            "series",
            "order_datetime"
        ]
    )
    {

        return Order::
        join("customers", "customer_id", "customers.id")->
        when($search != "", function ($query) use ($search) {
            return $query->where("orders.code", "like", "%" . $search . "%")
                ->orWhere("customers.caption", "like", "%" . $search . "%");

        })->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");

            return $query->orderBy($order_by[0], $order_by[1]);

        })->when($select != [], function ($query) use ($select) {
            $select[] = "orders.id as id";

            return $query->select($select);
        });

    }

    public static function OffFloat($number, $round_off_number)
    {
        return (int)($number * pow(10, $round_off_number)) * pow(10, -$round_off_number);
    }

    public function calculate()
    {

        $list = ProductRequestForm::
        join("product_request_form_item", "product_request_form_id", "product_request_forms.id")->
        join("products", "products.id", "product_id")->
        select("products.weight", "amount_request", "product_request_form_item.product_id", "degree_id", "product_request_form_id")->
        selectRaw(" amount_request as total_row")->
        selectRaw(" weight* amount_request as total_row_weight")->
        selectRaw(" weight* amount_remaining as total_row_weight_remaining")->
        selectRaw(" amount_remaining as total_row_remaining")->
        selectRaw(" weight* amount_sent as total_row_weight_sent")->
        selectRaw(" amount_sent as total_row_sent")->
        selectRaw(" weight  as total_row_weight_end_inventory ")->
        where("order_id", $this->id)->
        where("product_request_forms.applicant_type_id", 30)->
        get();

        $total = 0;
        $total_weight = 0;
        $total_remaining = 0;
        $total_weight_remaining = 0;
        $total_weight_in_warehouse = 0;
        $total_weight_sent = 0;
        $unit = null;

        if (count($list) == 0) { // اگر هنوز درخواست کالا از انبار برای سفارش ثبت نشده است
            $list = OrderFactor::
            join("products", "products.id", "product_id")->
            select("products.weight", "product_id", "degree_id")->
            selectRaw(" carton as amount_request")->
            selectRaw(" carton as total_row")->
            selectRaw(" weight* carton as total_row_weight")->
            selectRaw(" weight* carton as total_row_weight_remaining")->
            selectRaw(" carton as total_row_remaining")->
            selectRaw("  0 as total_row_weight_sent")->
            selectRaw(" 0 as total_row_sent")->
            selectRaw(" weight  as total_row_weight_end_inventory ")->
            where("order_id", $this->id)->
            get();

        }

        foreach ($list as $item) {
            if (!$unit) {
                $p = Product::find($item->product_id);
                if ($p) {
                    $unit = $p->unit;
                }
            }
            $total += $item->total_row;
            $total_weight += $item->total_row_weight;
            $total_weight_sent += $item->total_row_weight_sent;
            $total_weight_remaining += $item->total_row_weight_remaining;
            $total_remaining += $item->total_row_remaining;


            $product_inventory = WarehouseProduct::getProductInventoryList([$item->product_id])[$item->product_id];

            ProductInventory::
            join("product_request_form_packing_type", "product_request_form_packing_type.packing_type_id", "product_inventory.packing_type_id")->
            where([
                "product_inventory.product_id" => $item->product_id,
                "product_request_form_id" => $item->product_request_form_id
            ])->
            groupBy("product_inventory.product_id")->
            first();
            $total_weight_in_warehouse +=
                min(
                    ($product_inventory * $item->weight),
                    $item->total_row_weight_remaining
                );
        }

        $this->total = round($total);
        $this->total_weight = round($total_weight);
        $this->total_weight_sent = round($total_weight_sent);
        $this->total_remaining = round($total_remaining);
        $this->total_weight_remaining = round($total_weight_remaining);
        $this->unit = $unit;

        $this->total_weight_in_warehouse = round($total_weight_in_warehouse);

        if ($this->total_weight == 0) {
            $this->total_weight_sent_raito = "0";
            $this->total_weight_in_warehouse_raito = "0";
        } else {

            $this->total_weight_sent_raito = round(($this->total_weight - $this->total_weight_remaining) / $this->total_weight, 2) * 100;

        }

        if ($this->total_weight_remaining != 0) {
            $this->total_weight_in_warehouse_raito = round(($this->total_weight_in_warehouse) / $this->total_weight_remaining, 2) * 100;
        } else {
            $this->total_weight_in_warehouse_raito = 0;
        }


        return $this;
    }

    public function calculate2()
    {

        $order_list = OrderList::
        join("products", "products.id", "order_list.product_id")->
        //  join( "product_inventory", "products.id", "product_inventory.product_id" )->
        select("products.weight", "order_list.amount", "product_id")->
        selectRaw(" weight* carton as total_row_wieght")->
        selectRaw(" weight* amount_remaining as total_row_weight_remaining")->
        selectRaw(" weight* amount_sent as total_row_weight_sent")->
        selectRaw(" weight  as total_row_weight_end_inventory ")->
        where("order_id", $this->id)->
        where("from_order_list_id", null)->
        get();

        $total_weight = 0;
        $total_weight_remaining = 0;
        $total_weight_in_warehouse = 0;
        $total_weight_sent = 0;

        foreach ($order_list as $item) {
            $total_weight += $item->total_row_wieght;
            $total_weight_sent += $item->total_row_weight_sent;
            $total_weight_remaining += $item->total_row_weight_remaining;
            $total_weight_in_warehouse +=
                min(
                    $item->product->getInventory() * $item->wieght,
                    $item->total_row_weight_remaining
                );
        }

        $this->total_weight = round($total_weight);
        $this->total_weight_sent = round($total_weight_sent);
        $this->total_weight_remaining = round($total_weight_remaining);

        $this->total_weight_in_warehouse = round($total_weight_in_warehouse);

        if ($this->total_weight == 0) {
            $this->total_weight_sent_raito = "0";
            $this->total_weight_in_warehouse_raito = "0";
        } else {

            $this->total_weight_sent_raito = round(($this->total_weight - $this->total_weight_remaining) / $this->total_weight, 2) * 100;

        }

        if ($this->total_weight_remaining != 0) {
            $this->total_weight_in_warehouse_raito = round(($this->total_weight_in_warehouse) / $this->total_weight_remaining, 2) * 100;
        } else {
            $this->total_weight_in_warehouse_raito = 0;
        }


        return $this;
    }

    public function order_logs()
    {
        return $this->hasMany(OrderLog::class);
    }

    public function log($message = "", $status_id = 0, $customer_message = "", $event_id = null)
    {

        1 / 0;


    }

    /**
     * بروز رسانی نوع فاکتور و درصد افزایش قیمت در فاکتور های غیر رسمی
     * @param Order $order
     * @return object
     */
    public static function SellingTypeRefresh(Order $order)
    {
        $allSellingAmount = $order->customer->getAllSellingAmount();
        $informal_percent = $allSellingAmount["selling_type"][2]["percent"];

        //
        $order->selling_type_id = 2;// غیر رسمی
        if ($informal_percent > $order->customer->percent_max_informal_purchase || $order->customer->percent_max_informal_purchase <= 0) {
            $order->selling_type_id = 1; //رسمی
        }
        // بروز رسانی درصد افزایش قیمت در خرید های غیررسمی
        $order->increase_percentage_in_informal_sale = $order->customer->increase_percentage_in_informal_sale;

        $order->save();

        return $order;
    }

    public function factorRefresh()
    {

        $this->orderFactor()->delete();
        $this->orderList()->where("order_kind_id", 2)->delete();
        $this->orderList()->where("erp_status_id", -100)->delete();
        $this->orderList()->whereNull("customer_id")->delete();

        OrderList::join("products", "product_id", "products.id")->
        where("order_id", $this->id)->
        where("active_status_id", 1210)->delete();

        // اگر مشتری سطح دو است باید تعرفه سطح یک را برداریم.
        $tariff = $this->customer->parent_id ? $this->customer->parent->tariff : $this->customer->tariff;

        // ذخیره کردن لاگ تعرفه برای محاسبه مبلغ فاکتور در برگ خروج
        $tariff_log = TariffLog::where([
            "tariff_id" => $tariff->id,
        ])->
        whereIn("status_id", [520100530, 520100540])-> //  آپلود لیست | قیمت گذاری
        orderByDesc("id")->first();
        if ($tariff_log) {
            $this->tariff_log_id = $tariff_log->id;
            $this->save();

        } else {
            return ["result" => false, "error" => "آخرین تغیرات تعرفه یافت نشد، لطفا با پشتیبانی تماس بگیرید."];
        }

        foreach ($this->orderList as $item) {


            $orderFactor = OrderFactor::create($item->toArray());
            $orderFactor->order_list_id = $item->id;
            $orderFactor->customer_id = $this->customer->id;

            // لیست بسته بندی های مجاز
            $packing_type_ids = OrderListPackingType::where([
                "order_id" => $item->order_id,
                "order_list_id" => $item->id,
            ])->pluck("packing_type_id")->toArray();

            $packing_type_ids[] = -1;

            $tariff_product = ProductTariff::where(
                [
                    "product_id" => $item->product_id,
                    "degree_id" => $item->degree_id,
                    "tariff_id" => $tariff->id
                ])->
            whereIn("packing_type_id", $packing_type_ids)->
            first();

            if (!$tariff_product) {
                $product=$item->product;
                $item->delete();
                return ["result" => false, "error" => "کالای " . $product->fullCaption() . " در تعرفه وجود ندارد، این ردیف از لیست سفارش حذف گردید، لطفا تعرفه را اصلاح نمایید و مجدد تلاش کنید."];
            }


            $orderFactor->fea = $tariff_product->fea;
            if ($this->selling_type_id == 2) {
                // بعد از محاسبه مبلغ واحد مقدار اعشار آن حذف می شود.
                $orderFactor->fea = round($orderFactor->fea * (1 + $this->increase_percentage_in_informal_sale / 100));
            }

            $orderFactor->price = $orderFactor->fea * $item->carton;

            // اگر فروش غیر رسمی است، مالیات صفر می باشد.
            $orderFactor->tax_percent = $this->selling_type_id == 2 ? 0 : $tariff_product->tax + $tariff_product->fare;

            $orderFactor->increase_percentage_deadline_per_day = $tariff_product->increase_percentage_deadline_per_day;

            $orderFactor->save();
        }

        return [
            "result" => 1,
        ];

    }

    public function updatePercentOff()
    {

        // حذف رکوردهای مربوط به تخفیف حجمی
        $this->orderList()->where("order_kind_id", 2)->delete();

        foreach ($this->orderFactor as $orderFactorItem) {

            $offer = $orderFactorItem->product->gerCurrentOffer($this->customer, $orderFactorItem->carton);

            if ($offer) {
                $orderFactorItem->offer_id = $offer->id;
                $orderFactorItem->percent_off = $offer->percent_off;
                $orderFactorItem->percent_off_price = round($offer->percent_off * $orderFactorItem->price / 100);

                if ($offer->percent_free > 0 && $offer->product_free_id != 0 &&
                    round($offer->percent_free * $orderFactorItem->carton / 100) > 0) {


                    // New Order List
                    $new_order_list = OrderList::create($orderFactorItem->orderListItem->toArray());

                    $new_order_list->carton = round($offer->percent_free * $orderFactorItem->carton / 100);
                    $new_order_list->amount_remaining = $new_order_list->carton;
                    $new_order_list->amount = $new_order_list->carton * $new_order_list->number_in_carton;
                    $new_order_list->order_kind_id = 2; // // تخفیف حجمی
                    $new_order_list->product_id = $offer->product_free_id;
                    $new_order_list->save();

                    // New Order Factor
                    $new_order_factor = OrderFactor::create($orderFactorItem->toArray());
                    $new_order_factor->order_kind_id = 2; // تخفیف حجمی
                    $new_order_factor->fea = $this->customer->tariff->currency->free_price;
                    $new_order_factor->carton = $new_order_list->carton;
                    $new_order_factor->price = $new_order_factor->fea * $new_order_list->carton;
                    $new_order_factor->tax_percent = $orderFactorItem->tax_percent;
                    $new_order_factor->product_id = $offer->product_free_id;
                    $new_order_factor->save();

                }
            }

            $orderFactorItem->special_off_price = 0;
            $orderFactorItem->save();

        }
    }

    public function updatePercentCash()
    {

        // محاسبه تخفیف نقدی فاکتور
        foreach ($this->orderFactor()->where("order_kind_id", "!=", 2)->get() as $orderFactorItem) {
            if ($this->payment_method_id != 100) {
                $orderFactorItem->cash_off_percent = 0;
                $orderFactorItem->cash_off_price = 0;
            } else {
                $orderFactorItem->cash_off_percent = $this->customer->cash_off_percent;
                $orderFactorItem->cash_off_price = round($orderFactorItem->cash_off_percent / 100 *
                    ($orderFactorItem->price - $orderFactorItem->percent_off_price));
            }

            $orderFactorItem->special_off_price = 0;
            $orderFactorItem->save();

        }
    }

    public function updateRoundOff()
    {

        // محاسبه تخفیف حذف اعشاری: به ازای هر سطر با توجه به تنظیمات n رقم اعشار را از مبلغ نهایی حذف و به عنوان تخصیف در نظر می گیرد.
        $round_off_number = Setting::getIntegerValue("round_off_for_total_price_in_sale");
        foreach ($this->orderFactor()->where("order_kind_id", "!=", 2)->get() as $orderFactorItem) {

            $total_price_without_float = self::OffFloat($orderFactorItem->total_price, $round_off_number);

            $orderFactorItem->round_off_price = $orderFactorItem->total_price - $total_price_without_float;
            $orderFactorItem->save();

        }

    }

    /**
     * @return محاسبه تخیف خرید رسمی
     */
    public function updateFormalOff()
    {

        $percent = 0;
        $total_tax_price = OrderFactor::where("order_id", $this->id)->
        addSelect(DB::raw("  sum(tax_price) as value"))->
        first()->value;
        if ($this->selling_type_id == 1) {
            $percent = $this->customer->percent_tax_off_in_formal_factor;
        }

        $f_price = $percent / 100 * $total_tax_price;
        $total_price = OrderFactor::where("order_id", $this->id)->
        addSelect(DB::raw("  sum(total_price) as value"))->
        first()->value;
        // محاسبه تخفیف خرید رسمی
        $this->percent_tax_off_in_formal_factor = $percent;
        $this->save();

        foreach ($this->orderFactor as $orderFactorItem) {
            // همه سطر های در مبلغ تخفیف رسمی ضرب می شوند.

            $orderFactorItem->tax_off_in_formal_factor_price =
                $total_price == 0 ?
                    0 :
                    $orderFactorItem->total_price / $total_price * $f_price;
            $orderFactorItem->save();

        }
    }

    public function updateSpecialOff()
    {

        $x = OrderFactor::where("order_id", $this->id)->
        addSelect(DB::raw(" (sum(total_price_with_tax) / sum(total_price)) as value"))->
        first()->value;
        $s_price = $this->special_off_price / $x;

        $total_price = OrderFactor::where("order_id", $this->id)->
        addSelect(DB::raw("  sum(total_price) as value"))->
        first()->value;


        foreach ($this->orderFactor as $orderFactorItem) {

            $orderFactorItem->special_off_price = $orderFactorItem->total_price_with_tax / $total_price * $s_price;
            $orderFactorItem->save();

        }
    }

    public function updatePrice()
    {

        foreach ($this->orderFactor as $orderFactorItem) {

            $orderFactorItem->total_off_price =
                $orderFactorItem->percent_off_price +
                $orderFactorItem->cash_off_price +
                $orderFactorItem->special_off_price +
                $orderFactorItem->tax_off_in_formal_factor_price +
                $orderFactorItem->round_off_price;

            $orderFactorItem->total_price = $orderFactorItem->price - $orderFactorItem->total_off_price;

            $orderFactorItem->tax_price = $orderFactorItem->total_price * $orderFactorItem->tax_percent / 100;
            $orderFactorItem->total_price_with_tax = $orderFactorItem->total_price + $orderFactorItem->tax_price;
            $orderFactorItem->save();

        }
    }

    /*
     * بروز رسانی قیمت با توجه به درصد افزایش قیمت روز
     */
    public function updateFeaIncrease()
    {
        //تا بررسی نهایی غیر فعال شد.
//        $sum_order_factor_with_tax = OrderFactor::where("order_id", $this->id)->sum("total_price_with_tax");
//
//        foreach ($this->orderFactor as $orderFactorItem) {
//            $increase_deadline_per_day_fea[$orderFactorItem->id] = 0;
//        }
//        foreach ($this->order_payment_method as $paymentMethod) {
//
//            // اگر چکی پرداخت می کنند.
//            if ($paymentMethod->check_delivery_days && $paymentMethod->check_delivery_days > 0) {
//
//                $payment_ratio = $paymentMethod->amount / $sum_order_factor_with_tax;
//
//                foreach ($this->orderFactor as $orderFactorItem) {
//
//                    // مبلغ واحد * نسبت پرداخت چکی * تعداد روز * درصد روکش
//                    $increase_deadline_per_day_fea[$orderFactorItem->id]+=
//                        $orderFactorItem->fea *
//                        $payment_ratio *
//                        $paymentMethod->check_delivery_days *
//                        $orderFactorItem->increase_percentage_deadline_per_day / 100;
//
//
//
////                 return   "increase_deadline_per_day_fea=".
////                     $orderFactorItem->fea ."*.".
////                     $payment_ratio ."*".
////                     $paymentMethod->check_delivery_days ."*".
////                     $orderFactorItem->increase_deadline_per_day." / 100";
//
//                }
//
//            }
//        }
//
//        //مبلع افزایش قیمت را به ازای هر ردیف اضافه می کنیم.
//        foreach ($this->orderFactor as $orderFactorItem) {
//            $orderFactorItem->fea+= $increase_deadline_per_day_fea[$orderFactorItem->id] ;
//            $orderFactorItem->increase_deadline_per_day+= $increase_deadline_per_day_fea[$orderFactorItem->id] ;
//            $orderFactorItem->price = $orderFactorItem->fea * $orderFactorItem->carton;
//            $orderFactorItem->save();
//        }

    }

    public function allowEditPreFactor($status_id = null)
    {
        return !in_array($status_id ?? $this->status_id, [304010, 304020, 304030]);
    }

    ############################### OrderPermission #########################
    public function nextOrderPermission($comment = "", $customer_comment = "", $user_id = null)
    {
        $current_status_id = $this->status_id;
        $list_not_allow = [35030, 35040, 35050, 35060, 35070];
        if (in_array($current_status_id, $list_not_allow)) {
            return 0;
        }
        $orderPTItem = OrderPermissionType::where("order_status_id", $current_status_id)->first();
        $priority_order = 0;
        if ($orderPTItem) {
            $priority_order = $orderPTItem->priority_order;
        }

        $permission = OrderPermissionCustomer::
        join("order_permission_types", "order_permission_type_id", "order_permission_types.id")->
        where("customer_id", $this->customer_id)->
        where("priority_order", ">", $priority_order)->
        orderBy("priority_order")->
        select("order_permission_types.*")->
        first();


        $order_status_id = 35030; // در انتظار آماده سازی
        if ($permission) {
            $order_status_id = $permission->order_status_id;
        }
        $this->status_id = $order_status_id;
        $this->save();


        // وضعیت قبل از تایید
        switch ($current_status_id) {
            case 304010: // ثبت پیش نویس توسط مشتری
                $this->sendSms("ordersms304010");
                $this->order_datetime = Carbon::now();
                $this->save();
                break;

        }

        // وضعیت بعد از تایید
        switch ($order_status_id) {
            case 304030: // در انتظار تایید پیش فاکتور توسط مشتری
                $this->sendSms("ordersms304030");
                break;
            case 35030:// در اننتظار آماده سازی

// اگر حداقل یکی از سطر ها پردازش شده، یغنی به صورت دستی پردازش صورت گرفته و لازم نیست که پردازشی توسط کد R انجام شود.
                $count = OrderList::where("order_id", $this->id)->where(["status_id" => 301])->count();

                OrderList::where("order_id", $this->id)->update([
                    "status_id" => $count == 0 ? 300 : 301, // در انتظار پردازش // پردازش نشده
                    "erp_status_id" => 305
                ]);
                foreach ($this->orderList as $item) {
                    $item->log("", $user_id);
                }


                // آیا انبار جهت خروج کالا، نیاز به ثبت فرم مجوز بارگیری توسط فروش دارد؟
                $does_sales_need_to_register_a_loading_permit_form = Setting::getIntegerValue("does_sales_need_to_register_a_loading_permit_form");
                if (!$does_sales_need_to_register_a_loading_permit_form) {
                    // ارسال درخواست کالا از انبار
                    ProductRequestForm::newRequest(
                        $this,
                        $this->customer_id,
                        30,
                        1,
                        null,
                        $this->delivery_datetime,
                        ""
                    );
                } else {
                    // در بخش مجوز های بارگیری به صورت دستی درخواست ثبت می کنند.
                }


                $this->sendSms("ordersms35030");


                break;


        }

        if ($permission) {
            $this->sendSms("customerorderchangealert", ["order_permission_type_id" => $permission->id]);
        }

        event(new OrderLogEvent($this, $current_status_id, $comment, $customer_comment, null, $user_id));

    }

    public function previousOrderPermission($message = "", $customer_message = "")
    {
        $list_not_allow = [35030, 35040, 35050, 35060, 35070];
        if (in_array($this->status_id, $list_not_allow)) {
            return 0;
        }

        $orderPTItem = OrderPermissionType::where("order_status_id", $this->status_id)->first();

        if (!$orderPTItem) {
            return false;
        }
        $priority_order = $orderPTItem->priority_order;
        $permission = OrderPermissionCustomer::
        join("order_permission_types", "order_permission_type_id", "order_permission_types.id")->
        where("customer_id", $this->customer_id)->
        where("priority_order", "<", $priority_order)->
        orderByDesc("priority_order")->
        first();

        $order_status_id = 304010;
        if ($permission) {
            $order_status_id = $permission->order_status_id;
        }
        $this->status_id = $order_status_id;
        $this->save();


        return true;
    }

    ############################## End OrderPermission ####################


    public function sendSms($smsTemplate, $data = null)
    {

        $company_name = Setting::getStringValue("company_name");

        $token = $this->code();
        $token2 = null;
        $token3 = null;
        $token10 = $company_name;
        $token20 = $this->customer->caption ?? "";

        switch ($smsTemplate) {

            case "ordersmsexistform": // صدور برگ خروج
                if ($this->customer->send_exit_form_sms) { // اگر اجازه ارسال پیامک به مشتری را دارد
                    $token10 = $this->customer->caption ?? "";
                    $token20 = Setting::getStringValue("software_name");
                    $token2 = "*";
                    $token = "_APP_NAME_";

                    if (isset($data["form"])) {
                        $token2 = $data["form"]->getNumberCode();
                        $token = "_APP_NAME_/DCEF/" . $data["form"]->id . "/" . $data["form"]->random;
                    }


                    $address = $this->customer->getDefaultAddress();
                    if ($address) {
                        Notification::send("00" . ($address->mobile_country->area_code ?? "98") . $address->mobile,
                            new SMSNotification($smsTemplate, $token, $token2, $token3, $token10, $token20));
                    }
                }
                break;
            case "ordersms304030":
                if ($this->customer->send_order_sms) { // اگر اجازه ارسال پیامک به مشتری را دارد
                    $token10 = $this->customer->caption ?? "";
                    $token20 = Setting::getStringValue("software_name");
                    $address = $this->customer->getDefaultAddress();
                    $token3 = "_APP_NAME_/DCOF/" . $this->id;

                    if ($address) {
                        Notification::send("00" . ($address->mobile_country->area_code ?? "98") . $address->mobile,
                            new SMSNotification($smsTemplate, $token, $token2, $token3, $token10, $token20));
                    }
                }
                break;
            case "customerorderchangealert":
                $token2 = $this->getStatus();
                $token10 = Setting::getStringValue("software_name");
                $post_ids = OrderPermissionCustomer::
                whereNotIn("send_sms_for_post_id", Post::InvalidPost())->
                where("customer_id", $this->customer_id)->
                whereNotNull("send_sms_for_post_id")->
                where("order_permission_type_id", $data["order_permission_type_id"])->
                pluck("send_sms_for_post_id")->
                toArray();
                $post_ids[] = 0;

                $post_users = PostUser::whereIn("post_id", $post_ids)->get();

                foreach ($post_users as $post_user) {

                    Notification::send("00" . ($address->mobile_country->area_code ?? "98") . $post_user->worker->mobile,
                        new SMSNotification($smsTemplate, $token, $token2, $token3, $token10, $token20));

                }

                break;
        }


    }

    public function getExitFormList($form_status_ids = [])
    {
        return $list = Form::join("product_request_form_form", "forms.id", 'form_id')->
        join("product_request_forms", "product_request_form_id", "product_request_forms.id")->
        where("product_request_forms.order_id", $this->id)->
        where("product_request_forms.applicant_type_id", 30)->
        when($form_status_ids != [], function ($query) use ($form_status_ids) {
            return $query->whereIn("forms.status_id", $form_status_ids);
        })->
        select("forms.*")->
        addSelect("product_request_forms.code as product_request_form_code", "product_request_form_id as product_request_form_id")->
        get();
    }

    public function get_processed_type()
    {

        ///  اسکریپت فعال است: 100 پردازش توسط  دستیار
        /// اسکریپت فعال است، عدم نیاز به پردازش توسط دستیار 200
        /// اسکریپت غیر فعال است و آیا بعدا پردازش شود بله: 300
        /// حالت عادی 400
        // بررسی اینکه 	کارشناس دیجیتال برنامه ریزی دیاکو فعال است یا خیر
        $script = Script::find(33);
        $active_digital_planning_specialist = $script && $script->active_status_id == 1200 ? 1 : 0;
        $order_log = OrderLog::where("order_id", $this->id)->
        whereIn("event_id", [35100, 35103, 35105, 35104])->pluck("event_id", "event_id");

        if ($active_digital_planning_specialist) {
            if (isset($order_log[35103]) && !isset($order_log[35105])) {
                return 200; // عدم نیاز به پردازش توسط دستیار دیجیتال
            } else {
                $order_log = OrderLog::where("order_id", $this->id)->
                whereIn("event_id", [35103, 35105,35104])->orderByDesc("id")->first();
                if ($order_log && $order_log->event_id == 35103) {
                    return 200;
                }
                else if ($order_log && $order_log->event_id == 35104) {
                    return 101; // پردازش توسط دستیار دیجیتال به خطا خورده است
                }
                else {
                    return 100; // درخواست تویط دستیار پردازش می شود.
                }
            }
        }

        if (isset($order_log[35100])) {
            return 300; // دستیار دیجیتال فعال نیست و بعدا پردازش می شود.
        } else {
            return 400; // پردازش توسط اپراتور
        }
    }

    /***
     * گرفتن فاکتور از روی برگ خروج(فروش)
     * @param Order $order
     * @param Form $form
     * @return array
     */
    public static function GetFactorFromExitFrom(Order $order, Form $form)
    {

        $seller = Setting::getStringValue("company_name");
        $national_code = Setting::getStringValue("national_code");
        $economic_number = Setting::getStringValue("economic_number");
        $text_footer_per_factor = Setting::getStringValue("text_footer_per_factor");
        $round_off_number = Setting::getIntegerValue("round_off_for_total_price_in_sale");

        $form_factor = [];
        // گرفتن تعرفه
        foreach ($form->item()->groupBy("product_id")->groupBy("packing_type_id")->groupBy("degree_id")->get() as $form_item) {

            $form_factor_row_result = self::GetPriceFromFormItem($form_item, $order, "group_by_product", $round_off_number);
            if ($form_factor_row_result['result']) {
                $form_factor_row = $form_factor_row_result["factor_row"];
            } else {
                return $form_factor_row_result;
            }
            $key = $form_item->product_id . "_" . $form_item->packing_type_id . "_" . $form_item->degree_id;
            if (!isset($form_factor[$key])) {
                $form_factor[$key] = [
                    "item" => $form_item,
                    "price" => $form_factor_row["price"],
                    "amount" => $form_factor_row["amount"],
                    "fea" => $form_factor_row["fea"],
                    "total_off_price" => $form_factor_row["total_off_price"],
                    "total_price" => $form_factor_row["total_price"],
                    "tax_percent" => $form_factor_row["tax_percent"],
                    "tax_price" => $form_factor_row["tax_price"],
                    "total_price_with_tax" => $form_factor_row["total_price_with_tax"],
                    "product_service_caption" => $form_factor_row["product_service_caption"],
                    "product_service_code" => $form_factor_row["product_service_code"],
                ];
            } else {
                $form_factor[$key]["amount"] += $form_factor_row["amount"];
                $form_factor[$key]["price"] += $form_factor_row["price"];
                $form_factor[$key]["total_off_price"] += $form_factor_row["total_off_price"];
                $form_factor[$key]["total_price"] += $form_factor_row["total_price"];
                $form_factor[$key]["tax_price"] += $form_factor_row["tax_price"];
                $form_factor[$key]["total_price_with_tax"] += $form_factor_row["total_price_with_tax"];
            }

        }


        return [
            "text_footer_per_factor" => $text_footer_per_factor,
            "seller" => $seller,
            "national_code" => $national_code,
            "economic_number" => $economic_number,
            "form_factor" => $form_factor,
            "result" => true
        ];
    }

    public static function GetPriceFromFormItem_old(FormItem $form_item, Order $order, $amount_type = "group_by_product", $round_off_number = 6)
    {

        $tariff_log_id = $order->tariff_log_id;
        $order_list_packing_types = OrderListPackingType::
        where(["order_id" => $order->id, "packing_type_id" => $form_item->packing_type_id])->
        first();

        // پیدا کردن ردیف در سفارش
        $order_factor = OrderFactor::where([
            "order_id" => $order->id,
            "product_id" => $form_item->product_id,
            "degree_id" => $form_item->degree_id,
            "order_list_id" => $order_list_packing_types->order_list_id ?? 0
        ])->first();

        if (!$order_factor) {
            // اگر درجه معادل وجود نداشت یکی از درجه ها را انتخاب می کنیم.
            $order_factor = OrderFactor::where([
                "order_id" => $order->id,
                "product_id" => $form_item->product_id,
            ])->first();

            if (!$order_factor) {

                // ممکن است، یک برگ خروج برای سفارش های مختلف باشد، بنابراین اگر هیچ ردیف متناظری یافت نشد، آن را در سفارش های دیگر برگ خروچ بررسی می کنیم.

                $other_product_request_forms = ProductRequestFormForm::
                join("product_request_forms", "product_request_forms.id", "product_request_form_id")->
                where([
                    "form_id" => $form_item->form_id,
                ])->
                where("order_id", "!=", $order->id)->
                get();

                $exist_cart = false;
                foreach ($other_product_request_forms as $other_product_request_form) {

                    $order_list_packing_types_2 = OrderListPackingType::
                    where(["order_id" => $other_product_request_form->order_id, "packing_type_id" => $form_item->packing_type_id])->
                    first();

                    // پیدا کردن ردیف در سفارش
                    $order_factor_2 = OrderFactor::where([
                        "order_id" => $other_product_request_form->order_id,
                        "product_id" => $form_item->product_id,
                        "degree_id" => $form_item->degree_id,
                        "order_list_id" => $order_list_packing_types_2->order_list_id ?? 0
                    ])->first();

                    if (!$order_factor_2) {
                        // اگر درجه معادل وجود نداشت یکی از درجه ها را انتخاب می کنیم.
                        $order_factor_2 = OrderFactor::where([
                            "order_id" => $order->id,
                            "product_id" => $form_item->product_id,
                        ])->first();
                    }

                    if (!$order_factor_2) {
                        continue;
                    }
                    $exist_cart = true;
                    $tariff_log_id = $other_product_request_form->order->tariff_log_id;
                    break;
                }

                if (!$exist_cart) {
                    return ["message" => "برای کالای " . $form_item->product->caption . " با بسته بندی " . $form_item->packing_type->caption . " ردیف متناظری در سفارش یافت نشد، لطفا با پشتیبانی تماس بگیرید.",
                        "error" => "برای کالای " . $form_item->product->caption . " با بسته بندی " . $form_item->packing_type->caption . " ردیف متناظری در سفارش یافت نشد، لطفا با پشتیبانی تماس بگیرید.",
                        "result" => false
                    ];
                }
            }
        }

// پیدا کردن ردیف متناظر در تعرفه
        $product_tariff_log = ProductTariffLog::where(
            [
                "product_id" => $form_item->product_id,
                "degree_id" => $form_item->degree_id,
                "tariff_log_id" => $order->tariff_log_id
            ])->
        first();

        if (!$product_tariff_log) {

            $master_degree = Degree::where([
                "goods_kind_id" => $form_item->product->goods_kind_id,
                "degree_type_id" => 1
            ])->first();
            if (!$master_degree) {
                return [
                    "message" => "درجه اصلی  رسته  " . $form_item->product->goods_kind->caption . " در تعرفه " . $order->tariff_log->tariff->caption . " وجود ندارد. ",
                    "error" => "درجه اصلی  رسته  " . $form_item->product->goods_kind->caption . " در تعرفه " . $order->tariff_log->tariff->caption . " وجود ندارد. ",
                    "result" => false
                ];

            }
            $product_tariff_log = ProductTariffLog::where(
                [
                    "product_id" => $form_item->product_id,
                    "degree_id" => $master_degree->id,
                    "tariff_log_id" => $tariff_log_id
                ])->
            first();

            if (!$product_tariff_log) {
                return [
                    "message" => "لیست تعرفه برای سفارش یافت نشد، امکان دریافت فاکتور وجود ندارد" . "<br/>کد کالا:"
                        . ($form_item->product->code ?? "***") . "<br/> درجه" . ($master_degree->caption ?? "***") . "<br/>تعرفه " . ($order->tariff_log->tariff->id . " - شماره لاگ: " . $order->tariff_log_id),
                    "error" => "لیست تعرفه برای سفارش یافت نشد، امکان دریافت فاکتور وجود ندارد" . "<br/>کد کالا:"
                        . ($form_item->product->code ?? "***") . "<br/> درجه" . ($master_degree->caption ?? "***") . "<br/>تعرفه " . ($order->tariff_log->tariff->id . " - شماره لاگ: " . $order->tariff_log_id),
                    "result" => false
                ];
            }
        }


        $form_factor_row["item"] = $form_item;
        $form_factor_row["product_service_code"] = $product_tariff_log->service_id ?
            $product_tariff_log->service->code :
            $product_tariff_log->product->code;

        $form_factor_row["product_service_caption"] = $product_tariff_log->getCaption();

        // محصول درجه در تعرفه یافت شد
        $form_factor_row["fea"] = $product_tariff_log->fea;
        if ($order->selling_type_id == 2) {
            // رند کردن مبلغ در فروش های غیررسمی
            if ($order->customer->round_fee_in_informal_sale) {
                $form_factor_row["fea"] = round($form_factor_row["fea"] * (1 + $order->increase_percentage_in_informal_sale / 100));
            } else {
                $form_factor_row["fea"] = $form_factor_row["fea"] * (1 + $order->increase_percentage_in_informal_sale / 100);
            }
        }

        switch ($amount_type) {
            case "group_by_product":
                // جمع کل همه ردیف های مشاده در بسته بندی و درجه
                $form_factor_row["amount"] =
                    FormItem::where([
                        "form_id" => $form_item->form->id,
                        "product_id" => $form_item->product_id,
                        "packing_type_id" => $form_item->packing_type_id,
                        "degree_id" => $form_item->degree_id,
                    ])->
                    sum("amount");
                break;
            case "form_item":
                $form_factor_row["amount"] =
                    FormItem::where([
                        "id" => $form_item->id,
                        "product_id" => $form_item->product_id,
                        "packing_type_id" => $form_item->packing_type_id,
                        "degree_id" => $form_item->degree_id,
                    ])->
                    sum("amount");
                break;
        }


        $form_factor_row["price"] = $form_factor_row["fea"] * round($form_factor_row["amount"], 7);

        //مبلغ تخفیف
        $form_factor_row["total_off_price"] = $order_factor->total_off_price / $order_factor->price * $form_factor_row["price"];

        $form_factor_row["total_price"] = $form_factor_row["price"] - $form_factor_row["total_off_price"];

        // مبلغ تخفیف اعشاری
        $total_price_without_float = Order::OffFloat($form_factor_row["total_price"], $round_off_number);
        $form_factor_row["total_off_price"] += $form_factor_row["total_price"] - $total_price_without_float;
        $form_factor_row["total_price"] = $total_price_without_float;

        $form_factor_row["tax_percent"] = $order->selling_type_id == 2 ? 0 : $product_tariff_log->tax + $product_tariff_log->fare;
        $form_factor_row["tax_price"] = $form_factor_row["total_price"] * $form_factor_row["tax_percent"] / 100;

        $form_factor_row["total_price_with_tax"] = $form_factor_row["total_price"] + $form_factor_row["tax_price"];

        return ["result" => true, "factor_row" => $form_factor_row];


    }

    public static function GetPriceFromFormItem(FormItem $form_item, Order $order, $amount_type = "group_by_product", $round_off_number = 6)
    {


        $order_list_packing_types = OrderListPackingType::
        where(["order_id" => $order->id, "packing_type_id" => $form_item->packing_type_id])->
        first();

        // پیدا کردن ردیف در سفارش
        $order_factor = OrderFactor::where([
            "order_id" => $order->id,
            "product_id" => $form_item->product_id,
            "degree_id" => $form_item->degree_id,
            "order_list_id" => $order_list_packing_types->order_list_id ?? 0
        ])->first();

        if (!$order_factor) {
            // اگر درجه معادل وجود نداشت یکی از درجه ها را انتخاب می کنیم.
            $order_factor = OrderFactor::where([
                "order_id" => $order->id,
                "product_id" => $form_item->product_id,
            ])->first();

            if (!$order_factor) {

                // ممکن است، یک برگ خروج برای سفارش های مختلف باشد، بنابراین اگر هیچ ردیف متناظری یافت نشد، آن را در سفارش های دیگر برگ خروچ بررسی می کنیم.

                $other_product_request_forms = ProductRequestFormForm::
                join("product_request_forms", "product_request_forms.id", "product_request_form_id")->
                where([
                    "product_request_form_form.form_id" => $form_item->form_id,
                ])->
                where("order_id", "!=", $order->id)->
                get();

                $exist_cart = false;
                foreach ($other_product_request_forms as $other_product_request_form) {

                    $order_list_packing_types_2 = OrderListPackingType::
                    join("order_list", "order_list.id", "=", "order_list_packing_type.order_list_id")->
                    where([
                        "order_list.order_id" => $other_product_request_form->order_id,
                        "product_id" => $form_item->product_id,
                        "packing_type_id" => $form_item->packing_type_id])->
                    first();

                    // پیدا کردن ردیف در سفارش
                    $order_factor_2 = OrderFactor::where([
                        "order_id" => $other_product_request_form->order_id,
                        "product_id" => $form_item->product_id,
                        "degree_id" => $form_item->degree_id,
                        "order_list_id" => $order_list_packing_types_2->order_list_id ?? 0
                    ])->first();

                    if (!$order_factor_2) {
                        // اگر درجه معادل وجود نداشت یکی از درجه ها را انتخاب می کنیم.
                        $order_factor_2 = OrderFactor::where([
                            "order_id" => $other_product_request_form->order_id,
                            "product_id" => $form_item->product_id,
                        ])->first();
                    }

                    if (!$order_factor_2) {
                        continue;
                    }
                    $exist_cart = true;

                    $order_factor = $order_factor_2;
                    $order_list_packing_types = $order_list_packing_types_2;
                    $order = Order::find($other_product_request_form->order_id);
                    break;
                }

                if (!$exist_cart) {
                    return ["message" => "برای کالای " . $form_item->product->caption . " با بسته بندی " . $form_item->packing_type->caption . " ردیف متناظری در سفارش یافت نشد، لطفا با پشتیبانی تماس بگیرید.",
                        "error" => "برای کالای " . $form_item->product->caption . " با بسته بندی " . $form_item->packing_type->caption . " ردیف متناظری در سفارش یافت نشد، لطفا با پشتیبانی تماس بگیرید.",
                        "result" => false
                    ];
                }
            }
        }

// پیدا کردن ردیف متناظر در تعرفه
        $product_tariff_log = ProductTariffLog::where(
            [
                "product_id" => $form_item->product_id,
                "degree_id" => $form_item->degree_id,
                "tariff_log_id" => $order->tariff_log_id,
                "packing_type_id" => $form_item->packing_type_id,
            ])->
        first();

        // اگر ردیف متناظری با بتوجه به درجه و نوع بسته بندی نداشت، نوع بسته بندی را حذف می کنیم.
        if (!$product_tariff_log) {
            $product_tariff_log = ProductTariffLog::where(
                [
                    "product_id" => $form_item->product_id,
                    "degree_id" => $form_item->degree_id,
                    "tariff_log_id" => $order->tariff_log_id,
                ])->
            first();
        }

        // اگر ردیف متناظری با توجه به درجه و بسته بندی وجود نداشت، با درجه اصلی چک می کنیم.
        if (!$product_tariff_log) {

            $master_degree = Degree::where([
                "goods_kind_id" => $form_item->product->goods_kind_id,
                "degree_type_id" => 1
            ])->first();
            if (!$master_degree) {
                return [
                    "message" => "درجه اصلی  رسته  " . $form_item->product->goods_kind->caption . " در تعرفه " . $order->tariff_log->tariff->caption . " وجود ندارد. ",
                    "error" => "درجه اصلی  رسته  " . $form_item->product->goods_kind->caption . " در تعرفه " . $order->tariff_log->tariff->caption . " وجود ندارد. ",
                    "result" => false
                ];

            }
            $product_tariff_log = ProductTariffLog::where(
                [
                    "product_id" => $form_item->product_id,
                    "degree_id" => $master_degree->id,
                    "tariff_log_id" => $order->tariff_log_id
                ])->
            first();

            if (!$product_tariff_log) {
                return [
                    "message" => "لیست تعرفه برای سفارش یافت نشد، امکان دریافت فاکتور وجود ندارد" . "<br/>کد کالا:"
                        . ($form_item->product->code ?? "***") . "<br/> درجه" . ($master_degree->caption ?? "***") . "<br/>تعرفه " . ($order->tariff_log->tariff->id . " - شماره لاگ: " . $order->tariff_log_id),
                    "error" => "لیست تعرفه برای سفارش یافت نشد، امکان دریافت فاکتور وجود ندارد" . "<br/>کد کالا:"
                        . ($form_item->product->code ?? "***") . "<br/> درجه" . ($master_degree->caption ?? "***") . "<br/>تعرفه " . ($order->tariff_log->tariff->id . " - شماره لاگ: " . $order->tariff_log_id),
                    "result" => false
                ];
            }
        }


        $form_factor_row["item"] = $form_item;
        $form_factor_row["product_service_code"] = $product_tariff_log->service_id ?
            $product_tariff_log->service->code :
            $product_tariff_log->product->code;

        $form_factor_row["product_service_caption"] = $product_tariff_log->getCaption();

        // محصول درجه در تعرفه یافت شد
        $form_factor_row["fea"] = $product_tariff_log->fea;
        if ($order->selling_type_id == 2) {
            // رند کردن مبلغ در فروش های غیررسمی
            if ($order->customer->round_fee_in_informal_sale) {
                $form_factor_row["fea"] = round($form_factor_row["fea"] * (1 + $order->increase_percentage_in_informal_sale / 100));
            } else {
                $form_factor_row["fea"] = $form_factor_row["fea"] * (1 + $order->increase_percentage_in_informal_sale / 100);
            }
        }

        switch ($amount_type) {
            case "group_by_product":
                // جمع کل همه ردیف های مشاده در بسته بندی و درجه
                $form_factor_row["amount"] =
                    FormItem::where([
                        "form_id" => $form_item->form->id,
                        "product_id" => $form_item->product_id,
                        "packing_type_id" => $form_item->packing_type_id,
                        "degree_id" => $form_item->degree_id,
                    ])->
                    sum("amount");
                break;
            case "form_item":
                $form_factor_row["amount"] =
                    FormItem::where([
                        "id" => $form_item->id,
                        "product_id" => $form_item->product_id,
                        "packing_type_id" => $form_item->packing_type_id,
                        "degree_id" => $form_item->degree_id,
                    ])->
                    sum("amount");
                break;
        }


        $form_factor_row["price"] = $form_factor_row["fea"] * round($form_factor_row["amount"], 7);

        //مبلغ تخفیف
        $form_factor_row["total_off_price"] = $order_factor->total_off_price / $order_factor->price * $form_factor_row["price"];

        $form_factor_row["total_price"] = $form_factor_row["price"] - $form_factor_row["total_off_price"];

        // مبلغ تخفیف اعشاری
        $total_price_without_float = Order::OffFloat($form_factor_row["total_price"], $round_off_number);
        $form_factor_row["total_off_price"] += $form_factor_row["total_price"] - $total_price_without_float;
        $form_factor_row["total_price"] = $total_price_without_float;

        $form_factor_row["tax_percent"] = $order->selling_type_id == 2 ? 0 : $product_tariff_log->tax + $product_tariff_log->fare;
        $form_factor_row["tax_price"] = $form_factor_row["total_price"] * $form_factor_row["tax_percent"] / 100;

        $form_factor_row["total_price_with_tax"] = $form_factor_row["total_price"] + $form_factor_row["tax_price"];

        return ["result" => true, "factor_row" => $form_factor_row, "order_code" => $order->id];


    }

    public static function getAllSellingAmount($customer_id, $setting_types = null, $invalid_order_id = null, $product_id = null)
    {
        // براساس نوع فاکتور (رسمی، غیررسمی)
        $status_list = Setting::getStringValue($setting_types ?? "sale_formal_status_list");
        $status_list = json_decode($status_list, true);
        $list = Order::join("order_factor", "orders.id", "order_id")->
        when($customer_id, function ($query) use ($customer_id) {
            return $query->where("orders.customer_id", $customer_id);
        })->
        when($invalid_order_id, function ($query) use ($invalid_order_id) {
            return $query->where("orders.id", "!=", $invalid_order_id);
        })->
        when($product_id, function ($query) use ($product_id) {
            return $query->where("order_factor.product_id", $product_id);
        })->
        whereIn("orders.status_id", $status_list)->
        groupBy("selling_type_id")->
        selectRaw("selling_type_id,sum(total_price_with_tax) as total_price_with_tax,sum(carton* number_in_carton) as amount")->
        get();

        $amount_list = [];

        foreach (SellingType::get() as $item) {
            $amount_list ["selling_type"][$item->id] =
                ["caption" => $item->caption, "total_price_with_tax" => 0, "percent" => 0];
        }

        $sum = 0;
        $sum_amount = 0;
        foreach ($list as $item) {
            $amount_list["selling_type"][$item->selling_type_id]["total_price_with_tax"] = $item->total_price_with_tax;
            $sum += $item->total_price_with_tax;
            $sum_amount += $item->amount;
        }
        foreach ($list as $item) {
            $amount_list["selling_type"][$item->selling_type_id]["percent"] = round($item->total_price_with_tax / $sum * 100);

        }

        $amount_list["total_selling_type"] = $sum;
        $amount_list["total_selling_type_amount"] = $sum_amount;

        // بر اساس وضعیت سفارش
        $status_list = [304020, 304030, 304030, 304040, 304050, 304060, 304070, 304075, 304080, 35030];
        $status_caption = Status::whereIn("id", $status_list)->pluck("caption", "id")->toArray();

        $list = Order::join("order_factor", "orders.id", "order_id")->
        when($invalid_order_id, function ($query) use ($invalid_order_id) {
            return $query->where("orders.id", "!=", $invalid_order_id);
        })->
        when($customer_id, function ($query) use ($customer_id) {
            return $query->where("orders.customer_id", $customer_id);
        })->
        when($product_id, function ($query) use ($product_id) {
            return $query->where("order_factor.product_id", $product_id);
        })->
        whereIn("orders.status_id", $status_list)->
        groupBy("orders.status_id")->
        selectRaw("sum(total_price_with_tax) as total_price_with_tax,orders.status_id")->get();
        // pluck( "total_price_with_tax", "status_id" );

        foreach ($list as $item) {
            $amount_list["status_item"][$item->status_id]["total_price_with_tax"] = round($item->total_price_with_tax);
            $amount_list["status_item"][$item->status_id]["caption"] = ($item->status_id == 35030 ? "" : " در انتظار ") . $status_caption[$item->status_id];
        }

        return $amount_list;
    }


}

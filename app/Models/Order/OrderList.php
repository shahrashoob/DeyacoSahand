<?php

namespace App\Models\Order;

use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Production\Production;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use App\Models\Customer\Customer;
use App\Models\Utility\Priority;
use App\Models\LineProduct\Product;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderList extends Model
{
    use HasFactory;
    use Loggable;

    protected $table = "order_list";
    protected $fillable = [

        "order_id",
        "product_id",
        "service_id",
        "customer_id",

        "production_card_id",
        "number_in_carton",
        "carton",
        "amount",
        "order_status_id",
        "wherehouse_id",
        "order_datetime",
        "priority_id",
        "order_type_id",
        "erp_status_id",
        "prefactor_number",
        "description_sheet_id",
        "description_request_id",
        "amount_sent",
        "amount_remaining",
        "amount_ep",
        "inventory",
        "sum_wpc",
        "sum_wo",
        "bp",
        "mi",
        "mp",
        "po",
        "pc",
        "ap",
        "loadable_status_id",
        "status_id",
        "production_card_code",
        "call_id",
        "degree_id",
        "packing_type_id",
        "type_of_sale_of_product_id"
    ];

    public function description_request()
    {
        return $this->belongsTo(Message::class, "description_request_id", "id");
    }

    public function description_sheet()
    {
        return $this->belongsTo(Message::class, "description_sheet_id", "id");

    }

    public function order()
    {
        return $this->belongsTo(Order::class);

    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);

    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function service()
    {
        return $this->belongsTo(Product::class, "service_id");
    }

    public function production()
    {
        return $this->belongsTo(Production::class, "production_card_id");

    }

    public function degree()
    {
        return $this->belongsTo(Degree::class);

    }

    public function order_kind()
    {
        return $this->belongsTo(OrderKind::class, "order_kind_id", "id");

    }

    public function status()
    {
        return $this->belongsTo(Status::class, "erp_status_id", "id");
    }

    public function order_status()
    {
        return $this->belongsTo(Status::class, "order_status_id", "id");
    }

    public function processing_status()
    {
        return $this->belongsTo(Status::class, "status_id", "id");
    }

    public function loadable_status()
    {
        return $this->belongsTo(Status::class, "loadable_status_id", "id");
    }

    public function order_date()
    {
        return jdate(Carbon::parse($this->order_datetime)->timestamp)->format('Y/m/d');

    }

    public function get_create_date_and_time()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');

    }

    public function priority()
    {
        return $this->belongsTo(Priority::class, "priority_id", "id");
    }

    public static function search(
        $text = "", $order_by = "",
        $select = []
    )
    {

        return OrderList::where("id", ">", 0);

    }

    public function log($message = "",$user_id=null)
    {

        $msg = null;
        if ($message != "") {
            $msg = Message::create(
                [
                    "text" => $message,
                    "other_id" => $this->id,
                    "message_type_id" => 120
                ]
            );
        }


        return OrderListLog::create([
            "order_list_id" => $this->id,
            "erp_status_id" => $this->status->id,
            "message_id" => $msg->id ?? 0,
            "user_id" => $user_id ?? Auth::user()->id
        ]);
    }

    public static function getProductReserveAmount($product_id, $degree_id = null, $type = "sum_amount")
    {
        $status_list = Setting::getStringValue("sale_planing_status_list");
        $status_list = json_decode($status_list, true);

        $warehouse_status = [];
        if (in_array(35090, $status_list)) { // تایید برگ خروج
            $status_list = array_diff($status_list, [35090]);
            $status_list[] = 7005008; // در انتظار تایید برگ خروج (انبار)
        }
        if (in_array(35040, $status_list)) { // ارسال ناقص
            $status_list = array_diff($status_list, [35040]);
            $status_list[] = 7005004; // در انتظار ارسال باقی مانده درخواست
        }

        if ($type == "sum_amount") {
            $query = OrderList::
            join("orders", "orders.id", "=", "order_list.order_id")->
            whereIn("orders.status_id", $status_list)->
            when($product_id, function ($query) use ($product_id) {
                $query->where("product_id", $product_id);
            })->
            when($degree_id, function ($query) use ($degree_id) {
                $query->where("degree_id", $degree_id);
            })->
            whereNull("from_order_id")->
            whereIn("erp_status_id", [360, 340, 305]);

            $amount = $query->addSelect(DB::raw(" sum(amount_remaining) as amount"))->first();

            return $amount;
        } else {
            return OrderList::
            join("orders", "orders.id", "=", "order_list.order_id")->
            whereIn("orders.status_id", $status_list)->
            when($product_id, function ($query) use ($product_id) {
                $query->where("product_id", $product_id);
            })->
            when($degree_id, function ($query) use ($degree_id) {
                $query->where("degree_id", $degree_id);
            })->
            whereNull("from_order_id")->
            whereIn("erp_status_id", [360, 340, 305])->
            with("order")->
            get();
        }

    }


    public function getPackingType($type = "caption")
    {

        $text = "";
        $list = OrderListPackingType::where("order_list_id", $this->id)->with("packing_type")->get();
        switch ($type) {
            case "caption":
                if (count($list) > 1) {
                    $text = count($list) . " نوع بسته بندی مجاز انتخاب شده";
                } elseif (count($list) == 1) {
                    $text = $list[0]->packing_type->caption;
                }
                break;
            case "code":
                if (count($list) > 1) {
                    $text = count($list) . " نوع بسته بندی مجاز انتخاب شده";
                } elseif (count($list) == 1) {
                    $text = $list[0]->packing_type->code;
                }
                break;

            case "tooltip":
                foreach ($list as $item) {
                    $text .= ($item->packing_type->caption ?? "**") . "\n";
                }
                break;

            case "list":
                foreach ($list as $item) {
                    $text .= $item->packing_type->caption . ", ";
                }
                $text = trim($text, ", ");
                break;
            case "packing_type_ids":
                $ids = [];
                foreach ($list as $item) {
                    $ids[] = $item->packing_type_id;
                }

                return $ids;
            case "packing_types":
                $packing_types = [];
                foreach ($list as $item) {
                    $packing_types[] = $item->packing_type;
                }

                return $packing_types;
                break;
        }

        if ($type == "packing_types") {
            return [];
        }
        return $text;
    }

}

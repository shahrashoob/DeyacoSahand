<?php

namespace App\Models\Production;

use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Models\Contractor\ContractorAllocation;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Order\RequestFromWarehouse;
use App\Models\Post\Post;
use App\Models\Post\PostUser;
use App\Models\Utility\Priority;
use App\Models\Utility\Setting;
use App\Notifications\SMSNotification;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use  App\Models\LineProduct\Line;
use  App\Models\LineProduct\Product;
use  App\Models\LineProduct\LineProductStation;
use  App\Models\Worker;
use App\Models\Utility\Status;
use App\Models\Utility\Log;
use App\Models\Order\OrderList;
use App\Models\Order\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Utility\Message;

class Production extends Model
{
    use HasFactory;
    use Loggable;

    protected $table = "production_cards";
    protected $fillable = [
        "set_up_time",
        "down_time",
        "unemployment_time",
        "number",
        "number_of_packing_form",
        "number_product",
        "sub_number_product",
        "line_allocation",
        "production_time",
        "production_speed",
        "consumer_price",
        "date_of_expriation",
        "status_id",
        "line_id",
        "supervisor_worker_id",
        "line_product_id",
        "serial",
        "number_in_carton",
        "waiting_status_id",
        "max_delivery_datetime",
        "production_type_id",
        "product_creation_process_id",
        "normal_amount"
    ];

    public function datetimes()
    {

        return $this->hasMany(ProductionDateTime::class, "production_card_id", "id")->where("status_id", "!=", 510000100);
    }

    public function line()
    {
        return $this->belongsTo(Line::class, "line_id", "id");
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, "product_id", "id");
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class, "prioriry_id", "id");
    }

    public function line_product()
    {
        return $this->belongsTo(LineProductStation::class, "line_product_id", "id");
    }

    public function machine_allocation()
    {
        return $this->hasMany(MachineAllocation::class)->where("status_id", 5310010);
    }

    public function machine_reallocation()
    {
        return $this->hasMany(MachineAllocation::class)->where("status_id", 5310050);
    }

    public function machine_reserve()
    {
        return $this->hasMany(MachineAllocation::class)->where("status_id", 5310040);
    }

    public function supervisor_worker()
    {
        return $this->belongsTo(Worker::class, "supervisor_worker_id", "id");
    }

    public function status()
    {
        return $this->belongsTo(Status::class, "status_id", "id");
    }

    public function waiting_status()
    {
        return $this->belongsTo(Status::class, "waiting_status_id", "id");
    }

    public function packing_types()
    {
        return $this->hasMany(ProductionPackingType::class);
    }

    public function parent_production()
    {
        return $this->belongsTo(Production::class, "parent_production_id");
    }

    public function production_form_item()
    {
        return $this->hasMany(ProductionFormItem::class);
    }

    public function production_type()
    {
        return $this->belongsTo(ProductionType::class);
    }

    public function getProductionChannelType()
    {
        $line_production = LineProductStation::where("product_id", $this->product_id)->first();
        if (!$line_production) {
            return null;
        }

        return $line_production->production_channel_type;
    }

    public function product_creation_process()
    {
        return $this->belongsTo(Product\ProductCreation\ProductCreationProcess::class);
    }

    public function getStatus()
    {

        if ($this->status_id == 500) {

            switch ($this->waiting_status_id) {
                case 7001003:// در حال تولید ماشین بافندگی


                    $count = Machine::join("machine_allocation", "machine_id", "machines.id")->
                    where([
                        "production_id" => $this->id,
                        "machine_allocation.status_id" => 5310010 //  تخصیص داده شده
                    ])->
                    whereIn("production_status_id", [
                        7003016, // در حال بافت
                        7003035, // در حال بافت پارچه پایانی
                        7003038, // در انتظار استخراج پارچه پایانی (توقف کارت تولید)
                        7003026, // در انتظار پایان چله جهت تغییر کالیته
                        7003040, // در انتظار شروع استخراج چله (توقف کارت تولید)
                        7003037, // در حال تغییر نخ پود (توقف کارت تولید)
                        7003042, //  در انتظار پایان بافت (کارت تولید جاری)
                    ])->
                    groupBy("machine_id")->get();

//
                    return " در حال بافت  توسط " . count($count) . " ماشین";
                    break;
                case 7008002: // در حال تولید توسط n پیمانکار
                    $count = MachineAllocation::where("production_id", $this->id)->count();

                    return "در حال تولید توسط " . $count . " پیمانکار";
//                case 7001001:
//                    return  "در انتظار تخصیص (جت 2 -پلی استر)";
            }


            return $this->status->caption . " (" . ($this->waiting_status->caption ?? "*تولید*") . ")";
        } else {
            return $this->status->caption;
        }
    }

    public function order_list()
    {
        return $this->belongsTo(OrderList::class, "order_list_id", "id");
    }

    public function order()
    {
        return $this->belongsTo(Order::class, "order_id", "id");
    }

    public function RFWs()
    {

        return $this->hasMany(RequestFromWarehouse::class, "production_card_id", "id");
    }

    public function forms()
    {
        return $this->hasMany(Form::class, "production_card_id", "id");

    }

    public function production_log()
    {
        return $this->hasMany(ProductionLog::class, "production_id", "id");
    }

    public function get_log_with_status($status_id = false)
    {
        if (!$status_id) {
            return ProductionLog::where(["production_id" => $this->id])->get();
        }

        return ProductionLog::where(["status_id" => $status_id, "production_id" => $this->id])->first();
    }

    public function get_allocation_amount($machine_or_contractor = false, $supply_type_id = 1, $allocation_id = null, $check_parent_allocation_id = true, $unit_type_id = 1)
    {
        $col_name_of_amount = "allocation_amount";
        switch ($unit_type_id) {
            case 1:
                $col_name_of_amount = "allocation_amount";
                break;
            case 2:
                $col_name_of_amount = "allocation_sub_amount";
                break;
            case 3:
                1 / 0;
                break;
            case 4:
                $col_name_of_amount = "number_of_packing_form";
                break;
            case 1000: //
                // همه ستون ها را جمع می کندو خروجی آرایه است.
        }

        if ($supply_type_id == 3) { // پیمانکاری
            return ContractorAllocation::
            where(["production_id" => $this->id])->
            when($machine_or_contractor, function ($query) use ($machine_or_contractor) {
                $query->where("contractor_id", $machine_or_contractor);
            })->
            when($allocation_id, function ($query) use ($allocation_id) {
                $query->where("allocation_id", $allocation_id);
            })->
            whereNotIn("status_id", [5310030])->sum("allocation_amount");
        } else {
            $query = MachineAllocation::
            where(["production_id" => $this->id])->
            when($machine_or_contractor, function ($query) use ($machine_or_contractor) {
                $query->where("machine_id", $machine_or_contractor);
            })->
            when($allocation_id, function ($query) use ($allocation_id) {
                $query->where("allocation_id", $allocation_id);
            })->
            when($check_parent_allocation_id, function ($query) {
                $query->whereNull("parent_allocation_id"); // تخصیص اصلی است و تخصیص مجدد نمی باشد.
            })->
            whereIn("status_id", ["5310010", "5310020", "5310040"]);

            if ($unit_type_id == 1000) {
                // بعضی از وقت ها نیاز است همه مقادیر را نمایش دهیم.
                return $query->selectRaw("sum(allocation_amount) as allocation_amount, sum(allocation_sub_amount) as allocation_sub_amount,sum(number_of_packing_form) as number_of_packing_form ")->first()->toArray();

            } else {
                return $query->sum($col_name_of_amount);
            }
        }
    }

    public function get_production_amount($allocation_id = null)
    {
        // مقداری از کارت تولید که تاکنون تولید شده است.
        return round(ProductionFormItem::
        whereNull("source_production_form_item_id")-> // اولین فرمی است که برای کارت ایجاد شده است.
        whereNull("packing_form_item_id")-> // کد آیتم بسته بندی که معادل آن آیتم فرم تولید ایجاد شده است.
        where("production_id", $this->id)->
        when($allocation_id, function ($query) use ($allocation_id) {
            return $query->where("allocation_id", $allocation_id);
        })->
        whereNotIn("status_id", ProductionForm::StatusNotValidForProductionAmount())-> // تزریق شده به ماشین باید حذف شود
        sum("production_form_item.amount"), 4);
    }

    ################### date time
    public function get_create_date()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('Y/m/d');
    }

    public function get_create_time()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i');
    }

    public function get_create_date_and_time()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');

    }

    public function get_max_delivery_date()
    {
        if ($this->max_delivery_datetime) {
            return jdate(Carbon::parse($this->max_delivery_datetime)->timestamp)->format('Y/m/d ');
        }

    }

    ###############
    public function serial()
    {

        if ($this->serial == null) {

            $nth_in_day = $string = Str::of($this->nth_in_day)
                ->when($this->nth_in_day < 100, function ($string) {
                    return Str::of('0')->append($string);
                })->when($this->nth_in_day < 10, function ($string) {
                    return Str::of('0')->append($string);
                });

            $number_all = ceil($this->number);
            $number = Str::of($number_all)
                ->when($number_all < 10, function ($string) {
                    return Str::of('00000')->append($string);
                })->when($number_all >= 10 && $number_all < 100, function ($string) {
                    return Str::of('0000')->append($string);
                })->when($number_all >= 100 && $number_all < 1000, function ($string) {
                    return Str::of('000')->append($string);
                })->when($number_all >= 1000 && $number_all < 10000, function ($string) {
                    return Str::of('00')->append($string);
                })->when($number_all >= 10000 && $number_all < 100000, function ($string) {
                    return Str::of('0')->append($string);
                })->when($number_all >= 1000000, function ($string) {
                    return Str::of('')->append($string);
                });


            $day = jdate(Carbon::parse($this->created_at)->timestamp)->format('Ymd');

            $this->serial = $nth_in_day . $number . $day;

            $this->save();
        }


        return $this->serial;
    }

    public static function getFromSerial($serial_s)
    {

        return Production::where("serial", $serial_s)->first();

    }

    public function number()
    {
        return $this->number . " " . $this->product->unit->caption;
    }

    public function number_product()
    {
        if ($this->number_product) {
            return $this->number_product . " " . $this->product->unit->carton_name();
        } else {
            return "ثبت نشده";
        }
    }

    public static function search(
        $text = "", $order_by = "production_cards.created_at__desc", $lines = [],
        $select = [
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
            "production_cards.order_id"
        ],
        $productionStatusIds = [],
        $supply_type_id = 1,
        $product_ids = [],
        $order_ids = [],
        $goods_kind_id = false

    )
    {

        return Production::join("products", "production_cards.product_id", "products.id")->
        when(count($lines) > 0, function ($query) {
            return $query->join("line_product_station", "products.id", "line_product_station.product_id");
        })->
        where("supply_type_id", $supply_type_id)->
        when($text != "", function ($query) use ($text) {
            return $query->where(function ($query) use ($text) {
                return $query->where("serial", "like", "%" . $text . "%")
                    ->orWhere("products.code", "like", "%" . $text . "%")
                    ->orWhere("products.caption", "like", "%" . $text . "%");
            });

        })->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");

            return $query->orderBy($order_by[0], $order_by[1]);

        })->
        when($select != [], function ($query) use ($select) {
            $select[] = "production_cards.id as id";

            return $query->select($select);
        })->
        when($text == "" && count($lines) > 0, function ($query) use ($lines) {
            return $query->whereIn("line_product_station.line_id", $lines);
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
        when($order_ids != [], function ($query) use ($order_ids) {
            return $query->whereIn("production_cards.order_id", $order_ids);

        })->
        addSelect(DB::raw("production_cards.id as production_card_id"))->
        groupBy("production_cards.id")
            // ->
            // with(["product"=>function( $product) use($product_info) {
            //   $product->orWhere("cod","like","%".$product_info."%")->orWhere("caption","like","%".$product_info."%");
            // }])
            ;
    }

    public function evaluation_indicator()
    {


        $production_datetimes = $this->datetimes;

        $production_time = 0;
        foreach ($production_datetimes as $item) {

            $start_datetime = Carbon::create($item->start_datetime);
            $end_datetime = Carbon::create($item->end_datetime);

            $diffInMinutes = $end_datetime->diffInMinutes($start_datetime);
            $production_time += $diffInMinutes;

        }


        if ($production_time <= 0) {
            return "شیفت ها وارد نشده است";
        }
        $allocated_time = $production_time -
            ($this->set_up_time + $this->down_time + $this->unemployment_time);

        if ($allocated_time <= 0) {
            return "زمان تخصیص به درستی وارد نشده است.";
        }
        if ($this->line_product->min_of_production * $allocated_time / 60 == 0) {
            return "اطلاعات خط نا معتبر است، لطفا با پشتیبانی تماس بگیرید.";
        }
        $PI = $this->number_product / ($this->line_product->min_of_production * $allocated_time / 60);

        $PI = $PI > 2 ? 2 : $PI;
        $this->productivity_index = $PI;
        $this->save();

        foreach ($production_datetimes as $item) {

            $start_datetime = Carbon::create($item->start_datetime);
            $end_datetime = Carbon::create($item->end_datetime);

            $diffInMinutes = $end_datetime->diffInMinutes($start_datetime);
            $time_to_shift = $diffInMinutes / ($item->shift_time);

            $subPI = $PI >= $this->line_product->efficiency ? $time_to_shift * $PI : ($time_to_shift * ($PI - 2));

            $item->sub_productivity_index = $subPI;
            $item->save();
        }

        return "";
    }


    public function _register_card()
    {

        // این روش قدیمی می باشد، وقتی که هنوز وضعیت در انتظار را اضافه نکرده بودیم.

        $extraP = ExtraProduction::where("product_id", $this->product_id)->
        firstOrCreate(["product_id" => $this->product_id]);


        $diff_number = $this->number_product - $this->number;

        // اگر کمتر از مقدار درخواستی کارت صادر شده باشد
        if ($diff_number < 0) {

            $diff_number = -$diff_number;
            // اگر مازاد معادل وجود دارد
            if ($extraP->amount >= $diff_number) {
                $extraP->amount -= $diff_number;
                $extraP->save();
            } else {
                $this->create_subsidiary_product($diff_number, $extraP);
            }
        } // end if $diff_number<0
        else {
            // مقدار مازاد تولید شده

            $extraP->amount += $diff_number;
            $extraP->save();


        }


        $this->status_id = 520;//"Finished";
        $this->waiting_status_id = 500090;//"Finished";
        $this->save();

    }


    public function register_card2()
    {

        $extraP = ExtraProduction::where("product_id", $this->product_id)->
        firstOrCreate(["product_id" => $this->product_id]);

        if ($extraP->amount < 0) {
            $this->create_subsidiary_product(-$extraP->amount);

            $extraP->amount = 0;
            $extraP->save();
        }

    }


    public function create_subsidiary_product($number)
    {

        // صدور کارت فرعی جدید
        if (isset($this->order_list)) {
            $new_order_list = $this->order_list->replicate();
        } else {
            $new_order_list = new OrderList();
            $new_order_list->product_id = $this->product_id;
            $new_order_list->number_in_carton = $this->number_in_carton;
            $new_order_list->priority_id = $this->prioriry_id;
            $new_order_list->customer_id = $this->customer_id;
        }

        $new_order_list->from_order_id = $this->order_id;
        $new_order_list->from_order_list_id = $this->order_list_id;
        $new_order_list->from_production_card_id = $this->id;
        $new_order_list->erp_status_id = 322; // خاتمه یافته - کارت فرعی
        $new_order_list->status_id = 302; // خاتمه یافته - کارت فرعی
        $new_order_list->save();
        $new_production = $this->replicate();
        $production_serial_base = Str::of($this->serial)->explode('/')[0];

        $version_number = Production::where("serial", "like", "%" . $production_serial_base . "%")->count();

        $new_production->serial = $production_serial_base . "/" . ($version_number + 1);

        $new_production->status_id = 500;//"Waiting for production";
        $new_production->waiting_status_id = 500010;

        $new_production->number = $number;

        $new_production->order_id = $this->order_id;
        $new_production->order_list_id = $new_order_list->id;
        $new_production->customer_id = $this->customer_id;
        $new_production->product_id = $this->product_id;
        $new_production->version = $version_number;
        $new_production->prioriry_id = $this->prioriry_id;
        $new_production->is_master_of_rfw = 0;
        $new_production->save();
        $new_production->created_at = $this->created_at;
        $new_production->number_product = null;
        $new_production->save();
        $new_production->log("", 550);

        if ($this->version == null) {
            $this->version = 0;
            $this->save();
            $this->log(" صدور کارت تولید فرعی " . $version_number, 550);
        }

        foreach ($this->RFWs as $RFW) {
            $new_RFW = $RFW->replicate();
            $new_RFW->order_list_id = $new_order_list->id;
            $new_RFW->production_card_id = $new_production->id;
            $new_RFW->amount = $RFW->amount_remaining;
            $new_RFW->amount_sent = 0;
            $new_RFW->amount_remaining = 0;
            $new_RFW->status_id = 380;
            $new_RFW->master_production_id = null;
            $new_RFW->save();

        }

        // end  else $extraP->amount >=$diff_number
    }

    public function replace($replace)
    {

        $extraP = ExtraProduction::where("product_id", $this->product_id)->
        firstOrCreate(["product_id" => $this->product_id]);

        if ($extraP->amount < $replace->number) {
            return false;
        }

        $extraP->amount -= $replace->number;
        $extraP->save();

        $replace->status_id = 500;
        $replace->waiting_status_id = 500100;
        $replace->save();
        $replace->RFWTerminate();
        $production_log = $replace->log("", 500, 500100);

        ProductionReplace::create([
            "production_id" => $replace->id,
            "production_replace_id" => $this->id,
            "production_log_id" => $production_log->id,
        ]);

        return true;
    }

    public function RFWTerminate()
    {

        foreach ($this->RFWs as $item) {
            $item->status_id = 386001; // خاتمه یافته
            $item->save();
        }
    }

//    public function changeProductionStatusGroupInForm( $log_status, $new_status ) {
//
//        $rfw_production_ids = RequestFromWarehouse::where( "master_production_id", $this->id )->
//        groupBy( "production_card_id" )->pluck( "production_card_id" );
//
//        $production_list = Production::whereIn( "id", $rfw_production_ids )->get();
//        foreach ( $production_list as $item ) {
//            $item->log( "", false, $log_status );
//            $item->waiting_status_id = $new_status;
//            $item->save();
//        }
//    }

    public function log($message = "", $status_id = false, $waiting_status_id = false)
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


        return ProductionLog::create([
            "production_id" => $this->id,
            "status_id" => $status_id == false ? $this->status->id : $status_id,
            "waiting_status_id" => $waiting_status_id == false ? $this->waiting_status_id : $waiting_status_id,
            "message_id" => $msg->id ?? 0,
            "user_id" => Auth::user()->id
        ]);

    }


    public static function CreateHandmadeProduction(
        $order, $order_list, $product,
        $parent_production_id, $max_delivery_datetime,
        $amount = null, $production_type_id = 1, $packing_types = null,
        $priority_id = null, $number_of_packing_form = null, $text = "",
        $production_event_id = 7008003, $normal_amount = null, $user_id = null)
    {


        // اگر بسته بندی مقدار عیب یابی دارد، آن را محاسبه می کنیم.

        $normal_number_of_packing = null;
        if (isset($packing_types)) {

            foreach ($packing_types as $packing_type) {


                // بررسی اینکه همه مقدار های نرمال بسته نیدی های با هم برابر باشد.
                if ($normal_number_of_packing != null && $normal_number_of_packing != $packing_type->normal_amount && !$packing_type->take_amount_from_parent_production_card) {
                    $text = "";
                    foreach ($packing_types as $packing_type) {
                        $text .= $packing_type->caption . " - مقدار لوگو: " . $packing_type->normal_amount . " " . $product->unit->caption . "<br/>";
                    }
                    return [
                        "result" => false,
                        "error" => "با توجه به اینکه برای کارت تولید بیش از یک بسته بندی انتخاب شده است و مقدار نرمال بسته بندی ها با هم متفاوت است، امکان ثبت کارت تولید وجود ندارد." .
                            "<br/>" . $text
                    ];
                }
                $normal_number_of_packing = $packing_type->normal_amount;

                // بسته بندی شامل برند است؟
                if ($packing_type->packaging_forms_include_brand) {

                    // مقدار برند را از کارت پدر می گیرد؟
                    if ($normal_amount) {
                        // مقدار نرمال مشخص شده و نیازی نیست از جایی بگیریم
                    } elseif ($normal_number_of_packing != null && ($order || $parent_production_id) && $packing_type->take_amount_from_parent_production_card) {

                        // اگر کارت پدر داشت که مقدار کارت پدر، در غیر این صورت اگر سفارش داشت مقدار سفارش را در نظر می گیریم.
                        if (isset($parent_production_id) && $parent_production_id > 0) {
                            $normal_amount = Production::find($parent_production_id)->normal_amount ?? null;
                        } elseif (isset($order)) {
                            $normal_amount = $packing_type->normal_amount;
                            if ($packing_type->normal_amount_unit_type_id == 2) {

                                if ($product->sub_unit_id != 300) {
                                    return [
                                        "result" => false,
                                        "error" => "نوع واحد کالا برای محاسبه مقدار نرمال بسته بندی از جنس واحد دوم است و واحد فرعی کالا از جنس وزنی نمی باشد، بنابراین امکان محاسبه مقدار لوکو برای کارت تولید وجود ندارد."
                                    ];
                                }
                                if (!$product->weight) {
                                    return [
                                        "result" => false,
                                        "error" => "وزن کالای " . $product->capiton . " نامعتبر است."
                                    ];
                                }
                                $normal_amount = $packing_type->normal_amount / $product->weight;
                            }

                        }

                    } else {
                        // اگر مقدار برند را از خودش می گیرد که مقدار نرمال بسته بندی است.
                        $normal_amount = $packing_type->normal_amount;
                    }
                }
            }
        }


        $production = new Production();
        $production->order_id = $order->id ?? null;
        $production->order_list_id = $order_list->id ?? null;
        $production->customer_id = $order_list->customer_id ?? null;
        $production->product_id = $product->id;
        $production->number = $amount ?? $order_list->amount;
        $production->number_of_packing_form = $number_of_packing_form;
        $production->number_in_carton = $product->number_in_carton;
        $production->version = 1;
        $production->nth_in_day = Production::where("created_at", ">", Carbon::yesterday()->addDay()->format('Y-m-d'))->count() + 1;
        $production->status_id = 500;
        $production->parent_production_id = $parent_production_id;

        $production->production_type_id = $production_type_id;

        // با توجه به نوع تامین، در انتظار تخصیص ماشین، یا در انتظار تخصیص پیمانکار می شود.
        $production->waiting_status_id = $product->goods_kind->getStatusId($product, "first_status_for_product_creation");
        $production->prioriry_id = $priority_id ?? $order_list->priority_id;
        $production->max_delivery_datetime = $max_delivery_datetime;
        $production->normal_amount = $normal_amount; // مقدار نرمال بسته بندی ها که ممکن است از سطح بالاتر گرفته شود.


        // چک کردن مقدار با توجه به قاب کالا
        $result_doff = Product::CheckFrameForDoffs($product, $production->number, 0);
        if (!$result_doff["result"]) {
            // اگر مقدار قاب کارت تولید مشکل دارد و مقدار وسط را سیستم تشخیص داده، به اندازه مقدار وسط کارت می زنیم و لازم نیست خطا بدهیم.
            if (isset($result_doff["middle_amount"]) && $result_doff["middle_amount"] > 0) {
                $production->number = $result_doff["middle_amount"];
                $result_doff["result"] = true;
            } else {
                return $result_doff;
            }
        }


        $production->save();


        $production = Production::find($production->id);
        $production->serial();

        if (isset($packing_types)) {
            foreach ($packing_types as $packing_type) {
                ProductionPackingType::create([
                    "production_id" => $production->id,
                    "packing_type_id" => $packing_type->id
                ]);
            }
        }

        //ارسال پیامک برای ناظر ها
        $send_sms_in_create_production = Setting::getIntegerValue("send_sms_in_create_production"); //

        if ($send_sms_in_create_production) {
            $company_name = Setting::getStringValue("company_name"); //
            // قالب پیامک برای کارت تولید و دستور پیمان متفاوت است.
            $template = "supervisoralertforcreateproductioncard";
            switch ($product->supply_type_id) {
                case 2: // خرید
                case 4: // تحویل امانی
                    $template = "supervisoralertforcreatesuppliercard";
                    break;
                case 3: // پیمانکار
                    $template = "supervisoralertforcreatecontractorcard";
            }

            $supervisor = Post::where("is_system_supervisor", 1)->get();
            foreach ($supervisor as $item) {

                foreach ($item->worker as $super_worker) {

                    Notification::send(
                        "00" . ($super_worker->mobile_country->area_code ?? "98") . $super_worker->mobile,
                        new SMSNotification($template,
                            $product->code,
                            ($amount ?? $order_list->amount) . "(" . ($product->unit->caption ?? "***") . ")",
                            $production->serial(1),
                            $company_name ?? "***",
                            $product->caption

                        )
                    );

                }

            }
        }

        event(new ProductionCardLogEvent($production, $text, $user_id, $production_event_id,));


        // به خروجی doff کارت تولید اضافه می کنیم و ارسال میکنیم.
        $result_doff["production"] = $production;
        return $result_doff;
    }


    // گرفتن لیست کارت های تولید که کارت تولید نمونه گیری می تواند بین آنها رزور شود.
    public static function getReserveAfterAllocationOption(Production $production, Machine $machine)
    {

        $reserve_after_allocation_option = [
            "list" => [],
            "message" => "",
            "result" => true
        ];
        if ($production->production_type_id == 1) {
            return $reserve_after_allocation_option; // برای کارت های تولیدی فعلا برنامه ای نداریم.
        }

        // این تابع تنها در حالتی کار می کند که خواسته باشیم یک کارت را بین دوکارت دیگر رزرو کنیم که کانال تولید همه آنها یک باشد.
        $reserve_after_allocation_option["list"] [] = [
            "value" => "",
            "text" => "لطفا یک کارت تولید را انتخاب نمایید."
        ];
        $reserve_list = $machine->ReserveAllocation()->get();
        $current_allocation = $machine->getCurrentAllocation();
        if (!$current_allocation) {
            // اگر تخصیص جاری برای ماشین وجود نداشت، آخرین تخصیص خاتمه یافته را بر می داریم.
//            $reserve_after_allocation_option["result"]  = false;
//            $reserve_after_allocation_option["message"] = "تخصیص جاری برای ماشین وجود ندارد";
//
//            return $reserve_after_allocation_option;

            $current_allocation = Allocation::where("machine_id", $machine->id)->where("status_id", 5310020)->orderByDesc("id")->first();

            if (!$current_allocation) {

                $reserve_after_allocation_option["result"] = false;
                $reserve_after_allocation_option["message"] = "از آنجایی که کارت نمونه گیری باید پس از یک کارت تولیدی تخصیص داده شود، هیچ تخصیص جاری یا تخصیص خاتمه یافته ای با کارت تولیدی از قبل برای ماشین وجود ندارد.";

                return $reserve_after_allocation_option;

            }
        }
        $current_machine_allocation = $current_allocation->items()->first();


        // بررسی اینکه کالای تخصیص جاری مسیرمحصیول و کانال تولید داشته باشد.
        $current_product_station = LineProductStation::where([
            "machine_type_id" => $machine->machine_type_id,
            "product_id" => $current_machine_allocation->product_id
        ])->first();

        $product_station_sampling = LineProductStation::where([
            "machine_type_id" => $machine->machine_type_id,
            "product_id" => $production->product_id
        ])->first();


        if (!$product_station_sampling) {
            $reserve_after_allocation_option["result"] = false;
            $reserve_after_allocation_option["message"] = "مسیر محصول برای " . $production->product->fullCaption() . " تعریف نشده است.";

            return $reserve_after_allocation_option;
        }
        if (!$product_station_sampling->production_channel_type) {
            $reserve_after_allocation_option["result"] = false;
            $reserve_after_allocation_option["message"] = " کانال تولید برای " . $product_station_sampling->product->fullCaption() . " مشخص نشده است.";

            return $reserve_after_allocation_option;
        }
        if (!$current_product_station) {
            $reserve_after_allocation_option["result"] = false;
            $reserve_after_allocation_option["message"] = "مسیر محصول برای " . $current_machine_allocation->product->fullCaption() . " تعریف نشده است.";

            return $reserve_after_allocation_option;
        }
        if (!$current_product_station->production_channel_type) {
            $reserve_after_allocation_option["result"] = false;
            $reserve_after_allocation_option["message"] = " کانال تولید برای " . $current_machine_allocation->product->fullCaption() . " مشخص نشده است.";

            return $reserve_after_allocation_option;
        }

        if (count($reserve_list) == 0 || !$current_allocation) {


            // کانال تولید کارت نمونه گیری و کانال جاری ماشین باید یکی باشد.
            $current_production_channel = $machine->getCurrentProductionChannel();
            if ($product_station_sampling->production_channel_type->id != ($current_production_channel->production_channel_type_id ?? -1)) {

                $reserve_after_allocation_option["result"] = false;
                $reserve_after_allocation_option["message"] = " با توجه به کانال تولید ماشین،  رزور کارت امکان پذیر نمی باشد.";

                return $reserve_after_allocation_option;

            }

            $reserve_after_allocation_option["list"][] = [
                "value" => $current_machine_allocation->allocation_id,
                "text" => $current_machine_allocation->production->serial() . " (تخصیص شماره " . $current_machine_allocation->allocation_id . ")"
            ];
            $reserve_after_allocation_option["result"] = true;

            return $reserve_after_allocation_option;
        }

        $reserve_list_co_channel = [];
        foreach ($reserve_list as $allocation) {
            $machine_allocation = $allocation->items()->first();
            if (!$machine_allocation) {
                $reserve_after_allocation_option["result"] = false;
                $reserve_after_allocation_option["message"] = "آیتم های تخصیص شماره " . $allocation->id . " یافت نشد.";

                return $reserve_after_allocation_option;
            }


            // بررسی اینکه کالای تخصیص بعدی مسیر محصول و کانال تولید داشته باشد
            $next_product_station = LineProductStation::where([
                "machine_type_id" => $machine->machine_type_id,
                "product_id" => $machine_allocation->product_id
            ])->first();

            if (!$next_product_station) {
                $reserve_after_allocation_option["result"] = false;
                $reserve_after_allocation_option["message"] = "مسیر محصول برای " . $next_product_station->product->fullCaption() . " تعریف نشده است.";

                return $reserve_after_allocation_option;
            }
            if (!$next_product_station->production_channel_type) {
                $reserve_after_allocation_option["result"] = false;
                $reserve_after_allocation_option["message"] = " کانال تولید برای " . $next_product_station->product->fullCaption() . " تعریف نشده است.";

                return $reserve_after_allocation_option;
            }
            if (!$product_station_sampling->production_channel_type) {
                $reserve_after_allocation_option["result"] = false;
                $reserve_after_allocation_option["message"] = " کانال تولید برای " . $product_station_sampling->product->fullCaption() . " تعریف نشده است.";

                return $reserve_after_allocation_option;
            }

            if (
                $next_product_station->production_channel_type->id == $current_product_station->production_channel_type->id &&
                $product_station_sampling->production_channel_type->id == $next_product_station->production_channel_type->id) {

                $reserve_after_allocation_option["list"][] = [
                    "value" => $current_machine_allocation->allocation_id,
                    "text" => $current_machine_allocation->production->serial() . " (تخصیص شماره " . $current_machine_allocation->allocation_id . ")"
                ];
            }


            $current_machine_allocation = $allocation->items()->first();


        }

        if (count($reserve_after_allocation_option["list"]) == 1) {
            $reserve_after_allocation_option["result"] = false;
            $reserve_after_allocation_option["message"] = "با توجه به کانال تولید کارت نمونه گیری، کارت های رزور و کارت جاری ماشین، امکان تخصیص کارت نمونه گیری وجود ندارد.";

            return $reserve_after_allocation_option;
        }

        return $reserve_after_allocation_option;
    }

    public static function sendSmsAfterAllocation($production, $machine)
    {
        // ارسال پیامک تخصیص کارت تولید به پست های سازمانی بعد از تخصیص کارت تولید
        $post_id = Setting::getStringValue("send_sms_in_create_allocation_machine_to_post_id1");

        $post = Post::find($post_id);
        if (!$post) {
            return;
        }
        $users = PostUser::getCurrentUserByShiftWorkAndLeaveOvertimeByPostId("worker", $post_id);
        $smsTemplate = "allertincreateallocationproductioncardtomachine";
        $token = $production->serial();
        $token2 = "";
        $token3 = "";
        $token10 = $post->caption;
        $token20 = $machine->caption;

        foreach ($users as $worker) {
            Notification::send("00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile,
                new SMSNotification($smsTemplate, $token, $token2, $token3, $token10, $token20));

        }
    }

    public static function sendSmsAfterAllocationCancel($production, $machine)
    {
        // ارسال پیامک حذف کارت تولید به پست های سازمانی
        $post_id = Setting::getStringValue("send_sms_in_create_allocation_machine_to_post_id1");

        $post = Post::find($post_id);
        if (!$post) {
            return;
        }
        $users = PostUser::getCurrentUserByShiftWorkAndLeaveOvertimeByPostId("worker", $post_id);
        $smsTemplate = "allertinremoveallocationproductioncardtomachine";
        $token = $production->serial();
        $token2 = "";
        $token3 = "";
        $token10 = $post->caption;
        $token20 = $machine->caption;

        foreach ($users as $worker) {
            Notification::send("00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile,
                new SMSNotification($smsTemplate, $token, $token2, $token3, $token10, $token20));
        }
    }

    public static function sendSmsAfterFailedAutoAllocation($production, $allocation_amount, $error)
    {
        // ارسال پیامک عدم انجام تخصیص اتوماتیک به پست های سازمانی
        $post_id = Setting::getStringValue("send_sms_in_create_allocation_machine_to_post_id1");
        $software_name = Setting::getStringValue("software_name");
        $post = Post::find($post_id);
        if (!$post) {
            return;
        }
        $users = PostUser::getCurrentUserByShiftWorkAndLeaveOvertimeByPostId("worker", $post_id);
        $smsTemplate = "allertinfailedautoallocationproductioncardtomachine";
        $token = $production->serial();
        $token2 = $allocation_amount;
        $token3 = $software_name;
        $token10 = $post->caption;
        $token20 = $error;

        foreach ($users as $worker) {
            Notification::send("00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile,
                new SMSNotification($smsTemplate, $token, $token2, $token3, $token10, $token20));

        }
    }
//    public function diffDateTime($status_id)
//    {
//
//        $text = "";
//        $datetime = null;
//
//        switch ($status_id) {
//            case 500020:
//                $form1_status = $this->get_log_with_status(500020);
//                if ($form1_status) {
//                    $datetime = $form1_status->get_datetime();
//                }
//
//                break;
//        }
//    }

    public function getPackingType($type = "caption")
    {

        $text = "";
        $list = $this->packing_types()->get();
        switch ($type) {
            case "caption":
                if (count($list) > 1) {
                    $text = count($list) . " نوع بسته بندی مجاز انتخاب شده";
                } elseif (count($list) == 1) {
                    $text = $list[0]->packing_type->caption;
                }
                break;

            case "tooltip":
                foreach ($list as $item) {
                    $text .= $item->packing_type->caption . "\n";
                }
                break;

            case "caption_br":
                foreach ($list as $item) {
                    $text .= $item->packing_type->code . "-" . $item->packing_type->caption;
                    if (count($list) > 1) {
                        $text .= "<br>";
                    }
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
                break;
        }


        return $text;
    }

}


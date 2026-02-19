<?php

namespace App\Models\LineProduct\Product\ProductRequest;

use App\Events\Contractor\ContractorLogEvent;
use App\Events\Form\PackingLogEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\Order\OrderLogEvent;
use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Events\Warps\WarpsAvailableEvent;
use App\Http\Controllers\Contractor\Panel as ContractorPanel;
use App\Http\Controllers\Sales\ProductRequestPermissionController;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Tariff\ProductTariffLog;
use App\Models\Contractor\Contractor;
use App\Models\Contractor\ContractorAllocation;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeInputBand;
use App\Models\LineProduct\Machine\MachineTypeInputBandPackingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Station;
use App\Models\Order\Order;
use App\Models\Order\OrderList;
use App\Models\Order\TransKind;
use App\Models\Post\Post;
use App\Models\Post\PostUser;
use App\Models\Production\Production;
use App\Models\Supplier\Supplier;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Notification\SMSMessage;
use App\Models\Utility\Script\Script;
use App\Models\Utility\Script\ScriptLog;
use App\Models\Utility\Setting;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Utility\Status;
use App\Models\Utility\Transport\TransportItem;
use App\Models\Utility\Transport\TransportPackingForm;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;


class ProductRequestForm extends Model
{

    use HasFactory;

    protected $table = "product_request_forms";
    protected $fillable = [
        "applicant_type_id",
        "applicant_id",
        "machine_id", // در آینده حذف می شود.
        "status_id",
        "allocation_id",
        "order_id",
        "user_id",
        "form_id",
        "warehouse_id",
        "coordinate_date_time",
        "active_status_id",
        "product_request_form_type_id",
        "code",
    ];
    public static $perfix_status_code = "7005";

    public function items()
    {
        return $this->hasMany(ProductRequestFormItem::class, "product_request_form_id");
    }

    public function transport_items()
    {
        return $this->hasMany(TransportItem::class);
    }

    public function forms()
    {
        return $this->hasMany(ProductRequestFormForm::class, "product_request_form_id");
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, "user_id", "id");
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function getStatus()
    {

        if ($this->status_id == 7005004) {
            $list = $this->getExistFormForConfirmList();
            $msg = "";
            foreach ($list as $item) {
                $msg .= $item->code . "(" . ProductRequestForm::getFormWaitingStatusList("caption", $item->status_id) . ")" . ", ";
            }
            $msg = trim($msg, ", ");

            return ($this->status->caption ?? "***") . " " . $msg;
        }

        return $this->status->caption ?? "";
    }

    public function getExistFormForConfirmList()
    {
        return $list = ProductRequestFormForm::join("forms", "form_id", "forms.id")->
        where("product_request_form_id", $this->id)->
        whereIn("forms.status_id", ProductRequestForm::getFormWaitingStatusList())->
        select("forms.code", "forms.status_id")->
        get();
    }

    public function form()
    {
        // این رکورد باید حذف شود، چون لیست فرم ها را داریم.
        return $this->belongsTo(Form::class);
    }

    /**
     * اطلاعات خاصی که برای یک درخواست می تواند وجود داشته باشد.
     */
    public function json_data()
    {
        return $this->belongsTo(JsonDataList::class, "json_data_id");
    }

    public static function getFormWaitingStatusList($type = "ids", $id = null)
    {
        switch ($type) {
            case "ids":
                return [
                    500000500, // در انتظار تایید درخواست کننده
                    500000514, //در انتظار تایید وصول مطالبات
                    500000515, //در انتظار تایید پیش نویس واحد مالی
                    500000520, //در انتظار تایید واحد نهای مالی
                    500000525, // در انتظار ثبت بارگیری واحد بارگیری
                    500000530, // در انتظار تایید خروج توسط نگهبانی
                    500000535, // در انتظار تایید کنترل کیفیت
                ];
                break;
            case "caption":
                $list = [
                    500000500 => "درخواست کننده", // در انتظار تایید درخواست کننده
                    500000514 => "وصول مطالبات", //در انتظار تایید پیش نویس واحد مالی
                    500000515 => "پیش نویس", //در انتظار تایید پیش نویس واحد مالی
                    500000520 => "نهایی", //در انتظار تایید واحد  نهای مالی
                    500000525 => "بارگیری", // در انتظار ثبت بارگیری واحد بارگیری
                    500000530 => "نگهبانی", // در انتظار تایید خروج توسط نگهبانی
                    500000535 => "کنترل کیفیت", // در انتظار تایید کنترل کیفیت
                ];

                return $list[$id];
                break;
        }

    }


    public function product_request_form_packing_types()
    {
        return $this->hasMany(ProductRequestFormPackingType::class);
    }

    public function allocation()
    {
        return $this->belongsTo(Allocation::class);
    }

    public function get_create_date_and_time()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');

    }

    public function coordinate_date_time()
    {
        if ($this->coordinate_date_time) {
            return jdate(Carbon::parse($this->coordinate_date_time)->timestamp)->format('H:i Y/m/d ');
        }

        return "";
    }

    public function getCode()
    {
        if (isset($this->code)) {
            return $this->code;
        }

// Request Product
        $this->code = "DCRP/" . (1000 + $this->id);
        $this->save();

        return $this->code;
    }


    public static function productCountInWarehouse($product_id, $applicant_type_id, $applicant_id, $product_request_form_id = null)
    {

        $packing_type_ids = ProductRequestForm:: // نوع بسته بندی های مجاز
        join("product_request_form_packing_type", "product_request_form_id", "product_request_forms.id")->
        where(["product_id" => $product_id])->
        where("applicant_type_id", $applicant_type_id)->
        when($product_request_form_id, function ($query) use ($product_request_form_id) {
            return $query->where("product_request_form_id", $product_request_form_id);
        })->
//        where( "applicant_id", $applicant_id )-> // الوکیشن حذف شد، چون به اشتباه بسته بندی های دیگری را در نظر می گرفت، برای درخواست هایی که از دستیار می آید شماره تخصیص نال است.
        groupBy("packing_type_id")->
        pluck("packing_type_id", "packing_type_id")->toArray();

        array_push($packing_type_ids, -1);

        $input_count = WarehouseProduct::
        join("warehouses", "warehouse_id", "warehouses.id")->
        where("warehouses.warehouse_type_id", 1)->
        where([
            "product_id" => $product_id,
            "output" => 0
        ])->
        whereIn("packing_type_id", $packing_type_ids)->
        count(); // ورود
        $output_count = WarehouseProduct::
        join("warehouses", "warehouse_id", "warehouses.id")->
        where("warehouses.warehouse_type_id", 1)->
        where([
            "product_id" => $product_id,
            "input" => 0
        ])->
        whereIn("packing_type_id", $packing_type_ids)->
        count(); // خروج

        $warps_request_form = ProductRequestForm:: // تعداد درخواست های ماژول پایان نیافته
        join("product_request_form_item", "product_request_form_id", "product_request_forms.id")->
        where(["product_id" => $product_id])->
        where("applicant_type_id", $applicant_type_id)->
        when($product_request_form_id, function ($query) use ($product_request_form_id) {
            return $query->where("product_request_form_id", "<", $product_request_form_id);
        })->
        whereNotIn("status_id", [
            7005002,//تحویل شده
            7005006, // کنسل شده
            7005009 // خاتمه یافته
        ])->
        count();

        return ($input_count - $output_count) - $warps_request_form;
    }

    public static function newRequest_old($allocation_or_order, $applicant_id, $applicant_type_id, $product_exist_in_warehouse, $other, $datetime, $message = "")
    {

//        $product_request_form = null;
//
//        switch ($applicant_type_id) {
//            case 10: // درخواست چله از طرف ماشین
//                $machine = Machine::find($applicant_id);
//                $result = ProductRequestForm::where([
//                    "allocation_id" => $allocation_or_order->id
//                ])->
//                whereNotIn("status_id", [
//                    ProductRequestForm::$perfix_status_code . "002",
//                    ProductRequestForm::$perfix_status_code . "006"
//                ])->
//                exists();
//
//                if ($result) {
//                    return [
//                        "result" => false,
//                        "message" => "یک درخواست باز برای کالا وجود دارد."
//                    ];
//                }
//
//                $machine_input_output_band = CurrentMachineInput::
//                where([
//                    "allocation_id" => $allocation_or_order->id,
//                    "machine_id" => $applicant_id,
//                    "goods_kind_id" => 3
//                ])->get();
//
//                foreach ($machine_input_output_band as $item) {
//
//                    $bom = $item->product->get_first_bom_from_route($item->machine);
//                    $bom_item = BOMItem::where([
//                        "bill_of_material_id" => $bom->id,
//                        "product_id" => $item->product_id,
//                        "material_id" => $item->material_id
//                    ])->
//                    first();
//
//                    // ایجاد فرم درخواست کالا در صورتی که وجود ندارد یا انبار آن متفاوت است.
//                    if ($product_request_form == null || $product_request_form->warehouse_id != $bom_item->warehouse_id) {
//                        $product_request_form = ProductRequestForm::create([
//                            "applicant_id" => $applicant_id,
//                            "applicant_type_id" => $applicant_type_id,
//                            "allocation_id" => $allocation_or_order->id,
//                            "status_id" => ProductRequestForm::$perfix_status_code . ($product_exist_in_warehouse ? "001" : "003"),
//                            "user_id" => \Auth::user()->id,
//                            "warehouse_id" => $bom_item->warehouse_id,
//                            "coordinate_date_time" => $datetime
//                        ]);
//                        $product_request_form->getCode();
//                        $product_request_forms[]=$product_request_form;
//                    }
//
//                    // ایجاد رکورد به ازای هر باند
//                    $warps_request_form_item = ProductRequestFormItem::create(
//                        [
//                            "product_request_form_id" => $product_request_form->id,
//                            "production_id" => $item->production_id,
//                            "product_id" => $item->material_id, // کد چله
//                            "degree_id" => $bom_item->material->getMasterDegree("id"), //
//                            "band_code" => $item->band_code,
//                            "input_line_code" => $item->input_line_code
//                        ]
//                    );
//
//
//                    // بسته بندی های مجاز به ازای هر باند
//                    $machine_type_packing_type_ids = MachineTypeInputBandPackingType::where([
//                        "machine_type_id" => $machine->machine_type_id,
//                        "goods_kind_id" => 3 // چله آهار شده
//                    ])->pluck("packing_type_id");
//                    foreach ($machine_type_packing_type_ids as $packing_type_id) {
//                        ProductRequestFormPackingType::create([
//                            "product_request_form_id" => $product_request_form->id,
//                            "product_request_form_item_id" => $warps_request_form_item->id,
//                            "product_id" => $item->material_id, // کد چله
//                            "degree_id" => $item->material->getMasterDegree("id"), //
//                            "packing_type_id" => $packing_type_id
//                        ]);
//                    }
//
//
//                }
//                $machineLog = new MachineLog();
//                $machineLog->machine_event_type_id = 580; //ثبت درخواست چله جدید توسط سیستم
//
//                $machineLog->save();
//                event(new MachineLogEvent($machine, $machineLog));
//
//                break;
//            case 20:// درخواست از طرف پیمان کار
//                $result = ProductRequestForm::where([
//                    "allocation_id" => $allocation_or_order->id
//                ])->
//                whereNotIn("status_id", [
//                    ProductRequestForm::$perfix_status_code . "002",
//                    ProductRequestForm::$perfix_status_code . "006"
//                ])->
//                exists();
//
//                if ($result) {
//                    return [
//                        "result" => false,
//                        "message" => "یک درخواست باز برای کالا وجود دارد."
//                    ];
//                }
//                $line_product_station = $other;
//                $product_request_form = null;
//                $allocation_item = $allocation_or_order->items()->first();
//                $allocation_amount = $allocation_item->allocation_amount; //  مقدار تخصیص
//
//                // گرفتن اولین BOM
//                $bom = BOM::where("product_route_id", $line_product_station->product_route_id)->first();
//
//                foreach ($bom->items()->orderBy("warehouse_id")->get() as $bom_item) {
//
//                    // ایجاد فرم درخواست کالا در صورتی که وجود ندارد یا انبار آن متفاوت است.
//                    if ($product_request_form == null || $product_request_form->warehouse_id != $bom_item->warehouse_id) {
//                        $product_request_form = ProductRequestForm::create([
//                            "applicant_id" => $applicant_id,
//                            "applicant_type_id" => $applicant_type_id,
//                            "allocation_id" => $allocation_or_order->id,
//                            "status_id" => ProductRequestForm::$perfix_status_code . ($product_exist_in_warehouse ? "001" : "003"),
//                            "user_id" => \Auth::user()->id,
//                            "warehouse_id" => $bom_item->warehouse_id,
//                            "coordinate_date_time" => $datetime,
//                            "order_id" => $allocation_item->production->order_id ?? null
//                        ]);
//                        $product_request_form->getCode();
//                        $product_request_forms[]=$product_request_form;
//                    }
//                    // ایجاد رکورد به ازای هر ردیف BOM
//                    $product_request_form_item = ProductRequestFormItem::create(
//                        [
//                            "product_request_form_id" => $product_request_form->id,
//                            // چون برای پیمانکاری فقط یک ردیف ایجاد می شود.
//                            "production_id" => $allocation_or_order->items()->first()->production_id,
//                            "product_id" => $bom_item->material_id, //
//                            "degree_id" => $bom_item->material->getMasterDegree("id"), //
//                            "amount_request" => $bom_item->amount * $allocation_amount, //
//                            "amount_remaining" => $bom_item->amount * $allocation_amount, //
//                        ]
//                    );
//
//
//                    // بسته بندی های مجاز به ازای هر ردیف
//                    foreach ($bom_item->material->packing_types as $packing_type) {
//                        ProductRequestFormPackingType::create([
//                            "product_request_form_id" => $product_request_form->id,
//                            "product_request_form_item_id" => $product_request_form_item->id,
//                            "product_id" => $bom_item->material_id,
//                            "packing_type_id" => $packing_type->id
//                        ]);
//                    }
//                }
//
//                break;
//            case 30:// درخواست از طرف مشتری و فروش
//
//                $result = ProductRequestForm::where([
//                    "applicant_type_id" => $applicant_type_id,
//                    "applicant_id" => $applicant_id,
//                    "order_id" => $allocation_or_order->id
//                ])->
//                exists();
//
//                if ($result) {
//                    return [
//                        "result" => false,
//                        "message" => "یک درخواست باز برای سفارش وجود دارد."
//                    ];
//                }
//                $product_request_form = null;
//
//                foreach ($allocation_or_order->orderList()->get() as $order_list) {
//                    $warehouse_id = $order_list->degree->warehouse_id;
//                    // ایجاد فرم درخواست کالا در صورتی که وجود ندارد یا انبار آن متفاوت است.
//                    if ($product_request_form == null || $product_request_form->warehouse_id != $warehouse_id) {
//                        $product_request_form = ProductRequestForm::create([
//                            "applicant_id" => $applicant_id,
//                            "applicant_type_id" => $applicant_type_id,
//                            "order_id" => $allocation_or_order->id,
//                            "status_id" => ProductRequestForm::$perfix_status_code . ($product_exist_in_warehouse ? "001" : "003"),
//                            "user_id" => \Auth::user()->id,
//                            "warehouse_id" => $warehouse_id,
//                            "coordinate_date_time" => $datetime
//                        ]);
//                        $product_request_form->getCode();
//                        $product_request_forms[]=$product_request_form;
//                    }
//
//                    // ایجاد رکورد به ازای هر ردیف Order
//                    $product_request_form_item = ProductRequestFormItem::create(
//                        [
//                            "product_request_form_id" => $product_request_form->id,
//                            "order_list_id" => $order_list->id,
//                            "product_id" => $order_list->product_id, //
//                            "amount_request" => $order_list->amount, //
//                            "amount_remaining" => $order_list->amount, //
//                            "degree_id" => $order_list->degree_id, //
//                        ]
//                    );
//
//                    ProductRequestFormPackingType::create([
//                        "product_request_form_id" => $product_request_form->id,
//                        "product_request_form_item_id" => $product_request_form_item->id,
//                        "product_id" => $order_list->product_id,
//                        "packing_type_id" => $order_list->packing_type_id
//                    ]);
//                }
//
//                break;
//            case 40:
////                return ProductRequestForm::newRequest1( $allocation_or_order, $applicant_id, $applicant_type_id, $product_exist_in_warehouse, $other, $datetime, $message );
//                break;
//            default:
//                1 / 0;
//
//        }
//
//
//        event(new ProductRequestFormLogEvent($product_request_form, $message, null, 7005001));
//
//        return [
//            "result" => true,
//            "product_request_form" => $product_request_form,
//        ];

    }

    public static function newRequest($allocation_or_order, $applicant_id, $applicant_type_id, $product_exist_in_warehouse, $other, $datetime, $message = "", $product_request_form_status_id = null)
    {

        $product_request_form = null;
        $product_request_forms = null;

        switch ($applicant_type_id) {
            case 10: // درخواست چله از طرف ماشین
                $machine = Machine::find($applicant_id);
                $result = ProductRequestForm::where([
                    "allocation_id" => $allocation_or_order->id
                ])->
                whereNotIn("status_id", [
                    ProductRequestForm::$perfix_status_code . "002",
                    ProductRequestForm::$perfix_status_code . "006"
                ])->
                exists();

                if ($result) {
                    return [
                        "result" => false,
                        "message" => "یک درخواست باز برای کالا وجود دارد."
                    ];
                }

                $machine_input_output_band = CurrentMachineInput::
                where([
                    "allocation_id" => $allocation_or_order->id,
                    "machine_id" => $applicant_id,
                    "goods_kind_id" => 3
                ])->get();

                foreach ($machine_input_output_band as $item) {

                    $bom = $item->product->get_first_bom_from_route($item->machine);
                    $bom_item = BOMItem::where([
                        "bill_of_material_id" => $bom->id,
                        "product_id" => $item->product_id,
                        "material_id" => $item->material_id
                    ])->
                    first();

                    // ایجاد فرم درخواست کالا در صورتی که وجود ندارد یا انبار آن متفاوت است.
                    if ($product_request_form == null || $product_request_form->warehouse_id != $bom_item->warehouse_id) {
                        $product_request_form = ProductRequestForm::create([
                            "applicant_id" => $applicant_id,
                            "applicant_type_id" => $applicant_type_id,
                            "allocation_id" => $allocation_or_order->id,
                            "status_id" => ProductRequestForm::$perfix_status_code . ($product_exist_in_warehouse ? "001" : "003"),
                            "user_id" => \Auth::user()->id,
                            "warehouse_id" => $bom_item->warehouse_id,
                            "coordinate_date_time" => $datetime
                        ]);
                        $product_request_form->getCode();
                        $product_request_forms[] = $product_request_form;
                    }

                    // ایجاد رکورد به ازای هر باند
                    $warps_request_form_item = ProductRequestFormItem::create(
                        [
                            "product_request_form_id" => $product_request_form->id,
                            "production_id" => $item->production_id,
                            "product_id" => $item->material_id, // کد چله
                            "degree_id" => $bom_item->material->getMasterDegree("id"), //
                            "band_code" => $item->band_code,
                            "input_line_code" => $item->input_line_code
                        ]
                    );


                    // بسته بندی های مجاز به ازای هر باند
                    $machine_type_packing_type_ids = MachineTypeInputBandPackingType::where([
                        "machine_type_id" => $machine->machine_type_id,
                        "goods_kind_id" => 3 // چله آهار شده
                    ])->pluck("packing_type_id");
                    foreach ($machine_type_packing_type_ids as $packing_type_id) {
                        ProductRequestFormPackingType::create([
                            "product_request_form_id" => $product_request_form->id,
                            "product_request_form_item_id" => $warps_request_form_item->id,
                            "product_id" => $item->material_id, // کد چله
                            "degree_id" => $item->material->getMasterDegree("id"), //
                            "packing_type_id" => $packing_type_id
                        ]);
                    }


                }
                $machineLog = new MachineLog();
                $machineLog->machine_event_type_id = 580; //ثبت درخواست چله جدید توسط سیستم

                $machineLog->save();
                event(new MachineLogEvent($machine, $machineLog));

                break;
            case -10: // درخواست کالا از طرف ماشین به انبارک
                // در زمانی که نوع درخواست 40 راه اندازی شد، این نوع درخواست حذف است.
                $machine = Machine::find($applicant_id);

                $result = ProductRequestForm::where([
                    "allocation_id" => $allocation_or_order->id
                ])->
                whereNotIn("status_id", [
                    ProductRequestForm::$perfix_status_code . "002",
                    ProductRequestForm::$perfix_status_code . "006"
                ])->
                exists();

                if ($result) {
                    return [
                        "result" => false,
                        "message" => "یک درخواست باز برای کالا وجود دارد."
                    ];
                }


                // در لیست ورودی های ماشین، به ازای هر کالا از انبارک درخواست می دهیم.
                $machine_input_output_band = CurrentMachineInput::
                where([
                    "allocation_id" => $allocation_or_order->id,
                    "machine_id" => $applicant_id,
                ])->
                groupBy("material_id")->
                select("*")->
                selectRaw("count(material_id) input_line_count, sum(amount) as sum_amount_request")->
                get();

                // ایجاد فرم درخواست کالا به انبارک ماشین.
                $product_request_form = ProductRequestForm::create([
                    "applicant_id" => $applicant_id,
                    "applicant_type_id" => $applicant_type_id,
                    "allocation_id" => $allocation_or_order->id,
                    "status_id" => ProductRequestForm::$perfix_status_code . ($product_exist_in_warehouse ? "001" : "003"),
                    "user_id" => \Auth::user()->id,
                    "warehouse_id" => $machine->warehouse_id,
                    "coordinate_date_time" => $datetime
                ]);
                $product_request_form->getCode();

                foreach ($machine_input_output_band as $item) {

                    $bom = $item->product->get_first_bom_from_route($item->machine);
                    $bom_item = BOMItem::where([
                        "bill_of_material_id" => $bom->id,
                        "product_id" => $item->product_id,
                        "material_id" => $item->material_id
                    ])->
                    first();

                    // آبا برای هر کارت تولید امکان مصرف ماده با لات های مختلف وجود دارد؟
                    $machine_type_input_band = MachineTypeInputBand::
                    join("machine_type_input_band_goods_kind", "machine_type_input_band_id", "machine_type_input_bands.id")->
                    where([
                        "goods_kind_id" => $item->material->goods_kind_id,
                        "machine_type_id" => $machine->machine_type_id
                    ])->
                    select("can_used_material_with_different_lot_per_production_card")->
                    first();
                    if ($machine_type_input_band) {
                        $can_used_material_with_different_lot_per_production_card =
                            $machine_type_input_band["can_used_material_with_different_lot_per_production_card"];
                    } else {
                        $can_used_material_with_different_lot_per_production_card = 1;
                    }


                    // ایجاد رکورد به ازای هر باند
                    $warps_request_form_item = ProductRequestFormItem::create(
                        [
                            "product_request_form_id" => $product_request_form->id,
                            "production_id" => $item->production_id,
                            "product_id" => $item->material_id,
                            // کد کالا در BOM

                            "degree_id" => $item->material->getMasterDegree("id"),
                            //
                            "band_code" => -1,
                            "input_line_code" => null,
                            "amount_request" => $item->sum_amount_request,
                            // مقدار درخواست برابر است مجموع مقدار در همه خطهای ووردی برای کالا
                            "min_number_of_packing_forms" => $item->input_line_count,
                            // حداقل بسته بندی که باید تحویل شود.
                            "can_deliver_material_with_different_lot" => $can_used_material_with_different_lot_per_production_card
                        ]
                    );


                    // بسته بندی های مجاز به ازای هر باند
                    $machine_type_packing_type_ids = MachineTypeInputBandPackingType::where([
                        "machine_type_id" => $machine->machine_type_id,
                        "goods_kind_id" => $item->material->goods_kind_id
                    ])->pluck("packing_type_id");
                    foreach ($machine_type_packing_type_ids as $packing_type_id) {
                        ProductRequestFormPackingType::create([
                            "product_request_form_id" => $product_request_form->id,
                            "product_request_form_item_id" => $warps_request_form_item->id,
                            "product_id" => $item->material_id, // کد چله
                            "degree_id" => $item->material->getMasterDegree("id"), //
                            "packing_type_id" => $packing_type_id
                        ]);
                    }


                }


                $machineLog = new MachineLog();
                $machineLog->machine_event_type_id = 20; // ثبت درخواست کالا از انبارک

                $machineLog->save();
                event(new MachineLogEvent($machine, $machineLog));

                break;
            case 20:// درخواست از طرف پیمان کار

                $user_id = isset($other["user_id"]) ? $other["user_id"] : \Auth::user()->id;
                //$line_product_station = $other["line_product_station"];

                $result = ProductRequestForm::where([
                    "allocation_id" => $allocation_or_order->id,
                    "applicant_type_id" => $applicant_type_id,
                ])->
                whereNotIn("status_id", [
                    ProductRequestForm::$perfix_status_code . "002",
                    ProductRequestForm::$perfix_status_code . "006"
                ])->
                exists();

                if ($result) {
                    return [
                        "result" => false,
                        "message" => "یک درخواست باز برای کالا وجود دارد.",
                        "error" => "یک درخواست باز برای کالا وجود دارد."
                    ];
                }

                $product_request_form = null;
                foreach ($allocation_or_order->items as $allocation_item) {

                    $allocation_amount = $allocation_item->allocation_amount; //  مقدار تخصیص

                    $line_product_station = LineProductStation::where("product_id", $allocation_item->product_id)->
                    where("contractor_id", $applicant_id)->first();
                    // گرفتن اولین BOM
                    $bom = BOM::where("product_route_id", $line_product_station->product_route_id ?? 0)->first();
                    if (!$bom) {
                        SMSMessage::ExceptionError("در زمان درخواست کالا از انبار برای تخصیص " . $allocation_item->allocation_id . " مقدار BOM " . $allocation_item->product->code . " یافت نشد.");
                    } else {
                        foreach ($bom->items()->orderBy("warehouse_id")->get() as $bom_item) {

                            // ایجاد فرم درخواست کالا در صورتی که وجود ندارد یا انبار آن متفاوت است.
                            if ($product_request_form == null || $product_request_form->warehouse_id != $bom_item->warehouse_id) {
                                $product_request_form = ProductRequestForm::create([
                                    "applicant_id" => $applicant_id,
                                    "applicant_type_id" => $applicant_type_id,
                                    "allocation_id" => $allocation_or_order->id,
                                    "status_id" => ProductRequestForm::$perfix_status_code . ($product_exist_in_warehouse ? "001" : "003"),
                                    "user_id" => $user_id,
                                    "warehouse_id" => $bom_item->warehouse_id,
                                    "coordinate_date_time" => $datetime,
                                    "order_id" => $allocation_item->production->order_id ?? null
                                ]);
                                $product_request_form->getCode();
                                $product_request_forms[] = $product_request_form;
                            }
                            // ایجاد رکورد به ازای هر ردیف BOM
                            $product_request_form_item = ProductRequestFormItem::create(
                                [
                                    "product_request_form_id" => $product_request_form->id,
                                    "production_id" => $allocation_item->production_id,
                                    "product_id" => $bom_item->material_id, //
                                    "amount_request" => ceil($bom_item->amount * $allocation_amount), //
                                    "amount_remaining" => ceil($bom_item->amount * $allocation_amount), //
                                ]
                            );


                            $master_degree_id = $bom_item->material->getMasterDegree("id");

                            // بسته بندی های مجاز به ازای هر ردیف
                            foreach ($bom_item->material->packing_types as $packing_type) {
                                ProductRequestFormPackingType::create([
                                    "product_request_form_id" => $product_request_form->id,
                                    "product_request_form_item_id" => $product_request_form_item->id,
                                    "product_id" => $bom_item->material_id,
                                    "packing_type_id" => $packing_type->id,
                                    "degree_id" => $master_degree_id, //
                                ]);
                            }
                        }
                    }
                }
                break;
            case
            30:// درخواست از طرف مشتری و فروش

                $user_id = isset($other["user_id"]) ? $other["user_id"] : \Auth::user()->id;
                $result = ProductRequestForm::where([
                    "applicant_type_id" => $applicant_type_id,
                    "applicant_id" => $applicant_id,
                    "order_id" => $allocation_or_order->id
                ])->
                exists();

                if ($result && !isset($other)) {
                    return [
                        "result" => false,
                        "message" => "یک درخواست باز برای سفارش وجود دارد."
                    ];
                }

                $product_request_form = null;

                // برای اینکه انبار درخواست کالا را تشخصی دهد از لیست تعرفه انبار را انتخاب می کند.
                $product_tariff = ProductTariffLog::where("tariff_log_id", $allocation_or_order->tariff_log_id)->pluck("warehouse_id", "product_id")->toArray();

                foreach ($allocation_or_order->orderList()->get() as $order_list) {

                    // اگر مقدار other ست شده باشد یعنی اینکه نباید به اندازه کل سفارش درخواست خروج ثبت کنیم و هر کالایی که در درخواست بود درخواست خروج آن را ثبت می کنیم.
                    if (isset($other) && !isset($other["order_list"][$order_list->id])) { //
                        //اگر نبایستی برای ردیف سفارش درخواست ثبت کنیم از آن رد می شویم.
                        continue;

                    }
                    //محاسبه مقدار
                    if (isset($other)) {
                        $order_list_amount = $other["order_list"][$order_list->id];
                    } else {
                        $order_list_amount = $order_list->amount;
                    }

                    // اگر انبار به هر دلیل وجود نداشت از پیش فرض درجه کالا انتخاب می کند.
                    $warehouse_id = isset($product_tariff[$order_list->product_id]) ? $product_tariff[$order_list->product_id] : $order_list->degree->warehouse_id;

                    // ایجاد فرم درخواست کالا در صورتی که وجود ندارد یا انبار آن متفاوت است.
                    if ($product_request_form == null || $product_request_form->warehouse_id != $warehouse_id) {
                        $product_request_form = ProductRequestForm::create([
                            "applicant_id" => $applicant_id,
                            "applicant_type_id" => $applicant_type_id,
                            "order_id" => $allocation_or_order->id,
                            "status_id" => ProductRequestForm::$perfix_status_code . ($product_exist_in_warehouse ? "001" : "003"),
                            "user_id" => $user_id,
                            "warehouse_id" => $warehouse_id,
                            "coordinate_date_time" => $datetime,
                            "product_request_form_type_id" => isset($other) ? 2 : 1 // نوع درخواست به انبار
                        ]);
                        $product_request_form->getCode();
                        $product_request_forms[] = $product_request_form;
                    }

                    // ایجاد رکورد به ازای هر ردیف Order
                    $product_request_form_item = ProductRequestFormItem::create(
                        [
                            "product_request_form_id" => $product_request_form->id,
                            "order_list_id" => $order_list->id,
                            "product_id" => $order_list->product_id, //
                            "amount_request" => $order_list_amount, //
                            "amount_remaining" => $order_list_amount, //
                        ]
                    );

                    foreach ($order_list->getPackingType("packing_type_ids") as $packing_type_id) {
                        ProductRequestFormPackingType::create([
                            "product_request_form_id" => $product_request_form->id,
                            "product_request_form_item_id" => $product_request_form_item->id,
                            "product_id" => $order_list->product_id,
                            "packing_type_id" => $packing_type_id,
                            "degree_id" => $order_list->degree_id, //
                        ]);
                    }
                }

                break;

            case 40: // درخواست از طرف انبارک است.

                // other=["warehouse_id"=>1 , "material_id_list"=>[],"degree_id_list"=>[]]
                $warehouse_id = $other["warehouse_id"];
                $material_list = $other["material_list"];
                $degree_id_list = $other["degree_id_list"];
                $goods_kind_id_list = $other["goods_kind_id_list"];

                // حذاقل / حداکثر مجاز تعداد بسته بندی
                $warehouse_material_packing_count = $other["warehouse_material_packing_count"];
                $warehouse_material_packing_count_max = isset($other["warehouse_material_packing_count_max"]) ? $other["warehouse_material_packing_count_max"] : null;
                $machine = $other["machine"];
                $user_id = $other["user_id"];
                $log_script = $other["log_script"];
                $product_request_form_is_enabled = $other["product_request_form_is_enabled"];
                $allocation_id = isset($other["allocation_id"]) ? $other["allocation_id"] : null;
                $remove_old_request = isset($other["remove_old_request"]) ? $other["remove_old_request"] : 1;

                // درخواست های قبلی را حذف می کنیم.
                $first_material_id = array_key_first($material_list);
                $first_material = Product::find($first_material_id);
                $goods_kind_id = $first_material->goods_kind_id ?? 0;

                if ($remove_old_request) { // آیا درخواست های قبلی حذف شوند
                    $old_requests = ProductRequestForm::
                    join("product_request_form_item", "product_request_forms.id", "product_request_form_id")->
                    join("products", "products.id", "product_id")->
                    where([
                        "product_request_forms.applicant_type_id" => 40,
                        "product_request_forms.applicant_id" => $applicant_id,
                        "product_request_forms.warehouse_id" => $warehouse_id
                    ])->
                    whereNotIn("product_request_forms.status_id", [7005002, 7005009, 7005003])-> // خاتمه یافته , تحویل شده و تکمیل موجودی
                    where("goods_kind_id", $goods_kind_id)->
                    select("product_request_forms.*")->
                    groupBy("product_request_forms.id")->
                    get();

                    foreach ($old_requests as $request) {

                        // اگر درخواست کالا از طرف انبارک بر اساس تخصیص می باشد، وقتی برای یک تخصیص درخواست صادر می شود، نباید درخواست تخصیص های دیگر کنسل شود، و فقط باید تخصیس های همان تخصیص که از قبل بوده کنسل شود.
                        if ($allocation_id && $request->allocation_id && $request->allocation_id != $allocation_id) {
                            continue;
                        }

                        $request->status_id = 7005009; // خاتمه یافته
                        $request->save();
                        event(new ProductRequestFormLogEvent($request, "", null, 7005016, $user_id));
                    }
                }


                // ثبت درخواست جدید
                $product_request_form = ProductRequestForm::create([
                    "applicant_id" => $applicant_id,
                    "applicant_type_id" => $applicant_type_id,
                    "status_id" => isset($product_request_form_status_id) ?
                        $product_request_form_status_id : ProductRequestForm::$perfix_status_code . "001",
                    "user_id" => $user_id,
                    "warehouse_id" => $warehouse_id,
                    "coordinate_date_time" => $datetime,
                    "allocation_id" => $allocation_id
                ]);

                $product_request_form->getCode();
                $product_request_forms[] = $product_request_form;

                // ذخیره شماره فرم درخواست در لاگ اسکریپت 1007
                if (isset($log_script)) {
                    $log_script->other_id = $product_request_form->id;
                    $log_script->save();
                } else {
                    $other["machine_id"] = $machine->id;
                    unset($other["machine"]);
                    // اطلاعات Other را در یک لاگ جدید ذخیره می کنیم.
                    event(new ProductRequestFormLogEvent($product_request_form, "", null, 7005019, $user_id ?? null, $other));

                }

                $active_status_id = 7005102; // غیر فعال
                foreach ($material_list as $material_id => $amount) {

                    if (!is_null($amount) && round($amount, 4) <= 0) {
                        // اگر مقدار محاسبه شده، کمتر .0001 بود، مقدار آن را نادیده می گیریم.
                        continue;
                    }
                    // ایجاد رکورد به ازای هر ردیف
                    $product_request_form_item = ProductRequestFormItem::create(
                        [
                            "product_request_form_id" => $product_request_form->id,
                            "product_id" => $material_id,
                            "amount_request" => is_null($amount) ? null : round($amount, 4),
                            "amount_remaining" => is_null($amount) ? null : round($amount, 4),
                            "min_number_of_packing_forms" => $warehouse_material_packing_count[$warehouse_id][$material_id],
                            "max_number_of_packing_forms" => isset($warehouse_material_packing_count_max[$warehouse_id][$material_id]) ? $warehouse_material_packing_count_max[$warehouse_id][$material_id] : null
                        ]
                    );

                    // بسته بندی های مجاز به ازای هر باند
                    $machine_type_packing_type_ids = MachineTypeInputBandPackingType::where([
                        "machine_type_id" => $machine->machine_type_id,
                        "goods_kind_id" => $goods_kind_id_list[$material_id]
                    ])->pluck("packing_type_id");

                    foreach ($machine_type_packing_type_ids as $packing_type_id) {

                        foreach ($degree_id_list[$warehouse_id][$material_id] as $degree_id) {
                            ProductRequestFormPackingType::create([
                                "product_request_form_id" => $product_request_form->id,
                                "product_request_form_item_id" => $product_request_form_item->id,
                                "product_id" => $material_id, // کد چله
                                "degree_id" => $degree_id, //
                                "packing_type_id" => $packing_type_id
                            ]);
                        }
                    }

                    // بررسی انیکه وضعیت پیش فرض درخواست فعال باشد یا غیر فعال
                    if ($product_request_form_is_enabled) {
                        $active_status_id = 7005101; // فعال
                    }
                }


                $product_request_form->active_status_id = $active_status_id;
                $product_request_form->save();
                break;

            case 60:// در خواست از طرف تامین کننده بابت تحویل امانی (قرض)
                $user_id = isset($other["user_id"]) ? $other["user_id"] : \Auth::user()->id;
                $input_form_id = $other["form_id"]; // فرم کلی ورودی که برای وورد تامین کننده زده ایم.

                $input_form = Form::find($input_form_id);

                $result = ProductRequestForm::where([
                    "allocation_id" => $allocation_or_order->id,
                    "applicant_type_id" => $applicant_type_id,
                ])->
                whereNotIn("status_id", [
                    ProductRequestForm::$perfix_status_code . "002",
                    ProductRequestForm::$perfix_status_code . "006"
                ])->
                exists();

                if ($result) {
                    return [
                        "result" => false,
                        "message" => "یک درخواست باز برای کالا وجود دارد."
                    ];
                }

                $product_request_form = null;
                $allocation_item = $allocation_or_order->items()->first();
                $allocation_amount = $allocation_item->allocation_amount; //  مقدار تخصیص


                foreach ($input_form->general_items as $general_item) {

                    // ایجاد فرم درخواست کالا در صورتی که وجود ندارد یا انبار آن متفاوت است.
                    if ($product_request_form == null) {
                        $product_request_form = ProductRequestForm::create([
                            "applicant_id" => $applicant_id,
                            "applicant_type_id" => $applicant_type_id,
                            "allocation_id" => $allocation_or_order->id,
                            "status_id" => ProductRequestForm::$perfix_status_code . ($product_exist_in_warehouse ? "001" : "003"),
                            "user_id" => $user_id,
                            "warehouse_id" => $input_form->warehouse_id,
                            "coordinate_date_time" => $datetime,
                            "order_id" => null
                        ]);
                        $product_request_form->getCode();
                        $product_request_forms[] = $product_request_form;
                    }
                    // ایجاد رکورد به ازای هر ردیف BOM
                    $product_request_form_item = ProductRequestFormItem::create(
                        [
                            "product_request_form_id" => $product_request_form->id,
                            // چون برای پیمانکاری فقط یک ردیف ایجاد می شود.
                            "production_id" => $allocation_or_order->items()->first()->production_id,
                            "product_id" => $general_item->product_id, //
                            "amount_request" => $general_item->amount, //
                            "amount_remaining" => $general_item->amount, //
                        ]
                    );


                    // بسته بندی های مجاز به ازای هر ردیف
                    ProductRequestFormPackingType::create([
                        "product_request_form_id" => $product_request_form->id,
                        "product_request_form_item_id" => $product_request_form_item->id,
                        "product_id" => $general_item->product_id,
                        "packing_type_id" => $general_item->packing_type_id,
                        "degree_id" => $general_item->degree_id, //
                    ]);

                }

                break;
            case 70: // برگشت از خرید
                $user_id = isset($other["user_id"]) ? $other["user_id"] : \Auth::user()->id;
                $input_form_id = $other["form_id"]; // فرم کلی ورودی که برای وورد تامین کننده زده ایم.

                $input_form = Form::find($input_form_id);

                $result = ProductRequestForm::where([
                    "form_id" => $input_form_id,
                    "applicant_type_id" => $applicant_type_id,
                ])->
                whereNotIn("status_id", [
                    ProductRequestForm::$perfix_status_code . "002",
                    ProductRequestForm::$perfix_status_code . "006"
                ])->
                exists();

                if ($result) {
                    return [
                        "result" => false,
                        "message" => "یک درخواست باز برای کالا وجود دارد."
                    ];
                }

                $product_request_form = null;

                $form_item_list = $input_form->item()->
                groupBy("product_id")->
                selectRaw("sum(amount) as amount,id, product_id,degree_id,packing_form_item_id")->
                get();
                foreach ($form_item_list as $form_item) {

                    // ایجاد فرم درخواست کالا در صورتی که وجود ندارد یا انبار آن متفاوت است.
                    if ($product_request_form == null) {
                        $product_request_form = ProductRequestForm::create([
                            "applicant_id" => $applicant_id,
                            "applicant_type_id" => $applicant_type_id,
                            "form_id" => $input_form->id,
                            "status_id" => ProductRequestForm::$perfix_status_code . ($product_exist_in_warehouse ? "001" : "003"),
                            "user_id" => $user_id,
                            "warehouse_id" => $input_form->warehouse_id,
                            "coordinate_date_time" => $datetime,
                            "order_id" => null
                        ]);
                        $product_request_form->getCode();
                        $product_request_forms[] = $product_request_form;
                    }
                    // ایجاد رکورد به ازای هر ردیف BOM
                    $product_request_form_item = ProductRequestFormItem::create(
                        [
                            "product_request_form_id" => $product_request_form->id,
                            "product_id" => $form_item->product_id, //
                            "amount_request" => $form_item->amount, //
                            "amount_remaining" => $form_item->amount, //
                        ]
                    );

                    // بسته بندی های مجاز به ازای هر ردیف
                    ProductRequestFormPackingType::create([
                        "product_request_form_id" => $product_request_form->id,
                        "product_request_form_item_id" => $product_request_form_item->id,
                        "product_id" => $form_item->product_id,
                        "packing_type_id" => $form_item->packing_form_item->packing_form->packing_type_id,
                        "degree_id" => $form_item->degree_id, //
                    ]);

                }

                break;

            case 80: // درخواست متفرقه ویژه دوره پیاده سازی
                $user_id = isset($other["user_id"]) ? $other["user_id"] : \Auth::user()->id;


                $product_request_form = null;

                $form_item_list = "";


                $product_request_form = ProductRequestForm::create([
                    "applicant_id" => $applicant_id,
                    "applicant_type_id" => $applicant_type_id,

                    "status_id" => ProductRequestForm::$perfix_status_code . ($product_exist_in_warehouse ? "001" : "003"),
                    "user_id" => $user_id,
                    "warehouse_id" => $other["warehouse_id"],
                    "coordinate_date_time" => $datetime,
                    "order_id" => null
                ]);
                $product_request_form->getCode();
                $product_request_forms[] = $product_request_form;

                // اطلاعات اضافه درخواست: از آنجایی که درخواست مربوط به دوره پیاده سازی است، مرکز هزنیه و نوع تراکنش را از کاربر دریافت می کنیم.
                $json_data = [
                    "cost_center_id" => $other["cost_center_id"],
                    "trans_kind_id" => $other["trans_kind_id"],
                ];
                $json_data_list = JsonDataList::create([
                    "message_type_id" => 330,
                    "other_id" => $product_request_form->id,
                    "data" => json_encode($json_data)
                ]);

                $product_request_form->json_data_id = $json_data_list->id;
                $product_request_form->save();


                foreach ($other["product_ids"] as $product_id) {
                    // ایجاد رکورد به ازای هر ردیف در درخواست
                    $product_request_form_item = ProductRequestFormItem::create(
                        [
                            "product_request_form_id" => $product_request_form->id,
                            "product_id" => $product_id, //
                            "amount_request" => $other["product_amount_list"][$product_id], //
                            "amount_remaining" => $other["product_amount_list"][$product_id], //
                        ]
                    );

                    foreach ($other["packing_type_list_select"][$product_id] as $packing_type_id => $val1) {
                        foreach ($other["degree_list_select"][$product_id] as $degree_id => $val2) {

                            // بسته بندی های مجاز به ازای هر ردیف
                            ProductRequestFormPackingType::create([
                                "product_request_form_id" => $product_request_form->id,
                                "product_request_form_item_id" => $product_request_form_item->id,
                                "product_id" => $product_id,
                                "packing_type_id" => $packing_type_id,
                                "degree_id" => $degree_id, //
                            ]);
                        }
                    }

                }
                break;


        }
        if (isset($product_request_form)) {
            event(new ProductRequestFormLogEvent($product_request_form, $message, null, 7005001, $user_id ?? null));
        }

        return [
            "result" => true,
            "product_request_form" => $product_request_form,
            "product_request_forms" => $product_request_forms,
        ];

    }

// عدم تایید فرم درخواست کالا
    public
    function rejectRequest($form = null, $user_id = null, $text = "")
    {


        $PRFF = ProductRequestFormForm::
        where("product_request_form_id", $this->id)->
        when($form, function ($query) use ($form) {
            return $query->where("form_id", $form->id);
        })->
        first();

        if (!$PRFF) {
            return ["result" => false, "error" => "فرم درخواست کالا یافت نشد."];
        }

        $form = $PRFF->form;

        // بررسی اینکه فرم درخواست کالا تراکنش انبار نداشته باشد
        $wp_row_exists = WarehouseProduct::where("form_id", $form->id)->exists();

        if ($wp_row_exists) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه تراکنش خروج برای برگ خروج " . $form->code . " صادر شده است، امکان عدم تایید وجود ندارد. "
            ];
        }

        $PRFF->form->status_id = 500000100;
        $PRFF->form->save();
        event(new FormLogEvent($PRFF->form, $text, $user_id));


        $PRFF_list = ProductRequestFormForm::where("form_id", $form->id)->get();
        foreach ($PRFF_list as $item) {
            // تغییر وضعیت فرم درخواست کالا
            $item->product_request_form->updateApplicantStatus(7005011, null, $user_id);// عدم تایید برگ خروج
            //عدم تایید برگ خروج
            event(new ProductRequestFormLogEvent($item->product_request_form, $text, $form->id, 7005011, $user_id));
        }


        foreach ($PRFF->form->item as $item) {
            ProductRequestForm::reverseWarehouseStatusFroPackingInReject($item->packing_form_item->packing_form);
        }

        return ["result" => true];
    }

    public
    static function reverseWarehouseStatusFroPackingInReject(PackingForm $packing_form)
    {

//        if ( in_array( $packing_form->warehouse_status_id, [ 4201, 4205 ] ) ) {
//            return;
//        }

        if ($packing_form->packing_form_master) {
//            $packing_form->warehouse_status_id = 4205; // داخل بسته بزرگتر
//            $packing_form->save();
// برای پدرش اجرای شود و وضعیت را بروز کند.
            ProductRequestForm::reverseWarehouseStatusFroPackingInReject($packing_form->packing_form_master);
        } else {
            $packing_form->warehouse_status_id = 4201; // داخل انبار
            $packing_form->save();
        }
    }

// کنسل کردن درخواست
    public
    function cancelRequest()
    {
        $this->status_id = 7005006; // کنسل شده
        $this->save();
        event(new ProductRequestFormLogEvent($this, "", null, 7005008));
    }


    public
    function checkIfValidConfirmRequest($form_id = null)
    {

        $product_request_form_form = ProductRequestFormForm::where(
            "product_request_form_id", $this->id
        )->
        when($form_id, function ($query) use ($form_id) {
            $query->where("form_id", $form_id);
        })->
        orderByDesc("id")->
        first();
        if (!$product_request_form_form) {
            return ["result" => false, "error" => "فرم درخواست یافت نشد."];
        }

        if (in_array($product_request_form_form->form->status_id, $this->getValidStatusForConformForm())) {
            $script1015 = Script::find(15); // اسکریپت ثبت تراکنش های سامانه
            if ($script1015 && $script1015->active_status_id == 1200) {
                // ==> Script 1015
                $product_request_form_form->warehouse_transaction_status_id = 500100100; // در انتظار ثبت تراکنش انبار(برگ خروج)
                $product_request_form_form->warehouse_transaction_user_id = Auth::user()->id;
                $product_request_form_form->save();
            } else {
                return $this->confirmRequest($form_id, $product_request_form_form);
            }
        }

        return ["result" => true];
    }

// تایید برگ خروج از انبار و ثبت فرم خروج
// ثبت تراکنش انبار
    public
    function confirmRequest($form_id = null, $product_request_form_form = null, $user_id = null)
    {

        if (!$product_request_form_form) {
            $product_request_form_form = ProductRequestFormForm::where(
                "product_request_form_id", $this->id
            )->
            when($form_id, function ($query) use ($form_id) {
                $query->where("form_id", $form_id);
            })->
            orderByDesc("id")->
            first();
            if (!$product_request_form_form) {
                return ["result" => false, "error" => "فرم درخواست یافت نشد."];
            }
        }

        // بررسی اینکه فرم درخواست کالا تراکنش انبار نداشته باشد
        $wp_row_exists = WarehouseProduct::where("form_id", $product_request_form_form->form->id)->exists();

        if ($wp_row_exists) {
            return [
                "result" => true,
                "warning" => "تراکنش برای فرم  " . $product_request_form_form->form->code . " ثبت شده است. "
            ];
        }

        $packing_form_ids_where_exit = [];

        switch ($this->applicant_type_id) {
            case 10:
                $product_request_form_list = [];
                // به روز رسانیRFW
                foreach ($product_request_form_form->form->item as $form_item) {

                    // بروز رسانی مقدار تحویل شده
                    $form_item->product_request_form_item->amount_sent += $form_item->amount;
                    $form_item->product_request_form_item->amount_remaining -= $form_item->amount;
                    if ($form_item->product_request_form_item->amount_remaining < 0) {
                        $form_item->product_request_form_item->amount_remaining = 0;
                    }
                    $form_item->product_request_form_item->save();

                    // تغییر وضعیت حامل
                    $form_item->carrier->SetStatus(
                        5320003, // در حال مصرف
                        $form_item->product_request_form_item->product_request_form->applicant->fullCaption(),
                        5320103,
                        null,
                        null,
                        null,
                        $user_id
                    );
                    $product_request_form = $form_item->product_request_form_item->product_request_form;
                    $product_request_form_list[] =
                        [
                            "product_request_form" => $product_request_form,
                            "form" => $product_request_form_form->form
                        ];

                    $packing_form_ids_where_exit[$form_item->packing_form_item->packing_form_id] = $form_item->packing_form_item->packing_form;
                }


                // تغییر دادن وضعیت درخواست های کالا از انبار
                foreach ($product_request_form_list as $prf_item) {

                    //تغییر وضعیت فرم درخواست کالا
                    $prf_item["product_request_form"]->status_id = WarpsRequestForm::$perfix_status_code . "002";
                    $prf_item["product_request_form"]->save();
                    event(new ProductRequestFormLogEvent($prf_item["product_request_form"], "", $prf_item["form"]->id, 7005012, $user_id));

                }


                break;
            case 60: // تامین کننده (تحویل امانی - قرض)
            case 70: // تامین کننده - برگشت از خرید
            case 20:// پیمانکار
            case 80: //خروج متفرقه
                $product_request_form_list = [];
                $packing_forms = [];
                // بروز رسانی مقدار فرم در فرم درخواست
                foreach ($product_request_form_form->form->item as $form_item) {

                    if ($form_item->product_request_form_item) {
                        $form_item->product_request_form_item->amount_sent += $form_item->amount;
                        $form_item->product_request_form_item->amount_remaining -= $form_item->amount;
                        if ($form_item->product_request_form_item->amount_remaining < 0) {
                            $form_item->product_request_form_item->amount_remaining = 0;
                        }
                        $form_item->product_request_form_item->save();
                        $product_request_form = ProductRequestForm::find($form_item->product_request_form_item->product_request_form_id);

                        $product_request_form_list[$product_request_form->id] =
                            [
                                "product_request_form" => $product_request_form,
                                "form" => $product_request_form_form->form
                            ];

                    }

                    // لیست بسته بندی های تحویل شده
                    if ($form_item->packing_form_item->packing_form_id && !isset($packing_forms[$form_item->packing_form_item->packing_form_id])) {
                        $packing_forms[$form_item->packing_form_item->packing_form_id] = $form_item->packing_form_item->packing_form;
                    }


                    $packing_form_ids_where_exit[$form_item->packing_form_item->packing_form_id] = $form_item->packing_form_item->packing_form;

                }


                // خالی کردن حامل های بسته بندی
                foreach ($packing_forms as $key => $item) {
                    if ($item->carrier) {
                        $item->carrier->SetEmpty($user_id);
                    }
                }


                // تغییر دادن وضعیت درخواست های کالا از انبار
                foreach ($product_request_form_list as $prf_item) {

                    //تغییر وضعیت فرم درخواست کالا
                    $prf_item["product_request_form"]->updateApplicantStatus(5310104, null, $user_id); // تایید دریافت مواد اولیه
                    event(new ProductRequestFormLogEvent($prf_item["product_request_form"], "", $prf_item["form"]->id, 7005012, $user_id));
                }


                break;
            case 30:// سفارش
                $product_request_form_list = [];
                // بروز رسانی مقدار فرم در فرم درخواست
                foreach ($product_request_form_form->form->item as $form_item) {

                    if ($form_item->product_request_form_item) {
                        $form_item->product_request_form_item->amount_sent += $form_item->amount;
                        $form_item->product_request_form_item->amount_remaining -= $form_item->amount;
                        if ($form_item->product_request_form_item->amount_remaining < 0) {
                            $form_item->product_request_form_item->amount_remaining = 0;
                        }
                        $form_item->product_request_form_item->save();
                        $product_request_form = ProductRequestForm::find($form_item->product_request_form_item->product_request_form_id);

                        $product_request_form_list[$product_request_form->id] =
                            [
                                "product_request_form" => $product_request_form,
                                "form" => $product_request_form_form->form
                            ];

                    }

                    $packing_form_ids_where_exit[$form_item->packing_form_item->packing_form_id] = $form_item->packing_form_item->packing_form;

                }


                // تغییر دادن وضعیت درخواست های کالا از انبار
                foreach ($product_request_form_list as $prf_item) {

                    //تغییر وضعیت فرم درخواست کالا
                    $prf_item["product_request_form"]->updateApplicantStatus(5310104, null, $user_id); // ثبت تراکنش انبار
                    event(new ProductRequestFormLogEvent($prf_item["product_request_form"], "", $prf_item["form"]->id, 7005012, $user_id));
                }

                break;
            case 40:// انبارک
                $product_request_form_list = [];
                // بروز رسانی مقدار فرم در فرم درخواست
                foreach ($product_request_form_form->form->item as $form_item) {

                    if ($form_item->product_request_form_item) {
                        $form_item->product_request_form_item->amount_sent += $form_item->amount;
                        $form_item->product_request_form_item->amount_remaining -= $form_item->amount;
                        if ($form_item->product_request_form_item->amount_remaining < 0) {
                            $form_item->product_request_form_item->amount_remaining = 0;
                        }
                        $form_item->product_request_form_item->save();
                        $product_request_form = ProductRequestForm::find($form_item->product_request_form_item->product_request_form_id);

                        $product_request_form_list[$product_request_form->id] =
                            [
                                "product_request_form" => $product_request_form,
                                "form" => $product_request_form_form->form
                            ];

                    }

                    $packing_form_ids_where_exit[$form_item->packing_form_item->packing_form_id] = $form_item->packing_form_item->packing_form;

                }


                // تغییر دادن وضعیت درخواست های کالا از انبار
                foreach ($product_request_form_list as $prf_item) {

                    //تغییر وضعیت فرم درخواست کالا
                    $prf_item["product_request_form"]->updateApplicantStatus(5310104, null, $user_id); // ثبت تراکنش انبار
                    event(new ProductRequestFormLogEvent($prf_item["product_request_form"], "", $prf_item["form"]->id, 7005012, $user_id));
                }

                // بروز کردن جدول RFW با توجه به مقداری که تحویل شده است.


                break;
            default:
                return ["result" => false, "error" => "این ماژول هنوز پیاده سازی نشده."];
        }

        //         ثبت برگ خروج از انبار در انبار
        event(new PutInWarehouseEvent($product_request_form_form->form));

        ProductRequestForm::updateMasterFormInConformExitForm($packing_form_ids_where_exit, $product_request_form_form->form->id, false, $user_id);


        // بعد از ثبت تراکنش خروج برای هر نوع درخواست کننده ای چه کاری باید انجام شود.
        switch ($this->applicant_type_id) {

            case 40:  // انبارک
                // ثبت  تراکنش ورود برای انبارک
                /********************/
                // ثبت فرم ورود به انبار برای تراکنش نقل انتقال از انبار به انبارک

                $exit_form = $product_request_form_form->form;
                $user_id = $user_id ?? Auth::id();


                $entry_form = Form::CreateFrom([
                    "user_id" => $user_id,
                    "form_type_id" => 304,
                    "status_id" => 500000410, // در انتظار تایید انبار
                    "trans_kind" => 5, // ورود متفرقه
                    "warehouse_id" => $this->applicant_id,
                    "ic" => $exit_form->warehouse->ic ?? ""
                ]);

                $entry_form->getCode("DCRF");
                event(new FormLogEvent($entry_form, "", $user_id));

                foreach ($exit_form->item as $form_item) {
                    FormItem::create([
                        "form_id" => $entry_form->id,
                        "packing_form_item_id" => $form_item->packing_form_item_id,
                        "packing_type_id" => $form_item->packing_type_id,
                        "product_id" => $form_item->product_id,
                        "amount" => $form_item->amount,
                        "sub_amount" => $form_item->sub_amount,
                        "carrier_id" => $form_item->carrier_id,
                        "degree_id" => $form_item->degree_id,
                        "lot_number_id" => $form_item->lot_number_id,
                        "description" => $form_item->description,
                    ]);


                }


                $entry_form = Form::find($entry_form->id); // چون آیتم های فرم تولید به صورت نادرست شناخته می شوند، یک بار دیگر فراخوانی می کنیم.


                event(new PutInWarehouseEvent($entry_form, null, null, true, $user_id));
                $entry_form->status_id = 500000200;
                $entry_form->save();
                event(new FormLogEvent($entry_form, "", $user_id));

                $packing_form_list = PackingForm::join("packing_form_item", "packing_form_item.packing_form_id", "packing_forms.id")->
                join("form_item", "form_item.packing_form_item_id", "packing_form_item.id")->
                where("form_item.form_id", $entry_form->id)->
                select("packing_forms.*")->get();


                $master_packing_form_ids = PackingForm::MasterPackingFormIds($packing_form_list);
                $master_packing_form_list = PackingForm::whereIn("id", $master_packing_form_ids)->get();

                foreach ($master_packing_form_list as $packing_form) {
                    $packing_form->status_id = 7007003; // تحویل شده به انبار
                    $packing_form->warehouse_id = $this->applicant_id;
                    $packing_form->save();
                    event(new PackingLogEvent($packing_form, 7007006, null, "", $entry_form->id, $user_id));
                }

                $master_is_null_packing_forms = PackingForm::MasterIsNullPackingForms($packing_form_list);
                foreach ($master_is_null_packing_forms as $packing_form_master_is_null) {
                    if (!isset($master_packing_form_ids[$packing_form_master_is_null->id])) {
                        $packing_form_master_is_null->status_id = 7007003; // تحویل شده به انبار
                        $packing_form_master_is_null->warehouse_id = $this->applicant_id;
                        $packing_form_master_is_null->save();
                        event(new PackingLogEvent($packing_form_master_is_null, 7007006, null, "", $entry_form->id, $user_id));
                    }
                }

                // مقدار تحویل شده را در جدول اسکریپت 1007 بروز می کنیم.
                ProductRequestFromAllocation::UpdateMaterialAmount($product_request_form, $entry_form);

                break;
        }

        return ["result" => true];
    }

    /**
     * @param $packing_form_ids_where_exit
     * @param $form_id
     * بعد از تایید نهایی برگ خروج باید وضعیت بسته بندی های اصلی به خارج شده تغییر کند.
     *
     * @return void
     */
    public
    static function updateMasterFormInConformExitForm($packing_form_ids_where_exit, $form_id, $empty_carrier = false, $user_id = null)
    {
        $master_packing_form_ids = PackingForm::MasterPackingFormIds($packing_form_ids_where_exit);
        $master_packing_form_list = PackingForm::whereIn("id", $master_packing_form_ids)->get();

        foreach ($master_packing_form_list as $item) {
            if (!$item->packing_form_master) {
                $item->status_id = 7007012; // خارج شده از انبار
                $item->warehouse_status_id = 4202; // خارج شده از انبار
                $item->warehouse_id = null;
                $item->save();
                event(new PackingLogEvent($item, 7007010, null, "", $form_id, $user_id));

                if ($empty_carrier && $item->carrier) {
                    $item->carrier->SetEmpty($user_id);
                }
            }

        }

        $master_is_null_packing_form = PackingForm::MasterIsNullPackingForms($packing_form_ids_where_exit);
        foreach ($master_is_null_packing_form as $packing_form_master_is_null) {
            if (!isset($master_packing_form_ids[$packing_form_master_is_null->id])) {
                $packing_form_master_is_null->status_id = 7007012; // خارج شده از انبار
                $packing_form_master_is_null->warehouse_status_id = 4202; // خارج شده از انبار
                $packing_form_master_is_null->warehouse_id = null;
                $packing_form_master_is_null->save();
                event(new PackingLogEvent($packing_form_master_is_null, 7007010, null, "", $form_id, $user_id));

                if ($empty_carrier && $packing_form_master_is_null->carrier) {
                    $packing_form_master_is_null->carrier->SetEmpty($user_id);
                }
            }
        }
    }

    public
    function machine()
    {
        if ($this->applicant_type_id == 10) {
            return $this->belongsTo(Machine::class, "applicant_id");
        } else {
            return $this->belongsTo(Machine::class, "applicant_id")->where("id", 0)->first();
        }
    }

// نام درخواست دهنده
    public
    function applicant()
    {
        switch ($this->applicant_type_id) {
            case 10:
                return $this->belongsTo(Machine::class, "applicant_id");
                break;
            case 20:
                return $this->belongsTo(Contractor::class, "applicant_id");
            case 30:
                return $this->belongsTo(Customer::class, "applicant_id");
            case 40:// انبارک ماشین
                return $this->belongsTo(Warehouse::class, "applicant_id");
            case 60: // تامین کننده (تحویل امانی - قرض)
            case 70: // تامین کننده ( برگشت از خرید)
                return $this->belongsTo(Supplier::class, "applicant_id");
            case 80: // درخواست متفرقه
                return $this->belongsTo(Worker::class, "applicant_id");
        }
    }

    public
    function applicant_type()
    {

        return $this->belongsTo(ApplicantType::class);

    }

    public
    function production()
    {
        return $this->belongsTo(Production::class);
    }

    public
    function getReferenceNumber()
    {
        switch ($this->applicant_type_id) {
            case 10: // ماشین
                return $this->allocation ? $this->allocation->code() : "***";
            case 20: // پیمانکار
                if ($this->allocation) {
                    $allocation_item = $this->allocation->items()->first();

                    return $allocation_item && $allocation_item->production ? $allocation_item->production->serial() : "****";
                }

                return "***";
            case 30:
                return $this->order->code();
            case 40:
                $script_log = ScriptLog::where("script_id", 7)->where("other_id", $this->id)->first();
                if ($script_log) {
                    return "1007/" . $script_log->id ?? "";
                } else {
                    return "";

                }
            case 60: // تامین کننده (تحویل امانی - قرض)
                return $this->allocation ? $this->allocation->code() : "***";
            case 70: // تامین کننده برگشت کالا
                $special_license = SpecialLicense::where(["special_license_type_id" => 9, "param5" => $this->id])->first();
                if ($special_license) {
                    return $special_license->code;
                }
                return "***";

            case 80: //درخواست متفرقه
                return "";
            default:
                1 / 0;
        }
    }

    public
    static function getLatestRequestForm($applicant_id, $applicant_type_id, $goods_kind_id = null)
    {
        return ProductRequestForm::
        join("product_request_form_item", "product_request_forms.id", "product_request_form_id")->
        join("products", "products.id", "product_id")->
        whereIn("product_request_forms.status_id", [7005002, 7005008])->
        when($goods_kind_id, function ($query) use ($goods_kind_id) {
            return $query->where("goods_kind_id", $goods_kind_id);
        })->
        where("applicant_id", $applicant_id)->
        where("applicant_type_id", $applicant_type_id)->
        orderByDesc("product_request_forms.id")->
        select("product_request_forms.*")->
        first();
    }

    public
    function getCreateFormUser()
    {
        $first = ProductRequestFormLog::where(["product_request_form_id" => $this->id])->orderBy("id")->first();
        if (isset($first)) {
            return $first->worker;
        }
    }

    public
    function get_log_with_status($status_id = false)
    {

        if (!$status_id) {
            return ProductRequestFormLog::where(["product_request_form_id" => $this->id])->orderBy("id", "desc")->paginate(10);
        }

        return ProductRequestFormLog::where([
            "status_id" => $status_id,
            "product_request_form_id" => $this->id
        ])->first();
    }


    public
    function getAmount($type)
    {
        $packing_form_ids = TransportPackingForm::where("product_request_form_id", $this->id)->pluck("packing_form_id")->toArray();

        return PackingFormItem::whereIn("packing_form_id", $packing_form_ids)->sum($type);
    }


    public
    function getIC()
    {

        switch ($this->applicant_type_id) {
            case 10:
                return $this->machine->machine_type->cost_center->code ?? "";
            case 20:
                return $this->applicant->getIC();
            case 30:
                return $this->applicant->getIC(); // کد مرکز نوسا
            case 40:
                $warehouse = Warehouse::find($this->applicant_id);
                $warehouse_type = $warehouse->warehouse_type_id ?? 0;
                switch ($warehouse_type) {
                    case 2: // انبارک ماشین
                        $machine = Machine::where("warehouse_id", $this->applicant_id)->first();

                        return $machine->machine_type->cost_center->code ?? "";; // کد مرکز نوسا
                        break;
                    case 3: // انبارک گروه ماشین
                        $machine_type = MachineType::where("id", $warehouse->belonging_to_id)->first();

                        return $machine_type->cost_center->code ?? "";; // کد مرکز نوسا
                        break;
                    case 4: // انبارک ایستگاه کاری

                        $station = Station::where("id", $warehouse->belonging_to_id)->first();
                        return $station->cost_center->code ?? "";; // کد مرکز نوسا
                        break;
                    case 5: // انبارک خط تولید
                        $line = Line::where("id", $warehouse->belonging_to_id)->first();

                        return $line->cost_center->code ?? "";; // کد مرکز نوسا
                        break;
                }
            case 60: // تامین کننده -تحویل امانی قرض
            case 70: // تامین کننده - برگشت از خرید
                return $this->applicant->getIC();
            case 80:
                $json_data = $this->json_data->data ?? "";
                $json_data = json_decode($json_data);
                if (isset($json_data->cost_center_id)) {
                    $cost_center = CostCenter::find($json_data->cost_center_id);
                    if ($cost_center) {
                        return $cost_center->code;
                    }
                    1 / 0;
                }
                1 / 0;

            default:
                1 / 0;
        }
    }

    public
    function getTranKind()
    {

        switch ($this->applicant_type_id) {
            case 10: // ماشین
                return 8; // تحویل به تولید
            case 20:
                return 9; // تحویل به پیمانکار
            case 30:
                return 6; // فروش
            case 40:
                return 8; // تحویل به تولید
            case 60: // تامین کننده (تحویل امانی - قرض)
                return 10; // تحویل امانی
            case 70: // تامین کننده - برگشت از خرید
                if ($this->applicant->supplier_type_id == 1) {
                    return 24; //  برگشت از خرید داخلی (دوره قبل)
                }
                if ($this->applicant->supplier_type_id == 2) {
                    return 25; //  برگشت از خرید خارجی (دوره قبل)
                }
                break;
            case 80: // خروج متفرقه
                $json_data = $this->json_data->data ?? "";
                $json_data = json_decode($json_data);
                if (isset($json_data->trans_kind_id)) {
                    return $json_data->trans_kind_id;
                }
                1 / 0;
            default:
                1 / 0;

        }


    }

    public
    function getFormItemDescription($packing_form_item, $product_request_form_item, $form)
    {

        $trans_kind = TransKind::find($this->getTranKind());
        switch ($this->applicant_type_id) {

            case 10: // ماشین
                // تحویل به تولید
                return $trans_kind->caption . "(" . $this->applicant->caption . ") " .
                    "با کد بسته بندی " . ($packing_form_item->packing_form ? $packing_form_item->packing_form->getCode() : "") .
                    " و سریال تولید " . ($packing_form_item->production_form_item->production->serial ?? "") . " و " .
                    " برگ خروج " . ($form->code ?? "");

            case 20:
                // تحویل به پیمانکار
                $production_card = "";
                foreach ($this->allocation->items as $item) {
                    $production_card .= $item->production->serial . ", ";
                }
                $production_card = trim($production_card, ", ");

                return $trans_kind->caption . "(" . $this->applicant->caption . ") " .
                    "با کد بسته بندی " . ($packing_form_item->packing_form ? $packing_form_item->packing_form->getCode() : "") .
                    " و دستور پیمان " . ($production_card) . " و " .
                    " برگ خروج " . ($form->code ?? "");

            case 30:
                // فروش
                $code = $product_request_form_item->product_request_form->order ?
                    $product_request_form_item->product_request_form->order->code() : "***";

                return $trans_kind->caption . "(" . $this->applicant->caption . ") " .
                    "با کد بسته بندی " . ($packing_form_item->packing_form ? $packing_form_item->packing_form->getCode() : "") .
                    " و شماره سفارش " . ($code) . " و " .
                    " برگ خروج " . ($form->code ?? "");

            case 40: // انبارک ماشین
                // مصرف
                return $trans_kind->caption . "(" . $this->applicant->caption . ") " .
                    "با کد بسته بندی " . ($packing_form_item->packing_form ? $packing_form_item->packing_form->getCode() : "") .
                    " و " . " برگ خروج " . ($form->code ?? "");


            case 60: // // تحویل به تامین کننده (تحویل امانی - قرض)
            case 70: // تامین کننده - برگشت از خرید
                return $trans_kind->caption . "(" . $this->applicant->caption . ") " .
                    "با کد بسته بندی " . ($packing_form_item->packing_form ? $packing_form_item->packing_form->getCode() : "") .
                    " و تخصیص پیمان " . ($this->allocation_id) . " و " .
                    " برگ خروج " . ($form->code ?? "");
            case 80:
                return $trans_kind->caption .
                    "با کد بسته بندی " . ($packing_form_item->packing_form ? $packing_form_item->packing_form->getCode() : "") .
                    " و " . " برگ خروج " . ($form->code ?? "");
            default:
                1 / 0;

        }


    }

    public
    function getFirstFormStatus()
    {
        // گرفتن اولین وضعیت فرم خروج
        return $this->nextStatusForExistForm(0);
    }

    public
    function lot_number_should_be_check_equals()
    {

        switch ($this->applicant_type_id) {
            case 10:
                $machine_input_output_bands = CurrentMachineInput::
                where([
                    "allocation_id" => $this->allocation_id,
                    "goods_kind_id" => 3
                ])->
                get();
                // کمتر از 2 تا باشد
                if (count($machine_input_output_bands) <= 1) {
                    return false;
                }

                if ($machine_input_output_bands[0]->band_code == $machine_input_output_bands[1]->band_code) {
                    return true;
                }
                foreach ($machine_input_output_bands as $item) {
                    if ($item->percent_of_use != 100) {
                        return true;
                    }
                }
                break;
        }

        return false;
    }


    public
    function nextStatusForExistForm($current_exit_form_status_id)
    {

        $status_id = -1;
        switch ($this->applicant_type_id) {
            case 10:

                if ($current_exit_form_status_id == 0) {
                    return 500000500;//// در انتظار تایید درخواست کننده(ماشین)
                } elseif ($current_exit_form_status_id == 500000500) { //// در انتظار تایید درخواست کننده(ماشین)
                    return 500000200; //تایید شده
                }

                return 500000200; //تایید شده
                break;
            case 60:// تامین کننده - قرض
            case 70: // تامین کننده - برگشت از خرید
            case 20:// پیمانکار
            case 30:// مشتری
                return $this->applicant->nextStatusForExistForm($current_exit_form_status_id);
            case 40:// انبارک
            case 80: // خروج متفرقه
                if ($current_exit_form_status_id == 0) {
                    return 500000535;////در انتظار تایید کنترل کیفیت
                } elseif ($current_exit_form_status_id == 500000535) { ////در انتظار تایید کنترل کیفیت
                    return 500000200; //تایید شده
                }

                return 500000200; //تایید شده
                break;

            default:
                1 / 0;
        }
    }

    public
    function updateExistFormStatusForm(Form $form, $event_id = false, $message = "")
    {

        // بروز رسانی وضعیت فرم
        $new_status_id = $this->nextStatusForExistForm($form->status_id);
        $old_status_id = $form->status_id;

        $form->status_id = $new_status_id;
        $form->save();

        event(new FormLogEvent($form, $message));

        $this->sendSms($form, $new_status_id);

        $this->sendLeadingSms($form, $old_status_id);

        // ممکن است یک فرم برای چند درخواست باشد، بنابراین باید وضعیت همه آنها بروز شود
        $product_request_form_forms = ProductRequestFormForm::where("form_id", $form->id)->get();

        foreach ($product_request_form_forms as $product_request_form_form) {
            $product_request_form = $product_request_form_form->product_request_form;
            $product_request_form->updateApplicantStatus($event_id, ["form" => $form]);
        }

        //اگر فرم تایید شده بود، وضعیت بسته بندی ها به وضعیت تحویل شده به درخواست کننده تبدیل می شود
        if ($form->status_id == 500000200) {

            $packing_form_list = PackingForm::join("packing_form_item", "packing_form_item.packing_form_id", "packing_forms.id")->
            join("form_item", "form_item.packing_form_item_id", "packing_form_item.id")->
            where("form_item.form_id", $form->id)->
            select("packing_forms.*")->get();


            // ثبت لاگ برای بسته بندی هایی که حداقل یک بسته بندی فرعی دارند.
            $master_packing_form_ids = PackingForm::MasterPackingFormIds($packing_form_list);
            $master_packing_form_list = PackingForm::whereIn("id", $master_packing_form_ids)->get();

            foreach ($master_packing_form_list as $packing_form) {
                PackingForm::ConfirmToApplicant($packing_form, $form, $this->applicant_type_id);
            }

            // ثبت لاگ برای بسته بدی هایی بسته بندی فرعی ندارند و Master آنها نال است.
            $master_is_null_packing_forms = PackingForm::MasterIsNullPackingForms($packing_form_list);

            foreach ($master_is_null_packing_forms as $packing_form) {
                if (!isset($master_packing_form_ids[$packing_form->id])) {
                    PackingForm::ConfirmToApplicant($packing_form, $form, $this->applicant_type_id);
                }
            }

        }
    }

    public
    function updateApplicantStatus($event_id = false, $data = null, $user_id = null)
    {


        $product_request_form = ProductRequestForm::find($this->id);

        //تغییر وضعیت فرم های درخواست
        $status_id = 7005002; // تحویل (ارسال) شده

        $form_list = $product_request_form->getExistFormForConfirmList();
        $sum_amount_sent = 0;
        $sum_amount_request = 0;
        if (count($form_list) > 0) { // بعد از اینجا وضعیت فرم تعییر می کند
            // اگر فرم دیگری برای تایید وجود دارد؟
            $status_id = 7005004;
        } else {

            foreach ($product_request_form->items as $prf_item) {

                $product = $prf_item->product;

                $min = $prf_item->amount_request * $product->goods_kind->be_lower_in_confirm_exit_form / 100;
                if ($prf_item->amount_remaining > $min) {
                    $status_id = 7005008; // در انتظار تحویل (ارسال) بافی مانده کالا
                }

                $sum_amount_sent += $prf_item->amount_sent;
                $sum_amount_request += $prf_item->amount_request;

            }

            // اگر مقدار همه ردیف ها صفر شده است، یعنی درخواست کنسل شده است.
            if ($sum_amount_request == 0) {
                $status_id = 7005006; // کنسل شده
            }

            // فعلا حذف شد
//            if ($status_id == 7005008 && $product_request_form->product_request_form_type_id == 2 && $product_request_form->applicant_type_id == 30) {
//                // درخواست های خروج دستی صادر شده توسط واحد فروش
//                $type_of_status_id_after_confirm_product_request_form_in_permission = Setting::getIntegerValue("type_of_status_id_after_confirm_product_request_form_in_permission");
//                switch ($type_of_status_id_after_confirm_product_request_form_in_permission) {
//                    case 1: // همه بشود تحویل شده، حتی اگر کامل تحویل نشده است.
//                        break;
//                }
//            }
        }

        // اگر هیچ مقداری ارسال نشده است، باید وضعیت آن در انتظار ارسال بشود.
        if ($status_id == 7005008 && $sum_amount_sent == 0) {
            $status_id = 7005001;
        }

        $product_request_form->status_id = $status_id;
        $product_request_form->save();

        switch ($this->applicant_type_id) {
            case 10: // درخواست چله از طرف ماشین
// ویرایش وضعیت ماشین اگر در انتظار چله است

                $machine = Machine::find($this->applicant_id);
                foreach ($this->items as $item) {
                    event(new WarpsAvailableEvent($item->product, $user_id));
                }
//                event( new ProductRequestFormLogEvent( $this, "", ( isset( $data["form"] ) ? $data["form"] : null ), 7005013 ) );
                break;

            case 20:
                // تغییر وضعیت پیمانکار
                $contractor = Contractor::find($this->applicant_id);
                foreach ($this->allocation->items as $contractor_allocation) {


                    $contractor_allocation->status_id = 5310108; // در انتظار تایید دریافت مواد اولیه

                    if (count($form_list) <= 0) { // بعد از اینجا وضعیت تغییر می کند

                        // اگر هیچ فرمی در انتظار تایید وجود نداشت و حداقل یک فرم تایید شده بود: وضیعت می شود در حال تولید، در غیر این صورت هیچ بشود همان در انتظار تایید مواد اولیه
                        $list_confirmed_count = ProductRequestFormForm::join("forms", "form_id", "forms.id")->
                        where("product_request_form_id", $product_request_form->id)->
                        where("forms.status_id", 500000200)-> // تایید شده
                        count();
                        if ($list_confirmed_count > 0) {
                            $contractor_allocation->status_id = 5310104; // در حال تولید توسط پیمانکار
                        }
                    }

                    $contractor_allocation->save();

                    switch ($event_id) {
                        case 5310108: // تحویل مواد اولیه

                            event(new ContractorLogEvent($contractor, 5310108, $contractor_allocation->production, $contractor_allocation, null, $user_id));

                            break;
                        case 5310104: // تایید دریافت مواد اولیه

                            event(new ContractorLogEvent($contractor, 5310104, $contractor_allocation->production, $contractor_allocation, null, $user_id));
                            break;
                        case 7005002: // تایید دریافت کالا (درخواست کننده)
                            event(new ProductRequestFormLogEvent($product_request_form, "", $data["form"]->id, 7005002, $user_id));

                            break;
                        default:
                            break;
                    }
                }
                break;

            case 30:
                // تغییر وضعیت سفارش
                $order = $product_request_form->order;
                $list = $order->getExistFormList([35090]);

                // 1 چون بعد از تایید بروز رسانی وضعیت فرم تغییر می کند.
                if (count($list) > 0) { // هیچ برگ خروجی در انتظار تایید ندارد.
                    $order->status_id = 35090; // در انتظار تایید برگ خروج
                } else {
                    // ابتدا باید چک کنیم که به اندازه کل سفارش، درخواست خروج از انبار صادر شده باشد
                    $order->status_id = 35050; // ارسال شده

                    $order_list_items = OrderList::
                    join("orders", "orders.id", "order_list.order_id")->
                    leftJoin("product_request_form_item", 'product_request_form_item.order_list_id', 'order_list.id')->
                    where("orders.customer_id", $order->customer_id)->
                    where("orders.id", $order->id)->
                    whereNull("from_order_id")-> // ردیف سفارش اصلی باشد.
                    selectRaw(
                        "order_list.product_id,
                        sum(product_request_form_item.amount_remaining) as amount_remaining,sum(product_request_form_item.amount_sent) as amount_sent,sum(product_request_form_item.amount_request) as amount_request, order_list.amount as order_amount
                        ")->
                    groupBy("order_list.id")->
                    with("product.goods_kind")->
                    get();
                    $sum_order_amount_sent = 0;
                    foreach ($order_list_items as $order_list_item) {

                        $product = $order_list_item->product;
                        // حداقل مقداری که باید ارسال شود تا ردیف سفارش بشود ارسال شده
                        $min = $order_list_item->order_amount * (1 - $product->goods_kind->be_lower_in_confirm_exit_form / 100);
                        if ($order_list_item->amount_sent + 0 < $min) {
                            $order->status_id = 35040; // ارسال ناقص
                        }
                        $sum_order_amount_sent += $order_list_item->amount_sent;

                    }
                    // اگر همه درخواست های آن کنسل شده باشد.
                    if ($sum_order_amount_sent == 0) {
                        $order->status_id = 35030; // در انتظار آماده سازی
                    }

                }

                $order->save();

                switch ($event_id) {

                    case 5310108: // تحویل مواد اولیه (ارسال کالا)

                        event(new OrderLogEvent($order, 35091, "", "", null, $user_id));

                        // ارسال پیامک با توجه به اولین وضعیت فرم خروج از انبار
                        $this->sendSms($data["form"], $data["form"]->status_id);

                        break;

                    case 5310104:

                        event(new OrderLogEvent($order, 35096, "", "", null, $user_id)); //// ثبت تراکنش انبار

                        break;

                    case 7005011: // عدم تایید برگ خروج

                        event(new OrderLogEvent($order, 35095, "", "", null, $user_id));
                        break;
                    case 7005002: // تایید دریافت کالا (درخواست کننده)
                        event(new ProductRequestFormLogEvent($product_request_form, "", $data["form"]->id, 7005002, $user_id));
                        event(new OrderLogEvent($product_request_form->order, 35092, "", "", $data["form"]->id, $user_id));

                        break;
                    default:
                        break;
                }

                break;


            case 40: // انبارک

                break;


            case 60:
                // تغییر وضعیت تامین کننده
                //$supplier = Contractor::find($this->applicant_id);
                // لاگی برای تامین کنندگان در نظر نگرفتیم.

                break;
            case 70: // تامین کننده - برگشت از خرید
                break;
            case 80: //خروج متفرقه
                break;

            default:
                1 / 0;

        }
    }

    public
    function sendSms($form, $new_status_id)
    {
        switch ($this->applicant_type_id) {
            case 10:// ماشین
                break;
            case 20:// پیمانکار
                break;
            case 30:// مشتری
                return $this->applicant->sendSmsForExistForm($new_status_id, $form, $this->order);
                break;
            case 40:// انبارک
                break;
            case 60:// تامین کنندگان
                break;
            case 70: // تامین کننده - برگشت از خرید
                break;
            case 70: // خروج متفرقه
                break;
            case 80: // خروج متفرقه
                break;
            default:
                1 / 0;
        }
    }

    public
    function sendLeadingSms($form, $status_id)
    {
        // ارسال پیامک بارگیری به انبار
        switch ($this->applicant_type_id) {

            case 10:// ماشین
                $exit_form_status_id = Setting::getStringValue("send_loading_sms_for_machine_in_exit_form_status_id");
                $post_id = Setting::getStringValue("loading_post_id_sms_for_machine_exit_form_status_id");

                break;

            case 20:// پیمانکار
                $exit_form_status_id = Setting::getStringValue("send_loading_sms_for_contractor_in_exit_form_status_id");
                $post_id = Setting::getStringValue("loading_post_id_sms_for_contractor_exit_form_status_id");

                break;

            case 30:// مشتری
                $exit_form_status_id = Setting::getStringValue("send_loading_sms_for_customer_in_exit_form_status_id");
                $post_id = Setting::getStringValue("loading_post_id_sms_for_customer_exit_form_status_id");

                break;

            case 40:
                //پیامک ارسال نمی شود.
                $exit_form_status_id = -1;
                break;


            case 60:// تامین کنندگان - قرض
            case 70: // تامین کننده - برگشت از خرید
                $exit_form_status_id = Setting::getStringValue("send_loading_sms_for_supplier_in_exit_form_status_id");
                $post_id = Setting::getStringValue("loading_post_id_sms_for_supplier_exit_form_status_id");

                break;

            case 80:
                //پیامک ارسال نمی شود.
                $exit_form_status_id = -1;
                break;

            default:
                1 / 0;
        }


        if ($exit_form_status_id == $status_id) {

            $post = Post::find($post_id);
            if ($post) {

                $workers = PostUser::getCurrentUserByShiftWorkAndLeaveOvertimeByPostId("worker", $post_id);
                $software_name = Setting::getStringValue("software_name");
                $template = "exitformalarmfortransport";

                $token = $form->getCode();
                $token2 = $this->getCode();
                $token3 = $form->id . "/" . $form->getRandom();
                $token10 = $post->caption;
                $token20 = $software_name;

                foreach ($workers as $worker) {
                    Notification::send(
                        "00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile,
                        new SMSNotification($template, $token, $token2, $token3, $token10, $token20)
                    );
                }
            }
        }


    }


    public
    function getValidStatusForSendLoadingSms()
    { // حذف است.
        $exit_status_id = 0;
        1 / 0;
        // با توجه به تنظیمات هر درخواست کننده وضعیت های مجاز ارسال پیامک را استخراج می کنیم.
//        switch ($this->applicant_type_id) {
//
//            case 10:
//                $exit_status_id = Setting::getStringValue("machine_exit_form_status_id");
//            case 20:// پیمانکار
//                $exit_status_id = Setting::getStringValue("contractor_exit_form_status_id");
//            case 30:// مشتری
//                $exit_status_id = Setting::getStringValue("customer_exit_form_status_id");
//            case 40:// انبارک
//                $exit_status_id = Setting::getStringValue("warehouse_exit_form_status_id");
//            case 60:// تامین کنندگان - قرض
//            case 70:// تامین کنندگان - برگشت از خرید
//                $exit_status_id = Setting::getStringValue("supplier_exit_form_status_id");
//
//                break;
//            default:
//                1 / 0;
//        }
//
//        switch ($exit_status_id) {
//            case 500000515: // پیش نویس
//                return [500000515, 500000520, 500000525, 500000530, 500000500];
//                break;
//            case 500000520: // نهایی
//                return [500000520, 500000525, 500000530, 500000500];
//                break;
//            case 500000525: // حمل و نقل
//                return [500000530, 500000525, 500000500];
//                break;
//            case 500000530: // نگهبانی
//                return [500000530, 500000500];
//                break;
//            case 500000500: // درخواست کننده
//                return [500000500];
//                break;
//        }

        1 / 0;
    }

    public
    function getValidStatusForConformForm()
    {
        $exit_status_id = 0;

        // با توجه به تنظیمات هر درخواست کننده وضعیت های مجاز ثبت تراکنش را استخراج می کنیم.
        switch ($this->applicant_type_id) {

            case 10:
                $exit_status_id = Setting::getStringValue("machine_exit_form_status_id");
                break;
            case 20:// پیمانکار
                $exit_status_id = Setting::getStringValue("contractor_exit_form_status_id");
                break;
            case 30:// مشتری
                $exit_status_id = Setting::getStringValue("customer_exit_form_status_id");
                break;
            case 40:// انبارک
                $exit_status_id = Setting::getStringValue("warehouse_exit_form_status_id");
                break;
            case 60:// تامین کننده - قرض
            case 70: // تامین کننده - برگشت از خرید
                $exit_status_id = Setting::getStringValue("supplier_exit_form_status_id");
                break;
            case 80:// خروج متفرقه
                $exit_status_id = Setting::getStringValue("worker_exit_form_status_id");
                break;
            default:
                1 / 0;
        }

        switch ($exit_status_id) {
            case 500000514: // وصول مطالبات
                return [500000514, 500000515, 500000520, 500000525, 500000530, 500000500];
                break;
            case 500000515: // پیش نویس
                return [500000515, 500000520, 500000525, 500000530, 500000500];
                break;
            case 500000520: // نهایی
                return [500000520, 500000525, 500000530, 500000500];
                break;
            case 500000525: // حمل و نقل
                return [500000525, 500000530, 500000500];
                break;
            case 500000530: // نگهبانی
                return [500000530, 500000500];
                break;
            case 500000500: // درخواست کننده
                return [500000500];
                break;
            case 500000535: // کنترل کیفیت
                return [500000500, 500000535];
                break;
        }

        1 / 0;
    }


    public
    function getCurrentExistFromForDashboard()
    {
        return self::Get_CurrentExistFromForDashboard($this->applicant_type_id);
    }

    public
    static function Get_CurrentExistFromForDashboard($applicant_type_id)
    {
        $exit_status_id = 0;

        // با توجه به تنظیمات هر درخواست کننده وضعیت های برگ خروج از انبار که هنوز تراکنش آنها ثبت نشده است.
        switch ($applicant_type_id) {

            case 10:
                $exit_status_id = Setting::getStringValue("machine_exit_form_status_id");
                break;
            case 20:// پیمانکار
                $exit_status_id = Setting::getStringValue("contractor_exit_form_status_id");
                break;
            case 30:// مشتری
                $exit_status_id = Setting::getStringValue("customer_exit_form_status_id");
                break;
            case 40:// انبارک
                $exit_status_id = Setting::getStringValue("warehouse_exit_form_status_id");
                break;
            case 60:// تامین کننده - قرض
            case 70: // تامین کننده - برگشت از خرید
                $exit_status_id = Setting::getStringValue("supplier_exit_form_status_id");
                break;
            case 80:// خروج متفرقه
                $exit_status_id = Setting::getStringValue("worker_exit_form_status_id");
                break;
            default:
                1 / 0;
        }

        switch ($exit_status_id) {
            case 500000514: // وصول مطالبات
                return [500000514];
                break;
            case 500000515: // پیش نویس
                return [500000514, 500000515];
                break;
            case 500000520: // نهایی
                return [500000514, 500000515, 500000520];
                break;
            case 500000525: // حمل و نقل
                return [500000514, 500000515, 500000520, 500000525];
                break;
            case 500000530: // نگهبانی
                return [500000514, 500000515, 500000520, 500000525, 500000530];
                break;
            case 500000500: // درخواست کننده
                return [500000514, 500000515, 500000520, 500000525, 500000530, 500000500];
                break;
            case 500000535: // کنترل کیفیت
                return [500000535];
                break;
        }

        1 / 0;
    }

    public
    function allow_confirmation_according_applicant()
    {
        switch ($this->applicant_type_id) {
            case 10: // ماشین
                //فعلا پیاده سازی نشده
                return false;
                break;
            case 20:// پیمانکار
                $contractor_allocation = ContractorAllocation::where("allocation_id", $this->allocation_id)->first();
                $result = ContractorPanel\DashboardController::checkPermissionConditions($contractor_allocation, ContractorPanel\ConfirmationOfReceiptOfProductController::$info);
                if ($result["result"]) {
                    return true;
                }

                return false;

                break;
            case 30: // مشتری
                $user_id = \Auth::user()->id;
                $customer = Customer::findWidthUserId($user_id);
                if (!$customer || $customer->id != $this->order->customer_id) {
                    return false;
                }

                return true;
                break;
            case 40: // انبارک
                // چون ربات این کار را انجام می دهد، نیاز به تایید ندارد.
                return true;
                break;
            case 60:// تامین کنندگان
            case 70: // تامین کننده - برگشت از خرید
                1 / 0;
                break;
        }
    }

    public
    function test()
    {
        //تغییر وضعیت فرم های درخواست
        $status_id = 7005002; // تحویل (ارسال) شده

        $form_list = $product_request_form->getExistFormForConfirmList();
        if (count($form_list) > 1) { // بعد از اینجا وضعیت فرم تعییر می کند
            // اگر فرم دیگری برای تایید وجود دارد؟
            $status_id = 7005004;
        } else {
            foreach ($product_request_form->items as $prf_item) {

                $product = $prf_item->product;

                $min = $prf_item->amount_request * $product->goods_kind->be_lower_in_confirm_exit_form / 100;
                if ($prf_item->amount_remaining > $min) {
                    $status_id = 7005008; // در انتظار تحویل (ارسال) بافی مانده کالا
                }

            }
        }
        $product_request_form->status_id = $status_id;
        $product_request_form->save();
    }

    /**
     * @param Product $product
     * @return مقدار مجوز های صادر شده برای یک کالا
     */
    public static function ProductRequestFormAmount(Product $product, $type = "sum")
    {
        $order_status_list = ProductRequestPermissionController::$order_status_list;
        $product_request_form_remaining_query = Product\ProductRequest\ProductRequestForm::join("product_request_form_item", "product_request_forms.id", "product_request_form_id")->
        where("product_id", $product->id)->
        where("product_request_forms.status_id", ProductRequestPermissionController::$product_request_forms_status_list);
        if ($type == "sum") {
            return $product_request_form_remaining = $product_request_form_remaining_query->sum("amount_remaining");
        } else {
            return $product_request_form_remaining_query->get();
        }

    }

    public function get_product_request_permissions()
    {
        $item = Product\ProductRequestPermission\ProductRequestPermissionItem::where("product_request_form_id", $this->id)->first();
        return $item->product_request_permission ?? null;
    }

    /**
     * @param ProductRequestForm $product_request_form
     * @return array|true[]
     * با توجه به تنظیمات انبار امکان ارسال کالا تا ** روز قبل از تاریخ تحویل امکان پذیر است یا خیر؟
     */
    public static function AllowCheckOutRequest(ProductRequestForm $product_request_form)
    {
        $ealier_day = $product_request_form->warehouse->earlier_delivery_time;
        if (!$ealier_day) {
            return [
                "result" => true,
            ];
        }

        $earlier_delivery = Carbon::parse($product_request_form->coordinate_date_time)->addDay(-$ealier_day);

        if ($earlier_delivery->greaterThan(Carbon::now())) {
            return [
                "result" => false,
                "error" => "امکان تحویل کالا تا $ealier_day روز قبل از تاریخ تحویل امکان پذیر نمی باشد."
            ];
        }

        return [
            "result" => true,
        ];
    }

    /***
     * @param Warehouse $warehouse
     * @return true[]|void
     * تابع اوتامات که وقتی آن را صدا می زنیم تمامی درخواست هایی که تاریخ تحویل آنها گذشته است، را کنسل می کند.
     */

    public static function RemoveMaxDeliveryTime(Warehouse $warehouse)
    {
        $max_delivery_time = $warehouse->max_delivery_time;
        if (!$max_delivery_time) {
            return [
                "result" => true,
            ];
        }
        $max_delivery_date = Carbon::now()->addDay(-$max_delivery_time);
        $list = ProductRequestForm::
        where("warehouse_id", $warehouse->id)->
        where("applicant_type_id", 30)->
        where("coordinate_date_time", "<", $max_delivery_date)->
        whereIn("status_id", [7005001, 7005003, 7005008])-> // تحویل درخواست، باقی مانده درخواست، تکمیل موجودی
        get();

        foreach ($list as $product_request_form) {

            $update = false;
            foreach ($product_request_form->items as $product_request_item) {
                $result = ProductRequestPermissionController::RemoveProductRequestFormItem($product_request_form, $product_request_item->product, 2);
                if ($result["result"]) {
                    $update = true;
                }
            }

            if ($update) {
                $product_request_form->updateApplicantStatus();
            }
            $product_request_form->status_id = 7005009; // خاتمه یافته شده
            $product_request_form->save();
            //خاتمه یافته (حداکثر زمان ارسال)
            event(new ProductRequestFormLogEvent($product_request_form, "", null, 7005027, 2));

            return $product_request_item;
        }
    }
}

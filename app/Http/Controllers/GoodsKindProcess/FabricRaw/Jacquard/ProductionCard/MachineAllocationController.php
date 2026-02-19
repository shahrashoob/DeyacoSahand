<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\ProductionCard;

use App\Events\Machine\MachineAllocationEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\ProductionCard\GeneralMachineAllocationController;
use App\Http\Controllers\GoodsKindProcess\General\ProductionCard\GeneralProductionChannelController;
use App\Http\Controllers\Utility\Script\Script1021Controller;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Allocation\AllocationBrand;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\CurrentMachineInputMaterialDegree;
use App\Models\LineProduct\Machine\CurrentMachineMaterialFlow;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachinePropertyValue;
use App\Models\LineProduct\Machine\MachineStatus;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeInputBand;
use App\Models\LineProduct\Machine\MachineTypeOutputBand;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\MaterialFlow;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\Post\Post;
use App\Models\Post\PostUser;
use App\Models\Production\Production;
use App\Models\Utility\Setting;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\View;

class MachineAllocationController extends Controller
{
    //
    public static $info = [
        "route" => "fabric_raw.jacquard.machine_allocation.",
        "enable_status" => ["001", "002", "003"],
        "next_status" => [],
        "button" => ["caption" => "تخصیص ماشین (ژاکارد)", "class" => "btn-success"],
        "view_path" => "goods_kind_process.fabric_raw.jacquard.production_card.machine_allocation."
    ];
    public $shoulder_width_id = 220228;
    public static $shoulderWidthId = 220228;
    public $dashboard_route = "fabric_raw.machine_allocation.";
    public $controller_info;
    public $view_path;
    public $route_path;

    public function __construct()
    {
        $perfix_status_code = DashboardController::$perfix_status_code;
        $this->view_path = View::share("perfix_status_code", $perfix_status_code);
        $this->controller_info = MachineAllocationController::$info;
        $this->route_path = MachineAllocationController::$info["route"];
    }

    public function select_band(Request $request, $machine_id, MachineType $machine_type, Production $production, $is_first_production = false)
    {

        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }
        if ($production->product->sub_unit2_id == 1400) {
            if (!$production->product->frame_ratio_unit2) {
                return back()->withErrors("لطفا نسبت قاب به واحد اصلی را برای " . $production->product->caption . " ثبت نمایید.");

            }
        }
        $key = "machine_type_" . $machine_type->id;
        $machine = Machine::find($request->$key ?? $machine_id);

        session(["permutation_list_" . $production->id => null]); // انتخاب حالت ها را حذف می کنیم.

        $result_select_band_auto = self::SelectBandAuto($machine, $production, $is_first_production);

        if (!$result_select_band_auto["result"]) {
            return redirect()->
            route($this->dashboard_route . "index", $production)->
            withErrors($result_select_band_auto["error"]);

        } else {

            $reserve_after_allocation_option = $result_select_band_auto["reserve_after_allocation_option"];
            $warps_amount_list = $result_select_band_auto["warps_amount_list"];
            $warps_count = $result_select_band_auto["warps_count"];
            $allocation_amount_list = $result_select_band_auto["allocation_amount_list"];
            $open_band_list = $result_select_band_auto["open_band_list"];
            $band_count_allocation = $result_select_band_auto["band_count_allocation"];

            return view($this->controller_info["view_path"] . "select_band", compact("reserve_after_allocation_option", "warps_amount_list", "warps_count", "allocation_amount_list", "machine", "production", "open_band_list", "band_count_allocation"));

        }
    }

    public
    static function SelectBandAuto(Machine $machine, Production $production, $is_first_production, $allocation_amount_requested = null)
    {

        $machine_type = $machine->machine_type;

        $allocation_amount_list = [];

        if (!isset($machine)) {
            return [
                "result" => false,
                "error" => "لطفا یک ماشین جهت تخصیص انتخاب نمایید."
            ];
        }

        $in_delivering_result = MachineLog::InDelivering($machine);
        if (!$in_delivering_result["result"]) {
            // در حال تحویل شیفت
            return $in_delivering_result;
        }

        // حذف تخصیص های در انتظار تایید
        if ($is_first_production) {
            $allocation_delete_ids = MachineAllocation::where([
                "machine_id" => $machine->id,
                "status_id" => 5310005
            ])->pluck("allocation_id")->toArray();
            Allocation\AllocationData::whereIn("allocation_id", $allocation_delete_ids)->delete();
            MachineAllocation::where(["machine_id" => $machine->id, "status_id" => 5310005])->delete();

        }

        // چک کردن اینکه گروه ماشین در خط محصول وجود داشته باشد.
        if (!$production->product->line_product_station()->where("machine_type_id", $machine_type->id)->exists()) {
            return [
                "result" => false,
                "error" => "ماشین " . $machine->caption . " در مسیر - محصول های تعریف شده برای محصول (" . $production->product->caption . ") وجود ندارد."
            ];

        }

        // چک کردن وضعیت ماشین
        $machine_check = Machine::
        join("machine_status", "machines.production_status_id", "machine_status.production_status_id")->
        where([
            "machine_type_id" => $machine_type->id,
            "possibility_of_allocation_machine" => 1,
            "machines.id" => $machine->id,
            "machines.active_status_id" => 1200
        ])->first();

        if (!isset($machine_check)) {
            return [
                "result" => false,
                "error" => "وضعیت ماشین برای تخصیص نامعتبر است."
            ];
        }

        // لیست نقص های کالای یک سطح بالاتر را به دست می آوریم، ماشین نباید نقص فعالی از آن مجموعه داشته باشد اگر داشت
        // اجازه تخصیص نمی دهد.
        $result_fault = Allocation::CheckAllocationFault($machine, $production);
        if (!$result_fault["result"]) {
            return [
                "result" => false,
                "error" => $result_fault["error"]
            ];
        }

        // عرض پارچه
        $product_shoulder_width = $production->product->getPropertyValue(self::$shoulderWidthId);
        if (!isset($product_shoulder_width)) {
            return [
                "result" => false,
                "error" => "مشخصه عرض شانه برای محصول ثبت نشده است"
            ];
        }
        $product_shoulder_width = $product_shoulder_width->value;

        //         عرض شانه ماشین
        $machine_shoulder_width = MachinePropertyValue::
        where(["machine_type_id" => $machine->machine_type->id, "machine_property_id" => 1])-> //?
        first();
        if (!isset($machine_shoulder_width)) {
            return [
                "result" => false,
                "error" => "عرض ماشین برای نوع ماشین ثبت نشده است"
            ];
        }
        $machine_shoulder_width = $machine_shoulder_width->value;

// عرض  باقی مانده ماشین
        $machine_shoulder_width_allocation = 0;
        $product_allocation_list = MachineAllocation::where([
            "machine_id" => $machine->id,
            "status_id" => 5310005
        ])->
        get();

        foreach ($product_allocation_list as $allocation_item) {
            $machine_shoulder_width_allocation += $allocation_item->product->getPropertyValue(self::$shoulderWidthId)->value;

        }
        $machine_shoulder_width_allocation = $machine_shoulder_width - $machine_shoulder_width_allocation;


        // حداکثر چند باند می تواند تخصیص دهد.
        // تعداد باند خروجی پارچه خام را مشخص می کنیم
        $band_number = MachineTypeOutputBand::get_number_band(4); //
        if ($band_number <= 0) {
            return [
                "result" => false,
                "error" => "باند خروجی گروه ماشین به درستی تعریف نشده است."
            ];

        }
        $band_count_allocation = floor($machine_shoulder_width_allocation / $product_shoulder_width);
        if ($band_count_allocation > $band_number) {
            return [
                "result" => false,
                "error" => "تعداد باند های خروجی ماشین حداکثر $band_number می باشد و با توجه به عرض پارچه می بایست   تا به صورت  $band_count_allocation باند بافته شود که امکان پذیر نمی باشد، لطفا مشخصات کالا را بررسی بفرمایید و یا پارچه دیگر برای تخصیص انتخاب کرده و این کارت را به عنوان کارت دوم انتخاب کرده و تخصیص دهید. "
            ];
        }
        $band_count_allocation = min($band_count_allocation, $band_number);

        // محاسبه برای باند فعلی
        /**
         * محاسبه حداکثر مقدار قابل تخصیص برای کارت تولید
         * در هر باند خروجی
         */
        $sum_allocation_amount = MachineAllocation::where("production_id", $production->id)->
        whereIn("status_id", ["5310010", "5310020", "5310040"])->sum("allocation_amount");

        if (!$allocation_amount_requested) {
            $allocation_amount_requested = $production->number - $sum_allocation_amount;
        }

        if ($production->number - $sum_allocation_amount <= 0) {
            return [
                "result" => false,
                "error" => "با توجه به مقدار کارت تولید و تخصیصی ها انجام شده، امکان تخصیص جدید برای کارت تولید   وجود ندارد."
            ];
        }
        if ($allocation_amount_requested > $production->number - $sum_allocation_amount) {
            return [
                "result" => false,
                "error" => "با توجه به مقدار کارت تولید و تخصیصی ها انجام شده، امکان تخصیص " .
                    $allocation_amount_requested . " " .
                    $production->product->unit->caption .
                    "  از کارت امکان پذیر نمی باشد. "

            ];
        }

        if ($band_count_allocation < 1) {

            // هیچ باند جدیدی نمی تواند تخصیص دهد، تایید نهایی تخصیص
            return [
                "result" => false,
                "error" => "هیچ باندی نمی توانید به کارت تخصیص دهد.",
            ];

        } else {
            // حداقل می تواند یک باند به کارت تخصیص دهد.
            $warps_amount_list = null;
            // شماره باند تخصیص آزاد
            $reserve_band_list = $product_allocation_list->pluck("band_code")->toArray();
            $open_band_list = [];
            for ($i = 1; $i <= $band_number; $i++) {
                if (!in_array($i, $reserve_band_list) && count($open_band_list) < $band_count_allocation) {
                    $open_band_list[] = $i;

                    //مقدار تخصیص را به تعداد باندهای مشابه تقسیم می کنیم
                    $allocation_amount_list[$i] = round(($allocation_amount_requested) / $band_count_allocation, 2);
                }
                $warps_amount_list[$i]["result"] = null;
                $warps_amount_list[$i]["message"] = "null";
                $warps_amount_list[$i]["sum_amount_reserve"] = 0;
                $warps_amount_list[$i]["amount_begin_of_weaving"] = 0;
            }


            $warps_request = ProductRequestForm:: getLatestRequestForm($machine->warehouse_id, 40, 3);
            $warps_count = 0;
//            $end_machine_log                  = new MachineLog();
//            $end_machine_log->contour_1_value = session( "contour_1_value" );
//            $end_machine_log->contour_2_value = session( "contour_2_value" );
//            $end_machine_log->contour_3_value = session( "contour_3_value" );
//            $end_machine_log->contour_4_value = session( "contour_4_value" );
//            $end_machine_log->contour_5_value = session( "contour_5_value" );

            if ($warps_request) {
                $i = 1;
                // بررسی مقدار باقی مانده چله با توجه به قطب ها
                foreach ($warps_request->items as $item) {

                    // چله کارت درحال رزرو را به دست می آوریم و مقدار باقی مانده را با آن مقایسه می کنیم.
                    /*********************************/
//                    $current_warps_bom = BOMItem::join( "products", "material_id", "products.id" )->
//                    where( "product_id", $production->product_id )->
//                    where( "material_id", $item->product_id )->
//                    orderBy( "material_id" )->
//                    first();
                    /********************/

                    $result = Warps::getRemainingAmountOfWarps($warps_request, $item, null, $item->product_id ?? null);
                    $warps_amount_list[$i] = $result;
                    $i++;
                }
                $warps_count = $warps_request->items->count();

            }

            // به دست آوردن لیست کارت هایی که می توان بعد از آنها نمونه گیری کرد.
            $reserve_after_allocation_option = Production::getReserveAfterAllocationOption($production, $machine);

            if ($reserve_after_allocation_option["result"] == false) {
                return [
                    "result" => false,
                    "error" => $reserve_after_allocation_option["message"]
                ];

            }
            $reserve_after_allocation_option = $reserve_after_allocation_option["list"];

            return [
                "result" => true,
                "warning" => "باید تخصیص انتخاب کنید که بعد از آن نمونه گیری شود.",
                "reserve_after_allocation_option" => $reserve_after_allocation_option,
                "warps_amount_list" => $warps_amount_list,
                "warps_count" => $warps_count,
                "allocation_amount_list" => $allocation_amount_list,
                "machine" => $machine,
                "production" => $production,
                "open_band_list" => $open_band_list,
                "band_count_allocation" => $band_count_allocation
            ];


        }
    }

    public
    function select_other_production(Machine $machine, Production $production)
    {

        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }

        $production_allow_list = $this->get_other_production_for_allocation($machine, $production);
        // هیچ کارت تولیدی برای بافت در باندهای دیگر یافت نشد، تایید تخصیص و خاتمه
        if (count($production_allow_list) == 0) {

            return redirect()->route($this->controller_info["route"] . "select_input_line", [$machine, $production, 0])->withErrors("با توجه به عرض شانه پارچه خام و عرض شانه ماشین، هیچ کارت تولیدی جهت تخصیص همراه کارت انتخاب شده یافت نشد، در نتیجه امکان ثخصیص کارت به تنهایی وجود ندارد.");

        }

        return \view($this->controller_info["view_path"] . "select_other_production", compact("machine", "production", "production_allow_list"));
    }

    public function select_other_band_production(Machine $machine, Production $production, Production $other_production)
    {
        $band_code = 2;
        $allocation = Allocation::where(
            [
                "machine_id" => $machine->id,
                "status_id" => 5310005,
            ]
        )->first();
        if (!$allocation) {
            return back()->withErrors("تخصیص یافت نشد، لطفا مجدد تلاش کنید.");
        }

        $machine_allocation = $allocation->items()->first();
        if (!$machine_allocation) {
            return back()->withErrors("تخصیص یافت نشد، لطفا مجدد تلاش کنید.");
        }
        // به دست آوردن حداکثر مقدار قابل تخصیص
        $before_allocation = $other_production->get_allocation_amount(false, 1, null, true, 1000);
        $before_allocation_amount = $before_allocation["allocation_amount"] ?? 0;
        if ($machine_allocation->allocation_amount + $before_allocation_amount > $other_production->number) {
            return back()->withErrors(" با توجه به اینکه قبلا مقدار $before_allocation_amount متر از کارت تخصیص داده شده است و مقدار کارت تولید " . $other_production->number . " متر می باشد. امکان ثبت تخصیص به  " . $machine_allocation->allocation_amount . " از کارت امکان پذیر نمی باشد. ");
        }

        $machine_allocation_array = $machine_allocation->toArray();
        $machine_allocation_array["production_id"] = $other_production->id;
        $machine_allocation_array["band_code"] = $band_code;
        $machine_allocation_array["product_id"] = $other_production->product_id;
        MachineAllocation::create(
            $machine_allocation_array
        );

        return redirect()->route($this->controller_info["route"] . "select_input_line", [$machine, $production, 0]);

    }

    public
    function select_band_submit(Request $request, Machine $machine, Production $production)
    {

        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }

        //در صورتی که کارت نمونه گیری باشد، باید اولویت کارت مشخص شود (کارت بین کدام کارت ها قرار بگیرد)
        session(["reserve_after_allocation_id_" . $production->id => $request->reserve_after_allocation_id]);


        // تعداد باند خروجی پارچه خام را مشخص می کنیم
        $band_number = MachineTypeOutputBand::get_number_band(4); //
        if ($band_number <= 0) {
            return redirect()->
            route($this->dashboard_route . "index", $production)->
            withErrors("باند خروجی گروه ماشین به درستی تعریف نشده است.");
        }

        // محاسبه تعداد باند تخصیص داده شده
        $number_of_band_selected = 0;
        for ($i = 1; $i <= $band_number; $i++) {
            $band_id = "band_" . $i;
            $band_amount = "band_amount_" . $i;
            if (isset($request->$band_id) && $request->$band_id > 0) {
                $number_of_band_selected++;
            }
        }
        $all_allocation_amount = 0;
        // تخصیص باندهای انتخاب شده به کارت تولید
        for ($i = 1; $i <= $band_number; $i++) {
            // $band_id                               = "band_" . $i;
            // $band_name                             = "band_name_" . $i;
            $band_amount = "band_amount_" . $i;
            $allocation_amount = $request->$band_amount;
            $all_allocation_amount += $allocation_amount;
            $allocation_amount_for_each_band[$i] = $allocation_amount;

        }

        // انتخاب اولین مسیر محصول، حتما هم وجود دارد
        $line_product_station = LineProductStation::where([
            "product_id" => $production->product_id,
            "machine_type_id" => $machine->machine_type_id
        ])->first();

        //چک کردن کانال تولید جاری ماشین و کانال تولید کارت تولید
        $result = ProductionChannel::CheckProductionChannelForAllocation(
            $machine, $production, $all_allocation_amount,
            $line_product_station->station_operation_id,
            $line_product_station->station_operation->station_operation_category_id
        );
        if (!$result["result"]) {
            if (isset($result["error"])) {
                return back()->withErrors($result["error"]);
            } else {
                $production_channel_type = $result["production_channel_type"];
                $station_operation_category_id = $result["station_operation_category_id"];

                // به صورت اتوماتیک یک کانال ایجاد می کنیم.
                $result_production_channel = GeneralProductionChannelController:: create_production_channel($machine, $production_channel_type, $production_channel_type->min_capacity, $production_channel_type->max_capacity, $allow_create_channel = false, $station_operation_category_id);
                if (!$result_production_channel["result"]) {
                    return back()->withErrors($result_production_channel["error"]);
                }
            }
        }


        $resultSelectBandSubmitAuto = self::SelectBandSubmitAuto($machine, $production, $number_of_band_selected, $allocation_amount_for_each_band, Auth::user()->id);

        if (!$resultSelectBandSubmitAuto["result"]) {
            return back()->withErrors($resultSelectBandSubmitAuto["error"]);
        }

        // انتخاب یک کارت تولید دیگر برای بافت در باند های دیگر
        return redirect()->route($this->controller_info["route"] . "select_other_production", [
            $machine,
            $production
        ]);

    }

    public
    static function SelectBandSubmitAuto(Machine $machine, Production $production, $number_of_band_selected, $allocation_amount_for_each_band, $user_id)
    {


        /**
         * حدف تخصیص های معلق قبلی
         */
        MachineAllocation::where([
            "machine_id" => $machine->id,
            "status_id" => 5310005
        ])->delete();


        /**
         * چک شود کیلوگرم کالا ثبت شده باشد.
         */
        if ($production->product->weight <= 0) {
            return [
                "result" => false,
                "error" => "مقدار وزن کالا معتبر نمی باشد."
            ];
        }


        $before_allocation_amount = false;
        // تعداد باند خروجی پارچه خام را مشخص می کنیم
        $band_number = MachineTypeOutputBand::get_number_band(4); //
        if ($band_number <= 0) {
            return [
                "result" => false,
                "error" => "باند خروجی گروه ماشین " . $machine->caption . " به درستی تعریف نشده است."
            ];
        }


        // تخصیص باندهای انتخاب شده به کارت تولید
        for ($band_code = 1; $band_code <= $band_number; $band_code++) {

            if (isset($allocation_amount_for_each_band[$band_code]) && $allocation_amount_for_each_band[$band_code] > 0) {

                $allocation_amount = $allocation_amount_for_each_band[$band_code];
                if ($before_allocation_amount != false && $before_allocation_amount != $allocation_amount) {
                    return [
                        "result" => false,
                        "error" => "مقدار تخصیص داده شده به هر باند باید برابر باشد."
                    ];

                }
                $before_allocation_amount = $allocation_amount;
                $all_allocation_amount = $allocation_amount * $number_of_band_selected;

                $result_doff = self::getNumberOfDoff($all_allocation_amount, $production, $machine, $number_of_band_selected);
                if (!$result_doff["result"]) {
                    return [
                        "result" => false,
                        "error" => $result_doff["error"]
                    ];
                }

                $number_of_doffs_done = 0;
                $max_number_of_doffs = $result_doff["number_of_doff"];
                $doff_amount_list = $result_doff["doff_amount_list"];
                $brand_info = $result_doff["brand_info"];
                $amount_of_each_doffs = round($all_allocation_amount / ($max_number_of_doffs * $number_of_band_selected), 2);

                // به ازای هر داف یک ردیف در لیست زیر ایجاد می کنیم، ( در حالتی که قاب وجود دارد.)
                $packing_type_doffs = null;
                if ($doff_amount_list) {
                    $k = 0;
                    foreach ($doff_amount_list as $doff_amount) {
                        $packing_type_doffs[] = [
                            "packing_type_id" => 0,
                            "max_number_of_doffs" => 1,
                            "amount_of_each_doffs" => $doff_amount,
                            "number_of_doffs_done" => 0,
                            "number_of_brand" => isset($brand_info[$k]) ? count($brand_info[$k]["brand_amount_list"]) : 0,
                            "brand_info" => isset($brand_info[$k]) ? $brand_info[$k] : null,
                        ];
                        $k++;
                    }
                    $amount_of_each_doffs = $doff_amount_list[0]; // مقدار داف تخصیص را برابر با اولین داف قرار می دهیم.
                }


                event(new MachineAllocationEvent(
                    $production,
                    $machine,
                    "Fabric_Raw",
                    $band_code,
                    $allocation_amount,
                    $number_of_doffs_done,
                    $max_number_of_doffs,
                    $amount_of_each_doffs,
                    $user_id,
                    null,
                    $packing_type_doffs,

                ));

            }

        }

//
//        //چک کردن کانال تولید جاری ماشین و کانال تولید کارت تولید
//        $result = ProductionChannel::CheckProductionChannelForAllocation( $machine, $production, $all_allocation_amount );
//        if ( ! $result["result"] ) {
//            if ( isset( $result["error"] ) ) {
//                return [
//                    "result"=>false,
//                    "error"=>$result["error"]

//                ];
//                return back()->withErrors( $result["error"] );
//            } else {
//                return redirect()->route( $this->controller_info["route"] . "create_new_channel", [
//                    $machine,
//                    $production,
//                    $all_allocation_amount
//                ] );
//            }
//        }


        //  $production_allow_list = $this->get_other_production_for_allocation( $machine, $production );
        // هیچ کارت تولیدی برای بافت در باندهای دیگر یافت نشد، تایید تخصیص و خاتمه
        // if ( count( $production_allow_list ) == 0 ) {

        return [
            "result" => true,
            "machine" => $machine
        ];
        //return redirect()->route( $this->controller_info["route"] . "select_input_line", [ $machine, 0 ] );
        // }


        // انتخاب یک کارت تولید دیگر برای بافت در باند های دیگر
        //  return redirect()->route( $this->controller_info["route"] . "select_other_production", [
//        $machine,
//            $production
//        ] );

    }

    private
    function get_other_production_for_allocation($machine, $production)
    {
        //         عرض شانه ماشین
        $machine_shoulder_width = MachinePropertyValue::
        where(["machine_type_id" => $machine->machine_type->id, "machine_property_id" => 1])->
        first()->value;

        // عرض  باقی مانده ماشین
        $machine_shoulder_width_allocation = 0;
        $product_allocation_list = MachineAllocation::where([
            "machine_id" => $machine->id,
            "status_id" => 5310005
        ])->
        get();

        foreach ($product_allocation_list as $item) {
            $item_value = $item->production->product->getPropertyValue($this->shoulder_width_id);
            $machine_shoulder_width_allocation += isset($item_value) ? $item_value->value : 0;

        }
        $machine_shoulder_width_allocation = $machine_shoulder_width - $machine_shoulder_width_allocation;

        /**
         * پیدا کردن کارت تولید هایی که می تواند در باندهای دیگر ماشین بافته شوند
         */

        // وضعیت های مجاز تخصیص
        $production_status_list = [7001001, 7001002, 7001003];

        // به دست آورن مقدار مشخصه ها
        $production_list = Production::
        whereIn("waiting_status_id", $production_status_list)->
        where("id", "!=", $production->id)->
        get();

        $checklist = [220224, 220225, 220237, 220238, 220241, 220242, 220279, 220280];
        $property_value_product = GoodsKindPropertyValue::
        where("product_id", $production->product->id)->
        whereIn("goods_kind_property_id", $checklist)->orderBy("goods_kind_property_id")->
        pluck("value")->toArray();


        $production_allow_list = [];
        foreach ($production_list as $item) {

            $property_value_item = GoodsKindPropertyValue::
            where("product_id", $item->product->id)->
            whereIn("goods_kind_property_id", $checklist)->
            pluck("value", "goods_kind_property_id")->toArray();

            // عرض پارچه
            $product_shoulder_width = $item->product->getPropertyValue($this->shoulder_width_id);
            if (isset($product_shoulder_width)) {
                $product_shoulder_width = $product_shoulder_width->value;
            } else {
                $product_shoulder_width = -1;
            }

// همه مشخصه های پارچه یکی باشد و عرض شانه باقی مانده ماشین از عرض شانه کالا کمتر مساوی باشد.
            if (count(array_diff($property_value_product, $property_value_item)) == 0 && $machine_shoulder_width_allocation >= $product_shoulder_width) {
                $production_allow_list [] = $item;
            }

        }

        return $production_allow_list;
    }

    // مقدار دهی باند ورودی و خط ورودی
    public
    function select_input_line(Machine $machine, Production $production, $is_edit = false, $permutation_select_number = 0, $end_result_index = 0)
    {

        $permutation_list = session("permutation_list_" . $production->id);

        $result_select_input_lint = self::SelectInputLineAuto($machine, $is_edit, $permutation_select_number, $end_result_index, $permutation_list);

        if (!$result_select_input_lint["result"]) {

            if (isset($result_select_input_lint["warning"])) {

                $production = $result_select_input_lint["production"];
                $permutation_list = $result_select_input_lint["permutation_list"];

                session(["permutation_list_" . $production->id => $permutation_list]);

                // انتخاب یکی از حالت های قابل قبول برای تخصیص
                return redirect()->
                route($this->controller_info["route"] . "select_permutation", [$machine, $production]);
            }

//            if (isset($result_select_input_lint["select_other_production"])) {
//                // انتخاب یک کارت تولید دیگر برای بافت در باند های دیگر
//                return redirect()->route($this->route_path . "select_other_production", [$machine, $production]);
//
//            }

            return redirect()->route($this->route_path . "select_band", [$machine, $machine->machine_type, $production, 1])->withErrors($result_select_input_lint["error"]);
        }


        $warps_count = $result_select_input_lint["warps_count"];
        $amount_list = $result_select_input_lint["amount_list"];
        $production = $result_select_input_lint["production"];
        $machine_input_output_band_list = $result_select_input_lint["machine_input_output_band_list"];
        $machine_allocation = $result_select_input_lint["machine_allocation"];
        $permutation_list = $result_select_input_lint["permutation_list"];
        $permutation_select_number = $result_select_input_lint["permutation_select_number"];
        $end_result_index = $result_select_input_lint["end_result_index"];


        session(["permutation_list_" . $production->id => $permutation_list]);
        session(["permutation_select_number_" . $production->id => $permutation_select_number]);
        session(["end_result_index_" . $production->id => $end_result_index]);


        return view($this->controller_info["view_path"] . "select_input_line", compact("machine", "warps_count", "amount_list", "production", "machine_input_output_band_list", "machine_allocation"));
    }

    public
    static function SelectInputLineAuto(Machine $machine, $is_edit = false, $permutation_select_number = 0, $end_result_index = 0, $permutation_list = null, $declared_inventory = [])
    {


        $amount_list = [];

        $machine_allocation = MachineAllocation::where([
            "machine_id" => $machine->id,
            "status_id" => 5310005
        ])->get();

        if (count($machine_allocation) == 0) {
            return [
                "result" => false,
                "error" => "هیچ ماشینی تخصیص داده نشده است و یا با توجه به مشخصات کالا امکان تخصیص کارت به ماشین وجود ندارد."
            ];

        }

        $allocation = $machine_allocation[0]->allocation;


        if (!$is_edit) {
            CurrentMachineInput::DeleteData($machine_allocation[0]->allocation_id);
        }

        foreach ($machine_allocation as $allocation_item) {

            $production = $allocation_item->production;

            // Check BOM Exists
            if ($production->product->bill_of_material()->count() == 0) {
                return [
                    "result" => false,
                    "error" => "BOM کالای" . $production->product->fullCaption() . " تعریف نشده است."
                ];

            }

            /* به دست آورد مسیر تولید */
            $line_product_station = LineProductStation::where([
                "product_id" => $production->product_id,
                "machine_type_id" => $machine->machine_type_id
            ])->
            first();

            if (!$line_product_station) {
                return [
                    "result" => false,
                    "error" => "  برای  تولید کالای " . $production->product->fullCaption() . " توسط ماشین های " . $machine->machine_type->caption . " مسیری یافت نشد."
                ];


            }


            // انتخاب اولین BOM از مسیر برای تخصیص
            $bom = $production->product->get_first_bom_from_route($machine);
            if (!$bom) {
                $product_route = ProductRoute::find($line_product_station->product_route_id);

                return [
                    "result" => false,
                    "error" => "  برای  تولید کالای " . $production->product->fullCaption() . " توسط ماشین های " . $machine->machine_type->caption . " (" . $product_route->fullCaption() . ") " . " هیچ BOMی تعریف نشده است."
                ];


            }

            $result_check_weight = Product::CheckWeight($production->product, $bom);
            if (!$result_check_weight["result"]) {

                return [
                    "result" => false,
                    "error" => $result_check_weight["error"]
                ];

            }

            if ($bom->items->count() == 0) {
                return [
                    "result" => false,
                    "error" => $bom->caption . "  برای کالای " . $production->product->fullCaption() . " ;به صورت کامل تعریف نشده است."
                ];

            }

            $bom_input_goods_kind_check = [];
            foreach ($bom->items()->with("material")->get() as $bom_item) {
                if ($bom_item->degrees()->count() == 0) {
                    return [
                        "result" => false,
                        "error" => "درجه های مجاز " . $bom_item->product->caption . "  در تعریف BOM  مشخص نشده است."
                    ];

                }
                if (isset($bom_input_goods_kind_check[$bom_item->material->goods_kind_id][$bom_item->input_line_code])) {
                    return [
                        "result" => false,
                        "error" => " ورودی های مواد اولیه در  BOM کالای " . $bom_item->product->caption . "  نا معتبر است،<br/> به ازای هر ورودی فقط یک ماده اولیه می تواند وارد شود.."
                    ];
                }
                $bom_input_goods_kind_check[$bom_item->material->goods_kind_id][$bom_item->input_line_code] = 1;
            }

            //گرفتن گراف جریان مواد
            // بررسی یک به یک بودن گراف
            $material_flow = MaterialFlow::
            where("bill_of_material_id", $bom->id)->
            where("machine_type_id", $machine->machine_type_id)->
            where("band_code", $allocation_item->band_code)->
            get();
            if (count($material_flow) == 0) {
                return [
                    "result" => false,
                    "error" => "گراف جریان مواد برای " . $production->product->fullCaption() . " در باند تعریف نشده است،"
                ];

            }
            // چک کردن اینکه گراف جریان مواد به درستی تعریف شده است یا خیر
            $material_flow_graph_link_count = MaterialFlow::
            where("bill_of_material_id", $bom->id)->
            where("machine_type_id", $machine->machine_type_id)->
            count();
            $output_count = MachineTypeOutputBand::where("machine_type_id", $machine->machine_type_id)->
            where("active_status_id", 1200)->first();
            if (!$output_count) {
                return [
                    "result" => false,
                    "error" => "خط های خروجی برای ماشین مشخص نشده اند."
                ];
            }
            $check_material_flow_in_machine_allocation = Setting::getIntegerValue("check_material_flow_in_machine_allocation");

            if ($check_material_flow_in_machine_allocation) {
                $output_line_number = $output_count->output_line_number;
                if ($material_flow_graph_link_count < $bom->items()->where("material_id", "!=", $bom->product_id)->count() * $output_line_number) {
                    return [
                        "result" => false,
                        "error" => "گراف جریان مواد برای " . $production->product->fullCaption() . " به صورت کامل تعریف نشده است،"
                    ];

                }
            }


            foreach ($material_flow as $graph_link) {

                // به دست آوردن شناسه باند ورودی
                $input_band = MachineTypeInputBand::
                join("machine_type_input_band_goods_kind", "machine_type_input_band_id", "machine_type_input_bands.id")->
                where([
                    "active_status_id" => 1200, // فعال
                    "machine_type_id" => $machine->machine_type_id,
                    "goods_kind_id" => $graph_link->bom_item->material->goods_kind_id
                ])->
                select("machine_type_input_bands.id", "input_line_number")->
                get();

                if (count($input_band) != 1) {
                    return [
                        "result" => false,
                        "error" => "مشخصات باند ورودی برای " . $graph_link->bom_item->material->fullCaption() . " یافت نشد، لطفا با پشتیبانی سیستم تماس بگیرید."
                    ];

                }

                $input_band = $input_band[0];

                // بررسی محدودیت تعداد ورودی های ماشین
                if ($graph_link->bom_item->input_line_code > $input_band->input_line_number) {
                    return [
                        "result" => false,
                        "error" => "با توجه به محدودیت تعداد خط های ورودی در " . $graph_link->bom_item->material->goods_kind->caption . "  امکان تخصیص وجود ندارد."
                    ];


                }

                $consume_warehouse_id = BOMItem::getConsumeWarehouseId($graph_link->bom_item, $production, $machine);
                // به ازای لینکی که در گراف وجود دارد یک درخواست یک ردیف ورودی ثبت می کند.
                CurrentMachineInput::CreateOrUpdate(
                    $input_band->id,
                    $graph_link->bom_item->input_line_code,   //  کد خط ورودی
                    $graph_link->bom_item->material_id,
                    $graph_link->bom_item->amount,
                    CurrentMachineInput::getAmountRequired($graph_link, $allocation, $allocation_item->production_id),
                    $graph_link->bom_item->percent_of_use,
                    $graph_link->bom_item->material->goods_kind_id,
                    $allocation->id,
                    $machine->id,
                    $allocation_item->product_id,
                    $allocation_item->production_id,
                    $graph_link->band_code,
                    1,
                    null,
                    null,
                    $consume_warehouse_id,
                    $graph_link->bom_item->warehouse_id,
                    $graph_link->bom_item,
                    $graph_link->bom_item->bill_of_material_entering_type_id
                );

            }


            $amount_list[$allocation_item->band_code]["production_amount"] = $allocation_item->allocation_amount;
            $amount_list[$allocation_item->band_code]["warps"] = [
                "result" => null,
                "amount" => "0",
                "sub_amount" => "0",
                "sum_amount_reserve" => "0",
                "amount_begin_of_weaving" => "0",
                "message" => "چله ای از قبل بر روی دستگاه قرار نداشته است."
            ];
        }


        // محاسبه ضریف مصرف کانال تولید برای تخصیص
        $result_consumption_percent = BOM::GetConsumptionOfProductionChannel($bom);

        if (!$result_consumption_percent["result"]) {
            return $result_consumption_percent;
        }
        $allocation->consumption_percent_of_production_channel = $result_consumption_percent["value"];
        $allocation->save();

        // بررسی اینکه درصد استفاده هر کالایی مضربی از 100 باشد.
        $band_count = count($machine_allocation); // درصد استفاده در تعداد باند ضرب می شود.
        $check_percent_list = CurrentMachineInput::
        where("allocation_id", $allocation->id)->
        groupBy("material_id")->
        havingRaw("ROUND(SUM(percent_of_use), 3) * ? % 100 > 0", [$band_count])-> // جمع درصد ها تا 3 رقم اعشار رند می وشد.
        select("goods_kind_id", "input_line_code", "material_id")->
        get();

        if (count($check_percent_list) != 0) {
            $message = "";
            foreach ($check_percent_list as $check_item) {
                $message .= "با توجه به درصد مصرف " . $check_item->material->fullCaption() . " در BOM امکان تخصیص این کالا به ماشین وجود ندارد. " . "<br/>" . " تعداد باندهای کالا برابر با $band_count
                 می باشد، لطفا مشخصات کالا و مشخصات مسیر محصول را بررسی فرمایید.";
            }

            return [
                "result" => false,
                "error" => $message,

            ];

        }


        // بررسی اینکه به ازای هر ورودی چله فقط یک حامل در آن وجود داشته باشد.
//          $check_warps_percent_list = CurrentMachineInput::
//        where("allocation_id", $allocation->id)->
//        where("goods_kind_id", 3)-> // چله
//        //where( "percent_of_use", "!=", 100 )-> //
//        select("goods_kind_id", "input_line_code", "material_id", "percent_of_use")->
//        get();
//        foreach ($check_warps_percent_list as $check_warps) {
//
//            if ($check_warps->percent_of_use * $band_count != 100) {
//
////                return [
////                    "result" => false,
////                    "error" => "درصد مصرف چله در BOM به درستی تعریف نشده است."
////                ];
//
//            }
//        }

// دریافت ورودی های ماشین
//        $machine_input_output_band_list = CurrentMachineInput::where( [
//            "allocation_id" => $machine_allocation[0]->allocation_id
//        ] )->
//        orderBy( "input_band_id" )->orderBy( "input_line_code" )->
//        get();


// بررسی مقدار بافی مانده چله در هر باند
        $message = "";
        $warps_request = ProductRequestForm:: getLatestRequestForm($machine->warehouse_id, 40, 3);
        $warps_count = 0;
        if ($warps_request) {
            // بررسی مقدار باقی مانده چله با توجه به قطب ها
            $input_band_code = 1;
            foreach ($warps_request->items as $item) {

                $result = Warps::getRemainingAmountOfWarps($warps_request, $item, null, $item->product_id ?? null);
                if (!$result["result"]) {
                    $message .= $result["message"] . "<br/>";
                }
                // اولین ورودی درخواست را به اولین ورودی ماشین تخصیص می دهد و همین طور ورودی دوم و سوم.
                // ولی درست آن وقتی مشخص می شود که اپراتور پله ها را بر روی ماشین قرار می دهد و تزریق مواد اولیه انجام می دهد
                $amount_list[$input_band_code]["warps"] = $result;
                $input_band_code++;
            }
            $warps_count = $warps_request->items->count();
        }


        //ذخیره مقدار باقی مانده چله در زمان شروع بافت پارچه
        if (isset($amount_list[1]["warps"]["amount_begin_of_weaving"])) {
            Allocation\AllocationData::SetFloatValue(100, $amount_list[1]["warps"]["amount_begin_of_weaving"], $machine_allocation[0]->allocation_id);
        }

// وقتی دو کارت 130 و 260 را با هم تخصیص می دهیم، BOM کالای اصلی را بررسی می کنیم، بنابراین کارت تولید مرجمع ما می شود کارت آیتم تخصیص با اولویت 1 که اگر این کار را انجام ندیهم ، آخرین کارت را در نظر می گیرد، با توجه به حلقه قبلی
        $production = $machine_allocation[0]->production;
        $bom = $production->product->get_first_bom_from_route($machine);

        // این خط کد نادرست است، برای جاهایی که دو کارت دارند مقدار تخصیص کارت اصلی را محاسبه میکنند ( مثل نگین عرض 130 و 260)
        $allocation_amount = $allocation->items()->where("product_id", $bom->product_id)->sum("allocation_amount");
        //  $permutation_list  = Allocation::getMaxAllocationAmountAccordingToWarehouse( $allocation, $allocation_amount, $bom, $declared_inventory );

        if (!isset($permutation_list)) {

            $permutation_list = Allocation::getMaxAllocationAmountAccordingToWarehouse($allocation, $allocation_amount, $bom, $declared_inventory, $production->id);
        }

        // ذخیره اطلاعات محاسبات تخصیص
        Allocation\AllocationData::SetData(200, $permutation_list, $allocation->id);

        // بررسی موجودی انبار جهت تخصیص
        if ($machine->check_inventory_for_allocation && $permutation_select_number == 0) {

            if ($permutation_list["end_result"][0]["amount_can_be_produced"] < $allocation_amount) { // جمع کل تخصیص)

                return [
                    "result" => false,
                    "warning" => " با توجه به اینکه چند حالت برای تخصیص وجود دارد، باید یکی از حالت های تخصیص انتخاب گردد.",
                    "permutation_list" => $permutation_list,
                    "production" => $production
                ];
                // انتخاب یکی از حالت های قابل قبول برای تخصیص
//                return redirect()->
//                route( $this->controller_info["route"] . "select_permutation", [ $machine, $production ] );

            }

        }


        // اگر از حالت های جایگزین انتخاب شده باشد، باید مقدار، درصد مصرف و تعداد در ورودی های ماشین و همچنین کد کالا تغییر کند.
        if ($permutation_select_number > 0) {


            if (!isset($permutation_list["permutation_list"][$permutation_select_number][$end_result_index])) {
                return [
                    "result" => false,
                    "error" => "اطلاعات انتخاب اولویت به درستی وارد نشده است."
                ];
            } else {


                $new_allocation_amount = $permutation_list["permutation_list"][$permutation_select_number][$end_result_index];
                $number_of_band_selected = $allocation->items()->count();
                $new_allocation_amount = $new_allocation_amount;
// $new_allocation_amount کل مقدار تخصیص می باشد.
                $result_doff = self::getNumberOfDoff($new_allocation_amount, $production, $machine, $number_of_band_selected);
                if (!$result_doff["result"]) {
                    return [
                        "result" => false,
                        "error" => $result_doff["error"]
                    ];
                }

                $number_of_doffs_done = 0;
                $max_number_of_doffs = $result_doff["number_of_doff"];
                $doff_amount_list = $result_doff["doff_amount_list"];
                $brand_info = $result_doff["brand_info"];
                $amount_of_each_doffs = round($new_allocation_amount / ($max_number_of_doffs * $number_of_band_selected), 2);

                // به ازای هر داف یک ردیف در لیست زیر ایجاد می کنیم، ( در حالتی که قاب وجود دارد.)
                $packing_type_doffs = null;
                if ($doff_amount_list) {
                    $k = 0;
                    foreach ($doff_amount_list as $doff_amount) {
                        $packing_type_doffs[] = [
                            "packing_type_id" => 0,
                            "max_number_of_doffs" => 1,
                            "amount_of_each_doffs" => $doff_amount,
                            "number_of_doffs_done" => 0,
                            "number_of_brand" => isset($brand_info[$k]) ? count($brand_info[$k]["brand_amount_list"]) : 0,
                            "brand_info" => isset($brand_info[$k]) ? $brand_info[$k] : null,
                        ];
                        $k++;
                    }
                    $amount_of_each_doffs = $doff_amount_list[0]; // مقدار داف تخصیص را برابر با اولین داف قرار می دهیم.
                }


                // بروز رسانی مقدار تخصیص
                foreach ($allocation->items as $allocation_item) {
                    // $new_allocation_amount مقدار کل تخصیص است و باید به تعداد باندها تقسیم شود.
                    $allocation_item->allocation_amount = $new_allocation_amount / $number_of_band_selected;
                    $allocation_item->max_number_of_doffs = $result_doff["number_of_doff"];
                    $allocation_item->amount_of_each_doffs = $amount_of_each_doffs;
                    $allocation_item->save();
                }

                if ($packing_type_doffs) {
                    Allocation\AllocationDoffs::where("allocation_id", $allocation->id)->delete();
                    Allocation\AllocationDoffs::AddList($allocation->id, $packing_type_doffs);
                }

                foreach ($permutation_list["end_result"][$end_result_index]["material"] as $material_info) {
                    self:: UpdateCurrentMachineInput($allocation, $material_info, $new_allocation_amount, $production);
                }

                $machine_input_output_band_list = CurrentMachineInput::where([
                    "allocation_id" => $machine_allocation[0]->allocation_id
                ])->
                groupBy("input_line_code", "material_id")->
                orderBy("input_band_id")->orderBy("input_line_code")->
                get();

                $machine_allocation = MachineAllocation::where([
                    "machine_id" => $machine->id,
                    "status_id" => 5310005
                ])->get();
            }

        } else {

            if (!isset($permutation_list["end_result"][0]["amount_can_be_produced"])) {
                return [
                    "result" => false,
                    "error" => "اطلاعات انتخاب اولویت به درستی وارد نشده است."
                ];
            } else {


                foreach ($permutation_list["end_result"][0]["material"] as $material_info) {
                    self:: UpdateCurrentMachineInput($allocation, $material_info, $allocation_amount, $production);

                }

                $machine_input_output_band_list = CurrentMachineInput::where([
                    "allocation_id" => $machine_allocation[0]->allocation_id
                ])->
                groupBy("input_line_code", "material_id")->
                orderBy("input_band_id")->orderBy("input_line_code")->
                get();

                $machine_allocation = MachineAllocation::where([
                    "machine_id" => $machine->id,
                    "status_id" => 5310005
                ])->get();
            }
        }


        return [
            "result" => true,
            "warps_count" => $warps_count,
            "amount_list" => $amount_list,
            "production" => $production,
            "machine_input_output_band_list" => $machine_input_output_band_list,
            "machine_allocation" => $machine_allocation,
            "permutation_select_number" => $permutation_select_number,
            "end_result_index" => $end_result_index,
            "permutation_list" => $permutation_list,
        ];

    }

    /**
     * @param \App\Models\LineProduct\Machine\Machine $machine
     * @param \App\Models\Production\Production $production
     * انتخاب حالت های مختلف
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public
    function select_permutation(Machine $machine, Production $production)
    {

        $permutation_list = session("permutation_list_" . $production->id);
        if (!isset($permutation_list)) {
            session(["permutation_list_" . $production->id => null]);

            return redirect()->route($this->dashboard_route . "index", $production)->withErrors("نشست شما به پایان رسیده است، لطفا دوباره تلاش کنید.");
        }

        if (count($permutation_list["permutation_list"]) == 0) {

            $message = GeneralMachineAllocationController::GetMessagePermutation($permutation_list, $production, $machine);

            return redirect()->route($this->dashboard_route . "index", $production)->
            withErrors($message);

        }

//return $permutation_list;
        $products_ids = [];
        foreach ($permutation_list["permutation_list"] as $p_number => $permutation_item) {
            foreach ($permutation_item as $key => $item) {

                foreach ($permutation_list["end_result"][$key]["material"] as $material) {
                    $products_ids[] = $material["material_id"];
                }
            }
        }

        $products = Product::whereIn("id", $products_ids)->get()->keyBy("id");

        // به دست آوردن SP به ازای هر لیست
        $bom = null;
        foreach ($permutation_list["end_result"] as $key_result => $item) {

            if (isset($item["bom_id"])) {
                $bom = Product\BOM\BOM::find($item["bom_id"]);
            }
            $material_ids = [];
            foreach ($item["material"] as $material_info) {
                $material_ids[] = $material_info["material_id"];
            }
            if ($bom) {

                $result = Product\BOM\BOMPermutation::GetSPCodeFromMaterialId($bom, $material_ids);
                if ($result["result"]) {
                    $permutation_list["end_result"][$key_result]["bom_permutation"] = $result["bill_of_material_permutation"];
                }
            }

        }


        return view($this->controller_info["view_path"] . "select_permutation", compact("machine", "products", "production", "permutation_list"));

    }

    public
    function submit_select_permutation(Request $request, Machine $machine, Production $production)
    {

        $permutation_list = session("permutation_list_" . $production->id);
        if (!isset($permutation_list)) {
            return redirect()->route($this->dashboard_route . "index", $production)->withErrors("نشست شما به پایان رسیده است، لطفا دوباره تلاش کنید.");
        }
        if (!$request->permutation_select_number) {
            return back()->withErrors("لطفا یک حالت را انتخاب نمایید.");
        }
        session(["permutation_select_number_" . $production->id => $request->permutation_select_number]);

        $end_result_id = self::getFirstIndexAfter($permutation_list["permutation_list"][$request->permutation_select_number], -1);

//return $request->all();
        return redirect()->route($this->controller_info["route"] . "select_input_line", [
            $machine,
            $production,
            0,
            $request->permutation_select_number,
            $end_result_id
        ]);
    }

    public
    function get_allocation_different(
        Allocation $allocation, Production $production
    )
    {

        // در صورتی که کارت نمونه گیری باشد، این متغیر در select_band مقدار دهی می شود.

        $reserve_after_allocation_id = session("reserve_after_allocation_id_" . $production->id);
        $reserve_after_allocation = Allocation::find($reserve_after_allocation_id ?? 0);

// گرفتن تخصیص قبلی
        $before_allocation = $allocation->before_allocation();
        if ($reserve_after_allocation) {
            if ($reserve_after_allocation->machine_id != $allocation->machine_id) {
                $machine_allocation = $allocation->items()->first();
                return redirect()->
                route($this->route_path . "select_band", [$machine_allocation->machine_id, $machine_allocation->machine->machine_type_id, $machine_allocation->production_id, true])->
                withErrors("کارت تولید انتخاب شده نامعتبر است، لطفا یکبار دیگر تلاش کنید.");
            }
            $before_allocation = $reserve_after_allocation;
        }


        //بررسی اینکه مشخص عرض ماشین برای هر دو تخصیص وجود داشته باشد.
        if ($before_allocation && count($before_allocation->items) > 0) {

            $result = $before_allocation->items()->first()->getRouteProperty(1, 1);
            if (!$result["result"]) {
                return back()->withErrors("مشخصه عرض شانه برای " . $before_allocation->items()->first()->product->caption . " تعریف نشده است، لطفا با مسئولین اطلاعات پایه تماس بگیرید. ");
            }
        }

        if ($allocation->items()->count() == 0) {
            return back()->withErrors("آیتم های تخصیص یافت نشد، لطفا یکبار دیگر تلاش کنید.");
        }
        $result = $allocation->items->first()->getRouteProperty(1, 1);

        if (!$result["result"]) {
            return back()->withErrors("مشخصه عرض شانه برای " . $allocation->items()->first()->product->caption . " تعریف نشده است، لطفا با مسئولین اطلاعات پایه تماس بگیرید. ");
        }


        $value = Allocation::getDifferentTowAllocation($before_allocation, $allocation, "Fabric_Raw");

        $value_log = $value;
        $value_log["before_allocation"] = $before_allocation->id ?? "";
        Allocation\AllocationData::SetData(300, $value_log, $allocation->id);

        $before_warps_bom = CurrentMachineInput::where("allocation_id", $before_allocation->id ?? 0)->
        where("goods_kind_id", 3)->
        groupBy("input_line_code", "material_id")->
        orderBy("material_id")->
        get();


        $current_warps_bom = CurrentMachineInput::where("allocation_id", $allocation->id ?? 0)->
        where("goods_kind_id", 3)->
        groupBy("input_line_code", "material_id")->
        orderBy("material_id")->
        get();


        $before_yarn_weft_bom = CurrentMachineInput::where("allocation_id", $before_allocation->id ?? 0)->
        where("goods_kind_id", 2)->
        groupBy("input_line_code", "material_id")->
        orderBy("material_id")->
        get();
        $current_yarn_weft_bom = CurrentMachineInput::where("allocation_id", $allocation->id ?? 0)->
        where("goods_kind_id", 2)->
        groupBy("input_line_code", "material_id")->
        orderBy("material_id")->
        get();

        return view($this->controller_info["view_path"] . "get_allocation_different", compact("allocation", "value", "before_warps_bom", "current_warps_bom", "before_yarn_weft_bom", "current_yarn_weft_bom"));

    }

    public
    function confirm_submit(
        Machine $machine
    )
    {

        $machine_allocation = MachineAllocation::where([
            "machine_id" => $machine->id,
            "status_id" => 5310005
        ])->
        orderBy("band_code")-> // برای اینکه در زمان گرفتن کارت تولید، کارتی که بر روی باند یک است به عنوان اولین اندیس باشد.
        get();

        // گرفتن تخصیص معلق
        $allocation = Allocation::where([
            "machine_id" => $machine->id,
            "status_id" => 5310005
        ])->first();

        if (count($machine_allocation) == 0 || !$allocation) {
            return back()->withErrors("هیچ ماشینی تخصیص داده نشده است.");

        }

        $production = $machine_allocation[0]->production;


        // انتخاب اولین مسیر محصول، حتما هم وجود دارد
        $line_product_station = LineProductStation::where([
            "product_id" => $production->product_id,
            "machine_type_id" => $machine->machine_type_id
        ])->first();

        //چک کردن کانال تولید جاری ماشین و کانال تولید کارت تولید
        $result = ProductionChannel::CheckProductionChannelForAllocation($machine, $production, $allocation->getAllocationAmount(),
            $line_product_station->station_operation_id,
            $line_product_station->station_operation->station_operation_category_id
        );;

        if (!$result["result"]) {
            if (isset($result["error"])) {
                return back()->withErrors($result["error"]);

            } else {
                $production_channel_type = $result["production_channel_type"];
                $station_operation_category_id = $result["station_operation_category_id"];
                // به صورت اتوماتیک یک کانال ایجاد می کنیم.
                $result_production_channel = GeneralProductionChannelController:: create_production_channel($machine, $production_channel_type, $production_channel_type->min_capacity, $production_channel_type->max_capacity, true, $station_operation_category_id);
                if (!$result_production_channel["result"]) {
                    return back()->withErrors($result_production_channel["error"]);

                }
            }
        }
        // در صورتی که کارت نمونه گیری باشد، این متغیر در select_band مقدار دهی می شود.
        $reserve_after_allocation_id = session("reserve_after_allocation_id_" . $production->id);

        $result_confirm_auto = self::ConfirmSubmitAuto($machine, Auth::user()->id, $reserve_after_allocation_id);

        if (!$result_confirm_auto["result"]) {
            return back()->withErrors($result_confirm_auto["error"]);
        }

        $allocation = $result_confirm_auto["allocation"];
        $production = $result_confirm_auto["production"];


        // بررسی انیکه حالت های تخصیص انتخاب شده است یا خیر
        $permutation_list = session("permutation_list_" . $production->id);
        $permutation_select_number = session("permutation_select_number_" . $production->id);
        $end_result_index = session("end_result_index_" . $production->id);

        // اگر از حالت های جایگزین انتخاب شده باشد، باید مقدار، درصد مصرف و تعداد در ورودی های ماشین و همچنین کد کالا تغییر کند.
        if (
            isset($permutation_select_number) &&
            $permutation_select_number > 0
        ) {
            $end_result_index++;
            // انتخاب ردیف بعدی از حالت انتخاب شده جهت تخصیص
            $end_result_index = $this->getFirstIndexAfter($permutation_list["permutation_list"][$permutation_select_number], $end_result_index);

            if ($end_result_index > 0) {

                foreach ($allocation->items as $machine_allocation) {
                    event(new MachineAllocationEvent(
                        $production,
                        $machine,
                        "Fabric_Raw",
                        $machine_allocation->band_code,
                        $machine_allocation->allocation_amount,
                        $machine_allocation->number_of_doffs_done,
                        $machine_allocation->max_number_of_doffs,
                        $machine_allocation->amount_of_each_doffs
                    ));
                }

                return redirect()->route($this->controller_info["route"] . "select_input_line", [
                    $machine,
                    $production,
                    0,
                    $permutation_select_number,
                    $end_result_index
                ]);
            }

        }


        return redirect()->route("production.dashboard.list")->with(["success" => "عملیات تخصیص با موفقیت انجام شد."]);

    }

    public
    static function ConfirmSubmitAuto(Machine $machine, $user_id, $reserve_after_allocation_id = 0)
    {

        // در صورتی که کارت نمونه گیری باشد، این متغیر در select_band مقدار دهی می شود.
        $reserve_after_allocation = Allocation::find($reserve_after_allocation_id ?? 0);

        $machine_allocation = MachineAllocation::where([
            "machine_id" => $machine->id,
            "status_id" => 5310005
        ])->
        orderBy("band_code")-> // برای اینکه در زمان گرفتن کارت تولید، کارتی که بر روی باند یک است به عنوان اولین اندیس باشد.
        get();

        // گرفتن تخصیص معلق
        $allocation = Allocation::where([
            "machine_id" => $machine->id,
            "status_id" => 5310005
        ])->first();

        if (count($machine_allocation) == 0 || !$allocation) {
            return [
                "result" => false,
                "error" => "در زمان ثبت نهایی تخصیص هیچ ماشینی یافت نشد، لطفا یکبار دیگر تلاش کنید و در صورت تکرار خطا با پشتیبانی تماس بگیرید."
            ];

        }

        $production = $machine_allocation[0]->production;


        // اضافه کردن مقدار تخصیص به کانال تولید
        $all_allocation_amount = 0;
        foreach ($machine_allocation as $ma) {
            $all_allocation_amount += $ma->allocation_amount;
        }
        $production_channel_type = $production->getProductionChannelType();
        if (!$production_channel_type) {
            return [
                "result" => false,
                "error" => "نوع کانال تولید برای کارت تولید مشخص نشده است."
            ];

        }

        // انتخاب اولین مسیر محصول، حتما هم وجود دارد
        $line_product_station = LineProductStation::where([
            "product_id" => $production->product_id,
            "machine_type_id" => $machine->machine_type_id
        ])->first();
        $result_add_allocation = Allocation\MachineAllocationProductionChannel::AddAllocation($machine, $allocation, $production_channel_type->id, $line_product_station->station_operation->station_operation_category_id, $all_allocation_amount);
        if (!$result_add_allocation["result"]) {
            return [
                "result" => false,
                "error" => $result_add_allocation["error"]
            ];

        }

        // بررسی تفاوت تخصیص جدید با تخصیص قبلی
        $allocation = $allocation->setChangesFromBeforeAllocation("Fabric_Raw", $reserve_after_allocation);


        if ($allocation->has_design_change == 1 && $allocation->has_warps_change == 0) {
            return [
                "result" => false,
                "error" => "تخصیص در حالتی که طراحی ماشین تغییر کند و چله عوض نشود، هنوز پیاده سازی نشده است، لطفا با پشتیبانی تماس بگیرید."
            ];
        }


        $allocation_status = 5310040; // تخصیص رزرو شده

        // تغییر وضعیت تخصیص فعلی
        $allocation->status_id = $allocation_status;


        // به دست آوردن اولویت کارت
        switch ($production->production_type_id) {
            case 1:
                if (!$reserve_after_allocation) {

                    $priority_number = $machine->ReserveAllocation()->count() + 1;
                } else {
                    $priority_number = $reserve_after_allocation->priority_number + .5;
                }

                break;
            case 2:
                if (!$reserve_after_allocation) {

                    // تخصیصی که باید بعد از آن کارت نمونه گیری تخصیصی داده شود، یافت نشد.
                    $priority_number = .5;
                } elseif ($reserve_after_allocation->status_id == 5310020) { // خاتمه یافته
                    $priority_number = .5;
                } else {
                    $priority_number = $reserve_after_allocation->priority_number + .5;
                }

                break;
            default:
                //نوع کارت تولید مشخص نشده است
                $priority_number = $machine->ReserveAllocation()->count() + 1;

        }


        $allocation->priority_number = $priority_number;
        $allocation->save();


        foreach ($allocation->items as $item) {

            $item->status_id = $allocation_status;
            $item->save();

// بروز رسانی وضعیت کارت تولید
            if ($item->production->waiting_status_id == "7001" . "001") {
                $item->production->waiting_status_id = "7001" . "002";
                $item->production->save();
                event(new ProductionCardLogEvent($item->production, "", $user_id));
            }


        }

        // آخرین وضعیت  قبل از تخصیص ماشین
        $machineLog = MachineLog::create();

        $machineLog->machine_event_type_id = 90; // وضعیت قبل از تخصیص ماشین
        event(new MachineLogEvent($machine, $machineLog, "", false, null, $user_id));

        // اگر وضعیت ماشین نداشتن سفارش یا درحال بافت باشد به درحال بافت پارچه یایانی تغییر می کند.
        if ($machine->production_status_id == 7003016 || $machine->production_status_id == 7003017) {
            // بروزرسانی وضعیت تولید ماشین
            $machine->setStatus(
                null,
                null,
                7003042,// در انتظار پایان بافت (کارت تولید جاری)
                null,
                "Fabric_Raw"
            );
        }

        // لاگ تخصیص جدید ماشین
        $machineLog = MachineLog::create();
        $machineLog->machine_event_type_id = 92;
        $machineLog->allocation_id = $allocation->id;
        event(new MachineLogEvent($machine, $machineLog, "", false, null, $user_id));

        Allocation::updatePriorityNumber($machine);

        Production::sendSmsAfterAllocation($production, $machine);


        return [
            "result" => true,
            "allocation" => $allocation,
            "production" => $production
        ];


    }

    public
    function checkPermission(
        Production $production
    )
    {

//         بررسی دسترسی در ماژول
        $result = DashboardController::checkPermissionConditions($production, null);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

//    public
//    function sendSms(
//        $production, $machine
//    ) {
//        // ارسال پیامک تخصیص کارت تولید به پست های سازمانی
//        $post_id = Setting::getStringValue( "send_sms_in_create_allocation_machine_to_post_id1" );
//
//        $post = Post::find( $post_id );
//        if ( ! $post ) {
//            return;
//        }
//        $users       = PostUser::getCurrentUserByShiftWorkAndLeaveOvertimeByPostId( "worker", $post_id );
//        $smsTemplate = "allertincreateallocationproductioncardtomachine";
//        $token       = $production->serial();
//        $token2      = "";
//        $token3      = "";
//        $token10     = $post->caption;
//        $token20     = $machine->caption;
//
//        foreach ( $users as $worker ) {
//            Notification::send( "00" . "98" . $worker->mobile,
//                new SMSNotification( $smsTemplate, $token, $token2, $token3, $token10, $token20 ) );
//
//        }
//    }


    public
    static function UpdateCurrentMachineInput($allocation, $material_info, $allocation_amount, $production)
    {
//$production کارت اولین آیتم تخصیص می باشد، چون وقتی دو کارت متفاوت داشته باشیم، نمی توانیم، مقدار ستون ها را بروز رکنیم و به مشکل بر میخوریم.

        // این مورد برای حالتی که مقدار ماده اولیه جایگزین برای کالا انتخاب می شود و دو کارت متفاوت با هم در حال تخصیص می باشد، درست نیست، چون که مقدار ماده اولیه را برای کارت دوم جابجا نمی کند
        // برای حل این مشکل باید چک کنیم اگر دو کارت با هم تخصیص داده می شوند، مقدار ماده اولیه هم به نسبت کارت اول به کارت دوم ویرایش شود و یا اینکه مقدار ماده کارت دوم هم محاسبه شود.
        // مشکل بعدی این است که مقدار ماده اولیه ای که بررسی می کند برای مقدار کارت اول است و کارت دوم را چک نمی کند.

        // بروزرسانی کالاها در گراف جریان مواد
        CurrentMachineMaterialFlow::where([
            "allocation_id" => $allocation->id,
            "input_band_id" => $material_info["input_band_id"],
            "input_line_code" => $material_info["input_line_code"]
        ])->update(["material_id" => $material_info["material_id"]]);

        // بروز رسانی مقدار مواد اولیه مصرفی
        CurrentMachineInput::where([
            "allocation_id" => $allocation->id,
            "input_band_id" => $material_info["input_band_id"],
            "input_line_code" => $material_info["input_line_code"]
        ])->

        where("production_id", $production->id)->

        update([
            "material_id" => $material_info["material_id"],
            "amount" => $material_info["amount"],
            "percent_of_use" => $material_info["percent_of_use"],
            "number" => $material_info["number"],
            "amount_required" =>
                $allocation_amount *
                CurrentMachineInput::getConsumedAmount(
                    $material_info["amount"],
                    $material_info["number"],
                    $material_info["percent_of_use"]
                ),
            "priority_number" => $material_info["priority_number"]
        ]);

    }

    public
    static function getFirstIndexAfter($array, $index)
    {

        for ($i = $index; $i < 1000; $i++) {
            if (!isset($array[$i])) {
                continue;
            }

            return $i;
        }

        return -1;
    }

    public
    static function getNumberOfDoff($all_allocation_amount, $production, $machine, $number_of_band_selected)
    {
        $out_put = MachineTypeOutputBand::
        where("machine_type_id", $machine->machine_type_id)->
        where("active_status_id", 1200)->
        first();

        if (!$out_put) {
            return [
                "result" => false,
                "error" => " در بخش خروجی های ماشین، برای گروه ماشین " . $machine->machine_type->caption . " الگوریتم داف مشخص نشده است، لطفا الگوریتم داف در خروجی های پارچه خام را برای گروه ماشین مشخص نمایید."
            ];
        }
        $key = "NumberOfDoffAlgorithm" . $out_put->doff_algorithm_id;

        return self::$key($all_allocation_amount, $production, $machine, $number_of_band_selected);
    }

    public
    static function NumberOfDoffAlgorithm601($all_allocation_amount, $production, $machine, $number_of_band_selected)
    {

        //         حداکثر مقدار جهت داف (کیلوگرم)
        $machine_max_doffs = MachinePropertyValue::
        where(["machine_type_id" => $machine->machine_type->id, "machine_property_id" => 5])->
        first();
        if (!isset($machine_max_doffs) || $machine_max_doffs->value <= 0) {
            return ["result" => false, "error" => " حداکثر مقدار جهت داف (کیلوگرم) ثبت نشده است"];
        }
        $machine_max_doffs = $machine_max_doffs->value;

        /* شاخص حجی */
        $property_value = GoodsKindPropertyValue::
        where("product_id", $production->product_id)->
        where("goods_kind_property_id", 220411)->
        first();

        if (!$property_value) {
            return ["result" => false, "error" => "شاخص حجمی برای کالای " . $production->product->caption . " تعریف نشده است."];
        }

        /*
                 * وزن کالا
                 */
        $weight = $all_allocation_amount // متراژ کل پارچه
            * $production->product->weight * $property_value->value;
        /**
         * محاسبه تعداد داف و متغیر های alfa, m_alfa, beta
         */
        $n = 1;
        while ($weight / $n > $machine_max_doffs) {
            $n++;
        }


        $result_check_doff = Product::CheckFrameForDoffs($production->product, $all_allocation_amount / $number_of_band_selected, $n, $production->normal_amount);

        if (!$result_check_doff["result"]) {
            return $result_check_doff;
        }


        // اگر خروچی الگوریتم داف نال است و ما برند داریم، یک لیست خودمان ایجاد می کنیم.
        if ($result_check_doff["doff_amount_list"] == null && $production->normal_amount) {
            $doff_amount_list = [];
            for ($k = 0; $k < $n; $k++) {
                $doff_amount_list[] = round($all_allocation_amount / $n);
            }
            $result_check_doff["doff_amount_list"] = $doff_amount_list;
        }

        $result_check_doff["number_of_doff"] = $n;
        return $result_check_doff;

    }

    public static function NumberOfDoffAlgorithm602($all_allocation_amount, $production, $machine, $number_of_band_selected)
    {
        $machine_max_doffs = MachinePropertyValue::
        where(["machine_type_id" => $machine->machine_type->id, "machine_property_id" => 5])->
        first();
        if (!isset($machine_max_doffs) || $machine_max_doffs->value <= 0) {
            return ["result" => false, "error" => " حداکثر مقدار جهت داف (کیلوگرم) ثبت نشده است"];
        }
        $machine_max_doffs = $machine_max_doffs->value;

        /* شاخص حجی */
        $property_value = GoodsKindPropertyValue::
        where("product_id", $production->product_id)->
        where("goods_kind_property_id", 220411)->
        first();
        if (!$property_value) {
            return ["result" => false, "error" => "شاخص حجمی برای کالای " . $production->product->caption . " تعریف نشده است."];
        }
        //مقدار لوگو برای هر پارچه
        $brand_amount = $production->normal_amount;
        //اگر برند نباشه
        if (!$brand_amount || $brand_amount <= 0) {
            return self::NumberOfDoffAlgorithm601($all_allocation_amount, $production, $machine, $number_of_band_selected);
        }
        //وزن پارچه
        //تبدیل مقدار پارچه به کیلوگرم

        $meter_all = $all_allocation_amount / $number_of_band_selected;
        // تعداد لوگو مورد نیاز
        $number_of_brands = ceil($meter_all / $brand_amount);
        //مقدار هر لوگو پس از توزیع
        $brand_meter = $meter_all / $number_of_brands;

        //محاسبه تعداد قاب ها
        $has_frame = !empty($production->product->frame_ratio_unit2) && $production->product->sub_unit2_id == 1400;

        $doff_frame_list = null;
        if ($has_frame) {
            $frame_ratio = $production->product->frame_ratio_unit2;
            $total_frames = floor($meter_all / $frame_ratio);
            $frames_per_brand = floor($total_frames / $number_of_brands);
            // اگر قاب دارد باید مقدار برند مضربی از قاب باشد.
            $brand_meter = floor($brand_meter / $frame_ratio) * $frame_ratio;

            $doff_frame_list = [];
        } else {
            $doff_frame_list = null;

        }
        //تشکیل داف با حداکثر وزن
        $doffs = [];
        $remaining_brands = $number_of_brands;
        while ($remaining_brands > 0) {
            $current_doff_brands = 0;
            $current_doff_weight = 0;
            while ($current_doff_brands < $remaining_brands &&
                $current_doff_weight + $brand_meter * $number_of_band_selected <= $machine_max_doffs / $production->product->weight) {
                $current_doff_brands++;
                $current_doff_weight += $brand_meter * $number_of_band_selected; //
            }

            if ($current_doff_brands == 0) {
                return [
                    "result" => false,
                    "error" => "با مقدار فعلی غلطک امکان محاسبه داف وجود ندارد ، لطفا مقادیر را بررسی کنید"
                ];
            }

            $current_doff_weight = $current_doff_weight / $number_of_band_selected; // وزن دو باند است که باید یک باند باشد.

            $doff = [
                'weight' => round($current_doff_weight, 6),
                'brands' => $current_doff_brands,
                // 'brand_weight' => $brand_meter,
            ];

            if ($has_frame) {
                $doff['frames_per_brand'] = $frames_per_brand;
            }
            $doffs[] = $doff;
            $remaining_brands -= $current_doff_brands;
        }
        // چک کردن اینکه مقدار کل داف ها با مقدار کل برابر باشد.
        // اگر برابر نبود مقدار آخرین داف را بروز می کنیم.
        $all_allocation_amount_check = 0;
        foreach ($doffs as $doff) {
            $all_allocation_amount_check += $doff["weight"];

            // اگر قاب است، مقدار هر داف باید مضربی از قاب باشد.
//            if ($has_frame) {
//                if( floor( $doff["weight"] / $frame_ratio) * $frame_ratio != $doff["weight"]+0 ){
//                    return [
//                        "result" => false,
//                        "error" => "با توجه به اینکه یکی از واحد های کالا قاب می باشد، و تعداد باندهای در حال تخصیص $number_of_band_selected عدد می باشد، لازم است تا تعداد قاب ها جهت تخصیص مضربی از $number_of_band_selected باشد ".
//                            "<br/> تعداد ".floor($all_allocation_amount/ $frame_ratio)." قاب قابلیت تخصیص به ماشین را ندارد."
//                    ];
//
//                }
        }

        $remaining_in_last_doff = round($all_allocation_amount / $number_of_band_selected - $all_allocation_amount_check, 6);
        if ($remaining_in_last_doff != 0) {
            $doffs[count($doffs) - 1]["weight"] += $remaining_in_last_doff;
            $doffs[count($doffs) - 1]["weight"] = round($doffs[count($doffs) - 1]["weight"], 6);

            if ($has_frame) {

                $frame = floor(round($doffs[count($doffs) - 1]["weight"] / $frame_ratio));
                if (round($frame * $frame_ratio, 3) != round($doffs[count($doffs) - 1]["weight"] + 0,3)) {

                    $frame= round( $all_allocation_amount / $frame_ratio , 3);
                    $min_frame1 = $frame - 1;
                    $min_frame2 = $frame - 2;
                    $min_frame3 = $frame - 3;
                    $min1 = $min_frame1 * $frame_ratio;
                    $min2 = $min_frame2 * $frame_ratio;
                    $min3 = $min_frame3 * $frame_ratio;
                    $unit_caption = $production->product->unit->caption;
                    return [
                        "result" => false,
                        "error" => "با توجه به اینکه یکی از واحد های کالا قاب می باشد، و تعداد باندهای در حال تخصیص $number_of_band_selected عدد می باشد، لازم است تا تعداد قاب ها جهت تخصیص مضربی از $number_of_band_selected باشد " .
                            "<br/> تعداد " . $frame . " قاب قابلیت تخصیص به ماشین را ندارد." .
                            "<br/>" . " مقدار  پیشنهادی:" .
                            ($min1 > 0 && ((int)$min_frame1/ $number_of_band_selected) * $number_of_band_selected == $min_frame1 ?( " $min1 $unit_caption ($min_frame1 قاب) " . ","." - مقدار هر باند: ".round($min1 / $number_of_band_selected,4)." ".$unit_caption ): "") .

                            ($min2 > 0 && ((int)$min_frame2/ $number_of_band_selected) * $number_of_band_selected == $min_frame2 ?( " $min2 $unit_caption ($min_frame2 قاب) " . ","." - مقدار هر باند: ".round($min2 / $number_of_band_selected,4)." ".$unit_caption ): "").
                            ($min3 > 0 && ((int)$min_frame3/ $number_of_band_selected) * $number_of_band_selected == $min_frame3 ?( " $min3 $unit_caption ($min_frame3 قاب) " . ","." - مقدار هر باند: ".round($min3 / $number_of_band_selected,4)." ".$unit_caption ): "")
//.(round( $doffs[count($doffs) - 1]["weight"] / $frame_ratio) * $frame_ratio )."---".$doffs[count($doffs) - 1]["weight"]."***"
                    ];
                }

            }
        }
        $result = [
            "result" => true,
            "doff_amount_list" => array_map(fn($doff) => $doff['weight'], $doffs),
        ];
        if ($has_frame) {
            $result["doff_frame_list"] = array_map(fn($doff) => round($doff['weight'] / $frame_ratio, 6), $doffs);
        } else {
            $result["doff_frame_list"] = null;
        }
        $result["brand_info"] = AllocationBrand::GetBrandForItem($brand_amount, array_map(fn($doff) => $doff['weight'], $doffs), $production->product);
        $result["number_of_doff"] = count($doffs);
        return $result;
    }

    function create_new_channel(Machine $machine, Production $production, $allocation_amount)
    {

        // از طریق کنترلر جنرال یک کانال جدید ایجاد می کنیم.
        $GPCC = new GeneralProductionChannelController();
        $GPCC->route_path = $this->controller_info["route"];
        $GPCC->dashboard_route = $this->dashboard_route;

        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }

        return $GPCC->create_new_channel($machine, $production, $allocation_amount);
    }

    public
    function store_new_channel(Request $request, Machine $machine, Production $production)
    {
        // از طریق کنترلر جنرال یک کانال جدید ایجاد می کنیم.
        $GPCC = new GeneralProductionChannelController();
        $GPCC->route_path = $this->controller_info["route"];
        $GPCC->dashboard_route = $this->dashboard_route;

        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }

        // انتخاب اولین مسیر محصول، حتما هم وجود دارد
        $line_product_station = LineProductStation::where([
            "product_id" => $production->product_id,
            "machine_type_id" => $machine->machine_type_id
        ])->first();

        return $GPCC->store_new_channel($request, $machine, $production,
            $line_product_station->station_operation->station_operation_category_id);
    }

    // تابع تخصیص اتوماتیک، این تابع در اسکریپت 1021 در زمانی که یک عیب ایجاد می شود، فراخوانی می شود.
    public
    static function AutoAllocation(Machine $machine, $production, $allocation_amount, $user_id, $declared_inventory, $priority_number)
    {
        $link_script = new Script1021Controller();
        // گام 1
        $result_select_band_auto = self::SelectBandAuto($machine, $production, 1, $allocation_amount);

        if (!$result_select_band_auto["result"]) {
            return [
                "result" => false,
                "error" => $result_select_band_auto["error"]
            ];

        }

        $allocation_amount_list = $result_select_band_auto["allocation_amount_list"];
        $band_count_allocation = $result_select_band_auto["band_count_allocation"];

        // انتخاب اولین مسیر محصول، حتما هم وجود دارد
        $line_product_station = LineProductStation::where([
            "product_id" => $production->product_id,
            "machine_type_id" => $machine->machine_type_id
        ])->first();

        // گام 2
        //چک کردن کانال تولید جاری ماشین و کانال تولید کارت تولید
        $result = ProductionChannel::CheckProductionChannelForAllocation(
            $machine, $production, $allocation_amount,
            $line_product_station->station_operation_id,
            $line_product_station->station_operation->station_operation_category_id
        );

        if (!$result["result"]) {
            if (isset($result["error"])) {
                return [
                    "result" => false,
                    "error" => $result["error"]
                ];

            } else {
                $production_channel_type = $result["production_channel_type"];
                $station_operation_category_id = $result["station_operation_category_id"];

                // به صورت اتوماتیک یک کانال ایجاد می کنیم.
                $result_production_channel = GeneralProductionChannelController:: create_production_channel($machine, $production_channel_type, $production_channel_type->min_capacity, $production_channel_type->max_capacity, true, $station_operation_category_id);
                if (!$result_production_channel["result"]) {
                    return [
                        "result" => false,
                        "error" => $result_production_channel["error"]
                    ];

                }
            }
        }

        // گام 3
        $resultSelectBandSubmitAuto = self::SelectBandSubmitAuto($machine, $production, $band_count_allocation, $allocation_amount_list, $user_id);

        if (!$resultSelectBandSubmitAuto["result"]) {
            return [
                "result" => false,
                "error" => $resultSelectBandSubmitAuto["error"]
            ];
        }

        // گام 4

        $result_select_input_lint = self::SelectInputLineAuto($machine, false, 0, null, null, $declared_inventory);

        if (!$result_select_input_lint["result"]) {

            if (isset($result_select_input_lint["warning"])) {

                // اگر دقیقا یک حالت انتخاب داریم آن را انتخاب می کنیم در غیر این صورت خطا میدهیم.
                if (count($result_select_input_lint["permutation_list"]["permutation_list"]) == 1) {
                    // در یک حالت انتخاب فقط یک تخصیص داریم
                    if (count($result_select_input_lint["permutation_list"]["permutation_list"][1]) == 1) {
                        // یک بار دیگر با اولیوت 1 اجرا می کنیم.
                        $result_select_input_lint = self::SelectInputLineAuto($machine, false, 1, 1, $result_select_input_lint["permutation_list"], $declared_inventory);

                    }

                }
                // نتوانستیم با یک حالت تخصیص دهیم.
                if (!$result_select_input_lint["result"]) {
                    return [
                        "result" => false,
                        "error" => "با توجه به اینکه تعداد حالت های تخصیص بیش از 1 مورد است، امکان تصمیم گیری برای دستیار دیجیتال وجود ندارد."
                    ];
                }
            } else {

                return [
                    "result" => false,
                    "error" => $result_select_input_lint["error"]
                ];
            }
        }


        // گام 5
        //اگر اولیت 1 است، بعد از اولین تخصیص رزور می کند.
        if ($priority_number == 1) {
            if ($production->production_type_id == 2) {
                //چون نمونه گیری است و کارت جاری (دارد/ندارد) از نمونه گیری است، آخرین تخصیص نمونه گیری را پیدا می کنیم که بعد از آن تخصیص بدهد.
                $reserve_after_current_allocation = $machine->ReserveAllocation(false, $production_type_id = 2, $orderByPriority = "Desc")->first();
            } else {
                $reserve_after_current_allocation = $machine->getCurrentAllocation();
            }
        } else {
            $reserve_after_current_allocation = $machine->ReserveAllocation(false, false, $orderByPriority = "Asc", null, $priority_number)->first();
        }

        $result_confirm_auto = self::ConfirmSubmitAuto($machine, $user_id, $reserve_after_current_allocation->id ?? null);

        if (!$result_confirm_auto["result"]) {
            return [
                "result" => false,
                "error" => $result_confirm_auto["error"]
            ];

        }

        return [
            "result" => true
        ];
    }
}


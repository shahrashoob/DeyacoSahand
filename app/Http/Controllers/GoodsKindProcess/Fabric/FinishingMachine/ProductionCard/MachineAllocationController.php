<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\ProductionCard;


use App\Events\Machine\MachineAllocationEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine\StartToStartMachineController;
use App\Http\Controllers\GoodsKindProcess\General\ProductionCard\GeneralMachineAllocationController;
use App\Http\Controllers\GoodsKindProcess\General\ProductionCard\GeneralProductionChannelController;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypePropertyValue;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeInputBand;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\MaterialFlow;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\LineProduct\StationOperation;
use App\Models\Production\Production;
use App\Models\Production\ProductionChannelType;
use App\Models\Production\ProductionFormItem;
use App\Models\Production\ProductionPackingType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

/**
 *  ماژول عمومی تکمیل Finishing Machine
 */
class MachineAllocationController extends Controller
{
    //
    public static $info = [
        "route" => "fabric.finishing_machine.machine_allocation.",
        "enable_status" => ["001"],
        "next_status" => [],
        "button" => ["caption" => "تخصیص ماشین ", "class" => "btn-success"],
        "view_path" => "goods_kind_process.fabric.finishing_machine.production_card.machine_allocation."
    ];

    public $dashboard_route = "fabric.machine_allocation.";
    public $controller_info;
    public $route_path;
    public $view_path;

    public function __construct()
    {
        $perfix_status_code = DashboardController::$perfix_status_code;
        $this->view_path = self::$info["view_path"];
        $this->route_path = self::$info["route"];
        $this->controller_info = MachineAllocationController::$info;
    }

    public function select_band(Request $request, $machine_id, MachineType $machine_type, Production $production, $is_first_production, LineProductStation $line_product_station, $current_machine_allocation_id = null)
    {
        if (in_array($production->status_id, [7301004])) {
            return back()->withErrors("با توجه به اینکه وضعیت کارت تولید " . $production->status->caption . " می باشد، امکان تخصیص کارت وجود ندارد.");
        }

        $key = "machine_type_" . $machine_type->id;
        $machine = Machine::find($request->$key ?? $machine_id);
        $priority_number = 1;
        session(["permutation_list" => null]); // انتخاب حالت ها را حذف می کنیم.

        $result_select_band_auto = self::SelectBandAuto($machine, $production, $is_first_production, $line_product_station, $current_machine_allocation_id);

        if (!$result_select_band_auto["result"]) {
            return redirect()->
            route($this->dashboard_route . "index", $production)->
            withErrors($result_select_band_auto["error"]);

        } else {
            $reserve_after_allocation_option = $result_select_band_auto["reserve_after_allocation_option"];
            $allocation_amount_list = $result_select_band_auto["allocation_amount_list"];
            $open_band_list = $result_select_band_auto["open_band_list"];
            $band_count_allocation = $result_select_band_auto["band_count_allocation"];
            $production_allocation_togethers = $result_select_band_auto["production_allocation_togethers"];
            $first_line_product_station = $result_select_band_auto["first_line_product_station"];


            return \view($this->view_path . "select_band",
                compact("allocation_amount_list", "band_count_allocation",
                    "machine", "production", "allocation_amount_list", 'open_band_list'
                    , "reserve_after_allocation_option", "production_allocation_togethers"
                    , "first_line_product_station", "current_machine_allocation_id"));

        }


    }

    public static function SelectBandAuto(Machine $machine, Production $master_production, $is_first_production, $current_line_product_station, $current_machine_allocation_id)
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

        // حذف کارت تولید های در انتظار تایید
        if ($is_first_production) {
            $allocation_delete_ids = MachineAllocation::where([
                "machine_id" => $machine->id,
                "status_id" => 5310005
            ])->pluck("allocation_id")->toArray();
            Allocation\AllocationData::whereIn("allocation_id", $allocation_delete_ids)->delete();
            MachineAllocation::where(["machine_id" => $machine->id, "status_id" => 5310005])->delete();

        }

        $line_product_station_list =
            $master_production->product->
            line_product_station()->
            with("production_channel_type", "station_operation")->
            get();

        foreach ($line_product_station_list as $line_product_station) {
            // چک کردن اینکه همه مسیر محصول ها کانال تولید داشته باشند.
            if (!$line_product_station->production_channel_type_id) {
                return [
                    "result" => false,
                    "error" => "در تعریف کالا، همه مسیر محصول ها باید دارای کانال تولید معتبر باشد، <br/>" . " لطفا اطلاعات کالا را ویرایش یا تکمیل نمایید."
                ];
            }

        }


        // لیست شناسه کانال تولید ها در هر دسته کانال
        // به دست آوردن کانال های مجاز بعدی
//        $production_channel_next_list = ProductionChannel::
//        join("production_channel_types", "production_channel_type_id", "production_channel_types.id")->
//        join(
//            "production_channel_next_ones",
//            "production_channel_next_ones.production_channel_type_id",
//            "production_channels.production_channel_type_id"
//        )->
//        whereIn("production_channels.id", $production_channel_ids_in_category)->
//        whereIn("machine_type_id", $machine_type_ids)->
//        select("production_channel_next_ones.*", "production_channel_category_id")->
//        orderBy("priority_number")->
//        get();
//        $production_channel_next_group = [];
//        foreach ($production_channel_next_list as $production_channel_next) {
//
//            $production_channel_next_group
//            [$production_channel_next->machine_type_id]
//            [$production_channel_next->production_channel_category_id]
//            [$production_channel_next->production_channel_type_id]
//            [$production_channel_next->priority_number]
//                =
//                $production_channel_next->next_production_channel_type_id;
//        }
//
//        return $production_channel_next_group;
        // چک کردن اینکه گروه ماشین در خط محصول وجود داشته باشد.

        if (!$master_production->product->line_product_station()->where("machine_type_id", $machine_type->id)->exists()) {
            return [
                "result" => false,
                "error" => "ماشین " . $machine->caption . " در مسیر - محصول های تعریف شده برای محصول (" . $master_production->product->caption . ") وجود ندارد."
            ];
        }

        // اولین مسیر محصول
        $first_line_product_station = $current_line_product_station;

//            $master_production->product->
//        line_product_station()->
//        where([
//            "priority_number" => $priority_number,
//            "status_id" => 1200
//        ])->
//        orderBy("priority_number")->
//        first();

        // به دست آوردن اینکه متغیر نوع الگوریتم تشخیص کارت های مشابه


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
        $result_fault = Allocation::CheckAllocationFault($machine, $master_production);
        if (!$result_fault["result"]) {
            return [
                "result" => false,
                "error" => $result_fault["error"]
            ];
        }

        $open_band_list = [1];
        $band_count_allocation = 1;
        $sum_allocation_amount = $master_production->get_allocation_amount();

        if ($master_production->number - $sum_allocation_amount <= 0 && !$current_line_product_station) {
            return [
                "result" => false,
                "error" => "با توجه به مقدار کارت تولید و تخصیصی ها انجام شده، امکان تخصیص جدید برای کارت تولید وجود ندارد."
            ];

        }

        // به دست آورن لیست کارت های تولید در اننتظار تخصیص و تخصیص مجدد
        // که از اولویت n به بعد با کالای جاری اشتراک دارند.


        // آیا ماژول فرم تولید در ماشین فعال است.
        $value_207 = MachineModuleTypePropertyValue::getValue("73030011207", $machine->machine_type_id);

        $production_list_result = self::GetProductionListByAlgorithm($value_207, $master_production);
        if (!$production_list_result["result"]) {
            return $production_list_result;
        }
        $production_list = $production_list_result["production_list"];

        $production_allocation_togethers = [];

        foreach ($production_list as $production_item) {

            $line_product_station_for_allocation = $production_item->product->
            line_product_station()->
            where("status_id", 1200)->
            orderBy("priority_number")->
            first();
//            if($production_item->id ==392){
//                // return $production_allocation_togethers;
//                  return $production_item;
//              //  return  $allocation_amount_new = $production_item->number - $production_item->get_allocation_amount();;
//                return $line_product_station_for_allocation->station_sub_operation_id." --".$first_line_product_station->station_sub_operation_id;

//             return  $production_item; $line_product_station_for_allocation = $production_item->product->
//                line_product_station()->orderBy("priority_number")->get();
//             return   $first_line_product_station->id." !=.". $line_product_station_for_allocation->id;
//                return $line_product_station_for_allocation;
//            }
            // اگر عملیات های فرعی متفاوت است از آن رد می شویم.
            if (!$line_product_station_for_allocation || $first_line_product_station->station_sub_operation_id != $line_product_station_for_allocation->station_sub_operation_id) {
//echo $first_line_product_station->id." !=". $line_product_station_for_allocation->id;
                continue;
            }
            // اگر مقداری برای تخصیص وجود ندارد، رد می شویم.

            // اگر کالا بچی بود و حالا باید با توجه به واحد بچ مواد اولیه مقدار کالا را نمایش دهیم.
            //و واحد بچ بسته بندی است مثل رز رنگ
            if ($first_line_product_station->batch && $first_line_product_station->material_unit_type_id_dependent_to_batch == 4) {
                $allocation_amount_new = $production_item->number_of_packing_form - $production_item->get_allocation_amount(false, 1, null, true, $first_line_product_station->material_unit_type_id_dependent_to_batch);

            } else { // کالای پیوسته
                $allocation_amount_new = $production_item->number - $production_item->get_allocation_amount();


            }

            if ($allocation_amount_new <= 0) {

                continue;
            }

            $production_allocation_togethers[] = [
                "machine_allocation" => null,
                "production" => $production_item,
                "allocation_amount" => $allocation_amount_new,
                "line_product_station_id" => $line_product_station_for_allocation->id,
                "production_channel_type_id" => $line_product_station_for_allocation->production_channel_type_id,
                "station_operation_type_id" => $line_product_station_for_allocation->station_operation->station_operation_type_id,
                "station_operation_category_id" => $line_product_station_for_allocation->station_operation->station_operation_category_id,
            ];
            // echo $production_item->id."<br/>";

        }

        // مرحله 2: لیست تخصیص های مجدد، که اولین عملیات فرعی آنها با اولین عملیات کارت جاری یکی است.
        $machine_allocation_list = MachineAllocation::
        whereNotNull("parent_allocation_id")->
        where(["status_id" => 5310050])->
        with("line_product_station")->
        get();

        // تخصیی های مجدد هم باید طبق الگوریتم نماییش داده شوند.
        $re_allocation_result = self::GetReAllocationByAlgorithm($value_207, $master_production, $machine_allocation_list);
        if (!$re_allocation_result["result"]) {
            return $re_allocation_result;
        }
        $machine_allocation_list = $re_allocation_result["machine_allocation_list"];
        foreach ($machine_allocation_list as $machine_allocation_item) {
            // آخرین عملیاتی که بر روی تخصیص انجام شده است.
            $next_line_product_station = $machine_allocation_item->line_product_station ?? null;
            if (!$next_line_product_station) {
                continue;
            }
            // مسیر محصول فعال نمی باشد.
            if ($next_line_product_station->status_id != 1200) {
                continue;
            }
            // اگر عملیات فرعی بعدی و عملیات فرعی الان با هم متفاوت هستند، ادامه بده و لازم نیست چک کنید.
            if ($next_line_product_station->station_sub_operation_id != $first_line_product_station->station_sub_operation_id) {
                continue;
            }


            if ($first_line_product_station->batch) {
                switch ($first_line_product_station->material_unit_type_id_dependent_to_batch) {
                    case 1:
                        $allocation_amount_new = $machine_allocation_item->allocation_amount;
                        break;
                    case 2:
                        $allocation_amount_new = $machine_allocation_item->allocation_sub_amount;
                        break;
                    case 3:
                        1 / 0;
                        break;
                    case 4:
                        $allocation_amount_new = $machine_allocation_item->number_of_packing_form;
                        break;
                }

            } else { // کالای پیوسته
                $allocation_amount_new = $machine_allocation_item->allocation_amount;


            }


            $production_allocation_togethers[] = [
                "machine_allocation" => $machine_allocation_item,
                "production" => $machine_allocation_item->production,
                "allocation_amount" => $allocation_amount_new,
                "line_product_station_id" => $next_line_product_station->id,
                "production_channel_type_id" => $next_line_product_station->production_channel_type_id,
                "station_operation_type_id" => $next_line_product_station->station_operation->station_operation_type_id,
                "station_operation_category_id" => $next_line_product_station->station_operation->station_operation_category_id,

            ];

        }

        //مقدار تخصیص را به تعداد باندهای مشابه تقسیم می کنیم
        $allocation_amount_list[1] = round(($master_production->number - $sum_allocation_amount) / $band_count_allocation, 2);
        $allocation_amount_list[2] = "";
        $allocation_amount_list[3] = "";
        $allocation_amount_list[4] = round(($master_production->number_of_packing_form - $sum_allocation_amount) / $band_count_allocation, 2);;


        // گرفتن لیست کارت های تولید که کارت تولید نمونه گیری می تواند بین آنها رزور شود.
        $reserve_after_allocation_option = Production::getReserveAfterAllocationOption($master_production, $machine);

        if ($reserve_after_allocation_option["result"] == false) {
            return $reserve_after_allocation_option;
        }

        $reserve_after_allocation_option = $reserve_after_allocation_option["list"];


        // ذخیره کردن شناسه کارت ها و تخصیص های مجدد پیشنهاد شده، برای اینکه دوباره آنها را محاسبه نکنیم.
        $production_allocation_togethers_session = [];
        foreach ($production_allocation_togethers as $item) {
            $production_allocation_togethers_session[] = [
                "machine_allocation_id" => $item["machine_allocation"]->id ?? null,
                "parent_allocation_id" => $item["machine_allocation"]->parent_allocation_id ?? null,
                "production_id" => $item["production"]->id ?? Null,
                "allocation_amount" => $item["allocation_amount"],
                "line_product_station_id" => $item["line_product_station_id"],
                "production_channel_type_id" => $item["production_channel_type_id"],
                "station_operation_type_id" => $item["station_operation_type_id"],
                "station_operation_category_id" => $item["station_operation_category_id"],
            ];

        }

        session(["production_allocation_togethers_session_" . $master_production->id => $production_allocation_togethers_session]);


        return [
            "result" => true,
            "reserve_after_allocation_option" => $reserve_after_allocation_option,
            "allocation_amount_list" => $allocation_amount_list,
            "machine" => $machine,
            "production" => $master_production,
            "band_count_allocation" => $band_count_allocation,
            "open_band_list" => $open_band_list,
            "production_allocation_togethers" => $production_allocation_togethers,
            "first_line_product_station" => $first_line_product_station
        ];
    }

    public function submit(Request $request, Machine $machine, Production $master_production)
    {


        $result = $this->checkPermission($master_production);
        if ($result != "") {
            return $result;
        }
        $first_line_product_station_id = $request->first_line_product_station_id;
        $main_allocation_amount = $request->main_allocation_amount;
        $together_checkbox = $request->together_checkbox;
        $together_allocation_amount = $request->together_allocation_amount;
        $current_machine_allocation_id = $request->current_machine_allocation_id;
        $reserve_after_allocation_id = session("reserve_after_allocation_id");
        $production_allocation_togethers_session = session("production_allocation_togethers_session_" . $master_production->id);

        $result = self::PostSubmit($machine, $master_production,
            $first_line_product_station_id,
            $main_allocation_amount,
            $together_checkbox,
            $together_allocation_amount,
            $current_machine_allocation_id,
            $reserve_after_allocation_id,
            $production_allocation_togethers_session
        );

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $allocation = $result["allocation"];

        return redirect()->route($this->route_path . "confirm", [$machine, $master_production, $allocation]);

    }

    public static function PostSubmit(
        Machine $machine, Production $master_production,
                $first_line_product_station_id,
                $main_allocation_amount,
                $together_checkbox,
                $together_allocation_amount,
                $current_machine_allocation_id,

                $reserve_after_allocation_id,
                $production_allocation_togethers_session
    )
    {
        if (in_array($master_production->status_id, [520])) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه وضعیت کارت تولید " . $master_production->status->caption . " می باشد، امکان تخصیص کارت وجود ندارد."
            ];
        }
        /**
         * حدف تخصیص های معلق قبلی
         */
        MachineAllocation::where([
            "machine_id" => $machine->id,
            "status_id" => 5310005
        ])->delete();

        MachineAllocation::where([
            "status_id" => 5310050
        ])->update(["allocation_id" => 0]);

        $first_line_product_station = LineProductStation::find($first_line_product_station_id);
        if (!$first_line_product_station) {
            return [
                "result" => false,
                "error" => "اولین مسیر محصول جهت تخصیص یافت نشد، لطفا با پشتیبانی تماس بگیرید."
            ];

        }

        $allocation_amount = $main_allocation_amount;
        $number_of_doffs_done = 0;
        $max_number_of_doffs = 1;
        $amount_of_each_doffs = $allocation_amount;
        $production_allocation_togethers = [];
//        $together_checkbox = $together_checkbox;
//        $together_allocation_amount = $together_allocation_amount;
//        $current_machine_allocation_id = $current_machine_allocation_id;
        $production_ids = [];
        $production_list = [];
        $allocation_sum = 0;
        $band_code = 1;
        $operation_category_production_channel_type = [];


        // گرفتن مقدار کارت تولید بر اساس نوع تخصیص و بچ
        $master_production_number = $master_production->number;
        switch ($first_line_product_station->material_unit_type_id_dependent_to_batch) {
            case 1: // واحد اصلی
                $master_production_number = $master_production->number;
                break;
            case 2: // واحد فرعی
            case 3:
                1 / 0;
            case 4:
                $master_production_number = $master_production->number_of_packing_form;
                break;
        }
        if (
            $master_production->get_allocation_amount(false, 1, null, true, $first_line_product_station->material_unit_type_id_dependent_to_batch) +
            $allocation_amount >
            $master_production_number
        ) {
            return [
                "result" => false,
                "error" => "مقدار تخصیص برای کارت اصلی بیش از مقدار کارت تولید می باشد."
            ];

        }
//        return $request->all();
        // در صورتی که کارت نمونه گیری باشد، این متغیر در select_band مقدار دهی می شود.


        // به دست آوردن لیست کارت ها و تخصیص های مجدد که انتخاب کرده اند.
        if ($current_machine_allocation_id == 0) {
            $production_allocation_togethers[] = [
                "machine_allocation_id" => null,
                "production_id" => $master_production->id,

                "allocation_amount" => self::GetAllocationByType($first_line_product_station, $main_allocation_amount + 0, "allocation_amount"),
                "allocation_sub_amount" => self::GetAllocationByType($first_line_product_station, $main_allocation_amount + 0, "allocation_sub_amount"),
                "number_of_packing_form" => self::GetAllocationByType($first_line_product_station, $main_allocation_amount + 0, "number_of_packing_form"),

                "line_product_station_id" => $first_line_product_station->id,
                "production_channel_type_id" => $first_line_product_station->production_channel_type_id,
                "station_operation_type_id" => $first_line_product_station->station_operation->station_operation_type_id,
                "station_operation_category_id" => $first_line_product_station->station_operation->station_operation_category_id,
                "parent_allocation_id" => null,
            ];
        }


        $production_ids[] = $master_production->id;
        $allocation_sum += $main_allocation_amount;
        $production_channel_allocation_amount = [];

        foreach ($production_allocation_togethers_session as $key => $item) {
            if (isset($together_checkbox[$key]) && isset($together_allocation_amount[$key])) {

                if ($together_allocation_amount[$key] > $item["allocation_amount"]) {

                    $k = $key + 1;
                    return [
                        "result" => false,
                        "error" => "مقدار تخصیص برای ردیف $k به درستی انتخاب نشده است. "
                        //.$together_allocation_amount[$key] .">". $item["allocation_amount"]
                    ];

                } else {
                    $production_allocation_togethers[] = [
                        "machine_allocation_id" => $item["machine_allocation_id"],
                        "production_id" => $item["production_id"],

                        "allocation_amount" => self::GetAllocationByType($first_line_product_station, $together_allocation_amount[$key] + 0, "allocation_amount"),
                        "allocation_sub_amount" => self::GetAllocationByType($first_line_product_station, $together_allocation_amount[$key] + 0, "allocation_sub_amount"),
                        "number_of_packing_form" => self::GetAllocationByType($first_line_product_station, $together_allocation_amount[$key] + 0, "number_of_packing_form"),

                        "line_product_station_id" => $item["line_product_station_id"],
                        "production_channel_type_id" => $item["production_channel_type_id"],
                        "station_operation_type_id" => $item["station_operation_type_id"],
                        "station_operation_category_id" => $item["station_operation_category_id"],
                        "parent_allocation_id" => $item["parent_allocation_id"],
                        "key" => $key
                    ];
                    $production_ids[] = $item["production_id"];


                    $allocation_sum += $together_allocation_amount[$key];
                }


            }
        }

        if (count($production_allocation_togethers) == 0) {
            return [
                "result" => false,
                "error" => "لطفا حداقل یک تخصیص یا کارت تولید را انتخاب نمایید"
            ];

        }
        $production_list = Production::whereIn("id", $production_ids)->with("product")->
        get()->keyBy("id");

        // بررسی اینکه بچ برای مسیر محصول ها کامل می شود یا خیر و
        // تا قبل از مسیر محصولی که نیاز به تخصیص دارد چک می شود.

        $line_product_station_list = LineProductStation::
        where([
            "product_id" => $first_line_product_station->product_id,
            "product_route_id" => $first_line_product_station->product_route_id,
            "status_id" => 1200
        ])->
        where("priority_number", ">=", $first_line_product_station->priority_number)->
        with("station_operation")->
        orderBy("priority_number")->
        get();
        $production_channel_type_id = $first_line_product_station->production_channel_type_id;

        foreach ($line_product_station_list as $line_product_station) {

            // تا جایی بررسی می کند که نیاز به تخصیص آن بله باشد (به غیز از اولین مسیر محصیول)
            if (
                $line_product_station->id != $first_line_product_station->id
                &&
                $line_product_station->is_need_allocation_at_first

            ) {

                // دیگر نیاز نیست که بررسی کند.
                break;

            }

            // به ازای هر دسته عملیات یک کانال تولید موازی داریم.
            $operation_category_production_channel_type[$line_product_station->station_operation->station_operation_category_id] = [
                "station_operation_id" => $line_product_station->station_operation_id,
                "production_channel_type_id" => $line_product_station->production_channel_type_id,
                "station_operation_category_id" => $line_product_station->station_operation->station_operation_category_id,
            ];

            if ($line_product_station->station_operation->station_operation_type_id == 2) {
                // تولید پیوسته نیاز به بررسی بچ ندارد.
                continue;
            }

            // بررسی بچ
            $result_batch = StationOperation::CheckBath($line_product_station->batch, $line_product_station->batch_error_percentage, $allocation_sum);
            if (!$result_batch["result"]) {
                $unit_caption = $line_product_station->get_unit_type_dependent_to_batch_caption($line_product_station->product);
                return [
                    "result" => false,
                    "error" => (
                        "جمع کل مقدار های انتخاب شده جهت تخصیص " . $allocation_sum . " " . $unit_caption . " می باشد و " .
                        "خط محصول کالا با اولویت " . $line_product_station->priority_number .
                        " در عملیات " . $line_product_station->station_operation->caption .
                        "  نمی توان بچ تولید " . $line_product_station->batch . " " . $unit_caption . "
                        با درصد خطای " . $line_product_station->batch_error_percentage . " را پر کند، 
                            <br/>" .
                        "بنابراین امکان تخصیص با مقدار انتخاب شده برای کارت های تولید وجود ندارد.")
                ];
            }

            // بررسی اینکه کانال تولید های خط های مختلف با هم برابر هستند.
//            if ($production_channel_type_id != $line_product_station->production_channel_type_id) {
//                return back()->withErrors(
//                    "در تعریف مسیر محصول، کانال تولید ردیف های مختلف برای یک گروه ماشین (" .
//                    $machine->machine_type->caption .
//                    ") باید با هم برابر باشید.");
//            }
            $production_channel_type_id = $line_product_station->production_channel_type_id;

        }

        foreach ($operation_category_production_channel_type as $key => $operation_category_production_channel) {

            $production_channel_type_id = $operation_category_production_channel["production_channel_type_id"];
            //چک کردن کانال تولید جاری ماشین و کانال تولید کارت تولید
            $production_channel_type = ProductionChannelType::find($production_channel_type_id);
            if (!$production_channel_type) {
                return back()->withErrors("کانال تولید برای مسیر محصول قابل شناسایی نیست، لطفا با پشتیبانی تماس بگیرید.");
            }
            $result = ProductionChannel::CheckProductionChannelForAllocation(
                $machine, $master_production, $allocation_sum,
                $operation_category_production_channel["station_operation_id"],
                $operation_category_production_channel["station_operation_category_id"],
                $production_channel_type
            );
            if (!$result["result"]) {
                if (isset($result["error"])) {
                    return $result;
                } else {
                    $production_channel_type = $result["production_channel_type"];
                    $station_operation_category_id = $result["station_operation_category_id"];

                    // به صورت اتوماتیک یک کانال ایجاد می کنیم.
                    $result_production_channel = GeneralProductionChannelController:: create_production_channel($machine, $production_channel_type, $production_channel_type->min_capacity, $production_channel_type->max_capacity, $allow_create_channel = true, $station_operation_category_id);
                    if (!$result_production_channel["result"]) {
                        return $result_production_channel;
                    }

                    $operation_category_production_channel_type[$key]["production_channel_type"] = $production_channel_type;

                }
            }

            $operation_category_production_channel_type[$key]["production_channel_type"] = $result["production_channel_type"];

        }


        // به دست آوردن اینکه به ازای هر کانال تولید در دسته های مختلف چقدر باید به ماشین تخصیص دهییم.
        $allocation_amount_for_each_channel_list = []; // مقدار تخصیص به هر کانال تولید
        foreach ($production_allocation_togethers as $production_allocation_together_item) {

            $channel_type_id = $production_allocation_together_item["production_channel_type_id"];
            $category_id = $production_allocation_together_item["station_operation_category_id"];
            if ($category_id == "") {
                return [
                    "result" => false,
                    "error" => "دسته عملیات برای ماشین عملیات های ایستگاه کاری، مشخص نشده است، لطفا با پشتیبانی تماس بگرید."
                ];
            }
            if (!isset($allocation_amount_for_each_channel_list[$channel_type_id][$category_id])) {
                $allocation_amount_for_each_channel_list[$channel_type_id][$category_id] = 0;
            }

            $allocation_amount_for_each_channel_list[$channel_type_id][$category_id] +=
                $production_allocation_together_item["allocation_amount"];

        }


        foreach ($operation_category_production_channel_type as $operation_category_production_channel) {

            $production_channel_type = $operation_category_production_channel["production_channel_type"];
            $station_operation_category_id = $operation_category_production_channel["station_operation_category_id"];

            // به صورت اتوماتیک یک کانال ایجاد می کنیم.
            $result_production_channel = GeneralProductionChannelController::create_production_channel($machine, $production_channel_type, $production_channel_type->min_capacity, $production_channel_type->max_capacity, $allow_create_channel = false, $station_operation_category_id);

        }


// ثبت یک تخصیص با وضعیت معلق;
        foreach ($production_allocation_togethers as $production_allocation_together_item) {

            $allocation_amount_tg =
                $production_allocation_together_item["allocation_amount"];
            $allocation_sub_amount_tg =
                $production_allocation_together_item["allocation_sub_amount"];
            $number_of_packing_form_tg =
                $production_allocation_together_item["number_of_packing_form"];;


            event(new MachineAllocationEvent(
                $production_list[$production_allocation_together_item["production_id"]],
                $machine,
                "Fabric_Raw",
                $band_code,
                $allocation_amount_tg,
                $number_of_doffs_done,
                $max_number_of_doffs,
                $production_allocation_together_item["allocation_amount"],
                null,
                $production_allocation_together_item["line_product_station_id"],
                null,
                $production_allocation_together_item["parent_allocation_id"],
                $production_allocation_together_item["machine_allocation_id"],
                $allocation_sub_amount_tg,
                $number_of_packing_form_tg,
                $first_line_product_station->material_unit_type_id_dependent_to_batch ?? 1

            ));
        }


        // گرفتن تخصیص معلق
        $allocation = Allocation::where([
            "machine_id" => $machine->id,
            "status_id" => 5310005
        ])->first();


        // محاسبه ضریف مصرف کانال تولید برای تخصیص
        $result_consumption_percent = BOM::GetConsumptionOfProductionChannel(null, [$master_production->product_id]);
        if (!$result_consumption_percent["result"]) {
            return $result_consumption_percent;
        }
        $allocation->consumption_percent_of_production_channel = $result_consumption_percent["value"];
        $allocation->save();


        $allocation->save();

        foreach ($allocation_amount_for_each_channel_list as $key_channel_id => $allocation_amount_for_each_channel) {
            foreach ($allocation_amount_for_each_channel as $key_category_id => $allocation_amount) {

                $result_add_allocation = Allocation\MachineAllocationProductionChannel::AddAllocation($machine, $allocation,
                    $key_channel_id,
                    $key_category_id,
                    $allocation_amount);

                if (!$result_add_allocation["result"]) {
                    return $result_add_allocation["error"];
                }
            }

        }


        // به دست آوردن اولویت کارت
        switch ($master_production->production_type_id) {
            case 1:
                $priority_number = $machine->ReserveAllocation()->count() + 1;
                break;
            case 2:
                $after_allocation = Allocation::find($reserve_after_allocation_id);
                if (!$after_allocation) {
                    return [
                        "result" => false,
                        "error" => "تخصیصی که باید بعد از آن کارت نمونه گیری تخصیصی داده شود، یافت نشد."
                    ];

                }
                $priority_number = $after_allocation->priority_number + .5;
                break;
            default:
                return [
                    "result" => false,
                    "error" => "نوع کارت تولید مشخص نشده است"
                ];

        }

        $allocation->priority_number = $priority_number;
        $allocation->save();


        return [
            "result" => true,
            "allocation" => $allocation,
        ];
    }

    public function confirm(Machine $machine, Production $production, Allocation $allocation)
    {
        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }

        $list_product_station_list = self::GetLineProductStationMahcineAllocation($allocation);
        return view($this->view_path . "confirm", compact("production", "machine", "allocation", "list_product_station_list"));
    }

    public function confirm_submit(Request $request, Machine $machine, Production $production, Allocation $allocation)
    {
        //
        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }
        $request_machine_allocation = $request->machine_allocation;


        $result = self::PostConfirmSubmit($allocation, $production, $request_machine_allocation);

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        return redirect()->route("production.machine.index")->with(["success" => "عملیات تخصیص با موفقیت انجام شد."]);
    }

    public static function PostConfirmSubmit(Allocation $allocation, Production $production,
                                                        $request_machine_allocation, $user_id = null)
    {
        $machine_allocation_data = [];
        $list_item_col = ["is_need_start_setup", "is_need_start_of_operation", "is_need_end_of_operation", "is_need_final_setting", "is_need_for_quality_control", "is_need_allocation_at_first"];

        $list_product_station_list = self::GetLineProductStationMahcineAllocation($allocation);
        $is_first_item = true;
        if ($list_product_station_list != []) { // اگر بچ است، آرایه خالی است.
            foreach ($allocation->items as $machine_allocation) {
                foreach ($list_product_station_list[$machine_allocation->id] as $line_product_station) {
                    foreach ($list_item_col as $item_col) {

                        if (!isset($request_machine_allocation[$machine_allocation->id][$line_product_station->id][$item_col])) {
                            $machine_allocation_data[$machine_allocation->id][$line_product_station->id][$item_col] = 0;
                        } else {
                            $machine_allocation_data[$machine_allocation->id][$line_product_station->id][$item_col] = 1;
                        }
                    }
                }
                // برای اولین ردیف تخصیص باید شروع عملیات true باشد.
//            if ($is_first_item) {
//                $is_first_item = false;
//                if ($machine_allocation_data[$machine_allocation->id]["is_need_start_of_operation"] == 0) {
//                    return back()->withErrors(
//                        "شروع عملیات برای اولین آیتم تخصیص باید انتخاب گردد، <br/>
//                            در صورتی که امکان انتخاب عملیات شروع وجود ندارد، با واحد اطلاعات پایه تماس بگیرید.");
//                }
//
//            }
            }
        }

        $machine = $allocation->machine;
        // اگر ماشین نداشتن سفارش است، تخصیص رزور
        $allocation_status_id = $machine->production_status_id == 7303001 ? 5310010 : 5310040;


        // ذخیره اطلاعات تنظیمات ستاب و عملیات کارت ها
        Allocation\AllocationData::SetData(400, $machine_allocation_data, $allocation->id);

        // پیدا کردن اولین وضعیت ماشین بعد از جاری شدن کارت
        $next_status_result = \App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine\DashboardController::
        GetNextStatus($machine, $allocation, 0, -1, false, $check_status_id = [5310005, 5310050]);

        if (!$next_status_result["result"]) {
            return $next_status_result;
        }

        /***********************************************************/

        //به ازای هر ورودی ماشین یک درخواست ثبت شود.
        $result = self::MaterialFlow($allocation, $machine);
        if (!$result["result"]) {
            return $result;
        }

        $bom_list = $result["bom_list"];
        // $allocation_amount = $allocation->getAllocationAmount();
        // بررسی امکان تخصیص با توجه به موجودی انبار

        foreach ($allocation->items as $machine_allocation) {
            $permutation_list = Allocation::getMaxAllocationAmountAccordingToWarehouse($allocation, $machine_allocation->allocation_amount, $bom_list[$machine_allocation->id], [], $machine_allocation->production_id);

            // ذخیره اطلاعات محاسبات تخصیص
            Allocation\AllocationData::SetData(200, $permutation_list, $allocation->id);
            if (count($permutation_list["permutation_list"]) == 0
                || $permutation_list["end_result"][0]["amount_can_be_produced"] < $machine_allocation->allocation_amount
            ) {
                $message = GeneralMachineAllocationController::GetMessagePermutation($permutation_list, $machine_allocation->production, $machine);

                return [
                    "result" => false,
                    "error" => $message
                ];

            }
            // محاسبه ضریفت مصرف کانال تولید برای تخصیص
            $result_consumption_percent = BOM::GetConsumptionOfProductionChannel($bom_list[$machine_allocation->id]);

            if (!$result_consumption_percent["result"]) {
                return $result_consumption_percent;
            }
            if ($allocation->consumption_percent_of_production_channel < $result_consumption_percent["value"]) {
                $allocation->consumption_percent_of_production_channel = $result_consumption_percent["value"];
            }
        }

        // اضافه کردن مقدار تخصیص به کانال تولید
        $production_channel_type = $production->getProductionChannelType();
        if (!$production_channel_type) {
            return [
                "result" => false,
                "error" => "نوع کانال تولید برای کارت تولید مشخص نشده است."
            ];
        }

        /***********************************************************/

//         تغیر وضعیت همه آیتم های تخصیص
        $number_allocation = 0;
        // آیا ماژول ثبت تولید در ماشین فعال است.
        $value_202 = MachineModuleTypePropertyValue::getValue("73030011202", $machine->machine_type_id);
        $fist_machine_allocation = null;
        foreach ($allocation->items as $item) {

            if (!$fist_machine_allocation) {
                $fist_machine_allocation = $item;
            }
            $number_allocation++;
            if ($number_allocation != 1 && $value_202 && $allocation_status_id == 5310010) {
                // اگر مازول تولید در کالا فعال است، پس فقط اولین آیتم آن جاری می شود و مابعی بعد از ثبت تولید فعال می شوند.
                // این برای تنظیمات تلاش رنگ در دوره پیاده سازی اضافه گردید.
                $item->status_id = 5310040; // تخصیص رزور
            } else {
                $item->status_id = $allocation_status_id;
            }
            $item->save();

// بروز رسانی وضعیت کارت تولید
            if (in_array($item->production->waiting_status_id, [7301005, 7301001])) { // در انتظار تخصیص ، در انتظار تخصیص مجدد
                $item->production->waiting_status_id = "7301" . "002"; // در انتظار نصب و راه اندازی
                $item->production->save();
                event(new ProductionCardLogEvent($item->production));
            }
        }

//        // آخرین وضعیت  قبل از تخصیص ماشین
        $machineLog = MachineLog::create();

        $machineLog->machine_event_type_id = 90; // وضعیت قبل از تخصیص ماشین
        event(new MachineLogEvent($machine, $machineLog, "", null, null, $user_id));


        Production::sendSmsAfterAllocation($production, $machine);


        MachineAllocationController::updatePriorityNumber($machine);

//        // اگر وضعیت ماشین نداشتن سفارش باشد ماژول *** اجرا می شود.
        if ($machine->production_status_id == 7303001) {
            $machine->production_status_id = $next_status_result["status_id"];
            $machine->on_status_id = $next_status_result["on_status_id"];
            $machine->machine_off_reason_id = $next_status_result["machine_off_reason_id"];

            $machine->save();

        }


//        // لاگ تخصیص جدید ماشین
        $machineLog = MachineLog::create();
        $machineLog->machine_event_type_id = 92;
        $machineLog->allocation_id = $allocation->id;
        event(new MachineLogEvent($machine, $machineLog, "", null, null, $user_id));

        $allocation->status_id = $allocation_status_id;
        $allocation->save();


        $start_to_start_line_product_station = \App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine\DashboardController::
        GetStartToStartLineProductStation($fist_machine_allocation);

        if ($start_to_start_line_product_station) {
            StartToStartMachineController::StartToStartForAllocation($machine, $allocation);
        }

        return [
            "result" => true,

        ];
    }

    public function checkPermission(Production $production)
    {

//         بررسی دسترسی در ماژول
        $result = DashboardController::checkPermissionConditions($production, null);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }


    /**
     * @return array
     * به ازای هر تخصیص، و لیست خط هایی که باید در ماشین عملیات برای آنها انجام شود، بررسی می کنیم که به چه عملیات های شروع و پایانی نیاز
     * دارد
     * اگر بچ بود، هیچ ردیفی نداریم و فقط برای پیوسته است.
     */
    public static function GetLineProductStationMahcineAllocation(Allocation $allocation)
    {
        $list_product_station = [];
        foreach ($allocation->items as $machine_allocation) {

            // اگر عملیات بچ است، دیگر لازم نیست که اطلاعات را از آن بگیرید بنابراین از آن رد می شودیم.
            if ($machine_allocation->line_product_station->station_operation->station_operation_type_id == 1) {
                break;
            }

            // به ازای هر ردیف تخصیص، ردیف مسیر محصول را به دست می آوریم.
            $list_product_station[$machine_allocation->id] =
                LineProductStation::where([
                    "product_id" => $machine_allocation->line_product_station->product_id,
                    "machine_type_id" => $machine_allocation->line_product_station->machine_type_id,
                    "product_route_id" => $machine_allocation->line_product_station->product_route_id,
                ])->
                where("priority_number", ">=", $machine_allocation->line_product_station->priority_number)->
                get();
        }

        return $list_product_station;
    }

    public static function updatePriorityNumber(Machine $machine)
    {
        $reserve_list = $machine->ReserveAllocation()->get();
        $priority_number = 1;
        foreach ($reserve_list as $allocation) {

            $allocation->priority_number = $priority_number;
            $allocation->save();

            $priority_number++;
        }

        $current_allocation = $machine->getCurrentAllocation();
        if ($current_allocation) {
            $current_allocation->priority_number = 0;
            $current_allocation->save();
        }
    }

    public static function MaterialFlow($allocation, $machine)
    {
        // محاسبه مقدار مواد اولیه براساس مقدار تخصیص (۱) است یا مقدار فرم تولید استخراج شده از ماشین قبل (۲) (فاقد واحد)
        $value_209 = MachineModuleTypePropertyValue::getValue("73030011209", $machine->machine_type_id);

        // به دست آوردن شناسه باند ورودی
        $machine_type_input_band = MachineTypeInputBand::
        join("machine_type_input_band_goods_kind", "machine_type_input_band_id", "machine_type_input_bands.id")->
        where([
            "active_status_id" => 1200, // فعال
            "machine_type_id" => $machine->machine_type_id,
        ])->
        groupBy("goods_kind_id")->
        select("machine_type_input_bands.id", "input_line_number", "goods_kind_id")->
        get()->
        keyBy("goods_kind_id");
        $bom_list = [];
        foreach ($allocation->items as $allocation_item) {

            $production = $allocation_item->production;

            // مقدار محاسبه تخصیص براساس تنظیمات ؤ205
            $allocation_item_allocation_amount = null;
            $production_form_item = null;

            // محاسبه مقدار مواد اولیه براساس مقدار تخصیص (۱) است یا مقدار فرم تولید استخراج شده از ماشین قبل (۲)
            switch ($value_209) {
                case 1:
                    // بر اساس برگ خروج تحویل شده به ماشین بر اساس تخصیص
                    $allocation_item_allocation_amount = $allocation_item->allocation_amount;
                    break;
                case 3:
                    break;

                case 2:
                    $result_production_form = self::GetBeforeProductionFormItem($allocation_item);
                    if (!$result_production_form["result"]) {
                        return $result_production_form;
                    }
                    $production_form_item = $result_production_form["production_form_item"];
                    $allocation_item_allocation_amount = $production_form_item->final_amount;

                    // چون ممکن است بخشی از کارت تخصیص داده شود، مقدار تخصیص هم باید شکسته شود
                    // مقدار تخصیص = مقدار فرم تولید ماشین قبلی * مقدار تخصیص / مقدار کل تخصیص هایی که parent_id آنها با parent_id این تخصیص برابر است.
                    $sum_parent_allocation_id = MachineAllocation::
                    where("parent_allocation_id", $allocation_item->parent_allocation_id)->
                    where("status_id", "!=", 5310030)->sum("allocation_amount");
                    $allocation_item->allocation_amount =ceil(
                        $allocation_item_allocation_amount * $allocation_item->allocation_amount / $sum_parent_allocation_id);
                    $allocation_item->save();

                    break;

                    break;
                default:
                    return [
                        "result" => false,
                        "error" => "مقدار مشخصه ۷۳۰۳۰۰۱۱۲۰۹ در مسیر محصول مشخص نشده است، لطفا در بخش تنظیمات مربوط به " . $machine->machine_type->caption . " را کامل کرده و دوباره تلاش کنید. "
                    ];
            }


// انتخاب اولین BOM از مسیر برای تخصیص
            $bom = BOM::where([
                "product_id" => $allocation_item->product_id,
                "product_route_id" => $allocation_item->line_product_station->product_route_id
            ])->
            first();
            if (!$bom) {
                $product_route = $allocation_item->line_product_station->product_route ?? null;

                return [
                    "result" => false,
                    "error" => "  برای  تولید کالای " . $production->product->fullCaption() . " توسط ماشین های " . $machine->machine_type->caption . " (" . ($product_route ? $product_route->fullCaption() : "***") . ") " . " هیچ BOMی تعریف نشده است."
                ];

            }
            $bom_list[$allocation_item->id] = $bom;
//            $bom_items = $bom->
//            items()->
//            where("station_operation_id", $allocation_item->line_product_station->station_operation_id)->
//            get();
//            if ($bom_items->count() == 0) {
//                return [
//                    "result" => false,
//                    "error" => $bom->caption . "  برای کالای " . $production->product->fullCaption() . " ;به صورت کامل تعریف نشده است."
//                ];
//            }
//            $input_line_code_count = BOMItem::where(["bill_of_material_id" => $bom->id])->count();
//            // بررسی محدودیت تعداد ورودی های ماشین
//            if ($input_line_code_count > $input_band_count) {
//                return [
//                    "result" => false,
//                    "error" => "با توجه به محدودیت تعداد خط های ورودی در " . "ووردی نخ " . "  امکان تخصیص وجود ندارد."
//                ];
//
//            }

            // لیست همه ردیف های BOM
            $bom_material_items_all = BOMItem::
            where(["bill_of_material_id" => $bom->id,

            ])->get()->keyBy("material_id");

            //لیست ردیف های BOM  که در تخصیص درگیری می شوند ( با توجه به مسیر محصول و نوع عملیات)
            $bom_material_items = BOMItem::
            where(["bill_of_material_id" => $bom->id,
                "bill_of_material_dependency_type_id" => 1// وابسته به ماده اولیه
//                ,"material_id"=> 7,
            ])->

            where("station_operation_id", $allocation_item->line_product_station->station_operation_id)->
            groupBy("material_id")->
            get()->keyBy("material_id");


            foreach ($bom_material_items as $bom_material_item) {

                $material_info = BOMItem::where(["bill_of_material_id" => $bom->id])->
                where("material_id", $bom_material_item->material_id)->
                where("station_id", $allocation_item->machine->machine_type->station_id)->
                selectRaw("
                       sum(amount) as amount,
                       percent_of_use,
                       sum(number) as number,
                      sum(amount * percent_of_use * number /100) as amount_required,
                      min(input_line_code) as input_line_code_from,
                      max(input_line_code) as input_line_code_to,
                      productive_consume_warehouse_type_id,
                      productive_consume_warehouse_id,
                      sampling_consume_warehouse_type_id,
                      sampling_consume_warehouse_id"
                )->
                first();


                if (!isset($machine_type_input_band[$bom_material_item->material->goods_kind_id])) {
                    return [
                        "result" => false,
                        "error" => "مشخصات باند ورودی برای " . " رسته کالایی  " . $bom_material_item->material->goods_kind->caption . " یافت نشد، لطفا با پشتیبانی سیستم تماس بگیرید."
                    ];
                }
                // ورودی ماشین برای رسته کالایی مواد اولیه وجود داشته باشد.
                $input_band_count = $machine_type_input_band[$bom_material_item->material->goods_kind_id]->input_line_number ?? 0;

                if ($input_band_count < 1) {
                    return [
                        "result" => false,
                        "error" => "مشخصات باند ورودی برای " . " رسته کالایی  " . " یافت نشد، لطفا با پشتیبانی سیستم تماس بگیرید."
                    ];
                }


                // اگر مقدار تخصیص بر اساس واحد فرعی یا بسته بندی باشد، مقدار مورد نیاز کالا مشخص نمی باشد و بعد از تحویل مواد اولیه مشخص می گردد.
                switch ($value_209) {
                    case 1: // مقدار تخصیص * مقدار مورد نیاز

                        $amount_required = $allocation_item_allocation_amount ?
                            $material_info->amount_required
                            * $allocation_item_allocation_amount
                            :
                            null;
                        break;
                    case 3: // بر اساس مقدار فرم تولید
                        $amount_required = null;
                        if (
                            $bom_material_item->bill_of_material_dependency_type_id == 1 &&
                            $bom_material_item->dependent_on_material_id == $bom_material_item->product_id &&
                            $value_209 == 3
                        ) {
                            $amount_required = $material_info->amount_required * $allocation_item->allocation_amount;
                        }
                        break;
                    case 2: // مقدار فرم قبلی ( با توجه به واحد اصلی/قرعی ) * مقدار مورد نیاز

                        if ($bom_material_item->dependent_on_main_unit_type_id == 1) {
                            // واحد اصلی
                            $amount_dependent = $production_form_item->final_amount;
                        } elseif ($bom_material_item->dependent_on_main_unit_type_id == 2) {
                            $amount_dependent = $production_form_item->sub_amount;
                        }
                        $amount_required = $material_info->amount_required * $amount_dependent;
                        break;
                }


                // اگر مقدار ماده اولیه به کالای اصلی وابسته نیست و به یکی از مواد اولیه دیگر وابسته است، باید مقدار آن ماده اولیه را به دست بیاوریم.
                // مثلا در رنگرزی نخ مقدار صابون به آب وابسته است.
                if (
                    $allocation_item_allocation_amount &&
                    $bom_material_item->bill_of_material_dependency_type_id == 1 &&
                    $bom_material_item->dependent_on_material_id != $bom_material_item->product_id &&
                    $value_209 != 3
                ) {

                    $result_amount_dependency = self::GetAmountDependency($allocation_item, $bom_material_items_all, $bom_material_item, $material_info->amount_required, $production_form_item);
                    if (!$result_amount_dependency["result"]) {

                        $result_amount_dependency = self::GetAmountDependency($allocation_item, $bom_material_items_all, $bom_material_item, $material_info->amount_required, $production_form_item);

                        $result_amount_dependency["error"] = "در تخصیص کارت " . $allocation_item->production->serial . " (" . $allocation_item->product->caption . "): " . $result_amount_dependency["error"];
                        return $result_amount_dependency;
                    } else {
                        $amount_required = $result_amount_dependency["amount_dependent"];
                    }

                }

                //انبار مصرف کالا
                $consume_warehouse_id = BOMItem::getConsumeWarehouseId($bom_material_item, $production, $machine);

                if (!$consume_warehouse_id) {
                    return [
                        "result" => false,
                        "error" => "انبار مصرف کالا برای کالای " . $production->product->caption . " در گروه ماشین" . $machine->machine_type->caption . " نا مشخص است و یا انبارک برای ماشین تعریف نشده است." .
                            "<br/> برای برطرف شدن مشکل لطفا اطلاعات ماشین را بروز رسانی کرده و از صحت تعریف BOM کالا اطمینان حاصل نمایید."
                    ];
                }
                $graph_link = MaterialFlow::where([
                    "bill_of_material_id" => $bom_material_item->bill_of_material_id,
                    "material_id" => $bom_material_item->material_id

                ])->first();
                if (!$graph_link) {

                    return [
                        "result" => false,
                        "error" => "گراف جریان مواد برای " . $production->product->caption . " در گروه ماشین " . $machine->machine_type->caption . " به درستی تعریف نشده است، " .
                            "<br/>" . "لطفا با واحد اطلاعات پایه تماس بگیرید."
                    ];
                }

                if ($amount_required > 0) { // اگر مقدار وابسته است و با برگ خروج مشخص می شود، نمی توانیم مقدار را محاسبه کنیم و در زمان تحویل مواد اولیه محاسبه می شود.
                    // به ازای لینکی که در گراف وجود دارد یک درخواست یک ردیف ورودی ثبت می کند.
                    CurrentMachineInput::CreateOrUpdate(
                        $machine_type_input_band[$bom_material_item->material->goods_kind_id]->id,
                        $bom_material_item->input_line_code,   //  کد خط ورودی
                        $bom_material_item->material_id,
                        $material_info->amount,
                        $amount_required,
                        $material_info->percent_of_use,
                        $bom_material_item->material->goods_kind_id,
                        $allocation->id,
                        $machine->id,
                        $allocation_item->product_id,
                        $allocation_item->production_id,
                        $graph_link->band_code ?? 1,
                        $material_info->number,
                        null,
                        null,
                        $consume_warehouse_id,
                        $bom_material_item->warehouse_id,
                        $bom_material_item,
                        $bom_material_item->bill_of_material_entering_type_id
                    );
                }
            }
        }


        return ["result" => true, "bom_list" => $bom_list];
    }


    /**
     * @param $allocation
     * @param $machine
     * @param ProductionFormItem $production_form_item
     * @return array
     * وقتی متغیر V209==3 می باشد، یغنی باید اول برگ خروج از انبار ثبت شود بعد مقدار مواد اولیه وابسته به ماده اولیه ساختاری محاسبه شود.
     */
    public static function UpdateMaterialFlowV209_3($allocation, $machine, ProductionFormItem $production_form_item)
    {
        // به دست آوردن شناسه باند ورودی
        $machine_type_input_band = MachineTypeInputBand::
        join("machine_type_input_band_goods_kind", "machine_type_input_band_id", "machine_type_input_bands.id")->
        where([
            "active_status_id" => 1200, // فعال
            "machine_type_id" => $machine->machine_type_id,
        ])->
        groupBy("goods_kind_id")->
        select("machine_type_input_bands.id", "input_line_number", "goods_kind_id")->
        get()->
        keyBy("goods_kind_id");
        $bom_list = [];
        foreach ($allocation->items as $allocation_item) {

            if ($production_form_item->production_id != $allocation_item->production_id) {
                1 / 0; // اینجا یعنی تخصیص بیش از یک آیتم داشته است و ما نمی توانیم وقتی تخصیص بیش از یک آیتم داشته باشد، مقدار مواد اولیه را محاسبه کنیم.
            }
            $production = $allocation_item->production;

            // مقدار محاسبه تخصیص براساس تنظیمات ؤ205
            $allocation_item_allocation_amount = $production_form_item->amount;


// انتخاب اولین BOM از مسیر برای تخصیص
            $bom = BOM::where([
                "product_id" => $allocation_item->product_id,
                "product_route_id" => $allocation_item->line_product_station->product_route_id
            ])->
            first();
            if (!$bom) {
                $product_route = $allocation_item->line_product_station->product_route ?? null;

                return [
                    "result" => false,
                    "error" => "  برای  تولید کالای " . $production->product->fullCaption() . " توسط ماشین های " . $machine->machine_type->caption . " (" . ($product_route ? $product_route->fullCaption() : "***") . ") " . " هیچ BOMی تعریف نشده است."
                ];

            }
            $bom_list[$allocation_item->id] = $bom;


            // لیست همه ردیف های BOM
            $bom_material_items_all = BOMItem::
            where(["bill_of_material_id" => $bom->id,

            ])->get()->keyBy("material_id");

            //لیست ردیف های BOM  که در تخصیص درگیری می شوند ( با توجه به مسیر محصول و نوع عملیات)
            $bom_material_items = BOMItem::
            where(["bill_of_material_id" => $bom->id,
                "bill_of_material_dependency_type_id" => 1// وابسته به ماده اولیه
//                ,"material_id"=> 7,
            ])->

            where("station_operation_id", $allocation_item->line_product_station->station_operation_id)->
            groupBy("material_id")->
            get()->keyBy("material_id");


            foreach ($bom_material_items as $bom_material_item) {

                $material_info = BOMItem::where(["bill_of_material_id" => $bom->id])->
                where("material_id", $bom_material_item->material_id)->
                where("station_id", $allocation_item->machine->machine_type->station_id)->
                selectRaw("
                       sum(amount) as amount,
                       percent_of_use,
                       sum(number) as number,
                      sum(amount * percent_of_use * number /100) as amount_required,
                      min(input_line_code) as input_line_code_from,
                      max(input_line_code) as input_line_code_to,
                      productive_consume_warehouse_type_id,
                      productive_consume_warehouse_id,
                      sampling_consume_warehouse_type_id,
                      sampling_consume_warehouse_id"
                )->
                first();


                if (!isset($machine_type_input_band[$bom_material_item->material->goods_kind_id])) {
                    return [
                        "result" => false,
                        "error" => "مشخصات باند ورودی برای " . " رسته کالایی  " . $bom_material_item->material->goods_kind->caption . " یافت نشد، لطفا با پشتیبانی سیستم تماس بگیرید."
                    ];
                }
                // ورودی ماشین برای رسته کالایی مواد اولیه وجود داشته باشد.
                $input_band_count = $machine_type_input_band[$bom_material_item->material->goods_kind_id]->input_line_number ?? 0;

                if ($input_band_count < 1) {
                    return [
                        "result" => false,
                        "error" => "مشخصات باند ورودی برای " . " رسته کالایی  " . " یافت نشد، لطفا با پشتیبانی سیستم تماس بگیرید."
                    ];
                }


                $amount_required = null;
                // اگر مقدار ماده اولیه به کالای اصلی وابسته نیست و به یکی از مواد اولیه دیگر وابسته است، باید مقدار آن ماده اولیه را به دست بیاوریم.
                // مثلا در رنگرزی نخ مقدار صابون به آب وابسته است.
                // یا مقدار رنگ وابسته به نخ خام میباشد
                if (
                    $allocation_item_allocation_amount &&
                    $bom_material_item->bill_of_material_dependency_type_id == 1 &&
                    $bom_material_item->dependent_on_material_id != $bom_material_item->product_id
                ) {

                    $result_amount_dependency = self::GetAmountDependency($allocation_item, $bom_material_items_all, $bom_material_item, $material_info->amount_required, $production_form_item, false);
                    if (!$result_amount_dependency["result"]) {

                        $result_amount_dependency = self::GetAmountDependency($allocation_item, $bom_material_items_all, $bom_material_item, $material_info->amount_required, $production_form_item, false);

                        $result_amount_dependency["error"] = "در تخصیص کارت " . $allocation_item->production->serial . " (" . $allocation_item->product->caption . "): " . $result_amount_dependency["error"];
                        return $result_amount_dependency;
                    } else {
                        $amount_required = $result_amount_dependency["amount_dependent"];
                    }

                }

                //انبار مصرف کالا
                $consume_warehouse_id = BOMItem::getConsumeWarehouseId($bom_material_item, $production, $machine);

                if (!$consume_warehouse_id) {
                    return [
                        "result" => false,
                        "error" => "انبار مصرف کالا برای کالای " . $production->product->caption . " در گروه ماشین" . $machine->machine_type->caption . " نا مشخص است و یا انبارک برای ماشین تعریف نشده است." .
                            "<br/> برای برطرف شدن مشکل لطفا اطلاعات ماشین را بروز رسانی کرده و از صحت تعریف BOM کالا اطمینان حاصل نمایید."
                    ];
                }
                $graph_link = MaterialFlow::where([
                    "bill_of_material_id" => $bom_material_item->bill_of_material_id,
                    "material_id" => $bom_material_item->material_id

                ])->first();
                if (!$graph_link) {

                    return [
                        "result" => false,
                        "error" => "گراف جریان مواد برای " . $production->product->caption . " در گروه ماشین " . $machine->machine_type->caption . " به درستی تعریف نشده است، " .
                            "<br/>" . "لطفا با واحد اطلاعات پایه تماس بگیرید."
                    ];
                }

                if ($amount_required > 0) { // اگر مقدار وابسته است و با برگ خروج مشخص می شود، نمی توانیم مقدار را محاسبه کنیم و در زمان تحویل مواد اولیه محاسبه می شود.
                    // به ازای لینکی که در گراف وجود دارد یک درخواست یک ردیف ورودی ثبت می کند.
                    CurrentMachineInput::CreateOrUpdate(
                        $machine_type_input_band[$bom_material_item->material->goods_kind_id]->id,
                        $bom_material_item->input_line_code,   //  کد خط ورودی
                        $bom_material_item->material_id,
                        $material_info->amount,
                        $amount_required,
                        $material_info->percent_of_use,
                        $bom_material_item->material->goods_kind_id,
                        $allocation->id,
                        $machine->id,
                        $allocation_item->product_id,
                        $allocation_item->production_id,
                        $graph_link->band_code ?? 1,
                        $material_info->number,
                        null,
                        null,
                        $consume_warehouse_id,
                        $bom_material_item->warehouse_id,
                        $bom_material_item,
                        $bom_material_item->bill_of_material_entering_type_id
                    );
                }
            }
        }


        return ["result" => true, "bom_list" => $bom_list];
    }

    public static function GetAmountDependency(MachineAllocation $machine_allocation, $bom_material_items, $bom_material_item, $amount_required, $production_form_item, $parent_allocation_id_check = true)
    {

        if (!isset($bom_material_items[$bom_material_item->dependent_on_material_id])) {
            return [
                "result" => false,
                "error" => "در تعریف BOM برای ماده اولیه (" . $bom_material_item->material->caption . ") نوع کالای وابسته نامعتبر است، لطفا با واحد طراحی کالا تماس بگیرید."
            ];
        }
        $dependency_material = $bom_material_items[$bom_material_item->dependent_on_material_id];

        if ($dependency_material->bill_of_material_dependency_type_id == 1 && $dependency_material->dependent_on_material_id != $bom_material_item->product_id) {
            return [
                "result" => false,
                "error" => "در تعریف BOM برای ماده اولیه (" . $bom_material_item->material->caption . ") نوع وابستگی ماده اولیه با سطح وابستگی به ماده اولیه می رسد، لطفا با واحد طراحی کالا تماس بگیرید."

            ];
        }

        if ($dependency_material->bill_of_material_dependency_type_id == 2) {
            return [
                "result" => true,
                "amount_dependent" => ($dependency_material->amount * $dependency_material->number * $dependency_material->percent_of_use / 100) * $amount_required,
            ];
        }

        if (!$machine_allocation->parent_allocation_id && $parent_allocation_id_check) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه  تخصیص کارت تولید " . $machine_allocation->production->serail() . " اولین تخصیص مسیر محصول است، امکان محاسبه مقدار مواد اولیه وابسته وجود ندارد. "
            ];
        }

        if ($bom_material_item->dependent_on_main_unit_type_id == 1) {
            // واحد اصلی
            $amount_dependent = $production_form_item->final_amount;
        } elseif ($bom_material_item->dependent_on_main_unit_type_id == 2) {
            $amount_dependent = $production_form_item->sub_amount;
        }

        if (!$amount_dependent) {
            return [
                "result" => false,
                "error" => " امکان محاسبه کالای  وابسته (" .
                    $production_form_item->product->caption .
                    ") جهت ماده اولیه (" .
                    $bom_material_item->material->caption .
                    ") وجود ندارد."
            ];
        }

        if ($bom_material_item->dependent_on_material_unit_type_id != 1) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه واحد مرجع ماده اولیه (" .
                    $bom_material_item->material->caption .
                    ") واحد اصلی نمی باشد، امکان محاسبه مقدار مورد نیاز وجود ندارد، لطفا با واحد پشتیبانی تماس بگیرید."
            ];
        } else {
            return [
                "result" => true,
                "amount_dependent" => $amount_dependent * $amount_required,
            ];
        }

    }

    public static function GetBeforeProductionFormItem(MachineAllocation $allocation_item)
    {
        // به دست آوردن مقدار فرم قبلی
        $production_form_items = ProductionFormItem::join("production_forms", "production_forms.id", "production_form_id")->where([
            "production_id" => $allocation_item->production_id,
            "allocation_id" => $allocation_item->parent_allocation_id
        ])->
       // where("production_forms.status_id", "!=", 7302004)->
        get();

        if (count($production_form_items) != 1 ) {
//            return [
//                "result" => false,
//                "error"=>count($production_form_items)
//            ];
            $production_form_items_text = "<br/>" . "ردیف های فرم تولید:";
            foreach ($production_form_items as $production_form_item) {
                $production_form_items_text .= $production_form_item->code . "<br/>";
            }
            return [
                "result" => false,
                "error" => "با توجه به اینکه در فرم تولید ماشین " .
                    ($allocation_item->parent_allocation->machine->caption ?? "***") . " بیش از یک ردیف (یا هیچ ردیفی) برای کارت تولید " .
                    $allocation_item->production->serial() .
                    " وجود دارد، امکان محاسبه مقدار ماده اولیه وجود ندارد،<br/> لطفا تنظیمات گروه ماشین در بخش خطای تولید را بررسی کنید و در صورت اطمینان از صحت تنظیمات با واحد پشتیبانی تماس بگیرید." .
                    $production_form_items_text
            ];
        }

        // ماده اولیه به کدام واحد کالا وابسته است.
        $production_form_item = $production_form_items->first();

        return [
            "result" => true,
            "production_form_item" => $production_form_item,
        ];
    }

    public static function GetAllocationByType(LineProductStation $lineProductStation, $amount, $type)
    {
        switch ($type) {
            case "allocation_amount":
                // مقدار اصلی تخصییص: یا مسیر محصول سری است و یا اکر بچ است، نوع واحد وابسته به بچ واحد اصلی است.
                if (!$lineProductStation->batch || $lineProductStation->material_unit_type_id_dependent_to_batch == 1) {
                    return $amount;
                }
                return null;
                break;
            case "allocation_sub_amount":
                // مقدار اصلی تخصییص: یا مسیر محصول سری است و یا اکر بچ است، نوع واحد وابسته به بچ واحد فرعی است.
                if (!$lineProductStation->batch || $lineProductStation->material_unit_type_id_dependent_to_batch == 2) {
                    return $amount;
                }
                return null;
                break;
            case "number_of_packing_form":
                // مقدار اصلی تخصییص: یا مسیر محصول سری است و یا اکر بچ است، نوع واحد وابسته به بچ واحد فرعی است.
                if ($lineProductStation->material_unit_type_id_dependent_to_batch == 4) {
                    return $amount;
                }
                return null;
                break;
        }

    }

    public static function GetProductionListByAlgorithm($value_207, Production $production)
    {
        $allow_machine_allocation_list = [];

        switch ($value_207) {
            case 1: // بر اساس تشابه مسیر محصول
                // مرحله 1: لیست کارت هایی که مقدار تخصیص آنها از مقدار کارت تولید کمتر است.
                $production_list = Production::
                whereIn("waiting_status_id", [7301001, 7301002, 7301003])->
                where("id", "!=", $production->id)->
                with("product")->
                with("product.line_product_station")->
                get();
                break;
            case 2: // بر اساس تشابه کالا
                $production_list = Production::
                whereIn("waiting_status_id", [7301001, 7301002, 7301003])->
                where("id", "!=", $production->id)->
                where("product_id", $production->product_id)->
                with("product")->
                with("product.line_product_station")->
                get();
                break;

            case 3: //  بر اساس تشابه کلا و بسته بندی مجاز
                $production_ids = Production::
                whereIn("waiting_status_id", [7301001, 7301002, 7301003])->
                where("product_id", $production->product_id)->
                pluck("id");

                $production_ids_all[] = $production->id;
                $production_packing_type_list = ProductionPackingType::whereIn("production_id", $production_ids)->pluck("packing_type_id", "production_id")->toArray();

                $production_packing_type = [];
                foreach ($production_packing_type_list as $production_id => $packing_type_id) {
                    $production_packing_type[$production_id][$packing_type_id] = $packing_type_id;
                }

                if (!isset($production_packing_type[$production->id])) {
                    return [
                        "result" => false,
                        "error" => "با توجه به اینکه الگوریتم تشخیص کارت هایی که با هم می توانند بر اساس نوع بسته بندی می باشد و کارت در حال تخصیص هیچ نوع بسته بندی مجازی ندارد، امکان تخصیص وجود ندارد."
                    ];
                }
                $production_ids_all = []; // لیست کارت های تولید نهایی
                foreach ($production_packing_type as $p_id => $item) {
                    if (count(array_diff($item, $production_packing_type[$production->id])) +
                        count(array_diff($production_packing_type[$production->id], $item)) == 0) {
                        $production_ids_all[] = $p_id;
                    }
                }

                $production_list = Production::
                whereIn("id", $production_ids_all)->
                where("id", "!=", $production->id)->
                with("product")->
                with("product.line_product_station")->
                get();

                break;
            default:
                return [
                    "result" => false,
                    "error" => "الگوریتم تشخیص کارت هایی که با هم می توانند تخصیص داده شوند در تنظیمات گروه ماشین مشخص نشده است، لطفا با پشتیبانی تماس بگیرید. "
                ];
        }


        return [
            "result" => true,
            "production_list" => $production_list
        ];

    }

    public static function GetReAllocationByAlgorithm($value_207, Production $master_production, $machine_allocation_list)
    {

        $reallocation_list = [];
        switch ($value_207) {
            case 1: // بر اساس تشابه مسیر محصول


                $reallocation_list = $machine_allocation_list;


                break;
            case 2: // بر اساس تشابه کالا
                $reallocation_list = [];
                foreach ($machine_allocation_list as $machine_allocation_item) {
                    if ($machine_allocation_item->product_id == $master_production->product_id) {
                        $reallocation_list[] = $machine_allocation_item;
                    }
                }

                break;

            case 3: //  بر اساس تشابه کلا و بسته بندی مجاز
                $production_ids = [$master_production->id];
                foreach ($machine_allocation_list as $machine_allocation_item) {
                    if ($machine_allocation_item->product_id == $master_production->product_id) {
                        $production_ids[] = $machine_allocation_item->production_id;
                    }
                }


                $production_packing_type_list = ProductionPackingType::whereIn("production_id", $production_ids)->pluck("packing_type_id", "production_id")->toArray();

                $production_packing_type = [];
                foreach ($production_packing_type_list as $production_id => $packing_type_id) {
                    $production_packing_type[$production_id][$packing_type_id] = $packing_type_id;
                }

                if (!isset($production_packing_type[$master_production->id])) {
                    return [
                        "result" => false,
                        "error" => "با توجه به اینکه الگوریتم تشخیص کارت هایی که با هم می توانند بر اساس نوع بسته بندی می باشد و کارت در حال تخصیص هیچ نوع بسته بندی مجازی ندارد، امکان تخصیص وجود ندارد."
                    ];
                }
                $production_ids_all = []; // لیست کارت های تولید نهایی
                foreach ($production_packing_type as $p_id => $item) {
                    if (count(array_diff($item, $production_packing_type[$master_production->id])) +
                        count(array_diff($production_packing_type[$master_production->id], $item)) == 0) {
                        $production_ids_all[] = $p_id;
                    }
                }

                foreach ($machine_allocation_list as $machine_allocation_item) {
                    if (in_array($machine_allocation_item->production_id, $production_ids_all)) {
                        $reallocation_list[] = $machine_allocation_item;
                    }
                }


                break;
            default:
                return [
                    "result" => false,
                    "error" => "الگوریتم تشخیص کارت هایی که با هم می توانند تخصیص داده شوند در تنظیمات گروه ماشین مشخص نشده است، لطفا با پشتیبانی تماس بگیرید. "
                ];
        }

        return [
            "result" => true,
            "machine_allocation_list" => $reallocation_list
        ];
    }


    public static function AutoAllocation(MachineAllocation $machineAllocation, $user_id)
    {


        $before_machine_allocation = MachineAllocation::
        where("production_id", $machineAllocation->production_id)->
        where("allocation_id", $machineAllocation->parent_allocation_id)->first();
        if (!$before_machine_allocation) {
            return [
                "result" => false,
                "error" => "شماره تخصیص قبلی یافت نشد."
            ];
        }


        $machine = Machine::where("number_code", $before_machine_allocation->machine->next_relation_machine_code)->
        where("machine_type_id", $machineAllocation->line_product_station->machine_type_id)->first();
        if (!$machine) {
            return [
                "result" => false,
                "error" => "ماشین متناظر بعدی یافت نشد."
            ];
        }


        $first_line_product_station_id = $machineAllocation->line_product_station->id;
        $main_allocation_amount = 0;
        $together_checkbox = ["on"];
        $together_allocation_amount = [$machineAllocation->allocation_amount];
        $current_machine_allocation_id = $machineAllocation->id;

        $reserve_after_allocation_id = null;
        $production_allocation_togethers_session = [

            [
                "machine_allocation_id" => $machineAllocation->id,
                "parent_allocation_id" => $machineAllocation->parent_allocation_id,
                "production_id" => $machineAllocation->production_id,
                "allocation_amount" => $machineAllocation->allocation_amount,
                "line_product_station_id" => $machineAllocation->line_product_station_id,
                "production_channel_type_id" => $machineAllocation->line_product_station->production_channel_type_id,
                "station_operation_type_id" => $machineAllocation->line_product_station->station_operation->station_operation_type_id,
                "station_operation_category_id" => $machineAllocation->line_product_station->station_operation->station_operation_category_id,
            ]
        ];
        $result = self::PostSubmit($machine, $machineAllocation->production,
            $first_line_product_station_id,
            $main_allocation_amount,
            $together_checkbox,
            $together_allocation_amount,
            $current_machine_allocation_id,

            $reserve_after_allocation_id,
            $production_allocation_togethers_session
        );

        if (!$result["result"]) {
            return $result;
        }

        $allocation = $result["allocation"];

        $list_product_station_list = self::GetLineProductStationMahcineAllocation($allocation);

        $request_machine_allocation = [];
        foreach ($allocation->items as $machine_allocation_item) {
            if (isset($list_product_station_list[$machine_allocation_item->id])) {
                foreach ($list_product_station_list[$machine_allocation_item->id] as $item) {


                    $request_machine_allocation[$machineAllocation->id] = null;
                    $request_machine_allocation[$machineAllocation->id][$item->id] = [];

                    if ($item->is_need_start_setup) {
                        $request_machine_allocation[$machineAllocation->id][$item->id]["is_need_start_setup"] = 1;
                    }
                    if ($item->is_need_start_of_operation) {
                        $request_machine_allocation[$machineAllocation->id][$item->id]["is_need_start_of_operation"] = 1;
                    }
                    if ($item->is_need_end_of_operation) {
                        $request_machine_allocation[$machineAllocation->id][$item->id]["is_need_end_of_operation"] = 1;
                    }
                    if ($item->is_need_final_setting) {
                        $request_machine_allocation[$machineAllocation->id][$item->id]["is_need_final_setting"] = 1;
                    }
                    if ($item->is_need_for_quality_control) {
                        $request_machine_allocation[$machineAllocation->id][$item->id]["is_need_for_quality_control"] = 1;
                    }
                    if ($item->is_need_allocation_at_first) {
                        $request_machine_allocation[$machineAllocation->id][$item->id]["is_need_allocation_at_first"] = 1;
                    }

                }
            }
        }

        $result = self::PostConfirmSubmit($allocation, $machineAllocation->production, $request_machine_allocation, $user_id);

        if (!$result["result"]) {
            return $result;
        }

        return [
            "result" => true,
        ];

    }


}


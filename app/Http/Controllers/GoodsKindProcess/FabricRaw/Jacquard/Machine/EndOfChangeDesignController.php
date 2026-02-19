<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\CurrentMachineInputLog;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Machine\MachineTypeOutputBandPackingType;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\Option;
use Carbon\Carbon;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;

class EndOfChangeDesignController extends Controller
{
    public static $info = [
        "route" => "fabric_raw.jacquard.machine.end_of_change_design.",
        "enable_status" => ["047"],

        "button" => ["caption" => "پایان تغییر کالیته", "class" => "btn-primary"],
        "view_path" => "goods_kind_process.fabric_raw.jacquard.machine.end_of_change_design.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct()
    {
        $this->route_path = EndOfChangeDesignController::$info["route"];
        $this->view_path = EndOfChangeDesignController::$info["view_path"];
    }

    public function index(Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        return redirect()->route($this->route_path . "has_article_change", compact("machine"));

    }

    public function has_article_change(Machine $machine)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }
        $allocation = $machine->getFirstReserveAllocation();

        if (!$allocation) {
            return back()->withErrors("تخصیص جاری ماشین یافت نشد.");
        }
        if ($allocation->has_article_change) {
            return view($this->view_path . "has_article_change", compact("machine"));
        } else {
            return redirect()->route($this->route_path . "has_yarn_weft_change", compact("machine"));

        }

    }

    public function has_yarn_weft_change(Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }


        if (!$machine->check_inventory_for_allocation) {

            return back()->withErrors("دریافت لات چله برای حالتی که تنظیمات چک کردن مواد اولیه برای انبارک غیر فعال است، امکان پذیر نیست، لطفا با پشتیبانی تماس بگیرید. ");

//            // روش دریافت لات نخ
//            $allocation         = $machine->getFirstReserveAllocation();
//            $reserve_input_list = CurrentMachineInput::
//            where( [
//                "machine_id"    => $machine->id,
//                "goods_kind_id" => 2,
//                "allocation_id" => ( $allocation->id ?? - 1 )
//            ] )->
//
//            orderBy( "input_line_code" )->
//            get();

//            return view( $this->view_path . "has_yarn_weft_change", compact( "machine", "reserve_input_list" ) );
        } else {

            // روش دریافت بسته بندی های رسته های کالایی که رباط درخواست برای آنها فعال است.

            // ارسال درخواست برای کدام رسته های کالایی فعال است.
            $goods_kind_ids = MachineTypeInputBandGoodsKind:: getGoodsKindIdsWhereRequestFromRobot($machine, 1);


            $allocation = $machine->getFirstReserveAllocation();
            $reserve_input_list = CurrentMachineInput::
            where([
                "machine_id" => $machine->id,
                "allocation_id" => ($allocation->id ?? -1)
            ])->
            whereIn("goods_kind_id", $goods_kind_ids)->
            groupBy("input_line_code", "material_id")->
            orderBy("input_line_code")->
            get();

            $before_allocation = Allocation::where("machine_id", $machine->id)->whereIn("status_id", [
                5310010,
                5310020
            ])->orderByDesc("id")->first();

            $before_input_list = CurrentMachineInput::
            where([
                "machine_id" => $machine->id,
                "allocation_id" => ($before_allocation->id ?? -1)
            ])->
            orderBy("input_line_code")->
            get();

            $before_input_value = [];
            foreach ($before_input_list as $item) {
                $before_input_value[$item->material_id][$item->input_line_code] = $item;
            }

            $current_input_list = $reserve_input_list;

            return view($this->view_path . "injection_of_material", compact("machine", "current_input_list", "before_input_value"));


        }

    }

    // در صورتی که check_inventory_for_allocation برای ماشین غیرفعال باشد
    public function submit_yarn_weft_change(Request $request, Machine $machine)
    {

        return back()->withErrors("دریافت لات چله برای حالتی که تنظیمات چک کردن مواد اولیه برای انبارک غیر فعال است، امکان پذیر نیست، لطفا با پشتیبانی تماس بگیرید. ");

//        $result = $this->checkPermission( $machine );
//        if ( $result != "" ) {
//            return $result;
//        }
//        $allocation         = $machine->getFirstReserveAllocation();
//        $reserve_input_list = CurrentMachineInput::
//        where( [ "machine_id" => $machine->id, "goods_kind_id" => 2, "allocation_id" => ( $allocation->id ?? - 1 ) ] )->
//
//        orderBy( "input_line_code" )->
//        get();
//
//        $message = "";
//        foreach ( $reserve_input_list as $item ) {
//            $id       = "input_" . $item->id;
//            $lot_code = $request->$id;
//
//            if ( ! LotNumber::ExistsCode( $lot_code, $item->material_id ) ) {
//                $message .= "لات " . $lot_code . " برای " . $item->material->fullCaption() . " (ورودی " . $item->input_line_code . " ) " . " در سیستم تعریف نشده است." . "<br/>";
//            }
//        }
//        if ( $message != "" ) {
//            return back()->withErrors( $message );
//        }
//
//        foreach ( $reserve_input_list as $item ) {
//            $id       = "input_" . $item->id;
//            $lot_code = $request->$id;
//
//            $lot_number          = LotNumber::where( [
//                "product_id" => $item->material_id,
//                "code"       => $lot_code
//            ] )->first();
//            $item->lot_number_id = $lot_number->id;
//            $item->save();
//        }
//
//
//        /// بروز رسانی ظرفیت چله
//        Warps::updateCarrierInCurrentInputOutputBand( $machine, "updateProductionChannel", $allocation );
//
//        return redirect()->route( $this->route_path . "has_weft_density_change", compact( "machine" ) );
    }

    // در صورتی که check_inventory_for_allocation برای ماشین فعال باشد
    public function submit_injection_of_material(Request $request, Machine $machine)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $allocation = $machine->getFirstReserveAllocation();

        // ارسال درخواست برای کدام رسته های کالایی فعال است.
        $goods_kind_ids = MachineTypeInputBandGoodsKind:: getGoodsKindIdsWhereRequestFromRobot($machine, $active = 1);


        $reserve_input_list = CurrentMachineInput::
        where(["machine_id" => $machine->id, "allocation_id" => ($allocation->id ?? -1)])->
        whereIn("goods_kind_id", $goods_kind_ids)->
        orderBy("input_line_code")->
        get();


        $input_number = count($reserve_input_list);
        $result = InjectionOfMaterialController::SetInjectionMaterial($request, $machine, $allocation, $input_number);
        if ($result["result"]) {

            /// بروز رسانی لات و ورودی چله
            $reserve_input_list = CurrentMachineInput::
            where([
                "machine_id" => $machine->id,
                "goods_kind_id" => 3,
                "allocation_id" => ($allocation->id ?? -1)
            ])->
            whereNull("packing_form_id")->
            get();

            if (count($reserve_input_list) > 0) /// گرفتن آخرین تخصیص
            {
                $last_terminate_allocation = Allocation::
                join("machine_allocation", "allocation_id", "allocations.id")->
                where("allocations.machine_id", $machine->id)->
                where("allocations.status_id", 5310020)->
                orderByDesc("production_start_date")->
                select("allocations.*")->
                first();
                if (!$last_terminate_allocation) {
                    $last_terminate_allocation = Allocation::
                    join("machine_allocation", "allocation_id", "allocations.id")->
                    where("allocations.machine_id", $machine->id)->
                    where("allocations.status_id", 5310030)->
                    orderByDesc("allocation_id")->
                    select("allocations.*")->
                    first();
                    if (!$last_terminate_allocation) {
                        return back()->withErrors("تکمیل اطلاعات ورودی چله با خطا کد 1 مواجه شده است، لطفا با پشتیبانی تماس بگیرید.");

                    }
                }

                $last_terminate_input_list = CurrentMachineInput::
                where([
                    "machine_id" => $machine->id,
                    "goods_kind_id" => 3,
                    "allocation_id" => ($last_terminate_allocation->id ?? -1)
                ])->
                get();

                foreach ($reserve_input_list as $reserve_input) {
                    foreach ($last_terminate_input_list as $terminate_input) {
                        if ($terminate_input->input_line_code == $reserve_input->input_line_code) {

                            $reserve_input->packing_form_id = $terminate_input->packing_form_id;
                            $reserve_input->carrier_id = $terminate_input->carrier_id;
                            $reserve_input->lot_number_id = $terminate_input->lot_number_id;
                            $reserve_input->save();
                        }
                    }

                    if (!isset($reserve_input->packing_form_id)) {
                        return back()->withErrors("تکمیل اطلاعات ورودی چله با خطا کد 2 مواجه شده است، لطفا با پشتیبانی تماس بگیرید." . "<br/>" . $reserve_input->id);
                    }
                }

            }

            // به روز رسانی ظرفیت چله
            // Warps::updateCarrierInCurrentInputOutputBand( $machine, "updateProductionChannel", $allocation );


            return redirect()->route($this->route_path . "has_weft_density_change", compact("machine"));

        } else {
            return back()->withErrors($result["error"]);

        }

        return redirect()->route($this->route_path . "has_weft_density_change", compact("machine"));
    }

    public function has_weft_density_change(Machine $machine)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }
        $allocation = $machine->getFirstReserveAllocation();

        if ($allocation->has_weft_density_change) {
            return view($this->view_path . "has_weft_density_change", compact("machine"));
        } else {
            return redirect()->route($this->route_path . "has_meter_change", compact("machine"));

        }

    }

    public function has_meter_change(Machine $machine)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }
        $allocation = $machine->getFirstReserveAllocation();


        return view($this->view_path . "has_meter_change", compact("machine", "allocation"));

    }

    public function complete(Machine $machine)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $allocation = $machine->getFirstReserveAllocation();


        return view($this->view_path . "index", compact("machine", "allocation"));

    }

    public function submit(Request $request, Machine $machine)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $before_allocation = $machine->getCurrentAllocation();
        $allocation = $machine->getFirstReserveAllocation();

        if (!$allocation) {
            return back()->withErrors("تخصیص رزور یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
        }
// دریافت قطب ها
        $message = "";
        $last_row_log = MachineLog::getLastLogWithContour($machine);

        $contour_result = $last_row_log->checkMinContour(
            $request->contour_1_value,
            $request->contour_2_value,
            $request->contour_3_value,
            $request->contour_4_value,
            $request->contour_5_value);
        if (
            isset($last_row_log) && !$contour_result["result"]
        ) {
            $message .= $contour_result["error"];
        }

        if ($message != "") {
            return back()->withErrors($message);
        }

        // لاگ ماشین
        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 570;
        $machineLog->contour_1_value = $request->contour_1_value * $contour_result["ratio"];
        $machineLog->contour_2_value = $request->contour_2_value * $contour_result["ratio"];
        $machineLog->contour_3_value = $request->contour_3_value * $contour_result["ratio"];
        $machineLog->contour_4_value = $request->contour_4_value * $contour_result["ratio"];
        $machineLog->contour_5_value = $request->contour_5_value * $contour_result["ratio"];
        $machineLog->shift_work_id = $request->shift_work_id;
        $machineLog->save();


//        //کارت تولید رزور به کارت تولید اصلی تبدیل می شود
        if ($before_allocation) {
            $before_allocation->status_id = 5310020; //  تخصیص های پایان یافته
            $before_allocation->save();
            foreach ($before_allocation->items as $item) {
                $item->status_id = 5310020; //  تخصیص های پایان یافته
                $item->save();
            }
        }


        $allocation->status_id = 5310010; //   تخصیص داده شده
        $allocation->save();
        foreach ($allocation->items as $item) {
            $item->status_id = 5310010; //  تخصیص داده شده
            $item->production_start_date = Carbon::now();
            $item->save();
        }

        // اگر فرم تولید وجود نداشت که در حال بافت پارچه پایانی باشد، وضعیت فرم تولید در انتظار بارگذاری را به در حال بافت تغییر می دهد
        $form_7002008 = ProductionForm::where([
            "machine_id" => $machine->id,
            "status_id" => 7002008  // در حال بافت پارچه پایانی
        ])->first();
        $form_7002011 = ProductionForm::where([
            "machine_id" => $machine->id,
            "status_id" => 7002011  // در انتظار بارگذاری
        ])->first();

        if (!$form_7002008 && $form_7002011) {
            $form_7002011->ChangeStatus(7002001, "", 7002015); // در حال بافت
            $form_7002011->in_the_weaving_machine_log_id = $machineLog->id;
            $form_7002011->save();
        }

// ممکن است به دلیل کش کردن مقادیر، اطلاعات تخصیص به درستی لود نشود، به همین دلیل یک بار دیگر فراخواانی می کنیم.
        $machine = Machine::find($machine->id);
        $allocation = $machine->getCurrentAllocation();

        $before_allocation = Allocation::
        where("id", "<", $allocation->id)->
        where("machine_id", $machine->id)->
        where("status_id", 5310020)-> //  تخصیص های پایان یافته
        orderByDesc("id")->first();

        // تولید لات پارچه
        FabricRaw:: ChangeLot($allocation);
        FabricRaw:: ChangeLot($allocation, 7002011);

        if ($allocation->items[0]->production->production_type_id == 2) { //
            $machine->setStatus(
                null,
                53002,
                7003053, // در انتظار شروع نمونه گیری
                1830);
        } elseif ($before_allocation && $before_allocation->items[0]->product_id != $allocation->items[0]->product_id) {
            $machine->setStatus(
                null,
                null,
                7003013, // در انتظار راه اندازی شیفت
                1611);

        } else {
            if ($machine->getFirstReserveAllocation()) {
                $machine->setStatus(
                    null,
                    53001, // روشن
                    7003042,
                    null);
            } else {
                $machine->setStatus(
                    null,
                    53001, // روشن
                    7003016,
                    null);
            }
            // تغییر وضعیت کارت تولید
            foreach ($allocation->items as $item) {
                if ($item->production->waiting_status_id == 7001002) {
                    $item->production->waiting_status_id = 7001003;
                    $item->production->save();
                    event(new ProductionCardLogEvent($item->production));
                }
            }

        }
        event(new MachineLogEvent($machine, $machineLog));


        $production_form = $machine->getCurrentProductionForm();
        if ($production_form) {
            // بروزرسانی مقادیر فرم تولید
            $last_machine_log = MachineLog::getLastLogWithContour($machine);
            ProductionForm::UpdateAmountWithLastContour($production_form, $last_machine_log);
        }

        // بروز رسانی ستون لاگ ماشین و فرم تولید در جدول لاگ ورودی های ماشین
        $current_machine_log_input = CurrentMachineInputLog::
        where(["allocation_id" => $allocation->id, "machine_id" => $machine->id])->
        whereNull("machine_log_id")->first();

        if ($current_machine_log_input) {
            $current_machine_log_input->machine_log_id = $machineLog->id;
            $current_machine_log_input->production_form_id = $production_form->id ?? -1;
            $current_machine_log_input->save();
        }


        return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "عملیات با موفقیت انجام شد."]);

    }

    public function checkPermission(Machine $machine)
    {

        $result = DashboardController::checkPermissionConditions($machine, EndOfChangeDesignController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}

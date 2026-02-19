<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\Maintenance\Maintenance;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class DeclarationEndOfWarpsController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.machine.declaration_end_of_warps.",
        "enable_status" => [ "026" ],
        "button"        => [ "caption" => "اعلام پایان چله", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.declaration_end_of_warps.",
        "message"       => [ "confirm" => "آیا از پایان چله اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = DeclarationEndOfWarpsController::$info["route"];
        $this->view_path  = DeclarationEndOfWarpsController::$info["view_path"];
    }

    public function submit( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, DeclarationEndOfWarpsController::$info );

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }


        $allocation = $machine->getCurrentAllocation();

        // در تخصیصی جدید طراحی عوض نشده
        if ( ! $allocation->has_design_change ) {
            return redirect()->route( $this->route_path . "index", compact( "machine" ) );

        }

        $machine->setStatus(
            null,
            53002,
            7003001,
            1630
        );

        // حامل چله خالی شود
     //   WarpsRequestForm::setEmptyBeforeWarpsCarrier( $machine );

        // تغییر وضعیت فرم تولید
        ProductionForm::ChangeStatusFromTo(
            $allocation,
            7002001, // در حال تکمیل)
            7002007 // در انتظار استخراج پارچه پایانی
        );


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 340;
        event( new MachineLogEvent( $machine, $machineLog, "در تخصیص جدید طراحی عوض شده" ) );

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function index( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $shift_work_option = Option::get( "shift_work" );

        return view( $this->view_path . "index", compact( "machine", "shift_work_option" ) );

    }

    public function submit_type2( Request $request, Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        // دریافت قطب ها
        $last_row_log = MachineLog::getLastLogWithContour($machine);
        if (
            isset( $last_row_log ) &&
            ! $last_row_log->checkMinContour(
                $request->contour_1_value,
                $request->contour_2_value,
                $request->contour_3_value,
                $request->contour_4_value,
                $request->contour_5_value )
        ) {
            return back()->withErrors( "مقدار قطب ها به درستی وارد نشده است" );
        }

        $allocation = $machine->getCurrentAllocation();

        // کارت تولید رزرو به اصلی تبدیل می شود
        foreach ( $allocation->items as $item ) {
            $item->production_id         = $item->reserve_production_id;
            $item->reserve_production_id = null;
            $item->save();
        }
        $allocation = $machine->getCurrentAllocation();

        if ( $allocation->has_weft_density_change ) {
            // تیکت فنی ارسال شود
            $value = Maintenance::get_density_value_for_maintenance( $allocation );
            Maintenance::AddNew( $allocation, $machine,
                100, $value[0]." , ". $value[1], $value[2]." , ". $value[3],null,null,6003001,6002002
            );

        }

        // تغییر نخ پود داریم یا خیر؟
        if ( $allocation->has_yarn_weft_change ) {
            $machine->setStatus(
                null,
                53002,
                7003027, // در انتظار تغییر نخ پود
                1640, // تغییر نخ پود
                "Fabric_Raw"
            );
        } else {

            // پیدا کردن کد چله
          //  $warps_is_in_warehouse = Warps::warpsExistInWarehouse( $allocation );

            if ( $warps_is_in_warehouse ) {
                // چله در انبار هست
                $machine->setStatus(
                    null,
                    53002,
                    7003028,
                    1617 );

            } else {
                // چله در انبار نیست
                $machine->setStatus(
                    null,
                    53002,
                    7003029,
                    1616 );

            }

            // ران شدن ماژول چله
            WarpsRequestForm::newRequest( $allocation, $machine, $warps_is_in_warehouse );

        }

        // حامل چله خالی شود
      //  WarpsRequestForm::setEmptyBeforeWarpsCarrier( $machine );

        // تغییر وضعیت فرم تولید
        ProductionForm::ChangeStatusFromTo(
            $allocation,
            7002001, // در حال تکمیل)
            $item->status_id = $allocation->has_product_change ?
                7002007 : // در انتظار استخراج پارچه پایانی
                7002008 // در حال تکمیل پارچه پایانی
        );


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 340;
        $machineLog->contour_1_value       = $request->contour_1_value;
        $machineLog->contour_2_value       = $request->contour_2_value;
        $machineLog->contour_3_value       = $request->contour_3_value;
        $machineLog->contour_4_value       = $request->contour_4_value;
        $machineLog->contour_5_value       = $request->contour_5_value;
        $machineLog->shift_work_id         = $request->shift_work_id;

        event( new MachineLogEvent( $machine, $machineLog ,"",false,$last_row_log) );

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, DeclarationEndOfWarpsController::$info );
        if(
            isset($result["error_type"]) &&
            $result["error_type"]=="for_machine_status" &&
            $machine->production_status_id==7003041){ // در حال استخراج پارچه (دستور توقف تولید)
            return "";
        }
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}

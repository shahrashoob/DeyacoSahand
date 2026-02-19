<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class ChangeYarnForChangeDesignController extends Controller{

    public static $info = [
        "route"         => "fabric_raw.machine.change_yarn_for_change_design.",
        "enable_status" => [ "027" ],
        "button"        => [ "caption" => "تغییر نخ پود", "class" => "btn-warning" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.change_yarn_for_change_design.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = ChangeYarnForChangeDesignController::$info["route"];
        $this->view_path  = ChangeYarnForChangeDesignController::$info["view_path"];
    }

    public function index( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $allocation=$machine->getCurrentAllocation();
        $machine_input_share_band=CurrentMachineInput::
        where([
            "allocation_id"=>$allocation->id,
            "goods_kind_id"=>2 // نخ
        ])->get();


        $shift_work_option = Option::get( "shift_work" );

        return view( $this->view_path . "index", compact( "machine", "machine_input_share_band", "shift_work_option" ) );
    }

    public function submit( Request $request, Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }
        $allocation = $machine->getCurrentAllocation();
        $machine_input_share_band=CurrentMachineInput::
        where([
            "allocation_id"=>$allocation->id,
            "goods_kind_id"=>2 // نخ
        ])->get();



        //  (ورودی اشتراکی ) دریافت لات نخ
        $message_lot = "همبافت (لات) وارد شده: ";
        foreach ( $machine_input_share_band as $item ) {

            $lot_code                     = "lot_" . $item->id . "_code";
            if(!isset($request->$lot_code)){
                return back()->withErrors("همه همبافت های مورد نیاز وارد نشده است.");
            }
            $lot_number                   = LotNumber::firstOrCreate( [
                "product_id" => $item->material_id,
                "code"       => $request->$lot_code
            ] );
            $item->lot_number_id=$lot_number->id;
            $item->save();
            $message_lot.=$request->$lot_code." - ";
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

        // پیدا کردن کد چله
//        $warps_is_in_warehouse = Warps::warpsExistInWarehouse( $allocation );

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



        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 350;
        $machineLog->contour_1_value       = $request->contour_1_value;
        $machineLog->contour_2_value       = $request->contour_2_value;
        $machineLog->contour_3_value       = $request->contour_3_value;
        $machineLog->contour_4_value       = $request->contour_4_value;
        $machineLog->contour_5_value       = $request->contour_5_value;
        $machineLog->shift_work_id         = $request->shift_work_id;
        event( new MachineLogEvent( $machine, $machineLog,$message_lot ,false,$last_row_log) );

        // لات پارچه
        $allocation = $machine->getCurrentAllocation();
        FabricRaw:: ChangeLot( $allocation );


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );


    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, ChangeYarnForChangeDesignController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}


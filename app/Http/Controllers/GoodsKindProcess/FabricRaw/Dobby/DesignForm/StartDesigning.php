<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\DesignForm;

use App\Events\Fabric_Raw\FabricRawDesignFormLogEvent;
use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\Fabric_Raw\Machine;
use App\Models\GoodsKindProcess\Fabric_Raw\Desing\FabricRawDesignForm;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\MachineLog;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class StartDesigning extends Controller {
    public static $info = [
        "route"         => "fabric_raw.design_form.start_designing.",
        "enable_status" => [ "001" ],
        "button"        => [ "caption" => "شروع طراحی", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.design_form.start_designing.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.design_form.dashboard.";

    public function __construct() {
        $this->route_path = StartDesigning::$info["route"];
        $this->view_path  = StartDesigning::$info["view_path"];
    }

    public function index( FabricRawDesignForm $design_form ) {
        $result = $this->checkPermission( $design_form );
        if ( $result != "" ) {
            return $result;
        };

        $radio_option = [
            [ "value" => 1, "label" => "بله", "id" => "yes" ],
            [ "value" => 0, "label" => "خیر", "id" => "no" ]
        ];

        return view( $this->view_path . "index", compact( "design_form", "radio_option" ) );
    }


    public function submit( Request $request, FabricRawDesignForm $design_form ) {
        $result = $this->checkPermission( $design_form );
        if ( $result != "" ) {
            return $result;
        };

        if ( isset( $request->design_available ) && $request->design_available == 1 ) { // A=> T

            if ( isset( $request->need_to_convert ) && $request->need_to_convert == 1 ) { // D => T
                $design_form->status_id = DashboardController::$perfix_design_form_status_code . "002";
            } elseif ( isset( $request->need_to_convert ) && $request->need_to_convert == 0 ) { // D => F
                $design_form->status_id = DashboardController::$perfix_design_form_status_code . "004";


                if ( $design_form->machine->production_status_id == \App\Http\Controllers\GoodsKindProcess\Fabric_Raw\Dobby\Machine\DashboardController::$perfix_production_status_code . "020" ) {
                    $machineLog                        = new MachineLog();
                    $machineLog->machine_event_type_id = 240;

                    $design_form->machine->on_status_id          = 53002;
                    $design_form->machine->machine_off_reason_id = 1602;
                    $design_form->machine->production_status_id  = \App\Http\Controllers\GoodsKindProcess\Fabric_Raw\Dobby\Machine\DashboardController::$perfix_production_status_code . "004";
                    $design_form->machine->save();


                    event( new MachineLogEvent( $design_form->machine, $machineLog ) );
                }

            } else {
                return back()->withErrors( "لطفا به سوالات پاسخ دهید." );
            }

        } elseif ( isset( $request->design_available ) && $request->design_available == 0 ) { // A => F

            // C => F
            // پیدا کردن کد چله
//            $design_form->warps_is_in_warehouse = Warps::warpsExistInWarehouse( $design_form->allocation );

            if ( $design_form->warps_is_in_warehouse ) { // C => T

                // چله در انبار هست
                $design_form->status_id = DashboardController::$perfix_design_form_status_code . "003";
                // ران شدن ماژول چله

                WarpsRequestForm::newRequest( $design_form->allocation, $design_form->machine, $design_form->warps_is_in_warehouse );

            } else {
                // چله در انبار نیست
                $design_form->status_id = DashboardController::$perfix_design_form_status_code . "005";

            }


        } else {
            return back()->withErrors( "لطفا به سوالات پاسخ دهید." );
        }

        $design_form->design_available = $request->design_available;
        $design_form->need_to_convert  = $request->need_to_convert;
        $design_form->it_has_pinning   = $request->it_has_pinning;

        $design_form->save();
        event( new FabricRawDesignFormLogEvent( $design_form ) );

        return redirect()->route( $this->dashboard_route . "index" );

    }

    public function checkPermission( FabricRawDesignForm $design_form ) {


        $result = DashboardController::checkPermissionConditions( $design_form, StartDesigning::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }


}

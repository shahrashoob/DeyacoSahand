<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class EndOfWarpsExtractionController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.machine.end_of_warps_extraction.",
        "enable_status" => [ "041" ],
        "button"        => [ "caption" => "پایان استخراج چله", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.end_of_warps_extraction.",
        "message"       => [ "confirm" => "آیا از پایان استخراج چله اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = EndOfWarpsExtractionController::$info["route"];
        $this->view_path  = EndOfWarpsExtractionController::$info["view_path"];
    }

    public function index( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }


        $shift_work_option = Option::get( "shift_work" );

        return view( $this->view_path . "index", compact( "machine", "shift_work_option" ) );
    }

    public function submit( Request $request, Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        // آیا فرم تحویل چله به انبار با وضعیت تایید نشده وجود دارد
        $form = Form::where( [
            "form_type_id"      => 305,
            "applicant_type_id" => 10, // ماشین
            "applicant_id"      => $machine->id
        ] )->
        where( "status_id", "!=", 500000200 )-> // تایید شده
        first();
        if ( isset( $form ) ) {
            $message = "کاربر گرامی لطفا قبل از ثبت 'پایان استخراج چله' نسبت به تحویل " .
                       $form->item[0]->product->fullCaption() .
                       " به انبار و اخذ تایید انباردار اقدام نمایید. <br/> عدم تایید انبار دار به منزله عدم پایان استخراج چله می باشد.";

            return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->
            withErrors( $message );

        }

        $allocation = $machine->getCurrentAllocation();
        // در تخصیصی جدید طراحی عوض شده
        if ( $allocation->has_design_change ) {

            $machineLog                        = new MachineLog();
            $machineLog->machine_event_type_id = 495;
            event( new MachineLogEvent( $machine, $machineLog ) );

            return ( new DeclarationEndOfWarpsController() )->submit( $machine );
        }
        else{

            return redirect()->route( $this->route_path . "index", compact( "machine" ) );

        }


    }

    public function submit_type2( Request $request, Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 495;
        event( new MachineLogEvent( $machine, $machineLog ) );

        return ( new DeclarationEndOfWarpsController() )->submit_type2( $request, $machine );
    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, EndOfWarpsExtractionController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}

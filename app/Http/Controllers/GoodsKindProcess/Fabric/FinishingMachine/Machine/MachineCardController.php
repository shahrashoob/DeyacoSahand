<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine;

use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use function back;
use function view;

class MachineCardController extends Controller {
    //
    public static $info = [
        "route"         => "fabric.finishing_machine.machine.machine_card.",
        "enable_status" => ["001","002","003","901","902","903","904","905" ],
        "button"        => [ "caption" => " پرینت کارت ماشین ", "class" => "btn-info" ],
        "view_path"     => "goods_kind_process.fabric.finishing_machine.machine.machine_card.",
        "message"       => [ "confirm" => "آیا از پرینت کارت اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        $this->route_path = MachineCardController::$info["route"];
        $this->view_path  = MachineCardController::$info["view_path"];
    }

    public function submit( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        \App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\MachineCardController::Print( $machine );
        return back()->with( [ "success" => "پرینت کارت ماشین برای چاپ به پرینتر پیش فرض سیستم ارسال شد." ] );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, MachineCardController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}

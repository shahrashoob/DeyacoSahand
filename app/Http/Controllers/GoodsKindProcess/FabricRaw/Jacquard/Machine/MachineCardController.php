<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

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
        "route"         => "fabric_raw.jacquard.machine.machine_card.",
        "enable_status" => [
            "001",
            "002",
            "003",
            "004",
            "005",
            "006",
            "007",
            "008",
            "009",
            "010",
            "011",
            "012",
            "013",
            "014",
            "015",
            "016",
            "017",
            "018",
            "019",
            "020",
            "021",
            "022",
            "023",
            "024",
            "025",
            "026",
            "027",
            "028",
            "029",
            "030",
            "031",
            "032",
            "033",
            "034",
            "035",
            "036",
            "037",
            "038",
            "039",
            "040",
            "041",
            "042",
            "043",
            "044",
            "045",
            "046",
            "047",
            "048",
            "049",
            "050",
            "051",
            "052"
        ],
        "button"        => [ "caption" => " پرینت کارت ماشین ", "class" => "btn-info" ],
        "view_path"     => "goods_kind_process.fabric_raw.jacquard.machine.machine_card.",
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


        return back()->with( [ "success" => "پرینت کارت ماشین برای چاپ به پرینتر پیش فرض سیستم ارسال شد." ] );

    }

    public static function Print(Machine $machine) {
        $company_name  = Setting::getStringValue( "company_name" );
        $software_name = Setting::getStringValue( "software_name" );

        $static_ip = Setting::getStringValue( "static_ip" );
        $local_ip  = url( "" );

        $url = route( "Machine_ShortLink", [ $machine, $machine->code ] );

        //اگر شرکت دارای ای پی بیرونی و ای پی لوکال باشد، لینک را بر روی ای پی بیرونی تنظیم می کنیم.
        if ( $static_ip != "" ) {
            $url = \Illuminate\Support\Str::replace( $local_ip, $static_ip, $url );
        }

        $qr = QrCode::size( 180 )->generate( $url );

        $view_path=MachineCardController::$info["view_path"];
        $html[0] = view( $view_path . "_head" )->render() ;

        $html[0] =$html[ 0 ] . view( $view_path . "_info", compact( "machine", "qr", "software_name", "company_name" ) )->render();


        $html[0] = $html[ 0 ] . view( $view_path . "_footer" )->render();

        $current_worker=Worker::find(Auth::id());

        $print_file = PrinterFile::create( [
            "user_id"      => $current_worker->id,
            "filename"     => $machine->getCode() . ".pdf",
            "status_id"    => 305001, // در انتظار دانلود
            "is_landscape" => 0,
            "printer_id"   => $current_worker->default_label_printer_id
        ] );
        Pdf::labelPrinter( $html,
            "P",
            $machine->getCode() . ".pdf", [
                95,
                123
            ],
            $print_file
        );
    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, MachineCardController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}

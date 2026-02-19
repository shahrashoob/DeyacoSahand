<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Utility\Message;
use App\Models\Utility\Option;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function back;
use function redirect;
use function view;

class EditLogController extends Controller {
    public static $info = [
        "route" => "fabric_raw.machine.edit_log.",
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
            "042"
        ],
        "button" => [ "caption" => "ویرایش لاگ ماشین", "class" => "btn-info" ],
        "view_path" => "goods_kind_process.fabric_raw.machine.edit_log.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = EditLogController::$info["route"];
        $this->view_path  = EditLogController::$info["view_path"];
    }

    public function index( Machine $machine, MachineLog $machine_log ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $shift_work_option = Option::get( "shift_work", $machine_log->shift_work_id );

        return view( $this->view_path . "index", compact( "machine", "machine_log", "shift_work_option" ) );

    }

    public function submit( Request $request, Machine $machine, MachineLog $machine_log ) {

        $new_message =Carbon::now()->format( 'H:i:s Y/m/d ' ).":". "قطب و شیف کاری ویرایش شد"."-".Auth::user()->firstname." ".Auth::user()->lastname . "-(" .
                             ($machine_log->shift_work->caption??"---") . "-" .
                       $machine_log->contour_1_value . " - " .
                       $machine_log->contour_2_value . " - " .
                       $machine_log->contour_3_value . " - " .
                       $machine_log->contour_4_value . " - " .
                       $machine_log->contour_5_value . " ) ";

        if ( $machine_log->message_id ) {
             $machine_log->message->text.="<br/>".$new_message;
            $machine_log->message->save();
        }
        else{
            $msg = Message::create(
                [
                    "text"            => $new_message,
                    "other_id"        => $machine_log->id,
                    "message_type_id" => 160
                ]
            );
            $machine_log->message_id=$msg->id;
        }
        $machine_log->shift_work_id=$request->shift_work_id;
        $machine_log->contour_1_value=$request-> contour_1_value;
        $machine_log->contour_2_value =$request-> contour_1_value;
        $machine_log->contour_3_value =$request-> contour_1_value;
        $machine_log->contour_4_value =$request-> contour_1_value;
        $machine_log->contour_5_value=$request-> contour_1_value;
        $machine_log->save();

        return redirect()->route("fabric_raw.machine.log.index",$machine)->with(["success"=>"ویرایش با موفقیت انجام شد"]);
    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, LogController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}

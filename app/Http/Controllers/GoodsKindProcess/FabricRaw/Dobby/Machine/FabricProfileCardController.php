<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\Machine\Machine;
use App\Models\Utility\Pdf;
use function back;
use function view;

class FabricProfileCardController extends Controller {
    //
    public static $info = [
        "route"         => "fabric_raw.machine.fabric_profile_card.",
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
        "button"        => [ "caption" => " کارت مشخصات پارچه ", "class" => "btn-info" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.fabric_profile_card.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = FabricProfileCardController::$info["route"];
        $this->view_path  = FabricProfileCardController::$info["view_path"];
    }

    public function index( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $allocation            = $machine->getCurrentAllocation();
        $productionFromItemLot = FabricRaw::getCurrentLot( $allocation, true );
        if ( ! $allocation ) {
            return back()->withErrors( "امکان پرینت کارت تولید وجود ندارد." );
        }
        $html[0]="";
        foreach ( $allocation->items as $item ) {
            $product    = $item->product;
            $production = $item->production;
            $band_code  = $item->band_code;
            $html[]     = view( $this->view_path . "_band_info", compact( "machine", "allocation", "product", "band_code", "production", "productionFromItemLot" ) )->render();
        }


        $html[0]                    = view( $this->view_path . "_head" )->render() . $html[0];
        $html[ count( $html ) - 1 ] = $html[ count( $html ) - 1 ] . view( $this->view_path . "_footer" )->render();

        //  return ($html);
        return Pdf::createAsHtml( $html, "P", $allocation->id . "_" . $machine->fullCaption(), "A5", " " );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, FabricProfileCardController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}

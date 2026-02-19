<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralMaterialDeliveryConfirmationController;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralMaterialDeliveryRejectController;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\GoodsKindProcess\Fabric_Raw\Fabric_Raw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class MaterialDeliveryConfirmationController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.material_delivery_confirmation.",
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
            "052",
            "053",
            "054"
        ],
        "button"        => [ "caption" => "تایید تحویل مواد اولیه از انبار", "class" => "btn-success" ],
        "view_path"     => "goods_kind_process.fabric_raw.jacquard.machine.material_delivery_confirmation.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        $this->route_path = MaterialDeliveryConfirmationController::$info["route"];
        $this->view_path  = MaterialDeliveryConfirmationController::$info["view_path"];
    }

    public function index( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->index(  $machine );


    }

    public function submit( Request $request, Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->submit( $request, $machine );
    }


    public function getGeneralController() {
        $publicController                  = new GeneralMaterialDeliveryConfirmationController();
        $publicController->route_path      = $this->route_path;
        $publicController->dashboard_route = $this->dashboard_route;

        return $publicController;
    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, MaterialDeliveryConfirmationController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}

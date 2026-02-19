<?php

namespace App\Http\Controllers\Warehouse\WarehouseHandling;

use App\Events\Form\PackingLogEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\Product\RejectProductLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Events\Warehouse\WarehouseHandlingEvent;
use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\PrintQRController;
use App\Http\Controllers\Utility\Script\Script1012Controller;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRawGrading;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\RejectProduct\RejectProductForm;
use App\Models\Post\Post;
use App\Models\Utility\Notification\SMSMessage;
use App\Models\Utility\Option;
use App\Models\Utility\QueueOfLargeOperation;
use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandling;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandlingPackingForm;
use App\Models\Warehouse\WarehouseProduct;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckPackingFormController extends Controller
{
    public static $info = [
        "route" => "wh.warehouse_handling.check_packing_form.",
        "enable_status" =>  ["301", "302", "304", "311", "312", "313", "322", "323"],
        "button" => ["caption" => "استعلام بسته بندی", "class" => "btn-info"],
        "view_path" => "warehouse.warehouse_handling.check_packing_form.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_path = "wh.warehouse_handling.dashboard.index";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view_path"];
    }

    public function index(WarehouseHandling $warehouse_handling)
    {

        $result = $this->checkPermission($warehouse_handling);
        if ($result != "") {
            return $result;
        }

        return view($this->view_path . "index", compact("warehouse_handling"));
    }

    public function submit(WarehouseHandling $warehouse_handling, Request $request)
    {
        $result = $this->checkPermission($warehouse_handling);
        if ($result != "") {
            return $result;
        }
        $message = "";
        $code = "DCPK/" . $request->packing_code;
        $packing_form = PackingForm::where("code", $code)->first();
        if (!$packing_form) {
            $message = "بسته بندی با کد " . $code . " در سامانه وجود ندارد.";
            return back()->withErrors($message);
        } else {
            $packing_form_warehouse_product = WarehouseHandlingPackingForm::
            where("warehouse_handling_id", $warehouse_handling->id)->
            where("packing_form_id", $packing_form->id)->
            first();
            if ($packing_form_warehouse_product) {
                $message = " وضعیت بسته بندی $code در انبار گردانی " . "<b>" . $packing_form_warehouse_product->status->caption . "</b>" . " می باشد.";
            } else {
                $message = "بسته بندی با کد " . $code . " در انبار گردانی وجود ندارد.";
                return back()->withErrors($message);
            }
        }
        return back()->with(["success" => $message]);
    }

    public function checkPermission(WarehouseHandling $warehouseHandling)
    {

//        $result = DashboardController::checkPermissionConditions($warehouseHandling, self::$info);
//        if (!$result["result"]) {
//            return back()->withErrors($result["message"]);
//        }

        return "";
    }
}

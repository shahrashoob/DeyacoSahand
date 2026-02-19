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

class EndOfHandlingController extends Controller
{
    public static $info = [
        "route" => "wh.warehouse_handling.end_of_handling.",
        "enable_status" => ["302"],
        "button" => ["caption" => "پایان انبارگردانی", "class" => "btn-primary"],
        "message" => ["confirm" => "آیا از پایان انبارگردانی اطمینان دارید؟"],
        "view_path" => "warehouse.warehouse_handling.add_packing_form.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_path = "wh.warehouse_handling.dashboard.index";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view_path"];
    }

    public function submit(WarehouseHandling $warehouse_handling)
    {
        $result = $this->checkPermission($warehouse_handling);
        if ($result != "") {
            return $result;
        }
//        if (WarehouseHandling::HasErrorInPackingForm($warehouse_handling)) {
//            return back()->withErrors("لطفا ابتدا موارد قرمز رنگ را بررسی فرمایید و سپس پایان انبارگردانی را انجام دهید.
//
//            ");
//        }
        $warehouse_handling->status_id = 524000303; // در حال پردایش اولیه
        $warehouse_handling->end_datetime=now();
        $warehouse_handling->save();
        event(new WarehouseHandlingEvent($warehouse_handling, 524000302));

        $data["warehouse_handling_id"] = $warehouse_handling->id;
        QueueOfLargeOperation::AddToQueue($data, 201);

        return back()->with(["success" => "پایان انبارگردانی با موفقیت ثبت گردید"]);
    }

    public function checkPermission(WarehouseHandling $warehouseHandling)
    {

        $result = DashboardController::checkPermissionConditions($warehouseHandling, self::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}

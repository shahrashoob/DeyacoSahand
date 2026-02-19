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

class ConfirmStep3Controller extends Controller
{
    public static $info = [
        "route" => "wh.warehouse_handling.confirm_step3.",
        "enable_status" => ["313"],
        "button" => ["caption" => "تایید انبارگردانی مرحله سوم", "class" => "btn-primary"],
        "message" => ["confirm" => "آیا از تایید انبارگردانی اطمینان دارید؟"],
        "view_path" => "",

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
        $warehouse_handling->status_id = 524000321; // در انتظار پردازش نهایی
        $warehouse_handling->save();
        event(new WarehouseHandlingEvent($warehouse_handling, 524000306));


        $data["warehouse_handling_id"] = $warehouse_handling->id;
        QueueOfLargeOperation::AddToQueue($data, 202); // پردازش نهایی


        return back()->with(["success" => "تایید انبارگردانی با موفقیت ثبت گردید"]);
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

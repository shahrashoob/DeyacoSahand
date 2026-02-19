<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\Matthys\ProductionCard;

use App\Events\Fabric_Raw\FabricRawDesignFormLogEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Post\Post;
use App\Models\Post\PostUser;
use App\Models\Production\Production;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\Setting;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AllocationCancelController extends Controller
{

    public static $info = [
        "route" => "warps.matthys.allocation_cancel.",
        "enable_status" => ["001", "002", "003"],
        "next_status" => [],
        "button" => ["caption" => "کنسل کردن تخصیص ", "class" => "btn-danger"],
        "view_path" => "goods_kind_process.warps.production_card.allocation_cancel."
    ];

    public function index(Allocation $allocation, Production $production)
    {

        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }

        $result_cancel = \App\Http\Controllers\GoodsKindProcess\Warps\ProductionCard\AllocationCancelController::GetIndex($allocation, $production);
        if ($result_cancel["result"]) {
            return back()->with(["success" => $result_cancel["message"]]);
        } else {
            return back()->withErrors($result_cancel["error"]);
        }

    }

    public function checkPermission(Production $production)
    {

        $result = DashboardController::checkPermissionConditions($production,
            \App\Http\Controllers\GoodsKindProcess\Warps\ProductionCard\AllocationCancelController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }


}

<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\KarlMayer\ProductionCard;


use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\Production\Production;

class AllocationCancelController extends Controller
{

    public static $info = [
        "route" => "warps.karl_mayer.allocation_cancel.",
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

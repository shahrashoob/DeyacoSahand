<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\Post\PostStatus;
use App\Models\Production\Production;
use App\Models\Production\ProductionPackingType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class ProductionCardController extends Controller
{
    //goods_kind_process/fabric_raw/production_card
    public static $perfix_status_code = "7001";
    public $view_path = "goods_kind_process.fabric_raw.production_card.";
    public $route_path = "goods_kind_process.fabric_raw.production_card.";

    public function __construct()
    {

    }

    public function view_card(Production $production,$back_url_type="")
    {

        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        };

        $controller_info = ProductionCardController::get_controller_info();
        MachineAllocation::where(["production_id" => $production->id, "status_id" => 5310005])->delete();



        return view($this->view_path . "view_card",
            compact("production", "controller_info","back_url_type" ));
    }

    public static function NormalAmount(Production $production)
    {
        $normal_amount = null;
        if(!$production->normal_amount) {
            if ($production->parent_production) {
                $packing_type = ProductionPackingType::where("production_id", $production->parent_production_id)->first();

                if ($packing_type->packing_type && $packing_type->packing_type->normal_amount) {
                    $normal_amount = $packing_type->packing_type->normal_amount;
                }
            } else {
                $packing_type = ProductionPackingType::where("production_id", $production->id)->first();
                if ($packing_type->packing_type && $packing_type->packing_type->normal_amount) {
                    $normal_amount = $packing_type->packing_type->normal_amount;
                }
            }

            $product = $production->product;
            $normal_amount = $normal_amount / $product->weight;

            if ($product->frame_ratio_unit2 && $product->sub_unit2_id == 1400) {
                $normal_amount = round($normal_amount / $product->frame_ratio_unit2) * $product->frame_ratio_unit2;
            }
            if($normal_amount) {
                $production->normal_amount = round($normal_amount,2);
            }
            $production->save();
        }

        return $production;
    }
    public static function get_controller_info()
    {
        return $controller_info = [
            "01" => ProductionCard\MachineAllocationController::$info,
            "03" => ProductionCard\FinishedAllocationController::$info,
            "04" => ProductionCard\TerminateProductionController::$info,
        ];
    }

    public static function get_controller_info_for_permission()
    {
        return $controller_info = [
            "01" => ProductionCard\MachineAllocationController::$info,
            "02" => ProductionCard\AllocationCancelController::$info,
            "03" => ProductionCard\FinishedAllocationController::$info,
            "04" => ProductionCard\TerminateProductionController::$info,
        ];
    }

    public static function checkPermissionConditions(Production $production, $info = false)
    {


        $allowed_status_ids = PostStatus::getAllowedStatus();

        if (!in_array($production->waiting_status_id, $allowed_status_ids)) {
            return [
                "result" => false,
                "message" => "شما اجازه مشاهده کارت تولید را ندارید.",
            ];
        }


        if ($info != false) {
            foreach ($info["enable_status"] as &$value) {
                $value = ProductionCardController::$perfix_status_code . $value;
            }
            unset($value);
            if (!in_array($production->waiting_status_id, $info["enable_status"])) {
                return [
                    "result" => false,
                    "message" => "وضعیت کارت تولید جهت عملیات نامعتبر است",
                ];
            }

            $post_user = Auth::user()->posts->first();
            if (!$post_user->checkButtonPermission($info["route"] . "index")) {
                return [
                    "result" => false,
                    "message" => "دسترسی  عملیات برای شما تعریف نشده است",
                ];
            }
        }


        return [
            "result" => true,
        ];

    }

    public function checkPermission(Production $production)
    {
        $result = ProductionCardController::checkPermissionConditions($production);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }
    }
}

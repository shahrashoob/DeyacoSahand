<?php

namespace App\Http\Controllers\Warehouse\WarehouseHandling;

use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\Utility\SmartObject;
use App\Models\Utility\Status;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandling;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandlingForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    var $view_path = "warehouse.warehouse_handling.dashboard.";
    var $route_path = "wh.warehouse_handling.dashboard.";

    public function index(Request $request)
    {

        $list = WarehouseHandling::orderByDesc("id")->paginate(30);

        return view($this->view_path . "index", compact("list"));
    }

    public function view(WarehouseHandling $warehouse_handling)
    {

        $controller_info = self::get_controller_info();
        $controller_info_public = self::controller_info_public();
        $status_list = $warehouse_handling->packing_forms()->
        groupBy("status_id")->
        selectRaw("count(id) as count , status_id")->
        pluck("count", "status_id");

        $smart_object = null;
        // در صورتی که باسکول انتخاب شده است، باید اتصال برقرار باشد.
        if ($warehouse_handling->use_of_smart_object) {
            /************************************/
            // گرفتن باسکول
            $url_scale = route("hr.personal.select_smart_object", [2, "wh.warehouse_handling.dashboard.view", $warehouse_handling]);
            $result_smart_object = SmartObject::GetScaleValue();
            if (!$result_smart_object["result"]) {
                if (isset($result_smart_object["warning"])) {
                    return redirect()->route($url_scale)->
                    withErrors("با توجه به اینکه برای شما چند باسکول  تعریف شده است، لطفا یکی از باسکول ها را انتخاب نمایید.");
                } else {
                    return redirect()->back()->withErrors($result_smart_object["error"]);
                }
            }
            $smart_object_value = $result_smart_object["smart_object_value"];
            $smart_object = $result_smart_object["smart_object"];
            /**********************************/
            if (!$smart_object) {
                return back()->withErrors("امکان اتصال به اشیاء هومشند برای شما وجود ندارد.");
            }
        }

        $count_all_packing_form = $warehouse_handling->packing_forms()->count();

        $warehouse_handling_form = WarehouseHandlingForm::where("warehouse_handling_id", $warehouse_handling->id)->
        orderByDesc("id")->first();

        $packing_form_count = $warehouse_handling->packing_forms()->
        whereNotIn("status_id", [524000403, 524000407])-> // خوانده نشده ها را حذف می کنیم.
        count();

        $packing_form_reading_list = $warehouse_handling->packing_forms()->
        pluck("packing_form_id", "packing_form_id")->toArray();

        $result_permission_packing_form = DashboardController::checkPermissionConditions($warehouse_handling, AddPackingFromController::$info);;
        $permission_add_packing_form = $result_permission_packing_form["result"];
        $packing_form_data = [];
        if (!in_array($warehouse_handling->status_id, AddPackingFromController::$info["allowed_status_ids"])) {
            $permission_add_packing_form = false; // اگر در وضعیت نامعتبر است، اجازه مشاهده نداشته باشد.
        }

        $entry_with_pin = $warehouse_handling->warehouse->allow_entry_with_pin?1:0;
        if ($permission_add_packing_form) {
            $packing_form_data = PackingForm::
            where("warehouse_id", $warehouse_handling->warehouse_id)->
            where("warehouse_status_id", 4201)->
            where("packing_forms.status_id", 7007003)->
            selectRaw("packing_forms.id,packing_forms.code,pin1")->
            get()->keyBy(
                $entry_with_pin ? "pin1" : "code"
            );
            // pluck("packing_forms.id", "packing_forms.id")->toArray();

        }


        return view($this->view_path . "view", compact(
            "warehouse_handling", "permission_add_packing_form",
            "packing_form_count", "warehouse_handling_form", "controller_info", "controller_info_public", "status_list",
            'count_all_packing_form', "packing_form_data", "packing_form_reading_list",
            "smart_object","entry_with_pin"
        ));
    }

    public function packing_form_list(WarehouseHandling $warehouse_handling, $status_id)
    {

        $status = Status::find($status_id);
        if (!$status || $status->status_type_id != 5240) {
            return back()->withErrors("وضعیت انبارگردانی نامعتبر است.");
        }
        $list = $warehouse_handling->packing_forms()->where("status_id", $status->id)->paginate();

        if ($status->status_type_id != 5240) {
            return back()->withErrors("وضعیت انبارگردانی نامعتبر است.");
        }

        $permission_add_packing_form = true;
        if (!in_array($warehouse_handling->status_id, EndOfReviewController::$info["allowed_status_ids"])) {
            $permission_add_packing_form = false; // اگر در وضعیت نامعتبر است، اجازه بررسی نداشته باشد.
        }

        return view($this->view_path . "packing_form_list", compact("warehouse_handling", "status", "list", "permission_add_packing_form"));
    }


    public static function get_controller_info()
    {
        $controller_info = [
//            "01" => AddPackingFromController::$info,
            "02" => EndOfHandlingController::$info,
            "03" => ConfirmStep1Controller::$info,
            "04" => ConfirmStep2Controller::$info,
            "05" => ConfirmStep3Controller::$info,
            "06" => RejectController::$info,
            "07" => EndOfReviewController::$info,
        ];

        return $controller_info;
    }

    public static function controller_info_public()
    {
        $controller_info = [
            "01" => PrintController::$info,
            "02" => CheckPackingFormController::$info,
        ];

        return $controller_info;
    }

    public static function checkPermissionConditions(WarehouseHandling $warehouseHandling, $info = false, $other_status = false)
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission($info["route"] . "index")) {
            return [
                "result" => false,
                "message" => "دسترسی  عملیات برای شما تعریف نشده است",
            ];
        }

        if (!in_array(\Illuminate\Support\Str::substr($warehouseHandling->status_id, -3), $info["enable_status"])) {
            return [
                "result" => false,
                "message" => "وضعیت انبارگردانی جهت اجرای ماژول نامعتبر است.",
            ];
        }

        return [
            "result" => true,
        ];

    }

}

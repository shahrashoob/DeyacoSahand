<?php

namespace App\Http\Controllers\LineProductStation\Reservoir;

use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingFormLog;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Carrier\CarrierLog;
use App\Models\LineProduct\Reservoir\Reservoir;
use App\Models\LineProduct\Reservoir\ReservoirType;
use App\Models\Post\PostStatus;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DashboardController extends Controller
{
    //
    public $view_path = "line_product_station.reservoir.dashboard.";
    public $route_path = "line_product_station.reservoir.dashboard.";

    public function index(Reservoir $reservoir)
    {
        return $this->DCRE_ShortLink($reservoir, $reservoir->getRandom());
    }

    public function log(Reservoir $reservoir)
    {
        $logs=PackingFormLog::where("packing_form_id",$reservoir->packing_form_id)->paginate(15);
        return view($this->view_path . 'log', compact('reservoir','logs'));
    }

    public function DCRE_ShortLink(Reservoir $reservoir, $key)
    {
        if ($reservoir->random != $key) {
            return back()->withErrors("صفحه مورد نظر برای مخزن یافت نشد.");
        }
        return view($this->view_path . "short_link_view", compact("reservoir", "key"));
    }

    public static function checkPermissionConditions(Reservoir $reservoir, $info = false)
    {

//        $allowed_status_ids = PostStatus::getAllowedStatus();
//        if (!in_array($packing_form->status_id, $allowed_status_ids)) {
//            return [
//                "result" => false,
//                "message" => "شما اجازه مشاهده فرم را ندارید.",
//            ];
//        }

//        if ($info != false) {
//            foreach ($info["enable_status"] as &$value) {
//                $value = PackingFormController::$perfix_packing_status_code . $value;
//            }
//            unset($value);
//            if (!in_array($packing_form->status_id, $info["enable_status"])) {
//                return [
//                    "result" => false,
//                    "message" => "وضعیت فرم بسته بندی جهت عملیات نامعتبر است",
//                ];
//            }
//            $post_user = Auth::user()->posts->first();
//            if (!$post_user->checkButtonPermission($info["route"] . "index")) {
//                return [
//                    "result" => false,
//                    "message" => "دسترسی  عملیات برای شما تعریف نشده است",
//                ];
//            }
//        }

        return [
            "result" => true,
        ];

    }

    public function checkPermission(Reservoir $reservoir)
    {
        $result = self::checkPermissionConditions($reservoir);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

//        $result_check = PackingForm::CheckChangePackingIsOK($packing_form->packing_form_parent);
//
//        if (!$result_check["result"]) {
//            return back()->withErrors($result_check["error"]);
//
//        }

    }
}
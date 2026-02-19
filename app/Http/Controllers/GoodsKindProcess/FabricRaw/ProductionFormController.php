<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRawGrading;
use App\Models\Post\PostStatus;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class ProductionFormController extends Controller
{
    public static $perfix_status_code = "7002";
    var $view_path = "goods_kind_process.fabric_raw.production_form.";
    var $route_path = "fabric_raw.production_form.";

    public function __construct() {

        View::share( "perfix_status_code", ProductionFormController::$perfix_status_code );
        View::share( "view_path", $this->view_path );
        View::share( "route_path", $this->route_path );
    }

    public function view( ProductionForm $production_form ) {

        $result=  $this->checkPermission( $production_form );
        if ( $result != "" ) {
            return $result;
        }

        //حذف همه رکوردهای معلق درجه بندی
        FabricRawGrading::where( [
            "production_form_id" => $production_form->id,
            "status_id"          => 7006001
        ] )->delete();

        $controller_info       = ProductionFormController::get_controller_info();
        $controller_info_bands = ProductionFormController::get_controller_info_bands();

        $special_condition = [];

        ProductionForm::UpdateVersion($production_form);

        return \view( $this->view_path . "view", compact( "production_form", "controller_info", "special_condition", "controller_info_bands" ) );

    }

    public static function checkPermissionConditions( ProductionForm $production_form, $info = false ) {

        if ( $info != false ) {
            foreach ( $info["enable_status"] as &$value ) {
                $value = ProductionFormController::$perfix_status_code . $value;
            }
            unset( $value );
            if ( ! in_array( $production_form->status_id, $info["enable_status"] ) ) {
                return [
                    "result"  => false,
                    "message" => "وضعیت فرم تولید جهت عملیات نامعتبر است",
                ];
            }

            $post_user = Auth::user()->posts->first();
            if ( ! $post_user->checkButtonPermission( $info["route"] . "index" ) ) {
                return [
                    "result"  => false,
                    "message" => "دسترسی  عملیات برای شما تعریف نشده است",
                ];
            }
        }

        return [
            "result" => true,
        ];

    }

    public function checkPermission( ProductionForm $production_form ) {
        $result = ProductionFormController::checkPermissionConditions( $production_form );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

    }

    public static function get_controller_info() {
        return $controller_info = [
            "01" => FabricRaw\ProductionForm\FabricExtractionController::$info,
            "02" => FabricRaw\ProductionForm\ExtractionItemController::$info,
//            "02" => FinishingFabricExtractionController::$info,
//            "03" => FabricExtractionForStopOrderController::$info,
        ];
    }

    public static function get_controller_info_bands() {
        return $controller_info_bands = [
//            "01" => GradingController::$info,
//            "02" => GradingCancelController::$info,
        ];
    }

    public static function get_controller_info_all() {
        return $controller_info = [
            "01" => FabricRaw\ProductionForm\FabricExtractionController::$info,
            "02" => FabricRaw\ProductionForm\ExtractionItemController::$info,
//            "02" => GradingController::$info,
//            "03" => FinishingFabricExtractionController::$info,
//            "04" => FabricExtractionForStopOrderController::$info,
//            "05" => GradingCancelController::$info,
        ];
    }
}

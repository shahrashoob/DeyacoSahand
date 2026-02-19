<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionForm;

use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Production\ProductionFormItem;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;

class GradingCancelController extends Controller {
    public static $info = [
        "route"                              => "fabric_raw.production_form.grading_cancel.",
        "enable_status"                      => [ "004","003", ],
        "production_form_item_enable_status" => [  "004" ],
        "button"                             => [ "caption" => "حذف درجه بندی", "class" => "btn-danger" ],
        "message"                            => [ "confirm" => "آیا از حذف درجه بندی های انجام شده اطمینان دارید؟" ],
        "view_path"                          => "goods_kind_process.fabric_raw.production_form.grading_cancel.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.production_form.dashboard.";

    public function __construct() {
        $this->route_path = GradingCancelController::$info["route"];
        $this->view_path  = GradingCancelController::$info["view_path"];
    }


    public function submit( Request $request, ProductionFormItem $production_form_item ) {

        $result = $this->checkPermission( $production_form_item );
        if ( $result != "" ) {
            return $result;
        }

        $grading_list = $production_form_item->fabric_grading()->where( "packing_form_item_id", "!=", 0 )->get();

        if ( count( $grading_list ) > 0 ) {
            $message = "با توجه به اینکه برای ردیف (های): ";
            foreach ( $grading_list as $item ) {
                $message .= $item->code . ",";
            }
            $message .= " حامل انتخاب شده است، امکان حذف درجه بندی وجود ندارد.";

            return back()->withErrors( $message );
        }

        // حذف رکوردهای درجه بندی
        $production_form_item->fabric_grading()->delete();

        $production_form_item->status_id=0; // برگشت به وضعیت در انتظار درجه بندی
        $production_form_item->initial_shrinkage_percent=0; //
        $production_form_item->second_shrinkage_percent=0; //
        $production_form_item->general_shrinkage_percent=0; //
        $production_form_item->amount_after_control=0; //
        $production_form_item->save();

        $production_form_item->production_form->status_id=7002003; // در انتظار درجه بندی
        $production_form_item->production_form->save();

        event(new ProductionFormLogEvent(
            $production_form_item->production_form,
            700213, // حذف درجه بندی
            $production_form_item
        ));

        return redirect()->route( $this->dashboard_route . "view",  $production_form_item->production_form );
    }

    public function checkPermission( ProductionFormItem $production_form_item ) {

        $result = DashboardController::checkPermissionConditions( $production_form_item->production_form, GradingCancelController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
